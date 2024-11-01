<div id="main" class="site-main site-info">
    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">
            <article class="hentry">
                <div class="vc-section p-3">
                    <div class="vc-header">
                        <div class="vc-header-left">
                            <h3 class="vc-title"><?php echo esc_html(__("My History Entries", 'victorious'));?></h3>
                        </div>
                    </div>
                    <div class="vc-table">
                        <?php if(!empty($leagues)):?>
                            <table cellspacing="0" cellpadding="0">
                                <thead>
                                    <tr>
                                        <th style="width: 6%"><?php echo esc_html(__('ID', 'victorious'))?></th>
                                        <th style="width: 15%"><?php echo esc_html(__('Date', 'victorious'))?></th>
                                        <th style="width: <?php echo get_option('victorious_no_cash') == 0 ? '18%' : '43%';?>"><?php echo esc_html(__('Name', 'victorious'))?></th>
                                        <th style="width: 12%"><?php echo esc_html(__('Type', 'victorious'))?></th>
                                        <th style="width: 10%"><?php echo esc_html(__('Entries', 'victorious'))?></th>
                                        <?php if(get_option('victorious_no_cash') == 0):?>
                                            <th style="width: 10%"><?php echo esc_html(__('Entry Fee', 'victorious'))?></th>
                                            <th style="width: 7%"><?php echo esc_html(__('Prizes', 'victorious'))?></th>
                                        <?php endif;?>
                                        <th style="width: 6%"><?php echo esc_html(__('Rank', 'victorious'))?></th>
                                        <?php if(get_option('victorious_no_cash') == 0):?>
                                            <th style="width: 8%"><?php echo esc_html(__('Winnings', 'victorious'))?></th>
                                        <?php endif;?>
                                        <th style="width: 8%">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($leagues as $league):
                                        $balance_type = $league['balance_type'];
                                    ?>
                                        <tr>
                                            <td data-label="<?php echo esc_html(__('ID', 'victorious'))?>"><?php echo esc_html($league['leagueID']);?></td>
                                            <td data-label="<?php echo esc_html(__('Date', 'victorious'))?>"><?php echo VIC_DateTranslate($league['startDate']);?></td>
                                            <td data-label="<?php echo esc_html(__('Name', 'victorious'))?>"><?php echo esc_html($league['name']);?></td>
                                            <td data-label="<?php echo esc_html(__('Type', 'victorious'))?>"><?php echo VIC_ParseGameTypeName($league['gameType']);?></td>
                                            <td data-label="<?php echo esc_html(__('Entries', 'victorious'))?>"><?php echo esc_html($league['entries']);?> / <?php echo esc_html($league['size']);?></td>
                                            <?php if(get_option('victorious_no_cash') == 0):?>
                                                <td data-label="<?php echo esc_html(__('Entry Fee', 'victorious'))?>"><?php echo esc_html($league['entry_fee'] == 0 ? __("Free", "victorious") : VIC_FormatMoney($league['entry_fee'], $balance_type['currency_code_symbol'], $balance_type['currency_position']));?></td>
                                                <td data-label="<?php echo esc_html(__('Prizes', 'victorious'))?>"><?php echo VIC_FormatMoney($league['prizes'], $balance_type['currency_code_symbol'], $balance_type['currency_position']);?></td>
                                            <?php endif;?>
                                            <td data-label="<?php echo esc_html(__('Rank', 'victorious'))?>"><?php echo esc_html($league['rank']);?></td>
                                            <?php if(get_option('victorious_no_cash') == 0):?>
                                                <td data-label="<?php echo esc_html(__('Winnings', 'victorious'))?>"><?php echo VIC_FormatMoney($league['winnings'], $balance_type['currency_code_symbol'], $balance_type['currency_position']);?></td>
                                            <?php endif;?>
                                            <td class="text-right">
                                                <input type="button" class="vc-button btn-yellow btn-size-xs btn-radius5 mb-1" value="<?php echo esc_html(__('View', 'victorious'))?>" onclick="window.location = '<?php echo VICTORIOUS_URL_CONTEST.$league['leagueID']."/?num=".$league['entry_number'];?>'">
                                            </td>
                                        </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        <?php else:?>
                            <?php echo esc_html(__("There are no history entries", 'victorious'));?>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>