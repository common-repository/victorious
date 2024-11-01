<?php
$Victorious_Withdrawls = new Victorious_Withdrawls();
class Victorious_Withdrawls
{
    private static $payment;
    public function __construct() 
    {
        self::$payment = new VIC_Payment();
    }
    
    public static function manageWithdrawls()
    {
        //load css js
        wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS.'admin/admin.js');
        wp_enqueue_script('jquery-ui-dialog');
        
        //task action delete
        if(isset($_POST["task"]) && $task = sanitize_text_field($_POST["task"]))
        {
            switch($task)
            {
                case "delete":
                    self::delete();
                    break;
            }
        }
        
        $aGateways = self::$payment->viewGateway();
        include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";
        include VICTORIOUS__PLUGIN_DIR_VIEW.'withdrawls/class.table-withdrawls.php';
        $myListTable = new VIC_TableWithdrawls();
        $myListTable->prepare_items(isset($_GET['s']) ? sanitize_text_field($_GET['s']) : null);
        include VICTORIOUS__PLUGIN_DIR_VIEW.'withdrawls/index.php';
    }
}
?>