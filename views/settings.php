<?php
    $victorious = new VIC_Victorious();
    $global_setting = $victorious->getGlobalSetting();
    if($global_setting['multiple_currency_support'])
    {
        $victorious->updateCoinExchangeRate();
    }
    else
    {
        update_option( 'victorious_global_currency_enable', 0);
    }
    $currencies = VIC_Currency();
?>
<script>
  jQuery( function() {
      jQuery( "#tabs").tabs();
      jQuery(".subtabs").tabs();
  } );
</script>
<div class="wrap vc-wrap-settings">
    <?php include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";?>
    <div class="vc-header bg-transparent">
        <div class="vc-header-left">
            <!-- <div class="logo-admin">
                <img src="/custom_layout/assets/images/logo.png" alt="">
            </div> -->
            <h3 class="vc-title-admin"><?php echo esc_html(__('Victorious Dashboard', 'victorious'));?></h3>
            <p class="vc-des mb-0"><?php echo esc_html(__('Ip', 'victorious')).': '.VIC_IpAddress();?> | <?php echo esc_html(__('Token', 'victorious')).': '.get_option('victorious_api_token');?></p>
        </div>
        <div class="vc-header-right">
            <a href="#" class="color-blue">
                Victorious shop
            </a>
            |
            <a href="#" class="color-blue">
                Support portal
            </a>
        </div>
    </div>
    <form method="post" action="options.php" id="tabs">
        <?php settings_fields( 'victorious-settings-group' ); ?>
        <?php do_settings_sections( 'victorious-settings-group' ); ?>
        <div class="vc-admin-tabs">
            <h3 class="vc-title-admin vc-title-admin-tab"><?php echo esc_html(__('Victorious Settings', 'victorious'));?></h3>
            <ul class="nav nav-tabs">
                <li class="nav-item"><a class="nav-link" href="#tabs-1"><?php echo esc_html(__('General', 'victorious'));?></a></li>
                <li class="nav-item"><a class="nav-link" href="#tabs-2"><?php echo esc_html(__('Contest', 'victorious'));?></a></li>
                <li class="nav-item"><a class="nav-link" href="#tabs-3"><?php echo esc_html(__('Gateway', 'victorious'));?></a></li>
                <li class="nav-item"><a class="nav-link" href="#tabs-4"><?php echo esc_html(__('Currency', 'victorious'));?></a></li>
                <li class="nav-item"><a class="nav-link" href="#tabs-5"><?php echo esc_html(__('Social', 'victorious'));?></a></li>
                <?php if($global_setting['allow_push_notification']):?>
                <li class="nav-item"><a class="nav-link" href="#tabs-6"><?php echo esc_html(__('Google Firebase', 'victorious'));?></a></li>
                <?php endif;?>
                <li class="nav-item"><a class="nav-link" href="#tabs-7"><?php echo esc_html(__('Bracket points', 'victorious'));?></a></li>
                <li class="nav-item"><a class="nav-link" href="#tabs-8"><?php echo esc_html(__('Email', 'victorious'));?></a></li>
            </ul>
        </div>

        <div id="tabs-1">
            <h5 class="vc-tabpane-title"><?php echo esc_html(__('General', 'victorious'));?></h5>
            <div class="vc-tabpane-container">
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Api Token', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_api_token" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_api_token'));?>" />
                        <p class="description"><?php echo esc_html(__('This is your unique license key for your plugin. You must get this by logging into victorious.club. Then copy and paste your key here.', 'victorious'));?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Api Url', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_api_url" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_api_url'));?>" />
                        <p class="description"><?php echo esc_html(__("This is the URL that your plugin will connect to in order to set and get data. This should not be modified unless you are instructed to by Victorious's technical staff.", 'victorious'));?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Api Admin Url', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_api_url_admin" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_api_url_admin'));?>" />
                        <p class="description"><?php echo esc_html(__("This is the URL that your plugin will connect to in order to set and get admin data. This should not be modified unless you are instructed to by Victorious's technical staff.", 'victorious'));?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                    <?php echo esc_html(__('Timezone', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <select id="victorious_timezone" name="victorious_timezone" class="form-control">
                            <option value="">
                                <?php echo esc_html(__('None', 'victorious'));?>
                            </option>
                            <?php echo wp_timezone_choice(get_option('victorious_timezone')); ?>
                        </select>
                        <p class="description"><?php echo esc_html(__("Change timezone in case you have a problem with date time.", 'victorious'));?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                    <?php echo esc_html(__('Date format', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <select id="victorious_timezone" name="victorious_date_format" class="form-control">
                            <option value="">
                                <?php echo esc_html(__('Default', 'victorious'))." ".VIC_DateTranslate(__('Ex: Mon Jan 22', 'victorious'), false);?>
                            </option>
                            <option value="D d M" <?php if(get_option("victorious_date_format") == "D d M"):?>selected="true"<?php endif;?>>
                                M D d <?php echo VIC_DateTranslate(__('Ex: Mon 22 Jan', 'victorious'), false);?>
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div id="tabs-2">
        <h5 class="vc-tabpane-title"><?php echo esc_html(__('Contest', 'victorious'));?></h5>
            <div class="vc-tabpane-container">
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Entry Fee', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <div class="array-holder">
                            <?php $aEntrys = get_option('victorious_entry_fee');?>
                            <?php
                                if($aEntrys != null):
                                foreach($aEntrys as $aEntry):
                            ?>
                            <div class="array-item">
                                <input type="text" name="victorious_entry_fee[]" class="regular-text ltr entry_fee" value="<?php echo esc_html($aEntry);?>" />
                                <a href="#" onclick="return removeArray(this)"><?php echo esc_html(__('Remove', 'victorious'));?></a>
                            </div>
                            <?php
                                endforeach;
                                else:;
                            ?>
                            <div class="array-item">
                                <input type="text" name="victorious_entry_fee[]" class="regular-text ltr entry_fee"/>
                                <a href="#" onclick="return removeArray(this)"><?php echo esc_html(__('Remove', 'victorious'));?></a>
                            </div>
                            <?php endif;?>
                            <input type="button" data-name="victorious_entry_fee[]" value="<?php echo esc_html(__('Add', 'victorious'));?>" class="vc-button btn-blue-outlined btn-size-xs btn-radius5" style="margin-top: 5px" onclick="return addArray(this)" >
                            <p class="description"><?php echo esc_html(__('These are the values that will appear in the Entry Fee drop down menu when creating a new contest.', 'victorious'));?></p>
                        </div>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('League Size', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <div class="array-holder">
                            <?php $aSizes = get_option('victorious_league_size');?>
                            <?php
                                if($aSizes != null):
                                foreach($aSizes as $aSize):
                            ?>
                            <div class="array-item">
                                <input type="text" name="victorious_league_size[]" class="regular-text ltr entry_fee" value="<?php echo esc_html($aSize);?>" />
                                <a href="#" onclick="return removeArray(this)"><?php echo esc_html(__('Remove', 'victorious'));?></a>
                            </div>
                            <?php
                                endforeach;
                                else:;
                            ?>
                            <div class="array-item">
                                <input type="text" name="victorious_league_size[]" class="regular-text ltr entry_fee"/>
                                <a href="#" onclick="return removeArray(this)"><?php echo esc_html(__('Remove', 'victorious'));?></a>
                            </div>
                            <?php endif;?>
                            <input type="button" data-name="victorious_league_size[]" value="<?php echo esc_html(__('Add', 'victorious'));?>" class="vc-button btn-blue-outlined btn-size-xs btn-radius5" style="margin-top: 5px" onclick="return addArray(this)" >
                            <p class="description"><?php echo esc_html(__('This are the values that will appear in the League Size drop down menu when creating a new contest.', 'victorious'));?></p>
                        </div>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Payout Percentage', 'victorious'));?>(%)
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_winner_percent" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_winner_percent'));?>" />
                        <p class="description"><?php echo esc_html(__('This is the percentage that is paid out to winners. For example if the payout is 90%, then this means the site would have a rake of 10% per contest. 10% Would be the profit the site makes on a contest.', 'victorious'));?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('First Place Payout Percentage', 'victorious'));?>(%)
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_first_place_percent" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_first_place_percent'));?>" />
                        <p class="description"><?php echo esc_html(__('This is the percentage that is paid out to the first place winner in a top 3 get paid contest.', 'victorious'));?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Second Place Payout Percentage', 'victorious'));?>(%)
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_second_place_percent" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_second_place_percent'));?>" />
                        <p class="description"><?php echo esc_html(__('This is the percentage that is paid out to the second place winner in a top 3 get paid contest.', 'victorious'));?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Third Place Payout Percentage', 'victorious'));?>(%)
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_third_place_percent" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_third_place_percent'));?>" />
                        <p class="description"><?php echo esc_html(__('This is the percentage that is paid out to the third place winner in a top 3 get paid contest.', 'victorious'));?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Processing fee percentage', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_fee_percentage" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_fee_percentage'));?>" /> %
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Credit Exchange Rate (Deposit)', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_cash_to_credit" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_cash_to_credit'));?>" />
                        <p class="description"><?php echo esc_html(__('In addition to making money from a rake per contest. You can also set a rate for when user purchase credits. For example, $1 USD can buy 10 Credits. But to withdraw funds, it might cost 11 Credits to cash out $1 USD. In this field enter how many credits a user will receive for $1. It is not uncommon to say 1. So $1 buys 1 credit.', 'victorious'));?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Credit Exchange Rate (Withdraw)', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_credit_to_cash" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_credit_to_cash'));?>" />
                        <p class="description"><?php echo esc_html(__('In this field enter how many credits a user needs to withdraw to receive $1.', 'victorious'));?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Allow Create Contest', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <select class="postform form-control" name="victorious_create_contest">
                            <option <?php echo get_option('victorious_create_contest') == 1 ? 'selected="true"' : '';?> value="1"><?php echo esc_html(__('True', 'victorious'));?></option>
                            <option <?php echo get_option('victorious_create_contest') == 0 ? 'selected="true"' : '';?> value="0"><?php echo esc_html(__('False', 'victorious'));?></option>
                        </select>
                        <p class="description"><?php echo esc_html(__('Allow user create new contest at frontend.', 'victorious'));?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('No cash', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <select class="postform form-control" name="victorious_no_cash">
                            <option <?php echo get_option('victorious_no_cash') == 0 ? 'selected="true"' : '';?> value="0"><?php echo esc_html(__('False', 'victorious'));?></option>
                            <option <?php echo get_option('victorious_no_cash') == 1 ? 'selected="true"' : '';?> value="1"><?php echo esc_html(__('True', 'victorious'));?></option>
                        </select>
                        <p class="description"><?php echo esc_html(__('This will hide ALL references to Entry Fee and Prizes.  This is mainly used for site who are NOT offering CASH games.', 'victorious'));?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('No invite user list', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <select class="postform form-control" name="victorious_no_invite_user_list">
                            <option <?php echo get_option('victorious_no_invite_user_list') == 0 ? 'selected="true"' : '';?> value="0"><?php echo esc_html(__('False', 'victorious'));?></option>
                            <option <?php echo get_option('victorious_no_invite_user_list') == 1 ? 'selected="true"' : '';?> value="1"><?php echo esc_html(__('True', 'victorious'));?></option>
                        </select>
                        <p class="description"><?php echo esc_html(__('This will hide user list when invite user to a contest.', 'victorious'));?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Minimum Deposit', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_minimum_deposit" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_minimum_deposit'));?>" />
                        <p class="description"><?php echo esc_html(__('Minimum Deposit Value', 'victorious'));?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Show import picks', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="checkbox"  name="victorious_show_import_pick" <?php checked( 1, get_option( 'victorious_show_import_pick' ) ); ?>  value="1"/>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Recieve email when players join a contest', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="checkbox"  name="victorious_get_email_from_better_join_contest" <?php checked( 1, get_option( 'victorious_get_email_from_better_join_contest' ) ); ?>  value="1"/>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Allow share teams and players', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="checkbox"  name="victorious_share_teams_players" <?php checked( 1, get_option( 'victorious_share_teams_players' ) ); ?>  value="1"/>
                        <p class="description"><?php echo esc_html(__('Note: This function only works if facebook app id is entered.', 'victorious'));?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('B icon', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <select class="postform form-control" name="victorious_b_icon">
                            <option <?php echo get_option('victorious_b_icon') == 1 ? 'selected="true"' : '';?> value="1"><?php echo esc_html(__('True', 'victorious'));?></option>
                            <option <?php echo get_option('victorious_b_icon') == 0 ? 'selected="true"' : '';?> value="0"><?php echo esc_html(__('False', 'victorious'));?></option>
                        </select>
                        <p class="description"><?php echo esc_html(__('Show B icon next to user name that have entered less that 50 contests.', 'victorious'));?></p>
                    </div>
                </div>
                <!--<div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php //echo esc_html(__('Send weekly statistic to', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" placeholder="sample@abc.com" class="regular-text ltr form-control" name="victorious_weekly_statistic_email"  value="<?php echo get_option( 'victorious_weekly_statistic_email' );?>"/>
                    </div>
                </div>-->
            </div>
        </div>
        <div id="tabs-3" class="subtabs">
        <h5 class="vc-tabpane-title"><?php echo esc_html(__('Gateway', 'victorious'));?></h5>
            <div class="vc-tabpane-container">
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Deposit Gateway', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <?php
                            $gateway_type = get_option('victorious_payout_gateway');
                            $gateway_type = !empty($gateway_type) ? $gateway_type : array();
                        ?>
                        <select class="postform form-control" name="victorious_payout_gateway[]" multiple="">
                            <option <?php echo in_array(VICTORIOUS_GATEWAY_PAYPAL, $gateway_type) ? 'selected="true"' : '';?> value="<?php echo VICTORIOUS_GATEWAY_PAYPAL;?>"><?php echo esc_html(__('Paypal', 'victorious'));?></option>
                            <option <?php echo in_array(VICTORIOUS_GATEWAY_PAYSIMPLE, $gateway_type) ? 'selected="true"' : '';?> value="<?php echo VICTORIOUS_GATEWAY_PAYSIMPLE;?>"><?php echo esc_html(__('Pay Simple', 'victorious'));?></option>
                        </select>
                        <p class="description"><?php echo esc_html(__('Select gateway to payout.', 'victorious'));?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Withdrawal Gateway', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <?php
                            $gateway_payout = get_option('victorious_payout_method');
                            $gateway_payout = !empty($gateway_payout) ? $gateway_payout : array();
                        ?>
                        <select class="postform form-control" name="victorious_payout_method[]" multiple="">
                            <option <?php echo in_array(VICTORIOUS_GATEWAY_PAYPAL, $gateway_payout) ? 'selected="true"' : '';?> value="<?php echo VICTORIOUS_GATEWAY_PAYPAL;?>"><?php echo esc_html(__('Paypal', 'victorious'));?></option>
                            <option <?php echo in_array(VICTORIOUS_GATEWAY_PAYSIMPLE, $gateway_payout) ? 'selected="true"' : '';?> value="<?php echo VICTORIOUS_GATEWAY_PAYSIMPLE;?>"><?php echo esc_html(__('Pay Simple', 'victorious'));?></option>
                        </select>
                        <p class="description"><?php echo esc_html(__('Select method to withdraw.', 'victorious'));?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Gateway settings', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <ul class="list-payment">
                            <li class="list-payment-item"><a href="#tabs-31"><?php echo esc_html(__('Paypal', 'victorious'));?></a></li>
                            <li class="list-payment-item"><a href="#tabs-32"><?php echo esc_html(__('Paypal Pro', 'victorious'));?></a></li>
                            <li class="list-payment-item"><a href="#tabs-33"><?php echo esc_html(__('Paypal App', 'victorious'));?></a></li>
                            <li class="list-payment-item"><a href="#tabs-35"><?php echo esc_html(__('PaySimple', 'victorious'));?></a></li>
                        </ul>
                    </div>
                </div>
                <div id="tabs-31">
                    <div class="vc-tabpane-item">
                        <div class="vc-tabpane-item-l">
                            <?php echo esc_html(__('Paypal Type', 'victorious'));?>
                        </div>
                        <div class="vc-tabpane-item-r">
                            <?php
                                $paypal_type = get_option('victorious_paypal_type');
                                $paypal_type = !empty($paypal_type) ? $paypal_type : array();
                            ?>
                            <select class="postform form-control" name="victorious_paypal_type[]" multiple="">
                                <option <?php echo in_array(VICTORIOUS_PAYPAL_TYPE_NORMAL, $paypal_type) ? 'selected="true"' : '';?> value="<?php echo VICTORIOUS_PAYPAL_TYPE_NORMAL;?>"><?php echo esc_html(__('Normal', 'victorious'));?></option>
                                <option <?php echo in_array(VICTORIOUS_PAYPAL_TYPE_PRO, $paypal_type) ? 'selected="true"' : '';?> value="<?php echo VICTORIOUS_PAYPAL_TYPE_PRO;?>"><?php echo esc_html(__('Pro', 'victorious'));?></option>
                            </select>
                            <p class="description"><?php echo esc_html(__('Select paypal method to payout', 'victorious'));?></p>
                        </div>
                    </div>
                    <div class="vc-tabpane-item">
                        <div class="vc-tabpane-item-l">
                            <?php echo esc_html(__('Paypal Sandbox', 'victorious'));?>
                        </div>
                        <div class="vc-tabpane-item-r">
                            <select class="postform form-control" name="paypal_test">
                                <option <?php echo get_option('paypal_test') == 1 ? 'selected="true"' : '';?> value="1"><?php echo esc_html(__('True', 'victorious'));?></option>
                                <option <?php echo get_option('paypal_test') == 0 ? 'selected="true"' : '';?> value="0"><?php echo esc_html(__('False', 'victorious'));?></option>
                            </select>
                            <p class="description"><?php echo esc_html(__('If value is True, Paypal will change to testing mode.', 'victorious'));?></p>
                        </div>
                    </div>
                    <div class="vc-tabpane-item">
                        <div class="vc-tabpane-item-l">
                            <?php echo esc_html(__('Paypal Email', 'victorious'));?>
                        </div>
                        <div class="vc-tabpane-item-r">
                            <input type="text" name="paypal_email_account" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('paypal_email_account'));?>" />
                            <p class="description"><?php echo esc_html(__('The email that represents your PayPal account.', 'victorious'));?></p>
                        </div>
                    </div>
                </div>
                <div id="tabs-32">
                    <div class="vc-tabpane-item">
                        <div class="vc-tabpane-item-l">
                            <?php echo esc_html(__('Paypal Pro Username', 'victorious'));?>
                        </div>
                        <div class="vc-tabpane-item-r">
                            <input type="text" name="victorious_paypal_pro_username" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_paypal_pro_username'));?>" />
                            <p class="description"><?php echo esc_html(__('The Username used for PayPal Pro account.', 'victorious'));?></p>
                        </div>
                    </div>
                    <div class="vc-tabpane-item">
                        <div class="vc-tabpane-item-l">
                            <?php echo esc_html(__('Paypal Pro Password', 'victorious'));?>
                        </div>
                        <div class="vc-tabpane-item-r">
                            <input type="text" name="victorious_paypal_pro_password" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_paypal_pro_password'));?>" />
                            <p class="description"><?php echo esc_html(__('The Password used for PayPal Pro account.', 'victorious'));?></p>
                        </div>
                    </div>
                    <div class="vc-tabpane-item">
                        <div class="vc-tabpane-item-l">
                            <?php echo esc_html(__('Paypal Pro Signature', 'victorious'));?>
                        </div>
                        <div class="vc-tabpane-item-r">
                            <input type="text" name="victorious_paypal_pro_signature" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_paypal_pro_signature'));?>" />
                            <p class="description"><?php echo esc_html(__('The Signature used for PayPal Pro account.', 'victorious'));?></p>
                        </div>
                    </div>
                </div>
                <div id="tabs-33">
                    <div class="vc-tabpane-item">
                        <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Paypal Client Id', 'victorious'));?>
                        </div>
                        <div class="vc-tabpane-item-r">
                            <input type="text" name="victorious_paypal_client_id" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_paypal_client_id'));?>" />
                            <p class="description"><?php echo esc_html(__('This is used for PayPal App.', 'victorious'));?></p>
                        </div>
                    </div>
                </div>
                <div id="tabs-35">
                    <div class="vc-tabpane-item">
                        <div class="vc-tabpane-item-l">
                            <?php echo esc_html(__('Sandbox', 'victorious'));?>
                        </div>
                        <div class="vc-tabpane-item-r">
                            <select class="postform form-control" name="victorious_paysimple_test">
                                <option <?php echo get_option('victorious_paysimple_test') == 1 ? 'selected="true"' : '';?> value="1"><?php echo esc_html(__('True', 'victorious'));?></option>
                                <option <?php echo get_option('victorious_paysimple_test') == 0 ? 'selected="true"' : '';?> value="0"><?php echo esc_html(__('False', 'victorious'));?></option>
                            </select>
                            <p class="description"><?php echo esc_html(__('If value is True, PaySimple will change to testing mode.', 'victorious'));?></p>
                        </div>
                    </div>
                    <div class="vc-tabpane-item">
                        <div class="vc-tabpane-item-l">
                            <?php echo esc_html(__('User name', 'victorious'));?>
                        </div>
                        <div class="vc-tabpane-item-r">
                            <input type="text" name="victorious_paysimple_username" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_paysimple_username'));?>" />
                        </div>
                    </div>
                    <div class="vc-tabpane-item">
                        <div class="vc-tabpane-item-l">
                            <?php echo esc_html(__('Api key', 'victorious'));?>
                        </div>
                        <div class="vc-tabpane-item-r">
                            <input type="text" name="victorious_paysimple_api_key" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_paysimple_api_key'));?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="tabs-4">
        <h5 class="vc-tabpane-title"><?php echo esc_html(__('Currency', 'victorious'));?></h5>
            <div class="vc-tabpane-container">
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Currency', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <select class="postform form-control" name="victorious_currency">
                            <?php foreach($currencies as $key => $currency):?>
                            <option <?php echo get_option('victorious_currency') == $key || (get_option('victorious_currency') == "" && $key == "USD|$") ? 'selected="true"' : '';?> value="<?php echo esc_html($key);?>">
                                <?php echo esc_html($currency);?>
                            </option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Currency position', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <select class="postform form-control" name="victorious_currency_position">
                            <option <?php echo get_option('victorious_currency_position') == "before" ? 'selected="true"' : '';?> value="before"><?php echo esc_html(__('Before value', 'victorious'));?></option>
                            <option <?php echo get_option('victorious_currency_position') == "after" ? 'selected="true"' : '';?> value="after"><?php echo esc_html(__('After value', 'victorious'));?></option>
                        </select>
                    </div>
                </div>
                <?php if($global_setting['multiple_currency_support']):?>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Use global currency', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="checkbox" id="global_currency_enable" name="victorious_global_currency_enable" <?php checked( 1, get_option( 'victorious_global_currency_enable' ) ); ?>  value="1" onclick="enableGlobalCurrency()"/>
                    </div>
                </div>
                <div class="vc-tabpane-item global_currency" <?php if(get_option( 'victorious_global_currency_enable' ) == 0):?>style="display: none"<?php endif;?>>
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Global currency name', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_global_currency_name" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_global_currency_name'));?>" />
                    </div>
                </div>
                <div class="vc-tabpane-item global_currency" <?php if(get_option( 'victorious_global_currency_enable' ) == 0):?>style="display: none"<?php endif;?>>
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Global currency symbol', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_global_currency_symbol" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_global_currency_symbol'));?>" />
                    </div>
                </div>
                <?php endif;?>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Multiple balances', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="checkbox" id="multiple_balances" name="victorious_multiple_balances" <?php checked( 1, get_option( 'victorious_multiple_balances' ) ); ?>  value="1" onclick="enableMultipleBalances()"/>
                        <p class="description"><?php echo esc_html(__('If this feature is disabled or no balance type is selected, default balance type "USD" will be used.', 'victorious'));?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item multiple_balances_items" <?php if(get_option( 'victorious_multiple_balances' ) == 0):?>style="display: none"<?php endif;?>>
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Balance types', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <?php
                            $gateway_type = get_option('victorious_multiple_balances_type');
                            $gateway_type = !empty($gateway_type) ? $gateway_type : array();
                        ?>
                        <select class="postform form-control" name="victorious_multiple_balances_type[]" multiple="" style="width: 150px;height: 200px;">
                            <option <?php echo in_array(VICTORIOUS_BALANCE_TYPE_DEFAULT, $gateway_type) ? 'selected="true"' : '';?> value="<?php echo VICTORIOUS_BALANCE_TYPE_DEFAULT;?>"><?php echo esc_html(__('Default', 'victorious'));?></option>
                        </select>
                        <p class="description">
                            <?php echo esc_html(__('You must also enable gateway to get it worked', 'victorious'));?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div id="tabs-5">
            <h5 class="vc-tabpane-title"><?php echo esc_html(__('Social', 'victorious'));?></h5>
            <div class="vc-tabpane-container">
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Facebook app id', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_facebook_app_id" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_facebook_app_id'));?>" />
                        <p class="description"><?php echo sprintf(esc_html(__('Enter your facebook app id or register a new app id %s. Remember add you site url in your app by going to setting menu, then find a box named website.', 'victorious')), '<a href="https://developers.facebook.com" target="_blank">here</a>');?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php if($global_setting['allow_push_notification']):?>
        <div id="tabs-6">
            <h5 class="vc-tabpane-title"><?php echo esc_html(__('General', 'victorious'));?></h5>
            <div class="vc-tabpane-container">
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Api key', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_firebase_apikey" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_firebase_apikey'));?>" />
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Sender id', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_firebase_senderid" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_firebase_senderid'));?>" />
                    </div>
                </div>

            </div>
        </div>
        <?php endif;?>
        <div id="tabs-7">
        <h5 class="vc-tabpane-title"><?php echo esc_html(__('Bracket points', 'victorious'));?></h5>
            <div class="vc-tabpane-container">
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Winner of group', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_bracket_point_group_winner" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_bracket_point_group_winner'));?>" />
                        <p class="description"><?php echo sprintf(esc_html(__('Default %s points.', 'victorious')), '10');?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Runner-up of group', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_bracket_point_group_runnerup" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_bracket_point_group_runnerup'));?>" />
                        <p class="description"><?php echo sprintf(esc_html(__('Default %s points.', 'victorious')), '5');?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('1/16', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_bracket_point_16" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_bracket_point_16'));?>" />
                        <p class="description"><?php echo sprintf(esc_html(__('Default %s points.', 'victorious')), '20');?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('1/8', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_bracket_point_8" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_bracket_point_8'));?>" />
                        <p class="description"><?php echo sprintf(esc_html(__('Default %s points.', 'victorious')), '25');?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('1/4', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_bracket_point_4" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_bracket_point_4'));?>" />
                        <p class="description"><?php echo sprintf(esc_html(__('Default %s points.', 'victorious')), '30');?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Champion', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_bracket_point_first" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_bracket_point_first'));?>" />
                        <p class="description"><?php echo sprintf(esc_html(__('Default %s points.', 'victorious')), '50');?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Second', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_bracket_point_second" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_bracket_point_second'));?>" />
                        <p class="description"><?php echo sprintf(esc_html(__('Default %s points.', 'victorious')), '45');?></p>
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('Third', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_bracket_point_third" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_bracket_point_third'));?>" />
                        <p class="description"><?php echo sprintf(esc_html(__('Default %s points.', 'victorious')), '40');?></p>
                    </div>
                </div>
            </div>
        </div>
        <div id="tabs-8">
        <h5 class="vc-tabpane-title"><?php echo esc_html(__('Email', 'victorious'));?></h5>
            <div class="vc-tabpane-container">
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('From name', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_email_from_name" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_email_from_name'));?>" />
                    </div>
                </div>
                <div class="vc-tabpane-item">
                    <div class="vc-tabpane-item-l">
                        <?php echo esc_html(__('From email', 'victorious'));?>
                    </div>
                    <div class="vc-tabpane-item-r">
                        <input type="text" name="victorious_email_from_email" class="regular-text ltr form-control" value="<?php echo esc_attr(get_option('victorious_email_from_email'));?>" />
                    </div>
                </div>
            </div>
        </div>
        <?php submit_button(); ?>
    </form>
</div>

<script>
    function enableGlobalCurrency(){
        if(jQuery('#global_currency_enable').is(':checked')){
            jQuery('.global_currency').show();
        }
        else{
            jQuery('.global_currency').hide();
        }
    }    
    
    function enableMultipleBalances(){
        if(jQuery('#multiple_balances').is(':checked')){
            jQuery('.multiple_balances_items').show();
        }
        else{
            jQuery('.multiple_balances_items').hide();
        }
    }
</script>