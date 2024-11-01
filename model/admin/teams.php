<?php
class VIC_Teams extends VIC_Model
{
    public function isTeamExist($teamID)
    {
        if($this->sendRequest("isTeamExist", array('teamID' => $teamID)) == 1)
        {
            return true;
        }
        return false;
    }
    
	public function getTeams($teamID = null, $orgsID = null, $all = false, $playerdraft = false)
    {
        $params = array();
        if($teamID != null)
        {
            $params['teamID'] = $teamID;
        }
        if((int)$orgsID > 0)
        {
            $params['orgsID'] = (int)$orgsID;
        }
        if($all)
        {
            $params['all'] = true;
        }
        if($playerdraft)
        {
            $params['playerdraft'] = true;
        }
        $data = $this->sendRequest("teams", $params);
        if(!is_array($teamID) && (int)$teamID > 0)
        {
            $data = $this->parseTeamsData($data);
            $data = $data[0];
        }
        return $data;
    }
    
    public function getTeamImageName($teamID)
    {
        $data = $this->getTeams($teamID);
        if($data != null)
        {
            return $data['image'];
        }
        return null;
    }
    
    public function getTeamsByFilter($sport_id, $aConds, $sSort = 'teamID DESC', $iPage = '', $iLimit = '')
    {
        $params = array('sport_id' => $sport_id, 'aConds' => $aConds, 'sSort' => $sSort, 'iPage' => $iPage, 'iLimit' => $iLimit);
        $data = $this->sendRequest("teamsByFilter", $params);
        return array($data['iCnt'], $data['aRows']);
    }
    
    public function getTeamsBackgroundByFilter($aConds, $sSort = 'teamID DESC', $iPage = '', $iLimit = '')
    {  
        $params = array('keyword' => $aConds, 'sSort' => $sSort, 'iPage' => $iPage, 'iLimit' => $iLimit);
      
        $data = $this->sendRequest("getTeamsBackgroundByFilter", $params,true,true);
        return array($data['iCnt'], $data['aRows']);
    }
    
    public function parseBackgroundTeamsData($aData){
        foreach($aData as $k=>$team){

             $f_img_path = VICTORIOUS__PLUGIN_DIR . 'assets/teams/' . $team['teamID'];
             $images = glob($f_img_path.'.*');
             $image_path = '';
             if($images){
                 $image_path = $images[0];
                 $image_path = explode("/",$image_path);
                 $image_path = $image_path[count($image_path)-1];
                 $image_path = VICTORIOUS__PLUGIN_URL.'assets/teams/' . $image_path;
             }
             $aData[$k]['background_path'] = $image_path;
           
        }
          return $aData;
    }
    public function getTeamName($teamID, $all = false)
    {
        $data = $this->getTeams($teamID, null, $all);
        return $data['name'];
    }
    
    public function parseTeamsData($data = null)
    {
        if($data != null)
        {
            foreach($data as $k => $v)
            {
                if($v['siteID'] > 0)
                {
                    if(substr($v['image'], -1) != '/'){
                        $data[$k]['full_image_path'] = VICTORIOUS_IMAGE_URL.$this->replaceSuffix($v['image']);
                    }else{
                        $data[$k]['full_image_path'] = 'http://0.gravatar.com/avatar/0115cda8dbceda5ee5d0ce5e953b1313?s=46&d=mm&r=g';
                    }
                }
                else 
                {
                    if(substr($v['image'], -1) != '/'){
                        $data[$k]['full_image_path'] = $this->replaceSuffix($v['image']);
                    }else{
                        $data[$k]['full_image_path'] = 'http://0.gravatar.com/avatar/0115cda8dbceda5ee5d0ce5e953b1313?s=46&d=mm&r=g';
                    }
                }
            }
        }
        return $data;
    }

    //////////////////////////////////////////add, update, delete//////////////////////////////////////////
    public function add($aVals)
    {
        $teamID = $this->sendRequest("addTeams", $this->parseTeamsDataForModify($aVals));
        
        //upload new image
        $image = $this->uploadImage();
        $this->updateTeamsImage($teamID, $image);
        
        if($teamID > 0)
        {
            return true;
        }
        return false;
    }

    public function update($aVals)
    {
        $result = $this->sendRequest("updateTeams", $this->parseTeamsDataForModify($aVals, true));
        
        if (isset($_FILES['image']['name']) && ($_FILES['image']['name'] != ''))
        {
            //get current image name
            $sFileName = $this->getTeamImageName($aVals['teamID']);

            //delete old image
            $this->deleteImage($sFileName);
            
            //upload new image
            $image = $this->uploadImage();
            $this->updateTeamsImage($aVals['teamID'], $image);
        }
        return $result;
    }
    
    public function updateTeamsImage($teamID, $image)
    {
        return $this->sendRequest("updateTeams", array('teamID' => (int)$teamID, 'image' => $image));
    }
    
    public function updateTeam($data)
    {
        return $this->sendRequest("updateTeams", $data);
    }
    
    private function parseTeamsDataForModify($aVals, $isUpdate = false)
    {
        $data = array(
            'organization_id' => $aVals['organization'],
            'name' => $aVals['name'],
            'nickName' => $aVals['nickName'],
            'homepageLink' => $aVals['homepageLink'],
            'cityname' => $aVals['cityname'],
            'teamname' => $aVals['teamname'],
            'record' => $aVals['record'],
            'salary' => str_replace(',', '', $aVals['salary']),
        );
        if($isUpdate)
        {
            $data['teamID'] = $aVals['teamID'];
        }
        return $data;
    }
    
    public function delete($teamID)
    {
        $sFileName = $this->getTeamImageName($teamID);
        $result = $this->sendRequest("deleteTeams", array('teamID' => $teamID));
        if($result)
        {
            $this->deleteImage($sFileName);
            return true;
        }
        return false;
    }
}
?>