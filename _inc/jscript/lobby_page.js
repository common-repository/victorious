jQuery.lobby = 
{
    initLobby: function(){
        jQuery(document).on('click', '.has_password', function(){
            jQuery.global.contestPasswordModal(jQuery(this).data('id'));
        })
        
        //join upload photo
        jQuery(document).on('click', '.btn-join', function(){
            jQuery(this).attr('disabled', 'disabled');
            var params = {
                action: 'submitUploadPhoto',
                league_id: jQuery(this).data('id'),
                entry_number: jQuery(this).data('entryNumber'),
            }
            jQuery.post(ajaxurl, params, function(result) {
                result = JSON.parse(result);
                if(result.success == 0)
                {
                    jQuery(this).removeAttr('disabled');
                    alert(result.message);
                }
                else
                {
                    window.location = result.redirect;
                }
            })
        })

        jQuery('.vc-filter-item').change(function(){
            jQuery.lobby.showLobbyPage();
        })

        //sort
        jQuery(document).on('click', '#vc-table-lobby thead tr th', function(){
            var header = jQuery(this);
            jQuery('#vc-table-lobby thead tr th').each(function(){
                if(jQuery(this).data('sort_field') != header.data('sort_field'))
                {
                    jQuery(this).find('.material-icons').remove();
                    jQuery(this).data('sort_type', '');
                }
            });
            if(typeof header.data('sort_type') == 'undefined' || header.data('sort_type') == '' || header.data('sort_type') == 'desc')
            {
                header.data('sort_type', 'asc');
                header.find('.material-icons').remove();
                header.append('<span class="material-icons">expand_less</span>');
            }
            else
            {
                header.data('sort_type', 'desc');
                header.find('.material-icons').remove();
                header.append('<span class="material-icons">expand_more</span>');
            }

            jQuery.lobby.doSort();
        })
    },
    
    loadLobbyPage: function()
    {
        var params = '';
        if(jQuery('#selectPoolId').val() != '')
        {
            params = '&poolId=' + jQuery('#selectPoolId').val();
        }
        if(jQuery('#selectGameType').val() != '')
        {
            params = '&game_type=' + jQuery('#selectGameType').val();
        }
        jQuery.post(ajaxurl, "action=loadLeagueLobby" + params, function(result) {
            jQuery('#lobbyData').html(result);
            
            jQuery('#lobbyContent th.f-title').trigger('click');
            
            //disable sports have no leauges
            jQuery('.f-sport ul li input').each(function(){
                var org_id = jQuery(this).val();
                if(org_id != '')
                {
                    var hasLeague = false;
                    jQuery('.vc-lobby-item').each(function(){
                        if(jQuery(this).data('organization') == org_id)
                        {
                            hasLeague = true;
                            return false;
                        }
                    });
                    if(!hasLeague)
                    {
                        jQuery(this).attr('disabled', 'true');
                        jQuery(this).closest('label').addClass('f-disabled');
                    }
                    else 
                    {
                        jQuery(this).removeAttr('disabled', 'true');
                        jQuery(this).closest('label').removeClass('f-disabled');
                    }
                }
            });
            
            //check show data
            jQuery.lobby.showLobbyPage();
        })
        
        //rugby comfirm
        jQuery(document).on('click', '.live_draft_confirm_join', function(e){
            e.preventDefault();
            e.stopPropagation();
            jQuery('#dlgRugbyConfirmJoin .btn_ok').attr('href', jQuery(this).attr('href'));
            jQuery('#live_draft_time').html(jQuery(this).data('draft_time'));
            jQuery.playerdraft.showDialog("#dlgRugbyConfirmJoin");
        })

        //rugby comfirm
        jQuery(document).on('click', '.playoff_confirm_join', function(e){
            e.preventDefault();
            e.stopPropagation();
            var leagueId = jQuery(this).data('id');
            if (confirm(wpfs['playoff_confirm_join']) == true) {
                jQuery.playoff.joinContest(leagueId);
            }
        })
    },
    
    showLobbyPage: function()
    {
        var org = jQuery('#vc-filter-sport').val();
        var keyword = /*jQuery('.f-text-search .f-search-input').val().toString()*/ '';
        var contestType = jQuery('#vc-filter-contest-type').val();
        var leagueSize = jQuery('#vc-filter-size').val();
        leagueSize = leagueSize.split("-");
        var multiEntry = jQuery('.f-multientry input').is(':checked');
        var entryFeeStart = 0 /*parseInt(jQuery('.f-entryfee .ui-rangeSlider-leftLabel .ui-rangeSlider-label-inner').text())*/;
        var entryFeeEnd = 100000 /*parseInt(jQuery('.f-entryfee .ui-rangeSlider-rightLabel .ui-rangeSlider-label-inner').text())*/;
		var contestCreator = jQuery('#vc-filter-creator').val();
		if(no_cash == 1)
        {
            entryFeeStart = entryFeeEnd = 0;
        }
        var startTime = jQuery('#vc-filter-start-time').val();
        
        //filter
        var minTime = 0;
        jQuery('.vc-lobby-item').hide();
        jQuery('.vc-lobby-item').each(function(){
            var league_name = jQuery(this).data('name').toString();
            var league_size = parseInt(jQuery(this).data('size'));
            var league_entry_fee = parseFloat(jQuery(this).data('entry_fee'));
            var league_today = jQuery(this).data('today');
            var league_organization = parseInt(jQuery(this).data('organization'));
            var league_start_timestamp = jQuery(this).data('start_timestamp');
            var creatorIsAdmin = jQuery(this).data('creator_is_admin');
            if(keyword == '' || league_name.search(new RegExp(keyword,'i')) > -1)
            {
                //filter sport
                if((typeof org == typeof undefined) || org == '' || (org == league_organization))
                {
                    //filter starttime
                    if((startTime == 'today' && league_today == true) || 
                       ((startTime == 'next' && league_today == false)) || 
                        startTime == 'all')
                    {
                        //filter type
                        if((contestType == 'headtohead' && league_size == 2) || 
                           (contestType == 'league' && league_size > 2) ||
                            contestType == 'all')
                        {
                            //filter size
                            if((league_size == parseInt(leagueSize[0])) ||
                               (league_size >= parseInt(leagueSize[0]) && league_size <= parseInt(leagueSize[1])) ||
                               (leagueSize[0].indexOf('+') !== -1 && league_size >= parseInt(leagueSize[0])) ||
                               leagueSize[0].toString() == 'all')
                            {
                                //filter entry fee
                                if(league_entry_fee >= entryFeeStart && 
                                   league_entry_fee <= entryFeeEnd)
                                {
                                    if (contestCreator == 'all' ||
                                        (contestCreator == 'admin' && creatorIsAdmin) ||
                                        (contestCreator == 'user' && !creatorIsAdmin))
                                    {
                                        jQuery(this).show();
                                        if(minTime == 0 || minTime > league_start_timestamp)
                                        {
                                            minTime = league_start_timestamp;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        })

        //set countdown
        if(minTime > 0)
        {
            clearCountDown();
            getCountdown("lobbyCountdown", true, minTime);
            jQuery('#contestCountdown').show();
        }
        else 
        {
            jQuery('#contestCountdown').hide();
        }
        if((jQuery('.vc-lobby-item.f-lobby-hide').length == jQuery('.vc-lobby-item').length) || jQuery('.vc-lobby-item').length == 0)
        {
            jQuery('.f-empty-view').show();
            jQuery('#wrapContest').hide();
            jQuery("#contestCountdown").hide();
        }
        else 
        {
            jQuery('.f-empty-view').hide();
            jQuery('#wrapContest').show();
            jQuery("#contestCountdown").show();
        }
        //jQuery('#lobbyContent #lobbyData').empty().append(html);
        jQuery("#lobbyContent").tablesorter();
        jQuery("#lobbyContent").trigger("updateAll");        
        
        //sort
        jQuery.lobby.doSort();
    },
    
    search: function()
    {
        jQuery.lobby.showLobbyPage();
        var item = jQuery('#f-foo');
        if(item.find('.f-search-input').val() != '')
        {
            item.find('.f-search-reset').css('display', 'inline');
        }
        else 
        {
            item.find('.f-search-reset').hide();
        }
    },
    
    doSort: function(){
        var header = '';
        jQuery('#vc-table-lobby thead tr th').each(function(){
            if(typeof jQuery(this).data('sort_type') != 'undefined' && jQuery(this).data('sort_type') != '')
            {
                header = jQuery(this);
                return false;
            }
        });
        if(header != '')
        {
            var className = header.data('sort_field');
            var arrData = jQuery("#lobbyData .vc-lobby-item").get();
            var sortType = header.data('sort_type');
            arrData.sort(function (a, b) {
                var val1 = jQuery(a).find('.' + className).data('sort');
                var val2 = jQuery(b).find('.' + className).data('sort');
                if (jQuery.isNumeric(val1) && jQuery.isNumeric(val2)){
                    return sortType == 'desc' ? val1 - val2 : val2 - val1;
                }
                else{
                    return sortType == 'desc' ? val1.localeCompare(val2) : val2.localeCompare(val1);
                }
            });

            jQuery.each(arrData, function (index, row) {
                jQuery("#lobbyData").append(row);
            });
        }
    },
    
    suvivorDecisionDlg : function()
    {
        jQuery("#dlgSuvivorDecision").dialog({
            height: 'auto',
            width:'500',
            modal:true,
            title:wpfs['goliath_decision_time'],
            open:function(){
                jQuery('#msgSuvivorDecision').empty().hide(); 
                jQuery('#formSuvivorDecision')[0].reset();
            },
            buttons: [{
                id: "btn_split",
                text: wpfs['split'],
                click: function () {
                    jQuery.lobby.goliathMakeDecision(0);
                }
            },
            {
                id: "btn_continue",
                text: wpfs['continue'],
                click: function () {
                    jQuery.lobby.goliathMakeDecision(1);
                }
            }]
        });
        return false;
    },
    
    goliathMakeDecision: function(type)
    {
        jQuery.post(ajaxurl, "action=goliathMakeDecision" + "&type="+type, function(result) {
            result = JSON.parse(result);
            jQuery('#dlgSuvivorDecision').dialog('close')
            alert(result.message);
        })
    },
}

jQuery(window).load(function(){
    //jQuery('#f-foo')[0].reset();
    jQuery.lobby.loadLobbyPage();
    //setTimeout(function() { jQuery.lobby.loadLobbyPage() }, 2000);
    setInterval(function() { jQuery.lobby.loadLobbyPage() }, 300000);
})


jQuery(document).on('click', '.f-filter ul li input', function(){
    switch(jQuery(this).attr('data-filter-type'))
    {
        case 'sport':
            jQuery('.f-sport label').removeClass('f-checked');
            jQuery(this).parents('label').addClass('f-checked');
            break;
        case 'type':
            jQuery('.f-type label').removeClass('f-checked');
            jQuery(this).parents('label').addClass('f-checked');
            if(jQuery(this).val() == 'league')
            {
                jQuery('.f-filter .f-size').show();
                jQuery('.f-filter .f-multientry').show();
            }
            else 
            {
                jQuery('.f-filter .f-size').hide();
                jQuery('.f-filter .f-size label:first li input').trigger('click');
                jQuery('.f-filter .f-multientry').hide();
            }
            break;
        case 'size':
            jQuery('.f-filter .f-size label').removeClass('f-checked');
            jQuery(this).parents('label').addClass('f-checked');
            break;
        case 'start':
            jQuery('.f-startTime label').removeClass('f-checked');
            jQuery(this).parents('label').addClass('f-checked');
            break;
        case 'creator':
			jQuery('.f-contestCreator label').removeClass('f-checked');
			jQuery(this).parents('label').addClass('f-checked');
			break;
    }
    jQuery.lobby.showLobbyPage();
})


jQuery(document).on('keyup', '#f-foo .f-search-input', function(){
    jQuery.lobby.search();
})

jQuery(document).on('click', '#f-foo .f-search-reset', function(){
    jQuery('#f-foo .f-search-input').val('');
    jQuery.lobby.search();
})

jQuery(function() {
    function parsePrize(prize)
    {
        if(prize >= 1000)
        {
            prize = (prize / 1000) + 'K';
        }
        return prize;
    }
});

function quoteEncoding(str)
{
    str = str.replace("&#39;", "'");
    str = str.replace(/'/g, "\\'");
    return str;
}