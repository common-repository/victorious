<div id="dlgRequestPayment" style="display: none">
    <?php if($gatewayList == null):?>
        <?php echo esc_html(__('No available payment gateway, please contact admin.', 'victorious'));?>
    <?php else:?>
        <form id="formRequestPayment">
            <input type="hidden" id="gateway_name" name="gateway_name" value="">
            <p>
                <?php echo esc_html(__('Available balance', 'victorious'));?>: <span class="balance"></span><br/>
                <?php echo esc_html(__('Rate', 'victorious'));?>: <?php echo esc_html(get_option('victorious_credit_to_cash'));?> <?php echo esc_html(__('withdraw equals', 'victorious'));?> <?php echo VIC_FormatMoney(1);?>
            </p>
            <?php if(!empty($global_setting['allow_multiple_balances']) && !empty($balance_types)):?>
                <p>
                    <?php echo esc_html(__('Balance Type', 'victorious'));?>
                    <select class="form-control" name="val[balance_type_id]" id="withdrawal_balance_type" onchange="jQuery.payment.withdrawalChangeBalanceType()">
                        <?php foreach($balance_types as $balance_type):?>
                            <option value="<?php echo esc_attr($balance_type['id']);?>">
                                <?php echo esc_html($balance_type['name']);?>
                            </option>
                        <?php endforeach;?>
                    </select>
                </p>
            <?php endif;?>
            <p>
                <?php echo esc_html(__('Amount', 'victorious'));?>:<br/>
                <input type="text" name="val[credits]" />
            </p>
            <p>
                <?php echo esc_html(__('Reason', 'victorious'));?>:<br/>
                <textarea rows="5" cols="50" name="val[reason]"></textarea>
            </p>
            <div id="withdrawal_gateway">
                <p <?php if(count($withdrawl_gateways) == 1):?>style="display:none;"<?php endif;?>>
                   <select id="payout_gateway" onchange="jQuery.payment.loadPaymentMethod()" name="val[gateway_id]">

                    <?php foreach($gatewayList as $gateway_value => $gateway): ?>
                        <option value="<?php echo esc_html($gateway_value); ?>"><?php echo esc_html($gateway['name']); ?></option>
                    <?php endforeach; ?>
                 </select>
               </p>
                <div class="payout_paypal" style="display: none">
                    <p>
                        <?php echo esc_html(__('Email', 'victorious'));?>:<br/>
                        <input type="text" name="val[email]" size="60" value="<?php if(isset($aUserPayment['email'])):?><?php echo esc_html($aUserPayment['email']);?><?php endif;?>" />
                    </p>
                </div>
                <?php if(array_key_exists(VICTORIOUS_GATEWAY_DFSCOIN, $gatewayList)):?>
                <div class="payout_dfscoin" style="display: none">
                    <p>
                        <?php echo esc_html(__('Wallet address', 'victorious'));?>:<br/>
                        <input type="text" name="val[dfscoin_wallet_address]" size="60" value="<?php echo isset($aUserPayment['dfscoin_wallet_address']) ? esc_html($aUserPayment['dfscoin_wallet_address']) : '';?>">
                    </p>
                </div>
                <?php endif;?>
            </div>
        </form>
        <div id="msgRequestPayment" class="public_message"></div>
    <?php endif;?>
</div>