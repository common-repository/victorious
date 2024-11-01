<?php
$message_body = "--$multipartSep\r\n"
. "Content-Type: text/plain; charset=ISO-8859-1; format=flowed\r\n"
. "Content-Transfer-Encoding: 7bit\r\n"
. "\r\n"
. "--$multipartSep\r\n"
. "Content-Type: text/csv\r\n"
. "Content-Transfer-Encoding: base64\r\n"
. "Content-Disposition: attachment; filename=\"".$filename."\"\r\n"
. "\r\n"
. "$attachment\r\n"
. "--$multipartSep--";
?>