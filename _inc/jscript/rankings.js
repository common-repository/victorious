jQuery.ranking = {
    enterLeagueHistory: function (entry_number, page)
    {
        var leagueID = jQuery("#importleagueID").val();
        if (typeof page == 'undefined')
        {
            page = 1;
        }
        var sort_by = jQuery('#sortBy').val();
        var sort_value = jQuery('#sortValue').val();
        var date_type = '';
        var date_type_number = '';
        if(jQuery('#date_type').length > 0)
        {
            date_type = jQuery('#date_type').val();
            if(date_type == 'weekly')
            {
                date_type_number = jQuery('#date_type_number_weekly').val();
            }
            else
            {
                date_type_number = jQuery('#date_type_number_monthly').val();
            }
        }

        var data = {
            action: 'getNormalGameResult',
            leagueID: leagueID,
            entry_number: entry_number,
            page: page,
            sort_by: sort_by,
            sort_value: sort_value,
            date_type: date_type,
            date_type_number: date_type_number
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery("#result_content").html(result);
        });
    },

    doSort: function (entry_number, page, sort_by)
    {
        var sort_value = jQuery('#sortValue').val();
        if (sort_value == 'asc' || sort_value == '')
        {
            sort_value = 'desc';
        } else if (sort_value == 'desc')
        {
            sort_value = 'asc';
        }
        jQuery('#sortBy').val(sort_by);
        jQuery('#sortValue').val(sort_value);
        this.enterLeagueHistory(entry_number, page);
    },

    selectUser: function (selID, entry_number)
    {
        var result = jQuery("#dataResult").html().trim();
        result = jQuery.parseJSON(result);
        var league = result.league;
        var users = result.users;
        if (!league.can_view_user)
        {
            alert("You can see another users' picks after league start only.");
        } else
        {
            if (users != null)
            {
                for (var i in users)
                {
                    if (users[i].userID == selID && users[i].entry_number == entry_number)
                    {
                        jQuery.ranking.showUserResult(0, users[i]);
                    }
                }
            }
        }
    },
    selectPickSquaresUser: function (selID, entry_number) {
        var result = jQuery("#dataResult").val();
        result = jQuery.parseJSON(result);
        var league = result.league;
        var users = result.users;
        if (!league.can_view_user)
        {
            alert("You can see another users' picks after league start only.");
        } else
        {
            if (users != null)
            {
                for (var i in users)
                {
                    if (users[i].userID == selID && users[i].entry_number == entry_number)
                    {
                        picksquare = jQuery.parseJSON(user.picks);
                        jQuery("#yourresult_" + users[i].fightID).empty().append(picksquare.join());

                    }
                }
            }
        }
    }
    ,

    showUserResult: function (mypick, user)
    {
        if(mypick == 1)
        {
            user = jQuery("#dataCurrentUser").html().trim();
            user = jQuery.parseJSON(user);
        }
        var result = jQuery("#dataResult").html().trim();
        result = jQuery.parseJSON(result);
        var league = result.league;
        var header = headerName = body = totalPoints = '';
        if (mypick == 1)
        {
            header = jQuery("#myResultHeader");
            headerName = "My Pick";
            body = "myresult_";
            totalPoints = "myTotalPoints";
        } else
        {
            header = jQuery("#yourResultHeader");
            headerName = "Competitor Pick";
            body = "yourresult_";
            totalPoints = "YourTotalPoints";
        }
        header.empty().append(headerName + ' (' + user.user_login + ')');
        if (user.picks != null)
        {
            var html = '';
            var fixture = '';
            for (var i in user.picks)
            {
                fixture = user.picks[i];
                var styleWinner = styleMethod = styleMinute = styleRound = stylematchOverUnder = styleWinnerSpread = 'style="color:red"';
                var htmlPoint = "No points";
                if (league.gameType == 'BOTHTEAMSTOSCORE') {
                    html = '';
                }
                if (league.is_complete)
                {
                    if (fixture.matchWinner)
                    {
                        styleWinner = 'style="color:green"';
                    }
                    if (fixture.matchWinnerSpread)
                    {
                        styleWinnerSpread = 'style="color:green"';
                    }
                    if (fixture.matchMethod)
                    {
                        styleMethod = 'style="color:green"';
                    }
                    if (fixture.matchMinute)
                    {
                        styleMinute = 'style="color:green"';
                    }
                    if (fixture.styleRound)
                    {
                        styleRound = 'style="color:green"';
                    }
                    if (fixture.matchOverUnder)
                    {
                        stylematchOverUnder = 'style="color:green"';
                    }
                    if (fixture.points != '')
                    {
                        htmlPoint = 'Points: ' + fixture.points;
                    }
                }
                if (league.gameType == 'PICKULTIMATE')
                {
                    html =
                            '<div ' + styleWinner + '>' + fixture.name + ' (Winner)</div>\n\
                        <div ' + styleWinnerSpread + '>' + fixture.name_spread + ' (Spread)</div>\n\
                        <div ' + stylematchOverUnder + '>' + fixture.over_under_value + ' ' + fixture.over_under + '&nbsp;</div>';
                } else
                {
                    if (league.gameType == 'HOWMANYGOALS') {
                        if (!isNaN(fixture.points) && parseFloat(fixture.points) > 0) {
                            html =
                                    '<div style="color:green" >' + fixture.name + '</div>';
                        } else {
                            html = '<div style="color:red">' + fixture.name + '</div>';
                        }
                    } else if (league.gameType == 'BOTHTEAMSTOSCORE') {
                        var nameDetails = fixture.name;
                        for (var k in nameDetails) {
                            if (nameDetails[k].points > 0) {
                                html += '<div style="color:green">' + nameDetails[k].choose + '</div>';
                            } else {
                                html += '<div style="color:red">' + nameDetails[k].choose + '</div>';
                            }
                            html += '<div>Points: ' + nameDetails[k].points + '</div><br>'
                        }

                    } else {
                        html =
                                '<div ' + styleWinner + '>' + fixture.name + '</div>';
                    }

                }
                if (fixture.method != '')
                {
                    html += '<div ' + styleMethod + '>Method: ' + fixture.method + '&nbsp;</div>';
                }
                if (fixture.round != '')
                {
                    html += '<div ' + styleRound + '>Round: ' + fixture.round + '&nbsp;</div>';
                }
                if (fixture.minute != '')
                {
                    html += '<div ' + styleMinute + '>Minute: ' + fixture.minute + '&nbsp;</div>';
                }
                if (league.gameType != 'BOTHTEAMSTOSCORE') {
                    html += '<div>' + htmlPoint + '</div>';
                }
                jQuery("#" + body + fixture.fightID).empty().append(html);
                jQuery("#" + totalPoints).empty().append("Total points " + user.points);
            }

            // for new tie breaker
            if (league.allow_new_tie_breaker == 1) {
                // heightest score team
                var htmlTeams = '<span>' + user.highest_team_score.selected + '</span><br>' + 'Points: ' + user.highest_team_score.points;
                jQuery("#" + body + 'highest_scoring_team').empty().append(htmlTeams);
                jQuery(".actual_result_highest_scoring_team").empty().append(user.highest_team_score.actual_result);
                if (user.highest_team_score.points != 0) {
                    jQuery("#" + body + 'highest_scoring_team span').css('color', 'green');
                } else {
                    jQuery("#" + body + 'highest_scoring_team span').css('color', 'red');
                }

                // player to score
                var htmlPScore = '<span>' + user.player_score.selected + '</span><br>' + 'Points: ' + user.player_score.points;
                jQuery("#" + body + 'player_to_score').empty().append(htmlPScore);
                if (user.player_score.points != 0) {
                    jQuery("#" + body + 'player_to_score span').css('color', 'green');
                } else {
                    jQuery("#" + body + 'player_to_score span').css('color', 'red');
                }

                // total goals
                var htmlTotalGoals = '<span>' + user.total_goals.selected + '</span><br> Points: ' + user.total_goals.points;
                jQuery("#" + body + 'total_goals').empty().append(htmlTotalGoals);
                jQuery(".actual_result_total_goals").empty().append(user.total_goals.actual_result);
                if (user.total_goals.points != 0) {
                    jQuery("#" + body + 'total_goals span').css('color', 'green');
                } else {
                    jQuery("#" + body + 'total_goals span').css('color', 'red');
                }
            }
        }
    },

    inviteFriends: function ()
    {
        var dialog = jQuery("#dlgInviteFriend").dialog({
            width: 600,
            minWidth: 600,
            modal: true,
            open: function () {
                jQuery('.ui-widget-overlay').bind('click', function () {
                    jQuery('#dlgInviteFriend').dialog('close');
                })
                jQuery('.ui-widget-overlay').addClass('custom-overlay');
            }
        });
    },

    checkAll: function ()
    {
        jQuery("input[name='val[friend_ids][]']").attr('checked', true);
    },

    checkNone: function ()
    {
        jQuery("input[name='val[friend_ids][]']").removeAttr('checked');
    },

    sendInvite: function ()
    {
        jQuery('#inviteForm').find('.inviting').show();
        var dataSring = jQuery('#inviteForm').serialize();
        jQuery.post(ajaxurl, 'action=sendInviteFriend&' + dataSring, function (result) {
            var data = JSON.parse(result);
            if (data.notice)
            {
                alert(data.notice);
            } 
            else
            {
                alert(data.message);
                jQuery("#dlgInviteFriend").dialog('close');
                jQuery('#inviteForm').find('.inviting').hide();
            }
        })
        return false;
    },

    liveEntriesResult: function (poolID, leagueID, entry_number)
    {
        var data = {
            action: 'liveEntriesResult',
            poolID: poolID,
            leagueID: leagueID
        };
        jQuery.post(ajaxurl, data, function (result) {
            //jQuery.ranking.enterLeagueHistory(entry_number, 1);
        });
    },

    loadDateType: function (entry_number) {
        if (jQuery('#date_type').val() == 'weekly') {
            jQuery('#date_type_number_weekly').show();
            jQuery('#date_type_number_monthly').hide();
            jQuery("#date_type_number_weekly").val(jQuery("#date_type_number_weekly option:first").val());
        } else if (jQuery('#date_type').val() == 'monthly') {
            jQuery('#date_type_number_weekly').hide();
            jQuery('#date_type_number_monthly').show();
            jQuery("#date_type_number_monthly").val(jQuery("#date_type_number_monthly option:first").val());
        } else {
            jQuery('#date_type_number_weekly').hide();
            jQuery('#date_type_number_monthly').hide();
        }

        this.enterLeagueHistory(entry_number);
    },
    
    showLoading: function(wrapper){
        jQuery(wrapper).append('<div class="f-loading"></div>');
    },
}