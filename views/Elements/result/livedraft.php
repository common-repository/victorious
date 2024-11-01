<div class="contentPlugin">
    <div id="f-live-scoring-app">
        <input type="hidden" id="scoringCats" value='<?php echo json_encode($scoringCats); ?>' />
        <input type="hidden" id="multiEntry" value='<?php echo esc_attr($league['multi_entry']); ?>' />
        <input type="hidden" id="leagueOptionType" value='<?php echo esc_attr($league['option_type']); ?>' />
        <input type="hidden" id="gameType" value='<?php echo esc_attr($league['gameType']); ?>' />
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
                    <li class="f-table-type"><?php echo esc_html(__('Multiplayer league', 'victorious')) ?> (<?php echo esc_html($league['entries']); ?> <?php echo 'entries';?>)</li>
                    <?php if (get_option('victorious_no_cash') == 0): ?>
                        <li class="f-entry"><?php echo esc_html(__('Entry', 'victorious')) ?>: <?php echo VIC_FormatMoney($league['entry_fee']); ?></li>
                    <?php endif; ?>
                </ul>
                <ul class="f-prizes-breakdown">
                    <li class="f-prize-row">
                        <span class="f-pos-name">1st:</span> $0
                    </li>
                </ul>
            </div>
        </div>
        <?php if (!empty($aFights)): ?>
            <div class="f-column-12" id="f-live-scoring-fixture-info">
                <section>
                    <ul>
                        <?php foreach ($aFights as $aFight): ?>
                            <li class="f-fixture-card">
                                <ul class="f-fixture-card-live-status f-pending">
                                    <li class="f-fixture-card-away"><?php echo esc_html($aFight['nickName1']); ?> <?php echo esc_html($aFight['team1score']); ?></li>
                                    <li class="f-fixture-card-home"><?php echo esc_html($aFight['nickName2']); ?> <?php echo esc_html($aFight['team2score']); ?></li>
                                    <li class="f-fixture-card-time">
                                        <?php if (empty($aFight['status'])): ?>
                                            <?php if ($aFight['is_closed'] == 1): ?>
                                                <?php echo esc_html(__('FINAL', 'victorious'));?>
                                            <?php elseif ($aFight['is_closed'] == 2): ?>
                                                <?php echo esc_html(__('POSTPONE', 'victorious'));?>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php echo esc_html($aFight['status']) ?>
                                        <?php endif; ?>&nbsp;
                                    </li>
                                </ul>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            </div>
        <?php endif; ?>
        <div class="clear"></div>
        <div id="tabs">
            <ul>
              <li>
                  <a href="#tabs_weekly_result">
                      <?php echo esc_html(__('Weekly Result','victorious'));?>
                  </a>
              </li>
              <li>
                  <a href="#tabs_score_board">
                      <?php echo esc_html(__('League Standings','victorious'));?>
                  </a>
              </li>
            </ul>
            <div id="tabs_weekly_result">
                <div class="f-column-12" style="padding:25px 0;">
                    <select id="week" style="max-width: 250px;" onchange="jQuery.livedraft.loadOpponentByweek();jQuery.livedraft.loadOpponentScores();">
                        <?php foreach($week as $key=>$value): ?>
                        <option <?php echo esc_attr($key + 1 == $week_select ?"selected":""); ?> value="<?php echo  $value; ?>">
                           <?php if($value != 'final'): ?>
                             <?php echo sprintf(esc_html(__('Week %s','victorious')),$value); ?>
                            <?php else: ?>
                                <?php echo esc_html(__('Final','victorious'));?>
                            <?php endif; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <select id="opponent" style="max-width: 250px;" onchange="jQuery.livedraft.loadOpponentScores();">
                        <?php foreach($opponents as $week => $opponent_list): ?>
                            <optgroup id="opponent_week_<?php echo esc_attr($week);?>" style="display: none">
                                <?php foreach($opponent_list as $opponent): ?>
                                    <option value="<?php echo esc_attr($opponent['id']); ?>">
                                        <?php echo esc_html($opponent['user_name'])." ".esc_html(__('vs','victorious'))." ".esc_html($opponent['opponent_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="result_detail_data"></div>
                <div class="clear"></div>
                <?php if ($scoringCats != null): ?> 
                    <div>
                        <h3 style="margin-bottom: 0"><?php echo esc_html(__('Scoring Categories', 'victorious'));?></h3>
                        <?php foreach ($scoringCats as $item): ?> 
                            <?php $scoring_name = (!empty($item['alias']) ? $item['alias'] : str_replace('_', ' ', $item['name']));
                                echo VIC_ScoringTranslate($scoring_name).' = '.$item['points'];
                            ?><br/>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?> 
                <?php if ($bonus != null): ?>
                    <div id="bonusPoints">
                        <h3 style="margin-bottom: 0"><?php echo esc_html(__('Bonus', 'victorious'));?></h3>
                        <?php echo esc_html($bonus); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div id="tabs_score_board">
                <div class="f-column-12 f-small-screen-pane score_board" id="f-live-scoring-leaderboard">
                    <div>
                        <table class="f-condensed" id="tableScores">
                            <thead>
                                <tr>
                                    <th style="width:7%;"></th>
                                    <th><?php echo esc_html(__('User', 'victorious'));?></th>
                                    <th class="f-text-align-right" style="width:8%;"><?php echo esc_html(__('GP', 'victorious'));?></th>
                                    <th class="f-text-align-right" style="width:8%;"><?php echo esc_html(__('W', 'victorious'));?></th>
                                    <th class="f-text-align-right" style="width:8%;"><?php echo esc_html(__('L', 'victorious'));?></th>
                                    <th class="f-text-align-right" style="width:10%;"><?php echo esc_html(__('PF', 'victorious'));?></th>
                                    <th class="f-text-align-right" style="width:10%;"><?php echo esc_html(__('PA', 'victorious'));?></th>
                                    <th class="f-text-align-right" style="width:10%;"><?php echo esc_html(__('PTS', 'victorious'));?></th>
                                    <?php if (get_option('victorious_no_cash') == 0): ?>
                                        <th class="f-text-align-right" style="width:14%;"><?php echo esc_html(__('Prizes', 'victorious'));?></th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<script type="text/javascript">
    jQuery(window).load(function () {
        jQuery("#tabs" ).tabs();
        jQuery.livedraft.loadOpponentByweek();
        var isLive = <?php echo esc_attr($league['is_live']); ?>;
        if (isLive)
        {
            jQuery.livedraft.liveEntriesResult(<?php echo esc_attr($league['poolID']); ?>, <?php echo esc_attr($league['leagueID']); ?>, <?php echo esc_attr($entry_number); ?>, 1);
            setInterval(function () {
                jQuery.livedraft.liveEntriesResult(<?php echo esc_attr($league['poolID']); ?>, <?php echo esc_attr($league['leagueID']); ?>, <?php echo esc_attr($entry_number); ?>, 1)
            }, 60000);
        } 
        else
        {
            jQuery.livedraft.loadFixtureScores(<?php echo esc_attr($league['leagueID']); ?>);
            jQuery.livedraft.loadOpponentScores();
            jQuery.livedraft.loadContestScores();
        }
    });
</script>