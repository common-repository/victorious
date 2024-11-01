<input type="hidden" id="league_id" value="<?php echo esc_attr($league['leagueID']);?>" />
<input type="hidden" id="user_id" value="<?php echo VIC_GetUserId();?>" />
<input type="hidden" id="entry_number" value="<?php echo esc_attr($entry_number);?>" />
<input type="hidden" id="is_live" value="<?php echo esc_attr($league['is_live']);?>" />

<?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'pick_header.php');?>

<div id="standing"></div>
<div class="clear"></div>
<div id="result"></div>
<div class="clear"></div>
<div class="f-lightbox f-legacy-lightbox" id="dlgFriends" style="display: none;">

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery.bothteamstoscore.initBothTeamsToScoreResult();
    })
</script>