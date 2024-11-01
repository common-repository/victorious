<?php

require_once("admin/RestClient.php");
require( ABSPATH . WPINC . '/pluggable.php' );

class VIC_Sportbook extends VIC_Model
{
    public function getSportbookContest($league_id, $entry_number)
    {
        $this->method = "GET";
        return $this->send("getSportbookContest", array(
            "league_id" => $league_id,
            "entry_number" => $entry_number
        ));
    }
    
    public function submitSportbook($league_id, $entry_number, $wager_type, $wager, $to_win, $team_id, $can_edit = false)
    {
        return $this->send("submitSportbook", array(
            'league_id' => $league_id,
            'entry_number' => $entry_number,
            'wager_type' => $wager_type,
            'wager' => $wager,
            'to_win' => $to_win,
            'team_id' => $team_id,
            'can_edit' => $can_edit
        ));
    }

    public function validateSubmitSportbook($league_id, $entry_number, $wager_type, $wager, $to_win, $team_id, $can_edit = false)
    {
        return $this->send("validateSubmitSportbook", array(
            'league_id' => $league_id,
            'entry_number' => $entry_number,
            'wager_type' => $wager_type,
            'wager' => $wager,
            'to_win' => $to_win,
            'team_id' => $team_id,
            'can_edit' => $can_edit
        ));
    }
    
    public function getSportbookContestResult($league_id)
    {
        $this->method = "GET";
        return $this->send("getSportbookContestResult", array("league_id" => $league_id));
    }
    
    public function getSportbookResult($league_id, $page = 1)
    {
        $this->method = "GET";
        $data = $this->send("getSportbookResult", array(
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

    public function getSportbookResultDetail($league_id, $user_id, $entry_number)
    {
        $this->method = "GET";
        $data =  $this->send("getSportbookResultDetail", array(
            "league_id" => $league_id,
            "user_id" => $user_id,
            "entry_number" => $entry_number
        ));
        if (isset($data['score']))
        {
            $data['score'] = array_merge($data['score'], $this->parseUserData($data['score'], $data['score']['userID']));
        }
        return $data;
    }
    
    public function updateOverUnderPoint(){
        $this->method = "GET";
        return $this->send("updateSportbookOverUnderPoint", array(
            "league_id" => $league_id,
            "user_id" => $user_id,
            "entry_number" => $entry_number
        ));
    }
}

?>