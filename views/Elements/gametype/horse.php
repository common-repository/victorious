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
            <a class="f-lightboxLeagueEntries_show" href="#" onclick="return jQuery.global.ruleScoring(<?php echo esc_attr($league['leagueID']); ?>, 2)"><?php echo esc_attr($league['entries']); ?></a>
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
<?php if ($otherLeagues != null && get_option('victorious_show_import_pick') == 1): ?>
    <p style="margin: 20px 0 10px;"><?php echo esc_html(__('Below is a list of games you have already entered for this event. Simply click on \'Import Picks\' to import your picks from that game.', 'victorious'))?></p>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="table table-striped table-bordered importpicks">
        <thead>
            <tr>
                <th><?php echo esc_html(__('Name', 'victorious'))?></th>
                <th class="mobile"><?php echo esc_html(__('Opponent', 'victorious'))?></th>
                <th><?php echo esc_html(__('Type', 'victorious'))?></th>
                <th class="mobile"><?php echo esc_html(__('Entry Fee', 'victorious'))?></th>
                <th><?php echo esc_html(__('Size', 'victorious'))?></th>
                <th class="mobile"><?php echo esc_html(__('Structure', 'victorious'))?></th>
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
                            <input type="button" value="<?php echo esc_html(__('Import Picks', 'victorious'))?>"  class="btn btn-success" onclick="jQuery.playerdraft.addMultiPlayers('<?php echo esc_attr($otherLeague['player_id'])?>')">
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
                        <span class="f-fixture-team-home"><?php echo esc_html(__("H:", "victorious")." ".$aFight['nickName1']); ?></span>
                        @
                        <span class="f-fixture-team-away"><?php echo esc_html(__("A:", "victorious")." ".$aFight['nickName2']); ?></span>
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
<div class="f-row">
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
                <li>
                    <a href="" data-id="" class="f-is-active" onclick="jQuery.playerdraft.setActivePosition(this);return jQuery.playerdraft.loadPlayers();"><?php echo esc_html(__('All', 'victorious')) ?></a>
                </li>
                <?php if ($aRounds != null && $aPool['round_position']): ?>
                    <?php foreach ($aRounds as $aRound):?>
                        <li>
                            <a href="" data-round_squad="<?php echo esc_attr($aRound['squad']); ?>" onclick="jQuery.playerdraft.setActivePosition(this);return jQuery.playerdraft.loadPlayers();">
                                <?php echo esc_html($aRound['name']); ?>
                            </a>
                        </li>
                    <?php endforeach;?>
                <?php endif; ?>
                <li class="f-player-search">
                    <label class="f-is-hidden" for="player-search"><?php echo esc_html(__('Find a Horse', 'victorious'));?></label>
                    <input type="search" id="player-search" placeholder="<?php echo esc_html(__('Find a horse...', 'victorious'));?>" incremental="" autosave="fd-player-search" results="10">
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
                            <th class="f-player-salary">
                                <?php echo esc_html(__('Salary', 'victorious'));?>
                            </th>
                            <th class="f-player-add"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            usort($aPlayers, function($a, $b) {
                                return $b['salary'] - $a['salary'];
                            });
                            foreach ($aPlayers as $aPlayer):
                        ?>
                            <tr class="f-pR" data-role="player" id="player_number_<?php echo esc_attr($aPlayer['id']);?>" data-id="<?php echo esc_attr($aPlayer['id']);?>" data-position="<?php echo esc_attr($aPlayer['position_id']);?>" data-team="<?php echo esc_attr($aPlayer['team_id']);?>" data-player_name="<?php echo esc_attr($aPlayer['name']);?>">
                                <td class="f-player-position"><?php echo !empty($aPlayer['race']) ? $aPlayer['race'] : '';?></td>
                                <td class="f-player-name">
                                    <div onclick="jQuery.playerdraft.playerInfo(<?php echo esc_attr($aPlayer['id']);?>)">
                                        <?php echo esc_html($aPlayer['name'].VIC_PlayerIndicator($aPlayer['indicator_alias'], $aPlayer['is_pitcher']));?>
                                    </div>
                                </td>
                                <td style="display: none"><?php echo esc_html($aPlayer['salary']);?></td>
                                <td class="f-player-salary"><?php echo VIC_FormatMoney($aPlayer['salary']);?></td>
                                <td class="f-player-add">
                                    <a class="f-button f-tiny f-text f-player-add-button" id="buttonAdd<?php echo esc_attr($aPlayer['id']);?>" onclick="jQuery.playerdraft.addPlayer(<?php echo esc_attr($aPlayer['id']);?>)" style="display: none;">
                                        <i class="fa fa-plus-circle"></i>
                                    </a>
                                    <a class="f-button f-tiny f-text f-player-remove-button" id="buttonRemove<?php echo esc_attr($aPlayer['id']);?>" onclick="jQuery.playerdraft.clearPlayer(<?php echo esc_attr($aPlayer['id']);?>)" style="display: none;">
                                        <i class="fa fa-minus-circle"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
            <div class="f-row f-legend">
                <div class="f-draft-legend-key-title" data-role="expandable-heading" onclick="return jQuery.playerdraft.showIndicatorLegend()"><?php echo esc_html(__('Indicator legend', 'victorious'));?></div>
                <div class="f-clear"></div>
                <div class="f-draft-legend-key-content">
                    <ul>
                        <?php foreach ($indicators as $indicator): ?>
                            <?php
                            $indicatorClass = '';
                            switch ($indicator['alias']) {
                                case 'IR':
                                    $indicatorClass = 'f-player-badge f-player-badge-injured-out';
                                    break;
                                case 'O':
                                    $indicatorClass = 'f-player-badge f-player-badge-injured-out';
                                    break;
                                case 'D':
                                    $indicatorClass = 'f-player-badge f-player-badge-injured-possible';
                                    break;
                                case 'Q':
                                    $indicatorClass = 'f-player-badge f-player-badge-injured-possible';
                                    break;
                                case 'P':
                                    $indicatorClass = 'f-player-badge f-player-badge-injured-probable';
                                    break;
                                case 'NA':
                                    $indicatorClass = 'f-player-badge f-player-badge-injured-out';
                                    break;
                                case 'S':
                                    $indicatorClass = 'f-player-badge f-player-badge-injured-possible';
                                    break;
                            }
                            ?>
                            <li>
                                <span class="<?php echo esc_attr($indicatorClass); ?>">
                                    <?php echo esc_html($indicator['alias']); ?>
                                </span> 
                                <?php echo esc_html($indicator['name']); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
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
                            <?php echo VIC_FormatMoney($aPool['salary_remaining']); ?>
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
                <?php for ($i = 1; $i <= count($aRounds); $i++): ?> 
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
            </ul>
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
                <input id="btnSubmit" value="<?php echo esc_html(__('Enter', 'victorious'));?>" class="f-button f-jumbo f-primary">
            </div>
        </footer>
    </section>
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
<script type="text/template" id="dataPositions">
    <?php echo json_encode($aPositions); ?>
</script>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery.playerdraft.setData();
        jQuery.playerdraft.initPlayerdraft();
    })
</script>
