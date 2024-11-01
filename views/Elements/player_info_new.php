<div>
    <div class="f-player-stats-lightbox p-3">
        <div class="f-player-chunk d-flex mb-3">
            <div class="f-player-image" style="background-image: none;">
                <img alt="<?php echo esc_html($player['name']); ?>" src="<?php echo !empty($player['image_url']) ? $player['image_url'] : $player['full_image_path']; ?>" onerror="jQuery.global.setNoImage(jQuery(this))">
            </div>
            <div class="f-player-container px-3">
                <div class="f-player-info">
                    <span class="f-player-pos"><?php echo esc_html($position['name']);?></span>
                    <h1 class="f-player-name my-1"><?php echo esc_html($player['name']); ?></h1>
                    <?php if (!empty($team)): ?>
                        <span class="f-player-team"><?php echo esc_html($team['name']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="f-player-stats f-brief">
                    <?php if(!$player['is_coin']):?>
                        <div class="f-stat">
                            <b><?php echo esc_html($played); ?></b> <?php echo esc_html(__("Played", "victorious")); ?>
                        </div>
                        <div class="f-stat">
                            <b><?php echo VIC_FormatMoney($player['salary'], "USD|$"); ?></b> <?php echo esc_html(__("Salary", "victorious")); ?>
                        </div>
                    <?php else:?>
                        <div class="f-stat">
                            <b><?php echo VIC_FormatMoney($player['salary'], null, null, null, false); ?></b>
                        </div>
                    <?php endif; ?>
                </div>
            </div>       
        </div>
        <ul class="f-tabs">
            <li>
                <a data-tabname="tab1" href="#tab1"><?php echo esc_html(__("Summary", "victorious")); ?></a>
            </li>
            <?php if(!$player['is_coin']):?>
            <li>
                <a data-tabname="tab2" href="#tab2"><?php echo esc_html(__("Game Log", "victorious")); ?></a>
            </li>
            <li>
                <a data-tabname="tab3" href="#tab3"><?php echo esc_html(__("Player News", "victorious")); ?></a>
            </li>
            <?php endif; ?>
        </ul>
        <div class="f-player-stats-lb-tab tab1" id="tab1">
            <div class="f-player-stats f-season">
                <h3 class="my-2"><?php echo esc_html(__("Season Statistics", "victorious")); ?></h3>
                <div class="f-well f-clearfix" id="playerStatistic">
                    <div class="f-stat">
                        <b><?php echo esc_html($played); ?></b> <?php echo esc_html(__("Game(s)", "victorious")); ?>
                    </div>
                    <?php if ($season_stats != null): ?>
                        <?php foreach ($season_stats as $stat): ?>
                            <div class="f-stat" title="<?php echo esc_html($stat['name']); ?> ">
                                <b><?php echo esc_html($stat['points']); ?></b> <?php echo esc_html($stat['alias']); ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="clear"></div>
                <?php if($performance_chart != null):?>
                <br/>
                <h3 class="my-2"><?php echo esc_html(__("Recent Perfomances", "victorious")); ?></h3>
                <div class="f-well f-clearfix recent_performances" id="recentPerformances">
                    <div class="player_perfomance">
                        <?php foreach($performance_chart as $chart):?>
                        <div class="performance_item">
                            <div class="fantasy_point">
                                <b><?php echo esc_html($chart['total_fantasy_points']);?></b>
                                <br/>
                                <?php echo esc_html(date('M d Y', strtotime($chart['date'])));?>
                            </div>
                            <?php if(!empty($chart['opponent_team'])):?>
                            <div class="player_opponent">
                                @ <?php echo esc_html($chart['opponent_team']['name']);?>
                            </div>
                            <?php endif;?>
                            <div class="clear"></div>
                        </div>
                        <?php endforeach;?>
                    </div>
                    <div class="player_chart">
                        <div id="perfomance_chart" style="width: 100%; height: 100%"></div>
                    </div>
                </div>
                <?php endif;?>
            </div>
        </div>
        <?php if(!$player['is_coin']):?>
        <div class="f-player-stats-lb-tab f-tab2" id="tab2">
            <div class="f-game-log">
                <h3 class="my-2"><?php echo esc_html(__("Game Log", "victorious")); ?></h3>
                <div class="f-table-container" id="gameLog">
                    <?php if(!empty($match_stats)):?>
                        <table class="f-game-log f-condensed f-text-align-right">
                            <thead>
                                <tr>
                                    <th>
                                        <?php echo esc_html(__("Date", "victorious")); ?>
                                    </th>
                                    <?php foreach ($match_stats[0]['stats'] as $stat): ?>
                                        <th title="<?php echo esc_html($stat['name']); ?>">
                                            <?php echo esc_html($stat['alias']); ?>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody class="f-text-align-right">
                                <?php foreach ($match_stats as $stats): ?>
                                    <tr>
                                        <td>
                                            <?php echo date('M d', strtotime($stats['pool']['startDate']));?>
                                        </td>
                                        <?php foreach ($stats['stats'] as $stat): ?>
                                            <td><?php echo esc_html($stat['points']); ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else:?>
                        <?php echo esc_html(__("This player has not played any matches yet.", "victorious"));?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div id="tab3" class="f-player-stats-lb-tab f-tab3">
            <div class="f-player-news">
                <div class="f-row">
                    <h3 class="my-2"><?php echo esc_html(__("Player News", "victorious")); ?></h3>
                </div>
                <div class="f-clear f-news-item" data-role="scrollable-body" id="playerNews" data-google="<?php echo esc_html($allow_google_news);?>">
                    <?php if($player_news != null):
                        $style = 'style="padding-bottom:5px;margin-bottom:5px;border-bottom:solid 1px #8b8b8b"';
                    ?>
                        <?php foreach($player_news as $k => $news):
                            if (count($player_news) == $k + 1)
                            {
                                $style = '';
                            }
                        ?>
                            <div <?php echo esc_html($style);?>>
                                <?php echo esc_html($news['updated']); ?><br/>
                                <?php echo esc_html($news['title']); ?><br/>
                                <?php echo esc_html($news['content']); ?>
                            </div>
                        <?php endforeach;?>
                    <?php else:?>
                        <?php echo esc_html(__("No news", "victorious"));?>
                    <?php endif;?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if($performance_chart != null):
    $chart_data = array();
    foreach($performance_chart as $chart_item)
    {
        $chart_data[] = array(
            date('M d', strtotime($chart_item['date'])),
            (float)$chart_item['total_fantasy_points']
        );
    }
    $chart_data = json_encode($chart_data);
?>
<script type="text/javascript">
    google.charts.load('current', {'packages':['line']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var chart_date = jQuery.parseJSON('<?php echo esc_attr($chart_data);?>');
        var data = new google.visualization.DataTable();
        data.addColumn('string', '');
        data.addColumn('number', 'AVG');

        data.addRows(chart_date);

        var options = {
            chart: {
                title: '',
                subtitle: ''
            },
            backgroundColor: 'transparent',
            is3D:true
        };
        var chart = new google.charts.Line(document.getElementById('perfomance_chart'));
        chart.draw(data, google.charts.Line.convertOptions(options));
    }
</script>
<?php endif;?>