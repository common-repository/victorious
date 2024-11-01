<?php if(get_option('victorious_no_cash') == 0):?>
    <?php require_once('dlg_account_info.php');?>
    <?php require_once('dlg_request_payment.php');?>
    <?php require_once('dlg_transfer_to_account.php');?>
    <?php if($isHasCoupon):?>
        <?php require_once('dlg_coupon.php');?>
    <?php endif;?>
    <article class="hentry">
        <div class="vc-tabpane-container p-4 mb-3">
            <h1><?php echo esc_html(__('Account information', 'victorious'));?></h1>
            <div class="form-group row">
                <div class="col-sm-2"><?php echo esc_html(__('Email', 'victorious'));?></div>
                <div class="col-sm-10">
                    <?php if(isset($aUserPayment['email'])):?><?php echo esc_html($aUserPayment['email']);?><?php endif;?>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-2"><?php echo esc_html(__('Available balance', 'victorious'));?></div>
                <div class="col-sm-10">
                    <?php echo VIC_GetUserBalance();?>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-sm-2"><?php echo esc_html(__('Pending request', 'victorious'));?></div>
                <div class="col-sm-10">
                    <?php echo VIC_FormatMoney($withdrawPending > 0 ? $withdrawPending : 0);?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-10">
                    <a class="vc-button btn-green btn-size-sm btn-radius5 font-weight-normal" href="<?php echo VICTORIOUS_URL_ADD_FUNDS;?>"><?php echo esc_html(__('Add funds', 'victorious'));?></a>
                    <a class="vc-button btn-green btn-size-sm btn-radius5 font-weight-normal" href="javascript:void(0)" onclick="return jQuery.payment.requestPayment('<?php echo esc_html(__('Request payment', 'victorious'));?>')"><?php echo esc_html(__('Request payment', 'victorious'));?></a>
                    <?php if(!empty($global_setting['transfer_money_to_another_account'])):?>
                        <a class="vc-button btn-green btn-size-sm btn-radius5 font-weight-normal" href="javascript:void(0)" onclick="return jQuery.payment.transferToAccountDlg('<?php echo esc_html(__('Transfer funds to Users', 'victorious'));?>')">
                            <?php echo esc_html(__('Transfer', 'victorious'));?>
                        </a>
                    <?php endif;?>
                    <?php if($isHasCoupon):?>
                        <a class="vc-button btn-green btn-size-sm btn-radius5 font-weight-normal" href="javascript:void(0)" onclick="return jQuery.payment.showDlgCoupon('<?php echo esc_html(__("Add money", 'victorious'));?>')">
                            <?php echo esc_html(__('Add money by coupon code', 'victorious'));?>
                        </a>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </article>
<?php else:?>
    <?php echo esc_html(__('This function is currently unavailable.', 'victorious'));?>
<?php endif; ?>
