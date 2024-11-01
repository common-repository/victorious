<?php
class VIC_PaymentCallbackController
{
    public static function callback()
    {
		require_once(VICTORIOUS__PLUGIN_DIR_MODEL."payment.php");
        require_once(VICTORIOUS__PLUGIN_DIR_MODEL."molpay.php");
		$payment = new VIC_Payment();
        $molpay = new VIC_MolPay();

		$molpay->write_log("Receive callback");
		if(empty($_POST))
		{
			$molpay->write_log("no data params");
			exit;
		}
		$molpay->write_log($_POST);
		if(empty($_POST['applicationCode']) && sanitize_text_field($_POST['applicationCode']) != trim(get_option('victorious_molpay_application_code')))
		{
			$molpay->write_log("applicationCode not found");
			exit;
		}
		
		$fundhistory_id = sanitize_text_field($_POST['referenceId']);
		$fundhistory = $payment->getFundhistoryDetail(array(
			"fundshistoryID" => $fundhistory_id
		));
		if(empty($fundhistory))
		{
			$molpay->write_log("fundhistory not found");
			exit;
		}
		if($fundhistory['is_checkout'] == 1)
		{
			$molpay->write_log("This transaction has already finished");
			exit;
		}
        if (!empty($_POST['paymentStatusCode']) && sanitize_text_field($_POST['paymentStatusCode']) == "00")
        {
			$molpay->write_log("fund history data");
			$molpay->write_log($fundhistory);
			$molpay->write_log("start updating user balance");
			if ($payment->updateUserBalance((int) $fundhistory['amount'], false, 0, $fundhistory['userID']))
			{
				$fund_data = array(
					'transactionID' => sanitize_text_field($_POST['paymentId']),
					'is_checkout' => 1
				);
				$payment->updateFundhistory($fundhistory['fundshistoryID'], $fund_data, $fundhistory['userID'], 'completed');
				$molpay->write_log("Successfully transaction ");
				exit;
			}
        }
		$payment->updateFundhistory($fundhistory['fundshistoryID'], array('is_checkout' => 1), $fundhistory['userID'], 'failed');
		$molpay->write_log("Transaction failed");
		exit;
    }
    
    public static function gamble()
    {
        require_once(VICTORIOUS__PLUGIN_DIR_MODEL."payment.php");
        require_once(VICTORIOUS__PLUGIN_DIR_MODEL."gamble.php");
        $payment = new VIC_Payment();
        $gamble = new VIC_GatewayGamble();
        
        $gamble->write_log("Receive callback");
		if(empty($_REQUEST['result']))
		{
			$gamble->write_log("no data params");
			exit;
		}
		$result = stripslashes(sanitize_text_field($_REQUEST['result']));
		$params = json_decode($result, true);
		$gamble->write_log($params);
        
        $fundhistory = $payment->getFundhistoryDetail(array(
            "verification_code" => $params['MerchantTransactionID']
        ));
        if(empty($fundhistory))
        {
            $gamble->write_log("fundhistory not found");
            exit;
        }
        
        if($params['StatusCode'] == 0)
        {
            $gamble->write_log("fund history data");
			$gamble->write_log($fundhistory);
            
            //withdraw
            if($fundhistory['type'] == "WITHDRAW")
            {
                $gamble->write_log("start updating data for withdrawal");
                $status = $params['TransactionStatusMessage'] != "Complete" ? "PENDING" : "APPROVED";
                $payment->updateWithdraw($fundhistory['withdrawlID'], array(
                    'status' => $status
                ));
                $gamble->write_log("Successfully withdrawal");
				exit;
            }
            
            //add fund
			if($fundhistory['status'] == 'completed')
            {
                exit;
            }
            if($params['TransactionStatusMessage'] != "Complete")
            {
                $gamble->write_log("Transaction status: ".$params['TransactionStatusMessage']);
                exit;
            }
			$gamble->write_log("start updating user balance");
			if ($payment->updateUserBalance((int) $fundhistory['amount'], false, 0, $fundhistory['userID']))
			{
				$fund_data = array(
					'transactionID' => '', 
					'is_checkout' => 1
				);
				$payment->updateFundhistory($fundhistory['fundshistoryID'], $fund_data, $fundhistory['userID'], 'completed');
				$gamble->write_log("Successfully transaction");
				exit;
			}
        }
        else
        {
            $payment->updateFundhistory($fundhistory['fundshistoryID'], array('is_checkout' => 1), $fundhistory['userID'], 'failed');
            $molpay->write_log("Transaction failed");
        }
        exit;
    }
}

?>