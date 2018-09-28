<?php
require_once(dirname(__FILE__).'/FormInput.class.php');
require_once(dirname(__FILE__).'/Form.class.php');
require_once(dirname(__FILE__).'/FormBuilder.class.php');

class FormInputCrud {

    private $form, $form_input, $form_builder;

    function __construct() {
        $this->form_input   = new FormInput();
        $this->form_builder = new FormBuilder();
        $this->form         = new Form();
    }

    //@desc: add an input to the form session
    public function addInput($request,$display = false) {

        $form_id   = $request['form_id'];
        $input_type  = $request['input_type'];
        $form_input  = $request['form_input'];
        $input_key   = $request['input_key'];

        //parse the jquery form serialization
        if(!is_array($form_input))
            parse_str($form_input,$input_params);

        //set the input parameters for the inputprocessor
        foreach((array)$input_params as $k=>$v) {
            if(!strstr($k,'input_attr') 
               && !strstr($k,'radio') 
               && !strstr($k,'check')) {
                $input_param[$k] = $v;
            }
        }

        //set the array of attributes for the inputprocessor
        foreach((array)$input_params['input_attr_key'] as $k=>$v) {
             $input_param['attr'][$v] = $input_params['input_attr_val'][$k];
        }

        foreach((array)$input_params['radio_val'] as $k=>$v) {
             $radios[] = array(
                               'label'=>$input_params['radio_label'][$k],
                               'value'=>$input_params['radio_val'][$k]
                              );
        }

        switch($input_type) {
            case "select": 
                $choices = $this->selectParseChoices($input_param);
            break;
            case "radiogroup":
                $radios = $this->radiogroupParseRadios($input_params); 
            break;
            case "combobox":
                $boxes = $this->comboboxParseCheckboxes($input_params);
            break;
        }
 
        $input_arr[$input_param['name']] = array(
                            'type'=>$input_type,
                            'label'=>$input_param['label'],
                            'value'=>$input_param['value'],
                            'attr'=>$input_param['attr'],
                            'choices'=>$choices,
                            'boxes'=>$boxes,
                            'radios'=>$radios,
                            'add_empty'=>$input_param['add_empty']
                           );

        //add the input to the form sessio
        if(!empty($input_key) || $input_key === "0") {
            $_SESSION['open_form'][$form_id]['inputs'][$input_key] = $input_arr;
        }
        else 
            $_SESSION['open_form'][$form_id]['inputs'][] = $input_arr;


        //display the input as editbale
        if($display) {
            $this->form_input->renderAndDisplay($input_arr,true,true);
        }
    }

    public function selectParseChoices($input_param) {
        $choices = $input_param['choices'];

        //parse choices from csv string into an array
        if(!empty($choices)) {
            $choices = urldecode($choices);
            $choices = explode(',',$choices);
        }

        return $choices;
    }

    public function radiogroupParseRadios($input_params) {

        foreach((array)$input_params['radio_val'] as $k=>$v) {
             $radios[] = array(
                               'label'=>$input_params['radio_label'][$k],
                               'value'=>$input_params['radio_val'][$k]
                              );
        }

        return $radios;
    }

    public function comboboxParseCheckboxes($input_params) {
    
        foreach((array)$input_params['check_value'] as $k=>$v) {
             $name = $input_params['check_name'][$k];

             $boxes[$name] = array(
                                   'label'=>$input_params['check_label'][$k],
                                   'value'=>$input_params['check_value'][$k]
                                  );
        }

        return $boxes;
    }


    //@desc: display input builder for the input to edit
    public function editInput($request) {
        $form_id    = $request['form_id'];
        $input_name = $request['input_name'];

        $input_key = $this->form->getInputKeyByName($form_id,$input_name);

        $input     = $this->form->getInputByName($form_id,$input_name);

        $input_params = $input[$input_name];
        $input_type   = $input_params['type'];

        //parse the attributes for the inputbuilder
        foreach((array)$input_params['attr'] as $k=>$v) {
             $attributes[] = array('input_attr_key'=>$k,'input_attr_val'=>$v);
        }

        unset($input_params['attr']);

        foreach((array)$input_params as $k=>$v) {
            if(!empty($v))
                $new_ip[$k]['value'] = $v;
        }

        if($input_type == 'select') {
            if($new_ip['add_empty']['value'])
                $new_ip['add_empty']['attr'] = array('checked'=>'checked');

            $choices = &$new_ip['choices']['value'];

            if(count($choices))
                $choices = implode(',',$choices);
        }

        $new_ip['name']['value'] = $input_name;

        unset($new_ip['type']);

        $new_ip['input_type'] = $input_params['type'];;

        $ik['input_key'] = array('type'=>'hidden','value'=>$input_key,'name'=>'input_key');

        $this->form_input->renderAndDisplay($ik);
        $this->form_builder->inputBuilder($new_ip,true);

        foreach((array)$attributes as $attribute) {
            foreach($attribute as $k=>$v) {
                $attr[$k]['value'] = $v;
            }
            $attr['input_type'] = 'attribute';
            $this->form_builder->inputBuilder($attr,true);
        }

       foreach((array)$input_params['boxes'] as $name=>$params) {
             $check['check_name']['value']  = $name;
             $check['check_value']['value'] = $params['value'];
             $check['check_label']['value'] =  $params['label'];
             $check['input_type'] = 'check';
             $this->form_builder->inputBuilder($check,true);
        }

       foreach((array)$input_params['radios'] as $name=>$params) {
             $check['radio_val']['value'] =  $params['value'];
             $check['radio_label']['value'] =  $params['label'];
             $check['input_type'] = 'radio';
             $this->form_builder->inputBuilder($check,true);
        }
    }

    //@desc: update the input in the form session
    public function updateInput($request,$display=false) {
        $this->deleteInput($request);
        $this->addInput($request);

        $inputs = $_SESSION['open_form'][$request['form_id']]['inputs'];

        ksort($inputs);

        //echo "<pre>";
        //print_r($inputs);
        //echo "</pre>";
 
        if($display) {
            echo $this->form->renderInputs($inputs,true);
        }
    }

    //desc: delete the input from the form session
    public function deleteInput($request,$display=false) {

        extract($request);

        $input_key = $this->form->getInputKeyByName($form_id,$input_name);

        unset($_SESSION['open_form'][$form_id]['inputs'][$input_key]);

        if($display)
            echo $this->form->renderInputs($_SESSION['open_form'][$form_id]['inputs'],true);
    }
}
