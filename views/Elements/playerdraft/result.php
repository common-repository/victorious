<input type="hidden" id="scoringCats" value='<?php echo json_encode($scoringCats); ?>' />
<input type="hidden" id="multiEntry" value='<?php echo esc_attr($league['multi_entry']); ?>' />
<input type="hidden" id="leagueOptionType" value='<?php echo esc_attr($league['option_type']); ?>' />
<input type="hidden" id="gameType" value='<?php echo esc_attr($league['gameType']); ?>' />
<input type="hidden" id="is_motocross" value='<?php echo esc_attr($league['is_motocross']); ?>' />
<input type="hidden" id="leagueID" value='<?php echo esc_attr($league['leagueID']); ?>' />
<input type="hidden" id="poolID" value='<?php echo esc_attr($league['poolID']); ?>' />
<input type="hidden" id="userID" value='<?php echo esc_attr($user_id); ?>' />
<input type="hidden" id="entry_number" value='<?php echo esc_attr($entry_number); ?>' />
<input type="hidden" id="is_live" value="<?php echo esc_attr($league['is_live']);?>" />

<article class="hentry">
    <div class="vc-section">
        <div class="p-4">
            <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."result_header.php");?>
            <div class="vc-table-small" id="vc-leaderboard">
                <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."playerdraft/leaderboard.php");?>
            </div>
        </div>
        <div id="vc-leaderboard-detail">
            <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."playerdraft/leaderboard_detail.php");?>
        </div>
    </div>
</article>

<?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW . 'dlg_info.php');?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery.playerdraft.initPlayerdraftResult();
    })
</script>