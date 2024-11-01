<div class="wrap vc-wrap">
    <h2>
        <?php echo !$bIsEdit ? esc_html(__("Add Auto Contest", 'victorious')) : esc_html(__("Edit Auto Contest", 'victorious'));?>
        <a class="add-new-h2" href="<?php echo esc_url(self::$urlmanageautocontest);?>"><?php echo esc_html(__("Manage Auto Contests", 'victorious'));?></a>
        <?php if($bIsEdit):?>
        <a class="add-new-h2" href="<?php echo esc_url(self::$urladdnewautocontest);?>"><?php echo esc_html(__("Add New", 'victorious'));?></a>
        <?php endif;?>
    </h2>
    <?php include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";?>
    <?php if($checkAPIToken === true):?>
        <div class="vc-tabpane-container-header">
            <?php echo esc_html(__("Create New Auto Contest", 'victorious'));?>
        </div>
        <div class="vc-tabpane-container">
            <?php echo settings_errors();?>
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="val[id]" value="<?php echo !empty($auto_contest['id']) ? esc_html($auto_contest['id']) : "";?>" />
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Pick Your Sports", 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>                  
                    <?php foreach($aSports as $aSport):?>
    
                        <?php if(!empty($aSport['child']) && is_array($aSport['child'])):?>
    
                        <h3 class="vc-tabpane-title"><?php echo esc_html($aSport['name']);?></h3>
                        <div class="list-sport">
                            <?php foreach($aSport['child'] as $aOrg):?>
    
                                <?php if($aOrg['is_active'] == 1):?>
                                    <?php 
                                        $selected = '';
                                        $itemlist = !empty($auto_contest['sports']) ? explode(',', $auto_contest['sports']) : array();
                                        if(!empty($auto_contest['sports']) && in_array($aOrg['id'], $itemlist))
                                        {
                                            $selected = 'checked="checked"';
                                        }
                                    ?>
                                    <div class="list-sport-item">
                                        <div class="list-sport-item-content">
                                            <input name="val[sports][]" id="<?php echo esc_attr($aOrg['id']);?>" value="<?php echo esc_html($aOrg['id']);?>" type="checkbox" <?php echo esc_attr($selected); ?>>
                                            <label><span for="<?php echo esc_html($aOrg['id']);?>"><?php echo esc_html($aOrg['name']);?></span></label>
                                        </div>
                                    </div>
                                <?php endif;?>
    
                            <?php endforeach;?>
                        </div>
                        <?php endif;?>
    
                    <?php endforeach;?>
                </div>
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title"> <?php echo esc_html(__("Game Type", 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>                                             
                    <?php foreach ($game_type as $k => $val) {
                        $selected = '';
                        $itemlist = !empty($auto_contest['game_type']) ? explode(',', $auto_contest['game_type']) : array();
                        if(!empty($auto_contest['game_type']) && in_array($k, $itemlist))
                        {
                            $selected = 'checked="checked"';
                        }
                        echo '<label class="checkbox-control"><input type="checkbox" name="val[game_type][]" value="'.$k.'" '.$selected.' > '.esc_html($val).'<span class="checkmark"></span></label>';
                    } ?>
                </div>
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("League Size", 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                    <select name="val[league_size]">
                        <?php 
                            foreach ($aLeagueSizes as $k => $val) {
                                $selected = '';
                                if(!empty($auto_contest['league_size']) && $auto_contest['league_size'] == $val)
                                {
                                    $selected = 'selected="selected"';
                                }
                                echo '<option value="'.$val.'" '.$selected.' >'.esc_html($val).'</option>';
                            }
                        ?>
                    </select>
                </div>  
                <div class="vc-dashboard-item border-white pb-0 leagueDiv group_prize_structure">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__('Prize Structure', 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                    <label class="radio-control">
                        <input type="radio" name="val[prize_structure]" value="WINNER" checked="true" class="prize_structure">
                        <?php echo esc_html(__('Winner takes all', 'victorious'));?> 
                        <span class="checkmark"></span>
                    </label>
                    <div id="winnerPercent" class="structure_value" style="display: none;">
                        <input type="text" class="form-control mt-2" value="<?php echo !empty($auto_contest['winner_percent']) ? esc_html($auto_contest['winner_percent']) : get_option('victorious_winner_percent');?>" name="val[winner_percent]"><br/>
                    </div>
                    <label class="radio-control">
                        <input  class="prize_structure" type="radio" name="val[prize_structure]" value="TOP_3" <?php if(!empty($auto_contest['prize_structure']) && $auto_contest['prize_structure'] == "TOP_3"):?>checked="true"<?php endif;?>>
                        <?php echo esc_html(__('Top 3 get prizes', 'victorious'));?> 
                        <span class="checkmark"></span>
                    </label>
                    <div id="top3Percent" class="structure_value" style="display: none;">
                        <label style="width: 30px;display: inline-block">1st:</label> <input type="text" class="form-control mt-2" value="<?php echo !empty($auto_contest['first_percent']) ? esc_html($auto_contest['first_percent']) : get_option('victorious_first_place_percent');?>" name="val[first_percent]" id="firstPercent"><br/>
                        <label style="width: 30px;display: inline-block">2nd:</label> <input type="text" class="form-control mt-2" value="<?php echo !empty($auto_contest['second_percent']) ? esc_html($auto_contest['second_percent']) : get_option('victorious_second_place_percent');?>" name="val[second_percent]" id="secondPercent"><br/>
                        <label style="width: 30px;display: inline-block">3rd:</label> <input type="text" class="form-control mt-2" value="<?php echo !empty($auto_contest['third_percent']) ? esc_html($auto_contest['third_percent']) : get_option('victorious_third_place_percent');?>" name="val[third_percent]" id="thirdPercent"><br/>
                        <?php echo esc_html(__('Default values are set by values in settings', 'victorious'));?><br/>
                        <?php echo esc_html(__('Top 3\'s percentages are required, if one of them is set to 0, default value will be set (1st: 50, 2nd: 30, 3rd: 20)', 'victorious'));?>
                    </div>
            
                    <label class="radio-control d-inline-block">
                        <input  class="prize_structure" type="radio" name="val[prize_structure]" value="MULTI_PAYOUT" <?php if(!empty($auto_contest['prize_structure']) && $auto_contest['prize_structure'] == "MULTI_PAYOUT"):?>checked="true"<?php endif;?>>
                        <?php echo esc_html(__('Multi payout', 'victorious'));?> 
                        <span class="checkmark"></span>
                    </label>
                    <span id="addPayouts" onclick="return addPayouts();" href="javascript:void(0)" style="cursor: pointer; display: none" class="structure_value">
                        <img title="<?php echo esc_html(__("Add", 'victorious'));?>" alt="<?php echo esc_html(__("Add", 'victorious'));?>" src="<?php echo VICTORIOUS__PLUGIN_URL_IMAGE.'add.png';?>" style="vertical-align: bottom;">
                    </span>
                    <div id="payoutExample" style="display: none;" class="structure_value mt-2">
                        <?php echo esc_html(__('Example', 'victorious'));?>: <br/>
                        1st: <?php echo esc_html(__('From', 'victorious'));?>  1 <?php echo esc_html(__('to', 'victorious'));?> 1: 40%<br/>
                        2nd: <?php echo esc_html(__('From', 'victorious'));?>  2 <?php echo esc_html(__('to', 'victorious'));?> 2: 30%<br/>
                        3rd: <?php echo esc_html(__('From', 'victorious'));?>  3 <?php echo esc_html(__('to', 'victorious'));?> 3: 20%<br/>
                        4th - 6th: <?php echo esc_html(__('From', 'victorious'));?> 4 <?php echo esc_html(__('to', 'victorious'));?> 6: 10%<br/>
                        <?php echo esc_html(__('Total percent must be 100%', 'victorious'));?>
                    </div>
                    <div id="payouts">
                        <?php if(!empty($auto_contest['payouts'])):
                            $payouts = json_decode($auto_contest['payouts'], true);
                        ?>
                            <?php foreach($payouts as $payout):?> 
                                <div class="mt-2">
                                    <label style="display: inline-block;width: auto">From</label>
                                    <input type="text" style="display: inline-block;width: 50px;padding: 2px 5px;text-align:center" value="<?php echo esc_html($payout['from']);?>" name="val[payouts_from][]">
                                    <label style="display: inline-block;width: auto">To</label>
                                    <input type="text" style="display: inline-block;width: 50px;padding: 2px 5px;text-align:center" value="<?php echo esc_html($payout['to']);?>" name="val[payouts_to][]">
                                    <label style="display: inline-block;width: auto">:</label>
                                    <input type="text" style="display: inline-block;width: 50px;padding: 2px 5px;text-align:center" value="<?php echo esc_html($payout['percent']);?>" name="val[percentage][]">
                                    <label style="display: inline-block;width: auto">%</label>
                                    <a href="javascript:void(0)" onclick="return removePayouts(jQuery(this).parent());">
                                        <img src="<?php echo VICTORIOUS__PLUGIN_URL_IMAGE;?>delete.png" alt="<?php echo esc_html(__('Delete', 'victorious'));?>" title="<?php echo esc_html(__('Delete', 'victorious'));?>">
                                    </a>
                                </div>
                            <?php endforeach;?>
                        <?php endif;?>
                    </div>
                </div>  
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Entry Fee", 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>
                    <select name="val[entry_fee]" class="form-control">
                        <?php 
                        echo '<option value="0">'.esc_html(__("Free", 'victorious')).'</option>';
                        foreach ($aEntryFees as $k => $val) {
                            $selected = '';
                            if(!empty($auto_contest['entry_fee']) && $auto_contest['entry_fee'] == $val)
                            {
                                $selected = 'selected="selected"';
                            }
                            echo '<option value="'.$val.'" '.$selected.' >'.esc_html($val).'</option>';
                        } ?>
                    </select>
                </div>
                <div class="vc-dashboard-item border-white pb-0">
                    <h3 class="vc-tabpane-title"><?php echo esc_html(__("Status", 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>    
                        <select name="val[status]" class="form-control">
                            <option value="active" <?php echo !empty($auto_contest['status']) && $auto_contest['status'] == 'active' ? 'selected="selected"' : "";?>><?php echo esc_html(__("Active", 'victorious'));?></option>
                            <option value="inactive" <?php echo !empty($auto_contest['status']) && $auto_contest['status'] == 'inactive' ? 'selected="selected"' : "";?>><?php echo esc_html(__("InActive", 'victorious'));?></option>
                        </select>
                    </div>         
                    <div class="vc-dashboard-item border-white pb-0">
                        <h3 class="vc-tabpane-title"><?php echo esc_html(__("Name", 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>    
                        <input type="text" class="form-control" name="val[name]" size="50" value="<?php echo !empty($auto_contest['name']) ? esc_html($auto_contest['name']) : "";?>" />
                        <br/>
                        <?php echo esc_html(__("Avaiable variables", 'victorious'));?>
                        <p style="padding-left: 20px;">
                            #sport: sport name
                        </p>
                </div>  
        
                <?php submit_button(); ?>
            </form>
        </div>
    <?php endif;?>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        selectPrizeStructure();
    })
    
    jQuery(document).on('click', '.prize_structure', function(e){
        selectPrizeStructure();
    });
    
    function selectPrizeStructure()
    {
        jQuery("#top3Percent").hide();
        jQuery("#addPayouts").hide();
        jQuery("#payoutExample").hide();
        jQuery("#payouts").hide();
        jQuery("#winnerPercent").hide();
        switch(jQuery('.prize_structure:checked').val()){
            case "WINNER":
                jQuery("#winnerPercent").show();
                break;
            case "TOP_3":
                jQuery("#top3Percent").show();
                break;
            case "MULTI_PAYOUT":
                jQuery("#addPayouts").show();
                jQuery("#payoutExample").show();
                jQuery("#payouts").show();
                break;
        }
    }
    
    function addPayouts()
    {
        var html =
                '<div class="mt-2">\n\
        <label style="display: inline-block;width: auto">' + wpfs['from'] + '</label>\n\
        <input type="text" name="val[payouts_from][]" value="" style="display: inline-block;width: 50px;padding: 2px 5px;text-align:center">\n\
        <label style="display: inline-block;width: auto">' + wpfs['to'] + '</label>\n\
        <input type="text" name="val[payouts_to][]" value="" style="display: inline-block;width: 50px;padding: 2px 5px;text-align:center">\n\
        <label style="display: inline-block;width: auto">:</label>\n\
        <input type="text" name="val[percentage][]" value="" style="display: inline-block;width: 50px;padding: 2px 5px;text-align:center">\n\
        <label style="display: inline-block;width: auto">%</label>\n\
        <a onclick="return removePayouts(jQuery(this).parent());" href="javascript:void(0)">\n\
            <img title="<?php echo esc_html(__("Delete", 'victorious'));?>" alt="<?php echo esc_html(__("Delete", 'victorious'));?>" src="<?php echo VICTORIOUS__PLUGIN_URL_IMAGE;?>delete.png"\>\n\
        </a>\n\
    </div>';
        jQuery("#payouts").append(html);
        return false;
    }
    
    function removePayouts(item)
    {
        item.remove();
        this.calculatePrizes();
        return false;
    }
</script>