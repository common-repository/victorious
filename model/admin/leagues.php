<?php
class VIC_Leagues extends VIC_Model
{
    public function isLeagueExist($leagueID)
    {
        if($this->sendRequest("isLeagueExist", array('leagueID' => $leagueID)) == 1)
        {
            return true;
        }
        return false;
    }
    
    public function getLeaguesByFilter($aConds, $sSort = 'leagueID DESC', $iPage = '', $iLimit = '')
    {
        $params = array('aConds' => $aConds, 'sSort' => $sSort, 'iPage' => $iPage, 'iLimit' => $iLimit);
        $data = $this->send("leaguesByFilter", $params, true);
        return array($data['iCnt'], $data['aRows'], $data['allow_export_pick']);
    }
    
    public function parseLeagueData($datas = null)
    {
        if($datas != null)
        {
            foreach($datas as $k => $data)
            {
                $user = get_userdata($data['creator_userID']);
                $datas[$k]['creator'] = $user != null ? $user->user_login : null;
            }
        }
        return $datas;
    }

    public function delete($leagueID)
    {
        $result = $this->sendRequest("deleteLeague", array('leagueID' => $leagueID));
        if($result)
        {
            return true;
        }
        return false;
    }

    public function getLeagueByYear($year = 0)
    {
        $params = array();
        if((int)$year > 0)
        {
            $params['year'] = $year;
        }
        return $this->sendRequest("getLeagueByYear", $params);
    }
    
    public function setFeatureContest($id, $is_feature, $feature_image)
    {
        $params = array(
            'id' => $id,
            'is_feature' => $is_feature,
            'feature_image' => $feature_image
        );
        return $this->sendRequest('setFeatureContest', $params);
    }
}
?>