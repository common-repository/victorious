<div class="wrap vc-wrap">

    <h2>

        <?php echo !$bIsEdit ? esc_html(__("Add Contests", 'victorious')) : esc_html(__("Edit Contests", 'victorious'));?>
        <a class="add-new-h2" href="<?php echo esc_url(self::$url);?>"><?php echo esc_html(__("Manage Contests", 'victorious'));?></a>
        <?php if($bIsEdit):?>

        <a class="add-new-h2" href="<?php echo esc_url(self::$urladdnew);?>"><?php echo esc_html(__("Add New", 'victorious'));?></a>

        <?php endif;?>

    </h2>
    <?php include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";?>
    <?php if($checkAPIToken === true):?>
        <div class="vc-tabpane-container-header">
            <?php echo esc_html(__("Create New Contest", 'victorious'));?>
        </div>
        <div class="vc-tabpane-container">
        <?php if($aSports != null && $aPools != null && $aPools != '[]'):?>
            <?php echo settings_errors();?>

            <form id="formadmin_createcontest" method="post" action="" enctype="multipart/form-data">

                <input type="hidden" id="leagueIDData" name="leagueID" value="<?php echo esc_html($aForms['leagueID']);?>" />
                <input type="hidden" id="gameTypeData" value="<?php echo esc_html(strtolower($aForms['gameType']));?>" />
                <input type="hidden" id="listMotocrossOrg" value="<?php echo json_encode($list_motocross_sports); ?>">
                <input type="hidden" id="motocross_id" value="<?php echo esc_html($motocross_id); ?>">
                <input type="hidden" id="selectPool" value='<?php echo esc_html($aForms['poolID']);?>' />
                <input type="hidden" id="selectFight" value='<?php echo !empty($aForms['fixtures']) ? json_encode(explode(',', $aForms['fixtures'])) : json_encode(array());?>' />
                <input type="hidden" id="selectRound" value='<?php echo !empty($aForms['rounds']) ? json_encode(explode(',', $aForms['rounds'])) : json_encode(array());?>' />
                <input type="hidden" id="plugin_url_image" value='<?php echo VICTORIOUS__PLUGIN_URL_IMAGE?>' />
                <input type="hidden" name="mixing_game_type" value="playerdraft" />
                <input type="hidden" id="is-motocross-game" value='<?php echo esc_html($is_motocross_game);?>' />
                <input type="hidden" id="is-single-game" value='<?php echo esc_html($is_single_game);?>' />
                <input type="hidden" id="is-mixing-game" value='<?php echo esc_html($is_mixing_game);?>' />
                <?php if($is_mixing): ?>
                <input type="hidden" id="type_create_contest" value="mixing" />
                <?php endif; ?>
                <script id="sportData">
                    <?php echo json_encode($aSports);?>
                </script>
                <script id="poolData">
                    <?php echo json_encode($aPools);?>
                </script>
                <script id="fightData">
                    <?php echo json_encode($aFights);?>
                </script>
                <script id="positionData">
                    <?php echo json_encode($aPositions);?>
                </script>
                <script id="lineupData">
                    <?php echo $aForms['lineup'];?>
                </script>
                <script id="lineupNoPositionData">
                    <?php echo json_encode($aForms['lineup_no_position'] != null ? esc_html($aForms['lineup_no_position']) : array());?>
                </script>
                <script id="teamLineupData">
                    <?php echo json_encode($team_lineups);?>
                </script>
                <script id="roundData">
                    <?php echo json_encode($aRounds);?>
                </script>
                <script id="mixingPoolData">
                    <?php echo json_encode($aMixingPools != null ? $aMixingPools : array());?>
                </script>                      
                <div class="vc-dashboard-item border-white pb-0 sport_type_group" id="sportType" style="display: none">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__('Sport type'));?></h3>
                    <select name="sport_type" class="form-control" onchange="jQuery.createcontest.selectSportType()">
                        <?php if($is_single_game): ?>
                            <option value="single"><?php echo esc_html(__('Single Sport', 'victorious'));?></option>
                        <?php endif; ?>
                        <?php if($is_mixing_game && $allow_mixing_sport): ?>
                            <option value="mixing" <?php if(isset($aForms['is_mixing']) && $aForms['is_mixing']):?>selected="true"<?php endif;?>>
                                <?php echo esc_html(__('Mixing sport', 'victorious'));?>
                            </option>
                        <?php endif; ?>
                        <?php if($is_motocross_game && $allow_motocross): ?>
                        <option value="motocross" <?php if($is_league_motocross):?>selected="true"<?php endif;?>><?php echo esc_html(__('Moto Cross', 'victorious'));?></option>
                        <?php endif; ?>
                    </select>
                </div>

                <?php if($is_mixing_game && $allow_mixing_sport): ?>
                    <div class="vc-dashboard-item border-white pb-0 mixing_sport_group">
                        <h3 class="vc-tabpane-title"><?php echo esc_html(__('Date'));?></h3>
                        <select class="form-control" name="listDate" id="listDate" onchange="jQuery.createcontest.mixingLoadFixtures(true)">
                            <?php foreach($aDates as $date => $pools): ?>
                            <option value="<?php echo esc_html($date);?>" <?php if(isset($aForms['start_date']) && $date == date('Y-m-d', strtotime($aForms['start_date']))):?>selected="true"<?php endif;?>>
                                    <?php echo esc_html(date('M j Y',  strtotime($date)));?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="vc-dashboard-item border-white pb-0">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="vc-tabpane-title"><?php echo esc_html(__('Name your league', 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>

                            <input type="text" class="form-control" id="leaguename" name="name" placeholder="<?php echo esc_html(__('Name your league', 'victorious'));?>" value="<?php echo esc_html($aForms['name']);?>">
                        </div>
                        <div class="col-md-6 single_sport_group">
                            <h3 class="vc-tabpane-title"><?php echo esc_html(__('Pick your sport'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>

                            <?php if($aSports != null):?>

                                <select id="sports" class="form-control" name="orgID" onchange="jQuery.createcontest.loadPools();">

                                <?php foreach($aSports as $aSport):?>

                                    <?php if(!empty($aSport['child']) && is_array($aSport['child']) && $aSport['child'] != null):?>

                                    <option disabled="true"><?php echo esc_html($aSport['name']);?></option>

                                    <?php foreach($aSport['child'] as $aOrg):?>

                                        <?php if($aOrg['is_active'] == 1):?>

                                        <option value="<?php echo esc_html($aOrg['id']);?>" only_playerdraft="<?php echo esc_attr($aOrg['only_playerdraft']);?>" playerdraft="<?php echo esc_attr($aOrg['is_playerdraft']);?>" is_team="<?php echo esc_attr($aOrg['is_team']);?>" is_round="<?php echo esc_attr($aOrg['is_round']);?>" is_picktie="<?php echo esc_attr($aOrg['is_picktie']);?>" upload_photo="<?php echo esc_attr($aOrg['upload_photo']);?>" style="padding-left: 20px" <?php if($aForms['organizationID'] == $aOrg['id']):?>selected="true"<?php endif;?>>

                                            <?php echo esc_html($aOrg['name']);?>

                                        </option>

                                        <?php endif;?>

                                    <?php endforeach;?>

                                    <?php endif;?>

                                <?php endforeach;?>

                                </select>

                            <?php endif;?>
                        </div>
                    </div>                  
                </div>
                <div class="vc-dashboard-item border-white pb-0">
                    <div class="row">
                        <div class="col-md-6 single_sport_group event_group">
                            <h3 class="vc-tabpane-title"><?php echo esc_html(__('Events', 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>

                            <div id="poolDates"></div>
                        </div>
                        <div class="col-md-6 single_sport_group">
                            <h3 class="vc-tabpane-title"><?php echo esc_html(__("Game Type", 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>

                            <select class="form-control" name="game_type" id="game_type" onchange="jQuery.createcontest.gameTypeAttr()">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="vc-dashboard-item border-white pb-0 for_team" id="wrapFixtures" style="display: none">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="vc-tabpane-title"><?php echo esc_html(__('Fixture Selection', 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                            <div id="fixtureDiv"></div>
                        </div>
                    </div>
                </div>

                <div valign="top" id="wrapRounds" class="vc-dashboard-item border-white pb-0 single_sport_group">

                    <td id="roundDiv"></td>

                </div>
            
                <div class="vc-dashboard-item border-white pb-0" id="wrapPlayerRestriction" style="display:none">

                    <div class="row">
                        <div class="col-md-6 for_playerdraft">
                            <h3 class="vc-tabpane-title"><?php echo esc_html(__("Player Restriction", 'victorious'));?></h3>


                            <input type="text" class="form-control" name="player_restriction" onkeyup="this.value = accounting.formatNumber(this.value)" value="<?php echo esc_html($aForms['player_restriction']);?>">
                            <p>
                                <?php echo esc_html(__('Restriction on how many players can be picked from a single team. If this value is 0 or not set, there is no restriction for picking players', 'victorious'));?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <?php if($trade_player):?>
                                <div class="vc-dashboard-item border-white pb-0 for_playerdraft" style="display:none">
                                    <h3 class="vc-tabpane-title d-inline-block mt-0"><?php echo esc_html(__('Trade Player', 'victorious'));?></h3>
                                    <label class="checkbox-control d-inline-block mt-0 ml-4">                
                                    <input type="checkbox" name="trade_player" value="1" <?php if($aForms['trade_player']):?>checked="true" <?php endif;?> />
                                        <span class="checkmark"></span>
                                    </label>                     
                                </div>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
                <div class="vc-dashboard-item border-white pb-0 for_playerdraft for_portfolio for_olddraft salary_remaining">

                    <h3 class="vc-tabpane-title"><?php echo esc_html(__('Salary Cap', 'victorious'));?></h3>

                    <input type="text" class="form-control" name="salary_remaining" value="<?php echo number_format($aForms['salary_remaining']);?>"  onkeyup="this.value = accounting.formatNumber(this.value)">

                    <p class="mb-0"><?php echo esc_html(__('IF this is not set, NO SALARY cap will be used. End users will be able to pick any player.', 'victorious'));?></p>

                    <label class="checkbox-control">
                        <?php echo esc_html(__('Unlimited', 'victorious'));?>
                        <input type="checkbox" name="salary_cap_unlimited" value="1" <?php if($aForms['salary_cap_unlimited']):?>checked="true" <?php endif;?>/>                        <span class="checkmark"></span>
                    </label>

                </div>
                <div class="vc-dashboard-item border-white pb-0 for_portfolio">

                    <h3 class="vc-tabpane-title"><?php echo esc_html(__('Multiplier', 'victorious'));?></h3>


                    <input type="text" class="form-control" name="porfolio_multiplier" value="<?php echo esc_html($aForms['porfolio_multiplier']);?>">

                    <p><?php echo esc_html(__('First position \'s point will be multiplied with xx value, leave empty or < 1 to disable', 'victorious'));?></p>

                </div>
                <!-- Nhung field duoi day ko biet de o dau -->
                <?php if(!empty($is_show_weekly_pick)): ?>
                    <div class="vc-dashboard-item border-white pb-0 show_weekly_pick">
                        <h3 class="vc-tabpane-title">
                            <?php echo esc_html(__("Show weekly picks", 'victorious'));?> <span class="description"></span>
                        </h3>
                        <label class="checkbox-control">
                            <input type="checkbox" class="form-control" id="show_weekly_pick" name="show_weekly_pick" value="1" <?php echo !empty($aForms['show_weekly_pick']) ? 'checked':'';  ?>>
                            <span class="checkmark"></span>
                        </label>
                    </div>
                <?php endif;?>


                <?php if($global_setting['specify_dates_for_season_long_contests'] == 1):?>
                    <div class="vc-dashboard-item border-white pb-0 specify_dates_for_season_long" style="display: none;">
                        <h3 class="vc-tabpane-title">
                            <?php echo esc_html(__('Start date', 'victorious'));?>
                        </h3>
                        <div class="table">
                            <div class="table_left" style="width:auto">
                                <input type="text" class="form-control" id="yearly_contest_start" name="yearly_contest_start" readonly style="width: auto" value="<?php echo !empty($aForms['yearly_contest_start']) && strtotime($aForms['yearly_contest_start']) !== false && strtotime($aForms['yearly_contest_start']) > 0 ? date("m/d/Y", strtotime($aForms['yearly_contest_start'])) : '';?>">
                            </div>
                            <div class="table_right">
                            </div>
                        </div>
                    </div>
                    <div class="vc-dashboard-item border-white pb-0 specify_dates_for_season_long" style="display: none;">
                        <h3 class="vc-tabpane-title">
                            <?php echo esc_html(__('End date', 'victorious'));?>
                        </h3>
                        <div class="table">
                            <div class="table_left" style="width:auto">
                                <input type="text" class="form-control" id="yearly_contest_end" name="yearly_contest_end" readonly style="width: auto" value="<?php echo !empty($aForms['yearly_contest_end']) && strtotime($aForms['yearly_contest_end']) !== false && strtotime($aForms['yearly_contest_end']) > 0 ? date("m/d/Y", strtotime($aForms['yearly_contest_end'])) : '';?>">
                            </div>
                            <div class="table_right">
                            </div>
                        </div>
                    </div>
                <?php endif;?>
                <?php //include VICTORIOUS__PLUGIN_DIR_VIEW.'Elements/create_contest_admin_live_draft.php';?>
                <?php //include VICTORIOUS__PLUGIN_DIR_VIEW.'Elements/create_contest_admin_survival.php';?>


                <?php if(!empty($allowCustomSpread)):?>
                    <div id="spreadpoint" class="vc-dashboard-item border-white pb-0" style="display: none">
                        <h3 class="vc-tabpane-title"><?php echo esc_html(__("Spread point", 'victorious'));?></h3>
                        <table>
                            <thead>
                            <tr>
                                <th></th>
                                <th style="text-align: center"><?php echo esc_html(__('Team 1 spread points'));?><br/>(EX: +30)</th>
                                <th style="text-align: center"><?php echo esc_html(__('Team 2 spread points'));?><br/>(EX: -30)</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <p><?php echo esc_html(__('If all values are empty, default value will be set', 'victorious'));?></p>
                    </div>
                <?php endif;?>
                <div class="vc-dashboard-item border-white pb-0" id="ultimate_pick_point" style="display: none">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Ultimate point", 'victorious'));?></h3>
                    <table>
                        <thead>
                        <tr>
                            <th></th>
                            <th style="text-align: center"><?php echo esc_html(__('Over/under'));?><br/>(EX: 30)</th>
                            <th style="text-align: center"><?php echo esc_html(__('Team 1 spread points'));?><br/>(EX: +30)</th>
                            <th style="text-align: center"><?php echo esc_html(__('Team 2 spread points'));?><br/>(EX: -30)</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="vc-dashboard-item border-white pb-0" id="wrapOptionType" style="display: none">

                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Options", 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>

                    <select class="form-control" disabled="true" name="option_type" id="optionType" onchange="jQuery.createcontest.optionType()">
                        <option value="salary" <?php if(strtolower($aForms['option_type']) == 'salary'):?>selected="true"<?php endif;?>>

                            <?php echo esc_html(__('Salary', 'victorious'));?>

                        </option>
                        <option value="group" <?php if(strtolower($aForms['option_type']) == 'group'):?>selected="true"<?php endif;?>>

                            <?php echo esc_html(__('Group', 'victorious'));?>

                        </option>

                    </select>

                </div>

                <div class="vc-dashboard-item border-white pb-0 for_playerdraft for_portfolio for_group" id="wrapLineup">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Lineup", 'victorious'));?></h3>
                    <div id="lineupResult"></div>
                    <p><?php echo esc_html(__('If all values are 0, it will get default lineup', 'victorious'));?></p>
                </div>

                <div class="vc-dashboard-item border-white pb-0 for_portfolio for_olddraft" style="display: none">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__('Start date', 'victorious'));?></h3>
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" class="form-control" id="contest_cut_date" name="contest_cut_date" disabled value="<?php echo !empty($aForms['contest_cut_date']) ? date("m/d/Y", strtotime($aForms['contest_cut_date'])) : '';?>">
                        </div>
                        <div class="col-md-2">
                            <select name="contest_cut_hour" class="form-control">
                                <?php foreach($hours as $hour):?>
                                    <option value="<?php echo esc_html($hour);?>" <?php if(!empty($aForms['contest_cut_date']) && date('H', strtotime($aForms['contest_cut_date'])) == $hour):?>selected="selected"<?php endif;?>><?php echo esc_html($hour);?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="contest_cut_minute" class="form-control">
                                <?php foreach($minutes as $minute):?>
                                    <option value="<?php echo esc_html($minute);?>" <?php if(!empty($aForms['contest_cut_date']) && date('i', strtotime($aForms['contest_cut_date'])) == $minute):?>selected="selected"<?php endif;?>><?php echo esc_html($minute);?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="vc-dashboard-item border-white pb-0 for_portfolio for_olddraft" style="display: none">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__('End date', 'victorious'));?></h3>
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" class="form-control" id="contest_end_date" name="contest_end_date" disabled value="<?php echo !empty($aForms['contest_end_date']) ? date("m/d/Y", strtotime($aForms['contest_end_date'])) : '';?>">
                        </div>
                        <div class="col-md-2">
                            <select name="contest_end_hour" class="form-control">
                                <?php foreach($hours as $hour):?>
                                    <option value="<?php echo esc_html($hour);?>" <?php if(!empty($aForms['contest_end_date']) && date('H', strtotime($aForms['contest_end_date'])) == $hour):?>selected="selected"<?php endif;?>><?php echo esc_html($hour);?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="contest_end_minute" class="form-control">
                                <?php foreach($minutes as $minute):?>
                                    <option value="<?php echo esc_html($minute);?>" <?php if(!empty($aForms['contest_end_date']) && date('i', strtotime($aForms['contest_end_date'])) == $minute):?>selected="selected"<?php endif;?>><?php echo esc_html($minute);?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="vc-dashboard-item border-white pb-0">
                    <div class="row">
                        <div class="col-md-6 radio">

                            <h3 class="vc-tabpane-title"><?php echo esc_html(__('Opponent', 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>

                            <label class="radio-control">
                                
                                <?php echo esc_html(__('Anyone', 'victorious'));?>

                                <input type="radio" name="opponent" id="oppoRadio1" value="public" checked="true">
                                <span class="checkmark"></span>
                            </label>

                            <label class="radio-control">

                                <?php echo esc_html(__('Friends Only', 'victorious'));?>

                                <input type="radio" name="opponent" id="oppoRadio1" value="private" <?php if(strtolower($aForms['opponent']) == "private"):?>checked="true"<?php endif;?>>
                                <span class="checkmark"></span>
                            </label>
                        </div>

                        <div class="col-md-6 admin_contest_type radio" id="wrapContestType">

                            <h3 class="vc-tabpane-title"><?php echo esc_html(__('Contest Type', 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                            <label class="radio-control">
                                <?php echo esc_html(__('Head to head', 'victorious'));?> 	
                                <input id="typeRadios7"  type="radio" name="type" value="head2head" checked="true">
                                <span class="checkmark"></span>
                            </label>
                            <label class="radio-control">
                                <?php echo esc_html(__('League', 'victorious'));?>	
                                <input id="typeRadios8" type="radio" name="type" value="league" <?php if($aForms['size'] > 2):?>checked="true"<?php endif;?>>
                                <span class="checkmark"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="vc-dashboard-item border-white pb-0">
                    <div class="row">
                        <div class="password col-md-6" style="display: none" id="password_content">

                            <h3 class="vc-tabpane-title"><?php echo esc_html(__('Password', 'victorious'));?></h3>

                            <input type="text" class="form-control" disabled="disabled" id="password" name="password" value="<?php echo esc_html($aForms['password']);?>">

                        </div>
                        <div <?php if($aForms['size'] == '' || $aForms['size'] == 2):?>style="display: none"<?php endif;?> class="col-md-6 leagueDiv" onchange="jQuery.createcontest.calculatePrizes()">

                            <h3 class="vc-tabpane-title"><?php echo esc_html(__('League Size', 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>

                            <select class="form-control" name="leagueSize" id="leagueSize">

                                <?php foreach($aLeagueSizes as $aLeagueSize):?>

                                    <option value="<?php echo esc_html($aLeagueSize);?>" <?php if($aForms['size'] == $aLeagueSize):?>selected="true"<?php endif;?>>

                                        <?php echo esc_html($aLeagueSize);?>

                                    </option>

                                <?php endforeach;?>

                            </select>

                        </div>
                        <div class="col-md-6 for_playoff" style="display: none">
                            <h3 class="vc-tabpane-title"><?php echo esc_html(__('League Size', 'victorious'));?></h3>
                            <select class="form-control" name="leagueSize" id="leagueSize" onchange="jQuery.createcontest.calculatePrizes()">
                                <option value="10">10</option>
                                <option value="12">12</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="vc-dashboard-item border-white pb-0 for_playoff">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="vc-tabpane-title"><?php echo esc_html(__('Wild Card round start draft date', 'victorious'));?></h3>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" class="form-control disabled" name="playoff_wildcard_start_date" id="playoff_wildcard_draft_start" readonly style="width: auto" value="<?php echo isset($aForms['playoff_wildcard_start_date']) ? $aForms['playoff_wildcard_start_date'] : '';?>">
                        </div>
                        <div class="col-md-1">
                            <?php echo esc_html(__("Hour", 'victorious'));?>
                        </div>
                        <div class="col-md-1">
                            <select class="form-control" name="playoff_wildcard_start_hour">
                                <?php for($i = 0; $i <= 23; $i++):?>
                                    <option value="<?php echo $i;?>" <?php echo isset($aForms['playoff_wildcard_start_hour']) && $aForms['playoff_wildcard_start_hour'] == $i ? 'selected="selected"' : '';?>>
                                        <?php echo $i;?>
                                    </option>
                                <?php endfor;?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <?php echo esc_html(__("Minute", 'victorious'));?>
                        </div>
                        <div class="col-md-1">
                            <select class="form-control" name="playoff_wildcard_start_minute">
                                <?php for($i = 0; $i <= 59; $i++):?>
                                    <option value="<?php echo $i;?>" <?php echo isset($aForms['playoff_wildcard_start_minute']) && $aForms['playoff_wildcard_start_minute'] == $i ? 'selected="selected"' : '';?>>
                                        <?php echo $i;?>
                                    </option>
                                <?php endfor;?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="vc-dashboard-item border-white pb-0 for_playoff">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="vc-tabpane-title"><?php echo esc_html(__('Divisional round start draft date', 'victorious'));?></h3>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" class="form-control disabled" name="playoff_divisional_start_date" id="playoff_divisional_draft_start" readonly style="width: auto" value="<?php echo isset($aForms['playoff_divisional_start_date']) ? $aForms['playoff_divisional_start_date'] : '';?>">
                        </div>
                        <div class="col-md-1">
                            <?php echo esc_html(__("Hour", 'victorious'));?>
                        </div>
                        <div class="col-md-1">
                            <select class="form-control" name="playoff_divisional_start_hour">
                                <?php for($i = 0; $i <= 23; $i++):?>
                                    <option value="<?php echo $i;?>" <?php echo isset($aForms['playoff_divisional_start_hour']) && $aForms['playoff_divisional_start_hour'] == $i ? 'selected="selected"' : '';?>>
                                        <?php echo $i;?>
                                    </option>
                                <?php endfor;?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <?php echo esc_html(__("Minute", 'victorious'));?>
                        </div>
                        <div class="col-md-1">
                            <select class="form-control" name="playoff_divisional_start_minute">
                                <?php for($i = 0; $i <= 59; $i++):?>
                                    <option value="<?php echo $i;?>" <?php echo isset($aForms['playoff_divisional_start_minute']) && $aForms['playoff_divisional_start_minute'] == $i ? 'selected="selected"' : '';?>>
                                        <?php echo $i;?>
                                    </option>
                                <?php endfor;?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="vc-dashboard-item border-white pb-0 for_playoff">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="vc-tabpane-title"><?php echo esc_html(__('Conference round start draft date', 'victorious'));?></h3>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" class="form-control disabled" name="playoff_conference_start_date" id="playoff_conference_draft_start" readonly style="width: auto" value="<?php echo isset($aForms['playoff_conference_start_date']) ? $aForms['playoff_conference_start_date'] : '';?>">
                        </div>
                        <div class="col-md-1">
                            <?php echo esc_html(__("Hour", 'victorious'));?>
                        </div>
                        <div class="col-md-1">
                            <select class="form-control" name="playoff_conference_start_hour">
                                <?php for($i = 0; $i <= 23; $i++):?>
                                    <option value="<?php echo $i;?>" <?php echo isset($aForms['playoff_conference_start_hour']) && $aForms['playoff_conference_start_hour'] == $i ? 'selected="selected"' : '';?>>
                                        <?php echo $i;?>
                                    </option>
                                <?php endfor;?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <?php echo esc_html(__("Minute", 'victorious'));?>
                        </div>
                        <div class="col-md-1">
                            <select class="form-control" name="playoff_conference_start_minute">
                                <?php for($i = 0; $i <= 59; $i++):?>
                                    <option value="<?php echo $i;?>" <?php echo isset($aForms['playoff_conference_start_minute']) && $aForms['playoff_conference_start_minute'] == $i ? 'selected="selected"' : '';?>>
                                        <?php echo $i;?>
                                    </option>
                                <?php endfor;?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="vc-dashboard-item border-white pb-0 for_playoff">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="vc-tabpane-title"><?php echo esc_html(__('Super Bowl round start draft date', 'victorious'));?></h3>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <input type="text" class="form-control disabled" name="playoff_super_bowl_start_date" id="playoff_super_bowl_draft_start" readonly style="width: auto" value="<?php echo isset($aForms['playoff_super_bowl_start_date']) ? $aForms['playoff_super_bowl_start_date'] : '';?>">
                        </div>
                        <div class="col-md-1">
                            <?php echo esc_html(__("Hour", 'victorious'));?>
                        </div>
                        <div class="col-md-1">
                            <select class="form-control" name="playoff_super_bowl_start_hour">
                                <?php for($i = 0; $i <= 23; $i++):?>
                                    <option value="<?php echo $i;?>" <?php echo isset($aForms['playoff_super_bowl_start_hour']) && $aForms['playoff_super_bowl_start_hour'] == $i ? 'selected="selected"' : '';?>>
                                        <?php echo $i;?>
                                    </option>
                                <?php endfor;?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <?php echo esc_html(__("Minute", 'victorious'));?>
                        </div>
                        <div class="col-md-1">
                            <select class="form-control" name="playoff_super_bowl_start_minute">
                                <?php for($i = 0; $i <= 59; $i++):?>
                                    <option value="<?php echo $i;?>" <?php echo isset($aForms['playoff_super_bowl_start_minute']) && $aForms['playoff_super_bowl_start_minute'] == $i ? 'selected="selected"' : '';?>>
                                        <?php echo $i;?>
                                    </option>
                                <?php endfor;?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="vc-dashboard-item border-white pb-0 for_playoff">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="vc-tabpane-title"><?php echo esc_html(__('Countdown time (in second)', 'victorious'));?></h3>
                            <input type="number" class="form-control"  name="playoff_draft_countdown" value="<?php echo isset($aForms['playoff_draft_countdown']) ? $aForms['playoff_draft_countdown'] : 90;?>">
                        </div>
                    </div>
                </div>
                <div class="vc-dashboard-item border-white pb-0 for_playoff">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="vc-tabpane-title"><?php echo esc_html(__('Minute prior draft (in minute)', 'victorious'));?></h3>
                            <input type="number" class="form-control"  name="playoff_minute_prior_draft" value="<?php echo isset($aForms['playoff_minute_prior_draft']) ? $aForms['playoff_minute_prior_draft'] : 10;?>">
                        </div>
                    </div>
                </div>
                <div class="vc-dashboard-item border-white pb-0 leagueDiv" style="display: none">
                    <div class="row">
                        <div class="col-md-6">
                            <div <?php if($aForms['size'] == '' || $aForms['size'] == 2):?>style="display: none"<?php endif;?> class="vc-dashboard-item border-white pb-0 leagueDiv">
                                <h3 class="vc-tabpane-title d-inline-block mt-0 w-200"><?php echo esc_html(__('Multi Entry', 'victorious'));?></h3>
                                <label class="checkbox-control d-inline-block mt-0 ml-4">
                                    <input type="checkbox" name="multi_entry" value="1" <?php if($aForms['multi_entry']):?>checked="true" <?php endif;?> id="multi_entry" onclick="jQuery.createcontest.loadSpecifyNumberOfMultiEntries()"/>                            
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div style="display: none" class="number_of_multi_entries">

                                <h3 class="vc-tabpane-title"><?php echo esc_html(__('Number of multi entries', 'victorious'));?></h3>

                                <input type="text" class="form-control" name="number_of_multi_entries" disabled="disabled" value="<?php echo esc_html($aForms['number_of_multi_entries']);?>">
                                <?php echo esc_html(__('(0 or empty value means unlimited)', 'victorious'));?>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="vc-dashboard-item border-white pb-0">
                    <div class="row">
                        <div class="col-md-6">

                            <h3 class="vc-tabpane-title"><?php echo esc_html(__('Entry Fee', 'victorious'));?></h3>

                            <select class="form-control" id="entry_fee" name="entry_fee" onchange="jQuery.createcontest.calculatePrizes()">

                                <option value="0"><?php echo esc_html(__('Free Practice', 'victorious'));?></option> 

                                <?php foreach($aEntryFees as $aEntryFee):?>

                                    <option value="<?php echo esc_html($aEntryFee);?>" <?php if(!empty($aForms['entry_fee']) && $aForms['entry_fee'] == $aEntryFee):?>selected="true"<?php endif;?>>

                                        <?php echo esc_html($aEntryFee);?>

                                    </option>
                                <?php endforeach;?>

                            </select>

                        </div>
                        <?php if(!empty($global_setting['allow_multiple_balances'])):?>
                            <div class="col-md-6 balance_type_group" <?php if(empty($aForms['entry_fee'])):?>style="display: none"<?php endif;?>>
                                <h3 class="vc-tabpane-title"><?php echo esc_html(__('Balance Type', 'victorious'));?></h3>
                                <select class="form-control" id="balance_type" name="balance_type_id" onchange="jQuery.createcontest.calculatePrizes()">
                                    <?php foreach($balance_types as $balance_type):?>
                                        <option value="<?php echo esc_html($balance_type['id']);?>" <?php if($aForms['balance_type_id'] == $balance_type['id']):?>selected="true"<?php endif;?> data-sign="<?php echo esc_html($balance_type['currency_code'].'|'.$balance_type['symbol']);?>" data-currency_position="<?php echo esc_html($balance_type['currency_position']);?>">
                                            <?php echo esc_html($balance_type['name']).' - '.esc_html($balance_type['symbol']);?>
                                        </option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        <?php endif;?>
                        
                    </div>                
                </div>
                <div <?php if($aForms['size'] == '' || $aForms['size'] == 2):?>style="display: none"<?php endif;?> class="vc-dashboard-item border-white pb-0 leagueDiv group_prize_structure">

                    <h3 class="vc-tabpane-title" scope="row"><?php echo esc_html(__('Prize Structure', 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                    <div class="radio">
                        <label class="radio-control">
                            <?php echo esc_html(__('Winner takes all', 'victorious'));?>
                            <input type="radio" name="structure" value="winnertakeall" checked="true" id="typeRadios9">
                            <span class="checkmark"></span>
                        </label>

                        <label class="radio-control">
                            <?php echo esc_html(__('Top 3 get prizes', 'victorious'));?>
                            <input id="typeRadios10" type="radio" name="structure" value="top3" <?php if(strtolower($aForms['prize_structure']) == "top_3"):?>checked="true"<?php endif;?>>
                            <span class="checkmark"></span>
                        </label>

                        <div id="top3Percent" style="<?php if(strtolower($aForms['prize_structure']) != "top_3"):?>display: none;<?php endif;?>margin-left: 50px;">
                            <label style="width: 30px;display: inline-block">1st:</label> <input type="text" value="<?php echo !empty($aForms['first_percent']) ? esc_html($aForms['first_percent']) : get_option('victorious_first_place_percent');?>" name="first_percent" id="firstPercent" onkeyup="jQuery.createcontest.calculatePrizes()"><br/>
                            <label style="width: 30px;display: inline-block">2nd:</label> <input type="text" value="<?php echo !empty($aForms['second_percent']) ? esc_html($aForms['second_percent']) : get_option('victorious_second_place_percent');?>" name="second_percent" id="secondPercent" onkeyup="jQuery.createcontest.calculatePrizes()"><br/>
                            <label style="width: 30px;display: inline-block">3rd:</label> <input type="text" value="<?php echo !empty($aForms['third_percent']) ? esc_html($aForms['third_percent']) : get_option('victorious_third_place_percent');?>" name="third_percent" id="thirdPercent" onkeyup="jQuery.createcontest.calculatePrizes()"><br/>
                            <?php echo esc_html(__('Default values are set by values in settings', 'victorious'));?><br/>
                            <?php echo esc_html(__('Top 3\'s percentages are required, if one of them is set to 0, default value will be set (1st: 50, 2nd: 30, 3rd: 20)', 'victorious'));?>
                        </div>

                        <label class="radio-control">
                            <?php echo esc_html(__('Multi payout', 'victorious'));?>
                            <input id="typeRadios11" type="radio" name="structure" value="multi_payout" <?php if(strtolower($aForms['prize_structure']) == "multi_payout"):?>checked="true"<?php endif;?>>
                            <span class="checkmark"></span>
                            <a id="addPayouts" onclick="return jQuery.createcontest.addPayouts();" href="#" <?php if(empty($aForms['payouts'])):?>style="display: none"<?php endif;?>>
                                <img title="<?php echo esc_html(__("Add", 'victorious'));?>" alt="<?php echo esc_html(__("Add", 'victorious'));?>" src="<?php echo VICTORIOUS__PLUGIN_URL_IMAGE.'add.png';?>">
                            </a>
                        </label>


                        <div id="payoutExample" style="display: none;margin-left: 50px;">
                            <?php echo esc_html(__("Click on the + button to create a tier. You can set a range of players to receive a certain percentage of the payout.", 'victorious'));?>
                            <br/>
                            <?php echo esc_html(__('Example', 'victorious'));?>: <br/>
                            1st: <?php echo esc_html(__('From', 'victorious'));?>  1 <?php echo esc_html(__('to', 'victorious'));?> 1: 40%<br/>
                            2nd: <?php echo esc_html(__('From', 'victorious'));?>  2 <?php echo esc_html(__('to', 'victorious'));?> 2: 30%<br/>
                            3rd: <?php echo esc_html(__('From', 'victorious'));?>  3 <?php echo esc_html(__('to', 'victorious'));?> 3: 20%<br/>
                            4th - 6th: <?php echo esc_html(__('From', 'victorious'));?> 4 <?php echo esc_html(__('to', 'victorious'));?> 6: 10%<br/>
                            <?php echo esc_html(__('Total percent must be 100%', 'victorious'));?>
                        </div>

                        <div id="payouts" style="margin-left: 50px;">
                            <?php if(!empty($aForms['payouts'])):
                                $payouts = json_decode($aForms['payouts'], true);
                            ?>
                                <?php foreach($payouts as $payout):?>
                                    <div>
                                        <label style="display: inline-block;width: auto">From</label>
                                        <input type="text" onkeyup="jQuery.createcontest.calculatePrizes()" style="display: inline-block;width: 50px;padding: 2px 5px;text-align:center" value="<?php echo esc_html($payout['from']);?>" name="payouts_from[]">
                                        <label style="display: inline-block;width: auto">To</label>
                                        <input type="text" onkeyup="jQuery.createcontest.calculatePrizes()" style="display: inline-block;width: 50px;padding: 2px 5px;text-align:center" value="<?php echo esc_html($payout['to']);?>" name="payouts_to[]">
                                        <label style="display: inline-block;width: auto">:</label>
                                        <input type="text" onkeyup="jQuery.createcontest.calculatePrizes()" style="display: inline-block;width: 50px;padding: 2px 5px;text-align:center" value="<?php echo esc_html($payout['percent']);?>" name="percentage[]">
                                        <label style="display: inline-block;width: auto">%</label>
                                        <a href="#" onclick="return jQuery.createcontest.removePayouts(jQuery(this).parent());">
                                            <img src="<?php echo VICTORIOUS__PLUGIN_URL_IMAGE;?>delete.png" alt="<?php echo esc_html(__('Delete', 'victorious'));?>" title="<?php echo esc_html(__('Delete', 'victorious'));?>">
                                        </a>
                                    </div>
                                <?php endforeach;?>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
                <?php
                $show_payout = '';
                    if(empty($aForms['picksquares_payouts'])){
                        $show_payout = "display:none";
                    }
                ?>
                <div class="vc-dashboard-item border-white pb-0 picksquare_payout" style="<?php echo esc_html($show_payout); ?>">

                    <h3 class="vc-tabpane-title"><?php echo esc_html(__('Payouts', 'victorious'));?></h3>

                    <a id="pickSquareAddPayouts" onclick="return jQuery.createcontest.addPayoutsPickSquare();" href="#">
                    <img title="<?php echo esc_html(__("Add", 'victorious'));?>" alt="<?php echo esc_html(__("Add", 'victorious'));?>" src="<?php echo VICTORIOUS__PLUGIN_URL_IMAGE . 'add.png'; ?>">
                    </a>
                    <div id="picksquare_payouts" >
                        <?php if(!empty($aForms['picksquares_payouts'])):
                            $payouts = json_decode($aForms['picksquares_payouts'], true);
                        ?>
                        <?php foreach($payouts as $payout):?> 
                                <div>
                                    <input type="text"  style="display: inline-block;width: 150px;padding: 2px 5px;text-align:center" value="<?php echo esc_html($payout['name']);?>" name="payouts_name[]">
                                    <input type="text"  style="display: inline-block;width: 150px;padding: 2px 5px;text-align:center" value="<?php echo esc_html($payout['price']);?>" name="payouts_price[]">
                                    <label style="display: inline-block;width: auto"><?php echo VIC_GetCurrencySymbol();?></label>
                                    <a href="#" onclick="return jQuery.createcontest.removePayouts(jQuery(this).parent());">
                                        <img src="<?php echo VICTORIOUS__PLUGIN_URL_IMAGE;?>delete.png" alt="Delete" title="Delete">
                                    </a>
                                </div>
                        <?php endforeach;?>

                        <?php endif;  ?>
                    </div>

                </div>

                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__('Number of entries to close a contest', 'victorious'));?></h3>
                    <select class="form-control" name="entry_close_contest">
                        <?php foreach(range(1, 10) as $item):?>
                            <option value="<?php echo esc_html($item);?>" <?php if($aForms['entry_close_contest'] == $item):?>selected="true"<?php endif;?>>
                                <?php echo esc_html($item);?>
                            </option>

                        <?php endforeach;?>
                    </select>
                    <p>
                        <?php echo esc_html(__('If number of users enter contest is less than or equal this value, all picks will be removed and contest will be canceled.', 'victorious'));?>
                        <?php echo esc_html(__('It is only applied to free contest and only affect if value is not 0.', 'victorious'));?>
                    </p>
                </div>



                <div class="vc-dashboard-item border-white pb-0 payout_percentage" style="display: none">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__('Payout Percentage', 'victorious'));?></h3>
                    <input type="text" class="form-control" value="<?php echo !empty($aForms['winner_percent']) ? esc_html($aForms['winner_percent']) : get_option('victorious_winner_percent');?>" name="winner_percent" id="winnerPercent" onkeyup="jQuery.createcontest.calculatePrizes()"><br/>
                    <?php echo esc_html(__('Default value is set by value in settings', 'victorious'));?><br/>
                    <?php echo esc_html(__('If this value is set to 0, default value will be set to 90', 'victorious'));?>
                </div>
                <div class="vc-dashboard-item border-white pb-0 for_olddraft">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__('Insurance Fee (%)', 'victorious'));?></h3>
                    <input type="text" class="form-control" name="olddraft_insurance_fee" value="<?php echo esc_html($aForms['olddraft_insurance_fee']);?>">
                </div>

                <?php if(!empty($allow_guaranteed_prize)):?>
                    <?php include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/create_contest_guaranteed.php";?>
                <?php endif;?>
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title d-inline-block mt-0 w-200"><?php echo esc_html(__('Clone contest', 'victorious'));?></h3>
                    <label class="checkbox-control d-inline-block mt-0 ml-4">
                        <input type="checkbox" name="clone" value="1" <?php if($aForms['clone']):?>checked="true" <?php endif;?> /> 
                        <span class="checkmark"></span>
                    </label>     
                </div>                 
                <?php if(!empty($allowEditStartedContests)):?>
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title d-inline-block mt-0 w-200"><?php echo esc_html(__('Allow edit when started', 'victorious'));?></h3>
                    <label class="checkbox-control d-inline-block mt-0 ml-4">
                        <input type="checkbox" name="started_edit" value="1" <?php if($aForms['started_edit']):?>checked="true" <?php endif;?> />
                        <span class="checkmark"></span>
                    </label>     
                </div> 
                <?php endif;?>
                
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title d-inline-block mt-0 w-200"><?php echo esc_html(__('Refund on No Fill', 'victorious'));?></h3>
                    <label class="checkbox-control d-inline-block mt-0 ml-4">
                        <input type="checkbox" name="is_refund" value="1" <?php if(!isset($aForms['is_refund']) || $aForms['is_refund']):?>checked="true" <?php endif;?> />
                        <span class="checkmark"></span>
                    </label>     
                </div> 
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title d-inline-block mt-0 w-200"><?php echo esc_html(__('PayOut Winners', 'victorious'));?></h3>
                    <label class="checkbox-control d-inline-block mt-0 ml-4">
                    <input type="checkbox" name="is_payouts" value="1" <?php if(!isset($aForms['is_payouts']) || $aForms['is_payouts']):?>checked="true" <?php endif;?> />
                        <span class="checkmark"></span>
                    </label>     
                </div>
                <div class="vc-dashboard-item border-white pb-0 allow_select_tie" style="<?php echo (!isset($game_type) || $game_type != 'PICKEM')?'display:none;':''; ?>">
                    <h3 class="vc-tabpane-title d-inline-block mt-0 w-200"><?php echo esc_html(__("Allow Ties", 'victorious'));?> <span class="description"></span></h3>
                    <label class="checkbox-control d-inline-block mt-0 ml-4">
                        <input type="checkbox" id="allow_tie" name="allow_tie" value="1" <?php echo !empty($is_allow_tie) ? 'checked':'';  ?>>
                        <span class="checkmark"></span>
                    </label>
                </div>
                <div class="vc-dashboard-item border-white pb-0 allow_new_tie_breaker" style="<?php echo (!isset($game_type) || ($game_type != 'PICKEM' || $game_type != 'HOWMANYGOALS' || $game_type != 'BOTHTEAMSTOSCORE'))?'display:none;':''; ?>">
                    <h3 class="vc-tabpane-title d-inline-block mt-0 w-200"><?php echo esc_html(__("Allow new tie breaker", 'victorious'));?> <span class="description"></span></h3>
                    <label class="checkbox-control d-inline-block mt-0 ml-4">
                        <input type="checkbox" id="allow_new_tie_breaker" name="allow_new_tie_breaker" value="1" <?php echo !empty($is_allow_new_tie_breaker) ? 'checked':'';  ?>>
                        <span class="checkmark"></span>
                    </label>
                </div>
                <?php if(!empty($contest_only_rookies)):?>
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title d-inline-block mt-0 w-200"><?php echo esc_html(__('Only for rookies', 'victorious'));?></h3>
                    <label class="checkbox-control d-inline-block mt-0 ml-4">
                        <input type="checkbox" name="only_rookies" value="1" <?php if($aForms['only_rookies']):?>checked="true" <?php endif;?> />
                        <span class="checkmark"></span>
                    </label>     
                </div> 
                <?php endif;?>
                <div class="vc-dashboard-item border-white pb-0 prize_structure_group" <?php if(empty($aForms['entry_fee'])):?>style="display:none;"<?php endif;?>>

                    <h3 class="vc-tabpane-title"><?php echo esc_html(__('Prizes Structure', 'victorious'));?></h3>

                    <div name="prizesum" id="prizesum"></div>

                </div>
                <div class="vc-dashboard-item border-white pb-0">

                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Note", 'victorious'));?></h3>

                    <td>

                        <textarea rows="5" class="large-text code" name="note"><?php echo esc_textarea($aForms['note']);?></textarea>

                    </td>

                </div>

                <?php submit_button(); ?>

            </form>
        </div>
        <?php else:?>

        <?php echo esc_html(__("There are no events.", 'victorious'));?>

        <?php endif;?>
    <?php endif;?>
</div>

