<script>
    jQuery(document).ready(function(){
        jQuery.admin.initAddSport();

        <?php if (!$bIsEdit): ?>
        jQuery('#select-sport').on('change', function (){
            if (jQuery(this).val() != 0) {
                window.location.href = "<?php echo self::$urladdnew . "&parent_id=";?>" + jQuery(this).val();
            }
        });
        <?php endif; ?>

    })
</script>

<style>
    .step-create {
        margin: 0 auto;
        width: 50%;
    }

    .step-order {
        color: #a5a5a5;
        font-size: 12px;
    }

    .step-type {
        font-weight: bold;
        font-size: 14px;
    }

    .step-status {
        font-size: 12px;
        color: #007bff;
    }

    .sport-action {
        padding: 20px 0;
        display: flex;
        justify-content: space-between;
    }

    .container-title {
        font-size: 22px;
        font-weight: bold;
    }

    .in-progress::before {
        content: '';
        width: 10px;
        height: 10px;
        background: #1B719E;
        border-radius: 50%;
        display: block;
        top: -12px;
        position: absolute;
    }
    .in-progress::after {
        content: '';
        width: 50%;
        height: 3px;
        display: block;
        background: #1B719E;
        position: absolute;
        top: -8px;
        left: 17px;
    }
    .pending::before {
        content: '';
        width: 10px;
        height: 10px;
        background: #a5a5a5;
        border-radius: 50%;
        display: block;
        top: -12px;
        position: absolute;
    }
    .complete::before {
        content: '';
        width: 10px;
        height: 10px;
        background: #1B719E;
        border-radius: 50%;
        display: block;
        top: -12px;
        position: absolute;
    }
    .complete::after {
        content: '';
        width: 100%;
        height: 3px;
        display: block;
        background: #1B719E;
        position: absolute;
        top: -8px;
        left: 17px;
    }
</style>

<div class="wrap vc-wrap">

    <h2>

        <?php //echo !$bIsEdit ? esc_html(__("Add Sport", 'victorious')) : esc_html(__("Edit Sport", 'victorious'));?>
        <a class="add-new-h2" href="<?php echo esc_url(self::$url);?>"><?php echo esc_html(__("Manage Sports", 'victorious'));?></a>
        <?php if($bIsEdit):?>

        <a class="add-new-h2" href="<?php echo esc_url(self::$urladdnew);?>"><?php echo esc_html(__("Add New", 'victorious'));?></a>

        <?php endif;?>

    </h2>
    <?php include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";?>
    <?php if($checkAPIToken === true):?>

        <?php if ($bIsEdit): ?>
        <div class="vc-tabpane-container-header" style="text-align: left">
            <?php
            $title = !$bIsEdit ? esc_html(__("Create Sport", 'victorious')) : esc_html(__("Edit Sport", 'victorious'));
            if (!empty($aForms['organisation'])) {
                $title = !$bIsEdit ? esc_html(__("Create Organisation", 'victorious')) : esc_html(__("Edit Organisation", 'victorious'));
            }

            echo $title;?>
        </div>
        <?php endif; ?>

        <div class="vc-tabpane-container">
            <?php echo settings_errors();?>
            <form method="post" action="" enctype="multipart/form-data" autocomplete="off">

                <input type="hidden" name="val[id]" value="<?php echo esc_attr($aForms['id']);?>" />
                <input type="hidden" name="val[organisation]" value="<?php echo !empty($aForms['organisation']) ? 1 : 0;?>" />

                <?php if (!$bIsEdit): ?>
                <div class="step-create">
                    <div class="row">
                        <div class="col-md-6 <?php echo !empty($aForms['organisation']) ? 'complete' : 'in-progress'?>">
                            <div class="step-order"><?php echo esc_html(__('STEP 1', 'victorious'))?></div>
                            <div class="step-type"><?php echo esc_html(__('Create Sport', 'victorious'))?></div>
                            <div class="step-status"><?php echo !empty($aForms['organisation']) ? esc_html(__('Complete', 'victorious')) : esc_html(__('In Progress', 'victorious'))?></div>
                        </div>
                        <div class="col-md-6 <?php echo !empty($aForms['organisation']) ? 'in-progress' : 'pending'?>">
                            <div class="step-order"><?php echo esc_html(__('STEP 2', 'victorious'))?></div>
                            <div class="step-type"><?php echo esc_html(__('Create Organisation', 'victorious'))?></div>
                            <div class="step-status"><?php echo !empty($aForms['organisation']) ? esc_html(__('In Progress', 'victorious')) : esc_html(__('Pending', 'victorious'))?></div>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="sport-action">
                    <div class="container-title">
                        <?php echo !empty($aForms['organisation']) ? esc_html(__("Create Organisation", 'victorious')) : esc_html(__("Create Sport", 'victorious')); ?>
                    </div>
                    <div class="container-button">
                        <?php if (!empty($aForms['organisation']) && empty($aForms['id']) && !empty($aForms['parent_id'])): ?>
                            <a href="javascript:history.back(1);" class="button button-cancel"><?php echo esc_html(__('Back', 'victorious'))?></a>
                        <?php else: ?>
                            <a href="<?php echo esc_url(self::$url);?>" class="button button-cancel"><?php echo esc_html(__('Cancel', 'victorious'))?></a>
                        <?php endif; ?>

                        <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_html(__('Next', 'victorious'))?>">
                    </div>
                </div>
                <div class="clear"></div>
                <?php endif; ?>

                <div class="mb-4"><?php echo esc_html(__("A sports league is a group of sports teams or individual athletes that compete against each other and gain points in a specific sport.", 'victorious'))?></div>
                <div class="mb-4"><?php echo esc_html(__('On this page, you will create a sport, in the next page you will assign an organization to the sport.', 'victorious'))?></div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="vc-dashboard-item border-white pb-0">
                            <div class="row">
                                <?php if ((!$bIsEdit || !empty($aForms['organisation'])) && !empty($aSports)): ?>
                                <div class="col-md-4">
                                    <label>
                                        <span class="date-time-text format-i18n"><?php echo !empty($aForms['organisation']) ? esc_html(__('Select Sport', 'victorious')) . '<span style="color: red">*</span>' : esc_html(__('Select from list', 'victorious'))?></span>
                                    </label>
                                    <select name="val[parent_id]" class="form-control" id="select-sport">
                                        <option value="0"></option>

                                        <?php foreach($aSports as $aSport):?>

                                            <option <?php echo esc_attr($aForms['parent_id'] == $aSport['id'] ? 'selected="true"' : '');?> value="<?php echo esc_html($aSport['id']);?>"><?php echo esc_html($aSport['name']);?></option>

                                        <?php endforeach;?>
                                    </select>
                                </div>
                                <?php endif; ?>
                                <div class="col-md-4">
                                    <label>
                                        <span class="date-time-text format-i18n">
                                            <?php echo !empty($aForms['organisation']) ? esc_html(__('Organisation name', 'victorious')) . '<span style="color: red">*</span>' : ($bIsEdit ? esc_html(__('Name Sport', 'victorious')) : (!empty($aSports) ? esc_html(__('Or Enter New Sport', 'victorious')) : esc_html(__('Enter New Sport', 'victorious'))))?></span>
                                    </label>

                                    <input type="text" name="val[name]" class="regular-text ltr form-control" value="<?php echo esc_html($aForms['name']);?>" />
                                </div>

                                <?php if (empty($aForms['organisation'])): ?>
                                <div class="col-md-4">
                                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Sport Icon", 'victorious'));?></h3>
                                    <?php if(isset($aForms) && isset($aForms['image']) && $aForms['image'] != null):?>

                                        <div class="p_4" id="js_slide_current_image">

                                            <img src="<?php echo esc_html($aForms['full_image_path']);?>" height="38px" alt="<?php echo esc_html($aForms['name']);?>" />

                                            <?php if(isset($aForms) && $aForms['siteID'] > 0):?>

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
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                        
                <h3 class="vc-tabpane-title"><?php echo esc_html(__("Sport Settings", 'victorious'));?></h3>
                <div class="vc-dashboard-item border-white pb-0">
                    <label class="checkbox-control">
                        Upload photo 
                        <br>
                        Select this checkbox, if you want end users to be able to upload photos along with each contest.
                        <br>
                        This usually is used when the contest involves E-Sports where a photo of the end users screen on their play console as proof of victory
                        <input type="checkbox" id="upload_photo" name="val[upload_photo]" <?php echo esc_attr($aForms['upload_photo'] == 1 ? 'checked="true"' : '');?> value="1" />
                        <span class="checkmark"></span>
                    </label>
                </div>
                <div class="vc-dashboard-item border-white pb-0">                    
                    <label class="checkbox-control">
                        Allow Playerdraft
                        <br>
                        Select this checkbox if you want to support the traditional fantasy draft game type. 
                        <br>
                        This settings involves settings up everything manually such as players, schedules, contests and updates. 
                        <input type="checkbox" id="is_playerdraft" name="val[is_playerdraft]" <?php echo esc_attr($aForms['is_playerdraft'] == 1 ? 'checked="true"' : '');?> value="1" />
                        <span class="checkmark"></span>
                    </label>
                </div>
                <div class="vc-dashboard-item border-white pb-0">
                    <label class="checkbox-control">
                        Team Sport
                        <br>
                        Select this checkbox if your sport is a team sport like soccer, basketball, hockey and baseball. 
                        <br>
                        This setting is needed for the player draft game type.
                        <input type="checkbox" id="is_team" name="val[is_team]" <?php echo esc_attr( $aForms['is_team'] == 1 ? 'checked="true"' : '');?> value="1" />
                        <span class="checkmark"></span>
                    </label>                      
                </div>

                <?php if ($bIsEdit) submit_button(); ?>

            </form>
        </div>
    <?php endif;?>
</div>

