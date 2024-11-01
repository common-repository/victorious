<?php

require_once("admin/RestClient.php");
require( ABSPATH . WPINC . '/pluggable.php' );

class VIC_Playoff extends VIC_Model
{
    public function validateEnterPlayoff($league_id, $entry_number)
    {
        return $this->send("validateEnterPlayoff", array(
            "league_id" => $league_id,
            "entry_number" => $entry_number
        ));
    }

    public function getPlayoffContest($league_id, $entry_number)
    {
        return $this->send("getPlayoffContest", array(
            "league_id" => $league_id,
            "entry_number" => $entry_number
        ));
    }

    public function getPlayoffPlayerList($league_id, $position_id, $fight_id, $player_id, $sort_by, $sort_type, $keyword = '', $page = 1)
    {
        return $this->send("getPlayoffPlayerList", array(
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

    public function submitPlayoff($league_id, $entry_number, $lineup_ids, $player_ids)
    {
        return $this->send("submitPlayoff", array(
            'league_id' => $league_id,
            'entry_number' => $entry_number,
            'lineup_ids' => $lineup_ids,
            'player_ids' => $player_ids
        ));
    }

    public function validateSubmitPlayoff($league_id, $entry_number, $lineup_ids, $player_ids)
    {
        return $this->send("validateSubmitPlayoff", array(
            'league_id' => $league_id,
            'entry_number' => $entry_number,
            'lineup_ids' => $lineup_ids,
            'player_ids' => $player_ids
        ));
    }

    public function getPlayoffContestResult($league_id)
    {
        $this->method = "GET";
        return $this->send("getPlayoffContestResult", array("league_id" => $league_id));
    }

    public function getPlayoffResult($league_id, $page = 1)
    {
        $this->method = "GET";
        $data = $this->send("getPlayoffResult", array(
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

    public function getPlayoffResultDetail($league_id, $user_id, $entry_number, $week)
    {
        $this->method = "GET";
        $data =  $this->send("getPlayoffResultDetail", array(
            "league_id" => $league_id,
            "user_id" => $user_id,
            "entry_number" => $entry_number,
            "week" => $week,
        ));
        if (isset($data['score']))
        {
            $data['score'] = array_merge($data['score'], $this->parseUserData($data['score'], $data['score']['userID']));
        }
        return $data;
    }

    public function getPlayoffLiveResult($league_id)
    {
        $this->method = "GET";
        return $this->send("getPlayoffLiveResult", array(
            'league_id' => $league_id
        ));
    }

    public function joinContest($league_id)
    {
        return $this->send("joinPlayoffContest", array(
            'league_id' => $league_id
        ));
    }

    public function inDraftingUsers($league_id){
        $this->method = "GET";
        $data = $this->send("inDraftingUsers", array(
            'league_id' => $league_id
        ));

        if (isset($data['scores'])){
            foreach ($data['scores'] as $k => $score){
                $data['scores'][$k]['user'] = $this->parseUserData(null, $score['userID']);
            }
        }
        return $data;
    }

    public function draftPlayer($league_id, $player_id, $position_id, $entry_number){
        return $this->send("playoffDraftPlayer", array(
            'league_id' => $league_id,
            'player_id' => $player_id,
            'position_id' => $position_id,
            'entry_number' => $entry_number
        ));
    }

    public function removeDraftPlayer($league_id, $player_id, $entry_number){
        return $this->send("playoffRemoveDraftPlayer", array(
            'league_id' => $league_id,
            'player_id' => $player_id,
            'entry_number' => $entry_number
        ));
    }

    public function updatePlayoffTurn($league_id, $user_id){
        $tmp = !empty(get_option('victorious_playoff_change_turn')) ? get_option('victorious_playoff_change_turn') : array();
        if($tmp[$league_id] == $user_id){
            $tmp[$league_id] = '';
        }
        else{
            $tmp[$league_id] = $user_id;
        }
        update_option('victorious_playoff_change_turn', $tmp);
    }

    public function autoDraftPlayer($league_id, $entry_number = 1){
        return $this->send("playoffAutoDraftPlayer", array(
            'league_id' => $league_id,
            'entry_number' => $entry_number
        ));
    }

    public function playoffTrigger(){
        return $this->send("playoffTrigger");
    }

    public function playoffCheckDraftStart(){
        return $this->send("playoffCheckDraftStart");
    }

    public function sendStartDraftEmail($user_id, $data)
    {
        $admin_email = (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email'));
        $user_profile = $this->get_user_info($user_id);
        $href_change = VICTORIOUS_URL_GAME . '/league_id='.$data['leagueID'].'&num=1';
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

        $headers .= 'To: ' . $user_profile->user_email . "\r\n";

        $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . $admin_email . ">\r\n";
        $message_subject = sprintf(__("Your draft is about to start", 'victorious'));
        $to_mail = $user_profile->user_email;
        $contest_name = $data['name'];
        $time_prior_draft = $data['playoff_minute_prior_draft'] / 60;
        require('admin/emailTemplates/playoff_start_draft.php');
        $is_send_success = true;
        try
        {
            wp_mail($to_mail, $message_subject, $message_body, $headers);
        }
        catch (Exception $ex)
        {
            $is_send_success = false;
        }
        return $is_send_success;
    }

    public function sendCancelContestEmail($user_id, $data)
    {
        $admin_email = (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email'));
        $user_profile = $this->get_user_info($user_id);
        $href_change = VICTORIOUS_URL_LOBBY;
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

        $headers .= 'To: ' . $user_profile->user_email . "\r\n";

        $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . $admin_email . ">\r\n";
        $message_subject = sprintf(__("Contest has been canceled", 'victorious'));
        $to_mail = $user_profile->user_email;
        $contest_name = $data['name'];
        require('admin/emailTemplates/playoff_cancel_contest.php');
        $is_send_success = true;
        try
        {
            wp_mail($to_mail, $message_subject, $message_body, $headers);
        }
        catch (Exception $ex)
        {
            $is_send_success = false;
        }
        return $is_send_success;
    }

    public function get_user_info($userID)
    {
        $user = $this->get_user_by("id", $userID);

        if ($user)
        {
            $user = $user->data;
            $avatar_url = $this->get_avatar_url($this->get_avatar($userID));
            $user->avatar_url = $avatar_url;
        }
        return $user;
    }
}

?>