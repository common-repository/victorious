<div class="contentPlugin">
    <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'pick_header.php');?>
    <div class="contestStructure">
        <div class="left">
            <div>
                <div class="label"><?php echo esc_html(__('Available passes', 'victorious'));?>:</div>
                <span id="available_pass" data-value="<?php echo esc_attr($available_max_pass);?>"><?php echo esc_attr($available_pass);?></span>
            </div>
        </div>
    </div>
    <form id="formData">
        <input type="hidden" value="<?php echo esc_attr($league['leagueID']); ?>" name="league_id">
        <input type="hidden" value="<?php echo esc_attr($entry_number); ?>" name="entry_number">
        <input type="hidden" value="<?php echo !empty($_SESSION['fv_invitedby']) ? $_SESSION['fv_invitedby'] : ""; ?>" name="invitedby">
        <?php if($fights != null):?>
            <table border="0" class="table table-striped table-bordered table-responsive table-condensed">
                <tbody>
                    <?php foreach ($fights as $fight): 
                        $home_team = $fight['home_team'];
                        $away_team = $fight['away_team'];
                    ?>
                        <tr>
                            <td style="text-align:center;width:30%">
                                <label>
                                    <?php if (!empty($home_team['full_image_path'])): ?>
                                        <div class="team_image">
                                            <img src="<?php echo esc_attr($home_team['full_image_path']); ?>"/>
                                        </div>
                                        <br/>
                                    <?php endif; ?>
                                    <?php echo esc_html($home_team['name']); ?>
                                    <br/>
                                    <input type="radio" class="fightID" value="<?php echo esc_attr($home_team['teamID']); ?>" name="winners[<?php echo esc_attr($fight['fightID']); ?>]" data-fightid="<?php echo esc_attr($fight['fightID']); ?>" <?php if(!empty($home_team['is_pick'])): ?>checked="checked"<?php endif; ?>>
                                </label>
                            </td>
                            <td style="text-align:center;vertical-align: middle">
                                <br> <?php echo esc_html(__('VS', 'victorious'));?>
                                <br><?php echo VIC_DateTranslate($fight['startDate']); ?>
                            </td>
                            <td style="text-align:center;width:30%">
                                <label>
                                    <?php if (!empty($away_team['full_image_path'])): ?>
                                        <div class="team_image">
                                            <img src="<?php echo esc_attr($away_team['full_image_path']); ?>" />
                                        </div>
                                        <br>
                                    <?php endif; ?>
                                    <?php echo esc_html($away_team['name']); ?>
                                    <br>
                                    <input type="radio" class="fightID" value="<?php echo esc_attr($away_team['teamID']); ?>" name="winners[<?php echo esc_attr($fight['fightID']); ?>]" data-fightid="<?php echo esc_attr($fight['fightID']); ?>" <?php if(!empty($away_team['is_pick'])): ?>checked="checked"<?php endif; ?>>
                                </label>   
                            </td>
                            <td style="text-align:center;width:10%">
                                <label>
                                    <div class="team_image"></div>
                                    <br><br>
                                    <input type="radio" class="fightID is_pass" name="winners[<?php echo esc_attr($fight['fightID']); ?>]" value="0" <?php if(!empty($fight['survivor_is_pass'])): ?>checked="checked"<?php endif; ?>/>
                                    <?php echo esc_html(__('Pass', 'victorious'));?>
                                </label>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif;?>
    </form>
    <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'pick_footer.php');?>
</div>

<script>
    jQuery(document).ready(function(){
        jQuery.goliath.initGoliath();
    })
</script>