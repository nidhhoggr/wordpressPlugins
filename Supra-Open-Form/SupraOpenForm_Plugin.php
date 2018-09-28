<?php
require_once(dirname(__FILE__).'/classes/AjaxHandler.class.php');
include_once('SupraOpenForm_LifeCycle.php');

class SupraOpenForm_Plugin extends SupraOpenForm_LifeCycle {

    private $ajaxHandler;

    function __construct() {
        $this->ajaxHandler = new AjaxHandler();
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return array of option meta data.
     */
    public function getOptionMetaData() {
        //  http://plugin.michael-simpson.com/?page_id=31
        return array('notify_email'=>array('Notification Email:'),
                     'email_is_plain' => array('Use Plain Text Emails', 'false', 'true'),
            //'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
        );
    }

    public function getPluginDisplayName() {
        return 'Supra Open Form';
    }

    protected function getMainPluginFileName() {
        return 'supra-open-form.php';
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Called by install() to create any database tables if needed.
     * @return void
     */
    protected function installDatabaseTables() {
                global $wpdb;
                $openForms = $this->prefixTableName('open_forms');
                $openFormSubmissions = $this->prefixTableName('open_form_submissions');
 
                $openFormsSql = "
                CREATE TABLE IF NOT EXISTS `$openForms` (
                `id` int(8) NOT NULL AUTO_INCREMENT,
                `name` varchar(64) NOT NULL,
                `wp_post_id` int(8) NOT NULL,
                `success_msg` longtext NOT NULL,
                `inputs` longtext NOT NULL,
                PRIMARY KEY (`id`)
                );";

                $openFormSubmissionsSql = "
                CREATE TABLE IF NOT EXISTS `$openFormSubmissions` (
                `id` int(8) NOT NULL AUTO_INCREMENT,
                `form_id` int(8) NOT NULL,
                `submission` longtext NOT NULL,
                `datetime` varchar(15) NOT NULL,
                PRIMARY KEY (`id`)
                );";

                $wpdb->query($openFormsSql);
                $wpdb->query($openFormSubmissionsSql);
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Drop plugin-created tables on uninstall.
     * @return void
     */
    protected function unInstallDatabaseTables() {
                global $wpdb;
                $tables[] = $this->prefixTableName('open_forms');
                $tables[] = $this->prefixTableName('open_form_submissions');
                foreach($tables as $table) {
                    $wpdb->query("DROP TABLE IF EXISTS `$table`");
                }
    }


    /**
     * Perform actions when upgrading from version X to version Y
     * See: http://plugin.michael-simpson.com/?page_id=35
     * @return void
     */
    public function upgrade() {
    }


    public function sofFormBuilder() {
        require_once(dirname(__FILE__).'/sof_form_builder.php');
    }

    public function sofFormInfo() {
        require_once(dirname(__FILE__).'/sof_form_info.php');
    }

    public function callAdminActions() {
        add_menu_page("Supra Open Form", "Supra Open Form", "manage_options", "supra_open_form", array(&$this,"SettingsPage"));
        add_submenu_page("supra_open_form", "Form Info", "Form Info", "manage_options", "sof_info", array(&$this,"sofFormInfo"));
        add_submenu_page("supra_open_form", "Form Builder", "Form Builder", "manage_options", "sof_builder", array(&$this,"sofFormBuilder"));
    }

    public function formBuilderAjax() {
        $this->ajaxHandler->formBuilder($_REQUEST);    
        die();
    }
   
    public function formSubmissionAjax() {
        $this->ajaxHandler->formSubmission($_REQUEST);    
        die();
    }
    
    public function inputCrudAjax() {
        $this->ajaxHandler->inputCrud($_REQUEST);    
        die();
    }

    public function addActionsAndFilters() {

        // Add options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        //add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));
        //add_action('admin_menu', array(&$this, 'sofFormBuilder'));

        add_action('admin_menu', array(&$this, 'callAdminActions'));


        //ajax actions
        add_action('wp_ajax_formBuilder', array(&$this, 'formBuilderAjax'));
        add_action('wp_ajax_nopriv_formSubmission', array(&$this, 'formSubmissionAjax'));
        add_action('wp_ajax_formSubmission', array(&$this, 'formSubmissionAjax'));
        add_action('wp_ajax_inputCrud', array(&$this, 'inputCrudAjax'));


        // Example adding a script & style just for the options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        //        if (strpos($_SERVER['REQUEST_URI'], $this->getSettingsSlug()) !== false) {
        //            wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));
        //            wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        //        }


        // Add Actions & Filters
        // http://plugin.michael-simpson.com/?page_id=37


        // Adding scripts & styles to all pages
        // Examples:
                  wp_enqueue_script('jquery');
        //        wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        //        wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));


        // Register short codes
        // http://plugin.michael-simpson.com/?page_id=39

        include_once('SupraOpenForm_RenderFormShortCode.php');
        $sc = new SupraOpenForm_RenderFormShortCode();
        $sc->register('supra-open-form');

        // Register AJAX hooks
        // http://plugin.michael-simpson.com/?page_id=41

    }
}
