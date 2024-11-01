<?php

class VIC_AddfundsController
{

    private static $payment;
    private static $victorious;
    private static $coupon;
    private static $balanceType;
    public function __construct()
    {
        self::$payment = new VIC_Payment();
        self::$victorious = new VIC_Victorious();
        self::$coupon = new VIC_CouponModel();
        self::$balanceType = new VIC_BalanceType();
    }

    public static function process()
    {
        add_action('wp_enqueue_scripts', array('VIC_AddfundsController', 'theme_name_scripts'));
        add_filter('the_content', array('VIC_AddfundsController', 'addContent'));
    }

    public static function theme_name_scripts()
    {
        wp_enqueue_script('payment.js', VICTORIOUS__PLUGIN_URL_JS . 'payment.js');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');
        wp_enqueue_style('bootstrap.css', VICTORIOUS__PLUGIN_URL_CSS.'bootstrap.css');
        wp_enqueue_style('style.css', VICTORIOUS__PLUGIN_URL_CSS.'style.css');
        wp_enqueue_style('Material', VICTORIOUS__PLUGIN_URL_CSS.'material_icons.css');
    }

    public static function addContent()
    {
        if (!in_the_loop())
        {
            return;
        }
        if (isset($_SESSION['is_transaction']))
        {
            unset($_SESSION['is_transaction']);
        }
        if (isset($_SESSION['iFundHitoryId']))
        {
            unset($_SESSION['iFundHitoryId']);
        }
        if (isset($_SESSION['totalMoney']))
        {
            unset($_SESSION['totalMoney']);
        }

        $gatewayList = self::$payment->gatewayList();

        //balance type
        $balance_types = self::$balanceType->getBalanceTypeList(array(
            'enabled' => 1,
            'is_core' => 0
        ));

		if(count($gatewayList) == 1 && !isset($_GET['type']) && $balance_types == null)
        {
            wp_redirect(VICTORIOUS_URL_ADD_FUNDS.'?type='.key($gatewayList));
            exit;
        }

        //check redirect if there is only one gateway
        $gateway_type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : "";
        $payout_gateways = !empty(get_option('victorious_payout_gateway')) ? get_option('victorious_payout_gateway') : array();

        if ($payout_gateways == null && $balance_types == null)
        {
            VIC_Redirect(VICTORIOUS_URL_LOBBY, __('No available payment gateway, please contact admin.', 'victorious'));
        }
        if (count($payout_gateways) == 1 && $balance_types == null)
        {
            $gateway_type = $payout_gateways[0];
        }
        if (isset($_GET['type']) && !self::$payment->isGatewayExist($gateway_type))
        {
            VIC_Redirect(VICTORIOUS_URL_LOBBY, __('Payment gateway not found', 'victorious'));
        }
        if ($gateway_type == VICTORIOUS_GATEWAY_DFSCOIN)
        {
            wp_enqueue_script('currency.js', VICTORIOUS__PLUGIN_URL_JS.'dfs_currency.js');
        }

        //check can play
        $canplay = false;
        if (self::$victorious->canPlay())
        {
            $sUrlSubmit = VICTORIOUS_URL_ADD_FUNDS;
            //$aGateways = self::$payment->viewGateway();
            $canplay = true;
        }
        $isHasCoupon = self::$coupon->isHasCoupon(CP_ACTION_EXTRA_DEPOSIT) || self::$coupon->isHasCoupon(CP_ACTION_ADD_MONEY);
        $fee_percentage = (int) get_option('victorious_fee_percentage');
        $country_list = self::$victorious->getCountryList();
        $global_setting = self::$victorious->getGlobalSetting();

        //auto update dfscoin exchange rate
        if (empty($_GET['type']))
        {
            if ($global_setting['multiple_currency_support'])
            {
                self::$victorious->updateCoinExchangeRate();
            }
        }
        
        //set message for return url
        if(!empty($_GET['return_url']))
        {
            VIC_SetMessage(__('Online payment was successfully completed and transaction is being processed', 'victorious'));
        }

        //check pay simple customer
        if($gateway_type == VICTORIOUS_GATEWAY_PAYSIMPLE){
            $checkExistingCustomer = self::$payment->paySimpleCheckExistingCustomer();
        }

        include VICTORIOUS__PLUGIN_DIR_VIEW . 'addfunds.php';
    }
}

?>