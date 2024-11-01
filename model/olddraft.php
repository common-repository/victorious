<?php

require_once("admin/RestClient.php");
require( ABSPATH . WPINC . '/pluggable.php' );

class VIC_OldDraft extends VIC_Model
{
    public function validateEnterOldDraft($league_id, $entry_number)
    {
        return $this->send("validateEnterOldDraft", array(
            "league_id" => $league_id,
            "entry_number" => $entry_number
        ));
    }

    public function getOldDraftContest($league_id, $entry_number)
    {
        return $this->send("getOldDraftContest", array(
            "league_id" => $league_id,
            "entry_number" => $entry_number
        ));
    }

    public function getOldDraftPlayerList($league_id, $position_id, $sort_by, $sort_type, $keyword = '', $page = 1)
    {
        return $this->send("getOldDraftPlayerList", array(
            'league_id' => $league_id,
            'position_id' => $position_id,
            'keyword' => $keyword,
            'page' => $page,
            'sort_by' => $sort_by,
            'sort_type' => $sort_type
        ));
    }

    public function submitOldDraft($league_id, $entry_number, $lineup_ids, $player_ids, $olddraft_insurance)
    {
        return $this->send("submitOldDraft", array(
            'league_id' => $league_id,
            'entry_number' => $entry_number,
            'lineup_ids' => $lineup_ids,
            'player_ids' => $player_ids,
            'olddraft_insurance' => $olddraft_insurance,
        ));
    }

    public function validateSubmitOldDraft($league_id, $entry_number, $lineup_ids, $player_ids, $olddraft_insurance)
    {
        return $this->send("validateSubmitOldDraft", array(
            'league_id' => $league_id,
            'entry_number' => $entry_number,
            'lineup_ids' => $lineup_ids,
            'player_ids' => $player_ids,
            'olddraft_insurance' => $olddraft_insurance,
        ));
    }

    public function getOldDraftEntry($league_id, $entry_number)
    {
        return $this->send("getOldDraftEntry", array(
            'league_id' => $league_id,
            'entry_number' => $entry_number
        ));
    }

    public function getOldDraftContestResult($league_id)
    {
        $this->method = "GET";
        return $this->send("getOldDraftContestResult", array("league_id" => $league_id));
    }

    public function getOldDraftResult($league_id, $page = 1)
    {
        $this->method = "GET";
        $data = $this->send("getOldDraftResult", array(
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

    public function getOldDraftResultDetail($league_id, $user_id, $entry_number)
    {
        $this->method = "GET";
        $data =  $this->send("getOldDraftResultDetail", array(
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

    public function getOldDraftLiveResult($league_id)
    {
        $this->method = "GET";
        return $this->send("getOldDraftLiveResult", array(
            'league_id' => $league_id
        ));
    }
}

?>