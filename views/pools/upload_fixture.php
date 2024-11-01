<?php foreach($aFights as $aFight):?>
<div class="fight_container">
    <div class="title_area">
        <div class="fight_number_title">*<?php echo esc_html(__("Fixture", 'victorious'));?> <?php echo esc_html($aFight['count']);?></div>
        <a onclick="return jQuery.fight.removeFight(this);" class="fight_action fight_remove" href="#">
            <img src="<?php echo esc_url(VICTORIOUS__PLUGIN_URL_IMAGE.'delete.png');?>" alt="Delete" title="Delete" />
        </a>&nbsp;&nbsp;
        <a onclick="return jQuery.fight.addFight(this);" class="fight_action fight_add" href="#">
            <img src="<?php echo esc_url(VICTORIOUS__PLUGIN_URL_IMAGE.'add.png');?>" alt="Add" title="Add" />
        </a>
        <input type="hidden" name="val[fight][]" class="fight" value="" />
        <input type="hidden" data-name="fightID" value="<?php echo esc_html($aFight['fightID']);?>" />
    </div>
    <table>
        <tr>
            <td>
                <div class="table">
                    <div class="table_left">
                        <?php echo esc_html(__("Fixture Name"));?>  <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span>:
                    </div>
                    <div class="table_right">
                        <input type="text" data-name="fight_name" value="<?php echo esc_html($aFight['name']);?>" size="40"/>
                    </div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="table">
                    <div class="table_left text-left">
                        <?php echo esc_html(__("Start Date"));?>  <span class="description">(<?php echo esc_html(__("required", 'victorious'));?>)</span>:
                    </div>
                    <div class="table_right">
                        <input type="text" class="fightDatePicker" data-name="fight_startDate" value="<?php echo esc_html($aFight['startDateOnly']);?>" size="40"/>
                        <?php echo esc_html(__("Hour", 'victorious'));?>:
                        <select data-name="fight_startHour">
                            <?php foreach($aPoolHours as $aPoolHour):?>
                            <option value="<?php echo esc_attr($aPoolHour);?>" <?php echo esc_attr($aFight['startHour'] == $aPoolHour ? 'selected="true"' : '');?>><?php echo esc_html($aPoolHour);?></option>
                            <?php endforeach;?>
                        </select>
                        <?php echo esc_html(__("Minute", 'victorious'));?>:
                        <select data-name="fight_startMinute">
                            <?php foreach($aPoolMinutes as $aPoolMinute):?>
                            <option value="<?php echo esc_attr($aPoolMinute);?>" <?php echo esc_attr($aFight['startMinute'] == $aPoolMinute ? 'selected="true"' : '');?>><?php echo esc_html($aPoolMinute);?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
    </table>
</div>
<?php endforeach;?>