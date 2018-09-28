<?php 
require_once(dirname(__FILE__).'/InputProcessor.class.php');
class FormInput {

    private $input_processor;

    function __construct($inputs = array(),$display=false) {

        $this->input_processor = new InputProcessor();

        if(count($inputs))
            $this->renderAndDisplay($inputs,$display);
    }

    function render($inputs,$editable = false) {
        return $this->renderAndDisplay($inputs,false,$editable);
    }

    function renderAndDisplay($inputs,$display=true,$editable=false) {
        $input_keys = array_keys($inputs);

        if(count($inputs))
            $htmlinputs = $this->renderInputs($inputs);

        foreach((array)$htmlinputs as $k=>$input) {

            $input_name = $input_keys[$k];
            $input_type = $inputs[$input_name]['type'];

            if($editable) {
                $form_input .= $this->renderInputEditor($input_name,$input_type);
                $form_input .= $this->renderInputDeletor($input_name);
                $form_input = $this->wrapInput($form_input,'input_crud');
            }

            $form_input .= $input;
        }

        if($display)
            echo $form_input;
        else
            return $form_input;
    }

    private function renderInputEditor($input_name,$input_type) {

        $edit_input['edit_input'] = array(
            'type'=>'submit',
            'value'=>'edit',
            'attr'=>array('data-input-name'=>$input_name,'data-input-type'=>$input_type)
        );

        return $this->render($edit_input);
    }

    private function renderInputDeletor($input_name) {

        $edit_input['delete_input'] = array(
            'type'=>'submit',
            'value'=>'delete',
            'attr'=>array('data-input-name'=>$input_name)
        );

        return $this->render($edit_input);
    }

    function getInputTypes() {

        return array(
                     'text',
                     'textarea',
                     'password',
                     'hidden',
                     'radiogroup',
                     'checkbox',
                     'combobox',
                     'select',
                     'submit'
                    );
    }

    function renderInput($name,$input) {

        switch($input['type']) {
            case "radiogroup":
                $output = $this->input_processor->processRadiogroup($name,$input);
            break; 
            case "textarea":
                $output = $this->input_processor->processTextarea($name,$input);
            break;
            case "select":
                $output = $this->input_processor->processSelect($name,$input);
            break;
            case "combobox":
                $output = $this->input_processor->processCombobox($input);
            break;
            default:
                $output = $this->input_processor->processRegInput($name,$input);
        }

        return $output;
    }

    function renderInputs($inputs) {

        foreach((array)$inputs as $name=>$input) {

            $rendered = $this->renderInput($name,$input);

            $input_arr[] = $this->wrapInput($rendered);

        }

        return $input_arr;
    }

    function wrapInput($input,$class = "input") {

        return '<div class="'.$class.'">' . $input . '</div>';

    }
}
