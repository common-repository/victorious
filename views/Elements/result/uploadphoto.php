<input type="hidden" id="league_id" value="<?php echo esc_attr($league['leagueID']);?>" />
<input type="hidden" id="user_id" value="<?php echo VIC_GetUserId();?>" />
<input type="hidden" id="entry_number" value="<?php echo esc_attr($entry_number);?>" />
<input type="hidden" id="is_live" value="<?php echo esc_attr($league['is_live']);?>" />

<?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'pick_header.php');?>
<div class="contestStructure">
    <div class="left">
        <div>
            <div class="label"><?php echo esc_html(__('Schedule', 'victorious'));?>:</div>
            <div class="content">
                <?php if($fights != null):?>
                    <?php foreach($fights as $fight):?>
                        <div><?php echo esc_html($fight['name']).': '.VIC_DateTranslate($fight['startDate']);?></div>
                    <?php endforeach;?>
                <?php endif;?>
            </div>
        </div>
        <?php if(!empty($league['note'])):?>
        <div>
            <div class="label"><?php echo esc_html(__('Note', 'victorious'));?>:</div>
            <div class="content">
                <?php echo esc_html($league['note']);?>
            </div>
        </div>
        <?php endif;?>
    </div>
</div>

<div id="standing"></div>
<div class="clear"></div>
<div id="result"></div>
<div class="clear"></div>
<?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'dlg_upload_photo.php');?>
<?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'qq_template.php');?>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery.uploadphoto.initResult();
    })
</script>