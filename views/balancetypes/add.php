<div class="wrap vc-wrap">
    <h2>
        <?php echo !$bIsEdit ? esc_html(__("Add Currency", 'victorious')) : esc_html(__("Edit Balance Type", 'victorious'));?>
        <a class="add-new-h2" href="<?php echo esc_url(self::$url);?>"><?php echo esc_html(__("Manage Balance Types", 'victorious'));?></a>
        <?php if($bIsEdit):?>
        <a class="add-new-h2" href="<?php echo esc_url(self::$urladdnew);?>"><?php echo esc_html(__("Add New", 'victorious'));?></a>
        <?php endif;?>
    </h2>
    <?php include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";?>
    <?php if($checkAPIToken === true):?>
        <div class="vc-tabpane-container-header">
            <?php echo esc_html(__("Create New Balance Types", 'victorious'));?>
        </div>
        <div class="vc-tabpane-container">
            <?php echo settings_errors();?>
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="val[id]" value="<?php echo !empty($data['id']) ? esc_attr($data['id']) : '';?>" />
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title d-inline-block mt-0"><?php echo esc_html(__("Enable"));?></h3>
                    <label class="checkbox-control d-inline-block mt-0 ml-4">
                        <input type="checkbox" class="form-control" name="val[enabled]" value="1" <?php echo !isset($data['enabled']) || $data['enabled'] == 1 ? 'checked="checked"' : '';?> />
                        <span class="checkmark"></span>
                    </label>
                </div>
                <?php if(empty($data['is_core'])):?>
                    <div class="vc-dashboard-item border-white pb-0">
                        <h3 class="vc-tabpane-title"><?php echo esc_html(__("Image", 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                        <?php if(isset($data) && isset($data['image']) && $data['image'] != null):?>
                            <div class="p_4" id="js_slide_current_image">
                                <img src="<?php echo esc_url($data['image_url']);?>" width="80px" height="80px" alt="<?php echo esc_html($data['name']);?>" />
                                <br />
                                <a href="#" onclick="jQuery.admin.newImage(); return false;"><?php echo esc_html(__("Click here to upload new image", 'victorious'));?></a>
                            </div>
                        <?php endif;?>
                        <div id="js_submit_upload_image" <?php if(isset($data) && isset($data['image']) && $data['image'] != null):?>style="display:none"<?php endif;?>>
                            <div class="vc-custom-file">
                                <input type="file" id='image' name="image" />
                            </div>
                            <p><?php echo esc_html(__("You can upload a jpg, gif or png file", 'victorious'));?></p>
                        </div>
                    </div>
                <?php endif;?>
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Name"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                    <td>
                        <input type="text" name="val[name]" class="regular-text ltr form-control" value="<?php echo !empty($data['name']) ? esc_attr($data['name']) : '';?>" />
                    </td>
                </div>
                <?php if(empty($data['is_core'])):?>
                    <div class="vc-dashboard-item border-white pb-0">
                        <h3 class="vc-tabpane-title"><?php echo esc_html(__("Currency Code"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                        <input type="text" name="val[currency_code]" class="regular-text ltr form-control" value="<?php echo !empty($data['currency_code']) ? esc_attr($data['currency_code']) : '';?>" />
                    </div>
                    <div class="vc-dashboard-item border-white pb-0">
                        <h3 class="vc-tabpane-title"><?php echo esc_html(__("Currency position"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                        <select name="val[currency_position]" class="regular-text ltr form-control">
                            <option value="<?php echo VICTORIOUS_CURRENCY_POS_BEFORE;?>"><?php echo VIC_CurrencyPositionList(VICTORIOUS_CURRENCY_POS_BEFORE);?></option>
                            <option <?php echo !empty($data['currency_position']) && $data['currency_position'] == VICTORIOUS_CURRENCY_POS_AFTER ? 'selected="true"' : '';?> value="<?php echo VIC_CurrencyPositionList(VICTORIOUS_CURRENCY_POS_AFTER);?>"><?php echo esc_html(__('After value', 'victorious'));?></option>
                        </select>
                    </div>
                    <div class="vc-dashboard-item border-white pb-0">
                        <h3 class="vc-tabpane-title"><?php echo esc_html(__("Currency Symbol", 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                        <input type="text" name="val[symbol]" class="regular-text ltr form-control" value="<?php echo !empty($data['symbol']) ? esc_attr($data['symbol']) : '';?>" />
                    </div>
                    <div class="vc-dashboard-item border-white pb-0">
                        <h3 class="vc-tabpane-title"><?php echo esc_html(__("Info", 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                        <textarea type="text" name="val[info]" class="regular-text ltr form-control"><?php echo !empty($data['info']) ? esc_html($data['info']) : '';?></textarea>
                    </div>
                <?php endif;?>
                <?php submit_button(); ?>
            </form>
        </div>
    <?php endif;?>
</div>