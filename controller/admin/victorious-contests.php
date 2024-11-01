<?php
$Victorious_Contests = new Victorious_Contests();
class Victorious_Contests
{
    private static $orgs;
    private static $victorious;
    private static $leagues;
    private static $url;
    private static $urladdnew;
    private static $urladd;
    private static $playerposition;
    private static $pools;
    private static $payment;
    private static $balanceType;
    public function __construct() 
    {
        self::$orgs = new VIC_Organizations();
        self::$victorious = new VIC_Victorious();
        self::$leagues = new VIC_Leagues();
        self::$playerposition = new VIC_PlayerPosition();
        self::$pools = new VIC_Pools();
        self::$payment = new VIC_Payment();
        self::$balanceType = new VIC_BalanceType();
        self::$url = admin_url().'admin.php?page=manage-contests';
        self::$urladdnew = admin_url().'admin.php?page=add-contests';
        self::$urladd = wp_get_referer();
    }
    
    public static function manageContests()
    {
        if(!empty($_GET['leagueID']))
        {
            self::exportUserPicks(sanitize_text_field($_GET['leagueID']));
        }
        
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') , 'victorious');
        }

        //load css js
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS.'admin/admin.js');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script('fine-uploader.js', VICTORIOUS__PLUGIN_URL_JS.'fine-uploader.js');
        wp_enqueue_script('global.js', VICTORIOUS__PLUGIN_URL_JS.'global.js');
        wp_enqueue_script('uploadphoto.js', VICTORIOUS__PLUGIN_URL_JS.'uploadphoto.js');
        wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');
        wp_enqueue_style('fine-uploader-new.css', VICTORIOUS__PLUGIN_URL_CSS.'fine-uploader-new.css');

        //task action delete
        if(isset($_POST["task"]) && $task = sanitize_text_field($_POST["task"]))
        {
            switch($task)
            {
                case "delete":
                    self::delete();
                    break;
            }
        }
        
        //list friend
        $aFriends = self::$victorious->getAllPlayerInfo();
        $iTotalFriends = count($aFriends);
        sort($aFriends, SORT_ASC);
        usort($aFriends, function($a, $b){
            $a = strtolower($a['full_name'] ? $a['full_name'] : $a['user_name']);
            $b = strtolower($b['full_name'] ? $b['full_name'] : $b['user_name']);
            return strcmp($a, $b);
        });
        include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";
        include VICTORIOUS__PLUGIN_DIR_VIEW.'contests/class.table-contests.php';
        $myListTable = new VIC_TableContests();
        $myListTable->prepare_items(isset($_GET['s']) ? sanitize_text_field($_GET['s']) : null);
        include VICTORIOUS__PLUGIN_DIR_VIEW.'contests/index.php';
    }
    
    private static function exportUserPicks($leagueID)
    {
        if($leagueID > 0)
        {
            $data = self::$victorious->showUserPicks($leagueID);
            $users = $data['picks'];
            $league = $data['league'];
            $scores = $data['scores'];
            if($data['result'] == 0)
            {
                VIC_Redirect(self::$url, __('This league does not exist.', 'victorious'));
            }
            else 
            {
                ob_clean();
                header('Pragma: public');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Cache-Control: private', false);
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename=data.csv');
                $file = fopen('php://output', 'w');
                
                fputcsv($file, array('League Name', $league['name']));
                fputcsv($file, array('Game Type', $league['gameType']));

                if($league['gameType'] == VICTORIOUS_GAME_TYPE_UPLOADPHOTO){
                    fputcsv($file, array('User list'));
                    fputcsv($file, array('#', 'User', 'Email'));
                    if($scores != null){
                        foreach($scores as $k => $score){
                            $user = self::$victorious->get_user_by("id", $score['userID']);
                            fputcsv($file, array($k + 1, $user->user_login, $user->user_email));
                        }
                    }
                }
                else if($users != null)
                {
                    if(($league['gameType'] == 'PLAYERDRAFT' || $league['gameType'] == 'BEST5'))
                    {
                        fputcsv($file, array('Num', 'User', 'Entry Number', 'ID', 'Team Name', 'Pick Name'));
                    }
                    else if($league['gameType'] != 'PLAYERDRAFT') 
                    {
                        fputcsv($file, array('Num', 'User', 'Entry Number', 'ID', 'Fight Name', 'Pick Name'));
                    }
                    else 
                    {
                        fputcsv($file, array('Num', 'User', 'Entry Number', 'ID', 'Pick Name'));
                    }
                    foreach ($users as $ku => $user)
                    {
                        if($user['entries'] != null)
                        {
                            foreach ($user['entries'] as $entry)
                            {
                                if($entry['pick_items'] != null)
                                {
                                    foreach ($entry['pick_items'] as $k => $pick)
                                    {
                                        $num = $login_name = $entry_number = '';
                                        if($k == 0)
                                        {
                                            $num = $ku + 1;
                                            $login_name = $user['user_login'];
                                            $entry_number = $entry['entry_number'];
                                        }
                                        if(($league['gameType'] == 'PLAYERDRAFT' || $league['gameType'] == 'BEST5'))
                                        {
                                            fputcsv($file, array($num, $login_name, $entry_number, $pick['id'], $pick['team_name'], $pick['name']));
                                        }
                                        else if($league['gameType'] != 'PLAYERDRAFT') 
                                        {
                                            fputcsv($file, array($num, $login_name, $entry_number, $pick['id'], $pick['fight_name'], $pick['name']));
                                        }
                                        else 
                                        {
                                            fputcsv($file, array($num, $login_name, $entry_number, $pick['id'], $pick['name']));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                else 
                {
                    fputcsv($file, array("No picks"));
                }

                fclose($file);
                exit;
            }
        }
    }
    
    public static function addContests()
    {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') , 'victorious');
        }
        
        //load css js
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS.'admin/admin.js');
        wp_enqueue_script('fight.js', VICTORIOUS__PLUGIN_URL_JS.'admin/fight.js');
        wp_enqueue_script('createcontest.js', VICTORIOUS__PLUGIN_URL_JS.'createcontest.js', 5);
        wp_enqueue_script('accounting.js', VICTORIOUS__PLUGIN_URL_JS.'accounting.js');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');
        wp_enqueue_style('admin.css', VICTORIOUS__PLUGIN_URL_CSS.'admin.css');
        
        //edit data
        $iEditId = isset($_GET['id']) ? sanitize_text_field($_GET['id']) : null;
        $bIsEdit = $iEditId > 0 ? true : false;

        //add or update
		self::modify($bIsEdit);

        //pools and fights
        $global_setting = self::$victorious->getGlobalSetting();
        $aDatas = self::$victorious->loadCreateLeagueForm($iEditId);

        $aPools = $aDatas['pools'];
        $aFights = $aDatas['fights'];
        $aRounds = $aDatas['rounds'];
        $aSports = $aDatas['sports'];
        $aPositions = $aDatas['player_positions'];
        $aForms = $aDatas['league'];
        $allowCustomSpread = $aDatas['allow_custom_spread'];
        $allowEditStartedContests = $aDatas['allow_edit_started_contests'];
        
        $aLeagueSizes = get_option('victorious_league_size');
        $aEntryFees = get_option('victorious_entry_fee');
        $is_allow_tie = $aDatas['league']['allow_tie'];
        $is_allow_new_tie_breaker = $aDatas['league']['allow_new_tie_breaker'];
        $game_type = $aDatas['league']['gameType'];
        //mixing sport
        $allow_mixing_sport = $aDatas['allow_mixing_sport'];
        $aDates = $aDatas['mixing_pools'];
        $aMixingPools = $aDatas['mixing_pools'];
        $is_mixing = $aDatas['league']['is_mixing'];
		$trade_player = $aDatas['trade_player'];		
        $list_motocross_sports = $aDatas['list_motocross_org'];
        $motocross_id = $aDatas['motocross_id'];
        $is_league_motocross = false;
        $allow_motocross = $aDatas['allow_motocross'];
        $is_single_game = $aDatas['is_single_game'];
        $is_mixing_game = $aDatas['is_mixing_game'];
        $is_motocross_game = $aDatas['is_motocross_game'];
        $allow_guaranteed_prize = $aDatas['allow_guaranteed_prize'];
        $is_show_weekly_pick = $aDatas['is_show_weekly_pick'];
        $contest_only_rookies = $aDatas['contest_only_rookies'];
        $team_lineups = $aDatas['team_lineups'];
        $balance_types = self::$balanceType->getBalanceTypeList();
        $hours = self::$pools->getPoolHours();
        $minutes = self::$pools->getPoolMinutes();

        if($aDatas['league']){
            $is_league_motocross = $aDatas['league']['is_motocross'];
        }
        if(!empty($aForms['playoff_wildcard_start'])){
            $tmp = explode(' ', $aForms['playoff_wildcard_start']);
            $date = $tmp[0];
            $time = explode(':', $tmp[1]);
            $aForms['playoff_wildcard_start_date'] = $date;
            $aForms['playoff_wildcard_start_hour'] = $time[0];
            $aForms['playoff_wildcard_start_minute'] = $time[1];
        }
        if(!empty($aForms['playoff_divisional_start'])){
            $tmp = explode(' ', $aForms['playoff_divisional_start']);
            $date = $tmp[0];
            $time = explode(':', $tmp[1]);
            $aForms['playoff_divisional_start_date'] = $date;
            $aForms['playoff_divisional_start_hour'] = $time[0];
            $aForms['playoff_divisional_start_minute'] = $time[1];
        }
        if(!empty($aForms['playoff_conference_start'])){
            $tmp = explode(' ', $aForms['playoff_conference_start']);
            $date = $tmp[0];
            $time = explode(':', $tmp[1]);
            $aForms['playoff_conference_start_date'] = $date;
            $aForms['playoff_conference_start_hour'] = $time[0];
            $aForms['playoff_conference_start_minute'] = $time[1];
        }
        if(!empty($aForms['playoff_super_bowl_start'])){
            $tmp = explode(' ', $aForms['playoff_super_bowl_start']);
            $date = $tmp[0];
            $time = explode(':', $tmp[1]);
            $aForms['playoff_super_bowl_start_date'] = $date;
            $aForms['playoff_super_bowl_start_hour'] = $time[0];
            $aForms['playoff_super_bowl_start_minute'] = $time[1];
        }
        
        include VICTORIOUS__PLUGIN_DIR_VIEW.'contests/add.php';
    }

    private static function validData($aVals)
    {
        $valid = self::$victorious->validCreateLeague($_POST);
        
        switch($valid)
        {
            case 2;
                VIC_Redirect(self::$urladd, __('Sport does not exist. Please try again.', 'victorious'));
                break;
            case 3;
                VIC_Redirect(self::$urladd, __('Date does not exist. Please try again.', 'victorious'));
                break;
            case 4;
                VIC_Redirect(self::$urladd, __('Fixture does not exist. Please try again.', 'victorious'));
                break;
            case 5;
                VIC_Redirect(self::$urladd, __('Please select at least a fixture.', 'victorious'));
                break;
            case 6;
                VIC_Redirect(self::$urladd, __('This game type does not exist.', 'victorious'));
                break;
            case 7;
                VIC_Redirect(self::$urladd, __('This sport does not support playerdraft type.', 'victorious'));
                break;
            case 8;
                VIC_Redirect(self::$urladd, __('Please enter league name', 'victorious'));
                break;
            case 9;
                VIC_Redirect(self::$urladd, __('Round does not exist. Please try again', 'victorious'));
                break;
            case 10;
                VIC_Redirect(self::$urladd, __('Please select at least two rounds', 'victorious'));
                break;
            case 11;
                VIC_Redirect(self::$urladd, __('Invalid payouts', 'victorious'));
                break;
            case 12;
                VIC_Redirect(self::$urladd, __('Max player restriction is 4', 'victorious'));
                break;
            case 13;
                VIC_Redirect(self::$urladd, __('Invalid player change quantity', 'victorious'));
                break;
            case 14;
                VIC_Redirect(self::$urladd, __('Please enter number of minutes a contestant has to draft', 'victorious'));
                break;
            case 15;
                VIC_Redirect(self::$urladd, __('Number of players that be changed via Waiver Wire must be lower than player quantity of lineup', 'victorious'));
                break;
            case 16;
                VIC_Redirect(self::$urladd, __('Invalid league size', 'victorious'));
                break;
            case 17;
                VIC_Redirect(self::$urladd, __('Please enter start draft date time', 'victorious'));
                break;
            case 18;
                VIC_Redirect(self::$urladd, __('Please input guaranteed prize', 'victorious'));
                break;
            case 19;
            case 20;
                VIC_Redirect(self::$urladd, __('Please input all guaranteed prize structure fields and total percent must be 100%', 'victorious'));
                break;
            case 20;
                VIC_Redirect(self::$urladd, __('Waiver wire start day can not be behind waiver wire end day', 'victorious'));
                break;
            case 22;
                VIC_Redirect(self::$urladd, __('Number of entries to close a contest must be lower than league size', 'victorious'));
                break;
            case 23;
                VIC_Redirect(self::$urladd, __('No games for select date range', 'victorious'));
                break;
            case 24;
                VIC_Redirect(self::$urladd, __('Allow pick from or Allow pick to is empty', 'victorious'));
                break;
            case 25;
                VIC_Redirect(self::$urladd, __('Draft game type requires at least two fixtures', 'victorious'));
                break;
            case 26;
                VIC_Redirect(self::$urladd, __('Invalid cut date', 'victorious'));
                break;
            case 27;
                VIC_Redirect(self::$urladd, __('Invalid end date', 'victorious'));
                break;
            case 28;
                VIC_Redirect(self::$urladd, __('Invalid salary cap', 'victorious'));
                break;
        }
        
        if(!in_array($_POST['leagueSize'], get_option('victorious_league_size')))
        {
            VIC_Redirect(self::$urladd, __('League size does not exist', 'victorious'));
        }
        else if($_POST['entry_fee'] > 0 && !in_array($_POST['entry_fee'], get_option('victorious_entry_fee')))
        {
            VIC_Redirect(self::$urladd, __('Entry fee does not exist', 'victorious'));
        }
        return true;
    }
    
    private static function modify()
    {
        if(!empty($_POST['live_draft_start_date']))
        {
            $_POST['live_draft_start'] = date("Y-m-d H:i:s", strtotime(sanitize_text_field($_POST['live_draft_start_date'])." ".sanitize_text_field($_POST['live_draft_start_hour']).":".sanitize_text_field($_POST['live_draft_start_minute']).":00"));
        }
        if(!empty($_POST['yearly_contest_start']))
        {
            $_POST['yearly_contest_start'] = date('Y-m-d', strtotime(sanitize_text_field($_POST['yearly_contest_start'])))." 00:00:00";
        }
        if(!empty($_POST['yearly_contest_end']))
        {
            $_POST['yearly_contest_end'] = date('Y-m-d', strtotime(sanitize_text_field($_POST['yearly_contest_end'])))." 23:59:00";
        }
        if(!empty($_POST['playoff_wildcard_start_date']))
        {
            $_POST['playoff_wildcard_start'] = date("Y-m-d H:i:s", strtotime(sanitize_text_field($_POST['playoff_wildcard_start_date'])." ".sanitize_text_field($_POST['playoff_wildcard_start_hour']).":".sanitize_text_field($_POST['playoff_wildcard_start_minute']).":00"));
        }
        if(!empty($_POST['playoff_divisional_start_date']))
        {
            $_POST['playoff_divisional_start'] = date("Y-m-d H:i:s", strtotime(sanitize_text_field($_POST['playoff_divisional_start_date'])." ".sanitize_text_field($_POST['playoff_divisional_start_hour']).":".sanitize_text_field($_POST['playoff_divisional_start_minute']).":00"));
        }
        if(!empty($_POST['playoff_conference_start_date']))
        {
            $_POST['playoff_conference_start'] = date("Y-m-d H:i:s", strtotime(sanitize_text_field($_POST['playoff_conference_start_date'])." ".sanitize_text_field($_POST['playoff_conference_start_hour']).":".sanitize_text_field($_POST['playoff_conference_start_minute']).":00"));
        }
        if(!empty($_POST['playoff_super_bowl_start_date']))
        {
            $_POST['playoff_super_bowl_start'] = date("Y-m-d H:i:s", strtotime(sanitize_text_field($_POST['playoff_super_bowl_start_date'])." ".sanitize_text_field($_POST['playoff_super_bowl_start_hour']).":".sanitize_text_field($_POST['playoff_super_bowl_start_minute']).":00"));
        }
        if (isset($_POST) && $aVals = $_POST)
		{
			if (self::validData($aVals))
			{
                if(self::$victorious->isLeagueExist($aVals['leagueID'])) //update
                {
                    if (self::$victorious->createLeague($_POST))
                    {
                        VIC_Redirect(self::$urladd, __('Succesfully updated', 'victorious'));
                    }
                }
                else //add
                {   
                    $leagueID = self::$victorious->createLeague($_POST);
                    if((int)$leagueID > 0)
                    {
                        //buddy press integration
                        $league = self::$victorious->getLeagueDetail($leagueID);
                        if($league != null)
                        {
                            $league = $league[0];
                            self::$victorious->addCreateContestActivity($league, VIC_GetUserId());
                        }
                        
                        //check send mid season email for survival game type
                        if(strtoupper(sanitize_text_field($_POST['game_type'])) == VICTORIOUS_GAME_TYPE_SURVIVAL)
                        {
                            self::$victorious->survivalSendMidSeasonEmails($leagueID);
                        }
        
                        VIC_Redirect(self::$url, __('Succesfully added', 'victorious'));
                    }
                }
                VIC_Redirect(self::$urladd, __('Something went wrong! Please try again.', 'victorious'));
			}
		}
    }
    
    private static function delete()
	{
        if (!empty($_POST['id']))
		{
            $aIds = array_map('sanitize_text_field', $_POST['id']);
			$iDeleted = 0;
			foreach ($aIds as $iId)
			{
				if (self::$leagues->delete($iId))
				{
                    self::$victorious->deleteContestActivities($iId);
					$iDeleted++;
				}
			}
			
			if ($iDeleted > 0)
			{
                VIC_Redirect(self::$url, 'Succesfully deleted');
			}
		}
        VIC_Redirect(self::$url);
	}
}
?>