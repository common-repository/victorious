var result_select_id = "";
var result_select_page = "";
jQuery.playerdraft =
{
    setData: function ()
    {
        this.aListTeamPlayer = {};
        this.aPlayers = jQuery('#dataPlayers').html();
        this.salaryRemaining = jQuery('#dataSalaryRemaining').html();
        this.salaryCap = jQuery('#dataSalaryRemaining').html();
        this.playerIdPicks = jQuery('#dataPlayerIdPicks').html();
        this.league = jQuery('#dataLeague').html();
        this.aFights = jQuery('#dataFights').html();
        this.aPool = jQuery('#dataPool').html();
        this.aIndicators = jQuery('#dataIndicators').html();
        this.scoringCat = '';
        this.is_soccer = jQuery('#is-soccer').length > 0 ? jQuery('#is-soccer').html() : '';
        this.is_soccer_flex = jQuery('#is-soccer-flex').html();
        this.is_soccer_field = jQuery('#is-soccer-field').html();
        this.list_position_soccer = jQuery('#list-position-soccer').length > 0 ? JSON.parse(jQuery('#list-position-soccer').html()) : '';
        this.extra_positions = jQuery('#extra-positions').length > 0 ? JSON.parse(jQuery('#extra-positions').html()) : '';
        this.aPostiions = jQuery('#dataPositions').html();
        this.checkPlayerButtonDisplay();
        //player news paging
        jQuery(document).on('click', '#player_news_paging li a', function(e){
            e.preventDefault();
            e.stopPropagation();
            jQuery.playerdraft.loadPlayerNews(jQuery(this).attr('href'));
        })
        this.calculateAvgPerPlayer();
    },
    golfSkinSetData: function ()
    {
        this.aPlayers = jQuery('#dataPlayers').html();
        this.salaryRemaining = jQuery('#dataSalaryRemaining').html();
        this.salaryCap = jQuery('#dataSalaryRemaining').html();
        this.playerIdPicks = jQuery('#dataPlayerIdPicks').html();
        this.league = jQuery('#dataLeague').html();
        this.aFights = jQuery('#dataFights').html();
        this.aPool = jQuery('#dataPool').html();
        this.aIndicators = jQuery('#dataIndicators').html();
        this.scoringCat = '';
        this.totalMoney = jQuery('#dataTotalMoney').html();
        this.balance = jQuery('#dataBalance').html();
        this.entry_fee = jQuery('#dataentryFee').html();
        this.is_entry_fee = jQuery('#dataIsEntryFee').html();
        aGolfSkinPlayers = JSON.parse(jQuery('#dataPlayerGolfSkin').html());
        if (jQuery.isArray(aGolfSkinPlayers)) {
            this.aGolfSkinPlayers = {};
        } else {
            this.aGolfSkinPlayers = aGolfSkinPlayers;
        }
    },
    mixSetData: function ()
    {
        this.aListTeamPlayer = {};
        this.aPlayers = jQuery('#dataPlayers').html();
        this.salaryRemaining = jQuery('#dataSalaryRemaining').html();
        this.salaryRemaining = jQuery.parseJSON(this.salaryRemaining);
        this.salaryCap = jQuery('#dataSalaryRemaining').html();
        this.salaryCap = jQuery.parseJSON(this.salaryCap);
        this.playerIdPicks = jQuery('#dataPlayerIdPicks').html();
        this.league = jQuery('#dataLeague').html();
        this.aFights = jQuery('#dataFights').html();
        this.aPool = jQuery('#dataPool').html();
        this.aIndicators = jQuery('#dataIndicators').html();
        this.scoringCat = '';
        this.aLineUps = jQuery('#dataLineups').html();
        this.aPostiions = jQuery('#dataPositions').html();
        this.currentOrgId = Object.keys(this.salaryRemaining)[0];
    },
    setMotocross: function () {
        this.aListTeamPlayer = {};
        this.aMotoPos = {};
        this.aLineUps = jQuery.parseJSON(jQuery('#dataLineups').html());

        var player_picks = jQuery.parseJSON(this.playerIdPicks);
        if (typeof player_picks != 'undefined' && player_picks != null) {
            for (var i in player_picks) {
                var player_id = player_picks[i].player_id;
                var pos = player_picks[i].pos;
                this.addMotocrossPlayer(null, player_id, pos)
                jQuery('.f-player-list-table-motocross select.mt_pos_select[data-id="' + player_id + '"]').children('option[value="' + pos + '"]').attr('selected', 'selected');
            }
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
        if(position_id > 0 || position_id == 'IR' || typeof teamId1 != 'undefined' || typeof teamId2 != 'undefined' || keyword != '' || round_squad != '')
        {
            jQuery('.f-player-list-table tbody tr').hide();
            jQuery('.f-player-list-table tbody tr').each(function(){
                if (keyword == '' || jQuery(this).data('player_name').toString().trim().search(new RegExp(keyword, 'i')) > -1)
                {
                    if ((typeof teamId1 == 'undefined' && typeof teamId2 == 'undefined') || (jQuery(this).data('team') == teamId1 || jQuery(this).data('team') == teamId2))
                    {
                        if ((jQuery(this).data('position') == position_id) || (jQuery(this).data('secondPosition') == position_id) || position_id == '' || position_id == 'IR' || (round_squad != '' && jQuery.inArray(parseInt(jQuery(this).data('id')), round_squad) != -1))
                        {
                            if (position_id == 'IR')
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
                if(jQuery(this).find('.f-player-remove-button').is(':visible'))
                {
                    jQuery(this).find('.f-player-add-button').hide();
                }
            })
        }
        else
        {
            jQuery('.f-player-list-table tbody tr').show();
        }
        this.checkSignButtonForFullPosition();
        return false;
    },
    loadMotocrossPlayer: function () {

        var position_id = jQuery('.f-tabs li a.f-is-active').attr('data-id');
        var teamId1 = jQuery('.fixture-item.f-is-active').attr('data-team-id1');
        var teamId2 = jQuery('.fixture-item.f-is-active').attr('data-team-id2');
        var aPool = jQuery.parseJSON(this.aPool);
        var aPlayers = jQuery.parseJSON(this.aPlayers);
        var keyword = jQuery('#motocross-player-search').val().toString();
        var lineUps = jQuery('#dataLineups').html();
        lineUps = jQuery.parseJSON(lineUps);
        var privateer_id = jQuery('#privater-id').html();
        var html_select = '';
        html_select += '<option value=0>Select place for racer</option>';
        for (var i in lineUps) {
            html_select += '<option value="' + lineUps[i].id + '">' + lineUps[i].name + '</option>';
        }
        html_select += '</select>';
        if (aPlayers.length > 0)
        {
            var html = '';
            for (var i = 0; i < aPlayers.length; i++)
            {
                var aPlayer = aPlayers[i];
                if (keyword == '' || aPlayer.name.toString().search(new RegExp(keyword, 'i')) > -1)
                {
                    if ((typeof teamId1 == 'undefined' && typeof teamId2 == 'undefined') ||
                            (aPlayer.team_id == teamId1 || aPlayer.team_id == teamId2)
                            )
                    {
                        if ((aPlayer.position_id == position_id) ||
                                position_id == '')
                        {
                            var match = '';
                            //indicator
                            var htmlIndicator = '';
                            switch (aPlayer.indicator_alias)
                            {
                                case 'IR':
                                    htmlIndicator = '<span class="f-player-badge f-player-badge-injured-out">IR</span>';
                                    break;
                                case 'O':
                                    htmlIndicator = '<span class="f-player-badge f-player-badge-injured-out">O</span>';
                                    break;
                                case 'D':
                                    htmlIndicator = '<span class="f-player-badge f-player-badge-injured-possible">D</span>';
                                    break;
                                case 'Q':
                                    htmlIndicator = '<span class="f-player-badge f-player-badge-injured-possible">Q</span>';
                                    break;
                                case 'P':
                                    htmlIndicator = '<span class="f-player-badge f-player-badge-injured-probable">P</span>';
                                    break;
                                case 'NA':
                                    htmlIndicator = '<span class="f-player-badge f-player-badge-injured-out">NA</span>';
                                    break;
                            }
                            var positionName = aPlayer.position;
                            if (aPool.no_position == 1)
                            {
                                positionName = '&nbsp;';
                            }

                            //pitcher for mlb
                            var htmlPitcher = '';

                            html += '<tr class="f-pR" data-role="player">\n\
                                <td class="f-player-name">\n\
                                    <div onclick="jQuery.playerdraft.playerInfo(' + aPlayer.id + ')">' + aPlayer.name + htmlPitcher + htmlIndicator + '</div>\n\
                                </td>';
                            var player_country = '';
//                                if(typeof aPlayer.country != 'undefined' && aPlayer.country != null){
//                                    player_country = aPlayer.country;
//                                }
                            var player_country = (aPlayer.is_privateers == 1) ? "Privateer" : '';
                            var last_race = (typeof aPlayer.last_motocross_race != 'undefined' && aPlayer.last_motocross_race != null) ? aPlayer.last_motocross_race : '';
                            html +=
                                    '<td class="f-player-country">' + player_country + '</td>\n\
                                 <td><class="f-player-last-race">' + last_race + '</td>                           \n\
                                <td class="f-player-select">';

                            if (aPlayer.disable == 0)
                            {
                                html += '<select data-id="' + aPlayer.id + '"  class="mt_pos_select" onchange="jQuery.playerdraft.addMotocrossPlayer(this,' + aPlayer.id + ')">' + html_select;

                            }
                            html += '</td>\n\
                            </tr>';
                        }
                    }
                }
            }
            if (html != '')
            {
                jQuery('#listPlayers tbody').empty().append(html);
                jQuery('#listPlayers .f-player-list-empty').hide();

            } else
            {
                jQuery('#listPlayers tbody').empty();
                jQuery('#listPlayers .f-player-list-empty').show();
            }
        }

        jQuery('.mt_pos_select').each(function () {
            var id = jQuery(this).attr('data-id');
            var player = jQuery.playerdraft.findPlayer(id);
            if (player.is_privateers == "0") {
                jQuery(this).find('option[value=' + privateer_id + ']').remove();
            }
        });
        return false;
    },
    addMotocrossPlayer: function (obj, id, position) {
        var player = this.findPlayer(id);
        if (typeof player != 'undefined' && player != null) {
            if (typeof position != 'undefine' && position != null) {
                var position_id = position;
            } else {
                var position_id = jQuery(obj).val();
            }

            if (this.checkExistMotoPos(position_id) == true && this.aMotoPos[position_id] != id) {
                var old_p_id = this.aMotoPos[position_id];
                this.clearPlayer(old_p_id);

            }
            var item = jQuery('.player-position-' + position_id + ':not(.f-has-player)').first();
            var match = '';
            item.addClass('f-has-player');
            item.attr('id', 'f-has-player' + id);
            item.attr('data-id', id);
            item.find('.f-empty-roster-slot-instruction').hide();
            item.find('.f-player-image').empty().show().append('<img src="' + player.full_image_path + '" onerror="jQuery.playerdraft.setNoImage(jQuery(this))" />');
            item.find('.f-player').empty().append(player.name).css('visibility', 'visible').attr("onclick", "jQuery.playerdraft.playerInfo(" + player.id + ")");
            item.find('.f-fixture').empty().append(match);
            item.find('.f-button').css('visibility', 'visible');
            if (player.disable == 1)
            {
                item.find('.f-button').remove();
            } else
            {
                item.find('.f-button').attr('onclick', 'jQuery.playerdraft.clearMotocrossPlayer(' + id + ',' + position_id + ')');
            }
            // add selected value
            this.addMotocrossValue(position_id, id);

        }



    },
    getSizeMtObject: function () {
        return Object.keys(this.aMotoPos).length;
    },
    checkMotocrossPosUnique: function (pos_id) {
        for (var i in this.aLineUps) {
            if (this.aLineUps[i].id == pos_id && this.aLineUps[i].is_unique == 1) {
                return true;
            }
        }
        return false;
    },
    checkExistMotoPos: function (position_id) {
        if ((position_id in this.aMotoPos)) {
            return true;
        }
        return false;
    },
    addMotocrossValue: function (pos_id, p_id) {


        this.aMotoPos[pos_id] = p_id;

        jQuery('.f-player-list-table-motocross select.mt_pos_select').each(function () {
            var objSelect = jQuery(this);
            objSelect.children('option[value="' + pos_id + '"]').hide();
            if (objSelect.attr('data-id') == p_id) {

                var options = objSelect.children('option');
                options.each(function () {
                    var option = jQuery(this);
                    var option_value = option.val();
                    if (option_value == 0) {
                        jQuery(this).hide();
                    }

                    var is_unique = jQuery.playerdraft.checkMotocrossPosUnique(option_value);
                    var is_current_unique = jQuery.playerdraft.checkMotocrossPosUnique(pos_id);
                    if (is_unique == true && is_current_unique == true) {
                        option.hide();
                    }
                    if (option_value == pos_id) {
                        option.show();
                    }
                });
            }

        });
    },
    mixingGetPoolByOrgID: function (org_id)
    {
        var aPool = jQuery.parseJSON(this.aPool);
        for (var i in aPool) {
            if (aPool[i].organization == org_id) {
                return aPool[i];
            }
        }
        return false;
    },

    mixingLoadPlayers: function ()
    {
        var org_id = jQuery("#mixing_orginazation_id").val();
        var position_id = jQuery('.f-tabs li a.f-is-active').attr('data-id');
        var teamId1 = jQuery('.fixture-item.f-is-active').attr('data-team-id1');
        var teamId2 = jQuery('.fixture-item.f-is-active').attr('data-team-id2');
        var aPool = jQuery.playerdraft.mixingGetPoolByOrgID(org_id);
        var aPlayers = jQuery.parseJSON(this.aPlayers);
        var aIndicators = jQuery.parseJSON(this.aIndicators);
        var keyword = jQuery('#mixing-player-search').val().toString();
        var teamOrgID = jQuery('.fixture-item.f-is-active').attr('data-sport-id');


        if (typeof teamOrgID != 'undefined' && teamOrgID != org_id) {

//            jQuery('.f-pick-your-team').find('*').removeClass('s-sport-is-active');
//            var current_sport = jQuery('.fixture-item.f-is-active').parent('.f-fixture-picker-button-container').find('a:first');
//            jQuery.playerdraft.mixingSelectTypeSport(current_sport,teamOrgID,'Unlimited','Add PLAYERS');
//            return;
        }
        aPlayers = aPlayers[org_id];
        aIndicators = aIndicators[org_id];

        if (aPlayers.length > 0)
        {
            var html = '';
            for (var i = 0; i < aPlayers.length; i++)
            {
                var aPlayer = aPlayers[i];
                if (keyword == '' || aPlayer.name.toString().search(new RegExp(keyword, 'i')) > -1)
                {
                    if ((typeof teamId1 == 'undefined' && typeof teamId2 == 'undefined') ||
                            (aPlayer.team_id == teamId1 || aPlayer.team_id == teamId2)
                            )
                    {
                        if ((aPlayer.position_id == position_id) ||
                                position_id == '')
                        {
                            var match = '';
                            if (aPlayer.teamID2 == aPlayer.team_id)
                            {
                                match = '<b>' + aPlayer.team2 + '</b>@' + aPlayer.team1;
                            } else
                            {
                                match = aPlayer.team2 + '@<b>' + aPlayer.team1 + '</b>';
                            }

                            //indicator
                            var htmlIndicator = '';
                            switch (aPlayer.indicator_alias)
                            {
                                case 'IR':
                                    htmlIndicator = '<span class="f-player-badge f-player-badge-injured-out">IR</span>';
                                    break;
                                case 'O':
                                    htmlIndicator = '<span class="f-player-badge f-player-badge-injured-out">O</span>';
                                    break;
                                case 'D':
                                    htmlIndicator = '<span class="f-player-badge f-player-badge-injured-possible">D</span>';
                                    break;
                                case 'Q':
                                    htmlIndicator = '<span class="f-player-badge f-player-badge-injured-possible">Q</span>';
                                    break;
                                case 'P':
                                    htmlIndicator = '<span class="f-player-badge f-player-badge-injured-probable">P</span>';
                                    break;
                                case 'NA':
                                    htmlIndicator = '<span class="f-player-badge f-player-badge-injured-out">NA</span>';
                                    break;
                            }
                            var positionName = aPlayer.position;
                            if (aPool.no_position == 1)
                            {
                                positionName = '&nbsp;';
                            }

                            //pitcher for mlb
                            var htmlPitcher = '';
                            if (aPlayer.is_pitcher == 1)
                            {
                                htmlPitcher = ' <span class="f-player-badge f-player-badge-injured-possible">S</span> ';
                            }
                            html += '<tr class="f-pR" data-role="player">\n\
                                <td class="f-player-position">' + positionName + '</td>\n\
                                <td class="f-player-name">\n\
                                    <div onclick="jQuery.playerdraft.playerInfo(' + aPlayer.id + ')">' + aPlayer.name + htmlPitcher + htmlIndicator + '</div>\n\
                                </td>';
                            if (aPool.only_playerdraft == 0)
                            {
                                html +=
                                        '<td class="f-player-played">' + aPlayer.myteam + '</td>\n\
                                <td class="f-player-fixture">' + match + '</td>';
                            }
                            html +=
                                    '<td class="f-player-salary">' + VIC_FormatMoney(aPlayer.salary) + '</td>\n\
                                <td class="f-player-add">';
                            if (aPlayer.disable == 0)
                            {
                                html +=
                                        '<a class="f-button f-tiny f-text f-player-add-button" id="buttonAdd' + aPlayer.id + '" onclick="jQuery.playerdraft.addPlayer(' + aPlayer.id + ')">\n\
                                <i class="fa fa-plus-circle"></i>\n\
                            </a>\n\
                            <a class="f-button f-tiny f-text f-player-remove-button" id="buttonRemove' + aPlayer.id + '" onclick="jQuery.playerdraft.clearPlayer(' + aPlayer.id + ')">\n\
                                <i class="fa fa-minus-circle"></i>\n\
                            </a>';
                            }
                            html += '</td>\n\
                            </tr>';
                        }
                    }
                }
            }
            if (html != '')
            {
                jQuery('#listPlayers tbody').empty().append(html);
                jQuery('#listPlayers .f-player-list-empty').hide();
                jQuery('th.f-player-salary').trigger('click');
                jQuery('th.f-player-salary').trigger('click');

                //check player in line
                jQuery('.f-roster-position').each(function () {
                    var id = jQuery(this).attr('data-id');
                    jQuery('#buttonAdd' + id).hide();
                    jQuery('#buttonAdd' + id).parents('tr').addClass('f-player-in-lineup');
                    jQuery('#buttonRemove' + id).css('display', 'block');
                })
            } else
            {
                jQuery('#listPlayers tbody').empty();
                jQuery('#listPlayers .f-player-list-empty').show();
            }
        }
        return false;
    },
    mixingSelectTypeSport: function (item, org_id, salary_ullimited, sAddPlayer) {
        this.currentOrgId = org_id;
        jQuery('.f-pick-your-team').find('*').removeClass('s-sport-is-active');
        jQuery(item).addClass('s-sport-is-active');
        jQuery("#mixing_orginazation_id").val(org_id);
        // clear all players from selected
//       jQuery('.f-roster .f-roster-position').each(function(){
//                if(typeof jQuery(this).attr('data-id') != typeof undefined)
//                {
//                    jQuery.playerdraft.clearPlayer(jQuery(this).attr('data-id'));
//                }
//            });
        jQuery.playerdraft.mixingLoadPlayers();
        // handle salary
//        var htmlSalary = '';
//        var aPool = jQuery.playerdraft.mixingGetPoolByOrgID(org_id);
//        var salary = aPool.salary_remaining;
//        if(salary > 0){
//            htmlSalary = '$'+salary;
//        }else{
//            htmlSalary = salary_ullimited;
//        }
//        jQuery('#salaryRemaining').html(htmlSalary);
//        this.salaryRemaining = salary;

        // handle available players
        var aPositions = jQuery.parseJSON(this.aPostiions);
        aPositions = aPositions[org_id];
        var htmlPositions = '';
        for (var i in aPositions) {
            htmlPositions += '<li>';
            htmlPositions += '<a href="" data-id="' + aPositions[i].id + '" onclick="jQuery.playerdraft.setActivePosition(this);return jQuery.playerdraft.mixingLoadPlayers();">';
            htmlPositions += aPositions[i].name + '</a>';
            htmlPositions += '</li>';
        }
        jQuery('ul.f-player-list-position-tabs>li').not('li:first,li:last').remove();
        jQuery('ul.f-player-list-position-tabs>li:first').after(htmlPositions);
        jQuery('ul.f-player-list-position-tabs>li:first a').click();



        // handle lineup
//        var aLineups = jQuery.parseJSON(this.aLineUps);
//        var htmlLineups = '';
//        aLineups = aLineups[org_id];
//        if(aLineups.constructor === Array){
//            for(var i in aLineups){
//                var lineUp = aLineups[i];;
//                for(var j = 0; j < lineUp.total; j++){
//                    htmlLineups+='<li class="f-roster-position f-count-0 player-position-'+aLineups[i].id+'">';
//                    htmlLineups+='<div class="f-player-image"></div>';
//                    htmlLineups+= '<div class="f-position">'+aLineups[i].name;
//                    htmlLineups+= '<span class="f-empty-roster-slot-instruction">'+sAddPlayer+'</span></div>';
//                    htmlLineups+= '<div class="f-player"></div>';
//                    htmlLineups+= '<div class="f-salary">$0</div>';
//                    htmlLineups+= '<div class="f-fixture"></div>';
//                    htmlLineups+= '<a class="f-button f-tiny f-text">';
//                    htmlLineups+= '<i class="fa fa-minus-circle"></i>';
//                    htmlLineups+= '</a></li>';
//                }
//            }
//           jQuery('section.f-roster ul').empty().html(htmlLineups);
//        }

        // handle avg salary
        //jQuery.playerdraft.calculateAvgPerPlayer();

        // handle avg salary
        jQuery('.f-roster ul li').hide();
        jQuery('.f-roster ul li.cls-sport-' + org_id).show();

        var salary_remaining = this.salaryRemaining[org_id];
        jQuery('#salaryRemaining').empty().append(VIC_FormatMoney(Math.round(salary_remaining)));
        this.calculateAvgPerPlayer();
        if (this.salaryRemaining[org_id] > 0) {
            jQuery('#salaryRemaining').removeClass('f-error');
        } else {
            jQuery('#salaryRemaining').addClass('f-error');
        }
        if (this.salaryCap[org_id] == 0) {
            jQuery('#salaryRemaining').removeClass('f-error').html('Unlimited');
        }

        this.fixMixingSportIndexBtn();
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

    mixingLoadPlayersixture: function (item)
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

    doSort: function (item)
    {
        jQuery("#listPlayers table").tablesorter();
        jQuery("#listPlayers table").trigger("updateAll");
        var index = item.index() + 1;
        jQuery("#listPlayers table").trigger("sorton", [[[index, "n"]]]);
        if (this.sortIndex != index)
        {
            this.sortType = '';
            this.sortIndex = index;
        }
        item.parent().find('.f-icon').hide();
        if (this.sortType == 'asc')
        {
            item.find('.f-sorted-desc').show();
            this.sortType = 'desc';
        } else if (this.sortType == 'desc')
        {
            item.find('.f-sorted-asc').show();
            this.sortType = 'asc';
        } else
        {
            this.sortType = 'asc';
            item.find('.f-sorted-asc').show();
        }
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
        }

        // for mixing league
        if (jQuery('#type_league').val() == 'mixing') {
            var org_id = Object.keys(this.salaryCap)[0];
            var section = jQuery('.f-pick-your-team section[data-org-id="' + org_id + '"]');
            section.find('a:first-child').trigger('click');
        }
    },
    getPlayerInfo: function (p_id) {
        var players = jQuery.parseJSON(jQuery.playerdraft.aPlayers);
        for (var org_id in players) {
            for (var i in players[org_id]) {
                if (players[org_id][i].id == p_id) {
                    return players[org_id][i];
                }
            }
        }
    }
    ,
    mixingGetOrgIDByPlayerID: function (player_id)
    {
        var aPlayers = jQuery.parseJSON(this.aPlayers);
        for (var org in aPlayers) {
            for (var i in aPlayers[org]) {
                if (aPlayers[org][i].id == player_id) {
                    return org;
                }
            }
        }
    },
    mixingGetDetailPlayerByPlayerID: function (player_id)
    {
        var aPlayers = jQuery.parseJSON(this.aPlayers);
        for (var org in aPlayers) {
            for (var i in aPlayers[org]) {
                if (aPlayers[org][i].id == player_id) {
                    return aPlayers[org][i];
                }
            }
        }
    },
    addPlayer: function (id, load_lineup, player)
    {
        var aPool = jQuery.parseJSON(this.aPool);
        player = typeof player != 'undefined' ? player : this.findPlayer(id);
        // for mixing sport game
        if (jQuery("#type_league").val() == 'mixing') {

            if (this.playerIdPicks != '') {
                player = jQuery.playerdraft.mixingGetDetailPlayerByPlayerID(id);
                org_id = jQuery.playerdraft.mixingGetOrgIDByPlayerID(player.id);
                aPool = jQuery.playerdraft.mixingGetPoolByOrgID(org_id);
            } else {
                var org_id = jQuery('#mixing_orginazation_id').val();
                aPool = jQuery.playerdraft.mixingGetPoolByOrgID(org_id);
            }
        }

        if (typeof player != 'undefined')
        {
            var is_nfl_flex = jQuery('.f-player-list-position-tabs li a.f-is-active').data('isFlex');
            var position_id = is_nfl_flex == 1 ? player.second_position_id : player.position_id;
            if (aPool.no_position == 1)
            {
                position_id = 0;
            }
            if (jQuery("#game_type").val() == 'GOLFSKIN') {
                var item = jQuery('.player-position' + ':not(.f-has-player)').first();
            } else {
                var item = jQuery('.player-position-' + position_id + ':not(.f-has-player)').first();

                //nfl flex
                if(load_lineup == 1 && item.length != 1){
                    position_id = player.second_position_id;
                    item = jQuery('.player-position-' + position_id + ':not(.f-has-player)').first();
                }
            }
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
                        var _player = jQuery.playerdraft.findPlayer(data_id);
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

                if (jQuery("#game_type").val() == 'GOLFSKIN') {
                    var round_id = jQuery(".f-fixture-picker-button-container .f-is-active").attr('data-id');
                    if (!jQuery.isArray(this.aGolfSkinPlayers[round_id])) {
                        this.aGolfSkinPlayers[round_id] = [];
                    }
                    if (this.aGolfSkinPlayers[round_id].indexOf(id) === -1) {
                        this.aGolfSkinPlayers[round_id].push(id);
                        this.golfSkinCaculateMoney('plus');
                    }

                }

                // position step
                if (typeof this.is_soccer != 'undefined' && this.is_soccer != '' && typeof list_position_soccer != 'undefined') {
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
                if (aPool.only_playerdraft == 0)
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

                if(typeof item.attr('data-constructor') != 'undefined')
                {
                    item.find('.f-player-image').empty().append('<img src="' + player.game_image_path + '" onerror="jQuery.playerdraft.setNoImage(jQuery(this))" />');
                }
                else
                {
                    item.find('.f-player-image').empty().append('<img src="' + player.full_image_path + '" onerror="jQuery.playerdraft.setNoImage(jQuery(this))" />');
                }
                item.find('.f-player').empty().append(player.name).show().attr("onclick", "jQuery.playerdraft.playerInfo(" + player.id + ")");
                item.find('.f-salary').empty().append(VIC_FormatMoney(player.salary, "$")).show();
                item.find('.f-fixture').empty().append(match);
                item.find('.f-button').show();
                if(typeof item.find('.f-team') != 'undefined' && typeof player.team_name != 'undefined')
                {
                    item.find('.f-team').html(player.team_name);
                }

                // custome for soccer field
                if (this.is_soccer_field) {

                    var match_salary = player.team1 + '@<b>' + player.team2 + ' ' + VIC_FormatMoney(player.salary, "$");
                    var pos_name = player.position + ' ' + player.name;
                    var item_field = jQuery('#custom-field-soccer .position-' + position_id + ':not(.field-has-player)').first();
                    item_field.attr('id', 'soccer-player-' + player.id);
                    item_field.addClass('field-has-player');
                    item_field.find('.f-player-image').empty().html('<img src="' + player.full_image_path + '" onerror="jQuery.playerdraft.setNoImage(jQuery(this))" />');
                    item_field.find('.team-salary').empty().html(match_salary);
                    item_field.find('.pos-player').empty().html(pos_name);


                }

                if (player.disable == 1)
                {
                    item.find('.f-button').remove();
                } else
                {
                    item.find('.f-button').attr('onclick', 'jQuery.playerdraft.clearPlayer(' + id + ')');
                }

                this.calculateSalary(id, 'add');
                this.calculateAvgPerPlayer();

                //move flex position
                /*if (is_flex) {
                    var clss = "#custom-field-soccer .group_position_" + player.position;
                    item_field.remove();
                    jQuery(clss).append(item_field);
                }*/
            } else
            {
                if (!jQuery('.f-errorMessage').is(':visible'))
                {
                    var positionName = "'" + player.position + "'";
                    if (aPool.no_position == 1)
                    {
                        positionName = '';
                    }
                    if (jQuery("#game_type").val() == 'GOLFSKIN') {
                        jQuery('.f-errorMessage').empty().append(wpfs['fullpositions1'] + " " + wpfs['fullpositions2']).slideToggle().delay(4000).fadeOut();

                    } else {
                        jQuery('.f-errorMessage').empty().append(wpfs['fullpositions1'] + positionName + " " + wpfs['fullpositions2']).slideToggle().delay(4000).fadeOut();
                    }
                }
            }
        }

        this.checkSignButtonForFullPosition();
    },

    addMultiPlayers: function (playersID)
    {
        playersID = playersID.split(',');
        // first: clear all players
        jQuery('.f-roster .f-roster-position').each(function () {
            if (typeof jQuery(this).attr('data-id') != typeof undefined)
            {
                jQuery.playerdraft.clearPlayer(jQuery(this).attr('data-id'));
            }
        });
        //second: add new players
        for (var i = 0; i < playersID.length; i++)
        {
            this.addPlayer(playersID[i]);
        }
    },

    clearPlayer: function (id)
    {
        var player = this.findPlayer(id);
        jQuery('#buttonAdd' + id).css('display', 'block');
        jQuery('#buttonAdd' + id).parents('tr').removeClass('f-player-in-lineup');
        jQuery('#buttonRemove' + id).hide();
        this.resetLineup(id)

        if (jQuery("#league_type").val() == 'GOLFSKIN') {
            this.golfSkinClearPlayer(id);
        } else {
            this.calculateSalary(id, 'remove');
            this.calculateAvgPerPlayer();
        }
        if (typeof player != 'undefined') {
            this.deletePlayerWithTeams(player.team_id, player.id);
        }
        // change player each week

        //if (is_load_players) {
            //this.loadPlayers();
        //}
        // change player each week
        if (this.is_soccer_field) {
            var item_field = jQuery('#custom-field-soccer #soccer-player-' + id);
            var real_position = item_field.attr('data-field-pos');
            var position_detail = this.getPositionDetail(real_position);
            item_field.removeAttr('id');
            item_field.removeClass('field-has-player');
            item_field.find('.f-player-image').empty();
            item_field.find('.team-salary').empty();
            item_field.find('.pos-player').empty().html(position_detail.name);
            // check id is extra
            if (position_detail.is_extra != "0") {
                var html = '<div class="position-' + real_position + ' position-wrapper">';
                html += '<div class="f-player-image f-no-image"></div>';
                html += '<div class="lineup_group">';
                html += '<p class="team-salary"></p>';
                html += '<p class="pos-player">' + position_detail.name + '</p>';
                html += '</div>';
                html += '</div>';
                item_field.remove();
                var clss = "#custom-field-soccer .group_position_none";
                jQuery(clss).append(html);
            }

        }

        this.checkSignButtonForFullPosition();
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

    golfSkinClearPlayer: function (id)
    {
        id = parseInt(id);
        var round_id = jQuery(".f-fixture-picker-button-container .f-is-active").attr('data-id');
        if (jQuery.isArray(this.aGolfSkinPlayers[round_id])) {
            var index = this.aGolfSkinPlayers[round_id].indexOf(id);
            if (index !== -1) {
                this.aGolfSkinPlayers[round_id].splice(index, 1);
                this.golfSkinCaculateMoney('minus');
            }
        }
    },
    nextToAvailablePosition: function (position_id) {
        // get next  position not fill
        var default_quantity = this.list_position_soccer[position_id].default_quantity;
        var current_quantity = this.list_position_soccer[position_id].current_quantity;
        if (default_quantity == current_quantity) {
            // next
            var is_next = false;
            for (var pos in this.list_position_soccer) {
                if (pos == position_id) {
                    is_next = true;
                    continue;
                }

                if (is_next == true && this.list_position_soccer[pos].default_quantity != this.list_position_soccer[pos].current_quantity) {
                    jQuery('ul.f-player-list-position-tabs li a[data-id="' + pos + '"]').trigger('click');
                    return false;
                }
            }
            // second loop
            for (var pos in this.list_position_soccer) {
                if (pos == position_id) {
                    return false;
                }
                if (this.list_position_soccer[pos].default_quantity != this.list_position_soccer[pos].current_quantity) {
                    jQuery('ul.f-player-list-position-tabs li a[data-id="' + pos + '"]').trigger('click');
                    return false;
                }
            }
        }

    },
    clearMotocrossPlayer: function (id, pos) {
        // get postion id
        jQuery('#buttonAdd' + id).css('display', 'block');
        jQuery('#buttonAdd' + id).parents('tr').removeClass('f-player-in-lineup');
        jQuery('#buttonRemove' + id).hide();
        // reset lineup
        var item = jQuery('.player-position-' + pos);
        item.removeClass('f-has-player');
        item.removeAttr('id');
        item.removeAttr('data-id');
        item.find('.f-empty-roster-slot-instruction').show();
        item.find('.f-player-image').empty();
        item.find('.f-player').empty().css('visibility', 'hidden').removeAttr("onclick");
        item.find('.f-salary').empty().css('visibility', 'hidden');
        item.find('.f-button').css('visibility', 'hidden');
        delete this.aMotoPos[pos];
        jQuery('.f-player-list-table-motocross select.mt_pos_select').each(function () {
            var player_id = jQuery(this).attr('data-id');
            var is_player_exist = jQuery.playerdraft.checkMotoCrossPlayerExistUniquePos(player_id);
            var is_unique_pos = jQuery.playerdraft.checkMotocrossPosUnique(pos);
            if (!is_player_exist || !is_unique_pos) {
                jQuery(this).children('option[value="' + pos + '"]').show();

            }
        })
        var current_player = jQuery('.f-player-list-table-motocross select[data-id="' + id + '"]');
        current_player.children('option[value=0]').attr('selected', 'selected');
        var list_options = current_player.children('option');
        list_options.each(function () {
            var option = jQuery(this);
            var cur_pos = option.val();
            var iCheck = jQuery.playerdraft.checkExistMotoPos(cur_pos);
            if (iCheck == 0 && jQuery.playerdraft.checkMotocrossPosUnique(cur_pos)) {
                option.show();
            }
        });


    },
    clearAllMotocrossPlayer: function () {
        if (confirm(wpfs['players_out_team']))
        {
            jQuery('.f-roster .f-roster-position').each(function () {
                if (typeof jQuery(this).attr('data-id') != typeof undefined)
                {
                    jQuery.playerdraft.clearMotocrossPlayer(jQuery(this).attr('data-id'), jQuery(this).attr('data-pos'));
                }
            });
        }
    },
    clearAllPlayer: function (no_confirm)
    {
        if (no_confirm == 1 || confirm(wpfs['players_out_team']))
        {
            jQuery('.f-roster .f-roster-position').each(function () {
                if (typeof jQuery(this).attr('data-id') != typeof undefined)
                {
                    jQuery.playerdraft.clearPlayer(jQuery(this).attr('data-id'));
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
        item.find('.f-salary').empty().hide();
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
        // for mixing sport
        if (jQuery('#type_league').val() == 'mixing') {
            var org_id = jQuery('#mixing_orginazation_id').val();
            aPlayers = aPlayers[org_id];
        }

        //
        for (var i = 0; i < aPlayers.length; i++)
        {
            if (aPlayers[i].id == id)
            {
                return aPlayers[i];
            }
        }
    },
    mixFindPlayerToClear: function (id)
    {
        var aPlayers = jQuery.parseJSON(this.aPlayers);
        for (var i in aPlayers) {
            for (var j in aPlayers[i]) {
                if (aPlayers[i][j].id == id) {
                    return aPlayers[i][j];
                }
            }
        }
    },

    calculateSalary: function (player_id, task)
    {
        var is_mixing = false;
        var org_id = 0;
        var salarycap = 0;
        if (jQuery('#type_league').val() == 'mixing') {
            is_mixing = true;
            var player = this.getPlayerInfo(player_id);
            org_id = player.org_id;
            salarycap = this.salaryCap[org_id];
            if (this.checkMixingLineUpsFull() == true) {
                jQuery('#btnNextSport').closest('.f-contest-enter-button-container').hide();
            } else {
                jQuery('#btnNextSport').closest('.f-contest-enter-button-container').show();
            }
        } else {
            salarycap = this.salaryCap;
        }

        if (salarycap > 0)
        {
            var salary_remaining = 0;

            if (is_mixing) {
                var player = this.mixFindPlayerToClear(player_id); // mixing league
                is_mixing = true;
                // get current active sports
                salary_remaining = this.salaryRemaining[org_id];
            } else {
                var player = this.findPlayer(player_id);  // single mixing
                salary_remaining = this.salaryRemaining;
            }


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
            jQuery('#salaryRemaining').empty().append(VIC_FormatMoney(salary_remaining, "$"));
            if (is_mixing) {
                this.salaryRemaining[org_id] = salary_remaining;
            } else {
                this.salaryRemaining = salary_remaining;
            }
        }
    },
    getCurrentActiveSport: function () {
        var org_id = 0;
        jQuery('.f-fixture-picker .f-fixture-picker-button-container').each(function () {
            var first_class_a = jQuery(this).find('a:first-child');
            if (first_class_a.hasClass('s-sport-is-active')) {
                org_id = jQuery(this).closest('section').attr('data-org-id');

            }
        });
        return org_id;
    },
    calculateAvgPerPlayer: function ()
    {

        var total = jQuery('.f-roster-position:not(.f-has-player)').filter(":visible").length;
        var salary_remaining = 0;
        if (jQuery('#type_league').val() == 'mixing') {
            jQuery('.f-fixture-picker .f-fixture-picker-button-container').each(function () {
                var first_class_a = jQuery(this).find('a:first-child');

                if (first_class_a.hasClass('s-sport-is-active')) {
                    var org_id = jQuery(this).closest('section').attr('data-org-id');

                    salary_remaining = jQuery.playerdraft.salaryRemaining[org_id];
                }
            });
        } else {
            salary_remaining = this.salaryRemaining;
        }


        if (total > 0)
        {
            total = salary_remaining / total;
        } else
        {
            total = 0;
        }
        jQuery('#AvgPlayer').empty().append(VIC_FormatMoney(Math.round(total), "$"));
    },
    nextMixingSport: function () {


        var btnValue = jQuery('#btnNextSport').attr('data-value');

        var org_id = 0;
        if (btnValue == 'next') {
            org_id = this.getNextPreviousOrgId(true);
        } else {
            org_id = this.getNextPreviousOrgId(false);
        }

        this.currentOrgId = org_id;
        var section = jQuery('.f-pick-your-team section[data-org-id="' + org_id + '"]');
        section.find('a:first-child').trigger('click');
        this.fixMixingSportIndexBtn();

    },
    getNextPreviousOrgId: function (is_next) {
        var listOrgs = Object.keys(this.salaryRemaining);
        var cur_position = 0;
        for (var i in listOrgs) {
            if (listOrgs[i] == this.currentOrgId) {
                cur_position = i;
                break;
            }
        }

        if (is_next) {
            cur_position = parseInt(cur_position) + 1;
        } else {
            cur_position = parseInt(cur_position) - 1;
        }
        return listOrgs[cur_position];
    },
    fixMixingSportIndexBtn: function () {
        var listOrgs = Object.keys(this.salaryRemaining);
        if (listOrgs[listOrgs.length - 1 ] == this.currentOrgId) { // current is last org id, turn back
            jQuery('#btnNextSport').attr('data-value', 'back').val('Back');
        } else if (listOrgs[0] == this.currentOrgId) {
            jQuery('#btnNextSport').attr('data-value', 'next').val('Next');
        }
    }
    ,
    submitData: function ()
    {
        // check salary cap
        if (jQuery('#type_league').val() == 'mixing') {
            for (var org_id in this.salaryRemaining) {
                var salary = parseFloat(this.salaryRemaining[org_id]);
                if (salary < 0 && this.salaryCap[org_id] > 0) {
                    alert(wpfs['team_exceed_salary_cap']);
                    return false;
                }
            }
        }
        if (jQuery('.f-roster-position:not(.f-has-player)').length > 0)
        {
            alert(wpfs['player_each_position']);
        } else if (this.salaryCap > 0 && this.salaryRemaining < 0)
        {
            alert(wpfs['team_exceed_salary_cap']);
        } else
        {
            jQuery('#formLineup').find('input[name="player_id[]"]').remove();
            jQuery('#btnSubmit').attr("disabled", "true");
            jQuery('.f-roster .f-roster-position').each(function () {
                if (typeof jQuery(this).attr('data-id') != typeof undefined && jQuery(this).find('a.f-button').length > 0)
                {
                    if (jQuery('#type_league').val() == 'motocross') {
                        jQuery('#formLineup').append('<input type="hidden" value="' + jQuery(this).attr('data-id') + '_' + jQuery(this).attr('data-pos') + '" name="player_id[]">');
                    } else {
                        var player_id = jQuery(this).attr('data-id');
                        jQuery('#formLineup').append('<input type="hidden" value="' + player_id + '" name="player_id[]">');
                        if (jQuery.playerdraft.is_soccer_flex) {
                            jQuery('#formLineup').append('<input type="hidden" value="' + jQuery(this).attr('data-position') + '" name="player_position[' + player_id + ']">');

                        }
                    }
                }
            });
            jQuery('#formLineup').submit();
        }
    },
    golfSkinSubmitData: function () {
        var aLeague = jQuery.parseJSON(this.league);
        var rounds = aLeague.rounds;
        var isSuccess = false
        rounds = rounds.split(",");
        for (var i in rounds) {
            if (jQuery.isArray(this.aGolfSkinPlayers[rounds[i]]) && this.aGolfSkinPlayers[rounds[i]].length > 1) {
                isSuccess = true;
                break;
            }
        }

        if (isSuccess == false) {
            alert(wpfs['golfskin_player_position']);
            return;
        }
        var obj = this;
        jQuery('#btnSubmit').attr('disabled', 'true').text(wpfs['working'] + '...');
        if (this.is_entry_fee) {
            jQuery.post(ajaxurl, 'action=getUserbalance', function (result) {
                result = jQuery.parseJSON(result);
                jQuery('#btnSubmit').removeAttr('disabled').text(wpfs['enter']);
                if (parseInt(result.balance) < obj.totalMoney) {
                    alert(wpfs['golfskin_add_balance']);
                } else {
                    jQuery("#total_money").val(obj.totalMoney);
                    jQuery("#players").val(JSON.stringify(obj.aGolfSkinPlayers));
                    jQuery('#formLineup').submit();

                }
            });
        } else {
            jQuery("#total_money").val(obj.totalMoney);
            jQuery("#players").val(JSON.stringify(obj.aGolfSkinPlayers));
            jQuery('#formLineup').submit();
        }

    }
    ,
    userResult: function (leagueID, is_curent, userID, username, avatar, rank, totalScore, entry_number)
    {
        //load result
        var round_id = 0;
        if (jQuery("#gameType").val() == 'GOLFSKIN') {
            round_id = jQuery('#list_round').val();
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
            'week': 0,
            'is_motocross': jQuery('#is_motocross').val(),
            'gameType': jQuery("#gameType").val(),
            'leagueOptionType': jQuery('#leagueOptionType').val(),
        };
        /*if (is_curent == 1)
        {
            jQuery('#f-seat-1 .f-loading').show();
        } else
        {
            jQuery('#f-seat-2 .f-loading').show();
        }*/
        jQuery.post(ajaxurl, data, function (data) {
            /*if (is_curent == 1)
            {
                jQuery('#f-seat-1').empty().append(data);
            } else
            {
                jQuery('#f-seat-2').empty().append(data);
            }*/
            jQuery('#vc-leaderboard-detail').html(data);
        })


        return false;
    },

    getScoringPointById: function (id)
    {
        var scoringCats = jQuery("#scoringCats").val();
        if (scoringCats != '')
        {
            scoringCats = jQuery.parseJSON(scoringCats);
            for (var i in scoringCats)
            {
                if (scoringCats[i].id == id)
                {
                    return scoringCats[i].points;
                }
            }
        }
        return 0;
    },

    searchPlayers: function ()
    {
        jQuery.playerdraft.loadPlayers();
    },
    searchMixingPlayers: function ()
    {

        jQuery.playerdraft.mixingLoadPlayers();
    },

    searchMotocrossPlayers: function ()
    {

        jQuery.playerdraft.loadMotocrossPlayer();
        //check player in line
        jQuery('.f-roster-position').each(function () {
            var id = jQuery(this).attr('data-id');
            var pos_id = jQuery(this).attr('data-pos');
            jQuery.playerdraft.addMotocrossPlayer(null, id, pos_id);
        });
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

    sendInviteFriendEmail: function ()
    {
        var warning = jQuery('.f-manual-email-form-button .f-warning');
        var dataSring = jQuery('#formInviteFriend').serialize();
        jQuery.post(ajaxurl, 'action=sendInviteFriend&' + dataSring, function (result) {
            var data = JSON.parse(result);
            if (data.notice)
            {
                warning.empty().append(data.notice).css('display', 'inline-block').delay(4000).fadeOut();
            } else
            {
                warning.empty().append(data.message).css('display', 'inline-block').delay(4000).fadeOut();
            }
        })
        return false;
    },

    loadContestScores: function (leagueID, entry_number)
    {
        var data = {
            'action': 'loadContestScores',
            'leagueID': leagueID,
            'entry_number': entry_number,
            'week': 0,
            'multiEntry': jQuery('#multiEntry').val()
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery('#tableScores tbody').empty().append(result);
            jQuery('#tableScores tbody tr.f-user-highlight').trigger('click');
        });
    },
    loadUserResultByRound: function () {
        jQuery("#tableScores .f-user-highlight").trigger('click');
    },
    loadFixtureScores: function (leagueID)
    {
        var data = 'leagueID=' + leagueID;
        jQuery.post(ajaxurl, "action=loadFixtureScores&" + data, function (result) {
            jQuery("#f-live-scoring-fixture-info").after(result).remove();
        });
    },

    showIndicatorLegend: function ()
    {
        var item = jQuery('.f-draft-legend-key-content');
        if (!item.is(':visible'))
        {
            item.slideDown();
        } else
        {
            item.slideUp();
        }
    },

    ////////////////////////tab////////////////////////
    loadPlayerNews: function(link)
    {
        jQuery('#playerNews').html(this.loading());
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

    playerInfo: function (player_id)
    {
        var pool = jQuery.parseJSON(this.aPool);
        var orgID = jQuery.parseJSON(this.aPool).organization;
        var player = this.findPlayer(player_id);
        jQuery.playerdraft.showDialog('#dlgInfo', this.loading());
        jQuery.post(ajaxurl, "action=loadPlayerStatistics&orgID=" + orgID + '&playerID=' + player_id + '&poolID=' + pool.poolID, function (result) {
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
                jQuery('#playerGame').html("A: " + player.teamName2 + ' @ ' + "H: " + player.teamName1);
            }

            //load player news from google
            if(jQuery('#playerNews').data('google') == 1)
            {
                jQuery.playerdraft.loadPlayerNews();
            }
        })
    },

    ruleScoring: function (leagueID, tab)
    {
        jQuery.playerdraft.showDialog('#dlgInfo', this.loading());
        var data = 'leagueID=' + leagueID;
        jQuery.post(ajaxurl, "action=loadPoolInfo&" + data, function (result) {
            jQuery('#dlgInfo').addClass('f-quickfire-lightbox');
            jQuery('#dlgInfo .f-body').html(result);

            jQuery('#week_filter option').each(function(){
                if(jQuery('.fixture_week_' + jQuery(this).attr('value')).length == 0)
                {
                    jQuery(this).remove();
                }
            });
            jQuery.playerdraft.loadWeeklyFixture();
            switch (tab)
            {
                case 2:
                    jQuery('#tabRuleScoring li:first').next().trigger('click');
                    break;
                case 3:
                    jQuery('#tabRuleScoring li:last').prev().trigger('click');
                    break;
                case 4:
                    jQuery('#tabRuleScoring li:last').trigger('click');
                    break;
            }
        })
        return false;
    },

    dlgEntries: function (leagueID, name)
    {
        var html = '<div>\n\
                <div class="f-lightbox-entries f-entries">\n\
                    <header>\n\
                        <h4>' + name + '</h4>\n\
                    </header>\n\
                    <div id="f-contest-lightbox-content">\n\
                        <div class="f-quickfire-tab" id="tab-info">\n\
                            <div class="f-tab-game-info"></div>\n\
                        </div>\n\
                    </div>\n\
                    <div class="f-quickfire-footer f-no-content"></div>\n\
                </div>\n\
            </div>';
        this.showDialog('#dlgInfo', html)
        jQuery.playerdraft.loadTabLeagueEntries(jQuery(this), leagueID);
    },

    dlgPrize: function (leagueID, name)
    {
        var html = '<div>\n\
                <div class="f-lightbox-prizes f-entries">\n\
                    <header>\n\
                        <h4>' + name + '</h4>\n\
                    </header>\n\
                    <div id="f-contest-lightbox-content">\n\
                        <div class="f-quickfire-tab" id="tab-info">\n\
                            <div class="f-tab-game-info"></div>\n\
                        </div>\n\
                    </div>\n\
                    <div class="f-quickfire-footer f-no-content"></div>\n\
                </div>\n\
            </div>';
        this.showDialog('#dlgInfo', html)
        jQuery.playerdraft.loadTabLeaguePrizes(jQuery(this), leagueID);
    },

    loadTabScoringCategory: function (item, leagueID)
    {
        if (!item.find('a').hasClass('f-is-active'))
        {
            jQuery('#tabRuleScoring li a').removeClass('f-is-active');
            item.find('a').addClass('f-is-active');
            var data = 'leagueID=' + leagueID;
            jQuery('.f-lightbox .f-tab-game-info').empty().append(this.loading());
            jQuery.post(ajaxurl, "action=loadLeagueScoringCategory&" + data, function (result) {
                jQuery('.f-lightbox .f-tab-game-info').empty().append(result);
                jQuery('#week_filter option').each(function(){
                    if(jQuery('.fixture_week_' + jQuery(this).attr('value')).length == 0)
                    {
                        jQuery(this).remove();
                    }
                });
                jQuery.playerdraft.loadWeeklyFixture();
            })
        }
    },

    loadTabLeagueEntries: function (item, leagueID)
    {
        if (!item.find('a').hasClass('f-is-active'))
        {
            jQuery('#tabRuleScoring li a').removeClass('f-is-active');
            item.find('a').addClass('f-is-active');
            var data = 'leagueID=' + leagueID;
            jQuery('.f-lightbox .f-tab-game-info').empty().append(this.loading());
            jQuery.post(ajaxurl, "action=loadLeagueEntries&" + data, function (result) {
                jQuery('.f-tab-game-info').empty().append(result);
            })
        }
    },

    loadTabLeaguePrizes: function (item, leagueID)
    {
        if (!item.find('a').hasClass('f-is-active'))
        {
            jQuery('#tabRuleScoring li a').removeClass('f-is-active');
            item.find('a').addClass('f-is-active');

            var data = 'leagueID=' + leagueID;
            jQuery('.f-lightbox .f-tab-game-info').empty().append(this.loading());
            jQuery.post(ajaxurl, "action=loadLeaguePrizes&" + data, function (result) {
                jQuery('.f-lightbox .f-tab-game-info').empty().append(result);
            })
        }
    },

    loadTabInviteFriends: function (item, leagueID)
    {
        if (!item.find('a').hasClass('f-is-active'))
        {
            jQuery('#tabRuleScoring li a').removeClass('f-is-active');
            item.find('a').addClass('f-is-active');

            var data = 'leagueID=' + leagueID;
            jQuery('.f-lightbox .f-tab-game-info').empty().append(this.loading());
            jQuery.post(ajaxurl, "action=loadInviteFriends&" + data, function (result) {
                jQuery('.f-lightbox .f-tab-game-info').empty().append(result);
            })
        }
    },

    loadWeeklyFixture: function ()
    {
        jQuery('.fixture_weekly').hide();
        jQuery('.fixture_week_' + jQuery('#week_filter').val()).show();
    },

    sendUserPickEmail: function (leagueID)
    {
        var data = {
            action: 'sendUserPickEmail',
            leagueID: leagueID
        };
        jQuery.post(ajaxurl, data, function (result) {})
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

    copyLink: function (url)
    {
        Copied = jQuery('.f-refer-link input').createTextRange();
        Copied.execCommand("RemoveFormat");
        Copied.execCommand(url);
    },
    selectGolfSkinRounds: function (e) {
        this.golfSkinResetPlayers();
        var round_id = jQuery(e).attr('data-id');
        for (var i in this.aGolfSkinPlayers) {
            if (round_id == i) {
                for (var j in this.aGolfSkinPlayers[round_id]) {
                    this.addPlayer(this.aGolfSkinPlayers[round_id][j]);
                }
            }
        }
    },
    golfSkinResetPlayers: function () {

        var obj = this;
        jQuery('.f-roster .f-roster-position').each(function () {
            if (typeof jQuery(this).attr('data-id') != typeof undefined)
            {

                var id = jQuery(this).attr('data-id');
                jQuery('#buttonAdd' + id).css('display', 'block');
                jQuery('#buttonAdd' + id).parents('tr').removeClass('f-player-in-lineup');
                jQuery('#buttonRemove' + id).hide();
                obj.resetLineup(id);

            }
        });
        //========
    },
    golfSkinCaculateMoney: function (type) {
        if (this.is_entry_fee == 0) {
            return;
        }
        switch (type) {
            case 'plus':
                this.totalMoney += this.entry_fee;
                break;
            case 'minus':
                this.totalMoney -= this.entry_fee;
                break;
        }
        jQuery(".f-salary-remaining .f-salary-remaining-container span").html(VIC_FormatMoney(this.totalMoney));
    },
    checkMixingLineUpsFull: function () {
        var size = jQuery('.f-roster-position:not(.f-has-player)').length;
        if (size > 0) {
            return false;
        }
        return true;
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
    getPositionDetail: function (id) {
        var aPositions = jQuery.parseJSON(this.aPostiions);
        for (var i in aPositions) {
            if (aPositions[i].id == id) {
                return aPositions[i];
            }
        }
        return false;
    },
    checkMotoCrossPlayerExistUniquePos: function (player_id) {
        for (var i in this.aMotoPos) {
            if (this.aMotoPos[i] == player_id) {
                var is_unique = this.checkMotocrossPosUnique(i);
                if (is_unique) {
                    return true;
                }
            }
        }
        return false;
    },

    resetCountdown: function(timestamp){
        var targetDate = new Date(parseInt(timestamp) * 1000);
        this.clock.countdown(targetDate);
    },

    checkSignButtonForFullPosition: function(){
        if(jQuery('.group_position_Flex').length > 0 || jQuery('.player-position-2067').length > 0)
        {
            return;
        }
        var positions = [];
        var not_full_positions = [];
        jQuery('.f-roster-container li.f-roster-position').each(function(){
            var position_id = jQuery(this).data('position');
            if(positions.indexOf(position_id) === -1)
            {
                positions.push(position_id);
            }
            if(!jQuery(this).hasClass('f-has-player') && not_full_positions.indexOf(position_id) === -1)
            {
                not_full_positions.push(position_id);
            }
        });
        var full_positions = jQuery(positions).not(not_full_positions).get();

        //show or hide add button
        jQuery('.f-player-list-table tbody tr').each(function(){
            if(full_positions.indexOf(jQuery(this).data('position')) !== -1)
            {
                jQuery(this).find('.f-player-add-button').hide();
            }
            else if(!jQuery(this).find('.f-player-remove-button').is(':visible'))
            {
                jQuery(this).find('.f-player-add-button').show();
            }
        })

        //nfl flex
        var is_nfl_flex = jQuery('.f-player-list-position-tabs li a.f-is-active').data('isFlex');
        var nfl_flex_position_id = jQuery('.f-player-list-position-tabs li a.f-is-active').data('id');
        var is_full = true;
        jQuery('.player-position-' + nfl_flex_position_id).each(function(){
            if(!jQuery(this).hasClass('f-has-player'))
            {
                is_full = false;
                return;
            }
        })
        if(is_nfl_flex && !is_full){
            jQuery('.f-player-list-table tbody tr').each(function(){
                if(!jQuery(this).find('.f-player-remove-button').is(':visible')){
                    jQuery(this).find('.f-player-add-button').show();
                }
            })
        }
    },

    showInviteFriendDlg: function (league_id) {
        var data = {
            action: 'showInviteFriendDlg',
            league_id: league_id,
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery('#dlgFriends').html(result);
            jQuery.playerdraft.showDialog('#dlgFriends');
        });
    },

    initPlayerdraft: function()
    {
        this.editLineup();

        //submit data
        jQuery(document).on('click', '#btnSubmit', function(){
            // check salary cap
            if (jQuery('#type_league').val() == 'mixing') {
                for (var org_id in this.salaryRemaining) {
                    var salary = parseFloat(this.salaryRemaining[org_id]);
                    if (salary < 0 && this.salaryCap[org_id] > 0) {
                        alert(wpfs['team_exceed_salary_cap']);
                        return false;
                    }
                }
            }
            if (jQuery('.f-roster-position:not(.f-has-player)').length > 0)
            {
                alert(wpfs['player_each_position']);
                return false;
            }
            else if (this.salaryCap > 0 && this.salaryRemaining < 0)
            {
                alert(wpfs['team_exceed_salary_cap']);
                return false;
            }
            else
            {
                jQuery('#formLineup').find('input[name="player_id[]"]').remove();
                jQuery('#btnSubmit').attr("disabled", "true");
                jQuery('.f-roster .f-roster-position').each(function () {
                    if (typeof jQuery(this).attr('data-id') != typeof undefined && jQuery(this).find('a.f-button').length > 0)
                    {
                        if (jQuery('#type_league').val() == 'motocross') {
                            jQuery('#formLineup').append('<input type="hidden" value="' + jQuery(this).attr('data-id') + '_' + jQuery(this).attr('data-pos') + '" name="player_id[]">');
                        } else {
                            var player_id = jQuery(this).attr('data-id');
                            jQuery('#formLineup').append('<input type="hidden" value="' + player_id + '" name="player_id[]">');
                            if (jQuery.playerdraft.is_soccer_flex) {
                                jQuery('#formLineup').append('<input type="hidden" value="' + jQuery(this).attr('data-position') + '" name="player_position[' + player_id + ']">');

                            }
                        }
                    }
                });
            }
            jQuery.global.disableButton('btnSubmit');

            //submit data
            jQuery.post(ajaxurl, 'action=submitPlayerdraft&' + jQuery('#formLineup').serialize(), function(result) {
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
        })
    },

    //////////////////////////////////////////result//////////////////////////////////////////
    initPlayerdraftResult: function()
    {
        var is_live = jQuery('#is_live').val();

        //check live
        if(is_live == 1)
        {
            setInterval(function(){
                jQuery.league.liveEntriesResult(jQuery('#poolID').val(), jQuery('#leagueID').val(), jQuery('#entry_number').val())
            }, 600000);
        }
        else
        {
            //jQuery.portfolio.loadResult();
        }

        //result detail
        jQuery(document).on('click', '#table_standing tr', function(){
            result_select_id = jQuery(this).attr('id');
            jQuery('#table_standing tr').removeClass('f-user-highlight');
            jQuery(this).addClass('f-user-highlight');
            var user_id = jQuery(this).data('user_id');
            var entry_number = jQuery(this).data('entry_number');
            jQuery.playerdraft.loadResultDetail(user_id, entry_number);
        })
    },

    loadResultDetail: function (user_id, entry_number)
    {
        var leagueID = jQuery('#leagueID').val();
        var round_id = 0;
        if (jQuery("#gameType").val() == 'GOLFSKIN') {
            round_id = jQuery('#list_round').val();
        }

        jQuery.global.showLoading('#vc-leaderboard');
        jQuery.global.showLoading('#vc-leaderboard-detail');
        var params = {
            action: 'loadUserResult',
            leagueID: leagueID,
            userID: user_id,
            entry_number: entry_number,
            roundID: round_id,
            week: 0,
            is_motocross: jQuery('#is_motocross').val(),
            gameType: jQuery("#gameType").val(),
            leagueOptionType: jQuery('#leagueOptionType').val(),
            my_user_id: jQuery('#userID').val(),
            my_entry_number: jQuery('#entry_number').val(),
        };
        jQuery.post(ajaxurl, params, function (data) {
            jQuery.global.hideLoading('#vc-leaderboard');
            jQuery('#vc-leaderboard-detail').html(data);
        });
    },
};

jQuery(document).on('click', '.f-refer-prompt-tab-buttons a', function () {
    jQuery('.f-refer-prompt-tab-buttons a').removeClass('f-is-active');
    jQuery(this).blur().addClass('f-is-active');
    var tabName = jQuery(this).attr('data-tab-name');

    jQuery('.f-refer-prompt-tabs div').removeClass('f-is-active');
    jQuery('.f-refer-prompt-tabs div').each(function () {
        if (jQuery(this).attr('data-tab-name') == tabName)
        {
            jQuery(this).addClass('f-is-active').show();
        }
    });
});

jQuery('#formInviteFriend').submit(function (e) {
    e.preventDefault();
    var dataSring = jQuery('#formInviteFriend').serialize();
    jQuery.post(ajaxurl, '=sendInviteFriend&' + dataSring, function (result) {
        var data = JSON.parse(result);
        if (data.notice)
        {
            alert(data.notice);
        } else
        {
            alert(data.message);
        }
    });
    return false;
});

function checkAll()
{
    jQuery("input[name='val[friend_ids][]']").attr('checked', true);
}

function checkNone()
{
    jQuery("input[name='val[friend_ids][]']").removeAttr('checked');
}