<?php
/*
   Plugin Name: Supra Open Form
   Plugin URI: http://wordpress.org/extend/plugins/supra-open-form/
   Version: 1.3
   Author: J. Persie
   Description: a plugin to create, modify and retrieve forms and manage submissions
   Text Domain: supra-open-form
   License: GPL3
  */


$SupraOpenForm_minimalRequiredPhpVersion = '5.0';

/**
 * Check the PHP version and give a useful error message if the user's version is less than the required version
 * @return boolean true if version check passed. If false, triggers an error which WP will handle, by displaying
 * an error message on the Admin page
 */
function SupraOpenForm_noticePhpVersionWrong() {
    global $SupraOpenForm_minimalRequiredPhpVersion;
    echo '<div class="updated fade">' .
      __('Error: plugin "Supra Open Form" requires a newer version of PHP to be running.',  'supra-open-form').
            '<br/>' . __('Minimal version of PHP required: ', 'supra-open-form') . '<strong>' . $SupraOpenForm_minimalRequiredPhpVersion . '</strong>' .
            '<br/>' . __('Your server\'s PHP version: ', 'supra-open-form') . '<strong>' . phpversion() . '</strong>' .
         '</div>';
}


function SupraOpenForm_PhpVersionCheck() {
    global $SupraOpenForm_minimalRequiredPhpVersion;
    if (version_compare(phpversion(), $SupraOpenForm_minimalRequiredPhpVersion) < 0) {
        add_action('admin_notices', 'SupraOpenForm_noticePhpVersionWrong');
        return false;
    }
    return true;
}


/**
 * Initialize internationalization (i18n) for this plugin.
 * References:
 *      http://codex.wordpress.org/I18n_for_WordPress_Developers
 *      http://www.wdmac.com/how-to-create-a-po-language-translation#more-631
 * @return void
 */
function SupraOpenForm_i18n_init() {
    $pluginDir = dirname(plugin_basename(__FILE__));
    load_plugin_textdomain('supra-open-form', false, $pluginDir . '/languages/');
}


//////////////////////////////////
// Run initialization
/////////////////////////////////

// First initialize i18n
SupraOpenForm_i18n_init();


// Next, run the version check.
// If it is successful, continue with initialization for this plugin
if (SupraOpenForm_PhpVersionCheck()) {
    // Only load and run the init function if we know PHP version can parse it
    include_once('supra-open-form_init.php');
    SupraOpenForm_init(__FILE__);
}
