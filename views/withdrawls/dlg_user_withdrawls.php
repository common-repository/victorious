<div id="msgUserWithdrawls" class="public_message"></div>
<form id="formUserWithdrawls" style="width: 500px;">
    <table>
        <tr>
            <td style="width: 170px"><?php echo esc_html(__("Name", 'victorious'));?></td>
            <td class="full_name">
                <?php echo esc_html($user['user_login']);?>
            </td>
        </tr>
        <tr>
            <td><?php echo esc_html(__("Amount", 'victorious'));?></td>
            <td class="amount">
                <?php echo VIC_FormatMoney($withdraw['amount'], $withdraw['balance_type']['currency_code_symbol']);?>
            </td>
        </tr>
        <tr>
            <td><?php echo esc_html(__("Real Amount", 'victorious'));?></td>
            <td class="real_amount">
                <?php echo VIC_FormatMoney($withdraw['real_amount'], $withdraw['balance_type']['currency_code_symbol']);?>
            </td>
        </tr>
        <?php if($withdraw['gateway'] == VICTORIOUS_GATEWAY_DFSCOIN):?>
        <tr>
            <td><?php echo esc_html(__("Dfs coins", 'victorious'));?></td>
            <td class="real_amount">
                <?php echo round($withdraw['real_amount'] / get_option('victorious_dfscoin_exchange_rate'));?>
            </td>
        </tr>
        <?php endif;?>
        <?php if($withdraw['gateway'] == VICTORIOUS_GATEWAY_FOOTYCASHCOIN):?>
        <tr>
            <td><?php echo esc_html(__("FootyCash Coins", 'victorious'));?></td>
            <td class="real_amount">
                <?php echo round($withdraw['real_amount'] / get_option('victorious_footycashcoin_exchange_rate'));?>
            </td>
        </tr>
        <?php endif;?>
        <tr>
            <td><?php echo esc_html(__("Gateway", 'victorious'));?></td>
            <td class="gateway">
                <?php echo esc_html($withdraw['gateway']);?>
            </td>
        </tr>
        <tr>
            <td><?php echo esc_html(__("Request Date", 'victorious'));?></td>
            <td class="request_date">
                <?php echo date('M-d-Y', strtotime($withdraw['requestDate']));?>
            </td>
        </tr>
        <tr>
            <td><?php echo esc_html(__("Reason", 'victorious'));?></td>
            <td class="reason">
                <?php echo esc_html($withdraw['reason']);?>
            </td>
        </tr>
    </table>
    <hr>
    <table>
        <tr>
            <td>
                <?php echo esc_html(__("Action", 'victorious'));?>
                <input type="hidden" name="withdrawlID" class="withdrawlID" value="<?php echo esc_html($withdraw['withdrawlID']);?>" />
            </td>
        </tr>
        <tr>
            <td>
                <?php if($withdraw['status'] == 'NEW'):?>
                    <select name="status" class="status">
                        <option value=""><?php echo esc_html(__("Select status", 'victorious'));?></option>
                        <option value="APPROVED"><?php echo esc_html(__("APPROVED", 'victorious'));?></option>
                        <option value="DECLINED"><?php echo esc_html(__("DECLINED", 'victorious'));?></option>
                    </select>
                <?php else:?>
                <b><?php echo esc_html($withdraw['status']);?></b>
                <?php endif;?>
            </td>
        </tr>
        <tr>
            <td><?php echo esc_html(__("Response Message", 'victorious'));?></td>
        </tr>
        <tr>
            <td>
                <?php if($withdraw['status'] == 'NEW'):?>
                    <textarea rows="5" cols="50" name="response_message" class="response_message"></textarea>
                <?php else:?>
                    <?php echo esc_html($withdraw['response_message']);?>
                <?php endif;?>
            </td>
        </tr>
    </table>
    <?php if($withdraw['status'] == 'NEW'):?>
        <?php if($withdraw['gateway'] == VICTORIOUS_GATEWAY_DFSCOIN):?>
            <table class="gateway_dfscoin">
                <tr>
                    <td>
                        <?php echo esc_html(__('Please follow these steps:', 'victorious'));?><br/>
                        1: <?php echo esc_html(__('Download program here', 'victorious'));?><br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;<?php echo esc_html(__('Windows', 'victorious'));?> - <a href="https://github.com/NicoDFS/DFSCoin/raw/master/dfscoin-qt-windows.zip" target="_blank"><?php echo esc_html(__('Download', 'victorious'));?></a><br/>
                        &nbsp;&nbsp;&nbsp;&nbsp;<?php echo esc_html(__('Linux', 'victorious'));?> - <a href="https://github.com/NicoDFS/DFSCoin/raw/master/dfscoin-qt-linux.tar.gz" target="_blank"><?php echo esc_html(__('Download', 'victorious'));?></a><br/>
                        2: <?php echo esc_html(__('Please Copy This DFSCoin Wallet Address:', 'victorious'))." <b>".$withdraw['dfscoin_wallet_address']."</b>";?><br/>
                        3: <?php echo esc_html(__('Pay Via Your Desktop Wallet To Given Wallet Address.', 'victorious'));?><br/>
                        3: <?php echo esc_html(__('Copy Transaction Id From Your Wallet to text box below.', 'victorious'));?><br/>
                        4: <?php echo esc_html(__('Click On Deposit Now Button.', 'victorious'));?><br/>
                        5: <?php echo esc_html(__('Now Deposit Has Been Successfully Completed.', 'victorious'));?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php echo esc_html(__("Transaction Id", 'victorious'));?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="text" name="transactionID" style="width:100%"/>
                    </td>
                </tr>
            </table>
        <?php endif;?>
        <?php if($withdraw['gateway'] == VICTORIOUS_GATEWAY_PAYPAL):?>
            <input type="hidden" name="gateway" value="<?php echo esc_html($withdraw['gateway']);?>" />
        <?php endif;?>
    <?php endif;?>
</form>
<?php if($withdraw['status'] == 'NEW' && $withdraw['gateway'] == VICTORIOUS_GATEWAY_PAYPAL):?>
<form id="paypalCheckout" action="" method="post">
    <input type="hidden" name="cmd" value="_xclick" />
    <input type="hidden" name="business" value="" />
    <input type="hidden" name="quantity" value="1" />
    <input type="hidden" name="item_name" value="" />
    <input type="hidden" name="item_number" value="1" />
    <input type="hidden" name="amount" value="" />
    <input type="hidden" name="currency_code" value="USD" />
    <input type="hidden" name="cancel_return" value="" />
    <input type="hidden" name="notify_url" value="" />
    <input type="hidden" name="return" value="" />
    <input type="hidden" name="custom" value="" />
</form>
<?php endif;?>
