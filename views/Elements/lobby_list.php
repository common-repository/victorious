<?php if(!empty($leagues)):?>
    <?php foreach($leagues as $league):
        $balance_type = $league['balance_type'];
    ?>
        <tr class="vc-lobby-item" id="lobby_<?php echo esc_html($league['leagueID']);?>" data-start_timestamp="<?php echo esc_attr($league['startTimeStamp']);?>" data-name="<?php echo esc_html($league['name']);?>" data-organization="<?php echo esc_attr($league['organization']);?>" data-size="<?php echo esc_attr($league['size']);?>" data-entry_fee="<?php echo esc_attr($league['entry_fee']);?>" data-today="<?php echo esc_attr($league['today']);?>" data-entry_fee="<?php echo esc_attr($league['entry_fee']);?>" data-creator_is_admin="<?php echo esc_attr($league['creator_is_admin']);?>">
            <td class="text-left f-title" data-label="<?php echo esc_html(__("CONTEST", "victorious"));?>" data-sort="<?php echo esc_html($league['name']);?>">
                <div class="d-flex align-items-center justify-content-end justify-content-md-start">
                    <?php if(!empty($league['icon'])):?>
                        <img src="<?php echo esc_html($league['icon']);?>" alt="" width="40px" height="40px" class="me-2">
                    <?php endif;?>
                    <div>
                        <a href="javascript:void(0)" class="vc-name-link" onclick="return jQuery.global.ruleScoring('<?php echo esc_html($league['leagueID']);?>', 1)"><?php echo esc_html($league['name']);?></a>
                        <?php if($league['is_guaranteed']):?>
                            <div class="text-sm-gray"><?php echo esc_html(__("Guaranteed", "victorious"));?></div>
                        <?php endif;?>
                    </div>
                </div>
            </td>
            <td class="text-md-left f-gametype" data-label="<?php echo esc_html(__("TYPE", "victorious"));?>" data-sort="<?php echo ($league['is_live_draft'] && strtolower($league['gameType']) == 'playerdraft' && $league['live_draft_minute_change_player'] > 0) ? esc_html(__('LIVE DRAFT', 'victorious')) : $league['gameType'];?>">
                <?php if($league['gameType'] == 'GOLFSKIN'):?>
                    <?php echo esc_html(__("Skin", "victorious"));?>
                <?php else:?>
                    <?php echo VIC_ParseGameTypeName($league['gameType']);?>
                <?php endif;?>
            </td>
            <td class="text-md-left f-entries" data-label="<?php echo esc_html(__("ENTRIES", "victorious"));?>" data-sort="<?php echo esc_html($league['entries']);?>">
                <a href="javascript:void(0)" onclick="return jQuery.global.ruleScoring('<?php echo esc_html($league['leagueID']);?>', 2)">
                    <?php echo esc_html($league['entries']);?>
                </a>
                <?php echo esc_html(__("of", "victorious"));?>
                <?php echo esc_html($league['size']);?>
                <?php if($league['only_rookies']):?>
                    <div class="indicator">
                        <div title="<?php echo esc_html(__("Only rookies", "victorious"));?>" class="multi-entry">R</div>
                    </div>
                <?php endif;?>
                <div class="vc-progress">
                    <div class="vc-progress-bar" style="width: <?php echo ($league['entries'] / $league['size'] * 100).'%';?>"></div>
                </div>
            </td>
            <?php if(!$no_cash):?>
                <td class="text-md-center f-prizes" data-label="Prizes" data-sort="<?php echo esc_html($league['entry_fee']);?>">
                    <a href="javascript:void(0)" onclick="return jQuery.global.ruleScoring('<?php echo esc_html($league['leagueID']);?>', 3)">
                        <?php
                        if($league['entry_fee'] == 0 && $league['is_guaranteed'])
                        {
                            echo VIC_FormatMoney($league['guaranteed_prize'], $balance_type['currency_code_symbol'], $balance_type['currency_position']);
                        }
                        else
                        {
                            echo VIC_FormatMoney($league['prizes'], $balance_type['currency_code_symbol'], $balance_type['currency_position']);
                        }
                        ?>
                    </a>
                    <br>
                    <?php if($league['multi_payout']):?>
                        <div class="text-sm-gray"><?php echo esc_html(__("Multi payout", "victorious"));?></div>
                    <?php endif;?>
                </td>
            <?php endif;?>
            <td class="text-md-center f-starttime" data-label="<?php echo sprintf(esc_html(__('Starts (%s)','victorious'),$time_zone_abbr)); ?>" data-sort="<?php echo esc_attr($league['startTimeStamp']);?>"><?php echo VIC_DateTranslate($league['startDate']);?></td>
            <td class="text-md-center f-entryfee" data-label="<?php echo esc_html(__("ENTRY", "victorious"));?>" data-sort="<?php echo esc_html($league['entry_fee']);?>">
                <?php if($league['allow_enter']):?>
                    <?php if($league['gameType'] == VICTORIOUS_GAME_TYPE_UPLOADPHOTO && !empty($league['is_joined'])):?>
                        <a href="javascript:void(0)" class="vc-button btn-transparent btn-size-sm btn-radius5 flex-column f-disabled" disabled="disabled">
                            <b><?php echo esc_html($league['entry_fee'] == 0 ? __("Free", "victorious") : VIC_FormatMoney($league['entry_fee'], $balance_type['currency_code_symbol'], $balance_type['currency_position']));?></b>
                            <div class="d-flex align-items-center" style="white-space: nowrap;"><?php echo esc_html(__("Joined", "victorious"));?><span class="material-icons"> keyboard_arrow_right </span></div>
                        </a>
                    <?php elseif($league['gameType'] == VICTORIOUS_GAME_TYPE_NFL_PLAYOFF):?>
                        <?php if(isset($league['is_joined']) && $league['is_joined'] == 1):?>
                            <a href="javascript:void(0)" class="vc-button btn-transparent btn-size-sm btn-radius5 flex-column f-disabled" disabled="disabled">
                                <b><?php echo esc_html($league['entry_fee'] == 0 ? __("Free", "victorious") : VIC_FormatMoney($league['entry_fee'], $balance_type['currency_code_symbol'], $balance_type['currency_position']));?></b>
                                <div class="d-flex align-items-center" style="white-space: nowrap;"><?php echo esc_html(__("Joined", "victorious"));?><span class="material-icons"> keyboard_arrow_right </span></div>
                            </a>
                        <?php else:?>
                            <a href="javascript:void(0)" class="vc-button btn-transparent btn-size-sm btn-radius5 flex-column playoff_confirm_join" data-id="<?php echo $league['leagueID'];?>" id="btnPlayoffJoinContest<?php echo $league['leagueID'];?>">
                                <b><?php echo esc_html($league['entry_fee'] == 0 ? __("Free", "victorious") : VIC_FormatMoney($league['entry_fee'], $balance_type['currency_code_symbol'], $balance_type['currency_position']));?></b>
                                <div class="d-flex align-items-center" style="white-space: nowrap;">
                                    <?php echo esc_html(__("Join", "victorious"));?>
                                </div>
                            </a>
                        <?php endif;?>
                    <?php elseif($league['is_live_draft'] == 1 && isset($league['is_joined']) && $league['is_joined'] == 1):?>
                        <a href="javascript:void(0)" class="vc-button btn-transparent btn-size-sm btn-radius5 flex-column f-disabled" disabled="disabled">
                            <b><?php echo esc_html($league['entry_fee'] == 0 ? __("Free", "victorious") : VIC_FormatMoney($league['entry_fee'], $balance_type['currency_code_symbol'], $balance_type['currency_position']));?></b>
                            <div class="d-flex align-items-center" style="white-space: nowrap;"><?php echo esc_html(__("Joined", "victorious"));?><span class="material-icons"> keyboard_arrow_right </span></div>
                        </a>
                    <?php elseif($league['full'] == 0 || ($league['enter'] && $league['multi_entry'] == 0)):?>
                        <a href="<?php echo (($league['password'] != null && !$league['enter']) || $league['gameType'] == VICTORIOUS_GAME_TYPE_UPLOADPHOTO) ? "javascript:void(0)" : VICTORIOUS_URL_GAME.$league['leagueID'].($league['multi_entry'] == 1 && isset($league['next_entry']) ? "?num=".$league['next_entry'] : "");?>" class="vc-button btn-transparent btn-size-sm btn-radius5 flex-column <?php echo esc_attr($league['gameType'] == VICTORIOUS_GAME_TYPE_UPLOADPHOTO ? 'btn-join' : '');?> <?php if($league['is_live_draft'] == 1):?>live_draft_confirm_join<?php endif;?> <?php if($league['password'] != null && !$league['enter']):?>has_password<?php endif;?>" <?php if($league['is_live_draft'] == 1):?>data-draft_time="<?php echo esc_attr($league['live_draft_start']);?>"<?php endif;?> data-id="<?php echo esc_attr($league['leagueID']);?>" data-entry-number="<?php echo esc_attr($league['next_entry']);?>">
                            <b><?php echo esc_html($league['entry_fee'] == 0 ? __("Free", "victorious") : VIC_FormatMoney($league['entry_fee'], $balance_type['currency_code_symbol'], $balance_type['currency_position']));?></b>
                            <div class="d-flex align-items-center" style="white-space: nowrap;">
                                <?php
                                if($league['enter'] && $league['multi_entry'] == 0)
                                {
                                    echo esc_html(__("Edit", "victorious"));
                                }
                                else if($league['is_live_draft'] == 1)
                                {
                                    echo esc_html(__("Join", "victorious"));
                                }
                                else
                                {
                                    echo esc_html($league['multi_entry'] ? __("Multi entry", "victorious") : __("Enter", "victorious"));;
                                }
                                ?>
                                <span class="material-icons"> keyboard_arrow_right </span>
                            </div>
                        </a>
                    <?php else:?>
                        <a href="javascript:void(0)" class="vc-button btn-transparent btn-size-sm btn-radius5 flex-column">
                            <b><?php echo esc_html($league['entry_fee'] == 0 ? __("Free", "victorious") : VIC_FormatMoney($league['entry_fee'], $balance_type['currency_code_symbol'], $balance_type['currency_position']));?></b>
                            <div class="d-flex align-items-center" style="white-space: nowrap;"><?php echo esc_html(__("Full", "victorious"));?><span class="material-icons"> keyboard_arrow_right </span></div>
                        </a>
                    <?php endif;?>
                <?php endif;?>
            </td>
        </tr>
    <?php endforeach;?>
<?php endif;?>