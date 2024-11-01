<div class="contentPlugin">
    <?php if(!empty($futureEvents)):?>
        <form action="<?php echo VICTORIOUS_URL_FUTURE_EVENT;?>" method="GET">
            <div>
                <select name="org" style="width:200px;">
                    <option value="">
                        <?php echo esc_html(__('All sports', 'victorious'));?>
                    </option>
                    <?php foreach($organizations as $org_id => $organization):?>
                        <option value="<?php echo esc_attr($org_id)?>" <?php if(isset($_GET['org']) && $_GET['org'] ==$org_id):?>selected="true"<?php endif;?>>
                            <?php echo esc_html($organization)?>
                        </option>
                    <?php endforeach;?>
                </select>
                <button class="f-create-contest f-button f-primary f-right">
                    <?php echo esc_html(__('Search', 'victorious'));?>
                </button>
                <div class="clear"></div>
            </div>
            <br/>
            <div id="leagues_future_events">

                <div class="tableLiveEntries table6">
                    <div class="tableTitle">
                        <div style="width: 6%"><?php echo esc_html(__('ID', 'victorious'));?></div>
                        <div style="width: 33%"><?php echo esc_html(__('Name', 'victorious'));?></div>
                        <div style="width: 20%"><?php echo esc_html(__('Sport', 'victorious'));?></div>
                        <div style="width: 15%"><?php echo esc_html(__('Start Date', 'victorious'));?></div>
                        <div style="width: <?php echo get_option('victorious_create_contest') ? '6%' : '18%';?>;text-align:center;"><?php echo esc_html(__('Fixture', 'victorious'));?></div>
                        <div style="width: 8%">&nbsp;</div>
                        <?php if(get_option('victorious_create_contest')):?>
                        <div style="width: 12%">&nbsp;</div>
                        <?php endif;?>
                    </div>
                </div>
                <div class="tableLiveEntries tableLiveEntriesContent  table6">
                    <?php foreach($futureEvents as $item):?>
                    <div >
                        <div style="width: 6%"><span><?php echo esc_html(__('ID', 'victorious'));?></span><?php echo esc_html($item['poolID'])?></div>
                        <div style="width: 33%"><span><?php echo esc_html(__('Name', 'victorious'));?></span><?php echo esc_html($item['poolName'])?></div>
                        <div style="width: 20%"><span><?php echo esc_html(__('Sport', 'victorious'));?></span><?php echo esc_html($item['organization'])?></div>
                        <div style="width: 15%"><span><?php echo esc_html(__('Start Date', 'victorious'));?></span><?php echo VIC_DateTranslate($item['startDate'])?></div>
                        <div style="width: <?php echo get_option('victorious_create_contest') ? '6%' : '18%';?>;text-align:center;">
                            <?php if($item['only_playerdraft'] == 0):?>
                            <a href="javascript:void(0)" onclick="return viewPoolFixture(<?php echo esc_attr($item['poolID']);?>, '<?php echo esc_html(__("fixtures", 'victorious'));?>')">
                                <?php echo esc_html(__("View", 'victorious'));?>
                            </a>
                            <?php endif;?>
                        </div>
                        <div style="width: 8%">
                            <?php if(isset($item['total_contest']) && $item['total_contest'] > 0):?>
                            <a class="fanlucci-button" href="<?php echo VICTORIOUS_URL_LOBBY."?event_id=".$item['poolID'];?>">
                                <?php echo esc_html(__('Enter', 'victorious'));?>
                            </a>
                            <?php endif;?>
                        </div>
                        <?php if(get_option('victorious_create_contest')):?>
                        <div style="width: 12%">
                            <a class="fanlucci-button" href="<?php echo VICTORIOUS_URL_CREATE_CONTEST."?event_id=".$item['poolID'];?>">
                                <?php echo esc_html(__('Create contest', 'victorious'));?>
                            </a>
                        </div>
                        <?php endif;?>
                    </div>
                    <?php endforeach;?>
                </div>
            </div>
        </form>
        <div id="dlgFixture" style="display: none"></div>
    <?php else:?>
        <?php echo esc_html(__("There are no future events", 'victorious'));?>
    <?php endif;?>
</div>
