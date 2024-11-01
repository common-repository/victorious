<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 03/04/2017
 * Time: 4:57 CH
 */
$error_code_message = [
    '505' => esc_html(__('Your license key is empty or invalid. Please check the key in Settings / Victorious', 'victorious')),
    '507' => esc_html(__('Your license key is empty or invalid. Please check the key in Settings / Victorious', 'victorious')),
];
$model = new VIC_Model();
$checkAPIToken = $model->checkAPITokenAdmin();
if(get_option('victorious_api_token') == null)
{
    $checkAPIToken = 505;
}
if (isset($error_code_message[$checkAPIToken])) {
    echo "<div class='error settings-error notice'><p><strong>";
    echo esc_html($error_code_message[$checkAPIToken]);
    echo '</strong></p></div>';
}