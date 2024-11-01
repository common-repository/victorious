var result_select_id = "";
var result_select_page = "";
var searchTimer;
var searchTimeout = 500;
jQuery.playerdraft =
{
    initPlayerDraft: function()
    {
        this.calculateSalaryRemaining();
        this.checkModifyLineup();

        //load player list
        jQuery.playerdraft.loadDraftPlayerList();
        
        jQuery(".table-sorting").click(function () {
            var item = jQuery(this);
            var sortType = item.data('sort');
            jQuery('.table-sorting').removeClass('active-sort').find('.material-icons').hide();
            item.addClass('active-sort');
            if (sortType == 'asc'){
                item.find('.f-sorted-desc').show();
                sortType = 'desc';
            }
            else{
                item.find('.f-sorted-asc').show();
                sortType = 'asc';
            }
            item.data('sort', sortType);

            //load player list
            jQuery.playerdraft.loadDraftPlayerList();
        });
        
        jQuery(document).on('click', '.btn_add_lineup', function(){
            jQuery.playerdraft.addLineup(jQuery(this).data('id'));
        })
        
        jQuery(document).on('click', '.btn_remove_lineup', function(){
            jQuery.playerdraft.removeLineup(jQuery(this).data('player_id'));
        })
        
        jQuery(document).on('click', '#btn_clear_all_lineup', function(){
            jQuery('.f-roster .btn_remove_lineup').each(function(){
                jQuery(this).trigger('click');
            });
        })
        
        jQuery(document).on('keyup', '#player-search', function(){
            clearTimeout(searchTimer);
            if (jQuery('#player-search').val) {
                searchTimer = setTimeout(function(){
                    //load player list
                    jQuery.playerdraft.loadDraftPlayerList();
                }, searchTimeout);
            }
        })
        
        jQuery(document).on('click', '.vc-fixture-item', function(){
            jQuery('.vc-fixture-item').removeClass('f-is-active');
            jQuery(this).addClass('f-is-active');

            //load player list
            jQuery.playerdraft.loadDraftPlayerList();
        })

        //submit data
        jQuery(document).on('click', '#btnSubmit', function(){
            jQuery.global.disableButton('btnSubmit');
            if (!jQuery.playerdraft.checkLineupFulled())
            {
                alert(wpfs['player_each_position']);
                jQuery.global.enableButton('btnSubmit');
            } 
            else if(jQuery.playerdraft.checkOverSalaryCap())
            {
                alert(wpfs['team_exceed_salary_cap']);
                jQuery.global.enableButton('btnSubmit');
            }
            else
            {
                var lineup_ids = [];
                var player_ids = [];
                jQuery('.f-roster-position.filled').each(function(){
                    lineup_ids.push(jQuery(this).data('id'));
                    if(jQuery(this).data('player_id') != "")
                    {
                        player_ids.push(jQuery(this).data('player_id'));
                    }
                })
                jQuery('#lineup_ids_value').val(lineup_ids.join(','));
                jQuery('#player_ids_value').val(player_ids.join(','));

                //submit data
                jQuery.post(ajaxurl, 'action=submitPlayerDraftNew&' + jQuery('#formLineup').serialize(), function(result) {
                    var json = jQuery.parseJSON(result);
                    if(json.success == 0 && json.redirect)
                    {
                        document.location = json.redirect;
                    }
                    else if(json.success == 0)
                    {
                        jQuery.global.enableButton('btnSubmit');
                        alert(json.message);
                    }
                    else
                    {
                        document.location = json.redirect;
                    }
                })
            }
        })

        //show player info
        jQuery(document).on('click', '.player_info', function(){
            var id = jQuery(this).data('id');
            jQuery.playerdraft.playerInfo(id);
        })

        //load player list by position
        jQuery(document).on('click', '#vc-position a', function(){
            jQuery('#vc-position a').removeClass('f-is-active');
            jQuery(this).addClass('f-is-active');
            jQuery('#pagination').remove();

            //load player list
            jQuery.playerdraft.loadDraftPlayerList();
        })

        //load player by page
        jQuery(document).on('click', '#pagination a', function(){
            jQuery('#pagination').find('li').removeClass('active');
            jQuery(this).closest('li').addClass('active');

            //load player list
            jQuery.playerdraft.loadDraftPlayerList();
        })

        //load first position data
        jQuery('.f-player-list-position-tabs li:first a').trigger('click');
    },

    loadDraftPlayerList: function(){
        jQuery.global.showLoading('#player-content');

        var sortItem = jQuery('.table-sorting.active-sort');
        var page = 1;
        if(jQuery('#pagination').length > 0){
            page = jQuery('#pagination').find('.active a').data('page');
        }
        var params = {
            action: 'getDraftPlayerList',
            league_id: jQuery('#league-id').val(),
            position_id: jQuery('#vc-position a.f-is-active').data('id'),
            fight_id: jQuery('.vc-fixture-item.f-is-active').data('id'),
            player_id: typeof jQuery('#vc-position a.f-is-active').data('playerId') != 'undefined' ? jQuery('#vc-position a.f-is-active').data('playerId') : '',
            keyword: jQuery('#player-search').val(),
            page: page,
            sort_type: sortItem.data('type'),
            sort: sortItem.data('sort')
        };
        jQuery.post(ajaxurl, params, function (result) {
            jQuery('#player-content').html(result);

            //check picked
            jQuery('.f-roster-position.filled').each(function(){
                var player_id = jQuery(this).data('player_id');
                if(jQuery('#player_' + player_id).length > 0){
                    jQuery('#player_' + player_id).find('.btn_add_lineup').hide();
                    jQuery('#player_' + player_id).find('.btn_remove_lineup').show();
                }
            })

            jQuery.playerdraft.checkModifyLineup();
        })
    },
    
    filterPlayers: function()
    {
        var keyword = jQuery('#player-search').val();
        var fight_id = parseInt(jQuery('.vc-fixture-item.f-is-active').data('id'));
        var position_id = parseInt(jQuery('.f-player-list-position-tabs li a.f-is-active').data('id'));
        jQuery('.player_item').each(function(){
            var player_name = jQuery(this).data('name');
            var player_fight_id = parseInt(jQuery(this).data('fight_id'));
            var player_position_id = parseInt(jQuery(this).data('position_id'));
            if ((keyword == "" || player_name.toString().trim().search(new RegExp(keyword, 'i')) != -1) && ((fight_id == 0 || player_fight_id == fight_id) && (position_id == 0 || player_position_id == position_id)))
            {
                jQuery(this).show();
            }
            else
            {
                jQuery(this).hide();
            }
        })
    },
    
    positionStep: function()
    {
        if(jQuery('#is_position_step').val() == 1)
        {
            var position_id = parseInt(jQuery('.f-player-list-position-tabs li a.f-is-active').data('id'));
            if(jQuery('.lineup_' + position_id + '.filled').length == jQuery('.lineup_' + position_id).length)
            {
                jQuery('.f-player-list-position-tabs li a.f-is-active').closest('li').next().find('a').trigger('click');
            }
        }
    },
    
    addLineup: function(player_id)
    {
        var player = jQuery("#player_" + player_id);
        var image = player.data('image');
        var name = player.data('name');
        var salary = parseFloat(player.data('salary'));
        var fixture = player.find('.f-player-fixture').html();
        var position_id = parseInt(player.data('position_id'));

        var lineup = jQuery('.lineup_' + position_id + ':not(.filled):first');
        lineup.addClass('lineup_player_' + player_id);
        lineup.data('player_id', player_id);
        lineup.data('player_salary', salary);
        lineup.addClass('filled');
        lineup.find('.f-empty-roster-slot-instruction').hide();
        if(image != ''){
            lineup.find('.f-player-image').html('<img src="' + image + '" />').show();
        }
        lineup.find('.f-player').html(name);
        lineup.find('.f-salary').html(VIC_FormatMoney(salary)).css('visibility', 'visible');
        lineup.find('.f-fixture').html(fixture).css('visibility', 'visible');
        lineup.find('.btn_remove_lineup').css('visibility', 'visible').data('player_id', player_id);
        this.checkModifyLineup();
        this.calculateSalaryRemaining();
        this.positionStep();
    },
    
    removeLineup: function(player_id)
    {
        var lineup = jQuery('.lineup_player_' + player_id);
        var emptyName = lineup.find('.f-player').data('empty');
        lineup.removeClass('lineup_player_' + player_id);
        lineup.data('player_id', '');
        lineup.data('player_salary', '');
        lineup.removeClass('filled');
        lineup.find('.f-empty-roster-slot-instruction').show();
        lineup.find('.f-player-image').empty().hide();
        lineup.find('.f-player').html(emptyName);
        lineup.find('.f-salary').empty().css('visibility', 'hidden');
        lineup.find('.f-fixture').empty().css('visibility', 'hidden');
        lineup.find('.btn_remove_lineup').css('visibility', 'hidden').data('player_id', '');;
        this.checkModifyLineup();
        this.calculateSalaryRemaining();
    },
    
    checkLineupFulled: function()
    {
        if(jQuery('.f-roster-position.filled').length >= jQuery('.f-roster-position').length)
        {
            //jQuery('.btn_add_lineup').hide();
            return true;
        }
        //jQuery('.btn_add_lineup').show();
        return false;
    },
    
    checkModifyLineup: function()
    {
        var position_checked = [];
        jQuery('.f-roster-position').each(function(){
            var id = jQuery(this).data('id');
            if(position_checked.indexOf(id) == -1)
            {
                position_checked.push(id);
                if(jQuery('.lineup_' + id + '.filled').length == jQuery('.lineup_' + id).length)
                {
                    jQuery('.add_lineup_' + id).hide();
                    jQuery('.remove_lineup_' + id).hide();
                }
                else
                {
                    jQuery('.add_lineup_' + id).show();
                    jQuery('.remove_lineup_' + id).hide();
                }
            }
        })
        
        jQuery('.f-roster-position.filled').each(function(){
            var player_id = jQuery(this).data('player_id');
            jQuery('#btn_add_lineup_' + player_id).hide();
            jQuery('#btn_remove_lineup_' + player_id).show();
        })
    },
    
    removeAllLineup: function()
    {
        jQuery('.f-roster-position').each(function(){
            jQuery.playerdraft.removeLineup(jQuery(this).data('player_id'));
        })
    },
    
    calculateSalaryRemaining: function ()
    {
        var salary_remaining = parseFloat(jQuery('#salaryRemaining').data("value"));
        var salary = 0;
        var total_empty_lineup = jQuery('.f-roster-position:not(.filled)').length;
        jQuery('.f-roster-position.filled').each(function(){
            salary += parseFloat(jQuery(this).data('player_salary'));
        })
        salary_remaining = salary_remaining - salary;
        var avg = total_empty_lineup > 0 ? Math.round(salary_remaining / total_empty_lineup) : 0;
        jQuery('#AvgPlayer').html(VIC_FormatMoney(avg, "$"));
        jQuery('#salaryRemaining').html(VIC_FormatMoney(salary_remaining, "$"));
        if(parseFloat(salary_remaining) < 0)
        {
            jQuery('#salaryRemaining').addClass('f-error');
        }
        else
        {
            jQuery('#salaryRemaining').removeClass('f-error');
        }
    },
    
    checkOverSalaryCap()
    {
        var salary_remaining = parseFloat(jQuery('#salaryRemaining').data("value"));
        var salary = 0;
        jQuery('.f-roster-position.filled').each(function(){
            salary += parseFloat(jQuery(this).data('player_salary'));
        })
        if(salary > salary_remaining)
        {
            return true;
        }
        return false;
    },

    //////////////////////////////////////////player info//////////////////////////////////////////
    playerInfo: function (player_id)
    {
        jQuery.playerdraft.showDialog('#dlgInfo', jQuery.global.loading());
        var data = {
            action: 'loadPlayerInfo',
            player_id: player_id
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery('#dlgInfo .f-body').html(result);
            jQuery(".f-player-stats-lightbox").tabs({active: 0});
            
            //load player news from google
            //if(jQuery('#playerNews').data('google') == 1)
            //{
                jQuery.playerdraft.loadPlayerNews();
            //}
        })
    },
    
    loadPlayerNews: function(link)
    {
        jQuery('#playerNews').html(jQuery.global.loading());
        if(typeof link == 'undefined' || link == '')
        {
            link = '';
        }
        var data = {
            action: 'loadPlayerNews',
            link: link,
            player_name: jQuery('.f-player-info .f-player-name').html().trim(),
            player_team: jQuery('.f-player-info .f-player-team').length > 0 ? jQuery('.f-player-info .f-player-team').html().trim() : '',
            lang: current_lang
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery('#playerNews').html(result);
        });
    },
    
    showDialog: function (dlg, data)
    {
        dlg = jQuery(dlg);
        if (typeof data !== 'undefined' && data != '')
        {
            dlg.find('.f-body').empty().append(data).show();
        }
        dlg.find('.f-body').show();
        dlg.fadeIn();
    },

    closeDialog: function (dlg)
    {
        dlg = jQuery(dlg);
        dlg.find('.f-body').hide();
        dlg.removeClass("f-quickfire-lightbox");
        dlg.fadeOut();
        return false;
    },
    
    isPlayerInline: function (player_id)
    {
        var existed = false;
        jQuery('.f-roster .f-roster-position').each(function () {
            if (jQuery(this).attr('data-id') == player_id)
            {
                existed = true;
            }
        });
        if (existed)
        {
            return true;
        }
        return false;
    },
    
    //////////////////////////////////////////result//////////////////////////////////////////
    initPlayerDraftResult: function()
    {
        var is_live = jQuery('#is_live').val();

        //check live
        if(is_live == 1)
        {
            jQuery.playerdraft.liveEntriesResult();
            setInterval(function(){ 
                jQuery.playerdraft.liveEntriesResult();
            }, 60000);
        }
        else
        {
            jQuery.playerdraft.loadResult();
        }
        
        //result detail
        jQuery(document).on('click', '#table_standing tr', function(){
            result_select_id = jQuery(this).attr('id');
            jQuery('#table_standing tr').removeClass('active');
            jQuery(this).addClass('active');
            var user_id = jQuery(this).data('user_id');
            var entry_number = jQuery(this).data('entry_number');
            jQuery.playerdraft.loadResultDetail(user_id, entry_number);
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
        jQuery.global.showLoading('#vc-leaderboard');
        var data = {
            action: 'playerDraftLoadResult',
            page: typeof page != 'undefined' ? page : 1,
            league_id: league_id,
            user_id: jQuery('#user_id').val(),
            entry_number: entry_number,
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery('#result').html("");
            jQuery('#f-live-scoring-leaderboard').html(result);
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
    
    loadResultDetail: function (user_id, entry_number)
    {
        var league_id = jQuery('#league_id').val();

        jQuery.global.showLoading('#vc-leaderboard');
        jQuery.global.showLoading('#vc-leaderboard-detail');
        var data = {
            action: 'playerDraftLoadResultDetail',
            league_id: league_id,
            user_id: user_id,
            entry_number: entry_number
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery.global.showLoading('#vc-leaderboard');
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
            jQuery.playerdraft.loadResult();
        });
    },
};