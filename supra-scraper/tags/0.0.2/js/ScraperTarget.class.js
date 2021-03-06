SupraScraper.ScraperTarget = (function() { 

    var currMethod;

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

    var handleIngestion = function() {

      switch(currMethod) {
        case 'text': 
          handleTextIngestion();
        break;
        case 'url':
          handleUrlIngestion();
        break; 
        case 'csv':
          handleCsvIngestion();
        break;
      }
    }

    var handleTextIngestion = function() {
      data = $(textSelector).val();
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

    var handleUrlIngestion = function() {
      data = $(urlSelector).val();
      $.ajax({
        type: "POST",
        data: {
          action: 'supra_scraper',
          command: 'ingestion-by-url',
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
      
      $.ajax({
        type: "POST",
        data: {
          action: 'supra_scraper',
          command: 'ingestion-by-csv',
          args: dataC
        },
        url: ajaxurl,
        success: function(rsp) {
          handleResponse(rsp);
          $('#patience').hide(); 
        }
      });
    }

    var handleResponse = function(rsp) {
      rsp = $.parseJSON(rsp); 
      for(aErrK in rsp.aErrs) {
        $('#aErrList').append('<li>' + rsp.aErrs[aErrK] + '</li>');
      }
      for(debugK in rsp.debug) {
        $('#debugList').append('<li>' + rsp.debug[debugK] + '</li>');
      }
    }

    return {
        applyBinding: function() {
            applyBinding();
        }
    }
})();
