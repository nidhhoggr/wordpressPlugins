$( function() {

      var action = 'inputCrud';

      $('#update_input').hide();


      $('#add_input').click( function() {

          var input = $('#input_builder').serialize().replace(/%5B%5D/g, '[]');

          $.ajax({
              type: "POST",
              url: ajaxurl,
              data: {
                     add_input: true,
                     action: action,
                     form_id:    form_id,
                     form_input: input,
                     input_type: input_type
              },
              success: function(msg) {
                  $('#form_built').append(msg);
              }
          });

      });

      $('#edit_input').live('click', function() {

          var input_name  = $(this).data('input-name');
          var input_type  = $(this).data('input-type');

                  $('#input_type').val(input_type);
                  $('#input_type').click();

          var ajaxCall = function () {
          $.ajax({
              type: "POST",
              url: ajaxurl,
              data: {
                     edit_input: true,
                     action: action,
                     form_id: form_id,
                     input_name: input_name
              },
              success: function(msg) {
                  $('#input_builder').html(msg);
                  $('#input_type').val(input_type);
                  $('#add_input').hide();
                  $('#update_input').show();
              }
          });

          };
          setTimeout( ajaxCall, 500);
      });

      function showAdders() {

          switch(input_type) {
              case "radiogroup":
                  $('#add_attr').removeAttr('disabled');
                  $('#rem_attr').removeAttr('disabled');
              break;
              case "combobox":
                  $('#add_attr').removeAttr('disabled');
                  $('#rem_attr').removeAttr('disabled');
              break;
              default:
                  $('#add_attr').removeAttr('disabled');
                  $('#rem_attr').removeAttr('disabled');
              break;
          }

      }

      $('#update_input').click( function () {


          input_type  = $('#input_type').val();
          var input_name  = $('#name').val();
          var input_key   = $('#input_key').val();
          var form_input  = $('#input_builder').serialize().replace(/%5B%5D/g, '[]');

          $.ajax({
              type: "POST",
              url: ajaxurl,
              data: {
                     update_input: true,
                     form_id:    form_id,
                     action: action,
                     form_input: form_input,
                     input_name: input_name,
                     input_key: input_key,
                     input_type: input_type
              },
              success: function(msg) {
                  $('#form_built').html(msg);
              }
          });
      });

      $('#delete_input').live('click', function () {

          var input_name  = $(this).data('input-name');
          var input_type  = $('#input_type').val();

          $.ajax({
              type: "POST",
              url: ajaxurl,
              data: {
                     delete_input: true,
                     action: action,
                     form_id: form_id,
                     input_name: input_name
              },
              success: function(msg) {
                  $('#form_built').html(msg);
              }
          });
      });
});
