<?php VIC_GetMessage();?>
<article class="hentry">
    <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/feature_list.php");?>
    <div>
        <div class="vc-header mb-4 bg-blue">
            <div class="vc-filter-wrap">
                <select id="vc-filter-sport" class="vc-filter-item">
                    <option value=""><?php echo esc_html(__('All sports', 'victorious'));?></option>
                    <?php if(!empty($aSports)):?>
                        <?php foreach($aSports as $aSport):?>
                            <?php if(!empty($aSport['child'])):?>
                                <?php foreach($aSport['child'] as $sport):?>
                                    <option value="<?php echo esc_html($sport['id']);?>"><?php echo esc_html($sport['name']);?></option>
                                <?php endforeach;?>
                            <?php endif;?>
                        <?php endforeach;?>
                    <?php endif;?>
                </select>
                <select id="vc-filter-contest-type" class="vc-filter-item">
                    <option value="all"><?php echo esc_html(__('All contest types', 'victorious'));?></option>
                    <option value="headtohead"><?php echo esc_html(__('Head-to-head', 'victorious'))?></option>
                    <option value="league"><?php echo esc_html(__('Leagues', 'victorious'))?></option>
                </select>
                <select id="vc-filter-size" class="vc-filter-item">
                    <option value="all"><?php echo esc_html(__('Entry', 'victorious'));?></option>
                    <option value="3">3</option>
                    <option value="4-10">4-10</option>
                    <option value="11+">11+</option>
                </select>
                <select id="vc-filter-start-time" class="vc-filter-item">
                    <option value="all"><?php echo esc_html(__('All start times', 'victorious'))?></option>
                    <option value="next"><?php echo esc_html(__('Next available', 'victorious'));?></option>
                    <option value="today"><?php echo esc_html(__('Today', 'victorious'))?></option>
                </select>
                <select id="vc-filter-creator" class="vc-filter-item" style="display: none">
                    <option value="all"><?php echo esc_html(__('All creators', 'victorious')) ?></option>
                    <option value="admin"><?php echo esc_html(__('Admin created', 'victorious')); ?></option>
                    <option value="user"><?php echo esc_html(__('User created', 'victorious')) ?></option>
                </select>
            </div>
            <div class="vc-header-right">
                <a href="<?php echo VICTORIOUS_URL_CREATE_CONTEST;?>" class="vc-button btn-green btn-size-sm btn-radius5">
                    <span class="btn-icon"><img src="<?php echo VICTORIOUS__PLUGIN_URL_CSS.'images/plus_icon.png';?>" alt=""></span>
                    <?php echo esc_html(__('Create Contest', 'victorious'))?>
                </a>
            </div>
        </div>

        <div class="vc-table">
            <table cellspacing="0" cellpadding="0" class="text-md-center" id="vc-table-lobby">
                <thead>
                    <tr>
                        <th data-sort_field="f-title">
                            <span id="contestCountdown">
                                <span><?php echo esc_html(__('Next contest starts in:', 'victorious'));?></span>
                                <span id="lobbyCountdown"></span>
                            </span>
                        </th>
                        <th class="text-left" data-sort_field="f-gametype" style="width: 11%"><?php echo esc_html(__('Type', 'victorious'));?></th>
                        <th class="text-left" data-sort_field="f-entries" style="width: 12%"><?php echo esc_html(__('Entries', 'victorious'));?></th>
                        <?php if(get_option('victorious_no_cash') == 0):?>
                        <th data-sort_field="f-prizes" style="width: 10%"><?php echo esc_html(__('Prizes', 'victorious'));?></th>
                        <?php endif;?>
                        <th data-sort_field="f-starttime" style="width: 18%"><?php echo esc_html(sprintf(__('Starts (%s)','victorious'),$time_zone_abbr)); ?></th>
                        <th data-sort_field="f-entryfee" style="width: 15%"><?php echo esc_html(__('Entry', 'victorious'));?></th>
                    </tr>
                </thead>
                <tbody  id="lobbyData"></tbody>
            </table>
        </div>
    </div>
</article>
<?php require_once('dlg_info.php');?>
<?php require_once('dlg_rugby_confirm_join.php');?>
<?php require_once('dlg_contest_password.php');?>

    <script>
        jQuery(document).ready(function(){
            jQuery.lobby.initLobby();
        })
    </script>

<?php if(!empty($goliathDecision)):?>
    <?php require_once('dlg_survivor_decision.php');?>
    <script>
        jQuery(document).ready(function(){
            jQuery.lobby.suvivorDecisionDlg();
        })
    </script>
<?php endif;?>