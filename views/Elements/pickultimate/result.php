<input type="hidden" id="league_id" value="<?php echo esc_attr($league['leagueID']);?>" />
<input type="hidden" id="user_id" value="<?php echo VIC_GetUserId();?>" />
<input type="hidden" id="entry_number" value="<?php echo esc_attr($entry_number);?>" />
<input type="hidden" id="is_live" value="<?php echo esc_attr($league['is_live']);?>" />

<div id="main" class="site-main site-info">
    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">
            <article class="hentry">
                <div class="vc-section">
                    <div class="p-4">
                        <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."result_header.php");?>
                        <div class="vc-table-small" id="vc-leaderboard">
                            <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."pickultimate/leaderboard.php");?>
                        </div>
                    </div>
                    <div id="vc-leaderboard-detail"></div>
                </div>
            </article>
        </div>
    </div>
</div>

<?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW . 'dlg_info.php');?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery.pickultimate.initPickUltimateResult();
    })
</script>