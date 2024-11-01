<div class="wrap vc-wrap">

    <h2>

        <?php echo !$bIsEdit ? esc_html(__("Add Fighters", 'victorious')) : esc_html(__("Edit Fighters", 'victorious'));?>
        <a class="add-new-h2" href="<?php echo esc_url(self::$url);?>"><?php echo esc_html(__("Manage Fighters", 'victorious'));?></a>
        <?php if($bIsEdit):?>

        <a class="add-new-h2" href="<?php echo esc_url(self::$urladdnew);?>"><?php echo esc_html(__("Add New", 'victorious'));?></a>

        <?php endif;?>

    </h2>
    <?php include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";?>
    <?php if($checkAPIToken === true):?>
        <div class="vc-tabpane-container-header">
            <?php echo esc_html(__("Create New Fighter", 'victorious'));?>
        </div>
        <div class="vc-tabpane-container">
            <?php echo settings_errors();?>
            <form method="post" action="" enctype="multipart/form-data">

                <input type="hidden" name="val[fighterID]" value="<?php echo esc_attr($aForms['fighterID']);?>" />


                <div class="vc-dashboard-item border-white pb-0">

                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Image", 'victorious'));?></h3>

                    <?php if(isset($aForms) && isset($aForms['image']) && $aForms['image'] != null):?>

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

                </div>

                <div class="vc-dashboard-item border-white pb-0">

                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Name"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>


                    <input type="text" name="val[name]" class="regular-text ltr form-control" value="<?php echo esc_attr($aForms['name']);?>" />


                </div>
                <div class="vc-dashboard-item border-white pb-0">

                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Organization"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>


                    <?php if($aSports != null):?>

                        <select id="org" name="val[org_id]" class="form-control">

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
                <div class="row">
                    <div class="col-md-6">
                        <div class="vc-dashboard-item border-white pb-0">

                            <h3 class="vc-tabpane-title"><?php echo esc_html(__("Nick name", 'victorious'));?></h3>

                            <input type="text" name="val[nickName]" class="regular-text ltr form-control" value="<?php echo esc_attr($aForms['nickName']);?>" />

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="vc-dashboard-item border-white pb-0">

                            <h3 class="vc-tabpane-title"><?php echo esc_html(__("Age", 'victorious'));?></h3>

                            <input type="text" name="val[age]" class="regular-text ltr form-control" value="<?php echo esc_attr($aForms['age']);?>" />

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="vc-dashboard-item border-white pb-0">

                            <h3 class="vc-tabpane-title"><?php echo esc_html(__("Fight camp", 'victorious'));?></h3>

                            <input type="text" name="val[fightCamp]" class="regular-text ltr form-control" value="<?php echo esc_attr($aForms['fightCamp']);?>" />

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="vc-dashboard-item border-white pb-0">

                            <h3 class="vc-tabpane-title"><?php echo esc_html(__("Strengths", 'victorious'));?></h3>

                            <input type="text" name="val[strengths]" class="regular-text ltr form-control" value="<?php echo esc_attr($aForms['strengths']);?>" />

                        </div>
                    </div>
                </div>
                
                <div class="vc-dashboard-item border-white pb-0">

                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Home page link", 'victorious'));?></h3>

                    <input type="text" name="va l[homepageLink]" class="regular-text ltr form-control" value="<?php echo esc_attr($aForms['homepageLink']);?>" />

                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="vc-dashboard-item border-white pb-0">

                            <h3 class="vc-tabpane-title"><?php echo esc_html(__("Height", 'victorious'));?></h3>

                            <input type="text" name="val[height]" class="regular-text ltr form-control" value="<?php echo esc_attr($aForms['height']);?>" />

                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="vc-dashboard-item border-white pb-0">

                            <h3 class="vc-tabpane-title"><?php echo esc_html(__("Weight", 'victorious'));?></h3>

                            <input type="text" name="val[weight]" class="regular-text ltr form-control" value="<?php echo esc_attr($aForms['weight']);?>" />

                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="vc-dashboard-item border-white pb-0">

                            <h3 class="vc-tabpane-title"><?php echo esc_html(__("Record", 'victorious'));?></h3>

                            <input type="text" name="val[record]" class="regular-text ltr form-control" value="<?php echo esc_attr($aForms['record']);?>" />

                        </div>
                    </div>
                </div>                   
                

                

              

                <?php submit_button(); ?>

            </form>
        </div>
    <?php endif;?>
</div>

