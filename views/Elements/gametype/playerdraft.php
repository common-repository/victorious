<?php VIC_GetMessage(); ?>
<input type="hidden" id="type_league" value="single">
<div class="f-contest-title-date">
    <h1 class="f-contest-title f-heading-styled"><?php echo esc_html($league['name']); ?></h1>
    <div class="f-contest-date-container">
        <div class="f-contest-date-start-time">
            <?php echo esc_html(__('Contest starts', 'victorious'));?> <?php echo VIC_DateTranslate($aPool['startDate']); ?>
        </div>
    </div>
</div>
<ul class="f-contest-information-bar">
    <li class="f-contest-entries-league"><?php echo esc_html(__('Entries:', 'victorious'));?>
        <b>
            <a class="f-lightboxLeagueEntries_show" href="#" onclick="return jQuery.global.ruleScoring(<?php echo esc_attr($league['leagueID']); ?>, 2)"><?php echo esc_html($league['entries']); ?></a>
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
                <a class="f-lightboxPrizeList_show" href="javascript:void(0)"  onclick="return jQuery.global.ruleScoring(<?php echo esc_attr($league['leagueID']); ?>, 3)">
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
<?php if ($otherLeagues != null && get_option('victorious_show_import_pick') == 1): ?>
    <p style="margin: 20px 0 10px;"><?php echo esc_html(__('Below is a list of games you have already entered for this event. Simply click on \'Import Picks\' to import your picks from that game.', 'victorious'));?></p>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="table table-striped table-bordered importpicks">
        <thead>
            <tr>
                <th><?php echo esc_html(__('Name', 'victorious'));?></th>
                <th class="mobile"><?php echo esc_html(__('Opponent', 'victorious'));?></th>
                <th><?php echo esc_html(__('Type', 'victorious'));?></th>
                <th class="mobile"><?php echo esc_html(__('Entry Fee', 'victorious'));?></th>
                <th><?php echo esc_html(__('Size', 'victorious'));?></th>
                <th class="mobile"><?php echo esc_html(__('Structure', 'victorious'));?></th>
                <th colspan="2">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($otherLeagues as $otherLeague): ?>
                <tr>
                    <td>
                        <div><?php echo esc_html($otherLeague['name']); ?></div>
                    </td>
                    <td class="mobile">
                        <div><?php echo esc_html($otherLeague['opponent']); ?></div>
                    </td>
                    <td>
                        <div><?php echo esc_html($otherLeague['gameType']); ?></div>
                    </td>
                    <td class="mobile">
                        <div><?php echo esc_html($otherLeague['entry_fee']); ?></div>
                    </td>
                    <td>
                        <div><?php echo esc_html($otherLeague['size']); ?></div>
                    </td>
                    <td class="mobile">
                        <div><?php echo esc_html($otherLeague['prize_structure']); ?></div>
                    </td>
                    <td colspan="2">
                        <div>
                            <input type="button" value="<?php echo esc_html(__('Import Picks', 'victorious'));?>"  class="btn btn-success" onclick="jQuery.playerdraft.addMultiPlayers('<?php echo esc_attr($otherLeague['player_id']) ?>')">
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<div class="f-pick-your-team">
    <section data-role="fixture-picker" class="f-fixture-picker">
        <?php if (!empty($aFights)): ?>
            <h1><?php echo esc_html(__('Players available from (click to filter):', 'victorious'));?></h1>
            <div class="f-fixture-picker-button-container">
                <a class="f-button f-mini f-is-active fixture-item" onclick="jQuery.playerdraft.setActiveFixture(this);return jQuery.playerdraft.loadPlayers();"><?php echo esc_html(__('All Games', 'victorious'));?></a>
                <?php foreach ($aFights as $aFight): ?>
                    <a data-team-id1="<?php echo esc_attr($aFight['fighterID1']); ?>" data-team-id2="<?php echo esc_attr($aFight['fighterID2']); ?>" <?php if ($aFight['started'] == 0 || $league["trade_player"] == 1): ?>onclick="jQuery.playerdraft.setActiveFixture(this);return jQuery.playerdraft.loadPlayers();"<?php endif; ?> class="f-button f-mini fixture-item <?php if ($aFight['started'] == 1 && $league["trade_player"] == 0): ?>f-is-disabled<?php endif; ?>">
                        <span class="f-fixture-team-home"><?php echo esc_html(__("A:", "victorious"))." ".esc_html($aFight['nickName2']); ?></span>
                        @
                        <span class="f-fixture-team-away"><?php echo esc_html(__("H:", "victorious"))." ".esc_html($aFight['nickName1']); ?></span>
                        <span class="f-fixture-start-time"><?php echo VIC_DateTranslate($aFight['startDate']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($aRounds)): ?>
            <h1><?php echo esc_html(__('Players available from (click to filter):', 'victorious'));?></h1>
            <div class="f-fixture-picker-button-container">
                <a class="f-button f-mini f-is-active fixture-item" onclick="jQuery.playerdraft.setActiveFixture(this);return jQuery.playerdraft.loadPlayers();"><?php echo esc_html(__('All Games', 'victorious'));?></a>
                <?php foreach ($aRounds as $aRound): ?>
                    <a class="f-button f-mini fixture-item">
                        <span class="f-fixture-team-home"><?php echo esc_html($aRound['name']); ?></span>
                        <span class="f-fixture-start-time"><?php echo VIC_DateTranslate($aRound['startDate']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>
<div class="f-row" <?php if($is_lineup_scoccer_field):?>id="custom-field-soccer"<?php endif;?>>
    <section class="f-contest-player-list-container" data-role="player-list">
        <div class="f-row">
            <h1>
                <?php echo esc_html($aPool['is_horse_racing'] ? __('Available Horses', 'victorious') : __('Available Players', 'victorious'));?>
                <?php if ($league["is_live"] && $league["trade_player"]): ?>
                    <br/>
                    <?php echo esc_html(__('Note: You are only able to change one player', 'victorious'));?>
                <?php endif; ?>
            </h1>
            <ul class="f-player-list-position-tabs f-tabs f-row">
                <?php if(!$is_position_step):?>
                <li>
                    <a href="" data-id="" class="f-is-active" onclick="jQuery.playerdraft.setActivePosition(this);return jQuery.playerdraft.loadPlayers();"><?php echo esc_html(__('All', 'victorious'));?></a>
                </li>
                <?php endif;?>
                <?php if ($aRounds != null && $aPool['round_position']): ?>
                    <?php foreach ($aRounds as $aRound):?>
                        <li>
                            <a href="" data-round_squad="<?php echo esc_attr($aRound['squad']); ?>" onclick="jQuery.playerdraft.setActivePosition(this);return jQuery.playerdraft.loadPlayers();">
                                <?php echo esc_html($aRound['name']); ?>
                            </a>
                        </li>
                    <?php endforeach;?>
                <?php elseif ($aPositions != null): ?>
                    <?php
                    foreach ($aPositions as $aPosition):
                        if (isset($aPosition['is_extra']) && !empty($aPosition['is_extra'])) {
                            continue;
                        }
                        ?>
                        <li>
                            <a class="<?php echo ($is_position_step && $aPosition['name'] == 'G')?"f-is-active":""; ?>" href="" data-id="<?php echo esc_attr($aPosition['id']); ?>" data-is-flex="<?php echo isset($aPosition['is_flex']) ? $aPosition['is_flex'] : 0; ?>" onclick="jQuery.playerdraft.setActivePosition(this);return jQuery.playerdraft.loadPlayers();">
                                <?php echo esc_html($aPosition['name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if($is_position_step):?>
                <li>
                     <a href="" data-id="" class="<?php echo esc_attr($is_lineup_scoccer_field?"":"f-is-active"); ?>" onclick="jQuery.playerdraft.setActivePosition(this);return jQuery.playerdraft.loadPlayers();"><?php echo esc_html(__('All', 'victorious'));?></a>
                </li>
                <?php endif;?>
                <li class="f-player-search">
                    <label class="f-is-hidden" for="player-search"><?php echo esc_attr($aPool['is_horse_racing'] ? __('Find a Horse', 'victorious') : __('Find a Player', 'victorious'));?></label>
                    <input type="search" id="player-search" placeholder="<?php echo esc_attr($aPool['is_horse_racing'] ? __('Find a horse...', 'victorious') : __('Find a player...', 'victorious'));?>" incremental="" autosave="fd-player-search" results="10">
                </li>
            </ul>
            <div data-role="scrollable-header">
                <table class="f-condensed f-player-list-table-header f-header-fields">
                    <thead>
                        <tr>
                            <th colspan="2" class="f-player-name table-sorting">
                                <?php echo esc_html(__('Name', 'victorious'));?>
                                <i class="f-icon f-sorted-asc fa fa-angle-up" style="display: none;"></i>
                                <i class="f-icon f-sorted-desc fa fa-angle-down" style="display: none;"></i>
                            </th>
                            <?php if (!$aPool['only_playerdraft']): ?>
                                <th class="f-player-played table-sorting hidden-xs">
                                    <i class="f-icon f-sorted-asc fa fa-angle-up" style="display: none;"></i>
                                    <i class="f-icon f-sorted-desc fa fa-angle-down" style="display: none;"></i>
                                    <?php echo esc_html(__('Team', 'victorious'));?>
                                </th>
                                <th class="f-player-fixture table-sorting hidden-xs">
                                    <?php echo esc_html(__('Game', 'victorious'));?>
                                    <i class="f-icon f-sorted-asc fa fa-angle-up" style="display: none;"></i>
                                    <i class="f-icon f-sorted-desc fa fa-angle-down" style="display: none;"></i>
                                </th>
                            <?php endif; ?>
                            <th class="f-player-salary table-sorting">
                                <i class="f-icon f-sorted-asc fa fa-angle-up" style="display: none;"></i>
                                <i class="f-icon f-sorted-desc fa fa-angle-down" style="display: inline;"></i>

                                <?php echo esc_html(__('Salary', 'victorious'));?>
                            </th>
                            <th class="f-player-add"></th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="f-errorMessage"></div>
            <div data-role="scrollable-body" id="listPlayers">
                <div class="f-player-list-empty"><?php echo esc_html(__('No matching players. Try changing your filter settings.', 'victorious'));?></div>
                <table class="f-condensed f-player-list-table">
                    <thead class="f-is-hidden">
                        <tr>
                            <th class="f-player-name">
                                <?php echo esc_html(__('Pos', 'victorious'));?>
                            </th>
                            <th class="f-player-name">
                                <?php echo esc_html(__('Name', 'victorious'));?>
                            </th>
                            <?php if (!$aPool['only_playerdraft']): ?>
                                <th class="f-player-played">
                                    <?php echo esc_html(__('Team', 'victorious'));?>
                                </th>
                                <th class="f-player-fixture">
                                    <?php echo esc_html(__('Game', 'victorious'));?>
                                </th>
                            <?php endif; ?>
                            <th class="f-player-salary">
                                <?php echo esc_html(__('Salary', 'victorious'));?>
                            </th>
                            <th class="f-player-add"></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="f-row f-legend">
                <div class="f-draft-legend-key-title" data-role="expandable-heading" onclick="return jQuery.playerdraft.showIndicatorLegend()"><?php echo esc_html(__('Indicator legend', 'victorious'));?></div>
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
                    <i class="fa fa-lock"></i> <?php echo esc_html(__('Locks @', 'victorious'));?> <?php echo VIC_DateTranslate($aPool['startDate']); ?>
                    <span class="f-game_status_open"></span>
                </p>
            </div>
            <div class="f-salary-remaining">
                <div class="f-salary-remaining-container">
                    <span class="f-salary-remaining-amount" id="salaryRemaining">
                        <?php if ($aPool['salary_remaining'] > 0): ?>
                            <?php echo VIC_FormatMoney($aPool['salary_remaining'], "USD|$"); ?>
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
            <?php if (isset($is_lineup_scoccer_field) && $is_lineup_scoccer_field): ?>
                <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/game_soccer_field.php");?>
            <?php else:?>
                <ul>
                    <?php if ($aLineups != null && is_array($aLineups)): ?>
                        <?php foreach ($aLineups as $aLineup): ?>
                            <?php for ($i = 0; $i < $aLineup['total']; $i++): ?>
                                <li class="f-roster-position f-count-0 player-position-<?php echo esc_attr($aLineup['id']); ?>" <?php if ($aPool['is_round'] == 1): ?>style="padding-left: 0;background: none;"<?php endif; ?> data-position="<?php echo esc_attr($aLineup['id']); ?>">
                                    <div class="f-player-image" <?php if ($aPool['is_round'] == 1): ?>style="display: none;"<?php endif; ?>></div>
                                    <div class="f-position"><?php echo esc_html($aLineup['name']); ?>

                                        <span class="f-empty-roster-slot-instruction"><?php echo esc_html($aPool['is_horse_racing'] ? __('Add horse', 'victorious') : __('Add player', 'victorious'));?></span>
                                    </div>
                                    <div class="f-player"></div>
                                    <div class="f-salary">$0</div>
                                    <div class="f-fixture"></div>
                                    <a class="f-button f-tiny f-text">
                                        <i class="fa fa-minus-circle"></i>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php for ($i = 0; $i < $aLineups; $i++): ?>
                            <li class="f-roster-position f-count-0 player-position-0" <?php if ($aPool['is_round'] == 1): ?>style="padding-left: 0;background: none;"<?php endif; ?>>
                                <div class="f-player-image" <?php if ($aPool['is_round'] == 1): ?>style="display: none;"<?php endif; ?>></div>
                                <div class="f-position">

                                    <span class="f-empty-roster-slot-instruction"><?php echo esc_html($aPool['is_horse_racing'] ? __('Add horse', 'victorious') : __('Add player', 'victorious'));?></span>
                                </div>
                                <div class="f-player"></div>
                                <div class="f-salary">$0</div>
                                <div class="f-fixture"></div>
                                <a class="f-button f-tiny f-text">
                                    <i class="fa fa-minus-circle"></i>
                                </a>
                            </li>
                        <?php endfor; ?>
                    <?php endif; ?>
                </ul>
                <div class="f-row import-clear-button-container">
                    <button class="f-button f-mini f-text f-right" id="playerPickerClearAllButton" onclick="jQuery.playerdraft.clearAllPlayer()" type="button">
                        <small><i class="fa fa-minus-circle"></i> <?php echo esc_html(__('Clear all', 'victorious'));?></small>
                    </button>
                </div>
            <?php endif;?>
        </section>
        <footer class="f-">
            <div class="f-contest-entry-fee-container">
                <form id="formLineup">
                    <div id="enterForm.game_id.e" class="f-form_error"></div>
                    <input type="hidden" value="<?php echo esc_attr($league['leagueID']); ?>" name="leagueID">
                    <input type="hidden" value="<?php echo esc_attr($entry_number); ?>" name="entry_number">
                </form>
            </div>
            <div class="f-contest-enter-button-container">
                <input type="button" id="btnSubmit" value="<?php echo esc_html(__('Enter', 'victorious'));?>" class="f-button f-jumbo f-primary">
            </div>
        </footer>
    </section>
    <?php if ($is_soccer_field): ?>
        <div style="clear: both;"></div>
        <section id="custom-field-soccer" class="f field-soccer f-roster">
            <div class="img-field-background">
                <?php
                foreach ($aLineups as $aLineup):
                    $position_name = $aLineup['name'];
                    if (!$aLineup['name'] || (isset($aLineup['is_extra']) && !empty($aLineup['is_extra']))) {
                        $position_name = 'none';
                    }
                    ?>
                    <div class="group_position_<?php echo esc_attr($position_name); ?>">

                        <?php for ($i = 0; $i < $aLineup['total']; $i++): ?>


                            <div class="position-<?php echo esc_attr($aLineup['id']); ?> position-wrapper" style="<?php echo esc_attr($style_css)?>" data-field-pos="<?php echo esc_attr($aLineup['id']); ?>">
                                <div class="f-player-image f-no-image"></div>
                                <div class="lineup_group">
                                    <p class="team-salary"></p>
                                    <p class="pos-player"><?php echo esc_html($aLineup['name']); ?></p>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>
    <div id="dlgRugbyRemoveInjury" style="display: none">
        <h5><?php echo esc_html(__('Choose the place to move this player','victorious'));?></h5>
        <p style="margin-left: 50px;"><input type="radio" name="move_action" value="0" checked> <?php echo esc_html(__('IR Area','victorious'));?></p>
        <p style="margin-left: 50px;"><input type="radio" name="move_action" value="1"> <?php echo esc_html(__('Available Players Area','victorious'));?></p>
    </div>
    <div id="dlgRugbyRemoveInjuryArea" style="display: none">
        <h4><?php echo esc_html(__('Are you sure you want to remove this player out of IR list?','victorious'));?></h4>
    </div>
<?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW . 'dlg_info.php');?>
<script type="text/template" id="dataPlayers">
<?php echo json_encode($aPlayers); ?>
</script>
<script type="text/template" id="dataSalaryRemaining">
<?php echo esc_attr($aPool['salary_remaining']); ?>
</script>
<script type="text/template" id="dataPlayerIdPicks">
<?php echo json_encode($playerIdPicks); ?>
</script>
<script type="text/template" id="dataLeague">
<?php echo json_encode($league); ?>
</script>
<script type="text/template" id="dataFights">
<?php echo json_encode($aFights); ?>
</script>
<script type="text/template" id="dataPool">
<?php echo json_encode($aPool); ?>
</script>
<script type="text/template" id="dataIndicators">
<?php echo json_encode($indicators); ?>
</script>
<script type="text/template" id="player-restriction">
<?php echo (!empty($league['player_restriction']) ? $league['player_restriction'] : 0); ?>
</script>
<script type="text/template" id="is-soccer-flex">
<?php echo esc_attr($is_soccer_flex); ?>
</script>
<script type="text/template" id="is-soccer-field">
<?php echo esc_attr($is_soccer_field); ?>
</script>
<script type="text/template" id="extra-positions">
<?php echo json_encode($extra_positions); ?>
</script>
<script type="text/template" id="dataPositions">
    <?php echo json_encode($aPositions); ?>
</script>
<?php if($is_position_step):?>
    <script type="text/template" id="is-soccer">
    <?php echo esc_attr($is_lineup_scoccer_field); ?>
    </script>
    <script type="text/template" id="list-position-soccer">
    <?php echo !empty($list_postion_soccer) ? json_encode($list_postion_soccer) : json_encode(array()); ?>
    </script>
    <script type="text/template" id="field-image">
        <?php echo VICTORIOUS__PLUGIN_URL_IMAGE . 'field-bg.png'; ?>
    </script>
<?php endif;?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery.playerdraft.setData();
        resizeSoccerField();
        jQuery('.f-player-list-position-tabs li a:first').trigger('click');
        jQuery.playerdraft.initPlayerdraft();
    });
    jQuery(window).resize(function(){
        resizeSoccerField();
    });
    
    function resizeSoccerField()
    {
        var width = parseFloat(window.innerWidth);
        var height = '960px';
        if(width < 900)
        {
            height = (width * 1.6) + 'px';
        }
        jQuery("#custom-field-soccer .img-field-background").css('height', height);
        jQuery("#custom-field-soccer .f-roster").css('height', height);
    }
</script>
