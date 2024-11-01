<?php
$ajax = new VIC_Ajax();
$ajax::init();

class VIC_Ajax
{
    private static $victorious;
    private static $pools;
    private static $fighters;
    private static $orgs;
    private static $teams;
    private static $payment;
    private static $user;
    private static $statistic;
    private static $scoringcategory;
    private static $playerposition;
    private static $players;
    private static $coupon;
    private static $sports;
    private static $leagues;
    private static $autocontest;
    private static $sportbook;
    private static $uploadphoto;
    private static $playerdraft;
    private static $portfolio;
    private static $olddraft;
    private static $playoff;

    public static function init()
    {
        self::$victorious = new VIC_Victorious();
        self::$pools = new VIC_Pools();
        self::$fighters = new VIC_Fighters();
        self::$orgs = new VIC_Organizations();
        self::$teams = new VIC_Teams();
        self::$payment = new VIC_Payment();
        self::$user = new VIC_User();
        self::$statistic = new VIC_Statistic();
        self::$scoringcategory = new VIC_ScoringCategory();
        self::$playerposition = new VIC_PlayerPosition();
        self::$players = new VIC_Players();
        self::$coupon = new VIC_CouponModel();
        self::$sports = new VIC_Sports();
        self::$leagues = new VIC_Leagues();
        self::$autocontest = new VIC_AutoContest();
        self::$sportbook = new VIC_Sportbook();
        self::$uploadphoto = new VIC_UploadPhoto();
        self::$playerdraft = new VIC_Playerdraft();
        self::$portfolio = new VIC_Portfolio();
        self::$olddraft = new VIC_OldDraft();
        self::$playoff = new VIC_Playoff();

        $action = !empty($_POST['action']) ? sanitize_text_field($_POST['action']) : (!empty($_GET['action']) ? sanitize_text_field($_GET['action']) : "");
        if(isset($_REQUEST['qqfilename']))
        {
            $action = 'qqUploadFile';
        }
        
        //load translate
        load_plugin_textdomain( 'victorious', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
        
        if(!empty($action) && method_exists('VIC_Ajax', $action))
        {
            if($action == "createContest" && self::$victorious->checkBuddyPressInstalled())
            {
                add_action("wp_ajax_$action", array('VIC_Ajax', $action));
            }
            else
            {
                self::$action();
            }
        }
    }
    
    public static function updateNewContests()
    {
        //leagues
        $aLeagues = self::$victorious->getLeagueLobby();
        $aLeagues = self::$victorious->parseLeagueData($aLeagues);
        exit(json_encode($aLeagues));
    }

    public static function LeagueResults()
    {
        $iLeagueId = sanitize_text_field($_POST['leagueId']);
        $isLive = isset($_POST['isLive']) ? sanitize_text_field($_POST['isLive']) : '';
        $sData = self::$victorious->leagueResults($iLeagueId, $isLive);
        exit(json_encode($sData));
    }
    
    public static function getNormalGameResult()
    {
        $page = isset($_POST['page']) ? sanitize_text_field($_POST['page']) : 1;
        $sort_by = isset($_POST['sort_by']) ? sanitize_text_field($_POST['sort_by']) : '';
        $sort_value = isset($_POST['sort_value']) ? sanitize_text_field($_POST['sort_value']) : '';
        $entry_number = sanitize_text_field($_POST['entry_number']);
        
        $result = self::$victorious->getNormalGameResult(
            sanitize_text_field($_POST['leagueID']),
            $entry_number, 
            $page,
            $sort_by,
            $sort_value,
            sanitize_text_field($_POST['date_type']),
            sanitize_text_field($_POST['date_type_number']));
        
        $league = $result['league'];
        $pool = $result['pool'];
        $num_page = $result['num_page'];
        $current_page = $result['current_page'];
        $users = $result['users'];
        $fights = $result['fights'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/pick_result.php");
        exit;
    }
    
    public static function loadLiveEntries()
    {
        $leagues = self::$victorious->getLiveEntries();
        $leagues = self::$victorious->parseLeagueData($leagues);
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/live_entries.php");
        exit;
    }
    
    public static function userpicks()
    {
        $iLeagueId = sanitize_text_field($_POST['leagueId']);
        $sData = self::$victorious->getUserPicks($iLeagueId);
        exit($sData);
    }
    
    public static function sendInviteFriend()
    {
        $friend_ids = !empty($_POST['val']['friend_ids']) ? sanitize_text_field($_POST['val']['friend_ids']) : '';
        $emails = !empty($_POST['val']['emails']) ? sanitize_text_field($_POST['val']['emails']) : array();
        $importleagueID = !empty($_POST['val']['importleagueID']) ? sanitize_text_field($_POST['val']['importleagueID']) : '';
        $link_url_contest = !empty($_POST['val']['link_url_contest']) ? sanitize_text_field($_POST['val']['link_url_contest']) : '';
        $message_boxinvite = !empty($_POST['val']['message_boxinvite']) ? sanitize_text_field($_POST['val']['message_boxinvite']) : '';

        if(!array_filter($emails) && $friend_ids == null)
        {
            exit(json_encode(array('notice' => __('You have not selected any friends to invite', 'victorious'))));
        }
        else if($importleagueID == "" || $importleagueID == "0" )
        {
            exit(json_encode(array('notice' => __('Sorry the system detected a spam attempt please contact support', 'victorious'))));
        }
        else if(!self::isValidInviteEmail($emails))
        {
            exit(json_encode(array('notice' => __('Please enter a valid email address', 'victorious'))));
        }
        else 
        {
            foreach($emails as $k => $item)
            {
                if($item == null)
                {
                    unset($emails[$k]);
                }
            }
            array_values($emails);

            $params = array(
                'friend_ids' => $friend_ids != null ? implode(',', $friend_ids) : null,
                'importleagueID' => $importleagueID,
                'emails' => implode(',', $emails),
                'link_url_contest' => $link_url_contest,
                'message_boxinvite' => $message_boxinvite
            );
            $data = self::$victorious->inviteFriend($params);
            exit($data);
        }
    }
    
    private static function isValidInviteEmail($data)
    {
        foreach($data as $item)
        {
            if($item != null && !self::$payment->validEmail($item))
            {
                return false;
            }
        }
        return true;
    }

    public static function loadPoolsByOrg()
    {
        $orgID = sanitize_text_field($_POST['orgID']);
        $aPools = self::$pools->getPools(null, $orgID, true, true);
        $resultPools = $resultFights = $sport = null;
        if($aPools != null)
        {
            foreach($aPools as $aPool)
            {
                $resultPools .= '<option value="'.$aPool['poolID'].'">'.$aPool['poolName'].'</option>';
                $sport = $aPool['type'];
            }
        }
        exit(json_encode(array('resultPools' => $resultPools, 'resultFights' => $resultFights, 'sport' => $sport)));
    }
    
    public static function loadFights()
    {
        $poolID = sanitize_text_field($_POST['poolID']);
        $aFights = self::$pools->getFights($poolID, null, true);
        $resultFights = null;
        if($aFights != null)
        {
            foreach($aFights as $aFight)
            {
                $resultFights .= '<input type="checkbox" checked="checked" name="fixture_'.$poolID.'_'.$aFight['fightID'].'" id="fixture_'.$poolID.'_'.$aFight['fightID'].'" value="'.$aFight['fightID'].'">'
                                .'<label for="fixture_'.$poolID.'_'.$aFight['fightID'].'">'.$aFight['name'].'   '.date("H:i:s",strtotime($aFight['startDate'])) .'</label><br/>';

            }

        }
        exit($resultFights);
    }
    
    public static function calculatePrizes()
    {
        $type = sanitize_text_field($_POST['type']);
        $structure = sanitize_text_field($_POST['structure']);
        $size = sanitize_text_field($_POST['size']);
        $entryFee = sanitize_text_field($_POST['entry_fee']);
        $prizes = self::$pools->calculatePrizes($type , $structure, $size, $entryFee);
        
        $result = '<table style="width:100%">'
                . '<tr><td style="text-align:left">Pos</td><td style="text-align:right">Prize</td></tr>';
        $count = 0;
        foreach($prizes as $prize)
        {
            $count++;
            $place = null;
            switch ($count)
            {
                case 1:
                    $place = '1st';
                    break;
                case 2:
                    $place = '2nd';
                    break;
                case 3:
                    $place = '3rd';
                    break;
            }
            $result .= '<tr><td style="text-align:left">'.$place.'</td><td style="text-align:right">$'.$prize.'</td></tr>';
        }
        $result .= '</table>';
        exit($result);
    }
    
    public static function viewPoolFixture()
    {
        $iPoolID = sanitize_text_field($_POST['iPoolID']);
        $aFights = self::$pools->getFights($iPoolID, null, true);
        $sResult = '';
        $count = 0;
        if($aFights != null)
        {
            $aPool = self::$pools->getPools($iPoolID, null, false, true);
            if($aPool['type'] == 'MMA' || $aPool['type'] == 'BOXING')
            {
                $teamOrFighterHeader = __("Fighter", 'victorious');
            }
            else 
            {
                $teamOrFighterHeader = __("Team", 'victorious');
            }
            $sResult .= '<table class="table table-striped table-bordered table-responsive table-condensed">';
            foreach($aFights as $aFight)
            {
                $spread1 = $aFight['team1_spread_points'];
                $spread2 = $aFight['team2_spread_points'];
                $sResult .= '<tr>
                                <td style="text-align:center">'.$spread1.' '.$aFight['name'].' '.$spread2.'</td>
                            </tr>';
            }
            $sResult .= '</table>';
        }
        else 
        {
            $sResult = '<center>'.__("No fixtures", 'victorious').'</center>';
        }
        exit($sResult);
    }
    
    //////////////////////////////v2//////////////////////////////
    public static function loadPoolInfo()
    {
        $data = self::$victorious->getPoolInfo(sanitize_text_field($_POST['leagueID']));
        $contest_url = VICTORIOUS_URL_SUBMIT_PICKS.sanitize_text_field($_POST['leagueID']);
        $pool = $data['pool'];
        $league = $data['league'];
        $league = self::$victorious->parseLeagueData($league);
        $balance_type = $league['balance_type'];
        $scorings = $data['scoringcats'];
        $fights = $data['fights'];
        $rounds = $data['rounds']; 
        $entries = $data['entries'];
        $note = $league['note'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/contest_detail_popup.php");
        exit;
    }
    
    public static function loadLeagueScoringCategory()
    {
        $data = self::$victorious->getPoolInfo(sanitize_text_field($_POST['leagueID']));
        $contest_url = VICTORIOUS_URL_SUBMIT_PICKS.sanitize_text_field($_POST['leagueID']);
        $pool = $data['pool'];
        $league = $data['league'];
        $scorings = $data['scoringcats'];
        $fights = $data['fights'];
        $rounds = $data['rounds']; 
        $entries = $data['entries']; 
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/contest_detail_tab_scoring.php");
        exit;
    }
    
    public static function loadLeagueEntries()
    {
        $league_id = sanitize_text_field($_POST['leagueID']);
        $league = self::$victorious->getLeagueDetail($league_id);
        $league = $league[0];
        
        $users = self::$victorious->getEntries($league_id);
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/contest_detail_tab_entry.php");
        exit;
    }
    
    public static function loadLeaguePrizes()
    {
        $league = self::$victorious->getLeagueDetail(sanitize_text_field($_POST['leagueID']));
        $league = $league[0];
        $balance_type = $league['balance_type'];
        
        $structure = '';
        if($league['prize_structure'] == 'WINNER')
        {
            $structure = 'winnertakeall';
        }
        else if($league['prize_structure'] == 'MULTI_PAYOUT')
        {
            $structure = 'multi_payout';
        }
        else 
        {
            $structure = 'top3';
        }
        $payouts = null;
        if(!empty($league['payouts']))
        {
            $payouts = json_decode($league['payouts'], true);
        }
        $prizes = self::$pools->calculatePrizes('' , $structure, $league['size'], $league['entry_fee'], $payouts, $league['winner_percent'], $league['first_percent'], $league['second_percent'], $league['third_percent']);
        $aDatas = array();
        if($prizes != null)
        {
            foreach($prizes as $place => $prize)
            {
                $aDatas[] = array('place' => $place, 'prize' => $prize);
            }
        }
        else 
        {
            $aDatas[] = array('place' => '1st', 'prize' => 0);
        }
        $prizes = $aDatas;
        $note = $league['note'];
        
        //guaranteed prize
        $guaranteed_prizes = array();
        if($league['is_guaranteed'])
        {
            $guaranteed_payouts = null;
            if(!empty($league['guaranteed_payouts']))
            {
                $guaranteed_payouts = json_decode($league['guaranteed_payouts'], true);
            }
            $guaranteed_prizes = self::$pools->calculatePrizes('' , $structure, 1, $league['guaranteed_prize'], $guaranteed_payouts, 100, $league['guaranteed_first_percent'], $league['guaranteed_second_percent'], $league['guaranteed_third_percent']);
            $aDatas = array();
            if($guaranteed_prizes != null)
            {
                foreach($guaranteed_prizes as $place => $prize)
                {
                    $aDatas[] = array('place' => $place, 'prize' => $prize);
                }
            }
            else 
            {
                $aDatas[] = array('place' => '1st', 'prize' => 0);
            }
            $guaranteed_prizes = $aDatas;
        }
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/contest_detail_tab_prize.php");
        exit;
    }
    
    public static function loadInviteFriends()
    {
        $leagueID = sanitize_text_field($_POST['leagueID']);
        //list friend
        $aFriends = self::$victorious->getAllPlayerInfo();
        $iTotalFriends = count($aFriends);
        sort($aFriends, SORT_ASC);
        usort($aFriends, function($a, $b) {
            $a = strtolower($a['full_name'] ? $a['full_name'] : $a['user_name']);
            $b = strtolower($b['full_name'] ? $b['full_name'] : $b['user_name']);
            return strcmp($a, $b);
        });
        /*foreach ($aPlayers as $player) {
            $quote_list_data[] = $player['name'];
        }
        $quote_list_data = implode(',', $quote_list_data);*/
        
        $link_contest = VICTORIOUS_URL_GAME;
        /*if ($league['gameType'] == 'GOLFSKIN') {
            $link_contest = VICTORIOUS_URL_GAME;
        }*/
        
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/contest_detail_tab_invite.php");
        exit;
    }
    
    public static function loadLeagueDetail()
    {
        $league = self::$victorious->getLeagueDetail(sanitize_text_field($_POST['leagueID']));
        if($league != null)
        {
            $league = $league[0];
        }
        exit(json_encode($league));
    }
    
    public static function updatePlayerDraftResult()
    {
        if(!self::$pools->updatePlayerDraftResult($_POST))
        {
            exit('<div class=\"error_message\">'.__('Something went wrong! Please try again', 'victorious').'</div>');
        }
        exit('Successfully updated');
    }
    
    public static function loadUserResult()
    {
        $result_detail = self::$victorious->getPlayerPicksResult(sanitize_text_field($_POST['leagueID']), sanitize_text_field($_POST['userID']), sanitize_text_field($_POST['entry_number']),sanitize_text_field($_POST['roundID']),sanitize_text_field($_POST['week']));
        if(isset($result_detail['new']))
        {
            $my_result_detail = self::$victorious->getPlayerPicksResult(sanitize_text_field($_POST['leagueID']), sanitize_text_field($_POST['my_user_id']), sanitize_text_field($_POST['my_entry_number']),sanitize_text_field($_POST['roundID']),sanitize_text_field($_POST['week']));
            $leagueOptionType = $result_detail['league']['balance_type'];
            require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."playerdraft/leaderboard_detail.php");
            exit;
        }
        else
        {
            if(isset($result_detail['stats']))
            {
                $players = $result_detail['stats'];
                $change_players = $result_detail['change_players'];
            }
            else
            {
                $players = $result_detail;
                $change_players = null;
            }
            $is_motocross = sanitize_text_field($_POST['is_motocross']);
            $gameType = sanitize_text_field($_POST['gameType']);
            $leagueOptionType = sanitize_text_field($_POST['leagueOptionType']);
            $rank = sanitize_text_field($_POST['rank']);
            $username = sanitize_text_field($_POST['username']);
            $totalScore = sanitize_text_field($_POST['totalScore']);
            $avatar = sanitize_text_field($_POST['avatar']);
            $name = $gameType == 'GOLFSKIN' ? __("Skin", "victorious") : __("Score", "victorious");

            //exit(json_encode($aResults));
            require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/user_result.php");
            exit;
        }
    }
    
    public static function loadLeagueLobby()
    {
        //leagues
        $leagues = self::$victorious->getLeagueLobby(array(
            'game_type' => !empty($_POST['game_type']) ? sanitize_text_field($_POST['game_type']) : ''
        ));
        if(!empty($_POST['poolId']) && (int)$_POST['poolId'] > 0 && $leagues != null)
		{
			foreach($leagues as $k => $league)
			{
				if($league['poolID'] != sanitize_text_field($_POST['poolId']))
				{
					unset($leagues[$k]);
				}
			}
			$leagues = array_values($leagues);
		}
        $leagues = self::$victorious->parseLeagueData($leagues);
        $no_cash = get_option('victorious_no_cash');

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/lobby_list.php");
        exit;
    }
    
    public static function liveEntriesResult()
    {
       self::$victorious->liveEntriesResult(sanitize_text_field($_POST['leagueID']));
       exit;
    }
    
    public static function loadContestScores()
    {
        $leagueID = sanitize_text_field($_POST['leagueID']);
        $multiEntry = sanitize_text_field($_POST['multiEntry']);
        $no_cash = get_option('victorious_no_cash');
        $week = sanitize_text_field($_POST['week']);
        //scores
        $scores = self::$victorious->getScores($leagueID,$week);
        
        //cur user scores
        $currUserScore = null;
        if($scores != null)
        {
            foreach($scores as $k => $aScore)
            {
                $aScore[$k]['current'] = false;
                if($aScore['userID'] == VIC_GetUserId() && $aScore['entry_number'] == sanitize_text_field($_POST['entry_number']))
                {
                    $scores[$k]['current'] = true;
                }
            }
        }
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/user_score.php");
        exit;
        //exit(json_encode($aScores));
    }
    
    public static function loadFixtureScores()
    {
        $data = self::$victorious->loadFixtureScores(sanitize_text_field($_POST['leagueID']));
        $aFights = $data['fights'];
        $aRounds = $data['rounds'];
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/fixture_result.php");
        exit;
    }
    
    public static function loadPlayerStatistics()
    {
        $data = self::$victorious->getPlayerStatistics(sanitize_text_field($_POST['playerID']));
        if(isset($data['new']))
        {
            $played = $data['played'];
            $minute_played = $data['minute_played'];
            $player = $data['player'];
            $position = $player['position'];
            $team = $player['team'];
            $season_stats = $data['season_stats'];
            $match_stats = $data['match_stats'];
            $performance_chart = $data['performance_chart'];
            $allow_google_news = $data['allow_google_news'];
            require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/player_info_new.php");
            exit;
        }
        else
        {
            $player = $data['player'];
            $sport = $data['sport'];
            $player_stats = $data['scoring_category'];
            $opponent_stats = $data['opponent_scoring_category'];
            $opponent_name = $data['opponent_name'];
            $opponent_played = $data['opponent_played'];
            $stats = $data['stats'];
            $player_news = $data['news'];
            $played = $data['played'];
            $allow_google_news = $data['allow_google_news'];
            $performance_chart = $data['performance_chart'];
            require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/player_info.php");
            exit;
        }
    }
    
    public static function loadPlayerNews()
    {
        require_once(VICTORIOUS__PLUGIN_DIR_MODEL.'simple_html_dom.php');
        header('Content-Type: text/html; charset=ISO-8859-15');
        $site_url = 'https://www.google.com';
        
        //check url
        $keyword = $_POST['player_name'].' '.sanitize_text_field($_POST['player_team']);
        $keyword = str_replace(' ', '%20', $keyword);
        if(!empty($_POST['lang']))
        {
            $locale = sanitize_text_field($_POST['lang']);
        }
        else
        {
            $locale = explode('_', get_locale());
            $locale = !empty($locale[0]) ? $locale[0] : 'en';
        }
        $url = $site_url.'/search?q='.$keyword.'&lr=lang_'.$locale.'&tbm=nws&*';
        if(!empty($_POST['link']))
        {
            $url = sanitize_text_field($_POST['link']);
        }
        
        //load data
        $resp = wp_remote_get($url);
        $resp = wp_remote_retrieve_body($resp);
        
        //parse html
        $html = str_get_html($resp);
        
        //check has result
        if(!$html->find('#search', 0) || trim($html->find('#search', 0)->plaintext) == '')
        {
            echo esc_html(__("No news", "victorious"));
            exit;
        }
        
        //has result
        $result = $html->find('#search', 0)->innertext;
        $result = str_replace('href="', 'target="_blank" href="'.$site_url, $result);
        $result .= '<div><ul id="player_news_paging" class="pagination">';
        foreach($html->find('#nav tbody td') as $link)
        {
            if($link->find('a', 0))
            {
                $result .= '<li><a href="'.$site_url.$link->find('a', 0)->href.'">'.$link->plaintext.'</a></li>';
            }
            else
            {
                $result .= '<li><span>'.$link->plaintext.'</span></li>';
            }
        }
        $result .= '</div></ul>';
        echo esc_html($result);
        exit;
    }

    //////////////////
    ///   payment   //
    /////////////////
    public static function addCredits()
    {
        $credits = sanitize_text_field($_POST['credits']);
        $gateway = sanitize_text_field($_POST['gateway']);
        $balance_type = self::$payment->getBalanceTypeByGateway($gateway);
		unset($_SESSION['paypal_complete']);

        if(!isset($_SESSION['is_transaction']))
        {
            if((!is_numeric($credits) || (int)$credits < 1) &&
                $gateway != VICTORIOUS_GATEWAY_DFSCOIN)
            {
                exit(json_encode(array('notice' => __('Credits not valid', 'victorious'))));
            }
            else if($credits < get_option('victorious_minimum_deposit') &&
                $gateway != VICTORIOUS_GATEWAY_DFSCOIN)
            {
                exit(json_encode(array('notice' => __('Credits must be greater than ').get_option('victorious_minimum_deposit', 'victorious'))));
            }
            else if(!empty($_POST['coupon_code']) && 
                    !self::$coupon->isCouponCodeExist(sanitize_text_field($_POST['coupon_code']), CP_ACTION_EXTRA_DEPOSIT) && $gateway != VICTORIOUS_GATEWAY_FTTECH)
            {
                exit(json_encode(array('notice' => __('This code does not exist', 'victorious'))));
            }
            else if(!empty($_POST['coupon_code']) && 
                    self::$coupon->isCouponCodeUsed(sanitize_text_field($_POST['coupon_code']), CP_ACTION_EXTRA_DEPOSIT) && $gateway != VICTORIOUS_GATEWAY_FTTECH)
            {
                exit(json_encode(array('notice' => __('This code has already used', 'victorious'))));
            }
            else if(!empty($_POST['coupon_code']) && 
                    self::$coupon->isCouponCodeLimit(sanitize_text_field($_POST['coupon_code']), CP_ACTION_EXTRA_DEPOSIT) && $gateway != VICTORIOUS_GATEWAY_FTTECH)
            {
                exit(json_encode(array('notice' => __('This code has reached to limit', 'victorious'))));
            }
            else if(!self::$payment->isGatewayExist($gateway) && $gateway != VICTORIOUS_GATEWAY_PAYPAL_PRO)
            {
                exit(json_encode(array('notice' => __('Please select gateway', 'victorious'))));
            }

            //validate gateway
            switch($gateway)
            {
                case VICTORIOUS_GATEWAY_PAYPAL_PRO:
                    self::validDateGatewayPaypalpro($_POST);
                    break;
                case VICTORIOUS_GATEWAY_DFSCOIN:
                    self::validateCoinGateway($_POST);
                    break;
            }
            
            if($gateway != VICTORIOUS_GATEWAY_DFSCOIN)
            {
                $money = self::$payment->changeCreditToCash($credits);
                $site_profit = $credits - $money;
                $credits = self::$payment->feePercentage($credits);

                $reason = '';
                if(!empty($_POST['coupon_code']))
                {
                    $coupon = self::$coupon->getCouponByCode(sanitize_text_field($_POST['coupon_code']), CP_ACTION_EXTRA_DEPOSIT);
                    if($coupon != null)
                    {
                        $money += self::$coupon->getTotalDiscountValue($coupon->discount_type, $coupon->discount_value, $money);
                        $reason = __("Coupon code: ".sanitize_text_field($_POST['coupon_code']), 'victorious');
                        self::$coupon->addCouponUsed($coupon->id, VIC_GetUserId());
                    }
                }
                $params = array(
                    'amount' => $money,
                    'operation' => 'ADD',
                    'type' => 'DEPOSIT',
                    'gateway' => $gateway,
                    'reason' => $reason,
                    'site_profit' => $site_profit,
                    'cash_to_credit' => (int)get_option('victorious_cash_to_credit'),
                    'balance_type' => $balance_type
                );
                $iFundHitoryId = self::$payment->addFundhistory($params);
            }
            if((int)$iFundHitoryId > 0 || 
               $gateway == VICTORIOUS_GATEWAY_DFSCOIN)
            {
                //gateway checkout
                switch($gateway)
                {
                    case VICTORIOUS_GATEWAY_PAYPAL_PRO:
                        self::gatewayPaypalpro($_POST, $credits, $gateway, $aSettings, $iFundHitoryId,$money);
                        break;
                    case VICTORIOUS_GATEWAY_DFSCOIN:
                        self::coinGateway($_POST);
                        break;
                    default :
                        $aSettings = array(
                            'paypal_email' => get_option('paypal_email_account'),
                            'business' => get_option('paypal_email_account'),
                            'item_name' => "Deposit ".$iFundHitoryId,
                            'item_number' => 1,
                            'amount' => $credits,
                            'notify_url' => VICTORIOUS_URL_NOTIFY_ADD_FUNDS,
                            'return' => VICTORIOUS_URL_SUCCESS_ADD_FUNDS,
                            'cancel_return' => VICTORIOUS_URL_ADD_FUNDS,
                            'custom' => VIC_GetUserId().'|'.$iFundHitoryId.'|'.$money
                        );

                        $sUrl = self::$payment->onlineTransaction($gateway, $aSettings,NULL,$money);
                        if($sUrl)
                        {
                            unset($_SESSION['is_transaction']);
                            if(strstr($sUrl, "//"))
                                exit(json_encode(array('result' => $sUrl)));
                            else
                                exit(json_encode(array('notice' => $sUrl)));
                        }
                        else
                        {
                            self::$payment->deleteFundhistory(array(
                                'id' => $iFundHitoryId
                            ));
                            exit(json_encode(array('notice' => __('Something went wrong! Please try again.', 'victorious'))));
                        }
                }
            }
        }
        else 
        {
            exit(json_encode(array('notice' => __('You are in transaction session. To start new session please refresh this page', 'victorious'))));
        }
    }
    
    private static function validateCoinGateway($data)
    {
        if(empty($data['transaction_id']))
        {
            exit(json_encode(array('notice' => __('Please input transaction id', 'victorious'))));
        }
        else
        {
            $existing = true;
            switch ($data['gateway'])
            {
                case VICTORIOUS_GATEWAY_DFSCOIN:
                    $existing = self::$payment->checkAppliedDfscoinTransaction($data['transaction_id']);
                    break;
            }
            if($existing)
            {
                exit(json_encode(array('notice' => __('This transaction id already exists', 'victorious'))));
            }
        }
    }
    
    private static function coinGateway($data)
    {
        $gateway = $data['gateway'];
        $result = self::$payment->onlineTransaction($gateway, $data);
        if($result['success'] == 1)
        {
            $amount = $result['amount'];
            $balance_type = self::$payment->getBalanceTypeByGateway($gateway);
            $balance_key = VIC_BalanceField($balance_type);
            switch ($gateway)
            {
                case VICTORIOUS_GATEWAY_DFSCOIN:
                    $amount = self::$payment->exchangeRateDfscoin($amount);
                    break;
            }
            $money = self::$payment->changeCreditToCash($amount);
            $site_profit = $amount - $money;
            $amount = $money;
            $user = self::$payment->getUserData();

            $reason = '';
            if(!empty($_POST['coupon_code']))
            {
                $coupon = self::$coupon->getCouponByCode(sanitize_text_field($_POST['coupon_code']), CP_ACTION_EXTRA_DEPOSIT);
                if($coupon != null)
                {
                    $amount += self::$coupon->getTotalDiscountValue($coupon->discount_type, $coupon->discount_value, $amount);
                    $reason = __("Coupon code: ".sanitize_text_field($_POST['coupon_code']), 'victorious');
                    self::$coupon->addCouponUsed($coupon->id, VIC_GetUserId());
                }
            }

            $params = array(
                'amount' => $amount,
                'operation' => 'ADD',
                'type' => 'DEPOSIT',
                'new_balance' => $user[$balance_key] + $amount,
                'gateway' => $gateway,
                'cash_to_credit' => (int)get_option('fanvictor_cash_to_credit'),
                'status' => "completed",
                'transactionID' => $result['txid'],
                'is_checkout' => 1,
                'site_profit' => $site_profit,
                'balance_type' => $balance_type
            );
            $iFundHitoryId = self::$payment->addFundhistory($params);
            if($iFundHitoryId > 0)
            {
                self::$payment->updateUserBalance($amount, false, 0, VIC_GetUserId(), $balance_type);
                VIC_SetMessage(__('Successfully add fund', 'victorious'));
                exit(json_encode(array('result' => VICTORIOUS_URL_ADD_FUNDS.'?type='.$gateway)));
            }
            else
            {
                exit(json_encode(array('notice' => __('Cannot add fund! Please try again.', 'victorious'))));
            }
        }
        else
        {
            exit(json_encode(array('notice' => $result['message'])));
        }
    }
    
    private static function validDateGatewayPaypalpro($data)
    {
        if(empty($data['first_name']))
        {
            exit(json_encode(array('notice' => __('Please input first name', 'victorious'))));
        }
        else if(empty($data['street']))
        {
            exit(json_encode(array('notice' => __('Please input address', 'victorious'))));
        }
        else if(empty($data['last_name']))
        {
            exit(json_encode(array('notice' => __('Please input last name', 'victorious'))));
        }
        else if(empty($data['credit_card_number']))
        {
            exit(json_encode(array('notice' => __('Please input credit card number', 'victorious'))));
        }
        else if(empty($data['city']))
        {
            exit(json_encode(array('notice' => __('Please input city', 'victorious'))));
        }
        else if(empty($data['credit_card_type']))
        {
            exit(json_encode(array('notice' => __('Please select credit card type', 'victorious'))));
        }
        else if(empty($data['countrycode']))
        {
            exit(json_encode(array('notice' => __('Please select country', 'victorious'))));
        }
        else if(empty($data['expire_month']))
        {
            exit(json_encode(array('notice' => __('Please input expire month', 'victorious'))));
        }
        else if(empty($data['expire_year']))
        {
            exit(json_encode(array('notice' => __('Please input expire year', 'victorious'))));
        }
        else if(empty($data['cvv']))
        {
            exit(json_encode(array('notice' => __('Please input security code', 'victorious'))));
        }
        else if(empty($data['state']))
        {
            exit(json_encode(array('notice' => __('Please input state', 'victorious'))));
        }
    }
    
    private static function gatewayPaypalpro($data, $credits, $gateway, $aSettings, $iFundHitoryId,$money)
    {
        $aSettings = array(
            'CREDITCARDTYPE' => $data['credit_card_type'],
            'ACCT' => $data['credit_card_number'],
            'EXPDATE' => $data['expire_month'].$data['expire_year'],
            'CVV2' => $data['cvv'],
            'FIRSTNAME' => $data['first_name'],
            'LASTNAME' => $data['last_name'],
            'STREET' => $data['street'],
            'CITY' => $data['city'],
            'STATE' => $data['state'],
            'COUNTRYCODE' => $data['countrycode'],
            'ZIP' => $data['zipcode'],
            'AMT' => $credits
        );
        $result = self::$payment->onlineTransaction($gateway, $aSettings, $iFundHitoryId,$money);
        if($result)
        {
            VIC_SetMessage(__('Successfully add fund', 'victorious'));
            if(!is_bool($result))
            {
                exit(json_encode(array('notice' => $result)));
            }
            exit(json_encode(array('result' => VICTORIOUS_URL_ADD_FUNDS.'?type='.VICTORIOUS_GATEWAY_PAYPAL)));
        }
        else
        {
            exit(json_encode(array('notice' => __('Something went wrong, please try again.', 'victorious'))));
        }
    }
    
    public static function requestPayment()
    {
        $user_id = VIC_GetUserId();
        $online = false;
        $priority = false;
        $flutterwave = false;
        $gateway_id = sanitize_text_field($_POST['val']['gateway_id']);
        $credits = sanitize_text_field($_POST['val']['credits']);
        $reason = sanitize_text_field($_POST['val']['reason']);
        $balance_type_id = !empty($_POST['val']['balance_type_id']) ? sanitize_text_field($_POST['val']['balance_type_id']) : '';
        if($balance_type_id != '' && $balance_type_id != VICTORIOUS_DEFAULT_BALANCE_TYPE_ID){
            $gateway_id = '';
        }

        if(!is_numeric($credits) || (int)$credits < 1)
        {
            exit(json_encode(array('notice' => 'Amount not valid')));
        }
        else if(!self::$payment->isAllowWithdraw($credits, $user_id, $balance_type_id))
        {
            exit(json_encode(array('notice' => __('Amount must not exceed your available balance', 'victorious'))));
        }

        //validate gateway
        switch($gateway_id)
        {
            case VICTORIOUS_GATEWAY_PAYPAL:
                $online = true;
                self::validDateRequestPaymentPaypal();
                break;
            case VICTORIOUS_GATEWAY_DFSCOIN:
                self::validDateRequestPaymentDFSCoin();
                break;
        }

        //update data
        if(self::$payment->updateUserBalance($credits, true, 0, null, $balance_type_id))
        {
            //add withdrawl
            $user_balance = self::$payment->getUserBalance($user_id, $balance_type_id);
            $withdraw_data = array(
                'userID' => $user_id,
                'amount' => $credits,
                'new_balance' => $user_balance['balance'],
                'reason' => $reason,
                'gateway' => $gateway_id,
                'balance_type_id' => $balance_type_id
            );
            if($gateway_id == VICTORIOUS_GATEWAY_DFSCOIN)
            {
                $withdraw_data['dfscoin_wallet_address'] = sanitize_text_field($_POST['val']['dfscoin_wallet_address']);
            }
            $withdrawlId = self::$payment->addWithdraw($withdraw_data);
                    
            //update user info
            $data = array(
                'email' => !empty($_POST['val']['email']) ? sanitize_text_field($_POST['val']['email']) : '',
                'name' => !empty($_POST['val']['name']) ? sanitize_text_field($_POST['val']['name']) : '',
                'house' => !empty($_POST['val']['house']) ? sanitize_text_field($_POST['val']['house']) : '',
                'street' => !empty($_POST['val']['street']) ? sanitize_text_field($_POST['val']['street']) : '',
                'unit_number' => !empty($_POST['val']['unit_number']) ? sanitize_text_field($_POST['val']['unit_number']) : '',
                'city' => !empty($_POST['val']['city']) ? sanitize_text_field($_POST['val']['city']) : '',
                'state' => !empty($_POST['val']['state']) ? sanitize_text_field($_POST['val']['state']) : '',
                'country' => !empty($_POST['val']['country']) ? sanitize_text_field($_POST['val']['country']) : '',
                'username' => !empty($_POST['val']['username']) ? sanitize_text_field($_POST['val']['username']) : '',
                'password' => !empty($_POST['val']['password']) ? sanitize_text_field($_POST['val']['password']) : '',
                'dfscoin_wallet_address' => !empty($_POST['val']['dfscoin_wallet_address']) ? sanitize_text_field($_POST['val']['dfscoin_wallet_address']) : '',
            );
 
            if(!self::$payment->isUserPaymentInfoExist())
            {
                self::$payment->addUserPaymentInfo($data);
            }
            else 
            {
                self::$payment->updateUserPaymentInfo($data);
            }
			$params = array(
				'userID' => $user_id,
                'amount' => $credits,
                'operation' => 'DEDUCT',
                'type' => 'WITHDRAW',
                'gateway' => $gateway_id,
                'new_balance' => $user_balance['balance'],
                'reason' => 'request  to withdraw',
                'status' => 'completed',
                'withdrawlID' => $withdrawlId,
                'balance_type_id' => $balance_type_id
            );
			$fundhistory_id = self::$payment->addFundhistory($params);
            
            //update verification code
            $verification_code = self::$payment->createVerificationCode($fundhistory_id);
            self::$payment->updateFundhistoryVerificationCode($fundhistory_id, $verification_code);
            
            //send email
            self::$victorious->sendRequestPaymentEmail($withdrawlId, $credits);

            exit(json_encode(array('result' => __('Your request has been sent', 'victorious'), 'redirect' => VICTORIOUS_URL_REQUEST_HISTORY)));
        }
        else
        {
            exit(json_encode(array('notice' => __('Something went wrong! Please try again.', 'victorious'))));
        }
    }
    
    public static function userWithdrawlDlg()
    {
        if(empty($_POST['id']))
        {
            exit(__('No data', 'victorious'));
        }
        $id = sanitize_text_field($_POST['id']);
        $withdraw = self::$payment->getWithdraw($id);
        if(empty($withdraw))
        {
            exit(__('No data', 'victorious'));
        }
        $user = self::$payment->getUserData($withdraw['userID']);
        switch ($withdraw['gateway'])
        {
            default :
                require_once(VICTORIOUS__PLUGIN_DIR_VIEW."/withdrawls/dlg_user_withdrawls.php");
        }
        exit();
    }
    
    private static function validDateRequestPaymentDFSCoin()
    {
        if(empty($_POST['val']['dfscoin_wallet_address'])){
            exit(json_encode(array('notice' => __('Please provide wallet address', 'victorious'))));
        }
    }

    private static function validDateRequestPaymentPaypal()
    {
        if(empty($_POST['val']['email']))
        {
            exit(json_encode(array('notice' => __('Please provide your email', 'victorious'))));
        }
    }
    
    public static function loadUserBalance()
    {
        $aUser = self::$payment->getUserData();
        $aUser['balance'] = VIC_GetUserBalance($aUser['ID']);
        exit($aUser['balance']);
    }

    //////////////////
    ///   admin   ///
    /////////////////
    
    public static function activeAutoContest(){
        $id = sanitize_text_field($_POST['id']);
        $active = sanitize_text_field($_POST['active']);
        if(self::$autocontest->updateAutoContestStatus(array('id' => $id, 'status' => $active))){
            exit(json_encode(array('result' => 'true')));
        }
        exit(json_encode(array('notice' => __('Something went wrong! Please try again.', 'victorious'))));
    }

    public static function activeOrgs()
    {
        $orgID = sanitize_text_field($_POST['id']);
        $active = sanitize_text_field($_POST['active']);
        if(self::$orgs->updateOrgsActive($orgID, $active))
        {
            exit(json_encode(array('result' => 'true')));
        }
        exit(json_encode(array('notice' => __('Something went wrong! Please try again.', 'victorious'))));
    }
	
	public static function reversePointOrgs()
    {
        $orgID = sanitize_text_field($_POST['id']);
        $active = sanitize_text_field($_POST['active']);
        if(self::$orgs->updateOrgsReversePoint($orgID, $active))
        {
            exit(json_encode(array('result' => 'true')));
        }
        exit(json_encode(array('notice' => __('Something went wrong! Please try again.', 'victorious'))));
    }
    
    public static function activeScoringCategory()
    {
        $id = sanitize_text_field($_POST['id']);
        $active = sanitize_text_field($_POST['active']);
        if(self::$scoringcategory->updateScoringCategoryActive($id, $active))
        {
            exit(json_encode(array('result' => 'true')));
        }
        exit(json_encode(array('notice' => __('Something went wrong! Please try again.', 'victorious'))));
    }
    
    public static function loadCbOrgs()
    {
        $sport = sanitize_text_field($_POST['sport']);
        $sel = sanitize_text_field($_POST['sel']);
        $aOrgs = self::$orgs->getOrgs(null, $sport, true);
        $result = null;
        if($aOrgs != null)
        {
            foreach($aOrgs as $aOrg)
            {
                $select = null;
                if($aOrg['organizationID'] == $sel)
                {
                    $select = 'selected="true"';
                }
                $result .= '<option value="'.$aOrg['organizationID'].'" '.$select.'>'.$aOrg['description'].'</option>';
            }
        }
        exit($result);
    }
    
    public static function loadCbFighters()
    {
        $orgsID = sanitize_text_field($_POST['orgsID']);
        $aFighters = self::$fighters->getFighters(null, $orgsID, true);
        $result = '<option value="">--'.__('Please select fighter', 'victorious').'--</option>';
        if($aFighters != null)
        {
            foreach($aFighters as $aFighter)
            {
                $result .= '<option value="'.$aFighter['fighterID'].'">'.$aFighter['name'].'</option>';
            }
        }
        exit($result);
    }
    
    public static function loadCbTeams()
    {
        $orgsID = sanitize_text_field($_POST['orgsID']);
        $aTeams = self::$teams->getTeams(null, $orgsID, true);
        $result = '<option value="">--'.__('Please select team', 'victorious').'--</option>';
        if($aTeams != null)
        {
            foreach($aTeams as $aTeam)
            {
                $result .= '<option value="'.$aTeam['teamID'].'">'.$aTeam['name'].'</option>';
            }
        }
        exit($result);
    }
    
    public static function loadUser()
    {
        $user_id = sanitize_text_field($_POST['user_id']);
        $aUser = self::$user->getUser($user_id);
        $aUser['balance'] = VIC_GetUserBalance($aUser['ID']);
        exit(json_encode($aUser));
    }
    
    /////////////////////////////////payment/////////////////////////////////
    public static function sendUserCredits()
    {
        $task = sanitize_text_field($_POST['task']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $balance_type_id = sanitize_text_field($_POST['balance_type_id']);
        $credits = sanitize_text_field($_POST['credits']);
        $reason = sanitize_text_field($_POST['reason']);
        if(!is_numeric($credits) || (int)$credits < 1)
        {
            exit(json_encode(array('notice' => __('Invalid amount', 'victorious'))));
        }
        else if($task == 'remove' && !self::$payment->isAllowWithdraw($credits, $user_id, $balance_type_id))
        {
            exit(json_encode(array('notice' => __('Amount must not exceed your available balance', 'victorious'))));
        }
        else
        {
            switch($task)
            {
                case 'remove':
                    $decrease = true;
                    $operation = 'DEDUCT';
                    $msg = __('Successfully deducted', 'victorious');
                    break;
                default :
                    $decrease = false;
                    $operation = 'ADD';
                    $msg = __('Successfully added', 'victorious');
                    break;
            }
            if(self::$payment->updateUserBalance($credits, $decrease, 0, $user_id, $balance_type_id))
            {
                $user_balance = self::$payment->getUserBalance($user_id, $balance_type_id);
                $params = array(
                    'userID' => $user_id,
                    'amount' => $credits,
                    'operation' => $operation,
                    'type' => 'CREDITS',
                    'new_balance' => $user_balance['balance'],
                    'reason' => $reason,
                    'status' => 'completed',
                    'balance_type_id' => $balance_type_id
                );
                self::$payment->addFundhistory($params);
				self::$victorious->sendUserCreditEmail($user_id, $credits, $operation, $reason);

				//load balance list
                $victorious = new VIC_Victorious();
                $payment = new VIC_Payment();
                $global_setting = $victorious->getGlobalSetting();
                $default_only = empty($global_setting['allow_multiple_balances']) ? true : false;
                $balance_content = $payment->getDisplayUserBalance($user_id, $default_only);

                exit(json_encode(array('result' => $msg, 'balance' => $balance_content)));
            }
            else
            {
                exit(json_encode(array('notice' => __('Something went wrong! Please try again.', 'victorious'))));
            }
        }
    }
    
    public static function sendUserWithdrawls()
    {
        $withdrawlID = sanitize_text_field($_POST['withdrawlID']);
        $action = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
        $response_message = sanitize_text_field($_POST['response_message']);
        if(empty($action) || VIC_WithdrawalStatus($action) == null)
        {
            exit(json_encode(array('notice' => __('Please select action', 'victorious'))));
        }
        else
        {
            $aWithdrawl = self::$payment->getWithdraw($withdrawlID);
            if(empty($aWithdrawl))
            {
                exit(json_encode(array('notice' => __('Request withdrawl not found', 'victorious'))));
            }
            $aVals = array('status' => $action, 
                           'response_message' => $response_message,
                           'processedDate' => date('Y-m-d H:i:s'));
            if($action == 'DECLINED' && $aWithdrawl['status'] != 'DECLINED')
            {
                if(self::$payment->updateWithdraw($withdrawlID, $aVals) &&
                   self::$payment->updateUserBalance($aWithdrawl['amount'], false, 0, $aWithdrawl['userID'], $aWithdrawl['balance_type_id']))
                {
                    $user_balance = self::$payment->getUserBalance($aWithdrawl['userID'], $aWithdrawl['balance_type_id']);
                    $params = array(
                        'userID' => $aWithdrawl['userID'],
                        'amount' => $aWithdrawl['amount'],
                        'new_balance' => $user_balance['balance'],
                        'gateway' => $aWithdrawl['gateway'],
                        'operation' => 'ADD',
                        'type' => 'WITHDRAW',
                        'reason' => 'declined withdrawl',
                        'status' => 'completed',
                        'balance_type_id' => $aWithdrawl['balance_type_id']
                    );
                    self::$payment->addFundhistory($params);
                    self::$victorious->sendApplyWithdrawlEmail($withdrawlID, $action);
                    exit(json_encode(array('result' => __('Successfully updated', 'victorious'), 'status' => VIC_WithdrawalStatus($action))));
                }
                else
                {
                    exit(json_encode(array('result' => __('Something went wrong! Please try again.', 'victorious'))));
                }
            }
            else if($action == 'APPROVED' && $aWithdrawl['status'] != 'APPROVED')
            {
                if($aWithdrawl['gateway'] == VICTORIOUS_GATEWAY_PAYPAL)
                {
                    $aUserPaymentInfo = self::$payment->getUserPaymentInfo($aWithdrawl['userID']);
                    if(isset($aUserPaymentInfo['email']) && $aUserPaymentInfo['email'] != null)
                    {
                        $aSettings = array(
                            'paypal_email' => $aUserPaymentInfo['email'],
                            'business' => $aUserPaymentInfo['email'],
                            'item_name' => "withdraw ".$withdrawlID,
                            'item_number' => 1,
                            'amount' => $aWithdrawl['real_amount'],
                            'notify_url' => VICTORIOUS_URL_NOTIFY_WITHDRAWLS,
                            'return' => VICTORIOUS_URL_SUCCESS_WITHDRAWLS,
                            'cancel_return' => admin_url().'admin.php?page=withdrawls',
                            'custom' => $withdrawlID.'|'.$response_message);
                        $paypal = new VIC_Paypal();
                        $url = $paypal->parseData($aSettings);
                        exit(json_encode(array('redirect' => $url)));
                    }
                    else 
                    {
                        exit(json_encode(array('notice' => __('This user does not provide email for online transaction', 'victorious'))));
                    }
                }
                elseif($aWithdrawl['gateway'] == VICTORIOUS_GATEWAY_DFSCOIN)
                {
                    self::withdrawCoin($_POST, $aWithdrawl);
                }
                else 
                {
                    $aVals = array('status' => 'APPROVED', 
                                    'response_message' => $response_message,
                                    'processedDate' => date('Y-m-d H:i:s'));
                    if(self::$payment->updateWithdraw($withdrawlID, $aVals))
                    {
                        if($aWithdrawl['status'] == 'DECLINED')
                        {
                            self::$payment->updateUserBalance($aWithdrawl['amount'], true, 0, $aWithdrawl['userID'], $aWithdrawl['balance_type_id']);
                            $user_balance = self::$payment->getUserBalance($aWithdrawl['userID'], $aWithdrawl['balance_type_id']);
                            $params = array(
                                'userID' => $aWithdrawl['userID'],
                                'amount' => $aWithdrawl['amount'],
                                'new_balance' => $user_balance['balance'],
                                'operation' => 'ADD',
                                'type' => 'WITHDRAW',
                                'reason' => 'approved withdrawl',
                                'status' => 'completed',
                                'balance_type_id' => $aWithdrawl['balance_type_id']
                            );
                            self::$payment->addFundhistory($params);
                        }
                        self::$victorious->sendApplyWithdrawlEmail($withdrawlID, $action);
                        exit(json_encode(array('result' => __('Successfully updated', 'victorious'))));
                    }
                    else
                    {
                        exit(json_encode(array('result' => __('Something went wrong! Please try again.', 'victorious'))));
                    }
                }
            }
            else if($action == 'APPROVED' && $aWithdrawl['status'] == 'APPROVED')
            {
                exit(json_encode(array('result' => __('This item already approved', 'victorious'))));
            }
            else if($action == 'DECLINED' && $aWithdrawl['status'] == 'DECLINED')
            {
                exit(json_encode(array('result' => __('This item already declined', 'victorious'))));
            }
            else
            {
                exit(json_encode(array('result' => __('Something went wrong! Please try again.', 'victorious'))));
            }
        }
    }
    
    private static function withdrawCoin($params, $withdrawl)
    {
        $response = array();
        if(empty($params['transactionID']))
        {
            exit(json_encode(array('notice' => __('Please input transaction id', 'victorious'))));
        }
        switch ($withdrawl['gateway'])
        {
            case VICTORIOUS_GATEWAY_DFSCOIN:
                if(empty($withdrawl['dfscoin_wallet_address']))
                {
                    exit(json_encode(array('notice' => __('No wallet address', 'victorious'))));
                }
                if(self::$payment->checkAppliedDfscoinTransaction($params['transactionID']))
                {
                    exit(json_encode(array('notice' => __('This transaction id already exists', 'victorious'))));
                }

                require_once("model".DIRECTORY_SEPARATOR."dfscoin.php");
                $dfscoin = new VIC_Dfscoin();
                $response = $dfscoin->transactionDetail($params['transactionID'], $withdrawl['dfscoin_wallet_address']);
                break;
        }

        if (!empty($response['success'])) 
        {
            $withdraw_data = array(
                'status' => 'APPROVED',
                'transactionID' => $response['data']['transactionID'],
                'processedDate' => date('Y-m-d H:i:s'),
                'response_message' => $response_message,
            );
            self::$payment->updateWithdraw($withdrawl['withdrawlID'], $withdraw_data);
            self::$payment->updateFundhistoryByWithdrawl($withdrawl['withdrawlID'], array(
                'transactionID' => $params['transactionID']
            ));

            self::$victorious->sendApplyWithdrawlEmail($withdrawl['withdrawlID'], $action);
            exit(json_encode(array('result' => __('Successfully updated', 'victorious'))));
        } 
        else
        {
            exit(json_encode(array('notice' => $response['message'])));
        }
    }
    
    /////////////////////////////////view result/////////////////////////////////
    public static function viewResult()
    {
        $iPoolID = sanitize_text_field($_POST['iPoolID']);
        $aFights = self::$pools->getFights($iPoolID);

        $sResult = '';
        $count = 0;
        if($aFights != null)
        {
            $sResult .= '<div id="resultMessage"></div>
                        <form id="formResult">
                        <input type="hidden" name="val[poolID]" value="'.$iPoolID.'" />';
            foreach($aFights as $aFight)
            {
                $count++;
                $aPool = self::$pools->getPools($aFight['poolID'], null, false, true);
                if($aPool['is_team'] == 0)
                {
                    $sFighterName1 = self::$fighters->getFighterName($aFight['fighterID1'], true);
                    $sFighterName2 = self::$fighters->getFighterName($aFight['fighterID2'], true);
                    $teamOrFighterHeader = "Fighter";
                    $sHtmlType = self::viewFighterHtmlType($aFight['fightID'], $aFight['methodID'], $aFight['roundID'], $aFight['minuteID']);
                }
                else 
                {
                    $sFighterName1 = self::$teams->getTeamName($aFight['fighterID1'], true);
                    $sFighterName2 = self::$teams->getTeamName($aFight['fighterID2'], true);
                    $teamOrFighterHeader = "Team";
                    $sHtmlType = self::viewTeamHtmlType($aFight['fightID'], $aFight['team1score'], $aFight['team2score']);
                }
                
                $aFight['fighterID1'] == $aFight['winnerID'] ? $win1 = 'selected = "true"' : $win1 = null;
                $aFight['fighterID2'] == $aFight['winnerID'] ? $win2 = 'selected = "true"' : $win2 = null;
                 ($win1 == null && $win2 == null)? $tie =  'selected = "true"' :$tie = null;
                $sResult .= '<div class="fight_container">
                                <div class="title_area">
                                    <div class="fight_number_title">'.$aFight['name'].'</div>
                                </div>
                                <table>
                                    <tr>
                                        <th>'.$teamOrFighterHeader.' 1</th>
                                        <th>'.$teamOrFighterHeader.' 2</th>
                                    </tr>
                                    <tr>
                                        <td>'.$sFighterName1.'</td>
                                        <td>'.$sFighterName2.'</td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">
                                            <div class="table">
                                                <div class="table_left">
                                                    '.__('Winner', 'victorious').':
                                                </div>
                                                <div class="table_right">
                                                    <select data-name="rounds" name="val[winnerID]['.$aFight['fightID'].']">
                                                        <option value="">Please select winner</option>
                                                        <option value="'.$aFight['fighterID1'].'" '.$win1.'>'.$sFighterName1.'</option>
                                                        <option value="'.$aFight['fighterID2'].'" '.$win2.'>'.$sFighterName2.'</option>
                                                         <option value="0" '.$tie.'>Draw</option>
                                                    </select>
                                                </div>
                                                <div class="clear"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    '.$sHtmlType.'
                                </table>
                            </div>';
            }
            $sResult .= '</form>';
        }
        exit($sResult);
    }
    
    private static function viewFighterHtmlType($fightID, $methodID = null, $roundID = null, $minuteID = null)
    {
        $sResult =  self::viewMethodOfVictoryHtml($fightID, $methodID).
                    self::viewRoundHtml($fightID, $roundID).
                    self::viewMinuteHtml($fightID, $minuteID);
        return $sResult;
    }
    
    private static function viewTeamHtmlType($fightID, $team1score = 0, $team2score = 0)
    {
        $sResult =  '<tr>
                        <td colspan="6">
                            <div class="table">
                                <div class="table_left">
                                    '.__('Team Score', 'victorious').' 1:
                                </div>
                                <div class="table_right">
                                    <input type="text" size="10" name="val[team1score]['.$fightID.']" value="'.$team1score.'">
                                </div>
                                <div class="clear"></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <div class="table">
                                <div class="table_left">
                                    '.__('Team Score', 'victorious').' 2:
                                </div>
                                <div class="table_right">
                                    <input type="text" size="10" name="val[team2score]['.$fightID.']" value="'.$team2score.'">
                                </div>
                                <div class="clear"></div>
                            </div>
                        </td>
                    </tr>';
        return $sResult;
    }

    private static function viewMethodOfVictoryHtml($fightID, $methodID = null)
    {
        $aMethods = self::$fighters->getMethods();
        $sResult = '<td colspan="6">
                                        <div class="table">
                                            <div class="table_left">
                                                '.__('Method of victory', 'victorious').':
                                            </div>
                                            <div class="table_right">
                                                <select data-name="rounds" name="val[methodID]['.$fightID.']">
                                                <option value="-1">Please select method</option>';
        foreach($aMethods as $aMethod)
        {
            $select = null;
            if($aMethod['methodID'] == $methodID)
            {
                $select = 'selected="true"';
            }
            $sResult .= '<option value="'.$aMethod['methodID'].'" '.$select.'>'.$aMethod['description'].'</option>';
        }
        $sResult .= '                     </select>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                    </td>
                                </tr>';
        return $sResult;
    }
    
    private static function viewRoundHtml($fightID, $roundID = null)
    {
        $aRounds = self::$fighters->getRounds();
        $sResult = '<td colspan="6">
                                        <div class="table">
                                            <div class="table_left">
                                                '.__('Round', 'victorious').':
                                            </div>
                                            <div class="table_right">
                                                <select data-name="rounds" name="val[roundID]['.$fightID.']">
                                                <option value="-1">Please select round</option>';
        foreach($aRounds as $aRound)
        {
            $select = null;
            if($aRound == $roundID)
            {
                $select = 'selected="true"';
            }
            $sResult .= '<option value="'.$aRound.'" '.$select.'>'.$aRound.'</option>';
        }
        $sResult .= '                     </select>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                    </td>
                                </tr>';
        return $sResult;
    }
    
    private static function viewMinuteHtml($fightID, $minuteID = null)
    {
        $aMinutes = self::$fighters->getMinutes();
        $sResult = '<td colspan="6">
                                        <div class="table">
                                            <div class="table_left">
                                                '.__('Minute', 'victorious').':
                                            </div>
                                            <div class="table_right">
                                                <select data-name="rounds" name="val[minuteID]['.$fightID.']">
                                                <option value="-1">Please select minute</option>';
        foreach($aMinutes as $aMinute)
        {
            $select = null;
            if($aMinute['minuteID'] == $minuteID)
            {
                $select = 'selected="true"';
            }
            $sResult .= '<option value="'.$aMinute['minuteID'].'" '.$select.'>'.$aMinute['description'].'</option>';
        }
        $sResult .= '                     </select>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                    </td>
                                </tr>';
        return $sResult;
    }
    /////////////////////////////////end view result/////////////////////////////////
    
    /////////////////////////////////update complete/////////////////////////////////
    public static function updateResult()
    {
        $aFights = self::$pools->getFights(sanitize_text_field($_POST['val']['poolID']));
        $success = true;
        foreach($aFights as $aFight)
        {
            $fightID = $aFight['fightID'];
            $data = array('poolID' => $aFight['poolID'],
                          'fightID' => $fightID,
                          'winnerID' => sanitize_text_field($_POST['val']['winnerID'][$fightID]),
                          'methodID' => isset($aVals['methodID'][$fightID]) ? sanitize_text_field($_POST['val']['methodID'][$fightID]) : '-1',
                          'roundID' => isset($aVals['roundID'][$fightID]) ? sanitize_text_field($_POST['val']['roundID'][$fightID]) : '-1',
                          'minuteID' => isset($aVals['minuteID'][$fightID]) ? sanitize_text_field($_POST['val']['minuteID'][$fightID]) : '-1',
                          'team1score' => isset($aVals['team1score'][$fightID]) ? sanitize_text_field($_POST['val']['team1score'][$fightID]) : 0,
                          'team2score' => isset($aVals['team2score'][$fightID]) ? sanitize_text_field($_POST['val']['team2score'][$fightID]) : 0);
            if(!self::$pools->updateFightResult($data))
            {
                $success = false;
            }
        }
        if(!$success)
        {
            exit('<div class=\"error_message\">'.__('Something went wrong! Please try again', 'victorious').'</div>');
        }
        exit('Successfully updated');
    }
    
    public static function updatePoolComplete()
    {
        $iPoolID = sanitize_text_field($_POST['iPoolID']);
        $status = sanitize_text_field($_POST['status']);
        if(!self::$pools->isPoolExist($iPoolID))
        {
            exit(json_encode(array('notice' => __('This pool does not exist', 'victorious'))));
        }
        else if($status != "NEW" && $status != "COMPLETE")
        {
            exit(json_encode(array('notice' => __('Please select status', 'victorious'))));
        }
        else if(!self::$pools->isPoolResultsUpdated($iPoolID))
        {
            exit(json_encode(array('notice' => __('Please update pool result', 'victorious'))));
        }
        else if($status == 'COMPLETE')
        {
            if(self::$pools->updatePoolComplete($iPoolID))
            {
                exit(json_encode(array('result' => 'Successfully updated')));
            }
            else 
            {
                exit(json_encode(array('notice' => __('Something went wrong! Please try again', 'victorious'))));
            }
        }
        else 
        {
            self::$pools->updatePoolStatus($iPoolID, 'NEW');
            exit(json_encode(array('result' => __('Successfully updated', 'victorious'))));
        }
    }
    
    public static function reverseResult()
    {
        $result = self::$pools->reverseResult(sanitize_text_field($_POST['poolID']));
        switch($result)
        {
            case 2:
                exit(json_encode(array('notice' => __('Event does not exist', 'victorious'))));
                break;
            case 1:
                exit(json_encode(array('result' => __('Successfully reversed', 'victorious'))));
                break;;
            default :
                exit(json_encode(array('notice' => __('Something went wrong! Please try again', 'victorious'))));
        }
    }
    /////////////////////////////////end update complete/////////////////////////////////
    
    /////////////////////////////////v2/////////////////////////////////
    public static function viewPlayerDraftResult()
    {
        $iPoolID = sanitize_text_field($_POST['iPoolID']);
        $data = self::$pools->viewPlayerDraftResult($iPoolID);
        exit(json_encode($data));
    }
    
    public static function loadPlayerPoints()
    {
        $poolID = sanitize_text_field($_POST['poolID']);
        $fightID = sanitize_text_field($_POST['fightID']);
        $roundID = sanitize_text_field($_POST['roundID']);
        $playerID = sanitize_text_field($_POST['playerID']);
        $page = sanitize_text_field($_POST['page']);
        
        //scoring category
        $item_per_page = 10;
        $aScorings = self::$scoringcategory->getPlayerStatsScoring($poolID, $fightID, $roundID, $playerID, $item_per_page, $page);
        
        $big = 999999999;
        /*$paging = paginate_links( array(
            'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format' => '#',
            'current' => max($page, get_query_var('paged') ),
            'total' => ceil($aScorings['total'] / $item_per_page)
        ));*/
        //$aScorings['paging'] = $paging;
        exit(json_encode($aScorings));
    }
    
    public static function addMoneyByCoupon()
    {        
        try
        {
            $res = self::applyCouponAddMoney(sanitize_text_field($_POST['coupon_code']));
            $resp = array('result' => $res['result']);
        }
        catch (Exception $ex)
        {
            $resp = array('notice' => $ex->getMessage());
        }
        exit(json_encode($resp));
    }
    
    private static function applyCouponAddMoney($code)
    {
        if(empty($code))
        {
            throw new Exception(__('Please input coupon code', 'victorious'));
        }
        else if(!self::$coupon->isCouponCodeExist($code, CP_ACTION_ADD_MONEY))
        {
            throw new Exception(__('This code does not exist', 'victorious'));
        }
        else if(self::$coupon->isCouponCodeUsed($code, CP_ACTION_ADD_MONEY))
        {
            throw new Exception(__('This code has already used', 'victorious'));
        }
        else if(self::$coupon->isCouponCodeLimit($code, CP_ACTION_ADD_MONEY))
        {
            throw new Exception(__('This code has reached to limit', 'victorious'));
        }
        else 
        {
            $coupon = self::$coupon->getCouponByCode($code, CP_ACTION_ADD_MONEY);
            if(!empty($coupon))
            {
                $user = self::$payment->getUserData();
                $discount_value = self::$coupon->getTotalDiscountValue($coupon->discount_type, $coupon->discount_value, $user['balance']);
                if(self::$payment->updateUserBalance($discount_value, false, 0, VIC_GetUserId()))
                {
                    $params = array(
                        'amount' => $discount_value,
                        'new_balance' => ($user['balance'] + $discount_value),
                        'operation' => 'ADD',
                        'type' => 'COUPON',
                        'status' => 'completed'
                    );
                    self::$payment->addFundhistory($params);
                    self::$coupon->addCouponUsed($coupon->id, VIC_GetUserId());
                    return array(
                        'result' => __('Successfully added', 'victorious'),
                        'amount' => $discount_value,
                        'balance' => ($user['balance'] + $discount_value),
                        'code' => $code
                        );
                }
            }
            throw new Exception(__('Something went wrong, please try again', 'victorious'));
        }
    }
    
    public static function applyCoupon()
    {
        $code = sanitize_text_field($_POST['coupon_code']);
        $msg = array(
            'text' => __('This code does not exist', 'victorious'),
            'title' => __('Coupon Info', 'victorious')
            );
        $resp = array('msg' => &$msg, 'ok' => false);
        if(self::$coupon->isCouponCodeExist($code, CP_ACTION_ADD_MONEY))
        {
            try
            {
                $res = self::applyCouponAddMoney($code);
                $msg['text'] = __('Money has been added.', 'victorious');
                $resp['ok'] = $msg['reload'] = true;
                $resp['details'] = $res;
            }
            catch (Exception $ex)
            {
                $msg['text'] = $ex->getMessage();
            }
        }
        elseif(self::$coupon->isCouponCodeExist($code, CP_ACTION_EXTRA_DEPOSIT))
        {
            $msg['text'] = __('Extra money will be added to your account after the deposit', 'victorious');
            $resp['ok'] = $resp['needSubmit'] = true;
        }
        exit(json_encode($resp));
    }
    
    public static function createContest()
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
            
        //check valid
        self::validCreateContestData();
        
        //add
        $_POST['is_refund'] = 1;
        $_POST['is_payouts'] = 1;

        $leagueID = self::$victorious->createLeague($_POST);
        if((int)$leagueID < 1)
        {
            echo json_encode(array(
                'result' => 1,
                'msg' =>  __("Something went wrong! Please try again", 'victorious')
            )); 
            exit;
        }
        $league = self::$victorious->getLeagueDetail($leagueID);
        if($league == null)
        {
            echo json_encode(array(
                'result' => 1,
                'msg' =>  __("Something went wrong! Please try again", 'victorious')
            )); 
            exit;
        }
        $league = $league[0];
        
        //buddy press integration
        self::$victorious->addCreateContestActivity($league, VIC_GetUserId());
        
        if($league['is_live_draft'] || $league['gameType'] == VICTORIOUS_GAME_TYPE_UPLOADPHOTO || $league['gameType'] == VICTORIOUS_GAME_TYPE_NFL_PLAYOFF)
        {
            VIC_SetMessage(__("Your contest has been created successfully", 'victorious'));
            echo json_encode(array(
                'result' => 1,
                'url' => VICTORIOUS_URL_LOBBY,
            )); 
            exit;
        }
        else 
        {
            echo json_encode(array(
                'result' => 1,
                'url' => VICTORIOUS_URL_GAME.$leagueID.($league['multi_entry'] ? "?num=1" : "")
            )); 
            exit;
        }
    }

	public static function getStat()
    {
        $dat=$_POST;
        if(empty($dat['sid']))
        {
            echo json_encode(array(
                'result' => 0,
                'msg' =>  __("No sport selected", 'victorious')
            )); 
            exit;
        }
        $dat['sid']+=0;
        
        if(empty($dat['pid']))
        {
            echo json_encode(array(
                'result' => 0,
                'msg' =>  __("No pool selected", 'victorious')
            )); 
            exit;
        }
        
       
       $filters=array("pg"=>$dat['pg'], "posID" => $dat['posid']);
       $item_per_page=20;
        
        //fetch data
        $data = self::$victorious->getStatJS($dat['sid'], $dat['pid'], $filters, $item_per_page, $dat['sort_name'], $dat['sort_value'], $dat['team_id'], $dat['posid']);
        
        if($data){
            exit(json_encode(array(
                    'result' => 1,
                    'data' =>  $data
                )
            ));
		}

        exit(json_encode(array(
                'result' => 0,
                'msg' =>  __("No data", 'victorious')
            )
        ));
    }
    
    private static function validCreateContestData()
    {

        $valid = self::$victorious->validCreateLeague($_POST);
        $msg = '';
        switch($valid)
        {
            case 2;
                $msg = __('Sport does not exist. Please try again.', 'victorious');
                //VIC_Redirect(VICTORIOUS_URL_CREATE_CONTEST, __('Sport does not exist. Please try again.', 'victorious'), true);
                break;
            case 3;
                $msg = __('Date does not exist. Please try again.', 'victorious');
                break;
            case 4;
                $msg = __('Fixture does not exist. Please try again.', 'victorious');
                break;
            case 5;
                $msg = __('Please select at least a fixture.', 'victorious');
                break;
            case 6;
                $msg = __('This game type does not exist.', 'victorious');
                break;
            case 7;
                $msg = __('This sport does not support playerdraft type.', 'victorious');
                break;
            case 8;
                $msg = __('Please enter league name', 'victorious');
                break;
            case 9;
                $msg = __('Round does not exist. Please try again', 'victorious');
                break;
            case 10;
                $msg = __('Please select at least one round', 'victorious');
                break;
            case 11;
                $msg = __('Invalid payouts', 'victorious');
                break;
            case 13;
                $msg = __('Invalid player change quantity', 'victorious');
                break;
            case 14;
                $msg = __('Please enter number of minutes a contestant has to draft', 'victorious');
                break;
            case 15;
                $msg = __('Number of players that be changed via Waiver Wire must be lower than player quantity of lineup', 'victorious');
                break;
            case 16;
                $msg = __('Invalid league size', 'victorious');
                break;
            case 17;
                $msg = __('Please enter start draft date time', 'victorious');
                break;
            case 21;
                $msg = __('Waiver wire start day can not be behind waiver wire end day', 'victorious');
                break;
            case 23;
                $msg = __('No games for select date range', 'victorious');
                break;
            case 24;
                $msg = __('Allow pick from or Allow pick to is empty', 'victorious');
                break;
            case 25;
                $msg = __('Draft game type requires at least two fixtures', 'victorious');
                break;
            case 26;
                $msg = __('Invalid start date', 'victorious');
                break;
            case 27;
                $msg = __('Invalid end date', 'victorious');
                break;
            case 28;
                $msg = __('Invalid salary cap', 'victorious');
                break;
        }

        if(!in_array($_POST['leagueSize'], get_option('victorious_league_size')))
        {
            $msg = __('League size does not exist', 'victorious');
        }
        else if($_POST['entry_fee'] > 0 && !in_array($_POST['entry_fee'], get_option('victorious_entry_fee')))
        {
            $msg = __('Entry fee does not exist', 'victorious');
        }
        if($msg != '')
        {
            echo json_encode(array(
                'result' => 0,
                'msg' => $msg
            )); 
            exit;
        }
    }
    
    private static function getMessageValidCreateContest($valid){
        switch($valid)
        {
            case 2;
                $msg = __('Sport does not exist. Please try again.', 'victorious');
                VIC_Redirect(VICTORIOUS_URL_CREATE_CONTEST, __('Sport does not exist. Please try again.', 'victorious'), true);
                break;
            case 3;
                $msg = __('Date does not exist. Please try again.', 'victorious');
                break;
            case 4;
                $msg = __('Fixture does not exist. Please try again.', 'victorious');
                break;
            case 5;
                $msg = __('Please select at least a fixture.', 'victorious');
                break;
            case 6;
                $msg = __('This game type does not exist.', 'victorious');
                break;
            case 7;
                $msg = __('This sport does not support playerdraft type.', 'victorious');
                break;
            case 8;
                $msg = __('Please enter league name', 'victorious');
                break;
            case 9;
                $msg = __('Round does not exist. Please try again', 'victorious');
                break;
            case 10;
                $msg = __('Please select at least two rounds', 'victorious');
                break;
            case 11;
                $msg = __('Invalid payouts', 'victorious');
                break;
        }
        return $msg;
    }
    private static function validMixingCreateContestData()
    {

        $data = array();
        if(isset($_POST['mixingPools'])){
            $data = sanitize_text_field($_POST['mixingPools']);
        }
        $msg = '';
        foreach($data as $index=>$item){
            $orgID = $index;
            $poolID = array_keys($item);
            $poolID = $poolID[0];
            $fights = $item[$poolID];
            $valid = self::$victorious->validCreateLeague($orgID, $poolID,
                sanitize_text_field($_POST['game_type']), sanitize_text_field($_POST['leaguename']),
                                                     $fights, 
                                                     isset($_POST['roundID']) ? sanitize_text_field($_POST['roundID']) : null,
                                                     isset($_POST['payouts_from']) ? sanitize_text_field($_POST['payouts_from']) : null,
                                                     isset($_POST['payouts_to']) ? sanitize_text_field($_POST['payouts_to']) : null,
                                                     isset($_POST['percentage']) ? sanitize_text_field($_POST['percentage']) : null);
            if($valid > 1){
                $msg =  self::getMessageValidCreateContest($valid);
            }
        }
        if(empty($data)){
           $msg =  self::getMessageValidCreateContest(4);
        }
        

        if(!in_array(sanitize_text_field($_POST['leagueSize']), get_option('victorious_league_size')))
        {
            $msg = __('League size does not exist', 'victorious');
        }
        else if(sanitize_text_field($_POST['entry_fee']) > 0 && !in_array(sanitize_text_field($_POST['entry_fee']), get_option('victorious_entry_fee')))
        {
            $msg = __('Entry fee does not exist', 'victorious');
        }
        if($msg != '')
        {
            echo json_encode(array(
                'result' => 0,
                'msg' => $msg
            )); 
            exit;
        }
    }
	
	public static function showPoolStatisticDetail()
    {
        $leagueID = sanitize_text_field($_POST['leagueID']);
        $aLeagues = self::$statistic->eventStatistic($leagueID);
        exit(json_encode($aLeagues));
    }
    
    public static function showUserPicks()
    {
        $leagueID = sanitize_text_field($_POST['leagueID']);
        $data = self::$victorious->showUserPicks($leagueID);
        if($data['result'] == 0)
        {
            echo esc_html(__("This league does not exist", 'victorious'));
            exit;
        }
        $users = $data['picks'];
        $league = $data['league'];
        
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW."/Elements/contest_picks.php");
        exit();
    }
    
    public static function sendUserPickEmail()
    {
        /*if(!empty($_SESSION['userPicksInfo']))
        {
            $info = sanitize_text_field($_SESSION['userPicksInfo']);
            if($info[0] == sanitize_text_field($_POST['leagueID']))
            {
                self::$victorious->sendUserPickEmail($info[0], $info[1], $info[2]);
            }
            unset($_SESSION['userPicksInfo']);
        }*/
        exit;
    }
    
    public static function loadSummary()
    {
        $data = self::$victorious->getGamesummary(sanitize_text_field($_POST['page']), sanitize_text_field($_POST['sort_by']), sanitize_text_field($_POST['sort_type']));
        $users = $data['users'];
        $num_page = $data['num_page'];
        $current_page = $data['current_page'];
        include VICTORIOUS__PLUGIN_DIR_VIEW.'Elements/user_summary.php';
        exit;
    }
    
    public static function sendPrivateInvitationEmail()
    {
        $friend_ids = !empty($_POST['friend_ids']) ? implode(',', sanitize_text_field($_POST['friend_ids'])) : null;
        $leagueID = !empty($_POST['leagueID']) ? sanitize_text_field($_POST['leagueID']) : '';
        $emails = !empty($_POST['emails']) ? implode(',', explode(PHP_EOL, sanitize_text_field($_POST['emails']))) : null;
        if(!self::$victorious->isLeagueExist($leagueID))
        {
            echo json_encode(array(
                'message' => __('Contest does not exist', 'victorious')
            ));
            exit;
        }
        else
        {
            $params = array(
                'friend_ids' => $friend_ids,
                'importleagueID' => $leagueID,
                'emails' => $emails
            );
            echo self::$victorious->inviteFriend($params);
            exit;
        }
    }
    public function getUserbalance(){
        $user = self::$payment->getUserData();
        echo json_encode(array('balance'=>$user['balance'],'url'=>VICTORIOUS_URL_ADD_FUNDS));exit;
    }
    public function sendUploadedFileStats(){
        $file = $_FILES['file'];
        if(empty($file)){
            echo json_encode(array('result'=>'0'));exit;
        }
        if($file['error'] >0){
                echo json_encode(array('result'=>'1'));exit;
        }
        $extension = end(explode('.', $file['name']));
         if(strtolower($extension) != 'csv'){
                echo json_encode(array('result'=>'2'));exit;
        }
        $data['file_name'] = $file['name'];
        $data['poolID'] = sanitize_text_field($_POST['poolID']);
        $data['org_id'] = sanitize_text_field($_POST['org_id']);
        $tool_url = self::$victorious->createFolderCustomSport($data);
        
        $upload['filename'] = $file['name'];
        $upload['upload_file'] = $file['tmp_name'];
        $upload['dir_path'] = $tool_url[0];
        $upload['filesize'] = $file['size'];
        $upload['poolID'] = sanitize_text_field($_POST['poolID']);

        self::sendFile($upload);
            
        
    }
    public static  function sendFile($file){
    $url = get_option('victorious_api_url_admin').'/upload_file.php';
        $headers = array("Content-Type:multipart/form-data"); // cURL headers for file uploading
        $postfields = array("filedata" => new \CurlFile($file['upload_file'], 'application/vnd.ms-excel', $file['filename']), "filename" => $file['filename'],'dir_path'=>$file['dir_path'],'poolID'=>$file['poolID'], 'filesize' => $file['filesize']);

        $args = array(
            'body'        => $postfields,
            'blocking'    => true,
            'headers'     => array(),
            'cookies'     => array(),
        );
        $response = wp_remote_post($url, $args);
        $is_success = wp_remote_retrieve_body($response);
        if($is_success){
            echo json_encode(array('result'=>'3','filename'=>$file['filename']));exit;
        }else{
            echo json_encode(array('result'=>'1'));exit;
        }
    }  
    
    public function loadStatsUploadedFile(){
        $data = self::$victorious->loadStatsUploadedFile($_POST);
        exit(json_encode($data));
    }
    
    public function sendUserJoincontestEmail() {
        if (get_option('victorious_get_email_from_better_join_contest')) {
            self::$victorious->sendUserJoincontestEmail(sanitize_text_field($_POST['league_id']), sanitize_text_field($_POST['entry_number']));
          
        }
    }
    
    public function deleteBackgroundTeamImage() {
        $res = array();

        $team_id = sanitize_text_field($_POST['teamID']);

        if ($team_id) {
            $dir_image = VICTORIOUS__PLUGIN_DIR . 'assets/teams/';
            $old_image = $dir_image . $team_id . '.*';
            $glob = glob($old_image);
            if (!empty($glob)) {
                foreach ($glob as $item) {
                    unlink($item);
                }
            }
        }

        
    }
    
    public function suggestUsername()
    {
        $data = self::$victorious->suggestUsername(sanitize_text_field($_POST['username']));
        $result = array();
        if($data != null)
        {
            foreach($data as $item)
            {
                $result[] = array(
                    'value' => $item['ID'],
                    'label' => $item['user_login']
                );
            }
        }
        echo json_encode($result);exit;
    }
    
    public function transferToAccount()
    {
        $amount = sanitize_text_field($_POST['amount']);
        $from_user_id = VIC_GetUserId();
        $transfer_username = sanitize_text_field($_POST['username']);
        $balance_type_id = !empty($_POST['balance_type_id']) ? sanitize_text_field($_POST['balance_type_id']) : '';
        $transfer_user = self::$payment->getUserByLoginName($transfer_username);
        if(!is_numeric($amount) || (int)$amount < 1)
        {
            exit(json_encode(array('notice' => 'Invalid amount')));
        }
        else if(!self::$payment->isAllowWithdraw($amount, $from_user_id, $balance_type_id))
        {
            exit(json_encode(array('notice' => __('Amount must not exceed your available balance', 'victorious'))));
        }
        else if($transfer_user == null)
        {
            exit(json_encode(array('notice' => __('User dose not exist', 'victorious'))));
        }
        else
        {
            $transfer_user_id = $transfer_user['ID'];
            
            //deduct for current user
            if(self::$payment->updateUserBalance($amount, true, $leagueID = 0, $from_user_id, $balance_type_id))
            {
                $user_balance = self::$payment->getUserData($from_user_id, $balance_type_id);
                $params = array(
                    'userID' => $from_user_id,
                    'amount' => $amount,
                    'operation' => 'DEDUCT',
                    'type' => 'TRANSFER',
                    'new_balance' => $user_balance['balance'],
                    'reason' => 'transfered to user id '.$transfer_user_id,
                    'status' => 'completed'
                );
                self::$payment->addFundhistory($params);
            }
            
            //transfer funds to new user
            if(self::$payment->updateUserBalance($amount, false, $leagueID = 0, $transfer_user_id, $balance_type_id))
            {
                $user_balance = self::$payment->getUserData($transfer_user_id, $balance_type_id);
                $params = array(
                    'userID' => $transfer_user_id,
                    'amount' => $amount,
                    'operation' => 'ADD',
                    'type' => 'TRANSFER',
                    'new_balance' => $user_balance['balance'],
                    'reason' => 'transfered from user id '.$from_user_id,
                    'status' => 'completed'
                );
                self::$payment->addFundhistory($params);
            }
            
            exit(json_encode(array('result' => __('Successfully transfered', 'victorious'))));
        }
    }
    
    public function loadMotocrossPlayerPoints(){
        $result = self::$victorious->loadMotocrossPlayerPoints($_POST);
        echo json_encode($result);exit;
    }
    
    public function updateMotocrossPlayerResult(){

        self::$victorious->updateMotocrossPlayerResult($_POST);
        exit('Successfully updated');
    }

    public function loadFlutterwaveBankEnquiry(){
        require_once VICTORIOUS__PLUGIN_DIR_MODEL . 'flutterwave.php';

        $flw = new FlutterWave();
        $banks = $flw->loadBankEnquiry();
        $userBankCode = self::$payment->getUserPaymentBankCode();

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/list_flutterwave_banks.php");
        exit;
    }

    public static function loadStatsSportInfo()
    {
        $sport_id = !empty($_POST['sport_id']) ? sanitize_text_field($_POST['sport_id']) : '';
        $data = self::$victorious->loadStatsSportInfo($sport_id);
        
        $pools = $data['pools'];
        $teams = $data['teams'];
        $player_positions = $data['player_positions'];
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/stats_sport_info.php");
        exit;
    }
    
    public static function loadStatsData()
    {
        $sport_id = !empty($_POST['sport_id']) ? sanitize_text_field($_POST['sport_id']) : '';
        $pool_id = !empty($_POST['pool_id']) ? sanitize_text_field($_POST['pool_id']) : '';
        $team_id = !empty($_POST['team_id']) ? sanitize_text_field($_POST['team_id']) : '';
        $position_id = !empty($_POST['position_id']) ? sanitize_text_field($_POST['position_id']) : '';
        $sort_name = !empty($_POST['sort_name']) ? sanitize_text_field($_POST['sort_name']) : '';
        $sort_value = !empty($_POST['sort_value']) ? sanitize_text_field($_POST['sort_value']) : '';
        $page = !empty($_POST['page']) ? sanitize_text_field($_POST['page']) : '';

        $data = self::$victorious->loadStatsData($sport_id, $pool_id, $team_id, $position_id, $sort_name, $sort_value, $page);
        if(isset($data['new']))
        {
            require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/stats_data_new.php");
            exit;
        }
        else
        {
            $sport = $data['sport'];
            $playerstats = $data['playerstats'];
            $scoring_categories = $data['scoring_categories'];
            $total_page = $data['total_page'];
            $page = $data['page'];
            $sort_name = $data['sort_name'];
            $sort_value = $data['sort_value'];
            require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/stats_data.php");
            exit;
        }
    }
    
    public static function rugbyLoadStatsData()
    {
        $league_id = !empty($_POST['league_id']) ? sanitize_text_field($_POST['league_id']) : '';
        $keyword = !empty($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '';
        $free_agent = !empty($_POST['free_agent']) ? sanitize_text_field($_POST['free_agent']) : '';
        $scoring_category_id = !empty($_POST['scoring_category_id']) ? sanitize_text_field($_POST['scoring_category_id']) : '';
        $position_id = !empty($_POST['position_id']) ? sanitize_text_field($_POST['position_id']) : '';
        $sort_name = !empty($_POST['sort_name']) ? sanitize_text_field($_POST['sort_name']) : '';
        $sort_value = !empty($_POST['sort_value']) ? sanitize_text_field($_POST['sort_value']) : '';
        $page = !empty($_POST['page']) ? sanitize_text_field($_POST['page']) : '';

        $data = self::$victorious->rugbyLoadStatsData($league_id, $keyword, $free_agent, $scoring_category_id, $position_id, $sort_name, $sort_value, $page);
        
        $sport = $data['sport'];
        $playerstats = $data['playerstats'];
        $total_page = $data['total_page'];
        $page = $data['page'];
        $sort_name = $data['sort_name'];
        $sort_value = $data['sort_value'];
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/stats_data_rugby.php");
        exit;
    }

    public static function loadDashboardStatsAjax(){
        $stats_type = ($_POST['stats_type'] != '') ? sanitize_text_field($_POST['stats_type']) : 'total_contest';
        $stats_year = ($_POST['stats_year'] != '') ? sanitize_text_field($_POST['stats_year']) : date('Y');
        switch ($stats_type) {
            case 'total_money':
                $cond = 'YEAR(date) = '.$stats_year;
                $list_all_fundhistory = self::$payment->getAllFundhistory($cond);
                $str_chart = "['Month', 'Total Money']";

                if(count($list_all_fundhistory) > 0){
                    foreach ($list_all_fundhistory as $key => $value) {
                        $str_chart .= ",['".$key."', ".$value."]";
                    }
                }else{
                    $str_chart .= ",['January', 0],['February', 0],['March', 0],['April', 0],['May', 0],['June', 0],['July', 0],['August', 0],['September', 0],['October', 0],['November', 0],['December', 0]"; 
                }
                break;

            case 'total_users_played':
                $total_users_played  = self::$sports->getUserPlayed($stats_year);
                
                $str_chart = "['Month', 'Total Users Played']";

                if(count($total_users_played) > 0){
                    foreach ($total_users_played as $key => $value) {
                        $str_chart .= ",['".$key."', ".$value."]";
                    }
                }else{
                    $str_chart .= ",['January', 0],['February', 0],['March', 0],['April', 0],['May', 0],['June', 0],['July', 0],['August', 0],['September', 0],['October', 0],['November', 0],['December', 0]"; 
                }
                break;
            
             case 'total_contest':
                $total_contest  = self::$leagues->getLeagueByYear($stats_year);
                $str_chart = "['Month', 'Total Contests']";
                if(count($total_contest) > 0){
                    foreach ($total_contest as $key => $value) {
                        $str_chart .= ",['".$key."', ".$value."]";
                    }
                }else{
                    $str_chart .= ",['January', 0],['February', 0],['March', 0],['April', 0],['May', 0],['June', 0],['July', 0],['August', 0],['September', 0],['October', 0],['November', 0],['December', 0]"; 
                }                
                break;
            default:
                break;
        }
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW . 'Elements/dashboard_stats_chart.php');
        exit;
    }

    public static function loadDateType(){
        $date_type  = sanitize_text_field($_POST['date_type']);
        $leagueID   = sanitize_text_field($_POST['leagueID']);

        $date_type = self::$victorious->getListDateType($leagueID, $date_type);
        $str = '<option value="">Please select...</option>';
        foreach ($date_type['date_type'] as $key => $value) {
            if(isset($value['week']))
                $str.= '<option value="'.$value['week'].'">'.$value['week'].'</option>';
            if(isset($value['month']))
                $str.= '<option value="'.$value['month'].'">'.$value['month'].'</option>';
        }
        echo esc_html($str);
        exit;
    }

    public static function liveEntriesResultWithDateTypeAjax(){
        $leagueID = sanitize_text_field($_POST['leagueID']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $date_type = sanitize_text_field($_POST['date_type']);
        $date_type_number = sanitize_text_field($_POST['date_type_number']);

        $aDatas = self::$victorious->getLeagueDetail($leagueID);
        $aLeague = $aDatas['league'];
        $aPool = $aDatas['pool'];
        //allow show popup
        $showInviteFriends = false;
        if(isset($_SESSION['showInviteFriends'.$leagueID]) && $aPool['status'] == "NEW")
        {
            unset($_SESSION['showInviteFriends'.$leagueID]);
            $showInviteFriends = true;
        }

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW . 'Elements/rankings_picktie.php');
        exit;
    }
    
    public static function cancelContest()
    {
        $result = self::$pools->cancelContest($_POST);
        switch($result)
        {
            case 2:
                exit(json_encode(array('notice' => __('Contest does not exist', 'victorious'))));
                break;
            case 1:
                exit(json_encode(array('result' => __('Contest has been cancelled', 'victorious'))));
                break;;
            default :
                exit(json_encode(array('notice' => __('Something went wrong! Please try again', 'victorious'))));
        }
    }
    
    public static function testConnection()
    {
        $checkAPIToken = self::$victorious->checkAPITokenAdmin();
        if(get_option('victorious_api_token') == null)
        {
            $checkAPIToken = 505;
        }
        if ($checkAPIToken != 1) 
        {
            exit(__('Connection failed, please check api key.', 'victorious'));
        }
        exit(__('Connected', 'victorious'));
    }
    
    public static function dlgSetFeatureContest()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $is_feature = sanitize_text_field($_POST['is_feature']);
        $league = self::$victorious->getLeagueDetail($league_id);

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW."contests/dlg_feature_contest.php");
        exit;
    }
    
    public static function qqUploadFile()
    {
        if (!function_exists('wp_handle_upload')) 
        {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }
        $uploadedfile = $_FILES['qqfile'];
        $upload_overrides = array( 'test_form' => false );
        $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
        $filepath = "";
        if($movefile)
        {
            $filepath = str_replace(VICTORIOUS_IMAGE_URL, '', $movefile['url']);
        }
        
        $source_file = VICTORIOUS_IMAGE_DIR.$filepath;
        if(!isset($_GET['resize']) || $_GET['resize'] == 1){
            $filepath = image_resize($source_file, VICTORIOUS_FEATURE_IMAGE_WIDTH, VICTORIOUS_FEATURE_IMAGE_HEIGHT, true, null, null, 100);
        }
        else{
            $filepath = image_resize($source_file, VICTORIOUS_NORMAL_IMAGE_WIDTH, VICTORIOUS_NORMAL_IMAGE_HEIGHT, true, null, null, 100);
        }
        $filepath = str_replace(VICTORIOUS_IMAGE_DIR, '', $filepath);
        unlink($source_file);
        echo json_encode(array(
            'success' => true,
            'filepath' => $filepath,
            'url' => $movefile['url']
        ));
        exit;
    }
    
    public static function doSetFeatureContest()
    {
        $is_feature = !empty($_POST['is_feature']) ? sanitize_text_field($_POST['is_feature']) : 0;
        $id = !empty($_POST['id']) ? sanitize_text_field($_POST['id']) : 0;
        $feature_image = !empty($_POST['feature_image']) ? sanitize_text_field($_POST['feature_image']) : 0;
        $result = self::$leagues->setFeatureContest($id, $is_feature, $feature_image);
        if($result)
        {
            echo json_encode(array(
                'success' => 1,
                'message' => __('Successfully set feature.', 'victorious'),
                'is_feature' => $is_feature,
                'id' => $id
            ));
            exit;
        }
        echo json_encode(array(
            'success' => 0,
            'message' => __('Cannot set feature, please try again.', 'victorious'),
            'is_feature' => $is_feature,
            'id' => $id
        ));
        exit;
    }
    
    public static function showInviteFriendDlg()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $link_contest = VICTORIOUS_URL_SUBMIT_PICKS;
        if(!empty($aLeague)){
            if($aLeague['gameType'] == 'PICKSQUARES'){
                $link_contest = VICTORIOUS_URL_PICK_SQUARES;
            }
        }
        //friend
        $aFriends = self::$victorious->getAllPlayerInfo();
        sort($aFriends, SORT_ASC);
        usort($aFriends, function($a, $b){
            $a = strtolower($a['full_name'] ? $a['full_name'] : $a['user_name']);
            $b = strtolower($b['full_name'] ? $b['full_name'] : $b['user_name']);
            return strcmp($a, $b);
        });
        $quote_list_data = self::$victorious->getListPickemTeamByLeagueID($league_id,$entry_number);
        $quote_list_data = $quote_list_data['result'];
        
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW.'dlg_info_friends.php');
        exit;
    }
    
    /////////////////////////////////////live score/////////////////////////////////////
    public static function liveScoreLatestDailyEvents()
    {
        $sport_id = !empty($_GET['sport_id']) ? sanitize_text_field($_GET['sport_id']) : "";
        $events = self::$victorious->getLatestDailyEvents($sport_id);
        
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW.'Elements/livescore/latest_events.php');
        exit;
    }
    
    public static function liveScoreFixtureScores()
    {
        $event_id = !empty($_GET['event_id']) ? sanitize_text_field($_GET['event_id']) : "";
        $fixtures = self::$victorious->getFixtureScores($event_id);

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW.'Elements/livescore/fixture_scores.php');
        exit;
    }
    
    public static function liveScoreTeamRoster()
    {
        $team_id = !empty($_GET['team_id']) ? sanitize_text_field($_GET['team_id']) : "";
        $sort_by = !empty($_GET['sort_by']) ? sanitize_text_field($_GET['sort_by']) : "";
        $sort_type = !empty($_GET['sort_type']) ? sanitize_text_field($_GET['sort_type']) : "";
        
        $data = self::$victorious->liveScoreTeamRoster($team_id, $sort_by, $sort_type);
        $players = $data['players'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW.'Elements/livescore/roster.php');
        exit;
    }

    public static function liveScoreTeamSchedule()
    {
        $team_id = !empty($_GET['team_id']) ? sanitize_text_field($_GET['team_id']) : "";
        
        $data = self::$victorious->liveScoreTeamSchedule($team_id, true);
        $team = $data['team'];
        $schedule = $data['schedule'];
        
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW.'Elements/livescore/schedule.php');
        exit;
    }
    
    public static function liveScoreTeamNews()
    {
        $team_id = !empty($_POST['team_id']) ? sanitize_text_field($_POST['team_id']) : "";
        $team = self::$victorious->getTeamDetail($team_id);
        
        $news = array();
        if($team != null)
        {
            $team = $team['team'];
            require_once(VICTORIOUS__PLUGIN_DIR_MODEL.'simple_html_dom.php');
            header('Content-Type: text/html; charset=ISO-8859-15');
            $site_url = 'https://www.google.com';

            //check url
            $keyword = str_replace(' ', '%20', $team['name']);
            $locale = "en";
            $url = $site_url.'/search?q='.$keyword.'&lr=lang_'.$locale.'&tbm=nws&*';
            if(!empty($_POST['link']))
            {
                $url = sanitize_text_field($_POST['link']);
            }

            //load data
            $resp = wp_remote_get($url);
            $resp = wp_remote_retrieve_body($resp);

            //parse html
            $html = str_get_html($resp);

            //check has result
            if(!$html->find('#search', 0) || trim($html->find('#search', 0)->plaintext) == '')
            {
                echo esc_html(__("No news", "victorious"));
                exit;
            }

            $news = array();
            foreach($html->find('#search .g') as $item)
            {
                $link = $item->find('h3.r a', 0)->href;
                $link = str_replace('/url?q=', '', $link);
                $link = substr($link, 0, strpos($link, "&amp;sa="));
                $news[] = array(
                    'link' => $link,
                    'title' => $item->find('h3.r a', 0)->innertext,
                    'brief' => $item->find('.st', 0)->innertext,
                    'image' => $item->find('.th', 0)->src
                );
            }

            //has result
            $paging = '<div class="paginationContainer"><div class="ng-table-pager"><ul id="player_news_paging">';
            foreach($html->find('#nav tbody td') as $link)
            {
                if($link->find('a', 0))
                {
                    $paging .= '<li><a href="'.$site_url.$link->find('a', 0)->href.'">'.$link->plaintext.'</a></li>';
                }
                else
                {
                    $paging .= '<li class="disabled"><a href="javascript:void(0)"><span>'.$link->plaintext.'</span></a></li>';
                }
            }
            $paging .= '</div></div></ul>';
        }
        
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW.'Elements/livescore/news.php');
        exit;
    }
    
    public static function liveScoreTeamStatistic()
    {
        $team_id = !empty($_GET['team_id']) ? sanitize_text_field($_GET['team_id']) : "";
        $position_id = !empty($_GET['position_id']) ? sanitize_text_field($_GET['position_id']) : "";
        $keyword = !empty($_GET['keyword']) ? sanitize_text_field($_GET['keyword']) : "";
        $page = !empty($_GET['page']) ? sanitize_text_field($_GET['page']) : 1;
        $sort_by = !empty($_GET['sort_by']) ? sanitize_text_field($_GET['sort_by']) : "";
        $sort_type = !empty($_GET['sort_type']) ? sanitize_text_field($_GET['sort_type']) : "";
        $sort_scoring_id = !empty($_GET['sort_scoring_id']) ? sanitize_text_field($_GET['sort_scoring_id']) : "";
        
        $data = self::$victorious->liveScoreTeamStatistic(array(
            "team_id" => $team_id, 
            "position_id" => $position_id, 
            "keyword" => $keyword, 
            "sort_by" => $sort_by, 
            "sort_type" => $sort_type, 
            "page" => $page,
            "sort_scoring_id" => $sort_scoring_id
        ));
        
        if(isset($data['new']))
        {
            require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/livescore/statistics_new.php");
            exit;
        }
        else 
        {
            $players = $data['players'];
            $teams = $data['teams'];
            $player_positions = $data['player_positions'];
            $scoring_categories = $data['scoring_categories'];
            $total_pages = $data['total_pages'];

            require_once(VICTORIOUS__PLUGIN_DIR_VIEW.'Elements/livescore/statistics.php');
            exit;
        }
    }
    
    public static function liveScoreTeamInjuries()
    {
        $team_id = !empty($_GET['team_id']) ? sanitize_text_field($_GET['team_id']) : "";
        
        $data = self::$victorious->liveScoreTeamInjuries($team_id);
        $players = $data['players'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW.'Elements/livescore/injuries.php');
        exit;
    }
    /////////////////////////////////////end live score/////////////////////////////////////
    
    /////////////////////////////////////firebase push notification/////////////////////////////////////
    public static function subscribePushNotification()
    {
        $user_id = VIC_GetUserId();
        self::$victorious->subscribePushNotification($user_id, sanitize_text_field($_POST['token']));
        exit;
    }
    
    public static function unSubcribePushNotification()
    {
        $user_id = VIC_GetUserId();
        self::$victorious->unSubscribePushNotification($user_id);
        exit;
    }
    /////////////////////////////////////end firebase push notification/////////////////////////////////////
    
    /////////////////////////////////////bracket/////////////////////////////////////
    public static function submitPickBracket()
    {
        $user_id = VIC_GetUserId();
        $league_id = !empty($_POST['league_id']) ? sanitize_text_field($_POST['league_id']) : "";
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 1;
        $team_ids = !empty($_POST['team_ids']) ? sanitize_text_field($_POST['team_ids']) : "";
        
        //load league
        $league = self::$victorious->getLeagueDetail($league_id);
        if($league == null)
        {
            exit(json_encode(array('success' => false, 'message' => __('Contest does not exist', 'victorious'))));
        }
        $league = $league[0];
        
        //check enough funds
        $makeBet = true;
        if($league['entry_fee'] > 0)
        {
            $makeBet = self::$payment->isMakeBetForLeague($league_id, $entry_number);
            if (!$makeBet && !self::$payment->isUserEnoughMoneyToJoin($league['entry_fee'], $league_id, $entry_number, $league['multi_entry'])) 
            {
                exit(json_encode(array('success' => false, 'message' => __('You do not have enough funds to enter. Please add funds', 'victorious'))));
            }
        }

        //submit data
        $result = self::$victorious->submitPickBracket($league_id, $entry_number, $team_ids);
        if($result['success'] == false)
        {
            switch ($result['code']) {
                
                case 1:
                    $message = __('Contest does not exist', 'victorious');
                    break;
                case 2:
                    $message = __('This contest has ended', 'victorious');
                    break;
                case 3:
                    $message = __('Sorry! This contest is full', 'victorious');
                    break;
                case 4:
                    $message = __('You must select two teams for each group','victorious');
                    break;
                case 5:
                    $message = __('You reached maximum number of multi entries.', 'victorious');
                    break;
                case 6:
                    $message = __('You cannot pick for started contest.', 'victorious');
                    break;
                default :
                    $message = __('Unknown error', 'victorious');
            }
            exit(json_encode(array('success' => 0, 'message' => $message)));
        }

        //make bet and deduct balance
        if ($makeBet == false) 
        {
            //decrease user money
            self::$payment->updateUserBalance($league['entry_fee'], true, $league_id);

            //add to history
            $user = self::$payment->getUserData();
            $params = array(
                'amount' => $league['entry_fee'],
                'leagueID' => $league_id,
                'new_balance' => $user['balance'],
                'operation' => 'DEDUCT',
                'type' => 'MAKE_BET',
                'entry_number' => $entry_number,
                'status' => 'completed'
            );
            self::$payment->addFundhistory($params);
        }
        
        //send pick email
        self::$victorious->sendUserPickEmail($league_id, VIC_GetUserId(), $entry_number);
            
        //buddy press integration
        if($league != null)
        {
            self::$victorious->addEnterContestActivity($league, VIC_GetUserId());
        }
            
        exit(json_encode(array('success' => 1, 'redirect' => VICTORIOUS_URL_ENTRY.$league_id. "/?num=" . $entry_number)));
    }
    
    public static function bracketLoadResult()
    {
        $user_id = VIC_GetUserId();
        $league_id = !empty($_POST['league_id']) ? sanitize_text_field($_POST['league_id']) : "";
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 1;
        $current_page = !empty($_POST['page']) ? sanitize_text_field($_POST['page']) : 1;
        
        //load league
        $league = self::$victorious->getLeagueDetail($league_id);
        $league = $league[0];
        
        //scores
        $data = self::$victorious->bracketResult($league_id, $current_page);
        $scores = $data['scores'];
        $total_page = $data['total_page'];
        
        //cur user scores
        $currUserScore = null;
        if($scores != null)
        {
            foreach($scores as $k => $aScore)
            {
                $aScore[$k]['current'] = false;
                if($aScore['userID'] == $user_id && $aScore['entry_number'] == sanitize_text_field($_POST['entry_number']))
                {
                    $scores[$k]['current'] = true;
                }
            }
        }
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."bracket/result.php");
        exit;
    }
    
    public static function bracketLoadResultDetail()
    {
        $user_id = !empty($_POST['user_id']) ? sanitize_text_field($_POST['user_id']) : "";
        $league_id = !empty($_POST['league_id']) ? sanitize_text_field($_POST['league_id']) : "";
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 1;
        $user = get_user_by('ID', $user_id);
        
        //get data
        $data = self::$victorious->bracketResultDetail($league_id, $user_id, $entry_number);
        $team_groups = $data['team_groups'];
        $picks = $data['picks'];
        $fixture16 = $data['fixture16'];
        $fixture8 = $data['fixture8'];
        $fixture4 = $data['fixture4'];
        $fixture2 = $data['fixture2'];
        $fixture1st = $data['fixture1st'];
        $fixture3rd = $data['fixture3rd'];
        list($group_left, $group_right) = self::$victorious->bracketGroupTeam($team_groups);
        list($group_left16, $group_right16) = self::$victorious->bracketGroupTeam($fixture16, true);
        list($group_left8, $group_right8) = self::$victorious->bracketGroupTeam($fixture8, true);
        list($group_left4, $group_right4) = self::$victorious->bracketGroupTeam($fixture4);
        
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."bracket/result_detail.php");
        exit;
    }
    /////////////////////////////////////bracket/////////////////////////////////////
    
    /////////////////////////////////////roundpickem/////////////////////////////////////
    public static function submitRoundPickem()
    {
        $league_id = !empty($_POST['leagueID']) ? sanitize_text_field($_POST['leagueID']) : "";
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 1;
        $winners = !empty($_POST['winners']) ? sanitize_text_field($_POST['winners']) : "";
        $predict_points = !empty($_POST['predict_points']) ? sanitize_text_field($_POST['predict_points']) : "";
        
        //league
        $league = self::$victorious->getLeagueDetail($league_id);
        
        //validate
        self::validateForAllGameType($league, $entry_number);
        $league = $league[0];
        
        //submit data
        $data = self::$victorious->submitUserPickRoundPickem($league_id, $entry_number, $winners, $predict_points);
        if(!empty($data['success']))
        {
            self::doAfterEnterContest($league, $entry_number);
            
            exit(json_encode(array('success' => 1, 'redirect' => VICTORIOUS_URL_CONTEST.$league_id.'/?num='.$entry_number.'&invite=1&pick=1')));
        }
        VIC_SetMessage(__('Cannot save data! Please try again.', 'victorious'));
        exit(json_encode(array('success' => 0, 'redirect' => VICTORIOUS_URL_GAME.$league_id.'/?num='.$entry_number)));
    }
    
    public static function roundPickemLoadResult()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $week = sanitize_text_field($_POST['week']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $current_page = sanitize_text_field($_POST['page']);
        
        $result = self::$victorious->getRoundPickemResult($league_id, $week, $current_page);
        $standing = $result['standing'];
        $total_page = $result['total_page'];
        $predict_match = $result['predict_match'];
        $total_predict_match_score = $result['total_predict_match_score'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."roundpickem/result.php");
        exit;
    }
    
    public static function roundPickemLoadResultDetail()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $week = sanitize_text_field($_POST['week']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $opponent_id = sanitize_text_field($_POST['opponent_id']);
        $opponent_entry_number = sanitize_text_field($_POST['opponent_entry_number']);
        
        $result = self::$victorious->getRoundPickemResultDetail($league_id, $week, $user_id, $entry_number, $opponent_id, $opponent_entry_number);
        $fights = $result['fights'];
        $total_my_points = $result['total_my_points'];
        $total_opponent_points = $result['total_opponent_points'];
		$bonus_my_points = $result['bonus_my_points'];
        $bonus_opponent_points = $result['bonus_opponent_points'];
        $current_user = self::$payment->getUserData();
        $opponent_user = self::$payment->getUserData($opponent_id);
        
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."roundpickem/result_detail.php");
        exit;
    }
    /////////////////////////////////////end roundpickem/////////////////////////////////////
    
    /////////////////////////////////////pickem/////////////////////////////////////
    public static function submitPickem()
    {
        $league_id = !empty($_POST['leagueID']) ? sanitize_text_field($_POST['leagueID']) : '';
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 1;
        $pool_id = !empty($_POST['poolID']) ? sanitize_text_field($_POST['poolID']) : '';
        
        //league
        $league = self::$victorious->getLeagueDetail($league_id);
        
        //validate
        self::validateForAllGameType($league, $entry_number);
        $league = $league[0];
        
        //submit data
        $params = self::$victorious->postUserPicksAllowFields($_POST);
        $data = self::$victorious->postUserPicks($pool_id, $league_id, $entry_number, $params);
        if(!empty($data['success']))
        {
            self::doAfterEnterContest($league, $entry_number);
            
            exit(json_encode(array('success' => 1, 'redirect' => VICTORIOUS_URL_CONTEST.$league_id.'/?num='.$entry_number.'&invite=1&pick=1')));
        }
        VIC_SetMessage(__('Cannot save data! Please try again.', 'victorious'));
        exit(json_encode(array('success' => 0, 'redirect' => VICTORIOUS_URL_GAME.$league_id.'/?num='.$entry_number)));
    }
    
    public static function pickemLoadResult()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $current_page = sanitize_text_field($_POST['page']);
        
        $result = self::$victorious->getPickemResult($league_id, $current_page);
        $league = $result['league'];
        $standing = $result['standing'];
        $total_page = $result['total_page'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."pickem/leaderboard.php");
        exit;
    }
    
    public static function pickemLoadResultDetail()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $opponent_id = sanitize_text_field($_POST['opponent_id']);
        $opponent_entry_number = sanitize_text_field($_POST['opponent_entry_number']);
        $week = !empty($_POST['week']) ? sanitize_text_field($_POST['week']) : '';

        //$my_result = self::$victorious->getPickemResultDetail($league_id, $week, $user_id, $entry_number, $opponent_id, $opponent_entry_number);
        $result = self::$victorious->getPickemResultDetail($league_id, $week, $user_id, $opponent_entry_number, $opponent_id, $opponent_entry_number);
        $league = $result['league'];
        $fights = $result['fights'];
        $tie_breaker = $result['tie_breaker'];

        $my_score_detail = $result['my_score'];
        $score_detail = $result['score'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."pickem/leaderboard_detail.php");
        exit;
    }
    /////////////////////////////////////end pickem/////////////////////////////////////
    
    /////////////////////////////////////bothteamstoscore/////////////////////////////////////
    public static function bothteamstoscoreLoadResult()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $current_page = sanitize_text_field($_POST['page']);
        
        $result = self::$victorious->getBothTeamsToScoreResult($league_id, $current_page);
        $league = $result['league'];
        $standing = $result['standing'];
        $total_page = $result['total_page'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."bothteamstoscore/result.php");
        exit;
    }
    
    public static function bothteamstoscoreLoadResultDetail()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $opponent_id = sanitize_text_field($_POST['opponent_id']);
        $opponent_entry_number = sanitize_text_field($_POST['opponent_entry_number']);
        
        $result = self::$victorious->getBothTeamsToScoreResultDetail($league_id, $week, $user_id, $entry_number, $opponent_id, $opponent_entry_number);
        $league = $result['league'];
        $fights = $result['fights'];
        $total_my_points = $result['total_my_points'];
        $total_opponent_points = $result['total_opponent_points'];
        $tie_breaker = $result['tie_breaker'];
        $current_user = self::$payment->getUserData();
        $opponent_user = self::$payment->getUserData($opponent_id);
        
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."bothteamstoscore/result_detail.php");
        exit;
    }
    /////////////////////////////////////end bothteamstoscore/////////////////////////////////////
    
    /////////////////////////////////////playerdraft/////////////////////////////////////
    public static function getDraftPlayerList(){
        $data = self::$playerdraft->getDraftPlayerList(
            sanitize_text_field($_POST['league_id']),
            sanitize_text_field($_POST['position_id']),
            sanitize_text_field($_POST['fight_id']),
            sanitize_text_field($_POST['player_id']),
            sanitize_text_field($_POST['sort_type']),
            sanitize_text_field($_POST['sort']),
            sanitize_text_field($_POST['keyword']),
            sanitize_text_field($_POST['page'])
        );
        $league = $data['league'];
        $players = $data['players'];
        $total_page = $data['total_page'];
        $current_page = sanitize_text_field($_POST['page']);

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."playerdraft/draft_player_list.php");
        exit;
    }

    public static function submitPlayerdraft()
    {
        $league_id = sanitize_text_field($_POST['leagueID']);
        $entry_number = isset($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 0;
        $action = 0;
        if (!empty($_POST['action']))
        {
            $action = sanitize_text_field($_POST['action']);
        }

        //get league data
        $league = self::$victorious->getLeagueDetail($league_id);

        //validate
        self::validateForAllGameType($league, $entry_number);
        self::validateSubmitPlayerdraft();
        $league = $league[0];

        //insert pick
        $data = array('leagueID' => sanitize_text_field($_POST['leagueID']),
            'player_id' => sanitize_text_field($_POST['player_id']),
            'entry_number' => $entry_number,
            'player_position' => sanitize_text_field($_POST['player_position']),
            'injury' => isset($_POST['injury']) ? sanitize_text_field($_POST['injury']) : "",
            'action' => $action);
        $data = self::$victorious->insertPlayerPicks($data);
        if($data == 1)
        {
            self::doAfterEnterContest($league, $entry_number);

            //push notification for user who joined contest
			$victorious_firebase_apikey = get_option('victorious_firebase_apikey');
			$victorious_firebase_senderid = get_option('victorious_firebase_senderid');	
            if (!empty($victorious_firebase_apikey) && !empty($victorious_firebase_senderid))
            {
                $user_ids = self::$victorious->getUserIdsJoinContest($league_id, VIC_GetUserId());
                if (!empty($user_ids['user_ids']))
                {
                    self::$victorious->sendNotificationUserJoinContest($user_ids['user_ids'], VIC_GetUserId(), $league['name'], VICTORIOUS_URL_CONTEST . $league['leagueID'] . "?num=" . $entry_number);
                }
            }
            exit(json_encode(array('success' => 1, 'redirect' => VICTORIOUS_URL_ENTRY.$league_id.'/?num='.$entry_number.'&invite=1&pick=1')));
        }
        VIC_SetMessage(__('Cannot save data! Please try again.', 'victorious'));
        exit(json_encode(array('success' => 0, 'redirect' => VICTORIOUS_URL_GAME.$league_id.'/?num='.$entry_number.'&invite=1&pick=1')));
    }
    
    private static function validateSubmitPlayerdraft()
    {
        $league_id = sanitize_text_field($_POST['leagueID']);
        $player_id = isset($_POST['player_id']) ? sanitize_text_field($_POST['player_id']) : 0;
        $valid = self::$victorious->validEnterPlayerdraft($league_id, $player_id);
        switch ($valid)
        {
            case 2:
                $message = __("This contest has ended", 'victorious');
                break;
            case 3:
                $message = __('Contest does not exist', 'victorious');
                break;
            case 4:
                $message = __('Sorry! This contest is full', 'victorious');
                break;
            case 5:
                $message = __("Your team has exceeded this game's salary cap. Please change your team so it fits under the salary cap before entering", 'victorious');
                break;
            case 6:
                $message = __("Please select a player for each position", 'victorious');
                break;
            case 7:
                $message = __("You can not pick players of started game", 'victorious');
                break;
            case 8:
                $message = __("You are only able to change one player", 'victorious');
                break;
            case 10:
                $message = __("One of these players already picked by other users. Please select other players.", 'victorious');
                break;
            case 12:
                $message = __('Only users who entered 50 contests or less can join this contest.', 'victorious');
                break;
            case 13:
                $message = __('Your picks has exceeded player limitation per team.', 'victorious');
                break;
            case 14:
                $message = __('You reached maximum number of multi entries.', 'victorious');
                break;
            default :
                $message = "";
        }
        if($message != "")
        {
            exit(json_encode(array('success' => 0, 'message' => $message)));
        }
    }

    /////////////////////////////////////end playerdraft/////////////////////////////////////
    
    /////////////////////////////////////picksquares/////////////////////////////////////
    public static function submitPickSquares()
    {
        $league_id = !empty($_POST['leagueID']) ? sanitize_text_field($_POST['leagueID']) : '';
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 1;
        $pool_id = !empty($_POST['poolID']) ? sanitize_text_field($_POST['poolID']) : '';
        
        //league
        $league = self::$victorious->getLeagueDetail($league_id);
        
        //validate
        self::validateForAllGameType($league, $entry_number);
        $league = $league[0];
       
        //submit data
        $params = self::$victorious->postUserPicksAllowFields($_POST);
        $data = self::$victorious->postUserPicks($pool_id, $league_id, $entry_number, $params);
        if(!empty($data['success']))
        {
            self::doAfterEnterContest($league, $entry_number);
            
            exit(json_encode(array('success' => 1, 'redirect' => VICTORIOUS_URL_CONTEST.$league_id.'/?num='.$entry_number.'&invite=1&pick=1')));
        }
        VIC_SetMessage(__('Cannot save data! Please try again.', 'victorious'));
        exit(json_encode(array('success' => 0, 'redirect' => VICTORIOUS_URL_GAME.$league_id.'/?num='.$entry_number)));
    }
    
    public static function pickSquaresLoadResult()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $current_page = sanitize_text_field($_POST['page']);
        
        $result = self::$victorious->getPickSquaresResult($league_id, $current_page);
        $league = $result['league'];
        $standing = $result['standing'];
        $total_page = $result['total_page'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."picksquares/result.php");
        exit;
    }
    
    public static function pickSquaresLoadResultDetail()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $opponent_id = sanitize_text_field($_POST['opponent_id']);
        $opponent_entry_number = sanitize_text_field($_POST['opponent_entry_number']);
        
        $result = self::$victorious->getPickSquaresResultDetail($league_id, $week, $user_id, $entry_number, $opponent_id, $opponent_entry_number);
        $league = $result['league'];
        $fights = $result['fights'];
        $total_my_points = $result['total_my_points'];
        $total_opponent_points = $result['total_opponent_points'];
        $tie_breaker = $result['tie_breaker'];
        $current_user = self::$payment->getUserData();
        $opponent_user = self::$payment->getUserData($opponent_id);
        
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."picksquares/result_detail.php");
        exit;
    }
    /////////////////////////////////////end picksquares/////////////////////////////////////
    
    /////////////////////////////////////pickultimate/////////////////////////////////////
    public static function submitPickUltimate()
    {
        $league_id = !empty($_POST['leagueID']) ? sanitize_text_field($_POST['leagueID']) : '';
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 1;
        $pool_id = !empty($_POST['poolID']) ? sanitize_text_field($_POST['poolID']) : '';
        
        //league
        $league = self::$victorious->getLeagueDetail($league_id);
        
        //validate
        self::validateForAllGameType($league, $entry_number);
        $league = $league[0];
        
        //submit data
        $params = self::$victorious->postUserPicksAllowFields($_POST);
        $data = self::$victorious->postUserPicks($pool_id, $league_id, $entry_number, $params);
        if(!empty($data['success']))
        {
            self::doAfterEnterContest($league, $entry_number);
            
            exit(json_encode(array('success' => 1, 'redirect' => VICTORIOUS_URL_CONTEST.$league_id.'/?num='.$entry_number.'&invite=1&pick=1')));
        }
        VIC_SetMessage(__('Cannot save data! Please try again.', 'victorious'));
        exit(json_encode(array('success' => 0, 'redirect' => VICTORIOUS_URL_GAME.$league_id.'/?num='.$entry_number)));
    }

    public static function pickUltimateLoadResult()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $current_page = sanitize_text_field($_POST['page']);

        $result = self::$victorious->getPickUltimateResult($league_id, $current_page);
        $league = $result['league'];
        $standing = $result['standing'];
        $total_page = $result['total_page'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."pickultimate/leaderboard.php");
        exit;
    }

    public static function pickUltimateLoadResultDetail()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $opponent_id = sanitize_text_field($_POST['opponent_id']);
        $opponent_entry_number = sanitize_text_field($_POST['opponent_entry_number']);
        $week = !empty($_POST['week']) ? sanitize_text_field($_POST['week']) : '';

        $result = self::$victorious->getPickUltimateResultDetail($league_id, $user_id, $entry_number, $opponent_id, $opponent_entry_number);
        $league = $result['league'];
        $fights = $result['fights'];

        $my_score = $result['my_score'];
        $opponent_score = $result['opponent_score'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."pickultimate/leaderboard_detail.php");
        exit;
    }
    /////////////////////////////////////end pickultimate/////////////////////////////////////
    
    /////////////////////////////////////goliath/////////////////////////////////////
    private static function validateSubmitGoliath()
    {
        $league_id = !empty($_POST['leagueID']) ? sanitize_text_field($_POST['leagueID']) : '';
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : '';
        $winners = !empty($_POST['winners']) ? sanitize_text_field($_POST['winners']) : '';

        $data = self::$victorious->validateSubmitGoliath($league_id, $entry_number, $winners);
        if(!$data['success'])
        {
            switch ($data['code']) 
            {
                case 1:
                    $message = __('Contest was ended', 'victorious');
                    break;
                case 2:
                    $message = __('You need to pick at least a winner or a pass', 'victorious');
                    break;
                case 3:
                    $message = __('You reach maximum pass', 'victorious');
                    break;
                case 4:
                    $message = __('Sorry! You were kicked', 'victorious');
                    break;
                case 5:
                    $message = __('Sorry! You cannot re-up at this time', 'victorious');
                    break;
                default :
                    $message = __('Unknown error', 'victorious');
            }
            exit(json_encode(array('success' => 0, 'message' => $message)));
        }
    }
    
    public static function submitGoliath()
    {
        $league_id = !empty($_POST['leagueID']) ? sanitize_text_field($_POST['leagueID']) : '';
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : '';
        $winners = !empty($_POST['winners']) ? sanitize_text_field($_POST['winners']) : '';
        $invitedby = !empty($_POST['invitedby']) ? sanitize_text_field($_POST['invitedby']) : '';
        
        //league
        $league = self::$victorious->getLeagueDetail($league_id);
        
        //validate
        self::validateForAllGameType($league, $entry_number);
        self::validateSubmitGoliath();
        $league = $league[0];
        
        //submit data
        $data = self::$victorious->submitGoliath($league_id, $entry_number, $winners, $invitedby);
        if(!empty($data['success']))
        {
            self::doAfterEnterContest($league, $entry_number, $data['can_reup_free']);
            
            exit(json_encode(array('success' => 1, 'redirect' => VICTORIOUS_URL_CONTEST.$league_id.'/?num='.$entry_number.'&invite=1&pick=1')));
        }
        VIC_SetMessage(__('Cannot save data! Please try again.', 'victorious'));
        exit(json_encode(array('success' => 0, 'redirect' => VICTORIOUS_URL_GAME.$league_id.'/?num='.$entry_number)));
    }
    
    public static function goliathLoadResult()
    {
        $user_id = VIC_GetUserId();
        $league_id = !empty($_POST['league_id']) ? sanitize_text_field($_POST['league_id']) : "";
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 1;
        $standing_type = !empty($_POST['standing_type']) ? sanitize_text_field($_POST['standing_type']) : 1;
        $current_page = !empty($_POST['page']) ? sanitize_text_field($_POST['page']) : 1;
        
        //load league
        $league = self::$victorious->getLeagueDetail($league_id);
        $league = $league[0];
        
        //scores
        $data = self::$victorious->goliathResult($league_id, $standing_type, $current_page);
        $scores = $data['scores'];
        $fights = $data['fights'];
        $total_page = $data['total_page'];
        
        //cur user scores
        if($scores != null)
        {
            foreach($scores as $k => $score)
            {
                $score[$k]['current'] = false;
                if($score['userID'] == $user_id && $score['entry_number'] == sanitize_text_field($_POST['entry_number']))
                {
                    $scores[$k]['current'] = true;
                }
            }
        }
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."goliath/result.php");
        exit;
    }
    
    public static function goliathLoadResultDetail()
    {
        $user_id = !empty($_POST['user_id']) ? sanitize_text_field($_POST['user_id']) : "";
        $league_id = !empty($_POST['league_id']) ? sanitize_text_field($_POST['league_id']) : "";
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 1;
        $opponent_id = sanitize_text_field($_POST['opponent_id']);
        $opponent_entry_number = sanitize_text_field($_POST['opponent_entry_number']);
        
        //get data
        $data = self::$victorious->goliathResultDetail($league_id, $user_id, $entry_number, $opponent_id, $opponent_entry_number);
        $fights = $data['fights'];
        $total_my_points = $data['total_my_points'];
        $total_opponent_points = $data['total_opponent_points'];
        $current_user = self::$payment->getUserData();
        $opponent_user = self::$payment->getUserData($opponent_id);
        
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."goliath/result_detail.php");
        exit;
    }
    
    public static function goliathLoadContestStats()
    {
        $league_id = !empty($_POST['league_id']) ? sanitize_text_field($_POST['league_id']) : "";
        $week = !empty($_POST['week']) ? sanitize_text_field($_POST['week']) : "";
        $fight_id = !empty($_POST['fight_id']) ? sanitize_text_field($_POST['fight_id']) : "";
        
        //get data
        $data = self::$victorious->goliathContestStats($league_id, $week, $fight_id);
        $fight = $data['fight'];
        $stats = $data['stats'];
        $week_stats = $data['week_stats'];
        
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."goliath/contest_stats.php");
        exit;
    }
    
    public static function goliathMakeDecision()
    {
        $data = self::$victorious->goliathMakeDecision(sanitize_text_field($_POST['type']));
        if(!$data['success'])
        {
            exit(json_encode(array('success' => 0, 'message' => __('Something went wrong! Please try again', 'victorious'))));
        }
        exit(json_encode(array('success' => 1, 'message' => __('Successfully updated', 'victorious'))));
    }

    /////////////////////////////////////end goliath/////////////////////////////////////
    
    /////////////////////////////////////minigoliath/////////////////////////////////////
    private static function validateSubmitMiniGoliath()
    {
        $league_id = !empty($_POST['leagueID']) ? sanitize_text_field($_POST['leagueID']) : '';
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : '';
        $winners = !empty($_POST['winners']) ? sanitize_text_field($_POST['winners']) : '';

        $data = self::$victorious->validateSubmitMiniGoliath($league_id, $entry_number, $winners);
        if(!$data['success'])
        {
            switch ($data['code']) 
            {
                case 1:
                    $message = __('Contest was ended', 'victorious');
                    break;
                case 2:
                    $message = __('You need to pick at least a winner or a tie', 'victorious');
                    break;
                case 3:
                    $message = __('You reach maximum join', 'victorious');
                    break;
                case 4:
                    $message = __('Sorry! You were kicked', 'victorious');
                    break;
                default :
                    $message = __('Unknown error', 'victorious');
            }
            exit(json_encode(array('success' => 0, 'message' => $message)));
        }
    }
    
    public static function submitMiniGoliath()
    {
        $league_id = !empty($_POST['leagueID']) ? sanitize_text_field($_POST['leagueID']) : '';
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : '';
        $winners = !empty($_POST['winners']) ? sanitize_text_field($_POST['winners']) : '';
        $invitedby = !empty($_POST['invitedby']) ? sanitize_text_field($_POST['invitedby']) : '';
        
        //league
        $league = self::$victorious->getLeagueDetail($league_id);
        
        //validate
        self::validateForAllGameType($league, $entry_number);
        self::validateSubmitMiniGoliath();
        $league = $league[0];
        
        //submit data
        $data = self::$victorious->submitMiniGoliath($league_id, $entry_number, $winners, $invitedby);
        if(!empty($data['success']))
        {
            self::doAfterEnterContest($league, $entry_number);
            
            exit(json_encode(array('success' => 1, 'redirect' => VICTORIOUS_URL_CONTEST.$league_id.'/?num='.$entry_number.'&invite=1&pick=1')));
        }
        VIC_SetMessage(__('Cannot save data! Please try again.', 'victorious'));
        exit(json_encode(array('success' => 0, 'redirect' => VICTORIOUS_URL_GAME.$league_id.'/?num='.$entry_number)));
    }
    
    public static function minigoliathLoadResult()
    {
        $user_id = VIC_GetUserId();
        $league_id = !empty($_POST['league_id']) ? sanitize_text_field($_POST['league_id']) : "";
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 1;
        $standing_type = !empty($_POST['standing_type']) ? sanitize_text_field($_POST['standing_type']) : 1;
        $current_page = !empty($_POST['page']) ? sanitize_text_field($_POST['page']) : 1;
        
        //load league
        $league = self::$victorious->getLeagueDetail($league_id);
        $league = $league[0];
        
        //scores
        $data = self::$victorious->minigoliathResult($league_id, $standing_type, $current_page);
        $scores = $data['scores'];
        $fights = $data['fights'];
        $total_page = $data['total_page'];
        
        //cur user scores
        if($scores != null)
        {
            foreach($scores as $k => $score)
            {
                $score[$k]['current'] = false;
                if($score['userID'] == $user_id && $score['entry_number'] == sanitize_text_field($_POST['entry_number']))
                {
                    $scores[$k]['current'] = true;
                }
            }
        }
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."minigoliath/result.php");
        exit;
    }
    
    public static function minigoliathLoadResultDetail()
    {
        $user_id = !empty($_POST['user_id']) ? sanitize_text_field($_POST['user_id']) : "";
        $league_id = !empty($_POST['league_id']) ? sanitize_text_field($_POST['league_id']) : "";
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 1;
        $opponent_id = sanitize_text_field($_POST['opponent_id']);
        $opponent_entry_number = sanitize_text_field($_POST['opponent_entry_number']);
        
        //get data
        $data = self::$victorious->minigoliathResultDetail($league_id, $user_id, $entry_number, $opponent_id, $opponent_entry_number);
        $fights = $data['fights'];
        $total_my_points = $data['total_my_points'];
        $total_opponent_points = $data['total_opponent_points'];
        $current_user = self::$payment->getUserData();
        $opponent_user = self::$payment->getUserData($opponent_id);
        
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."minigoliath/result_detail.php");
        exit;
    }
    /////////////////////////////////////end minigoliath/////////////////////////////////////
    
    private static function validateForAllGameType($league, $entry_number)
    {
        if($entry_number < 1)
        {
            $entry_number = 1;
        }
        if($league == null)
        {
            exit(json_encode(array('success' => 0, 'message' => __('Contest does not exist', 'victorious'))));
        }
        $league = $league[0];
        $league_id = $league['leagueID'];
        
        $data = self::$victorious->validateForAllGameType($league_id, $entry_number);
        if(!$data['success'])
        {
            switch ($data['code']) {
                
                case 1:
                    $message = __('Contest does not exist', 'victorious');
                    break;
                case 2:
                    $message = __('This contest has ended', 'victorious');
                    break;
                case 3:
                    $message = __('Sorry! This contest is full', 'victorious');
                    break;
                case 4:
                    $message = __('You reached maximum number of multi entries', 'victorious');
                    break;
                case 5:
                    $message = __('Only users who entered 50 contests or less can join this contest', 'victorious');
                    break;
                case 6:
                    $message = __('Invalid entry number', 'victorious');
                    break;
                default :
                    $message = __('Unknown error', 'victorious');
            }
            exit(json_encode(array('success' => 0, 'message' => $message)));
        }
        
        //validate for make bet
        $makeBet = true;
        if($league['entry_fee'] > 0)
        {
            $makeBet = self::$payment->isMakeBetForLeague($league_id, $entry_number);
            if (!$makeBet && !self::$payment->isUserEnoughMoneyToJoin($league['entry_fee'], $league_id, $entry_number, $league['multi_entry'], $league['balance_type_id']))
            {
                exit(json_encode(array('success' => false, 'message' => __('You do not have enough funds to enter. Please add funds', 'victorious'))));
            }
        }
    }
    
    private static function payForContest($league, $entry_number)
    {
        $makeBet = true;
        if($league['entry_fee'] > 0)
        {
            $makeBet = self::$payment->isMakeBetForLeague($league['leagueID'], $entry_number);
        }
        if($makeBet == false)
        {
            $entry_fee = $league['entry_fee'];

            //decrease user money
            self::$payment->updateUserBalance($entry_fee, true, $league['leagueID'], null, $league['balance_type_id']);

            //get balance
            $user_id = (int)VIC_GetUserId();
            $user_balance = self::$payment->getUserBalance($user_id, $league['balance_type_id']);

            $params = array(
                'amount' => $entry_fee,
                'leagueID' => $league['leagueID'],
                'new_balance' => $user_balance['balance'],
                'operation' => 'DEDUCT',
                'type' => 'MAKE_BET',
                'entry_number' => $entry_number,
                'status' => 'completed',
                'balance_type_id' => $league['balance_type_id']
            );
            self::$payment->addFundhistory($params);
        }

        if($league['olddraft_insurance_fee'] > 0 && !empty($league['olddraft_insurance'])) {
            $reason = 'insurance fee';
            $makeBet = self::$payment->isMakeBetForLeague($league['leagueID'], $entry_number, $reason);

            if($makeBet == false) {
                $entry_fee = round($league['olddraft_insurance_fee'] * $league['entry_fee'] / 100, 2);

                //decrease user money
                self::$payment->updateUserBalance($entry_fee, true, $league['leagueID'], null, $league['balance_type_id']);

                //get balance
                $user_id = (int)VIC_GetUserId();
                $user_balance = self::$payment->getUserBalance($user_id, $league['balance_type_id']);

                $params = array(
                    'amount' => $entry_fee,
                    'leagueID' => $league['leagueID'],
                    'new_balance' => $user_balance['balance'],
                    'operation' => 'DEDUCT',
                    'type' => 'MAKE_BET',
                    'entry_number' => $entry_number,
                    'status' => 'completed',
                    'balance_type_id' => $league['balance_type_id'],
                    'reason' => $reason
                );
                self::$payment->addFundhistory($params);
            }
        }
    }
    
    private static function doAfterEnterContest($league, $entry_number, $no_pay = false)
    {
        if($entry_number < 1)
        {
            $entry_number = 1;
        }
        $user_id = VIC_GetUserId();
        
        //pay for contest
        if(!$no_pay)
        {
            self::payForContest($league, $entry_number);
        }

        //send email
        self::$victorious->sendUserPickEmail($league['leagueID'], $user_id, $entry_number);

        //buddy press integration
        if ($league != null)
        {
            self::$victorious->addEnterContestActivity($league, $user_id);
        }
    }
    
    /////////////////////////////////////leaderboard/////////////////////////////////////
    public function getLivePoint()
    {
        $data = self::$victorious->getLivePoint(sanitize_text_field($_POST['leagueid']), sanitize_text_field($_POST['page']), sanitize_text_field($_POST['city']), sanitize_text_field($_POST['sort']));
		$scores = $data['scores'];
		$pages = $data['pages'];
		$sort = $data['sort'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW.'Elements/live_point.php');
        exit();
    }
    
    public function getLeagueByGameType()
    {
        $data =  self::$victorious->getLeagueByGameType(sanitize_text_field($_POST['gameType']));
        $leagues = $data['leagues'];
        
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW.'Elements/live_point_contest.php');
        exit();
    }
    /////////////////////////////////////end leaderboard/////////////////////////////////////
    
    /////////////////////////////////////cancel contest/////////////////////////////////////
    public static function userCancelContest()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        
        //validate
        $result = self::$victorious->validateCancelContest($league_id);
        if(!$result['success'])
        {
            switch($result['code'])
            {
                case 1:
                    exit(json_encode(array('notice' => __('Contest does not exist', 'victorious'))));
                    break;
                case 2:
                    exit(json_encode(array('notice' => __('Only creator can delete this contest', 'victorious'))));
                    break;
            }
        }
        
        //cancel contest
        $result = self::$victorious->cancelContest($league_id);
        $task = "ADD";
        $users = $result['users'];
        $league = $result['league'];
        $curent_user = wp_get_current_user();
        
        if($league['entry_fee'] > 0 && $users != null){
            $credits = $league['entry_fee'];
            foreach($users as $user){
                $userID = $user['userID'];
                //update balance
                if(self::$payment->updateUserBalance($credits, false, 0, $userID, $league['balance_type_id'])){
                    $user_balance = self::$payment->getUserBalance($userID, $league['balance_type_id']);
                    $params = array(
                        'userID' => $userID,
                        'amount' => $credits,
                        'operation' => "ADD",
                        'type' => 'CREDITS',
                        'new_balance' => $user_balance['balance'],
                        'reason' => "User Cancel Contest",
                        'status' => 'completed',
                        'leagueID'=>$league_id,
                        'balance_type' => $league['balance_type_id']
                    );
                    self::$payment->addFundhistory($params);
                    //self::$victorious->sendUserCreditEmail($userID, $credits, $task, $reason);
                }
            }
            /*self::$payment->deleteFundhistory(array(
                'league_id' => $league_id,
                'type' => 'MAKE_BET',
                'operation' => 'DEDUCT'
            ));*/
        }  
        
        VIC_SetMessage(__('Contest was cancelled', 'victorious'));
        exit(json_encode(array(
            'redirect' => VICTORIOUS_URL_LOBBY
        )));
    }
    /////////////////////////////////////end cancel contest/////////////////////////////////////
    
    /////////////////////////////////////leave contest/////////////////////////////////////
    public static function userLeaveContest()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 1;
        
        //validate
        $result = self::$victorious->validateLeaveContest($league_id, $entry_number);
        if(!$result['success'])
        {
            switch($result['code'])
            {
                case 1:
                    exit(json_encode(array('notice' => __('Contest does not exist', 'victorious'))));
                    break;
                case 2:
                    exit(json_encode(array('notice' => __('You are not join this contest', 'victorious'))));
                    break;
                case 3:
                    exit(json_encode(array('notice' => __('This contest has started', 'victorious'))));
                    break;
            }
        }
        
        //leave contest
        $result = self::$victorious->leaveContest($league_id, $entry_number);
        $task = "ADD";
        $user_id = $result['user_id'];
        $entry_number = $result['entry_number'];
        $league = $result['league'];
        $curent_user = wp_get_current_user();
        
        if($league['entry_fee'] > 0 && (int)$user_id > 0){
            $credits = $league['entry_fee'];
            //update balance
            if(self::$payment->updateUserBalance($credits, false, 0, $user_id, $league['balance_type_id'])){
                $user_balance = self::$payment->getUserBalance($user_id, $league['balance_type_id']);
                $params = array(
                    'userID' => $user_id,
                    'amount' => $credits,
                    'operation' => "ADD",
                    'type' => 'LEAVE_CONTEST',
                    'new_balance' => $user_balance['balance'],
                    'status' => 'completed',
                    'leagueID' => $league_id,
                    'entry_number' => $entry_number,
                    'balance_type' => $league['balance_type_id']
                );
                self::$payment->addFundhistory($params);
            }

            //insurance
            if($league['olddraft_insurance_fee'] > 0 && self::$payment->isMakeBetForLeague($league_id, $entry_number, 'insurance fee')){
                $fee = round($league['olddraft_insurance_fee'] * $league['entry_fee'] / 100, 2);

                //update balance
                if(self::$payment->updateUserBalance($fee, false, 0, $user_id, $league['balance_type_id'])){
                    $user_balance = self::$payment->getUserBalance($user_id, $league['balance_type_id']);
                    $params = array(
                        'userID' => $user_id,
                        'amount' => $fee,
                        'operation' => "ADD",
                        'type' => 'LEAVE_CONTEST',
                        'new_balance' => $user_balance['balance'],
                        'status' => 'completed',
                        'leagueID' => $league_id,
                        'entry_number' => $entry_number,
                        'balance_type' => $league['balance_type_id'],
                        'reason' => 'insurance fee'
                    );
                    self::$payment->addFundhistory($params);
                }
            }
        }  
        
        VIC_SetMessage(__('You have just leaved contest', 'victorious')." '".$league['name']."'");
        exit(json_encode(array(
            'redirect' => VICTORIOUS_URL_LOBBY
        )));
    }
    /////////////////////////////////////end leave contest/////////////////////////////////////
    
    /////////////////////////////////////survival/////////////////////////////////////
    public static function submitSurvival()
    {
        $league_id = !empty($_POST['leagueID']) ? sanitize_text_field($_POST['leagueID']) : '';
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : '';
        $current_week = !empty($_POST['current_week']) ? sanitize_text_field($_POST['current_week']) : '';
        $winners = !empty($_POST['winners']) ? sanitize_text_field($_POST['winners']) : '';
        
        //league
        $league = self::$victorious->getLeagueDetail($league_id);
        
        //validate
        self::validateForAllGameType($league, $entry_number);
        self::validateSubmitSurvival();
        $league = $league[0];
        
        //submit data
        $data = self::$victorious->submitUserPickSurvival($league_id, $entry_number, $current_week, $winners);
        if(!empty($data['success']))
        {
            self::doAfterEnterContest($league, $entry_number);
            
            exit(json_encode(array('success' => 1, 'redirect' => VICTORIOUS_URL_CONTEST.$league_id.'/?num='.$entry_number.'&invite=1&pick=1')));
        }
        VIC_SetMessage(__('Cannot save data! Please try again.', 'victorious'));
        exit(json_encode(array('success' => 0, 'redirect' => VICTORIOUS_URL_GAME.$league_id.'/?num='.$entry_number)));
    }
    
    private static function validateSubmitSurvival()
    {
        $league_id = !empty($_POST['leagueID']) ? sanitize_text_field($_POST['leagueID']) : '';
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : '';
        $current_week = !empty($_POST['current_week']) ? sanitize_text_field($_POST['current_week']) : '';
        $winners = !empty($_POST['winners']) ? sanitize_text_field($_POST['winners']) : '';

        $data = self::$victorious->validateSubmitSurvival($league_id, $entry_number, $current_week, $winners);
        if(!$data['success'])
        {
            switch ($data['code']) 
            {
                case 1:
                    $message = __('Contest was ended', 'victorious');
                    break;
                case 2:
                    $message = __('You need to pick a winner', 'victorious');
                    break;
                case 3:
                    $message = __('Fixture not found', 'victorious');
                    break;
                case 4:
                    $message = __('You have already picked this team', 'victorious');
                    break;
                case 5:
                    $message = __('Sorry! You were kicked', 'victorious');
                    break;
                case 6:
                    $message = __('You can not pick at this time', 'victorious');
                    break;
                default :
                    $message = __('Unknown error', 'victorious');
            }
            exit(json_encode(array('success' => 0, 'message' => $message)));
        }
    }
    
    public static function survivalLoadResult()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $week = sanitize_text_field($_POST['week']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $current_page = sanitize_text_field($_POST['page']);
        
        //league
        $league = self::$victorious->getLeagueDetail($league_id);
        $league = $league[0];
        
        $result = self::$victorious->getSurvivalResult($league_id, $week, $current_page);
        $standing = $result['standing'];
        $total_page = $result['total_page'];
        $predict_match = $result['predict_match'];
        $total_predict_match_score = $result['total_predict_match_score'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."survival/result.php");
        exit;
    }
    
    public static function survivalLoadResultDetail()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $week = sanitize_text_field($_POST['week']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $opponent_id = sanitize_text_field($_POST['opponent_id']);
        $opponent_entry_number = sanitize_text_field($_POST['opponent_entry_number']);
        
        $result = self::$victorious->getSurvivalResultDetail($league_id, $week, $user_id, $entry_number, $opponent_id, $opponent_entry_number);
        $fights = $result['fights'];
        $total_my_points = $result['total_my_points'];
        $total_opponent_points = $result['total_opponent_points'];
		$bonus_my_points = $result['bonus_my_points'];
        $bonus_opponent_points = $result['bonus_opponent_points'];
        $current_user = self::$payment->getUserData();
        $opponent_user = self::$payment->getUserData($opponent_id);
        
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."survival/result_detail.php");
        exit;
    }
    /////////////////////////////////////end survival/////////////////////////////////////
    
    /////////////////////////////////////teamdraft/////////////////////////////////////
    public static function submitTeamDraft()
    {
        $league_id = !empty($_POST['league_id']) ? sanitize_text_field($_POST['league_id']) : '';
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : '';
        $lineup_ids = !empty($_POST['lineup_ids']) ? sanitize_text_field($_POST['lineup_ids']) : '';
        $team_ids = !empty($_POST['team_ids']) ? sanitize_text_field($_POST['team_ids']) : '';
        
        //league
        $league = self::$victorious->getLeagueDetail($league_id);
        
        //validate
        self::validateForAllGameType($league, $entry_number);
        self::validateSubmitTeamDraft();
        $league = $league[0];
        
        //submit data
        $data = self::$victorious->submitTeamDraft($league_id, $entry_number, $lineup_ids, $team_ids);
        if(!empty($data['success']))
        {
            self::doAfterEnterContest($league, $entry_number);
            
            exit(json_encode(array('success' => 1, 'redirect' => VICTORIOUS_URL_CONTEST.$league_id.'/?num='.$entry_number.'&invite=1&pick=1')));
        }
        VIC_SetMessage(__('Cannot save data! Please try again.', 'victorious'));
        exit(json_encode(array('success' => 0, 'redirect' => VICTORIOUS_URL_GAME.$league_id.'/?num='.$entry_number)));
    }
    
    private static function validateSubmitTeamDraft()
    {
        $league_id = !empty($_POST['league_id']) ? sanitize_text_field($_POST['league_id']) : '';
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : '';
        $lineup_ids = !empty($_POST['lineup_ids']) ? sanitize_text_field($_POST['lineup_ids']) : '';
        $team_ids = !empty($_POST['team_ids']) ? sanitize_text_field($_POST['team_ids']) : '';

        $data = self::$victorious->validateSubmitTeamDraft($league_id, $entry_number, $lineup_ids, $team_ids);
        if(!$data['success'])
        {
            switch ($data['code']) 
            {
                case 1:
                    $message = __('Lineup not found', 'victorious');
                    break;
                case 2:
                    $message = __('You need to pick team for each position', 'victorious');
                    break;
                case 3:
                    $message = __('Team not found', 'victorious');
                    break;
                case 4:
                    $message = __("You cannot pick same team for same lineup", 'victorious');
                    break;
                case 5:
                    $message = __("You can only pick same team 2 times", 'victorious');
                    break;
                case 6:
                    $message = __("Your team has exceeded this game's salary cap. Please change your team so it fits under the salary cap before entering", 'victorious');
                    break;
                default :
                    $message = __('Unknown error', 'victorious');
            }
            exit(json_encode(array('success' => 0, 'message' => $message)));
        }
    }
    
    public static function teamDraftLoadResult()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $current_page = sanitize_text_field($_POST['page']);
        
        $data = self::$victorious->getTeamDraftResult($league_id, $current_page);
        $standing = $data['standing'];
        $total_page = $data['total_page'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."teamdraft/result.php");
        exit;
    }
    
    public static function teamDraftLoadResultDetail()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        
        $data = self::$victorious->getTeamDraftResultDetail($league_id, $user_id, $entry_number);
        $league = $data['league'];
        $score = $data['score'];
        $score_detail = $data['score_detail'];
        
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."teamdraft/result_detail.php");
        exit;
    }
    /////////////////////////////////////end teamdraft/////////////////////////////////////
    
    /////////////////////////////////////playerdraft/////////////////////////////////////
    public static function submitPlayerDraftNew()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 1;
        
        //league
        $league = self::$victorious->getLeagueDetail($league_id);

        //validate
        self::validateForAllGameType($league, $entry_number);
        //self::validateSubmitPlayerDraftNew();
        $league = $league[0];

        //submit data
        $data = self::$playerdraft->submitPlayerDraft($_POST);
        if(!empty($data['success']))
        {
            self::doAfterEnterContest($league, $entry_number);
            
            exit(json_encode(array('success' => 1, 'redirect' => VICTORIOUS_URL_ENTRY.$league_id.'/?num='.$entry_number.'&invite=1&pick=1')));
        }
        VIC_SetMessage(__('Cannot save data! Please try again.', 'victorious'));
        exit(json_encode(array('success' => 0, 'redirect' => VICTORIOUS_URL_GAME.$league_id.'/?num='.$entry_number)));
    }
    
    private static function validateSubmitPlayerDraftNew()
    {
        $league_id = !empty($_POST['league_id']) ? sanitize_text_field($_POST['league_id']) : '';
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : '';
        $lineup_ids = !empty($_POST['lineup_ids']) ? sanitize_text_field($_POST['lineup_ids']) : '';
        $player_ids = !empty($_POST['player_ids']) ? sanitize_text_field($_POST['player_ids']) : '';

        $data = self::$playerdraft->validateSubmitPlayerDraft($league_id, $entry_number, $lineup_ids, $player_ids);
        if(!$data['success'])
        {
            switch ($data['code']) 
            {
                case 1:
                    $message = __('Lineup not found', 'victorious');
                    break;
                case 2:
                    $message = __('You need to pick player for each position', 'victorious');
                    break;
                case 3:
                    $message = __('Player not found', 'victorious');
                    break;
                case 4:
                    $message = __("Your player has exceeded this game's salary cap. Please change your player so it fits under the salary cap before entering", 'victorious');
                    break;
                default :
                    $message = __('Unknown error', 'victorious');
            }
            exit(json_encode(array('success' => 0, 'message' => $message)));
        }
    }
    
    public static function playerDraftLoadResult()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $week = sanitize_text_field($_POST['week']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $current_page = sanitize_text_field($_POST['page']);
        
        $data = self::$victorious->getPlayerDraftResult($league_id, $week, $current_page);
        $standing = $data['standing'];
        $total_page = $data['total_page'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."playerdraft/result.php");
        exit;
    }
    
    public static function playerDraftLoadResultDetail()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        
        $data = self::$victorious->getPlayerDraftResultDetail($league_id, $user_id, $entry_number);
        $league = $data['league'];
        $score = $data['score'];
        $score_detail = $data['score_detail'];
        
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."playerdraft/result_detail.php");
        exit;
    }
    /////////////////////////////////////end playerdraft/////////////////////////////////////
    
    /////////////////////////////////////best5/////////////////////////////////////
    public static function best5LoadResult()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $current_page = sanitize_text_field($_POST['page']);
        
        $data = self::$victorious->getBest5Result($league_id, $current_page);
        $standing = $data['standing'];
        $total_page = $data['total_page'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."best5/result.php");
        exit;
    }
    
    public static function best5LoadResultDetail()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        
        $data = self::$victorious->getBest5ResultDetail($league_id, $user_id, $entry_number);
        $league = $data['league'];
        $score = $data['score'];
        $score_detail = $data['score_detail'];
        
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."best5/result_detail.php");
        exit;
    }
    /////////////////////////////////////end best5/////////////////////////////////////
    
    public static function sendContestPassword()
    {
        $league_id = !empty($_POST['league_id']) ? sanitize_text_field($_POST['league_id']) : '';
        $password = !empty($_POST['password']) ? sanitize_text_field($_POST['password']) : '';
        if(empty($league_id) || ($league = self::$victorious->getLeagueDetail(sanitize_text_field($league_id))) == null)
        {
            exit(json_encode(array('success' => 0, 'message' => __('Contest not found', 'victorious'))));
        }
        else if(empty($password))
        {
            exit(json_encode(array('success' => 0, 'message' => __('Password is required', 'victorious'))));
        }
        else
        {
            $league = $league[0];
            if($league['password'] != $password)
            {
                exit(json_encode(array('success' => 0, 'message' => __('Incorrect password', 'victorious'))));
            }
            $link = VICTORIOUS_URL_GAME.$league['leagueID'];
            if($league['multi_entry'] == 1 && isset($league['next_entry']))
            {
                $link .= "?num=".$league['next_entry']."&password=".$password;
            }
            else
            {
                $link .= "?password=".$password;
            }
            exit(json_encode(array('success' => 1, 'message' => $link)));
        }
    }
    
    public static function loadPlayerInfo()
    {
        $data = self::$victorious->loadPlayerInfo(sanitize_text_field($_POST['player_id']));
        $played = $data['played'];
        $minute_played = $data['minute_played'];
        $player = $data['player'];
        $position = $player['position'];
        $team = $player['team'];
        $season_stats = $data['season_stats'];
        $match_stats = $data['match_stats'];
        $performance_chart = $data['performance_chart'];
        
        require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/player_info_new.php");
        exit;
    }
    
    /////////////////////////////////////gateway gamble/////////////////////////////////////
    private static function applyDepositCoupon($amount)
    {
        $reason = '';
        if(!empty($_POST['coupon_code']))
        {
            $coupon = self::$coupon->getCouponByCode(sanitize_text_field($_POST['coupon_code']), CP_ACTION_EXTRA_DEPOSIT);
            if($coupon != null)
            {
                $amount += self::$coupon->getTotalDiscountValue($coupon->discount_type, $coupon->discount_value, $amount);
                $reason = __("Coupon code: ".sanitize_text_field($_POST['coupon_code']), 'victorious');
                self::$coupon->addCouponUsed($coupon->id, VIC_GetUserId());
            }
        }
        return $amount;
    }
    /////////////////////////////////////end gateway gamble/////////////////////////////////////
    
    /////////////////////////////////////sportbook/////////////////////////////////////
    public static function submitSportbook()
    {
        $league_id = !empty($_POST['league_id']) ? sanitize_text_field($_POST['league_id']) : '';
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 1;
        $wager_type = !empty($_POST['wager_type']) ? sanitize_text_field($_POST['wager_type']) : '';
        $wager = !empty($_POST['wager']) ? sanitize_text_field($_POST['wager']) : '';
        $to_win = !empty($_POST['to_win']) ? sanitize_text_field($_POST['to_win']) : '';
        $team_id = !empty($_POST['team_id']) ? sanitize_text_field($_POST['team_id']) : '';
        if(!is_user_logged_in()){
            exit(json_encode(array('success' => 0, 'redirect' => wp_login_url())));
        }
        
        //league
        $league = self::$victorious->getLeagueDetail($league_id);
        
        //validate
        self::validateForAllGameType($league, $entry_number);
        self::validateSubmitSportbook();
        $league = $league[0];
        
        //submit data
        $data = self::$sportbook->submitSportbook($league_id, $entry_number, $wager_type, $wager, $to_win, $team_id);
        if(!empty($data['success']))
        {
            self::doAfterEnterContest($league, $entry_number);
            exit(json_encode(array('success' => 1, 'redirect' => VICTORIOUS_URL_CONTEST.$league_id.'/?num='.$entry_number.'&invite=1&pick=1')));
        }
        VIC_SetMessage(__('Cannot save data! Please try again.', 'victorious'));
        exit(json_encode(array('success' => 0, 'redirect' => VICTORIOUS_URL_GAME.$league_id.'/?num='.$entry_number)));
    }
    
    private static function validateSubmitSportbook()
    {
        $league_id = !empty($_POST['league_id']) ? sanitize_text_field($_POST['league_id']) : '';
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : '';
        $wager_type = !empty($_POST['wager_type']) ? sanitize_text_field($_POST['wager_type']) : '';
        $wager = !empty($_POST['wager']) ? sanitize_text_field($_POST['wager']) : '';
        $to_win = !empty($_POST['to_win']) ? sanitize_text_field($_POST['to_win']) : '';
        $team_id = !empty($_POST['team_id']) ? sanitize_text_field($_POST['team_id']) : '';

        $data = self::$sportbook->validateSubmitSportbook($league_id, $entry_number, $wager_type, $wager, $to_win, $team_id);
        if(!$data['success'])
        {
            switch ($data['code']) 
            {
                case 1:
                    $message = __('Please select at least an item', 'victorious');
                    break;
                case 2:
                    $message = __('Team not found', 'victorious');
                    break;
                case 3:
                    $message = __('Please fill all wagers', 'victorious');
                    break;
                case 4:
                    $message = __('Betting credits exceeded this game\'s total credit', 'victorious');
                    break;
                default :
                    $message = __('Unknown error', 'victorious');
            }
            exit(json_encode(array('success' => 0, 'message' => $message)));
        }
    }
    
    public static function updateOverUnderPoint(){
        $league_id = sanitize_text_field($_POST['league_id']);
        $data = self::$sportbook->updateOverUnderPoint($league_id);
        $fights = !empty($data['fights']) ? $data['fights'] : array();
        
        exit(json_encode($fights));
    }

    public static function sportbookLoadResult()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $current_page = sanitize_text_field($_POST['page']);

        $result = self::$victorious->getSportbookResult($league_id, $current_page);
        $standing = $result['standing'];
        $total_page = $result['total_page'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."sportbook/leaderboard.php");
        exit;
    }

    public static function sportbookLoadResultDetail()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);

        $result_detail = self::$sportbook->getSportbookResultDetail($league_id, $user_id, $entry_number);
        if(empty($result_detail['picks'])){
            VIC_Redirect(VICTORIOUS_URL_LOBBY, __('No pick found', 'victorious'), true);
        }
        $picks = $result_detail['picks'];
        $score = $result_detail['score'];
        $fights = $result_detail['fights'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."sportbook/leaderboard_detail.php");
        exit;
    }

    public static function sportbookLiveEntriesResult()
    {
        self::$victorious->liveEntriesResult($_POST['leagueID']);
        exit;
    }
    /////////////////////////////////////end sportbook/////////////////////////////////////
    
    /////////////////////////////////////uploadphoto/////////////////////////////////////
    public static function submitUploadPhoto(){
        $league_id = sanitize_text_field($_POST['league_id']);
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 1;
        if(!is_user_logged_in()){
            exit(json_encode(array('success' => 0, 'redirect' => wp_login_url())));
        }
        
        //league
        $league = self::$victorious->getLeagueDetail($league_id);
        
        //validate
        self::validateForAllGameType($league, $entry_number);
        self::validateSubmitUploadPhoto();
        $league = $league[0];
        
        //submit data
        $params = array(
            'league_id' => $league_id,
            'entry_number' => $entry_number
        );
        $data = self::$uploadphoto->submitUploadPhoto($params);
        
        if(!empty($data['success']))
        {
            self::doAfterEnterContest($league, $entry_number);
            
            exit(json_encode(array('success' => 1, 'redirect' => VICTORIOUS_URL_ENTRY.$league_id.'/?num='.$entry_number)));
        }
        exit(json_encode(array('success' => 0, 'message' => __('Cannot join league! Please try again.', 'victorious'))));
    }
    
    private static function validateSubmitUploadPhoto()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 1;
        $params = array(
            'league_id' => $league_id,
            'entry_number' => $entry_number
        );
        $data = self::$uploadphoto->validateSubmitUploadPhoto($params);
        if(!$data['success'])
        {
            switch ($data['code']) 
            {
                case 1:
                    $message = __('You have already joined this contest', 'victorious');
                    break;
                default :
                    $message = __('Unknown error', 'victorious');
            }
            exit(json_encode(array('success' => 0, 'message' => $message)));
        }
    }
    
    public static function uploadPhotoLoadResult(){
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $current_page = sanitize_text_field($_POST['page']);
        $admin_page = !empty($_POST['admin_page']) ? sanitize_text_field($_POST['admin_page']) : false;
        
        $result = self::$uploadphoto->getResult($league_id, $current_page);
        $league = $result['league'];
        $standing = $result['standing'];
        $total_page = $result['total_page'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."uploadphoto/result.php");
        exit;
    }

    public static function uploadPhotoLoadResultDetail(){
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $admin_page = sanitize_text_field($_POST['admin_page']);

        $result = self::$uploadphoto->getResultDetail($league_id, $user_id, $entry_number);
        $league = $result['league'];
        $fights = $result['fights'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."uploadphoto/result_detail.php");
        exit;
    }

    public static function saveUploadPhotoResult(){
        //validate
        self::validateSaveResult();
        
        $params = array(
            'league_id' => sanitize_text_field($_POST['league_id']),
            'fixture_id' => sanitize_text_field($_POST['fixture_id']),
            'total_kill' => sanitize_text_field($_POST['total_kill']),
            'finish' => sanitize_text_field($_POST['finish']),
            'image' => sanitize_text_field($_POST['image'])
        );
        $data = self::$uploadphoto->saveResult($params);
        
        if($data['success']){
            exit(json_encode(array('success' => 1, 'message' => __('Result was updated', 'victorious'))));
        }
        exit(json_encode(array('success' => 0, 'message' => __('Cannot save! Please try again.', 'victorious'))));
    }
    
    private static function validateSaveResult(){
        $league_id = !empty($_POST['league_id']) ? sanitize_text_field($_POST['league_id']) : '';
        $fixture_id = !empty($_POST['fixture_id']) ? sanitize_text_field($_POST['fixture_id']) : '';
        $total_kill = !empty($_POST['total_kill']) ? sanitize_text_field($_POST['total_kill']) : '';
        $finish = !empty($_POST['finish']) ? sanitize_text_field($_POST['finish']) : '';
        $image = !empty($_POST['image']) ? sanitize_text_field($_POST['image']) : '';

        if(empty($league_id)){
            exit(json_encode(array('success' => 0, 'message' => __('Contest not found', 'victorious'))));
        }
        if(empty($fixture_id)){
            exit(json_encode(array('success' => 0, 'message' => __('Fixture is required', 'victorious'))));
        }
        if(empty($total_kill)){
            exit(json_encode(array('success' => 0, 'message' => __('Total kills value is required', 'victorious'))));
        }
        if(empty($finish)){
            exit(json_encode(array('success' => 0, 'message' => __('Finish value is required', 'victorious'))));
        }
        if(empty($image)){
            exit(json_encode(array('success' => 0, 'message' => __('Photo is required', 'victorious'))));
        }
        
        $data = self::$uploadphoto->validateSaveResult($league_id, $fixture_id, $total_kill, $finish, $image);
        if(!$data['success'])
        {
            switch ($data['code']) 
            {
                case 1:
                    $message = __('Contest not found', 'victorious');
                    break;
                case 2:
                    $message = __('You have not joined contest yet', 'victorious');
                    break;
                case 3:
                    $message = __('Fixture not found', 'victorious');
                    break;
                default :
                    $message = __('Unknown error', 'victorious');
            }
            exit(json_encode(array('success' => 0, 'message' => $message)));
        }
    }
    
    public static function completeUploadPhotoContest(){
        $league_id = !empty($_POST['league_id']) ? sanitize_text_field($_POST['league_id']) : '';
        if(empty($league_id) || ($league = self::$victorious->getLeagueDetail($league_id)) == null){
            exit(json_encode(array('success' => 0, 'message' => __('Contest not found', 'victorious'))));
        }
        
        $data = self::$uploadphoto->completeUploadPhotoContest($league_id);

        if($data['success']){
            VIC_SetMessage(__('Contest was completed', 'victorious'));
            exit(json_encode(array('success' => 1)));
        }
        exit(json_encode(array('success' => 0, 'message' => __('Cannot complete contest! Please try again.', 'victorious'))));
    }
    /////////////////////////////////////end uploadphoto/////////////////////////////////////

    /////////////////////////////////////paysimple gateway/////////////////////////////////////
    public static function paySimpleCreateCustomer(){
        $first_name = !empty($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
        $last_name = !empty($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
        $address = !empty($_POST['address']) ? sanitize_text_field($_POST['address']) : '';
        $city = !empty($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
        $zip_code = !empty($_POST['zip_code']) ? sanitize_text_field($_POST['zip_code']) : '';
        if(empty(trim($first_name))){
            exit(json_encode(array('notice' => __('Please input first name', 'victorious'))));
        }
        if(empty(trim($last_name))){
            exit(json_encode(array('notice' => __('Please input last name', 'victorious'))));
        }
        if(empty(trim($address))){
            exit(json_encode(array('notice' => __('Please input address', 'victorious'))));
        }
        if(empty(trim($city))){
            exit(json_encode(array('notice' => __('Please input city', 'victorious'))));
        }
        if(empty(trim($zip_code))){
            exit(json_encode(array('notice' => __('Please input zip code', 'victorious'))));
        }

        //create
        $result = self::$payment->paySimpleCreateCustomer($first_name, $last_name, $address, $city, $zip_code);
        if(!$result){
            exit(json_encode(array('notice' => __('Unable to create customer! Please try again.', 'victorious'))));
        }
        exit(json_encode(array('result' => VICTORIOUS_URL_ADD_FUNDS.'?type='.VICTORIOUS_GATEWAY_PAYSIMPLE)));
    }

    public static function paySimpleAddFund(){
        $user_id = (int)VIC_GetUserId();

        //validate
        self::validatePaySimpleGateway();
        $card_number = !empty($_POST['card_number']) ? sanitize_text_field($_POST['card_number']) : '';
        $cvv = !empty($_POST['cvv']) ? sanitize_text_field($_POST['cvv']) : '';
        $expiration_date = !empty($_POST['expiration_date']) ? sanitize_text_field($_POST['expiration_date']) : '';
        $amount = !empty($_POST['amount']) ? sanitize_text_field($_POST['amount']) : 0;

        //create transaction
        $params = array(
            'amount' => sanitize_text_field($amount),
            'operation' => 'ADD',
            'type' => 'DEPOSIT',
            'gateway' => VICTORIOUS_GATEWAY_PAYSIMPLE,
            'reason' => '',
            'site_profit' => 0,
            'cash_to_credit' => (int)get_option('victorious_cash_to_credit'),
        );
        $fundhistory_id = self::$payment->addFundhistory($params);
        if($fundhistory_id == null){
            exit(json_encode(array('notice' => __('Payment failed! Please try again', 'victorious'))));
        }

        //pay
        $result = self::$payment->paySimplePay($amount, $fundhistory_id, $card_number, $cvv, $expiration_date);
        if($result['success']){
            //update balance
            $balance_type = self::$payment->getBalanceTypeByGateway();
            self::$payment->updateUserBalance($amount, false, 0, $user_id, $balance_type);

            //update transaction status
            $fundData = array(
                'transactionID' => $result['transactionID']
            );
            self::$payment->updateFundhistory($fundhistory_id, $fundData, $user_id, 'completed');

            VIC_SetMessage(__('Payment successful', 'victorious'));
            exit(json_encode(array('url' => VICTORIOUS_URL_ADD_FUNDS.'?type='.VICTORIOUS_GATEWAY_PAYSIMPLE)));
        }

        //update failed
        self::$payment->updateFundhistory($fundhistory_id, null, $user_id, 'failed');

        exit(json_encode(array('notice' => __('Payment failed! Please try again', 'victorious'))));
    }

    private static function validatePaySimpleGateway()
    {
        require_once(VICTORIOUS__PLUGIN_DIR_MODEL.'card_detector.php');
        $detector = new VIC_CardDetector();
        $card_number = !empty($_POST['card_number']) ? sanitize_text_field($_POST['card_number']) : '';
        $cvv = !empty($_POST['cvv']) ? sanitize_text_field($_POST['cvv']) : '';
        $expiration_date = !empty($_POST['expiration_date']) ? sanitize_text_field($_POST['expiration_date']) : '';
        $amount = !empty($_POST['amount']) ? sanitize_text_field($_POST['amount']) : 0;

        if(empty($card_number)){
            exit(json_encode(array('notice' => __('Please input card number', 'victorious'))));
        }
        if(!$detector->detect($card_number)){
            exit(json_encode(array('notice' => __('Invalid card number', 'victorious'))));
        }

        if(empty($cvv)){
            exit(json_encode(array('notice' => __('Please input CVV', 'victorious'))));
        }
        if(empty($expiration_date)){
            exit(json_encode(array('notice' => __('Please input expiration date', 'victorious'))));
        }
        $expires =  date_format( \DateTime::createFromFormat('m/Y', $expiration_date),"m/Y");
        $now =  date_format(new \DateTime(),"m/Y");
        if ($expires < $now) {
            exit(json_encode(array('notice' => __('Invalid expiration date', 'victorious'))));
        }
        if(empty($amount)){
            exit(json_encode(array('notice' => __('Please input amount', 'victorious'))));
        }
        if(!is_numeric($amount) || (int)$amount < 1)
        {
            exit(json_encode(array('notice' => __('Invalid amount', 'victorious'))));
        }
        if($amount < get_option('victorious_minimum_deposit'))
        {
            exit(json_encode(array('notice' => __('Amount must be greater than ').get_option('victorious_minimum_deposit', 'victorious'))));
        }
    }

    public static function paySimpleRequestToWithdraw(){
        $user_id = VIC_GetUserId();
        $credits = sanitize_text_field($_POST['val']['credits']);
        $reason = sanitize_text_field($_POST['val']['reason']);
        $balance_type = self::$payment->getBalanceTypeByGateway(VICTORIOUS_GATEWAY_PAYSIMPLE);
        $balance_key = VIC_BalanceField($balance_type);

        //validate
        if(!is_numeric($credits) || (int)$credits < 1)
        {
            exit(json_encode(array('notice' => __('Invalid amount', 'victorious'))));
        }
        if(!self::$payment->isAllowWithdraw($credits, null, $balance_type))
        {
            exit(json_encode(array('notice' => __('Credits must not exceed your available balance', 'victorious'))));
        }

        //update data
        if(self::$payment->updateUserBalance($credits, true, 0, null, $balance_type))
        {
            //add withdrawl
            $aUser = self::$payment->getUserData();
            $withdraw_data = array(
                'userID' => $aUser['ID'],
                'amount' => $credits,
                'new_balance' => $aUser[$balance_key],
                'reason' => $reason,
                'gateway' => VICTORIOUS_GATEWAY_PAYSIMPLE,
                'balance_type' => $balance_type
            );
            $withdrawlId = self::$payment->addWithdraw($withdraw_data);

            //update user info
            $data = array(
                'email' => !empty($_POST['val']['email']) ? sanitize_text_field($_POST['val']['email']) : '',
                'name' => !empty($_POST['val']['name']) ? sanitize_text_field($_POST['val']['name']) : '',
                'house' => !empty($_POST['val']['house']) ? sanitize_text_field($_POST['val']['house']) : '',
                'street' => !empty($_POST['val']['street']) ? sanitize_text_field($_POST['val']['street']) : '',
                'unit_number' => !empty($_POST['val']['unit_number']) ? sanitize_text_field($_POST['val']['unit_number']) : '',
                'city' => !empty($_POST['val']['city']) ? sanitize_text_field($_POST['val']['city']) : '',
                'state' => !empty($_POST['val']['state']) ? sanitize_text_field($_POST['val']['state']) : '',
                'country' => !empty($_POST['val']['country']) ? sanitize_text_field($_POST['val']['country']) : '',
                'username' => !empty($_POST['val']['username']) ? sanitize_text_field($_POST['val']['username']) : '',
                'password' => !empty($_POST['val']['password']) ? sanitize_text_field($_POST['val']['password']) : '',
                'dfscoin_wallet_address' => !empty($_POST['val']['dfscoin_wallet_address']) ? sanitize_text_field($_POST['val']['dfscoin_wallet_address']) : '',
            );

            if(!self::$payment->isUserPaymentInfoExist($data))
            {
                self::$payment->addUserPaymentInfo($data);
            }
            else
            {
                self::$payment->updateUserPaymentInfo($data);
            }
            $params = array(
                'userID' => $user_id,
                'amount' => $credits,
                'operation' => 'DEDUCT',
                'type' => 'WITHDRAW',
                'gateway' => VICTORIOUS_GATEWAY_PAYSIMPLE,
                'new_balance' => $aUser[$balance_key],
                'reason' => 'request  to withdraw',
                'status' => 'completed',
                'withdrawlID' => $withdrawlId,
                'balance_type' => $balance_type
            );
            $fundhistory_id = self::$payment->addFundhistory($params);

            //update verification code
            $verification_code = self::$payment->createVerificationCode($fundhistory_id);
            self::$payment->updateFundhistoryVerificationCode($fundhistory_id, $verification_code);

            //send email
            self::$victorious->sendRequestPaymentEmail($withdrawlId, $credits);

            exit(json_encode(array('result' => __('Your request has been sent', 'victorious'), 'redirect' => VICTORIOUS_URL_REQUEST_HISTORY)));
        }
        else
        {
            exit(json_encode(array('notice' => __('Something went wrong! Please try again.', 'victorious'))));
        }
    }
    /////////////////////////////////////end paysimple gateway/////////////////////////////////////

    /////////////////////////////////////portfolio/////////////////////////////////////
    public static function getPortfolioPlayerList(){
        $data = self::$portfolio->getPortfolioPlayerList(
            sanitize_text_field($_POST['league_id']),
            sanitize_text_field($_POST['position_id']),
            sanitize_text_field($_POST['category_id']),
            sanitize_text_field($_POST['sort_type']),
            sanitize_text_field($_POST['sort']),
            sanitize_text_field($_POST['keyword']),
            sanitize_text_field($_POST['page'])
        );
        $players = $data['players'];
        $total_page = $data['total_page'];
        $current_page = sanitize_text_field($_POST['page']);

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."portfolio/game_player_list.php");
        exit;
    }

    public static function submitPortfolio()
    {
        $league_id = !empty($_POST['league_id']) ? sanitize_text_field($_POST['league_id']) : '';
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 1;
        $lineup_ids = !empty($_POST['lineup_ids']) ? sanitize_text_field($_POST['lineup_ids']) : '';
        $player_ids = !empty($_POST['player_ids']) ? sanitize_text_field($_POST['player_ids']) : '';
        $quantity = !empty($_POST['quantity']) ? sanitize_text_field($_POST['quantity']) : '';

        //league
        $league = self::$victorious->getLeagueDetail($league_id);

        //validate
        self::validateForAllGameType($league, $entry_number);
        self::validateSubmitPortfolio();
        $league = $league[0];

        //submit data
        $data = self::$portfolio->submitPortfolio($league_id, $entry_number, $lineup_ids, $player_ids, $quantity);
        if(!empty($data['success']))
        {
            self::doAfterEnterContest($league, $entry_number);

            exit(json_encode(array('success' => 1, 'redirect' => VICTORIOUS_URL_ENTRY.$league_id.'/?num='.$entry_number.'&invite=1&pick=1')));
        }
        VIC_SetMessage(__('Cannot save data! Please try again.', 'victorious'));
        exit(json_encode(array('success' => 0, 'redirect' => VICTORIOUS_URL_GAME.$league_id.'/?num='.$entry_number)));
    }

    private static function validateSubmitPortfolio()
    {
        $league_id = !empty($_POST['league_id']) ? sanitize_text_field($_POST['league_id']) : '';
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 1;
        $lineup_ids = !empty($_POST['lineup_ids']) ? sanitize_text_field($_POST['lineup_ids']) : '';
        $player_ids = !empty($_POST['player_ids']) ? sanitize_text_field($_POST['player_ids']) : '';
        $quantity = !empty($_POST['quantity']) ? sanitize_text_field($_POST['quantity']) : '';

        $data = self::$portfolio->validateSubmitPortfolio($league_id, $entry_number, $lineup_ids, $player_ids, $quantity);
        if(!$data['success'])
        {
            switch ($data['code'])
            {
                case 1:
                    $message = __('You cannot enter this contest at this time', 'victorious');
                    break;
                case 2:
                    $message = __('Lineup not found', 'victorious');
                    break;
                case 3:
                    $message = __('You need to pick player for each position', 'victorious');
                    break;
                case 4:
                    $message = __('Player not found', 'victorious');
                    break;
                case 5:
                    $message = __("Invalid quantity", 'victorious');
                    break;
                case 6:
                    $message = __("You have exceeded the salary cap for your selections. Please change your selection so it fits under the salary cap limit.", 'victorious');
                    break;
                default :
                    $message = __('Unknown error', 'victorious');
            }
            exit(json_encode(array('success' => 0, 'message' => $message)));
        }
    }

    public static function getPortfolioResult()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $current_page = sanitize_text_field($_POST['page']);

        $data = self::$portfolio->getPortfolioResult($league_id, $current_page);
        $standing = $data['standing'];
        $total_page = $data['total_page'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."portfolio/leaderboard.php");
        exit;
    }

    public static function getPortfolioResultDetail()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $my_user_id = sanitize_text_field($_POST['my_user_id']);
        $my_entry_number = sanitize_text_field($_POST['my_entry_number']);

        $my_data = self::$portfolio->getPortfolioResultDetail($league_id, $my_user_id, $my_entry_number);
        $data = self::$portfolio->getPortfolioResultDetail($league_id, $user_id, $entry_number);
        $league = $data['league'];
        $my_score = $my_data['score'];
        $my_score_detail = $my_data['score_detail'];
        $score = $data['score'];
        $score_detail = $data['score_detail'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."portfolio/leaderboard_detail.php");
        exit;
    }

    public static function getPortfolioLiveResult()
    {
        self::$portfolio->getPortfolioLiveResult(sanitize_text_field($_POST['league_id']));
        exit;
    }
    /////////////////////////////////////end portfolio/////////////////////////////////////

    /////////////////////////////////////olddraft/////////////////////////////////////
    public static function getOldDraftPlayerList(){
        $data = self::$olddraft->getOldDraftPlayerList(
            sanitize_text_field($_POST['league_id']),
            sanitize_text_field($_POST['position_id']),
            sanitize_text_field($_POST['sort_type']),
            sanitize_text_field($_POST['sort']),
            sanitize_text_field($_POST['keyword']),
            sanitize_text_field($_POST['page'])
        );
        $players = $data['players'];
        $total_page = $data['total_page'];
        $current_page = sanitize_text_field($_POST['page']);

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."olddraft/game_player_list.php");
        exit;
    }

    public static function submitOldDraft()
    {
        $league_id = !empty($_POST['league_id']) ? sanitize_text_field($_POST['league_id']) : '';
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 1;
        $lineup_ids = !empty($_POST['lineup_ids']) ? sanitize_text_field($_POST['lineup_ids']) : '';
        $player_ids = !empty($_POST['player_ids']) ? sanitize_text_field($_POST['player_ids']) : '';
        $olddraft_insurance = !empty($_POST['olddraft_insurance']) ? sanitize_text_field($_POST['olddraft_insurance']) : '';

        //league
        $league = self::$victorious->getLeagueDetail($league_id);

        //validate
        self::validateForAllGameType($league, $entry_number);
        self::validateSubmitOldDraft();
        $league = $league[0];

        //submit data
        $data = self::$olddraft->submitOldDraft($league_id, $entry_number, $lineup_ids, $player_ids, $olddraft_insurance);
        if(!empty($data['success']))
        {
            if($league['olddraft_insurance_fee'] > 0 && !empty($olddraft_insurance)) {
                $league['olddraft_insurance'] = 1;
            }
            self::doAfterEnterContest($league, $entry_number);

            exit(json_encode(array('success' => 1, 'redirect' => VICTORIOUS_URL_ENTRY.$league_id.'/?num='.$entry_number.'&invite=1&pick=1')));
        }
        VIC_SetMessage(__('Cannot save data! Please try again.', 'victorious'));
        exit(json_encode(array('success' => 0, 'redirect' => VICTORIOUS_URL_GAME.$league_id.'/?num='.$entry_number)));
    }

    private static function validateSubmitOldDraft()
    {
        $league_id = !empty($_POST['league_id']) ? sanitize_text_field($_POST['league_id']) : '';
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 1;
        $lineup_ids = !empty($_POST['lineup_ids']) ? sanitize_text_field($_POST['lineup_ids']) : '';
        $player_ids = !empty($_POST['player_ids']) ? sanitize_text_field($_POST['player_ids']) : '';
        $olddraft_insurance = !empty($_POST['olddraft_insurance']) ? sanitize_text_field($_POST['olddraft_insurance']) : '';

        $data = self::$olddraft->validateSubmitOldDraft($league_id, $entry_number, $lineup_ids, $player_ids, $olddraft_insurance);
        if(!$data['success'])
        {
            switch ($data['code'])
            {
                case 1:
                    $message = __('You cannot enter this contest at this time', 'victorious');
                    break;
                case 2:
                    $message = __('Lineup not found', 'victorious');
                    break;
                case 3:
                    $message = __('You need to pick player for each position', 'victorious');
                    break;
                case 4:
                    $message = __('Player not found', 'victorious');
                    break;
                case 5:
                    $message = __("Invalid quantity", 'victorious');
                    break;
                case 6:
                    $message = __("Your player has exceeded this game's salary cap. Please change your player so it fits under the salary cap before entering", 'victorious');
                    break;
                default :
                    $message = __('Unknown error', 'victorious');
            }
            exit(json_encode(array('success' => 0, 'message' => $message)));
        }
    }

    public static function getOldDraftResult()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $current_page = sanitize_text_field($_POST['page']);

        $data = self::$olddraft->getOldDraftResult($league_id, $current_page);
        $standing = $data['standing'];
        $total_page = $data['total_page'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."olddraft/leaderboard.php");
        exit;
    }

    public static function getOldDraftResultDetail()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $my_user_id = sanitize_text_field($_POST['my_user_id']);
        $my_entry_number = sanitize_text_field($_POST['my_entry_number']);

        $my_data = self::$olddraft->getOldDraftResultDetail($league_id, $my_user_id, $my_entry_number);
        $data = self::$olddraft->getOldDraftResultDetail($league_id, $user_id, $entry_number);
        $league = $data['league'];
        $my_score = $my_data['score'];
        $my_score_detail = $my_data['score_detail'];
        $score = $data['score'];
        $score_detail = $data['score_detail'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."olddraft/leaderboard_detail.php");
        exit;
    }

    public static function getOldDraftLiveResult()
    {
        self::$olddraft->getOldDraftLiveResult(sanitize_text_field($_POST['league_id']));
        exit;
    }
    /////////////////////////////////////end olddraft/////////////////////////////////////
    /// 
    /////////////////////////////////////playoff/////////////////////////////////////
    public static function getPlayoffPlayerList(){
        $data = self::$playoff->getPlayoffPlayerList(
            sanitize_text_field($_POST['league_id']),
            sanitize_text_field($_POST['position_id']),
            sanitize_text_field($_POST['fight_id']),
            sanitize_text_field($_POST['player_id']),
            sanitize_text_field($_POST['sort_type']),
            sanitize_text_field($_POST['sort']),
            sanitize_text_field($_POST['keyword']),
            sanitize_text_field($_POST['page'])
        );
        $league = $data['league'];
        $players = $data['players'];
        $total_page = $data['total_page'];
        $current_page = sanitize_text_field($_POST['page']);

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."playoff/draft_player_list.php");
        exit;
    }

    public static function submitPlayoff()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $entry_number = isset($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 0;
        $lineup_ids = isset($_POST['lineup_ids']) ? sanitize_text_field($_POST['lineup_ids']) : '';
        $player_ids = isset($_POST['player_ids']) ? sanitize_text_field($_POST['player_ids']) : '';

        //get league data
        $league = self::$victorious->getLeagueDetail($league_id);

        //validate
        self::validateForAllGameType($league, $entry_number);
        self::validateSubmitPlayoff();
        $league = $league[0];

        //insert pick
        $data = self::$playoff->submitPlayoff($league_id, $entry_number, $lineup_ids, $player_ids);
        if($data['success'])
        {
            self::doAfterEnterContest($league, $entry_number);

            exit(json_encode(array('success' => 1, 'redirect' => VICTORIOUS_URL_CONTEST.$league_id.'/?num='.$entry_number.'&invite=1&pick=1')));
        }
        VIC_SetMessage(__('Cannot save data! Please try again.', 'victorious'));
        exit(json_encode(array('success' => 0, 'redirect' => VICTORIOUS_URL_GAME.$league_id.'/?num='.$entry_number.'&invite=1&pick=1')));
    }

    private static function validateSubmitPlayoff()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $entry_number = isset($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number']) : 0;
        $lineup_ids = isset($_POST['lineup_ids']) ? sanitize_text_field($_POST['lineup_ids']) : '';
        $player_ids = isset($_POST['player_ids']) ? sanitize_text_field($_POST['player_ids']) : '';
        $valid = self::$playoff->validateSubmitPlayoff($league_id, $entry_number, $lineup_ids, $player_ids);
        switch ($valid)
        {
            case 1:
                $message = __('You cannot enter this contest at this time', 'victorious');
                break;
            case 2:
                $message = __('Lineup not found', 'victorious');
                break;
            case 3:
                $message = __('You need to pick player for each position', 'victorious');
                break;
            case 4:
                $message = __('Player not found', 'victorious');
                break;
            case 5:
                $message = __("Your player has exceeded this game's salary cap. Please change your player so it fits under the salary cap before entering", 'victorious');
                break;
            default :
                $message = "";
        }
        if($message != "")
        {
            exit(json_encode(array('success' => 0, 'message' => $message)));
        }
    }

    public static function getPlayoffResult()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $current_page = sanitize_text_field($_POST['page']);

        $data = self::$playoff->getPlayoffResult($league_id, $current_page);
        $standing = $data['standing'];
        $total_page = $data['total_page'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."playoff/leaderboard.php");
        exit;
    }

    public static function getPlayoffResultDetail()
    {
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $my_user_id = sanitize_text_field($_POST['my_user_id']);
        $my_entry_number = sanitize_text_field($_POST['my_entry_number']);
        $week = sanitize_text_field($_POST['week']);

        $my_data = self::$playoff->getPlayoffResultDetail($league_id, $my_user_id, $my_entry_number, $week);
        $data = self::$playoff->getPlayoffResultDetail($league_id, $user_id, $entry_number, $week);
        $league = $data['league'];
        $my_score = $my_data['score'];
        $my_score_detail = $my_data['score_detail'];
        $score = $data['score'];
        $score_detail = $data['score_detail'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."playoff/leaderboard_detail.php");
        exit;
    }

    public static function getPlayoffLiveResult()
    {
        self::$playoff->getPlayoffLiveResult(sanitize_text_field($_POST['leagueID']));
        exit;
    }

    public function playoffJoinContest(){
        $result = self::$playoff->joinContest(sanitize_text_field($_POST['leagueID']));
        if($result['success']){
            $draft_start_date = $result['playoff_wildcard_start'];
            $message = esc_html(__("Thank you for joinning. This contest will allow draft player on $draft_start_date. We will send you an email when draft time starts.", "victorious"));
        }
        else{
            $message = esc_html(__("You have already joined this contest or you cannot join this contest now! Please try again.", "victorious"));
        }

        exit(json_encode(array('success' => $result['success'], 'message' => $message)));
    }

    public function playoffInDraftingUsers(){
        $league_id = sanitize_text_field($_POST['league_id']);
        $result = self::$playoff->inDraftingUsers($league_id);
        $scores = $result['scores'];
        $can_draft = $result['can_draft'];
        $interval = $result['interval'];

        require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT."playoff/in_drafting_users.php");
        exit;
    }

    public function playoffDraftPlayer(){
        $league_id = sanitize_text_field($_POST['league_id']);
        $player_id = sanitize_text_field($_POST['player_id']);
        $position_id = sanitize_text_field($_POST['position_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);

        $result = self::$playoff->draftPlayer($league_id, $player_id, $position_id, $entry_number);
        $message = '';
        if(!empty($result['error_code'])){
            switch($result['error_code']){
                case 1:
                    $message = esc_html(__("You cannot draft player now! Please try again.", "victorious"));
                    break;
                case 2:
                    $message = esc_html(__("The player you draft does not match current lineup", "victorious"));
                    break;
                case 3:
                    $message = esc_html(__("You have already drafted player this turn", "victorious"));
                    break;
            }
        }
        else{
            self::$playoff->updatePlayoffTurn($league_id, $result['current_turn_user_id']);
        }
        
        exit(json_encode(array(
            'success' => $result['success'],
            'message' => $message,
            'draft_end' => $result['draft_end'],
            'redirect' => VICTORIOUS_URL_CONTEST.$league_id.'/?num='.$entry_number
        )));
    }

    public function playoffRemoveDraftPlayer(){
        $league_id = sanitize_text_field($_POST['league_id']);
        $player_id = sanitize_text_field($_POST['player_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);

        $result = self::$playoff->removeDraftPlayer($league_id, $player_id, $entry_number);
        $message = '';
        if(!empty($result['error_code'])){
            switch($result['error_code']){
                case 1:
                    $message = esc_html(__("You cannot remove player now!", "victorious"));
                    break;
                case 2:
                    $message = esc_html(__("The player you want to remove does not exist", "victorious"));
                    break;
            }
        }

        exit(json_encode(array(
            'success' => $result['success'],
            'message' => $message
        )));
    }

    public function playoffCheckChangeTurn(){
        $league_id = sanitize_text_field($_POST['league_id']);
        $user_id = sanitize_text_field($_POST['user_id']);

        $leagues = get_option('victorious_playoff_change_turn');
        //update_option('victorious_playoff_change_turn', array());

        $success = false;
        if($leagues != null && isset($leagues[$league_id])){
            if($leagues[$league_id] != $user_id){
                $success = true;
            }
        }

        exit(json_encode(array('success' => $success)));
    }

    public function playoffAutoDraftPlayer(){
        $league_id = sanitize_text_field($_POST['league_id']);
        $entry_number = sanitize_text_field($_POST['entry_number']);

        $result = self::$playoff->autoDraftPlayer($league_id, $entry_number);

        self::$playoff->updatePlayoffTurn($league_id, $result['current_turn_user_id']);

        exit(json_encode(array(
            'success' => $result['success'],
            'draft_end' => $result['draft_end'],
            'player' => $result['player'],
            'redirect' => VICTORIOUS_URL_CONTEST.$league_id.'/?num='.$entry_number
        )));
    }
    /////////////////////////////////////end playoff/////////////////////////////////////
}

?>