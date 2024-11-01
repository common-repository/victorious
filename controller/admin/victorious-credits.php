<?php
$Victorious_Credits = new Victorious_Credits();
class Victorious_Credits
{
    private static $balanceType;
    private static $victorious;
    public function __construct()
    {
        self::$balanceType = new VIC_BalanceType();
        self::$victorious = new VIC_Victorious();
    }
    
    public static function manageCredits()
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
        include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";
        include VICTORIOUS__PLUGIN_DIR_VIEW.'credits/class.table-credits.php';
        $myListTable = new VIC_TableCredits();
        $myListTable->prepare_items(isset($_GET['s']) ? sanitize_text_field($_GET['s']) : null);

        $global_setting = self::$victorious->getGlobalSetting();
        $balance_types = array();
        if(!empty($global_setting['allow_multiple_balances'])){
            $balance_types = self::$balanceType->getBalanceTypeList();
        }
        include VICTORIOUS__PLUGIN_DIR_VIEW.'credits/index.php';
    }
}
?>