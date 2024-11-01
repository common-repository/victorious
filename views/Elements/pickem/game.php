<div id="main" class="site-main site-info">
    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">
            <article class="hentry">
                <div class="vc-section">
                    <div class="p-3">
                        <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'pick_header.php');?>
                        <div class="vc-pickem-compare mt-5 p-2">
                            <form id="formData">
                                <input type="hidden" value="<?php echo esc_attr($aLeague['poolID']);?>" name="poolID">
                                <input type="hidden" value="<?php echo esc_attr($aLeague['leagueID']);?>" name="leagueID">
                                <input type="hidden" value="<?php echo esc_attr($entry_number);?>" name="entry_number">
                                <?php if($aLeague['gameType'] == 'BOTHTEAMSTOSCORE'): ?>
                                    <table border="0" class="table table-striped table-bordered table-responsive table-condensed tb-submit">
                                        <tbody>
                                        <?php foreach($aFights as $aFight):?>
                                            <?php
                                            if($aFight['both_teams_score']){
                                                $aInfo = explode(',',$aFight['both_teams_score']);
                                                $team1 = $aInfo[0];
                                                $team1 = explode('-',$team1);
                                                if(count($aInfo) > 1){
                                                    $team2 = $aInfo[1];
                                                    $team2 = explode('-',$team2);
                                                }else{
                                                    $team2[] = '';
                                                    $team2[] = '';
                                                }

                                            }else{
                                                $team1[] = '';
                                                $team1[] = '';
                                                $team2[] = '';
                                                $team2[] = '';
                                            }
                                            ?>
                                            <tr>
                                                <td style="text-align:center;width:30%">
                                                    <br><?php echo esc_html(__("H:", "victorious"))." ".esc_html($aFight['name1']);?>
                                                    <p style="font-weight: bold;"><?php echo esc_html(__('To Score','victorious')) ?></p>
                                                    <input style="float:none;" type="radio" class="fightID" value="Yes-<?php echo esc_attr($aFight['fighterID1']);?>" name="<?php echo esc_attr($aFight['fighterID1']); ?>_choose<?php echo esc_attr($aFight['fightID']);?>" data-fightid="<?php echo esc_attr($aFight['fightID']);?>" <?php if($team1[0] == 'Yes' && $team1['1'] == $aFight['fighterID1']):?>checked="checked"<?php endif;?> <?php if($aFight['started'] == 1):?>disabled="true"<?php endif;?>> Yes
                                                    <input style="float:none;margin-left: 5px;" type="radio" class="fightID" value="No-<?php echo esc_attr($aFight['fighterID1']);?>" name="<?php echo esc_attr($aFight['fighterID1']); ?>_choose<?php echo esc_attr($aFight['fightID']);?>" data-fightid="<?php echo esc_attr($aFight['fightID']);?>" <?php if($team1[0] == 'No' && $team1['1'] == $aFight['fighterID1']):?>checked="checked"<?php endif;?> <?php if($aFight['started'] == 1):?>disabled="true"<?php endif;?>> No
                                                </td>
                                                <td style="text-align:center;vertical-align: middle">
                                                    <br> VS
                                                    <br><?php echo VIC_DateTranslate($aFight['startDate']);?>
                                                </td>
                                                <td style="text-align:center;width:30%">
                                                    <br><?php echo esc_html(__("A:", "victorious"))." ".esc_html($aFight['name2']);?>
                                                    <p style="font-weight: bold;"><?php echo esc_html(__('To Score','victorious'));?></p>
                                                    <input style="float:none;" type="radio" class="fightID" value="Yes-<?php echo esc_attr($aFight['fighterID2']);?>" name="<?php echo esc_attr($aFight['fighterID2']); ?>_choose<?php echo esc_attr($aFight['fightID']);?>" data-fightid="<?php echo esc_attr($aFight['fightID']);?>" <?php if($team2[0] == 'Yes' && $team2['1'] == $aFight['fighterID2']):?>checked="checked"<?php endif;?> <?php if($aFight['started'] == 1):?>disabled="true"<?php endif;?>>Yes
                                                    <input style="float:none;margin-left: 5px;" type="radio" class="fightID" value="No-<?php echo esc_attr($aFight['fighterID2']);?>" name="<?php echo esc_attr($aFight['fighterID2']); ?>_choose<?php echo esc_attr($aFight['fightID']);?>" data-fightid="<?php echo esc_attr($aFight['fightID']);?>" <?php if($team2[0] == 'No' && $team2['1'] == $aFight['fighterID2']):?>checked="checked"<?php endif;?> <?php if($aFight['started'] == 1):?>disabled="true"<?php endif;?>>No
                                                </td>
                                            </tr>
                                        <?php endforeach;?>
                                        </tbody>
                                    </table>
                                <?php elseif($aLeague['gameType'] == 'HOWMANYGOALS'): ?>
                                    <!--        2AU: 2 AND UNDER -->
                                    <!--        EX3: EXACTLY 3   -->
                                    <!--        OV3: 3 AND OVER  -->
                                    <table border="0" class="table table-striped table-bordered table-responsive table-condensed tb-submit">
                                        <?php foreach ($aFights as $aFight): ?>
                                            <tr>
                                                <td rowspan="2"><?php echo esc_html($aFight['name1']); ?></td>
                                                <td colspan="3"><?php echo VIC_DateTranslate($aFight['startDate']); ?></td>
                                                <td rowspan="2"><?php echo esc_html($aFight['name2']) ?></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?php echo esc_html(__('2 And Under','victorious'));?><br>
                                                    <input style="float:none;" type="radio" class="fightID" value="<?php echo '2AU';?>" name="choose<?php echo esc_attr($aFight['fightID']);?>" data-fightid="<?php echo esc_attr($aFight['fightID']);?>" <?php if($aFight['how_goals'] == '2AU'):?>checked="checked"<?php endif;?> <?php if($aFight['started'] == 1):?>disabled="true"<?php endif;?>>
                                                </td>
                                                <td>
                                                    <?php echo esc_html(__('Exactly 3','victorious'));?><br>
                                                    <input style="float:none;" type="radio" class="fightID" value="<?php echo 'EX3';?>" name="choose<?php echo esc_attr($aFight['fightID']);?>" data-fightid="<?php echo esc_attr($aFight['fightID']);?>" <?php if($aFight['how_goals'] == 'EX3'):?>checked="checked"<?php endif;?> <?php if($aFight['started'] == 1):?>disabled="true"<?php endif;?>>
                                                </td>
                                                <td>
                                                    <?php echo esc_html(__('Over 3','victorious'));?><br>
                                                    <input style="float:none;" type="radio" class="fightID" value="<?php echo 'OV3';?>" name="choose<?php echo esc_attr($aFight['fightID']);?>" data-fightid="<?php echo esc_attr($aFight['fightID']);?>" <?php if($aFight['how_goals'] == 'OV3'):?>checked="checked"<?php endif;?> <?php if($aFight['started'] == 1):?>disabled="true"<?php endif;?>>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                    </table>
                                <?php elseif($aLeague['gameType'] != 'PICKEM' && $aLeague['gameType'] != VICTORIOUS_GAME_TYPE_PICKSPREAD):?>
                                    <table border="0" class="table table-striped table-bordered table-responsive table-condensed">
                                        <tbody>
                                        <?php foreach($aFights as $aFight):?>
                                            <tr>
                                                <td style="text-align:center;width:30%">
                                                    <?php echo esc_attr($aFight['allow_spread'] ? $aFight['team1_spread_points'] : '');?>
                                                    <?php echo esc_attr($aFight['allow_moneyline'] ? $aFight['team1_moneyline'] : '');?>
                                                    <?php if(!empty($aFight['full_image_path1'])):?>
                                                        <img src="<?php echo esc_url($aFight['full_image_path1']);?>" style="height:80px" />
                                                    <?php endif;?>
                                                    <br><?php echo esc_html(__("H:", "victorious"))." ".esc_html($aFight['name1']);?>
                                                    <br>&nbsp;
                                                    <input type="radio" class="fightID" value="<?php echo esc_attr($aFight['fighterID1']);?>" name="winner<?php echo esc_attr($aFight['fightID']);?>" data-fightid="<?php echo esc_attr($aFight['fightID']);?>" <?php if($aFight['winnerID'] == $aFight['fighterID1']):?>checked="checked"<?php endif;?> <?php if($aFight['started'] == 1):?>disabled="true"<?php endif;?>>
                                                </td>
                                                <td style="text-align:center;vertical-align: middle">
                                                    <?php echo esc_html($aFight['allow_spread'] ? __('Spread').'<br><br>' : '');?>
                                                    <?php echo esc_html($aFight['allow_moneyline'] ? __('Money Line').'<br><br>' : '');?>
                                                    VS
                                                    <br><?php echo VIC_DateTranslate($aFight['startDate']);?>
                                                </td>
                                                <td style="text-align:center;width:30%">
                                                    <?php echo esc_html($aFight['allow_spread'] ? $aFight['team2_spread_points'] : '');?>
                                                    <?php echo esc_html($aFight['allow_moneyline'] ? $aFight['team2_moneyline'] : '');?>
                                                    <?php if(!empty($aFight['full_image_path2'])):?>
                                                        <img src="<?php echo esc_url($aFight['full_image_path2']);?>" style="height:80px" />
                                                    <?php endif;?>
                                                    <br><?php echo esc_html(__("A:", "victorious"))." ".esc_html($aFight['name2']);?>
                                                    <br>&nbsp;
                                                    <input type="radio" class="fightID" value="<?php echo esc_attr($aFight['fighterID2']);?>" name="winner<?php echo esc_attr($aFight['fightID']);?>" data-fightid="<?php echo esc_attr($aFight['fightID']);?>" <?php if($aFight['winnerID'] == $aFight['fighterID2']):?>checked="checked"<?php endif;?> <?php if($aFight['started'] == 1):?>disabled="true"<?php endif;?>>
                                                </td>
                                                <?php if($aMethods != null):?>
                                                    <td style="text-align:center;vertical-align: middle">
                                                        <?php if($aMethods != null):?>
                                                            <select onchange="checkMethod(this.value,<?php echo esc_attr($aFight['fightID']);?>)" class="form-control method" data-id="<?php echo esc_attr($aFight['fightID']);?>" id="method<?php echo esc_attr($aFight['fightID']);?>" name="method<?php echo esc_attr($aFight['fightID']);?>" style="width:205px" <?php if($aFight['started'] == 1):?>disabled="true"<?php endif;?>>
                                                                <option value="-1"><?php echo esc_html(__("-- Select Method --", 'victorious'));?></option>
                                                                <?php foreach($aMethods as $aMethod):?>
                                                                    <option value="<?php echo esc_attr($aMethod["methodID"]);?>" <?php if($aFight['methodID'] == $aMethod["methodID"]):?>selected="true"<?php endif;?>>
                                                                        <?php echo esc_html($aMethod["description"]);?>
                                                                    </option>
                                                                <?php endforeach;?>
                                                            </select>
                                                        <?php endif;?>
                                                        <?php if($aRounds != null):?>
                                                            <select id="round<?php echo esc_attr($aFight['fightID']);?>" class="form-control mt-2" name="round<?php echo esc_attr($aFight['fightID']);?>" style="width:205px" <?php if($aFight['started'] == 1):?>disabled="true"<?php endif;?>>
                                                                <option value="-1"><?php echo esc_html(__("-- Select Round --", 'victorious'));?></option>
                                                                <?php foreach($aRounds as $aRound):?>
                                                                    <option value="<?php echo esc_attr($aRound);?>" <?php if($aFight['roundID'] == $aRound):?>selected="true"<?php endif;?>>
                                                                        <?php echo esc_html($aRound);?>
                                                                    </option>
                                                                <?php endforeach;?>
                                                            </select>
                                                        <?php endif;?>
                                                        <?php if($aMinutes != null):?>
                                                            <select onchange="checkMinute(this.value,<?php echo esc_attr($aFight['fightID']);?>)" class="form-control mt-2 minute" data-id="<?php echo esc_attr($aFight['fightID']);?>" id="minute<?php echo esc_attr($aFight['fightID']);?>" name="minute<?php echo esc_attr($aFight['fightID']);?>" style="width:205px" <?php if($aFight['started'] == 1):?>disabled="true"<?php endif;?>>
                                                                <option value="-1"><?php echo esc_html(__("-- Select Minute --", 'victorious'));?></option>
                                                                <?php foreach($aMinutes as $aMinute):?>
                                                                    <option value="<?php echo esc_attr($aMinute["minuteID"]);?>" <?php if($aFight['minuteID'] == $aMinute["minuteID"]):?>selected="true"<?php endif;?>>
                                                                        <?php echo esc_html($aMinute["description"]);?>
                                                                    </option>
                                                                <?php endforeach;?>
                                                            </select>
                                                        <?php endif;?>
                                                    </td>
                                                <?php endif;?>
                                            </tr>
                                        <?php endforeach;?>
                                        </tbody>
                                    </table>
                                <?php elseif($aLeague['gameType'] == 'PICKEM' ||  $aLeague['gameType'] == VICTORIOUS_GAME_TYPE_PICKSPREAD): ?>
                                    <?php foreach($aFights as $aFight):?>
                                        <?php if($aLeague['allow_tie']): ?>
                                            <div class="vc-pickem-compare-row row">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-4">
                                                    <div class="text-center">
                                                        <?php echo VIC_DateTranslate($aFight['startDate']);?>
                                                    </div>
                                                </div>
                                                <div class="col-md-4"></div>
                                            </div>
                                        <?php endif; ?>
                                        <div class="vc-pickem-compare-row row">
                                            <div class="<?php echo esc_attr($aMethods != null ? 'col-md-3' : 'col-md-4');?>">
                                                <div class="vc-pickem-compare-item vc-select-winner <?php echo ($aFight['winnerID'] == $aFight['fighterID1']) ? 'active' : '';?>">
                                                    <?php echo esc_html($aFight['allow_spread'] ? $aFight['team1_spread'] : '');?>
                                                    <?php echo esc_html($aFight['allow_moneyline'] ? $aFight['team1_moneyline'] : '');?>
                                                    <?php if(!empty($aFight['full_image_path1'])):?>
                                                        <img src="<?php echo esc_url($aFight['full_image_path1']);?>" alt="">
                                                    <?php endif;?>
                                                    <div class="vc-pickem-compare-item-name"><?php echo esc_html(__("H:", "victorious"))." ".esc_html($aFight['name1']);?></div>
                                                    <input type="radio" class="fightID" value="<?php echo esc_attr($aFight['fighterID1']);?>" name="winner<?php echo esc_attr($aFight['fightID']);?>" style="display: none" data-fightid="<?php echo esc_attr($aFight['fightID']);?>" <?php if($aFight['winnerID'] == $aFight['fighterID1']):?>checked="checked"<?php endif;?> <?php if($aFight['started'] == 1):?>disabled="true"<?php endif;?>>
                                                </div>
                                            </div>
                                            <div class="<?php echo esc_attr($aMethods != null ? 'col-md-3' : 'col-md-4');?>">
                                                <div class="text-center vc-select-winner <?php echo esc_html($aFight['winnerID'] === 0 ? 'active' : '');?>">
                                                    <?php echo esc_html($aFight['allow_spread'] ? __('Spread').'<br><br>' : '');?>
                                                    <?php echo esc_html($aFight['allow_moneyline'] ? __('Money Line').'<br><br>' : '');?>
                                                    <?php if($aLeague['allow_tie']): ?>
                                                        <br><?php echo esc_html(__('Draw'));?>
                                                        <br><input type="radio" class="fightID" value="0" name="winner<?php echo esc_attr($aFight['fightID']);?>" style="display: none" data-fightid="<?php echo esc_attr($aFight['fightID']);?>" <?php if($aFight['winnerID'] ===0):?>checked="checked"<?php endif;?> <?php if($aFight['started'] == 1):?>disabled="true"<?php endif;?>>
                                                    <?php else: ?>
                                                        <br>Vs<br><?php echo VIC_DateTranslate($aFight['startDate']);?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="<?php echo esc_attr($aMethods != null ? 'col-md-3' : 'col-md-4');?>">
                                                <div class="vc-pickem-compare-item vc-select-winner <?php echo ($aFight['winnerID'] == $aFight['fighterID2']) ? 'active' : '';?>">
                                                    <?php echo esc_html($aFight['allow_spread'] ? $aFight['team2_spread'] : '');?>
                                                    <?php echo esc_html($aFight['allow_moneyline'] ? $aFight['team2_moneyline'] : '');?>
                                                    <?php if(!empty($aFight['full_image_path2'])):?>
                                                        <img src="<?php echo esc_url($aFight['full_image_path2']);?>" alt="">
                                                    <?php endif;?>
                                                    <div class="vc-pickem-compare-item-name"><?php echo esc_html(__("A:", "victorious"))." ".esc_html($aFight['name2']);?></div>
                                                    <input type="radio" class="fightID" value="<?php echo esc_attr($aFight['fighterID2']);?>" name="winner<?php echo esc_attr($aFight['fightID']);?>" style="display: none" data-fightid="<?php echo esc_attr($aFight['fightID']);?>" <?php if($aFight['winnerID'] == $aFight['fighterID2']):?>checked="checked"<?php endif;?> <?php if($aFight['started'] == 1):?>disabled="true"<?php endif;?>>
                                                </div>
                                            </div>
                                            <?php if($aMethods != null):?>
                                                <div class="col-md-3">
                                                    <?php if($aMethods != null):?>
                                                        <select onchange="checkMethod(this.value,<?php echo esc_attr($aFight['fightID']);?>)" class="form-control method" data-id="<?php echo esc_attr($aFight['fightID']);?>" id="method<?php echo esc_attr($aFight['fightID']);?>" name="method<?php echo esc_attr($aFight['fightID']);?>" style="width:205px" <?php if($aFight['started'] == 1):?>disabled="true"<?php endif;?>>
                                                            <option value="-1"><?php echo esc_html(__("-- Select Method --", 'victorious'));?></option>
                                                            <?php foreach($aMethods as $aMethod):?>
                                                                <option value="<?php echo esc_attr($aMethod["methodID"]);?>" <?php if($aFight['methodID'] == $aMethod["methodID"]):?>selected="true"<?php endif;?>>
                                                                    <?php echo esc_html($aMethod["description"]);?>
                                                                </option>
                                                            <?php endforeach;?>
                                                        </select>
                                                    <?php endif;?>
                                                    <?php if($aRounds != null):?>
                                                        <select id="round<?php echo esc_attr($aFight['fightID']);?>" class="form-control mt-2" name="round<?php echo esc_attr($aFight['fightID']);?>" style="width:205px" <?php if($aFight['started'] == 1):?>disabled="true"<?php endif;?>>
                                                            <option value="-1"><?php echo esc_html(__("-- Select Round --", 'victorious'));?></option>
                                                            <?php foreach($aRounds as $aRound):?>
                                                                <option value="<?php echo esc_attr($aRound);?>" <?php if($aFight['roundID'] == $aRound):?>selected="true"<?php endif;?>>
                                                                    <?php echo esc_html($aRound);?>
                                                                </option>
                                                            <?php endforeach;?>
                                                        </select>
                                                    <?php endif;?>
                                                    <?php if($aMinutes != null):?>
                                                        <select onchange="checkMinute(this.value,<?php echo esc_attr($aFight['fightID']);?>)" class="form-control mt-2 minute" data-id="<?php echo esc_attr($aFight['fightID']);?>" id="minute<?php echo esc_attr($aFight['fightID']);?>" name="minute<?php echo esc_attr($aFight['fightID']);?>" style="width:205px" <?php if($aFight['started'] == 1):?>disabled="true"<?php endif;?>>
                                                            <option value="-1"><?php echo esc_html(__("-- Select Minute --", 'victorious'));?></option>
                                                            <?php foreach($aMinutes as $aMinute):?>
                                                                <option value="<?php echo esc_attr($aMinute["minuteID"]);?>" <?php if($aFight['minuteID'] == $aMinute["minuteID"]):?>selected="true"<?php endif;?>>
                                                                    <?php echo esc_html($aMinute["description"]);?>
                                                                </option>
                                                            <?php endforeach;?>
                                                        </select>
                                                    <?php endif;?>
                                                </div>
                                            <?php endif;?>
                                        </div>
                                    <?php endforeach;?>
                                <?php endif;?>
                                <br/>
                                <?php if(strtolower($aLeague['gameType']) == 'picktie'):?>
                                    <?php if($aLeague['show_weekly_pick']):?>
                                        <?php echo esc_html(__("Select game", 'victorious'));?>
                                        <select name="predict_point_game" style="margin-bottom:7px;">
                                            <?php foreach ($aFights as $aFight):?>
                                                <option value="<?php echo esc_attr($aFight['fightID']);?>" <?php if($aFight['fightID'] == $aFight['predict_point_game']):?>selected="selected"<?php endif;?>>
                                                    <?php echo esc_html(__("H:", "victorious"))." ".esc_html($aFight['name1']).' vs '.esc_html(__("A:", "victorious"))." ".esc_html($aFight['name2']);?>
                                                </option>
                                            <?php endforeach;?>
                                        </select>
                                        <br/>
                                    <?php endif;?>
                                    <?php echo esc_html(__("Predict point total", 'victorious'));?>
                                    <input type="text" name="predict_point" value="<?php echo esc_attr($aFights[0]['predict_point']);?>"/>
                                    <br/>
                                    <?php if($aLeague['show_weekly_pick']):?>
                                        (<?php echo esc_html(__("If players tie for the first place prize, the player with the closest prediction to the game point total of the select game will win..", 'victorious'));?>)
                                    <?php else:?>
                                        (<?php echo esc_html(__("If players tie for the first place prize, the player with the closest prediction to the game point total of the final game will win..", 'victorious'));?>)
                                    <?php endif;?>
                                <?php endif;?>
                                <?php if($aLeague['allow_new_tie_breaker'] == 1): ?>
                                    <h3><?php echo esc_html(__('Tie Breaker','victorious'));?></h3>
                                    <input type="hidden" name="allow_new_tie_breaker" value="1">
                                    <table style="border:none;max-width: 500px;" class="table table-striped table-responsive table-condensed">
                                        <tr>
                                            <td>  <?php echo esc_html(__("Highest scoring team",'victorious'));?></td>
                                            <td>
                                                <select type="text" name="highest_score_team">
                                                    <?php foreach($aFights as $aFight): ?>
                                                        <option <?php if(!empty($pickInfo) && $pickInfo['highest_score_team'] == $aFight['fighterID1']){echo 'selected';} ?> value="<?php echo esc_attr($aFight['fighterID1']) ?>"><?php echo esc_html($aFight['name1']); ?></option>
                                                        <option <?php if(!empty($pickInfo) && $pickInfo['highest_score_team'] == $aFight['fighterID2']){echo 'selected';} ?> value="<?php echo esc_attr($aFight['fighterID2']) ?>"><?php echo esc_html($aFight['name2']); ?></option>

                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>  <?php echo esc_html(__("Player to score",'victorious'));?></td>
                                            <td>
                                                <select type="text" name="player_score" >
                                                    <?php foreach($aPlayers as $player): ?>
                                                        <option <?php if(!empty($pickInfo) && $pickInfo['player_score'] == $player['id']){echo 'selected';} ?> value="<?php echo esc_attr($player['id']); ?>"><?php echo esc_html($player['name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>  <?php echo esc_html(__("Total goals",'victorious'));?></td>
                                            <td><input type="text" name="total_goals" value="<?php if(!empty($pickInfo)){echo esc_attr($pickInfo['total_goals']);}else{echo '0';}?> "/></td>
                                        </tr>
                                    </table>
                                <?php endif; ?>
                            </form>
                            <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'pick_footer.php');?>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function(){
        jQuery.pickem.initPickem();
    })
</script>