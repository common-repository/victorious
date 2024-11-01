<?php

class VIC_GameController {

    private static $orgs;
    private static $pools;
    private static $playerposition;
    private static $players;
    private static $teams;
    private static $victorious;
    private static $payment;
    private static $sportbook;
    private static $playerdraft;
    private static $portfolio;
    private static $olddraft;
    private static $playoff;

    public function __construct() {
        self::$payment = new VIC_Payment();
        self::$orgs = new VIC_Organizations();
        self::$pools = new VIC_Pools();
        self::$playerposition = new VIC_PlayerPosition();
        self::$players = new VIC_Players();
        self::$teams = new VIC_Teams();
        self::$victorious = new VIC_Victorious();
        self::$sportbook = new VIC_Sportbook();
        self::$playerdraft = new VIC_Playerdraft();
        self::$portfolio = new VIC_Portfolio();
        self::$olddraft = new VIC_OldDraft();
        self::$playoff = new VIC_Playoff();
    }

    public static function process() {
        if(!is_user_logged_in()){
            VIC_Redirect(wp_login_url());
        }
        if (!isset($_GET['league_id'])) {
            $lid = pageSegment(3);
            $queries = array('league_id' => $lid);
            if (!empty($_SERVER['QUERY_STRING'])) {
                $tmp = array();
                parse_str($_SERVER['QUERY_STRING'], $tmp);
                $queries = array_merge($queries, $tmp);
            }
            VIC_Redirect(VICTORIOUS_URL_GAME . '?' . http_build_query($queries));
        }
        add_action('wp_enqueue_scripts', array('VIC_GameController', 'loadCssJs'));

        //self::loadCssJs();
        if (isset($_GET['manage_trade_request']) && isset($_GET['league_id']) && isset($_GET['entry_number'])) {
            add_filter('the_content', array('VIC_GameController', 'liveDraftManageTradeRequest'));
        }
        elseif (isset($_GET['trade_target_id']) && isset($_GET['contest_id']) && isset($_GET['target_entry']) && isset($_GET['entry_number'])) {
            add_filter('the_content', array('VIC_GameController', 'liveDraftTradePlayer'));
        }
        elseif (isset($_GET['action'])/* && $_GET['action'] == 3 */) {
            //add_action('wp_enqueue_scripts', array('VIC_GameController', 'live_draft_theme_name_scripts'));
            add_filter('the_content', array('VIC_GameController', 'game'));
        }
        elseif (isset($_POST['submitPicks'])) {
            if ($_POST["session_id"] == session_id()) {
                add_filter('the_content', array('VIC_GameController', 'submitPicks'));
            }
            else {
                VIC_Redirect(VICTORIOUS_URL_CREATE_CONTEST, __("Contest does not exist.", 'victorious'), true);
            }
        }
        else {
            add_filter('the_content', array('VIC_GameController', 'game'));
        }
    }

    public static function loadCssJs() {
        $league_id = sanitize_text_field($_GET['league_id']);

        //league
        $league = self::$victorious->getLeagueDetail($league_id);
        if ($league == null) {
            VIC_Redirect(VICTORIOUS_URL_CREATE_CONTEST, __('Contest does not exist', 'victorious'), true);
        }
        $league = $league[0];
        if ($league['password'] != null && (empty($_GET['password']) || $league['password'] != sanitize_text_field($_GET['password']))) {
            $joined = self::$victorious->isJoinedContest($league_id);
            if (!$joined['joined']) {
                VIC_Redirect(VICTORIOUS_URL_LOBBY, __('Incorrect password', 'victorious'), true);
            }
        }

        wp_enqueue_script('global.js', VICTORIOUS__PLUGIN_URL_JS . 'global.js');
        wp_enqueue_script('tablesorter.js', VICTORIOUS__PLUGIN_URL_JS . 'tablesorter.js');
        wp_enqueue_style('bootstrap.css', VICTORIOUS__PLUGIN_URL_CSS.'bootstrap.css');
        wp_enqueue_style('style.css', VICTORIOUS__PLUGIN_URL_CSS.'style.css');
        wp_enqueue_style('Material', VICTORIOUS__PLUGIN_URL_CSS.'material_icons.css');
        switch ($league['gameType']) {
            case VICTORIOUS_GAME_TYPE_LIVEDRAFT:
                wp_enqueue_style('live_draft.css', VICTORIOUS__PLUGIN_URL_CSS . 'live_draft.css');
                wp_enqueue_script('jquery.countdown.js', VICTORIOUS__PLUGIN_URL_JS . 'jquery.countdown.js');
                wp_enqueue_script('countdown.min.js', VICTORIOUS__PLUGIN_URL_JS . 'countdown.min.js');
                wp_enqueue_script('livedraft.js', VICTORIOUS__PLUGIN_URL_JS . 'livedraft.js');
                wp_enqueue_script('live_draft_trade_player.js', VICTORIOUS__PLUGIN_URL_JS . 'live_draft_trade_player.js');
                wp_enqueue_script('playerdraft.js', VICTORIOUS__PLUGIN_URL_JS . 'playerdraft.js');
                wp_enqueue_script('loader.js', VICTORIOUS__PLUGIN_URL_JS.'chart_loader.js');
                break;
            case VICTORIOUS_GAME_TYPE_BRACKET:
                wp_enqueue_style('bracket.css', VICTORIOUS__PLUGIN_URL_CSS . 'bracket.css');
                wp_enqueue_script('bracket.js', VICTORIOUS__PLUGIN_URL_JS . 'bracket.js');
                break;
            case VICTORIOUS_GAME_TYPE_GOLFSKIN:
                self::golfskin($league);
                break;
            case VICTORIOUS_GAME_TYPE_ROUNDPICKEM:
                wp_enqueue_script('roundpickem.js', VICTORIOUS__PLUGIN_URL_JS . 'roundpickem.js');
                break;
            case VICTORIOUS_GAME_TYPE_PICKSQUARES:
                wp_enqueue_script('picksquares.js', VICTORIOUS__PLUGIN_URL_JS . 'picksquares.js');
                break;
            case VICTORIOUS_GAME_TYPE_PICKULTIMATE:
                wp_enqueue_script('pickultimate.js', VICTORIOUS__PLUGIN_URL_JS . 'pickultimate.js');
                break;
            case VICTORIOUS_GAME_TYPE_PICKMONEY:
            case VICTORIOUS_GAME_TYPE_PICKTIE:
            case VICTORIOUS_GAME_TYPE_PICKSPREAD:
            case VICTORIOUS_GAME_TYPE_HOWMANYGOALS:
            case VICTORIOUS_GAME_TYPE_BOTHTEAMSTOSCORE:
            case VICTORIOUS_GAME_TYPE_PICKEM:
                wp_enqueue_script('pickem.js', VICTORIOUS__PLUGIN_URL_JS . 'pickem.js');
                break;
            case VICTORIOUS_GAME_TYPE_GOLIATH:
                wp_enqueue_script('goliath.js', VICTORIOUS__PLUGIN_URL_JS . 'goliath.js');
                break;
            case VICTORIOUS_GAME_TYPE_MINIGOLIATH:
                wp_enqueue_script('minigoliath.js', VICTORIOUS__PLUGIN_URL_JS . 'minigoliath.js');
                break;
            case VICTORIOUS_GAME_TYPE_SURVIVAL:
                wp_enqueue_script('survival.js', VICTORIOUS__PLUGIN_URL_JS . 'survival.js');
                break;
            case VICTORIOUS_GAME_TYPE_TEAMDRAFT:
                wp_enqueue_script('teamdraft.js', VICTORIOUS__PLUGIN_URL_JS . 'teamdraft.js');
                break;
            case VICTORIOUS_GAME_TYPE_SPORTBOOK:
                wp_enqueue_script('sportbook.js', VICTORIOUS__PLUGIN_URL_JS . 'sportbook.js');
                break;
            /* case VICTORIOUS_GAME_TYPE_PLAYERDRAFT:
              wp_enqueue_script('loader.js', 'https://www.gstatic.com/charts/loader.js');
              wp_enqueue_script('playerdraftnew.js', VICTORIOUS__PLUGIN_URL_JS.'playerdraftnew.js');
              break; */
            case VICTORIOUS_GAME_TYPE_PORTFOLIO:
                wp_enqueue_script('portfolio.js', VICTORIOUS__PLUGIN_URL_JS . 'portfolio.js');
                break;
            case VICTORIOUS_GAME_TYPE_OLDDRAFT:
                wp_enqueue_script('olddraft.js', VICTORIOUS__PLUGIN_URL_JS . 'olddraft.js');
                break;
            case VICTORIOUS_GAME_TYPE_NFL_PLAYOFF:
                wp_enqueue_script('playoff.js', VICTORIOUS__PLUGIN_URL_JS . 'playoff.js');
                wp_enqueue_script('jquery.countdown.js', VICTORIOUS__PLUGIN_URL_JS . 'jquery.countdown.js');
                wp_enqueue_script('countdown.min.js', VICTORIOUS__PLUGIN_URL_JS . 'countdown.min.js');
                break;
            default :
                wp_enqueue_script('loader.js', VICTORIOUS__PLUGIN_URL_JS.'chart_loader.js');
                if ($league['is_racing']) {
                    wp_enqueue_script('playerdraft.js', VICTORIOUS__PLUGIN_URL_JS . 'playerdraft.js');
                    wp_enqueue_script('playerdraft_init.js', VICTORIOUS__PLUGIN_URL_JS . 'playerdraft_init.js');
                }
                /*else if ($league['is_horse']) {
                    wp_enqueue_script('playerdraft.js', VICTORIOUS__PLUGIN_URL_JS . 'playerdraft.js');
                    wp_enqueue_script('playerdraft_init.js', VICTORIOUS__PLUGIN_URL_JS . 'playerdraft_init.js');
                }*/
                else if ($league['is_motocross']) {
                    wp_enqueue_script('playerdraft.js', VICTORIOUS__PLUGIN_URL_JS . 'playerdraft.js');
                    wp_enqueue_script('playerdraft_init.js', VICTORIOUS__PLUGIN_URL_JS . 'playerdraft_init.js');
                }
                else if ($league['is_mixing']) {
                    wp_enqueue_script('playerdraft.js', VICTORIOUS__PLUGIN_URL_JS . 'playerdraft.js');
                    wp_enqueue_script('playerdraft_init.js', VICTORIOUS__PLUGIN_URL_JS . 'playerdraft_init.js');
                }
                else {
                    wp_enqueue_script('playerdraftnew.js', VICTORIOUS__PLUGIN_URL_JS . 'playerdraftnew.js');
                    //wp_enqueue_script('playerdraft.js', VICTORIOUS__PLUGIN_URL_JS . 'playerdraft.js');
                    //wp_enqueue_script('playerdraft_init.js', VICTORIOUS__PLUGIN_URL_JS . 'playerdraft_init.js');
                }
        }
    }

    public static function game() {
        if (!in_the_loop()) {
            return;
        }

        $league_id = sanitize_text_field($_GET['league_id']);
        $entry_number = !empty($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;

        //league
        $league = self::$victorious->getLeagueDetail($league_id);
        $league = $league[0];
        if ($league == null) {
            VIC_Redirect(VICTORIOUS_URL_LOBBY, __('Contest does not exist', 'victorious'), true);
        }
        if ($league['multi_entry'] && empty($_GET['num'])) {
            VIC_Redirect(VICTORIOUS_URL_LOBBY, __('Invalid entry number', 'victorious'), true);
        }

        if (!self::$payment->isUserEnoughMoneyToJoin($league['entry_fee'], $league_id, $entry_number, $league['multi_entry'], $league['balance_type_id'])) {
            VIC_Redirect(VICTORIOUS_URL_ADD_FUNDS, __('You do not have enough funds to enter. Please add funds', 'victorious'), true);
        }
        self::validateForAllGameType($league, $entry_number);
        switch ($league['gameType']) {
            case VICTORIOUS_GAME_TYPE_LIVEDRAFT:
                self::livedraft($league);
                break;
            case VICTORIOUS_GAME_TYPE_BRACKET:
                self::bracket($league);
                break;
            case VICTORIOUS_GAME_TYPE_GOLFSKIN:
                self::golfskin($league);
                break;
            case VICTORIOUS_GAME_TYPE_ROUNDPICKEM:
                self::roundPickem($league);
                break;
            case VICTORIOUS_GAME_TYPE_PICKSQUARES:
                self::picksquares($league);
                break;
            case VICTORIOUS_GAME_TYPE_PICKULTIMATE:
                self::pickultimate($league);
                break;
            case VICTORIOUS_GAME_TYPE_PICKMONEY:
            case VICTORIOUS_GAME_TYPE_PICKTIE:
            case VICTORIOUS_GAME_TYPE_PICKSPREAD:
            case VICTORIOUS_GAME_TYPE_HOWMANYGOALS:
            case VICTORIOUS_GAME_TYPE_BOTHTEAMSTOSCORE:
            case VICTORIOUS_GAME_TYPE_PICKEM:
                self::pickem($league);
                break;
            case VICTORIOUS_GAME_TYPE_GOLIATH:
                self::validateEnterGoliath($league, $entry_number);
                self::goliath($league);
                break;
            case VICTORIOUS_GAME_TYPE_MINIGOLIATH:
                self::validateEnterMiniGoliath($league, $entry_number);
                self::minigoliath($league);
                break;
            case VICTORIOUS_GAME_TYPE_SURVIVAL:
                self::validateEnterSurvival($league, $entry_number);
                self::survival($league);
                break;
            case VICTORIOUS_GAME_TYPE_TEAMDRAFT:
                self::teamdraft($league);
                break;
            case VICTORIOUS_GAME_TYPE_SPORTBOOK:
                self::sportbook($league);
                break;
            /*case VICTORIOUS_GAME_TYPE_PLAYERDRAFT:
                self::playerdraft($league);
                break;*/
            case VICTORIOUS_GAME_TYPE_PORTFOLIO:
                self::portfolio($league);
                break;
            case VICTORIOUS_GAME_TYPE_OLDDRAFT:
                self::olddraft($league);
                break;
            case VICTORIOUS_GAME_TYPE_NFL_PLAYOFF:
                self::playoff($league);
                break;
            default :
                if ($league['is_racing']) {

                    self::racing($league);
                }
                /*else if ($league['is_horse']) {
                    self::horse($league);
                }*/
                else if ($league['is_motocross']) {
                    self::motocross($league);
                }
                else if ($league['is_mixing']) {
                    self::mixing($league);
                }
                else {
                    self::playerdraft($league);
                }
        }
    }

    private static function validateForAllGameType($league, $entry_number) {
        if (!is_user_logged_in()) {
            return;
        }
        if ($league == null) {
            VIC_Redirect(VICTORIOUS_URL_LOBBY, __('Contest does not exist', 'victorious'), true);
        }
        $league_id = $league['leagueID'];

        $data = self::$victorious->validateForAllGameType($league_id, $entry_number);
        if (!$data['success']) {
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
            VIC_Redirect(VICTORIOUS_URL_LOBBY, $message, true);
        }

        //validate for make bet
        $makeBet = true;
        if ($league['entry_fee'] > 0) {
            $makeBet = self::$payment->isMakeBetForLeague($league_id, $entry_number);
            if (!$makeBet && !self::$payment->isUserEnoughMoneyToJoin($league['entry_fee'], $league_id, $entry_number, $league['multi_entry'], $league['balance_type_id'])) {
                VIC_Redirect(VICTORIOUS_URL_LOBBY, __('You do not have enough funds to enter. Please add funds', 'victorious'), true);
            }
        }
    }

    private static function golfskin($league) {
        $leagueId = pageSegment(3);
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 0;
        $orgId = $league['organizationID'];

        $data = self::$victorious->getEnterGameData($leagueId, $entry_number, 0, $orgId);
        $league = self::$victorious->parseLeagueData($league);
        $aPool = $data['pool'];
        $aFights = $data['fights'];
        $aRounds = $data['rounds'];
        $aPositions = $data['positions'];
        $aLineups = $data['lineup'];
        $aTeams = $data['teams'];
        $indicators = $data['indicators'];
        $playerIdPicks = $data['playerIdPicks'];
        $otherLeagues = $data['otherLeagues'];
        $aPlayers = $data['players'];
        $aPlayers = self::$players->parsePlayersData($aPlayers);
        $aPlayerGolfSkin = $data['golfskinPlayers'];

        $total_money = 0;
        $entry_fee = $league['entry_fee'];
        $is_entry_fee = 0;
        if ($entry_fee > 0) {
            $is_entry_fee = 1;
        }
        if (!is_array($aPlayerGolfSkin)) {
            $tmp_array = json_decode($aPlayerGolfSkin, true);
            foreach ($tmp_array as $players) {
                $total_money += count($players);
            }
        }
        if (is_array($aPlayerGolfSkin)) {
            $aPlayerGolfSkin = json_encode($aPlayerGolfSkin);
        }

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'gametype/golfskin.php';
    }

    private static function motocross($league) {
        $leagueId = pageSegment(3);
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 0;
        $orgId = $league['organizationID'];

        $data = self::$victorious->getEnterGameData($leagueId, $entry_number, 0, $orgId);
        $league = self::$victorious->parseLeagueData($league);
        $aPool = $data['pool'];
        $aFights = $data['fights'];
        $aRounds = $data['rounds'];
        $aPositions = $data['positions'];
        $aLineups = $data['lineup'];
        $aTeams = $data['teams'];
        $indicators = $data['indicators'];
        $playerIdPicks = $data['playerIdPicks'];
        $otherLeagues = $data['otherLeagues'];
        $aPlayers = $data['players'];
        $aPlayers = self::$players->parsePlayersData($aPlayers);

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'gametype/motocross.php';
    }

    private static function playerdraft_old($league) {
        $leagueId = pageSegment(3);
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 0;
        $orgId = $league['organizationID'];
        #action 1: edit injury player
        #action 2: edit player each week
        $action = 0;
        if (isset($_GET['action']) && !empty($_GET['action'])) {
            $action = sanitize_text_field($_GET['action']);
        }
        $data = self::$victorious->getEnterGameData($leagueId, $entry_number, $action, $orgId);

        $is_soccer = false;
        $is_soccer_flex = false;
        $is_soccer_field = false;
        $is_position_step = false;
        $is_lineup_scoccer_field = false;
        $privater_id = "";

        $league = self::$victorious->parseLeagueData($league);
        $aPool = $data['pool'];
        $aFights = $data['fights'];
        $aRounds = $data['rounds'];
        $aPositions = $data['positions'];
        $aLineups = $data['lineup'];
        $aTeams = $data['teams'];
        $indicators = $data['indicators'];
        $playerIdPicks = $data['playerIdPicks'];
        $otherLeagues = $data['otherLeagues'];
        $aPlayers = $data['players'];
        $aPlayers = self::$players->parsePlayersData($aPlayers);
        $is_soccer = $data['is_soccer'];
        $is_soccer_flex = $data['is_soccer_flex'];
        $is_soccer_field = $data['is_soccer_field'];
        $privater_id = isset($data['privateer_id']) ? $data['privateer_id'] : $privater_id;
        $is_lineup_scoccer_field = isset($data['is_soccer']) && $data['is_lineup_scoccer_field'] ? $data['is_soccer'] : $is_lineup_scoccer_field;
        $is_position_step = isset($data['is_position_step']) ? $data['is_position_step'] : $is_position_step;

        $allow_change_bg = self::$victorious->checkAllowChangeBackground();
        $allow_change_bg = $allow_change_bg['is_allow'];
        if ($allow_change_bg) {
            $newPlayers = self::eliminateTeamOponent($data['fights'], $aPlayers);
            $aPlayers = $newPlayers;
        }
        $extra_positions = array();
        if ($is_soccer_flex) {
            foreach ($aPositions as $position) {
                if ($position['is_extra']) {
                    $extra_positions[] = array('position_id' => $position['id'], 'quantity' => $position['default_quantity']);
                }
            }
        }

        if (($is_lineup_scoccer_field || $is_position_step) && $aLineups != null && is_array($aLineups)) {
            foreach ($aLineups as $pos) {
                if (!$pos['name'] || !$pos['enable']) {
                    continue;
                }
                $list_postion_soccer[$pos['id']] = array('default_quantity' => $pos['total'], 'current_quantity' => 0);
            }
        }

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'gametype/playerdraft.php';
    }

    public static function eliminateTeamOponent($aFights, $aPlayers) {
        global $wpdb;
        $tb_user_teams = $wpdb->prefix . 'user_teams';
        $user_id = VIC_GetUserId();
        if (empty($user_id)) {
            return $aPlayers;
        }
        if (empty($aPlayers)) {
            return $aPlayers;
        }
        $data = $wpdb->get_row("SELECT * FROM $tb_user_teams WHERE user_id= $user_id", ARRAY_A);
        if (empty($data)) {
            return $aPlayers;
        }
        $team_id = $data['team_id'];
        $aNewPlayers = array();
        foreach ($aPlayers as $k => $player) {
            if ($player['teamID1'] == $team_id || $player['teamID2'] == $team_id) {
                if ($player['team_id'] != $team_id) {
                    unset($aPlayers[$k]);
                }
            }
        }
        foreach ($aPlayers as $k => $player) {
            $aNewPlayers[] = $player;
        }
        return $aNewPlayers;
    }

    public static function submitPicks() {
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $league_id = sanitize_text_field($_POST['leagueID']);
        $action = 0;
        if (isset($_POST['action']) && !empty($_POST['action'])) {
            $action = sanitize_text_field($_POST['action']);
        }

        //check valid data
        $valid = self::validData();

        //get league data
        $league = self::$victorious->getLeagueDetail($league_id);

        //validate
        if ($league == null) {
            VIC_Redirect(VICTORIOUS_URL_CREATE_CONTEST, __('Contest does not exist', 'victorious'), true);
            exit;
        }
        $league = $league [0];
        $makeBet = self::$payment->isMakeBetForLeague($league['leagueID'], $entry_number);
        if (!$makeBet && !self::$payment->isUserEnoughMoneyToJoin($league['entry_fee'], $league_id, $entry_number, $league['multi_entry'], $league['balance_type_id'])) {
            VIC_Redirect(VICTORIOUS_URL_ADD_FUNDS, __('You do not have enough funds to enter. Please add funds', 'victorious'), true);
            exit;
        }


        //insert pick
        $data = array('leagueID' => sanitize_text_field($_POST['leagueID']),
            'player_id' => sanitize_text_field($_POST['player_id']),
            'entry_number' => $entry_number,
            'player_position' => sanitize_text_field($_POST['player_position']),
            'injury' => isset($_POST['injury']) ? sanitize_text_field($_POST['injury']) : "",
            'action' => $action);
        $entry_number = self::$victorious->insertPlayerPicks($data);

        if (isset($entry_number['error'])) {
            switch ($entry_number['error']) {
                case 1:
                    VIC_Redirect(VICTORIOUS_URL_MY_LIVE_ENTRIES, __('Your time to select a player has expired.  Your selection was randomly selected by the system', 'victorious'), true);
                    exit;
                    break;
                case 2:
                    VIC_Redirect(VICTORIOUS_URL_MY_LIVE_ENTRIES, __('You have reached the maximum changes you can make per week', 'victorious'), true);
                    exit;
                    break;
            }
        }
        if ($entry_number > 0) {
            if ($makeBet == false) {
                //decrease user money
                self::$payment->updateUserBalance($league['entry_fee'], true, $league['leagueID'], null, $league['balance_type_id']);

                //add to history
                $aUser = self::$payment->getUserData();
                $params = array(
                    'amount' => $league['entry_fee'],
                    'leagueID' => $league['leagueID'],
                    'new_balance' => $aUser['balance'],
                    'operation' => 'DEDUCT',
                    'type' => 'MAKE_BET',
                    'entry_number' => $entry_number,
                    'status' => 'completed'
                );
                self::$payment->addFundhistory($params);
            }

            //trade player
            if ($makeBet !== false && $league['entry_fee'] > 0 && $valid != 9 && $league["trade_player"] && $league["is_live"] && !$league["trade_pick"]) {
                $cost = $league['entry_fee'] / 2;
                //decrease user money
                self::$payment->updateUserBalance($cost, true, $league['leagueID'], null, $league['balance_type_id']);

                //add to history
                $aUser = self::$payment->getUserData();
                $params = array(
                    'amount' => $cost,
                    'leagueID' => $league['leagueID'],
                    'new_balance' => $aUser['balance'],
                    'operation' => 'DEDUCT',
                    'type' => 'MAKE_BET',
                    'entry_number' => $entry_number,
                    'reason' => 'Change player',
                    'status' => 'completed'
                );
                self::$payment->addFundhistory($params);
            }

            if (!$league["trade_player"]) {
                $_SESSION['showInviteFriends' . $league['leagueID']] = true;
            }
            self::$victorious->sendUserPickEmail(sanitize_text_field($_POST['leagueID']), VIC_GetUserId(), $entry_number);

            //buddy press integration
            if ($league != null) {
                self::$victorious->addEnterContestActivity($league, VIC_GetUserId());
            }

            //push notification for user who joined contest
            $victorious_firebase_apikey = get_option('victorious_firebase_apikey');
            $victorious_firebase_senderid = get_option('victorious_firebase_senderid');
            if (!empty($victorious_firebase_apikey) && !empty($victorious_firebase_senderid)) {
                $user_ids = self::$victorious->getUserIdsJoinContest($league['leagueID'], VIC_GetUserId());
                if (!empty($user_ids['user_ids'])) {
                    self::$victorious->sendNotificationUserJoinContest($user_ids['user_ids'], VIC_GetUserId(), $league['name'], VICTORIOUS_URL_CONTEST . $league['leagueID'] . "?num=" . $entry_number);
                }
            }

            if ($action == 1) {
                VIC_Redirect(VICTORIOUS_URL_MY_LIVE_ENTRIES, null, true);
                exit;
            }
            VIC_Redirect(VICTORIOUS_URL_ENTRY . sanitize_text_field($_POST['leagueID']) . "/?num=" . $entry_number, null, true);
        }
        VIC_Redirect(VICTORIOUS_URL_GAME . sanitize_text_field($_POST['leagueID']), __('Something went wrong! Please try again.', 'victorious'), true);
    }

    private static function validData() {
        $leagueID = sanitize_text_field($_POST['leagueID']);
        $entry_number = sanitize_text_field($_POST['entry_number']);
        //league
        $league = self::$victorious->getLeagueDetail($leagueID);

        //valid
        $isMixing = $league[0]['is_mixing'];

        if ($isMixing) { // mixing game
            $valid = self::$victorious->validEnterMixingPlayerdraft(sanitize_text_field($_POST['leagueID']), sanitize_text_field($_POST['player_id']));
        }
        else { // single game
            $valid = self::$victorious->validEnterPlayerdraft(sanitize_text_field($_POST['leagueID']), sanitize_text_field($_POST['player_id']));
        }

        switch ($valid) {
            case 2:
                VIC_Redirect(VICTORIOUS_URL_CREATE_CONTEST, __("This contest has ended", 'victorious'), true);
                break;
            case 3:
                VIC_Redirect(VICTORIOUS_URL_CREATE_CONTEST, __('Contest does not exist', 'victorious'), true);
                break;
            case 4:
                VIC_Redirect(VICTORIOUS_URL_CREATE_CONTEST, __('Sorry! This contest is full', 'victorious'), true);
                break;
            case 5:
                VIC_Redirect(VICTORIOUS_URL_GAME . $leagueID, __("Your team has exceeded this game's salary cap. Please change your team so it fits under the salary cap before entering", 'victorious'), true);
                break;
            case 6:
                VIC_Redirect(VICTORIOUS_URL_GAME . $leagueID, __("Please select a player for each position", 'victorious'), true);
                break;
            case 7:
                VIC_Redirect(VICTORIOUS_URL_GAME . $leagueID, __("You can not pick players of started game", 'victorious'), true);
                break;
            case 8:
                VIC_Redirect(VICTORIOUS_URL_GAME . $leagueID, __("You are only able to change one player", 'victorious'), true);
                break;
            case 10:
                VIC_Redirect(VICTORIOUS_URL_GAME . $leagueID, __("One of these players already picked by other users. Please select other players.", 'victorious'), true);
                break;
            case 12:
                VIC_Redirect(VICTORIOUS_URL_LOBBY, __('Only users who entered 50 contests or less can join this contest.', 'victorious'), true);
                break;
            case 13:
                VIC_Redirect(VICTORIOUS_URL_GAME . $leagueID, __('Your picks has exceeded player limitation per team.', 'victorious'), true);
                break;
            case 14:
                VIC_Redirect(VICTORIOUS_URL_LOBBY, __('You reached maximum number of multi entries.', 'victorious'), true);
                break;
        }

        if (!self::$payment->isUserEnoughMoneyToJoin($league[0]['entry_fee'], $leagueID, $entry_number, $league[0]['multi_entry'], $league[0]['balance_type'])) {
            VIC_Redirect(VICTORIOUS_URL_ADD_FUNDS, __('You do not have enough funds to enter. Please add funds', 'victorious'), true);
        }
        if ($valid != 9) {
            if ($valid == 11) { // for trade player
                if (!self::$payment->isUserEnoughMoneyToChangePlayer($league[0]['entry_fee'] / 2)) {
                    VIC_Redirect(VICTORIOUS_URL_ADD_FUNDS, __('You do not have enough funds to change players. Please add funds', 'victorious'), true);
                }
            }
        }
        return $valid;
    }

    private static function golfskinSubmitPicks() {
        self::golfskinValidata();
        $entry_number = sanitize_text_field($_POST['entry_number']);
        $data = array('leagueID' => sanitize_text_field($_POST['leagueID']),
            'players' => sanitize_text_field($_POST['players']),
            'poolID' => sanitize_text_field($_POST['poolID']),
            'entry_number' => $entry_number);
        $entry_number = self::$victorious->insertGolfSkinPlayerPicks($data);

        if ($entry_number > 0) {
            $league = self::$victorious->getLeagueDetail(sanitize_text_field($_POST['leagueID']));
            $league = $league [0];
            $makeBet = self::$payment->isMakeBetForLeague($league['leagueID'], $entry_number);
            $total_money = sanitize_text_field($_POST['total_money']);

            if ($makeBet == false) {
                //decrease user money
                self::$payment->updateUserBalance($total_money, true, $league['leagueID'], null, $league['balance_type_id']);
                //add to history
                $aUser = self::$payment->getUserData();
                $params = array(
                    'amount' => $total_money,
                    'leagueID' => $league['leagueID'],
                    'new_balance' => $aUser['balance'],
                    'operation' => 'DEDUCT',
                    'type' => 'MAKE_BET',
                    'entry_number' => $entry_number,
                    'status' => 'completed'
                );
                self::$payment->addFundhistory($params);
            }
            else {
                // update
                $diff_balance = $makeBet['amount'] - $total_money;
                if ($diff_balance != 0) {
                    //update balance
                    $status = ($diff_balance > 0) ? false : true;
                    self::$payment->updateUserBalance($diff_balance, $status, $league['leagueID'], null, $league['balance_type_id']);


                    $payout_percentage = get_option('victorious_winner_percent');
                    $site_profit = $total_money * (100 - $payout_percentage) / 100;
                    $aValues['site_profit'] = $site_profit;
                    self::$payment->updateFundhistory($makeBet['fundshistoryID'], $aValues, null, 'DEDUCT');
                }
            }
            $_SESSION['showInviteFriends' . $league['leagueID']] = true;
            $_SESSION['userPicksInfo'] = array(sanitize_text_field($_POST['leagueID']), get_current_user_id(), $entry_number);
            VIC_Redirect(VICTORIOUS_URL_ENTRY . sanitize_text_field($_POST['leagueID']) . "/?num=" . $entry_number, null, true);
        }
        VIC_Redirect(VICTORIOUS_URL_GAME . sanitize_text_field($_POST['leagueID']), __('Something went wrong! Please try again.', 'victorious'), true);
    }

    private static function golfskinValidata() {
        //league
        $league = self::$victorious->getLeagueDetail(sanitize_text_field($_POST['leagueID']));
        //valid
        $valid = self::$victorious->validEnterGolfSkin(sanitize_text_field($_POST['leagueID']), sanitize_text_field($_POST['player_id']));

        switch ($valid) {
            case 2:
                VIC_Redirect(VICTORIOUS_URL_CREATE_CONTEST, __("This contest has ended", 'victorious'), true);
                break;
            case 3:
                VIC_Redirect(VICTORIOUS_URL_CREATE_CONTEST, __('Contest does not exist', 'victorious'), true);
                break;
            case 4:
                VIC_Redirect(VICTORIOUS_URL_CREATE_CONTEST, __('Sorry! This contest is full', 'victorious'), true);
                break;
            case 5:
                VIC_Redirect(VICTORIOUS_URL_GAME . $league[0]['leagueID'], __("Please select at least one player for each round", 'victorious'), true);
                break;
        }
    }

    public static function liveDraftParseTradeString($list_players, $players) {

        foreach ($list_players as $player_id) {
            $player_name = $players[$player_id]['player_name'];
            $position_name = $players[$player_id]['position_name'];
            $str[] = "($position_name) $player_name";
        }
        return implode(',', $str);
    }

    //////////////////////live draft//////////////////////
    private static function livedraft($league) {
        $leagueId = pageSegment(3);
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 0;
        #action 1: edit injury player
        #action 2: edit player each week
        $action = 0;
        if (isset($_GET['action']) && !empty($_GET['action'])) {
            $action = sanitize_text_field($_GET['action']);
        }

        $data = self::$victorious->getEnterLiveDraftGameData($leagueId, $entry_number, $action);

        switch ($data) {
            case 2:
                VIC_Redirect(VICTORIOUS_URL_CREATE_CONTEST, __('Contest does not exist', 'victorious'), true);
                break;
            case 3:
                VIC_Redirect(VICTORIOUS_URL_CONTEST . $leagueId, null, true);
                break;
            case 4:
                VIC_Redirect(VICTORIOUS_URL_CREATE_CONTEST, __('Sorry! This contest was full', 'victorious'), true);
                break;
            case 5:
                if (!self::$payment->isUserEnoughMoneyToJoin($league['entry_fee'], $leagueId, $entry_number, $league['multi_entry'], $league['balance_type_id'])) {
                    VIC_Redirect(VICTORIOUS_URL_LOBBY, __('You do not have enough funds to enter. Please add funds.', 'victorious'), true);
                }
                $result = self::$victorious->joinLiveDraftContest($leagueId);
                if ($result == 1) {
                    //decrease user money
                    self::$payment->updateUserBalance($league['entry_fee'], true, $league['leagueID'], null, $league['balance_type_id']);

                    //add to history
                    $aUser = self::$payment->getUserData();
                    $params = array(
                        'amount' => $league['entry_fee'],
                        'leagueID' => $league['leagueID'],
                        'new_balance' => $aUser['balance'],
                        'operation' => 'DEDUCT',
                        'type' => 'MAKE_BET',
                        'entry_number' => $entry_number,
                        'status' => 'completed'
                    );
                    self::$payment->addFundhistory($params);
                    VIC_Redirect(VICTORIOUS_URL_LOBBY, __('you have successfully joined the contest - please log in at draft time to join the draft.', 'victorious'), true);
                }
                else {
                    VIC_Redirect(VICTORIOUS_URL_LOBBY, __('Something went wrong! Please try again.', 'victorious'), true);
                }
                break;
            case 6:
                VIC_Redirect(VICTORIOUS_URL_LOBBY, __('You have already joined this contest.', 'victorious'), true);
                break;
            case 7:
                VIC_Redirect(VICTORIOUS_URL_LOBBY, __('You are not allowed to draft player at this time.', 'victorious'), true);
                break;
            case 8:
                VIC_Redirect(VICTORIOUS_URL_LOBBY, __('Draft time has finished.', 'victorious'), true);
                break;
            case 9:
                VIC_Redirect(VICTORIOUS_URL_LOBBY, __('You can not change bench players at this time.', 'victorious'), true);
                break;
        }

        $league = $data['league'];
        $aFights = $data['fights'];
        $aRounds = $data['rounds'];
        $aPositions = $data['positions'];
        $aLineups = $data['lineup'];
        $aTeams = $data['teams'];
        $indicators = $data['indicators'];
        $playerIdPicks = $data['playerIdPicks'];
        $aPlayers = $data['players'];
        $aPlayers = self::$players->parsePlayersData($aPlayers);
        $limit_players = $data['limit_players'];
        $edit_injury_players = isset($data['edit_injury_players']) ? $data['edit_injury_players'] : false;
        $list_injury_players = isset($data['injury_players']) ? $data['injury_players'] : array();
        $allow_waiver_wire = $data['allow_waiver_wire'];
        $time_remaning = $data['time_remaning'];
        $except_player_ids = $data['except_player_ids'];
        $allow_live_draft = $data['allow_live_draft'];

        $allow_injury_position = array();
        if ($edit_injury_players) {
            foreach ($aPlayers as $player) {
                if (in_array($player['id'], $playerIdPicks) && $player['indicator_id'] == 1) {
                    $allow_injury_position[] = $player['position_id'];
                }
            }
        }
        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'gametype/livedraft.php';
    }

    public static function liveDraftManageTradeRequest() {
        if (!in_the_loop()) {
            return;
        }
        $leagueID = sanitize_text_field($_GET['league_id']);
        $entry_number = sanitize_text_field($_GET['entry_number']);

        $data = self::$victorious->liveDraftRequestTradePlayertList($leagueID, $entry_number);
        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'livedraft/live_draft_manage_tradeplayers.php';
    }

    public static function liveDraftTradePlayer() {
        if (!in_the_loop()) {
            return;
        }
        //valid data
        $valid = self::$victorious->liveDraftValidTradePlayer($_GET);
        switch ($valid) {
            case 2:
                VIC_Redirect(VICTORIOUS_URL_MY_LIVE_ENTRIES, __("You cannot change a player with yourself", 'victorious'), true);
                break;
            case 3:
                VIC_Redirect(VICTORIOUS_URL_MY_LIVE_ENTRIES, __("The requested user does not exist", 'victorious'), true);
                break;
        }

        //get data
        $target_id = sanitize_text_field($_GET['trade_target_id']);
        $contest_id = sanitize_text_field($_GET['contest_id']);
        $entry_number = sanitize_text_field($_GET['entry_number']);
        $target_entry = sanitize_text_field($_GET['target_entry']);
        $profiles_user = self::$victorious->get_user_info(VIC_GetUserId());
        $profile_target = self::$victorious->get_user_info($target_id);
        $data = self::$victorious->liveDraftGetDataTradePlayer($target_id, $contest_id, $entry_number, $target_entry);

        $user_players = self::$players->parsePlayersData($data['user']);
        $target_players = self::$players->parsePlayersData($data['target']);
        $pool = $data['pool'];
        $is_requested = $data['is_requested'];

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'livedraft/live_draft_tradeplayers.php';
    }

    //////////////////////end live draft//////////////////////
    //////////////////////racing//////////////////////
    private static function racing($league) {
        $leagueId = pageSegment(3);
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 0;
        $balance_type = $league['balance_type'];

        $data = self::$victorious->getEnterRacingGameData($leagueId, $entry_number);
        $aPool = $data['pool'];
        $aRounds = $data['rounds'];
        $aPositions = $data['positions'];
        $aLineups = $data['lineup'];
        $indicators = $data['indicators'];
        $playerIdPicks = $data['playerIdPicks'];
        $otherLeagues = $data['otherLeagues'];
        $aPlayers = $data['players'];
        $aPlayers = self::$players->parsePlayersData($aPlayers);

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'gametype/racing.php';
    }

    //////////////////////end racing//////////////////////
    //////////////////////horse//////////////////////
    private static function horse($league) {
        $leagueId = pageSegment(3);
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 0;
        $orgId = $league['organizationID'];
        $balance_type = $league['balance_type'];

        $data = self::$victorious->getEnterGameData($leagueId, $entry_number, 0, $orgId);
        $league = self::$victorious->parseLeagueData($league);
        $aPool = $data['pool'];
        $aFights = $data['fights'];
        $aRounds = $data['rounds'];
        $aPositions = $data['positions'];
        $aLineups = $data['lineup'];
        $aTeams = $data['teams'];
        $indicators = $data['indicators'];
        $playerIdPicks = $data['playerIdPicks'];
        $otherLeagues = $data['otherLeagues'];
        $aPlayers = $data['players'];
        $aPlayers = self::$players->parsePlayersData($aPlayers);

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'gametype/horse.php';
    }

    //////////////////////end horse//////////////////////
    //////////////////////mixing sport//////////////////////
    private static function mixing($league) {
        $leagueId = pageSegment(3);
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 0;
        $orgId = $league['organizationID'];
        $balance_type = $league['balance_type'];

        $data = self::$victorious->getEnterMixingGameData($leagueId, $entry_number);

        $league = self::$victorious->parseLeagueData($league);
        $aPool = $data['pool'];
        $aAllFights = $data['fights'];
        $aRounds = $data['rounds'];
        $aPositions = $data['positions'];
        $aLineups = $data['lineup'];
        $aTeams = $data['teams'];
        $indicators = $data['indicators'];
        $playerIdPicks = $data['playerIdPicks'];
        $otherLeagues = $data['otherLeagues'];
        $aPlayers = $data['players'];
        $aSports = $data['aSports'];
        $aPlayers = self::$players->parseMixingPlayersData($aPlayers);

        // count salary
        $salary_remaining = array();
        ksort($aSports);
        ksort($aAllFights);
        $aSportKeys = array_keys($aSports);
        $first_sports_id = $aSportKeys[0];
        foreach ($aSportKeys as $sport_id) {
            $salary = 0;
            foreach ($aPool as $pool) {
                if ($pool['organization'] == $sport_id) {
                    $salary = $pool['salary_remaining'];
                    break;
                }
            }
            $salary_remaining[$sport_id] = round($salary);
        }
        $first_salary = $salary_remaining[$first_sports_id];
        $salary_remaining = json_encode($salary_remaining);

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'gametype/mixing.php';
    }

    //////////////////////end mixing sport//////////////////////
    //////////////////////bracket//////////////////////
    private static function bracket($league) {
        //get prams
        $entry_number = !empty($_POST['entry_number']) ? sanitize_text_field($_POST['entry_number'])  : 1;

        //load data
        $data = self::$victorious->getBracketGame($league['leagueID'], $entry_number);
        $team_groups = $data['team_groups'];
        $my_picks = $data['my_picks'];
        list($group_left, $group_right) = self::$victorious->bracketGroupTeam($team_groups);

        //view
        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'gametype/bracket.php';
    }

    //////////////////////end bracket//////////////////////
    //////////////////////round pick em//////////////////////
    private static function validateEnterPickContest($league) {
        $leagueID = pageSegment(3);
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 0;
        $live_entry = isset($_GET['liveentry']) ? sanitize_text_field($_GET['liveentry']) : 0;

        $value = self::$victorious->validEnterNormalGame($leagueID);
        switch ($value) {
            case 3:
                VIC_Redirect(VICTORIOUS_URL_CONTEST . $leagueID, null, true);
                break;
            case 5:
                VIC_Redirect(VICTORIOUS_URL_GAME . $leagueID . '/?num=' . $entry_number, null, true);
                break;
            case 6:
                VIC_Redirect(VICTORIOUS_URL_CREATE_CONTEST, __('You can not edit started game', 'victorious'), true);
                break;
            case 7:
                VIC_Redirect(VICTORIOUS_URL_GAME . $leagueID . '/?num=' . $entry_number, null, true);
                break;
        }
        if (!self::$payment->isUserEnoughMoneyToJoin($league['entry_fee'], $leagueID, $entry_number, $league['multi_entry'], $league['balance_type_id'])) {
            VIC_Redirect(VICTORIOUS_URL_ADD_FUNDS, __('You do not have enough funds to enter. Please add funds', 'victorious'), true);
        }
        else if (isset($league['gameType']) && $league['gameType'] == 'PICKSQUARES') {
            VIC_Redirect(VICTORIOUS_URL_GAME . $leagueID . '/?num=' . $entry_number, null, true);
        }
    }

    private static function roundPickem($league) {
        $leagueID = pageSegment(3);
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 0;
        $balance_type = $league['balance_type'];

        $data = self::$victorious->getRoundPickemContest($leagueID, $entry_number);
        $fights = $data['fights'];
        $predict_matches = $data['predict_matches'];
        $current_week = $data['current_week'];

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'gametype/roundpickem.php';
    }

    //////////////////////end round pick em//////////////////////
    //////////////////////pick em//////////////////////
    private static function pickem($league) {
        $leagueID = pageSegment(3);
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 0;
        $balance_type = $league['balance_type'];

        $data = self::$victorious->getEnterNormalGameData($leagueID, $entry_number);
        $aLeague = $data['league'];
        $aPool = $data['pool'];
        $otherLeagues = $data['otherLeagues'];
        $aFights = $data['fights'];
        $aMethods = $data['methods'];
        $aMinutes = $data['minutes'];
        $aRounds = $data['rounds'];
        $aPlayers = $data['players'];
        $pickInfo = $data['pickInfo'];
        if ($pickInfo) {
            $pickInfo = $pickInfo['new_tie_breaker'];
            $pickInfo = json_decode($pickInfo, true);
        }

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'pickem/game.php';
    }

    //////////////////////end pick em//////////////////////
    //////////////////////pick squares//////////////////////
    private static function picksquares($league) {
        $league_id = pageSegment(3);
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 0;
        $balance_type = $league['balance_type'];

        $data = self::$victorious->getEnterNormalGameData($league_id, $entry_number);

        $aFights = $data['fights'];
        $aPickSquare = $data['picksquares'];
        $picksquare = '';
        $userSquares = '';
        $payoutPickSquare = json_decode($league['picksquares_payouts'], true);
        if (!empty($aPickSquare)) {
            $picksquare = $aPickSquare['pick_squares'];
            $userSquares = $aPickSquare['user_squares'];
        }
        if (empty($userSquares)) {
            $arr1 = range(0, 9);
            $arr2 = range(0, 9);
            shuffle($arr1);
            shuffle($arr2);
            $userSquares = array();
            $userSquares[] = $arr1;
            $userSquares[] = $arr2;
            $userSquares = json_encode($userSquares);
        }
        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'gametype/picksquares.php';
    }

    //////////////////////end pick squares//////////////////////
    //////////////////////pickultimate//////////////////////
    private static function pickultimate($league) {
        $league_id = pageSegment(3);
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 0;
        $balance_type = $league['balance_type'];

        $data = self::$victorious->getEnterNormalGameData($league_id, $entry_number);
        $otherLeagues = $data['otherLeagues'];
        $fights = $data['fights'];

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'pickultimate/game.php';
    }

    //////////////////////end pickultimate//////////////////////
    //////////////////////goliath//////////////////////
    private static function validateEnterGoliath($league, $entry_number) {
        $data = self::$victorious->validateEnterGoliath($league['leagueID'], $entry_number);
        if (!$data['success']) {
            switch ($data['code']) {
                case 1:
                    $message = __('Contest has ended', 'victorious');
                    break;
                case 2:
                    $message = __('There are live matches now, you can only pick after live matches finish', 'victorious');
                    break;
                case 3:
                    $message = __('Sorry! You were kicked', 'victorious');
                    break;
                case 4:
                    $message = __('Sorry! You cannot re-up at this time', 'victorious');
                    break;
                default :
                    $message = __('Unknown error', 'victorious');
            }
            VIC_Redirect(VICTORIOUS_URL_LOBBY, $message, true);
        }
    }

    private static function goliath($league) {
        $league_id = $league['leagueID'];
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 0;

        $data = self::$victorious->getGoliathContest($league_id, $entry_number);
        $fights = $data['fights'];
        $available_max_pass = $data['survivor_available_max_pass'];
        $available_pass = $data['survivor_available_pass'];

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'gametype/goliath.php';
    }

    //////////////////////end goliath//////////////////////
    //////////////////////minigoliath//////////////////////
    private static function validateEnterMiniGoliath($league, $entry_number) {
        $data = self::$victorious->validateEnterMiniGoliath($league['leagueID'], $entry_number);
        if (!$data['success']) {
            switch ($data['code']) {
                case 1:
                    $message = __('Contest has ended', 'victorious');
                    break;
                case 2:
                    $message = __('There are live matches now, you can only pick after live matches finish', 'victorious');
                    break;
                case 3:
                    $message = __('Sorry! You were kicked', 'victorious');
                    break;
                default :
                    $message = __('Unknown error', 'victorious');
            }
            VIC_Redirect(VICTORIOUS_URL_LOBBY, $message, true);
        }
    }

    private static function minigoliath($league) {
        $league_id = $league['leagueID'];
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 0;

        $data = self::$victorious->getMiniGoliathContest($league_id, $entry_number);
        $fights = $data['fights'];
        $entry_number = $data['entry_number'];

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'gametype/minigoliath.php';
    }

    //////////////////////end minigoliath//////////////////////
    //////////////////////survival//////////////////////
    private static function validateEnterSurvival($league, $entry_number) {
        $data = self::$victorious->validateEnterSurvival($league['leagueID'], $entry_number);
        if (!$data['success']) {
            switch ($data['code']) {
                case 1:
                    $message = __('Contest has ended', 'victorious');
                    break;
                case 2:
                    $message = __('Sorry! You were kicked', 'victorious');
                    break;
                case 3:
                    $message = __('You can not pick at this time', 'victorious');
                    break;
                default :
                    $message = __('Unknown error', 'victorious');
            }
            VIC_Redirect(VICTORIOUS_URL_LOBBY, $message, true);
        }
    }

    private static function survival($league) {
        $league_id = pageSegment(3);
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 0;

        $data = self::$victorious->getSurvivalContest($league_id, $entry_number);
        $fights = $data['fights'];
        $current_week = $data['current_week'];

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'gametype/survival.php';
    }

    //////////////////////end survival//////////////////////
    //////////////////////teamdraft//////////////////////
    private static function teamdraft($league) {
        $league_id = pageSegment(3);
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 0;
        $balance_type = $league['balance_type'];

        $data = self::$victorious->getTeamDraftContest($league_id, $entry_number);
        $fights = $data['fights'];
        $teams = $data['teams'];
        $lineups = $data['lineups'];

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'gametype/teamdraft.php';
    }

    //////////////////////end teamdraft//////////////////////
    //////////////////////playerdraft//////////////////////
    private static function playerdraft($league){
        $league_id = pageSegment(3);
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 0;

        $balance_type = $league['balance_type'];
        $settings = self::$victorious->getGlobalSetting();
        $data = self::$playerdraft->getPlayerDraftContest($league_id, $entry_number);
        $fights = $data['fights'];
        $rounds = $data['rounds'];
        $positions = $data['positions'];
        $lineups = $data['lineups'];
        $indicators = $data['indicators'];

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'playerdraft/game.php';
    }
    //////////////////////end playerdraft//////////////////////
    //////////////////////sportbook//////////////////////
    private static function sportbook($league) {
        if (!VIC_CanStartSportBook()) {
            VIC_Redirect(VICTORIOUS_URL_LOBBY, __("You are not allowed to play at this time", 'victorious'), true);
        }
        $league_id = pageSegment(3);
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 0;

        $balance_type = $league['balance_type'];
        $data = self::$sportbook->getSportbookContest($league_id, $entry_number);
        $fights = $data['fights'];
        $picks = $data['picks'];
        if ($fights == null) {
            VIC_Redirect(VICTORIOUS_URL_LOBBY, __("No game at this time", 'victorious'), true);
        }

        $data = self::$victorious->getSportFirstGame();
        $sports = $data['sport'];

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'sportbook/game.php';
    }

    //////////////////////end sportbook//////////////////////

    //////////////////////portfolio//////////////////////
    private static function portfolio($league){
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 0;
        $balance_type = $league['balance_type'];

        //validate enter
        self::validateEnterPortfolio($league, $entry_number);

        $data = self::$portfolio->getPortfolioContest($league['leagueID'], $entry_number);
        $positions = $data['positions'];
        $lineups = $data['lineups'];
        $categories = $data['categories'];

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'portfolio/game.php';
    }

    private static function validateEnterPortfolio($league, $entry_number) {
        $data = self::$portfolio->validateEnterPortfolio($league['leagueID'], $entry_number);
        if($data['success']){
            return;
        }
        switch ($data['code']) {
            case 1:
                VIC_Redirect(VICTORIOUS_URL_LOBBY, __('You cannot enter this contest at this time', 'victorious'), true);
                break;
        }
    }
    //////////////////////end portfolio//////////////////////

    //////////////////////olddraft//////////////////////
    private static function olddraft($league){
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 0;
        $balance_type = $league['balance_type'];

        //validate enter
        self::validateEnterOldDraft($league, $entry_number);

        $data = self::$olddraft->getOldDraftContest($league['leagueID'], $entry_number);
        $positions = $data['positions'];
        $lineups = $data['lineups'];
        $score = $data['score'];

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'olddraft/game.php';
    }

    private static function validateEnterOldDraft($league, $entry_number) {
        $data = self::$olddraft->validateEnterOldDraft($league['leagueID'], $entry_number);
        if($data['success']){
            return;
        }
        switch ($data['code']) {
            case 1:
                VIC_Redirect(VICTORIOUS_URL_LOBBY, __('You cannot enter this contest at this time', 'victorious'), true);
                break;
        }
    }
    //////////////////////end olddraft//////////////////////
    ///
    //////////////////////playoff//////////////////////
    private static function playoff($league){
        $league_id = pageSegment(3);
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 0;

        //validate enter
        self::validateEnterPlayoff($league, $entry_number);

        $balance_type = $league['balance_type'];
        $settings = self::$victorious->getGlobalSetting();
        $data = self::$playoff->getPlayoffContest($league_id, $entry_number);
        $fights = $data['fights'];
        $positions = $data['positions'];
        $lineups = $data['lineups'];
        $indicators = $data['indicators'];
        $current_week = $data['current_week'];

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'playoff/game.php';
    }

    private static function validateEnterPlayoff($league, $entry_number) {
        $data = self::$playoff->validateEnterPlayoff($league['leagueID'], $entry_number);
        if($data['success']){
            return;
        }
        switch ($data['code']) {
            case 1:
                VIC_Redirect(VICTORIOUS_URL_LOBBY, __('Please join contest first', 'victorious'), true);
                break;
            case 2:
                VIC_Redirect(VICTORIOUS_URL_LOBBY, __('You cannot enter draft room at this time', 'victorious'), true);
                break;
            case 3:
                VIC_Redirect(VICTORIOUS_URL_LOBBY, __('Sorry! You were eliminated.', 'victorious'), true);
                break;
            case 4:
                VIC_Redirect(VICTORIOUS_URL_LOBBY, __('Your draft turn was finished', 'victorious'), true);
                break;
        }
    }
    //////////////////////end playoff//////////////////////
}

?>