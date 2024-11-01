<?php
$Victorious_Statistic = new Victorious_Statistic();
class Victorious_Statistic
{
    private static $payment;
    public function __construct() 
    {
        self::$payment = new VIC_Payment();
    }
    
    public static function manageStatistic()
    {
        //load css js
        wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS.'admin/statistic.js');
        wp_enqueue_script('jquery-ui-dialog');
        include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";
        include VICTORIOUS__PLUGIN_DIR_VIEW.'statistic/class.table-statistic.php';
        $myListTable = new VIC_TableStatistic();
        $myListTable->prepare_items(isset($_GET['s']) ? sanitize_text_field($_GET['s']) : null);
        include VICTORIOUS__PLUGIN_DIR_VIEW.'statistic/index.php';
    }
}
?>