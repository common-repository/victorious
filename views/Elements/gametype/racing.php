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
            <span class="f-entryFee-value amount"><?php echo esc_attr($league['entry_fee'] == 0 ? __("Free", "victorious") : VIC_FormatMoney($league['entry_fee'])); ?></span>
        </li>
        <li class="f-contest-prize-container  f-gameEntry-inner-entryFeeSelected">
            <?php echo esc_html(__('Prizes', 'victorious'));?>:
            <span class="f-content-prize-amount">
                <a class="f-lightboxPrizeList_show" href="#"  onclick="return jQuery.global.ruleScoring(<?php echo esc_attr($league['leagueID']); ?>, 3)">
                    <?php echo VIC_FormatMoney($league['prizes']); ?>
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
    <table class="table table-striped table-bordered importpicks">
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
        <?php if (!empty($aRounds)): ?>
            <h1><?php echo esc_html(__('Players available from (click to filter):', 'victorious'));?></h1>
            <div class="f-fixture-picker-button-container">
                <a class="f-button f-mini f-is-active fixture-item" onclick="jQuery.playerdraft.setActiveFixture(this);return jQuery.playerdraft.loadPlayers();">
                    <?php echo esc_html(__('All Race Weekends', 'victorious'));?>
                </a>
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
<div class="f-row sport_racing">
    <section class="f-contest-player-list-container" data-role="player-list">
        <div class="f-row">
            <h1>
                <?php echo esc_html(__('Available Racers', 'victorious'));?>
                <?php if ($league["is_live"] && $league["trade_player"]): ?>
                    <br/>
                    <?php echo esc_html(__('Note: You are only able to change one player', 'victorious'));?>
                <?php endif; ?>
            </h1>
            <ul class="f-player-list-position-tabs f-tabs f-row">
                <?php if ($aPositions != null): ?>
                    <?php foreach ($aPositions as $k => $aPosition):?>
                        <li>
                            <a class="<?php echo esc_attr($k == 0 ? "f-is-active" : ""); ?>" href="" data-id="<?php echo esc_attr($aPosition['id']); ?>" onclick="jQuery.playerdraft.setActivePosition(this);return jQuery.playerdraft.loadPlayers();">
                                <?php echo esc_html($aPosition['name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>   
                <?php endif; ?>
                <li class="f-player-search">
                    <label class="f-is-hidden" for="player-search"><?php echo esc_html(__('Find a racer', 'victorious'));?></label>
                    <input type="search" id="player-search" placeholder="<?php echo esc_html(__('Find a racer...', 'victorious'));?>" incremental="" autosave="fd-player-search" results="10">
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
                                $htmlIndicator = '';
                                if(!empty($aPlayer['indicator_alias']))
                                {
                                    switch ($aPlayer['indicator_alias'])
                                    {
                                        case 'IR':
                                            $htmlIndicator = '<span class="f-player-badge f-player-badge-injured-out">IR</span>';
                                            break;
                                        case 'O':
                                            $htmlIndicator = '<span class="f-player-badge f-player-badge-injured-out">O</span>';
                                            break;
                                        case 'D':
                                            $htmlIndicator = '<span class="f-player-badge f-player-badge-injured-possible">D</span>';
                                            break;
                                        case 'Q':
                                            $htmlIndicator = '<span class="f-player-badge f-player-badge-injured-possible">Q</span>';
                                            break;
                                        case 'P':
                                            $htmlIndicator = '<span class="f-player-badge f-player-badge-injured-probable">P</span>';
                                            break;
                                        case 'NA':
                                            $htmlIndicator = '<span class="f-player-badge f-player-badge-injured-out">NA</span>';
                                            break;
                                    }
                                }
                        ?>
                            <tr class="f-pR" data-role="player" id="player_number_<?php echo esc_attr($aPlayer['id']);?>" data-id="<?php echo esc_attr($aPlayer['id']);?>" data-position="<?php echo esc_attr($aPlayer['position_id']);?>" data-team="<?php echo esc_attr($aPlayer['team_id']);?>" data-player_name="<?php echo esc_attr($aPlayer['name']);?>">
                                <td class="f-player-position">
                                    <?php echo !empty($aPlayer['position']) ? esc_html($aPlayer['position']) : '';?>
                                </td>
                                <td class="f-player-name">
                                    <div onclick="jQuery.playerdraft.playerInfo(<?php echo esc_attr($aPlayer['id']);?>)">
                                        <?php echo esc_html($aPlayer['name'].$htmlIndicator);?>
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
            <div class="racing_field">
                <?php foreach ($aLineups as $aLineup): ?>
                    <?php for ($i = 0; $i < $aLineup['total']; $i++): ?>
                        <div class="f-roster-position player-position-<?php echo esc_attr($aLineup['id']); ?> <?php if($aLineup['is_constructor']):?>f-constructor<?php endif;?>" data-constructor="<?php echo esc_attr($aLineup['is_constructor']);?>" data-position="<?php echo esc_attr($aLineup['id']); ?>">
                            <?php if($aLineup['is_constructor']):?>
                                <div class="f-player-image f-no-image-consturctor"></div>
                            <?php else:?>
                                <div class="f-player-image f-no-image"></div>
                                <div class="f-player-info">
                                    <div class="f-position">
                                        <span class="f-empty-roster-slot-instruction"><?php echo esc_html(__('Add Driver', 'victorious'));?></span>
                                    </div>
                                    <div class="f-player"></div>
                                    <div class="f-team"></div>
                                    <div class="f-salary">$0</div>
                                    <div class="f-fixture"></div>
                                    <div class="clear"></div>
                                </div>
                            <?php endif;?>
                            <a class="f-button f-tiny f-text">
                                <i class="fa fa-minus-circle"></i>
                            </a>
                        </div>
                    <?php endfor; ?>
                <?php endforeach; ?>
            </div>
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
    jQuery(window).load(function(){
        jQuery('.f-player-list-position-tabs li:first a').trigger('click');
    })
</script>
