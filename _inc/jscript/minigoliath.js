jQuery.minigoliath =
{
    initMiniGoliath: function()
    {
        //submit data
        jQuery(document).on('click', '#btnSubmit', function(){
            jQuery.global.disableButton('btnSubmit', wpfs['working'] + '...');

            //submit data
            jQuery.post(ajaxurl, 'action=submitMiniGoliath&' + jQuery('#formData').serialize(), function(result) {
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
        })
    },
    
    initMiniGoliathResult: function()
    {
        var is_live = jQuery('#is_live').val();
        if (is_live == 1)
        {
            jQuery.minigoliath.liveEntriesResult();
            setInterval(function () {
                jQuery.minigoliath.liveEntriesResult()
            }, 300000);
        }
        else
        {
            jQuery.minigoliath.loadResult();
        }
        
        //load standing type
        jQuery(document).on('change', '#standing_type', function(){
            if(jQuery(this).val() == 3)
            {
                jQuery('.for_stats').show();
                jQuery.minigoliath.loadContestStats();
            }
            else
            {
                jQuery('.for_stats').hide();
                jQuery.minigoliath.loadResult();
            }
        })
        
        //load result detail
        jQuery(document).on('click', '.user_result_item', function(){
            jQuery('.user_result_item').removeClass('selected');
            jQuery(this).addClass('selected');
            
            var opponent_id = jQuery(this).data('user_id');
            var opponent_entry_number = jQuery(this).data('entry_number');
            jQuery.minigoliath.loadResultDetail(opponent_id, opponent_entry_number);
        })
        
        //paging
        jQuery(document).on('click', '#paging_ranking .dib', function(){
            var page = jQuery(this).data('page');
            jQuery.minigoliath.loadResult(page);
        })
        
        //load game by week
        jQuery.minigoliath.loadGameByWeek();
        jQuery(document).on('change', '.select_week', function(){
            jQuery.minigoliath.loadGameByWeek();
            jQuery.minigoliath.loadContestStats();
        })
        jQuery(document).on('change', '.select_game', function(){
            jQuery.minigoliath.loadContestStats();
        })
    },
    
    loadResult: function (page)
    {
        var standing_type = jQuery('#standing_type').val();
        var league_id = jQuery('#league_id').val();
        var entry_number = jQuery('#entry_number').val();
        
        jQuery.global.showLoading('#result_rank');
        var data = {
            action: 'minigoliathLoadResult',
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
            action: 'minigoliathLoadResultDetail',
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
            jQuery.minigoliath.loadResult();
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