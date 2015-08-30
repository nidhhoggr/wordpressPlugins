<?php
namespace SupraCsvPremium;

require_once(dirname(__FILE__) . '/../../../../wp-config.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(dirname(__FILE__) . '/../SupraCsvParser_Plugin.php');

class SupraCsvAttachmentCreator extends \SupraCsvParser_Plugin {


    public function processAttachment($post_thumbnail, $POST_ID = null) {

        if(is_null($POST_ID) && !$this->isImage($post_thumbnail))
            return $post_thumbnail;

        $attachments = explode('|',$post_thumbnail);

        foreach($attachments as $attachment) {

            if($this->isImage($attachment)) {

                $new_img = $this->imageCreateFromAny($attachment);
                $attach_id = $this->createAttachment($new_img,$POST_ID);
                $attachment_ids[] = $attach_id;
            }
        }

        if(is_null($POST_ID))
            return $attachment_ids[0];
    }

    private function isImage( $url ) {
        $pos = strrpos( $url, ".");
        if ($pos === false) return false;
        $ext = strtolower(trim(substr( $url, $pos)));
        $imgExts = array(".gif", ".jpg", ".jpeg", ".png", ".tiff", ".tif"); // this is far from complete but that's always going to be the case...
        if ( in_array($ext, $imgExts) )
            return true;
        return false;
    }

    private function getImageType($filepath) {
        $imgTypes = array(
            "gif"=>1,
            "jpg"=>2,
            "jpeg"=>2,
            "png"=>3
        );

        $imageType = exif_imagetype($filepath);

        if(!$imageType) {

            $filename = end(explode('/',$filepath));

            $fnp = explode('.',$filename);

            $ext = strtolower($fnp[1]);

            $imageType = $imgTypes[$ext];

        }

        return $imageType;
    }


    private function imageCreateFromAny($filepath) { 

        $type = $this->getImageType($filepath); // [] if you don't have exif you could use getImageSize() 

        $allowedTypes = array( 
            1,  // [] gif 
            2,  // [] jpg 
            3  // [] png 
        ); 

        $fileNameArr = explode('/',$filepath);

        $filename = end($fileNameArr);

        $fnp = explode('.',$filename);

        $proposed_filename = $fnp[0] . '.' . $fnp[1];

        $wp_upload_dir = wp_upload_dir();

        $i=1;

        while(file_exists($wp_upload_dir['path'] . '/' . $proposed_filename )) {

            $proposed_filename = $fnp[0] . '_' . $i . '.' . $fnp[1];
            $i++;
        }

        $filename = $wp_upload_dir['path'] . '/' . $proposed_filename;

        if (!in_array($type, $allowedTypes)) { 
            return false; 
        } 

        switch ($type) { 
        case 1 : 
            $im = imageCreateFromGif($filepath); 
            imagegif($im,$filename);
            imagedestroy($im);
            break; 
        case 2 : 
            $im = imageCreateFromJpeg($filepath); 
            imagejpeg($im,$filename);
            imagedestroy($im);
            break; 
        case 3 : 
            $im = imageCreateFromPng($filepath); 
            imagepng($im,$filename);
            imagedestroy($im);
            break; 
        }    

        return $filename;  
    } 

    private function createAttachment($filename,$post_id) {

        $wp_filetype = wp_check_filetype(basename($filename), null );

        $wp_upload_dir = wp_upload_dir();

        $attachment = array(
            'guid' => $wp_upload_dir['url'] . '/' . basename( $filename ), 
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
            'post_content' => '',
            'post_status' => 'inherit',
            'post_parent'=>$post_id
        );

        $attach_id = wp_insert_attachment( $attachment, $filename);

        $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );

        wp_update_attachment_metadata( $attach_id, $attach_data );

        return $attach_id;
    }
}

/*
$scac = new SupraCsvAttachmentCreator();
$filename = $scac->processAttachment('http://fearlessflyer.com/main/wp-content/uploads/2011/09/file-url.jpg');
var_dump($filename);
 */
