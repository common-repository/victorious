var result_select_id = "";
var result_select_page = "";
var max_team_pick = 2;
jQuery.teamdraft =
{
    initTeamDraft: function()
    {
        this.calculateSalaryRemaining();
        this.checkLineupFulled();
        
        jQuery(".table-sorting").click(function () {
            jQuery.teamdraft.doSort(jQuery(this));
        });
        
        jQuery(document).on('click', '.btn_add_lineup', function(){
            jQuery.teamdraft.addLineup(jQuery(this).data('id'));
        })
        
        jQuery(document).on('click', '.btn_remove_lineup', function(){
            jQuery.teamdraft.removeLineup(jQuery(this));
        })
        
        jQuery(document).on('click', '#btn_clear_all_lineup', function(){
            jQuery.teamdraft.removeAllLineup();
        })
        
        jQuery(document).on('keyup', '#team-search', function(){
            var keyword = jQuery(this).val();
            jQuery('.team_item').each(function(){
                if (jQuery(this).data('name').toString().trim().search(new RegExp(keyword, 'i')) == -1)
                {
                    jQuery(this).hide();
                }
                else
                {
                    jQuery(this).show();
                }
            })
        })
        
        //select lineup filter
        jQuery(document).on('click', '#lineup_cat li a:not(.f-is-active)', function(){
            var id = jQuery(this).data('id');
            jQuery('#lineup_cat li a').removeClass('f-is-active');
            jQuery(this).addClass('f-is-active');
            jQuery.teamdraft.filterLineupCat();
        })
        
        jQuery(document).on('click', '.btn_delete_lineup', function(){
            var lineup_id = jQuery('#lineup_cat li a.f-is-active').data('id');
            if(lineup_id == 0)
            {
                return;
            }
            var team_id = jQuery(this).data('id');
            jQuery('.lineup_' + lineup_id + '.filled').each(function(){
                if(jQuery(this).attr('data-team_id') == team_id){
                    jQuery.teamdraft.removeLineup(jQuery(this));
                    return;
                }
            })
        })
        
        //submit data
        jQuery(document).on('click', '#btnSubmit', function(){
            jQuery.global.disableButton('btnSubmit');
            if (!jQuery.teamdraft.checkLineupFulled())
            {
                alert(wpfs['team_each_position']);
                jQuery.global.enableButton('btnSubmit');
            } 
            else if(jQuery.teamdraft.checkOverSalaryCap())
            {
                alert(wpfs['team_exceed_salary_cap']);
                jQuery.global.enableButton('btnSubmit');
            }
            else
            {
                var lineup_ids = [];
                var team_ids = [];
                jQuery('.f-roster-position').each(function(){
                    lineup_ids.push(jQuery(this).data('id'));
                    if(jQuery(this).attr('data-team_id') != "")
                    {
                        team_ids.push(jQuery(this).attr('data-team_id'));
                    }
                })
                jQuery('#lineup_ids_value').val(lineup_ids.join(','));
                jQuery('#team_ids_value').val(team_ids.join(','));
                
                //submit data
                jQuery.post(ajaxurl, 'action=submitTeamDraft&' + jQuery('#formLineup').serialize(), function(result) {
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
    },
    
    filterLineupCat: function()
    {
        jQuery('.team_item .btn_add_lineup').show();
        jQuery('.team_item .btn_delete_lineup').hide();
        this.checkLineupFulled();
        var cat = jQuery('#lineup_cat li a.f-is-active');
        var cat_id = cat.data('id');
        if(cat_id == 0)
        {
            return;
        }
        var no_duplicate_with = cat.data('no_duplicate_with');
        if(no_duplicate_with > 0)
        {
            jQuery('.lineup_' + no_duplicate_with).each(function(){
                jQuery('#team_' + jQuery(this).attr('data-team_id')).find('.btn_add_lineup').hide();
            });
        }
        
        jQuery('.lineup_' + cat_id + '.filled').each(function(){
            var team_id = jQuery(this).attr('data-team_id');
            jQuery('#team_' + team_id).find('.btn_add_lineup').hide();
            jQuery('#team_' + team_id).find('.btn_delete_lineup').show();
        });
    },
    
    addLineup: function(team_id)
    {
        var team = jQuery("#team_" + team_id);
        var image = team.data('image');
        var name = team.data('name');
        var salary = parseFloat(team.data('salary'));
        
        var select_tab = jQuery('.f-player-list-position-tabs li a.f-is-active');
        if(typeof select_tab != 'undefined' && parseInt(select_tab.data('id')) > 0)
        {
            var lineup = jQuery('.lineup_' + select_tab.data('id') + ':not(.filled):first');
        }
        else
        {
            var lineup = jQuery('.f-roster-position:not(.filled):first');
        }
        
        var lead_team = parseInt(lineup.data('lead_team'));
        if(this.checkDuplicateTeamForSameLineup(lineup, team_id))
        {
            alert(wpfs['you_cannot_pick_same_team_for_same_lineup']);
            return;
        }
        if(this.checkDuplicateWithSpecificLineup(team_id))
        {
            alert(wpfs['cannot_pick_same_team_for_lead_team']);
            return;
        }
        if(this.checkReachMaxTeamPick(team_id))
        {
            alert(wpfs['you_can_only_pick_same_team_n_times'].replace('%s', max_team_pick));
            return;
        }
        if(lead_team == 1)
        {
            salary = salary + Math.round(salary * 25 / 100);
        }
        lineup.attr('data-team_id', team_id);
        lineup.attr('data-team_salary', salary);
        lineup.addClass('filled');
        lineup.find('.f-empty-roster-slot-instruction').hide();
        lineup.find('.f-player-image').html('<img src="' + image + '" onerror="jQuery.teamdraft.setNoImage(jQuery(this))" />');
        lineup.find('.f-player').html(name);
        lineup.find('.f-salary').html(VIC_FormatMoney(salary)).css('visibility', 'visible');
        lineup.find('.btn_remove_lineup').css('visibility', 'visible');
        this.checkLineupFulled();
        this.calculateSalaryRemaining();
        this.filterLineupCat();
    },
    
    removeLineup: function(lineup)
    {
        lineup = lineup.closest('.f-roster-position');
        lineup.attr('data-team_id', '');
        lineup.attr('data-team_salary', '');
        lineup.removeClass('filled');
        lineup.find('.f-empty-roster-slot-instruction').show();
        lineup.find('.f-player-image').empty();
        lineup.find('.f-player').empty();
        lineup.find('.f-salary').empty().css('visibility', 'hidden');
        lineup.find('.btn_remove_lineup').css('visibility', 'hidden');
        this.checkLineupFulled();
        this.calculateSalaryRemaining();
        this.filterLineupCat();
    },
    
    checkLineupFulled: function()
    {
        if(jQuery('.f-roster-position.filled').length >= jQuery('.f-roster-position').length)
        {
            jQuery('.btn_add_lineup').hide();
            return true;
        }
        jQuery('.btn_add_lineup').show();
        return false;
    },
    
    checkDuplicateTeamForSameLineup(lineup, team_id)
    {
        var id = lineup.data('id');
        team_id = parseInt(team_id);
        if(jQuery('.lineup_' + id).length > 1)
        {
            var team_ids = [];
            jQuery('.lineup_' + id + '.filled').each(function(){
                var temp_team_id = parseInt(jQuery(this).attr('data-team_id'));
                if(team_ids.indexOf(temp_team_id) == -1){
                    team_ids.push(temp_team_id);
                }
            })
            if(team_ids.indexOf(team_id) > -1){
                return true;
            }
        }
        return false;
    },
    
    checkReachMaxTeamPick(team_id)
    {
        team_id = parseInt(team_id);
        var team_ids = [];
        var total = 0;
        jQuery('.f-roster-position.filled').each(function(){
            team_ids.push();
            if(team_id == parseInt(jQuery(this).attr('data-team_id')))
            {
                total += 1;
            }
        })
        if(total >= max_team_pick)
        {
            return true;
        }
        return false;
    },
    
    checkDuplicateWithSpecificLineup(team_id)
    {
        var lineup = jQuery('.f-roster-position:not(.filled):first');
        var no_duplicate_with = parseInt(lineup.data('no_duplicate_with'));
        if(no_duplicate_with == 0)
        {
            return false;
        }
        if(team_id == jQuery('.lineup_' + no_duplicate_with).attr('data-team_id'))
        {
            return true;
        }
        return false;
    },
    
    removeAllLineup: function()
    {
        jQuery('.f-roster-position').each(function(){
            jQuery.teamdraft.removeLineup(jQuery(this));
        })
    },
    
    calculateSalaryRemaining: function ()
    {
        var salary_remaining = parseFloat(jQuery('#salaryRemaining').data("value"));
        if(salary_remaining == 0)
        {
            return;
        }
        var salary = 0;
        var total_empty_lineup = jQuery('.f-roster-position:not(.filled)').length;
        var avg = total_empty_lineup > 0 ? Math.round(salary_remaining / total_empty_lineup) : 0;
        jQuery('.f-roster-position.filled').each(function(){
            salary += parseFloat(jQuery(this).data('team_salary'));
        })
        
        salary_remaining = salary_remaining - salary;
        jQuery('#AvgTeam').html(VIC_FormatMoney(avg, "$"));
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
        if(salary_remaining == 0)
        {
            return;
        }
        var salary = 0;
        jQuery('.f-roster-position.filled').each(function(){
            salary += parseFloat(jQuery(this).data('team_salary'));
        })
        if(salary > salary_remaining)
        {
            return true;
        }
        return false;
    },
    
    doSort: function (item)
    {
        jQuery("#listTeams table").tablesorter();
        jQuery("#listTeams table").trigger("updateAll");
        var index = item.index() + 1;
        jQuery("#listTeams table").trigger("sorton", [[[index, "n"]]]);
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
    
    setNoImage: function (item)
    {
        item.parent().addClass('f-no-image').css('background-image', '');
        item.remove();
    },
    
    //////////////////////////////////////////result//////////////////////////////////////////
    initTeamDraftResult: function()
    {
        var is_live = jQuery('#is_live').val();

        //check live
        if(is_live == 1)
        {
            //jQuery.teamdraft.liveEntriesResult();
            setInterval(function(){ 
                jQuery.teamdraft.liveEntriesResult();
            }, 240000);
        }
        /*else
        {
            jQuery.teamdraft.loadResult();
        }*/
        
        //result detail
        jQuery(document).on('click', '#table_standing tr', function(){
            result_select_id = jQuery(this).data('id');
            jQuery('#table_standing tr').removeClass('active');
            jQuery(this).addClass('active');
            var user_id = jQuery(this).data('user_id');
            var entry_number = jQuery(this).data('entry_number');
            jQuery.teamdraft.loadResultDetail(user_id, entry_number);
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
        jQuery.global.showLoading('#wrapper_standing');
        var data = {
            action: 'teamDraftLoadResult',
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
        
        jQuery.global.showLoading('#result');
        var data = {
            action: 'teamDraftLoadResultDetail',
            league_id: league_id,
            user_id: user_id,
            entry_number: entry_number
        };
        jQuery.post(ajaxurl, data, function (result) {
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
            jQuery.teamdraft.loadResult();
        });
    },
    
    setActiveFixture: function (item)
    {
        jQuery('.fixture-item').removeClass('f-is-active');
        jQuery(item).addClass('f-is-active');
        jQuery(item).blur();
        return false;
    },
    
    loadTeams: function(){
        var item = jQuery('.fixture-item.f-is-active');
        var item_table = jQuery('.f-player-list-table');
        if(item.hasClass('all-fixtures')){
            item_table.find('.team_item').show();
        }
        else{
            var team_id1 = item.data('teamId1');
            var team_id2 = item.data('teamId2');
            item_table.find('.team_item').hide();
            item_table.find('#team_' + team_id1).show();
            item_table.find('#team_' + team_id2).show();
        }
    }
};