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
                            <?php if(!empty($aWithdraws)):?>
                                <table cellspacing="0" cellpadding="0">
                                    <thead>
                                    <tr>
                                        <th style="width:90px"><?php echo esc_html(__('Request date', 'victorious'));?></th>
                                        <th style="width:80px"><?php echo esc_html(__('Credits', 'victorious'));?></th>
                                        <!--<th style="width:100px"><?php echo esc_html(__('Rate', 'victorious'));?></th>
                            <th style="width:80px"><?php echo esc_html(__('Real money', 'victorious'));?></th>-->
                                        <th style="width:200px"><?php echo esc_html(__('Reason', 'victorious'));?></th>
                                        <th style="width:80px"><?php echo esc_html(__('Status', 'victorious'));?></th>
                                        <th style="width:100px"><?php echo esc_html(__('Response date', 'victorious'));?></th>
                                        <th style="width:160px"><?php echo esc_html(__('Response message', 'victorious'));?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($aWithdraws as $aWithdraw):
                                        $balance_type = $aWithdraw['balance_type'];
                                        ?>
                                        <tr>
                                            <td data-label="<?php echo esc_html(__('Request date', 'victorious'));?>"><?php echo esc_html($aWithdraw['requestDate']);?></td>
                                            <td data-label="<?php echo esc_html(__('Credits', 'victorious'));?>"><?php echo VIC_FormatMoney($aWithdraw['amount'], $balance_type['currency_code_symbol'], $balance_type['currency_position']);?></td>
                                            <!--<td data-label="<?php echo esc_html(__('Rate', 'victorious'));?>"><?php echo get_option('victorious_credit_to_cash');?> <?php echo esc_html(__('credits equals', 'victorious'))." ".VIC_FormatMoney(1, $balance_type['currency_code_symbol'], $balance_type['currency_position']);?></td>
                            <td data-label="<?php echo esc_html(__('Real money', 'victorious'));?>"><?php echo VIC_FormatMoney($aWithdraw['real_amount'], $balance_type['currency_code_symbol'], $balance_type['currency_position']);?></td-->
                                            <td data-label="<?php echo esc_html(__('Reason', 'victorious'));?>"><?php echo esc_html($aWithdraw['reason']);?></td>
                                            <td data-label="<?php echo esc_html(__('Status', 'victorious'));?>"><?php echo esc_html($aWithdraw['status']);?></td>
                                            <td data-label="<?php echo esc_html(__('Response date', 'victorious'));?>"><?php echo esc_html($aWithdraw['processedDate']);?></td>
                                            <td data-label="<?php echo esc_html(__('Response message', 'victorious'));?>"><?php echo esc_html($aWithdraw['response_message']);?></td>
                                        </tr>
                                    <?php endforeach;?>
                                    </tbody>
                                </table>
                            <?php else:?>
                                <?php echo esc_html(__("There are no withdrawal histories", 'victorious'));?>
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