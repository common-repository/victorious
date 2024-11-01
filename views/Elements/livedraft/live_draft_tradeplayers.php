<?php VIC_GetMessage(); ?>
<style>
    #main.site{
        max-width: 1200px;
    }
</style>
<div class="rugby-trade-player">
    <?php if (empty($user_players) || empty($target_players)): ?>
        <h3> <?php echo esc_html(__('Data not found', 'victorious'));?></h3>
    <?php else:; ?>
        <p>
            -<?php echo sprintf(esc_html(__('Once you click send, %s will recieve an email notification regarding your trade request.', 'victorious')), $profile_target->display_name);?>
        </p>
        <?php if($is_requested):?>
            <p>
                -<?php echo esc_html(__('Note: You already requested to change players with this user. If you submit to change again, old request will be replaced.', 'victorious'));?>
            </p>
        <?php endif;?>
        <div class="clear"></div>
        <div class="left-column">
            <section class="f-roster-container" data-role="team">
                <header>
                    <div class="f-lineup-text-container">
                        <h1><?php echo esc_html(__('Your lineup', 'victorious'));?></h1>
                    </div>

                </header>
                <section class="f-roster">
                    <ul>
                        <?php foreach ($user_players as $player): ?>
                            <li class="f-roster-position f-count-0" data-org-key="<?php echo '1'.'_'.$player['id'].'_'.$player['position_id']; ?>">
                                <div class="f-player-image <?php echo!empty($player['full_image_path']) ? $player['full_image_path'] : "f-no-image" ?>">
                                    <?php if ($player['full_image_path']): ?>
                                        <img src="<?php echo 'http://victorious.club/vc-admin/images/' . $player['full_image_path']; ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="f-position">
                                  <?php echo esc_html($player['position_name']); ?>
                                </div>
                                <div class="f-player"  style="visibility: visible;"><?php echo esc_html($player['player_name']); ?></div>
                                <div class="f-fixture" style="visibility: visible;"><b><?php echo esc_html($player['teamname2']) ?></b>@<?php echo esc_html($player['teamname1']); ?></div>
                                <a class="f-button f-tiny f-text f-rugby-add" onclick="jQuery.tradeRugby.tradePlayer(1,this,<?php echo esc_attr($player['id'])?>,<?php echo esc_attr($player['position_id']); ?>)" style="visibility: visible;">
                                    <i class="fa fa-refresh"></i>
                                </a>
                                <a class="f-button f-tiny f-text f-rugby-remove" onclick="jQuery.tradeRugby.removeTradePosition(this,'<?php echo '1'.'_'.$player['id'].'_'.$player['position_id']; ?>')" style="visibility: visible;">
                                    <i class="fa fa-refresh"></i>
                                </a>
                            </li>
                        <?php endforeach; ?>

                    </ul>
                </section>
            </section>
        </div>
        <div class="middle-column" >
            <div class="rugby-message" style="display: hide;"></div>
            <div class="rugby-message-success" style="display: hide;"></div>
            <div class="user-position">
                <section class="f-roster-container" data-role="team">
                    <section class="f-roster">
                        <ul>
                        </ul>
                    </section>
                </section>
            </div>
            <div class="rugby-icon-transfer" >
                <i class="fa fa-refresh"></i>
            </div>
            <div class="target-position">
                <section class="f-roster-container" data-role="team">
                    <section class="f-roster">
                        <ul></ul>
<!--                        <ul>
                            <li class="f-roster-position f-count-0">
                                <div class="f-player-image f-no-image"></div>
                                <div class="f-position">
                                  Scrum Half 
                                </div>
                                <div class="f-player" style="visibility: visible;">Brendan McKibbin</div>
                                <div class="f-fixture" style="visibility: visible;"><b>Saracens</b>@London Irish</div>
                            </li>
                        </ul>-->
                    </section>
                </section>
            </div>
            <div class="send-trade-request">
                <input type="submit" data-nav-warning="off" id="btn-send" value="Send" class="f-button f-jumbo f-primary" onclick="jQuery.tradeRugby.sendTradeData()">
            </div>
        </div>
        <div class="right-column">
            <section class="f-roster-container" data-role="team">
                <header>
                    <div class="f-lineup-text-container">
                        <h1><?php echo sprintf(esc_html(__("%s 's line up", 'victorious')), $profile_target->display_name); ?></h1>
                    </div>

                </header>
                <section class="f-roster">
                    <ul>
                        <?php foreach ($target_players as $player): ?>
                            <li class="f-roster-position f-count-0" data-org-key="<?php echo '0'.'_'.$player['id'].'_'.$player['position_id']; ?>">
                                <div class="f-player-image <?php echo!empty($player['full_image_path']) ? $player['full_image_path'] : "f-no-image" ?>">
                                    <?php if ($player['full_image_path']): ?>
                                        <img src="<?php echo 'http://victorious.club/vc-admin/images/' . $player['full_image_path']; ?>">
                                    <?php endif; ?>
                                </div>
                                 <div class="f-position">
                                  <?php echo esc_html($player['position_name']); ?>
                                </div>
                                <div class="f-player"  style="visibility: visible;"><?php echo esc_html($player['player_name']); ?></div>
                                <div class="f-fixture" style="visibility: visible;"><b><?php echo esc_html($player['teamname2']) ?></b>@<?php echo esc_html($player['teamname1']); ?></div>
                                <a class="f-button f-tiny f-text f-rugby-add" onclick="jQuery.tradeRugby.tradePlayer(0,this,<?php echo esc_attr($player['id'])?>,<?php echo esc_attr($player['position_id']); ?>)" style="visibility: visible;">
                                    <i class="fa fa-refresh"></i>
                                </a>
                                   <a class="f-button f-tiny f-text f-rugby-remove" onclick="jQuery.tradeRugby.removeTradePosition(this,'<?php echo '0'.'_'.$player['id'].'_'.$player['position_id']; ?>')" style="visibility: visible;">
                                    <i class="fa fa-refresh"></i>
                                </a>
                            </li>
                        <?php endforeach; ?>

                    </ul>
                </section>
            </section>
        </div>
    <?php endif; ?>
</div>
<form id="frm-trade-players">
    <input type="hidden" name="target_id" value="<?php echo esc_attr($target_id); ?>">
    <input type="hidden" name="league_id" value="<?php echo esc_attr($contest_id); ?>">
    <input type="hidden" name="entry_number" value="<?php echo esc_attr($entry_number); ?>">
    <input type="hidden" name="target_entry" value="<?php echo esc_attr($target_entry); ?>">
    <input type="hidden" name="user_positions" value="" id="user_values">
    <input type="hidden" name="target_positions" value="" id="target_values">
    <input type="hidden" name="poolID" value="<?php echo esc_attr($pool['poolID']); ?>" >
    <input type="hidden" name="trade_amount" value="" id="trade_amount_values">
</form>
