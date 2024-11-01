<div class="for_survival" style="display: none">
    <h3 class="widget-title">
        <?php echo esc_html(__('Allow pick from', 'victorious'));?>
        <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span>
    </h3>
    <div class="">
        <div class="col-md-4">
            <select name="survival_allow_pick_from">
                <?php for($i = 0; $i < 7; $i++):
                    $day = strtolower(jddayofweek($i,1));
                ?>
                    <option value="<?php echo $day;?>">
                        <?php echo ucfirst($day);?>
                    </option>
                <?php endfor;?>
            </select>
        </div>
        <div class="col-md-1">
            <?php echo esc_html(__("Hour", 'victorious'));?>
        </div>
        <div class="col-md-1">
            <select name="survival_allow_pick_from_hour">
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
            <select name="survival_allow_pick_from_minute">
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
        <?php echo esc_html(__('Allow pick to', 'victorious'));?>
        <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span>
    </h3>
    <div class="">
        <div class="col-md-4">
            <select name="survival_allow_pick_to">
                <?php for($i = 0; $i < 7; $i++):
                    $day = strtolower(jddayofweek($i,1));
                ?>
                    <option value="<?php echo $day;?>">
                        <?php echo ucfirst($day);?>
                    </option>
                <?php endfor;?>
            </select>
        </div>
        <div class="col-md-1">
            <?php echo esc_html(__("Hour", 'victorious'));?>
        </div>
        <div class="col-md-1">
            <select name="survival_allow_pick_to_hour">
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
            <select name="survival_allow_pick_to_minute">
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
        <?php echo esc_html(__('Reminder', 'victorious'));?>
        <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span>
    </h3>
    <div class="">
        <div class="col-md-4">
            <select name="survival_reminder">
                <?php for($i = 0; $i < 7; $i++):
                    $day = strtolower(jddayofweek($i,1));
                ?>
                    <option value="<?php echo $day;?>">
                        <?php echo ucfirst($day);?>
                    </option>
                <?php endfor;?>
            </select>
        </div>
        <div class="col-md-1">
            <?php echo esc_html(__("Hour", 'victorious'));?>
        </div>
        <div class="col-md-1">
            <select name="survival_reminder_hour">
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
            <select name="survival_reminder_minute">
                <?php for($i = 0; $i <= 59; $i++):?>
                    <option value="<?php echo $i;?>">
                        <?php echo $i;?>
                    </option>
                <?php endfor;?>
            </select>
        </div>
        <div class="clear"></div>
    </div>
</div>

