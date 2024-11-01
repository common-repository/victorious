jQuery.livedraft =
{
    setLiveDraftData: function()
    {
        this.aListTeamPlayer = {};
        this.aPlayers = jQuery('#dataPlayers').html();
        this.salaryRemaining = jQuery('#dataSalaryRemaining').html();
        this.salaryCap = jQuery('#dataSalaryRemaining').html();
        this.playerIdPicks = jQuery('#dataPlayerIdPicks').html().trim();
        this.league = jQuery('#dataLeague').html();
        this.aIndicators = jQuery('#dataIndicators').html();
        this.aPostiions = jQuery('#dataPositions').html();
        this.is_turn_by_turn = jQuery('#is_turn_by_turn').html(); 
        this.limit_quantity_change_player = parseInt(jQuery('#limit-players').html());
        this.quantity_changed_players = parseInt(jQuery('#limit-players').html());
        this.edit_injury_players = parseInt(jQuery('#edit-injury-players').val());
        this.list_position_allow_edit = JSON.parse(jQuery('#position-injury-players').html());
        this.injury_players = JSON.parse(jQuery('#injury-players').html());
        this.allow_waiver_wire = parseInt(jQuery('#allow-waiver-wire').html());
        this.time_remaining = parseInt(jQuery('#time-remaining').html());
        this.original_injury_players = JSON.parse(jQuery('#injury-players').html());
        this.list_new_players = [];
        this.current_turn = jQuery('#current_turn').val();
        this.bench_quantity = parseInt(jQuery('#bench_quantity').html());
        this.except_player_ids = jQuery('#except_player_ids').length > 0 ? JSON.parse(jQuery('#except_player_ids').html()) : '';
        this.check_changed_turn = 1;
        this.is_bench = jQuery('#is_bench').length > 0 ? parseInt(jQuery('#is_bench').html()) : 0;
        
        jQuery.livedraft.editLineup();
    },
    
    liveDraftInit: function()
    {
        // set countdown for change player each week
        if(this.time_remaining > 0 && this.is_turn_by_turn == 1){
            this.turnByTurnCoundown(this.time_remaining);
        }
        //this.checkPlayerButtonDisplay();
    },
    
    liveDraftCheckAllowDraft: function(leagueID)
    {
        jQuery('#btnSubmit').attr('disabled', 'disabled').hide();
        var data = {
            'action': 'liveDraftCheckAllowDraft',
            'leagueID': leagueID,
        };
        jQuery.post(ajaxurl, data, function (result) {
            result = jQuery.parseJSON(result);
            if(result.allow == 1)
            {
                jQuery('#btnSubmit').removeAttr('disabled').show();
                for (var i = 1; i < 99999; i++)
                {
                    window.clearInterval(i);
                }
                console.log('aaa');
                jQuery.livedraft.setLiveDraftData();
                jQuery.livedraft.liveDraft();
                jQuery.livedraft.liveDraftGetUserInDraftRoom();
            }
        })
    },
    
    allowCheckChangeTurnInterval: function()
    {
        if(this.is_turn_by_turn && this.current_turn != jQuery('#user_id').val())
        {
            setInterval(function() { jQuery.livedraft.liveDraftCheckChangedTurn() }, 4000);
        }
    },
    
    clearCheckChangeTurnInterval: function()
    {
        for (var i = 1; i < 99999; i++)
        {
            window.clearInterval(i);
        }
    },
    
    loadPlayers: function ()
    {
        var position_id = jQuery('.f-tabs li a.f-is-active').attr('data-id');
        var round_squad = jQuery('.f-tabs li a.f-is-active').attr('data-round_squad');
        round_squad = typeof round_squad != 'undefined' ? round_squad.split(',').map(Number) : '';
        var teamId1 = jQuery('.fixture-item.f-is-active').attr('data-team-id1');
        var teamId2 = jQuery('.fixture-item.f-is-active').attr('data-team-id2');
        var keyword = jQuery('#player-search').val().toString();
        var edit_injury_players = this.edit_injury_players;
        if(position_id > 0 || position_id == 'IR' || typeof teamId1 != 'undefined' || typeof teamId2 != 'undefined' || keyword != '' || round_squad != '')
        {
            jQuery('.f-player-list-table tbody tr').hide();
            jQuery('.f-player-list-table tbody tr').each(function(){
                if (keyword == '' || jQuery(this).data('player_name').toString().trim().search(new RegExp(keyword, 'i')) > -1)
                {
                    if ((typeof teamId1 == 'undefined' && typeof teamId2 == 'undefined') || (jQuery(this).data('team') == teamId1 || jQuery(this).data('team') == teamId2))
                    {
                        if ((jQuery(this).data('position') == position_id) || position_id == '' || position_id == 'IR' || (round_squad != '' && jQuery.inArray(parseInt(jQuery(this).data('id')), round_squad) != -1))
                        {
                            // for edit injury players
                            if (edit_injury_players == 1 && position_id == 'IR')
                            {
                                if(typeof jQuery(this).data('ir') != 'undefined' && jQuery(this).data('ir') == 1)
                                {
                                    jQuery(this).show();
                                }
                            }
                            else
                            {
                                jQuery(this).show();
                            }
                        }
                    }
                }
            })
        }
        else
        {
            jQuery('.f-player-list-table tbody tr').show();
        }
        
        //bench
        this.benchLoadPlayers();

        //turn by turn
        if(this.is_turn_by_turn == 1)
        {
            if(this.except_player_ids != '')
            {
                for(var i in this.except_player_ids)
                {
                    jQuery('#player_number_' + this.except_player_ids[i]).hide();
                }
            }
            //this.loadPlayerTurnByTurn();
            this.checkCurrentTurn(this.current_turn);
            this.turnByTurn();
        }

        return false;
    },

    setNoImage: function (item)
    {
        item.parent().addClass('f-no-image').css('background-image', '');
        item.remove();
    },
    setActiveFixture: function (item)
    {
        jQuery('.fixture-item').removeClass('f-is-active');
        jQuery(item).addClass('f-is-active');
        jQuery(item).blur();
        return false;
    },
    setActivePosition: function (item)
    {
        jQuery('.f-tabs li a').removeClass('f-is-active');
        jQuery(item).addClass('f-is-active');
        jQuery(item).blur();
        return false;
    },

    editLineup: function ()
    {
        if (this.playerIdPicks != '')
        {
            var playerIdPicks = jQuery.parseJSON(this.playerIdPicks);
            if(playerIdPicks != null)
            {
                for (var i = 0; i < playerIdPicks.length; i++)
                {
                    this.addPlayer(playerIdPicks[i], 1);
                }
            }
            //turn by turn
            if(this.is_turn_by_turn == 1)
            {
                this.loadPlayerTurnByTurn();
                this.checkCurrentTurn(this.current_turn);
                if(this.except_player_ids != '')
                {
                    for(var i in this.except_player_ids)
                    {
                        jQuery('#player_number_' + this.except_player_ids[i]).hide();
                    }
                }
            }
        }
    },

    addPlayer: function (id, load_lineup, player)
    {
        var league = jQuery.parseJSON(this.league);
        player = typeof player != 'undefined' ? player : this.findPlayer(id);

        if (typeof player != 'undefined')
        {
            var position_id = player.position_id;
            if (league.no_position == 1)
            {
                position_id = 0;
            }
            var item = jQuery('.player-position-' + position_id + ':not(.f-has-player)').first();
            // extra position 
            var is_flex = false;
            if (item.length != 1) {
                for (var i in this.extra_positions) {
                    var extra_pos_id = this.extra_positions[i].position_id;
                    var position_name = this.getPositionNameById(position_id);
                    // check extra position >2
                    var same_position = {};
                    var is_full = false;
                    jQuery('.player-position-' + extra_pos_id + '.f-has-player').each(function () {
                        var data_id = jQuery(this).attr('data-id');
                        var _player = jQuery.livedraft.findPlayer(data_id);
                        if (typeof same_position[_player.position] == 'undefined') {
                            same_position[_player.position] = 0;
                        }
                        same_position[_player.position] += same_position[_player.position] + 1;
                    });
                    if (typeof same_position[player.position] != 'undefined' && same_position[player.position] > 1) {
                        is_full = true;
                    }
                    if (position_name != 'G' && is_full == false) {
                        var item = jQuery('.player-position-' + extra_pos_id + ':not(.f-has-player)').first();
                        if (item.length == 1 && position_name != false && position_name != 'G') {
                            position_id = extra_pos_id;
                            is_flex = true;
                            break;
                        }
                    }
                }

            }

            if (item.length == 1)
            {
                // check registration
                if (this.checkPlayerRegistriction(player.team_id, player.id)) {
                    this.addPlayerWithTeams(player.team_id, player.id);
                } else {
                    this.showErrorRegistriction();
                    return false;
                }

                // position step 
                if (typeof this.is_soccer != 'undefined' && this.is_soccer != '') {
                    // check
                    var player_position = player.position_id;
                    var default_quantity = this.list_position_soccer[player_position].default_quantity;
                    var current_quantity = this.list_position_soccer[player_position].current_quantity;
                    var new_value = parseInt(current_quantity) + 1;

                    this.list_position_soccer[player_position].current_quantity = new_value;

                    if (new_value == default_quantity) {
                        // next to positions
                        this.nextToAvailablePosition(player_position);
                    }

                }

                jQuery('#buttonAdd' + id).hide();
                jQuery('#buttonAdd' + id).parents('tr').addClass('f-player-in-lineup');
                jQuery('#buttonRemove' + id).css('display', 'block');
                var match = '';
                if (league.only_playerdraft == 0)
                {
                    if (player.teamID2 == player.team_id)
                    {
                        match = '<b>' + player.team2 + '</b>@' + player.team1;
                    } else
                    {
                        match = player.team2 + '@<b>' + player.team1 + '</b>';
                    }
                }

                item.addClass('f-has-player');
                item.attr('id', 'f-has-player' + id);
                item.attr('data-id', id);
                item.find('.f-empty-roster-slot-instruction').hide();
                
                if(typeof item.data('constructor') != 'undefined')
                {
                    item.find('.f-player-image').empty().append('<img src="' + player.game_image_path + '" onerror="jQuery.livedraft.setNoImage(jQuery(this))" />');
                }
                else
                {
                    item.find('.f-player-image').empty().append('<img src="' + player.full_image_path + '" onerror="jQuery.livedraft.setNoImage(jQuery(this))" />');
                }
                item.find('.f-player').empty().append(player.name).show().attr("onclick", "jQuery.livedraft.playerInfo(" + player.id + ")");
                //item.find('.f-salary').empty().append(VIC_FormatMoney(player.salary)).show();
                item.find('.f-fixture').empty().append(match);
                item.find('.f-button').show();
                if(typeof item.find('.f-team') != 'undefined' && typeof player.team_name != 'undefined')
                {
                    item.find('.f-team').html(player.team_name);
                }

                // hide available players
                if (typeof this.is_turn_by_turn != 'undefined' && this.is_turn_by_turn != 1 && this.quantity_changed_players == this.limit_quantity_change_player) {
                    // hide all player
                    this.loadPlayers();
                }
                
                // custome for soccer field
                if (this.is_soccer_field) {

                    var match_salary = player.team1 + '@<b>' + player.team2 + ' ' + VIC_FormatMoney(player.salary);
                    var pos_name = player.position + ' ' + player.name;
                    var item_field = jQuery('#custom-field-soccer .position-' + position_id + ':not(.field-has-player)').first();
                    item_field.attr('id', 'soccer-player-' + player.id);
                    item_field.addClass('field-has-player');
                    item_field.find('.f-player-image').empty().html('<img src="' + player.full_image_path + '" onerror="jQuery.livedraft.setNoImage(jQuery(this))" />');
                    item_field.find('.team-salary').empty().html(match_salary);
                    item_field.find('.pos-player').empty().html(pos_name);


                }
                
                if (player.disable == 1)
                {
                    item.find('.f-button').remove();
                } else
                {
                    item.find('.f-button').attr('onclick', 'jQuery.livedraft.clearPlayer(' + id + ')');
                }

                this.calculateSalary(id, 'add');
                this.calculateAvgPerPlayer();

                //move flex position
                if (is_flex) {
                    var clss = "#custom-field-soccer .group_position_" + player.position;
                    item_field.remove();
                    jQuery(clss).append(item_field);
                }
                
                // add injury status
                if (this.edit_injury_players == 1 || (this.allow_waiver_wire && this.original_injury_players.length > 0)) {
                    if (player.indicator_id == 1) {
                        jQuery('#f-has-player' + id + ' .f-player').append('<span class="f-player-badge f-player-badge-injured-out">IR</span>');
                    }
                    var list_injury = this.injury_players;
                    if (list_injury.indexOf(player.id) > 0) {
                        this.removeInjuryPlayer(player.id);
                        this.loadPlayers();
                    }
                }
            } else
            {
                if(this.bench_quantity > 0 && this.addBench(id, player) == 1)
                {
                    if (this.is_turn_by_turn != 1 && this.allow_waiver_wire == 1) {
                        this.validateQuantityChangePlayer();
                    }
                    this.turnByTurn();
                }
                else if (!jQuery('.f-errorMessage').is(':visible'))
                {
                    var positionName = "'" + player.position + "'";
                    if (league.no_position == 1)
                    {
                        positionName = '';
                    }
                    jQuery('.f-errorMessage').empty().append(wpfs['fullpositions1'] + positionName + " " + wpfs['fullpositions2']).slideToggle().delay(4000).fadeOut();
                }
            }
        }
        if(this.is_turn_by_turn == 1)
        {
            this.turnByTurn();
        }
        // validate limit player change each week
        if (this.is_turn_by_turn != 1 && this.allow_waiver_wire == 1 && this.edit_injury_players != 1) {
            this.validateQuantityChangePlayer(load_lineup);
        }
        
        //bench
        this.benchLoadPlayers();
    },
    
    clearPlayer: function (id)
    {
        var player = this.findPlayer(id);
        // remove injury players from rugby
        if (this.edit_injury_players) {
            var list_injury = this.injury_players;
            var is_load_players = false;
            if (list_injury.indexOf(id) < 0 && player.indicator_id == 1) { // remove from line up injury player
                this.showDialogRemoveInjury(id);
                return false;
            }

        }
        if (this.edit_injury_players || (this.allow_waiver_wire && (this.original_injury_players).length > 0)) {
            // check remove
            var list_orginal = this.original_injury_players;
            if (list_orginal.indexOf(player.id) > -1) {
                this.addInjuryPlayer(id);
                is_load_players = true;
            }
        }
        jQuery('#buttonAdd' + id).css('display', 'block');
        jQuery('#buttonAdd' + id).parents('tr').removeClass('f-player-in-lineup');
        jQuery('#buttonRemove' + id).hide();
        this.resetLineup(id)

        this.calculateSalary(id, 'remove');
        this.calculateAvgPerPlayer();
        if (typeof player != 'undefined') {
            //this.deletePlayerWithTeams(player.team_id, player.id);
        }
        // change player each week
        
        //if (is_load_players) {
            //this.loadPlayers();
        this.checkPlayerButtonDisplay();
        if (this.allow_waiver_wire) {
            this.validateQuantityChangePlayer();
        }
        //}
        if(this.is_turn_by_turn == 1)
        {
            this.turnByTurn();
        }
        
        //bench
        this.benchLoadPlayers();
    },
    
    checkPlayerButtonDisplay: function(){
        jQuery('.f-player-list-table .f-player-add-button').show();
        jQuery('.f-roster-container li.f-has-player').each(function(){
            if(jQuery(this).find('.f-button:visible').length > 0)
            {
                jQuery('#buttonAdd' + jQuery(this).data('id')).hide();
                jQuery('#buttonRemove' + jQuery(this).data('id')).show();
            }
        })
    },
    
    clearAllPlayer: function (no_confirm)
    {
        if (no_confirm == 1 || confirm(wpfs['players_out_team']))
        {
            jQuery('.f-roster .f-roster-position').each(function () {
                if (typeof jQuery(this).attr('data-id') != typeof undefined)
                {
                    jQuery.livedraft.clearPlayer(jQuery(this).attr('data-id'));
                }
            });
        }
    },

    resetLineup: function (id)
    {
        var item = jQuery('#f-has-player' + id);
        item.removeClass('f-has-player');
        item.removeAttr('id');
        item.removeAttr('data-id');
        item.find('.f-empty-roster-slot-instruction').show();
        item.find('.f-player-image').empty();
        item.find('.f-player').empty().hide().removeAttr("onclick");
        //item.find('.f-salary').empty().hide();
        item.find('.f-button').hide();
        item.find('.f-button').attr('onclick', '');
        if(typeof item.find('.f-team') != 'undefined')
        {
            item.find('.f-team').empty();
        }
        item.after(item.clone());
        item.remove();
    },

    findPlayer: function (id)
    {
        var aPlayers = jQuery.parseJSON(this.aPlayers);

        //
        for (var i = 0; i < aPlayers.length; i++)
        {
            if (aPlayers[i].id == id)
            {
                return aPlayers[i];
            }
        }
    },

    calculateSalary: function (player_id, task)
    {
        var is_mixing = false;
        var org_id = 0;
        var salarycap = 0;
        salarycap = this.salaryCap;

        if (salarycap > 0)
        {
            var salary_remaining = 0;
            var player = this.findPlayer(player_id);  // single mixing
            salary_remaining = this.salaryRemaining;


            switch (task)
            {
                case 'add':
                    salary_remaining -= parseFloat(player.salary);
                    if (salary_remaining < 0)
                    {
                        jQuery('#salaryRemaining').addClass('f-error');
                    }
                    break;
                case 'remove':
                    salary_remaining += parseFloat(player.salary);
                    if (salary_remaining > 0)
                    {
                        jQuery('#salaryRemaining').removeClass('f-error');
                    }
                    break;
            }
            jQuery('#salaryRemaining').empty().append(VIC_FormatMoney(salary_remaining));
            this.salaryRemaining = salary_remaining;
        }
    },
    
    calculateAvgPerPlayer: function ()
    {

        var total = jQuery('.f-roster-position:not(.f-has-player)').filter(":visible").length;
        var salary_remaining = 0;
        salary_remaining = this.salaryRemaining;


        if (total > 0)
        {
            total = salary_remaining / total;
        } else
        {
            total = 0;
        }
        jQuery('#AvgPlayer').empty().append(VIC_FormatMoney(Math.round(total)));
    },

    submitData: function ()
    {
        jQuery('.f-errorMessage').after('<div class="f-errorMessage"></div>').remove();
        // check salary cap
        if(this.is_turn_by_turn == 1 && jQuery('.f-roster-position.f-has-player').length == 0)
        {
            alert(wpfs['turn_by_turn_draft_a_player']);
        }
        else if (jQuery('.f-roster-position:not(.f-has-player)').length > 0 && this.is_turn_by_turn == 0)
        {
            alert(wpfs['player_each_position']);
        } 
        else if (this.salaryCap > 0 && this.salaryRemaining < 0)
        {
            alert(wpfs['team_exceed_salary_cap']);
        } 
        else
        {
            jQuery('#formLineup').find('input[name="player_id[]"]').remove();
            jQuery('#btnSubmit').attr("disabled", "true");
            jQuery('.f-roster .f-roster-position').each(function () {
                if (typeof jQuery(this).attr('data-id') != typeof undefined && jQuery(this).find('a.f-button').length > 0)
                {
                    var player_id = jQuery(this).attr('data-id');
                    jQuery('#formLineup').append('<input type="hidden" value="' + player_id + '" name="player_id[]">');
                    if (jQuery.livedraft.is_soccer_flex) {
                        jQuery('#formLineup').append('<input type="hidden" value="' + jQuery(this).attr('data-position') + '" name="player_position[' + player_id + ']">');

                    }
                }
            });
            if (this.edit_injury_players == 1 || (this.allow_waiver_wire && (this.injury_players).length > 0)) {
                var list_injury = this.injury_players;
                if (list_injury.length > 0) {
                    for (var i in list_injury) {
                        jQuery('#formLineup').append('<input type="hidden" value="' + list_injury[i] + '" name="injury[]">');
                    }
                }
            }

            if(this.is_turn_by_turn == 1)
            {
                jQuery('#btnSubmit').attr('disabled', 'disabled');
                //jQuery('.public_message').hide();
                jQuery.post(ajaxurl, jQuery('#formLineup').serialize() + '&action=liveDraftPickPlayer', function (result) {
                    result = jQuery.parseJSON(result);
                    jQuery('.public_message').html(result.msg).show();
                    if(result.result == 0)
                    {
                        //jQuery.livedraft.checkCurrentTurn();
                        jQuery('#btnSubmit').removeAttr('disabled');
                    }
                    else
                    {
                        jQuery('.f-roster-container .f-has-player .f-button').hide();
                        //var is_full_draft = 0;
                        if(jQuery('.f-roster-container li').length == jQuery('.f-roster-container li.f-has-player').length)
                        {
                            window.location = jQuery('#entry_link').val();
                        }
                        else if(result.is_full_draft)
                        {
                            window.location = jQuery('#entry_link').val();
                        }
                        else if(result.change_sort == 1)
                        {
                            jQuery("#change-player-countdown").after('<span id="change-player-countdown"></span>').remove();
                            jQuery.livedraft.liveDraft();
                        }
                    }
                })
            }
            else if(this.allow_waiver_wire == 1)
            {
                jQuery('#btnSubmit').attr('disabled', 'disabled');
                //jQuery('.public_message').hide();
                jQuery.post(ajaxurl, jQuery('#formLineup').serialize() + '&action=liveDraftRequestChangePlayer', function (result) {
                    if(result.result == 0)
                    {
                        jQuery('#btnSubmit').removeAttr('disabled');
                        jQuery('.public_message').html(result.msg).show();
                    }
                    else
                    {
                        window.location = jQuery('#live_entries_link').val();
                    }
                })
            }
            else
            {
                jQuery('#btnSubmit').attr('disabled', 'disabled');
                jQuery.post(ajaxurl, jQuery('#formLineup').serialize() + '&action=liveDraftPickBenchPlayer', function (result) {
                    result = jQuery.parseJSON(result);
                    if(result.success == 1)
                    {
                        window.location = jQuery('#live_entries_link').val();
                    }
                    else
                    {
                        jQuery('#btnSubmit').removeAttr('disabled');
                        alert(result.msg);
                    }
                })
            }
        }
    },
    
    userResult: function (leagueID, is_curent, userID, username, avatar, rank, totalScore, entry_number)
    {
        //load result
        var round_id = 0;
        
        // for rugby
        var week = jQuery('#week').val();
        if (typeof week == 'undefined') {
            week = 0;
        }
        var data = {
            'action': 'loadUserResult',
            'rank': rank,
            'avatar': avatar,
            'username': username,
            'totalScore': totalScore,
            'leagueID': leagueID,
            'userID': userID,
            'entry_number': entry_number,
            'roundID': round_id,
            'week': week,
            'is_motocross': jQuery('#is_motocross').val(),
            'gameType': jQuery("#gameType").val(),
            'leagueOptionType': jQuery('#leagueOptionType').val(),
        };
        if (is_curent == 1)
        {
            jQuery('#f-seat-1 .f-loading').show();
        } else
        {
            jQuery('#f-seat-2 .f-loading').show();
        }
        jQuery.post(ajaxurl, data, function (data) {
            if (is_curent == 1)
            {
                jQuery('#f-seat-1').empty().append(data);
            } else
            {
                jQuery('#f-seat-2').empty().append(data);
            }
        })


        return false;
    },

    searchPlayers: function ()
    {
        jQuery.livedraft.loadPlayers();
    },

    loadContestScores: function (leagueID, entry_number)
    {
        // for rugby
        var week = jQuery('#week').val();
        if (typeof week == 'undefined') {
            week = 0;
        }
        var data = {
            'action': 'loadContestScores',
            'leagueID': leagueID,
            'entry_number': entry_number,
            'week': week,
            'multiEntry': jQuery('#multiEntry').val()
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery('#tableScores tbody').empty().append(result);
            jQuery('#tableScores tbody tr.f-user-highlight').trigger('click');
        });
    },

    loadFixtureScores: function (leagueID)
    {
        var data = 'leagueID=' + leagueID;
        jQuery.post(ajaxurl, "action=loadFixtureScores&" + data, function (result) {
            jQuery("#f-live-scoring-fixture-info").after(result).remove();
        });
    },
    
    sendUserJoincontestEmail: function (league_id, entry_number) {
        var data = {
            action: 'sendUserJoincontestEmail',
            league_id: league_id,
            entry_number: entry_number
        };
        jQuery.post(ajaxurl, data, function (result) {});
    },

    loading: function ()
    {
        return '<div class="f-loading-indicator">\n\
            <div class="f-loading-circle f-loading-circle-1"></div>\n\
            <div class="f-loading-circle f-loading-circle-2"></div>\n\
            <div class="f-loading-circle f-loading-circle-3"></div>\n\
        </div>';
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

    addPlayerWithTeams: function (team_id, player_id) {
        if (!(team_id in this.aListTeamPlayer)) {
            this.aListTeamPlayer[team_id] = [];
        }
        var arr = this.aListTeamPlayer[team_id];
        arr.push(player_id);
        this.aListTeamPlayer[team_id] = arr;
    },
    deletePlayerWithTeams: function (team_id, player_id) {
        var arr = this.aListTeamPlayer[team_id];
        var index = arr.indexOf(player_id);
        if (index != -1) {
            arr.splice(index, 1);
            this.aListTeamPlayer[team_id] = arr;
        }

    },
    checkPlayerRegistriction: function (team_id, player_id) {
        if (!(team_id in this.aListTeamPlayer)) {
            return true;
        }
        var arr = this.aListTeamPlayer[team_id];
        if (jQuery('#player-restriction').text() != 0 && arr.length == jQuery('#player-restriction').text()) {
            return false;
        }
        return true;
    },
    showErrorRegistriction: function () {
        jQuery('.f-errorMessage').empty().append(wpfs['registriction_player'] + jQuery('#player-restriction').text() + wpfs['registriction_forteam']).slideToggle().delay(4000).fadeOut();

    },
    getPositionNameById: function (id) {
        var aPositions = jQuery.parseJSON(this.aPostiions);
        for (var i in aPositions) {
            if (aPositions[i].id == id) {
                return aPositions[i].name;
            }
        }
        return false;
    },

    rugbyLoadStatsByWeek: function (obj) {
        this.loadContestScores(jQuery('#leagueID').val(), jQuery('#entry_number').val())
    },
    validateQuantityChangePlayer: function (load_lineup) {

        if(this.allow_waiver_wire){
            if(load_lineup != 1)
            {
                var valid_number = 0;
                var playerIdPicks = jQuery.parseJSON(this.playerIdPicks);
                var new_list = [];
                jQuery('.f-roster-container li.f-has-player').each(function(){
                    if(playerIdPicks.indexOf(jQuery(this).data('id').toString()) == -1)
                    {
                        valid_number += 1;
                        new_list.push(jQuery(this).data('id'));
                    }
                })
                if(this.limit_quantity_change_player == jQuery('.f-roster-container li:not(.f-has-player)').length || valid_number >= this.limit_quantity_change_player)
                {
                    jQuery('.f-roster li.f-has-player .f-button').hide();
                    for(var i in new_list)
                    {
                        jQuery('#f-has-player' + new_list[i] + ' .f-button').show();
                    }
                }
                else
                {
                    jQuery('.f-roster li.f-has-player .f-button').show();
                }
            }
            jQuery('.f-roster li.f-has-player').each(function(){
                if(!jQuery(this).find('.f-button').is(':visible'))
                {
                    jQuery('#buttonRemove' + jQuery(this).data('id')).hide();
                }
                else if(jQuery(this).find('.f-button').is(':visible'))
                {

                    jQuery('#buttonAdd' + jQuery(this).data('id')).hide();
                    jQuery('#buttonRemove' + jQuery(this).data('id')).show();
                }
           });
        }
    },
    setEditInjuryPlayers: function () {
        jQuery('.f-roster-position.f-has-player').each(function () {
            var player_id = jQuery(this).attr('data-id');
            var player = jQuery.livedraft.findPlayer(player_id);
            if (player.indicator_id != 1) {
                jQuery(this).find('a.f-button').hide();
            }
        });
    },
    addInjuryPlayer: function (id) {
        var injury = this.injury_players;
        if (injury.indexOf(id) < 0) {
            injury.push(id.toString());
            this.injury_players = injury;
        }
    },
    removeInjuryPlayer: function (id) {
        var dialog = jQuery("#dlgRemoveInjuryArea").dialog({
            height: 'auto',
            width: '400',
            modal: true,
            title: wpfs['injury_remove_title'],
            open: function () {

            },
            buttons: {
                "Yes": function () {
                    var injury = jQuery.livedraft.injury_players;
                    id = id.toString();
                    if (injury.length > 0 && injury.indexOf(id) != -1) {
                        var index = injury.indexOf(id);
                        injury.splice(index, 1);
                        jQuery.livedraft.injury_players = injury;
                        jQuery.livedraft.loadPlayers();
                    }
                    dialog.dialog("close");
                },
                "No": function () {
                    dialog.dialog("close");
                }
            }
        });
    },
    showDialogRemoveInjury: function (id) {
        var dialog = jQuery("#dlgRemoveInjury").dialog({
            height: 'auto',
            width: '400',
            modal: true,
            title: wpfs['injury_remove_title'],
            open: function () {

            },
            buttons: {
                "OK": function () {
                    var move_action = jQuery('#dlgRemoveInjury input[name="move_action"]:checked').val();
                    move_action = parseInt(move_action);
                    switch (move_action) {
                        case 0: // move to IR area
                            jQuery('#buttonAdd' + id).css('display', 'block');
                            jQuery('#buttonAdd' + id).parents('tr').removeClass('f-player-in-lineup');
                            jQuery('#buttonRemove' + id).hide();
                            jQuery.livedraft.resetLineup(id);
                            jQuery.livedraft.addInjuryPlayer(id);
                            jQuery('#player_number_' + id).attr('data-ir', '1');
                            if (jQuery('.f-player-list-position-tabs .f-is-active').attr('data-id') == 'IR') {
                                jQuery.livedraft.loadPlayers();
                            }
                            break;
                        case 1: // move to Available area
                            jQuery('#buttonAdd' + id).css('display', 'block');
                            jQuery('#buttonAdd' + id).parents('tr').removeClass('f-player-in-lineup');
                            jQuery('#buttonRemove' + id).hide();
                            jQuery.livedraft.resetLineup(id);
                            jQuery('#player_number_' + id).removeAttr('data-ir').hide();
                            break;
                    }
                    dialog.dialog("close");
                }/*,
                 "No": function () {
                 dialog.dialog("close");
                 }*/
            }
        });
    },

    turnByTurn: function(){
        //turn by turn
        if(this.is_turn_by_turn == 1 && parseInt(this.limit_quantity_change_player) == parseInt(jQuery('.f-roster-position.f-has-player .f-button').length))
        {
            jQuery('.f-player-list-table .f-player-add a.f-player-add-button').hide();
        }
        jQuery('.f-player-list-table .f-player-remove-button').hide();
        jQuery('.f-roster-container .f-has-player').each(function(){
            if(jQuery(this).find('.f-button:visible').length > 0)
            {
                jQuery('#buttonRemove' + jQuery(this).data('id')).show();
            }
        });
    },

    loadPlayerTurnByTurn: function(){
        if(this.is_turn_by_turn == 1 && this.playerIdPicks != '')
        {
            var playerIdPicks = jQuery.parseJSON(this.playerIdPicks);
            var html = '<a class="f-button f-tiny f-text">\n\
                            <i class="fa fa-minus-circle"></i>\n\
                        </a>';
            jQuery('.f-player-list-table .f-player-add a.f-player-add-button').show();
            for (var i = 0; i < playerIdPicks.length; i++)
            {
                var player_id = playerIdPicks[i];
                if(player_id > 0)
                {
                    //jQuery('#f-has-player' + player_id + ' .f-button').remove();
                    jQuery('#buttonRemove' + player_id).remove();
                    jQuery('#buttonAdd' + player_id).remove();
                }
            }
            jQuery('.f-roster-position.f-has-player .f-button').each(function(){
                jQuery(this).remove();
            });
            jQuery('.f-roster-position:not(.f-has-player)').each(function(){
                if(jQuery(this).find('.f-button').length == 0)
                {
                    jQuery(this).append(html);
                }
            });
            
        }
    },

    liveDraft: function(current_turn, is_full_draft){
        //clear check changed turn interval
        this.clearCheckChangeTurnInterval();
        
        if(typeof current_turn == 'undefined' || current_turn == '')
        {
            current_turn = '';
        }
        var league = jQuery.parseJSON(this.league);
        var data = {
            'action': 'liveDraft',
            'leagueID': league.leagueID,
            'entry_number': league.entry_number,
            'current_turn' : current_turn
        };
        jQuery.post(ajaxurl, data, function (result) {
            if(typeof is_full_draft != 'undefined' && is_full_draft == 1)
            {
                window.location = jQuery('#entry_link').val();
            }
            result = jQuery.parseJSON(result);
            if(result.msg)
            {
                jQuery('.public_message').html(result.msg).show();
            }
            return jQuery.livedraft.setNewTurnData(result);
        });
    },
    
    setNewTurnData: function(result){
        if(/*result == 'null' || */result.is_full_draft == 1)
        {
            window.location = jQuery('#entry_link').val();
        }

        jQuery('#user_draft_turn').html(result.current_turn_user).attr('data-id', parseInt(result.current_turn));
        jQuery('#next_draft_turn').html(result.next_turn_user);
        jQuery.livedraft.playerIdPicks = JSON.stringify(result.player_pick_ids);
        jQuery.livedraft.current_turn = result.current_turn;
        jQuery.livedraft.except_player_ids = result.except_player_ids;
        /*for(var i in result.except_player_ids)
        {
            jQuery('#player_number_' + result.except_player_ids[i]).hide();
        }*/
        //jQuery.livedraft.aPlayers = current.aPlayers;
        jQuery.livedraft.loadPlayers();
        jQuery.livedraft.clearAllPlayer(1);
        //jQuery('.f-player-list-table tr').show();
        jQuery.livedraft.editLineup();
        if(result.current_turn != jQuery('#user_id'))
        {
            jQuery('#current_lineup').html(result.current_turn_user + "'s " + wpfs['lineup']);
        }
        else
        {
            jQuery('#current_lineup').html(wpfs['your_lineup']);
        }
        jQuery('#current_lineup').show();
        this.check_changed_turn = 1;
        jQuery('#userDraftRoom').val(parseInt(result.current_turn));
        jQuery.livedraft.check_changed_turn = 1;
        return jQuery.livedraft.turnByTurnCoundown(result.time_remaning);
    },
    
    checkCurrentTurn: function(current_turn_user_id){
        var user_id = jQuery('#user_id').val();
        if(user_id == current_turn_user_id)
        {
            jQuery('#btnSubmit').removeAttr('disabled').show();
            //jQuery.livedraft.loadPlayerTurnByTurn();
            //jQuery('.public_message').hide();
        }
        else
        {
            jQuery('#btnSubmit').attr('disabled', 'disabled').hide();
            jQuery('#listPlayers tbody .f-player-add-button').hide();
            jQuery('#listPlayers tbody .f-player-remove-button').hide();
        }
    },
    
    turnByTurnCoundown: function(timestamp){
        //check allow check change turn function runs
        this.allowCheckChangeTurnInterval();
        
        var targetDate = this.getTimestamp();
        if(typeof timestamp != 'undefined')
        {
            targetDate = new Date(parseInt(timestamp) * 1000);
        }
        var clock = jQuery("#change-player-countdown");
        var current_turn = this.current_turn;
        clock.countdown(targetDate, function(event) {
            jQuery(this).text(
                event.strftime('%M:%S')
            );
        }).on('finish.countdown', function(e) {
            if(jQuery.livedraft.check_changed_turn == 1)
            {
                clock.after('<span id="change-player-countdown"></span>').remove();
                jQuery.livedraft.check_changed_turn = 0;
                if(current_turn != jQuery('#user_id').val())
                {
                    current_turn = '';
                }
                return jQuery.livedraft.liveDraft(current_turn);   
            }
        });
    },
    
    getTimestamp: function() {
        return new Date(new Date().valueOf() + jQuery('#minute_change_player').val() * 60 * 1000);
    },

    resetCountdown: function(timestamp){
        var targetDate = new Date(parseInt(timestamp) * 1000);
        this.clock.countdown(targetDate);
    },
    
    loadContestScores: function ()
    {
        var data = {
            'action': 'liveDraftLoadContestScores',
            'leagueID': jQuery('#leagueID').val()
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery('#tableScores tbody').html(result);
        });
    },
    
    loadOpponentScores: function ()
    {
        var data = {
            'action': 'liveDraftLoadOpponentScores',
            'leagueID': jQuery('#leagueID').val(),
            'id': jQuery('#opponent').val(),
            'week': jQuery('#week').val()
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery('#result_detail_data').html(result);
        });
    },
    
    addBench: function(id, player)
    {
        var league = jQuery.parseJSON(this.league);
        id = typeof player != 'undefined' ? player.id : this.findPlayer(id);
        player = typeof player != 'undefined' ? player : this.findPlayer(id);
        //check all main positions picked
        var is_full_pick = 1;
        jQuery('.f-roster-position').each(function(){
            if(jQuery(this).data('position') > 0 && !jQuery(this).hasClass('f-has-player'))
            {
                is_full_pick = 0;
            }
        });
        if(this.bench_quantity > 0 && typeof player != 'undefined' && is_full_pick == 1)
        {
            var item = jQuery('.player-position-0:not(.f-has-player)').first();
            if (item.length == 1)
            {
                var match = '';
                if (league.only_playerdraft == 0)
                {
                    if (player.teamID1 == player.team_id)
                    {
                        match = '<b>' + player.team1 + '</b>@' + player.team2;
                    } 
                    else
                    {
                        match = player.team1 + '@<b>' + player.team2 + '</b>';
                    }
                }
                
                item.addClass('f-has-player');
                item.attr('id', 'f-has-player' + id);
                item.attr('data-id', id);
                item.find('.f-empty-roster-slot-instruction').hide();
                item.find('.f-player-image').empty().append('<img src="' + player.full_image_path + '" onerror="jQuery.livedraft.setNoImage(jQuery(this))" />');
                item.find('.f-player').empty().append(player.name).show().attr("onclick", "jQuery.livedraft.playerInfo(" + player.id + ")");
                //item.find('.f-salary').empty().append(VIC_FormatMoney(player.salary)).show();
                item.find('.f-fixture').empty().append(match);
                item.find('.f-button').show().attr('onclick', 'jQuery.livedraft.clearPlayer(' + id + ')');
                return 1;
            }
        }
        return 0;
    },
    
    liveDraftCheckChangedTurn: function()
    {
        if(this.is_turn_by_turn && this.current_turn == jQuery('#user_id').val())
        {
            this.clearCheckChangeTurnInterval();
        }
        if(this.check_changed_turn == 1)
        {
            var league = jQuery.parseJSON(this.league);
            var data = {
                'action': 'liveDraftCheckChangedTurn',
                'leagueID': league.leagueID,
                'current_turn' : this.current_turn
            };
            jQuery.post(ajaxurl, data, function (result) {
                var result = jQuery.parseJSON(result);
                if(result.msg)
                {
                    jQuery('.public_message').html(result.msg).show();
                }
                if(result.is_full_draft == 1)
                {
                    window.location = jQuery('#entry_link').val();
                }
                if(result.result == 1)
                {
                    jQuery.livedraft.check_changed_turn = 0;
                    jQuery("#change-player-countdown").after('<span id="change-player-countdown"></span>').remove();
                    jQuery.livedraft.liveDraft();
                }
            });
        }
    },
    
    benchLoadPlayers: function(){
        if(this.is_bench == 1)
        {
            jQuery('.f-player-list-table .f-player-in-lineup .f-player-remove-button').hide();
        }
    },
    
    liveDraftGetUserInDraftRoom: function(){
        var league = jQuery.parseJSON(this.league);
        var data = {
            'action': 'liveDraftGetUserInDraftRoom',
            'leagueID': league.leagueID,
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery('#userDraftRoomData').html(result);
        });
    },
    
    liveDraftSeeUserLineup: function(){
        var league = jQuery.parseJSON(this.league);
        var data = {
            'action': 'liveDraftSeeUserLineup',
            'leagueID': league.leagueID,
            'userID' : jQuery('#userDraftRoom').val()
        };
        jQuery.post(ajaxurl, data, function (result) {
            result = jQuery.parseJSON(result);
            if(result.length > 0)
            {
                jQuery.livedraft.clearAllPlayer(1);
                for(var key in result)
                {
                    jQuery.livedraft.addPlayer(result[key].player_id, '', result[key]);
                }
                if(parseInt(jQuery('#user_draft_turn').attr('data-id')) != parseInt(jQuery('#userDraftRoom').val()) || parseInt(jQuery('#user_id').val()) != parseInt(jQuery('#userDraftRoom').val()))
                {
                    jQuery('.f-player-add-button').hide();
                    jQuery('.f-player-remove-button').hide();
                    jQuery('.f-has-player .f-button').hide();
                }
            }
        });
    },
    
    loadOpponentByweek: function()
    {
        jQuery('#opponent optgroup').hide();
        jQuery('#opponent_week_' + jQuery('#week').val()).show();
        jQuery('#opponent_week_' + jQuery('#week').val() + ' option:nth-child(1)').attr("selected", "selected");
    },
    
    liveEntriesResult: function(poolID, leagueID, entry_number, is_live_draft)
    {
         var data = {
            action: 'liveEntriesResult',
            poolID: poolID,
            leagueID: leagueID
        };
        jQuery.post(ajaxurl, data, function(result) {
            jQuery.livedraft.loadFixtureScores(leagueID);
            jQuery.livedraft.loadOpponentScores();
            jQuery.livedraft.loadContestScores();
        })
    },
    
    playerInfo: function (player_id)
    {
        var league = jQuery('#dataLeague').html().trim();
        league = jQuery.parseJSON(league);
        var orgID = league.organization;
        //var player = this.findPlayer(player_id);
        jQuery.playerdraft.showDialog('#dlgInfo', this.loading());
        jQuery.post(ajaxurl, "action=loadPlayerStatistics&orgID=" + orgID + '&playerID=' + player_id + '&poolID=' + league.poolID, function (result) {
            jQuery('#dlgInfo .f-body').html(result);
            jQuery(".f-player-stats-lightbox").tabs({active: 0});
            
            //check show remove add button
            if (!jQuery.playerdraft.isPlayerInline(player_id))
            {
                jQuery('.f-player-stats-lightbox #btnRemove').hide();
                jQuery('.f-player-stats-lightbox #btnAdd').show();
            }
            else
            {
                jQuery('.f-player-stats-lightbox #btnAdd').hide();
                jQuery('.f-player-stats-lightbox #btnRemove').show();
            }
            
            //show game
            if(jQuery('#playerGame').length > 0)
            {
                jQuery('#playerGame').html(jQuery('#player_number_' + player_id + ' .f-player-fixture').html());
            }
            
            //load player news from google
            if(jQuery('#playerNews').data('google') == 1)
            {
                jQuery.playerdraft.loadPlayerNews();
            }
        })
    },
};

jQuery(document).on('keyup', '#player-search', function () {
    jQuery.livedraft.searchPlayers();
});