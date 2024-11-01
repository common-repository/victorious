<div id="rugby-manage-trade-player-page">
    <?php VIC_GetMessage();?>
    <?php if ($data['request_pairs'] != null): ?>
        <?php foreach($data['request_pairs'] as $request_pair): 
            $pairs = $request_pair['pairs'];
            $request = $request_pair['request'];
            $sender = self::$victorious->get_user_info($request['user_id']);
        ?>
        <div class="request-item">
            <div class="rugby-message-success"></div>
            <div class="rugby-message-error"></div>
            <div class="request-content">
                <?php echo sprintf(esc_html(__('%s would like trade','victorious')), $sender->display_name);?>:<br/>
                <?php 
                    foreach($pairs as $pair)
                    {
                        $change = $pair['change'];
                        $with = $pair['with'];
                        $amount = $pair['amount'];
                        echo sprintf(esc_html(__('<b>%s%s</b> with your player <b>%s</b>','victorious')), $change['name'], (float)$amount > 0 ? " +".$amount : "", $with['name'])."<br/>";
                    }
                ?>
            </div>

            <div class="request-handle">
                <button  onclick="jQuery.tradeRugby.approveTradeRequest(this,<?php echo esc_attr($request['id']) ?>)" type="button" class="btn-approve-trade f-button f-primary f-right" ><?php echo esc_html(__('Approve','victorious')) ?></button>
                <button  onclick="jQuery.tradeRugby.rejectTradeRequet(this,<?php echo esc_attr($request['id']) ?>)" type="button" class=" btn-reject-trade f-button f-primary f-right" ><?php echo esc_html(__('Reject','victorious')) ?></button>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <h3><?php echo esc_html(__('No request','victorious')) ?></h3>
    <?php endif; ?>

</div>