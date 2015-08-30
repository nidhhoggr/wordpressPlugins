<?php

wp_enqueue_script( 'scraper_misc', plugins_url('/js/misc.js', __FILE__) );
wp_enqueue_script( 'inputCloner', plugins_url('/js/inputCloner.js', __FILE__) );

require_once('classes/UploadCsv.php'); 

$uc = new UploadCsv();

$sscrappost = get_option('sscrap_post');
$sscrap_adap = get_option('sscrap_adap');
$sscrapingest= get_option('sscrap_ingest');
$ingest_debugger = get_option('sscrap_ingest_debugger');
$report_issue = get_option('sscrap_report_issue');
$csv_settings = get_option('sscrap_csv_settings');
$additional_csv_settings = get_option('sscrap_additional_csv_settings');
$encode_chars = get_option('sscrap_encode_special_chars');
$randomize = get_option('sscrap_randomize');

/*

print_r($sscrappost);
print_r($sscrap_adap);
print_r($sscrapingest);
print_r($ingest_debugger);
print_r($report_issue);
print_r($csv_settings);
print_r($additional_csv_settings);
print_r($encode_chars);

*/

?>
<div class="wrap_sscrap" style="width: 630px">
<h2>Supra Scraper Ingestion</h2>
        <hr />
<div style="float: left; width: 300px;">
<form name="sscrap_form" method="post">
        <h3><span id="scraperscrapingoptions_tt" class="tooltip"></span>Scraping Options</h3>
<!--
        <p>
            <span id="scrapelinks_tt" class="tooltip"></span>Scrape Links: <input type="checkbox" name="sscrap_scrapelinks" value="true" <?php echo ($sscrapingest['scrapelinks'])?'checked="checked"':''?>>
        </p>
        <p>
            <span id="scrapeimages_tt" class="tooltip"></span>Scrape Images: <input type="checkbox" name="sscrap_scrapeimages" value="true" <?php echo ($sscrapingest['scrapeimages'])?'checked="checked"':''?>>
        </p>
-->
        <p><span id="contentselector_tt" class="tooltip"></span>Content Selector: <input type="text" name="sscrap_contentselector" value="<?php echo $sscrapingest['contentselector']?>" size="20"></p>

<!--
        <h4><span id="scraperadpitivity_tt" class="tooltip"></span>Adaptivity Utilities</h4>
        <p>
            <span id="findfixedblocks_tt" class="tooltip"></span>Find Fixed Blocks: <input type="checkbox" name="sscrap_ffb" value="true" <?php echo ($sscrap_adap['ffb'])?'checked="checked"':''?>>
        </p>
        <p><span id="fbpagewidth_tt" class="tooltip"></span>Page Width<input type="text" name="sscrap_ffb_pagewidth" value="<?php echo $sscrap_adap['pagewidth']?>" <?php echo (!$sscrap_adap['ffb'])?'disabled':''?> size="20"></p>
-->
        <hr />

        <h3><span id="postsettings_tt" class="tooltip"></span>Post Settings</h3>
        <p>
            <span id="autopublish_tt" class="tooltip"></span>Auto Publish
            <select name="sscrap_autopub">
                <option value="0">false</option>
                <option value="1" <?php if($sscrappost['publish']) echo 'selected="selected"';?>>true</option>
            </select>
        </p>
        <p>
            <span id="posttype_tt" class="tooltip"></span>Post Type
            <select name="sscrap_posttype">
                <option value=""></option>
                <?php 
                  $types = array('post','page','attachment','nav_menu_item');
                  $str = '';   
                  foreach($types as $type) { 
                    $str .= '<option value="'.$type.'" ';
                    if($type == $sscrappost['type']) $str .= 'selected';
                    $str .= ' value="'.$type.'">'.$type.'</option>';
                  }
                  echo $str;
                ?>
            </select>
        </p>
        <p style="text-align: center">
            <b>or</b>
        </p>
        <p>
            <span id="customposttype_tt" class="tooltip"></span>Custom Post Type <span class="premium_only">(Premium Only)</span>
            <input type="text" name="sscrap_custom_posttype" value="" size="5" disabled>
        </p>

        <h3><span id="postdefaults_tt" class="tooltip"></span>Post Defaults</h3>
        <p>Default Title<input type="text" name="sscrap_defaulttitle" value="<?php echo $sscrappost['title']; ?>" size="20"></p>
        <p>Default Description<textarea name="sscrap_defaultdesc" id="sscrap_defaultdesc"><?php echo $sscrappost['desc']; ?></textarea></p>

        <hr />
        <h3><span id="csvsettings_tt" class="tooltip"></span>CSV Settings</h3>
        <p id="csv_settings">
            <?php $settings_keys = array('delimiter'=>',','enclosure'=>'"','escape'=>'\\'); ?>
            <?php foreach($settings_keys as $k=>$v): ?>
                <p class="sscrap_input"><?php echo $k?>:<input type='text' name='sscrap_csv_settings[<?php echo $k?>]' value='<?php echo($csv_settings[$k])?stripslashes($csv_settings[$k]):$v;?>' size='2' maxlength='2' /></p>
            <?php endforeach; ?>
        </p>

        <hr />
        <h3>HTML Meta to WP Post Meta</h3>
 
        <p id="storepostmeta">
            <span id="storepostmeta_tt" class="tooltip"></span>Store Post Meta: 
            <input type="checkbox" name="sscrap_storemeta" value="true" <?php echo ($sscrapingest['storemeta'])?'checked="checked"':''?>>
        </p>
        <h4><span id="postdefaults_tt" class="tooltip"></span>Post Meta Keys</h4>
        <p>Title:<input type="text" name="sscrap_pmtitle" value="<?php echo $sscrapingest['pm_title']; ?>" size="20"></p>
        <p>Meta Keywords:<input type="text" name="sscrap_pmkeys" value="<?php echo $sscrapingest['pm_keys']; ?>" size="20"></p>
        <p>Meta Description:<input type="text" name="sscrap_pmdesc" value="<?php echo $sscrapingest['pm_desc']; ?>" size="20"></p>

        <hr /> 

</div>
<div style="float: right; width: 300px;">
        <h3>Ingestion Settings</h3>
        <p id="ingestion_debugging">
            <span id="debugingestion_tt" class="tooltip"></span>Debug Ingestion: 
            <input type="checkbox" name="sscrap_ingest_debugger" value="true" <?php echo ($ingest_debugger)?'checked="checked"':''?>>
        </p>
        <p id="issue_reporting">
            <span id="reportissues_tt" class="tooltip"></span>Report Issues: <span class="premium_only">(Premium Only)</span><input type="checkbox" name="sscrap_report_issue" value="true" <?php echo ($report_issue)?'checked="checked"':''?> disabled>
        </p>
        <p id="randomize_is">
            <span id="randomize_is_tt" class="tooltip"></span>Randomize<span class="premium_only">(Premium Only)</span><input type="checkbox" name="sscrap_randomize_is" value="true" <?php echo($randomize['is'])?'checked="checked"':''?> disabled>
        </p>
        <p>
            <span id="randomize_min_tt" class="tooltip"></span>Minimum Interval <span class="premium_only">(Premium Only)</span>
            <input type="text" name="sscrap_randomize_min_int" value="<?php echo $randomize['min_int']?>" size="2" disabled>
        </p>
        <p>
            <span id="randomize_max_tt" class="tooltip"></span>Maximum Interval <span class="premium_only">(Premium Only)</span>
            <input type="text" name="sscrap_randomize_max_int" value="<?php echo $randomize['max_int']?>" size="2" disabled>
        </p>
        <hr />
        <h3><span id="scrapertarget_tt" class="tooltip"></span>Scraper Target</h3>
        <p>Site Links</p>
        <p>
        <textarea name="sscrap_sitelinkstext" id="sscrap_sitelinkstext"><?php ?></textarea></p>
        <p class="or_divider">
            <b>or</b>
        </p>
        <p>Site Links CSV
            <select name="sscrap_sitelinkscsv">
                <?php echo $uc->getFileSelectorOptions() ?>
            </select>
        </p>
        <div id="updateExisting">
          <p>
            <span id="targetupdate_tt" class="tooltip"></span>Update Existing Posts
            <input type="checkbox" name="sscrap_targetupdate" value="true">
          </p>
          <h3>CRON Job Urls</h3>
          <ul id="cron_job_url"></ul>
        </div>

</div>
<div style="clear: both"></div>

        <p>
            <span id="updateoptions_tt" class="tooltip"></span>
            <input type="submit" name="sscrap_submit" value="Update Options"  class="stackedButt" />
            <span id="ingest_tt" class="tooltip"></span>
            <input type="submit" name="sscrap_ingest" value="Start Ingestion" class="stackedButt" />
        </p>

        <div id="update_msg"></div>
        <ul id="aErrList"></ul>
        <ul id="debugList"></ul>        
        <div id="patience"></div>
</form>
</div>
