<?php
namespace SupraCsvPremium;

require_once(dirname(__FILE__) . '/SupraCsvPlugin.php');

class SupraCsvChunker extends SupraCsvPlugin {

    private $chunkedFiles = array();

    function __construct($settings = array()) {

        parent::__construct();

        $this->targetPath = $this->getPluginChunkDir(); 
        
        if(!file_exists($this->targetPath))
        {
            mkdir($this->targetPath, 0777, true);
        }
    }

    public function getTargetPath()
    {
        return $this->targetPath;
    }

    public function getChunkedFiles()
    {
        return $this->chunkedFiles;
    }

    public function getChunkNamespace()
    {
        return $this->chunkNamespace;
    }

    function splitFile($source, $lines=10) {

        $line = $i = 0;

        $j=1;

        $date = date("m-d-y");

        $this->buffer='';

        $this->handle = @fopen ($source, "r");

        $file = basename($source);

        $this->chunkNamespace = "{$file}_part_{$date}_";

        while (!feof ($this->handle)) {
            
            $this->buffer .= @fgets($this->handle, 4096);

            $i++;

            if ($i >= $lines) {

                $this->ingest($line, $i, $j, $lines); 
            }
        }

        if(!empty($this->buffer))
        {
            $this->ingest($line, $i, $j, $lines); 
        }

        //$this->logger->info(var_export($this->buffer, true));

        fclose ($this->handle);
    }

    private function ingest(&$line, &$i, &$j, $lines)
    {
        $fname = $this->targetPath. '/' . $this->chunkNamespace . $j;

        $this->chunkedFiles[$line] = $fname;

        if (!$fhandle = @fopen($fname, 'w')) {
            Throw new \Exception("Cannot open file ($fname)");
        }

        if (!@fwrite($fhandle, $this->buffer)) {
            Throw new \Exception("Cannot write to file ($fname)");
        }

        fclose($fhandle);

        $j++;

        $this->buffer='';

        $i=0;

        $line += $lines; 
    }
}
