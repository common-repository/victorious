<?php VIC_GetMessage(); ?>
<input type="hidden" id="is_position_step" value="<?php echo esc_attr($settings['is_position_step']);?>">
<input type="hidden" value="<?php echo esc_attr($league['leagueID']); ?>" id="league-id">
<input type="hidden" value="<?php echo esc_attr($league['playoff_draft_countdown']); ?>" id="draft-countdown">
<article class="hentry">
    <div class="vc-section">
        <div class="p-4">
            <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'draft_header.php');?>
            <?php if (!empty($fights)): ?>
                <div class="vc-pick-your-team">
                    <section data-role="fixture-picker" class="f-fixture-picker">
                        <div class="vc-pick-your-team-container f-fixture-picker-button-container">
                            <div class="vc-pick-your-team-item vc-pick-your-team-item-all f-is-active vc-fixture-item">
                                <?php echo esc_html(__('All', 'victorious'));?>
                            </div>
                            <?php foreach ($fights as $fight):
                                $home_team = $fight['home_team'];
                                $away_team = $fight['away_team'];
                                ?>
                                <div class="vc-pick-your-team-item vc-fixture-item" data-id="<?php echo esc_attr($fight['fightID']);?>">
                                    <span class="vc-pick-team-home"><?php echo esc_html(__("A:", "victorious"))." ".esc_html($away_team['nickName']); ?></span>@
                                    <span class="vc-pick-team-away"><?php echo esc_html(__("H:", "victorious"))." ".esc_html($home_team['nickName']); ?></span>
                                    <div class="vc-start-time"><?php echo VIC_DateTranslate($fight['startDate']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                </div>
            <?php endif; ?>
            <p>
                <?php echo $current_week['week_name'];?>
            </p>
            <div id="inDraftUserContent"></div>
        </div>
        <div class="p-4 bg-gray" style="border-radius: 0 0 10px 10px">
            <div class="vc-row">
                <div class="vc-col-md-8">
                    <h3 class="vc-title"><?php echo esc_html(__('Available Players', 'victorious'));?></h3>
                    <div class="vc-pick-player">
                        <div class="vc-pick-player-left">
                            <div class="vc-pick-player-list" id="vc-position">
                                <?php if(!$settings['is_position_step'] && !empty($positions) && count($positions) > 1):?>
                                    <div class="vc-pick-player-item">
                                        <a href="javascript:void(0)" data-id="0" class="f-is-active">
                                            <?php echo esc_html(__('All', 'victorious'));?>
                                        </a>
                                    </div>
                                <?php endif;?>
                                <?php if (!empty($rounds)): ?>
                                    <?php foreach ($rounds as $round):?>
                                        <div class="vc-pick-player-item">
                                            <a href="javascript:void(0)" data-player-id="<?php echo esc_attr($round['squad']); ?>" <?php if(count($rounds) == 1):?>class="f-is-active"<?php endif;?>>
                                                <?php echo esc_html($round['name']); ?>
                                            </a>
                                        </div>
                                    <?php endforeach;?>
                                <?php else: ?>
                                    <?php foreach ($positions as $position):?>
                                        <?php if (!empty($position['is_extra'])) {
                                            continue;
                                        }
                                        ?>
                                        <div class="vc-pick-player-item">
                                            <a href="javascript:void(0)" data-id="<?php echo esc_attr($position['id']); ?>" <?php if(count($positions) == 1):?>class="f-is-active"<?php endif;?>>
                                                <?php echo esc_html($position['name']); ?>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if($settings['is_position_step']):?>
                                        <div class="vc-pick-player-item">
                                            <a href="javascript:void(0)" data-id="0" class="f-is-active">
                                                <?php echo esc_html(__('All', 'victorious'));?>
                                            </a>
                                        </div>
                                    <?php endif;?>
                                <?php endif; ?>
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
                                <th class="table-sorting" data-type="name" data-sort="">
                                    <?php echo esc_html(__('Name', 'victorious'));?>
                                    <span class="material-icons f-sorted-asc" style="display: none;">expand_more</span>
                                    <span class="material-icons f-sorted-desc" style="display: none;">expand_less</span>
                                </th>
                                <?php if($league['is_team']):?>
                                <th style="width: 28%"><?php echo esc_html(__('Team', 'victorious'));?></th>
                                <th style="width: 20%"><?php echo esc_html(__('Game', 'victorious'));?></th>
                                <?php endif;?>
                                <th class="table-sorting active-sort" data-type="salary" data-sort="" style="width: 17%">
                                    <?php echo esc_html(__('Salary', 'victorious'));?>
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
                <div class="vc-col-md-4">
                    <div class="vc-roster">
                        <h3 class="vc-title"><?php echo esc_html(__('Your lineup', 'victorious'));?></h3>
                        <p class="vc-des">
                        </p>
                        <div class="vc-salary-container bg-white">
                            <?php /*if ($league['salary_remaining'] > 0): ?>
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
                            <?php endif;*/ ?>
                            <?php if ($lineups != null): ?>
                                <ul class="vc-roster-list f-roster">
                                    <?php foreach ($lineups as $lineup):
                                        $player = !empty($lineup['player']) ? $lineup['player'] : array();
                                        $fight = !empty($player['fight']) ? $player['fight'] : array();
                                        ?>
                                        <li class="vc-roster-list-item f-roster-position <?php echo esc_attr($player != null ? "filled" : "");?> lineup_<?php echo esc_attr($lineup['id']); ?> <?php echo esc_attr($player != null ? "lineup_player_".$player['id'] : "");?>" data-id="<?php echo esc_attr($lineup['id']); ?>" data-player_id="<?php echo esc_attr($player != null ? $player['id'] : "");?>" data-player_salary="<?php echo esc_attr($player != null ? $player['salary'] : "");?>" data-can-edit="<?php echo esc_attr($player != null ? $player['can_edit'] : 1);?>">
                                            <div class="vc-roster-avatar mr-3 f-player-image" <?php if(empty($player['image_url'])):?>style="display: none"<?php endif;?>>
                                                <?php if(!empty($player['image_url'])):?>
                                                    <img src="<?php echo esc_url($player['image_url']);?>" />
                                                <?php endif;?>
                                            </div>
                                            <div class="f-position">
                                                <span class="vc-player-position mr-2"><?php echo esc_html($lineup['name']); ?></span>
                                                <span class="color-blue f-player" data-empty="<?php echo esc_html(__('Add player', 'victorious'));?>"><?php echo esc_attr($player != null ? esc_html($player['name']) : esc_html(__('Add player', 'victorious')));?></span>
                                            </div>
                                            <?php if(empty($player) || $player['can_edit']):?>
                                                <a class="vc-player-remove btn_remove_lineup" data-id="<?php echo esc_attr($player != null ? $player['id'] : "");?>" style="<?php echo esc_attr($player != null ? 'visibility:visible' : 'visibility:hidden');?>">
                                                    <span class="material-icons"> remove_circle_outline </span>
                                                </a>
                                            <?php endif;?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>
<?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW . 'dlg_info.php');?>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery.playoff.initPlayoff();
    })

    //const button = document.querySelector('button');
    //const evtSource = new EventSource('<?php //echo VICTORIOUS_URL_SSE;?>//');
    //const eventList = document.querySelector('ul#abc');
    //
    //evtSource.onopen = function() {
    //    console.log("Connection to server opened.");
    //};
    //
    //evtSource.onmessage = function(e) {
    //    console.log('111111111');
    //    console.log(e.data);
    //    /*const newElement = document.createElement("li");
    //
    //    newElement.textContent = "message: " + e.data;
    //    eventList.appendChild(newElement);*/
    //};
    //
    //evtSource.onerror = function() {
    //    console.log("EventSource failed.");
    //};
    //
    //button.onclick = function() {
    //    console.log('Connection closed');
    //    evtSource.close();
    //};
</script>