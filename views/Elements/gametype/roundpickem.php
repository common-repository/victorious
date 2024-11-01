<div class="contentPlugin">
    <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'pick_header.php');?>
    <form id="formData">
        <input type="hidden" value="<?php echo esc_attr($league['poolID']); ?>" name="poolID">
        <input type="hidden" value="<?php echo esc_attr($league['leagueID']); ?>" name="leagueID">
        <input type="hidden" value="<?php echo esc_attr($entry_number); ?>" name="entry_number">
        
        <?php if($current_week < $league['start_week']):?>
            <?php echo sprintf(esc_html(__("Current week is %s, but this contest is created to pick from week %s, you can only start picking when week %s is open.", 'victorious')), $current_week, $league['start_week'], $league['start_week']); ?>
        <?php elseif($fights != null):?>
            <?php foreach($fights as $week => $fight_list):
                $predict_match_fight = isset($predict_matches[$week]) ? $predict_matches[$week]['fight'] : array();
                $predict_match_point = isset($predict_matches[$week]) ? $predict_matches[$week]['predict_point'] : 0;
            ?>
                <div class="fight_list" id="fights_<?php echo esc_attr($week);?>" style="display:none">
                    <table border="0" class="table table-striped table-bordered table-responsive table-condensed">
                        <tbody>
                            <?php foreach ($fight_list as $fight): 
                                $home_team = $fight['home_team'];
                                $away_team = $fight['away_team'];
                                $pick = $fight['pick'];
                            ?>
                                <tr>
                                    <td style="text-align:center;width:30%">
                                        <label>
                                            <?php if (!empty($home_team['full_image_path'])): ?>
                                                <img src="<?php echo esc_url($home_team['full_image_path']); ?>" style="height:80px" />
                                                <br/>
                                            <?php endif; ?>
                                            <?php echo esc_html($home_team['name']); ?>
                                            <br/>
                                            <input type="radio" class="fightID" value="<?php echo esc_attr($home_team['teamID']); ?>" name="winners[<?php echo esc_attr($fight['fightID']); ?>]" data-fightid="<?php echo esc_attr($fight['fightID']); ?>" <?php if (isset($pick['select_id']) && $pick['select_id'] == $home_team['teamID']): ?>checked="checked"<?php endif; ?> <?php if ($fight['started'] == 1): ?>disabled="true"<?php endif; ?>>
                                        </label>
                                    </td>
                                    <td style="text-align:center;vertical-align: middle">
                                        <br> VS
                                        <br><?php echo VIC_DateTranslate($fight['startDate']); ?>
                                    </td>
                                    <td style="text-align:center;width:30%">
                                        <label>
                                            <?php if (!empty($away_team['full_image_path'])): ?>
                                                <img src="<?php echo esc_url($away_team['full_image_path']); ?>" style="height:80px"  />
                                                <br>
                                            <?php endif; ?>
                                            <?php echo esc_html($away_team['name']); ?>
                                            <br>
                                            <input type="radio" class="fightID" value="<?php echo esc_attr($away_team['teamID']); ?>" name="winners[<?php echo esc_attr($fight['fightID']); ?>]" data-fightid="<?php echo esc_attr($fight['fightID']); ?>" <?php if (isset($pick['select_id']) && $pick['select_id'] == $away_team['teamID']): ?>checked="checked"<?php endif; ?> <?php if ($fight['started'] == 1): ?>disabled="true"<?php endif; ?>>
                                        </label>   
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <br/>
                    <?php echo sprintf(esc_html(__("Predict point total for match %s", 'victorious'), '<b>'.$predict_match_fight['name'].'</b>'));?>
                    <input type="text" name="predict_points[<?php echo esc_attr($week);?>]" value="<?php echo esc_attr($predict_match_point); ?>"/>
                    <br/>
                    <?php echo sprintf(esc_html(__("Tiebreaker: predict the points differential between %s", 'victorious')), str_replace('vs', '-', $predict_match_fight['name'])); ?>
                </div>
            <?php endforeach;?>
        <?php endif;?>
    </form>
    <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'pick_footer.php');?>
</div>

<script>
    jQuery(document).ready(function(){
        jQuery.roundpickem.initRoundpickem();
    })
</script>