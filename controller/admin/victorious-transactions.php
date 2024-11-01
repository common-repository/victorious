<?php
$Victorious_Transactions = new Victorious_Transactions();
class Victorious_Transactions
{
    public static function manageTransactions()
    {
        //load css js
        wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS.'admin/admin.js');
        wp_enqueue_script('statistic.js', VICTORIOUS__PLUGIN_URL_JS.'admin/statistic.js');
        wp_enqueue_script('jquery-ui-dialog');
        include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";
        include VICTORIOUS__PLUGIN_DIR_VIEW.'transactions/class.table-transactions.php';
        $myListTable = new VIC_TableTransactions();
        $myListTable->prepare_items(isset($_GET['s']) ? sanitize_text_field($_GET['s']) : null);
        include VICTORIOUS__PLUGIN_DIR_VIEW.'transactions/index.php';
    }
}
?>