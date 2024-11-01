jQuery(document).on('submit', "#formAddCredits", function(e){
    e.preventDefault();
});

jQuery.payment =
{
    sendCredits: function()
    {
        var dataString = jQuery('#formAddCredits').serialize();
        jQuery('.public_message').hide();
        jQuery('#btnAddCredits').attr('disabled', 'disabled');
        jQuery('#formAddCredits').find('.waiting').show();
        jQuery.post(ajaxurl, 'action=addCredits&' + dataString, function(result) {
            var data = JSON.parse(result);
            if(data.url)
            {
                window.location = data.url;
            }
            else if(data.notice)
            {
                jQuery('#btnAddCredits').removeAttr('disabled');
                jQuery('#msgAddCredits').empty().append(data.notice).show();
                jQuery('html,body').animate({
                    scrollTop: jQuery("#msgAddCredits").offset().top - 50},
                    'slow');
            }
            else if(data.result)
            {
                window.location = data.result;
            }
            jQuery('#formAddCredits').find('.waiting').hide();
        })
    },
    
    requestPayment : function(sTitle)
    {
        var payment = this;
        var send = wpfs['send'];
        var cancel = wpfs['cancel'];
        var dialog = jQuery("#dlgRequestPayment").dialog({
            height: 'auto',
            width:'500',
            modal:true,
            title:sTitle,
            open:function(){
                if(jQuery('#formRequestPayment').length > 0)
                {
                    jQuery('#msgRequestPayment').empty().hide(); 
                    jQuery('#formRequestPayment')[0].reset();
                    payment.loadUserBalance();
                    payment.loadPaymentMethod();
                    jQuery('#payout_gateway').trigger('change');
                }
            },
            buttons: {
                send: function() {
                    if(jQuery('#formRequestPayment').length > 0)
                    {
                        payment.sendRequestPayment();
                    }
                },
                cancel: function() {
                    dialog.dialog( "close" );
                }
            },
        });
        return false;
    },
    loadPaymentMethod: function(){
        var gateway_name = jQuery('#payout_gateway').val();
        if(typeof gateway_name == 'undefined'){
            return;
        }

        jQuery('.payout_paypal').hide();

        jQuery('.payout_paypal input[type=text]').attr('disabled',true);
        if(gateway_name == wpfs['VICTORIOUS_GATEWAY_PAYPAL'] ){ // paypal
            jQuery('.payout_paypal').show();
            jQuery('.payout_paypal input[type=text]').attr('disabled',false);
        }
        else if(gateway_name == wpfs['VICTORIOUS_GATEWAY_DFSCOIN']){ //dfscoin
            jQuery('.payout_dfscoin').show();
            jQuery('.payout_dfscoin input[type=text]').attr('disabled',false);
        }
        return false;
    },

    withdrawalChangeBalanceType: function(){
        if(jQuery('#withdrawal_balance_type').val() == wpfs['VICTORIOUS_DEFAULT_BALANCE_TYPE_ID']){
            jQuery('#withdrawal_gateway').show();
        }
        else{
            jQuery('#withdrawal_gateway').hide();
        }
    },

    loadUserBalance: function()
    {
        jQuery.post(ajaxurl, 'action=loadUserBalance', function(result) {
            jQuery('#formRequestPayment .balance').empty().append(result);
        })
        return false;
    },

    sendRequestPayment: function()
    {
        if(jQuery('#payout_gateway').val() == wpfs['VICTORIOUS_GATEWAY_PAYSIMPLE']){
            this.paySimpleRequestToWithdraw();
        }
        else{
            jQuery(".ui-dialog").find('button').addClass('ui-state-disabled').attr('disabled', 'true');
            jQuery(".ui-dialog").find('button:last').prev().find('span').empty().append('Processing');
            var dataString = jQuery('#formRequestPayment').serialize();
            jQuery.post(ajaxurl, 'action=requestPayment&' + dataString, function(result) {
                var data = JSON.parse(result);
                if(data.notice)
                {
                    jQuery('#msgRequestPayment').empty().append(data.notice).show();
                }
                else if(data.result)
                {
                    jQuery("#dlgRequestPayment").dialog( "close" );
                    window.location = data.redirect;
                }
                jQuery(".ui-dialog").find('button').removeClass('ui-state-disabled').removeAttr('disabled');
                jQuery(".ui-dialog").find('button:last').prev().find('span').empty().append(wpfs['send']);
            })
        }
    },

    paySimpleRequestToWithdraw: function(){
        jQuery(".ui-dialog").find('button').addClass('ui-state-disabled').attr('disabled', 'true');
        jQuery(".ui-dialog").find('button:last').prev().find('span').empty().append('Processing');
        var dataString = jQuery('#formRequestPayment').serialize();
        jQuery.post(ajaxurl, 'action=paySimpleRequestToWithdraw&' + dataString, function(result) {
            var data = JSON.parse(result);
            if(data.notice)
            {
                jQuery('#msgRequestPayment').empty().append(data.notice).show();
            }
            else if(data.result)
            {
                jQuery("#dlgRequestPayment").dialog( "close" );
                window.location = data.redirect;
            }
            jQuery(".ui-dialog").find('button').removeClass('ui-state-disabled').removeAttr('disabled');
            jQuery(".ui-dialog").find('button:last').prev().find('span').empty().append(wpfs['send']);
        })
    },
    
    showDlgCoupon : function(sTitle)
    {
        var payment = this;
        var add = wpfs['add'];
        var cancel = wpfs['cancel'];
        var dialog = jQuery("#dlgCoupon").dialog({
            height: 'auto',
            width:'300',
            modal:true,
            title:sTitle,
            open:function(){
                jQuery('#msgCoupon').empty().hide(); 
                jQuery('#formCoupon')[0].reset();
            },
            buttons: {
                add: function() {
                    jQuery.payment.addMoneyByCoupon();
                },
                cancel: function() {
                    dialog.dialog( "close" );
                }
            },
        });
        return false;
    },
    
    addMoneyByCoupon: function()
    {
        jQuery.post(ajaxurl, 'action=addMoneyByCoupon&' + jQuery('#formCoupon').serialize(), function(result) {
            result = jQuery.parseJSON(result);
            if(result.notice)
            {
                jQuery('#msgCoupon').empty().append(result.notice).show(); 
            }
            else 
            {
                location.reload();
            }
        })
    },
    
    addFundValue: function(value, percentage)
    {
        value = parseFloat(value);
        percentage = parseInt(percentage);
        if(value > 0)
        {
            value = value + parseFloat((value * percentage / 100).toFixed(2));
        }
        jQuery('#realCredits').empty().append(value);
    },
    
    applyCoupon: function()
    {
        var code = jQuery.trim(jQuery("#f-add-funds-coupon-code-input").val());
        if(code.length)
        {
            var msg = jQuery('.public_message').hide();
            var applyBtn = jQuery('#f-add-funds-coupon-button-apply').attr('disabled', 'disabled');
            jQuery.post(ajaxurl, 'action=applyCoupon&coupon_code=' + code, function(result) {
                result = jQuery.parseJSON(result);
                applyBtn.removeAttr('disabled');
                if(result.notice)
                {
                    msg.empty().append(result.notice).show(); 
                }
                if (result.needSubmit)
                {
                    jQuery("#f-add-funds-coupon-code").val(code);
                }
                if(result.msg)
                {
                    var dialog = jQuery('<div id="msgCouponInfo">').dialog({
                        height: 'auto',
                        width: '300',
                        modal: true,
                        title: result.msg.title || 'Coupon Info',
                        open: function () {
                            jQuery('#msgCouponInfo').empty().text(result.msg.text);
                        },
                        close: function () {
                            dialog.dialog("destroy");
                            if (result.msg.reload)
                            {
                                location.reload();
                            }
                        },
                        buttons: {
                            Ok: function () {
                                dialog.dialog("close");
                            }
                        }
                    });
                }
            });
        }
        else
        {
            jQuery("#f-add-funds-coupon-code").val("");
        }
    },
    
    transferToAccountDlg : function(sTitle)
    {
        var payment = this;
        var send = wpfs['send'];
        var cancel = wpfs['cancel'];
        var dialog = jQuery("#dlgTransferToAccount").dialog({
            height: 'auto',
            width:'500',
            modal:true,
            title:sTitle,
            open:function(){
                jQuery('#msgTransferToAccount').empty().hide(); 
                jQuery('#formTransferToAccount')[0].reset();
                jQuery.payment.suggestUsername();
            },
            buttons: {
                send: function() {
                    payment.transferToAccount();
                },
                cancel: function() {
                    dialog.dialog( "close" );
                }
            },
        });
        return false;
    },
    
    transferToAccount: function(){
        jQuery.post(ajaxurl, 'action=transferToAccount&' + jQuery('#formTransferToAccount').serialize(), function(result){
            var data = JSON.parse(result);
            if(data.notice)
            {
                jQuery('#msgTransferToAccount').empty().append(data.notice).show();
            }
            else if(data.result)
            {
                jQuery('#formTransferToAccount')[0].reset();
                jQuery('#msgTransferToAccount').empty().append(data.result).show();
            }
        });
    },
    
    suggestUsername: function(){
        var item = jQuery('#transfer_username');
        item.autocomplete({
            source: function (request, response) {
                jQuery.post(ajaxurl, 'action=suggestUsername&' + jQuery('#formTransferToAccount').serialize(), function(data){
                    if(data != 'null')
                    {
                        response(jQuery.parseJSON(data));
                    }
                    else
                    {
                        item.val('');
                    }
                });
            },
            minLength: 2,
            messages: {
                noResults: '',
                results: function() {}
            },
            select: function (event, ui) {
                item.val(ui.item.label);
                jQuery('#transfer_user_id').val(ui.item.value);
                return false;
            },
        }).data("ui-autocomplete")._renderItem = function(ul, item) {
            var term = this.term;
            //var regex = new RegExp("\\S*" + $.ui.autocomplete.escapeRegex(term) + "\\S*", "gi");
            var regex = new RegExp(jQuery.ui.autocomplete.escapeRegex(term), "gi");
            var label = item.label.replace(regex, '<b>$&</b>');
            return jQuery('<li id="cat_suggest_' + item.value + '"></li>').append(label).appendTo(ul);
        };;
    },
    
    ///////////////////////gamble gateway///////////////////////
    gambleAddFundStep1: function(){
        jQuery('#msgAddFund').html('').hide();
        jQuery('#formAddFund').find('.waiting').show();
        jQuery('#btnAddFund').attr('disabled', 'disabled');
        jQuery.post(ajaxurl, 'action=gambleAddFundStep1&' + jQuery('#formAddFund').serialize(), function(result){
            var json = jQuery.parseJSON(result);
            if(json.notice)
            {
                jQuery('#msgAddFund').html(json.notice).show();
                jQuery('#formAddFund').find('.waiting').hide();
                jQuery('#btnAddFund').removeAttr('disabled');
            }
            else if(!json.existing){
                jQuery('#gamebleContent').data('amount', json.amount);
                jQuery('#gamebleContent').html(json.content);
            }
            else{
                jQuery.payment.gambleAddFundStep2(json.amount);
            }
        });
    },
    
    gambleAddFundStep2: function(amount){
        jQuery.post(ajaxurl, 'action=gambleAddFundStep2&amount=' + amount, function(result){
            var json = jQuery.parseJSON(result);
            if(json.notice)
            {
                jQuery('#msgAddFund').html(json.notice).show();
                jQuery('#formAddFund').find('.waiting').hide();
                jQuery('#btnAddFund').removeAttr('disabled');
            }
            else if(json.code){
                jQuery('#gamebleContent').data('code', json.code);
                jQuery('#gamebleContent').html(json.content);
            }
        });
    },
    
    gamebleWithdrawal: function(code){
        var params = {
            action: "gamebleWithdrawal",
            code: code
        };
        jQuery.post(ajaxurl, params, function(result){
            if(jQuery.payment.isJson(result)){
                var json = jQuery.parseJSON(result);
                if(json.notice)
                {
                    jQuery('#gamebleContent').html(json.notice);
                }
            }
            else{
                jQuery('#gamebleContent').html(result);
            }
        });
    },
    
    gambleConfirmPayment: function(){
        var params = {
            action: "gambleConfirmPayment",
            code: jQuery('#gamebleContent').data('code')
        };
        jQuery.post(ajaxurl, params, function(result){
            var json = jQuery.parseJSON(result);
            if(json.notice)
            {
				alert(json.notice);
            }
            else{
                location.reload();
            }
        });
    },
    ///////////////////////end gamble gateway///////////////////////

    ///////////////////////payway gateway///////////////////////
    initAddFundPaySimple: function(){
        jQuery('#btnCreateCustomer').click(function(){
            jQuery.payment.paySimpleCreateCustomer();
        })

        jQuery('#btnAddCredits').click(function(){
            jQuery.payment.paySimpleAddFund();
        })
    },

    paySimpleCreateCustomer: function(){
        var dataString = jQuery('#formPaysimpleCustomer').serialize();
        jQuery('#msgCustomer').hide();
        jQuery('#btnCreateCustomer').attr('disabled', 'disabled');
        jQuery('#formPaysimpleCustomer').find('.waiting').show();
        jQuery.post(ajaxurl, 'action=paySimpleCreateCustomer&' + dataString, function(result) {
            var data = JSON.parse(result);
            if(data.notice){
                jQuery('#formPaysimpleCustomer').find('.waiting').hide();
                jQuery('#btnCreateCustomer').removeAttr('disabled');
                jQuery('#msgCustomer').html(data.notice).show();
            }
            else{
                window.location = data.result;
            }
        })
    },

    paySimpleAddFund: function(){
        jQuery('#msgAddCredits').hide();
        jQuery('#btnAddCredits').attr('disabled', 'disabled');
        jQuery('#formAddCredits').find('.waiting').show();
        jQuery.post(ajaxurl, 'action=paySimpleAddFund&' + jQuery('#formAddCredits').serialize(), function(result){
            var data = JSON.parse(result);
            if(data.notice){
                jQuery('#formAddCredits').find('.waiting').hide();
                jQuery('#btnAddCredits').removeAttr('disabled');
                jQuery('#msgAddCredits').html(data.notice).show();
            }
            else{
                window.location = data.url;
            }
        });
    },
    ///////////////////////end payway gateway///////////////////////
    
    isJson: function (str) {
        try
        {
            JSON.parse(str);
        } catch (e)
        {
            return false;
        }
        return true;
    }
}