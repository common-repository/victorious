<?php
include_once("paypal.php");

class VIC_Payment extends VIC_Model
{
    function validEmail($email)
    {
        $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; 
        if (preg_match($regex, $email)) {
            return true;
        } else { 
            return false;
        } 
    }
    
    function isGatewayExist($data)
    {
        $gatewayList = $this->gatewayList();
        if(array_key_exists($data, $gatewayList))
        {
            return true;
        }
        return false;
    }
    
    function gatewayList($withdrawl = false)
    {
        $gateways = array(
            VICTORIOUS_GATEWAY_PAYPAL => array(
                'name' => __('Paypal Gateway', 'victorious'),
                'icon' => "paypal.png"
            ),
            VICTORIOUS_GATEWAY_DFSCOIN => array(
                'name' => __('Dfs Gateway', 'victorious'),
                'icon' => "dfscoin.png"
            ),
            VICTORIOUS_GATEWAY_PAYSIMPLE => array(
                'name' => __('PaySimple', 'victorious'),
                'icon' => "paysimple.png"
            )
        );
        $allow_gateway = array();
        if(!$withdrawl)
        {
            $allow_gateway = get_option('victorious_payout_gateway');
            if($allow_gateway == null)
            {
                return array();
            }
        }
        else
        {
            $allow_gateway = get_option('victorious_payout_method');
            if($allow_gateway == null)
            {
                return array();
            }
        }
        foreach($gateways as $k => $gateway)
        {
            if(!in_array($k, $allow_gateway))   
            {
                unset($gateways[$k]);
            }
        }
        return $gateways;
    }
    
    function viewGateway()
    {
        $gateway = array();
        if(get_option('paypal_email_account') != null)
        {
            //$gateway[] = PAYPAL;
        }
        return $gateway;
    }
    
    function feePercentage($value)
    {
        $fee = get_option('victorious_fee_percentage');
        if($fee > 0)
        {
            $value = $value + round(($value * $fee / 100), 2);
        }
        return $value;
    }
    
    // for widthrawal
    function changeCashToCredit($iCash)
    {
        $credit = get_option('victorious_credit_to_cash');
        if($credit > 1){
            $iCash = round($iCash/$credit,2);
        }
        return $iCash;
    }
    
    // for deposit
    function changeCreditToCash($iCredit)
    {
        $money = 1;
        $credit = get_option('victorious_cash_to_credit');
        
        return $iCredit * $credit;
    }
    
    function onlineTransaction($gateway = PAYPAL, $aSettings, $fundshistoryID = null,$real_balance=0)
    {
        global $wpdb;
        switch($gateway)
        {
            case VICTORIOUS_GATEWAY_PAYPAL:
                $paypal = new VIC_Paypal();
                return $paypal->parseData($aSettings);
            case VICTORIOUS_GATEWAY_PAYPAL_PRO:
                return $this->gatewayPaypalpro($aSettings, $fundshistoryID, $real_balance);
            case VICTORIOUS_GATEWAY_DFSCOIN:
                return $this->gatewayDfscoin($aSettings);
            default :
                return false;
        }
        return false;
    }
    
    private function gatewayPaypalpro($aSettings, $fundshistoryID = null, $real_balance = 0)
    {
        $paypal = new PaypalPro();
        $result = $paypal->checkout($aSettings);
        if($result['success'] == 1)
        {
            $this->updateUserBalance(/*$aSettings['AMT']*/$real_balance, false, 0, VIC_GetUserId());
            $this->updateFundhistory($fundshistoryID, array(
                'transactionID' => $result['transactionID'], 
                'is_checkout' => 0), VIC_GetUserId(), "completed");
            return true;
        }
        else if($result['success'] == 0 && !empty($result['message']))
        {
            return $result['message'];
        }
    }
    
    function confirmPaypal()
    {
        //Paypal redirects back to this page using ReturnURL, We should receive TOKEN and Payer ID
        if(isset($_GET["token"]) && isset($_GET["PayerID"]))
        {
            //we will be using these two variables to execute the "DoExpressCheckoutPayment"
            //Note: we haven't received any payment yet.

            $token = sanitize_text_field($_GET["token"]);
            $payer_id = sanitize_text_field($_GET["PayerID"]);

            //get session variables
            $ItemName 			= sanitize_text_field($_SESSION['ItemName']); //Item Name
            $ItemPrice 			= sanitize_text_field($_SESSION['ItemPrice']) ; //Item Price
            $ItemNumber 		= sanitize_text_field($_SESSION['ItemNumber']); //Item Number
            $ItemDesc 			= sanitize_text_field($_SESSION['ItemDesc']); //Item Number
            $ItemQty 			= sanitize_text_field($_SESSION['ItemQty']); // Item Quantity
            $ItemTotalPrice 	= sanitize_text_field($_SESSION['ItemTotalPrice']); //(Item Price x Quantity = Total) Get total amount of product;

            $padata = 	'&TOKEN='.urlencode($token).
                        '&PAYERID='.urlencode($payer_id).
                        '&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode("SALE").

                        //set item info here, otherwise we won't see product details later	
                        '&L_PAYMENTREQUEST_0_NAME0='.urlencode($ItemName).
                        '&L_PAYMENTREQUEST_0_NUMBER0='.urlencode($ItemNumber).
                        '&L_PAYMENTREQUEST_0_DESC0='.urlencode($ItemDesc).
                        '&L_PAYMENTREQUEST_0_AMT0='.urlencode($ItemPrice).
                        '&L_PAYMENTREQUEST_0_QTY0='. urlencode($ItemQty).
                        '&PAYMENTREQUEST_0_ITEMAMT='.urlencode($ItemTotalPrice).
                        '&PAYMENTREQUEST_0_AMT='.urlencode($ItemTotalPrice).
                        '&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode('USD');
            
            $paypal = new VIC_MyPayPal(esc_attr(get_option('victorious_paypal_pro_username')),
                esc_attr(get_option('victorious_paypal_pro_password')),
                esc_attr(get_option('victorious_paypal_pro_signature')),
                get_option('paypal_test'));
            return $paypal->confirm($padata);
        }
        return false;
    }
    
    private function gatewayDfscoin($data)
    {
        require_once("dfscoin.php");
        $dfscoin = new VIC_Dfscoin();
        return $dfscoin->transactionDetail($data['transaction_id'], get_option('victorious_dfscoin_wallet_address'));
    }
    
    public function checkAppliedDfscoinTransaction($transaction_id)
    {
        global $wpdb;
        $table_fundhistory = $wpdb->prefix.'fundhistory';
        $sql = "SELECT * "
                . "FROM $table_fundhistory "
                . "WHERE transactionID = '$transaction_id' AND type = 'DEPOSIT' AND operation = 'ADD' AND gateway = '".VICTORIOUS_GATEWAY_DFSCOIN."'";
        $data = $wpdb->get_var($sql);
        if($data > 0)
        {
            return true;
        }
        return false;
    }
    
    function exchangeRateDfscoin($value)
    {
        $credit = get_option('victorious_dfscoin_exchange_rate');
        if($credit == null)
        {
            $credit = 1;
        }
        return round($value * $credit, 3);
    }
    
    ###########################
	#
	#       USER
	#
	###########################
    function getUserData($userID = null)
    {
        $user_id = (int)VIC_GetUserId();
        if((int)$userID > 0)
        {
            $user_id = $userID;
        }
        
        global $wpdb;
        $table_user = $wpdb->prefix.'users';
        $table_user_extended = $wpdb->prefix.'user_extended';
        $sCond = "WHERE u.ID = ".$user_id;
        $sql = "SELECT u.*, u.display_name as full_name, u.user_email as email, u.user_login as user_name, IFNULL(ue.balance, 0.00) as balance, ue.* "
             . "FROM $table_user u "
             . "LEFT JOIN $table_user_extended ue ON ue.user_id = u.ID "
             . $sCond;
        $data = $wpdb->get_row($sql, ARRAY_A);
        return $data;
    }
    
    public function isUserExtendedExist($user_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'user_extended';
        $sCond = "WHERE user_id = ".(int)$user_id;
        $sql = "SELECT COUNT(*) "
             . "FROM $table_name "
             . $sCond;
        $data = $wpdb->get_var($sql);
        if($data == 1)
        {
            return true;
        }
        return false;
    }

    public function isUserBalanceExist($user_id, $balance_type_id)
    {
        global $wpdb;
        $balance_type_id = $balance_type_id > 0 ? $balance_type_id : VICTORIOUS_DEFAULT_BALANCE_TYPE_ID;
        $table_name = VIC_GetTableName('balances');
        $sCond = "WHERE user_id = ".(int)$user_id." AND balance_type_id = ".(int)$balance_type_id." ";
        $sql = "SELECT COUNT(*) "
            . "FROM $table_name "
            . $sCond;
        $data = $wpdb->get_var($sql);
        if($data == 1)
        {
            return true;
        }
        return false;
    }
    
    public function isUserExist($user_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'users';
        $sCond = "WHERE ID = ".(int)$user_id;
        $sql = "SELECT COUNT(*) "
             . "FROM $table_name "
             . $sCond;
        $data = $wpdb->get_var($sql);
        if($data == 1)
        {
            return true;
        }
        return false;
    }
    
    public function getUserByLoginName($user_login)
    {
        if(empty($user_login))
        {
            return null;
        }
        global $wpdb;
        $table_name = $wpdb->prefix.'users';
        $sCond = "WHERE user_login = '$user_login'";
        $sql = "SELECT * "
             . "FROM $table_name "
             . $sCond;
        $data = $wpdb->get_row($sql);
        return json_decode(json_encode($data), true);
    }
    
    public function checkUserAlreadyJoinFeeContest($leagueID){
        global $wpdb;
        $sCons = "WHERE userID = ".VIC_GetUserId()." AND leagueID = ".(int)$leagueID;
        $table_name = $wpdb->prefix.'fundhistory';
        $sql = "SELECT count(*) "
             . "FROM $table_name "
             . $sCons;
        $data = $wpdb->get_var($sql);
        if($data > 0)
        {
            return true;
        }
        return false;
    }
    
    public function isUserEnoughMoneyToJoin($prize = 0, $leagueID = null, $entry_number = 1,$multi_entry=0, $balance_type_id = '')
    {
        if($prize == 0){
            return true;
        }
        $is_allow_check_bet = true;
        if($entry_number == 0 && $prize > 0){
            $is_user_join_contest = $this->checkUserAlreadyJoinFeeContest($leagueID);
            if($is_user_join_contest){
                $is_allow_check_bet = empty($multi_entry) ? true : false;
            }
            $entry_number = 1;
        }
        if($is_allow_check_bet == true && $this->isMakeBetForLeague($leagueID, $entry_number))
        {
            return true;
        }
        $user_id = (int)VIC_GetUserId();
        $user_balance = $this->getUserBalance($user_id, $balance_type_id);
        if($user_balance['balance'] < $prize)
        {
            return false;
        }
        return true;
    }
    
    public function isUserEnoughMoneyToChangePlayer($prize = 0)
    {
        $user = $this->getUserData();
        if((int)$user['balance'] < $prize)
        {
            return false;
        }
        return true;
    }
    
    public function updateUserBalance($amount = 0, $decrease = false, $leagueID = 0, $user_id = null, $balance_type_id = VICTORIOUS_DEFAULT_BALANCE_TYPE_ID)
    {
        global $wpdb;
        $balance_type_id = $balance_type_id > 0 ? $balance_type_id : VICTORIOUS_DEFAULT_BALANCE_TYPE_ID;
        $user_id = !empty($user_id) ? $user_id : (int)VIC_GetUserId();
        $user_balance = $this->getUserBalance($user_id, $balance_type_id);
        $amount = $decrease ? -$amount : $amount;
        $new_balance = $amount;
        if($user_balance != null){
            $new_balance = $user_balance['balance'] + $amount;
        }
        $values = array(
            'user_id' => $user_id,
            'balance_type_id' => $balance_type_id,
            'balance' => $new_balance
        );
        $table_name = VIC_GetTableName('balances');
        if($user_balance != null)
        {
            return $wpdb->update($table_name, $values, array('user_id' => $user_id, 'balance_type_id' => $balance_type_id));
        }
        return $wpdb->insert($table_name, $values);
    }
    
    public function isUserPaymentInfoExist()
    {
        global $wpdb;
        $sCond = "WHERE user_id = ".VIC_GetUserId();
        $table_name = $wpdb->prefix."user_payment";
        $sql = "SELECT user_id "
             . "FROM $table_name "
             . $sCond;
        $aData = $wpdb->get_row($sql);

        if(count($aData) == 1)
        {
            return true;
        }
        return false;
    }

    function getUserPaymentBankCode($userId = null)
    {
        global $wpdb;

        if (is_null($userId))
        {
            $userId = intval(VIC_GetUserId());
        }

        $table = $wpdb->prefix."user_payment";
        $sql = "SELECT bankcode FROM $table where user_id = $userId limit 1";
        $row = $wpdb->get_row($sql);

        return is_null($row) ? null : intval($row->bankcode);
    }

    function getUserPaymentInfo($user_id = null)
    {
        global $wpdb;
        $sCond = "WHERE up.user_id = ".(int)VIC_GetUserId();
        if((int)$user_id > 0)
        {
            $sCond = "WHERE up.user_id = ".(int)$user_id;
        }
        $table_userpayment = $wpdb->prefix."user_payment";
        $table_user = $wpdb->prefix."users";
        $table_userextended = $wpdb->prefix."user_extended";
        $sql = "SELECT up.*, u.display_name as full_name, IFNULL(ue.balance, 0.00) as balance "
             . "FROM $table_userpayment up "
             . "INNER JOIN $table_user u ON up.user_id = u.ID "
             . "LEFT JOIN $table_userextended ue ON ue.user_id = u.ID "
             . $sCond;
        $data = $wpdb->get_row($sql);
        $data = json_decode(json_encode($data), true);
        return $data;
    }
    
    public function addUserPaymentInfo($aVals)
    {
        global $wpdb;
        $aVals['user_id'] = VIC_GetUserId();
        $aVals['time_stamp'] = current_time('timestamp');
        $aVals['time_update'] = current_time('timestamp');
        return $wpdb->insert($wpdb->prefix."user_payment", $aVals);
    }
    
    public function updateUserPaymentInfo($aVals)
    {
        global $wpdb;
        $aVals['time_update'] = current_time('timestamp');
        return $wpdb->update($wpdb->prefix."user_payment", $aVals, array('user_id' => VIC_GetUserId()));
    }
    
    ###########################
	#
	#       FUNDHISTORY
	#
	###########################
    public function isMakeBetForLeague($leagueID, $entry_number = 1, $reason = '')
    {
        global $wpdb;
        
        //count make bet
        $sCons = "WHERE userID = ".VIC_GetUserId()." AND leagueID = ".(int)$leagueID." AND entry_number = ".$entry_number." AND type = 'MAKE_BET'";
        if($reason != ''){
            $sCons .= " AND reason = '".$reason."' ";
        }
        $table_name = $wpdb->prefix.'fundhistory';
        $sql = "SELECT count(*) "
             . "FROM $table_name "
             . $sCons;
        $make_bet_data = $wpdb->get_var($sql);
        
        //count leave contest
        $sCons = "WHERE userID = ".VIC_GetUserId()." AND leagueID = ".(int)$leagueID." AND entry_number = ".$entry_number." AND type = 'LEAVE_CONTEST'";
        if($reason != ''){
            $sCons .= " AND reason = '".$reason."' ";
        }
        $table_name = $wpdb->prefix.'fundhistory';
        $sql = "SELECT count(*) "
             . "FROM $table_name "
             . $sCons;
        $leave_data = $wpdb->get_var($sql);
        
        if($make_bet_data > 0 && $make_bet_data > $leave_data)
        {
            return true;
        }
        return false;
    }
    
    public function isPaypalCompleted($fundshistoryID)
    {
        global $wpdb;
        $sCons = "WHERE userID = ".VIC_GetUserId()." AND transactionID != '' AND fundshistoryID = ".(int)$fundshistoryID;
        $table_name = $wpdb->prefix.'fundhistory';
        $sql = "SELECT count(*) "
             . "FROM $table_name "
             . $sCons;
        $data = $wpdb->get_var($sql);
        if($data == 1)
        {
            return true;
        }
        return false;
    }
	
	function checkMoneyWonAdded($user_id, $amount, $operation, $type, $leagueID, $entry_number = 1)
    {
        global $wpdb;
        $sCons = "WHERE userID = $user_id AND amount = '".$amount."' AND operation = '".$operation."' AND type = '".$type."' AND leagueID = ".$leagueID." AND entry_number = $entry_number ";
        $table_name = $wpdb->prefix.'fundhistory';
        $sql = "SELECT count(*) "
             . "FROM $table_name "
             . $sCons;
        $data = $wpdb->get_var($sql);
        if($data == 1)
        {
            return true;
        }
        return false;
    }
    
    public function getFundhistory($aConds, $sSort = 'fundshistoryID DESC', $iPage = '', $iLimit = '')
	{	
        global $wpdb;
        $table_fundhistory = $wpdb->prefix."fundhistory";
        $aConds .= 'userID = '.(int)VIC_GetUserId();
        $sCond = $aConds != null ? "WHERE ".$aConds : '';
        $sql = "SELECT COUNT(*) "
             . "FROM $table_fundhistory "
             . $sCond;
        $iCnt = $wpdb->get_var($sql);

        $sCond = $aConds != null ? "WHERE ".$aConds : '';
        $sql = "SELECT *, DATE_FORMAT(date, '%Y-%m-%d') as date "
             . "FROM $table_fundhistory "
             . $sCond." "
             . "ORDER BY $sSort "
             . "limit $iPage, $iLimit ";
        $aRows = $wpdb->get_results($sql);
        $aRows = json_decode(json_encode($aRows), true);
        
        return array($iCnt, $aRows);
	}
    
    public function getFundhistoryDetail($params)
	{
		global $wpdb;
        $table_fundhistory = $wpdb->prefix."fundhistory";
		
		if(empty($params))
		{
			return array();
		}
		$where = array();
		foreach($params as $k => $v)
		{
			if(is_string($v) && $v == '')
			{
				$where[] = $k;
			}
			else 
			{
				$where[] = $k."= '".$v."'";
			}
		}
		$where = "WHERE ".implode(' AND ', $where);
		
		$sql = "SELECT * "
             . "FROM $table_fundhistory "
             . $where;
		return $wpdb->get_row($sql, ARRAY_A);
	}

    public function getAllFundhistory($aConds = null, $sSort = 'fundshistoryID DESC', $iPage = '', $iLimit = '')
    {   
        global $wpdb;
        $table_fundhistory = $wpdb->prefix."fundhistory";
        // $aConds .= 'userID = '.(int)VIC_GetUserId();

        $sCond = $aConds != null ? "WHERE ".$aConds : '';
        $sql = "SELECT site_profit, DATE_FORMAT(date, '%M') as month "
             . "FROM $table_fundhistory "
             . $sCond." ";
        $aRows = $wpdb->get_results($sql);

        $newArr = array();
        foreach ($aRows as $key => $value) {
            $newArr[$value->month] += $value->site_profit;
        }
        return $newArr;
    }
    
    public function parseFunhistoryData($aDatas = null)
    {
		// get list league
        $listLeagueID = array();
        foreach($aDatas as $item)
        {
            $listLeagueID[] = $item['leagueID'];

        }
        if($listLeagueID)
        {
            require_once'victorious.php';
            $victorious = new VIC_Victorious();
            $aListNameLeague = $victorious->getLeagueName($listLeagueID);
        }
		
        if($aDatas != null)
        {
            $balanceType = new VIC_BalanceType();
            foreach($aDatas as $k => $aData)
            {
				$nameContest = '--';
                if(isset($aListNameLeague[$aData['leagueID']]))
				{
                    $nameContest = $aListNameLeague[$aData['leagueID']];
				}
                $aDatas[$k]['name_contest'] = $nameContest;
                if($aData['operation'] == 'ADD')
                {
                    $aDatas[$k]['operation_sign'] = "+";
                }
                else if($aData['operation'] == 'DEDUCT')
                {
                    $aDatas[$k]['operation_sign'] = "-";
                }
                if($aData['type'] == 'MAKE_BET')
                {
                    $aDatas[$k]['type'] = "ENTRY FEE";
                }

                //balance type
                $balance_type = !empty($aData['balance_type_id']) ? $balanceType->getBalanceTypeDetail($aData['balance_type_id']) : $balanceType->getBalanceTypeDetail(VICTORIOUS_DEFAULT_BALANCE_TYPE_ID);
                $aDatas[$k]['balance_type'] = $balance_type;
            }
        }
        return $aDatas;
    }
    
    public function addFundhistory($params)
    {
        global $wpdb;
        $prize = !empty($params['amount']) ? $params['amount'] : 0;
        if($prize > 0)
        {
            $userID = (int)VIC_GetUserId();
            if(!empty($params['userID']))
            {
                $userID = $params['userID'];
            }
            
            //site profit
            if($params['type'] == 'MAKE_BET' && $params['operation'] == 'DEDUCT' && !empty($params['leagueID']) && $params['leagueID'] > 0)
            {
                $payout_percentage = get_option('victorious_winner_percent');
                $params['site_profit'] = $prize * (100 - $payout_percentage) / 100;
            }
            
            $values = array(
                'userID' => $userID, 
                'amount' => $prize,
                'operation' => $params['operation'],
                'type' => $params['type'],
                'new_balance' => !empty($params['new_balance']) ? $params['new_balance'] : 0,
                'gateway' => !empty($params['gateway']) ? $params['gateway'] : "",
                'reason' => !empty($params['reason']) ? $params['reason'] : "",
                'cash_to_credit' => !empty($params['cash_to_credit']) ? $params['cash_to_credit'] : "",
                'leagueID' => !empty($params['leagueID']) ? $params['leagueID'] : 0,
                'entry_number' => !empty($params['entry_number']) ? $params['entry_number'] : 1,
                'date' => date('Y-m-d H:i:s'),
                'site_profit' => !empty($params['site_profit']) ? $params['site_profit'] : 0,
                'status' => !empty($params['status']) ? $params['status'] : "initial",
                'transactionID' => !empty($params['transactionID']) ? $params['transactionID'] : "",
                'is_checkout' => !empty($params['is_checkout']) ? $params['is_checkout'] : 0,
                'withdrawlID' => !empty($params['withdrawlID']) ? $params['withdrawlID'] : "",
                'balance_type_id' => !empty($params['balance_type_id']) ? $params['balance_type_id'] : "",
                'verification_code' => !empty($params['verification_code']) ? $params['verification_code'] : ""
            );
            $table_name = $wpdb->prefix.'fundhistory';
            $wpdb->insert($table_name, $values);
            return $wpdb->insert_id;
        }
        return 0;
    }
    
    public function updateFundhistory($iId, $aValues, $user_id = null, $status = '')
    {
        global $wpdb;
        $iUserId = VIC_GetUserId();
        if((int)$user_id > 0)
        {
            $iUserId = $user_id;
        }
        $user = $this->getUserData($iUserId);
        $aValues['new_balance'] = $user['balance'];
        $aValues['status'] = $status;
        return $wpdb->update($wpdb->prefix.'fundhistory', $aValues, array('fundshistoryID' => (int)$iId));
    }
    
    public function updateFundhistoryVerificationCode($id, $code)
    {
        global $wpdb;
        $params = array(
            'verification_code' => $code
        );
        return $wpdb->update($wpdb->prefix.'fundhistory', $params, array('fundshistoryID' => (int)$id));
    }
    
    public function updateFundhistoryByWithdrawl($id, $params)
    {
        global $wpdb;
        if($params == null)
        {
            return false;
        }
        return $wpdb->update($wpdb->prefix.'fundhistory', $params, array('withdrawlID' => (int)$id));
    }
    
    public function deleteFundhistory($params)
    {
        global $wpdb;
        $where = array();
        if(!empty($params['id']))
        {
            $where['fundshistoryID'] = $params['id'];
        }
        if(!empty($params['league_id']))
        {
            $where['leagueID'] = $params['league_id'];
        }
        if(!empty($params['operation']))
        {
            $where['operation'] = $params['operation'];
        }
        if(!empty($params['type']))
        {
            $where['type'] = $params['type'];
        }
        if($where == null)
        {
            return;
        }
        return $wpdb->delete($wpdb->prefix.'fundhistory', $where);
    }
    
    public function getFundhistoryList($leagueID)
    {
        global $wpdb;
		$table_name = $wpdb->prefix.'fundhistory';
		$sql = "SELECT * "
             . "FROM $table_name "
             . "WHERE leagueID = ".$leagueID;
		$funds = $wpdb->get_results($sql);
        $data = array();
        if($funds != null)
        {
            $funds = json_decode(json_encode($funds), true);
            foreach($funds as $fund){
                $data[$fund['leagueID']."_".$fund['userID']."_".$fund['entry_number']."_".$fund['operation']."_".$fund['type']] = $fund;
            }
        }
        return $data;
	}
    
    ###########################
	#
	#       WITHDRAWLS
	#
	###########################
    public function isAllowWithdraw($amount = 0, $user_id = null, $balance_type_id)
    {
        $user_balance = $this->getUserBalance($user_id, $balance_type_id);
        if($user_balance['balance'] >= $amount)
        {
            return true;
        }
        return false;
    }
    
    public function getWithdraw($iId = null)
    {
        global $wpdb;
        $table_name = $wpdb->prefix.'withdrawls';
        $sCond = "WHERE userID = ".VIC_GetUserId();
        if((int)$iId > 0)
        {
            $sCond = "WHERE withdrawlID = ".$iId;
        }
        $sql = "SELECT *, DATE_FORMAT(requestDate, '%Y-%m-%d') as requestDate, DATE_FORMAT(processedDate, '%Y-%m-%d') as processedDate "
             . "FROM $table_name "
             . $sCond." "
             . "ORDER BY withdrawlID DESC ";
        if((int)$iId > 0)
        {
            $data = $wpdb->get_row($sql);
        }
        else
        {
            $data = $wpdb->get_results($sql);
        }
        $data = json_decode(json_encode($data), true);
        $data = $this->parseWithdrawData($data);
        return $data;
    }
    
    public function getListWithdraw($aConds, $sSort = 'withdrawlID DESC', $iPage = '', $iLimit = '')
	{	
        global $wpdb;
        $table_name = $wpdb->prefix."withdrawls";
        $aConds .= 'userID = '.(int)VIC_GetUserId();
        $sCond = $aConds != null ? "WHERE ".$aConds : '';
        $sql = "SELECT COUNT(*) "
             . "FROM $table_name "
             . $sCond;
        $iCnt = $wpdb->get_var($sql);

        $sCond = $aConds != null ? "WHERE ".$aConds : '';
        $sql = "SELECT *, DATE_FORMAT(requestDate, '%Y-%m-%d') as requestDate, DATE_FORMAT(processedDate, '%Y-%m-%d') as processedDate "
             . "FROM $table_name "
             . $sCond." "
             . "ORDER BY $sSort "
             . "limit $iPage, $iLimit ";
        $aRows = $wpdb->get_results($sql);
        $aRows = json_decode(json_encode($aRows), true);
        
        return array($iCnt, $aRows);
	}
    
    public function addWithdraw($data = array())
    {
        global $wpdb;
        $userID = VIC_GetUserId();
        if(!empty($data['userID']))
        {
            $userID = $data['userID'];
        }
        $amount = !empty($data['amount']) ? $data['amount'] : 0;
        $gateway = $data['gateway'];
        $values = array(
            'userID' => $userID, 
            'amount' => $amount,
            'real_amount' => $this->changeCashToCredit($amount),
            'credit_to_cash' => get_option('victorious_credit_to_cash'), 
            'new_balance' => !empty($data['new_balance']) ? $data['new_balance'] : 0,
            'reason' => !empty($data['reason']) ? $data['reason'] : "",
            'requestDate' => date('Y-m-d H:i:s'),
            'gateway' => $gateway,
            'balance_type_id' => !empty($data['balance_type_id']) ? $data['balance_type_id'] : ""
        );
        if($gateway == VICTORIOUS_GATEWAY_DFSCOIN && !empty($data['dfscoin_wallet_address'])){
            $values['dfscoin_wallet_address'] = $data['dfscoin_wallet_address'];
        }
        $wpdb->insert($wpdb->prefix.'withdrawls', $values);
        return $wpdb->insert_id;
    }
    
    public function updateWithdraw($iId, $aValues)
    {
        global $wpdb;
        return $wpdb->update($wpdb->prefix."withdrawls", $aValues, array('withdrawlID' => (int)$iId));
    }
    
    public function getPaymentInfoByUserID($user_id){
         global $wpdb;
        $sCond = "WHERE user_id = ".$user_id;
        $table_name = $wpdb->prefix."user_payment";
        $sql = "SELECT * "
             . "FROM $table_name "
             . $sCond;
        $aData = $wpdb->get_row($sql);

        if(count($aData) == 1)
        {
            return $aData;
        }
        return false;
    }
    
    public function moneywaveWithdrawl($data, $withdrawal_id)
    {
        $moneywave = new Moneywave(get_option('victorious_moneywave_api_key'), get_option('victorious_moneywave_secret'));
        $password = get_option('victorious_moneywave_wallet_password');
        $senderName = (!empty($data['firstname']) ? $data['firstname'] : '').' '.(!empty($data['lastname']) ? $data['lastname'] : '');
        $tran = new \HngX\Moneywave\Transactions\WalletToAccountTransaction($moneywave, $password);
        $tran->setDetails(array(
            "lock" => $password,
            "amount" => !empty($data['amount']) ? $data['amount'] : '',
            "bankcode" => !empty($data['sender_bank']) ? $data['sender_bank'] : '',
            "accountNumber" => !empty($data['sender_account_number']) ? $data['sender_account_number'] : '',
            "currency" => "NGN",
            "senderName" => !empty($data['sender_name']) ? $data['sender_name'] : '',
            "ref" => "moneywave".$withdrawal_id."_".rand(0, 1000)
        ))->dispatch();
        $response = $tran->getResponse();
        
        $fail_message = __('Failed!!','victorious');
        if($response != null)
        {
            if($response['status'] == "success")
            {
                return array(
                    'success' => 1,
                    'data' => array(
                        'transactionID' => $response['data']['data']['uniquereference']
                    )
                );
            }
            $fail_message = $response['message'];
        }
        return array(
            'success' => 0,
            'msg'=> $fail_message
        );
    }
    
    public function getBalanceTypeByGateway($gateway = '')
    {
        return VICTORIOUS_BALANCE_TYPE_DEFAULT;
    }
    
    public function createVerificationCode($id)
    {
        return md5($id.date('Y-m-d H:i:s'));
    }
	
	public function isMyVerificationCode($code)
	{
		if(empty(VIC_GetUserId()))
		{
			return VIC_GetUserId();
		}
		$data = $this->getFundhistoryDetail(array(
            'userID' => VIC_GetUserId(),
            'verification_code' => $code
        ));
		return $data != null ? true : false;
	}
    
    public function gamebleConfirmPayment($MerchantTransactionID)
    {
        require_once(VICTORIOUS__PLUGIN_DIR_MODEL."gamble.php");
        $gamble = new VIC_GatewayGamble();
        $gamble->setCustomerId(VIC_GetUserId());
        $params = $gamble->paymentDetail(array(
            'MerchantTransactionID' => $MerchantTransactionID
        ));
        
        $fundhistory = $this->getFundhistoryDetail(array(
            "verification_code" => $params['MerchantTransactionID']
        ));
        if(empty($fundhistory))
        {
            //$gamble->write_log("fundhistory not found");
            return false;
        }
        
        if($params['StatusCode'] == 0)
        {
            //$gamble->write_log("fund history data");
			//$gamble->write_log($fundhistory);
            
            //withdraw
            if($fundhistory['type'] == "WITHDRAW")
            {
                //$gamble->write_log("start updating data for withdrawal");
                $status = $params['TransactionStatusMessage'] != "Complete" ? "PENDING" : "APPROVED";
                $this->updateWithdraw($fundhistory['withdrawlID'], array(
                    'status' => $status
                ));
                //$gamble->write_log("Successfully withdrawal");
				return true;
            }
            
            //add fund
            if($params['TransactionStatusMessage'] != "Complete")
            {
                //$gamble->write_log("Transaction status: ".$params['TransactionStatusMessage']);
                return false;
            }
			//$gamble->write_log("start updating user balance");
			if ($this->updateUserBalance((float) $fundhistory['amount'], false, 0, $fundhistory['userID']))
			{
				$fund_data = array(
					'transactionID' => '', 
					'is_checkout' => 1
				);
				$this->updateFundhistory($fundhistory['fundshistoryID'], $fund_data, $fundhistory['userID'], 'completed');
				return true;
			}
        }
        else
        {
            $this->updateFundhistory($fundhistory['fundshistoryID'], array('is_checkout' => 1), $fundhistory['userID'], 'failed');
            return false;
        }
    }

    public function paySimpleCheckExistingCustomer(){
        require_once(VICTORIOUS__PLUGIN_DIR_MODEL."paysimple.php");
        $paysimple = new VIC_PaySimple();

        //get user
        $user_id = (int)VIC_GetUserId();
        $user = $this->getUserData($user_id);
        if(empty($user['pay_simple_acc_id'])){
            return false;
        }

        //check customer
        $customer = $paysimple->findCustomerById($user['pay_simple_acc_id']);
        if($customer === false){
            return false;
        }
        return true;
    }

    public function paySimpleCreateCustomer($first_name, $last_name, $address, $city, $zip_code){
        global $wpdb;
        require_once(VICTORIOUS__PLUGIN_DIR_MODEL."paysimple.php");
        $paysimple = new VIC_PaySimple();

        //get user
        $user_id = (int)VIC_GetUserId();
        $user = $this->getUserData($user_id);

        //create customer
        $data = array(
            array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'street_address_1' => $address,
                'street_address_2' => '',
                'city' => $city,
                'state' => '',
                'country' => '',
                'zip' => $zip_code,
                'email' => $user['email'],
                'phone' => '',
            )
        );
        $customer = $paysimple->createCustomer($data);
        if($customer === false){
            return false;
        }

        //save customer id
        $data = array(
            'pay_simple_acc_id' => $customer['Id']
        );
        $wpdb->update($wpdb->prefix."user_extended", $data, array('user_id' => $user_id));
        return true;
    }

    public function paySimplePay($amount, $order_id, $card_number, $cvv, $expiration_date)
    {
        global $wpdb;
        require_once(VICTORIOUS__PLUGIN_DIR_MODEL."paysimple.php");
        $paysimple = new VIC_PaySimple();

        //get user
        $user_id = (int)VIC_GetUserId();
        $user = $this->getUserData($user_id);
        $customer_id = $user['pay_simple_acc_id'];

        //customer
        $customer = $paysimple->findCustomerById($customer_id);

        //check created credit card
        $data = array(
            'card_number' => $card_number,
            'card_expire' => $expiration_date,
            'Customer' => $customer
        );
        $card = $paysimple->addCreditCardAccount($data);

        //pay
        if($card !== false){
            $data = array(
                'AccountId' => $card['Id'],
                'total' => $amount,
                'id' => $order_id,
                'card_sec' => $cvv,
                'paymentSubType' => '',
                'description' => '',
                'Customer' => $customer
            );
            $payment = $paysimple->createPayment($data);
        }
        $success = false;
        if($payment !== false){
            $success = true;
        }

        return array(
            'success' => $success,
            'transactionID' => !empty($payment['Id']) ? $payment['Id'] : ''
        );
    }

    public function getUserBalanceList($user_id)
    {
        global $wpdb;
        $table = VIC_GetTableName('balances');
        $sCond = "WHERE user_id = ".(int)$user_id." ";
        $sql = "SELECT * "
            . "FROM $table "
            . $sCond;
        $aRows = $wpdb->get_results($sql);
        $aRows = json_decode(json_encode($aRows), true);
        if($aRows == null){
            return $aRows;
        }

        return $this->parseUserBalanceData($aRows);
    }

    public function getUserBalance($user_id, $balance_type_id)
    {
        global $wpdb;
        $balance_type_id = $balance_type_id > 0 ? $balance_type_id : VICTORIOUS_DEFAULT_BALANCE_TYPE_ID;
        $table = VIC_GetTableName('balances');
        $sCond = "WHERE user_id = ".(int)$user_id." AND balance_type_id = ".$balance_type_id." ";
        $sql = "SELECT * "
            . "FROM $table "
            . $sCond;
        $aRows = $wpdb->get_results($sql);
        $aRows = json_decode(json_encode($aRows), true);
        $aRows = $this->parseUserBalanceData($aRows);
        return isset($aRows[0]) ? $aRows[0] : array();
    }

    private function parseUserBalanceData($aRows){
        if($aRows == null){
            return $aRows;
        }
        $mBalanceType = new VIC_BalanceType();
        foreach($aRows as $k => $aRow){
            $aRows[$k]['balance_type'] = $mBalanceType->getBalanceTypeDetail($aRow['balance_type_id']);
        }
        return $aRows;
    }

    public function parseWithdrawData($aDatas = null)
    {
        if($aDatas == null){
            return $aDatas;
        }
        $single = false;
        if(!isset($aDatas[0])){
            $single = true;
            $aDatas = array($aDatas);
        }

        $balanceType = new VIC_BalanceType();
        foreach($aDatas as $k => $aData)
        {
            //balance type
            $balance_type = !empty($aData['balance_type_id']) ? $balanceType->getBalanceTypeDetail($aData['balance_type_id']) : $balanceType->getBalanceTypeDetail(VICTORIOUS_DEFAULT_BALANCE_TYPE_ID);
            $aDatas[$k]['balance_type'] = $balance_type;
        }
        return $single ? $aDatas[0] : $aDatas;
    }

    public function getDisplayUserBalance($user_id = null, $default_only = false){
        if($user_id == null)
        {
            $user_id = (int)get_current_user_id();
        }
        if((int)$user_id > 0)
        {
            $balanceType = new VIC_BalanceType();
            $payment = new VIC_Payment();
            $balance_types = $balanceType->getBalanceTypeList(array(
                'default_only' => $default_only
            ));
            $user_balances = $payment->getUserBalanceList($user_id);
            $user_balances = $payment->groupArrayByKey($user_balances, array('balance_type_id'));

            $balance_content = array();
            if($balance_types != null){
                foreach($balance_types as $balance_type){
                    $code = $balance_type['currency_code'].'|'.$balance_type['symbol'];
                    $balance = isset($user_balances[$balance_type['id']]) ? $user_balances[$balance_type['id']]['balance'] : 0;
                    $balance_content[] = VIC_FormatMoney($balance, $code);
                }
            }
            $balance_content = implode(' - ', $balance_content);
            return $balance_content;
        }
        return VIC_FormatMoney(0);
    }

    public function initUserBalance($user_id)
    {
        global $wpdb;
        $balance_type_id = VICTORIOUS_DEFAULT_BALANCE_TYPE_ID;
        $user_balance = $this->getUserBalance($user_id, $balance_type_id);

        if($user_balance != null)
        {
            return;
        }

        $values = array(
            'user_id' => $user_id,
            'balance_type_id' => $balance_type_id,
            'balance' => 0
        );
        $table_name = VIC_GetTableName('balances');
        return $wpdb->insert($table_name, $values);
    }
}
?>