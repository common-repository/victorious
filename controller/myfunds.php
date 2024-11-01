<?php
class VIC_MyfundsController
{
    private static $payment;
    private static $user;
    private static $victorious;
    private static $coupon;
    private static $balanceType;
    public function __construct() 
    {
        self::$payment = new VIC_Payment();
        self::$user = new VIC_User();
        self::$victorious = new VIC_Victorious();
        self::$coupon = new VIC_CouponModel();
        self::$balanceType = new VIC_BalanceType();
    }

	public static function process()
	{
        add_action( 'wp_enqueue_scripts', array('VIC_MyfundsController', 'theme_name_scripts') );
        add_filter('the_content', array('VIC_MyfundsController', 'addContent'));
	}
    
    public static function theme_name_scripts()
    {
        wp_enqueue_script('payment.js', VICTORIOUS__PLUGIN_URL_JS.'payment.js');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script('jquery-ui-autocomplete');
        wp_enqueue_style('bootstrap.css', VICTORIOUS__PLUGIN_URL_CSS.'bootstrap.css');
        wp_enqueue_style('style.css', VICTORIOUS__PLUGIN_URL_CSS.'style.css');
        wp_enqueue_style('Material', VICTORIOUS__PLUGIN_URL_CSS.'material_icons.css');
        wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');
    }

    public static function addContent()
    {
        if(!in_the_loop())
        {
            return;
        }
        if(isset($_SESSION['is_transaction']))
        {
            unset($_SESSION['is_transaction']);
        }
        if(isset($_SESSION['iFundHitoryId']))
        {
            unset($_SESSION['iFundHitoryId']);
        }
        if(isset($_SESSION['totalMoney']))
        {
            unset($_SESSION['totalMoney']);
        }
        
        $gatewayList = self::$payment->gatewayList(true);
        $aUserPayment = self::$payment->getUserPaymentInfo(VIC_GetUserId());
        $aUser = self::$payment->getUserData();
        $withdrawPending = self::$user->getWithdrawlsTotal(VIC_GetUserId());
        $isHasCoupon = self::$coupon->isHasCoupon(CP_ACTION_ADD_MONEY);
        $withdrawl_gateways = get_option('victorious_payout_method');
        $global_setting = self::$victorious->getGlobalSetting();

        //balance type
        $balance_types = self::$balanceType->getBalanceTypeList(array(
            'enabled' => 1
        ));

        include VICTORIOUS__PLUGIN_DIR_VIEW.'myfunds.php';
    }
}
?>