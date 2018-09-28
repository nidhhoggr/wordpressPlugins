<?php
require_once(dirname(__FILE__).'/UsefulDb/ORM.php');
require_once(dirname(__FILE__).'/FormInput.class.php');
require_once(dirname(__FILE__).'/FormBuilder.class.php');
require_once(dirname(__FILE__).'/FormSubmission.class.php');
require_once(dirname(__FILE__).'/PluginBridge.class.php');

class Form {

    private $db, $form_input, $form_builder, $form_submission, $table;
    private $form = array();

    public function __construct($id = null) {

        $this->db = new ORM(DB_NAME,DB_HOST,DB_USER,DB_PASSWORD);    
        $this->form_input    = new FormInput();
        $this->form_builder  = new FormBuilder();
        $plugin_bridge = new PluginBridge();
        $this->table = $plugin_bridge->getTablePrefix('open_forms');
        $this->setForm($id);
    }

    public function getForm($key=null) {
        if(!empty($key))
            return $this->form[$key];
        else 
            return $this->form;
    }

    public function setForm($id = null,$form = null) {
        if(!empty($id) && empty($form)) {
            $this->clearForm($id);
            $this->form = $this->getFormById($id);
            $this->loadForm();
        }
        else if(empty($id) && !empty($form)) {
            $this->form = $form;
            $this->loadForm();
        }
    }

    public function hasInstance() {
        return count($this->form);
    }

    //@desc: retrieve all forms from the database
    public function retrieveForms() {
        return $this->db->find($this->table);
    }

    public function displayForms() {
        $forms = $this->retrieveForms();

        foreach((array)$forms as $f) {
            $form_row = '<div class="view_form" data-form-id="'.$f['id'].'">'.$f['name'].'</div>';
            $form_row .= '<div class="edit_form" data-form-id="'.$f['id'].'">edit form</div>';
            $form_row .= '<div class="view_submissions" data-form-id="'.$f['id'].'">view submissions</div>';
            echo $this->form_input->wrapInput($form_row,'form_row');
        }
    }

    //@desc: retrieve the form from the databasei by the id
    public function getFormById($id) {

        $form = $this->db->findOneBy($this->table,"*",'id ='.$id);

        if($form) {
            $form['inputs'] = unserialize(base64_decode($form['inputs']));
            $form['success_msg'] = stripslashes($form['success_msg']);
            $form['form_name'] = stripslashes($form['success_msg']);
        } 

        return $form;
    }

    public function getNextFormId() {
        $id = $this->db->findOneBy($this->table,"id",'1',' ORDER BY id DESC');

        return ++$id;
    }

    public function getFormByName($name) {

        $form = $this->db->findOneBy($this->table,"*","name = '$name'");

        if($form) {
            $form['inputs'] = unserialize(base64_decode($form['inputs']));
            $form['success_msg'] = stripslashes($form['success_msg']);
            $form['form_name'] = stripslashes($form['success_msg']);
        }

        return $form;
    }

    public function deleteForm($id) {

        $fs = new FormSubmission(&$this);

        $fs->deleteSubmissionsByFormId($id);

        $this->db->execute("
                            DELETE FROM ".$this->table."
                            WHERE id = '".$id."'
                           ");
       
    }

    //@desc: clear the form from session
    public function clearForm($form_id) {
        unset($_SESSION['open_form'][$form_id]);
    }

    //@desc: load the form into session
    public function loadForm() {
        $_SESSION['open_form'][$this->getForm('id')] = $this->getForm();
    }
 
    public function clearSession() {
        unset($_SESSION['open_form']);
    }
 
    //@desc: retrieve from from session and store in database
    public function saveForm($request) {

        extract($request);

        $inputs = $_SESSION['open_form'][$form_id]['inputs'];
 
        $success_msg = addslashes($success_msg);
        $form_name = addslashes($form_name);
        
        //sanitize values and lables
        foreach($inputs as $k=>$v) {
            if(!empty($v['label']))
                $inputs[$k]['label'] = addslashes($v['label']);
            if(!empty($v['value']))
                $inputs[$k]['value'] = addslashes($v['value']);
        } 
 
        ksort($inputs);

        $inputs      = base64_encode(serialize($inputs));

        if($this->getFormById($form_id)) {
            $this->db->execute("UPDATE ".$this->table."
                                SET `name` = '".$form_name."',`wp_post_id` = '".$wp_post_id."',`success_msg`='".$success_msg."',`inputs` = '".$inputs."'
                                WHERE id = ".$form_id.";");
        }
        else {
            $this->db->execute("
                                INSERT INTO ".$this->table."(`id`,`name`,`wp_post_id`,`success_msg`,`inputs`) 
                                VALUES (".$form_id.",'".$form_name."','".$wp_post_id."','".$success_msg."','".$inputs."')
                               ");
        }

        return $this->form_input->wrapInput('Form saved successfully','success');
    }

    public function display($form = null,$wrap = true,$editable = false) {

        if(empty($form))
            $form = $this->getForm();

        if($wrap){
            echo $this->form_input->wrapInput($form['name'],'form_name');
            echo $this->wrapInputs($form);
        }
        else {
            echo $this->renderInputs($form['inputs'],$editable);
        }
    }

    //@desc: wrap the inputs into a form element
    public function wrapInputs($form) {

        $output .= '<form name="'.$form['name'].'" id="'.$form['name'].'" class="open_form">';

        $output .= $this->renderInputs($form['inputs']);
 
        $output .= '</form>';

        return $output; 
    }

    //@desc: convert an array of input data into html
    public function renderInputs($inputs,$editable = false) {
        foreach((array)$inputs as $input) {
            $output .= $this->form_input->render($input,$editable);
        }

        return $output;
    }

    //@desc: insert form input into current form session
    public function insertInput($input,$editable = false) {
        $form = $this->getForm();        
        $form['inputs'][] = $input;
        $this->setForm(null,$form);
    }

    public function getInputKeyByName($form_id,$input_name) {

        $inputs = $_SESSION['open_form'][$form_id]['inputs'];

        foreach((array)$inputs as $k=>$v) {

            if($input_name == key($v)) {
                $found = $k;
                break;
            }
        }

        return $found;
    }

    public function getInputByName($form_id,$input_name) {
        $key = $this->getInputKeyByName($form_id,$input_name);
        return $_SESSION['open_form'][$form_id]['inputs'][$key];
    }
}
