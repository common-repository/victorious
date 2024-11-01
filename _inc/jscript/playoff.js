var result_select_id = "";
var result_select_page = "";
var searchTimer;
var searchTimeout = 500;
var changedTurn = 0;
jQuery.playoff =
{
    initPlayoff: function()
    {
        this.calculateSalaryRemaining();
        this.checkModifyLineup();

        //load player list
        jQuery.playoff.loadPlayoffPlayerList();
        
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
            jQuery.playoff.loadPlayoffPlayerList();
        });
        
        jQuery(document).on('click', '.btn_add_lineup', function(){
            var player_id = jQuery(this).data('id')
            if (confirm(wpfs['playoff_confirm_draft_player']) == true) {
                jQuery.playoff.draftPlayer(player_id)
            }
        })

        jQuery(document).on('click', '.btn_remove_lineup', function(){
            var player_id = jQuery(this).data('id')
            if (confirm(wpfs['playoff_confirm_remove_player']) == true) {
                jQuery.playoff.removeDraftPlayer(player_id)
            }
        })

        jQuery(document).on('keyup', '#player-search', function(){
            clearTimeout(searchTimer);
            if (jQuery('#player-search').val) {
                searchTimer = setTimeout(function(){
                    //load player list
                    jQuery.playoff.loadPlayoffPlayerList();
                }, searchTimeout);
            }
        })
        
        jQuery(document).on('click', '.vc-fixture-item', function(){
            jQuery('.vc-fixture-item').removeClass('f-is-active');
            jQuery(this).addClass('f-is-active');

            //load player list
            jQuery.playoff.loadPlayoffPlayerList();
        })

        //submit data
        jQuery(document).on('click', '#btnSubmit', function(){
            jQuery.global.disableButton('btnSubmit');
            if (!jQuery.playoff.checkLineupFulled())
            {
                alert(wpfs['player_each_position']);
                jQuery.global.enableButton('btnSubmit');
            } 
            else if(jQuery.playoff.checkOverSalaryCap())
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
                jQuery.post(ajaxurl, 'action=submitPlayoff&' + jQuery('#formLineup').serialize(), function(result) {
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
            jQuery.playoff.playerInfo(id);
        })

        //load player list by position
        jQuery(document).on('click', '#vc-position a', function(){
            jQuery('#vc-position a').removeClass('f-is-active');
            jQuery(this).addClass('f-is-active');
            jQuery('#pagination').remove();

            //load player list
            jQuery.playoff.loadPlayoffPlayerList();
        })

        //load player by page
        jQuery(document).on('click', '#pagination a', function(){
            jQuery('#pagination').find('li').removeClass('active');
            jQuery(this).closest('li').addClass('active');

            //load player list
            jQuery.playoff.loadPlayoffPlayerList();
        })

        //load first position data
        jQuery('.f-player-list-position-tabs li:first a').trigger('click');

        //load in drafting users
        jQuery.playoff.inDraftingUsers();

        var league_id = jQuery('#league-id').val();
        setInterval(function(){
            //load in drafting users
            if(jQuery('#inDraftUserContent').find('#inDrafting').length > 0){
                var data = {
                    action: 'playoffCheckChangeTurn',
                    league_id: league_id,
                    user_id: jQuery('.draft-turn.f-is-active').data('id')
                };
                jQuery.post(ajaxurl, data, function (result) {
                    var json = jQuery.parseJSON(result);
                    if(json.success == 1 && changedTurn == 0){
                        jQuery.playoff.inDraftingUsers(1);
                        changedTurn = 1;
                    }
                    else{
                        changedTurn = 0;
                    }
                })
            }
            else{
                jQuery.playoff.inDraftingUsers(1);
            }
            
        }, 1500);
    },

    loadPlayoffPlayerList: function(skip_loading){
        if(skip_loading !== 1){
            jQuery.global.showLoading('#player-content');
        }

        var sortItem = jQuery('.table-sorting.active-sort');
        var page = 1;
        if(jQuery('#pagination').length > 0){
            page = jQuery('#pagination').find('.active a').data('page');
        }
        var params = {
            action: 'getPlayoffPlayerList',
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

            //jQuery.playoff.checkModifyLineup();
            jQuery.playoff.decideCanDraft()
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
        //lineup.find('.btn_remove_lineup').css('visibility', 'visible').data('player_id', player_id);
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
            var can_edit = jQuery(this).data('canEdit');
            if(can_edit != 1){
                jQuery('#btn_add_lineup_' + player_id).hide();
                jQuery('#btn_remove_lineup_' + player_id).hide();
            }
            else{
                jQuery('#btn_add_lineup_' + player_id).hide();
                jQuery('#btn_remove_lineup_' + player_id).show();
            }
        })
    },
    
    removeAllLineup: function()
    {
        jQuery('.f-roster-position').each(function(){
            jQuery.playoff.removeLineup(jQuery(this).data('player_id'));
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
        jQuery.playoff.showDialog('#dlgInfo', jQuery.global.loading());
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
                jQuery.playoff.loadPlayerNews();
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
    initPlayoffResult: function()
    {
        var is_live = jQuery('#is_live').val();

        //check live
        if(is_live == 1)
        {
            jQuery.playoff.liveEntriesResult();
            setInterval(function(){ 
                jQuery.playoff.liveEntriesResult();
            }, 60000);
        }
        else
        {
            //jQuery.playoff.loadResult();
        }
        
        //result detail
        jQuery(document).on('click', '#table_standing tr', function(){
            result_select_id = jQuery(this).attr('id');
            jQuery('#table_standing tr').removeClass('f-user-highlight');
            jQuery(this).addClass('f-user-highlight');
            var user_id = jQuery(this).data('user_id');
            var entry_number = jQuery(this).data('entry_number');
            jQuery.playoff.loadResultDetail(user_id, entry_number);
        })

        //change week
        jQuery(document).on('change', '#playoff_week', function(){
            var user_id = jQuery('#table_standing tr.f-user-highlight').data('user_id');
            var entry_number = jQuery('#table_standing tr.f-user-highlight').data('entry_number');
            jQuery.playoff.loadResultDetail(user_id, entry_number);
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
            action: 'getPlayoffResult',
            page: typeof page != 'undefined' ? page : 1,
            league_id: league_id,
            user_id: jQuery('#user_id').val(),
            entry_number: entry_number,
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery('#result').html("");
            jQuery('#vc-leaderboard').html(result);
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
        var week = jQuery('#playoff_week').val();

        jQuery.global.showLoading('#vc-leaderboard');
        jQuery.global.showLoading('#vc-leaderboard-detail');
        var data = {
            action: 'getPlayoffResultDetail',
            league_id: league_id,
            user_id: user_id,
            entry_number: entry_number,
            my_user_id: jQuery('#user_id').val(),
            my_entry_number: jQuery('#entry_number').val(),
            week: week
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery.global.hideLoading('#vc-leaderboard');
            jQuery('#vc-leaderboard-detail').html(result);
        });
    },
    
    liveEntriesResult: function ()
    {
        var league_id = jQuery('#league_id').val();
        var data = {
            action: 'getPlayoffLiveResult',
            leagueID: league_id
        };
        jQuery.post(ajaxurl, data, function () {
            jQuery.playoff.loadResult();
        });
    },

    joinContest: function(leagueID){
        var btnId = 'btnPlayoffJoinContest' + leagueID;
        jQuery.global.disableButton(btnId);
        var data = {
            action: 'playoffJoinContest',
            leagueID: leagueID
        };
        jQuery.post(ajaxurl, data, function (result) {
            var json = jQuery.parseJSON(result);
            if(json.success == 1){
                alert(json.message);
                location.reload();
            }
            else{
                alert(json.message);
            }
            jQuery.global.enableButton(btnId);
        });
    },

    inDraftingUsers: function (skip_loading){
        var league_id = jQuery('#league-id').val();
        var data = {
            action: 'playoffInDraftingUsers',
            league_id: league_id
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery('#inDraftUserContent').html(result)
            jQuery.playoff.draftCountdown();
            jQuery.playoff.decideCanDraft();
            jQuery.playoff.loadPlayoffPlayerList(skip_loading);
        });
    },

    draftCountdown: function(){
        var targetDate = this.getTimestamp();
        var timestamp = jQuery('#inDraftUserContent').find('#inDrafting').data('interval')
        var canDraft = jQuery('#inDraftUserContent').find('#inDrafting').data('canDraft')
        if(typeof timestamp != 'undefined')
        {
            targetDate = timestamp;
        }
        var clock = jQuery("#playoff-countdown");
        clock.countdown(targetDate, function(event) {
            jQuery(this).text(
                event.strftime('%M:%S')
            );
        }).on('finish.countdown', function(e) {
            if(canDraft == 1){
                jQuery.playoff.autoDraftPlayer();
            }
        });
    },

    getTimestamp: function() {
        return new Date(new Date().valueOf() + jQuery('#draft-countdown').val() * 60 * 1000);
    },

    decideCanDraft: function(){
        var canDraft = jQuery('#inDraftUserContent').find('#inDrafting').data('canDraft')
        if (canDraft == 1) {
            jQuery('.btn_add_lineup').show()
        } else {
            jQuery('.btn_add_lineup').hide()
        }
    },

    draftPlayer: function (player_id){
        jQuery.global.showLoading('#player-content');

        var league_id = jQuery('#league-id').val();
        var player = jQuery("#player_" + player_id);
        var position_id = parseInt(player.data('position_id'));

        var data = {
            action: 'playoffDraftPlayer',
            league_id: league_id,
            player_id: player_id,
            position_id: position_id
        };
        jQuery.post(ajaxurl, data, function (result) {
            var json = jQuery.parseJSON(result);
            if(json.success == 1){
                if(json.draft_end == 1){
                    window.location = json.redirect;
                }
                else{
                    jQuery.playoff.addLineup(player_id)
                    jQuery.playoff.inDraftingUsers();
                }
            }
            else{
                alert(json.message);
            }
            jQuery.global.hideLoading('#player-content');
        });
    },

    removeDraftPlayer: function (player_id){
        jQuery.global.showLoading('#player-content');

        var league_id = jQuery('#league-id').val();
        var player = jQuery("#player_" + player_id);

        var data = {
            action: 'playoffRemoveDraftPlayer',
            league_id: league_id,
            player_id: player_id
        };
        jQuery.post(ajaxurl, data, function (result) {
            var json = jQuery.parseJSON(result);
            if(json.success == 1){
                jQuery.playoff.removeLineup(player_id)
            }
            else{
                alert(json.message);
            }
            jQuery.global.hideLoading('#player-content');
        });
    },

    autoDraftPlayer: function (){
        jQuery.global.showLoading('#player-content');
        var league_id = jQuery('#league-id').val();

        var data = {
            action: 'playoffAutoDraftPlayer',
            league_id: league_id
        };
        jQuery.post(ajaxurl, data, function (result) {
            var json = jQuery.parseJSON(result);
            if(json.success == 1){
                if(json.draft_end == 1){
                    window.location = json.redirect;
                }
                else{
                    //jQuery.playoff.addLineup(json.player_id)
                    jQuery.playoff.inDraftingUsers();

                    //add to lineup
                    var position_id = json.player.position_id;
                    var player_id = json.player.id;
                    var salary = json.player.salary;
                    var image = json.player.image;
                    var name = json.player.name;
                    var lineup = jQuery('.lineup_' + position_id + ':not(.filled):first');
                    lineup.addClass('lineup_player_' + player_id);
                    lineup.data('player_id', player_id);
                    lineup.data('player_salary', salary);
                    lineup.addClass('filled');
                    lineup.find('.f-empty-roster-slot-instruction').hide();
                    /*if(image != ''){
                        lineup.find('.f-player-image').html('<img src="' + image + '" />').show();
                    }*/
                    lineup.find('.f-player').html(name);
                    lineup.find('.f-salary').html(VIC_FormatMoney(salary)).css('visibility', 'visible');
                }
            }
            else{
                alert(json.message);
            }
            jQuery.global.hideLoading('#player-content');
        });
    }
};