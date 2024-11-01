<div id="main" class="site-main site-info">
    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">
            <article class="hentry">
                <div class="vc-section p-3">
                    <div class="vc-header">
                        <div class="vc-header-left">
                            <h3 class="vc-title"><?php echo esc_html(__("My Upcoming Entries", 'victorious'));?></h3>
                        </div>
                    </div>
                    <div class="vc-table">
                        <?php if(!empty($leagues)):?>
                            <table cellspacing="0" cellpadding="0">
                                <thead>
                                    <tr>
                                        <th style="width: 6%"><?php echo esc_html(__('ID', 'victorious'))?></th>
                                        <th><?php echo esc_html(__('Date', 'victorious'))?></th>
                                        <th style="width: <?php echo get_option('victorious_no_cash') == 0 ? '23%' : '40%';?>"><?php echo esc_html(__('Name', 'victorious'))?></th>
                                        <th style="width: 12%"><?php echo esc_html(__('Type', 'victorious'))?></th>
                                        <th style="width: 7%"><?php echo esc_html(__('Entries', 'victorious'))?></th>
                                        <th style="width: 7%"><?php echo esc_html(__('Number', 'victorious'))?></th>
                                        <?php if(get_option('victorious_no_cash') == 0):?>
                                        <th style="width: 9%"><?php echo esc_html(__('Entry Fee', 'victorious'))?></th>
                                        <th style="width: 7%"><?php echo esc_html(__('Prizes', 'victorious'))?></th>
                                        <?php endif;?>
                                        <th style="width: 16%"></th>
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
                                            <td data-label="<?php echo esc_html(__('Number', 'victorious'))?>"><?php echo esc_html($league['entry_number']);?></td>
                                            <?php if(get_option('victorious_no_cash') == 0):?>
                                            <td data-label="<?php echo esc_html(__('Entry Fee', 'victorious'))?>"><?php echo esc_html($league['entry_fee'] == 0 ? __("Free", "victorious") : VIC_FormatMoney($league['entry_fee'], $balance_type['currency_code_symbol'], $balance_type['currency_position']));?></td>
                                            <td data-label="><?php echo esc_html(__('Prizes', 'victorious'))?>"><?php echo VIC_FormatMoney($league['prizes'], $balance_type['currency_code_symbol'], $balance_type['currency_position']);?></td>
                                            <?php endif;?>
                                            <td class="text-right">
                                                <?php if(isset($league['allow_draft']) && $league['is_live_draft'] && $league['allow_draft'] == 1): ?>
                                                    <input type="button" class="vc-button btn-yellow btn-size-xs btn-radius5 mb-1" value="<?php echo esc_html(__('Draft', 'victorious'))?>" onclick="window.location = '<?php echo VICTORIOUS_URL_GAME."/".$league['leagueID']."?action=3";?>'">
                                                <?php elseif(isset($league['allow_bench']) && $league['is_live_draft'] && $league['allow_bench'] == 1): ?>
                                                    <input type="button" class="vc-button btn-yellow btn-size-xs btn-radius5 mb-1" value="<?php echo esc_html(__('Manage Lineup', 'victorious'))?>" onclick="window.location = '<?php echo VICTORIOUS_URL_GAME."/".$league['leagueID']."?action=4";?>'">
                                                <?php elseif(!$league['is_live_draft']):?>
                                                    <?php if($league['can_edit']):?>
                                                        <input type="button" class="vc-button btn-yellow btn-size-xs btn-radius5 mb-1" value="<?php echo $league['gameType'] == VICTORIOUS_GAME_TYPE_NFL_PLAYOFF ? esc_html(__('Draft room', 'victorious')) : esc_html(__('Edit', 'victorious'))?>" onclick="window.location = '<?php echo VICTORIOUS_URL_SUBMIT_PICKS.$league['leagueID']."/?num=".$league['entry_number'];?>'">
                                                    <?php endif;?>
                                                    <?php if($league['can_leave']):?>
                                                        <input type="button" class="vc-button btn-yellow btn-size-xs btn-radius5 mb-1 cancelContest" value="<?php echo esc_html(__("Leave", "victorious"));?>" onclick="jQuery.global.leaveContest('<?php echo esc_attr($league['leagueID']) ?>', '<?php echo esc_attr($league['entry_number']);?>')">
                                                    <?php endif;?>
                                                <?php endif;?>
                                                <?php if($league['is_live_draft']): ?>
                                                    <input style="float:right;" type="button" class="vc-button btn-yellow btn-size-xs btn-radius5 mb-1" value="<?php echo esc_html(__('Request Trade', 'victorious'))?>" onclick="jQuery.tradeRugby.showListUsers(<?php echo esc_attr($league['leagueID']); ?>,<?php echo esc_attr($league['entry_number']); ?>)">
                                                <?php endif; ?>
                                                <?php if(!empty($league['finished_draft'])):?>
                                                    <input type="button" class="vc-button btn-yellow btn-size-xs btn-radius5 mb-1" value="<?php echo esc_html(__('View results', 'victorious'))?>" onclick="window.location = '<?php echo VICTORIOUS_URL_CONTEST."/?league_id=".$league['leagueID']."&num=".$league['entry_number'];?>'">
                                                <?php endif;?>
                                            </td>
                                        </tr>
                                    <?php endforeach;?>
                                </tbody>
                            </table>
                        <?php else:?>
                            <?php echo esc_html(__("There are no upcoming entries", 'victorious'));?>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>
<div id="resultDialog" title="" style="display: none"></div>