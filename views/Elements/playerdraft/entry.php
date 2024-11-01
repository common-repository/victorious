<article class="hentry">    <div class="vc-section">        <div class="p-4">            <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."result_header.php");?>        </div>        <div class="p-4 bg-gray">            <div class="vc-row">                <div class="col-md-6">                    <div class="vc-table bg-white mb-2">                        <table>                            <thead>                                <tr>                                    <th <?php if($league["is_live_draft"]):?>colspan="2"<?php endif;?>><?php echo esc_html(__('Name', 'victorious'));?></th>                                    <?php if($aPool['is_team']):?>                                        <th><?php echo esc_html(__('Team', 'victorious'));?></th>                                        <th><?php echo esc_html(__('Game', 'victorious'));?></th>                                    <?php endif;?>                                    <?php if(!$league["is_live_draft"]):?>                                    <th><?php echo esc_html(__('Salary', 'victorious'));?></th>                                    <?php endif;?>                                </tr>                            </thead>                            <tbody>                                <?php foreach ($players as $player): ?>                                    <tr>                                        <td data-label="<?php echo esc_html(__('Name', 'victorious'));?>" <?php if($league["is_live_draft"]):?>colspan="2"<?php endif;?>>                                            <div class="vc-player-wrap">                                                <?php if(!empty($player['image_url'])):?>                                                <span class="vc-player-avatar f-player-image <?php if(empty($player['image_url'])):?>f-no-image<?php endif;?>">                                                    <img src="<?php echo esc_url($player['image_url']);?>">                                                </span>                                                <?php endif;?>                                                <div class="vc-player-info">                                                    <div class="vc-player-name color-blue"><?php echo esc_html($player['name']);?></div>                                                </div>                                            </div>                                        </td>                                        <?php if($aPool['is_team']):?>                                            <td data-label="<?php echo esc_html(__('Team', 'victorious'));?>"><?php echo esc_html($player['myteam']); ?></td>                                            <td data-label="<?php echo esc_html(__('Game', 'victorious'));?>">                                                <?php if (empty($aPool['only_playerdraft']) || !$aPool['only_playerdraft']): ?>                                                    <?php if ($player['teamID2'] == $player['team_id']): ?>                                                        <strong><?php echo esc_html(__("A:", "victorious"))." ".esc_html($player['team2']); ?></strong> @ <?php echo esc_html(__("H:", "victorious"))." ".esc_html($player['team1']); ?>                                                    <?php else: ?>                                                        <?php echo esc_html(__("A:", "victorious"))." ".esc_html($player['team2']); ?> @ <strong><?php echo esc_html(__("H:", "victorious"))." ".esc_html($player['team1']); ?></strong>                                                    <?php endif; ?>                                                <?php endif; ?>                                            </td>                                        <?php endif;?>                                        <?php if(!$league["is_live_draft"]):?>                                        <td data-label="<?php echo esc_html(__('Salary', 'victorious'));?>">                                            <?php echo VIC_FormatMoney($player['salary']); ?>                                        </td>                                        <?php endif; ?>                                    </tr>                                <?php endforeach; ?>                            </tbody>                        </table>                    </div>                </div>                <div class="col-md-6">                    <a class="vc-button btn-green btn-size-lg btn-radius5 btn-w100" href="<?php echo VICTORIOUS_URL_GAME . $league['leagueID'] . "/?num=" . $entry_number; ?>"><?php echo esc_html(__('Edit', 'victorious'));?></a>                    <h3 class="vc-title mt-3 text-center"><?php echo esc_html(__('What next?', 'victorious'));?></h3>                    <div id="invitePane">                        <input type="button" value="<?php echo esc_html(__('Challenge friends', 'victorious'));?>" class="vc-button btn-green btn-size-lg btn-radius5 btn-w100" onclick="return jQuery.global.ruleScoring('<?php echo esc_attr($league['leagueID']);?>', 4);">                    </div>                    <a class="vc-button btn-white btn-size-lg btn-radius5 color-blue btn-w100 mt-3" href="<?php echo VICTORIOUS_URL_LOBBY; ?>"><?php echo esc_html(__('Enter other contests', 'victorious'));?></a>                </div>            </div>        </div>    </div></article><?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW.'dlg_info.php');?><?php //require_once(VICTORIOUS__PLUGIN_DIR_VIEW.'dlg_info_friends.php'));?><script type="text/javascript">    var showInviteFriends = '<?php echo esc_attr($showInviteFriends); ?>';    if (showInviteFriends)    {        jQuery.global.ruleScoring('<?php echo esc_attr($league['leagueID']);?>', 4);<?php if (get_option('victorious_get_email_from_better_join_contest')): ?>            jQuery.playerdraft.sendUserJoincontestEmail('<?php echo esc_attr($league['leagueID']); ?>', '<?php echo esc_attr($entry_number); ?>');<?php endif; ?>    }<?php if ($allow_pick_email): ?>        jQuery(window).load(function () {            jQuery.playerdraft.sendUserPickEmail('<?php echo esc_attr($league['leagueID']); ?>');        });<?php endif; ?></script>