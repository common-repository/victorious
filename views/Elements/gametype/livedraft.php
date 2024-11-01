<?php VIC_GetMessage(); ?>
<input type="hidden" id="type_league" value="single">
<input type="hidden" id="edit-injury-players" value="<?php echo esc_attr($edit_injury_players);?>">
<input type="hidden" id="user_id" value="<?php echo VIC_GetUserId();?>">
<input type="hidden" id="current_turn" value="<?php echo esc_attr($data['current_turn']);?>">
<input type="hidden" id="entry_link" value="<?php echo VICTORIOUS_URL_ENTRY.$league['leagueID'];?>">
<input type="hidden" id="live_entries_link" value="<?php echo VICTORIOUS_URL_MY_LIVE_ENTRIES;?>">
<input type="hidden" id="minute_change_player" value="<?php echo esc_attr($league['live_draft_minute_change_player']);?>">
<div class="f-contest-title-date">
    <h1 class="f-contest-title f-heading-styled"><?php echo esc_html($league['name']); ?></h1>
    <div class="f-contest-date-container">
        <div class="f-contest-date-start-time">
            <?php echo esc_html(__('Contest starts', 'victorious'));?> <?php echo VIC_DateTranslate($league['live_draft_start_date']); ?>
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
            <span class="f-entryFee-value amount"><?php echo esc_html($league['entry_fee'] == 0 ? __("Free", "victorious") : VIC_FormatMoney($league['entry_fee'], null, null, $league['balance_type'])); ?></span>
        </li>
        <li class="f-contest-prize-container  f-gameEntry-inner-entryFeeSelected">
            <?php echo esc_html(__('Prizes', 'victorious'));?>:
            <span class="f-content-prize-amount">
                <a class="f-lightboxPrizeList_show" href="#"  onclick="return jQuery.global.ruleScoring(<?php echo esc_attr($league['leagueID']); ?>, 3)">
                    <?php echo VIC_FormatMoney($league['prizes'], null, null, $league['balance_type']); ?>
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
        <?php if (!empty($aFights)): ?>
            <h1><?php echo esc_html(__('Players available from (click to filter):', 'victorious'));?></h1>
            <div class="f-fixture-picker-button-container">
                <a class="f-button f-mini f-is-active fixture-item" onclick="jQuery.livedraft.setActiveFixture(this);return jQuery.livedraft.loadPlayers();"><?php echo esc_html(__('All Games', 'victorious'));?></a>
                <?php foreach ($aFights as $aFight):
                    $home_team = $aFight['home_team'];
                    $away_team = $aFight['away_team'];
                ?>
                    <a data-team-id1="<?php echo esc_attr($aFight['fighterID1']);?>" data-team-id2="<?php echo esc_attr($aFight['fighterID2']);?>" <?php if ($aFight['started'] == 0 || $league["trade_player"] == 1): ?>onclick="jQuery.livedraft.setActiveFixture(this);return jQuery.livedraft.loadPlayers();"<?php endif; ?> class="f-button f-mini fixture-item <?php if ($aFight['started'] == 1 && $league["trade_player"] == 0): ?>f-is-disabled<?php endif;?>">
                        <span class="f-fixture-team-home"><?php echo esc_html(__("A:", "victorious")." ".$away_team['nickName']); ?></span>
                        @
                        <span class="f-fixture-team-away"><?php echo esc_html(__("H:", "victorious")." ".$home_team['nickName']); ?></span>
                        <span class="f-fixture-start-time"><?php echo VIC_DateTranslate($aFight['startDate']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($aRounds)): ?>
            <h1><?php echo esc_html(__('Players available from (click to filter):', 'victorious'));?></h1>
            <div class="f-fixture-picker-button-container">
                <a class="f-button f-mini f-is-active fixture-item" onclick="jQuery.livedraft.setActiveFixture(this);return jQuery.livedraft.loadPlayers();"><?php echo esc_html(__('All Games', 'victorious'));?></a>
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
    <div class="draft_turn_by_turn">
        <?php if($action == 3):?>
            <div class="pull-left">
                <?php echo esc_html(__('User draft turn','victorious'));?>:
                <span id="user_draft_turn"><?php echo isset($data['current_turn_user']) ? $data['current_turn_user'] : "";?></span>
            </div>
            <div class="pull-right">
                <?php echo esc_html(__('Time remaining to draft player','victorious'));?>
                <span id="change-player-countdown"></span>
            </div>
            <div class="clear"></div>
            <div>
                <?php echo esc_html(__('Next draft turn','victorious'));?>:
                <span id="next_draft_turn"><?php echo isset($data['next_turn_user']) ? $data['next_turn_user'] : "";?></span>
            </div>
        <?php elseif($action == 2): ?>
            <div style="font-size: 20px;text-align: center;">
                <p><?php echo sprintf(esc_html(__('You can request to change %s player(s)','victorious')), $league['waiver_wire_player_quantity']); ?></p>
                <p><?php echo esc_html(__('If you have already requested to change players in this week, your old request will be replaced.', 'victorious'));?></p>
            </div>
        <?php endif;?>
    </div>
    <section class="f-contest-player-list-container" data-role="player-list">
        <div class="f-row">
            <h1>
                <?php if($action == 4):?>
                    <?php echo esc_html(__('Bench Players', 'victorious'));?>
                <?php else:?>
                    <?php echo esc_html(__('Available Players', 'victorious'));?>
                <?php endif;?>
                <?php if ($league["trade_player"]): ?>
                    <br/>
                    <?php echo esc_html(__('Note: You are only able to change one player', 'victorious'));?>
                <?php endif; ?>
            </h1>
            <ul class="f-player-list-position-tabs f-tabs f-row">
                <li>
                    <a href="javascript:void(0)" data-id="" class="f-is-active" onclick="jQuery.livedraft.setActivePosition(this);return jQuery.livedraft.loadPlayers();"><?php echo esc_html(__('All', 'victorious')) ?></a>
                </li>
                <?php if ($aPositions != null): ?>
                    <?php
                    foreach ($aPositions as $aPosition):
                        if (isset($aPosition['is_extra']) && !empty($aPosition['is_extra'])) {
                            continue;
                        }
                        ?>
                        <li>
                            <a href="javascript:void(0)" data-id="<?php echo esc_attr($aPosition['id']);?>" onclick="jQuery.livedraft.setActivePosition(this);return jQuery.livedraft.loadPlayers();">
                                <?php echo esc_html($aPosition['name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                     <?php if($edit_injury_players || !empty($list_injury_players)): ?>
                        <li>
                            <a href="javascript:void(0)" data-id="IR" onclick="jQuery.livedraft.setActivePosition(this);return jQuery.livedraft.loadPlayers();"> <?php echo esc_html(__('IR','victorious')) ?></a>
                        </li>
                     <?php endif; ?>   
                <?php endif; ?>
                <li class="f-player-search">
                    <label class="f-is-hidden" for="player-search"><?php echo esc_html(__('Find a Player', 'victorious'));?></label>
                    <input type="search" id="player-search" placeholder="<?php echo esc_html(__('Find a player...', 'victorious'));?>" incremental="" autosave="fd-player-search" results="10">
                </li>
            </ul>
            <div data-role="scrollable-header">
                <table class="f-condensed f-player-list-table-header f-header-fields">
                    <thead>
                        <tr>
                            <th colspan="2" class="f-player-name table-sorting" style="width: 49%;">
                                <?php echo esc_html(__('Name', 'victorious'));?>
                                <i class="f-icon f-sorted-asc fa fa-angle-up" style="display: none;"></i>
                                <i class="f-icon f-sorted-desc fa fa-angle-down" style="display: none;"></i>
                            </th>
                            <?php if (!$league['only_playerdraft']): ?>
                                <th class="f-player-played table-sorting" style="width: 108px;">
                                    <i class="f-icon f-sorted-asc fa fa-angle-up" style="display: none;"></i>
                                    <i class="f-icon f-sorted-desc fa fa-angle-down" style="display: none;"></i>
                                    <?php echo esc_html(__('Team', 'victorious'));?>
                                </th>
                                <th class="f-player-fixture table-sorting">
                                    <?php echo esc_html(__('Game', 'victorious'));?>
                                    <i class="f-icon f-sorted-asc fa fa-angle-up" style="display: none;"></i>
                                    <i class="f-icon f-sorted-desc fa fa-angle-down" style="display: none;"></i>
                                </th>
                            <?php endif; ?>
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
                            <?php if (!$league['only_playerdraft']): ?>
                                <th class="f-player-played">
                                    <?php echo esc_html(__('Team', 'victorious'));?>
                                </th>
                                <th class="f-player-fixture">
                                    <?php echo esc_html(__('Game', 'victorious'));?>
                                </th>
                            <?php endif; ?>
                            <th class="f-player-add"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            foreach ($aPlayers as $aPlayer): 
                                $htmlIndicator = '';
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
                                    case 'DtD':
                                        $htmlIndicator = '<span class="f-player-badge f-player-badge-injured-out">DtD</span>';
                                        break;
                                    case 'S':
                                        $htmlIndicator = '<span class="f-player-badge f-player-badge-injured-possible">S</span>';
                                        break;
                                }
                        ?>
                            <tr class="f-pR" data-role="player" id="player_number_<?php echo esc_attr($aPlayer['id']);?>" data-position="<?php echo esc_attr($aPlayer['position_id']);?>" data-team="<?php echo esc_attr($aPlayer['team_id']);?>" data-player_name="<?php echo esc_attr($aPlayer['name']);?>" data-indicator_id="<?php echo esc_attr($aPlayer['indicator_id']);?>">
                                <?php if($league['gameType'] != 'GOLFSKIN'):?>
                                    <td class="f-player-position"><?php echo esc_html($league['no_position'] == 1 ? "&nbsp;" : $aPlayer['position']);?></td>
                                <?php endif;?>
                                <td class="f-player-name">
                                    <div onclick="jQuery.livedraft.playerInfo(<?php echo esc_attr($aPlayer['id']);?>)">
                                        <?php echo esc_html($aPlayer['name'].$htmlIndicator);?>
                                    </div>
                                </td><td class="f-player-played"><?php echo esc_html($aPlayer['myteam']);?></td>
                                <td class="f-player-fixture">
                                    <?php if($aPlayer['teamID2'] == $aPlayer['team_id']):?>
                                        <b><?php echo esc_html($aPlayer['team2']);?></b>@<?php echo esc_html($aPlayer['team1']);?>
                                    <?php else:?>
                                        <?php echo esc_html($aPlayer['team2']);?>@<b><?php echo esc_html($aPlayer['team1']);?></b>
                                    <?php endif;?>
                                </td>
                                <td class="f-player-add">
                                    <a class="f-button f-tiny f-text f-player-add-button" id="buttonAdd<?php echo esc_attr($aPlayer['id']);?>" onclick="jQuery.livedraft.addPlayer(<?php echo esc_attr($aPlayer['id']);?>)" style="display: none;">
                                        <i class="fa fa-plus-circle"></i>
                                    </a>
                                    <a class="f-button f-tiny f-text f-player-remove-button" id="buttonRemove<?php echo esc_attr($aPlayer['id']);?>" onclick="jQuery.livedraft.clearPlayer(<?php echo esc_attr($aPlayer['id']);?>)" style="display: none;">
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
            <div id="userDraftRoomData"></div>
            <div class="f-lineup-text-container" style="margin-top:5px;">
                <p class="f-lineup-lock-message">
                    <i class="fa fa-lock"></i> <?php echo esc_html(__('Locks @', 'victorious'));?> <?php echo VIC_DateTranslate($league['startDate']); ?>
                    <span class="f-game_status_open"></span>
                </p>
            </div>
        </header>
        <footer class="f-">
            <div class="f-contest-entry-fee-container">
                <form id="formLineup" enctype="multipart/form-data" method="POST" action="<?php echo VICTORIOUS_URL_GAME;?>">
                    <div id="enterForm.game_id.e" class="f-form_error"></div>
                    <input type="hidden" value="1" name="submitPicks">
                    <input type="hidden" value="<?php echo esc_attr($league['leagueID']);?>" name="leagueID">
                    <input type="hidden" value="<?php echo esc_attr($entry_number);?>" name="entry_number">
                    <input type="hidden" value="<?php echo session_id();?>" name="session_id">
                    <input type="hidden" value="<?php echo esc_attr($action);?>" name="live_draft_action">
                    <input type="hidden" value="1" name="submitPicks">
                </form>
            </div>
            <div class="f-contest-enter-button-container">
                <input type="submit" data-nav-warning="off" id="btnSubmit" value="<?php echo esc_attr($action == 2 ? esc_html(__('Change', 'victorious')) : esc_html(__('Pick', 'victorious')));?>" class="f-button f-jumbo f-primary" onclick="jQuery.livedraft.submitData()">
            </div>
            <div class="public_message" style="display: none;"></div>
        </footer>
        <section class="f-roster">
            <ul>
                <?php if ($aLineups != null && is_array($aLineups)): ?>
                    <?php foreach ($aLineups as $aLineup): ?>
                        <?php for ($i = 0; $i < $aLineup['total']; $i++): ?>
                            <li class="f-roster-position f-count-0 player-position-<?php echo esc_attr($aLineup['id']);?>" <?php if ($league['is_round'] == 1): ?>style="padding-left: 0;background: none;"<?php endif; ?> data-position="<?php echo esc_attr($aLineup['id']);?>">
                                <div class="f-player-image" <?php if ($league['is_round'] == 1): ?>style="display: none;"<?php endif; ?>></div>
                                <div class="f-position"><?php echo esc_html($aLineup['name']); ?>

                                    <span class="f-empty-roster-slot-instruction"><?php echo esc_html(__('Add player', 'victorious'));?></span>
                                </div>
                                <div class="f-player"></div>
                                <div class="f-fixture"></div>
                                <a class="f-button f-tiny f-text">
                                    <i class="fa fa-minus-circle"></i>
                                </a>
                            </li>
                        <?php endfor; ?>
                    <?php endforeach; ?>
                <?php else: ?>  
                    <?php for ($i = 0; $i < $aLineups; $i++): ?> 
                        <li class="f-roster-position f-count-0 player-position-0" <?php if ($league['is_round'] == 1): ?>style="padding-left: 0;background: none;"<?php endif; ?>>
                            <div class="f-player-image" <?php if ($league['is_round'] == 1): ?>style="display: none;"<?php endif; ?>></div>
                            <div class="f-position">

                                <span class="f-empty-roster-slot-instruction"><?php echo esc_html(__('Add player', 'victorious'));?></span>
                            </div>
                            <div class="f-player"></div>
                            <div class="f-fixture"></div>
                            <a class="f-button f-tiny f-text">
                                <i class="fa fa-minus-circle"></i>
                            </a>
                        </li>
                    <?php endfor; ?>
                <?php endif; ?>
                <?php if($action != 4):?>
                    <?php for($i = 0; $i < $league['live_draft_bench_quantity']; $i++):?>
                        <li class="f-roster-position f-count-0 player-position-0" data-position="0">
                            <div class="f-player-image" <?php if ($league['is_round'] == 1): ?>style="display: none;"<?php endif; ?>></div>
                            <div class="f-position"><?php echo esc_html(__('Bench', 'victorious'));?>
                                <span class="f-empty-roster-slot-instruction"><?php echo esc_html(__('Add player', 'victorious'));?></span>
                            </div>
                            <div class="f-player"></div>
                            <div class="f-fixture"></div>
                            <a class="f-button f-tiny f-text">
                                <i class="fa fa-minus-circle"></i>
                            </a>
                        </li>
                    <?php endfor;?>
                <?php endif;?>
            </ul>
            <?php if(!$edit_injury_players && $action != 3 && $action != 2 && $action != 4): ?>
                <div class="f-row import-clear-button-container">
                    <button class="f-button f-mini f-text f-right" id="playerPickerClearAllButton" onclick="jQuery.livedraft.clearAllPlayer()" type="button">
                        <small><i class="fa fa-minus-circle"></i> <?php echo esc_html(__('Clear all', 'victorious'));?></small>
                    </button>
                </div>
            <?php endif; ?>
        </section>
    </section>
</div>
    <div id="dlgRemoveInjury" style="display: none">
        <h5><?php echo esc_html(__('Choose the place to move this player','victorious')) ?></h5>
        <p style="margin-left: 50px;"><input type="radio" name="move_action" value="0" checked> <?php echo esc_html(__('IR Area','victorious'));?></p>
        <p style="margin-left: 50px;"><input type="radio" name="move_action" value="1"> 
            <?php echo esc_html(__('Available Players Area','victorious'));?>
        </p>
    </div>
    <div id="dlgRemoveInjuryArea" style="display: none">
        <h4><?php echo esc_html(__('Are you sure you want to remove this player out of IR list?','victorious')) ?></h4>
    </div>
<?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW . 'dlg_info.php');?>
<script type="text/template" id="dataPlayers">
<?php echo json_encode($aPlayers); ?>
</script>
<script type="text/template" id="dataSalaryRemaining">
0
</script>
<script type="text/template" id="dataPlayerIdPicks">
<?php echo !empty($playerIdPicks) ? json_encode($playerIdPicks) : ''; ?>
</script>
<script type="text/template" id="dataLeague">
<?php echo json_encode($league); ?>
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
<script type="text/template" id="limit-players">
    <?php echo esc_attr($action == 3 ? 1 : $limit_players); ?>
</script>
<script type="text/template" id="position-injury-players">
    <?php echo json_encode($allow_injury_position); ?>
</script>
<script type="text/template" id="injury-players">
    <?php echo !empty($list_injury_players) ? json_encode($list_injury_players) : json_encode(array()); ?>
</script>
<script type="text/template" id="time-remaining">
    <?php echo esc_attr($time_remaning); ?>
</script>
<script type="text/template" id="allow-waiver-wire">
    <?php echo esc_attr($action == 2 && $allow_waiver_wire); ?>
</script>
<script type="text/template" id="bench_quantity">
    <?php echo esc_attr($league['live_draft_bench_quantity']); ?>
</script>
<script type="text/template" id="except_player_ids">
    <?php echo json_encode($except_player_ids); ?>
</script>
<script type="text/template" id="is_turn_by_turn">
    <?php echo esc_attr($action == 3 ? 1 : 0); ?>
</script>
<script type="text/template" id="is_bench">
    <?php echo esc_attr($action == 4 && $league['live_draft_bench_quantity'] > 0 ? 1 : 0); ?>
</script>
<script type="text/javascript">
    //jQuery.playerdraft.setData();
    <?php if($action == 3):?>
        <?php if($allow_live_draft):?>
            jQuery.livedraft.setLiveDraftData();
            jQuery.livedraft.liveDraftInit();
            jQuery.livedraft.liveDraftGetUserInDraftRoom();
        <?php else:?>
            jQuery.livedraft.liveDraftCheckAllowDraft(<?php echo esc_attr($league['leagueID']);?>);
            setInterval(function() { 
                jQuery.livedraft.liveDraftCheckAllowDraft(<?php echo esc_attr($league['leagueID']);?>);
            }, 10000);
        <?php endif;?>
    <?php else:?>
        jQuery.livedraft.setLiveDraftData();
        jQuery.livedraft.checkPlayerButtonDisplay();
    <?php endif;?>
</script>
