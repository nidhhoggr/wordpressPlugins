SupraScraper.Tooltips = (function() { 

    var bindTooltips = function() {

        $.ajax({
            type: 'POST',
            data: {'action':'supra_scraper','command':'get_tooltips','args':null},
            url: ajaxurl,
            success: function(msg){
                binding(msg); 
            }
        });
    }

    var binding_mapping = {

        'filemgmt_tt':['upload + ol'],

        'contentselector_tt':['contentselector'],

        'postsettings_tt':['post_settings'],
        'autopublish_tt':['auto_publish'],
        'posttype_tt':['post_type'],
        'customposttype_tt':['custom_post_type'],
        'postdefaults_tt':['post_defaults + ol'],

        'storepostmeta_tt':['storepostmeta'],
        'postmetakeys_tt':['postmetakeys'],
 
        'csvsettings_tt':['csv_settings + ol'],

        'debugingestion_tt':['debug_ingestion'],
        'reportissues_tt':['report_issues'],
        'randomize_is_tt':['randomize_is'],
        'randomize_min_tt':['randomize_min_int'],
        'randomize_max_tt':['randomize_max_int'],
        
        'scrapertarget_tt':['scrapertarget + ol'],

        'updateoptions_tt':['updateoptions'],
        'ingest_tt':['ingest'],

        'metakeymapping_tt':['metakeymapping'],
        'targetupdate_tt':['st_targetupdate']
    }

    var binding = function(docs) { 

        $.map(binding_mapping, function(page_elem,tip_elem) { 
 
            tip = ""; 

            $.map(page_elem, function(sel) { 

                tip += $(docs).find('#' + sel).html();
            });
 
           $('#' + tip_elem).qtip({ content: tip });

        })
    }

    return {
        bindTooltips: function() {
            bindTooltips();
        }
    }
})();
