<?php
require_once(dirname(__FILE__) .'/../classes/FormBuilder.class.php');
require_once(dirname(__FILE__) .'/../classes/Form.class.php');
require_once(dirname(__FILE__) .'/../classes/FormSubmission.class.php');
require_once(dirname(__FILE__).'/../classes/FormInput.class.php');
require_once(dirname(__FILE__) .'/../classes/FormInputCrud.class.php');

class AjaxHandler {

    public function formBuilder($request) {

        $fb = new FormBuilder();
        $form = new Form();

        if(!empty($request['select_input_type'])) {
            $fb->inputBuilder($request);
        }
        else if(!empty($request['add_attr'])) {
            $fb->inputBuilder($request);
        }
        else if(!empty($request['add_radio'])) {
            $fb->inputBuilder($request);
        }
        else if(!empty($request['clear_form'])) {
            $form->clearForm($request['form_name']);
        }
        else if(!empty($request['save_form'])) {
            echo $form->saveForm($request);
        }
    }

    public function formSubmission($request) {
        $form = new Form();
        $fi   = new FormInput();
        $fs= new FormSubmission();

        if(!empty($request['process_form'])) {
            $fs->submitForm($request);
        }
        else if(!empty($request['view_form'])) {
            $form_id = $request['form_id'];
            $form = new Form($form_id);
            $form_head['form_id'] = array('type'=>'hidden','value'=>$form_id);
            $form->insertInput($form_head);
            $form->display(null,true);
       }
       else if(!empty($request['view_submissions'])) {
            $fs->displaySubmissions($request['form_id']);
       }
       else if(!empty($request['view_submission'])) {
            $fs->displaySubmission($request['submission_id']);
       }
       else if(!empty($request['edit_form'])) {
            echo admin_url('admin.php?page=sof_builder&id='.$request['form_id']);
       }
       else if(!empty($request['delete_form'])) {
            $form->deleteForm($request['form_id']);
            echo admin_url('admin.php?page=sof_info');
       }

    }

    public function inputCrud($request) {

        $input_crud = new FormInputCrud();

        if($request['edit_input']) {
            $input_crud->editInput($request);
        }
        else if($request['update_input']) {
            $input_crud->updateInput($request,true);
        }
        else if($request['delete_input']) {
            $input_crud->deleteInput($request,true);
        }
        else if(!empty($request['add_input'])) {
            $input_crud->addInput($request,true);
        }
    }
}
