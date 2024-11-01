jQuery.stats =
{
    initStat: function(){
        jQuery.stats.loadStatsSportInfo();
        
        jQuery(document).on('click', '.tbl-wrapper table thead th', function(){
            jQuery('#page_value').val('');
            jQuery('#sort_name').val(jQuery(this).attr('data-sort_name'));
            var sort_value = jQuery(this).attr('data-sort_value');
            if(jQuery(this).attr('data-sort_value') == '' || sort_value == 'desc')
            {
                sort_value = 'asc';
            }
            else
            {
                sort_value = 'desc';
            }
            jQuery('#sort_value').val(sort_value);
            jQuery.stats.getStat();
        })
        
        jQuery(document).on('change', '.info_item select', function(){
            if(jQuery(this).attr('name') == 'pool_id')
            {
                jQuery.stats.loadTeamByPool();
            }
            jQuery('#page_value').val('');
            jQuery.stats.getStat()
        })
        
        //pagination
        jQuery(document).on('click', '#stats-page .page-item', function(){
            jQuery('#page_value').val(jQuery(this).attr('data-page'));
            jQuery.stats.getStat();
        })
    },
    
    loadTeamByPool:function()
    {
        var cb_pool = jQuery('#cb_pool');
        var teams = cb_pool.find('option:selected').data('teams');
        if(teams != '')
        {
            teams = teams.split(',');
            jQuery('#cb_team option').hide();
            jQuery('#cb_team option').each(function(){
                if(jQuery(this).attr('value') == 0 || teams.indexOf(jQuery(this).attr('value')) != -1)
                {
                    jQuery(this).show();
                }
            })
            jQuery('#cb_team').val(0);
        }
    },

    getStat: function ()
    {
        jQuery('.f-loading').show();
        jQuery.post(ajaxurl, 'action=loadStatsData&' + jQuery('#form_stats').serialize(), function (data){
            jQuery('#stats_data').html(data);
            jQuery('.f-loading').hide();
        })
    },
    
    loadStatsSportInfo: function()
    {
        jQuery('.f-loading').show();
        jQuery('#page_value').val('');
        jQuery.post(ajaxurl, 'action=loadStatsSportInfo&sport_id=' + jQuery('#sports').val(), function (data){
            jQuery('#sport_info').html(data);
            jQuery.stats.loadTeamByPool();
            jQuery.stats.getStat();
        })
    },
    
    initRugbyStat: function()
    {
        jQuery('#page_value').val('');
        jQuery('#sort_name').val('value');
        jQuery('#sort_value').val('desc');
        jQuery.stats.getRugbyStat()
        
        jQuery(document).on('click', '#stats-table thead th.data_filter', function(){
            jQuery('#page_value').val('');
            jQuery('#sort_name').val(jQuery(this).attr('data-sort_name'));
            var sort_value = jQuery(this).attr('data-sort_value');
            if(jQuery(this).attr('data-sort_value') == '' || sort_value == 'desc')
            {
                sort_value = 'asc';
            }
            else
            {
                sort_value = 'desc';
            }
            jQuery('#sort_value').val(sort_value);
            jQuery.stats.getRugbyStat();
        })
        
        jQuery(document).on('change', '.info_item select', function(){
            var sort_name = jQuery('#scoring_category_id').val() > 0 ? jQuery('#scoring_category_id').val() : 'value';
            jQuery('#page_value').val('');
            jQuery('#sort_name').val(sort_name);
            jQuery('#sort_value').val('desc');
            jQuery.stats.getRugbyStat();
        })
        
        //search
        var timer;
        var timeout = 500;

        jQuery('#search_keyword').keyup(function(){
            clearTimeout(timer);
            timer = setTimeout(function(){
                jQuery.stats.getRugbyStat();
            }, timeout);
        });
        
        //pagination
        jQuery(document).on('click', '#stats-page .page-item', function(){
            jQuery('#page_value').val(jQuery(this).attr('data-page'));
            jQuery.stats.getRugbyStat();
        })
    },
    
    getRugbyStat: function ()
    {
        jQuery('.f-loading').show();
        jQuery.post(ajaxurl, 'action=rugbyLoadStatsData&' + jQuery('#form_stats').serialize(), function (data){
            jQuery('#stats_data').html(data);
            jQuery('.f-loading').hide();
        })
    },
    
    playerInfo: function (player_id)
    {
        var poolID = jQuery('#league_id option:selected').data('pool_id');
        var orgID = jQuery('#league_id option:selected').data('org_id');
        jQuery.playerdraft.showDialog('#dlgInfo', jQuery.playerdraft.loading());
        jQuery.post(ajaxurl, "action=loadPlayerStatistics&orgID=" + orgID + '&playerID=' + player_id + '&poolID=' + poolID, function (result) {
            jQuery('#dlgInfo .f-body').html(result);
            jQuery(".f-player-stats-lightbox").tabs({active: 0});
            
            //check show remove add button
            jQuery('.f-player-stats-lightbox #btnRemove').hide();
            jQuery('.f-player-stats-lightbox #btnAdd').hide();
            
            //show game
            if(jQuery('#playerGame').length > 0)
            {
               // jQuery('#playerGame').html(player.teamName1 + ' vs ' + player.teamName2);
            }
            
            //load player news from google
            if(jQuery('#playerNews').data('google') == 1)
            {
                jQuery.playerdraft.loadPlayerNews();
            }
        })
    },
}