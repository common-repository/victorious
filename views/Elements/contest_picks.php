<input type="hidden" value='<?php echo json_encode($league);?>' id="leagueInfo" />
<?php if(!empty($users)):?>
    <input type="hidden" value='<?php echo json_encode($users);?>' id="pickData" />
    <label><?php echo esc_html(__("Game type", 'victorious'));?></label>:<?php echo esc_html($league['gameType']);?>
    <br/>
    <label><?php echo esc_html(__("User", 'victorious'));?></label>
    <select id="cbUsers" onchange="jQuery.admin.showPicksDetail();">
        <?php foreach($users as $user):?> 
            <option value="<?php echo esc_html($user['userID']);?>"><?php echo esc_html($user['user_login']);?></option>
        <?php endforeach;?>
    </select>
    <?php if($league['multi_entry']):?>
        <br/>
        <label><?php echo esc_html(__("Entry number", 'victorious'));?></label>
        <?php foreach($users as $user):?> 
        <select id="cbEntry<?php echo esc_attr($user['userID']);?>" class="cbEntry" style="display: none"  onchange="jQuery.admin.showPicksDetail();">
            <?php foreach($user['entries'] as $entry):?> 
                <option value="<?php echo esc_html($entry['entry_number']);?>"><?php echo esc_html($entry['entry_number']);?></option>
            <?php endforeach;?>
        </select>
        <?php endforeach;?>
    <?php endif;?>
    <table id="tbPickDetail" class="wp-list-table widefat books">
        <thead>
            <tr>
                <th style="width: 40px"><?php echo esc_html(__("ID", 'victorious'));?></th>
                <?php if($league['gameType'] == 'PLAYERDRAFT' && $league['is_team'] == 1):?>
                    <th style="width: 200px"><?php echo esc_html(__("Team", 'victorious'));?></th>
                <?php elseif($league['gameType'] != 'PLAYERDRAFT'):?> 
                    <th style="width: 200px"><?php echo esc_html(__("Fixture", 'victorious'));?></th>
                <?php endif;?>
                <th><?php echo esc_html(__("Pick Name", 'victorious'));?></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
<?php else:?> 
    <center><?php echo esc_html(__('No picks', 'victorious'));?></center>
<?php endif;?>