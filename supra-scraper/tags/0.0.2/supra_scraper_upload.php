<?php 
require_once(dirname(__FILE__).'/classes/UploadCsv.php');
$uc = new UploadCsv($_FILES);
wp_enqueue_script( 'scraper_misc', plugins_url('/js/misc.js', __FILE__) );
?>
<h3>
<span id="filemgmt_tt" class="tooltip"></span>
Site Links CSV File Management
</h3>
<h4>Downloads Sample Csv <a href="<?=plugins_url('/files/links.csv', __FILE__)?>">Here</a></h4>

<div id="supra_scraper_upload_forms" class="wrap_sscrap" style="width: 550px;">
    <?php $uc->renderForms();?>
</div>

