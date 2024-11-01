<?php if($fixtures != null):?>
    <div class="stats-game-scores">
        <ul class="game-scores-list">
            <?php foreach($fixtures as $k => $fixture):
                $home_team = $fixture['home_team'];
                $away_team = $fixture['away_team'];
            ?>
            <li class="game-score ng-scope <?php if(($k + 1) % 3 != 0):?>odd<?php endif;?>">
                <div class="game-score-status post-game">
                    <div class="status ng-binding ng-scope">
                        <?php
                            $status = VIC_ParseFixtureStatusName($fixture['status']);
                            echo esc_html($status != "" ? $status : __("Scheduled", "victorious"));
                        ?>
                    </div>
                </div>
                <div class="game-score-details">
                    <table class="game-score-table">
                        <tbody>
                            <tr class="ng-scope">
                                <th></th>
                                <th>T</th>
                            </tr>
                            <tr class="team-row">
                                <td class="team">
                                    <a href="<?php echo VICTORIOUS_URL_LIVESCORE.'?detail=1&team_id='.$home_team['teamID'];?>">
                                        <div class="logo ng-isolate-scope">
                                            <img src="<?php echo VIC_parseTeamImage($home_team['image_url']); ?>"/>
                                            <br/>
                                        </div>
                                        <div class="team-info">
                                            <span class="team-acronym ng-binding">
                                                <?php echo esc_html($home_team['nickName']);?>
                                            </span>
                                        </div>
                                    </a>
                                </td>
                                <td class="score-stats ng-scope">
                                    <strong>
                                        <span class="ng-binding ng-isolate-scope">
                                            <?php echo esc_html($home_team['score']);?>
                                        </span>
                                    </strong>
                                </td>
                            </tr>
                            <tr class="team-row">
                                <td class="team">
                                    <a href="<?php echo VICTORIOUS_URL_LIVESCORE.'?detail=1&team_id='.$away_team['teamID'];?>">
                                        <div class="logo ng-isolate-scope">
                                            <img src="<?php echo VIC_parseTeamImage($away_team['image_url']); ?>"/>
                                            <br/>
                                        </div>
                                        <div class="team-info">
                                            <span class="team-acronym ng-binding">
                                                <?php echo esc_html($away_team['nickName']);?>
                                            </span>
                                        </div>
                                    </a>
                                </td>
                                <td class="score-stats ng-scope">
                                    <strong>
                                        <span class="ng-binding ng-isolate-scope">
                                            <?php echo esc_html($away_team['score']);?>
                                        </span>
                                    </strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </li>
            <?php endforeach;?>
        </ul>
    </div>
<?php else:?>
    <?php echo esc_html(__('No events', 'victorious'));?>
<?php endif; ?>
