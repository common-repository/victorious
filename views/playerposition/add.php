<div class="wrap vc-wrap">
    <h2>
        <?php echo !$bIsEdit ? esc_html(__("Add Player Position", 'victorious')) : esc_html(__("Edit Player Position", 'victorious'));?>
        <a class="add-new-h2" href="<?php echo esc_url(self::$url);?>"><?php echo esc_html(__("Manage Player Positions", 'victorious'));?></a>
        <?php if($bIsEdit):?>
        <a class="add-new-h2" href="<?php echo esc_url(self::$url);?>"><?php echo esc_html(__("Add New", 'victorious'));?></a>
        <?php endif;?>
    </h2>
    <?php include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";?>
    <?php if($checkAPIToken === true):?>
        <div class="vc-tabpane-container-header">
            <?php echo esc_html(__("Create New Player Position", 'victorious'));?>
        </div>
        <div class="vc-tabpane-container">
            <?php echo settings_errors();?>
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="val[id]" value="<?php echo esc_attr($aForms['id']);?>" />
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Sport"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                    <td>
                        <?php if($aSports != null):?>

                            <select name="val[org_id]" class="form-control">

                            <?php foreach($aSports as $aSport):?>

                                <?php if(is_array($aSport['child']) && $aSport['child'] != null):?>

                                <option disabled="true"><?php echo esc_html($aSport['name']);?></option>

                                <?php foreach($aSport['child'] as $aOrg):?>

                                    <?php if($aOrg['is_active'] == 1):?>

                                    <option value="<?php echo esc_html($aOrg['id']);?>" style="padding-left: 20px" <?php if($aForms['org_id'] == $aOrg['id']):?>selected="true"<?php endif;?>>

                                        <?php echo esc_html($aOrg['name']);?>

                                    </option>

                                    <?php endif;?>

                                <?php endforeach;?>

                                <?php endif;?>

                            <?php endforeach;?>

                            </select>

                        <?php endif;?>
                    </td>
                </div>
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Name"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                    <td>
                        <input type="text" name="val[name]" class="regular-text ltr form-control" value="<?php echo esc_html($aForms['name']);?>" />
                    </td>
                </div>
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Player Quantity"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                    <td>
                        <input type="text" name="val[default_quantity]" class="regular-text ltr form-control" value="<?php echo empty($aForms['default_quantity']) ? 1 : esc_html($aForms['default_quantity']);?>" />
                    </td>
                </div>
                <?php submit_button(); ?>
            </form>
        </div>
    <?php endif;?>
</div>
