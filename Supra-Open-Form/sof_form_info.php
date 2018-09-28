<?php
    require_once(dirname(__FILE__).'/classes/Form.class.php');
    require_once(dirname(__FILE__).'/classes/FormSubmission.class.php');
    $form = new Form();
    $fs = new FormSubmission();
    wp_enqueue_script( 'global', plugins_url('/js/global.js', __FILE__), array('jquery') );
    wp_enqueue_script( 'form_submission', plugins_url('/js/form_submission.js', __FILE__) );
    wp_enqueue_style( 'form_buildercss', plugins_url('/css/form_builder.css', __FILE__) );

    $forms = $form->retrieveForms();
?>
<div id="open_form">
  <div id="left_of">
    <table id="sof_info">
      <thead>
        <th>Form</th>  
        <th>Shortcode</th>  
        <th>Submissions</th>  
        <th>delete</th>  
      </thead>
      <tbody> 
      <? 
        foreach((array)$forms as $i=>$f):

          $class = ($i%2) ? "even" : "odd";
        
          $id = $f['id'];

          $count = $fs->countSubmissionsByFormId($id);
      ?>
        <tr id="sof_info_row" class="<?=$class?>">
          <td id="form_name" class="edit_form" data-form-id="<?=$id?>"><?=$f['name']?></td>
          <td id="form_shortcode">[supra-open-form id=<?=$id?>]</td>
          <td id="form_submission_count"><?=$count?> <span class="view_submissions" data-form-id="<?=$f['id']?>">view</span></td>
          <td id="form_name" class="delete_form" data-form-id="<?=$id?>">delete</td>
        </tr>
       <? endforeach; ?>
      <tbody>
    </table>
  </div>
  <div id="right_of">
    <div id="form_submissions_header">Submissions</div>
    <div id="form_submissions"></div>
    <div id="form_submission_header">Submission</div>
    <div id="form_submission"></div>
  </div>
  <div id="cleaner"></div>
</div>
