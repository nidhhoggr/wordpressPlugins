$ = jQuery.noConflict();

var SupraScraper = {}

SupraScraper.Main = function() {

  _baseCall = function(dataCmd, dataArgs, cb) {

      $.ajax({
        type: 'POST',
        data: {'action':'supra_csv','command': dataCmd,'args': dataArgs},
        url: ajaxurl,
        success: function(msg){
          cb(msg);
        }
      });
  }

  return {
    baseCall: function(dataCmd, dataArgs, cb) {
      _baseCall(dataCmd, dataArgs, cb);
    }
   ,scrollToEl: function(el, cb, args) {
      if(typeof el.offset() !== "undefined") {

        $('html, body').animate({
          scrollTop: el.offset().top
        }, 2000);

      }
      if(cb) cb(args);
    }
  }
}


$(function() {
    SupraScraper.Tooltips.bindTooltips();
    SupraScraper.ScraperTarget.init();
});
