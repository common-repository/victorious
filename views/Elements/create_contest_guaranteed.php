<div class="vc-dashboard-item border-white pb-0">
    <h3 class="vc-tabpane-title d-inline-block mt-0 w-200"><?php echo esc_html(__('Guaranteed', 'victorious'));?></h3>
    <label class="checkbox-control d-inline-block mt-0 ml-4">                      
        <input type="checkbox" id="guaranteed" name="is_guaranteed" onchange="return jQuery.createcontest.loadGuaranteedPrizeStructure();" <?php if($aForms['is_guaranteed']):?>checked="true" <?php endif;?> value="1" />
        <span class="checkmark"></span>
    </label>
</div>

<div class="vc-dashboard-item border-white pb-0" id="guaranteed_prizeDiv" style="<?php if(!$aForms['guaranteed']):?>display: none;<?php endif;?>">

    <h3 class="vc-tabpane-title"><?php echo esc_html(__('Guaranteed Prize', 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>

    <input type="text" class="form-control" id="guaranteed_prize" name="guaranteed_prize" placeholder="Guaranteed Prize" value="<?php echo esc_html($aForms['guaranteed_prize']);?>">

</div>

<div class="vc-dashboard-item border-white pb-0" id="guaranteed_top3" style="<?php if(strtolower($aForms['prize_structure']) != "top_3"):?>display: none;<?php endif;?>">

    <h3 class="vc-tabpane-title"><?php echo esc_html(__('Guaranteed Prize Structure', 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>

    <div class="radio">
        <div id="guaranteedTop3Percent">
            <label style="width: 30px;display: inline-block">1st:</label> <input type="text" value="<?php echo !empty($aForms['guaranteed_first_percent']) ? esc_html($aForms['guaranteed_first_percent']) : get_option('victorious_first_place_percent');?>" name="guaranteed_first_percent" id="firstPercent" onkeyup="jQuery.createcontest.calculatePrizes()"><br/>
            <label style="width: 30px;display: inline-block">2nd:</label> <input type="text" value="<?php echo !empty($aForms['guaranteed_second_percent']) ? esc_html($aForms['guaranteed_second_percent']) : get_option('victorious_second_place_percent');?>" name="guaranteed_second_percent" id="secondPercent" onkeyup="jQuery.createcontest.calculatePrizes()"><br/>
            <label style="width: 30px;display: inline-block">3rd:</label> <input type="text" value="<?php echo !empty($aForms['guaranteed_third_percent']) ? esc_html($aForms['guaranteed_third_percent']) : get_option('victorious_third_place_percent');?>" name=" guaranteed_third_percent" id="thirdPercent" onkeyup="jQuery.createcontest.calculatePrizes()"><br/>
            <?php echo esc_html(__('Total percent must be 100%', 'victorious'));?>
        </div>
    </div>

    </td>

</div>

<div class="vc-dashboard-item border-white pb-0" id="guaranteed_multi_payout" style="<?php if(strtolower($aForms['prize_structure']) != "multi_payout"):?>display: none;<?php endif;?>">

    <h3 class="vc-tabpane-title"><?php echo esc_html(__('Guaranteed Prize Structure', 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></h3>

    <div class="radio guaranteed_multi_payout">
        <label>

            <?php echo esc_html(__('Multi payout', 'victorious'));?> 

        </label>

        <a id="addPayoutsGuaranteed" onclick="return jQuery.createcontest.addPayoutsGuaranteed();" href="#" <?php if(empty($aForms['guaranteed_payouts'])):?>style=""<?php endif;?>>
            <img title="<?php echo esc_html(__("Add", 'victorious'));?>" alt="<?php echo esc_html(__("Add", 'victorious'));?>" src="<?php echo VICTORIOUS__PLUGIN_URL_IMAGE.'add.png';?>">
        </a>
        <div id="payoutExample" style="">
            <?php echo esc_html(__('Example', 'victorious'));?>: <br/>
            1st: <?php echo esc_html(__('From', 'victorious'));?>  1 <?php echo esc_html(__('to', 'victorious'));?> 1: 40%<br/>
            2nd: <?php echo esc_html(__('From', 'victorious'));?>  2 <?php echo esc_html(__('to', 'victorious'));?> 2: 30%<br/>
            3rd: <?php echo esc_html(__('From', 'victorious'));?>  3 <?php echo esc_html(__('to', 'victorious'));?> 3: 20%<br/>
            4th - 6th: <?php echo esc_html(__('From', 'victorious'));?> 4 <?php echo esc_html(__('to', 'victorious'));?> 6: 10%<br/>
            <?php echo esc_html(__('Total percent must be 100%', 'victorious'));?>
        </div>
        <div id="payoutsGuaranteed">
            <?php if(!empty($aForms['guaranteed_payouts'])):
                $guaranteed_payouts = json_decode($aForms['guaranteed_payouts'], true);
            ?>
                <?php foreach($guaranteed_payouts as $payout):?> 
                    <div>
                        <label style="display: inline-block;width: auto">From</label>
                        <input type="text" onkeyup="jQuery.createcontest.calculatePrizes()" style="display: inline-block;width: 50px;padding: 2px 5px;text-align:center" value="<?php echo esc_html($payout['from']);?>" name="guaranteed_payouts_from[]">
                        <label style="display: inline-block;width: auto">To</label>
                        <input type="text" onkeyup="jQuery.createcontest.calculatePrizes()" style="display: inline-block;width: 50px;padding: 2px 5px;text-align:center" value="<?php echo esc_html($payout['to']);?>" name="guaranteed_payouts_to[]">
                        <label style="display: inline-block;width: auto">:</label>
                        <input type="text" onkeyup="jQuery.createcontest.calculatePrizes()" style="display: inline-block;width: 50px;padding: 2px 5px;text-align:center" value="<?php echo esc_html($payout['percent']);?>" name="guaranteed_percentage[]">
                        <label style="display: inline-block;width: auto">%</label>
                        <a href="#" onclick="return jQuery.createcontest.removePayouts(jQuery(this).parent());">
                            <img src="<?php echo VICTORIOUS__PLUGIN_URL_IMAGE;?>delete.png" alt="<?php echo esc_html(__('Delete', 'victorious'));?>" title="<?php echo esc_html(__('Delete', 'victorious'));?>">
                        </a>
                    </div>
                <?php endforeach;?>
            <?php endif;?>
        </div>
    </div>
</div>