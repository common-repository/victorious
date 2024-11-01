<?php VIC_GetMessage(); ?>
<input type="hidden" id="is_position_step" value="<?php echo esc_attr($settings['is_position_step']);?>">
<div class="f-contest-title-date">
    <h1 class="f-contest-title f-heading-styled"><?php echo esc_html($league['name']); ?></h1>
    <div class="f-contest-date-container">
        <div class="f-contest-date-start-time">
            <?php echo esc_html(__('Contest starts', 'victorious'));?> <?php echo VIC_DateTranslate($league['startDate']); ?>
        </div>
    </div>
</div>
<ul class="f-contest-information-bar">
    <li class="f-contest-entries-league"><?php echo esc_html(__('Entries:', 'victorious'));?> 
        <b>
            <a class="f-lightboxLeagueEntries_show" href="javascript:void(0)" onclick="return jQuery.global.ruleScoring(<?php echo esc_attr($league['leagueID']); ?>, 2)"><?php echo esc_html($league['entries']); ?></a>
        </b> / <?php echo esc_html($league['size']); ?>
        <span class="f-entries-player-league"> <?php echo esc_html(__('player league', 'victorious'));?></span>
    </li>
    <?php if (get_option('victorious_no_cash') == 0): ?>
        <li class="f-contest-entry-fee-container">
            <?php echo esc_html(__('Entry fee', 'victorious'));?>:
            <span class="f-entryFee-value amount"><?php echo esc_html($league['entry_fee'] == 0 ? __("Free", "victorious") : VIC_FormatMoney($league['entry_fee'], $balance_type['currency_code_symbol'], $balance_type['currency_position'])); ?></span>
        </li>
        <li class="f-contest-prize-container  f-gameEntry-inner-entryFeeSelected">
            <?php echo esc_html(__('Prizes', 'victorious'));?>:
            <span class="f-content-prize-amount">
                <a class="f-lightboxPrizeList_show" href="#"  onclick="return jQuery.global.ruleScoring(<?php echo esc_attr($league['leagueID']); ?>, 3)">
                    <?php echo VIC_FormatMoney($league['prizes'], $balance_type['currency_code_symbol'], $balance_type['currency_position']); ?>
                </a>
            </span>
        </li>
    <?php endif; ?>
    <li class="f-contest-rules-link-container">
        <a class="f-lightboxRulesAndScoring_show" onclick="return jQuery.global.ruleScoring(<?php echo esc_attr($league['leagueID']); ?>)" href="#">
            <?php echo esc_html(__('Rules &amp; Scoring', 'victorious'));?>
        </a>
    </li>
</ul>
<div class="clear"></div>
<div class="f-pick-your-team">
    <section data-role="fixture-picker" class="f-fixture-picker">
        <?php if (!empty($fights)): ?>
            <h1><?php echo esc_html(__('Players available from (click to filter):', 'victorious'));?></h1>
            <div class="f-fixture-picker-button-container">
                <a class="f-button f-mini f-is-active fixture-item" data-id="0"><?php echo esc_html(__('All Games', 'victorious'));?></a>
                <?php foreach ($fights as $fight): 
                    $home_team = $fight['home_team'];
                    $away_team = $fight['away_team'];
                ?>
                    <a class="f-button f-mini fixture-item" data-id="<?php echo esc_attr($fight['fightID']);?>">
                        <span class="f-fixture-team-home"><?php echo esc_html(__("A:", "victorious"))." ".esc_html($away_team['nickName']); ?></span>
                        @
                        <span class="f-fixture-team-away"><?php echo esc_html(__("H:", "victorious"))." ".esc_html($home_team['nickName']); ?></span>
                        <span class="f-fixture-start-time"><?php echo VIC_DateTranslate($fight['startDate']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>
<div class="f-row">
    <section class="f-contest-player-list-container" data-role="player-list">
        <div class="f-row">
            <div class="pick-player-top">
                <h1>
                    <?php echo esc_html(__('Available Players', 'victorious'));?>
                </h1>
            </div>
            <ul class="f-player-list-position-tabs f-tabs f-row">
                <?php if(!$settings['is_position_step']):?>
                <li>
                    <a href="javascript:void(0)" data-id="0">
                        <?php echo esc_html(__('All', 'victorious')) ?>
                    </a>
                </li>
                <?php endif;?>
                <?php foreach ($positions as $position):?>
                    <?php if (!empty($position['is_extra'])) {
                            continue;
                        }
                    ?>
                        <li>
                            <a href="javascript:void(0)" data-id="<?php echo esc_attr($position['id']); ?>">
                                <?php echo esc_html($position['name']); ?>
                            </a>
                        </li>
                <?php endforeach; ?>
                <?php if($settings['is_position_step']):?>
                    <li>
                        <a href="javascript:void(0)" data-id="0">
                            <?php echo esc_html(__('All', 'victorious')) ?>
                        </a>
                    </li>
                <?php endif;?>
                <li class="f-player-search">
                    <label class="f-is-hidden" for="player-search"><?php echo esc_html(__('Find a player', 'victorious'));?></label>
                    <input type="search" id="player-search" placeholder="<?php echo esc_html(__('Find a player...', 'victorious'));?>">
                </li>
            </ul>
            <div class="f-errorMessage"></div>
            <div id="listPlayers" style="min-height: 500px">
                <div class="f-player-list-empty"><?php echo esc_html(__('No matching teams. Try changing your filter settings.', 'victorious'));?></div>
                <table class="f-condensed f-player-list-table">
                    <thead>
                        <tr>
                            <th class="table-sorting" colspan="2" data-type="name" data-sort="">
                                <?php echo esc_html(__('Name', 'victorious'));?>
                                <i class="f-icon f-sorted-asc fa fa-angle-up" style="display: none;"></i>
                                <i class="f-icon f-sorted-desc fa fa-angle-down" style="display: none;"></i>
                            </th>
                            <th class="hidden-xs" style="width: 28%">
                                <?php echo esc_html(__('Team', 'victorious'));?>
                            </th>
                            <th class="hidden-xs" style="width: 20%">
                                <?php echo esc_html(__('Game', 'victorious'));?>
                            </th>
                            <th class="table-sorting active-sort" data-type="salary" data-sort="" style="width: 11%">
                                <?php echo esc_html(__('Salary', 'victorious'));?>
                                <i class="f-icon f-sorted-asc fa fa-angle-up" style="display: none;"></i>
                                <i class="f-icon f-sorted-desc fa fa-angle-down" style="display: inline;"></i>
                            </th>
                            <th class="f-player-add" style="width: 8%"></th>
                        </tr>
                    </thead>
                    <tbody id="player-content"></tbody>
                </table>
            </div>
            <div class="f-row f-legend">
                <div class="f-draft-legend-key-title"><?php echo esc_html(__('Indicator legend', 'victorious'));?></div>
                <div class="f-clear"></div>
                <div class="f-draft-legend-key-content">
                    <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'indicator_legend.php');?>
                    <div class="f-clear"></div>
                </div>
            </div>
        </div>
    </section>
    <section class="f-roster-container" data-role="team">
        <header>
            <div class="f-lineup-text-container">
                <h1><?php echo esc_html(__('Your lineup', 'victorious'));?></h1>
                <p class="f-lineup-lock-message">
                    <i class="fa fa-lock"></i> <?php echo esc_html(__('Locks @', 'victorious'));?> <?php echo VIC_DateTranslate($league['startDate']); ?>
                    <span class="f-game_status_open"></span>
                </p>
            </div>
            <div class="f-salary-remaining">
                <div class="f-salary-remaining-container">
                    <span class="f-salary-remaining-amount" id="salaryRemaining" data-value="<?php echo esc_attr($league['salary_remaining']);?>">
                        <?php if ($league['salary_remaining'] > 0): ?>
                            <?php echo VIC_FormatMoney($league['salary_remaining'], $balance_type['currency_code_symbol'], $balance_type['currency_position']); ?>
                        <?php else: ?>
                            <?php echo esc_html(__('Unlimited', 'victorious'));?>
                        <?php endif; ?>
                    </span><?php echo esc_html(__('Salary Remaining', 'victorious'));?>
                </div>
                <div class="f-player-average-container">
                    <span class="f-player-average-amount" id="AvgPlayer"></span><?php echo esc_html(__('Avg/Player', 'victorious'));?>
                </div>
            </div>
        </header>
        <section class="f-roster">
            <ul>
                <?php if ($lineups != null): ?>
                    <?php foreach ($lineups as $lineup): 
                        $player = !empty($lineup['player']) ? $lineup['player'] : array();
                        $fight = !empty($player['fight']) ? $player['fight'] : array();
                    ?>
                        <li class="f-roster-position <?php echo esc_attr($player != null ? "filled" : "");?> lineup_<?php echo esc_attr($lineup['id']); ?> <?php echo esc_attr($player != null ? "lineup_player_".$player['id'] : "");?>" data-id="<?php echo esc_attr($lineup['id']); ?>" data-player_id="<?php echo esc_attr($player != null ? $player['id'] : "");?>" data-player_salary="<?php echo esc_attr($player != null ? $player['salary'] : "");?>">
                            <div class="f-player-image <?php if(empty($player['image_url'])):?>f-no-image<?php endif;?>">
                                <?php if(!empty($player['image_url'])):?>
                                    <img src="<?php echo esc_attr($player['image_url']);?>" />
                                <?php endif;?>
                            </div>
                            <div class="f-position">
                                <div><?php echo esc_html($lineup['name']); ?></div>
                                <div class="f-empty-roster-slot-instruction" style="<?php echo esc_attr($player != null ? 'display:none' : "");?>"><?php echo esc_html(__('Add player', 'victorious'));?></div>
                            </div>
                            <div class="f-player player_info" data-id="<?php echo esc_attr($player != null ? $player['id'] : "");?>">
                                <?php echo esc_html($player != null ? $player['name'] : "");?>
                            </div>
                            <div class="f-salary" style="<?php echo esc_attr($player != null ? 'visibility:visible' : "");?>"><?php echo esc_html($player != null ? VIC_FormatMoney($player['salary'], $balance_type['currency_code_symbol'], $balance_type['currency_position']) : "");?></div>
                            <div class="f-fixture" style="<?php echo esc_attr($fight != null ? 'visibility:visible' : "");?>">
                                <?php if($fight != null):
                                    $home_team = $fight['home_team'];
                                    $away_team = $fight['away_team'];
                                ?>
                                    <?php if($player['team_id'] == $away_team['teamID']):?>
                                        <b><?php echo esc_html($away_team['nickName']);?></b> @ <?php echo esc_html($home_team['nickName']);?>
                                    <?php else:?>
                                        <?php echo esc_html($away_team['nickName']);?> @ <b><?php echo esc_html($home_team['nickName']);?></b>
                                    <?php endif;?>
                                <?php endif;?>
                            </div>
                            <a class="f-button f-tiny f-text btn_remove_lineup" data-player_id="<?php echo esc_attr($player != null ? $player['id'] : "");?>" style="<?php echo esc_attr($player != null ? 'visibility:visible' : "");?>">
                                <i class="fa fa-minus-circle"></i>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <div class="f-row import-clear-button-container">
                <button class="f-button f-mini f-text f-right" id="btn_clear_all_lineup">
                    <small><i class="fa fa-minus-circle"></i> <?php echo esc_html(__('Clear all', 'victorious'));?></small>
                </button>
            </div>
        </section>
        <footer class="f-">
            <div class="f-contest-entry-fee-container">
                <form id="formLineup">
                    <div class="f-form_error"></div>
                    <input type="hidden" value="<?php echo esc_attr($league['leagueID']); ?>" name="league_id" id="league-id">
                    <input type="hidden" value="<?php echo esc_attr($entry_number); ?>" name="entry_number">
                    <input type="hidden" value="" name="lineup_ids" id="lineup_ids_value">
                    <input type="hidden" value="" name="player_ids" id="player_ids_value">
                </form>
            </div>
            <div class="f-contest-enter-button-container">
                <input type="button" id="btnSubmit" value="<?php echo esc_html(__('Enter', 'victorious'));?>" class="f-button f-jumbo f-primary">
            </div>
        </footer>
    </section>
</div>
<?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW . 'dlg_info.php');?>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery.playerdraft.initPlayerDraft();
    })
</script>