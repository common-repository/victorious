<?php if($league == null):?>
    <div class="public_message">
        <?php echo esc_html(__("Contest not found", 'victorious'));?>
    </div>
<?php else:?>
    <div id="msgFeatureContest" class="public_message"></div>
    <?php if($is_feature == 1):?>
        <?php echo esc_html(__("Are you sure you want to set feature for this contest?", "victorious"));?><br/>
        <?php echo esc_html(__("The best image dimension should be 210px x 158px", "victorious"));?>
        <br/><br/>
        <div id="fine-uploader-validation"></div>
    <?php else:?>
        <?php echo esc_html(__("Are you sure you want to set unfeature for this contest?", "victorious"));?>
    <?php endif;?>
    <form id="formFeatureContest" style="width: 100%;">
        <input type="hidden" name="id" value="<?php echo esc_html($league_id);?>" />
        <input type="hidden" name="is_feature" value="<?php echo esc_html($is_feature);?>" />
        <input type="hidden" name="feature_image" id="feature_image" value="<?php echo esc_html($league['feature_image']);?>" />
    </form>
<?php endif;?>