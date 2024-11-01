var result_select_id = "";
var result_select_page = "";
jQuery.bothteamstoscore =
{
    initBothTeamsToScore: function()
    {
        //submit data
        jQuery(document).on('click', '#btnSubmit', function(){
            if(jQuery.global.pickSelected())
            {
                jQuery.global.disableButton('btnSubmit', wpfs['working'] + '...');

                //submit data
                jQuery.post(ajaxurl, 'action=submitBothTeamsToScore&' + jQuery('#formData').serialize(), function(result) {
                    var json = jQuery.parseJSON(result);
                    if(json.success == 0 && json.redirect)
                    {
                        document.location = json.redirect;
                    }
                    else if(json.success == 0)
                    {
                        jQuery.global.enableButton('btnSubmit', wpfs['enter']);
                        alert(json.message);
                    }
                    else
                    {
                        document.location = json.redirect;
                    }
                })
            }
        })
    },
    
    initBothTeamsToScoreResult: function()
    {
        var is_live = jQuery('#is_live').val();

        //check live
        if(is_live == 1)
        {
            jQuery.bothteamstoscore.liveEntriesResult();
            setInterval(function(){ 
                jQuery.bothteamstoscore.liveEntriesResult();
            }, 60000);
        }
        else
        {
            jQuery.bothteamstoscore.loadResult();
        }
        
        //result detail
        jQuery(document).on('click', '#table_standing tr', function(){
            result_select_id = jQuery(this).attr('id');
            jQuery('#table_standing tr').removeClass('active');
            jQuery(this).addClass('active');
            var opponent_id = jQuery(this).data('user_id');
            var opponent_entry_number = jQuery(this).data('entry_number');
            jQuery.bothteamstoscore.loadResultDetail(opponent_id, opponent_entry_number);
        })
    },
    
    loadResult: function (page)
    {
        var league_id = jQuery('#league_id').val();
        var entry_number = jQuery('#entry_number').val();
        if(typeof page != "undefined")
        {
            result_select_id = "";
            result_select_page = page;
        }
        if(result_select_page != "")
        {
            page = result_select_page;
        }
        jQuery.global.showLoading('#wrapper_standing');
        var data = {
            action: 'bothteamstoscoreLoadResult',
            page: typeof page != 'undefined' ? page : 1,
            league_id: league_id,
            user_id: jQuery('#user_id').val(),
            entry_number: entry_number,
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery('#result').html("");
            jQuery('#standing').html(result);
            if(result_select_id != "")
            {
                jQuery('#' + result_select_id).trigger('click');
            }
            else
            {
                jQuery('#table_standing tbody tr:first').trigger('click');
            }
        });
    },
    
    loadResultDetail: function (opponent_id, opponent_entry_number)
    {
        var league_id = jQuery('#league_id').val();
        var user_id = jQuery('#user_id').val();
        var entry_number = jQuery('#entry_number').val();
        
        jQuery.global.showLoading('#result');
        var data = {
            action: 'bothteamstoscoreLoadResultDetail',
            league_id: league_id,
            user_id: user_id,
            entry_number: entry_number,
            opponent_id: opponent_id,
            opponent_entry_number: opponent_entry_number,
            week: jQuery('#result_week').val()
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery('#result').html(result);
        });
    },
    
    liveEntriesResult: function ()
    {
        var league_id = jQuery('#league_id').val();
        var data = {
            action: 'liveEntriesResult',
            leagueID: league_id
        };
        jQuery.post(ajaxurl, data, function () {
            jQuery.bothteamstoscore.loadResult();
        });
    },
};