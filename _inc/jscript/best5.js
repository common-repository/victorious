var result_select_id = "";
var result_select_page = "";
jQuery.best5 =
{
    //////////////////////////////////////////result//////////////////////////////////////////
    initBest5Result: function()
    {
        var is_live = jQuery('#is_live').val();

        //check live
        if(is_live == 1)
        {
            jQuery.best5.liveEntriesResult();
            setInterval(function(){ 
                jQuery.best5.liveEntriesResult();
            }, 240000);
        }
        else
        {
            jQuery.best5.loadResult();
        }
        
        //result detail
        jQuery(document).on('click', '#table_standing tr', function(){
            result_select_id = jQuery(this).data('id');
            jQuery('#table_standing tr').removeClass('active');
            jQuery(this).addClass('active');
            var user_id = jQuery(this).data('user_id');
            var entry_number = jQuery(this).data('entry_number');
            jQuery.best5.loadResultDetail(user_id, entry_number);
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
            action: 'best5LoadResult',
            page: typeof page != 'undefined' ? page : 1,
            league_id: league_id,
            user_id: jQuery('#user_id').val(),
            entry_number: entry_number,
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery('#result').html("");
            jQuery('#f-live-scoring-leaderboard').html(result);
            if(result_select_id != "" && typeof result_select_id != "undefined")
            {
                jQuery('#' + result_select_id).trigger('click');
            }
            else
            {
                jQuery('#table_standing tbody tr:first').trigger('click');
            }
        });
    },
    
    loadResultDetail: function (user_id, entry_number)
    {
        var league_id = jQuery('#league_id').val();
        
        jQuery.global.showLoading('#result');
        var data = {
            action: 'best5LoadResultDetail',
            league_id: league_id,
            user_id: user_id,
            entry_number: entry_number
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
            jQuery.best5.loadResult();
        });
    },
};