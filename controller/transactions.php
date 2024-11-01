<?php
add_action( 'init', array('Transactions', 'process'));
class VIC_TransactionsController
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
        add_action('wp_enqueue_scripts', array('VIC_TransactionsController', 'theme_name_scripts'));
        add_filter('the_content', array('VIC_TransactionsController', 'addContent'));
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
        list($total_items, $aFundHistorys) = self::$payment->getFundhistory(null, 'fundshistoryID DESC', (1 - 1) * 10, 10);
        $aFundHistorys = self::$payment->parseFunhistoryData($aFundHistorys);
        
        $sUrlSubmit = VICTORIOUS_URL_ADD_FUNDS;
        $aGateways = self::$payment->viewGateway();
        include VICTORIOUS__PLUGIN_DIR_VIEW.'transactions.php';
    }
}
?>