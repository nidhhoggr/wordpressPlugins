=== Supra CSV Premium ===
Contributors: zmijevik
Author URI: http://www.supraliminalsolutions.com/blog/downloads/supra-csv-premium/
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=CLC8GNV7TRGDU
Tags: csv,import,parser,ingest,custom post,extract,export,attachments,thumbnails
Requires at least: 3.2.1
Tested up to: 4.0
Stable tag: 2.0.1

A plugin to ingest and extract posts from csv files. 

== Description ==

The purpose of this plugin is to parse uploaded csv files into any type of
post. Themes or plugin store data in posts and this plugin provides the functionality 
to upload data from the csv file to the records that the theme or plugin creates. 
Manage existing csv files and promote ease of use by creating presets for both postmeta 
and ingestion mapping. For more infomation on how to obtain the necessary info watch the 
detailed tutorials <http://www.supraliminalsolutions.com/blog/supra-csv-tutorials/>. To ingest csv files into custom posts or extract posts into csv files
you must upgrade to the premium version of the plugin. 

== Frequently Asked Questions ==

= How do I ingest mutiple taxonomy for a post?  =
Provide a pipe symbol | as a delimiter for the custom terms. more info is provide in the docs at III.a.a

= parse error. not well formed =
Make sure there are no special characters in the csv values. The will show as question marks in your debug output 

== Screenshots ==

1. Configuration Tab

2. Uploads Tab

3. Post Meta Mapping Tab

4. Ingestion Tab

5. Extraction Tab

6. Easily debug issues

== Changelog ==
= 2.0.1 =
* removed error throwing in remotePost class
* wrapping filename paraemeters of cli command in qoutes for name with spaces
* adding the log management page
= 2.0.0 =
* fixed major issues with parsing csv lines
* adding csv file chunking support
* added asynchrnous multithreading capabilities
* added the ability to skip post revision insertions
* added error logging
* added error tips
* removing error reporting functionality
* fixed bugs with post type taxonomy validation
* fixing remaining mysqli support issues
= 1.3.6 =
* Adding support for mysqli in the DBAL
= 1.3.5 =
* Adding suport my mysqli extension
* removing csv filetype validation
= 1.3.4 =
* fixing return of error message from xmlrpc parser
= 1.3.3 =
* fixed major performance issues by overhauling xmlrpc methodology to process confined. No overhead of network latency
* fixed broken CSS that removed styling in 1.3.2.
* fixed an array shim that conflicted with backbone library
= 1.3.2 =
* fixed an issue with extraction that was causing row inconsistencty
* fixed an issue with extraction that was causing blank column values to be overridden by previous values
= 1.3.1 =
* fixed an issue with previewing extracts after deletion
* fixed bugs in the export feature
* fixed javascript bugs with extracted file previews
* added overridable CSV settings to the extraction funtionality
= 1.3.0 =
* adding the jquery table sorter to the csv preview/download buttons
= 1.2.9 =
* made some improvements to the ajax functionality on various pages
* improved usability and user friendliness
* removing more php notice errors
* adding plugin installation error notifier
= 1.2.8 =
* supression of php notice
* fixed bug with updating post meta
= 1.2.7 =
* fixed post status bug
= 1.2.6 =
* removing php notice errors
= 1.2.5 =
* fixing css after firing the designer
= 1.2.4 =
* supporting multisite support
* fixing activation hooks to install samples csv files
* removing the unexpected charcters generated plugin activation error
= 1.2.3 =
* separated file extraction from upoad interface
* dynamically populating extracted files in extraction interface
* major refactoring of codebase for extract and upload functionality
* fized a major issue resolving directory names
* centralizing documentation
= 1.2.2 =
* centralizing documentation
= 1.2.1 =
* fixed a major issue resolving directory names
= 1.2.0 =
* fixed a bug that was preventing autosuggestions from populating
= 1.1.9 =
* added all the fields to the extraction post_fields in a select drop down
* replaced input with select drop down for meta keys in the extraction interface
= 1.1.8 =
* fixing script clash of enqueue scripts bug
* added file export functionality into uploads from extraction
* added the ability to select multiple post type in extraction
* imploding field array in extracted post csv file
= 1.1.6 =
* created help icon tooltips for the configuration page and updated the docs
* adding tooltips to the rest of the pages
* implemented a hooking API
* updating the docs and tooltips about hooking
* integrated last_post_id as a hook
= 1.1.5 =
* added max character count per line of the csv
= 1.1.4 =
* error logging including the csv filename and the line number of the row
* make the results of the previous ingestion clear when you select a new file to ingest so that it shows you it is uploading it
= 1.1.3 =
added the ability to update posts.

fixed a bug that would ingest blank csv rows

added the sample_basic_edit csv to demonstrate ingesting a record to edit a post
= 1.1.2 =
added the attachments mapping to ingestion page to associate multiple images with a post
= 1.1.1 =
added more detailed and verbose debug output
= 1.0.9 =
fixing thumbnail attachment bugswith naming convention and file storage issues
= 1.0.8 =
added post_parent and menu_order in predefined on the ingestion page
= 1.0.7 =
autopopulating suggestion meta keys in postinfo with enable checkboxes
= 1.0.6 =
making special character encoding optional and fixing bug with ingesting images with the same name
= 1.0.5 = 
fixed the delimiter bug in the export tab
= 1.0.4 =
decoding special characters in post description and title before ingestion
= 1.0.3 =
added post status to the list of predefined meta in the ingestion page
= 1.0.2 =
ability to use urls for post_thumbnail to import images
= 1.0.1 = 
auto populate export meta keys
= 1.0.0 = 
plugin split into premium and free version
