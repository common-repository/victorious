<div class="stats-table ng-scope" id="team-injuries">
    <?php if($players != null):?>
    <div class="nfl-team-injuries">
        <table cellpadding="0" cellspacing="0" border="0">
            <tbody>
                <tr class="headers">
                    <th class="first" colspan="2"><?php echo esc_html(__("Player", "victorious"));?></th>
                    <th><?php echo esc_html(__("Position", "victorious"));?></th>
                    <th><?php echo esc_html(__("Status", "victorious"));?></th>
                </tr>
                <?php foreach($players as $player):
                    $position = $player['position'];
                ?>
                <tr class="detail-cells ng-scope">
                    <td class="profile-img">
                        <a href="/nhl/player-bio/johan-franzen">
                            <img src="<?php echo VIC_parsePlayerImage($player['image_url']);?>">
                        </a>
                    </td>
                    <td class="first">
                        <span>
                            <strong>
                                <?php echo esc_html($player['name']);?>
                            </strong>
                        </span>
                    </td>
                    <td class="ng-binding">
                        <span class="ng-binding ng-isolate-scope"><?php echo esc_html($position['name']);?></span>
                    </td>
                    <td class="last ng-binding">
                        <?php $status = VIC_InjuryStatus($player['indicator_id']); echo esc_html($status['name']);?>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <?php else:?>
    <div class="no-injuries">
        <p><?php echo esc_html(__("There are currently no injuries in this team to report.", "victorious"));?></p>
    </div>
    <?php endif;?>
</div>