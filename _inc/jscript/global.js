jQuery.global =
{
    showLoading: function(wrapper){
        jQuery(wrapper).append('<div class="f-loading"></div>');
    },
    
    hideLoading: function(wrapper){
        jQuery(wrapper + ' .f-loading').remove();
    },
    
    disableButton: function(id, text){
        jQuery('#' + id).attr('disabled', true).prop('disabled', true);
        if(typeof text != 'undefined')
        {
            jQuery('#' + id).val(text);
        }
    },
    
    enableButton: function(id, text){
        jQuery('#' + id).removeAttr('disabled');
        if(typeof text != 'undefined')
        {
            jQuery('#' + id).val(text);
        }
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
    
    loadWeeklyFixture: function ()
    {
        jQuery('.fixture_weekly').hide();
        jQuery('.fixture_week_' + jQuery('#week_filter').val()).show();
    },
    
    ruleScoring: function (leagueID, tab)
    {
        jQuery.global.showDialog('#dlgInfo', this.loading());
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
            jQuery.global.loadWeeklyFixture();
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

    loadTabScoringCategory: function (item, leagueID)
    {
        if (!item.find('a').hasClass('active'))
        {
            jQuery('#tabRuleScoring li a').removeClass('active');
            item.find('a').addClass('active');
            var data = 'leagueID=' + leagueID;
            jQuery('#vc-info-content').html(this.loading());
            jQuery.post(ajaxurl, "action=loadLeagueScoringCategory&" + data, function (result) {
                jQuery('#vc-info-content').html(result);
                jQuery('#week_filter option').each(function(){
                    if(jQuery('.fixture_week_' + jQuery(this).attr('value')).length == 0)
                    {
                        jQuery(this).remove();
                    }
                });
                jQuery.global.loadWeeklyFixture();
            })
        }
    },

    loadTabLeagueEntries: function (item, leagueID)
    {
        if (!item.find('a').hasClass('active'))
        {
            jQuery('#tabRuleScoring li a').removeClass('active');
            item.find('a').addClass('active');
            var data = 'leagueID=' + leagueID;
            jQuery('#vc-info-content').html(this.loading());
            jQuery.post(ajaxurl, "action=loadLeagueEntries&" + data, function (result) {
                jQuery('#vc-info-content').html(result);
            })
        }
    },

    loadTabLeaguePrizes: function (item, leagueID)
    {
        if (!item.find('a').hasClass('active'))
        {
            jQuery('#tabRuleScoring li a').removeClass('active');
            item.find('a').addClass('active');

            var data = 'leagueID=' + leagueID;
            jQuery('#vc-info-content').html(this.loading());
            jQuery.post(ajaxurl, "action=loadLeaguePrizes&" + data, function (result) {
                jQuery('#vc-info-content').html(result);
            })
        }
    },
    
    loadTabInviteFriends: function (item, leagueID)
    {
        if (!item.find('a').hasClass('active'))
        {
            jQuery('#tabRuleScoring li a').removeClass('active');
            item.find('a').addClass('active');

            var data = 'leagueID=' + leagueID;
            jQuery('#vc-info-content').html(this.loading());
            jQuery.post(ajaxurl, "action=loadInviteFriends&" + data, function (result) {
                jQuery('#vc-info-content').html(result);
            })
        }
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
    
    pickSelected: function()
    {
        var ifAnyBoutChecked = true;
        jQuery('#formData .vc-pickem-compare-row').each(function(){
            if(jQuery(this).find('input[type=radio]').length > 0 && !jQuery(this).find('input[type=radio]').is(':checked'))
            {
                ifAnyBoutChecked = false;
                return;
            }
        })

        if(!ifAnyBoutChecked )
        {
            alert(wpfs['input_picks']);
            return false;
        }
        return true;
    },
    
    pickSelectedOnlyOne: function()
    {
        var ifAnyBoutChecked = false;
        jQuery('#formData table:first tr').each(function(){
            if(jQuery(this).find('input[type=radio]').length > 0 && jQuery(this).find('input[type=radio]').is(':checked'))
            {
                ifAnyBoutChecked = true;
                return;
            }
        })

        if(!ifAnyBoutChecked )
        {
            alert(wpfs['input_picks_only_one']);
            return false;
        }
        return true;
    },
    
    cancelContest: function(league_id)
    {
        if(confirm(wpfs['a_sure'])){
            jQuery.post(ajaxurl, "action=userCancelContest" + "&league_id="+league_id, function(result) {
                //jQuery.lobby.loadLobbyPage();
                result = JSON.parse(result);
                if(result.notice)
                {
                    alert(result.notice);
                }
                else
                {
                    window.location = result.redirect;
                }
            })
        }
    },
    
    leaveContest: function(league_id, entry_number)
    {
        if(confirm(wpfs['a_sure'])){
            jQuery.post(ajaxurl, "action=userLeaveContest" + "&league_id="+league_id + "&entry_number=" + entry_number, function(result) {
                result = JSON.parse(result);
                if(result.notice)
                {
                    alert(result.notice);
                }
                else
                {
                    window.location = result.redirect;
                }
            })
        }
    },
    
    contestPasswordModal: function (league_id)
    {
        var dialog = jQuery("#dlgContestPassword").dialog({
            height: 'auto',
            width:'400',
            modal:true,
            title: wpfs['password'],
            open:function(){
                jQuery("#formContestPassword").find('#league_id').val(league_id);
            },
            close:function(){
                jQuery('#msgContestPassword').empty().hide();
                jQuery("#formContestPassword").find('#password').val('');
            },
            buttons: {
                "Send": function() {
                    jQuery.global.sendContestPassword();
                },
                "Close": function() {
                    dialog.dialog( "close" );
                }
            },
        });
    },
    
    sendContestPassword: function()
    {
        jQuery.post(ajaxurl, "action=sendContestPassword&" + jQuery("#formContestPassword").serialize(), function (result) {
            var data = JSON.parse(result);
            if (data.success == 0)
            {
                jQuery('#msgContestPassword').html(data.message).show();
            }
            else
            {
                window.location = data.message;
            }
        });
    },
    
    setNoImage: function (item)
    {
        item.parent().addClass('f-no-image').css('background-image', '');
        item.remove();
    },

    initSuccessSubmitModal: function (){
        if($('#successSubmitModal').length == 0){
            return;
        }

        var popupShow = jQuery.global.checkCookie("pick-popup-hide");
        if(popupShow != "1") {
            $('#successSubmitModal').modal({show: true});
        }

        $('#successSubmitModal').on('hidden.bs.modal', function () {
            if($('#pick-popup-hide').is(':checked')){
                jQuery.global.setCookie("pick-popup-hide", 1, 100);
            }
        })
    },

    setCookie: function (cname,cvalue,exdays){
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires=" + d.toGMTString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    },

    getCookie: function(cname){
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    },

    checkCookie: function(cname){
        var user=jQuery.global.getCookie(cname);
        if (user != "") {
            return "1";
        } else {
            return "0";
        }
    }
};

function checkAll()
{
    jQuery("input[name='val[friend_ids][]']").attr('checked', true);
}

function checkNone()
{
    jQuery("input[name='val[friend_ids][]']").removeAttr('checked');
}