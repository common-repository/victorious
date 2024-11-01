<input type="hidden" id="league_id" value="<?php echo esc_attr($league['leagueID']);?>" />
<input type="hidden" id="user_id" value="<?php echo VIC_GetUserId();?>" />
<input type="hidden" id="entry_number" value="<?php echo esc_attr($entry_number);?>" />
<input type="hidden" id="is_live" value="<?php echo esc_attr($league['is_live']);?>" />
<div class="contentPlugin">
    <div id="f-live-scoring-app">
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
                <span class="f-final"><?php echo esc_html(__('FINAL', 'victorious'));?></span>
                <?php echo esc_html(__("Start", 'victorious'));?> <?php echo VIC_DateTranslate($league['startDate']); ?>
            </div>
        </div>
        <div class="f-column-12" id="f-scoring-table-info">
            <div class="f-clearfix" id="f-table-meta-information">
                <ul class="f-column-8">
                    <li class="f-table-type"><?php echo esc_html(__('Multiplayer league', 'victorious')) ?> (<?php echo esc_html($league['entries']); ?> <?php echo 'entries' ?>)</li>
                    <?php if (get_option('victorious_no_cash') == 0): ?>
                        <li class="f-entry"><?php echo esc_html(__('Entry', 'victorious')) ?>: <?php echo VIC_FormatMoney($league['entry_fee']); ?></li>
                    <?php endif; ?>
                </ul>
                <div class="pull-right">
                    <a class="f-lightboxRulesAndScoring_show" onclick="return jQuery.global.ruleScoring(<?php echo esc_attr($league['leagueID']); ?>, 0, 1)" href="javascript:void(0)">
                        <?php echo esc_html(__('Rules &amp; Scoring', 'victorious'));?>
                    </a>
                </div>
                <div class="clear"></div>
            </div>
        </div>
        <?php if (!empty($fights)): ?>
            <div class="f-column-12" id="f-live-scoring-fixture-info">
                <section>
                    <ul>
                        <?php foreach ($fights as $fight): 
                            $home_team = $fight['home_team'];
                            $away_team = $fight['away_team'];
                        ?>
                            <li class="f-fixture-card">
                                <ul class="f-fixture-card-live-status f-pending">
                                    <li class="f-fixture-card-away"><?php echo esc_html($home_team['nickName']); ?> <?php echo esc_html($fight['team1score']); ?></li>
                                    <li class="f-fixture-card-home"><?php echo esc_html($away_team['nickName']); ?> <?php echo esc_html($fight['team2score']); ?></li>
                                    <li class="f-fixture-card-time">
                                        <?php if (empty($fight['status'])): ?>
                                            <?php if ($fight['is_closed'] == 1): ?>
                                                <?php echo esc_html(__('FINAL', 'victorious'));?>
                                            <?php elseif ($fight['is_closed'] == 2): ?>
                                                <?php echo esc_html(__('POSTPONE', 'victorious'));?>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php echo esc_html($fight['status']) ?>
                                        <?php endif; ?>&nbsp;
                                    </li>
                                </ul>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            </div>
        <?php endif; ?>
        <div id="f-live-scoring-leaderboard">
            <?php $active_row = 0; require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."teamdraft/result.php");?>
        </div>
        <div id="result" class="fl-contest-right">
            <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."teamdraft/result_detail.php");?>
        </div>
        <div class="clear"></div>
        <?php if ($lineup_scorings != null): ?> 
            <div>
                <h3 style="margin-bottom: 0"><?php echo esc_html(__('Scoring Categories', 'victorious'));?></h3>
                <?php foreach ($lineup_scorings as $item): 
                    $lineup = $item['lineup'];
                    $scorings = $item['scorings'];
                ?> 
                    <?php echo esc_html($lineup['name']);?><br/>
                    <ul>
                        <?php foreach ($scorings as $scoring): ?>
                            <li>
                                <?php $scoring_name = (!empty($scoring['alias']) ? $scoring['alias'] : str_replace('_', ' ', $scoring['name']));
                                    echo VIC_ScoringTranslate($scoring_name).' = '.$scoring['points'];
                                ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery.teamdraft.initTeamDraftResult();
    })
</script>