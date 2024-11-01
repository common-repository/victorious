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
                <span class="f-final">FINAL</span>
                <?php echo esc_html(__("Start", 'victorious'));?> <?php echo VIC_DateTranslate($league['startDate']); ?>
            </div>
        </div>
        <div class="f-column-12" id="f-scoring-table-info">
            <div class="f-clearfix" id="f-table-meta-information">
                <ul class="f-column-8">
                    <li class="f-table-type"><?php echo esc_html(__('Multiplayer league', 'victorious'));?> (<?php echo esc_html($league['entries']); ?> <?php echo 'entries';?>)</li>
                    <?php if (get_option('victorious_no_cash') == 0): ?>
                        <li class="f-entry"><?php echo esc_html(__('Entry', 'victorious'));?>: <?php echo VIC_FormatMoney($league['entry_fee']); ?></li>
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
        <div class="f-column-12 f-small-screen-pane full_width" id="f-live-scoring-leaderboard">
            <div id="result_rank"></div>
        </div>
        <div class="clear"></div>
        <div>
            <?php 
                $maps = array(
                    "point_group_winner" => esc_html(__("Winner of group", 'victorious')),
                    "point_group_runnerup" => esc_html(__("Runner-up of group", 'victorious')),
                    "point_group_16" => esc_html(__("1/16", 'victorious')),
                    "point_group_8" => esc_html(__("1/8", 'victorious')),
                    "point_group_4" => esc_html(__("1/4", 'victorious')),
                    "point_first" => esc_html(__("Champion", 'victorious')),
                    "point_second" => esc_html(__("Second", 'victorious')),
                    "point_third" => esc_html(__("Third", 'victorious')),
                );
                $prize_structures = json_decode($league['bracket_prize'], true);
                foreach($prize_structures as $key => $prize)
                {
                    echo esc_html($maps[$key].": <b>".$prize."</b><br/>");
                }
            ?>
        </div>
        <div id="result_detail"></div>
        <div class="clear"></div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery.bracket.initBracketResult();
    })
</script>