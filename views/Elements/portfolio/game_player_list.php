<?php
    foreach ($players as $player):
?>
    <tr class="player_item" id="player_<?php echo esc_attr($player['id']);?>" data-image="<?php echo esc_attr($player['image_url']);?>" data-name="<?php echo esc_html($player['name']);?>" data-salary="<?php echo esc_attr($player['salary']);?>" data-fight_id="<?php echo esc_attr($fight['fightID']);?>" data-position_id="<?php echo esc_attr($player['position_id']);?>" data-prior-day="<?php echo esc_attr($player['prior_day']);?>" data-prior-day-percent="<?php echo esc_attr($player['prior_day_percent']);?>">
        <td class="f-player-name player_info" data-id="<?php echo esc_attr($player['id']);?>" data-label="<?php echo esc_html(__('Name', 'victorious'));?>">
            <div class="vc-player-wrap">
                <span class="vc-player-avatar f-player-image <?php if(empty($player['image_url'])):?>f-no-image<?php endif;?>">
                    <?php if(!empty($player['image_url'])):?>
                        <img src="<?php echo esc_url($player['image_url']);?>">
                    <?php endif;?>
                </span>
                <div class="vc-player-info">
                    <div class="vc-player-name color-blue"><?php echo esc_html($player['name']);?></div>
                </div>
            </div>
        </td>
        <td class="f-player-salary" data-label="<?php echo esc_html(__('Price', 'victorious'));?>"><?php echo VIC_FormatMoney($player['salary'], null, null, null, false);?></td>
        <td class="f-player-add">
            <a class="vc-player-add btn_add_lineup add_lineup_<?php echo esc_attr($player['position_id']);?>" id="btn_add_lineup_<?php echo esc_attr($player['id']);?>" data-id="<?php echo esc_attr($player['id']);?>">
                <span class="material-icons"> add_circle_outline </span>
            </a>
            <a class="vc-player-remove btn_remove_lineup remove_lineup_<?php echo esc_attr($player['position_id']);?>" style="display:none" id="btn_remove_lineup_<?php echo esc_attr($player['id']);?>" data-player_id="<?php echo esc_attr($player['id']);?>">
                <span class="material-icons"> remove_circle_outline </span>
            </a>
        </td>
    </tr>
<?php endforeach;?>
<tr>
    <td colspan="7">
        <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'pagination.php');?>
    </td>
</tr>
