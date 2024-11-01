<?php
    foreach ($players as $player):
        $team = $player['team'];
        $position = $player['position'];
        $injury_status = $player['injury_status'];
        $fight = $player['fight'];
        $home_team = $fight['home_team'];
        $away_team = $fight['away_team'];
?>
    <tr class="player_item" id="player_<?php echo esc_attr($player['id']);?>" data-image="<?php echo esc_attr($player['image_url']);?>" data-name="<?php echo esc_attr($player['name']);?>" data-salary="<?php echo esc_attr($player['salary']);?>" data-fight_id="<?php echo esc_attr($fight['fightID']);?>" data-position_id="<?php echo esc_attr($player['position_id']);?>">
        <td data-label="Name">
            <div class="vc-player-wrap">
                <?php if(!empty($player['image_url'])):?>
                <span class="vc-player-avatar f-player-image <?php if(empty($player['image_url'])):?>f-no-image<?php endif;?>">
                    <img src="<?php echo esc_url($player['image_url']);?>">
                </span>
                <?php endif;?>
                <div class="vc-player-info">
                    <div class="vc-player-name color-blue"><?php echo esc_html($player['name']);?></div>
                    <?php if($league['is_horse']):?>
                        <span class="vc-player-position"><?php echo esc_html($player['race']);?></span>
                    <?php elseif($position != null):?>
                        <span class="vc-player-position"><?php echo esc_html($position['name']);?></span>
                    <?php endif;?>
                    <?php if(!empty($injury_status)):?>
                        <span class="vc-player-status vc-ir"><?php echo VIC_PlayerIndicator($injury_status['alias'], $player['is_pitcher']);?></span>
                    <?php endif;?>
                </div>
            </div>
        </td>
        <?php if($league['is_team']):?>
        <td data-label="Team"><?php echo esc_html($team['name']);?></td>
        <td data-label="Game">
            <?php if($player['team_id'] == $away_team['teamID']):?>
                <b><?php echo esc_html(__("A:", "victorious"))." ".esc_html($away_team['nickName']);?></b> @ <?php echo esc_html(__("H:", "victorious"))." ".esc_html($home_team['nickName']);?>
            <?php else:?>
                <?php echo esc_html(__("A:", "victorious"))." ".esc_html($away_team['nickName']);?> @ <b><?php echo esc_html(__("H:", "victorious"))." ".esc_html($home_team['nickName']);?></b>
            <?php endif;?>
        </td>
        <?php endif;?>
        <td data-label="Salary"><?php echo VIC_FormatMoney($player['salary']);?></td>
        <td>
            <?php if($player['can_draft']):?>
                <a class="vc-player-add btn_add_lineup add_lineup_<?php echo esc_attr($player['position_id']);?>" id="btn_add_lineup_<?php echo esc_attr($player['id']);?>" data-id="<?php echo esc_attr($player['id']);?>">
                    <span class="material-icons"> add_circle_outline </span>
                </a>
            <?php endif;?>
        </td>
    </tr>
<?php endforeach;?>
<tr>
    <td colspan="7">
        <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'pagination.php');?>
    </td>
</tr>
