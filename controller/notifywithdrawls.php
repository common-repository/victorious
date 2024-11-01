<?php
class VIC_NotifywithdrawlsController
{
    private static $payment;
    private static $paypal;
    private static $victorious;
    public function __construct() 
    {
        self::$payment = new VIC_Payment();
        self::$paypal = new VIC_Paypal();
        self::$victorious = new VIC_Victorious();
    }
    
	public function process()
	{
        $status = self::$paypal->callback();
        
		if($status == 'completed' || $status == "pending")
		{
			$custom = explode('|', sanitize_text_field($_POST['custom']));
            $aWithdrawl = self::$payment->getWithdraw($custom[0]);
			$aVals = array('status' => 'APPROVED', 
						   'response_message' => $custom[1],
						   'processedDate' => date('Y-m-d H:i:s'),
						   'transactionID' => sanitize_text_field($_POST['txn_id']));
			self::$payment->updateWithdraw($custom[0], $aVals);
            self::$victorious->sendApplyWithdrawlEmail($custom[0], 'APPROVED');
            if($aWithdrawl != null &&$aWithdrawl['status'] == 'DECLINED')
            {
                self::$payment->updateUserBalance($aWithdrawl['amount'], true, 0, $aWithdrawl['userID']);
                $aUser = self::$payment->getUserData($aWithdrawl['userID']);
                $params = array(
                    'userID' => $aWithdrawl['userID'],
                    'amount' => $aWithdrawl['amount'],
                    'new_balance' => $aUser['balance'],
                    'operation' => 'DEDUCT',
                    'type' => 'WITHDRAW',
                    'reason' => 'approved withdrawl',
                    'status' => 'completed'
                );
                self::$payment->addFundhistory($params);
            }
		}
		else
		{
			VIC_Redirect(admin_url().'admin.php?page=withdrawls');
		}
	}
}
?>