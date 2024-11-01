<?php
$message_body = sprintf(esc_html(__('Good news! The Live Draft for contest: "%s" is now open. Please log in to participate in the draft. You can now begin to draft players. If you don\'t draft any players, the system will automatically select players for you. Please click the link below to draft players', 'victorious')), $data['name']).'<br>';
$message_body.= "<a href='".$href_change."'>". $href_change."</a><br>";
$message_body.= sprintf(esc_html(__('Thank you and good luck!','victorious')));