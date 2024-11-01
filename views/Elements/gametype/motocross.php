<?php VIC_GetMessage(); ?>
<input type="hidden" id="type_league" value="motocross">
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
            <span class="f-entryFee-value amount"><?php echo VIC_FormatMoney($league['entry_fee'], null, null, $league['balance_type']); ?></span>
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
<div class="f-row">
    <section class="f-contest-player-list-container" data-role="player-list">
        <div class="f-row">
            <h1><?php echo esc_html(__('Racers', 'victorious'));?></h1>
            <ul class="f-player-list-position-tabs f-tabs f-row">
                <li>
                    <a href="" data-id="" class="f-is-active" onclick="jQuery.playerdraft.setActivePosition(this);return jQuery.playerdraft.loadPlayers();"><?php echo esc_html(__('All', 'victorious')) ?></a>
                </li>
                <li class="f-player-search">
                    <label class="f-is-hidden" for="motocross-player-search"><?php echo esc_html(__('Find a Player', 'victorious'));?></label>
                    <input type="search" id="motocross-player-search" placeholder="<?php echo esc_html(__('Find a player...', 'victorious'));?>" incremental="" autosave="fd-player-search" results="10">
                </li>
            </ul>
            <div data-role="scrollable-header">
                <table class="f-condensed f-player-list-table-header f-player-list-table-motocross f-header-fields">
                    <thead>
                        <tr>
                            <th class="f-player-name">
                                <?php echo esc_html(__('Name', 'victorious'));?>
                            </th>
                            <th class="f-player-country">
                                <?php echo esc_html(__('Privateer', 'victorious'));?>
                            </th>
                            <th class="f-player-last-place">
                                <?php echo esc_html(__('Last Race Place', 'victorious'));?>
                            </th>
                            <th class="f-player-select"></th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="f-errorMessage"></div>
            <div data-role="scrollable-body" id="listPlayers">
                <div class="f-player-list-empty"><?php echo esc_html(__('No matching players. Try changing your filter settings.', 'victorious'));?></div>
                <table class="f-condensed f-player-list-table f-player-list-table-motocross">
                    <thead class="f-is-hidden">
                        <tr>
                            <th class="f-player-name">
                                <?php echo esc_html(__('Name', 'victorious'));?>
                            </th>
                            <th class="f-player-fppg">
                                <?php echo esc_html(__('FPPG', 'victorious'));?>
                            </th>
                            <th class="f-player-salary">
                                <?php echo esc_html(__('Salary', 'victorious'));?>
                            </th>
                            <th class="f-player-select"></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
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
    <section class="f-roster-container f-roster-motocross" data-role="team">
        <header>
            <div class="f-lineup-text-container">
                <h1><?php echo esc_html(__('Your lineup', 'victorious'));?></h1>
                <p class="f-lineup-lock-message">
                    <i class="fa fa-lock"></i> <?php echo esc_html(__('Locks @', 'victorious'));?> <?php echo VIC_DateTranslate($aPool['startDate']); ?>
                    <span class="f-game_status_open"></span>
                </p>
            </div>
            <div class="f-salary-remaining" style="min-height: 67px;">

            </div>
        </header>
        <section class="f-roster">
            <ul>
                <?php if ($aLineups != null && is_array($aLineups)): ?>
                    <?php foreach ($aLineups as $aLineup): ?>
                        <?php for ($i = 0; $i < $aLineup['total']; $i++): ?>
                            <li class="f-roster-position f-count-0 player-position-<?php echo esc_attr($aLineup['id']); ?>" data-pos="<?php echo esc_attr($aLineup['id']); ?>">
                                <div class="f-player-image" <?php if ($aPool['is_round'] == 1): ?>style="display: none;"<?php endif; ?>></div>
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
                        <li class="f-roster-position f-count-0 player-position-0" <?php if ($aPool['is_round'] == 1): ?>style="padding-left: 0;background: none;"<?php endif; ?>>
                            <div class="f-player-image" <?php if ($aPool['is_round'] == 1): ?>style="display: none;"<?php endif; ?>></div>
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
            </ul>
            <div class="f-row import-clear-button-container">
                <button class="f-button f-mini f-text f-right" id="playerPickerClearAllButton" onclick="jQuery.playerdraft.clearAllMotocrossPlayer()" type="button">
                    <small><i class="fa fa-minus-circle"></i> <?php echo esc_html(__('Clear all', 'victorious'));?></small>
                </button>
            </div>
        </section>
        <footer class="f-">
            <div class="f-contest-entry-fee-container">
                <form id="formLineup" enctype="multipart/form-data" method="POST" action="<?php echo VICTORIOUS_URL_GAME; ?>">
                    <div id="enterForm.game_id.e" class="f-form_error"></div>
                    <input type="hidden" value="1" name="submitPicks">
                    <input type="hidden" value="<?php echo esc_html($league['leagueID']); ?>" name="leagueID">
                    <input type="hidden" value="<?php echo esc_html($entry_number); ?>" name="entry_number">
                    <input type="hidden" value="<?php echo esc_html(session_id()); ?>" name="session_id">
                    <input type="hidden" value="1" name="submitPicks">
                </form>
            </div>
            <div class="f-contest-enter-button-container">
                <input type="submit" data-nav-warning="off" id="btnSubmit" value="<?php echo esc_html(__('Enter', 'victorious'));?>" class="f-button f-jumbo f-primary" onclick="jQuery.playerdraft.submitData()">
            </div>
        </footer>
    </section>
</div>
<?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW . 'dlg_info.php');?>
<script type="text/template" id="dataPlayers">
<?php echo json_encode($aPlayers); ?>
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
<script type="text/template" id="dataLineups">
    <?php echo json_encode($aLineups); ?>
</script>
<script type="text/template" id="extra-positions">
    <?php echo json_encode($extra_positions); ?>
</script>
<script type="text/template" id="privater-id">
    <?php echo esc_attr($privater_id); ?>
</script>
<script type="text/javascript">
    jQuery.playerdraft.setData();
</script>