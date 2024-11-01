<?php

require_once("admin/RestClient.php");
require( ABSPATH . WPINC . '/pluggable.php' );

class VIC_Playerdraft extends VIC_Model
{
    public function getPlayerDraftContest($league_id, $entry_number)
    {
        return $this->send("getPlayerDraftContest", array(
            "league_id" => $league_id,
            "entry_number" => $entry_number
        ));
    }

    public function getDraftPlayerList($league_id, $position_id, $fight_id, $player_id, $sort_by, $sort_type, $keyword = '', $page = 1)
    {
        return $this->send("getDraftPlayerList", array(
            'league_id' => $league_id,
            'position_id' => $position_id,
            'fight_id' => $fight_id,
            'player_id' => $player_id,
            'keyword' => $keyword,
            'page' => $page,
            'sort_by' => $sort_by,
            'sort_type' => $sort_type
        ));
    }

    public function submitPlayerDraft($data)
    {
        return $this->send("submitPlayerDraft", $data);
    }

    public function validateSubmitPlayerDraft($league_id, $entry_number, $lineup_ids, $player_ids)
    {
        return $this->send("validateSubmitPlayerDraft", array(
            'league_id' => $league_id,
            'entry_number' => $entry_number,
            'lineup_ids' => $lineup_ids,
            'player_ids' => $player_ids
        ));
    }
}

?>