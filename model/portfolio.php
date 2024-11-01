<?php

require_once("admin/RestClient.php");
require( ABSPATH . WPINC . '/pluggable.php' );

class VIC_Portfolio extends VIC_Model
{
    public function validateEnterPortfolio($league_id, $entry_number)
    {
        return $this->send("validateEnterPortfolio", array(
            "league_id" => $league_id,
            "entry_number" => $entry_number
        ));
    }

    public function getPortfolioContest($league_id, $entry_number)
    {
        return $this->send("getPortfolioContest", array(
            "league_id" => $league_id,
            "entry_number" => $entry_number
        ));
    }

    public function getPortfolioPlayerList($league_id, $position_id, $category_id, $sort_by, $sort_type, $keyword = '', $page = 1)
    {
        return $this->send("getPortfolioPlayerList", array(
            'league_id' => $league_id,
            'position_id' => $position_id,
            'category_id' => $category_id,
            'keyword' => $keyword,
            'page' => $page,
            'sort_by' => $sort_by,
            'sort_type' => $sort_type
        ));
    }

    public function submitPortfolio($league_id, $entry_number, $lineup_ids, $player_ids, $quantity)
    {
        return $this->send("submitPortfolio", array(
            'league_id' => $league_id,
            'entry_number' => $entry_number,
            'lineup_ids' => $lineup_ids,
            'player_ids' => $player_ids,
            'quantity' => $quantity,
        ));
    }

    public function validateSubmitPortfolio($league_id, $entry_number, $lineup_ids, $player_ids, $quantity)
    {
        return $this->send("validateSubmitPortfolio", array(
            'league_id' => $league_id,
            'entry_number' => $entry_number,
            'lineup_ids' => $lineup_ids,
            'player_ids' => $player_ids,
            'quantity' => $quantity,
        ));
    }

    public function getPortfolioEntry($league_id, $entry_number)
    {
        return $this->send("getPortfolioEntry", array(
            'league_id' => $league_id,
            'entry_number' => $entry_number
        ));
    }

    public function getPortfolioContestResult($league_id)
    {
        $this->method = "GET";
        return $this->send("getPortfolioContestResult", array("league_id" => $league_id));
    }

    public function getPortfolioResult($league_id, $page = 1)
    {
        $this->method = "GET";
        $data = $this->send("getPortfolioResult", array(
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

    public function getPortfolioResultDetail($league_id, $user_id, $entry_number)
    {
        $this->method = "GET";
        $data =  $this->send("getPortfolioResultDetail", array(
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

    public function getPortfolioLiveResult($league_id)
    {
        $this->method = "GET";
        return $this->send("getPortfolioLiveResult", array(
            'league_id' => $league_id
        ));
    }
}

?>