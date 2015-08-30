<?php
require_once(dirname(__FILE__).'/../Postingestorwebscraper_Plugin.php');
require_once(dirname(__FILE__).'/SupraScraperJobPersistence.class.php');

class UploadCsv extends Postingestorwebscraper_Plugin {

    private $mimes   = array("text/csv","text/comma-separated-values",'application/vnd.ms-excel','text/plain','text/tsv');
    private $success = false;
    private $error;
    private $preview_num = 10;

    function __construct($file = null) {
        //parent::__construct();

        if(!empty($file['uploaded'])) {
            $this->processFile($file['uploaded']);
        }

        if(!file_exists($this->getCsvDir())) {
            mkdir($this->getCsvDir(),0777,true);
            chmod($this->getCsvDir(),0777);
        }

        $this->jp = new SupraScraperJobPersistence();
    }

    public function renderForms() { 
        echo '<div id="response">'.$this->getErrorMsg().'</div>'; 
        echo $this->getForm();
        echo $this->getUploads();
        echo '<div id="supra_scraper_preview"></div>';
    }

    public function getSuccess() {
        return $this->success;
    }

    public function getErrorMsg() {
        return $this->error;
    }

    private function validateFileType($type) {

        $valid = false;

        foreach($this->mimes as $mime) {
            if($type == $mime) { 
                $valid = true;
                break;
            }
        }

        if(!$valid) {
            $this->error = '<span class="error">File is not a csv format</span>';
            $valid = false;
        } 
        
        return $valid;
    }

    private function processFile($file) {
        if($this->validateFileType($file['type'])) {
            $this->error = '<span class="error">Something went wrong.</span>';
            $target = $this->getCsvDir() . basename( $file['name']); 
 
            if(move_uploaded_file($file['tmp_name'], $target)) {
                $this->success = true;
                $this->error = '<span class="success">' . $file['name'] . " successfully uploaded</span>";
            }
        }
    }

    public function writeToFile($filename, $contents) {

       return file_put_contents($this->getCsvDir() . $filename, $contents);
    }

    private function getForm() {

            return '<form enctype="multipart/form-data" method="POST">
            Please choose a file: <input name="uploaded" type="file" />
            <input type="submit" value="Upload" />
            </form>';
    }

    private function getUploads() {
        $files = $this->getUploadedFiles();
       
        $list = null;
 
        foreach($files as $i=>$file) {
            $delete_button = '<button id="delete_upload" data-key="'.$i.'">Delete</button>';
            $download_button = '<button id="download_upload" data-file="'.$file.'">Preview / Download</button>';
            $list .= '<li>'.$delete_button.$download_button.$file.'</li>';
        }

        return '<ul id="uploaded_files">'.$list.'</ul>'; 
    }

    public function getUploadedFiles() {
        return array_diff((array)scandir($this->getCsvDir()), array('..', '.'));
    }

    public function getFileByKey($key) {
        if(empty($key)) return false;
        $files = $this->getUploadedFiles();
        return $files[$key];
    }

    public function deleteFileByKey($key) {

        $filename = $this->getFileByKey($key);

        $success = unlink($this->getCsvDir() . $filename);

        if($success) {
          $this->error = '<span class="success">Successfully deleted ' . $filename . '</span>';
          $this->jp->deleteJobByFilename($filename);
        } else {
          $this->error = '<span class="error">Error deleting ' . $filename . '</span>';
        }

        $this->renderForms();
    }

    function downloadFile($file) {
        $filename_abs = $this->getCsvDir() . $file;
        $filename_url = $this->getCsvDirUrl() . $file;
	echo '<b>(showing First '.$this->preview_num.' lines)</b> or ' .
             '<a href="'.$filename_url.'" target="_blank">Download File</a>';
        $row = 1;
        $csv_settings = get_option('sscrap_csv_settings');

        if (($handle = fopen($filename_abs, "r")) !== FALSE) {
            while (($buffer = fgets($handle, 4096)) !== false) {
                echo "<br />";
                $row++;
                echo $buffer;
                if($row==10) break;
            }
            fclose($handle);
        }
    }

    private function parseNextLine($handle,$csv_settings) {
        if (strnatcmp(phpversion(),'5.3') >= 0) { 
            return fgetcsv($handle,1000,stripslashes($csv_settings['delimiter']),stripslashes($csv_settings['enclosure']),stripslashes($csv_settings['escape']));
 
        } 
        else { 
            return fgetcsv($handle,1000,stripslashes($csv_settings['delimiter']),stripslashes($csv_settings['enclosure']));
        } 
    }

    public function getFileSelectorOptions() {

        $options = '<option value=""></option>';

        foreach($this->getUploadedFiles() as $key=>$file) {
            $options .= '<option value="'.$key.'">'.$file.'</option>';
        }

       return $options;
    }

    public function displayFileSelector() {

        $options = $this->getFileSelectorOptions();
        echo '<label for="select_csv_file">File To Ingest:</label><select id="select_csv_file">'.$options.'</select>';
    }
 
    public function getCronJobUrl($args) {

      extract($args);
      $urls = array();
      $rootUrl = admin_url('admin-ajax.php') . '?action=supra_scraper&command=ingestion-by-csv&args[dataC]=' . $fileKey;
      $urls['create'] = $rootUrl;
      $urls['update'] = $rootUrl . '&args[update]=true';
      return $urls;
    }
}
