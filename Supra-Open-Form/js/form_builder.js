$( function() {

      var action = 'formBuilder';      

      form_id = $('#form_id').val();

      if(form_id) {
          $('#save_form').val('Update Form');
      }

      $('#add_radio').hide();
      $('#rem_radio').hide();
      $('#add_check').hide();
      $('#rem_check').hide();
      $('#rem_attr').attr('disabled','disabled');
      $('#add_attr').attr('disabled','disabled');
      $('#rem_radio').attr('disabled','disabled');
      $('#add_radio').attr('disabled','disabled');
      $('#rem_check').attr('disabled','disabled');
      $('#add_check').attr('disabled','disabled');

      $('#input_type').live('click', function() {

          input_type = $(this).val();

          $.ajax({
              type: "POST",
              url: ajaxurl,
              data: {select_input_type: true, input_type: input_type, action: action},
              success: function(msg) {

                  if(input_type == "radiogroup") {
                      $('#add_attr').hide(); 
                      $('#rem_attr').hide();
                      $('#add_radio').removeAttr('disabled'); 
                      $('#add_radio').show(); 
                      $('#rem_radio').show(); 
                      $('#add_check').hide();
                      $('#rem_check').hide();
                  }
                  else if(input_type == "combobox") {
                      $('#add_check').removeAttr('disabled');
                      $('#add_check').show();
                      $('#rem_check').show();
                      $('#add_attr').hide();
                      $('#rem_attr').hide();
                      $('#add_radio').hide();
                      $('#rem_radio').hide();
                  }
                  else {
                      $('#add_attr').removeAttr('disabled');
                      $('#add_attr').show();
                      $('#rem_attr').show(); 
                      $('#add_radio').hide();
                      $('#rem_radio').hide();
                      $('#add_check').hide();
                      $('#rem_check').hide();
                  }

                  $('#input_builder').html(msg);
                  $('#add_input').show();
                  $('#update_input').hide();
              }
          });          

      });

      $('#clear_form').click( function() {

          $.ajax({
              type: "POST",
              url: ajaxurl,
              data: {clear_form: true, form_id: form_id, action: action},
              success: function(msg) {
                  $('#form_built').html(null);
                  $('#add_input').show();
                  $('#update_input').hide();
              }
          });

      });

      $('#save_form').click( function() {
          var form_name   = $('#form_name').val();
          var wp_post_id  = $('#wp_post_id').val();
          var success_msg = $('#success_msg').val();

          $.ajax({
              type: "POST",
              url: ajaxurl,
              data: {
                     save_form: true, 
                     form_name: form_name, 
                     wp_post_id: wp_post_id, 
                     success_msg: success_msg, 
                     form_id: form_id, 
                     action: action
              },
              success: function(msg) {
                  $('#notify').html(msg);
              }
          });

      });

      $('#add_attr').click( function() {

          var num = $('.attribute').length;
           
            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: {add_attr: true, input_type: 'attribute', action:action},
                success: function(msg) {
                    $('#input_builder').append(msg);
                }
            });        

          $('#rem_attr').removeAttr('disabled');
      });

      $('#rem_attr').click( function() {

          var num = $('.attribute').length;

          $('.attribute').eq(num-1).remove();

          $('#add_attr').removeAttr('disabled');

          if (num-1 == 0)
              $('#rem_attr').attr('disabled','disabled');
      });

      $('#add_radio').click( function() {

          var num = $('.radio').length;

            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: {add_radio: true, input_type: 'radio', action:action},
                success: function(msg) {
                    $('#input_builder').append(msg);
                }
            });    

          $('#rem_radio').removeAttr('disabled');
      });

      $('#rem_radio').click( function() {

          var num = $('.radio').length;

          $('.radio').eq(num-1).remove();

          $('#add_radio').removeAttr('disabled');

          if (num-1 == 0)
              $('#rem_radio').attr('disabled','disabled');
      });

      $('#add_check').click( function() {

          var num = $('.check').length;

            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: {add_radio: true, input_type: 'check', action:action},
                success: function(msg) {
                    $('#input_builder').append(msg);
                }
            });

          $('#rem_check').removeAttr('disabled');
      });

      $('#rem_check').click( function() {

          var num = $('.check').length;

          $('.check').eq(num-1).remove();

          $('#add_check').removeAttr('disabled');

          if (num-1 == 0)
              $('#rem_check').attr('disabled','disabled');
      });
});
