<?php
$player_name_list = implode(', ', $player_names);
$message_subject = esc_html(__('Player Injury Notification','victorious'));
$message_body = '<p>'.sprintf(esc_html(__('Dear %s,','victorious')), $user->data->user_login).'</p>';
$message_body .= '<p>'.sprintf(esc_html(__('We have new intel on player: %s','victorious')), $player_name_list).'</p>';
$message_body .= '<p>'. sprintf(esc_html(__('%s %s injured and will NOT play in the contest <a href="%s">%s</a>','victorious')), $player_name_list, count($player_names) > 1 ? __('are','victorious') : __('is','victorious'), $contest_url, $league_name).'</p>';
$message_body .= '<p>'.esc_html(__('Thank you','victorious')).'</p>';
$message_body .= '<p>'.get_option('blogname').'</p>';