<?php
require_once(dirname(__FILE__).'/../classes/Form.class.php');

$form = new Form();


$forms = $form->retrieveForms();



foreach($forms as $f) {

    echo $f['name'] . ' id:'.  $f['id'] . '<br />';

}

$form2 = $form->getFormById(2);

echo $form->display($form2);

