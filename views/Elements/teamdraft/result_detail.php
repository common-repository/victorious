<div class="f-loading" style="display: none"></div><div class="f-user-score-summary f-clearfix">    <div>        <div class="f-rank">            <header>                <h6><?php echo esc_html(__("Position", "victorious"));?></h6>            </header>            <h1><?php echo esc_html($score['rank'] == 0 ? "-" : $score['rank']);?></h1>        </div>        <div class="f-user-info">            <div style="background-image: url('<?php echo esc_url($score['avatar']);?>')" class="f-avatar f-left">                <?php echo esc_html($score['username']);?>            </div>            <h2 class="f-truncate">                <?php echo esc_html($score['username']);?>            </h2>        </div>        <div class="f-score right">            <header>                <h6><?php echo esc_html(__("Score", "victorious"));?></h6>            </header>            <h1 class="f-user-score f-positive  "><?php echo esc_html($score['points']);?></h1>        </div>    </div></div><div class="f-roster">    <?php if($score_detail != null):?>        <?php foreach($score_detail as $score):            $team = $score['team'];            $lineup = $score['lineup'];            $player_stats = $score['player_stats'];            $team_stats = $score['team_stats'];        ?>            <div class="f-roster-row f-finished">                <div class="f-roster-row-summary">                    <div class="f-pos">                        <span title="<?php echo esc_html($lineup['name']);?>">                            <?php echo esc_html($lineup['name']);?>                        </span>                    </div>                    <div>                        <div>                            <div class="f-name">                                <?php echo esc_html(__("Team", "victorious")).": ".esc_html($team['name']);?>                            </div>                            <?php if($team_stats != null):?>                                <div class="f-player-score-breakdown">                                    <ul class="f-player-card">                                        <?php foreach($team_stats as $team_stat):?>                                            <li class="f-player-card-item">                                                <?php echo esc_html($team_stat['real_points']);?>                                                <span title="<?php echo VIC_ScoringTranslate($team_stat['alias']);?>">                                                    <?php echo VIC_ScoringTranslate($team_stat['alias']).'('.esc_html($team_stat['fantasy_points']).')';?>                                                </span>                                            </li>                                        <?php endforeach;?>                                    </ul>                                </div>                            <?php endif;?>                        </div>                        <?php foreach($player_stats as $player_stat):                            $player = $player_stat['player'];                            $stats = $player_stat['stats'];                            $total_point = $player_stat['total_point'];                        ?>                        <div>                            <div class="f-name">                                <?php echo esc_html($player['name']);?> (<?php echo esc_html(__("Total point", "victorious")).": ".$total_point;?>)                            </div>                            <?php if($fight != null):                                $classAway = $player['team_id'] == $fight['home_team']['teamID'] ? 'f-player-team-highlight' : '';                                $classHome = $player['team_id'] == $fight['away_team']['teamID'] ? 'f-player-team-highlight' : '';                            ?>                                <div class="f-fixture-info">                                    <div>                                         <span class="f-away <?php echo esc_attr($classAway);?>">                                            <?php echo esc_html($fight['home_team']['nickName']);?>                                        </span>                                         <?php echo esc_html($fight['home_team']['team1score']);?> @                                        <span class="f-home <?php echo esc_attr($classHome);?>">                                            <?php echo esc_html($fight['away_team']['nickName']);?>                                        </span>                                         <?php echo esc_html($fight['away_team']['team2score']);?>                                    </div>                                </div>                            <?php endif;?>                            <div class="f-player-score-breakdown">                                <ul class="f-player-card">                                    <?php if($stats != null):?>                                        <?php foreach($stats as $stat):?>                                            <li class="f-player-card-item">                                                <?php echo esc_html($stat['real_points']);?>                                                <span title="<?php echo VIC_ScoringTranslate($stat['alias']);?>">                                                    <?php echo VIC_ScoringTranslate($stat['alias']).'('.$stat['fantasy_points'].')';?>                                                </span>                                            </li>                                        <?php endforeach;?>                                    <?php endif;?>                                </ul>                            </div>                        </div>                        <?php endforeach;?>                    </div>                    <div class="f-score">                        <div>                            <div class="f-fixture-status f-positive f-finished">                                <?php echo esc_html($score['total_points']);?>                            </div>                        </div>                    </div>                </div>            </div>        <?php endforeach;?>    <?php endif; ?></div>