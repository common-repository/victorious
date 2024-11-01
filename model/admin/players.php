<?php
class VIC_Players extends VIC_Model
{
    public function isPlayersExist($id)
    {
        if($this->sendRequest("isPlayersExist", array('id' => $id)) == 1)
        {
            return true;
        }
        return false;
    }
    
    public function getPlayersImageName($id)
    {
        $data = $this->getPlayers($id);
        if($data != null)
        {
            return $data['image'];
        }
        return null;
    }
    
	public function getPlayers($id = null, $teamID = null, $all = false)
    {
        $params = array();
        if($id != null)
        {
            $params['id'] = $id;
        }
        if($teamID != null)
        {
            $params['teamID'] = $teamID;
        }
        if($all)
        {
            $params['all'] = true;
        }
        return $this->sendRequest("players", $params);
    }

    public function getPlayersByFilter($sport_id, $aConds, $sSort = 'id DESC', $iPage = '', $iLimit = '')
    {
        $params = array('sport_id' => $sport_id, 'aConds' => $aConds, 'sSort' => $sSort, 'iPage' => $iPage, 'iLimit' => $iLimit);
        $data = $this->sendRequest("playersByFilter", $params);
        return array($data['iCnt'], $data['aRows']);
    }
    
    public function getAddPlayer($player_id, $sport_id)
    {
        return $this->sendRequest("addPlayerFormData", array(
            "player_id" => $player_id,
            "sport_id" => $sport_id
        ));
    }
    
    public function getPlayersName($id, $all = false)
    {
        $data = $this->getPlayers($id, null, $all);
        return $data['name'];
    }
    
    public function getIndicator()
    {
        return $this->sendRequest("indicator");
    }

    public function parsePlayersData($data = null, $is_arr = true)
    {
        if($data != null && $is_arr)
        {
            foreach($data as $k => $v)
            {
                if($v['siteID'] > 0)
                {
                    if(substr($v['image'], -1) != '/'){
                        $data[$k]['full_image_path'] = VICTORIOUS_IMAGE_URL.$this->replaceSuffix($v['image']);
                        $data[$k]['full_image_path_org'] = VICTORIOUS_IMAGE_URL.$this->replaceSuffix($v['image'], '');
                    }else{
                        $data[$k]['full_image_path'] = 'http://0.gravatar.com/avatar/0115cda8dbceda5ee5d0ce5e953b1313?s=46&d=mm&r=g';
                        $data[$k]['full_image_path_org'] = 'http://0.gravatar.com/avatar/0115cda8dbceda5ee5d0ce5e953b1313?s=46&d=mm&r=g';
                    }
                }
                else 
                {
                    if(substr($v['image'], -1) != '/'){
                        $data[$k]['full_image_path'] = $this->replaceSuffix($v['image']);
                        $data[$k]['full_image_path_org'] = $this->replaceSuffix($v['image'], '');
                    }else{
                        $data[$k]['full_image_path'] = 'http://0.gravatar.com/avatar/0115cda8dbceda5ee5d0ce5e953b1313?s=46&d=mm&r=g';
                        $data[$k]['full_image_path_org'] = 'http://0.gravatar.com/avatar/0115cda8dbceda5ee5d0ce5e953b1313?s=46&d=mm&r=g';
                    }
                }
                if(!empty($v['game_image']))
                {
                    $data[$k]['game_image_path'] = VICTORIOUS__PLUGIN_URL_IMAGE.'formula1/'.$v['game_image'];
                }
            }
        }
        else if($data != null && !$is_arr)
        {
            if($data['siteID'] > 0)
            {
                if(substr($data['image'], -1) != '/'){
                    $data['full_image_path'] = VICTORIOUS_IMAGE_URL.$this->replaceSuffix($data['image']);
                    $data['full_image_path_org'] = VICTORIOUS_IMAGE_URL.$this->replaceSuffix($data['image'], '');
                }else{
                    $data['full_image_path'] = 'http://0.gravatar.com/avatar/0115cda8dbceda5ee5d0ce5e953b1313?s=46&d=mm&r=g';
                    $data['full_image_path_org'] = 'http://0.gravatar.com/avatar/0115cda8dbceda5ee5d0ce5e953b1313?s=46&d=mm&r=g';
                }
            }
            else 
            {
                if(substr($data['image'], -1) != '/'){
                    $data['full_image_path'] = $this->replaceSuffix($data['image']);
                    $data['full_image_path_org'] = $this->replaceSuffix($data['image'], '');
                }else{
                    $data['full_image_path'] = 'http://0.gravatar.com/avatar/0115cda8dbceda5ee5d0ce5e953b1313?s=46&d=mm&r=g';
                    $data['full_image_path_org'] = 'http://0.gravatar.com/avatar/0115cda8dbceda5ee5d0ce5e953b1313?s=46&d=mm&r=g';
                }
                
            }
        }
        return $data;
    }
    public function parseMixingPlayersData($datas = null, $is_arr = true)
    {
                    
        foreach($datas as $index=>$data){
            if($data != null && $is_arr)
        {
            foreach($data as $k => $v)
            {
                if($v['siteID'] > 0)
                {
                    if(substr($v['image'], -1) != '/'){
                        $datas[$index][$k]['full_image_path'] = VICTORIOUS_IMAGE_URL.$this->replaceSuffix($v['image']);
                        $datas[$index][$k]['full_image_path_org'] = VICTORIOUS_IMAGE_URL.$this->replaceSuffix($v['image'], '');
                    }else{
                        $datas[$index][$k]['full_image_path'] = 'http://0.gravatar.com/avatar/0115cda8dbceda5ee5d0ce5e953b1313?s=46&d=mm&r=g';
                        $datas[$index][$k]['full_image_path_org'] = 'http://0.gravatar.com/avatar/0115cda8dbceda5ee5d0ce5e953b1313?s=46&d=mm&r=g';
                    }
                    
                }
                else 
                {
                    if(substr($v['image'], -1) != '/'){
                        $datas[$index][$k]['full_image_path'] = $this->replaceSuffix($v['image']);
                        $datas[$index][$k]['full_image_path_org'] = $this->replaceSuffix($v['image'], '');
                    }else{
                        $datas[$index][$k]['full_image_path'] = 'http://0.gravatar.com/avatar/0115cda8dbceda5ee5d0ce5e953b1313?s=46&d=mm&r=g';
                        $datas[$index][$k]['full_image_path_org'] = 'http://0.gravatar.com/avatar/0115cda8dbceda5ee5d0ce5e953b1313?s=46&d=mm&r=g';
                    }
                }
            }
        }
        else if($data != null && !$is_arr)
        {
            if($data['siteID'] > 0)
            {
                if(substr($v['image'], -1) != '/'){
                    $datas[$index]['full_image_path'] = VICTORIOUS_IMAGE_URL.$this->replaceSuffix($data['image']);
                    $datas[$index]['full_image_path_org'] = VICTORIOUS_IMAGE_URL.$this->replaceSuffix($data['image'], '');
                }else{
                    $datas[$index][$k]['full_image_path'] = 'http://0.gravatar.com/avatar/0115cda8dbceda5ee5d0ce5e953b1313?s=46&d=mm&r=g';
                    $datas[$index][$k]['full_image_path_org'] = 'http://0.gravatar.com/avatar/0115cda8dbceda5ee5d0ce5e953b1313?s=46&d=mm&r=g';
                }
            }
            else 
            {
                if(substr($v['image'], -1) != '/'){
                    $datas[$index]['full_image_path'] = $this->replaceSuffix($data['image']);
                    $datas[$index]['full_image_path_org'] = $this->replaceSuffix($data['image'], '');
                }else{
                    $datas[$index][$k]['full_image_path'] = 'http://0.gravatar.com/avatar/0115cda8dbceda5ee5d0ce5e953b1313?s=46&d=mm&r=g';
                    $datas[$index][$k]['full_image_path_org'] = 'http://0.gravatar.com/avatar/0115cda8dbceda5ee5d0ce5e953b1313?s=46&d=mm&r=g';
                }
                
            }
        }
        }
        return $datas;
    }

    //////////////////////////////////////////add, update, delete//////////////////////////////////////////
    public function add($aVals)
    {
        
        if(!isset($aVals['is_privateers'])){
            $aVals['is_privateers'] = 0;
        }
        $id = $this->sendRequest("addPlayer", $aVals, true ,false);
        if(!is_numeric($id) && $id != 'u1')
        {
            return $id;
        }

        //upload new image
        $image = $this->uploadImage();
        if($id == 'u1')
        {
            $id = $aVals['id'];
        }
        if($id > 0 && $image != null)
        {
            $this->updatePlayersImage($id, $image);
        }
        
        return $id;
    }

    public function updatePlayersImage($id, $image)
    {
        return $this->sendRequest("updatePlayers", array('id' => (int)$id, 'image' => $image));
    }
    
    public function updatePlayer($data)
    {
        return $this->sendRequest("updatePlayers", $data);
    }
    
    public function updatePlayerSalary($data)
    {
        return $this->sendRequest("updatePlayerSalary", $data);
    }
    
    private function parsePlayersDataForModify($aVals, $isUpdate = false)
    {
        $data = array('team_id' => $aVals['team_id'],
                      'org_id' => $aVals['org_id'],
                      'position_id' => $aVals['position_id'],
                      'name' => $aVals['name'],
                      'salary' => str_replace(',', '', $aVals['salary']),
                      'indicator_id' => $aVals['indicator_id']);
        if($isUpdate)
        {
            $data['id'] = $aVals['id'];
        }
        return $data;
    }
    
    public function delete($id)
    {
        $sFileName = $this->getPlayersImageName($id);
        $result = $this->sendRequest("deletePlayers", array('id' => $id));;
        if($result)
        {
            $this->deleteImage($sFileName);
            return true;
        }
        return false;
    }
}
?>