<?php
require_once(dirname(__FILE__) .'/../classes/FormSubmission.class.php');
require_once(dirname(__FILE__).'/../classes/FormInput.class.php');
require_once(dirname(__FILE__).'/../classes/Form.class.php');
$fi   = new FormInput();
$fs= new FormSubmission();

if(!empty($_REQUEST['process_form'])) {
    $fs->submitForm($_REQUEST);
}
else if(!empty($_REQUEST['view_form'])) {
    $form_id = $_REQUEST['form_id'];
    $form = new Form($form_id);
    $form_head['form_id'] = array('type'=>'hidden','value'=>$form_id);
    $form->insertInput($form_head);
    $form->display(null,true);
}
else if(!empty($_REQUEST['view_submissions'])) {
    $fs->displaySubmissions($_REQUEST['form_id']);
}
else if(!empty($_REQUEST['view_submission'])) {
    $fs->displaySubmission($_REQUEST['submission_id']);
}

