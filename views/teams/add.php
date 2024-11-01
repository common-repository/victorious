<div class="wrap vc-wrap">

    <h2>

        <?php echo !$bIsEdit ? esc_html(__("Add Team", 'victorious')) : esc_html(__("Edit Team", 'victorious'));?>
        <a class="add-new-h2" href="<?php echo esc_url(self::$url_select);?>"><?php echo esc_html(__("Select sport", 'victorious'));?></a>
        <a class="add-new-h2" href="<?php echo esc_url(self::$url);?>"><?php echo esc_html(__("Manage teams", 'victorious'));?></a>
        <?php if($bIsEdit):?>

        <a class="add-new-h2" href="<?php echo self::$urladdnew;?>"><?php echo esc_html(__("Add New", 'victorious'));?></a>

        <?php endif;?>

    </h2>
    <?php include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";?>
    <?php if($checkAPIToken === true):?>
        <div class="vc-tabpane-container-header">
            <?php echo esc_html(__("Create New Team", 'victorious'));?>
        </div>
        <div class="vc-tabpane-container">
            <?php echo settings_errors();?>

            <form method="post" action="" enctype="multipart/form-data">

                <input type="hidden" name="val[teamID]" value="<?php echo esc_attr($aForms['teamID']);?>" />

                    <div class="vc-dashboard-item border-white pb-0">

                        <h3 class="vc-tabpane-title"><?php echo esc_html(__("Image", 'victorious'));?></h3>

                        <?php if(!empty($aForms) && $aForms['siteID'] == 0):?>
                            <div class="p_4" id="js_slide_current_image">
                                <img src="<?php echo esc_url($aForms['full_image_path']);?>" width="80px" height="80px" alt="<?php echo esc_html($aForms['name']);?>" />
                            </div>
                        <?php else:?>
                            <?php if(!empty($aForms['image'])):?>

                                <div class="p_4" id="js_slide_current_image">

                                    <img src="<?php echo esc_url($aForms['full_image_path']);?>" width="80px" height="80px" alt="<?php echo esc_html($aForms['name']);?>" />

                                    <br />

                                    <a href="#" onclick="jQuery.admin.newImage(); return false;"><?php echo esc_html(__("Click here to upload new image", 'victorious'));?></a>

                                </div>

                            <?php endif;?>

                            <div id="js_submit_upload_image" <?php if(isset($aForms) && isset($aForms['image']) && $aForms['image'] != null):?>style="display:none"<?php endif;?>>

                                <div class="vc-custom-file">
                                    <input type="file" id='image' name="image" />
                                </div>
                                <p><?php echo esc_html(__("You can upload a jpg, gif or png file", 'victorious'));?></p>

                            </div>
                        <?php endif;?>

                    </div>

                    <div class="vc-dashboard-item border-white pb-0">

                        <h3 class="vc-tabpane-title"><?php echo esc_html(__("Sport"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>

                        <?php if($aSports != null):?>
                            <?php if(!empty($aForms) && $aForms['siteID'] == 0):?>
                                <?php foreach($aSports as $aSport):?>
                                    <?php foreach($aSport['child'] as $aOrg):?>
                                        <?php if($aForms['organization_id'] == $aOrg['id']):?>
                                            <?php echo esc_html($aOrg['name']);?>
                                        <?php endif;?>
                                    <?php endforeach;?>
                                <?php endforeach;?>
                            <?php else:?>
                                
                                <select name="val[organization]" class="form-control">

                                <?php foreach($aSports as $aSport):?>

                                    <?php if(is_array($aSport['child']) && $aSport['child'] != null):?>

                                    <option disabled="true"><?php echo esc_html($aSport['name']);?></option>

                                    <?php foreach($aSport['child'] as $aOrg):?>

                                        <?php if($aOrg['is_active'] == 1):?>

                                        <option value="<?php echo esc_attr($aOrg['id']);?>" style="padding-left: 20px" <?php if($aForms['organization_id'] == $aOrg['id']):?>selected="true"<?php endif;?>>

                                            <?php echo esc_html($aOrg['name']);?>

                                        </option>

                                        <?php endif;?>

                                    <?php endforeach;?>

                                    <?php endif;?>

                                <?php endforeach;?>

                                </select>
                            <?php endif;?>
                        <?php endif;?>

                    </div>

                    <div class="vc-dashboard-item border-white pb-0">

                        <h3 class="vc-tabpane-title"><?php echo esc_html(__("Name"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>

                        <td>
                            <?php if(!empty($aForms) && $aForms['siteID'] == 0):?>
                                <?php echo esc_html($aForms['name']);?>
                                <input type="hidden" name="val[name]" value="<?php echo esc_html($aForms['name']);?>" />
                            <?php else:?>
                                <input type="text" name="val[name]" class="regular-text ltr form-control" value="<?php echo esc_html($aForms['name']);?>" />
                            <?php endif;?>
                        </td>

                    </div>
                    <?php if(empty($aForms) || (!empty($aForms) && $aForms['siteID'] > 0)):?>
                    <div class="vc-dashboard-item border-white pb-0">

                        <h3 class="vc-tabpane-title"><?php echo esc_html(__("Nick name", 'victorious'));?></h3>

                        <td>

                            <input type="text" name="val[nickName]" class="regular-text ltr form-control" value="<?php echo esc_html($aForms['nickName']);?>" />

                        </td>

                    </div>

                    <div class="vc-dashboard-item border-white pb-0">

                        <h3 class="vc-tabpane-title"><?php echo esc_html(__("Home page link", 'victorious'));?></h3>

                        <td>

                            <input type="text" name="val[homepageLink]" class="regular-text ltr form-control" value="<?php echo esc_html($aForms['homepageLink']);?>" />

                        </td>

                    </div>

                    <div class="vc-dashboard-item border-white pb-0">

                        <h3 class="vc-tabpane-title"><?php echo esc_html(__("City name", 'victorious'));?></h3>

                        <td>

                            <input type="text" name="val[cityname]" class="regular-text ltr form-control" value="<?php echo esc_html($aForms['cityname']);?>" />

                        </td>

                    </div>

                    <div class="vc-dashboard-item border-white pb-0">

                        <h3 class="vc-tabpane-title"><?php echo esc_html(__("Team name", 'victorious'));?></h3>

                        <td>

                            <input type="text" name="val[teamname]" class="regular-text ltr form-control" value="<?php echo esc_html($aForms['teamname']);?>" />

                        </td>

                    </div>

                    <div class="vc-dashboard-item border-white pb-0">

                        <h3 class="vc-tabpane-title"><?php echo esc_html(__("Record", 'victorious'));?></h3>

                        <td>

                            <input type="text" name="val[record]" class="regular-text ltr form-control" value="<?php echo esc_html($aForms['record']);?>" />

                        </td>

                    </div>
                    <?php endif;?>
                    <div class="vc-dashboard-item border-white pb-0">

                        <h3 class="vc-tabpane-title"><?php echo esc_html(__("Salary"));?></h3>

                        <td>

                            <input id="salary" type="text" name="val[salary]" class="regular-text ltr form-control" value="<?php echo   number_format($aForms['salary']);?>" onkeyup="this.value = accounting.formatNumber(this.value)" />

                        </td>

                    </div>

                <?php submit_button(); ?>

            </form>
        </div>
    <?php endif;?>
</div>

