<?php VIC_GetMessage(); ?>
<article class="hentry">
    <div class="vc-section">
        <div class="p-4">
            <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'draft_header.php');?>
        </div>
        <div class="p-4 bg-gray" style="border-radius: 0 0 10px 10px">
            <div class="vc-row">
                <div class="vc-col-md-5">
                    <h3 class="vc-title"><?php echo esc_html(__('Available Picks', 'victorious'));?></h3>
                    <div class="vc-pick-player">
                        <div class="vc-pick-player-left">
                            <div class="vc-pick-player-list" id="vc-position">
                                <?php if(!empty($positions) && count($positions) > 0):?>
                                    <?php foreach ($positions as $position):?>
                                        <div class="vc-pick-player-item">
                                            <a href="javascript:void(0)" data-id="<?php echo esc_attr($position['id']); ?>">
                                                <?php echo esc_html($position['name']); ?>
                                            </a>
                                        </div>
                                    <?php endforeach;?>
                                <?php else:?>
                                    <div class="vc-pick-player-item" style="display: none">
                                        <a href="javascript:void(0)" data-id="0">
                                            <?php echo esc_html(__('All', 'victorious'));?>
                                        </a>
                                    </div>
                                <?php endif;?>
                            </div>
                        </div>
                        <div class="vc-pick-player-right">
                            <div class="f-player-search">
                                <span class="material-icons">search</span>
                                <input type="search" id="player-search" placeholder="<?php echo esc_html(__('Search...', 'victorious'));?>">
                            </div>
                        </div>
                        <?php if(!empty($categories)):?>
                            <div class="vc-pick-player-list mt-2">
                                <select class="form-control" id="player-category">
                                    <option value=""><?php echo esc_html(__('All categories', 'victorious'));?></option>
                                    <?php foreach ($categories as $category):?>
                                        <option value="<?php echo esc_attr($category['id']); ?>"><?php echo esc_html($category['name']); ?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        <?php endif;?>
                    </div>
                    <div class="vc-table bg-white mb-2">
                        <table cellspacing="0" cellpadding="0">
                            <thead>
                            <tr>
                                <th class="table-sorting" data-type="name" data-sort="">
                                    <?php echo esc_html(__('Name', 'victorious'));?>
                                    <span class="material-icons f-sorted-asc" style="display: none;">expand_more</span>
                                    <span class="material-icons f-sorted-desc" style="display: none;">expand_less</span>
                                </th>
                                <th class="table-sorting active-sort" data-type="price" data-sort="" style="width: 40%">
                                    <?php echo esc_html(__('Price', 'victorious'));?>
                                    <span class="material-icons f-sorted-asc" style="display: none;">expand_more</span>
                                    <span class="material-icons f-sorted-desc">expand_less</span>
                                </th>
                                <th style="width: 8%"></th>
                            </tr>
                            </thead>
                            <tbody id="player-content"></tbody>
                        </table>
                    </div>
                    <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'indicator_legend.php');?>
                </div>
                <div class="vc-col-md-7">
                    <div class="vc-roster">
                        <h3 class="vc-title"><?php echo esc_html(__('Your lineup', 'victorious'));?></h3>
                        <p class="vc-des">
                            <span class="material-icons icon-20 mr-1">lock</span> <?php echo esc_html(__('Locks @', 'victorious'));?> <?php echo VIC_DateTranslate($league['startDate']); ?><span class="f-game_status_open"></span>
                        </p>
                        <div class="vc-salary-container bg-white">
                            <div class="vc-salary-item vc-salary-remaining">
                                <div class="vc-salary-item-wrap">
                                    <?php echo esc_html(__('Total', 'victorious'));?>
                                    <div class="vc-salary-amount" id="totalAmount" data-value="0"><?php echo VIC_FormatMoney(0, 'USD|$');?></div>
                                </div>
                            </div>
                            <div class="vc-salary-item vc-salary-average">
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
                            <div class="clear"></div>
                            <div class="vc-table bg-white mb-2">
                                <table class="f-condensed f-roster">
                                    <thead>
                                    <tr>
                                        <th><?php echo esc_html(__('Name', 'victorious'));?></th>
                                        <th style="width: 15%"><?php echo esc_html(__('Price', 'victorious'));?></th>
                                        <th style="width: 22%"><?php echo esc_html(__('Prior Day +/-', 'victorious'));?></th>
                                        <th style="width: 12%"><?php echo esc_html(__('Shares', 'victorious'));?></th>
                                        <th style="width: 8%"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (!empty($lineups)): ?>
                                        <?php foreach ($lineups as $k => $lineup):
                                            $player = !empty($lineup['player']) ? $lineup['player'] : array();
                                        ?>
                                            <tr class="f-roster-position <?php echo esc_attr($player != null ? "filled" : "");?> lineup_<?php echo esc_attr($lineup['id']); ?> <?php echo esc_attr($player != null ? "lineup_player_".$player['id'] : "");?>" data-id="<?php echo esc_attr($lineup['id']); ?>" data-player_id="<?php echo esc_attr($player != null ? $player['id'] : "");?>" data-player_salary="<?php echo esc_attr($player != null ? $player['salary'] : "");?>">
                                                <td>
                                                    <div class="vc-player-wrap">
                                                        <div class="vc-roster-avatar mr-3 f-player-image f-player-image-coin">
                                                            <?php if(!empty($player['image_url'])):?>
                                                                <img src="<?php echo esc_url($player['image_url']);?>">
                                                            <?php endif;?>
                                                        </div>
                                                        <div class="f-position">
                                                            <span class="f-player color-blue f-coin player_info" data-id="<?php echo esc_attr($player != null ? $player['id'] : "");?>">
                                                                <?php echo esc_html($player != null ? esc_html($player['name']) : "");?>
                                                            </span>
                                                            <span class="f-empty-roster-slot-instruction" style="<?php echo esc_attr($player != null ? 'display:none' : "");?>"><?php echo count($lineups) > 1 ? $lineup['name'] : esc_html(__('Add coin', 'victorious'));?></span>
                                                            <?php if($k == 0 && $league['porfolio_multiplier'] > 1):?>
                                                                <span class="coin-multiplier"><?php echo 'x'.$league['porfolio_multiplier'];?></span>
                                                            <?php endif;?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="f-salary" style="<?php echo esc_attr($player != null ? 'visibility:visible' : "");?>"><?php echo esc_attr($player != null ? VIC_FormatMoney($player['salary'], null, null, null, false) : "");?></div>
                                                </td>
                                                <td>
                                                    <div class="f-prior-day-wrapper <?php echo esc_attr($player['prior_day'] > 0 ? 'good-point' : 'bad-point');?>" style="<?php echo esc_attr($player == null ? 'display: none' : "");?>">
                                                        <div class="f-prior-day"><?php echo VIC_FormatMoney($player['prior_day'], $balance_type['currency_code_symbol'], $balance_type['currency_position'], false); ?></div>
                                                        <p class="f-prior-day-percent">
                                                            (<?php echo esc_html($player['prior_day_percent']);?>%)
                                                        </p>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="f-coin-quantity" <?php if($player == null):?>style="display: none"<?php endif;?>>
                                                        <input type="number" min="1" class="form-control coin-quantity" value="<?php echo !empty($player['quantity']) ? $player['quantity'] : 1;?>" />
                                                    </div>
                                                </td>
                                                <td>
                                                    <a class="vc-player-remove btn_remove_lineup" data-player_id="<?php echo esc_attr($player != null ? $player['id'] : "");?>" style="<?php echo esc_attr($player != null ? 'visibility:visible' : 'visibility:hidden');?>">
                                                        <span class="material-icons"> remove_circle_outline </span>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
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
    <input type="hidden" value="" name="quantity" id="quantity_value">
</form>
<?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW . 'dlg_info.php');?>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery.portfolio.initPortfolio();
    })
</script>