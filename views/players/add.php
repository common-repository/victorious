<div class="wrap vc-wrap">

    <h2>

        <?php echo !$bIsEdit ? esc_html(__("Add Players", 'victorious')) : esc_html(__("Edit Players", 'victorious'));?>
        <a class="add-new-h2" href="<?php echo esc_url(self::$url_select);?>"><?php echo esc_html(__("Select sport", 'victorious'));?></a>
        <a class="add-new-h2" href="<?php echo esc_url(self::$url);?>"><?php echo esc_html(__("Manage Players", 'victorious'));?></a>
        <?php if($bIsEdit):?>

        <a class="add-new-h2" href="<?php echo self::$urladdnew;?>"><?php echo esc_html(__("Add new", 'victorious'));?></a>

        <?php endif;?>

    </h2>
    <?php include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";?>
    <?php if($checkAPIToken === true):?>
        <div class="vc-tabpane-container-header">
            <?php echo esc_html(__("Create New Players", 'victorious'));?>
        </div>
        <div class="vc-tabpane-container">
            <?php echo settings_errors();?>

            <input type="hidden" id="teamsData" value='<?php echo str_replace("'", "*", json_encode($aTeams));?>' />

            <input type="hidden" id="positionsData" value='<?php echo str_replace("'", "\'", json_encode($aPositions));?>' />

            <input type="hidden" id="selectTeam" value='<?php echo esc_attr($aForms['team_id']);?>' />

            <input type="hidden" id="selectPosition" value='<?php echo esc_attr($aForms['position_id']);?>' />

            <input type="hidden" id="listMotocrossOrg" value='<?php echo json_encode($aListMotocross);?>' />

            <form method="post" action="" enctype="multipart/form-data">

                <input type="hidden" name="val[id]" value="<?php echo esc_attr($aForms['id']);?>" />

                <?php if(isset($aForms) && $aForms['siteID'] > 0 || !$bIsEdit):;?>
                <div class="vc-dashboard-item border-white pb-0">

                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Image", 'victorious'));?></h3>

                    <?php if(!empty($aForms['image'])):?>

                        <div class="p_4" id="js_slide_current_image">

                            <img src="<?php echo esc_url($aForms['full_image_path']);?>" width="80px" height="80px" alt="<?php echo esc_html($aForms['name']);?>" />

                            <?php if(isset($aForms) && $aForms['siteID'] > 0):;?>

                            <br />

                            <a href="#" onclick="jQuery.admin.newImage(); return false;"><?php echo esc_html(__("Click here to upload new image", 'victorious'));?></a>

                            <?php endif;?>

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

                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Organization"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>

                    <?php if(!empty($aSports)):?>

                        <select id="org" class="form-control" name="val[org_id]" onchange="jQuery.players.loadTeams();jQuery.players.loadPositions();">

                        <?php foreach($aSports as $aSport):?>

                            <?php if(!empty($aSport['child'])):?>

                            <option disabled="true"><?php echo esc_html($aSport['name']);?></option>

                            <?php foreach($aSport['child'] as $aOrg):?>

                                <option value="<?php echo esc_html($aOrg['id']);?>" style="padding-left: 20px" <?php if($aForms['org_id'] == $aOrg['id']):?>selected="true"<?php endif;?>>

                                    <?php echo esc_html($aOrg['name']);?>

                                </option>

                            <?php endforeach;?>

                            <?php endif;?>

                        <?php endforeach;?>

                        </select>

                    <?php endif;?>

                </div>

                <div class="vc-dashboard-item border-white pb-0">

                    <h3 class="vc-tabpane-title"><?php echo "Teams";?></h3>

                    <div id="htmlTeams"></div>

                </div>

                <div class="vc-dashboard-item border-white pb-0">

                    <h3 class="vc-tabpane-title"><?php echo "Position";?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>

                    <div id="htmlPositions"></div>

                </div>
                <?php endif;?>
                <div class="vc-dashboard-item border-white pb-0">

                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Indicator", 'victorious'));?></h3>

                    <?php if(!empty($indicators)):?>

                    <select name="val[indicator_id]" class="form-control">

                        <option value="0"><?php echo esc_html(__("None", 'victorious'));?></option>

                        <?php foreach($indicators as $indicator):?>

                        <option <?php echo esc_attr($aForms['indicator_id'] == $indicator['id'] ? 'selected="true"' : '');?> value="<?php echo esc_html($indicator['id']);?>">

                            <?php echo esc_html($indicator['name']);?>

                        </option>

                        <?php endforeach;?>

                    </select>

                    <?php endif;?>

                </div>

                <div class="vc-dashboard-item border-white pb-0">

                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Name"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>

                    <input type="text" name="val[name]" class="regular-text ltr form-control" value="<?php echo esc_attr($aForms['name']);?>" <?php if(isset($aForms) && $aForms['siteID'] == 0):?>disabled="true"<?php endif;?> />

                </div>
                <div class="vc-dashboard-item border-white pb-0">

                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Salary"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>

                    <input id="salary" type="text" name="val[salary]" class="regular-text ltr form-control" value="<?php echo !empty($aForms['salary']) ?  number_format(esc_attr($aForms['salary'])) : 0;?>" onkeyup="this.value = accounting.formatNumber(this.value)" />

                </div>
                <div class="vc-dashboard-item border-white pb-0">

                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Country"));?> <span class="description"></h3>

                    <input id="country" type="text" name="val[country]" class="regular-text ltr form-control" value="<?php echo esc_attr($aForms['country']);?>" />

                </div>
                <div class="vc-dashboard-item border-white pb-0">

                    <h3 class="vc-tabpane-title d-inline-block"><?php echo esc_html(__("Privateer"));?> <span class="description"></h3>
                    
                    <label class="checkbox-control d-inline-block mt-0 ml-4">
                        <input id="is_privateers" type="checkbox" name="val[is_privateers]"  value="1"<?php echo  $aForms['is_privateers']?"checked":"";?> />
                        <span class="checkmark"></span>
                    </label>

                </div>

                <?php submit_button(); ?>

            </form>
        </div>
    <?php endif;?>
</div>



<script type="text/javascript">

jQuery(window).load(function(){

    jQuery.players.setData();

    jQuery.players.loadTeams();

    jQuery.players.loadPositions();

})

</script>