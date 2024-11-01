jQuery.livescore =
{
    showLoading: function(wrapper){
        jQuery(wrapper).append('<div class="f-loading"></div>');
    },
    
    loadLatestDailyEvents: function ()
    {
        var data = {
            action: 'liveScoreLatestDailyEvents',
            sport_id: jQuery('#sport_id').val(),
        };
        jQuery.ajaxSetup({ cache: false });
        jQuery.get(ajaxurl, data, function (result) {
            jQuery('#daily_events').html(result);
            jQuery.livescore.loadFixtureScores();
        });
    },
    
    loadFixtureScores: function ()
    {
        if(jQuery('#event_id').length > 0)
        {
            this.showLoading('#fixture_scores');
            var data = {
                action: 'liveScoreFixtureScores',
                event_id: jQuery('#event_id').val(),
            };
            jQuery.ajaxSetup({ cache: false });
            jQuery.get(ajaxurl, data, function (result) {
                jQuery('#fixture_scores').html(result);
            });
        }
        else
        {
            jQuery('#fixture_scores').html('');
        }
    },
    
    loadTeamRoster: function(team_id)
    {
        this.showLoading('#team_detail');
        var data = {
            action: 'liveScoreTeamRoster',
            team_id: team_id
        };
        data = jQuery.param(data);
        if(jQuery('#team-roster .current_sortable').length > 0)
        {
            var sort_by = jQuery('.current_sortable').data('sortby');
            var sort_type = jQuery('.current_sortable').data('sorttype');
            data += '&sort_by=' + sort_by + '&sort_type=' + sort_type;
        }
        jQuery.ajaxSetup({ cache: false });
        jQuery.get(ajaxurl, data, function (result) {
            jQuery('#team_detail').html(result);
            jQuery.livescore.sortingLayout("#team-roster");
        });
    },
    
    loadTeamSchedule: function(team_id)
    {
        this.showLoading('#team_detail');
        var data = {
            action: 'liveScoreTeamSchedule',
            team_id: team_id,
        };
        jQuery.ajaxSetup({ cache: false });
        jQuery.get(ajaxurl, data, function (result) {
            jQuery('#team_detail').html(result);
            jQuery('.flexslider').flexslider({
                animation: "slide",
                animationLoop: false,
                itemWidth: 80,
                itemMargin: 0,
                pausePlay: false,
                controlNav: false
            });
        });
    },
    
    loadTeamNews: function(team_id, link)
    {
        this.showLoading('#team_detail');
        var data = {
            action: 'liveScoreTeamNews',
            team_id: team_id,
        };
        if(typeof link != 'undefined' && link != '')
        {
            data['link'] = link;
        }
        jQuery.ajaxSetup({ cache: false });
        jQuery.post(ajaxurl, data, function (result) {
            jQuery('#team_detail').html(result);
            jQuery(document).on('click', '#player_news_paging li a', function(e){
                e.preventDefault();
                e.stopPropagation();
                jQuery.livescore.loadTeamNews(team_id, jQuery(this).attr('href'));
            })
        });
    },
    
    loadTeamStatistic: function(team_id, page)
    {
        this.showLoading('#team_detail');
        var data = {
            action: 'liveScoreTeamStatistic',
            team_id: team_id,
        };
        data = jQuery.param(data);
        if(jQuery('#team-stats #form_filter_statistic').length > 0)
        {
            data = jQuery('#form_filter_statistic').serialize() + '&' + data;
        }
        if(jQuery('#team-stats .current_sortable').length > 0)
        {
            var sort_by = jQuery('.current_sortable').data('sortby');
            var sort_type = jQuery('.current_sortable').data('sorttype');
            var sort_scoring_id = jQuery('.current_sortable').data('scoring_id');
            data += '&sort_by=' + sort_by + '&sort_type=' + sort_type;
            if(typeof sort_scoring_id != 'undefined')
            {
                data += '&sort_scoring_id=' + sort_scoring_id;
            }
        }
        if(typeof page != 'undefined')
        {
            data += '&page=' + page;
        }
        jQuery.ajaxSetup({ cache: false });
        jQuery.get(ajaxurl, data, function (result) {
            jQuery('#team_detail').html(result);
            jQuery.livescore.sortingLayout("#team-stats");
        });
    },
    
    sortingLayout: function(wrapper)
    {
        if(jQuery(wrapper + ' .current_sortable').length > 0)
        {
            var item = jQuery(wrapper + ' .current_sortable');
            if(item.data('sorttype') == "asc")
            {
                item.addClass('sort-asc');
            }
            else if(item.data('sorttype') == "desc")
            {
                item.addClass('sort-desc');
            }

            var item_index = jQuery(wrapper + ' .current_sortable').index();
            jQuery(wrapper + ' table tbody tr').each(function(){
                jQuery(this).find('td').eq(item_index).addClass('sorted');
            });
        }
    },
    
    showSchedule: function(key)
    {
        jQuery('.team-schedule').hide();
        jQuery('#schedule_' + key).show();
        jQuery('.stats-slider-date').removeClass('active');
        jQuery('#month_' + key).addClass('active');
    },
    
    loadTeamInjuries: function(team_id)
    {
        this.showLoading('#team_detail');
        var data = {
            action: 'liveScoreTeamInjuries',
            team_id: team_id,
        };
        jQuery.ajaxSetup({ cache: false });
        jQuery.get(ajaxurl, data, function (result) {
            jQuery('#team_detail').html(result);
        });
    },
};