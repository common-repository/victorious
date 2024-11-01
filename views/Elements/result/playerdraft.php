<div class="contentPlugin">
    <div id="f-live-scoring-app">
        <input type="hidden" id="scoringCats" value='<?php echo json_encode($scoringCats); ?>' />
        <input type="hidden" id="multiEntry" value='<?php echo esc_attr($league['multi_entry']); ?>' />
        <input type="hidden" id="leagueOptionType" value='<?php echo esc_attr($league['option_type']); ?>' />
        <input type="hidden" id="gameType" value='<?php echo esc_attr($league['gameType']); ?>' />
        <input type="hidden" id="is_motocross" value='<?php echo esc_attr($league['is_motocross']); ?>' />
        <input type="hidden" id="leagueID" value='<?php echo esc_attr($league['leagueID']); ?>' />
        <input type="hidden" id="entry_number" value='<?php echo esc_attr($entry_number); ?>' />
        <div class="f-column-12 f-clearfix">
            <div id="f-scoring-table-name">
                <h1>
                    <?php echo esc_html($league['name']); ?>
                    <?php if ($league['multi_entry'] == 1): ?>
                        (<?php echo esc_html(__("Multi Entry", 'victorious'));?>)
                    <?php endif; ?>
                </h1>
            </div>
            <div id="f-current-table-status">
                <span class="f-final">FINAL</span>
                <?php echo esc_html(__("Start", 'victorious'));?> <?php echo VIC_DateTranslate($league['startDate']); ?>
            </div>
        </div>
        <div class="f-column-12" id="f-scoring-table-info">
            <div class="f-clearfix" id="f-table-meta-information">
                <ul class="f-column-8">
                    <li class="f-table-type"><?php echo esc_html(__('Multiplayer league', 'victorious')) ?> (<?php echo esc_html($league['entries']); ?> <?php echo 'entries' ?>)</li>
                    <?php if (get_option('victorious_no_cash') == 0): ?>
                        <li class="f-entry"><?php echo esc_html(__('Entry', 'victorious')) ?>: <?php echo VIC_FormatMoney($league['entry_fee'], $balance_type['currency_code_symbol'], $balance_type['currency_position']); ?></li>
                    <?php endif; ?>
                </ul>
                <ul class="f-prizes-breakdown">
                    <li class="f-prize-row">
                        <span class="f-pos-name">1st:</span> $0
                    </li>
                </ul>
            </div>
        </div>
        <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."fixture_result.php");?>
        <div class="f-column-12 f-small-screen-pane" id="f-live-scoring-leaderboard">
            <div>
                <table class="f-condensed" id="tableScores">
                    <thead>
                        <tr>
                            <th style="width:35px;"></th>
                            <th class="f-text-align-left"><?php echo esc_html(__('User', 'victorious'));?></th>
                            <?php if ($league['multi_entry'] == 1): ?>
                                <th style="width:50px;"><?php echo esc_html(__('Entry', 'victorious'));?></th>
                            <?php endif; ?>
                            <th class="f-text-align-center" style="width:62px;"><?php echo esc_attr($league['gameType'] == 'GOLFSKIN' ? __('Skin', 'victorious') : __('Score', 'victorious')) ?></th>
                            <?php if (get_option('victorious_no_cash') == 0): ?>
                                <th class="f-text-align-center" style="width:90px;"><?php echo esc_html(__('Prizes', 'victorious'));?></th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."user_score.php");?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="fl-contest-right" >
            <div id="f-live-scoring-entry-details">
                <div class="f-slot f-column-6 f-entry-component f-small-screen-pane f-odd" id="f-seat-1"></div>
                <div class="f-slot f-column-6 f-entry-component f-small-screen-pane f-even" id="f-seat-2">
                    <div id="live-scoring-app" class="loading" style="display: none"></div>
                    <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."user_result_new.php");?>
                </div>
                <div class="clear"></div>

            </div>
        </div>
        <div class="clear"></div>
        <?php if ($scoringCats != null && $league['gameType'] != 'GOLFSKIN'): ?> 
            <div>
                <h3 style="margin-bottom: 0"><?php echo esc_html(__('Scoring Categories', 'victorious'));?></h3>
                <?php foreach ($scoringCats as $item): ?> 
                    <?php $scoring_name = (!empty($item['alias']) ? $item['alias'] : str_replace('_', ' ', $item['name']));
                        echo VIC_ScoringTranslate($scoring_name).' = '.$item['points'];
                    ?><br/>
                <?php endforeach; ?>
            </div>
        <?php endif; ?> 
        <?php if ($bonus != null && $league['gameType'] != 'GOLFSKIN'): ?>
            <div id="bonusPoints">
                <h3 style="margin-bottom: 0"><?php echo esc_html(__('Bonus', 'victorious'));?></h3>
                <?php echo esc_html($bonus); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<script type="text/javascript">
    jQuery(window).load(function () {
        var isLive = <?php echo esc_attr($league['is_live']); ?>;
        if (isLive)
        {
            //jQuery.league.liveEntriesResult(<?php echo esc_attr($league['poolID']); ?>, <?php echo esc_attr($league['leagueID']); ?>, <?php echo esc_attr($entry_number); ?>);
            setInterval(function () {
                jQuery.league.liveEntriesResult(<?php echo esc_attr($league['poolID']); ?>, <?php echo esc_attr($league['leagueID']); ?>, <?php echo esc_attr($entry_number); ?>)
            }, 60000);
        } else
        {
            //jQuery.playerdraft.loadContestScores(<?php echo esc_attr($league['leagueID']); ?>, <?php echo esc_attr($entry_number); ?>);
        }
    });
</script>