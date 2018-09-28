<?php
    require_once(dirname(__FILE__).'/classes/FormInput.class.php');
    require_once(dirname(__FILE__).'/classes/Form.class.php');

    $fi   = new FormInput();
    $form = new Form($id);

    if($form->hasInstance())
        $form_head['form_id'] = array('type'=>'hidden','value'=>$form->getForm('id'));
    else
        $form_head['form_id'] = array('type'=>'hidden','value'=>$form->getNextFormId());

    wp_enqueue_script( 'global', plugins_url('/js/global.js', __FILE__), array('jquery') );
    wp_enqueue_script( 'form_submission', plugins_url('/js/form_submission.js', __FILE__) );
    wp_enqueue_style( 'form_buildercss', plugins_url('/css/form_builder.css', __FILE__) );
    wp_enqueue_style( 'form_buildercss', plugins_url('/css/form_viewer.css', __FILE__) );

?>
<div id="notify"></div>
<div id="open_form_viewer">
  <? $form->insertInput($form_head);?>
  <? $form->display(null,true);?>
</div>
