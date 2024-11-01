<div class="contentPlugin">
    <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'pick_header.php');?>
    <form id="formData">
        <input type="hidden" value="<?php echo $league['poolID'];?>" name="poolID">
        <input type="hidden" value="<?php echo $league['leagueID'];?>" name="leagueID">
        <input type="hidden" value="<?php echo $entry_number;?>" name="entry_number">
        <table border="0" class="table table-striped table-bordered table-responsive table-condensed tb-submit">
            <thead> 
                <tr> 
                    <th><?php echo __("Time & Date");?></th>
                    <th><?php echo __("Game Vs. Points");?></th>
                    <th><?php echo __("Winner");?></th>
                    <th><?php echo __("Total Score");?></th>
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
                            <?php echo $fight['name1'];?> (<?php echo $fight['team1_spread_points'];?>)
                        </span>
                        <input type="radio" value="<?php echo $fight['fighterID1'];?>" name="spread<?php echo $fight['fightID'];?>" data-fightid="<?php echo $fight['fightID'];?>" <?php if($fight['winner_spreadID'] == $fight['fighterID1']):?>checked="checked"<?php endif;?> <?php if($fight['started'] == 1):?>disabled="true"<?php endif;?>>
                        <span>vs</span>
                        <span>
                            <?php echo $fight['name2'];?> (<?php echo $fight['team2_spread_points'];?>)
                        </span>
                        <input type="radio" value="<?php echo $fight['fighterID2'];?>" name="spread<?php echo $fight['fightID'];?>" data-fightid="<?php echo $fight['fightID'];?>" <?php if($fight['winner_spreadID'] == $fight['fighterID2']):?>checked="checked"<?php endif;?> <?php if($fight['started'] == 1):?>disabled="true"<?php endif;?>>
                    </td>
                    <td style="width:30%">
                        <span>
                            <?php echo $fight['name1'];?>
                        </span>
                        <input type="radio" class="fightID" value="<?php echo $fight['fighterID1'];?>" name="winner<?php echo $fight['fightID'];?>" data-fightid="<?php echo $fight['fightID'];?>" <?php if($fight['winnerID'] == $fight['fighterID1']):?>checked="checked"<?php endif;?> <?php if($fight['started'] == 1):?>disabled="true"<?php endif;?>>
                        <span>vs</span>
                        <span>
                            <?php echo $fight['name2'];?>
                        </span>
                        <input type="radio" class="fightID" value="<?php echo $fight['fighterID2'];?>" name="winner<?php echo $fight['fightID'];?>" data-fightid="<?php echo $fight['fightID'];?>" <?php if($fight['winnerID'] == $fight['fighterID2']):?>checked="checked"<?php endif;?> <?php if($fight['started'] == 1):?>disabled="true"<?php endif;?>>
                    </td>
                    <td>
                        <span>
                            <?php echo __('Over', 'victorious');?> <?php echo $fight['over_under'];?>
                        </span>
                        <input type="radio" value="over" name="over_under_value<?php echo $fight['fightID'];?>" <?php if($fight['over_under_value'] == 'over'):?>checked="checked"<?php endif;?> <?php if($fight['started'] == 1):?>disabled="true"<?php endif;?>>
                        <br/><br/>
                        <span>
                            <?php echo __('Under', 'victorious');?> <?php echo $fight['over_under'];?>
                        </span>
                        <input type="radio" value="under" name="over_under_value<?php echo $fight['fightID'];?>" <?php if($fight['over_under_value'] == 'under'):?>checked="checked"<?php endif;?> <?php if($fight['started'] == 1):?>disabled="true"<?php endif;?>>
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