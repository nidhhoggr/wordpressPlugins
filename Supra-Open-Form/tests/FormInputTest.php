<?php

require('../classes/FormInput.class.php');

$form_input['fart'] = array(
                            'type'=>'text',
                            'label'=>'Fart'
                           );

$form_input['smoking_combo'] = array(
                            'type'=>'combobox',
                            'label'=>'Smoker?',
                            'boxes'=>array(
                                           'smoke_yes'=>array('label'=>'Yes'),
                                           'smoke_no' =>array('label'=>'No')
                                          )
                           );

$form_input['sex'] = array(
                            'type'=>'radiogroup',
                            'label'=>'Sex',
                            'radios'=>array(
                                           array('value'=>'male','label'=>'Male'),
                                           array('value'=>'female','label'=>'Female'),
                                           array('value'=>'transgender','label'=>'Transgender')
                                          )
                           );

$choices = array('goose','rat','mouse','snake','cat');

$form_input['fav_animal'] = array(
                                  'type'=>'select',
                                  'label'=>'Favorite Animal',
                                  'choices'=>$choices,
                                  'attr'=>array('multiple'=>'multiple')
                                 );
$form_input['submit'] = array(
                              'type'=>'submit',
                              'value'=>'Submit'
                             );


$fi = new FormInput($form_input,true);

