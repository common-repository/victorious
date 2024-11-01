<?php
$message_subject = sprintf(esc_html(__('%s league %s has completed','victorious')),$siteTitle,str_replace('&#39;', "'", $emailInfo['league_name']));
$message_body = sprintf( esc_html(__('Congratulations %s !','victorious')).'
<br>
<br>
'.esc_html(__('You won $%s in league %s for coming in  %s place','victorious')).'<br><br><br>'.

        esc_html(__('Daily %s Leagues<br>','victorious')).'<br>'.
            esc_html(__('Get back in another game here %s','victorious')).'<br><br>'.

                esc_html(__('Good luck and thanks for playing!','victorious')),$emailInfo['username'],$emailInfo['money'],$emailInfo['league_name'],$emailInfo['place'],$siteTitle,'<a href="'.$website.'">'.$website.'</a>' );
?>