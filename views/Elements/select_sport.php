<div class="wrap">
    <h2>
        <?php echo esc_html(__("Select sport", 'victorious'));?>
    </h2>
    <?php include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";?>
    <?php if($checkAPIToken === true):?>
        <div class="vc-tabpane-container-header">
            <?php echo esc_html(__("Select sport", 'victorious'));?>
        </div>
        <div class="vc-tabpane-container">
            <?php echo settings_errors();?>

            <form method="get" action="<?php echo admin_url()."admin.php";?>" enctype="multipart/form-data">
                <input type="hidden" name="page" value="<?php echo sanitize_text_field($_GET['page']);?>" />

                <div class="vc-dashboard-item border-white pb-0">

                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Sport", 'victorious'));?></h3>

                    <div id="sportResult">

                        <?php if($aSports != null):?>

                            <select id="poolOrgs" name="sport_id" class="form-control">
                                <?php if(!empty($all_sport)):?>
                                    <option value=""><?php echo esc_html(__("All sports", 'victorious'));?></option>
                                <?php endif;?>
                                <?php foreach($aSports as $aSport):?>

                                    <?php if(!empty($aSport['child']) && is_array($aSport['child']) && $aSport['child'] != null):?>

                                    <option disabled="true"><?php echo esc_html($aSport['name']);?></option>

                                    <?php foreach($aSport['child'] as $aOrg):?>

                                        <?php if($aOrg['is_active'] == 1):?>

                                        <option value="<?php echo esc_attr($aOrg['id']);?>" is_team="<?php echo esc_attr($aOrg['is_team']);?>" only_playerdraft="<?php echo esc_attr($aOrg['only_playerdraft']);?>" is_round="<?php echo esc_attr($aOrg['is_round']);?>" style="padding-left: 20px">

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

                <p class="submit">
                    <input id="submit" class="button button-primary" value="<?php echo esc_html(__("Select", 'victorious'));?>" type="submit">
                </p>

            </form>
        </div>
    <?php endif;?>
</div>