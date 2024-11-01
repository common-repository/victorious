<?php
foreach ($players as $player):
    $position = $player['position'];
    $injury_status = $player['injury_status'];
    ?>
    <tr class="player_item" id="player_<?php echo esc_attr($player['id']);?>" data-image="<?php echo esc_attr($player['image_url']);?>" data-name="<?php echo esc_attr($player['name']);?>" data-salary="<?php echo esc_attr($player['salary']);?>" data-fight_id="<?php echo esc_attr($fight['fightID']);?>" data-position_id="<?php echo esc_attr($player['position_id']);?>">
        <td style="width: 10%;">
            <?php echo esc_html($position['name']);?>
        </td>
        <td class="player_info" data-id="<?php echo esc_attr($player['id']);?>">
            <?php echo esc_html($player['name']).($injury_status != null ? VIC_PlayerIndicator($injury_status['alias'], $player['is_pitcher']) : "");?>
        </td>
        <td><?php echo VIC_FormatMoney($player['salary']);?></td>
        <td>
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
