SupraScraper.ScraperTarget = (function() { 

    var currMethod;

    var prop = {};

    var urlSelector = 'input[name=sscrap_siteurl]';
    var textSelector = '#sscrap_sitelinkstext';
    var csvSelector = 'select[name=sscrap_sitelinkscsv]';
    var ingestBtnSelector = 'input[name=sscrap_ingest]';
    var updateBtnSelector = 'input[name=sscrap_submit]';
    var updateFormSelector = 'form[name=sscrap_form]';
   
    var applyBinding = function() { 

      $(urlSelector).live('click',function() {
        currMethod = 'url';
        $(urlSelector).removeClass('appearDisabled');
        $(textSelector).addClass('appearDisabled');
        $(csvSelector).addClass('appearDisabled');
      });

      $(textSelector).live('click',function() {
        currMethod = 'text';
        $(urlSelector).addClass('appearDisabled');
        $(textSelector).removeClass('appearDisabled');
        $(csvSelector).addClass('appearDisabled');
      });

      $(csvSelector).live('click',function() {
        currMethod = 'csv';
        $(urlSelector).addClass('appearDisabled');
        $(textSelector).addClass('appearDisabled');
        $(csvSelector).removeClass('appearDisabled');
      });

      $(ingestBtnSelector).live('click',function(e) {
        e.preventDefault();
        handleIngestion();  
      });

      $(updateBtnSelector).live('click',function(e) {
        e.preventDefault();
        handleUpdate();
      });
  
      $(csvSelector).on('change', function() {
        handleFileHasPosts($(this).val());
        setCronJobUrls($(this).val());
      });
    }

    var handleUpdate = function() {

      formdata = $(updateFormSelector).serialize();
       
      $.ajax({
        type: "POST",
        data: {
          action: 'supra_scraper',
          command: 'ingestion-update',
          args: formdata
        },
        url: ajaxurl,
        success: function(rsp) {
  
          rsp = $.parseJSON(rsp);
          $('#update_msg').html(rsp.msg); 
          setTimeout(function() {  
           $('#update_msg').fadeOut();
           setTimeout(function() {
             $('#update_msg').html(null);
             $('#update_msg').show();
           },2000);
          }, 2000);
        }
      });
    } 

    var handleFileHasPosts = function(fileKey) {

      $('input[name=sscrap_targetupdate').attr('checked', false);


      $.ajax({
        type: "POST",
        data: {
          action: 'supra_scraper',
          command: 'file-has-posts',
          args: fileKey
        },
        url: ajaxurl,
        success: function(rsp) {
          pRsp = $.parseJSON(rsp);
          if(pRsp) {
            $('#updateExisting').show();
            if(pRsp.debug)
            {   
                handleResponse(rsp); 
            }
          }
          else {
            $('#updateExisting').hide();
          }
        }
      });

    }

    var handleIngestion = function() {

      switch(currMethod) {
        case 'text': 
          handleTextIngestion();
        break;
        case 'csv':
          handleCsvIngestion();
        break;
      }
    }

    var handleTextIngestion = function() {
      data = $(textSelector).val();

      $('#patience').show();
      $('#debugList').html(null);
      $('#aErrList').html(null);

      $.ajax({
        type: "POST",
        data: {
          action: 'supra_scraper',
          command: 'ingestion-by-text',
          args: data
        },
        url: ajaxurl,
        success: function(rsp) {
          handleResponse(rsp);
        }
      });
    } 

    var handleCsvIngestion = function() {
      dataC = $(csvSelector).val();

      $('#patience').show();
      $('#debugList').html(null);
      $('#aErrList').html(null);
      update = $('input[name=sscrap_targetupdate]').is(':checked'); 

      $.ajax({
        type: "POST",
        data: {
          action: 'supra_scraper',
          command: 'ingestion-by-csv',
          args: {
            dataC: dataC,
            update: update
          }
        },
        url: ajaxurl,
        success: function(rsp) {
          handleResponse(rsp);
        }
      });
    }

    var handleResponse = function(rsp) {
      rsp = $.parseJSON(rsp); 

      ssMain.scrollToEl($('#patience'), function(rsp) {

          console.log(rsp);

          for(aErrK in rsp.aErrs) {
              $('#aErrList').append('<li>' + rsp.aErrs[aErrK] + '</li>');
          }
          for(debugK in rsp.debug) {
              $('#debugList').append('<li>' + rsp.debug[debugK] + '</li>');
          }
      }, rsp);

      $('#patience').hide(); 
    }

    var setCronJobUrls = function(fileKey) {
      $.ajax({
        type: "POST",
        data: {
          action: 'supra_scraper',
          command: 'get-cron-job-url',
          args: {
            fileKey: fileKey
          }
        },
        url: ajaxurl,
        success: function(rsp) {
          rsp = $.parseJSON(rsp);
          $('#cron_job_url').html(null);
          $('#cron_job_url').append('<li>Create</li><li class="cron-link">' + rsp.create + '</li>');
          $('#cron_job_url').append('<li>Update</li><li class="cron-link">' + rsp.update + '</li>');
        }
      })
    } 

    return {
        applyBinding: function() {
            applyBinding();
        },
        init: function() {
            ssMain = SupraScraper.Main(); 
            applyBinding(); 
        },
    }
})();
