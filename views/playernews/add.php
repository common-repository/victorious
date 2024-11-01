<div class="wrap vc-wrap">
    <h2>
        <?php echo !$bIsEdit ? esc_html(__("Add Player News", 'victorious')) : esc_html(__("Edit Player News", 'victorious'));?>
        <a class="add-new-h2" href="<?php echo esc_url(self::$url);?>"><?php echo esc_html(__("Manage Player News", 'victorious'));?></a>
        <?php if($bIsEdit):?>
        <a class="add-new-h2" href="<?php echo esc_url(self::$url);?>"><?php echo esc_html(__("Add New", 'victorious'));?></a>
        <?php endif;?>
    </h2>
    <?php include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";?>
    <?php if($checkAPIToken === true):?>
        <div class="vc-tabpane-container-header">
            <?php echo esc_html(__("Create New Player News", 'victorious'));?>
        </div>
        <div class="vc-tabpane-container">
            <?php echo settings_errors();?>
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="val[id]" value="<?php echo esc_attr($aForms['id']);?>" />
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Player"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                    <?php if(!empty($aPlayers)):?>
                        <select name="val[playerID]" class="form-control">
                            <option value="0"><?php echo esc_html(__("Select a player", 'victorious'));?></option>
                        <?php foreach($aPlayers as $aPlayer):?>
                            <option value="<?php echo esc_attr($aPlayer['id']);?>" <?php if($aForms['playerID'] == $aPlayer['id']):?>selected="true"<?php endif;?>>
                                <?php echo esc_html($aPlayer['name']);?>
                            </option>
                        <?php endforeach;?>
                        </select>
                    <?php endif;?>
                </div>
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Date"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                    <input type="text" name="val[updated]" id="date" class="regular-text ltr form-control" value="<?php echo esc_attr($aForms['updated']);?>" />
                    <?php echo esc_html(__("example", 'victorious'));?>: 2015-05-08 20:10:00
                </div>
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Title"));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                    <input type="text" name="val[title]" class="regular-text ltr form-control" value="<?php echo esc_attr($aForms['title']);?>" />
                </div>
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Content", 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                    <textarea rows="5" class="large-text code form-control" name="val[content]"><?php echo esc_html($aForms['content']);?></textarea>
                </div>
                <?php submit_button(); ?>
            </form>
        </div>
    <?php endif;?>
</div>