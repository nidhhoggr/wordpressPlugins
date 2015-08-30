$ = jQuery.noConflict();

var SupraScraper = {}

$(function() {
    SupraScraper.Tooltips.bindTooltips();
    SupraScraper.ScraperTarget.applyBinding();
});
