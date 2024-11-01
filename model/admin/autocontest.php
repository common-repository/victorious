<?php

class VIC_AutoContest extends VIC_Model
{
	public function __construct()
	{	
        $this->api_token = get_option('victorious_api_token');
        $this->api_url = get_option('victorious_api_url_admin');
        // $this->urladd = 'admincp.victorious.sports.add';
	}

	public function getAutoContests(){
		return $this->sendRequest("getAutoContests", array(), true);
	}
    
    public function getAutoContest($id){
		return $this->sendRequest("getAutoContest", array("id" => $id), true);
	}

	public function saveAutoContests($data){
		return $this->sendRequest("saveAutoContests", $data, true, false);
	}

	public function delete($id){
		return $this->sendRequest("deleteAutoContest", array('id' => $id), true);
	}

	public function getSport(){
		return $this->sendRequest("getSports", array(), true);
	}

	public function updateAutoContestStatus($data){
		return $this->sendRequest("updateAutoContestStatus", $data, true, false);
	}
}

?>