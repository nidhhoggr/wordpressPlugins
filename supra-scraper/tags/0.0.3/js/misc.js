$(function() {

    $('#delete_upload').live('click', function() {

        filename_key = $(this).data('key');

        $.ajax({
          type: 'POST',
          data: {'action':'supra_scraper','command':'delete_file','args':filename_key},
          url: ajaxurl,
          success: function(msg){
              $('#supra_scraper_upload_forms').html(msg);
          }
        });
    });

    $('#download_upload').live('click', function() {

        var file = $(this).data('file');

        $.ajax({
          type: 'POST',
          data: {'action':'supra_scraper','command':'download_file','args':file},
          url: ajaxurl,
          success: function(msg){
              $('#supra_scraper_preview').html(msg);
          }
        });
    });

    setTimeout(function() { 
      $('input[name=sscrap_storemeta]').trigger('change');
      $('input[name=sscrap_ffb]').trigger('change');
      $('input[name=sscrap_randomize_is]').trigger('change');
    }, 200);

    $('input[name=sscrap_storemeta]').live('change', function() {

      if($(this).prop('checked')) {
        $('input[name=sscrap_pmtitle]').prop('disabled',false);
        $('input[name=sscrap_pmkeys]').prop('disabled',false);
        $('input[name=sscrap_pmdesc]').prop('disabled',false);
      }
      else {
        $('input[name=sscrap_pmtitle]').prop('disabled',true);
        $('input[name=sscrap_pmkeys]').prop('disabled',true);
        $('input[name=sscrap_pmdesc]').prop('disabled',true );
      }
    });

    $('input[name=sscrap_randomize_is]').live('change', function() {

      if($(this).prop('checked')) {
        $('input[name=sscrap_randomize_min_int]').prop('disabled',false);
        $('input[name=sscrap_randomize_max_int]').prop('disabled',false);
      }
      else {
        $('input[name=sscrap_randomize_min_int]').prop('disabled',true);
        $('input[name=sscrap_randomize_max_int]').prop('disabled',true);
      }
    });


    $('input[name=sscrap_ffb]').live('change', function() {

      if($(this).prop('checked')) 
        $('input[name=sscrap_ffb_pagewidth]').prop('disabled',false);
      else 
        $('input[name=sscrap_ffb_pagewidth]').prop('disabled',true);
    });

});
