<?php VIC_GetMessage();?>
<div id="main" class="site-main site-info">
    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">
            <article class="hentry">
                <div class="vc-section p-3">
                    <div class="vc-header">
                        <div class="vc-header-left">
                            <h3 class="vc-title"><?php echo esc_html(__('Transaction History', 'victorious'));?></h3>
                        </div>
                    </div>
                    <div class="vc-table">
                        <?php if(get_option('victorious_no_cash') == 0):?>
                            <?php if(!empty($aFundHistorys)):?>
                                <table cellspacing="0" cellpadding="0">
                                    <thead>
                                    <tr>
                                        <th><?php echo esc_html(__('Date', 'victorious'));?></th>
                                        <th><?php echo esc_html(__('Operation', 'victorious'));?></th>
                                        <th><?php echo esc_html(__('Type', 'victorious'));?></th>
                                        <th><?php echo esc_html(__('Contest', 'victorious'));?></th>
                                        <th><?php echo esc_html(__('Gateway', 'victorious'));?></th>
                                        <th><?php echo esc_html(__('Status', 'victorious'));?></th>
                                        <th><?php echo esc_html(__('Transaction id', 'victorious'));?></th>
                                        <th><?php echo esc_html(__('Amount', 'victorious'));?></th>
                                        <th><?php echo esc_html(__('New balance', 'victorious'));?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($aFundHistorys as $aFundHistory):
                                        $balance_type = $aFundHistory['balance_type'];
                                        ?>
                                        <tr>
                                            <td data-label="<?php echo esc_html(__('Date', 'victorious'));?>"><?php echo esc_html($aFundHistory['date']);?></td>
                                            <td data-label="<?php echo esc_html(__('Operation', 'victorious'));?>"><?php echo esc_html($aFundHistory['operation']);?></td>
                                            <td data-label="<?php echo esc_html(__('Type', 'victorious'));?>"><?php echo esc_html($aFundHistory['type']);?></td>
                                            <td data-label="<?php echo esc_html(__('Contest', 'victorious'));?>">
                                                <?php echo esc_html($aFundHistory['name_contest']);?>
                                                <?php if($aFundHistory['leagueID'] > 0):?> (<?php echo esc_html($aFundHistory['leagueID']);?>)<?php endif;?>
                                            </td>
                                            <td data-label="<?php echo esc_html(__('Gateway', 'victorious'));?>"><?php echo !empty($aFundHistory['gateway']) ? esc_html($aFundHistory['gateway']) : '&nbsp;';?></td>
                                            <td data-label="<?php echo esc_html(__('Status', 'victorious'));?>"><?php echo !empty($aFundHistory['status']) ? esc_html($aFundHistory['status']) : '&nbsp;';?></td>
                                            <td data-label="<?php echo esc_html(__('Transaction id', 'victorious'));?>"><?php echo !empty($aFundHistory['transactionID']) ? esc_html($aFundHistory['transactionID']) : '&nbsp;';?></td>
                                            <td data-label="<?php echo esc_html(__('Amount', 'victorious'));?>"><?php echo esc_html($aFundHistory['operation_sign']).VIC_FormatMoney($aFundHistory['amount'], $balance_type['currency_code_symbol'], $balance_type['currency_position']);?></td>
                                            <td data-label="<?php echo esc_html(__('New balance', 'victorious'));?>"><?php echo VIC_FormatMoney($aFundHistory['new_balance'], $balance_type['currency_code_symbol'], $balance_type['currency_position']);?></td>
                                        </tr>
                                    <?php endforeach;?>
                                    </tbody>
                                </table>
                            <?php else:?>
                                <?php echo esc_html(__("There are no upcoming entries", 'victorious'));?>
                            <?php endif; ?>
                        <?php else:?>
                            <?php echo esc_html(__('This function is currently unavailable.', 'victorious'));?>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>