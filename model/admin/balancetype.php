<?php
class VIC_BalanceType extends VIC_Model
{
    public function __construct() 
    {
        global $wpdb;
        $this->api_token = get_option('victorious_api_token');
        $this->api_url = get_option('victorious_api_url_admin');
        $this->_sTable = VIC_GetTableName('balance_types');
    }
    
    public function getBalanceTypes($aConds, $sSort = 'id ASC', $iPage = '', $iLimit = '')
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
        
        $sql = "SELECT * "
             . "FROM $this->_sTable "
             . $sCond." "
             . "ORDER BY $sSort "
             . "limit $iPage, $iLimit";
        $aRows = $wpdb->get_results($sql);
        $aRows = json_decode(json_encode($aRows), true);
        $aRows = $this->parseBalanceTypeData($aRows);
        
		return array($iCnt, $aRows);
	}
    
    public function getBalanceTypeDetail($id)
    {
        global $wpdb;
        $sCond = "WHERE id = ".(int)$id;
        $sql = "SELECT * "
             . "FROM $this->_sTable "
             . $sCond;
        $aRows = $wpdb->get_results($sql);
        $aRows = json_decode(json_encode($aRows), true);
        $aRows = $this->parseBalanceTypeData($aRows);
        return isset($aRows[0]) ? $aRows[0] : null;
    }

    public function save($data){
        global $wpdb;
        $params = array(
            'name' => $data['name'],
            'currency_code' => $data['currency_code'],
            'currency_position' => $data['currency_position'],
            'symbol' => $data['symbol'],
            'info' => $data['info'],
            'enabled' => isset($data['enabled']) ? $data['enabled'] : 0,
            'ordering' => 2
        );
        if(isset($data['image'])){
            $params['image'] = $data['image'];
        }
        if(!empty($data['id'])){
            return $wpdb->update($this->_sTable, $params, array('id' => (int)$data['id']));
        }
        return $wpdb->insert($this->_sTable, $params);
    }

    public function saveCoreItem($data){
        global $wpdb;
        $params = array(
            'name' => $data['name'],
            'enabled' => isset($data['enabled']) ? $data['enabled'] : 0
        );
        return $wpdb->update($this->_sTable, $params, array('id' => (int)$data['id']));
    }

    public function getBalanceTypeList($params = array()){
        global $wpdb;
        $cond = array();
        $where = '';
        if(!empty($params['default_only'])){
            $cond[] = 'id = '.VICTORIOUS_DEFAULT_BALANCE_TYPE_ID;
        }
        if(isset($params['enabled'])){
            $cond[] = 'enabled = '.$params['enabled'];
        }
        if(isset($params['is_core'])){
            $cond[] = 'is_core = '.$params['is_core'];
        }
        if($cond != null){
            $where = 'WHERE '.implode(' AND ', $cond);
        }

        $sql = "SELECT * "
            . "FROM $this->_sTable "
            . " $where "
            . "ORDER BY ordering asc ";
        $aRows = $wpdb->get_results($sql);
        $aRows = json_decode(json_encode($aRows), true);
        return $this->parseBalanceTypeData($aRows);
    }

    public function delete($id){
        global $wpdb;
        $wpdb->delete($this->_sTable, array('id' => $id));
    }

    private function parseBalanceTypeData($aRows){
        if($aRows == null){
            return $aRows;
        }

        $upload_dir = wp_upload_dir();
        foreach($aRows as $k => $aRow){
            $aRows[$k]['image_url'] = $upload_dir['baseurl'].$aRow['image'];
            $aRows[$k]['currency_code_symbol'] = $aRow['currency_code'].'|'.$aRow['symbol'];
        }
        return $aRows;
    }
}
?>