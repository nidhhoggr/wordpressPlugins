<?php 
require_once(dirname(__FILE__).'/FormInput.class.php');

class FormBuilder {

    private $form_input;

    function __construct() {
        $this->form_input = new FormInput();
    }

    function inputBuilder($params,$edit=false) {

        $input_type = $params['input_type'];

        switch($input_type) {

            case "combobox":
                $form_input = $this->renderComboBoxBuilder();
            break;
            case "radiogroup":
                $form_input = $this->renderRadioGroupBuilder();
            break;
            case "select":
                $form_input = $this->renderSelectBuilder();
            break;
            case "radio":
                $form_input = $this->renderRadioAdder();
            break;
            case "check":
                $form_input = $this->renderCheckAdder();
            break;
            case "attribute":
                $form_input = $this->renderAttributeAdder();
            break;
            default:
                $form_input = $this->renderRegInputBuilder($input_type);
        }

        //echo "<pre>";
        //print_r($form_input);
        //print_r($params);
        //echo "</pre>";

        if($edit)
            $form_input = $this->custom_merge($form_input,$params);

        //echo "<pre>";
        //print_r($_SESSION);
        //print_r($form_input);
        //echo "</pre>";

        if(in_array($input_type,array('attribute','radio','check'))) {
            $form_input = $this->form_input->render($form_input);
            echo $this->form_input->wrapInput($form_input,$input_type);
        } 
        else {
            $this->form_input->renderAndDisplay($form_input);
        }
    }

    function custom_merge($form_inputs,$defaults) {

        foreach($form_inputs as $k=>$v) {
            if(!count($defaults[$k]))
                continue; 
            $array[$k] = array_merge($v,$defaults[$k]);
        }

        return array_merge($form_inputs,(array)$array);
    }

    function renderSelectBuilder() {


        $form_input['name'] = array(
                                    'type'=>'text',
                                    'label'=>'Select Name'
                                   );

        $form_input['label'] = array(
                                     'type'=>'text',
                                     'label'=>'Select Label'
                                    );

        $form_input['add_empty'] = array(
                                         'type'=>'checkbox',
                                         'label'=>'Add Empty?',
                                         'value'=>true
                                        );

        $form_input['choices'] = array(
                                     'type'=>'text',
                                     'label'=>'Choices',
                                     'value'=>'first,second,third,etc',
                                     'help'=>'provide comma seperated values'
                                    );

        return $form_input;
    }

    function renderRadioGroupBuilder() {


        $form_input['name'] = array(
                                    'type'=>'text',
                                    'label'=>'Radiogroup Name'
                                   );


        $form_input['label'] = array(
                                     'type'=>'text',
                                     'label'=>'Radiogroup Label'
                                    );

        return $form_input;
    }

    function renderComboBoxBuilder() {

        $form_input['label'] = array(
                                     'type'=>'text',
                                     'label'=>'Combobox Label'
                                    );

        return $form_input;
    }

    function renderRegInputBuilder($input_type) {

        
        $form_input['name'] = array(
                                    'type'=>'text',
                                    'label'=>'Name'
                                   );

        $form_input['value'] = array(
                                     'type'=>'text',
                                     'label'=>'Value'
                                    );


        if(!in_array($input_type,array('submit','hidden')))
            $form_input['label'] = array(
                                         'type'=>'text',
                                         'label'=>'Label'
                                        );

        return $form_input;
    }

    function renderAttributeAdder() {

        $form_input['input_attr_key'] = array(
                                          'type'=>'text',
                                          'label'=>'Attribute Key',
                                          'arrayable'=>true
                                         );

        $form_input['input_attr_val'] = array(
                                          'type'=>'text',
                                          'label'=>'Attribute Value',
                                          'arrayable'=>true
                                         );

        return $form_input;
    }

    function renderRadioAdder() {

        $form_input['radio_label'] = array(
                                     'type'=>'text',
                                     'label'=>'Radio Label',
                                     'arrayable'=>true
                                    );

        $form_input['radio_val'] = array(
                                     'type'=>'text',
                                     'label'=>'Radio Value',
                                     'arrayable'=>true
                                    );

        return $form_input;
    }

    function renderCheckAdder() {

        $form_input['check_name'] = array(
                                     'type'=>'text',
                                     'label'=>'Checkbox Name',
                                     'arrayable'=>true
                                    );

        $form_input['check_label'] = array(
                                     'type'=>'text',
                                     'label'=>'Checkbox Label',
                                     'arrayable'=>true
                                    );

        $form_input['check_value'] = array(
                                     'type'=>'hidden',
                                     'value'=>'true',
                                     'arrayable'=>true
                                    );

        return $form_input;
    }
}
