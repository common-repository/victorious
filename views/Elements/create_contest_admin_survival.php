<table class="for_survival form-table" style="display: none">
    <tr valign="top">
        <th scope="row"><?php echo esc_html(__('Allow pick from', 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></th>
        <td>
            <div class="table">
                <?php 
                    $survival_allow_pick_from = "";
                    $survival_allow_pick_from_hour = 0;
                    $survival_allow_pick_from_minute = 0;
                    if(!empty($aForms['survival_allow_pick_from']))
                    {
                        $temp = $aForms['survival_allow_pick_from'];
                        $temp = explode("_", $temp);
                        $survival_allow_pick_from = $temp[0];
                        $survival_allow_pick_from_hour = $temp[1];
                        $survival_allow_pick_from_minute = $temp[2];
                    }
                ?>
                <div class="table_left" style="width:auto">
                    <select name="survival_allow_pick_from">
                        <?php for($i = 0; $i < 7; $i++):
                            $day = strtolower(jddayofweek($i,1));
                        ?>
                            <option value="<?php echo $day;?>" <?php echo $survival_allow_pick_from == $day ? 'selected="selected"' : "";?>>
                                <?php echo ucfirst($day);?>
                            </option>
                        <?php endfor;?>
                    </select>                
                </div>
                <div class="table_right">
                    <?php echo esc_html(__("Hour", 'victorious'));?>
                    <select name="survival_allow_pick_from_hour">
                        <?php for($i = 0; $i <= 23; $i++):?>
                            <option value="<?php echo $i;?>" <?php echo $survival_allow_pick_from_hour == $i ? 'selected="selected"' : "";?>>
                                <?php echo $i;?>
                            </option>
                        <?php endfor;?>
                    </select>
                    <?php echo esc_html(__("Minute", 'victorious'));?>
                    <select name="survival_allow_pick_from_minute">
                        <?php for($i = 0; $i <= 59; $i++):?>
                            <option value="<?php echo $i;?>" <?php echo $survival_allow_pick_from_minute == $i ? 'selected="selected"' : "";?>>
                                <?php echo $i;?>
                            </option>
                        <?php endfor;?>
                    </select>
                </div>
            </div>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php echo esc_html(__('Allow pick to', 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></th>
        <td>
            <div class="table">
                <?php 
                    $survival_allow_pick_to = "";
                    $survival_allow_pick_to_hour = 0;
                    $survival_allow_pick_to_minute = 0;
                    if(!empty($aForms['survival_allow_pick_to']))
                    {
                        $temp = $aForms['survival_allow_pick_to'];
                        $temp = explode("_", $temp);
                        $survival_allow_pick_to = $temp[0];
                        $survival_allow_pick_to_hour = $temp[1];
                        $survival_allow_pick_to_minute = $temp[2];
                    }
                ?>
                <div class="table_left" style="width:auto">
                    <select name="survival_allow_pick_to">
                        <?php for($i = 0; $i < 7; $i++):
                            $day = strtolower(jddayofweek($i,1));
                        ?>
                            <option value="<?php echo $day;?>" <?php echo $survival_allow_pick_to == $day ? 'selected="selected"' : "";?>>
                                <?php echo ucfirst($day);?>
                            </option>
                        <?php endfor;?>
                    </select>
                </div>
                <div class="table_right">
                    <?php echo esc_html(__("Hour", 'victorious'));?>
                    <select name="survival_allow_pick_to_hour">
                        <?php for($i = 0; $i <= 23; $i++):?>
                            <option value="<?php echo $i;?>" <?php echo $survival_allow_pick_to_hour == $i ? 'selected="selected"' : "";?>>
                                <?php echo $i;?>
                            </option>
                        <?php endfor;?>
                    </select>
                    <?php echo esc_html(__("Minute", 'victorious'));?>
                    <select name="survival_allow_pick_to_minute">
                        <?php for($i = 0; $i <= 59; $i++):?>
                            <option value="<?php echo $i;?>" <?php echo $survival_allow_pick_to_minute == $i ? 'selected="selected"' : "";?>>
                                <?php echo $i;?>
                            </option>
                        <?php endfor;?>
                    </select>
                </div>
            </div>
        </td>
    </tr>
    <tr valign="top">
        <th scope="row"><?php echo esc_html(__('Reminder', 'victorious'));?> <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span></th>
        <td>
            <div class="table">
                <?php 
                    $survival_reminder = "";
                    $survival_reminder_hour = 0;
                    $survival_reminder_minute = 0;
                    if(!empty($aForms['survival_reminder']))
                    {
                        $temp = $aForms['survival_reminder'];
                        $temp = explode("_", $temp);
                        $survival_reminder = $temp[0];
                        $survival_reminder_hour = $temp[1];
                        $survival_reminder_minute = $temp[2];
                    }
                ?>
                <div class="table_left" style="width:auto">
                    <select name="survival_reminder">
                        <?php for($i = 0; $i < 7; $i++):
                            $day = strtolower(jddayofweek($i,1));
                        ?>
                            <option value="<?php echo $day;?>" <?php echo $survival_reminder == $day ? 'selected="selected"' : "";?>>
                                <?php echo ucfirst($day);?>
                            </option>
                        <?php endfor;?>
                    </select>
                </div>
                <div class="table_right">
                    <?php echo esc_html(__("Hour", 'victorious'));?>
                    <select name="survival_reminder_hour">
                        <?php for($i = 0; $i <= 23; $i++):?>
                            <option value="<?php echo $i;?>" <?php echo $survival_reminder_hour == $i ? 'selected="selected"' : "";?>>
                                <?php echo $i;?>
                            </option>
                        <?php endfor;?>
                    </select>
                    <?php echo esc_html(__("Minute", 'victorious'));?>
                    <select name="survival_reminder_minute">
                        <?php for($i = 0; $i <= 59; $i++):?>
                            <option value="<?php echo $i;?>" <?php echo $survival_reminder_minute == $i ? 'selected="selected"' : "";?>>
                                <?php echo $i;?>
                            </option>
                        <?php endfor;?>
                    </select>
                </div>
            </div>
        </td>
    </tr>
</table>