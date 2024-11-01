<table class="table table-bordered f-condensed table-result">    <thead>        <tr>            <th><?php echo esc_html(__("Fixture", "victorious"));?></th>            <th style="width:25%">                <?php echo esc_html(__("My Pick", "victorious"));?> (<?php echo esc_html($current_user['user_login']);?>)            </th>            <th style="width:25%">                <?php echo esc_html(__("Competitor Pick", "victorious"));?> (<?php echo esc_html($opponent_user['user_login']);?>)            </th>            <th style="width:25%">                <?php echo esc_html(__("Actual Result", "victorious"));?>            </th>        </tr>    </thead>    <?php if($fights != null):?>    <tbody>        <?php foreach($fights as $fight):            $home_team = $fight['home_team'];            $away_team = $fight['away_team'];            $my_pick = $fight['my_pick'];            $opponent_pick = $fight['opponent_pick'];            $styleTeam1Win = $styleTeam2Win = "incorrect";            if ($fight['winnerID'] == $fight['fighterID1'] || $fight['winnerID'] == 0)            {                $styleTeam1Win = "correct";            }            if ($fight['winnerID'] == $fight['fighterID2'] || $fight['winnerID'] == 0)            {                $styleTeam2Win = "correct";            }        ?>            <tr>                <td>                    <div style="font-weight:bold"><?php echo esc_html($home_team['name']);?></div>                    <div><?php echo esc_html(__("vs", "victorious"));?></div>                    <div style="font-weight:bold"><?php echo esc_html($away_team['name']);?></div>                    <?php echo VIC_DateTranslate($fight['startDate']); ?>                </td>                <td>                    <?php if(!empty($my_pick)):                        $team_select = $my_pick['team_select'];                    ?>                        <?php foreach($team_select as $item):?>                            <div class="<?php echo esc_attr($item['is_correct'] ? "correct" : "incorrect");?>">                                <?php echo esc_html($item['name']);?><br/>                                <?php echo esc_html(__("To Score", "victorious"));?>: <?php echo esc_html($item['is_score']);?><br/>                                <?php echo esc_html(__("Points", "victorious"));?>: <?php echo esc_html($item['points']);?>                            </div>                            <br/>                        <?php endforeach;?>                    <?php endif;?>                </td>                <td>                    <?php if(!empty($opponent_pick)):                        $team_select = $opponent_pick['team_select'];                    ?>                        <?php foreach($team_select as $item):?>                            <div class="<?php echo esc_attr($item['is_correct'] ? "correct" : "incorrect");?>">                                <?php echo esc_html($item['name']);?><br/>                                <?php echo esc_html(__("To Score", "victorious"));?>: <?php echo esc_html($item['is_score']);?><br/>                                <?php echo esc_html(__("Points", "victorious"));?>: <?php echo esc_html($item['points']);?>                            </div>                            <br/>                        <?php endforeach;?>                    <?php endif;?>                </td>                <td>                    <div class="h_column actual_result">                        <?php if ($fight['is_live'] || $league['is_complete']):?>                            <div class="<?php echo esc_attr($styleTeam1Win);?>">                                <?php echo esc_html($home_team['name']).' '.esc_html($home_team['score']);?>                            </div>                            <div class="<?php echo esc_attr($styleTeam2Win);?>">                                <?php echo esc_html($away_team['name']).' '.esc_html($away_team['score']);?>                            </div>                        <?php endif;?>                    </div>                </td>            </tr>        <?php endforeach;?>        <tr>            <td><?php echo esc_html(__("Total points", "victorious"));?></td>            <td>                <div id="myTotalPoints">                    <?php echo esc_html($total_my_points);?>                </div>            </td>            <td>                <div id="YourTotalPoints">                    <?php echo esc_html($total_opponent_points);?>                </div>            </td>            <td>&nbsp;</td>        </tr>    </tbody>    <?php endif;?></table>