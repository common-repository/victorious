<?php
class VIC_User extends VIC_Model
{
    public function __construct() 
    {
        global $wpdb;
        $this->api_token = get_option('victorious_api_token');
        $this->api_url = get_option('victorious_api_url_admin');
        $this->_sTable = $wpdb->prefix.'users';
        $this->_sTableUserExtended = $wpdb->prefix.'user_extended';
        $this->_sTableWithdrawls = $wpdb->prefix.'withdrawls';	
    }
    
    public function getUsers($aConds, $sSort = 'u.ID ASC', $iPage = '', $iLimit = '')
	{	
        if($aConds != null && is_array($aConds))
        {
            $aConds = implode('AND', $aConds);
        }
        global $wpdb;
        $sCond = $aConds != null ? "WHERE ".$aConds : '';
        $sql = "SELECT COUNT(*) "
             . "FROM $this->_sTable "
             . $sCond;
        $iCnt = $wpdb->get_var($sql);
        
        $sql = "SELECT u.*, IFNULL(ue.balance, 0.00) as balance "
             . "FROM $this->_sTable u "
             . "LEFT JOIN $this->_sTableUserExtended ue ON ue.user_id = u.ID "
             . $sCond." "
             . "ORDER BY $sSort "
             . "limit $iPage, $iLimit";
        $aRows = $wpdb->get_results($sql);
        $aRows = json_decode(json_encode($aRows), true);
        
		return array($iCnt, $aRows);
	}
    
    public function getUser($userID)
    {
        global $wpdb;
        $sCond = "WHERE u.ID = ".(int)$userID;
        $sql = "SELECT u.*, IFNULL(ue.balance, 0.00) as balance "
             . "FROM $this->_sTable u "
             . "LEFT JOIN $this->_sTableUserExtended ue ON ue.user_id = u.ID "
             . $sCond;
        $aRows = $wpdb->get_results($sql);
        $aRows = json_decode(json_encode($aRows), true);
        $aRows = $this->parseUsersData($aRows);
        return isset($aRows[0]) ? $aRows[0] : null;
    }
    
    public function getUsersWithdrawls($aConds, $sSort = 'w.withdrawlID DESC', $iPage = '', $iLimit = '')
	{	
        if($aConds != null && is_array($aConds))
        {
            $aConds = implode('AND', $aConds);
        }
        global $wpdb;
        $sCond = $aConds != null ? "WHERE ".$aConds : '';
        $sql = "SELECT COUNT(*) "
             . "FROM $this->_sTable u "
             . "INNER JOIN $this->_sTableWithdrawls w ON u.ID = w.userID "
             . $sCond;
        $iCnt = $wpdb->get_var($sql);

        $sql = "SELECT *, DATE_FORMAT(w.requestDate, '%Y-%m-%d') as requestDate "
             . "FROM $this->_sTable u "
             . "INNER JOIN $this->_sTableWithdrawls w ON u.ID = w.userID "
             . $sCond." "
             . "ORDER BY $sSort "
             . "limit $iPage, $iLimit ";
        $aRows = $wpdb->get_results($sql);
        $aRows = json_decode(json_encode($aRows), true);
        $aRows = $this->parseWithdrawData($aRows);
		return array($iCnt, $aRows);
	}
    
    public function parseUsersData($aDatas = null)
    {
        if($aDatas != null)
        {
            foreach($aDatas as $k => $aData)
            {
                $aDatas[$k]['payment_request_pending'] = $this->getWithdrawlsTotal($aData['ID']);
            }
        }
        return $aDatas;
    }

    public function parseWithdrawData($aDatas = null)
    {
        if($aDatas == null){
            return $aDatas;
        }
        $balanceType = new VIC_BalanceType();
        foreach($aDatas as $k => $aData)
        {
            //balance type
            $balance_type = !empty($aData['balance_type_id']) ? $balanceType->getBalanceTypeDetail($aData['balance_type_id']) : $balanceType->getBalanceTypeDetail(VICTORIOUS_DEFAULT_BALANCE_TYPE_ID);
            $aDatas[$k]['balance_type'] = $balance_type;
        }
        return $aDatas;
    }
    
    public function getWithdrawlsTotal($userID, $status = "NEW")
    {	
        global $wpdb;
        $sCond = "WHERE status = '$status' AND userID = $userID";
        $sql = "SELECT SUM(amount) as amount "
             . "FROM $this->_sTableWithdrawls "
             . $sCond;
        $aData = $wpdb->get_row($sql);
        return $aData->amount;
    }
    
    public function loadUserMultiEntries()
    {
        $this->method = "GET";
        $result = $this->sendRequest("loadUserMultiEntries");
        if(!empty($result['users']))
        {
            foreach($result['users'] as $k => $user)
            {
                $result['users'][$k]['info'] = $this->getUser($user['user_id']);
            }
        }
        return $result;
    }
    
    public function saveUserMultiEntries($params)
    {
        return $this->sendRequest("saveUserMultiEntries", $params);
    }
}
?>