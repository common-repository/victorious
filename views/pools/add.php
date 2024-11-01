<div class="wrap vc-wrap">

    <h2>

        <?php echo empty($bIsEdit) ? esc_html(__("Add Events", 'victorious')) : esc_html(__("Edit Events", 'victorious'));?>
        <a class="add-new-h2" href="<?php echo esc_url(self::$url);?>"><?php echo esc_html(__("Manage Events", 'victorious'));?></a>
        <?php if($bIsEdit):?>

        <a class="add-new-h2" href="<?php echo esc_url(self::$urladdnew);?>"><?php echo esc_html(__("Add New", 'victorious'));?></a>

        <?php endif;?>

    </h2>
    <?php include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";?>
    <?php if($checkAPIToken === true):?>
        <div class="vc-tabpane-container-header">
            <?php echo esc_html(__("Create New Event", 'victorious'));?>
        </div>
        <div class="vc-tabpane-container">
            <?php echo settings_errors();?>

            <form method="post" action="" enctype="multipart/form-data">

                <input type="hidden" id="sportData" value='<?php echo esc_attr($aSports);?>' />

                <input type="hidden" id="selType" value='<?php echo esc_attr($aForms['type']);?>' />

                <input type="hidden" id="selOrg" value='<?php echo esc_attr($aForms['organization']);?>' />

                <input type="hidden" id="positionData" value='<?php echo esc_attr($aPositions);?>' />

                <input type="hidden" id="lineupData" value='<?php echo esc_attr($aForms['lineup']);?>' />
                <input type="hidden" id="gameTypeSoccer" value='<?php echo esc_attr($aGameTypeSoccers);?>' />

                <input type="hidden" name="val[poolID]" value="<?php echo esc_attr($aForms['poolID']);?>" />
                <input type="hidden" id="motocross_id" value="<?php echo esc_attr($motocross_id); ?>">
                <input type="hidden" id="motocross_orgs" value='<?php echo esc_attr($aListMotocross); ?>'>
                <input type="hidden" id="allow_motocross" value="<?php echo esc_attr($allow_motocross); ?>">
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Image", 'victorious'));?></h3>
                    <?php if(isset($aForms) && isset($aForms['image']) && $aForms['image'] != null):?>

                    <div class="p_4" id="js_slide_current_image">

                        <img src="<?php echo esc_url($aForms['full_image_path']);?>" width="80px" height="80px" alt="<?php echo esc_html($aForms['poolName']);?>" />

                        <br />

                        <a href="#" onclick="jQuery.admin.newImage(); return false;"><?php echo esc_html(__("Image"));?><?php echo esc_html(__("Click here to upload new image", 'victorious'));?></a>

                    </div>

                    <?php endif;?>

                    <div id="js_submit_upload_image" <?php if(isset($aForms) && isset($aForms['image']) && $aForms['image'] != null):?>style="display:none"<?php endif;?>>
                        <div class="vc-custom-file">
                            <input type="file" id='image' name="image" />
                        </div>
                        <p><?php echo esc_html(__("You can upload a jpg, gif or png file", 'victorious'));?></p>              
                    </div>
                </div>
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Name"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                    <input type="text" name="val[poolName]" class="regular-text ltr form-control" value="<?php echo esc_html($aForms['poolName']);?>" />

                </div>
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Sport"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                    <?php if($aSports != null):?>

                    <select id="poolOrgs" class="form-control" name="val[organization]" onchange="jQuery.fight.displayType(); jQuery.fight.loadPosition(); jQuery.fight.loadFightersOrTeams();">

                        <?php foreach($aSports as $aSport):?>

                            <?php if(!empty($aSport['child']) && is_array($aSport['child']) && $aSport['child'] != null):?>

                            <option disabled="true"><?php echo esc_html($aSport['name']);?></option>

                            <?php foreach($aSport['child'] as $aOrg):?>

                                <?php if($aOrg['is_active'] == 1):?>

                                <option value="<?php echo esc_html($aOrg['id']);?>" is_team="<?php echo esc_html($aOrg['is_team']);?>" only_playerdraft="<?php echo esc_html($aOrg['only_playerdraft']);?>" is_round="<?php echo esc_html($aOrg['is_round']);?>" upload_photo="<?php echo esc_html($aOrg['upload_photo']);?>" style="padding-left: 20px" <?php if($aForms['organization'] == $aOrg['id']):?>selected="true"<?php endif;?>>

                                    <?php echo esc_html($aOrg['name']);?>

                                </option>

                                <?php endif;?>

                            <?php endforeach;?>

                            <?php endif;?>

                        <?php endforeach;?>

                    </select>

                    <?php endif;?>
                </div>
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Start Date"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                    <div class="d-flex align-items-center">
                        <input type="text" name="val[startDate]" value="<?php echo esc_html($aForms['startDateOnly']);?>" id="startDate" size="40" maxlength="150" class="form-control w-75"/>
                        <span class="mx-2"><?php echo esc_html(__("Hour", 'victorious'));?></span> 
                        <select name="val[startHour]" class="form-control w-25">

                            <?php foreach($aPoolHours as $aPoolHour):?>

                            <option value="<?php echo esc_html($aPoolHour);?>" <?php echo esc_html($aForms['startHour'] == $aPoolHour ? 'selected="true"' : '');?>><?php echo esc_html($aPoolHour);?></option>

                            <?php endforeach;?>

                        </select>
                        <span class="mx-2"><?php echo esc_html(__("Minute", 'victorious'));?></span>  
                        <select name="val[startMinute]" class="form-control w-25">

                            <?php foreach($aPoolMinutes as $aPoolMinute):?>

                            <option value="<?php echo esc_html($aPoolMinute);?>" <?php echo esc_attr($aForms['startMinute'] == $aPoolMinute ? 'selected="true"' : '');?>><?php echo esc_html($aPoolMinute);?></option>

                            <?php endforeach;?>

                        </select>       
                    </div>                           
                </div>
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Cut Date"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                    <div class="d-flex align-items-center">
                        <input type="text" name="val[cutDate]" value="<?php echo esc_html($aForms['cutDateOnly']);?>" id="cutDate" size="40" maxlength="150" class="form-control w-75" />
                        <span class="mx-2"><?php echo esc_html(__("Hour", 'victorious'));?></span> 
                        <select name="val[cutHour]" class="form-control w-25">
                            <?php foreach($aPoolHours as $aPoolHour):?>

                            <option value="<?php echo esc_html($aPoolHour);?>" <?php echo esc_attr($aForms['cutHour'] == $aPoolHour ? 'selected="true"' : '');?>><?php echo esc_html($aPoolHour);?></option>

                            <?php endforeach;?>

                        </select>
                        <span class="mx-2"><?php echo esc_html(__("Minute", 'victorious'));?></span>  
                        <select name="val[cutMinute]" class="form-control w-25">

                            <?php foreach($aPoolMinutes as $aPoolMinute):?>

                            <option value="<?php echo esc_html($aPoolMinute);?>" <?php echo esc_attr($aForms['cutMinute'] == $aPoolMinute ? 'selected="true"' : '');?>><?php echo esc_html($aPoolMinute);?></option>

                            <?php endforeach;?>

                        </select>        
                    </div>
                </div>
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title d-inline-block"><?php echo esc_html(__("Live Event", 'victorious'));?></h3>                                 
                    <label class="checkbox-control d-inline-block mt-0 ml-4">                   
                        <input type="checkbox" name="val[live_pool]" <?php echo esc_attr($aForms['live_pool']? 'checked="true"' : '');?> value="1" />
                        <span class="checkmark"></span>
                    </label>
                </div>
                <div class="vc-dashboard-item border-white pb-0 for_playerdraft salary_cap">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Salary Cap", 'victorious'));?></h3>
                    <input type="text" class="form-control w-75" name="val[salary_remaining]" value="<?php echo   number_format($aForms['salary_remaining']);?>" onkeyup="this.value = accounting.formatNumber(this.value)"/>
                    <span><?php echo esc_html(__('( 0 means unlimited )', 'victorious'));?></span>
                </div>
                <div class="vc-dashboard-item border-white pb-0 for_playerdraft">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Lineup", 'victorious'));?></h3>
                    <div id="lineupResult"></div>
                </div>
                <div class="vc-dashboard-item border-white pb-0 for_round">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Rounds", 'victorious'));?></h3>
                    <input type="text" class="form-control" name="val[rounds]" value="<?php echo esc_html($aForms['rounds']);?>" />
                </div>
                <div class="vc-dashboard-item border-white pb-0 exclude_fixture">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Fixture",'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                    <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW.'pools/fights.php');?>
                </div>
                <div class="vc-dashboard-item border-white pb-0 for_motocross">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Laps", 'victorious')); ?> <span class="description">(<?php echo esc_html(__("required", 'victorious')); ?>)</span></h3>
                    <div class="motocross_container">
                        <input class="form-control motocross_name" type="text" style="width:200px"  name="val[rounds]" value="<?php echo esc_html($aSelectedMotocross);  ?>">
                    </div>
                </div>
                <?php submit_button(); ?>
            </form>
        </div>
    <?php endif;?>
</div>