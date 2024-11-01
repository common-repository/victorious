<?php VIC_GetMessage(); ?>
<article class="hentry">
    <div class="vc-section">
        <div class="p-4">
            <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'draft_header.php');?>
        </div>
        <div class="p-4 bg-gray">
            <div class="vc-row">
                <div class="vc-col-md-8">
                    <h3 class="vc-title"><?php echo esc_html(__('Available Players', 'victorious'));?></h3>
                    <?php if($league['entry_fee'] > 0 && $league['olddraft_insurance_fee'] > 0):?>
                        <div class="header-right">
                            <label class="checkbox-control d-inline-block mt-0 mb-2">
                                <input type="checkbox" id="olddraft_insurance_fee" <?php if(!empty($score) && $score['olddraft_insurance']):?>checked="checked" disabled="disabled"<?php endif;?>>
                                <?php echo sprintf(esc_html(__('Pay %s to buy insurance', 'victorious')), VIC_FormatMoney(round($league['olddraft_insurance_fee'] * $league['entry_fee'] / 100, 2))); ?>
                                <span class="checkmark"></span>
                            </label>
                        </div>
                    <?php endif;?>
                    <div class="vc-pick-player">
                        <div class="vc-pick-player-left">
                            <div class="vc-pick-player-list" id="vc-position">
                                <div class="vc-pick-player-item">
                                    <a href="javascript:void(0)" data-id="0" class="f-is-active">
                                        <?php echo esc_html(__('All', 'victorious')) ?>
                                    </a>
                                </div>
                                <?php foreach ($positions as $position):?>
                                    <?php if (!empty($position['is_extra'])) {
                                        continue;
                                    }
                                    ?>
                                    <div class="vc-pick-player-item">
                                        <a href="javascript:void(0)" data-id="<?php echo esc_attr($position['id']); ?>">
                                            <?php echo esc_html($position['name']); ?>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="vc-pick-player-right">
                            <div class="f-player-search">
                                <span class="material-icons">search</span>
                                <input type="search" id="player-search" placeholder="<?php echo esc_html(__('Find a player...', 'victorious'));?>">
                            </div>
                        </div>
                    </div>
                    <div class="vc-table bg-white mb-2">
                        <table>
                            <thead>
                                <tr>
                                    <th class="table-sorting" colspan="2" data-type="name" data-sort="">
                                        <?php echo esc_html(__('Name', 'victorious'));?>
                                        <span class="material-icons f-sorted-asc" style="display: none;">expand_more</span>
                                        <span class="material-icons f-sorted-desc" style="display: none;">expand_less</span>
                                    </th>
                                    <th class="table-sorting active-sort" data-type="salary" data-sort="" style="width: 20%">
                                        <?php echo esc_html(__('Salary', 'victorious'));?>
                                        <span class="material-icons f-sorted-asc" style="display: none;">expand_more</span>
                                        <span class="material-icons f-sorted-desc" style="display: inline;">expand_less</span>
                                    </th>
                                    <th class="f-player-add" style="width: 8%"></th>
                                </tr>
                            </thead>
                            <tbody id="player-content"></tbody>
                        </table>
                    </div>
                </div>
                <div class="vc-col-md-4">
                    <div class="vc-roster">
                        <h3 class="vc-title"><?php echo esc_html(__('Your lineup', 'victorious'));?></h3>
                        <p class="vc-des">
                            <span class="material-icons icon-20 mr-1">lock</span> <?php echo esc_html(__('Locks @', 'victorious'));?> <?php echo VIC_DateTranslate($league['startDate']); ?><span class="f-game_status_open"></span>
                        </p>
                        <div class="vc-salary-container bg-white">
                            <div class="vc-salary-item vc-salary-remaining">
                                <div class="vc-salary-item-wrap">
                                    <?php echo esc_html(__('Salary Remaining', 'victorious'));?>
                                    <div class="vc-salary-amount" id="salaryRemaining" data-value="<?php echo esc_attr($league['salary_remaining']);?>">
                                        <?php if ($league['salary_remaining'] > 0): ?>
                                            <?php echo VIC_FormatMoney($league['salary_remaining'], $balance_type['currency_code_symbol'], $balance_type['currency_position']); ?>
                                        <?php else: ?>
                                            <?php echo esc_html(__('Unlimited', 'victorious'));?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="vc-salary-item vc-salary-average">
                                <div class="vc-salary-item-wrap">
                                    <?php echo esc_html(__('Avg/Player', 'victorious'));?>
                                    <div class="vc-salary-amount" id="AvgPlayer"></div>
                                </div>
                            </div>
                            <div class="clear"></div>
                            <?php if ($lineups != null): ?>
                                <ul class="vc-roster-list f-roster">
                                    <?php foreach ($lineups as $lineup):
                                        $player = !empty($lineup['player']) ? $lineup['player'] : array();
                                        $fight = !empty($player['fight']) ? $player['fight'] : array();
                                        ?>
                                        <li class="vc-roster-list-item f-roster-position <?php echo esc_attr($player != null ? "filled" : "");?> lineup_<?php echo esc_attr($lineup['id']); ?> <?php echo esc_attr($player != null ? "lineup_player_".$player['id'] : "");?>" data-id="<?php echo esc_attr($lineup['id']); ?>" data-player_id="<?php echo esc_attr($player != null ? $player['id'] : "");?>" data-player_salary="<?php echo esc_attr($player != null ? $player['salary'] : "");?>">
                                            <div class="vc-roster-avatar mr-3 f-player-image <?php if(empty($player['image_url'])):?>f-no-image<?php endif;?>">
                                                <?php if(!empty($player['image_url'])):?>
                                                    <img src="<?php echo esc_attr($player['image_url']);?>" />
                                                <?php endif;?>
                                            </div>
                                            <div class="f-position">
                                                <span class="vc-player-position mr-2"><?php echo esc_html($lineup['name']); ?></span>
                                                <span class="color-blue f-player" data-empty="<?php echo esc_html(__('Add player', 'victorious'));?>"><?php echo esc_html($player != null ? $player['name'] : __('Add player', 'victorious'));?></span>
                                            </div>
                                            <a class="vc-player-remove btn_remove_lineup" data-player_id="<?php echo esc_attr($player != null ? $player['id'] : "");?>" style="<?php echo esc_attr($player != null ? 'visibility:visible' : 'visibility:hidden');?>">
                                                <span class="material-icons"> remove_circle_outline </span>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                        <div class="vc-salary-footer">
                            <a href="javascript:void(0)" class="color-blue text-right mb-2 d-block" id="btn_clear_all_lineup"><?php echo esc_html(__('Clear all', 'victorious'));?></a>
                            <a href="javascript:void(0)" class="vc-button btn-green btn-size-lg btn-radius5 btn-w100" id="btnSubmit"><?php echo esc_html(__('Enter', 'victorious'));?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>
<form id="formLineup">
    <div class="f-form_error"></div>
    <input type="hidden" value="<?php echo esc_attr($league['leagueID']); ?>" name="league_id" id="league-id">
    <input type="hidden" value="<?php echo esc_attr($entry_number); ?>" name="entry_number">
    <input type="hidden" value="" name="lineup_ids" id="lineup_ids_value">
    <input type="hidden" value="" name="player_ids" id="player_ids_value">
</form>
<?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW . 'dlg_info.php');?>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery.olddraft.initOldDraft();
    })
</script>