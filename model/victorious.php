<?php

require_once("admin/RestClient.php");
require( ABSPATH . WPINC . '/pluggable.php' );

class VIC_Victorious extends VIC_Model
{
    public function postUserInfo($user_id = 0)
    {
        if ($user_id == 0)
        {
            $user_id = (int) VIC_GetUserId();
        }
        global $wpdb;
        $table_name = $wpdb->prefix . "users";
        $sCond = "WHERE ID = " . $user_id;
        $sql = "SELECT ID as user_id,user_registered, user_login as user_name, user_nicename as full_name, user_email as email "
                . "FROM $table_name "
                . $sCond;
        $aUser = $wpdb->get_row($sql, ARRAY_A);
        $aUser = json_decode(json_encode($aUser), true);
        $aUser['ip'] = sanitize_url($_SERVER['REMOTE_ADDR']);
        $aUser['timezone'] = get_option('victorious_timezone');
        $aUser['site_url'] = home_url();
        $this->sendRequest("userInfo", $aUser, false);
    }

    function canPlay()
    {
        return $this->sendRequest("canPlay", null, false);
    }

    public function getGamesummary($page = 1, $sort_by = '', $sort_type = '')
    {
        $aDatas = $this->sendRequest("gameSummary", array(
            'page' => $page,
            'sort_by' => $sort_by,
            'sort_type' => $sort_type
                ), false);
        if ($aDatas["users"] != null)
        {
            foreach ($aDatas["users"] as $k => $user)
            {
                $info = $this->get_user_by("id", $user["user_id"]);
                $aDatas["users"][$k]["user_login"] = $info->data->user_login;
                $aDatas["users"][$k]["user_nicename"] = $info->data->user_nicename;
            }
        }
        return $aDatas;
    }

    public function getFutureEvents()
    {
        return $this->send("futureEvents", null);
    }

    public function getNormalGameResult($leagueID, $entry_number, $page, $sort_by, $sort_value, $date_type = null, $date_type_number = null)
    {
        $data = $this->send("getNormalGameResult", array(
            'leagueID' => $leagueID,
            'entry_number' => $entry_number,
            'page' => $page,
            'sort_by' => $sort_by,
            'sort_value' => $sort_value,
            'date_type' => $date_type,
            'date_type_number' => $date_type_number
        ));

        if ($data["users"] != null)
        {
            foreach ($data["users"] as $k => $user)
            {
                $info = $this->get_user_by("id", $user["userID"]);
                $data["users"][$k]["user_login"] = $info->data->user_login;
            }
        }
        return $data;
    }

    public function getLeagueDetail($leagueID)
    {
        $league = $this->send("leagueDetail", array('leagueID' => $leagueID));
        return $this->parseLeagueData($league);
    }

    public function getLeagueName($listLeagueID)
    {

        return $this->sendRequest('getLeagueName', array('leagueid' => $listLeagueID), false);
    }

    public function postUserPicks($pool_id, $league_id, $entry_number, $params)
    {
        $params['poolID'] = $pool_id;
        $params['leagueID'] = $league_id;
        $params['entry_number'] = $entry_number;
        return $this->send("userpicks", $params);
    }

    public function postUserPicksAllowFields($data){
        if(empty($data)){
            return $data;
        }
        $fields = array("winner", "spread", "method", "round", "minute", "over_under_value", "choose",
            'pick_squares', 'user_squares', 'current_week', 'predict_point', 'predict_point_game', 'allow_new_tie_breaker', 'highest_score_team', 'player_score', 'total_goals');
        $params = array();
        foreach($data as $key => $item){
            foreach ($fields as $field) {
                $search = strpos($key, $field);
                if ($search !== false) {
                    $found = true;
                    break;
                }
            }
            if($found){
                $params[$key] = sanitize_text_field($item);
            }
        }
        return $params;
    }

    public function getUserPicks($leagueID)
    {
        return $this->sendRequest("getuserpicks", array('leagueID' => $leagueID), false);
    }

    public function getFights($leagueID)
    {
        return $this->sendRequest("fights", array('leagueID' => $leagueID, 'mode' => 'html'), false, false);
    }

    public function inviteFriend($data)
    {
        if (!empty($data['message_boxinvite']))
            $message_boxinvite = mysql_real_escape_string($data['message_boxinvite']);

        global $wpdb;
        $contacts = array();
        $trueContacts = array();
        $contacts = explode(",", $data["emails"]);
        $importleagueID = $data["importleagueID"];
        $inFriends = null;
        $user_id = !empty($data['user_id']) ? $data['user_id'] : (int) VIC_GetUserId();

        if (!empty($data['friend_ids']))
        {
            $friendIds = trim($data['friend_ids']);
            $table_name = $wpdb->prefix . "users";
            $sCond = "WHERE ID IN ($friendIds)";
            $sql = "SELECT user_email as email "
                    . "FROM $table_name "
                    . $sCond;
            $result = $wpdb->get_results($sql);
            $result = json_decode(json_encode($result), true);
            foreach ($result as $item)
            {
                $contacts[] = $item['email'];
            }
        }

        // we can't send invite to ourselves, so let's get username and email
        $table_name = $wpdb->prefix . "users";
        $sCond = "WHERE ID = " . $user_id;
        $sql = "SELECT user_login, user_email as email "
                . "FROM $table_name "
                . $sCond;
        $result = $wpdb->get_row($sql);
        $result = json_decode(json_encode($result), true);
        $myUsername = $result['user_login'];
        $myEmail = $result['email'];

        // check if value is an email address. If not
        // then get mmavictor username

        foreach ($contacts as $contact)
        {
            $contact = trim($contact);
            if ($contact == $myUsername || $contact == $myEmail)
                continue;

            $pos = strpos($contact, '@');
            if ($pos === false)
            {
                // go get the email of the user
                if ($mmavictorInfo = $this->getPlayerInfoByUsername($contact))
                    array_push($trueContacts, strtolower(trim($mmavictorInfo["email"])));
            }
            else
                array_push($trueContacts, strtolower(trim($contact)));
        }

        if (count($trueContacts) == 0)
            return json_encode(array("message" => "You haven't selected any contacts to invite or you can not invite yourself !"));

        $trueContacts = array_unique($trueContacts);
        $playerInfo = $this->getPlayerInfo($user_id);

        //league
        //$this->selectField(array('name', 'size', 'entry_fee', 'poolID'));
        $leagueInfo = $this->getLeagueDetail($importleagueID);
        $leagueInfo = $leagueInfo[0];
        $website = 'http://' . sanitize_url($_SERVER['SERVER_NAME']);
//        $contest = 'http://'.sanitize_url($_SERVER['SERVER_NAME']).'/fantasy/submit-picks/'.$importleagueID;
        $contest = $data['link_url_contest'];
        $siteTitle = (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname'));

        require_once('admin/emailTemplates/invite.php');
        $message = array('subject' => $message_subject,
            'body' => $message_body,
            'attachment' => "\n\rAttached message: \n\r" . $message_boxinvite);

        //$message_subject=$name.$message['subject'];
        $message_subject = $message['subject'];
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email')) . ">\r\n";
        $success = true;

        foreach ($trueContacts as $email)
        {
            try
            {
                mail($email, $message_subject, $message_body, $headers);
                $success = true;
            }
            catch (Exception $ex)
            {
                return json_encode(array("notice" => $ex->getMessage()));
            }
        }
        $message = "Invites Sent!";
        if (!$success)
        {
            $message = 'Something went wrong! Please try again.';
            return json_encode(array("notice" => $message));
        }
        return json_encode(array("message" => $message));
    }

    private function getPlayerInfoByUsername($username = null)
    {
        if ($username != null)
        {
            $result = $this->database()->select('*')
                    ->from(Phpfox::getT('user'))
                    ->where("username = '$username'")
                    ->execute('getSlaveRow');
            return $result;
        }
        return null;
    }

    private function getPlayerInfo($user_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "users";
        $sCond = "WHERE ID = " . (int) $user_id;
        $sql = "SELECT *, user_email as email, display_name as full_name "
                . "FROM $table_name "
                . $sCond;
        $result = $wpdb->get_row($sql);
        $result = json_decode(json_encode($result), true);
        $result['pubKey'] = $result['firstName'] = $result['lastName'] = '';
        $result['username'] = $result['user_login'];
        return $result;
    }

    public function getAllPlayerInfo()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "users";
        $sCond = "WHERE ID != " . VIC_GetUserId();
        $sql = "SELECT *, user_email as email, display_name as full_name "
                . "FROM $table_name "
                . $sCond;
        $result = $wpdb->get_results($sql);
        $result = json_decode(json_encode($result), true);

        return $result;
    }

    ////////////////////////////////v2////////////////////////////////////
    public function isLeagueExist($leagueID)
    {
        if ($this->sendRequest("isLeagueExist", array('leagueID' => $leagueID), false, false) == 1)
        {
            return true;
        }
        return false;
    }

    public function isNormalLeagueExist($leagueID)
    {
        if ($this->sendRequest("isNormalLeagueExist", array('leagueID' => $leagueID), false, false) == 1)
        {
            return true;
        }
        return false;
    }

    public function isPlayerDraftLeagueExist($leagueID)
    {
        if ($this->sendRequest("isPlayerDraftLeagueExist", array('leagueID' => $leagueID), false, false) == 1)
        {
            return true;
        }
        return false;
    }

    public function isPlayerDraftLeagueFull($leagueID, $entry_number)
    {
        if ($this->sendRequest("isPlayerDraftLeagueFull", array('leagueID' => $leagueID, 'entry_number' => $entry_number), false, false) == 1)
        {
            return true;
        }
        return false;
    }

    public function getListSports($params = null)
    {
        return $this->sendRequest("getListSports", $params, false);
    }

    public function getListGameTypeSoccer()
    {
        return $this->sendRequest("getListGameTypeSoccer", null, false);
    }

    public function getLeagueLobby($params = null)
    {
        $leagues = $this->send("getLeagueLobby", $params);
        if ($leagues != null)
        {
            foreach ($leagues as $k => $league)
            {
                if ($league['is_motocross'])
                {
                    $leagues[$k]["gameType"] = __('Rider Draft', 'victorious');
                }
                try
                {
                    $startDate = !empty($league["roundpickemStartDate"]) ? $league["roundpickemStartDate"] : $league["startDate"];
                    $date = new DateTime($startDate, new DateTimeZone(get_option('victorious_timezone')));
                    $leagues[$k]["startTimeStamp"] = $date->format('U');
                }
                catch (Exception $ex)
                {
                    
                }

                //icon
                /*if (!empty($league['pool_siteID']) && $league['pool_siteID'] > 0 && !empty($league['icon']))
                {
                    $leagues[$k]['icon'] = VICTORIOUS_IMAGE_URL . $league['icon'];
                }*/
            }
        }
        return $leagues;
    }
    
    public function getSportFirstGame($params = array()){
        $this->method = "GET";
        return $this->send("getSportFirstGame", $params);
    }

    public function getUpcomingEntries()
    {
        $leagues = $this->send("getUpcomingEntries");
        if ($leagues != null)
        {
            foreach ($leagues as $k => $league)
            {
                if ($league['is_motocross'])
                {
                    $leagues[$k]["gameType"] = __('Rider Draft', 'victorious');
                }
            }
        }
        return $leagues;
    }

    public function getHistoryEntries()
    {
        $leagues = $this->send("getHistoryEntries");
        if ($leagues != null)
        {
            foreach ($leagues as $k => $league)
            {
                if ($league['is_motocross'])
                {
                    $leagues[$k]["gameType"] = __('Rider Draft', 'victorious');
                }
            }
        }
        return $leagues;
    }

    public function getLiveEntries()
    {
        $leagues = $this->send("getLiveEntries");

        if ($leagues != null)
        {
            foreach ($leagues as $k => $league)
            {
                if ($league['is_motocross'])
                {
                    $leagues[$k]["gameType"] = __('Rider Draft', 'victorious');
                }
            }
        }
        return $leagues;
    }

    public function liveEntriesResult($league_id)
    {
        $this->send("liveEntriesResult", array('league_id' => $league_id));
    }

    public function parseLeagueData($aLeagues)
    {
        if ($aLeagues != null)
        {
            $single = false;
            if (!isset($aLeagues[0]))
            {
                $aLeagues = array($aLeagues);
                $single = true;
            }
            $pools = new VIC_Pools();
            $balanceType = new VIC_BalanceType();
            foreach ($aLeagues as $k => $aLeague)
            {
                $aLeagues[$k]['today'] = false;
                if (isset($aLeague['startDate']) && strtotime(date('Y-m-d')) == strtotime(date('Y-m-d', strtotime($aLeague['startDate']))))
                {
                    $aLeagues[$k]['today'] = true;
                }

                //icon
                if (!empty($aLeague['sport_siteID']) && $aLeague['sport_siteID'] > 0 && !empty($aLeagues[$k]['icon']))
                {
                    $aLeagues[$k]['icon'] = VICTORIOUS_IMAGE_URL . $aLeague['icon'];
                }

                //creator
                $user = $this->get_user_by("id", $aLeague['creator_userID']);
                $aLeagues[$k]['creator_name'] = $user != null ? $user->user_login : null;
                $aLeagues[$k]['creator_is_admin'] = $user != null ? user_can($user, 'manage_options') : true;

                //total prize for winners
                $structure = '';
                if ($aLeague['prize_structure'] == 'WINNER')
                {
                    $structure = 'winnertakeall';
                }
                else
                {
                    $structure = 'top3';
                }
                $prizes = $pools->calculatePrizes('', $structure, $aLeague['size'], $aLeague['entry_fee'], null, $aLeague['winner_percent'], $aLeague['first_percent'], $aLeague['second_percent'], $aLeague['third_percent']);
                $aLeagues[$k]['prizes'] = 0;
                foreach ($prizes as $prize)
                {
                    $aLeagues[$k]['prizes'] += $prize;
                }

                //balance type
                $balance_type = !empty($aLeague['balance_type_id']) ? $balanceType->getBalanceTypeDetail($aLeague['balance_type_id']) : $balanceType->getBalanceTypeDetail(VICTORIOUS_DEFAULT_BALANCE_TYPE_ID);
                $aLeagues[$k]['balance_type'] = $balance_type;
            }
            if ($single)
            {
                $aLeagues = $aLeagues[0];
            }
        }
        return $aLeagues;
    }

    public function insertPlayerPicks($data)
    {
        $entry_number = $this->sendRequest("insertPlayerPicks", $data, false, false);
        if (is_string($entry_number))
        {

            $entry_number = json_decode($entry_number, true);

            return $entry_number;
        }
        if ($entry_number > 0)
        {
            return $entry_number;
        }
        return false;
    }

    public function insertGolfSkinPlayerPicks($data)
    {
        $entry_number = $this->sendRequest("insertGolfSkinPlayerPicks", $data, false, false);
        if ($entry_number > 0)
        {
            return $entry_number;
        }
        return false;
    }

    public function deletePlayerPicks($leagueID)
    {
        if ($this->sendRequest("deletePlayerPicks", array('leagueID' => $leagueID), false, false))
        {
            return true;
        }
        return false;
    }

    public function getPlayerPicks($leagueID, $entry_number)
    {
        $data = $this->sendRequest("getPlayerPicks", array('leagueID' => $leagueID, 'entry_number' => $entry_number), false);
        return $data;
    }

    public function getPlayerPickEntries($leagueID)
    {
        $aDatas = $this->sendRequest("getPlayerPickEntries", array('leagueID' => $leagueID), false);
        return $this->parseUserData($aDatas);
    }

    public function getEntries($leagueID)
    {
        $data = $this->send("getEntries", array('leagueID' => $leagueID));
        return $this->parseUserData($data);
    }

    public function getScores($leagueID, $week = 0, $only_me = false)
    {
        $aDatas = $this->send("getScores", array('leagueID' => $leagueID, 'week' => $week, 'only_me' => $only_me), false);
        return $this->parseUserData($aDatas);
    }

    public function getPlayerPicksResult($league_id, $user_id, $entry_number, $round_id, $week)
    {
        $data = $this->send("getPlayerPicksResult", array(
            'league_id' => $league_id,
            'user_id' => $user_id,
            'entry_number' => $entry_number,
            'round_id' => $round_id,
            'week' => $week
        ));
        if (isset($data['score']))
        {
            $data['score'] = array_merge($data['score'], $this->parseUserData($data['score'], $data['score']['userID']));
        }
        return $data;
    }

    public function getPlayerStatistics($player_id)
    {
        $data = $this->send("getPlayerStatistics", array("playerID" => $player_id));
        if (!empty($data['player']))
        {
            if ($data['player']['siteID'] > 0)
            {
                $data['player']['full_image_path'] = VICTORIOUS_IMAGE_URL . $this->replaceSuffix($data['player']['image']);
            }
            else
            {
                $data['player']['full_image_path'] = $this->replaceSuffix($data['player']['image'], '');
            }
        }
        return $data;
    }

    public function getPoolInfo($leagueID)
    {
        $data = $this->send("getPoolInfo", array('leagueID' => $leagueID));
        $data['entries'] = $this->parseUserData($data['entries']);
        return $data;
    }

    public function getNewPools()
    {
        return $this->sendRequest("getNewPools", null, false);
    }

    public function validCreateLeague($data)
    {
        return $this->sendRequest("validCreateLeague", $data, false, false);
    }

    public function createLeague($data)
    {
        if (!isset($data['is_refund']))
        {
            $data['is_refund'] = 0;
        }
        if (!isset($data['is_payouts']))
        {
            $data['is_payouts'] = 0;
        }
        if ($data['game_type'] == "bracket")
        {
            $data['point_group_winner'] = get_option('victorious_bracket_point_group_winner');
            $data['point_group_runnerup'] = get_option('victorious_bracket_point_group_runnerup');
            $data['point_group_16'] = get_option('victorious_bracket_point_16');
            $data['point_group_8'] = get_option('victorious_bracket_point_8');
            $data['point_group_4'] = get_option('victorious_bracket_point_4');
            $data['point_first'] = get_option('victorious_bracket_point_first');
            $data['point_second'] = get_option('victorious_bracket_point_second');
            $data['point_third'] = get_option('victorious_bracket_point_third');
        }
        if ($data['game_type'] == "portfolio" || $data['game_type'] == "olddraft"){
            $data['contest_cut_date'] = date('Y-m-d H:i:s', strtotime($data['contest_cut_date'].' '.$data['contest_cut_hour'].':'.$data['contest_cut_minute'].':00'));
            $data['contest_end_date'] = date('Y-m-d H:i:s', strtotime($data['contest_end_date'].' '.$data['contest_end_hour'].':'.$data['contest_end_minute'].':00'));
        }
        return $this->send("createLeague", $data);
    }

    public function loadCreateLeagueForm($leagueID = null)
    {
        return $this->send("loadCreateLeagueForm", array("leagueID" => $leagueID));
    }

    public function validateEnterDraftGame($leagueID = null, $entry_number, $action_id = 0, $orgID = null)
    {
        return $this->sendRequest("validEnterGame", array(
                    "leagueID" => $leagueID,
                    "entry_number" => $entry_number,
                    "action" => $action_id,
                    "orgID" => $orgID
                        ), false, false);
    }

    public function getEnterGameData($leagueID, $entry_number, $action_id = 0, $orgID = null)
    {
        return $this->send("getEnterGameData", array(
                    "leagueID" => $leagueID,
                    "entry_number" => $entry_number,
                    "action" => $action_id,
                    "orgID" => $orgID
        ));
    }

    public function getEnterMixingGameData($leagueID, $entry_number)
    {
        return $this->sendRequest("getEnterMixingGameData", array("leagueID" => $leagueID, "entry_number" => $entry_number), false);
    }

    public function getEnterRacingGameData($leagueID, $entry_number, $action_id = 0)
    {
        return $this->sendRequest("getEnterRacingGameData", array(
                    "leagueID" => $leagueID,
                    "entry_number" => $entry_number,
                    "action" => $action_id
                        ), false);
    }

    public function getEnterLiveDraftGameData($leagueID, $entry_number, $action_id = 0)
    {
        $data = $this->send("getEnterLiveDraftGameData", array(
            "leagueID" => $leagueID,
            "entry_number" => $entry_number,
            "action" => $action_id
        ));
        if (!empty($data['next_turn']))
        {
            $temp = array();
            foreach ($data['next_turn'] as $user_id)
            {
                $user = $this->getPlayerInfo($user_id);
                $temp[] = $user['user_login'];
            }
            $data['next_turn_user'] = implode(", ", $temp);
        }
        if (!empty($data['current_turn']))
        {
            $user = $this->getPlayerInfo($data['current_turn']);
            $data['current_turn_user'] = $user['user_login'];
        }
        return $data;
    }

    public function getEnterNormalGameData($leagueID, $entry_number)
    {
        $data = $this->send("getEnterNormalGameData", array("leagueID" => $leagueID, "entry_number" => $entry_number));
        if (!empty($data['fights']))
        {
            $data['fights'] = $this->parseFightData($data['fights']);
        }
        return $data;
    }

    public function getMixingGameEntryData($leagueID, $entry_number)
    {
        return $this->send("getMixingGameEntryData", array("leagueID" => $leagueID, "entry_number" => $entry_number));
    }

    public function getGameEntryData($leagueID, $entry_number)
    {
        return $this->send("getGameEntryData", array("leagueID" => $leagueID, "entry_number" => $entry_number));
    }

    public function validEnterPlayerdraft($leagueID, $playerIDs)
    {
        return $this->send("validEnterPlayerdraft", array("leagueID" => $leagueID, "playerIDs" => $playerIDs));
    }

    public function validEnterGolfSkin($leagueID, $playerIDs)
    {
        return $this->sendRequest("validEnterGolfSkin", array("leagueID" => $leagueID, "playerIDs" => $playerIDs), false, false);
    }

    public function validEnterMixingPlayerdraft($leagueID, $playerIDs)
    {
        return $this->sendRequest("validEnterMixingPlayerdraft", array("leagueID" => $leagueID, "playerIDs" => $playerIDs), false, false);
    }

    public function getContestResult($leagueID)
    {
        $data = $this->send("getContestResult", array("leagueID" => $leagueID));
        if (!empty($data['opponents']))
        {
            foreach ($data['opponents'] as $week => $opponents)
            {
                foreach ($opponents as $k => $opponent)
                {
                    $user_profile = $this->getPlayerInfo($opponent['userID']);
                    $opponent_profile = $this->getPlayerInfo($opponent['opponentID']);
                    $data['opponents'][$week][$k]['user_name'] = $user_profile['user_login'];
                    $data['opponents'][$week][$k]['opponent_name'] = $opponent_profile['user_login'];
                }
            }
        }
        return $data;
    }

    public function loadFixtureScores($leagueID)
    {
        return $this->sendRequest("loadFixtureScores", array("leagueID" => $leagueID), false);
    }

    public function getStatData()
    {
        return $this->sendRequest("getStatData", null, false); //, false);die;
    }

    public function getStatJS($a, $b, $c, $d, $sort_name, $sort_value, $team_id, $position_id)
    {
        return $this->sendRequest("getStatJS", array(
                    "sid" => $a,
                    "pid" => $b,
                    "filters" => $c,
                    "lim" => $d,
                    "sort_name" => $sort_name,
                    "sort_value" => $sort_value,
                    "team_id" => $team_id,
                    "position_id" => $position_id), false);
    }

    public function showUserPicks($leagueID)
    {
        $data = $this->sendRequest("showUserPicks", array('leagueID' => $leagueID), false);
        if (!empty($data['picks']))
        {
            foreach ($data['picks'] as $k1 => $pick)
            {
                $user = $this->get_user_by("id", $pick['userID']);
                if ($user != null)
                {
                    $data['picks'][$k1]['user_login'] = $user->user_login;
                }
            }
        }
        return $data;
    }

    public function sendUserPickEmail($leagueID, $user_id, $entry_number)
    {
        $data = $this->send("showUserPicks", array(
            'leagueID' => $leagueID,
            'userID' => $user_id,
            'entry_number' => $entry_number), false);
        $aUser = $this->getPlayerInfo(VIC_GetUserId());
        if ($data != null && $aUser != null && !empty($aUser))
        {
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
            $headers .= 'To: ' . $aUser['user_email'] . "\r\n";
            $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email')) . ">\r\n";
            $league = $data['league'];
            if ($league['gameType'] == 'PICKSQUARES')
            {
                $picks = $data['picks'];
                if ($picks)
                {
                    $picks = json_decode($picks, true);
                }
            }
            else if ($league['gameType'] == VICTORIOUS_GAME_TYPE_PORTFOLIO || $league['gameType'] == VICTORIOUS_GAME_TYPE_OLDDRAFT){
                $picks = $data['picks'];
            }
            else
            {
                $picks = $data['picks'][0]['entries'][0]['pick_items'];
            }
            include 'admin/emailTemplates/picks.php';

            try
            {
                wp_mail($aUser['user_email'], $message_subject, $message_body, $headers);
            }
            catch (Exception $ex)
            {
                
            }
        }
    }

    public function sendUserJoincontestEmail($leagueID, $entry_number)
    {
        $data = $this->sendRequest("showUserPicks", array(
            'leagueID' => $leagueID,
            'userID' => VIC_GetUserId(),
            'entry_number' => $entry_number), false);
        $aUser = $this->getPlayerInfo(VIC_GetUserId());
        if ($data != null && $aUser != null && !empty($aUser))
        {
            $admin_email = (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email'));
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
            $headers .= 'To: ' . $admin_email . "\r\n";
            $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email')) . ">\r\n";
            $league = $data['league'];
            $user_name = $aUser['user_login'];
            if ($league['gameType'] == 'PICKSQUARES')
            {
                $picks = $data['picks'];
                if ($picks)
                {
                    $picks = json_decode($picks, true);
                }
            }
            else
            {
                $picks = $data['picks'][0]['entries'][0]['pick_items'];
            }
            include 'admin/emailTemplates/picks_admin.php';
            try
            {
                wp_mail($admin_email, $message_subject, $message_body, $headers);
            }
            catch (Exception $ex)
            {
                
            }
        }
    }

    public function sendRequestPaymentEmail($id, $credits)
    {
        $current_user = $this->getPlayerInfo(VIC_GetUserId());
        $emailAdmin = (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email'));
        $subject = 'Request Payment';
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'To: ' . $emailAdmin . "\r\n";
        $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . $emailAdmin . ">\r\n";
        $username = $current_user['user_login'];
        $mount = $credits;

        require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'admin/emailTemplates/withdrawl.php');
        try
        {
            wp_mail($emailAdmin, $subject, $message, $headers);
        }
        catch (Exception $ex)
        {
            return false;
        }
    }

    public function sendApplyWithdrawlEmail($id, $status)
    {
        $payment = new VIC_Payment();
        $withdrawl = $payment->getWithdraw($id);
        if ($withdrawl != null)
        {
            $user = $payment->getUserData($withdrawl['userID']);
            $emailAdmin = (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email'));
            $subject = 'Request Payment';
            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
            $headers .= 'To: ' . $user['user_email'] . "\r\n";
            $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . $emailAdmin . ">\r\n";

            if ($status == 'APPROVED')
            {
                require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'admin/emailTemplates/withdrawl_approved.php');
            }
            else if ($status == 'DECLINED')
            {
                require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'admin/emailTemplates/withdrawl_declined.php');
            }

            try
            {
                wp_mail($emailAdmin, $subject, $message, $headers);
            }
            catch (Exception $ex)
            {
                return false;
            }
        }
    }

    public function sendUserCreditEmail($user_id, $credits, $operation, $reason = null)
    {
        $payment = new VIC_Payment();
        $user = $payment->getUserData($user_id);
        $emailAdmin = (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email'));
        $subject = 'Credits';
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'To: ' . $user['user_email'] . "\r\n";
        $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . $emailAdmin . ">\r\n";

        if ($operation == 'ADD')
        {
            require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'admin/emailTemplates/credit_add.php');
        }
        else if ($operation == 'DEDUCT')
        {
            require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'admin/emailTemplates/credit_deduct.php');
        }

        try
        {
            wp_mail($user['user_email'], $subject, $message, $headers);
        }
        catch (Exception $ex)
        {
            return false;
        }
    }

    public function createFolderCustomSport($data)
    {
        return $this->sendRequest("createfolderCustomSport", $data, true);
    }

    public function loadStatsUploadedFile($data)
    {
        return $this->sendRequest("loadStatsUploadedFile", $data, true);
    }

    public function getTeamsBySports($data)
    {
        return $this->sendRequest("getTeamsBySports", array('sports' => $data), false);
    }

    public function checkAllowChangeBackground()
    {
        return false;
        //return $this->sendRequest("checkAllowChangeBackground", array(), false);
    }

    public function getListPickemTeamByLeagueID($id, $entry_number)
    {
        return $this->sendRequest("getListPickemTeamByLeagueID", array('leagueID' => $id, 'entry_number' => $entry_number), false);
    }

    public function getListMotocrossSports()
    {
        return $this->sendRequest("getListMotocrossSports", array(), true);
    }

    public function loadMotocrossPlayerPoints($params)
    {
        return $this->sendRequest('loadMotocrossPlayerPoints', $params, true);
    }

    public function updateMotocrossPlayerResult($params)
    {

        return $this->sendRequest('updateMotocrossPlayerResult', $params, true);
    }

    public function countUserJoinedContest()
    {
        return $this->sendRequest('countUserJoinedContest', array(), false, false);
    }

    public function liveDraftLoadListUserInLeague($league_id, $page)
    {
        $data = $this->sendRequest('liveDraftLoadListUserInLeague', array(
            'leagueID' => $league_id,
            'page' => $page
                ), false, true);
        if ($data['data'])
        {
            $new_data = $this->parseUserData($data['data']);
            $data['data'] = $new_data;
        }
        return $data;
    }

    public function liveDraftSendTradePlayers($params)
    {
        return $this->send('liveDraftSendTradePlayers', $params);
    }

    public function liveDraftValidTradePlayer($params)
    {
        return $this->sendRequest('liveDraftValidTradePlayer', $params, false, false);
    }

    public function liveDraftGetDataTradePlayer($target_id, $league_id, $entry_number, $target_entry)
    {
        return $this->send('liveDraftGetDataTradePlayer', array(
            'leagueID' => $league_id,
            'target_id' => $target_id,
            'entry_number' => $entry_number,
            'target_entry' => $target_entry
        ));
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

    private function liveDraftParseStringTradePlayer($result, $players)
    {
        $str = array();
        foreach ($result as $item)
        {
            $info = explode('_', $item);
            $player_id = $info[0];
            $player_position = $info[1];
            $position_name = $players[$player_id]['position_name'];
            $player_name = $players[$player_id]['player_name'];
            $str[] = "($position_name) $player_name";
        }
        return implode(", ", $str);
    }

    public function sendLiveDraftRequestTradeMail($data)
    {
        $user_profile = $this->get_user_info($data['user_id']);
        $target_profile = $this->get_user_info($data['target_id']);

        $admin_email = (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email'));
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'To: ' . $target_profile->user_email . "\r\n";
        $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . $admin_email . ">\r\n";

        $to_mail = $target_profile->user_email;
        $message_subject = sprintf(__("%s would like to trade player with you", 'victorious'), $user_profile->display_name);
        // get string players
        $s_user_players = $this->liveDraftParseStringTradePlayer($data['user_positions'], $data['players']);
        $s_target_players = $this->liveDraftParseStringTradePlayer($data['target_positions'], $data['players']);
        $href_trade = VICTORIOUS_URL_GAME . $data['leagueID'].'?manage_trade_request&league_id=' . $data['leagueID'] . '&entry_number=' . $data['target_entry'];
        $amount = $data['amount'];
        $site_name = get_option('blogname');
        $league_name = $data['league']['name'];
                
        ob_start();
        require_once('admin/emailTemplates/live_draft_request_trade_player.php');
        $message_body = ob_get_clean();
        try
        {
            wp_mail($to_mail, $message_subject, $message_body, $headers);
        }
        catch (Exception $ex)
        {
            
        }
    }

    public function liveDraftRequestTradePlayertList($league_id, $entry_number)
    {
        return $this->send('liveDraftRequestTradePlayertList', array(
            'leagueID' => $league_id, 
            'entry_number' => $entry_number
        ));
    }

    public function liveDraftApprovedTradeRequest($request_id)
    {
        return $this->send('liveDraftApprovedTradeRequest', array('request_id' => $request_id));
    }

    public function liveDraftRejectTradeRequest($request_id)
    {
        return $this->send('liveDraftRejectTradeRequest', array('request_id' => $request_id));
    }

    public function liveDraftSendmailApproved($data)
    {
        $admin_email = (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email'));
        $sender = $data['user_id'];
        $sender = $this->get_user_info($sender);
        $target = $data['target_id'];
        $target = $this->get_user_info($target);
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'To: ' . $sender->user_email . "\r\n";
        $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . $admin_email . ">\r\n";
        $message_subject = __('Your trade player request has been approved', 'victorious');
        
        $change = !empty($data['change']) ? implode(", ", $data['change']) : "";
        $with = !empty($data['with']) ? implode(", ", $data['with']) : "";
        $site_name = get_option('blogname');
        $league_name = $data['league']['name'];
                
        ob_start();
        require_once('admin/emailTemplates/live_draft_approve_trade_player.php');
        $message_body = ob_get_clean();
        
        try
        {
            wp_mail($sender->user_email, $message_subject, $message_body, $headers);
        }
        catch (Exception $ex)
        {
            
        }
    }

    public function liveDraftSendmailReject($data)
    {
        $sender = $data['user_id'];
        $sender = $this->get_user_info($sender);
        $target = $data['target_id'];
        $target = $this->get_user_info($target);
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $admin_email = (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email'));
        $headers .= 'To: ' . $sender->user_email . "\r\n";
        $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . $admin_email . ">\r\n";
        $message_subject = __('Your trade player request has been rejected', 'victorious');
        
        $change = !empty($data['change']) ? implode(", ", $data['change']) : "";
        $with = !empty($data['with']) ? implode(", ", $data['with']) : "";
        $site_name = get_option('blogname');
        $league_name = $data['league']['name'];
                
        ob_start();
        require_once('admin/emailTemplates/live_draft_reject_trade_player.php');
        $message_body = ob_get_clean();
        
        try
        {
            wp_mail($sender->user_email, $message_subject, $message_body, $headers);
        }
        catch (Exception $ex)
        {
            
        }
    }

    public function liveDraftChangePlayerWaiverWire($date)
    {
        return $this->sendRequest('liveDraftChangePlayerWaiverWire', null, false);
    }

    public static function getCountryList()
    {
        return array(
            'AF' => 'AFGHANISTAN',
            'AX' => 'ÅLAND ISLANDS',
            'AL' => 'ALBANIA',
            'DZ' => 'ALGERIA',
            'AS' => 'AMERICAN SAMOA',
            'AD' => 'ANDORRA',
            'AO' => 'ANGOLA',
            'AI' => 'ANGUILLA',
            'AQ' => 'ANTARCTICA',
            'AG' => 'ANTIGUA AND BAR­BUDA',
            'AR' => 'ARGENTINA',
            'AM' => 'ARMENIA',
            'AW' => 'ARUBA',
            'AU' => 'AUSTRALIA',
            'AT' => 'AUSTRIA',
            'AZ' => 'AZERBAIJAN',
            'BS' => 'BAHAMAS',
            'BH' => 'BAHRAIN',
            'BD' => 'BANGLADESH',
            'BB' => 'BARBADOS',
            'BY' => 'BELARUS',
            'BE' => 'BELGIUM',
            'BZ' => 'BELIZE',
            'BJ' => 'BENIN',
            'BM' => 'BERMUDA',
            'BT' => 'BHUTAN',
            'BO' => 'BOLIVIA',
            'BA' => 'BOSNIA AND HERZE­GOVINA',
            'BW' => 'BOTSWANA',
            'BV' => 'BOUVET ISLAND',
            'BR' => 'BRAZIL',
            'IO' => 'BRITISH INDIAN OCEAN TERRITORY',
            'BN' => 'BRUNEI DARUSSALAM',
            'BG' => 'BULGARIA',
            'BF' => 'BURKINA FASO',
            'BI' => 'BURUNDI',
            'KH' => 'CAMBODIA',
            'CM' => 'CAMEROON',
            'CA' => 'CANADA',
            'CV' => 'CAPE VERDE',
            'KY' => 'CAYMAN ISLANDS',
            'CF' => 'CENTRAL AFRICAN REPUBLIC',
            'TD' => 'CHAD',
            'CL' => 'CHILE',
            'CN' => 'CHINA',
            'CX' => 'CHRISTMAS ISLAND',
            'CC' => 'COCOS (KEELING) ISLANDS',
            'CO' => 'COLOMBIA',
            'KM' => 'COMOROS',
            'CG' => 'CONGO',
            'CD' => 'CONGO, THE DEMO­CRATIC REPUBLIC OF THE',
            'CK' => 'COOK ISLANDS',
            'CR' => 'COSTA RICA',
            'CI' => 'COTE D IVOIRE',
            'HR' => 'CROATIA',
            'CU' => 'CUBA',
            'CY' => 'CYPRUS',
            'CZ' => 'CZECH REPUBLIC',
            'DK' => 'DENMARK',
            'DJ' => 'DJIBOUTI',
            'DM' => 'DOMINICA',
            'DO' => 'DOMINICAN REPUBLIC',
            'EC' => 'ECUADOR',
            'EG' => 'EGYPT',
            'SV' => 'EL SALVADOR',
            'GQ' => 'EQUATORIAL GUINEA',
            'ER' => 'ERITREA',
            'EE' => 'ESTONIA',
            'ET' => 'ETHIOPIA',
            'FK' => 'FALKLAND ISLANDS (MALVINAS)',
            'FO' => 'FAROE ISLANDS',
            'FJ' => 'FIJI',
            'FI' => 'FINLAND',
            'FR' => 'FRANCE',
            'GF' => 'FRENCH GUIANA',
            'PF' => 'FRENCH POLYNESIA',
            'TF' => 'FRENCH SOUTHERN TERRITORIES',
            'GA' => 'GABON',
            'GM' => 'GAMBIA',
            'GE' => 'GEORGIA',
            'DE' => 'GERMANY',
            'GH' => 'GHANA',
            'GI' => 'GIBRALTAR',
            'GR' => 'GREECE',
            'GL' => 'GREENLAND',
            'GD' => 'GRENADA',
            'GP' => 'GUADELOUPE',
            'GU' => 'GUAM',
            'GT' => 'GUATEMALA',
            'GG' => 'GUERNSEY',
            'GN' => 'GUINEA',
            'GW' => 'GUINEA-BISSAU',
            'GY' => 'GUYANA',
            'HT' => 'HAITI',
            'HM' => 'HEARD ISLAND AND MCDONALD ISLANDS',
            'VA' => 'HOLY SEE (VATICAN CITY STATE)',
            'HN' => 'HONDURAS',
            'HK' => 'HONG KONG',
            'HU' => 'HUNGARY',
            'IS' => 'ICELAND',
            'IN' => 'INDIA',
            'ID' => 'INDONESIA',
            'IR' => 'IRAN, ISLAMIC REPUB­LIC OF',
            'IQ' => 'IRAQ',
            'IE' => 'IRELAND',
            'IM' => 'ISLE OF MAN',
            'IL' => 'ISRAEL',
            'IT' => 'ITALY',
            'JM' => 'JAMAICA',
            'JP' => 'JAPAN',
            'JE' => 'JERSEY',
            'JO' => 'JORDAN',
            'KZ' => 'KAZAKHSTAN',
            'KE' => 'KENYA',
            'KI' => 'KIRIBATI',
            'KP' => 'KOREA, DEMOCRATIC PEOPLES REPUBLIC OF',
            'KR' => 'KOREA, REPUBLIC OF',
            'KW' => 'KUWAIT',
            'KG' => 'KYRGYZSTAN',
            'LA' => 'LAO PEOPLES DEMO­CRATIC REPUBLIC',
            'LV' => 'LATVIA',
            'LB' => 'LEBANON',
            'LS' => 'LESOTHO',
            'LR' => 'LIBERIA',
            'LY' => 'LIBYAN ARAB JAMA­HIRIYA',
            'LI' => 'LIECHTENSTEIN',
            'LT' => 'LITHUANIA',
            'LU' => 'LUXEMBOURG',
            'MO' => 'MACAO',
            'MK' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF',
            'MG' => 'MADAGASCAR',
            'MW' => 'MALAWI',
            'MY' => 'MALAYSIA',
            'MV' => 'MALDIVES',
            'ML' => 'MALI',
            'MT' => 'MALTA',
            'MH' => 'MARSHALL ISLANDS',
            'MQ' => 'MARTINIQUE',
            'MR' => 'MAURITANIA',
            'MU' => 'MAURITIUS',
            'YT' => 'MAYOTTE',
            'MX' => 'MEXICO',
            'FM' => 'MICRONESIA, FEDER­ATED STATES OF',
            'MD' => 'MOLDOVA, REPUBLIC OF',
            'MC' => 'MONACO',
            'MN' => 'MONGOLIA',
            'MS' => 'MONTSERRAT',
            'MA' => 'MOROCCO',
            'MZ' => 'MOZAMBIQUE',
            'MM' => 'MYANMAR',
            'NA' => 'NAMIBIA',
            'NR' => 'NAURU',
            'NP' => 'NEPAL',
            'NL' => 'NETHERLANDS',
            'AN' => 'NETHERLANDS ANTI­LLES',
            'NC' => 'NEW CALEDONIA',
            'NZ' => 'NEW ZEALAND',
            'NI' => 'NICARAGUA',
            'NE' => 'NIGER',
            'NG' => 'NIGERIA',
            'NU' => 'NIUE',
            'NF' => 'NORFOLK ISLAND',
            'MP' => 'NORTHERN MARIANA ISLANDS',
            'NO' => 'NORWAY',
            'OM' => 'OMAN',
            'PK' => 'PAKISTAN',
            'PW' => 'PALAU',
            'PS' => 'PALESTINIAN TERRI­TORY, OCCUPIED',
            'PA' => 'PANAMA',
            'PG' => 'PAPUA NEW GUINEA',
            'PY' => 'PARAGUAY',
            'PE' => 'PERU',
            'PH' => 'PHILIPPINES',
            'PN' => 'PITCAIRN',
            'PL' => 'POLAND',
            'PT' => 'PORTUGAL',
            'PR' => 'PUERTO RICO',
            'QA' => 'QATAR',
            'RE' => 'REUNION',
            'RO' => 'ROMANIA',
            'RU' => 'RUSSIAN FEDERATION',
            'RW' => 'RWANDA',
            'SH' => 'SAINT HELENA',
            'KN' => 'SAINT KITTS AND NEVIS',
            'LC' => 'SAINT LUCIA',
            'PM' => 'SAINT PIERRE AND MIQUELON',
            'VC' => 'SAINT VINCENT AND THE GRENADINES',
            'WS' => 'SAMOA',
            'SM' => 'SAN MARINO',
            'ST' => 'SAO TOME AND PRINC­IPE',
            'SA' => 'SAUDI ARABIA',
            'SN' => 'SENEGAL',
            'CS' => 'SERBIA AND MON­TENEGRO',
            'SC' => 'SEYCHELLES',
            'SL' => 'SIERRA LEONE',
            'SG' => 'SINGAPORE',
            'SK' => 'SLOVAKIA',
            'SI' => 'SLOVENIA',
            'SB' => 'SOLOMON ISLANDS',
            'SO' => 'SOMALIA',
            'ZA' => 'SOUTH AFRICA',
            'GS' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS',
            'ES' => 'SPAIN',
            'LK' => 'SRI LANKA',
            'SD' => 'SUDAN',
            'SR' => 'SURINAME',
            'SJ' => 'SVALBARD AND JAN MAYEN',
            'SZ' => 'SWAZILAND',
            'SE' => 'SWEDEN',
            'CH' => 'SWITZERLAND',
            'SY' => 'SYRIAN ARAB REPUB­LIC',
            'TW' => 'TAIWAN, PROVINCE OF CHINA',
            'TJ' => 'TAJIKISTAN',
            'TZ' => 'TANZANIA, UNITED REPUBLIC OF',
            'TH' => 'THAILAND',
            'TL' => 'TIMOR-LESTE',
            'TG' => 'TOGO',
            'TK' => 'TOKELAU',
            'TO' => 'TONGA',
            'TT' => 'TRINIDAD AND TOBAGO',
            'TN' => 'TUNISIA',
            'TR' => 'TURKEY',
            'TM' => 'TURKMENISTAN',
            'TC' => 'TURKS AND CAICOS ISLANDS',
            'TV' => 'TUVALU',
            'UG' => 'UGANDA',
            'UA' => 'UKRAINE',
            'AE' => 'UNITED ARAB EMIR­ATES',
            'GB' => 'UNITED KINGDOM',
            'US' => 'UNITED STATES',
            'UM' => 'UNITED STATES MINOR OUTLYING ISLANDS',
            'UY' => 'URUGUAY',
            'UZ' => 'UZBEKISTAN',
            'VU' => 'VANUATU',
            'VE' => 'VENEZUELA',
            'VN' => 'VIET NAM',
            'VG' => 'VIRGIN ISLANDS, BRIT­ISH',
            'VI' => 'VIRGIN ISLANDS, U.S.',
            'WF' => 'WALLIS AND FUTUNA',
            'EH' => 'WESTERN SAHARA',
            'YE' => 'YEMEN',
            'ZM' => 'ZAMBIA',
            'ZW' => 'ZIMBABWE',
        );
    }

    public function sendLiveDraftChangePlayerWaiverWireEmail($user_id, $league_id, $change_list, $league)
    {
        $admin_email = (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email'));
        $user_profile = $this->get_user_info($user_id);
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'To: ' . $user_profile->user_email . "\r\n";
        $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . $admin_email . ">\r\n";

        $to_mail = $user_profile->user_email;
        $message_subject = sprintf(__("Weekly Waiver Wire", 'victorious'));
        require('admin/emailTemplates/live_draft_allow_waiver_wire.php');

        try
        {
            wp_mail($to_mail, $message_subject, $message_body, $headers);
            return true;
        }
        catch (Exception $ex)
        {
            return false;
        }
    }

    public function sendLiveDraftAutoPickPlayerEmail($user_id, $data)
    {
        $admin_email = (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email'));
        $user_profile = $this->get_user_info($user_id);
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

        $headers .= 'To: ' . $user_profile->user_email . "\r\n";

        $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . $admin_email . ">\r\n";
        $message_subject = sprintf(__("System auto picked players", 'victorious'));
        $to_mail = $user_profile->user_email;
        require('admin/emailTemplates/live_draft_auto_pick_players.php');
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

    public function joinLiveDraftContest($leagueID)
    {
        return $this->sendRequest('joinLiveDraftContest', array(
                    'leagueID' => $leagueID
                        ), false, false);
    }

    public function liveDraftStart()
    {
        return $this->sendRequest('liveDraftStart', null, false);
    }

    public function sendLiveDraftStartDraftEmail($user_id, $data)
    {
        $admin_email = (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email'));
        $user_profile = $this->get_user_info($user_id);
        $href_change = VICTORIOUS_URL_GAME . $data['leagueID'] . '/?num=1&action=3';
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";

        $headers .= 'To: ' . $user_profile->user_email . "\r\n";

        $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . $admin_email . ">\r\n";
        $message_subject = sprintf(__("Your draft is about to start", 'victorious'));
        $to_mail = $user_profile->user_email;
        require('admin/emailTemplates/live_draft_start_draft.php');
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

    public function sendLiveDraftCancelContestEmail($user_id, $data)
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
        require('admin/emailTemplates/live_draft_cancel_contest.php');
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

    public function liveDraftPickPlayer($params)
    {
        $data = $this->send("liveDraftPickPlayer", $params);
        return $this->liveDraftParseLastPick($data);
    }

    public function liveDraftPickBenchPlayer($params)
    {
        return $this->sendRequest("liveDraftPickBenchPlayer", $params, false);
    }

    public function liveDraftData($leagueID, $entry_number, $current_turn)
    {
        $data = $this->sendRequest("liveDraftData", array(
            "leagueID" => $leagueID,
            "entry_number" => $entry_number,
            "current_turn" => $current_turn
                ), false);
        $data['current_turn_user'] = '';
        $data['next_turn_user'] = '';
        $data['last_pick_username'] = '';
        if (!empty($data['next_turn']))
        {
            $temp = array();
            foreach ($data['next_turn'] as $user_id)
            {
                $user = $this->getPlayerInfo($user_id);
                $temp[] = $user['user_login'];
            }
            $data['next_turn_user'] = implode(", ", $temp);
        }
        if (!empty($data['current_turn']))
        {
            $user = $this->getPlayerInfo($data['current_turn']);
            $data['current_turn_user'] = $user['user_login'];
        }
        if (!empty($data['last_pick_user_id']))
        {
            $user = $this->getPlayerInfo($data['last_pick_user_id']);
            $data['last_pick_username'] = $user['user_login'];
        }
        return $data;
    }

    public function liveDraftLoadOpponentScores($params)
    {
        $data = $this->sendRequest("liveDraftLoadOpponentScores", $params, false); //exit($data);
        if ($data != null)
        {
            foreach ($data as $k => $item)
            {
                $user = $this->getPlayerInfo($data[$k]['user']['id']);
                $data[$k]['user']['username'] = $user['user_login'];
                $data[$k]['user']['avatar'] = $this->get_avatar_url($this->get_avatar($data[$k]['user']['id']));
            }
        }
        return $data;
    }

    public function liveDraftLoadContestScores($params)
    {
        $aDatas = $this->sendRequest("liveDraftLoadContestScores", $params, false);
        return $this->parseUserData($aDatas);
    }

    public function liveDraftCheckChangedTurn($params)
    {
        $data = $this->sendRequest("liveDraftCheckChangedTurn", $params, false);
        return $this->liveDraftParseLastPick($data);
    }

    public function liveDraftRequestChangePlayer($params)
    {
        return $this->sendRequest("liveDraftRequestChangePlayer", $params, false);
    }

    private function liveDraftParseLastPick($data)
    {
        $data['last_pick_username'] = '';
        if ($data['last_pick_user_id'] > 0)
        {
            $user = $this->getPlayerInfo($data['last_pick_user_id']);
            $data['last_pick_username'] = $user['user_login'];
        }
        if (!empty($data['next_turn']))
        {
            $temp = array();
            foreach ($data['next_turn'] as $user_id)
            {
                $user = $this->getPlayerInfo($user_id);
                $temp[] = $user['user_login'];
            }
            $data['next_turn_user'] = implode(", ", $temp);
        }
        if (!empty($data['current_turn']))
        {
            $user = $this->getPlayerInfo($data['current_turn']);
            $data['current_turn_user'] = $user['user_login'];
        }
        return $data;
    }

    public function liveDraftGetUserInDraftRoom($params)
    {
        $data = $this->sendRequest("liveDraftGetUserInDraftRoom", $params, false);
        return $this->parseUserData($data);
    }

    public function liveDraftSeeUserLineup($params)
    {
        $data = $this->send("liveDraftSeeUserLineup", $params);
        if ($data != null)
        {
            foreach ($data as $k => $item)
            {
                if ($item['siteID'] > 0)
                {
                    $data[$k]['full_image_path'] = VICTORIOUS_IMAGE_URL . $this->replaceSuffix($item['image']);
                }
                else
                {
                    $data[$k]['full_image_path'] = $this->replaceSuffix($item['image'], '');
                }
            }
        }
        return $data;
    }

    public function liveDraftCheckAllowDraft($params)
    {
        return $this->sendRequest("liveDraftCheckAllowDraft", $params, false);
    }

    public function getWeeklyPlayerStats()
    {
        return $this->sendRequest("weeklyPlayerStats", null, false);
    }

    public function sendWeeklyPlayerStats()
    {
        $admin_email = (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email'));
        $to_mail = get_option('victorious_weekly_statistic_email');
        $filename = 'weekly_stats.csv';
        $filepath = VICTORIOUS__PLUGIN_DIR . 'assets/' . $filename;
        if ($to_mail == null || !file_exists($filepath))
        {
            return false;
        }

        $multipartSep = '-----' . md5(time()) . '-----';
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-Type: multipart/mixed; boundary="' . $multipartSep . '"' . "\r\n";
        $headers .= 'To: ' . $to_mail . "\r\n";
        $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . $admin_email . ">\r\n";

        $message_subject = sprintf(__("Weekly Statistic", 'victorious'));
        $csv = fopen($filepath, 'r');
        $attachment = stream_get_contents($csv);
        fclose($csv);

        $message_body = '';
        require('admin/emailTemplates/weekly_statistic.php');
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

    public function loadStatsSportInfo($sport_id)
    {
        return $this->send("loadStatsSportInfo", array(
            'sport_id' => $sport_id
        ));
    }

    public function loadStatsData($sport_id, $pool_id, $team_id, $position_id, $sort_name, $sort_value, $page)
    {
        return $this->send("loadStatsData", array(
            'sport_id' => $sport_id,
            'sort_name' => $sort_name,
            'sort_value' => $sort_value,
            'page' => $page,
            'pool_id' => $pool_id,
            'team_id' => $team_id,
            'position_id' => $position_id,
        ));
    }

    public function loadStatsSport($params = null)
    {
        return $this->sendRequest("loadStatsSport", $params, false);
    }

    public function rugbyLoadStatsInfo($params = null)
    {
        return $this->sendRequest("rugbyLoadStatsInfo", $params, false);
    }

    public function rugbyLoadStatsData($league_id, $keyword, $free_agent, $scoring_category_id, $position_id, $sort_name, $sort_value, $page)
    {
        $data = $this->sendRequest("rugbyLoadStatsData", array(
            'league_id' => $league_id,
            'sort_name' => $sort_name,
            'sort_value' => $sort_value,
            'page' => $page,
            'keyword' => $keyword,
            'free_agent' => $free_agent,
            'scoring_category_id' => $scoring_category_id,
            'position_id' => $position_id,
        ), false);
        if (!empty($data['playerstats']))
        {
            foreach ($data['playerstats'] as $k => $item)
            {
                $data['playerstats'][$k]['owner'] = '';
                if ($item['userID'] == null)
                {
                    continue;
                }
                $user = $this->getPlayerInfo($item['userID']);
                $data['playerstats'][$k]['owner'] = $user['user_login'];
            }
        }
        return $data;
    }

    public function checkEntryCloseContest()
    {
        return $this->sendRequest("checkEntryCloseContest", null, false);
    }

    public function suggestUsername($keyword)
    {
        global $wpdb;
        $keyword = str_replace("'", "\'", $keyword);
        $table_user = $wpdb->prefix . 'users';
        $sql = "SELECT * "
                . "FROM $table_user "
                . "WHERE user_login LIKE '%$keyword%' AND ID != " . VIC_GetUserId();
        $result = $wpdb->get_results($sql);
        return json_decode(json_encode($result), true);
    }

    public function getGlobalSetting()
    {
        return $this->send("getGlobalSetting");
    }

    public function getPremiumFeatures()
    {
        return $this->sendRequest("getPremiumFeatures", null, false);
    }

    public function sendInjuiryEmail()
    {
        $result = $this->sendRequest("getInjuredPlayersToSendEmail", null, false);

        if ($result == null)
        {
            return;
        }

        //get user list
        $user_ids = array();
        $users = array();
        foreach ($result as $league)
        {
            if (empty($league['users']))
            {
                continue;
            }
            foreach ($league['users'] as $user_id)
            {
                if (in_array($user_id, $user_ids))
                {
                    continue;
                }
                $user_ids[] = $user_id;
                $userdata = get_user_by('ID', $user_id);
                $users[$user_id] = $userdata;
            }
        }

        //send email
        foreach ($result as $league_id => $league)
        {
            if (empty($league['injury_list']))
            {
                continue;
            }
            $league_name = $league['league_name'];
            $start_date = $league['start_date'];
            $injury_list = $league['injury_list'];
            $contest_url = "http://" . sanitize_url($_SERVER['SERVER_NAME']) . '/fantasy/game/' . $league_id;
            $player_names = array();
            foreach ($injury_list as $injury)
            {
                $player_names[] = $injury['name'];
            }
            foreach ($league['users'] as $user_id)
            {
                $user = $users[$user_id];
                $email = $user->data->user_email;
                $headers = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
                $headers .= 'To: ' . $email . "\r\n";
                $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email')) . ">\r\n";

                include 'admin/emailTemplates/injury_email.php';
                try
                {
                    wp_mail($email, $message_subject, $message_body, $headers);
                }
                catch (Exception $ex)
                {
                    
                }
            }
        }
    }

    public function updateCoinExchangeRate()
    {
    }

    public function getFeatureLeagues()
    {
        //return array();
        $leagues = $this->sendRequest("getLeagueLobby", array(
            'is_feature' => 1
                ), false);
        if ($leagues != null)
        {
            foreach ($leagues as $k => $league)
            {
                $leagues[$k]['feature_image_url'] = VICTORIOUS_IMAGE_URL . $league['feature_image'];
                $leagues[$k]['url'] = VICTORIOUS_URL_GAME . $league['leagueID'].($league['multi_entry'] == 1 && isset($league['next_entry']) ? "?num=".$league['next_entry'] : "");
            }
        }
        return $leagues;
    }

    public function validEnterNormalGame($leagueID)
    {
        return $this->sendRequest("validEnterNormalGame", array("leagueID" => $leagueID), false, false);
    }

    public function validateForAllGameType($league_id, $entry_number = 1)
    {
        return $this->send("validateForAllGameType", array(
            "league_id" => $league_id,
            "entry_number" => $entry_number
        ));
    }

    /////////////////////////////////////roundpickem/////////////////////////////////////
    public function getRoundPickemContest($league_id, $entry_number)
    {
        $data = $this->send("getRoundPickemContest", array(
            "league_id" => $league_id,
            "entry_number" => $entry_number
        ));
        if (!empty($data['fights']))
        {
            foreach ($data['fights'] as $k => $fights)
            {
                $data['fights'][$k] = $this->parseFightData($fights);
            }
        }
        return $data;
    }

    public function getRoundPickemCurrentWeek($leagueID)
    {
        return $this->sendRequest("getRoundPickemCurrentWeek", array(
                    "leagueID" => $leagueID,
                        ), false);
    }

    private function parseFightData($fights)
    {
        if (!empty($fights))
        {
            foreach ($fights as $k => $v)
            {
                if ($v['siteID'] == 0)
                {
                    $fights[$k]['full_image_path1'] = !empty($v['full_image_path1']) ? sprintf($v['full_image_path1'], "") : "";
                    $fights[$k]['full_image_path2'] = !empty($v['full_image_path2']) ? sprintf($v['full_image_path2'], "") : "";
                    continue;
                }
                $fights[$k]['full_image_path1'] = !empty($v['image1']) ? VICTORIOUS_IMAGE_URL . $this->replaceSuffix($v['image1']) : "";
                $fights[$k]['full_image_path2'] = !empty($v['image2']) ? VICTORIOUS_IMAGE_URL . $this->replaceSuffix($v['image2']) : "";
            }
        }
        return $fights;
    }

    public function getRoundPickemResult($league_id, $week, $page = 1)
    {
        $data = $this->send("getRoundPickemResult", array(
            "league_id" => $league_id,
            "week" => $week,
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

    public function getRoundPickemResultDetail($league_id, $week, $user_id, $entry_number, $opponent_id, $opponent_entry_number)
    {
        return $this->send("getRoundPickemResultDetail", array(
                    "league_id" => $league_id,
                    "week" => $week,
                    "user_id" => $user_id,
                    "entry_number" => $entry_number,
                    "opponent_id" => $opponent_id,
                    "opponent_entry_number" => $opponent_entry_number,
        ));
    }

    public function roundPickemWeeklyWinner()
    {
        return $this->sendRequest("roundPickemNotification", array('weekly_winner' => 1), false);
    }

    public function roundPickemReminder()
    {
        return $this->sendRequest("roundPickemNotification", array('reminder' => 1), false);
    }

    public function sendRoundPickemWeeklyNotification($league, $week, $user_id)
    {
        $admin_email = (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email'));
        $user_profile = $this->get_user_info($user_id);
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'To: ' . $user_profile->user_email . "\r\n";
        $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . $admin_email . ">\r\n";
        $to_mail = $user_profile->user_email;
        $message_subject = sprintf(__("New picks for week %s of league %s", 'victorious'), $week, str_replace('&#39;', "'", $league['name']));

        ob_start();
        require('admin/emailTemplates/roundpickem_reminder.php');
        $message_body = ob_get_clean();
        try
        {
            wp_mail($to_mail, $message_subject, $message_body, $headers);
            return true;
        }
        catch (Exception $ex)
        {
            return false;
        }
        return false;
    }

    public function sendRoundPickemWeeklyWinner($league, $week, $user_id, $winner_id)
    {
        $admin_email = (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email'));
        $user_profile = $this->get_user_info($user_id);
        $winner_profile = $this->get_user_info($winner_id);
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'To: ' . $user_profile->user_email . "\r\n";
        $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . $admin_email . ">\r\n";
        $to_mail = $user_profile->user_email;
        $message_subject = sprintf(__("Results for week %s of league %s are in!", 'victorious'), $week - 1, str_replace('&#39;', "'", $league['name']));

        ob_start();
        require('admin/emailTemplates/roundpickem_weekly_winner.php');
        $message_body = ob_get_clean();

        try
        {
            wp_mail($to_mail, $message_subject, $message_body, $headers);
            return true;
        }
        catch (Exception $ex)
        {
            return false;
        }
        return false;
    }

    public function submitUserPickRoundPickem($league_id, $entry_number, $winners, $predict_points)
    {
        return $this->send("submitUserPickRoundPickem", array(
            'league_id' => $league_id,
            'entry_number' => $entry_number,
            'winners' => $winners,
            'predict_points' => $predict_points
        ));
    }

    /////////////////////////////////////end roundpickem/////////////////////////////////////
    /////////////////////////////////////buddy press integration/////////////////////////////////////
    public function checkBuddyPressInstalled()
    {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        return is_plugin_active('buddypress/bp-loader.php');
    }

    public function addCreateContestActivity($league, $user_id)
    {
        if (empty($league) || !function_exists('bp_activity_add'))
        {
            return;
        }
        $global_setting = $this->getGlobalSetting();
        if ($global_setting['buddy_press_integration'] && $this->checkBuddyPressInstalled())
        {
            $link = bp_core_get_userlink($user_id);
            bp_activity_add(array(
                'user_id' => $user_id,
                'component' => 'activity',
                'type' => 'fv_create_contest',
                'action' => $link . ' just created a contest',
                'content' => $this->activityContestTemplate($league),
                'item_id' => $league['leagueID']
            ));
        }
    }

    public function addEnterContestActivity($league, $user_id)
    {
        if (empty($league) || !function_exists('bp_activity_add'))
        {
            return;
        }
        $global_setting = $this->getGlobalSetting();
        if ($global_setting['buddy_press_integration'] && $this->checkBuddyPressInstalled())
        {
            $link = bp_core_get_userlink($user_id);
            bp_activity_add(array(
                'user_id' => $user_id,
                'component' => 'activity',
                'type' => 'fv_enter_contest',
                'action' => $link . ' just entered a contest',
                'content' => $this->activityContestTemplate($league),
                'item_id' => $league['leagueID']
            ));
        }
    }

    public function addWonContestActivity($league, $user_id)
    {
        if (empty($league) || !function_exists('bp_activity_add'))
        {
            return;
        }
        $global_setting = $this->getGlobalSetting();
        if ($global_setting['buddy_press_integration'] && $this->checkBuddyPressInstalled())
        {
            $link = bp_core_get_userlink($user_id);
            bp_activity_add(array(
                'user_id' => $user_id,
                'component' => 'activity',
                'type' => 'fv_won_contest',
                'action' => $link . ' won a contest',
                'content' => $this->activityContestTemplate($league),
                'item_id' => $league['leagueID']
            ));
        }
    }

    private function activityContestTemplate($league)
    {
        return 'Sport: ' . $league['sport_name'] . '<br/>
            Name: ' . $league['name'] . '<br/>
            Entry fee: ' . ($league['entry_fee'] > 0 ? VIC_FormatMoney($league['entry_fee']) : __("Free", 'victorious')) . '<br/>
            Prize: ' . VIC_FormatMoney($league['prizes']) . '<br/>
            Start time: ' . $league['startDate'] . '<br/>
            <a href="' . VICTORIOUS_URL_SUBMIT_PICKS . $league['leagueID'] . '">View Contest</a>';
    }

    public function deleteContestActivities($league_id)
    {
        if (!function_exists('bp_activity_delete'))
        {
            return;
        }
        bp_activity_delete(array(
            'item_id' => $league_id,
            'type' => 'fv_create_contest'
        ));
        bp_activity_delete(array(
            'item_id' => $league_id,
            'type' => 'fv_enter_contest'
        ));
        bp_activity_delete(array(
            'item_id' => $league_id,
            'type' => 'fv_won_contest'
        ));
    }

    /////////////////////////////////////end buddy press integration/////////////////////////////////////
    /////////////////////////////////////live score/////////////////////////////////////
    public function getSportTree($team_sport = "")
    {
        return $this->send("getSportTree", array(
                    "team_sport" => $team_sport
        ));
    }

    public function getLatestDailyEvents($sport_id)
    {
        return $this->send("getLatestDailyEvents", array(
                    "sport_id" => $sport_id
        ));
    }

    public function getTeamDetail($team_id)
    {
        return $this->send("getTeamDetail", array(
                    "team_id" => $team_id,
        ));
    }

    public function getFixtureScores($event_id)
    {
        return $this->send("getFixtureScores", array(
                    "event_id" => $event_id
        ));
    }

    public function liveScoreTeamSchedule($team_id, $all = false)
    {
        return $this->send("getTeamSchedule", array(
                    "team_id" => $team_id,
                    "all" => $all
        ));
    }

    public function liveScoreTeamRoster($team_id, $sort_by = "", $sort_type = "")
    {
        return $this->send("getTeamRoster", array(
                    "team_id" => $team_id,
                    "sort_by" => $sort_by,
                    "sort_type" => $sort_type,
        ));
    }

    public function liveScoreTeamStatistic($params)
    {
        return $this->send("getTeamStatistic", array(
                    "team_id" => $params['team_id'],
                    "position_id" => !empty($params['position_id']) ? $params['position_id'] : "",
                    "keyword" => !empty($params['keyword']) ? $params['keyword'] : "",
                    "page" => !empty($params['page']) ? $params['page'] : 1,
                    "sort_by" => !empty($params['sort_by']) ? $params['sort_by'] : "",
                    "sort_type" => !empty($params['sort_type']) ? $params['sort_type'] : "",
                    "sort_scoring_id" => !empty($params['sort_scoring_id']) ? $params['sort_scoring_id'] : ""
        ));
    }

    public function liveScoreTeamInjuries($team_id)
    {
        return $this->send("getTeamInjuries", array(
                    "team_id" => $team_id,
        ));
    }

    /////////////////////////////////////end live score/////////////////////////////////////
    /////////////////////////////////////firebase push notification/////////////////////////////////////
    public function isFirebaseTokenExist($user_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'firebase_tokens';
        $sCond = "WHERE user_id = " . (int) $user_id;
        $sql = "SELECT COUNT(*) "
                . "FROM $table_name "
                . $sCond;
        $data = $wpdb->get_var($sql);
        if ($data > 0)
        {
            return true;
        }
        return false;
    }

    public function subscribePushNotification($user_id, $token)
    {
        if (empty($user_id) || empty($token))
        {
            return;
        }
        global $wpdb;
        $values = array('user_id' => $user_id, 'token' => $token);
        $table_name = $wpdb->prefix . 'firebase_tokens';
        if ($this->isFirebaseTokenExist($user_id))
        {
            return $wpdb->update($table_name, $values, array('user_id' => $user_id));
        }
        else
        {
            return $wpdb->insert($table_name, $values);
        }
    }

    public function unSubscribePushNotification($user_id)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'firebase_tokens';

        $wpdb->delete($table_name, array('user_id' => $user_id));
    }

    public function getFirebaseSubscribers($user_ids = null)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'firebase_tokens';

        $conds = "";
        if ($user_ids != null && is_array($user_ids))
        {
            $conds = "WHERE user_id IN(" . implode(',', $user_ids) . ")";
        }
        else if ($user_ids != null)
        {
            $conds = "WHERE user_id IN(" . $user_ids . ")";
        }
        $sql = "SELECT * "
                . "FROM $table_name $conds ";
        return $wpdb->get_results($sql, ARRAY_A);
    }

    public function pushNotification($token, $title, $body, $link, $image = "")
    {
        $apikey = get_option('victorious_firebase_apikey');
        $apikey = trim($apikey);
        if (empty($apikey))
        {
            return;
        }
        $data = array(
            "to" => $token,
            "notification" => array(
                "title" => $title,
                "body" => $body,
                "icon" => $image,
                "click_action" => $link
            )
        );

        $args = array(
            'body'        => json_encode($data),
            'blocking'    => true,
            'headers'     => array(
                'Content-Type: application/json',
                'Authorization: key=' . $apikey
            ),
            'cookies'     => array(),
        );
        wp_remote_post("https://fcm.googleapis.com/fcm/send", $args);
    }

    public function sendNotificationUserJoinContest($user_ids, $user_id, $league_name, $league_link)
    {
        //$title = __('Join contest', 'victorious');
        $title = "";
        $user = get_user_by('ID', $user_id);
        $body = sprintf(__('User %s joined "%s" league', 'victorious'), $user->data->user_login, $league_name);
        ;
        $subscribers = $this->getFirebaseSubscribers($user_ids);
        if ($subscribers != null)
        {
            $image = VICTORIOUS__PLUGIN_URL_IMAGE . "fanjago_logo.png";
            foreach ($subscribers as $subscriber)
            {
                $this->pushNotification($subscriber['token'], $title, $body, $league_link, $image);
            }
        }
    }

    public function sendNotificationTeamScore($user_ids, $team_name1, $team_name2, $team_score1, $team_score2, $player_score, $minute, $link)
    {
        //$title = __('Team score', 'victorious');
        $title = "";
        $body = sprintf(__("Goal: %s %s' (%s %s - %s %s)", 'victorious'), $player_score, $minute, $team_name1, $team_score1, $team_score2, $team_name2);
        $subscribers = $this->getFirebaseSubscribers($user_ids);
        if ($subscribers != null)
        {
            $image = VICTORIOUS__PLUGIN_URL_IMAGE . "fanjago_logo.png";
            foreach ($subscribers as $subscriber)
            {
                $this->pushNotification($subscriber['token'], $title, $body, $link, $image);
            }
        }
    }

    public function sendNotificationRedCard($user_ids, $player_name, $minute, $link)
    {
        //$title = __('Player red card', 'victorious');
        $title = "";
        $body = sprintf(__("Red Card: %s %s' ", 'victorious'), $player_name, $minute);
        $subscribers = $this->getFirebaseSubscribers($user_ids);
        if ($subscribers != null)
        {
            $image = VICTORIOUS__PLUGIN_URL_IMAGE . "fanjago_logo.png";
            foreach ($subscribers as $subscriber)
            {
                $this->pushNotification($subscriber['token'], $title, $body, $link, $image);
            }
        }
    }

    public function sendNotificationContestStart($user_ids, $time, $team_name1, $team_name2, $link)
    {
        //$title = __('Contest starts', 'victorious');
        $title = "";
        $body = sprintf(__('Kick-off (%s): %s - %s', 'victorious'), $time, $team_name1, $team_name2);
        $subscribers = $this->getFirebaseSubscribers($user_ids);
        if ($subscribers != null)
        {
            $image = VICTORIOUS__PLUGIN_URL_IMAGE . "fanjago_logo.png";
            foreach ($subscribers as $subscriber)
            {
                $this->pushNotification($subscriber['token'], $title, $body, $link, $image);
            }
        }
    }

    public function sendNotificationContestEnd($user_ids, $team_name1, $team_name2, $team_score1, $team_score2, $link)
    {
        //$title = __('Contest ends', 'victorious');
        $title = "";
        $body = sprintf(__('Full-time: %s %s - %s %s', 'victorious'), $team_name1, $team_score1, $team_score2, $team_name2);
        $subscribers = $this->getFirebaseSubscribers($user_ids);
        if ($subscribers != null)
        {
            $image = VICTORIOUS__PLUGIN_URL_IMAGE . "fanjago_logo.png";
            foreach ($subscribers as $subscriber)
            {
                $this->pushNotification($subscriber['token'], $title, $body, $link, $image);
            }
        }
    }

    public function getUserIdsJoinContest($league_id, $except_id = null)
    {
        return $this->send("getUserIdsJoinContest", array(
                    "league_id" => $league_id,
                    "except_id" => $except_id
        ));
    }

    /////////////////////////////////////end firebase push notification/////////////////////////////////////
    /////////////////////////////////////bracket/////////////////////////////////////
    public function getBracketGame($league_id, $entry_number)
    {
        return $this->send("getBracketGame", array(
                    "league_id" => $league_id,
                    "entry_number" => $entry_number
        ));
    }

    public function submitPickBracket($league_id, $entry_number, $team_ids)
    {
        $params = array(
            'league_id' => $league_id,
            'entry_number' => $entry_number,
            'team_ids' => $team_ids
        );
        return $this->send("submitPickBracket", $params);
    }

    public function bracketGroupTeam($data, $knockout = false)
    {
        $group_left = array();
        $group_right = array();
        if ($data == null)
        {
            return array($group_left, $group_right);
        }
        if (!$knockout)
        {
            foreach ($data as $k => $team_group)
            {
                if ($k % 2 == 0)
                {
                    $group_left[] = $team_group;
                }
                else
                {
                    $group_right[] = $team_group;
                }
            }
        }
        else
        {
            $change = 0;
            foreach ($data as $k => $team_group)
            {
                if ($k > 0 && $k % 2 == 0)
                {
                    $change = ($change == 0) ? 1 : 0;
                }
                if ($change == 0)
                {
                    $group_left[] = $team_group;
                }
                else
                {
                    $group_right[] = $team_group;
                }
            }
        }
        return array($group_left, $group_right);
    }

    public function bracketResult($league_id, $page = 1)
    {
        $data = $this->send("getBracketResult", array(
            "league_id" => $league_id,
            "page" => $page
        ));
        if (!empty($data['scores']))
        {
            $data['scores'] = $this->parseUserData($data['scores']);
        }
        return $data;
    }

    public function bracketResultDetail($league_id, $user_id, $entry_number)
    {
        return $this->send("getBracketResultDetail", array(
                    "league_id" => $league_id,
                    "user_id" => $user_id,
                    "entry_number" => $entry_number
        ));
    }

    /////////////////////////////////////end bracket/////////////////////////////////////
    /////////////////////////////////////goliath/////////////////////////////////////
    public function validateEnterGoliath($league_id, $entry_number)
    {
        return $this->send("validateEnterGoliath", array(
                    "league_id" => $league_id,
                    "entry_number" => $entry_number
        ));
    }

    public function getGoliathContest($league_id, $entry_number)
    {
        return $this->send("getGoliathContest", array(
                    "league_id" => $league_id,
                    "entry_number" => $entry_number
        ));
    }

    public function getGoliathContestResult($league_id)
    {
        return $this->send("getGoliathContestResult", array(
                    "league_id" => $league_id
        ));
    }

    public function validateSubmitGoliath($league_id, $entry_number, $winners)
    {
        return $this->send("validateSubmitGoliath", array(
            'league_id' => $league_id,
            'entry_number' => $entry_number,
            'winners' => $winners
        ));
    }

    public function submitGoliath($league_id, $entry_number, $winners, $invitedby = '')
    {
        if (!empty($invitedby))
        {
            $invitedby = get_user_by("login", $invitedby);
            $invitedby = $invitedby != null ? $invitedby->data->ID : 0;
        }
        return $this->send("submitGoliath", array(
            'league_id' => $league_id,
            'entry_number' => $entry_number,
            'winners' => $winners,
            'invitedby' => $invitedby
        ));
    }

    public function goliathResult($league_id, $standing_type, $page = 1)
    {
        $data = $this->send("getGoliathResult", array(
            "league_id" => $league_id,
            "standing_type" => $standing_type,
            "page" => $page
        ));
        if (!empty($data['scores']))
        {
            $data['scores'] = $this->parseUserData($data['scores']);
        }
        return $data;
    }

    public function goliathResultDetail($league_id, $user_id, $entry_number, $opponent_id, $opponent_entry_number)
    {
        return $this->send("getGoliathResultDetail", array(
                    "league_id" => $league_id,
                    "user_id" => $user_id,
                    "entry_number" => $entry_number,
                    "opponent_id" => $opponent_id,
                    "opponent_entry_number" => $opponent_entry_number,
        ));
    }

    public function goliathContestStats($league_id, $week, $fight_id)
    {
        return $this->send("getGoliathContestStats", array(
                    "league_id" => $league_id,
                    "week" => $week,
                    "fight_id" => $fight_id
        ));
    }

    public function sendSurvivorReminderEmail($user_id, $league, $fight)
    {
        $admin_email = (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email'));
        $user_profile = $this->get_user_info($user_id);
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'To: ' . $user_profile->user_email . "\r\n";
        $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . $admin_email . ">\r\n";
        $to_mail = $user_profile->user_email;
        $message_subject = sprintf(__('%s Upcoming Picks', 'victorious'), (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')));

        ob_start();
        require('admin/emailTemplates/survivor_reminder.php');
        $message_body = str_replace('[site_logo]', (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')), $message_body);
        $message_body = str_replace('[username]', $user_profile->user_login, $message_body);
        $message_body = str_replace('[round]', $fight['week'], $message_body);
        $message_body = str_replace('[game_type]', VIC_ParseGameTypeName($league['gameType']), $message_body);
        $message_body = str_replace('[start_time]', $league['startDate'], $message_body);

        try
        {
            wp_mail($to_mail, $message_subject, $message_body, $headers);
            return true;
        }
        catch (Exception $ex)
        {
            return false;
        }
        return false;
    }

    public function sendSurvivorReminderDecisionEmail($user_id, $league)
    {
        $admin_email = (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email'));
        $user_profile = $this->get_user_info($user_id);
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'To: ' . $user_profile->user_email . "\r\n";
        $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . $admin_email . ">\r\n";
        $to_mail = $user_profile->user_email;
        $message_subject = sprintf(__('%s Decision Time : SPLIT OR CONTINUE', 'victorious'), (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')));

        ob_start();
        require('admin/emailTemplates/survivor_reminder_decision.php');
        $message_body = str_replace('[site_logo]', (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')), $message_body);
        $message_body = str_replace('[username]', $user_profile->user_login, $message_body);
        $message_body = str_replace('[click_to_split]', home_url(), $message_body);
        $message_body = str_replace('[click_to_continue]', home_url(), $message_body);

        try
        {
            wp_mail($to_mail, $message_subject, $message_body, $headers);
            return true;
        }
        catch (Exception $ex)
        {
            return false;
        }
        return false;
    }

    public function checkGoliathDecision()
    {
        return $this->send("checkGoliathDecision");
    }

    public function goliathMakeDecision($type)
    {
        return $this->send("goliathMakeDecision", array(
                    'type' => $type
        ));
    }

    /////////////////////////////////////end goliath/////////////////////////////////////
    /////////////////////////////////////minigoliath/////////////////////////////////////
    public function validateEnterMiniGoliath($league_id, $entry_number)
    {
        return $this->send("validateEnterMiniGoliath", array(
                    "league_id" => $league_id,
                    "entry_number" => $entry_number
        ));
    }

    public function getMiniGoliathContest($league_id, $entry_number)
    {
        return $this->send("getMiniGoliathContest", array(
                    "league_id" => $league_id,
                    "entry_number" => $entry_number
        ));
    }

    public function validateSubmitMiniGoliath($league_id, $entry_number, $winners)
    {
        return $this->send("validateSubmitMiniGoliath", array(
            'league_id' => $league_id,
            'entry_number' => $entry_number,
            'winners' => $winners
        ));
    }

    public function submitMiniGoliath($league_id, $entry_number, $winners, $invitedby = '')
    {
        if (!empty($invitedby))
        {
            $invitedby = get_user_by("login", $invitedby);
            $invitedby = $invitedby != null ? $invitedby->data->ID : 0;
        }
        return $this->send("submitMiniGoliath", array(
            'league_id' => $league_id,
            'entry_number' => $entry_number,
            'winners' => $winners,
            'invitedby' => $invitedby
        ));
    }

    public function getMiniGoliathContestResult($league_id)
    {
        return $this->send("getMiniGoliathContestResult", array(
                    "league_id" => $league_id
        ));
    }

    public function minigoliathResult($league_id, $standing_type, $page = 1)
    {
        $data = $this->send("getMiniGoliathResult", array(
            "league_id" => $league_id,
            "standing_type" => $standing_type,
            "page" => $page
        ));
        if (!empty($data['scores']))
        {
            $data['scores'] = $this->parseUserData($data['scores']);
        }
        return $data;
    }

    public function minigoliathResultDetail($league_id, $user_id, $entry_number, $opponent_id, $opponent_entry_number)
    {
        return $this->send("getMiniGoliathResultDetail", array(
                    "league_id" => $league_id,
                    "user_id" => $user_id,
                    "entry_number" => $entry_number,
                    "opponent_id" => $opponent_id,
                    "opponent_entry_number" => $opponent_entry_number,
        ));
    }

    /////////////////////////////////////end minigoliath/////////////////////////////////////
    /////////////////////////////////////leaderboard/////////////////////////////////////
    public function getLivePoint($league_id, $page, $city, $sort)
    {
        $user_ids = $city != "" ? $this->getUserIdCountry($city) : "";
        $data = $this->send("getLivePoint", array(
            'league_id' => $league_id,
            'user_ids' => $user_ids,
            'page' => $page,
            'sort' => $sort
        ));
        if (!empty($data['scores']))
        {
            foreach ($data['scores'] as $k => $score)
            {
                $user = get_user_by('ID', $score['userID']);
                $data['scores'][$k]['full_name'] = $user->data->user_login;
            }
        }
        return $data;
    }

    public function getGameType()
    {
        return $this->send("getGameType");
    }

    public function getUserIdCountry($city)
    {
        global $wpdb;
        $table = $wpdb->prefix . "user_extended";
        $sql = "SELECT user_id,city FROM $table";
        $data = $wpdb->get_results($sql);
        $ids = array();
        foreach ($data as $v)
        {
            if ($v->city == $city)
            {
                $ids[] = $v->user_id;
            }
        }
        return $ids;
    }

    public function getLeagueByGameType($game_type)
    {
        return $this->send("getLeagueByGameType", array(
                    'game_type' => $game_type
        ));
    }

    /////////////////////////////////////end leaderboard/////////////////////////////////////

    public function validateCancelContest($league_id)
    {
        return $this->send('validateCancelContest', array(
                    'league_id' => $league_id
        ));
    }

    public function cancelContest($league_id)
    {
        return $this->send('cancelContest', array(
                    'league_id' => $league_id
        ));
    }

    public function validateLeaveContest($league_id, $entry_number)
    {
        return $this->send('validateLeaveContest', array(
                    'league_id' => $league_id,
                    'entry_number' => $entry_number
        ));
    }

    public function leaveContest($league_id, $entry_number)
    {
        return $this->send('leaveContest', array(
                    'league_id' => $league_id,
                    'entry_number' => $entry_number
        ));
    }

    public function getPickemResult($league_id, $page = 1)
    {
        $data = $this->send("getPickemResult", array(
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

    public function getPickemResultDetail($league_id, $week, $user_id, $entry_number, $opponent_id, $opponent_entry_number)
    {
        $data = $this->send("getPickemResultDetail", array(
                "league_id" => $league_id,
                "week" => $week,
                "user_id" => $user_id,
                "entry_number" => $entry_number,
                "opponent_id" => $opponent_id,
                "opponent_entry_number" => $opponent_entry_number,
        ));
        if ($data != null){
            $data['score'] = array_merge($data['score'], $this->parseUserData($data['score'], $data['score']['userID']));
            $data['my_score'] = array_merge($data['my_score'], $this->parseUserData($data['my_score'], $data['my_score']['userID']));
        }
        return $data;
    }
    
    /////////////////////////////////////bothteamstoscore/////////////////////////////////////
    public function getBothTeamsToScoreResult($league_id, $page = 1)
    {
        $data = $this->send("getBothTeamsToScoreResult", array(
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

    public function getBothTeamsToScoreResultDetail($league_id, $week, $user_id, $entry_number, $opponent_id, $opponent_entry_number)
    {
        return $this->send("getBothTeamsToScoreResultDetail", array(
                    "league_id" => $league_id,
                    "week" => $week,
                    "user_id" => $user_id,
                    "entry_number" => $entry_number,
                    "opponent_id" => $opponent_id,
                    "opponent_entry_number" => $opponent_entry_number,
        ));
    }

    /////////////////////////////////////survival/////////////////////////////////////
    public function getSurvivalContest($league_id, $entry_number)
    {
        $data = $this->send("getSurvivalContest", array(
            "league_id" => $league_id,
            "entry_number" => $entry_number
        ));
        if (!empty($data['fights']))
        {
            $data['fights'] = $this->parseFightData($data['fights']);
        }
        return $data;
    }

    public function getSurvivalCurrentWeek($league_id)
    {
        return $this->send("getSurvivalCurrentWeek", array(
                    "league_id" => $league_id,
        ));
    }

    public function getSurvivalResult($league_id, $week = 0, $page = 1)
    {
        $data = $this->send("getSurvivalResult", array(
            "league_id" => $league_id,
            "week" => $week,
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

    public function getSurvivalResultDetail($league_id, $week, $user_id, $entry_number, $opponent_id, $opponent_entry_number)
    {
        return $this->send("getSurvivalResultDetail", array(
                    "league_id" => $league_id,
                    "week" => $week,
                    "user_id" => $user_id,
                    "entry_number" => $entry_number,
                    "opponent_id" => $opponent_id,
                    "opponent_entry_number" => $opponent_entry_number,
        ));
    }

    public function survivalReminder()
    {
        return $this->send("survivalReminder");
    }

    public function sendSurvivalReminderEmail($user_id, $league, $week, $fight)
    {
        $admin_email = (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email'));
        $user_profile = $this->get_user_info($user_id);
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'To: ' . $user_profile->user_email . "\r\n";
        $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . $admin_email . ">\r\n";
        $to_mail = $user_profile->user_email;
        $message_subject = sprintf(__("%s Pick Deadline Reminder - %s", 'victorious'), str_replace('&#39;', "'", $league['name']), $week);

        ob_start();
        require('admin/emailTemplates/survival_reminder.php');
        $message_body = ob_get_clean();
        try
        {
            wp_mail($to_mail, $message_subject, $message_body, $headers);
            return true;
        }
        catch (Exception $ex)
        {
            return false;
        }
        return false;
    }

    public function sendSurvivalMidSeasonEmail($user_profile, $contest_name)
    {
        $admin_email = (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email'));
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'To: ' . $user_profile->user_email . "\r\n";
        $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . $admin_email . ">\r\n";
        $to_mail = $user_profile->user_email;
        $contest_name = str_replace('&#39;', "'", $contest_name);
        $message_subject = sprintf(__("New Pool Open - %s", 'victorious'), $contest_name);

        ob_start();
        require('admin/emailTemplates/survival_mid_season_pool.php');
        $message_body = ob_get_clean();
        try
        {
            wp_mail($to_mail, $message_subject, $message_body, $headers);
            return true;
        }
        catch (Exception $ex)
        {
            return false;
        }
        return false;
    }

    public function submitUserPickSurvival($league_id, $entry_number, $current_week, $winners)
    {
        return $this->send("submitUserPickSurvival", array(
            'league_id' => $league_id,
            'entry_number' => $entry_number,
            'current_week' => $current_week,
            'winners' => $winners
        ));
    }

    public function validateSubmitSurvival($league_id, $entry_number, $current_week, $winners)
    {
        return $this->send("validateSubmitSurvival", array(
            'league_id' => $league_id,
            'entry_number' => $entry_number,
            'current_week' => $current_week,
            'winners' => $winners
        ));
    }

    public function validateEnterSurvival($league_id, $entry_number)
    {
        return $this->send("validateEnterSurvival", array(
                    "league_id" => $league_id,
                    "entry_number" => $entry_number
        ));
    }

    public function survivalSendMidSeasonEmails($league_id)
    {
        $result = $this->send("survivalCheckMidSeason", array(
            "league_id" => $league_id
        ));
        if ($result['league'] != null)
        {
            $users = get_users();
            if ($users != null)
            {
                foreach ($users as $user)
                {
                    $this->sendSurvivalMidSeasonEmail($user->data, $result['league']['name']);
                }
            }
        }
    }

    /////////////////////////////////////end survival/////////////////////////////////////
    
    /////////////////////////////////////picksquares/////////////////////////////////////
    public function getPickSquaresResult($league_id, $page = 1)
    {
        $data = $this->send("getPickSquaresResult", array(
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

    public function getPickSquaresResultDetail($league_id, $week, $user_id, $entry_number, $opponent_id, $opponent_entry_number)
    {
        return $this->send("getPickSquaresResultDetail", array(
            "league_id" => $league_id,
            "week" => $week,
            "user_id" => $user_id,
            "entry_number" => $entry_number,
            "opponent_id" => $opponent_id,
            "opponent_entry_number" => $opponent_entry_number,
        ));
    }
    /////////////////////////////////////end picksquares/////////////////////////////////////
    
    /////////////////////////////////////teamdraft/////////////////////////////////////
    public function getTeamDraftContest($league_id, $entry_number)
    {
        $this->method = "GET";
        return $this->send("getTeamDraftContest", array(
            "league_id" => $league_id,
            "entry_number" => $entry_number
        ));
    }
    
    public function submitTeamDraft($league_id, $entry_number, $lineup_ids, $team_ids)
    {
        return $this->send("submitTeamDraft", array(
            'league_id' => $league_id,
            'entry_number' => $entry_number,
            'lineup_ids' => $lineup_ids,
            'team_ids' => $team_ids
        ));
    }

    public function validateSubmitTeamDraft($league_id, $entry_number, $lineup_ids, $team_ids)
    {
        return $this->send("validateSubmitTeamDraft", array(
            'league_id' => $league_id,
            'entry_number' => $entry_number,
            'lineup_ids' => $lineup_ids,
            'team_ids' => $team_ids
        ));
    }
    
    public function getTeamDraftContestResult($league_id)
    {
        $this->method = "GET";
        return $this->send("getTeamDraftContestResult", array("league_id" => $league_id));
    }
    
    public function getTeamDraftResult($league_id, $page = 1)
    {
        $this->method = "GET";
        $data = $this->send("getTeamDraftResult", array(
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

    public function getTeamDraftResultDetail($league_id, $user_id, $entry_number)
    {
        $this->method = "GET";
        $data =  $this->send("getTeamDraftResultDetail", array(
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
    /////////////////////////////////////end teamdraft/////////////////////////////////////
    
    /////////////////////////////////////playerdraft/////////////////////////////////////
    public function getPlayerDraftContestResult($league_id)
    {
        return $this->send("getPlayerDraftContestResult", array("league_id" => $league_id));
    }
    
    public function getPlayerDraftResult($league_id, $page = 1)
    {
        $data = $this->send("getPlayerDraftResult", array(
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

    public function getPlayerDraftResultDetail($league_id, $user_id, $entry_number)
    {
        $data =  $this->send("getPlayerDraftResultDetail", array(
            "league_id" => $league_id,
            "user_id" => $user_id,
            "entry_number" => $entry_number
        ));
        if (isset($data['new']))
        {
            $data['score'] = array_merge($data['score'], $this->parseUserData($data['score'], $data['score']['userID']));
        }
        return $data;
    }
    /////////////////////////////////////end playerdraft/////////////////////////////////////
    
    /////////////////////////////////////best5/////////////////////////////////////
    public function getBest5ContestResult($league_id)
    {
        $this->method = "GET";
        return $this->send("best5ContestResult", array("league_id" => $league_id));
    }
    
    public function getBest5Result($league_id, $page = 1)
    {
        $this->method = "GET";
        $data = $this->send("best5Result", array(
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

    public function getBest5ResultDetail($league_id, $user_id, $entry_number)
    {
        $this->method = "GET";
        $data =  $this->send("best5ResultDetail", array(
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
    /////////////////////////////////////end best5/////////////////////////////////////

    /////////////////////////////////////pickultimate/////////////////////////////////////
    public function getPickUltimateResult($league_id, $page = 1)
    {
        $data = $this->send("getPickUltimateResult", array(
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

    public function getPickUltimateResultDetail($league_id, $user_id, $entry_number, $opponent_id, $opponent_entry_number)
    {
        $data = $this->send("getPickUltimateResultDetail", array(
            "league_id" => $league_id,
            "user_id" => $user_id,
            "entry_number" => $entry_number,
            "opponent_id" => $opponent_id,
            "opponent_entry_number" => $opponent_entry_number,
        ));
        if (!empty($data['my_score'])){
            $data['my_score'] = array_merge($data['my_score'], $this->parseUserData($data['my_score'], $data['my_score']['userID']));
        }
        if (!empty($data['opponent_score'])){
            $data['opponent_score'] = array_merge($data['opponent_score'], $this->parseUserData($data['opponent_score'], $data['opponent_score']['userID']));
        }
        return $data;
    }
    /////////////////////////////////////end pickultimate/////////////////////////////////////
    
    public function isJoinedContest($league_id, $entry_number = 1)
    {
        return $this->send("isJoinedContest", array(
            "league_id" => $league_id,
            "entry_number" => $entry_number
        ));
    }
    
    public function loadPlayerInfo($player_id)
    {
        $this->method = "GET";
        return $this->send("playerInfo", array(
            "player_id" => $player_id,
        ));
    }

    public function sendPlayoffPlayerElimination($user_id, $contest_name, $players)
    {
        $admin_email = (get_option('victorious_email_from_email') != "" ? get_option('victorious_email_from_email') : get_option('admin_email'));
        $user_profile = $this->get_user_info($user_id);
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'To: ' . $user_profile->user_email . "\r\n";
        $headers .= "From: " . (get_option('victorious_email_from_name') != "" ? get_option('victorious_email_from_name') : get_option('blogname')) . " <" . $admin_email . ">\r\n";

        $to_mail = $user_profile->user_email;
        $user_name = $user_profile->user_login;
        require('admin/emailTemplates/playoff_player_elimination.php');

        try
        {
            wp_mail($to_mail, $message_subject, $message_body, $headers);
            return true;
        }
        catch (Exception $ex)
        {
            return false;
        }
    }
}

?>