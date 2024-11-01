<?php foreach($aFights as $aFight):?>
<div class="fight_container">
    <div class="title_area">
        <div class="fight_number_title">*<?php echo esc_html(__("Fixture", 'victorious'));?> <?php echo esc_html($aFight['count']);?></div>
        <a onclick="return jQuery.fight.removeFight(this);" class="fight_action fight_remove" href="#">
            <img src="<?php echo esc_url(VICTORIOUS__PLUGIN_URL_IMAGE.'delete.png');?>" alt="Delete" title="Delete" />
        </a>&nbsp;&nbsp;
        <a onclick="return jQuery.fight.addFight(this);" class="fight_action fight_add" href="#">
            <img src="<?php echo esc_url(VICTORIOUS__PLUGIN_URL_IMAGE.'add.png');?>" alt="Add" title="Add" />
        </a>
        <input type="hidden" name="val[fight][]" class="fight" value="" />
        <input type="hidden" data-name="fightID" value="<?php echo esc_html($aFight['fightID']);?>" />
    </div>
    <table>
        <tr class="for_normal_fight">
            <th>
                <span class="for_fighter"><?php echo esc_html(__("Fighter", 'victorious'));?> 1</span>
                <span class="for_team"><?php echo esc_html(__("Team", 'victorious'));?> 1</span>
            </th>
            <th>
                <span class="for_fighter"><?php echo esc_html(__("Fighter", 'victorious'));?> 2</span>
                <span class="for_team"><?php echo esc_html(__("Team", 'victorious'));?> 2</span>
            </th>
        </tr>
        <tr class="for_normal_fight">
            <td>
                <select data-name="fighterID1" data-sel="<?php echo esc_html($aFight['fighterID1']);?>" class="cbfighter for_fighter"></select>
                <select data-name="fighterID1" data-sel="<?php echo esc_html($aFight['fighterID1']);?>" style="display: none" class="cbteam for_team form-control mw-100"></select>
            </td>
            <td>
                <select data-name="fighterID2" data-sel="<?php echo esc_html($aFight['fighterID2']);?>" class="cbfighter for_fighter"></select>
                <select data-name="fighterID2" data-sel="<?php echo esc_html($aFight['fighterID2']);?>" style="display: none" class="cbteam for_team form-control mw-100"></select>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="table">
                    <h3 class="vc-tabpane-title">
                        <?php echo esc_html(__("Fixture Name"));?>  <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span>:
                    </h3>
                    <input type="text" class="form-control" data-name="fight_name" value="<?php echo esc_html($aFight['name']);?>" size="40"/>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="table">
                    <h3 class="vc-tabpane-title">
                        <?php echo esc_html(__("Start Date"));?>  <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span>:
                    </h3>
                    <div class="d-flex align-items-center">
                        <input type="text" class="form-control w-75" class="fightDatePicker" data-name="fight_startDate" value="<?php echo esc_attr($aFight['startDateOnly']);?>" size="40"/>
                        <span class="mx-2"><?php echo esc_html(__("Hour", 'victorious'));?>:</span>
                        <select data-name="fight_startHour" class="form-control w-25">
                            <?php foreach($aPoolHours as $aPoolHour):?>
                            <option value="<?php echo esc_html($aPoolHour);?>" <?php echo esc_attr($aFight['startHour'] == $aPoolHour ? 'selected="true"' : '');?>><?php echo esc_html($aPoolHour);?></option>
                            <?php endforeach;?>
                        </select>
                        <span class="mx-2"><?php echo esc_html(__("Minute", 'victorious'));?>:</span>                                         
                        <select data-name="fight_startMinute" class="form-control w-25">
                            <?php foreach($aPoolMinutes as $aPoolMinute):?>
                            <option value="<?php echo esc_html($aPoolMinute);?>" <?php echo esc_attr($aFight['startMinute'] == $aPoolMinute ? 'selected="true"' : '');?>><?php echo esc_html($aPoolMinute);?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
        <tr class="for_fighter">
            <td colspan="2">
                <div class="table">
                    <h3 class="vc-tabpane-title d-inline-block w-200">
                        <?php echo esc_html(__("Championship Fight", 'victorious'));?>:
                    </h3>
                    <div class="checkbox-control d-inline-block mt-0 ml-4">
                        <input type="checkbox" data-name="champFight" <?php echo isset($aFight['champFight']) && $aFight['champFight'] == 'YES' ? 'checked="true"' : '';?> value="1" id="champFight" />
                        <span class="checkmark"></span>
                    </div>
                </div>
            </td>
        </tr>
        <tr class="for_fighter">
            <td colspan="2">
                <div class="table">
                    <h3 class="vc-tabpane-title d-inline-block w-200">
                        <?php echo esc_html(__("Amateur Fight", 'victorious'));?>:
                    </h3>
                    <div class="checkbox-control d-inline-block mt-0 ml-4">
                        <input type="checkbox" data-name="amateurFight" <?php echo isset($aFight['amateurFight']) && $aFight['amateurFight'] == 'YES' ? 'checked="true"' : '';?> value="1" id="amateurFight" />
                        <span class="checkmark"></span>
                    </div>
                </div>
            </td>
        </tr>
        <tr class="for_fighter">
            <td colspan="2">
                <div class="table">
                    <h3 class="vc-tabpane-title d-inline-block w-200">
                        <?php echo esc_html(__("Main Card Fight", 'victorious'));?>:
                    </h3>
                    <div class="checkbox-control d-inline-block mt-0 ml-4">
                        <input type="checkbox" data-name="mainFight" <?php echo isset($aFight['mainFight']) && $aFight['mainFight'] == 'YES' ? 'checked="true"' : '';?> value="1" id="mainFight" />
                        <span class="checkmark"></span>
                    </div>
                </div>
            </td>
        </tr>
        <tr class="for_fighter">
            <td colspan="2">
                <div class="table">
                    <h3 class="vc-tabpane-title d-inline-block w-200">
                        <?php echo esc_html(__("Preliminary Card Fight", 'victorious'));?>:
                    </h3>
                    <div class="checkbox-control d-inline-block mt-0 ml-4">
                        <input type="checkbox" data-name="prelimFight" <?php echo isset($aFight['prelimFight']) && $aFight['prelimFight'] == 'YES' ? 'checked="true"' : '';?> value="1" id="prelimFight" />
                        <span class="checkmark"></span>
                    </div>
                </div>
            </td>
        </tr>
        <tr class="for_fighter">
            <td colspan="2">
                <div class="table">
                    <h3 class="vc-tabpane-title">
                        <?php echo esc_html(__("Round", 'victorious'));?>:
                    </h3>
                    <select data-name="rounds" class="form-control">
                        <option value="">--</option>
                        <?php foreach($aRounds as $aRound):?>
                        <option value="<?php echo esc_html($aRound);?>" <?php echo isset($aFight['rounds']) && $aFight['rounds'] == $aRound ? 'selected="true"' : '';?>><?php echo esc_html($aRound);?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </td>
        </tr>
        <tr class="for_sportbook">
            <td colspan="2">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <h3 class="vc-tabpane-title">
                            <?php echo esc_html(__("Team 1 win odd", 'victorious'));?>:
                        </h3>
                        <input type="text" class="form-control" data-name="team1_win" value="<?php echo esc_html($aFight['team1_win']);?>" size="40"/>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h3 class="vc-tabpane-title">
                            <?php echo esc_html(__("Team 2 win odd", 'victorious'));?>:
                        </h3>
                        <input type="text" class="form-control" data-name="team2_win" value="<?php echo esc_html($aFight['team2_win']);?>" size="40"/>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h3 class="vc-tabpane-title">
                            <?php echo esc_html(__("Draw odd", 'victorious'));?>:
                        </h3>
                        <input type="text" class="form-control" data-name="team_draw" value="<?php echo esc_html($aFight['team_draw']);?>" size="40"/>
                    </div>                   
                </div>
            </td>
        </tr>
        <tr class="for_sportbook">
            <td colspan="2">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <h3 class="vc-tabpane-title">
                            <?php echo esc_html(__("Over/Under", 'victorious'));?>:
                        </h3>
                        <input type="text" class="form-control" data-name="total_over_under" value="<?php echo esc_html($aFight['total_over_under']);?>" size="40"/>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h3 class="vc-tabpane-title">
                            <?php echo esc_html(__("Over odd", 'victorious'));?>:
                        </h3>
                        <input type="text" class="form-control" data-name="total_over" value="<?php echo esc_html($aFight['total_over']);?>" size="40"/>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h3 class="vc-tabpane-title">
                            <?php echo esc_html(__("Under odd", 'victorious'));?>:
                        </h3>
                        <input type="text" class="form-control" data-name="total_under" value="<?php echo esc_html($aFight['total_under']);?>" size="40"/>
                    </div>                                                    
                </div>
            </td>
        </tr>
        <tr class="for_sportbook">
            <td colspan="2">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h3 class="vc-tabpane-title">
                            <?php echo esc_html(__("Team 1 spread odd", 'victorious'));?>:
                        </h3>
                        <input type="text" class="form-control" data-name="team1_spread" value="<?php echo esc_html($aFight['team1_spread']);?>" size="40"/>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h3 class="vc-tabpane-title">
                            <?php echo esc_html(__("Team 2 spread odd", 'victorious'));?>:
                        </h3>
                        <input type="text" class="form-control" data-name="team2_spread" value="<?php echo esc_html($aFight['team2_spread']);?>" size="40"/>
                    </div>                                
                </div>
            </td>
        </tr>
        <tr class="for_sportbook">
            <td colspan="2">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h3 class="vc-tabpane-title">
                            <?php echo esc_html(__("Team 1 spread points odd", 'victorious'));?>:
                        </h3>
                        <input type="text" class="form-control" data-name="team1_spread_points" value="<?php echo esc_html($aFight['team1_spread_points']);?>" size="40"/>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h3 class="vc-tabpane-title">
                            <?php echo esc_html(__("Team 2 spread points odd", 'victorious'));?>:
                        </h3>
                        <input type="text" class="form-control" data-name="team2_spread_points" value="<?php echo esc_html($aFight['team2_spread_points']);?>" size="40"/>
                    </div>                                
                </div>
            </td>
        </tr>
    </table>
</div>
<?php endforeach;?>