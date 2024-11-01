jQuery.tradeRugby = {
    showListUsers: function (league_id, entry_number, page) {
        if(typeof page == 'undefined')
        {
            page = 1;
        }
        jQuery("#resultDialog .f-loading").show();
        if(page == 1)
        {
            jQuery('#resultDialog .list-users').remove();
            var dialog = jQuery("#resultDialog").dialog({
                maxHeight: 500,
                width: 800,
                minWidth: 600,
                modal: true,
                title: wpfs['user_list'],
                open: function () {
                    jQuery('.ui-widget-overlay').addClass('custom-overlay');
                }
            });
        }
        
        var data = {
            action: 'liveDraftLoadListUserInLeague',
            leagueID : league_id,
            entry_number: entry_number,
            page: page
        };
        jQuery.post(ajaxurl, data, function (response) {
            jQuery("#resultDialog .f-loading").hide();
            jQuery('#resultDialog #btn_loadmore').remove();
            jQuery('#resultDialog').append(response);
        });
    },
    tradePlayer: function (is_user, obj, player_id, position_id) {
        var cls_trade;
        var amount = "";
        if (is_user) // player belong to user
        {
            cls_trade = jQuery('.user-position ul');
            amount = '<div class="clear"></div>Amount <input type="text" class="trade_amount" />';
        } else {
            cls_trade = jQuery('.target-position ul');
        }
        var key = is_user + '_' + player_id + '_' + position_id;
        var clone_obj = jQuery(obj).closest('li').clone();
        // check exist player
        var item = jQuery('.middle-column ul li[data-key=' + key + '] ').length;
        if (item > 0) {
            jQuery('.rugby-message').empty().append('Player is already exist').slideToggle().delay(4000).fadeOut();
            return;
        }
        var data_id = player_id + '_' + position_id;
        clone_obj.attr('data-id', data_id);
        clone_obj.attr('data-key', key);
        clone_obj.find('.f-salary').remove();
        clone_obj.find('a.f-rugby-add').remove();
        clone_obj.find('a').show();
        if(amount != "")
        {
            clone_obj.attr('style', 'height:auto')
            clone_obj.append(amount);
        }
        clone_obj.addClass('trade_request_list');
        cls_trade.append(clone_obj);

        // change status for removing
        jQuery(obj).hide();
        jQuery(obj).closest('li').find('.f-rugby-remove').show();
    },
    getValueTradePlayer: function (cls) {
        var player = [];
        jQuery(cls).each(function () {
            var data_id = jQuery(this).attr('data-id');
            player.push(data_id);
        });
        if (player.length > 0) {
            return player;
        }
        return false;

    },
    parseTradePlayer: function (player,is_check_valid) {
        var obj_player = {};
        for (var i in player) {
            var key = player[i].split('_');
            var position = key[1];
            if (typeof obj_player[position] == 'undefined') {
                obj_player[position] = 0;
            }
            obj_player[position] += 1;
        }
        return obj_player;
    },

    removeTradePosition: function (obj, key) {
        jQuery('.middle-column ul li[data-key=' + key + '] ').remove();
//        jQuery(obj).hide();
//        jQuery(obj).closest('li').find('.f-rugby-add').show();
        jQuery('ul li[data-org-key='+key+']').find('.f-rugby-add').show();
        jQuery('ul li[data-org-key='+key+']').find('.f-rugby-remove').hide();

    }
    ,
    sendTradeData: function () {
        // count player trade
        var user_player = this.getValueTradePlayer('.user-position ul li');
        var target_player = this.getValueTradePlayer('.target-position ul li');
        if (!user_player || !target_player) {
            jQuery('.rugby-message').empty().append('Positions not match').slideToggle().delay(4000).fadeOut();
            return;
        }
        var obj_user = this.parseTradePlayer(user_player);
        var obj_target = this.parseTradePlayer(target_player);

        if(Object.keys(obj_user).length != Object.keys(obj_target).length){
            jQuery('.rugby-message').empty().append('Positions not match').slideToggle().delay(4000).fadeOut();
                return;
        }
        // check match
        for (var i in obj_user) {
            var is_found = false;
            for (var j in obj_target) {
                if (i == j && obj_user[i] == obj_target[j]) {
                    is_found = true;
                }
            }
            if (!is_found) {
                jQuery('.rugby-message').empty().append('Positions not match').slideToggle().delay(4000).fadeOut();
                return;
            }
        }
        
        //trade amount
        var trade_amount = [];
        jQuery('.trade_amount').each(function(){
            trade_amount.push(jQuery(this).val());
        });
        
        // start insert
        if(confirm(wpfs['confirm_change_player_with_other_user']))
        {
           jQuery('.rugby-trade-player #btn-send').attr('disabled', 'true').val(wpfs['sending']);
           user_player = JSON.stringify(user_player);
           target_player = JSON.stringify(target_player);
           jQuery('#user_values').val("").val(user_player);
           jQuery('#target_values').val("").val(target_player);
           jQuery('#trade_amount_values').val(trade_amount.join(","));
            jQuery.post(ajaxurl, 'action=liveDraftSendTradePlayers&' + jQuery('#frm-trade-players').serialize(), function (response) {
                response = JSON.parse(response);
                if(response.error){
                    jQuery('.rugby-message').empty().append('Positions not match').slideToggle().delay(4000).fadeOut();
                  return;
                }
                else if(typeof response.success != 'undefined' && !response.success){
                    jQuery('.rugby-message').empty().append(response.message).slideToggle().delay(4000).fadeOut();
                    return;
                }
                window.location = response.redirect;
            });
        }
    },
    approveTradeRequest: function(obj,request_id){
          jQuery(obj).closest('.request-item').find('.rugby-message-error').hide();
          jQuery(obj).closest('.request-item').find('.rugby-message-success').hide();
          this.disableButton(obj);
          jQuery.post(ajaxurl, 'action=liveDraftApprovedTradeRequest&request_id=' +request_id , function (response) {
            response = JSON.parse(response);
            jQuery.tradeRugby.enableButton(obj);
              if(response.error){ // show error
                  jQuery(obj).closest('.request-item').find('.rugby-message-error').empty().html(response.mss).show();
              }else{ // success full
                   jQuery.tradeRugby.disableButton(obj);
                   location.reload();
              }
          });
    },
    rejectTradeRequet: function(obj,request_id){
        this.disableButton(obj);
        jQuery.post(ajaxurl, 'action=liveDraftRejectTradeRequest&request_id=' +request_id , function (response) {
              response = JSON.parse(response);
              jQuery.tradeRugby.enableButton(obj);
              if(response.error){ // show error
                   jQuery(obj).closest('.request-item').find('.rugby-message-error').empty().html(response.mss).show();
              }else{ // success full
                   jQuery.tradeRugby.disableButton(obj);
                   location.reload();
              }
          });
    },
    disableButton: function(obj){
        jQuery(obj).closest('.request-item').find('.btn-approve-trade').attr('disabled',true);
        jQuery(obj).closest('.request-item').find('.btn-reject-trade').attr('disabled',true);
    },
    enableButton: function(obj){
         jQuery(obj).closest('.request-item').find('.btn-approve-trade').removeAttr('disabled');
         jQuery(obj).closest('.request-item').find('.btn-reject-trade').removeAttr('disabled');
    }



};