<div class="contentPlugin">
    <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'pick_header.php');?>
    <form id="formData">
        <input type="hidden" value="<?php echo esc_attr($league['poolID']);?>" name="poolID">
        <input type="hidden" value="<?php echo esc_attr($league['leagueID']);?>" name="leagueID">
        <input type="hidden" value="<?php echo esc_attr($entry_number);?>" name="entry_number">
        <input type="hidden" value="<?php echo esc_attr($aFights[0]['fightID']);?>" name="fightID">
        <input type="hidden" value="" name="winner<?php echo esc_attr($aFights[0]['fightID']); ?>">
        <input type="hidden" value='<?php echo esc_attr($picksquare); ?>' name="pick_squares" id="pick_squares">
        <input type="hidden" value='<?php echo esc_attr($userSquares); ?>' name="user_squares" id="user_squares">
        <table border="0" class="table table-striped table-bordered table-responsive table-condensed">
            <tbody>
                <?php foreach($aFights as $aFight):?>
                <tr>
                    <td style="text-align:center;width:30%">
                        <?php echo esc_html($aFight['allow_spread'] ? $aFight['team1_spread_points'] : '');?>
                        <?php echo esc_html($aFight['allow_moneyline'] ? $aFight['team1_moneyline'] : '');?>
                        <br><?php echo esc_html($aFight['name1']);?>
                        <br>&nbsp;
                    </td>
                    <td style="text-align:center;vertical-align: middle">
                        <?php echo esc_html($aFight['allow_spread'] ? __('Spread').'<br><br>' : '');?>
                        <?php echo esc_html($aFight['allow_moneyline'] ? __('Money Line').'<br><br>' : '');?>
                        VS
                        <br><?php echo VIC_DateTranslate($aFight['startDate']);?>
                    </td>
                    <td style="text-align:center;width:30%">
                        <?php echo esc_html($aFight['allow_spread'] ? $aFight['team2_spread_points'] : '');?>
                        <?php echo esc_html($aFight['allow_moneyline'] ? $aFight['team2_moneyline'] : '');?>
                        <br><?php echo esc_html($aFight['name2']);?>
                        <br>&nbsp;
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        <div class="row" style="margin-top:10px;margin-bottom: 10px;">
            <div class="col-md-12">
                <!--        draw cell-->
                <table border="1" class="table-bordered picksquare_table">
                    <?php
                      $picksquare = json_decode($picksquare,true);
                      $userSquares = json_decode($userSquares,true);
                      $rows = $userSquares[0];
                      $cols = $userSquares[1];
                    foreach ($rows as $row) {
                        echo '<tr>';
                        foreach ($cols as $col) {
                            $content = $row.'_'.$col;
                            if(!empty($picksquare)){
                                 if(in_array($content, $picksquare)){
                                      echo '<td style="background:yellow">'.esc_html($content).'</td>';
                                 }else{
                                      echo '<td>'.esc_html($content).'</td>';
                                 }
                            }else{
                                echo '<td>'.esc_html($content).'</td>';
                            }
                        }
                        echo '</tr>';
                    }
                    ?>
                </table>
            </div>
        </div>
    </form>
    <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'pick_footer.php');?>
</div>

<script>
    jQuery(document).ready(function(){
        jQuery.picksquares.initPickSquares();
    })
</script>