jQuery.goliath =
{
    initGoliath: function()
    {
        //submit data
        jQuery(document).on('click', '#btnSubmit', function(){
            if(jQuery.goliath.checkValidPass())
            {
                jQuery.global.disableButton('btnSubmit', wpfs['working'] + '...');

                //submit data
                jQuery.post(ajaxurl, 'action=submitGoliath&' + jQuery('#formData').serialize(), function(result) {
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
        
        //check pass
        jQuery(document).on('click', '.fightID', function(){
            return jQuery.goliath.checkAvailablePass();
        })
    },
    
    initGoliathResult: function()
    {
        var is_live = jQuery('#is_live').val();
        if (is_live == 1)
        {
            jQuery.goliath.liveEntriesResult();
            setInterval(function () {
                jQuery.goliath.liveEntriesResult()
            }, 300000);
        }
        else
        {
            jQuery.goliath.loadResult();
        }
        
        //load standing type
        jQuery(document).on('change', '#standing_type', function(){
            if(jQuery(this).val() == 3)
            {
                jQuery('.for_stats').show();
                jQuery.goliath.loadContestStats();
            }
            else
            {
                jQuery('.for_stats').hide();
                jQuery.goliath.loadResult();
            }
        })
        
        //load result detail
        jQuery(document).on('click', '.user_result_item', function(){
            jQuery('.user_result_item').removeClass('selected');
            jQuery(this).addClass('selected');
            
            var opponent_id = jQuery(this).data('user_id');
            var opponent_entry_number = jQuery(this).data('entry_number');
            jQuery.goliath.loadResultDetail(opponent_id, opponent_entry_number);
        })
        
        //paging
        jQuery(document).on('click', '#paging_ranking .dib', function(){
            var page = jQuery(this).data('page');
            jQuery.goliath.loadResult(page);
        })
        
        //load game by week
        jQuery.goliath.loadGameByWeek();
        jQuery(document).on('change', '.select_week', function(){
            jQuery.goliath.loadGameByWeek();
            jQuery.goliath.loadContestStats();
        })
        jQuery(document).on('change', '.select_game', function(){
            jQuery.goliath.loadContestStats();
        })
    },
    
    checkAvailablePass: function()
    {
        var pass = 0;
        var available_pass = parseInt(jQuery('#available_pass').data('value'));
        jQuery(".is_pass").each(function(){
            if(jQuery(this).is(':checked'))
            {
                pass += 1;
            }
        })
        if(jQuery.goliath.checkValidPass())
        {
            jQuery('#available_pass').html(available_pass - pass);
            return true;
        }
        return false;
    },
    
    checkValidPass: function()
    {
        var pass = 0;
        var available_pass = parseInt(jQuery('#available_pass').data('value'));
        jQuery(".is_pass").each(function(){
            if(jQuery(this).is(':checked'))
            {
                pass += 1;
            }
        })
        if(pass > available_pass)
        {
            alert(wpfs['goliath_invalid_pass']);
            return false;
        }
        return true;
    },
    
    loadResult: function (page)
    {
        var standing_type = jQuery('#standing_type').val();
        var league_id = jQuery('#league_id').val();
        var entry_number = jQuery('#entry_number').val();
        
        jQuery.global.showLoading('#result_rank');
        var data = {
            action: 'goliathLoadResult',
            league_id: league_id,
            entry_number: entry_number,
            standing_type: standing_type,
            page: typeof page != 'undefined' ? page : 1
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery('#result_rank').html(result);
            switch(parseInt(standing_type))
            {
                case 1:
                    jQuery('#result_rank table tbody tr:first').trigger('click');
                    break
                case 2:
                    jQuery('#result_detail').html("");
                    break
            }
        });
    },
    
    loadResultDetail: function (opponent_id, opponent_entry_number)
    {
        var league_id = jQuery('#league_id').val();
        var user_id = jQuery('#user_id').val();
        var entry_number = jQuery('#entry_number').val();
        
        jQuery.global.showLoading('#result_detail');
        var data = {
            action: 'goliathLoadResultDetail',
            league_id: league_id,
            user_id: user_id,
            entry_number: entry_number,
            opponent_id: opponent_id,
            opponent_entry_number: opponent_entry_number,
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery('#result_detail').html(result);
        });
    },
    
    liveEntriesResult: function()
    {
        var league_id = jQuery('#league_id').val();
        var data = {
            action: 'liveEntriesResult',
            leagueID: league_id
        };
        jQuery.post(ajaxurl, data, function() {
            jQuery.goliath.loadResult();
        })
    },
    
    loadContestStats: function()
    {
        var league_id = jQuery('#league_id').val();
        var week = jQuery('.select_week').val();
        var fight_id = jQuery('.select_game:visible').val();
        jQuery('#result_detail').html("");
        jQuery.global.showLoading('#result_rank');
        var data = {
            action: 'goliathLoadContestStats',
            league_id: league_id,
            week: week,
            fight_id: fight_id
        };
        jQuery.post(ajaxurl, data, function(result) {
            jQuery('#result_rank').html(result);
        })
    },
    
    loadGameByWeek: function()
    {
        var week = jQuery('.select_week').val();
        jQuery('.select_game').hide();
        jQuery('#select_game_' + week).show();
    }
};