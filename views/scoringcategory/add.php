<div class="wrap vc-wrap">

    <h2>

        <?php echo !$bIsEdit ? esc_html(__("Add Scoring Category", 'victorious')) : esc_html(__("Edit Scoring Category", 'victorious'));?>
        <a class="add-new-h2" href="<?php echo esc_html(self::$url);?>"><?php echo esc_html(__("Manage Scoring Categories", 'victorious'));?></a>
        <?php if($bIsEdit):?>

        <a class="add-new-h2" href="<?php echo esc_html(self::$urladdnew);?>"><?php echo esc_html(__("Add New", 'victorious'));?></a>

        <?php endif;?>

    </h2>
    <?php include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";?>
    <?php if($checkAPIToken === true):?>
        <div class="vc-tabpane-container-header">
            <?php echo esc_html(__("Create New Scoring Category", 'victorious'));?>
        </div>
        <div class="vc-tabpane-container">
            <?php echo settings_errors();?>

            <form method="post" action="" enctype="multipart/form-data">

                <input type="hidden" name="val[id]" value="<?php echo esc_attr($aForms['id']);?>" />

                <input type="hidden" id="scoringTypes" value='<?php echo json_encode($aScoringTypes);?>' />

                <input type="hidden" id="selectType" value="<?php echo esc_attr($aForms['scoring_type']);?>" />

                <div class="vc-dashboard-item border-white pb-0">

                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Organization"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>

                    <?php if($aSports != null):?>

                        <select name="val[org_id]" id="org" class="form-control" <?php if(isset($aForms) && $aForms['siteID'] == 0):;?>disabled="true"<?php endif;?> onchange="jQuery.scoringcat.loadScoringType()">

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

                </div>

                <div class="vc-dashboard-item border-white pb-0">

                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Type", 'victorious'));?></h3>

                    <select name="val[scoring_type]" class="form-control" <?php if(isset($aForms) && $aForms['siteID'] == 0):;?>disabled="true"<?php endif;?> id="htmlScoringTypes">

                    </select>

                </div>

                <div class="vc-dashboard-item border-white pb-0">

                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Name"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>

                    <input type="text" name="val[name]"  <?php if(isset($aForms) && $aForms['siteID'] == 0):;?>disabled="true"<?php endif;?> class="regular-text ltr form-control" value="<?php echo esc_html($aForms['name']);?>" />

                </div>
                <div class="vc-dashboard-item border-white pb-0">

                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Alias"));?> <span class="description"></span></h3>

                    <input type="text" name="val[alias]"  <?php if(isset($aForms) && $aForms['siteID'] == 0):;?>disabled="true"<?php endif;?> class="regular-text ltr form-control" value="<?php echo esc_html($aForms['alias']);?>" />

                </div>

                <div class="vc-dashboard-item border-white pb-0">

                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Point"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>

                    <input type="text" name="val[points]" class="regular-text ltr form-control" value="<?php echo esc_html($aForms['points']);?>" />

                </div>

                <?php submit_button(); ?>

            </form>
        </div>
    <?php endif;?>
</div>

