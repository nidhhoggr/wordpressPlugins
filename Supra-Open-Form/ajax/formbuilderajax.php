<?php
require_once(dirname(__FILE__) .'/../classes/FormBuilder.class.php');
require_once(dirname(__FILE__) .'/../classes/Form.class.php');

$fb = new FormBuilder();
$form = new Form();

if(!empty($_REQUEST['select_input_type'])) {
    $fb->inputBuilder($_REQUEST);
}
else if(!empty($_REQUEST['add_attr'])) {
    $fb->inputBuilder($_REQUEST);
}
else if(!empty($_REQUEST['clear_form'])) {
    $form->clearForm($_REQUEST['form_name']);
}
else if(!empty($_REQUEST['save_form'])) {
    echo $form->saveForm($_REQUEST);
}
