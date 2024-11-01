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
                    <li class="f-table-type"><?php echo esc_html(__('Multiplayer league', 'victorious')) ?> (<?php echo esc_html($league['entries']); ?> <?php echo ('entries') ?>)</li>
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
        <?php if (!empty($rounds)): ?>
            <div class="f-column-12" id="f-live-scoring-fixture-info">
                <section>
                    <ul>
                        <?php foreach ($rounds as $round): ?>
                            <li class="f-fixture-card">
                                <ul class="f-fixture-card-live-status f-pending">
                                    <li class="f-fixture-card-away"><?php echo esc_html($round['name']); ?></li>
                                    <li class="f-fixture-card-time">
                                        <?php if ($round['is_closed'] == 1): ?>
                                            <?php echo esc_html(__('FINAL', 'victorious'));?>
                                        <?php elseif ($round['is_closed'] == 2): ?>
                                            <?php echo esc_html(__('POSTPONE', 'victorious'));?>
                                        <?php endif; ?>
                                    </li>
                                </ul>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            </div>
        <?php endif; ?>
        <div id="f-live-scoring-leaderboard"></div>
        <div id="result" class="fl-contest-right"></div>
        <div class="clear"></div>
        <?php if ($scoring_categories != null): ?> 
            <div>
                <h3 style="margin-bottom: 0"><?php echo esc_html(__('Scoring Categories', 'victorious'));?></h3>
                <ul>
                    <?php foreach ($scoring_categories as $scoring_category): ?>
                        <li>
                            <?php $scoring_name = (!empty($scoring_category['alias']) ? $scoring_category['alias'] : str_replace('_', ' ', $scoring_category['name']));
                                echo VIC_ScoringTranslate($scoring_name).' = '.$scoring_category['points'];
                            ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery.best5.initBest5Result();
    })
</script>