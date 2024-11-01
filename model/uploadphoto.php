<?php

require_once("admin/RestClient.php");
require( ABSPATH . WPINC . '/pluggable.php' );

class VIC_UploadPhoto extends VIC_Model
{
    public function submitUploadPhoto($data)
    {
        return $this->send("uploadPhotoSubmitUploadPhoto", $data);
    }
    
    public function validateSubmitUploadPhoto($data)
    {
        return $this->send("uploadPhotoValidateSubmitUploadPhoto", $data);
    }
    
    public function validateSaveResult($league_id, $fixture_id, $total_kill, $finish, $image){
        return $this->send("uploadPhotoValidateSaveResult", array(
            'league_id' => $league_id,
            'fixture_id' => $fixture_id,
            'total_kill' => $total_kill,
            'finish' => $finish,
            'image' => $image
        ));
    }
    
    public function saveResult($data){
        return $this->send("uploadPhotoSaveResult", $data);
    }
    
    public function getResult($league_id, $page = 1){
        $data = $this->send("uploadPhotoGetResult", array(
            "league_id" => $league_id,
            "page" => $page
        ));
        if ($data != null)
        {
            foreach ($data['standing'] as $k => $standing)
            {
                $data['standing'][$k]['user'] = $this->parseUserData(null, $standing['userID']);
            }
        }
        return $data;
    }

    public function getResultDetail($league_id, $user_id, $entry_number){
        $data = $this->send("uploadPhotoGetResultDetail", array(
            "league_id" => $league_id,
            "user_id" => $user_id,
            "entry_number" => $entry_number
        ));
        if ($data != null)
        {
            foreach ($data['fights'] as $k => $item)
            {
                if(!empty($item['my_pick']['image'])){
                    $data['fights'][$k]['my_pick']['image_url'] = VICTORIOUS_IMAGE_URL.$item['my_pick']['image'];
                }
            }
        }
        return $data;
    }
    
    public function completeUploadPhotoContest($league_id){
        $result = $this->send("uploadPhotoCompleteContest", array(
            'league_id' => $league_id
        ));
        if($result['success']){
            //update money for winners
            //$this->updateUserMoneyWon();
        }
        return $result;
    }

    public function getContestResult($league_id){
        $this->method = "GET";
        return $this->send("uploadPhotoContestResult", array("league_id" => $league_id));
    }
}

?>