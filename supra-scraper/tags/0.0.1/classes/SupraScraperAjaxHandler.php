<?php
session_start();
require_once(dirname(__FILE__).'/UploadCsv.php');
require_once(dirname(__FILE__).'/SupraScraperIngestion.php');
class SupraScraperAjaxHandler extends Postingestorwebscraper_Plugin {

    //an instance of IngestCsv for the ingestion commands to share

    function __construct($request) {
        $uc = new UploadCsv();
        $ssi = new SupraScraperIngestion();

        switch($request['command']) {

            case "delete_file":
                $uc->deleteFileByKey($request['args']);
            break;
            case "download_file":
                $uc->downloadFile($request['args']);
            break;
            case "ingestion-update": 
                parse_str($request['args'], $req);
                $response = $ssi->updateOptions($req);
                echo json_encode($response); 
            break;
            case "get_tooltips":
                include(dirname(__FILE__) . '/../supra_scraper_docs.php');
            break;
            case "ingestion-by-text":
                $response = $ssi->ingestByText($request['args']);
                echo json_encode($response);
            break;
            case "ingestion-by-url":
                $response = $ssi->ingestByUrl($request['args']);
                echo json_encode($response);
            break;
            case "ingestion-by-csv":
                $response = $ssi->ingestByCsv($request['args']);
                echo json_encode($response);
            break;
        }
    }
}
