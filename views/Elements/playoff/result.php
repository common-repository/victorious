<input type="hidden" id="league_id" value="<?php echo esc_attr($league['leagueID']);?>" />
<input type="hidden" id="user_id" value="<?php echo esc_attr($user_id);?>" />
<input type="hidden" id="entry_number" value="<?php echo esc_attr($entry_number);?>" />
<input type="hidden" id="is_live" value="<?php echo esc_attr($league['is_live']);?>" />

<article class="hentry">
    <div class="vc-section">
        <div class="p-4">
            <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."result_header.php");?>
            <div class="vc-table-small" id="vc-leaderboard">
                <?php $active_row = 0; require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."playoff/leaderboard.php");?>
            </div>
        </div>
        <?php if(!empty($weeks)):?>
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <select id="playoff_week" class="form-control">
                        <?php foreach($weeks as $week):?>
                            <option value="<?php echo $week['week'];?>"><?php echo $week['name'];?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
        <?php endif;?>
        <div id="vc-leaderboard-detail">
            <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."playoff/leaderboard_detail.php");?>
        </div>
    </div>
</article>

<?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW . 'dlg_info.php');?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery.playoff.initPlayoffResult();
    })
</script>