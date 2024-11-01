<?php
$message_body = sprintf(esc_html(__('Sorry! Contest %s didn\'t have enough players to start and has been canceled. You can join other contests here', 'victorious')), $data['name']).'<br>';
$message_body.= "<a href='".$href_change."'>". $href_change."</a><br>";
$message_body.= sprintf(esc_html(__('Thanks','victorious')));