jQuery.admin =
{
    action: function(id, sAction, sUrl)
    {
        var task = jQuery("#submitTask");
        switch (sAction)
        {
            case 'delete':
                jQuery("#js_id_row" + id).attr('checked', true);
                if(!this.checkSelectedItem())
                {
                    alert('Please select an item');
                }
                else if(confirm(wpfs['a_sure']))
                {
                    task.val("delete");
                    this.doSubmit();
                }
                else 
                {
                    jQuery("#js_id_row" + id).removeAttr('checked');
                    task.val('submitTask');
                }
                break;
            case 'upload':
                task.val("upload");
                this.doSubmit();
                break;
            case 'save':
                if(jQuery('#the-list .no-items').length > 0)
                {
                    alert('No item found');
                }
                else
                {
                    task.val("save");
                    this.doSubmit();
                }
                break;
            default:
                break;	
        }

        return false;
    },
    
    doSubmit : function(task)
    {
        var frm = document.adminForm;
        /*if(task != '')
        {
            frm.action = frm.action + task
        }*/
        frm.submit();
    },

    checkSelectedItem : function()
    {
        if(jQuery('input[name$="id[]"]:checked').length > 0)
        {
            return true;
        }
        return false;
    },
    
    newImage : function()
    {
        jQuery("#js_submit_upload_image").show();
        jQuery("#js_slide_current_image").remove();
    },
    
    userCredits : function(item, userID, task, sTitle)
    {
        var payment = this;
        var dialog = jQuery("#dlgUserCredits").dialog({
            height: 'auto',
            width:'450',
            modal:true,
            title:sTitle,
            open:function(){
                jQuery('#msgUserCredits').empty().hide(); 
                jQuery('#formUserCredits')[0].reset();
                var user = jQuery(item).parents('tr');
                jQuery("#formUserCredits").find('.user_id').val(userID);
                jQuery("#formUserCredits").find('.full_name').empty().append(user.find('.column-name').text());
                jQuery("#formUserCredits").find('.total_balance').empty().append(user.find('.column-balance').text());
                jQuery("#formUserCredits").find('.payment_request_pending').empty().append(user.find('.column-payment_request_pending').text());
            },
            buttons: {
                "Send": function() {
                    payment.sendUserCredits(task, jQuery(item).parents('tr'), userID);
                },
                Cancel: function() {
                    dialog.dialog( "close" );
                }
            },
        });
        return false;
    },
    
    sendUserCredits: function(task, obj, userID)
    {
        var dataString = jQuery('#formUserCredits').serialize();
        jQuery.post(ajaxurl, 'action=sendUserCredits&task=' + task + "&" + dataString, function(result) {
            var data = JSON.parse(result);
            if(data.notice)
            {
                jQuery('#msgUserCredits').empty().append(data.notice).show();
            }
            if(data.result)
            {
                alert(data.result);
                obj.find('.column-balance').empty().append(data.balance);
                jQuery("#dlgUserCredits").dialog('close');
            }
	    });
    },

    activeAutoContest: function (id, active){
        var data = {
            action: 'activeAutoContest',
            id : id,
            active: active
        };

        jQuery.post(ajaxurl, data, function(result) {
            result = JSON.parse(result);
            if(result.notice)
            {
                alert(result.notice);
            }else{
                var item = jQuery('#setting' + id);
                if(item.find('.active').is(':visible'))
                {
                    item.find('.unactive').show();
                    item.find('.active').hide();
                }
                else
                {
                    item.find('.active').show();
                    item.find('.unactive').hide();
                }
            }
        });

    },
    
    activeOrgsSetting: function(id, active)
    {
        var data = {
            action: 'activeOrgs',
            id : id,
            active: active
        };
        jQuery.post(ajaxurl, data, function(result) {
            result = JSON.parse(result);
            if(result.notice)
            {
                alert(result.notice);
            }
            else
            {
                var item = jQuery('#setting' + id);
                if(item.find('.active').is(':visible'))
                {
                    item.find('.unactive').show();
                    item.find('.active').hide();
                }
                else
                {
                    item.find('.active').show();
                    item.find('.unactive').hide();
                }
            }
	});
    },
    
    reversePointOrgsSetting: function(id, active)
    {
        var data = {
            action: 'reversePointOrgs',
            id : id,
            active: active
        };
        jQuery.post(ajaxurl, data, function(result) {
            result = JSON.parse(result);
            if(result.notice)
            {
                alert(result.notice);
            }
            else
            {
                var item = jQuery('#rv_setting' + id);
                if(item.find('.active').is(':visible'))
                {
                    item.find('.unactive').show();
                    item.find('.active').hide();
                }
                else
                {
                    item.find('.active').show();
                    item.find('.unactive').hide();
                }
            }
	});
    },
    
    activeScoringCategorySetting: function(id, active)
    {
        var data = {
            action: 'activeScoringCategory',
            id : id,
            active: active
        };
        jQuery.post(ajaxurl, data, function(result) {
            result = JSON.parse(result);
            if(result.notice)
            {
                alert(result.notice);
            }
            else
            {
                var itemActive = jQuery('#active' + id);
                var itemUnActive = jQuery('#unactive' + id);
                if(itemActive.is(':visible'))
                {
                    itemUnActive.show();
                    itemActive.hide();
                }
                else
                {
                    itemActive.show();
                    itemUnActive.hide();
                }
            }
	});
    },
    
    loadUser: function(obj, userID)
    {
        var data = {
            action: 'loadUser',
            user_id : userID
        };
        jQuery.post(ajaxurl, data, function(result) {
            var result = JSON.parse(result);
            obj.find('.column-ID').empty().append(result.ID);
            obj.find('.column-name').empty().append(result.user_login);
            obj.find('.column-balance').empty().append(result.balance);
            obj.find('.column-payment_request_pending').empty().append(result.payment_request_pending);
	});
    },
    
    userWithdrawls : function(id)
    {
        var dialog_data = {
            height: 'auto',
            width:'auto',
            modal:true,
            title:wpfs['withdrawal']
        };
        var status = jQuery('.withdraw_status_' + id).html().trim().toLowerCase();
        if(status == 'new' || status == 'sent email')
        {
            dialog_data['buttons'] = {
                "Send": function() {
                    jQuery.admin.sendUserWithdrawls(id);
                },
                Cancel: function() {
                    dialog.dialog( "close" );
                }
            };
            jQuery('.ui-dialog-buttonset').parent().show();
        }
        else
        {
            jQuery('.ui-dialog-buttonset').parent().hide();
        }
        var dialog = jQuery("#dlgUserWithdrawls").dialog(dialog_data);

        //load dialog content
        var data = {
            action: 'userWithdrawlDlg',
            id : id
        };
        jQuery.post(ajaxurl, data, function(result) {
            jQuery('#dlgUserWithdrawls').html(result);
	});
        return false;
    },
    
    sendUserWithdrawls: function(withdraw_id)
    {
        var dataString = jQuery('#formUserWithdrawls').serialize();
        jQuery(".ui-dialog").find('button').addClass('ui-state-disabled').attr('disabled', 'true');
        jQuery(".ui-dialog").find('button:last').prev().find('span').empty().append('Processing');
        
        jQuery.post(ajaxurl, 'action=sendUserWithdrawls&' + dataString, function(result) {
            var data = JSON.parse(result);
            jQuery(".ui-dialog").find('button').removeClass('ui-state-disabled').removeAttr('disabled');
            jQuery(".ui-dialog").find('button:last').prev().find('span').empty().append(wpfs['send']);
            if(data.notice)
            {
                jQuery('#msgUserWithdrawls').empty().append(data.notice).show();
            }
            else if(data.redirect)
            {
                window.location = data.redirect;
            }
            else if(data.result)
            {
                alert(data.result);
                var status = jQuery("#formUserWithdrawls").find('.status').val();
                if(data.status){
                    status = data.status;
                }
                jQuery('.withdraw_status_' + withdraw_id).html(status);
                jQuery("#dlgUserWithdrawls").dialog('close').empty();
            }
        })
    },
    
    showPoolStatisticDetail : function(poolID, sTitle)
    {
        var dialog = jQuery("#dlgStatistic").dialog({
            height: 'auto',
            width:'800',
            modal:true,
            title:sTitle,
            buttons: {
                'Close': function() {
                    dialog.dialog( "close" );
                }
            }
        });
        var data = {
            action: 'showPoolStatisticDetail',
            poolID : poolID
        };
        jQuery.post(ajaxurl, data, function(result) {
            var data = JSON.parse(result);
            jQuery("#dlgStatistic").empty().append(data.result);
        })
        return false;
    },
    
    showUserPicks: function(leagueID)
    {
        jQuery('#dlgPicks').empty().append('<center>' + wpfs['pleasewait'] + '</center>');
        var dialog = jQuery("#dlgPicks").dialog({
            width:'800',
            modal:true,
			title:wpfs['pleasewait'],
            buttons: {
                'Close': function() {
                    dialog.dialog( "close" );
                }
            }
        });
        var data = {
            action: 'showUserPicks',
            leagueID : leagueID
        };
        jQuery.post(ajaxurl, data, function(data) {
            jQuery("#dlgPicks").empty().append(data);
            jQuery.admin.showPicksDetail();
            var league = jQuery.parseJSON(jQuery("#leagueInfo").val()); 
			var sTitle = ' ';
			if(league)
			{
				sTitle = league.name;
			}
            jQuery("#dlgPicks").dialog({
                height: 'auto',
                width:'800',
                modal:true,
                title:sTitle,
                buttons: {
                    'Close': function() {
                        dialog.dialog( "close" );
                    }
                }
            });
        })
        return false;
    },
    
    showPicksDetail: function()
    {
        var user_id = jQuery("#cbUsers").val();
        jQuery('.cbEntry').hide();
        jQuery('#cbEntry' + user_id).show();
        var entry_number = jQuery('#cbEntry' + user_id + ':visible').val();
        if(typeof entry_number == 'undefined')
        {
            entry_number = 1;
        }
        var pick_items = ''; 
        var picks = jQuery.parseJSON(jQuery("#pickData").val()); 
        var league = jQuery.parseJSON(jQuery("#leagueInfo").val()); 
        var entries = '';
        
        //find players list
        for(var i in picks)
        {
            if(picks[i].userID == user_id)
            {
                for(var j in picks[i].entries)
                {
                    var entries = picks[i].entries;
                    if(entries[j].entry_number == entry_number)
                    {
                        pick_items = entries[j].pick_items;
                        break;
                    }
                }
                break;
            }
        }

        //show players
        var html = '';
        for(var i in pick_items)
        {
            var html_fight_name = '';
            if(league.gameType == 'PLAYERDRAFT' && league.is_team == 1)
            {
                html_fight_name = '<td>' + pick_items[i].team_name + '</td>';
            }
            else if(league.gameType != 'PLAYERDRAFT') 
            {
                html_fight_name = '<td>' + pick_items[i].fight_name + '</td>';
            }
            html += 
                '<tr>\n\
                    <td>' + pick_items[i].id + '</td>\n\
                    ' + html_fight_name + '\n\
                    <td>' + pick_items[i].name + '</td>\n\
                </tr>';
        }
        jQuery('#tbPickDetail tbody').empty().append(html);
    },
    
    showInviteFriendDialog: function(sTitle, leagueID){
        jQuery('#formInviteFriend')[0].reset();
        var dialog = jQuery("#dlgFriends").dialog({
            maxHeight: 500,
            width:800,
            minWidth:600,
            modal:true,
            title:sTitle,
            open: function() {
                jQuery('.ui-widget-overlay').addClass('custom-overlay');
            }
        });
        jQuery('#formInviteFriend #leagueIdValue').val(leagueID);
    },
    
    sendPrivateInvitationEmail: function()
    {
        jQuery('#formInviteFriend .loading').show();
        jQuery('#formInviteFriend #btnSendInvite').attr('disabled', 'disabled');
        jQuery.post(ajaxurl, jQuery('#formInviteFriend').serialize() + '&action=sendPrivateInvitationEmail', function(data) {
            var json = jQuery.parseJSON(data);
            if(json.message)
            {
                alert(json.message);
            }
            else if(json.notice)
            {
                alert(json.notice);
            }
            jQuery('#formInviteFriend .loading').hide();
            jQuery('#formInviteFriend #btnSendInvite').removeAttr('disabled');
        })
    },
    
    checkAllFriends: function()
    {
        jQuery("input[name='friend_ids[]']").attr('checked', true);
    },

    checkNoneFriends: function()
    {
        jQuery("input[name='friend_ids[]']").removeAttr('checked');
    },
    showInfoManageSport: function(){
            var dialog = jQuery("#readmeDialog").dialog({
            height: 'auto',
            width:'400',
            modal:true,
            title:"Read me",
            open:function(){
                
            },
            buttons: {
                Cancel: function() {
                    dialog.dialog( "close" );
                }
            },
        });
    },
    deleteTeamImage: function(teamID){
        var result = confirm('Do you want to delete this image?');
        if(result){
            var content = {'action':'deleteBackgroundTeamImage','teamID':teamID};
            jQuery.post(ajaxurl,content,function(data){
                    window.location.reload();
            })
        }
    },
    showInfoContactSport: function(){
        var dialog = jQuery("#contactmeDialog").dialog({
            height: 'auto',
            width:'400',
            modal:true,
            title:"Contact Info",
            open:function(){
                
            },
            buttons: {
                Cancel: function() {
                    dialog.dialog( "close" );
                }
            },
        });
    },
    showInfoContactSport2: function(){
        var dialog = jQuery("#contactmeDialog2").dialog({
            height: 'auto',
            width:'400',
            modal:true,
            title:"Contact Info",
            open:function(){
                
            },
            buttons: {
                Cancel: function() {
                    dialog.dialog( "close" );
                }
            },
        });
    },
    loadDashboardStats: function(){
        var stats_type = jQuery('select[name="stats_type"]').val();
        var stats_year = jQuery('select[name="stats_year"]').val();

        var content = {'action':'loadDashboardStatsAjax', 'stats_type': stats_type, 'stats_year': stats_year};
        
        //window.location.href = window.location.href+'&stats_type='+stats_type+'&stats_year='+stats_year;

        jQuery.post(ajaxurl, content, function(data){
            jQuery('#dash_board_chart').html(data);
        })
    },
    
    cancelContest: function(item, leagueID){
        if(confirm(wpfs['a_sure']))
        {
            var params = {
                action:'cancelContest', 
                leagueID: leagueID
            };
            jQuery.post(ajaxurl, params, function(data) {
                var json = jQuery.parseJSON(data);
                if(json.result)
                {
                    jQuery(item).closest("tr").find("td.column-action2").html(wpfs['contest_cancelled']);
                    alert(json.result);
                }
                else if(json.notice)
                {
                    alert(json.notice);
                }
            })
        }
    },
    
    testConnection: function(item){
        jQuery(item).val(wpfs['test_connection_testing']);
        var params = {
            action:'testConnection', 
        };
        jQuery.post(ajaxurl, params, function(data) {
            jQuery(item).val(wpfs['test_connection']);
            alert(data);
        })
    },
    
    showSetFeatureContestDlg: function(league_id, is_feature){
        var dialog_data = {
            height: 'auto',
            width: 'auto',
            modal: true,
            title: wpfs['feature_contest'],
            close: function(event, ui) {
                jQuery('#dlgFeatureContest').empty();
            },
            buttons:{
                "Set feature": {
                    text: is_feature == 1 ? wpfs['button_set_feature'] : wpfs['button_ok'],
                    id: "btnSetFeatureContest",
                    click: function() {
                        jQuery.admin.doSetFeatureContest();
                    }
                },
                Cancel: function() {
                    dialog.dialog( "close" );
                }
            }
        };
        var dialog = jQuery("#dlgFeatureContest").dialog(dialog_data);

        //load dialog content
        var data = {
            action: 'dlgSetFeatureContest',
            league_id : league_id,
            is_feature : is_feature
        };
        jQuery.post(ajaxurl, data, function(result) {
            jQuery('#dlgFeatureContest').html(result);
            if(jQuery('#fine-uploader-validation').length > 0)
            {
                var restrictedUploader = new qq.FineUploader({
                    element: document.getElementById("fine-uploader-validation"),
                    request: {
                        endpoint: ajaxurl
                    },
                    multiple: false,
                    validation: {
                        allowedExtensions: ['jpeg', 'jpg', 'png', 'gif'],
                        //itemLimit: 1,
                        sizeLimit: 52428800 // 50 kB = 50 * 1024 bytes
                    },
                    callbacks: {
                        onComplete: function(id, name, result, xhr) {
                            jQuery('#formFeatureContest #feature_image').val(result.filepath);
                        }
                    }
                });
            }
	});
        return false;
    },
    
    doSetFeatureContest: function(){
        jQuery('#btnSetFeatureContest').attr('disabled', 'disabled');
        jQuery('#msgFeatureContest').empty().hide();
        var params = jQuery('#formFeatureContest').serialize();
        jQuery.post(ajaxurl, 'action=doSetFeatureContest&' + params, function(result) {
            result = JSON.parse(result);
            if(result.success == 1)
            {
                jQuery("#dlgFeatureContest").dialog("close");
                var class_feature_contest = '.feature_contest_' + result.id;
                if(result.is_feature == 1)
                {
                    jQuery(class_feature_contest + '.active').show();
                    jQuery(class_feature_contest + '.unactive').hide();
                }
                else
                {
                    jQuery(class_feature_contest + '.active').hide();
                    jQuery(class_feature_contest + '.unactive').show();
                }
            }
            else
            {
                jQuery('#msgFeatureContest').html(result.message).show();
            }
            jQuery('#btnSetFeatureContest').removeAttr('disabled');
        })
    },
    
    initAddSport: function(){
        //check select upload photo type
        this.selectUploadPhotoOption();
        jQuery('#upload_photo').change(function(){
            jQuery.admin.selectUploadPhotoOption();
        })
    },
            
    selectUploadPhotoOption: function(){
        if(jQuery('#upload_photo').is(':checked')){
            jQuery('#is_playerdraft').attr('disabled', 'disabled');
            jQuery('#is_team').attr('disabled', 'disabled');
        }
        else{
            jQuery('#is_playerdraft').removeAttr('disabled');
            jQuery('#is_team').removeAttr('disabled');
        }
    },
    
    loadUploadPhotoResult: function(leagueID, status)
    {
        //init standing
        jQuery.uploadphoto.initStanding(true);

        jQuery('#dlgUploadPhotResult').find('#standing').html('<center>' + wpfs['pleasewait'] + '</center>');
        var dialog = jQuery("#dlgUploadPhotResult").dialog({
            width:'800',
            modal:true,
            title:wpfs['pleasewait'],
            buttons: {
                'Close': function() {
                    dialog.dialog( "close" );
                }
            }
        });
        
        var buttons;
        if(status == 'COMPLETE'){
            buttons = {
                'Close': function() {
                    dialog.dialog( "close" );
                }
            };
        }
        else{
            buttons = {
                "Complete": function() {
                    if(confirm(wpfs['confirm_complete'])){
                        jQuery.admin.completeUploadPhotoContest(leagueID);
                    }
                },
                'Close': function() {
                    dialog.dialog( "close" );
                }
            };
        }
        var data = {
            action: 'uploadPhotoLoadResult',
            page: typeof page != 'undefined' ? page : 1,
            league_id: leagueID,
            admin_page: true
        };
        jQuery.post(ajaxurl, data, function(data) {
            jQuery("#dlgUploadPhotResult").find('#standing').html(data);
            jQuery.admin.showPicksDetail();
            jQuery("#dlgUploadPhotResult").dialog({
                height: 'auto',
                width:'1000',
                modal:true,
                title:wpfs['update_result'],
                open:function(){
                    if(status == 'COMPLETE'){
                        jQuery('.ui-dialog-buttonset').find('button:first').hide();
                    }
                    else{
                        jQuery('.ui-dialog-buttonset').find('button:first').show();
                    }
                },
                buttons: buttons
            });
        })
        return false;
    },
    
    completeUploadPhotoContest: function(leagueID){
        var data = {
            action: 'completeUploadPhotoContest',
            league_id: leagueID
        };
        jQuery.post(ajaxurl, data, function(result) {
            result = JSON.parse(result);
            if(result.success == 1){
                window.location.reload();
            }
            else{
                alert(result.message);
            }
        })
    }
}

