<div>
    <?php echo esc_html(__('Rate'));?>: <?php echo VIC_FormatMoney(1)." ".esc_html(__('deposit equals', 'victorious'));?> <?php echo get_option('victorious_cash_to_credit');?> <?php echo esc_html(__('credits'));?>
</div>
<?php if (!empty($fee_percentage)): ?>
    <div>
        <?php echo esc_html(__('Fee percentage'));?>: <?php echo get_option('victorious_fee_percentage');?>%
    </div>
<?php endif; ?>
<p>
    <?php echo esc_html(__('How many credits do you want to add', 'victorious'));?> (<?php echo sprintf(esc_html(__('minimum %s')), VIC_FormatMoney(get_option('victorious_minimum_deposit'))); ?>):<br/>
    <input type="text" name="credits" <?php if ($fee_percentage > 0): ?>onkeyup="jQuery.payment.addFundValue(this.value, '<?php echo esc_html($fee_percentage); ?>')"<?php endif; ?> /><br/>
    <?php if ($fee_percentage > 0): ?>
        <?php echo esc_html(__('Real Value'));?>: <span id="realCredits"></span>
    <?php endif; ?>
</p>
<?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW . 'Elements/addfund_coupon.php');?>