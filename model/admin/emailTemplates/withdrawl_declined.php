<?php
$message =sprintf(
'<p>'.esc_html(__('Sorry! Your withdrawal request has been declined. Your funds have been added back to your account.','victorious')).'</p>'.

'<p>'.esc_html(__('Request Date: %s','victorious')).'</p>'.
'<p>'.esc_html(__('Response Date: %s','victorious')).'</p>'.
'<p>'.esc_html(__('Amount: %s','victorious')).'</p>',date('M d Y' , strtotime($withdrawl['requestDate'])),date('M d Y' , strtotime($withdrawl['processedDate'])),$withdrawl['real_amount']);
if(!empty($withdrawl['response_message']))
{
    $message .=sprintf(
'<p>'.esc_html(__('Message: %s','victorious')).'</p>',$withdrawl['response_message']);
}
$message .= sprintf('
<p>'.esc_html(__('Please go to %s to check.','victorious')).'</p>','<a href="'.VICTORIOUS_URL_TRANSACTIONS.'" target="_blank">'.__('this link','victorious').'</a>');
?>