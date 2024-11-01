<?php VIC_GetMessage(); ?>
<article class="hentry">
    <div class="vc-tabpane-container p-4 mb-3">
        <?php if (get_option('victorious_no_cash') == 0): ?>
            <?php if ($canplay): ?>
                <?php if (empty($_GET['type']) && (count($payout_gateways) > 1 || $balance_types != null)): ?>
                    <?php if($gatewayList != null || $balance_types != null):?>
                        <div class="vc-header">
                            <div class="vc-header-left">
                                <h3 class="vc-title"><?php echo esc_html(__('Choose below payment gateway to make a deposit', 'victorious')) ?></h3>
                            </div>
                        </div>
                        <div class="vc-table">
                            <div class="payment-gateway-list">
                                <?php foreach($gatewayList as $gateway_value => $gateway):?>
                                <div class="payment-gateway-item">
                                    <div class="payment-gateway-wrap">
                                        <div class="payment-gateway-item-icon">
                                            <img src="<?php echo esc_url(VICTORIOUS__PLUGIN_URL_IMAGE.$gateway['icon'])?>" alt="">
                                        </div>
                                        <label class="radio-control">
                                            <?php echo esc_html($gateway['name']);?>
                                            <input type="radio" name="gametype" value="<?php echo esc_url(VICTORIOUS_URL_ADD_FUNDS . '?type='.$gateway_value) ?>" />
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach;?>
                                <?php if(!empty($global_setting['allow_multiple_balances']) && $balance_types != null):?>
                                    <?php foreach($balance_types as $balance_type):?>
                                        <div class="payment-gateway-item">
                                            <div class="payment-gateway-wrap">
                                                <div class="payment-gateway-item-icon">
                                                    <img src="<?php echo esc_url($balance_type['image_url'])?>" alt="">
                                                </div>
                                                <label class="radio-control">
                                                    <?php echo esc_html($balance_type['name']);?>
                                                    <input type="radio" name="gametype" data-balance="1" data-name="<?php echo esc_html($balance_type['name']);?>" data-info="<?php echo esc_html($balance_type['info'])?>" />
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach;?>
                                <?php endif;?>
                            </div>
                            <a href="javascript:void(0);" class="vc-button btn-green btn-size-lg btn-radius5 mt-4 btnSubmit"><?php echo esc_html(__('Continue', 'victorious')); ?></a>
                        </div>
                        <script type="text/javascript">
                            jQuery(document).ready(function () {
                                jQuery('.btnSubmit').click(function () {
                                    var link = jQuery('input[name=gametype]:checked').val();
                                    var is_balance = jQuery('input[name=gametype]:checked').data('balance');
                                    if(typeof link == 'undefined')
                                    {
                                        alert('<?php echo esc_html(__('Please select a payment gateway!', 'victorious'));?>');
                                    }
                                    else if(is_balance == 1){
                                        jQuery( "#dlgBalanceType" ).dialog({
                                            resizable: false,
                                            height: "auto",
                                            width: 400,
                                            modal: true,
                                            title: jQuery('input[name=gametype]:checked').data('name'),
                                            open:function(){
                                                jQuery( "#dlgBalanceType" ).html(jQuery('input[name=gametype]:checked').data('info'));
                                            },
                                            buttons: {
                                                "Ok": function() {
                                                    jQuery( this ).dialog( "close" );
                                                }
                                            }
                                        });
                                    }
                                    else
                                    {
                                        window.location = link;
                                    }
                                });
                            });
                        </script>
                    <?php else: ?>
                        <?php echo esc_html(__('There is no available gateway, please contact admin.', 'victorious')) ?>
                    <?php endif; ?>
                <?php endif; ?>
                <?php
                switch ($gateway_type)
                {
                    case VICTORIOUS_GATEWAY_PAYPAL:
                        include VICTORIOUS__PLUGIN_DIR_VIEW . 'Elements/addfund_paypal.php';
                        break;
                    case VICTORIOUS_GATEWAY_DFSCOIN:
                        include VICTORIOUS__PLUGIN_DIR_VIEW . 'Elements/addfund_dfscoin.php';
                        break;
                    case VICTORIOUS_GATEWAY_PAYSIMPLE:
                        include VICTORIOUS__PLUGIN_DIR_VIEW . 'Elements/addfund_paysimple.php';
                        break;
                }
                ?>
            <?php else: ?>
                <?php echo esc_html(__("Due to your location you cannot play in paid games so that they cannot add funds", 'victorious')); ?>
            <?php endif; ?>
        <?php else: ?>
            <?php echo esc_html(__('This function is currently unavailable.')); ?>
        <?php endif; ?>
    </div>
</article>
<div id="dlgBalanceType" title="" style="display: none;">