<?php
if($change_list != null)
{
    $message_body = sprintf(esc_html(__('Your valid requested player(s) of contest "%s" has been changed for week '.$league['week'], 'victorious')), str_replace('&#39;', "'", $league['name'])).":<br/>";
    $message_body .= implode(", ", $change_list)."<br/>";
}
else
{
    $message_body = sprintf(esc_html(__('You didn\'t request to change any player for contest "%s" or your request was rejected. Your lineup from previous week will be kept for week '.$league['week'], 'victorious')), $league['name'])."<br/>";
}
$message_body .= sprintf(esc_html(__('Thank you and good luck!','victorious')));