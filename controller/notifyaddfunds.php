<?php
class VIC_NotifyaddfundsController
{
    private static $payment;
    private static $paypal;
    public function __construct() 
    {
        self::$payment = new VIC_Payment();
        self::$paypal = new VIC_Paypal();
    }
    
	public static function process()
	{
        $status = self::$paypal->callback();
        $fundshistoryID = null;
        $custom = array();
        if(isset($_POST['custom']))
        {
            $custom = explode('|', sanitize_text_field($_POST['custom']));
            $fundshistoryID = $custom[1];
        }
		
        if(($status == 'completed' || $status == "pending") && !self::$payment->isPaypalCompleted($fundshistoryID))
		{
            self::$payment->updateUserBalance($custom[2], null, 0, $custom[0]);
            self::$payment->updateFundhistory($fundshistoryID, array('transactionID' => sanitize_text_field($_POST['txn_id']), 'is_checkout' => 1), $custom[0], $status);
        }
		else
		{
            VIC_Redirect(VICTORIOUS_URL_ADD_FUNDS, null, true);
		}
	}
}
?>