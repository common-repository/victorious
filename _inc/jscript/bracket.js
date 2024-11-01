var result_select_id = "";
var result_select_page = "";
jQuery.bracket =
{
    initBracket: function()
    {
        jQuery(document).on('click', '.item_action_button', function(){
            var id = jQuery(this).closest('.item').data('id');
            if(jQuery(this).hasClass('item_action_add'))
            {
                jQuery.bracket.addPick(id);
            }
            else
            {
                jQuery.bracket.removePick(id);
            }
            jQuery.bracket.checkMaxPick();
        })
        
        //submit data
        jQuery(document).on('click', '#btnSubmit', function(){
            jQuery.global.disableButton('btnSubmit', wpfs['working'] + '...');
            var ids = [];
            jQuery('.group_item .item.selected').each(function(){
                ids.push(jQuery(this).data('id'));
            })
            jQuery('#team_ids').val(ids.join(','));
            
            //submit data
            jQuery.post(ajaxurl, 'action=submitPickBracket&' + jQuery('#formData').serialize(), function(result) {
                var json = jQuery.parseJSON(result);
                if(json.success == 0)
                {
                    jQuery.global.enableButton('btnSubmit', wpfs['enter']);
                    alert(json.message);
                }
                else
                {
                    document.location = json.redirect;
                }
            })
        })
    },
    
    initBracketResult: function()
    {
        var is_live = jQuery('#is_live').val();
        var league_id = jQuery('#league_id').val();
        var user_id = jQuery('#user_id').val();
        var entry_number = jQuery('#entry_number').val();

        //check live
        if(is_live == 1)
        {
            jQuery.bracket.liveEntriesResult();
            setInterval(function(){ 
                jQuery.bracket.liveEntriesResult();
            }, 300000);
        }
        else
        {
            jQuery.bracket.loadResult();
        }
        
        //change week
        jQuery(document).on('change', '#result_week', function(){
            jQuery.bracket.loadResult();
        })

        //result detail
        jQuery(document).on('click', '#result_rank tr', function(){
            result_select_id = jQuery(this).attr('id');
            jQuery('#result_rank tr').removeClass('active');
            jQuery(this).addClass('active');
            var user_id = jQuery(this).data('user_id');
            var entry_number = jQuery(this).data('entry_number');
            jQuery.bracket.loadResultDetail(user_id, entry_number);
        })
    },
    
    addPick: function(id)
    {
        var parent = jQuery('#item_' + id);
        jQuery('#item_action_add_' + id).hide();
        jQuery('#item_action_remove_' + id).show();
        parent.addClass('selected');
    },
    
    removePick: function(id)
    {
        var parent = jQuery('#item_' + id);
        jQuery('#item_action_add_' + id).show();
        jQuery('#item_action_remove_' + id).hide();
        parent.removeClass('selected');
    },
    
    checkMaxPick: function()
    {
        var max_pick = 2;
        jQuery('.group_item').each(function(){
            jQuery(this).find('.item_action_add').closest('.item_action').show();
            if(jQuery(this).find('.item_action_remove:visible').length >= max_pick)
            {
                jQuery(this).find('.item_action_add:visible').closest('.item_action').hide();
            }
        });
    },
    
    setDefaultPicks: function(picks)
    {
        if(typeof picks == 'undefined')
        {
            return;
        }
        picks = picks.split(',');
        for(var i in picks)
        {
            var id = picks[i];
            jQuery.bracket.addPick(id);
        }
        jQuery.bracket.checkMaxPick();
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
        jQuery.global.showLoading('#result_rank');
        var data = {
            action: 'bracketLoadResult',
            league_id: league_id,
            entry_number: entry_number,
            page: typeof page != 'undefined' ? page : 1
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery('#result_rank').html(result);
            jQuery(document).on('click', '.user_result_item', function(){
                jQuery('.user_result_item').removeClass('selected');
                jQuery(this).addClass('selected');
                result_select_id = jQuery(this).attr('id');
            })
            if(result_select_id != "")
            {
                jQuery('#' + result_select_id).trigger('click');
            }
            else
            {
                jQuery('#result_rank tbody tr:first').trigger('click');
            }
        });
    },
    
    loadResultDetail: function (user_id, entry_number)
    {
        var league_id = jQuery('#league_id').val();
        
        jQuery.global.showLoading('#result_detail');
        var data = {
            action: 'bracketLoadResultDetail',
            league_id: league_id,
            user_id: user_id,
            entry_number: entry_number,
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery('#result_detail').html(result);
        });
    },
    
    liveEntriesResult: function(pool_id, league_id, entry_number)
    {
         var data = {
            action: 'liveEntriesResult',
            poolID: pool_id,
            leagueID: league_id
        };
        jQuery.post(ajaxurl, data, function(result) {
            jQuery.bracket.loadResult(league_id, entry_number);
        })
    }
};