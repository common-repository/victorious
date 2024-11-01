<?php
$message_body = sprintf(esc_html(__('Good news! The Live Draft for contest: "%s" will start in about %s minutes. Please log in to participate in the draft. You can choose to draft players or if you don\'t draft any players, the system will automatically select players for you. Please click the link below to enter draft room', 'victorious')), $contest_name, $time_prior_draft).'<br>';
$message_body.= "<a href='".$href_change."'>". $href_change."</a><br>";
$message_body.= sprintf(esc_html(__('Thank you and good luck!','victorious')));