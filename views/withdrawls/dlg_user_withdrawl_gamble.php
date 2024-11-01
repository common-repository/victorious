<div id="msgUserWithdrawls" class="public_message"></div>
<form id="formUserWithdrawls" style="width: 500px;">
    <table>
        <tr>
            <td style="width: 170px"><?php echo esc_html(__("Name", 'victorious'));?></td>
            <td class="full_name">
                <?php echo esc_html($user['user_login']);?>
            </td>
        </tr>
        <tr>
            <td><?php echo esc_html(__("Amount", 'victorious'));?></td>
            <td class="amount">
                <?php echo esc_html($withdraw['amount']);?>
            </td>
        </tr>
        <tr>
            <td><?php echo esc_html(__("Real Amount", 'victorious'));?></td>
            <td class="real_amount">
                <?php echo esc_html($withdraw['real_amount']);?>
            </td>
        </tr>
        <tr>
            <td><?php echo esc_html(__("Gateway", 'victorious'));?></td>
            <td class="gateway">
                <?php echo esc_html($withdraw['gateway']);?>
            </td>
        </tr>
        <tr>
            <td><?php echo esc_html(__("Request Date", 'victorious'));?></td>
            <td class="request_date">
                <?php echo date('M-d-Y', strtotime($withdraw['requestDate']));?>
            </td>
        </tr>
        <tr>
            <td><?php echo esc_html(__("Reason", 'victorious'));?></td>
            <td class="reason">
                <?php echo esc_html($withdraw['reason']);?>
            </td>
        </tr>
    </table>
    <hr>
    <table>
        <tr>
            <td>
                <?php echo esc_html(__("Action", 'victorious'));?>
                <input type="hidden" name="withdrawlID" class="withdrawlID" value="<?php echo esc_html($withdraw['withdrawlID']);?>" />
            </td>
        </tr>
        <tr>
            <td>
                <?php if($withdraw['status'] == 'NEW' || $withdraw['status'] == 'SENT_EMAIL'):?>
                    <select name="status" class="status">
                        <option value=""><?php echo esc_html(__("Select status", 'victorious'));?></option>
                        <option value="SENT_EMAIL"><?php echo esc_html(__("Send payout email", 'victorious'));?></option>
                        <option value="DECLINED"><?php echo esc_html(__("Decline", 'victorious'));?></option>
                    </select>
                <?php else:?>
                <b><?php echo VIC_WithdrawalStatus($withdraw['status']);?></b>
                <?php endif;?>
            </td>
        </tr>
        <tr>
            <td><?php echo esc_html(__("Response Message", 'victorious'));?></td>
        </tr>
        <tr>
            <td>
                <?php if($withdraw['status'] == 'NEW' || $withdraw['status'] == 'SENT_EMAIL'):?>
                <textarea style="width: 100%;height:100px;resize: none;" name="response_message" class="response_message"></textarea>
                    <p>
                        <?php echo esc_html(__("If you select to approve, an email will be sent to client. It contains a link so that user can click on it to payout.", 'victorious'));?>
                    </p> 
                <?php else:?>
                    <?php echo esc_html($withdraw['response_message']);?>
                <?php endif;?>
            </td>
        </tr>
    </table>
</form>