<div class="info_item">    <select name="pool_id" id="cb_pool">        <?php if($pools != null):?>            <?php foreach($pools as $pool):?>                <option value="<?php echo esc_attr($pool['poolID']);?>" data-teams="<?php echo esc_attr($pool['team_ids']);?>">                    <?php echo esc_html($pool['poolName']);?>                </option>            <?php endforeach;?>        <?php else:?>            <option value="0">                <?php echo esc_html(__("No events", 'victorious'));?>            </option>        <?php endif;?>    </select></div><?php if(!empty($teams)):?><div class="info_item">    <select name="team_id" id="cb_team">        <option value="0"><?php echo esc_html(__("All teams", 'victorious'));?></option>        <?php foreach($teams as $team):?>            <option value="<?php echo esc_attr($team['teamID']);?>">                <?php echo esc_html($team['name']);?>            </option>        <?php endforeach;?>    </select></div><?php endif;?><?php if(!empty($player_positions)):?><div class="info_item">    <select name="position_id">        <option value="0"><?php echo esc_html(__("All positions", 'victorious'));?></option>        <?php foreach($player_positions as $player_position):?>            <option value="<?php echo esc_attr($player_position['id']);?>">                <?php echo esc_html($player_position['name']);?>            </option>        <?php endforeach;?>    </select></div><?php endif;?>