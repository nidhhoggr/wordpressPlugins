<?php
require_once(dirname(__FILE__) .'/../classes/FormInputCrud.class.php');

$input_crud = new FormInputCrud();

if($_REQUEST['edit_input']) {
    $input_crud->editInput($_REQUEST);
}
else if($_REQUEST['update_input']) {
    $input_crud->updateInput($_REQUEST,true);
}
else if($_REQUEST['delete_input']) {
    $input_crud->deleteInput($_REQUEST,true);
}
else if(!empty($_REQUEST['add_input'])) {
    $input_crud->addInput($_REQUEST,true);
}

