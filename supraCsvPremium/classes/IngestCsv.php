<?php
namespace SupraCsvPremium;
require_once("Debug.php");
require_once('CsvLib.php');
require_once('RemotePost.php');
require_once('SupraCsvChunker.php');

class IngestCsv {

    public function setSupraCsvParser(SupraCsvParser $supraCsvParser)
    {
        $this->scp = $supraCsvParser;
    }

    public function ParseAndMap($filename) {

        $this->scp->setFile($filename);

        $mf = new SupraCsvMapperForm($this->scp);

        return $mf->getForm();
    }

    public function ingest($params) {

        $this->misc_options = $this->scp->getSetting('scsv_misc_options');

        $this->scp->setFile($params['filename']);

        $csvFile = $this->scp->getFile();
       
        $mapper = $this->scp->setMapping($params['mapping']);

        $mapping = $mapper->getMapping();
        
        //lets do asynch processing here
        if(@ $this->misc_options['is_ingestion_chunked'])
        {       
            $mapping = $mapper->getMapping();

            $mappingFile = $this->scp->getPluginChunkDir() . '/' . basename($csvFile) . '.mapping';

            $fh = fopen($mappingFile, 'w+');

            $columns = $this->scp->getColumns();

            $mappingJson = compact('mapping','columns');

            $mappingJson = json_encode($mappingJson, true);
            
            $written = fwrite($fh, $mappingJson);

            if(!$written)
            {
                Throw new \Exception(__METHOD__ . " could not write to mapping file" . $mappingFile); 
            }

            $mappingFilename = basename($mappingFile);

            $row_chunk_number = $this->misc_options['chunk_by_n_rows'];

            $supraCsvChunker = new SupraCsvChunker();

            $supraCsvChunker->splitFile($csvFile, $row_chunk_number);

            $chunkedFiles = $supraCsvChunker->getChunkedFiles();

            if(!count($chunkedFiles))
            {
                Throw new \Exception(__METHOD__ . ': could not chunk files for ' . $csvFile);
            }
            else
            {
                $this->delegateIngestionToBackground($chunkedFiles, $mappingFilename);
            }

            return $supraCsvChunker;
        }   
        else
        {
            return $this->scp->ingestContent();
        }
    }

    private function delegateIngestionToBackground($csvFiles, $mappingFilename)
    {
        $ingestScript = $this->scp->getPluginBasePath() . '/ingest.php';
            
        foreach($csvFiles as $csvFile)
        {
            $csvFile = basename($csvFile);

            $command = "php {$ingestScript} '{$csvFile}' '{$mappingFilename}'";

            if(@$this->misc_options['is_using_multithreads'])
            {
                $command = "({$command}) > /dev/null 2>/dev/null &";
            }

            $this->scp->getLogger()->info(__METHOD__ . " executing {$command}");

            exec($command);
        }
    }

    public function pollIngestionCompletion($chunkNamespace)
    {
        $glob = $this->scp->getPluginChunkDir() . '/' . $chunkNamespace . '*.ingest';

        foreach (glob($glob) as $filename) {
            $contents = file_get_contents($filename);
            rename($filename, $filename.'ed');
            return $contents;
        }

        return false;
    }

    public function getSupraCsvParser()
    {
        return $this->scp;
    }
}
