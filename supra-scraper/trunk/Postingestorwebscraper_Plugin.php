<?php
include_once('Postingestorwebscraper_LifeCycle.php');
require_once('classes/SupraScraperJobPersistence.class.php'); 

class Postingestorwebscraper_Plugin extends Postingestorwebscraper_LifeCycle {


    private $download_link = 'www.supraliminalsolutions.com/blog/listings/supra-scraper/';
    private $csv_dir = "csv";
    private $extracted_csv_dir = "extracted-csv";


    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return array of option meta data.
     */
    public function getOptionMetaData() {
    }

    public function getPageTable() {

        if(empty($this->page_table))
            $this->page_table = $this->prefixTableName('page');

        return $this->page_table;
    }

    protected function initOptions() {}

    public function getPluginDisplayName() {
        return 'Supra Scraper';
    }

    protected function getMainPluginFileName() {
        return 'postingestorwebscraper.php';
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Called by install() to create any database tables if needed.
     * Best Practice:
     * (1) Prefix all table names with $wpdb->prefix
     * (2) make table names lower case only
     * @return void
     */
    protected function installDatabaseTables() {
      global $wpdb;
      $page_table= $this->getPageTable();

      $pageSql = "
        CREATE TABLE IF NOT EXISTS `$page_table` (
          `id` int(8) NOT NULL AUTO_INCREMENT,
          `filename` varchar(256) NOT NULL,
          `post_id` int(8) NOT NULL,
          `links` longtext NOT NULL,
          `images` longtext NOT NULL,
          `fixedblocks` longtext NOT NULL,
          PRIMARY KEY (`id`)
        );";

      $wpdb->query($pageSql);
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Drop plugin-created tables on uninstall.
     * @return void
     */
    protected function unInstallDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
    }


    /**
     * Perform actions when upgrading from version X to version Y
     * See: http://plugin.michael-simpson.com/?page_id=35
     * @return void
     */
    public function upgrade() {
    }


    //page-factory
    public function __call($name, $arguments)
    {
        $callable = array('home','upload','ingest','docs');

        foreach($callable as $called) {
            if( substr($name,0,7) == "sscrap_" && strstr($name,$called)) {
                require_once(dirname(__FILE__) . '/supra_scraper_' . $called . '.php');
                break;
            }
        }
    }


    public function callAdminActions() {
        add_menu_page("Supra Scraper", "Supra Scraper", "manage_options", "supra_scraper", array(&$this,"sscrap_home"));
        add_submenu_page("supra_scraper", "Docs", "Docs", "manage_options", "supra_scraper_docs", array(&$this,"sscrap_docs"));
        add_submenu_page("supra_scraper", "Upload", "Upload", "manage_options", "supra_scraper_upload", array(&$this,"sscrap_upload"));
        add_submenu_page("supra_scraper", "Ingestion", "Ingestion", "manage_options", "supra_scraper_ingest", array(&$this,"sscrap_ingest"));
    }

    public function supraScraperAjax() {
        require_once(dirname(__FILE__).'/classes/SupraScraperAjaxHandler.php');
        $ah = new SupraScraperAjaxHandler($_REQUEST);
        die();
    }

    function suprascraper_enqueue_scripts() {
        wp_enqueue_style('supra_scraper_-style', plugins_url('/css/style.css', __FILE__));
        wp_enqueue_script('jquery');
        wp_enqueue_script('supra_scraper_globals', plugins_url('/js/global.js', __FILE__));
        wp_enqueue_script('supra_scraper_toolip-lib', plugins_url('/js/jquery.qtip-1.0.0-rc3.min.js', __FILE__));
        wp_enqueue_script('supra_scraper_toolip', plugins_url('/js/tooltip.js', __FILE__));
        wp_enqueue_script('supra_scraper_target', plugins_url('/js/ScraperTarget.class.js', __FILE__));
    }

    public function addActionsAndFilters() {
        add_action('admin_menu', array(&$this, 'callAdminActions'));
        add_action('wp_ajax_supra_scraper',array(&$this,'supraScraperAjax'));
        add_action('activated_plugin',array(&$this,'save_error'));
        add_action( 'admin_enqueue_scripts',array(&$this,'suprascraper_enqueue_scripts'));
        add_action( 'deleted_post', array(&$this,'pagepersistence_sync'));
    }

    function pagepersistence_sync( $pid ) {
        $jp = new SupraScraperJobPersistence();
        $res = $jp->removePostIdByPostId($pid);
    }

    function save_error(){
        update_option('suprascraperplugin_error',  ob_get_contents());
    }


    public function getPremiumLink($target,$text) {
        return '<a href="http://'.$target.'" target="_blank">'.$text.'</a>';
    }

    public function upgradeToPremiumMsg($reason=null) {
        return '<span class="error">Upgrade to '.$this->getPremiumLink($this->download_link,'premium').' to '.$reason.'</span>';
    }

    private function getPluginName() {
      $arr = array_reverse(explode('/', dirname(__FILE__)));
      return $arr[0];
    }


    public function getPluginDirUrl() {
        return WP_PLUGIN_URL . '/' . $this->getPluginName() .'/';
    }

    private function getPluginRelUploadsDir() {
        return '/uploads/' . $this->getPluginName() .'/';
    }

    private function getPluginUploadsDir() {
        return WP_CONTENT_DIR . $this->getPluginRelUploadsDir();
    }

    private function getPluginUploadsDirUrl() {
        return WP_CONTENT_URL . $this->getPluginRelUploadsDir();
    }

    public function getCsvDir() {
        return $this->getPluginUploadsDir() . $this->csv_dir . '/';
    }

    public function getExtractedCsvDir() {
        return $this->getPluginUploadsDir() . $this->extracted_csv_dir . '/';
    }

    public function getSampleCsvDir() {
        return $this->plugin->getSampleCsvDir();
    }

    public function getCsvDirUrl() {
        return $this->getPluginUploadsDirUrl() . $this->csv_dir . '/';
    }
 
    public function getAllowablePostTypes() {
      return array('post','page','attachment','nav_menu_item');
    }

}
