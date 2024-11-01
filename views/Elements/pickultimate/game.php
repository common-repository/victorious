<div id="main" class="site-main site-info">
    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">
            <article class="hentry">
                <div class="vc-section">
                    <div class="p-3">
                        <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'pick_header.php');?>
                        <div class="vc-table">
                            <form id="formData">
                                <input type="hidden" value="<?php echo esc_attr($league['poolID']);?>" name="poolID">
                                <input type="hidden" value="<?php echo esc_attr($league['leagueID']);?>" name="leagueID">
                                <input type="hidden" value="<?php echo esc_attr($entry_number);?>" name="entry_number">
                                <table cellspacing="0" cellpadding="0">
                                    <thead>
                                    <tr>
                                        <th><?php echo esc_html(__("Time & Date"));?></th>
                                        <th class="text-center"><?php echo esc_html(__("Game Vs. Points"));?></th>
                                        <th class="text-center"><?php echo esc_html(__("Winner"));?></th>
                                        <th class="text-center"><?php echo esc_html(__("Total Score"));?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($fights as $fight):?>
                                        <tr>
                                            <td data-label="<?php echo esc_html(__("Time & Date"));?>"><?php echo VIC_DateTranslate($fight['startDate']);?></td>
                                            <td>
                                                <div class="vc-pickem-compare-item vc-select-winner <?php echo ($fight['winner_spreadID'] == $fight['fighterID1']) ? 'active' : '';?>">
                                                    <?php if(!empty($fight['full_image_path1'])):?>
                                                        <img src="<?php echo esc_url($fight['full_image_path1']);?>" alt="">
                                                    <?php endif;?>
                                                    <div class="vc-pickem-compare-item-name"><?php echo esc_html($fight['name1']);?> (<?php echo esc_html($fight['team1_spread']);?>)</div>
                                                    <input type="radio" value="<?php echo esc_attr($fight['fighterID1']);?>" style="display: none" name="spread<?php echo esc_attr($fight['fightID']);?>" data-fightid="<?php echo esc_attr($fight['fightID']);?>" <?php if($fight['winner_spreadID'] == $fight['fighterID1']):?>checked="checked"<?php endif;?> <?php if($fight['started'] == 1):?>disabled="true"<?php endif;?>>
                                                </div>
                                                <div class="vc-pickem-compare-item vc-select-winner <?php echo ($fight['winner_spreadID'] == $fight['fighterID2']) ? 'active' : '';?>">
                                                    <?php if(!empty($fight['full_image_path2'])):?>
                                                        <img src="<?php echo esc_url($fight['full_image_path2']);?>" alt="">
                                                    <?php endif;?>
                                                    <div class="vc-pickem-compare-item-name"><?php echo esc_html($fight['name2']);?> (<?php echo esc_html($fight['team2_spread']);?>)</div>
                                                    <input type="radio" value="<?php echo esc_attr($fight['fighterID2']);?>" style="display: none" name="spread<?php echo esc_attr($fight['fightID']);?>" data-fightid="<?php echo esc_attr($fight['fightID']);?>" <?php if($fight['winner_spreadID'] == $fight['fighterID2']):?>checked="checked"<?php endif;?> <?php if($fight['started'] == 1):?>disabled="true"<?php endif;?>>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="vc-pickem-compare-item vc-select-winner <?php echo ($fight['winnerID'] == $fight['fighterID1']) ? 'active' : '';?>">
                                                    <?php if(!empty($fight['full_image_path1'])):?>
                                                        <img src="<?php echo esc_url($fight['full_image_path1']);?>" alt="">
                                                    <?php endif;?>
                                                    <div class="vc-pickem-compare-item-name"><?php echo esc_html($fight['name1']);?></div>
                                                    <input type="radio" class="fightID" value="<?php echo esc_attr($fight['fighterID1']);?>" style="display: none" name="winner<?php echo esc_attr($fight['fightID']);?>" data-fightid="<?php echo esc_attr($fight['fightID']);?>" <?php if($fight['winnerID'] == $fight['fighterID1']):?>checked="checked"<?php endif;?> <?php if($fight['started'] == 1):?>disabled="true"<?php endif;?>>
                                                </div>
                                                <div class="vc-pickem-compare-item vc-select-winner <?php echo ($fight['winnerID'] == $fight['fighterID2']) ? 'active' : '';?>">
                                                    <?php if(!empty($fight['full_image_path2'])):?>
                                                        <img src="<?php echo esc_url($fight['full_image_path2']);?>" alt="">
                                                    <?php endif;?>
                                                    <div class="vc-pickem-compare-item-name"><?php echo esc_html($fight['name2']);?></div>
                                                    <input type="radio" class="fightID" value="<?php echo esc_attr($fight['fighterID2']);?>" style="display: none" name="winner<?php echo esc_attr($fight['fightID']);?>" data-fightid="<?php echo esc_attr($fight['fightID']);?>" <?php if($fight['winnerID'] == $fight['fighterID2']):?>checked="checked"<?php endif;?> <?php if($fight['started'] == 1):?>disabled="true"<?php endif;?>>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="vc-pickem-compare-item vc-select-winner <?php echo ($fight['over_under_value'] == 'over') ? 'active' : '';?>">
                                                    <div class="vc-pickem-compare-item-name"><?php echo esc_html(__('Over', 'victorious'));?> <?php echo esc_html($fight['total_over_under']);?></div>
                                                    <input type="radio" value="over" style="display: none" name="over_under_value<?php echo esc_attr($fight['fightID']);?>" <?php if($fight['over_under_value'] == 'over'):?>checked="checked"<?php endif;?> <?php if($fight['started'] == 1):?>disabled="true"<?php endif;?>>
                                                </div>
                                                <div class="vc-pickem-compare-item vc-select-winner <?php echo ($fight['over_under_value'] == 'under') ? 'active' : '';?>">
                                                    <div class="vc-pickem-compare-item-name"><?php echo esc_html(__('Under', 'victorious'));?> <?php echo esc_html($fight['total_over_under']);?></div>
                                                    <input type="radio" value="under" style="display: none" name="over_under_value<?php echo esc_attr($fight['fightID']);?>" <?php if($fight['over_under_value'] == 'under'):?>checked="checked"<?php endif;?> <?php if($fight['started'] == 1):?>disabled="true"<?php endif;?>>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach;?>
                                    </tbody>
                                </table>
                            </form>
                            <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'pick_footer.php');?>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function(){
        jQuery.pickultimate.initPickUltimate();
    })
</script>