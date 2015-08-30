<style type="text/css">

#supra_docs li p {
  font-weight: normal; 
  font-size: 12px;
  padding: 15px;
  margin: 15px;
  background-color: #EEE;
  border: 1px solid #333;
  max-width: 450px;
}

#supra_docs li {
  font-weight: bold;    
}

#supra_docs ol li {
  font-size: 18px;
}

#supra_docs ol ol li {
  font-size: 16px;    
  list-style-type:upper-roman;
}

#supra_docs ol ol ol li {
  font-size: 14px;
  list-style-type:lower-alpha;
}


</style>
<div id="supra_docs">
<h3>Supra Scraper Documentation<h3>
<ol id="nav_list">
  <li><a href="#upload">Upload</a></li>
  <ol>
    <li>
      <a href="#file_types">
      File Types
      </a>
    </li>
    <li>
      <a href="#upload_crud_management">
      CRUD Management
      </a>
    </li>
  </ol>
  <li><a href="#ingestion">Ingestion</a></li>
  <ol>  
    <li>
      <a href="#scraperscrapingoptions">
      Scraping Options
      </a>
    </li>
    <ol> 
      <li>
        <a href="#contentselector">
        Content Selector
        </a>
      </li>
    </ol>
    <li>
      <a href="#post_settings">
      Post Settings
      </a>
    </li>
    <ol> 
      <li>
        <a href="#auto_publish">
        Auto Publish
        </a>
      </li> 
      <li>
        <a href="#post_type">
        Post Type
        </a>
      </li>
      <li>
        <a href="#custom_post_type">
        Custom Post Type
        </a>
      </li>
      <li>
        <a href="#post_defaults"> 
        Post Defaults
        </a>
      </li>
    </ol>
    <li><a href="#htmlmeta_wpmeta">HTML Meta to WP Post Meta</a></li>
    <ol>
      <li>
        <a href="#storepostmeta">
          Store Post Meta
        </a>
      </li>
      <li>
        <a href="#postmetakeys">
          Post Meta Keys
        </a>
      </li>
    </ol>
    <li><a href="#csv_settings">CSV Settings</a></li>
    <ol>
      <li>
        <a href="#delimiter">
          Delimiter
        </a>
      </li>
      <li>
        <a href="#enclosure">
          Enclosure
        </a>
      </li>
      <li>
        <a href="#escape">
          Escape
        </a>
      </li>
    </ol>
    <li><a href="#ingestion_settings">Ingestion Settings</a></li>
    <ol>
      <li>
        <a href="#debug_ingestion">
        Debug Ingestion
        </a>
      </li>
      <li>
        <a href="#report_issues">  
        Report Issues
        </a>
      </li>
      <li>
        <a href="#randomize_is">
        Randomize
        </a>
      </li>
      <li>
        <a href="#randomize_min_int">
        Minimum Interval
        </a>
      </li>
      <li>
        <a href="#randomize_max_int">
        Maximum Interval
        </a>
      </li>
    </ol>
    <li><a href="#scrapertarget">Scraper Target</a></li> 
    <li><a href="#st_targetupdate">Update Existing Posts</a></li> 
    <li><a href="#ingest">Ingest</a></li> 
    <li><a href="#updateoptions">Update Options</a></li> 
    <li><a href="#metakeymapping">Post Meta Key Mapping</a></li> 
  </ol>
</ol>
<ol id="docs_list">
  <li id="upload">Upload</li>
  <ol>
    <li id="file_types">
      File Types
        <p>
      the following file mime types are supported: text/csv, text/comma-separated-values, application/vnd.ms-excel, text/plain, text/tsv
        </p>
    </li>
    <li id="upload_crud_management">
      CRUD Management
        <p>
      deleting previewing and downloading the file can all be handled with ease
        </p>
    </li>
  </ol>
  <li id="ingestion">Ingestion</li>
  <ol>
    <li id="scraperscrapingoptions">Scraping Options</li>
    <ol>
      <li id="contentselector">
        Content Selector
        <p>
        This is used to specify what the wrapper element is. if the content you want to scrape happens to be in a div of class wrapper than you would specify .wrapper
        If no value is provided than the scraper will grap all content with the html body tag
        </p>
      </li>
    </ol>
    <li id="post_settings">
      Post Settings
      <p>The are settings related to specifying the attributes of a post.</p>
    </li>
    <ol> 
      <li id="auto_publish">
        Auto Publish
        <p>Toogle the checkbox to automiatically publish all ingested posts. If no value is provided the post will be ingested as pending.</p>
      </li> 
      <li id="post_type">
        Post Type
        <p>
        Select the type of post. Custom post types can be created by the plugins. Select the type of post to affect what options you will have
        for ingesting the file. Refer to <a href="http://codex.wordpress.org/Post_Types" target="_blank">Post Types</a>
        </p>   
      </li>
      <li id="custom_post_type">
        Custom Post Type
        <p>
        You can also proivde a custom post type by providing a value. If you decide to use the custom type leave the dropdown blank.
        </p>
      </li>
    </ol>
    
    <li id="post_defaults"> Post Defaults</li>
    <ol>
      <li>
        <p>Provide the default description and title when no value is provided in the ingested file.</p>
      </li>
      <li>
        <p>The usage of [title-tag] in the default title will grab the value in the html title tag.</p>  
      </li>
      <li>
        <p>The usage of [meta-tag] in the default description will grab the value of the meta description html tag.</p>
      </li>
    </ol>
    <li id="htmlmeta_wpmeta">HTML Meta to WP Post Meta</li>
    <ol>
      <li id="storepostmeta">
        Store Post Meta
        <p>
        if you want to the html page meta as wordpress post meta associated to each post simply toggle this checkbox and provide the post meta keys below.
        If the checkbox is untoggled post meta will not be stored regardless of what post meta keys are provided below.
        </p>
      </li>
      <li id="postmetakeys">
        Post Meta Keys
        <p>
        These are where the post meta keys are provided. For example one SEO plugin happens to use the following values: _su_title, _su_keywords, and _su_description
        </p>
      </li>
    </ol>
    <li id="csv_settings">CSV Settings</li>
    <ol>
      <li id="delimiter">
          Delimiter
        <p>
          The delimiter is what is used to separate values and if the file was a tsv it would be \t rather than ,
        </p>
      </li>
      <li id="enclosure">
          Enclosure
        <p>
          The enclosure is used to encapsulate strings to prevent parsing issues for special characters and spaces.
        </p>
      </li>
      <li id="escape">
          Escape
        <p>
          The escape is the charater used to ignore delimiters prefixed by this character. (supported>=PHP5.3)
        </p>
      </li>
    </ol>
    <li id="ingestion_settings">Ingestion Settings</li>
    <ol>
      <li id="debug_ingestion">
        Debug Ingestion
        <p>
        By toggling this checkbox bebug out will display on the screen once the ingestion is complete. This will allow you to provide erros in the support forum.
        </p>
      </li>
      <li id="report_issues">  
        Report Issues
        <p>
        By toggling this checkbox each error thrown will send debug information to admin to troubleshoot what may have went wrong. The limit of error reporting per 
        ingestion is confined to 3.
        </p>
      </li>
      <li id="randomize_is">
        Randomize
        <p>
        Randomization is a feature to make the scraper act at inconstent perormance in an attempt to trick bot detectors.
        Despite the fact values may be provided for maximum and minimum interval if this box is unchecked the scraper will not act randomly.
        </p>
      </li>
      <li id="randomize_min_int">
        Minimum Interval
        <p>
        Provide an integer in the amount of seconds that will be used to generate a random number. Any random number generated will be no lesser than this.
        </p>
      </li>
      <li id="randomize_max_int">
        Maximum Interval
        <p>
        Provide an integer in the amount of seconds that will be used to generate a random number. Any random number generated will be no greater than this.
        </p>
      </li>
    </ol>
    <li id="scrapertarget">Scraper Target</li>
    <ol>
      <li id="st_sitelinks">
        Site Links
        <p>
        Provide a new line delimited list of urls in this text box to be scraper
        </p>
      </li>
      <li id="st_sitelinkscsv">
        Site Links CSV
        <p>
        Select an existing upload file of links to scrape
        </p>
      </li>
    </ol>

    <li id="st_targetupdate">
        Update Existing Posts
        <p>
            When the checkbox is checked all associated posts to that job will be updated with the fresh content scraped. This is especially useful for running cron jobs. When the checkbox isn't new post will be created and these post will not be associated with the current job. You might see error messages indicating that there are more posts specified for the job than the pages. This simply means that the posts will not be saved to the job. This could have unexpected results when any posts related to a specific job are deleted. If this happens you will have to rename the csv file by deleting the upload and renaming it.
        </p>
    </li>

    <li id="ingest">
      Ingest
      <p>
      This will show a circular loader indicating to you that it is parsing the file. This may take a while.. if it takes longer
      than usual it may be related to php maximum execution limit or another php fatal error. You can toggle wordpress debug mode in the 
      wp-config.php to better troubleshoot this. If any errors are thrown they will be indicated in red with some descriptive 
      error information. If debug ingestion is enabled the result of the ingestion will be pretty ugly. 
      </p>
    </li>
    <li id="updateoptions">
      Update Options
      <p>
      This will save the current settings on this page with the exception of the values provided in any of the scraper target inputs
      </p>
    </li>
    <li id="metakeymapping">
      Meta Key Mapping
      <p>
      The purpose of this section is to scrape the content of child nodes into post meta. The Post Meta Key specified stores a value of the Node Selector Specified if any content was scraped and the node existed.
      </p>
    </li>
  </ol>
</ol>
</div>
