<?php
require_once(dirname(__FILE__).'/UsefulDb/ORM.php');
require_once(dirname(__FILE__).'/Form.class.php');
require_once(dirname(__FILE__).'/FormInput.class.php');

class FormSubmission {

    private $db, $form_input, $plugin_bridge, $form, $form_id, $last_submission_id, $table;

    public function __construct($form = null) {

        $this->db = new ORM(DB_NAME,DB_HOST,DB_USER,DB_PASSWORD);

        $this->form_input = new FormInput();

        $this->plugin_bridge = new PluginBridge();
        $this->table = $this->plugin_bridge->getTablePrefix('open_form_submissions');

        if(!empty($form))
            $this->form_id = $form;
        else 
            $this->form = new Form();
    }

    public function setFormId($form_id) {
        $this->form_id = $form_id;
    }

    public function submitForm($request) {

        $form_input = $request['form_input'];

        if(!is_array($form_input))
            parse_str($form_input,$submission);

        //form id must be the last element
        $form_id = array_pop($submission);
 
        $this->insertSubmission($form_id,$submission);

        $this->form_id = $form_id;

        $this->wp_submission_response();

        //$this->getSubmission($this->last_submission_id);
    }

    private function insertSubmission($form_id,$submission) {

        $submission = $this->mergeAssocLabels($form_id,$submission);

        $submission = base64_encode(serialize($submission));

        $this->db->execute("
                            INSERT INTO ".$this->table."(`id`,`form_id`,`submission`,`datetime`) 
                            VALUES (NULL,".$form_id.",'".$submission."','".time()."')
                          ");

        $this->last_submission_id = $this->db->lastInsertedId();
    }

    private function wp_submission_response() {
        $this->form->setForm($this->form_id);

        $wp_post_id = $this->form->getForm('wp_post_id');

        $notification_email = $this->plugin_bridge->getMetaOption('notify_email');

        if(!empty($notification_email)) {
            $submission = $this->displaySubmissionEmail($this->last_submission_id);
            wp_mail($notification_email,$submission['form_name'] . ' Submission Received',$submission['submission']);
        }

        if(!empty($wp_post_id)) {
            echo json_encode(array('type'=>'redirect','value'=>get_page_link($wp_post_id)));
        }
        else {
            $db_succ_msg = $this->form->getForm('success_msg');
            $def_succ_msg = "Form submission successful";
            $succ_msg = (empty($db_succ_msg)) ? $def_succ_msg : $db_succ_msg;
            $succ_msg = $this->form_input->wrapInput($succ_msg,'success');   
            echo json_encode(array('type'=>'flash','value'=>$succ_msg));
        }
    }

    private function getSubmission($id) {
        $submission = $this->db->findOneBy($this->table,"*",'id ='.$id);

        $submission['submission'] = unserialize(base64_decode($submission['submission']));

        return $submission;
    }

    private function mergeAssocLabels($form_id,$submission) {
        $form = $this->form->getFormById($form_id);

        //get the labels
        foreach($form['inputs'] as $num=>$inputs) {


            foreach($inputs as $name=>$input) {
                if($input['type'] == "combobox") {
                    foreach($input['boxes'] as $b_name=>$box) {
                        foreach($submission as $k=>$v) {
                            if($b_name == $k) {
                                $new_submission['combobox'][$num]['label'] = $input['label'];
                                $new_submission['combobox'][$num]['values'][$b_name] = $box['label'];
                            } 
                        }                                                
                    }
                }
                else if($input['type'] =="radiogroup") {
                    foreach($input['radios'] as $name=>$radio) {
                        foreach($submission as $k=>$v) {
                            if($radio['value'] == $v) {
                                $new_submission[$name]['label'] = $input['label'];
                                $new_submission[$name]['value'] = $radio['label'];
                            }
                        }
                    }
                }
                else {
 
                    $i[$name]['label'] = $input['label'];

                    foreach($submission as $k=>$v) {
                        if($name == $k) {
                            $new_submission[$k]['label'] = $i[$k]['label'];
                            $new_submission[$k]['value'] = $v;
                        }
                    }
                }
            }
        }

        return $new_submission;
    }

    private function getSubmissionsByFormId($id) {
        return $this->db->findBy($this->table,'*','form_id ='.$id);
    }
   
    public function countSubmissionsByFormId($id) {
        return $this->db->findOneBy($this->table,'COUNT(*)','form_id ='.$id);
    }

    public function deleteSubmissionsByFormId($id) {

        $this->db->execute("
                            DELETE FROM ".$this->table."
                            WHERE form_id = '".$id."'
                           ");

    }
 
    private function getSubmissions() {
        return $this->db->find($this->table);
    }

    public function displaySubmissions($form_id) {

        $submissions = $this->getSubmissionsByFormId($form_id);

        foreach((array)$submissions as $submission) {

            $fs_row = '<div class="view_submission" data-submission-id="'.$submission['id'].'">'.$this->toString($submission).'</div>';

            echo $this->form_input->wrapInput($fs_row,'fs_row');
        }

    }
    
    public function displaySubmission($id, $display = true) {

        $submission = $this->getSubmission($id);

        $form_id = $submission['form_id'];

        $this->form->setForm($form_id);

        $form_name = $this->form->getForm('name');

        $submitted .= $this->form_input->wrapInput($form_name,'form_name');
       
        foreach((array)$submission['submission'] as $k=>$input) {
            $input_row = null;

            if($k === "combobox") {
                foreach($input as $key=>$val) {
                    $input_row .= $this->form_input->wrapInput($val['label'],'input_label');
                    $values = implode(',',(array)$val['values']);
                    $input_row .= $this->form_input->wrapInput($values,'input_value');
                }
            }
            else {
                $input_row .= $this->form_input->wrapInput($input['label'],'input_label');
                $input_row .= $this->form_input->wrapInput($input['value'],'input_value');
            }
            $submitted .= $this->form_input->wrapInput($input_row,'input_row');
        }

        if($display)
            echo $submitted;
        else 
            return $submitted;
    }

    public function displaySubmissionEmail($id) {

        $submission = $this->getSubmission($id);

        $form_id = $submission['form_id'];

        $this->form->setForm($form_id);

        $form_name = $this->form->getForm('name');

        $email_is_plain = $this->plugin_bridge->getMetaOption('email_is_plain');

        if($email_is_plain == "true")
            $submission = $this->getPlainTextEmail($form_name,$submission);
        else
            $submission = $this->getHtmlEmail($form_name,$submission);

        $displayable = compact('form_name','submission');
 
        return $displayable;
    }

    private function getHtmlEmail($form_name,$submission) {

        $submitted .= $this->form_input->wrapInput('<h2>'.$form_name.'</h2>','form_name');

        foreach((array)$submission['submission'] as $k=>$input) {
            $input_row = null;

            if($k === "combobox") {
                foreach($input as $key=>$val) {
                    $input_row .= '<b>' . $val['label'] . ':</b> ';
                    $input_row .= '<i>' . implode(',',(array)$val['values']);
                }
            }
            else {
                $input_row .= '<b>' . $input['label'] . ':</b> ';
                $input_row .= '<i>' . $input['value'] . '</i>';
            }
            $submitted .= $this->form_input->wrapInput($input_row,'input_row');
        }

        return $submitted;
    }

    private function getPlainTextEmail($form_name,$submission) {

        $submitted .= "$form_name \r\n\r\n";

        foreach((array)$submission['submission'] as $k=>$input) {
            $input_row = null;

            if($k === "combobox") {
                foreach($input as $key=>$val) {
                    $submitted .= $val['label'] . "\r\n";
                    $submitted .= implode(',',(array)$val['values']) ."\r\n \r\n";
                }
            }
            else {
                $submitted .= $input['label'] . "\r\n";
                $submitted .= $input['value'] . "\r\n \r\n";
            }
        }
        return $submitted;
    }

    private function toString($submission,$field = 'datetime') {
        switch($field) {
            case "datetime":
                $tostring = date('M d, Y h:i a',$submission[$field]);
            break;
        }

        return $tostring;
    }
}
