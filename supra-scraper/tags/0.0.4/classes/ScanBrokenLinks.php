<?php
require_once(dirname(__FILE__) . '/PageIngestor.class.php');

class ScanBrokenLinks 
{ 

  private $err = array(); 

  function __construct() 
  { 
    global $WordpressModel, $LinksErroredModel;

    //$conditions = array('post_type = "page"','ID >= 696');
    $conditions = array('post_type = "page"');
    $this->posts = $WordpressModel->findBy(compact('conditions'));
    $this->lem = $LinksErroredModel;
    $this->lem->configure();
  }


  function url_validate($link)
  {
	#[url]http://www.jellyandcustard.com/2006/05/31/determining-if-a-url-exists-with-curl/[/url]
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $link);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 10); //follow up to 10 redirections - avoids loops
	$data = curl_exec($ch);
	curl_close($ch);
	preg_match_all("/HTTP\/1\.[1|0]\s(\d{3})/",$data,$matches);
 
	$code = end($matches[1]);
 
	if(!$data) 
	{
                $this->throwError($link,"No data was returned");
		return(false);
	} 
	else 
	{
		if($code==200) 
		{
			return(true);
		} 
		elseif($code==404) 
		{
                        $this->throwError($link,$code);
			return(false);
		}
	}
  }

  function throwError($link,$code) {

    var_dump($this->postId, $link, $code); 
    $this->err[$this->postId][] = compact('link','code');
  }

  function savePostErrors() {

    if(@count($this->err[$this->postId]) > 0) {
      $this->lem->post_id = $this->postId;
      $this->lem->errors = $this->err[$this->postId];
      var_dump("Saving error of " . $this->lem->save());
    }
  }

  function runScan() { 

    foreach($this->posts as $post) {

      var_dump("Scanning " . $post->ID); 

      $this->postId = $post->ID;

      $post_content = $post->post_content;

      $html = str_get_html($post_content);

      $links = $html->find('a, img');

      foreach($links as $link) {

        if($link->tag == "a")
          $n_link = $link->href;
        else if($link->tag == "img")
          $n_link = $link->src;
        else
          die('wierd tag of ' . $link->tag);

        //the link was blank
        if(!$n_link) continue;

        if(substr($n_link,0,1) == "#") continue;
        if(substr($n_link,0,11) == "javascript:") continue;
        if(substr($n_link,0,7) == "mailto:") continue;

        $this->url_validate($n_link);
      }

      $this->savePostErrors();
    }

  }
}

$sbl = new ScanBrokenLinks();

$sbl->runScan(); 
