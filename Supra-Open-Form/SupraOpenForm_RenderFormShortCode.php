<?php
include_once('SupraOpenForm_ShortCodeLoader.php');
 
class SupraOpenForm_RenderFormShortCode extends SupraOpenForm_ShortCodeLoader {
 
    public function handleShortcode($atts) {

	extract( shortcode_atts( array(
		'id' => '0'
	), $atts ) );

        include('sof_form_viewer.php');

        wp_localize_script( 'global', 'global_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    }
}
