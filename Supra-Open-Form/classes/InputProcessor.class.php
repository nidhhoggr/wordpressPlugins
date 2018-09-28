<?php 
class InputProcessor {

    public function processRegInput($name,$input) {

        $arrayable = $input['arrayable'];
        $type = $input['type'];
        $label = $input['label'];
        $attr = count($input['attr'])?$input['attr']:array();
        $value = $input['value'];

        if(!empty($label))
            $output .= '<label for="'.$name.'">'.$label.'</label>';

        $output .= '<input type="'.$type.'" ';

        foreach($attr as $k=>$v) {
            $output .= $k . '="'.$v.'" ';
        }

        $output .= 'name="'.$name;

        if($arrayable)
            $output .= '[]';

        $output .= '" ';

        $output .= 'id="'.$name.'" ';

        $output .= 'value="'.$value.'" ';

        $output .= '/>';

        return $output;
    } 

    public function processTextarea($name,$input) {
     
        $label = $input['label'];
        $attr = count($input['attr'])?$input['attr']:array();
        $value = $input['value'];

        if(!empty($label))
            $output .= '<label for="'.$name.'">'.$label.'</label>';

        $output .= '<textarea ';

        $output .= 'name="'.$name.'" ';

        foreach($attr as $k=>$v) {

            $output .= $k . '="'.$v.'" ';

        }

        $output .= 'id="'.$name.'" >';

        $output .= $value;

        $output .= '</textarea>';

        return $output;
    }

    public function processSelect($name,$input) {

	$label = $input['label'];
        $attr = count($input['attr'])?$input['attr']:array();
        $value = $input['value'];
        $choices = $input['choices'];
        $add_empty = $input['add_empty'];


        $output .= '<label for="'.$name.'">'.$label.'</label>';

        if($add_empty)
            $choices = array_merge(array(''),$choices);

        foreach($choices as $choice) {
            if(is_array($choice)) {
                $k = key($choice);
                $v = $choice[$k];
            }
            else {
                $k = $choice;
                $v = $choice;
            }

            $options .= '<option value="'.$k.'"';

            if($value == $k)
                $options .= 'selected="selected" ';

            $options .= '>'.$v.'</option>';

        }

        $output .= '<select name="'.$name.'"';

        $output .= 'id="'.$name.'" ';

        foreach($attr as $k=>$v) {

            $output .= $k . '="'.$v.'" ';

        }

        $output .='>'.$options.'</select>';

        return $output;
    }

    public function processCombobox($input) {

        $label = $input['label'];
        $boxes = count($input['boxes'])?$input['boxes']:array();

        $output .= '<fieldset><legend>'.$label.'</legend>';

        foreach($boxes as $name=>$box) {
            
            $checkbox = array_merge($box,array('type'=>'checkbox'));
            
            $output .= $this->processRegInput($name,$checkbox);
        }

        $output .= '</fieldset>';

        return $output;

    }

    public function processRadiogroup($name,$input) {

        $label = $input['label'];
        $radios = count($input['radios'])?$input['radios']:array();

        $output .= '<fieldset><legend>'.$label.'</legend>';

        foreach($radios as $radio) {

            $radioinput = array_merge($radio,array('type'=>'radio'));

            $output .= $this->processRegInput($name,$radioinput);
        }

        $output .= '</fieldset>';

        return $output;
    }
}
