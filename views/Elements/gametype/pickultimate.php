<div class="contentPlugin">
    <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'pick_header.php');?>
    <form id="formData">
        <input type="hidden" value="<?php echo esc_attr($league['poolID']);?>" name="poolID">
        <input type="hidden" value="<?php echo esc_attr($league['leagueID']);?>" name="leagueID">
        <input type="hidden" value="<?php echo esc_attr($entry_number);?>" name="entry_number">
        <table border="0" class="table table-striped table-bordered table-responsive table-condensed tb-submit">
            <thead> 
                <tr> 
                    <th><?php echo esc_html(__("Time & Date"));?></th>
                    <th><?php echo esc_html(__("Game Vs. Points"));?></th>
                    <th><?php echo esc_html(__("Winner"));?></th>
                    <th><?php echo esc_html(__("Total Score"));?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($fights as $fight):?>
                <tr>
                    <td style="width:20%">
                        <?php echo VIC_DateTranslate($fight['startDate']);?>
                    </td>
                    <td style="width:30%">
                        <span>
                            <?php echo esc_html($fight['name1']);?> (<?php echo esc_html($fight['team1_spread_points']);?>)
                        </span>
                        <input type="radio" value="<?php echo esc_attr($fight['fighterID1']);?>" name="spread<?php echo esc_attr($fight['fightID']);?>" data-fightid="<?php echo esc_attr($fight['fightID']);?>" <?php if($fight['winner_spreadID'] == $fight['fighterID1']):?>checked="checked"<?php endif;?> <?php if($fight['started'] == 1):?>disabled="true"<?php endif;?>>
                        <span>vs</span>
                        <span>
                            <?php echo esc_html($fight['name2']);?> (<?php echo esc_html($fight['team2_spread_points']);?>)
                        </span>
                        <input type="radio" value="<?php echo esc_attr($fight['fighterID2']);?>" name="spread<?php echo esc_attr($fight['fightID']);?>" data-fightid="<?php echo esc_attr($fight['fightID']);?>" <?php if($fight['winner_spreadID'] == $fight['fighterID2']):?>checked="checked"<?php endif;?> <?php if($fight['started'] == 1):?>disabled="true"<?php endif;?>>
                    </td>
                    <td style="width:30%">
                        <span>
                            <?php echo esc_html($fight['name1']);?>
                        </span>
                        <input type="radio" class="fightID" value="<?php echo esc_attr($fight['fighterID1']);?>" name="winner<?php echo esc_attr($fight['fightID']);?>" data-fightid="<?php echo esc_attr($fight['fightID']);?>" <?php if($fight['winnerID'] == $fight['fighterID1']):?>checked="checked"<?php endif;?> <?php if($fight['started'] == 1):?>disabled="true"<?php endif;?>>
                        <span>vs</span>
                        <span>
                            <?php echo esc_html($fight['name2']);?>
                        </span>
                        <input type="radio" class="fightID" value="<?php echo esc_attr($fight['fighterID2']);?>" name="winner<?php echo esc_attr($fight['fightID']);?>" data-fightid="<?php echo esc_attr($fight['fightID']);?>" <?php if($fight['winnerID'] == $fight['fighterID2']):?>checked="checked"<?php endif;?> <?php if($fight['started'] == 1):?>disabled="true"<?php endif;?>>
                    </td>
                    <td>
                        <span>
                            <?php echo esc_html(__('Over', 'victorious'));?> <?php echo esc_html($fight['over_under']);?>
                        </span>
                        <input type="radio" value="over" name="over_under_value<?php echo esc_attr($fight['fightID']);?>" <?php if($fight['over_under_value'] == 'over'):?>checked="checked"<?php endif;?> <?php if($fight['started'] == 1):?>disabled="true"<?php endif;?>>
                        <br/><br/>
                        <span>
                            <?php echo esc_html(__('Under', 'victorious'));?> <?php echo esc_html($fight['over_under']);?>
                        </span>
                        <input type="radio" value="under" name="over_under_value<?php echo esc_attr($fight['fightID']);?>" <?php if($fight['over_under_value'] == 'under'):?>checked="checked"<?php endif;?> <?php if($fight['started'] == 1):?>disabled="true"<?php endif;?>>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </form>
    <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'pick_footer.php');?>
</div>

<script>
    jQuery(document).ready(function(){
        jQuery.pickultimate.initPickUltimate();
    })
</script>