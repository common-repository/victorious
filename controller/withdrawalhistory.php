<?php
class VIC_WithdrawalHistoryController
{
    private static $payment;
    private static $victorious;
    public function __construct() 
    {
        self::$payment = new VIC_Payment();
        self::$victorious = new VIC_Victorious();
    }
    
	public static function process()
	{
        add_action('wp_enqueue_scripts', array('VIC_WithdrawalHistoryController', 'theme_name_scripts'));
        add_filter('the_content', array('VIC_WithdrawalHistoryController', 'addContent'));
	}
    
    public static function theme_name_scripts()
    {
        wp_enqueue_script('payment.js', VICTORIOUS__PLUGIN_URL_JS.'payment.js');

        wp_enqueue_style('bootstrap.css', VICTORIOUS__PLUGIN_URL_CSS.'bootstrap.css');
        wp_enqueue_style('style.css', VICTORIOUS__PLUGIN_URL_CSS.'style.css');
        wp_enqueue_style('Material', VICTORIOUS__PLUGIN_URL_CSS.'material_icons.css');
    }

    public static function addContent()
    {
        if(!in_the_loop())
        {
            return;
        }
        list($total_items, $aWithdraws) = self::$payment->getListWithdraw(null, 'withdrawlID DESC', (1 - 1) * 10, 10);
        $aWithdraws = self::$payment->parseWithdrawData($aWithdraws);
        
        $sUrlSubmit = VICTORIOUS_URL_REQUEST_HISTORY;
        $aGateways = self::$payment->viewGateway();
        include VICTORIOUS__PLUGIN_DIR_VIEW.'withdrawalhistory.php';
    }
}
?>