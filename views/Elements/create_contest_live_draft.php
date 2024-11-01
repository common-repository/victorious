<div class="for_live_draft" style="display: none">
    <h3 class="widget-title">
        <?php echo esc_html(__('Number of players that be changed via Waiver Wire', 'victorious'));?>
        <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span>
    </h3>
    <div class="">
        <div class="col-md-3">
            <select name="waiver_wire_player_quantity">
                <?php for($i = 1; $i <= 9; $i++):?>
                    <option value="<?php echo $i;?>">
                        <?php echo $i;?>
                    </option>
                <?php endfor;?>
            </select>
        </div>
        <div class="clear"></div>
    </div>
    
    <h3 class="widget-title">
        <?php echo esc_html(__('Number of minutes a contestant has to draft', 'victorious'));?>
        <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span>
    </h3>
    <div class="">
        <div class="col-md-3">
            <input type="text" name="live_draft_minute_change_player">
        </div>
        <div class="clear"></div>
        <p><?php echo esc_html(__('Must be equal or greater than 2 minutes', 'victorious'));?></p>
        <div class="clear"></div>
    </div>

    <h3 class="widget-title">
        <?php echo esc_html(__('Start draft date time', 'victorious'));?>
        <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span>
    </h3>
    <div class="">
        <div class="col-md-4">
            <input type="text" class="form-control disabled" name="live_draft_start_date" id="live_draft_start" readonly style="width: auto">
        </div>
        <div class="col-md-1">
            <?php echo esc_html(__("Hour", 'victorious'));?>
        </div>
        <div class="col-md-1">
            <select name="live_draft_start_hour">
                <?php for($i = 0; $i <= 23; $i++):?>
                    <option value="<?php echo $i;?>">
                        <?php echo $i;?>
                    </option>
                <?php endfor;?>
            </select>
        </div>
        <div class="col-md-1">
            <?php echo esc_html(__("Minute", 'victorious'));?>
        </div>
        <div class="col-md-1">
            <select name="live_draft_start_minute">
                <?php for($i = 0; $i <= 59; $i++):?>
                    <option value="<?php echo $i;?>">
                        <?php echo $i;?>
                    </option>
                <?php endfor;?>
            </select>
        </div>
        <div class="clear"></div>
    </div>
    
    <h3 class="widget-title">
        <?php echo esc_html(__('Waiver Wire starts', 'victorious'));?>
        <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span>
    </h3>
    <div class="">
        <div class="col-md-3">
            <select name="live_draft_waiver_wire_start">
                <?php for($i = 0; $i < 7; $i++):
                    $day = strtolower(jddayofweek($i,1));
                ?>
                    <option value="<?php echo $day;?>">
                        <?php echo ucfirst($day);?>
                    </option>
                <?php endfor;?>
            </select>
        </div>
        <div class="clear"></div>
    </div>
    
    <h3 class="widget-title">
        <?php echo esc_html(__('Waiver Wire ends', 'victorious'));?>
        <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span>
    </h3>
    <div class="">
        <div class="col-md-3">
            <select name="live_draft_waiver_wire_end">
                <?php for($i = 0; $i < 7; $i++):
                    $day = strtolower(jddayofweek($i,1));
                ?>
                    <option value="<?php echo $day;?>">
                        <?php echo ucfirst($day);?>
                    </option>
                <?php endfor;?>
            </select>
        </div>
        <div class="clear"></div>
    </div>
    
    <h3 class="widget-title">
        <?php echo esc_html(__('Number of bench players', 'victorious'));?>
    </h3>
    <div class="">
        <div class="col-md-3">
            <input type="text" name="live_draft_bench_quantity">
        </div>
        <div class="clear"></div>
    </div>
    
    <h3 class="widget-title">
        <?php echo esc_html(__('Number of minutes users can enter draft room before live draft starts', 'victorious'));?>
    </h3>
    <div class="">
        <div class="col-md-3">
            <input type="text" name="live_draft_minute_prior_draft_room">
        </div>
        <div class="clear"></div>
    </div>
</div>

