<?php
class VIC_SuccessaddfundsController
{
    private static $payment;
    public function __construct() 
    {
        self::$payment = new VIC_Payment();
    }
    
	public static function process()
	{
        if(isset($_GET['resp']))//for flutterwave
		{
            //$resp = '{'.$_GET['resp'].'}';
			
	
            $resp = str_replace("\\", '', sanitize_text_field($_GET['resp']));
			$data = json_decode($resp, true);
			
			$id = explode('-', $data['orderinfo']);
			if(empty($id[1]))
			{
				VIC_Redirect(VICTORIOUS_URL_TRANSACTIONS, __('Can not find order info', 'victorious'), true);
			}
			$fundhistory = self::$payment->getFundhistory("gateway = '".VICTORIOUS_GATEWAY_FLUTTERWAVE."' AND transactionID = '".trim($id[1])."' AND ", 'fundshistoryID DESC', 0, 1);
			
			if(empty($fundhistory[1][0]))
			{
				VIC_Redirect(VICTORIOUS_URL_TRANSACTIONS, __('Can not find this transaction', 'victorious'), true);
			}
			$fundhistory = $fundhistory[1][0];
				
			if($data['responsecode'] == '00')
			{
				self::$payment->updateUserBalance($fundhistory['amount'], false, 0, VIC_GetUserId());
                self::$payment->updateFundhistory($fundhistory['fundshistoryID'], array(
                'is_checkout' => 1), VIC_GetUserId(), "completed");
				VIC_Redirect(VICTORIOUS_URL_TRANSACTIONS, __('Transaction Complete', 'victorious'), true);
			}
			else
			{
				self::$payment->updateFundhistory($fundhistory['fundshistoryID'], array(
                'is_checkout' => 1), VIC_GetUserId(), "failed");
				VIC_Redirect(VICTORIOUS_URL_TRANSACTIONS, $data['responsemessage'], true);
			}
		}
        VIC_Redirect(VICTORIOUS_URL_TRANSACTIONS, __('Transaction Complete', 'victorious'), true);
	}
}
?>