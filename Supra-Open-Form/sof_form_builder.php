<?php
    require_once(dirname(__FILE__).'/classes/FormInput.class.php');
    require_once(dirname(__FILE__).'/classes/Form.class.php');

    $fi   = new FormInput();
    $form = new Form($_GET['id']);

    $inputtypes = $fi->getInputTypes();

    //get the page info 
    foreach(get_pages() as $wp_page) {
        $pages[] = array($wp_page->ID => $wp_page->post_title);
    }

    $db_succ_msg = $form->getForm('success_msg');
    $succ_msg_def = "if the page is empty the success message here will display instead.";
    $succ_msg = (empty($db_succ_msg))?$succ_msg_def:$db_succ_msg;

    $input_types['input_type']  = array('type'=>'select','label'=>'Input Type','choices'=>$inputtypes,'add_empty'=>true);
    $form_head['form_name']     = array('type'=>'text','label'=>'Form Name','value'=>$form->getForm('name'));
    $form_head['wp_post_id']   = array('type'=>'select','label'=>'Success Page','value'=>$form->getForm('wp_post_id'),'choices'=>$pages,'add_empty'=>true);
    $success_msg['success_msg']   = array(
                                          'type'=>'textarea',
                                          'label'=>'Success Message',
                                          'value'=>$succ_msg,
                                          'attr'=>array('rows'=>3,'cols'=>45),
                                         );

    if($form->hasInstance())
        $form_head['form_id'] = array('type'=>'hidden','value'=>$form->getForm('id'));
    else { 
        $form_head['form_id'] = array('type'=>'hidden','value'=>$form->getNextFormId());
        $form->clearSession(); 
    }

    $build_tools['add_input']   = array('type'=>'submit','value'=>'Add Input');
    $build_tools['update_input']= array('type'=>'submit','value'=>'Update Input');
    $build_tools['clear_form']  = array('type'=>'submit','value'=>'Clear Form');
    $build_tools['save_form']   = array('type'=>'submit','value'=>'Save Form');
    $input_attr['add_attr']     = array('type'=>'submit','value'=>'Add Attribute');
    $input_attr['rem_attr']     = array('type'=>'submit','value'=>'Remove Attribute');
    $input_radio['add_radio']   = array('type'=>'submit','value'=>'Add Radio');
    $input_radio['rem_radio']   = array('type'=>'submit','value'=>'Remove Radio');
    $input_check['add_check']   = array('type'=>'submit','value'=>'Add Checkbox');
    $input_check['rem_check']   = array('type'=>'submit','value'=>'Remove Checkbox');

    wp_enqueue_script( 'global', plugins_url('/js/global.js', __FILE__), array('jquery') );
    wp_enqueue_script( 'form_builder', plugins_url('/js/form_builder.js', __FILE__) );
    wp_enqueue_script( 'input_crud', plugins_url('/js/input_crud.js', __FILE__) );
    wp_enqueue_style( 'form_buildercss', plugins_url('/css/form_builder.css', __FILE__) );
?>
    <div id="open_form">
      <div id="notify"></div>
      <div id="left_of">
        <div id="input_builder_tools">
          <? $fi->renderAndDisplay($form_head);?>
          <div id="success_msg_div">
          <? $fi->renderAndDisplay($success_msg);?>
          </div>
          <div id="input_specs">
            <? $fi->renderAndDisplay($input_types);?>
            <? $fi->renderAndDisplay($input_attr);?>
            <? $fi->renderAndDisplay($input_radio);?>
            <? $fi->renderAndDisplay($input_check);?>
          </div>
        </div>
        <form id="input_builder">
        </form>
        <div id="builder_actions">
          <? $fi->renderAndDisplay($build_tools);?>
        </div>
      </div>
      <div id="right_of">
        <div id="form_built">
            <? $form->display(null,false,true);?>
        </div>
      </div>
      <div id="cleaner"></div>
    </div> 
