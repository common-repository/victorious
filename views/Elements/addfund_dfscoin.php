<?php VIC_GetMessage(); ?>
<div style="width: 70%;float: left">
    <div id="msgAddCredits" class="public_message"></div>
    <?php if(get_option('victorious_dfscoin_wallet_address') == null):?>
        <?php echo esc_html(__('No wallet address, please contact admin.', 'victorious'));?>
    <?php else:?>
        <form id="formAddCredits">
            <input type="hidden" name="gateway" value="<?php echo VICTORIOUS_GATEWAY_DFSCOIN;?>" />
            <h3><?php echo esc_html(__('Information transaction', 'victorious'));?></h3>
            <?php echo esc_html(__('Please follow these steps:', 'victorious'));?><br/>
            1: <?php echo esc_html(__('Download program here', 'victorious'));?><br/>
                &nbsp;&nbsp;&nbsp;&nbsp;<?php echo esc_html(__('Windows', 'victorious'));?> - <a href="https://github.com/NicoDFS/DFSCoin/raw/master/dfscoin-qt-windows.zip" target="_blank"><?php echo esc_html(__('Download', 'victorious'));?></a><br/>
                &nbsp;&nbsp;&nbsp;&nbsp;<?php echo esc_html(__('Linux', 'victorious'));?> - <a href="https://github.com/NicoDFS/DFSCoin/raw/master/dfscoin-qt-linux.tar.gz" target="_blank"><?php echo esc_html(__('Download', 'victorious'));?></a><br/>
            2: <?php echo esc_html(__('Please Copy This DFSCoin Wallet Address:', 'victorious'))." <b>".get_option('victorious_dfscoin_wallet_address')."</b>";?><br/>
            3: <?php echo esc_html(__('Pay Via Your Desktop Wallet To Given Wallet Address.', 'victorious'));?><br/>
            3: <?php echo esc_html(__('Copy Transaction Id From Your Wallet to text box below.', 'victorious'));?><br/>
            4: <?php echo esc_html(__('Click On Deposit Now Button.', 'victorious'));?><br/>
            5: <?php echo esc_html(__('Now Deposit Has Been Successfully Completed.', 'victorious'));?>
            <div class="row">
                <div class="col-md-6">
                    <p>
                        <?php echo esc_html(__('Transaction Id', 'victorious'));?>:<br/>
                        <input type="text" name="transaction_id"/>
                    </p>
                </div>
            </div>
            <input id="btnAddCredits" type="submit" class="vc-button btn-green btn-size-lg btn-radius5 mt-4 btnSubmit" value="<?php echo esc_html(__('Deposit Now', 'victorious'));?>" onclick="jQuery.payment.sendCredits()" />
            <span class="waiting" style="display: none"><?php echo esc_html(__('Please wait...', 'victorious'));?></span>
        </form>
    <?php endif;?>
</div>
<div class="coinmarketcap-currency-widget" data-currency="dfscoin" data-base="USD"  style="width: 29%;float: right"></div>
<div class="clear"></div>