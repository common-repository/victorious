<?php
class VIC_LobbyController
{
    private static $victorious;
    public static function show($game_type = null)
    {
        self::$victorious = new VIC_Victorious();
        $pool = new VIC_Pools();
        $pool->updateUserMoneyWon();
        self::content($game_type);
    }

    private static function content($game_type = null)
    {
        if(!empty($_GET['entry_close_contest']))
        {
            self::entry_close_contest();
            exit;
        }
        else if(!empty($_GET['send_injuiry_email']))
        {
            self::send_injuiry_email();
            exit;
        }
        else if(!empty($_GET['live_draft_start']))
        {
            self::live_draft_start();
            self::weekly_stats();
            exit;
        }
        else if(!empty($_GET['roundpickem_notification']))
        {
            self::roundpickem_notification();
            exit;
        }
        else if(!empty($_GET['push_notification_team_score']))
        {
            self::push_notification_team_score();
            exit;
        }
        else if(!empty($_GET['push_notification_player_red_card']))
        {
            self::push_notification_player_red_card();
            exit;
        }
        else if(!empty($_GET['push_notification_contest_start']))
        {
            self::push_notification_contest_start();
            exit;
        }
        else if(!empty($_GET['push_notification_contest_end']))
        {
            self::push_notification_contest_end();
            exit;
        }
        else if(!empty($_GET['survivor_reminder']))
        {
            self::survivor_reminder();
            exit;
        }
        else if(!empty($_GET['survivor_reminder_decision']))
        {
            self::survivor_reminder_decision();
            exit;
        }
        else if(!empty($_GET['survival_reminder']))
        {
            self::survival_reminder();
            exit;
        }
        else if(!empty($_GET['playoff_elimination']))
        {
            self::playoff_elimination();
            exit;
        }
        else
        {
            wp_enqueue_script('global.js', VICTORIOUS__PLUGIN_URL_JS.'global.js');
            wp_enqueue_script('playerdraft.js', VICTORIOUS__PLUGIN_URL_JS.'playerdraft.js');
            wp_enqueue_script('lobby_page.js', VICTORIOUS__PLUGIN_URL_JS.'lobby_page.js');
            wp_enqueue_script('countdown.min.js', VICTORIOUS__PLUGIN_URL_JS.'countdown.min.js');
            wp_enqueue_script('tablesorter.js', VICTORIOUS__PLUGIN_URL_JS.'tablesorter.js');
            wp_enqueue_script('accounting.js', VICTORIOUS__PLUGIN_URL_JS.'accounting.js');
            wp_enqueue_script('jquery.flexslider.js', VICTORIOUS__PLUGIN_URL_JS.'jquery.flexslider.js');
            wp_enqueue_script('playoff.js', VICTORIOUS__PLUGIN_URL_JS . 'playoff.js');
            wp_enqueue_style('style.css', VICTORIOUS__PLUGIN_URL_CSS.'style.css');
            wp_enqueue_style('flexslider.css', VICTORIOUS__PLUGIN_URL_CSS.'flexslider.css');
            wp_enqueue_style('bootstrap.css', VICTORIOUS__PLUGIN_URL_CSS.'bootstrap.css');
            wp_enqueue_style('Material', VICTORIOUS__PLUGIN_URL_CSS.'material_icons.css');

            $global_setting = self::$victorious->getGlobalSetting();
			$victorious_firebase_apikey = get_option('victorious_firebase_apikey');
			$victorious_firebase_senderid = get_option('victorious_firebase_senderid');
            if(!empty(VIC_GetUserId()) && 
               !empty($victorious_firebase_apikey) && 
               !empty($victorious_firebase_senderid) &&
               $global_setting['allow_push_notification'])
            {
                wp_enqueue_script('firebase.js', VICTORIOUS__PLUGIN_URL_JS.'firebase.js');
                wp_enqueue_script('firebase-messaging.js', VICTORIOUS__PLUGIN_URL_JS.'firebase_messaging.js');
                wp_enqueue_script('push_notification.js', VICTORIOUS__PLUGIN_URL_JS.'push_notification.js');
            }
            else if(!empty(VIC_GetUserId()))
            {
                self::$victorious->unSubscribePushNotification(VIC_GetUserId());
            }

            $aSports = self::$victorious->getListSports();
            $time_zone_abbr = 'CET';
            try{
                $timezone = get_option('victorious_timezone');
                $dateTime = new DateTime(); 
                $dateTime->setTimeZone(new DateTimeZone($timezone)); 
                $time_zone_abbr  = $dateTime->format('T');
            } catch (Exception $ex) {

            }
            
            $feature_leagues = self::$victorious->getFeatureLeagues();
            
            //goliath decision popup
            $goliathDecision = self::$victorious->checkGoliathDecision();
            
            include VICTORIOUS__PLUGIN_DIR_VIEW.'lobby.php';
        }
    }
    
    private static function live_draft_waiver_wire()
    {
        $date = date('Y-m-d');
        $result = self::$victorious->liveDraftChangePlayerWaiverWire($date);
        if(!empty($result['leagues']))
        {
            foreach($result['leagues'] as $league)
            {
                foreach($league['change_list'] as $user_id => $players)
                {
                    $change_list = array();
                    if($players != null)
                    {
                        foreach($players as $player)
                        {
                            $change_list[] = $player['from_name']." ".__('to', 'victorious')." ".$player['to_name'];
                        }
                    }
                    self::$victorious->sendLiveDraftChangePlayerWaiverWireEmail($user_id, $league['leagueID'], $change_list, $league);
                }
            }
        }
        return;
    }
    
    private static function live_draft_start()
    {
        $data = self::$victorious->liveDraftStart();
        if(!empty($data['data']))
        {
            foreach($data['data'] as $item)
            {
                if(!empty($item['user_ids']))
                {
                    $user_ids = explode(",", $item['user_ids']);
                    foreach($user_ids as $user_id)
                    {
                        if(isset($item['error']) && $item['error'] == 1) //send cancel contest
                        {
                            self::$victorious->sendLiveDraftCancelContestEmail($user_id, $item['league']);
                        }
                        else
                        {
                            self::$victorious->sendLiveDraftStartDraftEmail($user_id, $item['league']);
                        }
                    }
                }
            }
        }
        
        //change players
        self::live_draft_waiver_wire();
    }
    
    private static function weekly_stats()
    {
        $day = date('D');
        $victorious_last_report_date = get_option('victorious_last_report_date');
        if($day != 'Mon' || ($victorious_last_report_date != null && strtotime($victorious_last_report_date) <= strtotime(date('Y-m-d'))))
        {
            return;
        }
        $playerstats = self::$victorious->getWeeklyPlayerStats();
        if($playerstats == null)
        {
            return;
        }
        $excel_column = array(
            __('Player Name', 'victorious'),
            __('Player Position', 'victorious'),  
            __('Player Team', 'victorious'),  
            __('Date of Game', 'victorious'),  
            __('Opposition Team', 'victorious'),  
            __('Total Fantasy Points', 'victorious'), 
            __('Scrum Won', 'victorious'), 
            __('Penalties', 'victorious'), 
            __('Runs', 'victorious'), 
            __('Passes', 'victorious'), 
            __('Missed Goal', 'victorious'), 
            __('Lineout Won', 'victorious'), 
            __('Kicks Caught', 'victorious'), 
            __('Kicks a Goal', 'victorious'), 
            __('Tries', 'victorious'), 
            __('Offload', 'victorious'), 
            __('Metres', 'victorious'), 
            __('Try assist', 'victorious'), 
            __('Defenders Beaten', 'victorious'), 
            __('Clean Breaks', 'victorious'), 
            __('Red Cards', 'victorious'), 
            __('Yellow Cards', 'victorious'), 
            __('Tackles', 'victorious'), 
            __('Missed Tackles', 'victorious'), 
            __('Turnovers', 'victorious'), 
            __('Kicks', 'victorious') 
        );
        
        //export to excel
        ob_clean();
        $file = fopen(VICTORIOUS__PLUGIN_DIR.'assets/weekly_stats.csv', 'w');
        fputcsv($file, $excel_column);
        if($playerstats != null)
        {
            foreach($playerstats as $playerstat)
            {
                fputcsv($file, $playerstat);
            }
        }
        else
        {
            fputcsv($file, array("No stats"));
        }
        fclose($file);

        $is_sent = self::$victorious->sendWeeklyPlayerStats();
        if($is_sent)
        {
            update_option('victorious_last_report_date', date('Y-m-d'));
        }
        return;
    }
    
    private static function entry_close_contest()
    {
        $result = self::$victorious->checkEntryCloseContest();
    }
    
    private static function send_injuiry_email()
    {
        $result = self::$victorious->sendInjuiryEmail();
    }
    
    private static function roundpickem_notification()
    {
        if(!empty($_GET['weekly_winner']))
        {
            $data = self::$victorious->roundPickemWeeklyWinner();
            if(!empty($data['notifications']))
            {
                $notifications = $data['notifications'];
                foreach($notifications as $notification)
                {
                    if(empty($notification['winner_id']))
                    {
                        continue;
                    }
                    $user_ids = explode(',', $notification['user_ids']);
                    foreach($user_ids as $user_id)
                    {
                        self::$victorious->sendRoundPickemWeeklyWinner($notification['league'], $notification['week'], $user_id, $notification['winner_id']);
                    }
                }
            }
        }
        else if(!empty($_GET['reminder']))
        {
            $data = self::$victorious->roundPickemReminder();
            if(!empty($data['notifications']))
            {
                $notifications = $data['notifications'];
                foreach($notifications as $notification)
                {
                    if(empty($notification['user_ids']))
                    {
                        continue;
                    }
                    $user_ids = explode(',', $notification['user_ids']);
                    foreach($user_ids as $user_id)
                    {
                        self::$victorious->sendRoundPickemWeeklyNotification($notification['league'], $notification['week'], $user_id);
                    }
                }
            }
        }
    }
    
    /////////////////////////////////////firebase push notification/////////////////////////////////////
    private static function push_notification_team_score()
    {
		$victorious_firebase_apikey = get_option('victorious_firebase_apikey');
		$victorious_firebase_senderid = get_option('victorious_firebase_senderid');
        if(empty($victorious_firebase_apikey) || empty($victorious_firebase_senderid))
        {
            return;
        }
        $league_id = !empty($_GET['league_id']) ? sanitize_text_field($_GET['league_id']) : "";
        $team_name1 = !empty($_GET['team_name1']) ? sanitize_text_field($_GET['team_name1']) : "";
        $team_name2 = !empty($_GET['team_name2']) ? sanitize_text_field($_GET['team_name2']) : "";
        $team_score1 = !empty($_GET['team_score1']) ? sanitize_text_field($_GET['team_score1']) : 0;
        $team_score2 = !empty($_GET['team_score2']) ? sanitize_text_field($_GET['team_score2']) : 0;
        $player_score = !empty($_GET['player_score']) ? sanitize_text_field($_GET['player_score']) : 0;
        $minute = !empty($_GET['minute_score']) ? sanitize_text_field($_GET['minute_score']) : 0;
        if($league_id == "" || $team_name1 == "")
        {
            return;
        }
        
        $user_ids = self::$victorious->getUserIdsJoinContest($league_id);

        if($user_ids == null)
        {
            return;
        }
        $link = VICTORIOUS_URL_CONTEST.$league_id."?num=1";
        $data = self::$victorious->sendNotificationTeamScore($user_ids['user_ids'], $team_name1, $team_name2, $team_score1, $team_score2, $player_score, $minute, $link);
    }
    
    private static function push_notification_player_red_card()
    {
		$victorious_firebase_apikey = get_option('victorious_firebase_apikey');
		$victorious_firebase_senderid = get_option('victorious_firebase_senderid');
        if(empty($victorious_firebase_apikey) || empty($victorious_firebase_senderid))
        {
            return;
        }
        $league_id = !empty($_GET['league_id']) ? sanitize_text_field($_GET['league_id']) : "";
        $player_name = !empty($_GET['player_name']) ? sanitize_text_field($_GET['player_name']) : "";
        $minute = !empty($_GET['player_minute']) ? sanitize_text_field($_GET['player_minute']) : "";
        if($league_id == "" || $player_name == "")
        {
            return;
        }
        
        $user_ids = self::$victorious->getUserIdsJoinContest($league_id);
        
        if($user_ids == null)
        {
            return;
        }
        $link = VICTORIOUS_URL_CONTEST.$league_id."?num=1";
        $data = self::$victorious->sendNotificationRedCard($user_ids['user_ids'], $player_name, $minute, $link);
    }
    
    private static function push_notification_contest_start()
    {
		$victorious_firebase_apikey = get_option('victorious_firebase_apikey');
		$victorious_firebase_senderid = get_option('victorious_firebase_senderid');
        if(empty($victorious_firebase_apikey) || empty($victorious_firebase_senderid))
        {
            return;
        }
        $league_id = !empty($_GET['league_id']) ? sanitize_text_field($_GET['league_id']) : "";
        $time = !empty($_GET['time']) ? sanitize_text_field($_GET['time']) : "";
        $team_name1 = !empty($_GET['team_name1']) ? sanitize_text_field($_GET['team_name1']) : "";
        $team_name2 = !empty($_GET['team_name2']) ? sanitize_text_field($_GET['team_name2']) : "";
        if($league_id == "" || $time == "")
        {
            return;
        }
        
        $user_ids = self::$victorious->getUserIdsJoinContest($league_id);
        
        if($user_ids == null)
        {
            return;
        }
        $link = VICTORIOUS_URL_CONTEST.$league_id."?num=1";
        $data = self::$victorious->sendNotificationContestStart($user_ids['user_ids'], $time, $team_name1, $team_name2, $link);
    }
    
    private static function push_notification_contest_end()
    {
		$victorious_firebase_apikey = get_option('victorious_firebase_apikey');
		$victorious_firebase_senderid = get_option('victorious_firebase_senderid');
        if(empty($victorious_firebase_apikey) || empty($victorious_firebase_senderid))
        {
            return;
        }
        $league_id = !empty($_GET['league_id']) ? sanitize_text_field($_GET['league_id']) : "";
        $team_name1 = !empty($_GET['team_name1']) ? sanitize_text_field($_GET['team_name1']) : "";
        $team_name2 = !empty($_GET['team_name2']) ? sanitize_text_field($_GET['team_name2']) : "";
        $team_score1 = !empty($_GET['team_score1']) ? sanitize_text_field($_GET['team_score1']) : 0;
        $team_score2 = !empty($_GET['team_score2']) ? sanitize_text_field($_GET['team_score2']) : 0;
        if($league_id == "" || $team_name1 == "")
        {
            return;
        }
        
        $user_ids = self::$victorious->getUserIdsJoinContest($league_id);
        
        if($user_ids == null)
        {
            return;
        }
        $link = VICTORIOUS_URL_CONTEST.$league_id."?num=1";
        $data = self::$victorious->sendNotificationContestEnd($user_ids['user_ids'], $team_name1, $team_name2, $team_score1, $team_score2, $link);
    }
    /////////////////////////////////////end firebase push notification/////////////////////////////////////
    
    /////////////////////////////////////survivor/////////////////////////////////////
    private static function survivor_reminder()
    {
        if(empty($_POST))
        {
            return;
        }
        foreach($_POST as $item)
        {
            $user_ids = sanitize_text_field($item['user_ids']);
            if($user_ids == null)
            {
                continue;
            }
            $league = sanitize_text_field($item['league']);
            $fight = sanitize_text_field($item['fight']);
            foreach($user_ids as $user_id)
            {
                self::$victorious->sendSurvivorReminderEmail($user_id, $league, $fight);
            }
        }
    }
    
    private static function survivor_reminder_decision()
    {
        if(empty($_POST))
        {
            return;
        }
        foreach($_POST as $item)
        {
            $user_ids = sanitize_text_field($item['user_ids']);
            if($user_ids == null)
            {
                continue;
            }
            $league = sanitize_text_field($item['league']);
            foreach($user_ids as $user_id)
            {
                self::$victorious->sendSurvivorReminderDecisionEmail($user_id, $league);
            }
        }
    }
    /////////////////////////////////////survivor/////////////////////////////////////
    
    /////////////////////////////////////survival game type/////////////////////////////////////
    private static function survival_reminder()
    {
        if(empty($_POST))
        {
            return;
        }
        foreach($_POST as $item)
        {
            $user_ids = sanitize_text_field($item['user_ids']);
            if($user_ids == null)
            {
                continue;
            }
            $fight = sanitize_text_field($item['fight']);
            $league = sanitize_text_field($item['league']);
            $week = sanitize_text_field($item['week']);
            foreach($user_ids as $user_id)
            {
                self::$victorious->sendSurvivalReminderEmail($user_id, $league, $week, $fight);
            }
        }
    }
    /////////////////////////////////////end survival game type/////////////////////////////////////

    /////////////////////////////////////playoff game type/////////////////////////////////////
    private static function playoff_elimination()
    {
        if(empty($_GET['user_id']))
        {
            return;
        }

        $contest_name = sanitize_text_field($_GET['contest_name']);
        $user_id = sanitize_text_field($_GET['user_id']);
        $players = sanitize_text_field($_GET['players']);

        self::$victorious->sendPlayoffPlayerElimination($user_id, $contest_name, $players);
    }
    /////////////////////////////////////end playoff game type/////////////////////////////////////
}
?>