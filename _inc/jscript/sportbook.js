var result_select_id = "";
var result_select_page = "";

jQuery.sportbook = {
    initSportbook: function () {
        //edit
        jQuery('.sportbook_game_point.active').each(function () {
            jQuery.sportbook.loadSlip(jQuery(this));
            jQuery.sportbook.betSlipCount();
            jQuery.sportbook.calculateWagerRemaining();
        });
        jQuery.sportbook.detectEmptySlip();
        jQuery.sportbook.decideShowParlay();
        jQuery.sportbook.toggeBetslip();

        //select slip
        jQuery('.sportbook_game_point').click(function () {
            if (jQuery(this).hasClass('locked')) {
                return;
            }
            if (jQuery(this).hasClass('active')) {
                jQuery(this).removeClass('active');
                jQuery.sportbook.removeSlip(jQuery(this).data('id'));
            } else {
                jQuery(this).addClass('active');
                jQuery.sportbook.loadSlip(jQuery(this));
            }
            jQuery.sportbook.detectEmptySlip();
            jQuery.sportbook.decideShowParlay();
            jQuery.sportbook.betSlipCount();

            //remove team win if select draw and revert
            if (jQuery(this).data('type') == 'draw') {
                jQuery.sportbook.allowOnePickSameColumn(jQuery(this), 'win');
            }
            if (jQuery(this).data('type') == 'win') {
                jQuery.sportbook.allowOnePickSameColumn(jQuery(this), 'draw');
            }
        });

        //remove slip
        jQuery('#slipContent').on('click', '.remove_slip', function () {
            var id = jQuery(this).data('id');
            jQuery('#slip_' + id).removeClass('active');
            jQuery.sportbook.removeSlip(id);
            jQuery.sportbook.detectEmptySlip();
            jQuery.sportbook.decideShowParlay();
            jQuery.sportbook.betSlipCount();
            jQuery.sportbook.calculateWagerRemaining();
        })

        //remove all slip
        jQuery('.content-betslip').on('click', '.remove_all_slip', function () {
            jQuery('.sportbook_select_item').each(function () {
                jQuery(this).find('.remove_slip').trigger('click');
            })
        })

        //cauclate to win
        jQuery('#slipContent').on('keyup change', '.straight-wage .wager', function () {
            jQuery.sportbook.calculateToWinForWager(jQuery(this));
            jQuery.sportbook.calculateWagerRemaining();
        })

        jQuery('#slipContent').on('keyup change', '.straight-wage .to_win', function () {
            jQuery.sportbook.calculateWagerForToWin(jQuery(this));
            jQuery.sportbook.calculateWagerRemaining();
        })

        //cauclate parlay
        jQuery('.sportbook_slip').on('keyup change', '.parlay-wage .wager', function () {
            jQuery.sportbook.calculateToWinForParlay(jQuery(this));
            jQuery.sportbook.calculateWagerRemaining();
        })

        jQuery('.sportbook_slip').on('keyup change', '.parlay-wage .to_win', function () {
            jQuery.sportbook.calculateParlayForToWin(jQuery(this));
            jQuery.sportbook.calculateWagerRemaining();
        })

        //submit
        this.submitPicks();

        this.checkMatchOverTime();

        //live update over under point
        this.updateOverUnderPoint();
    },

    allowOnePickSameColumn(item, type_remove) {
        var parent = item.closest('.sportbook_pick_item');
        parent.find('.sportbook_game_point').each(function () {
            if (jQuery(this).hasClass('active') && jQuery(this).data('type') == type_remove) {
                jQuery(this).removeClass('active');
                jQuery.sportbook.removeSlip(jQuery(this).data('id'));
            }
        })
    },

    checkMatchOverTime() {
        setInterval(function () {
            jQuery('.sportbook_game_point').each(function () {
                var parentItem = jQuery(this).closest('.sportbook_pick_item');
                var startTime = parentItem.data('startTime');
                var locked = parentItem.data('locked');
                var now = Math.floor(Date.now() / 1000);
                var slipId = jQuery(this).data('id');
                var slipItem = jQuery('#' + slipId);
                if (now >= startTime || locked == 1) {
                    jQuery(this).addClass('locked').addClass('overtime');
                    var html = '<span class="igt-icon color-dark"><span class="material-icons">lock</span></span>';
                    jQuery(this).find('.sportbook_point_wrapper').html(html);

                    slipItem.find('.remove_slip').remove();
                    slipItem.find('.wager').attr('disabled', 'disabled');
                    slipItem.find('.to_win').attr('disabled', 'disabled');
                    slipItem.addClass('locked');
                }
            })
        }, 1000);
    },

    updateOverUnderPoint() {
        setInterval(function () {
            jQuery.post(ajaxurl, 'action=updateOverUnderPoint&league_id=' + jQuery('#league_id').val(), function (result) {
                var json = jQuery.parseJSON(result);
                jQuery('.sportbook_game_point.locked:not(.overtime)').each(function () {
                    var fight_id = jQuery(this).data('fightId');
                    var team_id = jQuery(this).data('teamId');
                    var type = jQuery(this).data('type');
                    var fight = json[fight_id + '_' + team_id];
                    var fight_draw = json[fight_id + '_draw'];
                    if (type == 'win' && parseFloat(fight['win_points']) > 0) {
                        var html = '<span></span><span>' + fight['win_points'] + '</span>';
                        jQuery(this).find('.info-box').html(html);
                        jQuery(this).removeClass('locked');
                    } else if (type == 'spread' && parseFloat(fight['spread_points']) > 0) {
                        var html = '<span></span><span>' + fight['spread_points'] + '</span>';
                        jQuery(this).find('.info-box').html(html);
                        jQuery(this).removeClass('locked');
                    } else if (type == 'over' && parseFloat(fight['total_over']) > 0) {
                        var html = '<span>O ' + fight['total_over_under'] + '</span><span>' + fight['total_over'] + '</span>';
                        jQuery(this).find('.info-box').html(html);
                        jQuery(this).removeClass('locked');
                    } else if (type == 'under' && parseFloat(fight['total_under']) > 0) {
                        var html = '<span>U ' + fight['total_over_under'] + '</span><span>' + fight['total_under'] + '</span>';
                        jQuery(this).find('.info-box').html(html);
                        jQuery(this).removeClass('locked');
                    } else if (type == 'draw' && parseFloat(fight_draw) > 0) {
                        var html = '<span></span><span>' + fight_draw + '</span>';
                        jQuery(this).find('.info-box').html(html);
                        jQuery(this).removeClass('locked');
                    }
                })
            })
        }, 30000);
    },

    loadSlip: function (item) {
        var template = jQuery(jQuery('#templateStraight').html());
        var id = item.data('id');
        var fightId = item.data('fightId');
        var teamId = item.data('teamId');
        var fightItem = jQuery('#fight_' + fightId);
        var type = item.data('type');
        var name = item.data('name');
        var value = item.data('value');
        var price = item.data('price');
        var wager = item.data('wager');
        var title = name + ' ' + value;
        var point_type = '';
        if (type == 'win') {
            point_type = wpfs['win_betting'];
        } else if (type == 'draw') {
            point_type = wpfs['draw_betting'];
        } else if (type == 'spread') {
            point_type = wpfs['spread_betting'];
        } else {
            point_type = wpfs['total_points_scored'];
        }
        template.data('teamId', teamId);
        template.data('fightId', fightId);
        template.data('wagerType', type);
        template.attr('id', id);
        template.find('.remove_slip').data('id', id);
        template.find('.select_title').html(fightItem.data('name'));
        template.find('.point_name').html(title);
        template.find('.point_type').html(point_type);
        template.find('.point_price').html(price);
        template.find('.wager').data('value', price);
        template.find('.wager').val(wager);
        jQuery('#slipContent').append(template);

        this.calculateToWinForWager(template.find('.wager'));
    },

    betSlipCount: function () {
        var total = jQuery('.sportbook_select_item').length;
        if (!jQuery('.parlay_slip').is(':visible')) {
            total -= 1;
        }
        jQuery('#betSlipCount').html(total);
    },

    removeSlip: function (id) {
        jQuery('#' + id).remove();
        jQuery('#slip_' + id).data('wager', '');
    },

    detectEmptySlip: function () {
        if (jQuery('.sportbook_game_point.active').length > 0) {
            jQuery('.straight_slip').show();
            jQuery('.empty-betslip').hide();
        } else {
            jQuery('.straight_slip').hide();
            jQuery('.empty-betslip').show();
        }
    },

    decideShowParlay: function () {
        var allow_parlay = true;
        var temp_type_group = '';
        var temp_fight_ids = [];
        if (jQuery('.sportbook_game_point.active').length < 2) {
            jQuery('.parlay_slip').hide();
            return;
        }
        jQuery('.sportbook_game_point.active').each(function () {
            var item = jQuery(this);
            var type_group = item.data('typeGroup');
            var team_fight_id = item.data('fightId');
            if (temp_type_group == '') {
                temp_type_group = type_group;
            } else if (temp_type_group != type_group || temp_fight_ids.includes(team_fight_id)) {
                allow_parlay = false;
                return;
            }
            temp_fight_ids.push(team_fight_id);
        })
        if (allow_parlay) {
            jQuery('.parlay_total').html(jQuery('.sportbook_game_point.active').length);
            jQuery('.parlay_slip').show();
            jQuery.sportbook.calculateToWinForParlay(jQuery('.sportbook_slip .parlay-wage .wager'));
        } else {
            jQuery('.parlay_slip').hide();
        }
    },

    calculateToWinForWager: function (item) {
        var parent = item.closest('.sportbook_select_item');
        var wagerType = parent.data('wagerType');
        var odd = item.data('value');
        var price = item.val();
        var to_win = '';
        if (price > 0) {
            if (/*wagerType == 'draw'*/ jQuery('#simple_point').val() == 1) {
                to_win = jQuery.sportbook.calculateSlipSimple(odd, price);
            } else {
                to_win = jQuery.sportbook.calculateSlip(odd, price);
            }
        }
        parent.find('.to_win').val(to_win);
    },

    calculateWagerForToWin: function (item) {
        var parent = item.closest('.sportbook_select_item');
        var wagerType = parent.data('wagerType');
        var odd = parent.find('.wager').data('value');
        var price = item.val();
        var to_win = '';
        if (price > 0) {
            if (/*wagerType == 'draw'*/ jQuery('#simple_point').val() == 1) {
                to_win = jQuery.sportbook.calculateSlipSimpleToWin(odd, price);
            } else {
                to_win = jQuery.sportbook.calculateSlipToWin(odd, price);
            }
        }
        parent.find('.wager').val(to_win);
    },

    calculateSlip: function (odd, wager) {
        odd = parseFloat(odd);
        wager = parseFloat(wager);
        var result = 0;
        if (odd < 0) {
            result = ((100 - odd) / -odd) * wager - wager;
        } else {
            result = ((100 + odd) / 100) * wager - wager;
        }
        return result.toFixed(2);
    },
    
    calculateSlipToWin: function (odd, towin) {
        odd = parseFloat(odd);
        towin = parseFloat(towin);
        var result = 0;
        if (odd < 0) {
            result = towin / (((-odd + 100) / -odd) - 1);
        } else {
            
            result = towin / (((odd + 100) / 100) -1);
        }
        return result.toFixed(2);
    },
    
    calculateSlipSimple: function (odd, wager) {
        odd = parseFloat(odd);
        wager = parseFloat(wager);
        var result = odd * wager;
        return result.toFixed(2);
    },
    
    calculateSlipSimpleToWin: function (odd, towin) {
        odd = parseFloat(odd);
        towin = parseFloat(towin);
        var result = towin / odd;
        return result.toFixed(2);
    },

    ///////////////calculate for parlay
    calculateToWinForParlay: function (item) {
        var parent = item.closest('.parlay-wage');
        var price = item.val();
        var to_win = '';
        if (price > 0) {
            if (jQuery('#simple_point').val() == 1) {
                to_win = jQuery.sportbook.calculateParlaySimple(price);
            } else {
                to_win = jQuery.sportbook.calculateParlay(price);
            }
        }
        parent.find('.to_win').val(to_win);
    },

    calculateParlayForToWin: function (item) {
        var parent = item.closest('.parlay-wage');
        var price = item.val();
        var to_win = '';
        if (price > 0) {
            if (jQuery('#simple_point').val() == 1) {
                to_win = jQuery.sportbook.calculateParlayToWinSimple(price);
            } else {
                to_win = jQuery.sportbook.calculateParlayToWin(price);
            }
        }
        parent.find('.wager').val(to_win);
    },
    
    calculateParlay: function (wager) {
        var total_ratio = 1;
        jQuery('.sportbook_game_point.active').each(function () {
            var ratio = 0;
            var odd = jQuery(this).data('price');
            if (odd < 0) {
                ratio = (100 - odd) / -odd;
            } else {
                ratio = (100 + odd) / 100;
            }
            total_ratio *= ratio;
        })
        return (total_ratio * wager - wager).toFixed(2);
    },
    
    calculateParlaySimple: function (wager) {
        var total_odd = 1;
        jQuery('.sportbook_game_point.active').each(function () {
            var odd = jQuery(this).data('price');
            total_odd *= odd;
        })
        return (total_odd * wager - wager).toFixed(2);
    },

    calculateParlayToWin: function (towin) {
        var total_ratio = 1;
        jQuery('.sportbook_game_point.active').each(function () {
            var ratio = 0;
            var odd = jQuery(this).data('price');
            if (odd < 0) {
                ratio = (100 - odd) / -odd;
            } else {
                ratio = (100 + odd) / 100;
            }
            total_ratio *= ratio;
        })
        return (towin / (total_ratio - 1)).toFixed(2);
    },

    calculateParlayToWinSimple: function (towin) {
        var total_odd = 1;
        jQuery('.sportbook_game_point.active').each(function () {
            var odd = jQuery(this).data('price');
            total_odd *= odd;
        })
        return (towin / (total_odd - 1)).toFixed(2);
    },

    calculateWagerRemaining: function () {
        var bet_credit = parseFloat(jQuery('#bet_credit').data('value'));
        var total = 0;
        jQuery('.money-input').each(function () {
            if (typeof jQuery(this).find('.wager').val() != 'undefined' && jQuery(this).find('.wager').val() != '') {
                total += parseFloat(jQuery(this).find('.wager').val());
            }
        })
        bet_credit = bet_credit - total;
        jQuery('#bet_credit').html(VIC_FormatMoney(bet_credit))
    },

    submitPicks: function () {
        //submit data
        jQuery(document).on('click', '#btnSubmit', function () {
            if (confirm(wpfs['sportbook_confirm_submit'])) {
                jQuery.global.disableButton('btnSubmit');
                if (!jQuery.sportbook.checkSelectSlip())
                {
                    alert(wpfs['sportbook_error_empty_slip']);
                    jQuery.global.enableButton('btnSubmit');
                } else if (!jQuery.sportbook.checkFillWager())
                {
                    alert(wpfs['sportbook_error_fill_all_wagers']);
                    jQuery.global.enableButton('btnSubmit');
                } else if (jQuery.sportbook.checkOverCredit())
                {
                    alert(wpfs['sportbook_error_over_credit']);
                    jQuery.global.enableButton('btnSubmit');
                } else
                {
                    var wager_type = [];
                    var wager = [];
                    var to_win = [];
                    var team_id = [];
                    jQuery('.sportbook_select_item:visible:not(".locked")').each(function () {
                        var item = jQuery(this);
                        wager_type.push(item.data('wagerType'));
                        wager.push(item.find('.wager').val());
                        to_win.push(item.find('.to_win').val());
                        team_id.push(item.data('fightId') + '_' + item.data('teamId'));
                    })
                    jQuery('#wager_type_value').val(wager_type.join(','));
                    jQuery('#wager_value').val(wager.join(','));
                    jQuery('#to_win_value').val(to_win.join(','));
                    jQuery('#team_id_value').val(team_id.join(','));

                    //submit data
                    jQuery.post(ajaxurl, 'action=submitSportbook&' + jQuery('#formData').serialize(), function (result) {
                        var json = jQuery.parseJSON(result);
                        if (json.success == 0 && json.redirect)
                        {
                            window.location = json.redirect;
                        } else if (json.success == 0)
                        {
                            jQuery.global.enableButton('btnSubmit');
                            alert(json.message);
                        } else
                        {
                            window.location = json.redirect;
                        }
                    })
                }
            }
        })
    },

    checkSelectSlip: function () {
        return jQuery('.sportbook_game_point.active:not(".locked")').length > 0 ? true : false;
    },

    checkFillWager: function () {
        var isValid = true;
        if (jQuery('.parlay_slip .sportbook_select_item:visible:not(".locked")').length > 0) {
            var parlay_value = jQuery('.parlay_slip .sportbook_select_item:visible:not(".locked")').find('.wager').val().trim();
            parlay_value = parseFloat(parlay_value);
            if (!isNaN(parlay_value) && parlay_value > 0) {
                return true;
            }
        }

        jQuery('.straight_slip .sportbook_select_item:visible:not(".locked")').each(function () {
            var wager = parseFloat(jQuery(this).find('.wager').val().trim());
            if (wager == 0 || isNaN(wager)) {
                isValid = false;
                return;
            }
        })
        return isValid;
    },

    checkOverCredit: function () {
        var bet_credit = parseFloat(jQuery('#bet_credit').data('value'));
        var total = 0;
        jQuery('.sportbook_select_item:visible').each(function () {
            var value = jQuery(this).find('.wager').val().trim();
            if (value != '') {
                total += parseFloat(value);
            }
        })
        if (total > bet_credit) {
            return true;
        }
        return false;
    },
    toggeBetslip: function(){
        jQuery('.btn-betslip').click(function(e) {
            jQuery(this).parent().find('.content-betslip').toggleClass('hide');
            e.stopPropagation();
        });
        jQuery('.content-betslip i').click(function(e) {
            jQuery(this).parents().find('.content-betslip').toggleClass('hide');
            e.stopPropagation();
        });
    },

    initSportbookResult: function()
    {
        var is_live = jQuery('#is_live').val();

        //check live
        if(is_live == 1)
        {
            //jQuery.sportbook.liveEntriesResult();
            setInterval(function(){
                jQuery.sportbook.liveEntriesResult();
            }, 60000);
        }

        //result detail
        jQuery(document).on('click', '#table_standing tr', function(){
            result_select_id = jQuery(this).attr('id');
            jQuery('#table_standing tr').removeClass('active');
            jQuery(this).addClass('active');
            jQuery.sportbook.loadResultDetail();
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
            action: 'sportbookLoadResult',
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

    loadResultDetail: function ()
    {
        var league_id = jQuery('#league_id').val();
        var user_id = jQuery('#user_id').val();
        var entry_number = jQuery('#entry_number').val();

        jQuery.global.showLoading('#vc-leaderboard');
        jQuery.global.showLoading('#vc-leaderboard-detail');
        var data = {
            action: 'sportbookLoadResultDetail',
            league_id: league_id,
            user_id: user_id,
            entry_number: entry_number
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
            action: 'sportbookLiveEntriesResult',
            leagueID: league_id
        };
        jQuery.post(ajaxurl, data, function () {
            jQuery.sportbook.loadResult();
        });
    },
};