<?php VIC_GetMessage(); ?>
<input type="hidden" id="mixing_orginazation_id" value="<?php echo esc_attr($aSportKeys[0]); ?>">
<input type="hidden" id="type_league" value="mixing">
<div class="f-contest-title-date">
    <h1 class="f-contest-title f-heading-styled"><?php echo esc_attr($league['name']); ?></h1>
    <div class="f-contest-date-container">
        <div class="f-contest-date-start-time">
            <?php echo esc_html(__('Contest starts', 'victorious'));?> <?php echo date('D M g H:i', strtotime($league['start_date'])); ?>
        </div>
    </div>
</div>
<ul class="f-contest-information-bar">
    <li class="f-contest-entries-league"><?php echo esc_html(__('Entries:', 'victorious'));?> 
        <b>
            <a class="f-lightboxLeagueEntries_show" href="#" onclick="return jQuery.global.ruleScoring(<?php echo esc_attr($league['leagueID']); ?>, 2)"><?php echo esc_attr($league['entries']); ?></a>
        </b> / <?php echo esc_attr($league['size']); ?>
        <span class="f-entries-player-league"> <?php echo esc_html(__('player league', 'victorious'));?></span>
    </li>
    <?php if (get_option('victorious_no_cash') == 0): ?>
        <li class="f-contest-entry-fee-container">
            <?php echo esc_html(__('Entry fee', 'victorious'));?>
            <span class="f-entryFee-value amount"><?php echo VIC_FormatMoney($league['entry_fee'], $balance_type['currency_code_symbol'], $balance_type['currency_position']); ?></span>
        </li>
        <li class="f-contest-prize-container  f-gameEntry-inner-entryFeeSelected">
            <?php echo esc_html(__('Prizes', 'victorious'));?>
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
    <p style="margin: 20px 0 10px;"><?php echo esc_html(__('Below is a list of games you have already entered for this event. Simply click on \'Import Picks\' to import your picks from that game.', 'victorious')) ?></p>
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="table table-striped table-bordered importpicks">
        <thead>
            <tr>
                <th><?php echo esc_html(__('Name', 'victorious')) ?></th>
                <th class="mobile"><?php echo esc_html(__('Opponent', 'victorious')) ?></th>
                <th><?php echo esc_html(__('Type', 'victorious')) ?></th>
                <th class="mobile"><?php echo esc_html(__('Entry Fee', 'victorious')) ?></th>
                <th><?php echo esc_html(__('Size', 'victorious')) ?></th>
                <th class="mobile"><?php echo esc_html(__('Structure', 'victorious')) ?></th>
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
                            <input type="button" value="<?php echo esc_html(__('Import Picks', 'victorious')) ?>"  class="btn btn-success" onclick="jQuery.playerdraft.addMultiPlayers('<?php echo esc_attr($otherLeague['player_id']) ?>')">
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<div class="f-pick-your-team">
    <?php foreach ($aAllFights as $index => $aFights): ?>
        <section data-role="fixture-picker" class="f-fixture-picker" style="margin-bottom: 5px;" data-org-id="<?php echo esc_attr($index); ?>">
            <?php if (!empty($aFights)): ?>
                <h1><?php echo esc_html(__('Players available from (click to filter):', 'victorious'));?></h1>
                <div class="f-fixture-picker-button-container">
                    <a class="f-button f-mini fixture-item <?php echo ($index == $first_sports_id) ? 's-sport-is-active' : '' ?>" onclick="jQuery.playerdraft.setActiveFixture(this);jQuery.playerdraft.mixingSelectTypeSport(this,<?php echo esc_attr($index); ?>, '<?php echo esc_html(__('Unlimited', 'victorious'));?>', '<?php echo esc_html(__('Add player', 'victorious'));?>')"><?php echo esc_html($aSports[$index]); ?></a>

                    <?php foreach ($aFights as $aFight): ?>
                        <a data-sport-id="<?php echo esc_attr($index); ?>" data-team-id1="<?php echo esc_attr($aFight['fighterID1']); ?>" data-team-id2="<?php echo esc_attr($aFight['fighterID2']); ?>" <?php if ($aFight['started'] == 0): ?>onclick="jQuery.playerdraft.setActiveFixture(this);
                                    return jQuery.playerdraft.mixingLoadPlayers(
                                            );"<?php endif; ?> class="f-button f-mini fixture-item <?php if ($aFight['started'] == 1): ?>f-is-disabled<?php endif; ?>">
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
                    <a class="f-button f-mini f-is-active fixture-item" onclick="jQuery.playerdraft.setActiveFixture(this);return jQuery.playerdraft.mixingLoadPlayers();">All</a>
        <?php foreach ($aRounds as $aRound): ?>
                        <a class="f-button f-mini fixture-item">
                            <span class="f-fixture-team-home"><?php echo esc_html($aRound['name']); ?></span>
                            <span class="f-fixture-start-time"><?php echo VIC_DateTranslate($aRound['startDate']); ?></span>
                        </a>
                <?php endforeach; ?>
                </div>
    <?php endif; ?>
        </section>

<?php endforeach; ?>
</div>
<div class="f-row">
    <section class="f-contest-player-list-container" data-role="player-list">
        <div class="f-row">
            <h1><?php echo esc_html(__('Available Players', 'victorious'));?></h1>
            <ul class="f-player-list-position-tabs f-tabs f-row">
                <li>
                    <a href="" data-id="" class="f-is-active" onclick="jQuery.playerdraft.setActivePosition(this);return jQuery.playerdraft.mixingLoadPlayers();"><?php echo esc_html(__('All', 'victorious')) ?></a>
                </li>
                <?php if ($aPositions != null): ?>
    <?php foreach ($aPositions[$first_sports_id] as $aPosition): ?>
                        <li>
                            <a href="" data-id="<?php echo esc_attr($aPosition['id']); ?>" onclick="jQuery.playerdraft.setActivePosition(this);return jQuery.playerdraft.mixingLoadPlayers();">
        <?php echo esc_html($aPosition['name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
<?php endif; ?>
                <li class="f-player-search">
                    <label class="f-is-hidden" for="mixing-player-search"><?php echo esc_html(__('Find a Player', 'victorious'));?></label>
                    <input type="search" id="mixing-player-search" placeholder="<?php echo esc_html(__('Find a player...', 'victorious'));?>" incremental="" autosave="fd-player-search" results="10">
                </li>
            </ul>
            <div data-role="scrollable-header">
                <table class="f-condensed f-player-list-table-header f-header-fields">
                    <thead>
                        <tr>
                            <th colspan="2" class="f-player-name table-sorting">
<?php echo esc_html(__('Name', 'victorious'));?>
                                <i class="f-icon f-sorted-asc fa fa-angle-up" style="display: inline;"></i>
                                <i class="f-icon f-sorted-desc fa fa-angle-down" style="display: inline;"></i>
                            </th>
                            <th class="f-player-played table-sorting">
                                <i class="f-icon f-sorted-asc fa fa-angle-up" style="display: inline;"></i>
                                <i class="f-icon f-sorted-desc fa fa-angle-down" style="display: inline;"></i>
<?php echo esc_html(__('Team', 'victorious'));?>
                            </th>
                            <th class="f-player-fixture table-sorting">
<?php echo esc_html(__('Game', 'victorious'));?>
                                <i class="f-icon f-sorted-asc fa fa-angle-up" style="display: inline;"></i>
                                <i class="f-icon f-sorted-desc fa fa-angle-down" style="display: inline;"></i>
                            </th>
                            <th class="f-player-salary table-sorting">
                                <i class="f-icon f-sorted-asc fa fa-angle-up" style="display: inline;"></i>
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
                            <th class="f-player-fppg">
<?php echo esc_html(__('FPPG', 'victorious'));?>
                            </th>
                            <th class="f-player-played">
<?php echo esc_html(__('Team', 'victorious'));?>
                            </th>
                            <th class="f-player-fixture">
<?php echo esc_html(__('Game', 'victorious'));?>
                            </th>
                            <th class="f-player-salary">
<?php echo esc_html(__('Salary', 'victorious'));?>
                            </th>
                            <th class="f-player-add"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="f-row f-legend">
                <div class="f-draft-legend-key-title" data-role="expandable-heading" onclick="return jQuery.playerdraft.showIndicatorLegend()"><?php echo esc_html(__('Indicator legend', 'victorious'));?></div>
                <div class="f-clear"></div>
                <div class="f-draft-legend-key-content">
                    <ul>
                        <?php foreach ($indicators[$first_sports_id] as $indicator): ?>
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
                                case 'DtD':
                                    $indicatorClass = 'f-player-badge f-player-badge-injured-possible';
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
                    <i class="fa fa-lock"></i> <?php echo esc_html(__('Locks @', 'victorious'));?> <?php echo date('D M g H:i', strtotime($league['start_date'])); ?>
                    <span class="f-game_status_open"></span>
                </p>
            </div>
            <div class="f-salary-remaining">
                <div class="f-salary-remaining-container">
                    <span class="f-salary-remaining-amount" id="salaryRemaining">
                        <?php if ($first_salary > 0): ?> 
                            <?php  echo VIC_FormatMoney($first_salary); ?>
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
                <?php if ($aLineups != null && is_array($aLineups)): ?>
                <?php $first_sport_id = array_keys($aLineups);$first_sport_id = $first_sport_id[0];?>
                    <?php foreach ($aLineups as $sport_id=>$aLineup): ?>
                        <?php foreach ($aLineup as $item): ?>
                            <?php for ($i = 0; $i < $item['total']; $i++): ?>
                                <li class="cls-sport-<?php echo esc_attr($sport_id); ?> f-roster-position f-count-0 player-position-<?php echo esc_attr($item['id']); ?>" <?php if (isset($aPool['is_round']) && $aPool['is_round'] == 1): ?>style="padding-left: 0;background: none;"<?php endif; ?> <?php if($sport_id != $first_sports_id){echo "style='display:none'";} ?>>
                                    <div class="f-player-image" <?php if (isset($aPool['is_round']) && $aPool['is_round'] == 1): ?>style="display: none;"<?php endif; ?>></div>
                                    <div class="f-position"><?php echo esc_html($item['name']); ?>
                                        <span class="f-empty-roster-slot-instruction"><?php echo esc_html(__('Add player', 'victorious'));?></span>
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
                    <?php endforeach; ?>
                <?php else: ?>  
                    <?php for ($i = 0; $i < $aLineups; $i++): ?> 
                        <li class="f-roster-position f-count-0 player-position-0" <?php if ($aPool[0]['is_round'] == 1): ?>style="padding-left: 0;background: none;"<?php endif; ?>>
                            <div class="f-player-image" <?php if ($aPool[0]['is_round'] == 1): ?>style="display: none;"<?php endif; ?>></div>
                            <div class="f-position">
                                <span class="f-empty-roster-slot-instruction"><?php echo esc_html(__('Add player', 'victorious'));?></span>
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
            <div class="f-contest-enter-button-container">
                <input type="button" data-nav-warning="off" data-value="next" id="btnNextSport" value="<?php echo esc_html(__('Next', 'victorious'));?>" class="f-button f-jumbo f-primary" onclick="jQuery.playerdraft.nextMixingSport()">
            </div>
            <div class="f-row import-clear-button-container">
                <button class="f-button f-mini f-text f-right" id="playerPickerClearAllButton" onclick="jQuery.playerdraft.clearAllPlayer()" type="button">
                    <small><i class="fa fa-minus-circle"></i> <?php echo esc_html(__('Clear all', 'victorious'));?></small>
                </button>
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
<?php echo esc_attr($salary_remaining); ?>
</script>
<script type="text/template" id="dataPlayerIdPicks">
<?php echo json_encode($playerIdPicks); ?>
</script>
<script type="text/template" id="dataLeague">
<?php echo json_encode($league); ?>
</script>
<script type="text/template" id="dataFights">
<?php echo json_encode($aAllFights); ?>
</script>
<script type="text/template" id="dataPool">
<?php echo json_encode($aPool); ?>
</script>
<script type="text/template" id="dataIndicators">
<?php echo json_encode($indicators); ?>
</script>
<script type="text/template" id="dataLineups">
<?php echo json_encode($aLineups); ?>
</script>
<script type="text/template" id="dataPositions">
    <?php echo json_encode($aPositions); ?>
</script>
<script type="text/template" id="player-restriction">
    <?php echo esc_attr($league['player_restriction']); ?>
</script>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery.playerdraft.mixSetData();
        jQuery.playerdraft.initPlayerdraft();
    })
</script>