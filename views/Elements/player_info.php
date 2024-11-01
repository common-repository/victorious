<div>
    <div class="f-player-stats-lightbox">
        <div class="f-player-chunk">
            <div class="f-player-image" style="background-image: none;">
                <img alt="<?php echo esc_attr($player['name']); ?>" src="<?php echo esc_url($player['full_image_path']); ?>" onerror="jQuery.playerdraft.setNoImage(jQuery(this))">
            </div>
            <div class="f-player-container">
                <div class="f-player-info">
                    <span class="f-player-pos"><?php echo esc_html($player['position_name']);?></span>
                    <h1 class="f-player-name"><?php echo esc_html($player['name']); ?></h1>
                    <?php if (!empty($player['team_name'])): ?>
                        <span class="f-player-team"><?php echo esc_html($player['team_name']); ?></span>
                    <?php endif; ?>
                    <?php if ($sport['is_team']): ?>
                        <div id="playerGame" class="f-player-team" style="margin-top: 5px;"></div>
                    <?php endif; ?>
                </div>
                <div class="f-player-stats f-brief">
                    <div class="f-stat">
                        <b><?php echo esc_html($played); ?></b> <?php echo esc_html(__("Played", "victorious")); ?></div>
                    <div class="f-stat">
                        <b><?php echo VIC_FormatMoney($player['salary'], "USD|$"); ?></b> <?php echo esc_html(__("Salary", "victorious")); ?> </div>
                </div>
            </div>
            <div class="f-add-button" style="display: none" id="btnAdd">
                <input type="button" value="<?php echo esc_html(__("Add Player", "victorious"));?>" class="f-button f-primary f-mini f-plbARB" onclick="jQuery.playerdraft.addPlayer(<?php echo esc_attr($player['id']);?>); jQuery.playerdraft.closeDialog('#dlgInfo');">
            </div>
            <div class="f-add-button" style="display: none" id="btnRemove">
                <input type="button" value="<?php echo esc_html(__("Remove Player", "victorious"));?>" class="f-button f-primary f-mini f-plbARB" onclick="jQuery.playerdraft.clearPlayer(<?php echo esc_attr($player['id']);?>); jQuery.playerdraft.closeDialog('#dlgInfo');">
            </div>
            <ul class="f-tabs">
                <li>
                    <a data-tabname="tab1" href="#tab1"><?php echo esc_html(__("Summary", "victorious")); ?></a>
                </li>
                <li>
                    <a data-tabname="tab2" href="#tab2"><?php echo esc_html(__("Game Log", "victorious")); ?></a>
                </li>
                <li>
                    <a data-tabname="tab3" href="#tab3"><?php echo esc_html(__("Player News", "victorious")); ?></a>
                </li>
            </ul>
        </div>
        <div class="f-player-stats-lb-tab tab1" id="tab1">
            <div class="f-player-stats f-season">
                <h1><?php echo esc_html(__("Season Statistics", "victorious")); ?></h1>
                <div class="f-well f-clearfix" id="playerStatistic">
                    <div class="f-stat">
                        <b><?php echo esc_html($played); ?></b> <?php echo esc_html(__("Game(s)", "victorious")); ?>
                    </div>
                    <?php if ($player_stats != null): ?>
                        <?php foreach ($player_stats as $stat): ?>
                            <div class="f-stat">
                                <b><?php echo esc_html($stat['points']); ?></b> <?php echo esc_html($stat['name']); ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if ($opponent_stats != null): ?>
                        <h1><?php echo esc_html(__("Opposing Pitcher", "victorious")); ?> - <?php echo esc_html($opponent_name); ?></h1>
                        <div id="playerStatistic" class="f-well f-clearfix">
                            <div class="f-stat">
                                <b><?php echo esc_html($opponent_played); ?></b> <?php echo esc_html(__("Game(s)", "victorious")); ?>
                            </div>
                            <?php foreach ($opponent_stats as $stat): ?>
                                <div class="f-stat">
                                    <b><?php echo esc_html($stat['points']); ?></b> <?php echo esc_html($stat['name']); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="clear"></div>
                <?php if($performance_chart != null):?>
                <br/>
                <h1><?php echo esc_html(__("Recent Perfomances", "victorious")); ?></h1>
                <div class="f-well f-clearfix recent_performances" id="recentPerformances">
                    <div class="player_perfomance">
                        <?php foreach($performance_chart as $chart):?>
                        <div class="performance_item">
                            <div class="fantasy_point">
                                <b><?php echo esc_html($chart['total_fantasy_points']);?></b>
                                <br/>
                                <?php echo date('M d Y', strtotime($chart['date']));?>
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
        <div class="f-player-stats-lb-tab f-tab2" id="tab2">
            <div class="f-game-log">
                <h1><?php echo esc_html(__("Game Log", "victorious")); ?></h1>
                <div class="f-table-container" id="gameLog">
                    <?php if(!empty($stats['scoring'])):?>
                        <table class="f-game-log f-condensed f-text-align-right">
                            <thead>
                                <tr>
                                    <?php foreach ($stats['cats'] as $stat): ?>
                                        <th><?php echo esc_html($stat); ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody class="f-text-align-right">
                                <?php foreach ($stats['scoring'] as $stat): ?>
                                    <tr>
                                        <?php foreach ($stat as $scoring): ?>
                                            <td><?php echo esc_html($scoring); ?></td>
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
                    <h1 class="f-left"><?php echo esc_html(__("Player News", "victorious")); ?></h1>
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
                            <div <?php echo esc_attr($style);?>>
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
    </div>
</div>

<?php if($performance_chart != null):
    $chart_data = array();
    usort($performance_chart, function($a, $b) {
        return strtotime($a['date']) - strtotime($b['date']);
    });
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