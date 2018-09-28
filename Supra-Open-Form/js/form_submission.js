$( function() {

      clear_all();

      var action = 'formSubmission';

      $('.open_form').live('submit', function() {
          var input = $(this).serialize();

          $.ajax({
              type: "POST",
              url: ajaxurl,
              data: {process_form: true, form_input: input, action: action},
              success: function(response) {

                  response = JSON.parse(response);
 
                  if(response.type == "redirect") {
                      window.location = response.value;
                  }
                  else if(response.type == "flash") {
                      $('#notify').html(response.value);
                  }
              }
          });
          
          return false;
      });

      $('.view_form').live('click', function() {

          var form_id = $(this).data('form-id');

          if(!view_form) {
              $.ajax({
                  type: "POST",
                  url: ajaxurl,
                  data: {view_form: true, form_id: form_id, action: action},
                  success: function(msg) {
                      show_form(msg);
                  }
              });
          }
          else {
              clear_all();
          }
      });

      $('.edit_form').live('click', function() {

          var form_id = $(this).data('form-id');
 
          $.ajax({
              type: "POST",
              url: ajaxurl,
              data: {edit_form: true, form_id: form_id, action: action},
              success: function(link) {
                  window.location = link;
              }
          });
      });

      $('.delete_form').live('click', function() {

          var form_id = $(this).data('form-id');

          if (!confirm('Are you sure you want to delete?')) {
            return false;
          }
          else {
              $.ajax({
                  type: "POST",
                  url: ajaxurl,
                  data: {delete_form: true, form_id: form_id, action: action},
                  success: function(link) {
                     window.location = link;
                  }
              });
          }
      });


      $('.view_submissions').live('click', function() {

          var form_id = $(this).data('form-id');

          if(!view_submissions) {
              $.ajax({
                  type: "POST",
                  url: ajaxurl,
                  data: {view_submissions: true, form_id: form_id, action: action},
                  success: function(msg) {
                      show_submissions(msg);
                  }
              });
          }
          else {
              clear_submission();
              clear_submissions();
          }
      });

      $('.view_submission').live('click', function() {

          var submission_id = $(this).data('submission-id');

          console.log(submission_id);

          if(!view_submission) {
              $.ajax({
                  type: "POST",
                  url: ajaxurl,
                  data: {view_submission: true, submission_id: submission_id, action: action},
                  success: function(msg) {
                      show_submission(msg);
                  }
              });
          }
          else {
              clear_submission();
          }
      });

});

function show_form(msg) {
    $('#form').html(msg);
    $('#form').show();
    view_form = true;
}

function show_submissions(msg) {
    $('#form_submissions').html(msg);
    $('#form_submissions_header').show();
    $('#form_submissions').show();
    view_submissions = true;
}

function show_submission(msg) {
    $('#form_submission').html(msg);
    $('#form_submission_header').show();
    $('#form_submission').show();
    view_submission = true;
}

function clear_form() {
    $('#form').html(null);
    $('#form').hide();
    view_form = false;
}

function clear_submissions() {
    $('#form_submissions').html(null);
    $('#form_submissions_header').hide();
    $('#form_submissions').hide();
    view_submissions = false;
}

function clear_submission() {
    $('#form_submission').html(null);
    $('#form_submission_header').hide();
    $('#form_submission').hide();
    view_submission = false;
}

function clear_all() {
    clear_submissions();
    clear_submission();
    clear_form();
}
