<?php
require_once('UploadCsv.php');
require_once('PageIngestor.class.php'); 
require_once('SupraScraperJobPersistence.class.php'); 
require_once(dirname(__FILE__) . '/../Postingestorwebscraper_Plugin.php'); 

class SupraScraperIngestion extends Postingestorwebscraper_Plugin {


    public function updateOptions($request) {
        $sscrappost['publish'] = $request['sscrap_autopub'];
        $sscrappost['type'] = (empty($request['sscrap_posttype'])) ? $request['sscrap_custom_posttype'] : $request['sscrap_posttype'];
        $sscrappost['title'] = $request['sscrap_defaulttitle'];
        $sscrappost['desc'] = $request['sscrap_defaultdesc'];
        //$sscrapingest['scrapeimages'] = $request['sscrap_scrapeimages'];
        //$sscrapingest['scrapelinks'] = $request['sscrap_scrapelinks'];
        $sscrapingest['contentselector'] = $request['sscrap_contentselector'];
        $sscrapingest['storemeta'] = $request['sscrap_storemeta'];
        if(!empty($sscrapingest['storemeta'])) {
            $sscrapingest['pm_title'] = $request['sscrap_pmtitle'];
            $sscrapingest['pm_keys'] = $request['sscrap_pmkeys'];
            $sscrapingest['pm_desc'] = $request['sscrap_pmdesc'];
        }
        //$sscrap_adap['ffb'] = $request['sscrap_ffb'];
        //if(!empty($sscrap_adap['ffb']))
        //$sscrap_adap['pagewidth'] = $request['sscrap_ffb_pagewidth'];
        $sscrap_adap = array('pagewidth','ffb'); 
        $ingest_debugger = $request['sscrap_ingest_debugger'];
        $csv_settings = $request['sscrap_csv_settings'];



        update_option('sscrap_post', $sscrappost);
        update_option('sscrap_adap', $sscrap_adap);
        update_option('sscrap_ingest', $sscrapingest);
        update_option('sscrap_ingest_debugger', $ingest_debugger);
        update_option('sscrap_csv_settings', $csv_settings);
        return array('msg'=>'<div class="updated"><p><strong>Configuration saved</strong></p></div>');
    }

    protected function getOptionKeys() {

        return array(
            'post',
            'adap',
            'ingest',
            'ingest_debugger',
            'report_issue',
            'csv_settings',
            'additional_csv_settings',
            'randomize',
            'metakeymapping',
        );
    }

    protected function getOptions() {

        foreach($this->getOptionKeys() as $ok) {
            $options[$ok] = get_option('sscrap_' . $ok);
        }

        return $options;
    }

    public function ingestByText($request) {

        foreach(preg_split("/((\r?\n)|(\r\n?))/", $request) as $line){
            $res = $this->_ingestPage($line);
            foreach($res['aErrs'] as $aErr) {
                $aErrs[] = $aErr;
            }
        } 

        $debug = null;

        if(count($this->debugInfo)>=1)
            $debug = $this->debugInfo;

        return compact('aErrs','debug');
    }

    public function ingestByCsv($request, $update = false) {

        $update = $update && $update !== "false"; 

        $uc = new UploadCsv();

        $jp = new SupraScraperJobPersistence();

        $fileName = $uc->getFileByKey($request); 

        $file = $this->getCsvDir() . $fileName;

        $handle = @fopen($file, "r");

        $fileLocation = null;

        $aErrs = array();

        $rowKey = 0;

        $hasJob = $jp->jobOfFilenameHasPosts($fileName);

        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
                $file = $fileLocation . trim($buffer);

                $currentJob = $jp->getCurrentJob();

                if($hasJob && $update) 
                {
                    $res = $this->_updatePage($file, $currentJob[$rowKey]);
                } 
                else 
                { 
                    $res = $this->_ingestPage($file);
                }

                if($res['post_id']) 
                {
                    if(!in_array($res['post_id'],$currentJob))
                    {
                        $jobRes = $jp->saveJobByFilename($fileName,$res['post_id']);

                        if(!$jobRes) 
                        {
                            $aErrs[] = 'Error: could not save post id to file name ' . $res['post_id'] . ' - ' . $fileName; 
                        }

                        $jp->refreshCurrentJob();
                    }
                }

                foreach($res['aErrs'] as $aErr) {
                    $aErrs[] = $aErr;
                }

                $rowKey++;
            }
            if (!feof($handle)) {
                $aErrs[] = "Error: unexpected fgets() fail";
            }
            fclose($handle);
        }

        $debug = null;

        foreach($jp->getDebugMsgs() as $debug_msg)
        {
            $this->debugInfo[] = $debug_msg;
        }

        if(count($this->debugInfo)>=1)
            $debug = $this->debugInfo;

        return compact('aErrs','debug');
    }

    protected function _getDomainName($url) {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            return $regs['domain'];
        }
        return false;
    }

    protected function _getPageDescription($meta, $description) {
        if(count($meta['desc']))
            return str_replace('[meta-tag]',$meta['desc'],$description);
    }

    protected function _getPageTitle($title_o, $title) {
        return str_replace('[title-tag]',$title_o,$title);
    }

    private function _ingestPage($page) {

        $options = $this->getOptions();

        if($options['ingest_debugger'])
            $this->debugInfo[] = 'ingesting page '.$page;

        $pi = new PageIngestor($page, $options['ingest']['contentselector']);

        if(!$pi->isParseable())
            return array('aErrs'=>array($page . ' is not parseable'));

        $pageInfo = $this->_getPageInfo($pi, $page, $options);

        //var_dump($pageInfo); 

        extract($pageInfo);

        $postTitle = $this->_getPageTitle($title, $options['post']['title']);
        $postDesc = $this->_getPageDescription($meta, $options['post']['desc']);

        $postType = $options['post']['type'];

        $post = array(
            'post_author'    => 1, //The user ID number of the author.
            'post_content'   => $content, //The full text of the post.
            'post_excerpt'   => $postDesc, //For all your post excerpt needs.
            'post_status'    => ((bool)$options['post']['publish'])?'publish':'pending', //Set the status of the new post.
            'post_title'     => $postTitle, //The title of your post.
            'post_type'      => $postType, //You may want to insert a regular post, page, link, a menu item or some custom post type
        );

        $aErrs = array();

        $err = null;

        $post_id = wp_insert_post($post, $err);

        if(!$post_id)
        {
            $aErrs[] = "Issue ingesting " . $pi->getFilename() . " with " . $post;
        }
        else
        {
            if($options['ingest_debugger'])
                $this->debugInfo[] = 'Successfully created post id of ' . $post_id;

        }

        if(isset($err)) $aErrs[] = "Wordpress Error: $err " . $pi->getFilename() . " with " . $post;

        if($options['ingest']['storemeta'] && $post_id && !$err) {

            $titlePm = $options['ingest']['pm_title'];
            $keysPm = $options['ingest']['pm_keys'];
            $descPm = $options['ingest']['pm_desc'];

            if(!empty($titlePm ))
                $pmr[] = add_post_meta($post_id,$titlePm,$title,true);

            if(!empty($keysPm ) && count($meta['tags']))
                $pmr[] = add_post_meta($post_id,$keysPm,implode(', ',$meta['tags']),true);

            if(!empty($descPm) && count($meta['desc']))
                $pmr[] = add_post_meta($post_id,$descPm,$meta['desc'],true);
        }

        return compact('aErrs','post_id');
    }

    private function _getPageInfo($pi, $page, $options) {

        if(@$options['adap']['ffb']) {
            $pi->setFixedContentWidth($options['adap']['pagewidth']);
            $blocks = $pi->getFixedPixelBlocks();
        }

        $pi->setSiteDomain($this->_getDomainName($page));

        $title = $pi->getTitle();
        $meta = $pi->getMeta();
        $content = $pi->getContent();

        if(@$options['ingest']['scrapelinks']) {
            $links = $pi->getLinks();
        }
        if(@$options['ingest']['scrapeimages']) {
            $images = $pi->getImages();
        }

        return compact('title','meta','content','links','images');
    }

    private function _updatePage($page, $postId) {

        $options = $this->getOptions();

        if($options['ingest_debugger'])
            $this->debugInfo[] = 'updating page '.$page;

        $pi = new PageIngestor($page, $options['ingest']['contentselector']);

        if(!$pi->isParseable())
            return array('aErrs'=>array($page . ' is not parseable'));

        $pageInfo = $this->_getPageInfo($pi, $page, $options);

        extract($pageInfo);

        $postTitle = $this->_getPageTitle($title, $options['post']['title']);
        $postDesc = $this->_getPageDescription($meta, $options['post']['desc']);
        $postType = $options['post']['type'];

        /**fps***/
        if(!in_array($postType,$this->getAllowablePostTypes())) {
            return array('aErrs'=>array($this->upgradeToPremiumMsg('use custom posttypes')));
        }

        $post = array(
            'ID'		=>$postId,
            'post_content'   => $content, //The full text of the post.
            'post_excerpt'   => $postDesc, //For all your post excerpt needs.
            'post_status'    => ((bool)$options['post']['publish'])?'publish':'pending', //Set the status of the new post.
            'post_title'     => $postTitle, //The title of your post.
            'post_type'      => $postType, //You may want to insert a regular post, page, link, a menu item or some custom post type
        );

        $aErrs = array();

        $err = null;

        $post_id = wp_update_post($post, $err);

        if(!$post_id)
        {
            $aErrs[] = "Issue updating " . $pi->getFilename() . " with " . var_export($err, TRUE);
        }
        else if($options['ingest_debugger'])
        {
            $this->debugInfo[] = 'Succecfully updated post id of ' . $post_id;
        }

        if(isset($err)) $aErrs[] = "Wordpress Error: $err " . $pi->getFilename() . " with " . $post;

        if($options['ingest']['storemeta'] && $post_id && !$err) {

            $titlePm = $options['ingest']['pm_title'];
            $keysPm = $options['ingest']['pm_keys'];
            $descPm = $options['ingest']['pm_desc'];

            if(!empty($titlePm ))
                $pmr[] = update_post_meta($post_id,$titlePm,$title,true);

            if(!empty($keysPm ) && count($meta['tags']))
                $pmr[] = update_post_meta($post_id,$keysPm,implode(', ',$meta['tags']),true);

            if(!empty($descPm) && count($meta['desc']))
                $pmr[] = update_post_meta($post_id,$descPm,$meta['desc'],true);
        }

        return compact('aErrs','post_id');
    }
}
