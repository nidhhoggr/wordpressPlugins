<?php

require_once(dirname(__FILE__) . '/../libs/HTML-DOM-Parser/simple_html_dom.php');

class PageIngestor 
{

    private 
        $site_domain,
        $content_width = null;

    function __construct($page = false, $contentSelector = false) 
    {

        if($page) 
        { 
            $this->_setFilename($page);
            $this->page = file_get_html($page);

            if($this->isParseable()) 
            { 
                if($contentSelector) $this->setContentDiv($contentSelector); 
                $this->_setMeta();
                $this->_setContent();
            }
        }
    }

    public function isParseable() 
    {
        return is_object($this->page);
    }

    private function _setFilename($page) 
    {
        if(strstr($page,'http'))
        {
            $this->filename = $page;
        }
        else
        {
            $fn = explode('/',$page);
            $fn = array_reverse($fn);
            $fn = $fn[0];
            $this->filename = $fn;
        }
    }

    private function _setMeta() 
    {
        $meta = array(
            'desc'=>array(),
            'tags'=>array()
        ); 

        $re = $this->page->find('meta[name=description]', 0);

        if($re) 
        { 
            $meta['desc'] = $re->content;
        }

        $rt = $this->page->find('meta[name=keywords]', 0);

        if($rt) 
        { 
            $tags = $rt->content;
            $meta['tags'] = explode(',', $tags);
        }

        $this->meta = $meta;
    }

    public function setContentDiv($div) 
    {
        $this->contentDivSelector = $div;
    }

    public function getContentDiv() 
    {
        if(empty($this->contentDivSelector))
            return 'body';
        else
            return $this->contentDivSelector;
    }

    private function _setContent() 
    {
        $this->content = $this->page->find($this->getContentDiv(), 0);
    }

    public function getFilename() 
    {
        return $this->filename;
    } 

    public function getMeta()
    {
        return $this->meta;
    }

    public function getTitle() 
    {
        return $this->page->find('title', 0)->innertext;
    }

    public function getContent() 
    {
        if(is_object($this->content))
        {
            return $this->content->innertext;
        }
    }

    public function getPagePostMeta($selectors) 
    {
        $meta = array(); 

        foreach($selectors as $meta_key=>$group_vals)
        {
            extract($group_vals);

            $node_obj = $this->page->find($nodeselector, 0);

            if(is_object($node_obj))
            {
                $meta[$meta_key] = $node_obj->plaintext;
            }
        }

        return $meta;
    }

    public function getImages($content = false) {

        $srcs = $gl = array();

        if(!$content) {
            $content = $this->content;
        }
        else {
            $content = str_get_html($content);
        }

        if(is_null($this->site_domain))
            throw new Exception('Must set the site domain');

        foreach($content->find('img') as $img) {
            $srcs[] = $img->src;
        }

        if(count($srcs) == 0) return;

        $srcs = array_unique($srcs);

        foreach($srcs as $src) {

            if ((substr($src, 0, 7) == 'http://') || (substr($src, 0, 8) == 'https://')) {
                if(strstr($src, $this->site_domain)) {
                    $gl[] = compact('link','count');
                }
            }
            else {
                $gl[] = $src;
            }
        }

        return $gl;
    } 

    public function isSiteDomainBased($src) {

        if(is_null($this->site_domain))
            throw new Exception('Must set the site domain');

        $is = false;

        if ((substr($src, 0, 7) == 'http://') || (substr($src, 0, 8) == 'https://')) {
            if(strstr($src, $this->site_domain)) {
                $is = true;
            }
        }

        return $is;
    }

    public function getLinks($content = false) {

        if(!$content) {
            $content = $this->content;
        }
        else {
            $content = str_get_html($content);
        }

        if(is_null($this->site_domain))
            throw new Exception('Must set the site domain');

        $hrefs = $gl = array();

        foreach($content->find('a') as $anchor) {

            if(!empty($anchor->href)) 
                $hrefs[] = $anchor->href;
        }

        if(count($hrefs) == 0) return;

        $links = array_count_values($hrefs);

        foreach($links as $link=>$count) {

            if ((substr($link, 0, 7) == 'http://') || (substr($link, 0, 8) == 'https://')) {
                if(strstr($link, $this->site_domain)) {
                    $gl[] = compact('link','count');
                }
            }
            else if(!strstr($link,'mailto:')) { 
                $gl[] = compact('link','count');
            }
        }

        return $gl;
    } 

    public function getBlocksOfFixedDimensions($content) {

        $blocks = $content->find('*[height], *[width]');

        $info = array();

        foreach($blocks as $block) {

            $info[] = $this->getBOFDInfo($block);
        } 

        return $info;
    }

    public function getBOFDInfo($block) {

        $info['obj'] = $block; 
        $info['tag'] = $block->tag;
        $info['content'] = $block->innertext;
        $info['taginfo'] = $this->_getBOFDTagInfo($block);
        return $info;
    }

    private function _getBOFDTagInfo($block) {

        $taginfo = explode('>',$block->outertext);
        return $taginfo[0] . '>';
    }

    public function isPixelFix($elObj) {
        $width = $elObj->width;
        return !strstr($width,'%');
    }

    public function getFixedPixelBlocks($content = false) {

        $info = $overall = array();

        if(!$content) {
            $content = $this->content;
        }
        else {
            $content = str_get_html($content);
        }

        $blocks = $this->getBlocksOfFixedDimensions($content);

        if(!count($blocks)) return;

        foreach($blocks as $block) {
            if($this->isPixelFix($block['obj'])) {
                $info[] = $block['taginfo'];
                $blocks[$block['taginfo']] = $block['obj'];
            }
        }

        if(count($info) == 0) return;

        $counted = array_count_values($info);

        foreach($counted as $info=>$count) {
            $block = array('obj'=>$blocks[$info]);
            $overall[] = array(
                'info'=>$info, 
                'count'=>$counted[$info],
                'resize'=> $this->getBlockSuggestedPercentage($block)
            );
        }

        return $overall;
    }

    public function getBlockSuggestedPercentage($block) {
        if(is_null($this->content_width)) 
            throw new Exception("Must set the content width");

        $el = $block['obj'];
        $width = $el->width;
        $suggested = ($width / $this->content_width);
        return round($suggested * 100,0) . '%';
    }

    public function setFixedContentWidth($width) {
        $this->content_width = $width;
    }

    public function setSiteDomain($domain) {
        $this->site_domain = $domain;
    } 
}
