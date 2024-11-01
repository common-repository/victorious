<?php VIC_GetMessage();?>
<input type="hidden" id="type_league" value="single">
<div class="f-contest-title-date">
    <h1 class="f-contest-title f-heading-styled"><?php echo esc_html($league['name']);?></h1>
    <div class="f-contest-date-container">
        <div class="f-contest-date-start-time">
            <?php echo esc_html(__('Contest starts', 'victorious'));?> <?php echo VIC_DateTranslate($aPool['startDate']);?>
        </div>
    </div>
</div>
<ul class="f-contest-information-bar">
    <li class="f-contest-entries-league"><?php echo esc_html(__('Entries:', 'victorious'));?> 
        <b>
            <a class="f-lightboxLeagueEntries_show" href="#" onclick="return jQuery.global.ruleScoring(<?php echo esc_attr($league['leagueID']);?>, 2)"><?php echo esc_html($league['entries']);?></a>
        </b> / <?php echo esc_html($league['size']);?>
        <span class="f-entries-player-league"> <?php echo esc_html(__('player league', 'victorious'));?></span>
    </li>

    <li class="f-contest-rules-link-container">
        <a class="f-lightboxRulesAndScoring_show" onclick="return jQuery.global.ruleScoring(<?php echo esc_attr($league['leagueID']);?>)" href="#">
            <?php echo esc_html(__('Rules &amp; Scoring', 'victorious'));?>
        </a>
    </li>
</ul>
<div class="clear"></div>
<div class="f-pick-your-team">
    <section data-role="fixture-picker" class="f-fixture-picker">
        <?php if(!empty($aRounds)):?>
		<h1><?php echo esc_html(__('Players available from (click to filter):', 'victorious'));?></h1>
		<div class="f-fixture-picker-button-container">
			<a class="f-button f-mini fixture-item" onclick="return jQuery.playerdraft.loadPlayers();">All</a>
            <?php foreach($aRounds as $key=>$aRound):?>
                        <a data-id="<?php echo esc_attr($aRound['id']); ?>" class="f-button f-mini fixture-item <?php if($key == 0){echo 'f-is-active';} ?>" onclick="jQuery.playerdraft.setActiveFixture(this);return jQuery.playerdraft.selectGolfSkinRounds(this);">
                <span class="f-fixture-team-home"><?php echo esc_html($aRound['name']);?></span>
                <span class="f-fixture-start-time"><?php echo VIC_DateTranslate($aRound['startDate']);?></span>
            </a>
			<?php endforeach;?>
        </div>
        <?php endif;?>
	</section>
</div>
<div class="f-row">
    <section class="f-contest-player-list-container" data-role="player-list">
        <div class="f-row">
            <h1><?php echo esc_html(__('Available Players', 'victorious'));?></h1>
            <ul class="f-player-list-position-tabs f-tabs f-row">
		<li>
                    <a href="" data-id="" class="f-is-active" onclick="jQuery.playerdraft.setActivePosition(this);return jQuery.playerdraft.loadPlayers();"><?php echo esc_html(__('All', 'victorious'))?></a>
                </li>
                <li class="f-player-search">
					<label class="f-is-hidden" for="player-search"><?php echo esc_html(__('Find a Player', 'victorious'));?></label>
					<input type="search" id="player-search" placeholder="<?php echo esc_html(__('Find a player...', 'victorious'));?>" incremental="" autosave="fd-player-search" results="10">
				</li>
			</ul>
            <div data-role="scrollable-header">
				<table class="f-condensed f-player-list-table-header f-header-fields">
					<thead>
						<tr>
                            <th colspan="2" class="f-player-name table-sorting">
								<?php echo esc_html(__('Name', 'victorious'));?>
								<i class="f-icon f-sorted-asc">▴</i>
								<i class="f-icon f-sorted-desc">▾</i>
							</th>
                            <?php if(!$aPool['only_playerdraft']):?>
							<th class="f-player-played table-sorting">
								<i class="f-icon f-sorted-asc">▴</i>
								<i class="f-icon f-sorted-desc">▾</i>
								<?php echo esc_html(__('Team', 'victorious'));?>
							</th>
							<th class="f-player-fixture table-sorting">
								<?php echo esc_html(__('Game', 'victorious'));?>
								<i class="f-icon f-sorted-asc">▴</i>
								<i class="f-icon f-sorted-desc">▾</i>
							</th>
                            <?php endif;?>
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
                            <?php if(!$aPool['only_playerdraft']):?>
                            <th class="f-player-played">
                                <?php echo esc_html(__('Team', 'victorious'));?>
                            </th>
                            <th class="f-player-fixture">
                                <?php echo esc_html(__('Game', 'victorious'));?>
                            </th>
                            <?php endif;?>
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
                    <i class="fa fa-lock"></i> <?php echo esc_html(__('Locks @', 'victorious'));?> <?php echo VIC_DateTranslate($aPool['startDate']);?>
                    <span class="f-game_status_open"></span>
                </p>
            </div>
            <?php if($is_entry_fee): ?>
            <div class="f-salary-remaining">
                <div class="f-salary-remaining-container">
                    <span id="f-salary"><?php echo VIC_FormatMoney($total_money); ?></span>
                </div>
            </div>
            <?php endif; ?>
        </header>
        <section class="f-roster">
            <ul>
                <?php if($aLineups != null && is_array($aLineups)):?>
                    <?php foreach($aLineups as $aLineup):?>
                        <?php for($i = 0; $i < $aLineup['total']; $i++):?>
                        <li class="f-roster-position f-count-0 player-position" <?php if($aPool['is_round'] == 1):?>style="padding-left: 0;background: none;"<?php endif;?>>
                            <div class="f-player-image" <?php if($aPool['is_round'] == 1):?>style="display: none;"<?php endif;?>></div>
                            <div class="f-position"><?php echo '';?>
                                <span class="f-empty-roster-slot-instruction"><?php echo esc_html(__('Add player', 'victorious'));?></span>
                            </div>
                            <div class="f-player"></div>
                            <a class="f-button f-tiny f-text">
                                <i class="fa fa-minus-circle"></i>
                            </a>
                        </li>
                        <?php endfor;?>
                    <?php endforeach;?>
                <?php else:?>  
                    <?php for($i = 0; $i < $aLineups; $i++):?> 
                        <li class="f-roster-position f-count-0 player-position-0" <?php if($aPool['is_round'] == 1):?>style="padding-left: 0;background: none;"<?php endif;?>>
                            <div class="f-player-image" <?php if($aPool['is_round'] == 1):?>style="display: none;"<?php endif;?>></div>
                            <div class="f-position">
                                <span class="f-empty-roster-slot-instruction"><?php echo esc_html(__('Add player', 'victorious'));?></span>
                            </div>
                            <div class="f-player"></div>
                            <div></div>
                            <a class="f-button f-tiny f-text">
                                <i class="fa fa-minus-circle"></i>
                            </a>
                        </li>
                    <?php endfor;?>
                <?php endif;?>
            </ul>
            <div class="f-row import-clear-button-container">
                <button class="f-button f-mini f-text f-right" id="playerPickerClearAllButton" onclick="jQuery.playerdraft.clearAllPlayer()" type="button">
                    <small><i class="fa fa-minus-circle"></i> <?php echo esc_html(__('Clear all', 'victorious'));?></small>
                </button>
            </div>
        </section>
        <footer class="f-">
            <div class="f-contest-entry-fee-container">
                <form id="formLineup" enctype="multipart/form-data" method="POST" action="<?php echo VICTORIOUS_URL_GAME;?>">
                    <div id="enterForm.game_id.e" class="f-form_error"></div>
                    <input type="hidden" value="1" name="submitPicks">
                    <input type="hidden" value="<?php echo esc_attr($league['leagueID']);?>" name="leagueID">
                    <input type="hidden" value="<?php echo esc_attr($entry_number);?>" name="entry_number">
                    <input type="hidden" value="<?php echo session_id();?>" name="session_id">
                    <input type="hidden" value="1" name="submitPicks">
                    <input type="hidden" value="<?php echo esc_attr($league['gameType']); ?>" name="game_type" id="game_type" >
                    <input type="hidden" value="0" name="total_money" id="total_money">
                    <input type="hidden" value='' name="players" id="players">
                    <input type="hidden" value='<?php echo esc_attr($league['poolID']) ?>' name="poolID">
                </form>
            </div>
            <div class="f-contest-enter-button-container">
                <input type="submit" data-nav-warning="off" id="btnSubmit" value="<?php echo esc_html(__('Enter', 'victorious'));?>" class="f-button f-jumbo f-primary" onclick="jQuery.playerdraft.golfSkinSubmitData()">
            </div>
        </footer>
    </section>
</div>
<?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW.'dlg_info.php');?>
<script type="text/template" id="dataPlayers">
    <?php echo json_encode($aPlayers);?>
</script>
<script type="text/template" id="dataSalaryRemaining">
    <?php echo esc_attr($aPool['salary_remaining']);?>
</script>
<script type="text/template" id="dataPlayerIdPicks">
    <?php echo json_encode($playerIdPicks);?>
</script>
<script type="text/template" id="dataLeague">
    <?php echo json_encode($league);?>
</script>
<script type="text/template" id="dataFights">
    <?php echo json_encode($aFights);?>
</script>
<script type="text/template" id="dataPool">
    <?php echo json_encode($aPool);?>
</script>
<script type="text/template" id="dataIndicators">
    <?php echo json_encode($indicators);?>
</script>
<script type="text/template" id="dataBalance">
    <?php echo esc_attr($balance);?>
</script>
<script type="text/template" id="dataPlayerGolfSkin">
    <?php echo esc_attr($aPlayerGolfSkin);?>
</script>
<script type="text/template" id="dataTotalMoney">
    <?php echo esc_attr($total_money);?>
</script>
<script type="text/template" id="dataentryFee">
    <?php echo esc_attr($entry_fee);?>
</script>
<script type="text/template" id="dataIsEntryFee">
    <?php echo esc_attr($is_entry_fee);?>
</script>
<script type="text/javascript">
    jQuery.playerdraft.golfSkinSetData();
</script>