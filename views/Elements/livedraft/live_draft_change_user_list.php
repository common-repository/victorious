<?php if($users != null):?>
    <ul class="list-users">
        <?php foreach($users as $user):?>
            <li>
                <div class="player_image">
                    <img src="<?php echo esc_url($user['avatar']);?>">
                </div>
                <div>
                    <?php echo esc_html($user['username']);?><br/>
                    <?php echo esc_html(__('Pick list', 'victorious'));?>: 
                        <?php 
                            if($user['players'] != null){
                                $player_name = array();
                                foreach($user['players'] as $player){
                                    $player_name[] = esc_html($player['name']);
                                }
                                echo implode(',', $player_name);
                            }
                        ?>
                    <br/>
                    <input class="btn btn-success btn-xs" value="<?php echo esc_html(__('Request Trade', 'victorious'));?>" onclick="window.location = '<?php echo VICTORIOUS_URL_GAME.$league_id.'?trade_target_id='.$user['userID'].'&target_entry='.$user['entry_number'].'&entry_number='.$entry_number.'&contest_id='.$league_id;?>'" type="button">
                </div>
                <div class="clear"></div>
            </li>
        <?php endforeach;?>
    </ul>
    <?php if(count($users) == 5):?>
    <input id="btn_loadmore" class="btn btn-success btn-xs" value="<?php echo esc_html(__('Load more', 'victorious'));?>" onclick="jQuery.tradeRugby.showListUsers(<?php echo esc_attr($league_id); ?>,<?php echo esc_attr($entry_number); ?>, <?php echo esc_attr($page); ?>)" type="button">
    <?php endif;?>
<?php else:?>
    <h3 style="text-align:center">
        <?php echo esc_html(__('There are no more users', 'victorious'));?>
    </h3>
<?php endif; ?>
