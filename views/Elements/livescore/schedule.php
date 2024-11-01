<div id="schedule" class="ng-scope">
    <?php if($schedule != null):?>
        <div class="slider-container ng-scope">
            <div class="slider-calendar">
                <div class="slider-wrapper flexslider carousel">
                    <ul class="stats-days-list slides">
                        <?php foreach($schedule as $key => $fixtures):
                            $month_year = explode('/', $key);
                            $month = $month_year[0];
                            $year = $month_year[1];
                        ?>
                        <li class="stats-slider-date ng-scope <?php if(date('m/Y') == $key):?>active<?php endif;?>" id="month_<?php echo str_replace('/', '_', $key);?>" onclick="jQuery.livescore.showSchedule('<?php echo str_replace('/', '_', $key);?>')">
                            <div class="date">
                                <span class="dotw ng-binding">
                                    <?php echo esc_html($month);?>
                                </span>
                                <span class="ng-binding">
                                    <?php echo esc_html($year);?>
                                </span>
                                <div class="games ng-binding ng-scope">
                                    <?php echo esc_html(count($fixtures));?>
                                </div>
                            </div>
                        </li>
                        <?php endforeach;?>
                    </ul>
                </div>
            </div>
        </div>
        <section class="stats-table">
            <?php foreach($schedule as $key => $fixtures):
                $month_year = explode('/', $key);
                $month = $month_year[0];
                $year = $month_year[1];
                usort($fixtures, function($a, $b) {
                    return strtotime($a['startDate']) - strtotime($b['startDate']);
                });
            ?>
            <div id="schedule_<?php echo str_replace('/', '_', $key);?>" class="team-schedule" <?php if(date('m/Y') != $key):?>style="display:none"<?php endif;?>>
                <div class="game-date">
                    <div class="stats-table-header">
                        <h4>
                            <strong class="ng-binding">
                                <?php echo esc_html($month).' '.esc_html($year);?>
                            </strong>
                        </h4>
                    </div>
                    <table class="schedule-date">
                        <thead>
                            <tr class="headers">
                                <th colspan="3"><?php echo esc_html(__("Teams", "victorious"));?></th>
                                <th class="last"><?php echo esc_html(__("Detail", "victorious"));?></th>
                            </tr>
                        </thead>
                        <tbody class="game-month">
                            <?php foreach($fixtures as $k => $fixture):
                                $home_team = $fixture['home_team'];
                                $away_team = $fixture['away_team'];
                            ?>
                            <tr class="detail-cells desktop-cells ng-scope odd">
                                <td>
                                    <a href="<?php echo VICTORIOUS_URL_LIVESCORE.'?detail=1&team_id='.$home_team['teamID'];?>">
                                        <span>
                                            <div class="logo ng-isolate-scope">
                                                <img src="<?php echo VIC_parseTeamImage($home_team['image_url']); ?>"/>
                                            </div> 
                                            <strong class="city-long ng-binding">
                                                <?php echo esc_html($home_team['name']);?>
                                            </strong>
                                            <strong class="city-short ng-binding">
                                                <?php echo esc_html($home_team['nickName']);?>
                                            </strong>
                                        </span>
                                    </a>
                                </td>
                                <td class="ng-scope">
                                    <?php if(strtotime($fixture['startDate']) < strtotime(date('Y-m-d H:i:s'))):?>
                                        <span class="ng-binding <?php echo esc_attr($home_team['score'] > $away_team['score'] ? "winner" : "");?>"><?php echo esc_html($home_team['score']);?></span>
                                        - 
                                        <span class="ng-binding <?php echo esc_attr($away_team['score'] > $home_team['score'] ? "winner" : "");?>"><?php echo esc_html($away_team['score']);?></span>
                                    <?php else:?>
                                        vs
                                    <?php endif;?>
                                </td>
                                <td>
                                    <a href="<?php echo VICTORIOUS_URL_LIVESCORE.'?detail=1&team_id='.$away_team['teamID'];?>">
                                        <span>
                                            <strong class="city-long ng-binding">
                                                <?php echo esc_html($away_team['name']);?>
                                            </strong>
                                            <strong class="city-short ng-binding">
                                                <?php echo esc_html($away_team['nickName']);?>
                                            </strong> 
                                            <div class="logo ng-isolate-scope">
                                                <img src="<?php echo VIC_parseTeamImage($away_team['image_url']); ?>"/>
                                            </div> 
                                        </span>
                                    </a>
                                </td>
                                <td class="game-detail final ng-scope">
                                    <span class="game-time ng-binding">
                                        <?php echo date('D M d g:i a', strtotime($fixture['startDate']));?>
                                    </span>
                                </td>
                            </tr>
                            <tr class="detail-cells mobile-cells ng-scope">
                                <td colspan="4" class="game-detail final ng-scope">
                                    <span class="game-time ng-binding">
                                        <?php echo date('D M d g:i a', strtotime($fixture['startDate']));?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endforeach;?>
        </section>
    <?php else:?>
        <div class="no-data-found">
            <?php echo esc_html(__("The information you requested is not available at this time, please check back again soon.", "victorious"));?>
        </div>
    <?php endif;?>
</div>
