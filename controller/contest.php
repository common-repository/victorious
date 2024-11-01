<?php

class VIC_ContestController {

    private static $orgs;
    private static $pools;
    private static $playerposition;
    private static $players;
    private static $teams;
    private static $victorious;
    private static $sportbook;
    private static $uploadphoto;
    private static $portfolio;
    private static $olddraft;
    private static $playoff;

    public function __construct() {
        self::$orgs = new VIC_Organizations();
        self::$pools = new VIC_Pools();
        self::$playerposition = new VIC_PlayerPosition();
        self::$players = new VIC_Players();
        self::$teams = new VIC_Teams();
        self::$victorious = new VIC_Victorious();
        self::$sportbook = new VIC_Sportbook();
        self::$uploadphoto = new VIC_UploadPhoto();
        self::$portfolio = new VIC_Portfolio();
        self::$olddraft = new VIC_OldDraft();
        self::$playoff = new VIC_Playoff();
    }

    public static function process() {
        if (!isset($_GET['league_id'])) {
            $lid = pageSegment(3);
            $queries = array('league_id' => $lid);
            if (!empty($_SERVER['QUERY_STRING'])) {
                $tmp = array();
                parse_str(sanitize_text_field($_SERVER['QUERY_STRING']), $tmp);
                $queries = array_merge($queries, $tmp);
            }
            VIC_Redirect(VICTORIOUS_URL_CONTEST . '?' . http_build_query($queries));
        }
        add_action('wp_enqueue_scripts', array('VIC_ContestController', 'loadCssJs'));
        add_filter('the_content', array('VIC_ContestController', 'contest'));
    }

    public static function loadCssJs() {
        $league_id = pageSegment(3);

        //league
        $league = self::$victorious->getLeagueDetail($league_id);
        if ($league == null) {
            VIC_Redirect(VICTORIOUS_URL_CREATE_CONTEST, __('Contest does not exist', 'victorious'), true);
        }
        $league = $league[0];

        wp_enqueue_script('global.js', VICTORIOUS__PLUGIN_URL_JS . 'global.js');
        wp_enqueue_script('accounting.js', VICTORIOUS__PLUGIN_URL_JS . 'accounting.js');
        wp_enqueue_script('victorious.js', VICTORIOUS__PLUGIN_URL_JS . 'victorious.js');

        wp_enqueue_style('style.css', VICTORIOUS__PLUGIN_URL_CSS.'style.css');
        wp_enqueue_style('bootstrap.css', VICTORIOUS__PLUGIN_URL_CSS.'bootstrap.css');
        wp_enqueue_style('Material', VICTORIOUS__PLUGIN_URL_CSS.'material_icons.css');

        switch ($league['gameType']) {
            case VICTORIOUS_GAME_TYPE_LIVEDRAFT:
                wp_enqueue_script('livedraft.js', VICTORIOUS__PLUGIN_URL_JS . 'livedraft.js');
                break;
            case VICTORIOUS_GAME_TYPE_BRACKET:
                wp_enqueue_script('bracket.js', VICTORIOUS__PLUGIN_URL_JS . 'bracket.js');
                wp_enqueue_style('bracket.css', VICTORIOUS__PLUGIN_URL_CSS . 'bracket.css');
                break;
            case VICTORIOUS_GAME_TYPE_MINIGOLIATH:
                wp_enqueue_script('minigoliath.js', VICTORIOUS__PLUGIN_URL_JS . 'minigoliath.js');
                break;
            case VICTORIOUS_GAME_TYPE_GOLIATH:
                wp_enqueue_script('goliath.js', VICTORIOUS__PLUGIN_URL_JS . 'goliath.js');
                break;
            case VICTORIOUS_GAME_TYPE_ROUNDPICKEM:
                wp_enqueue_script('roundpickem.js', VICTORIOUS__PLUGIN_URL_JS . 'roundpickem.js');
                break;
            case VICTORIOUS_GAME_TYPE_PICKSQUARES:
                wp_enqueue_script('picksquares.js', VICTORIOUS__PLUGIN_URL_JS . 'picksquares.js');
                break;
            case VICTORIOUS_GAME_TYPE_BOTHTEAMSTOSCORE:
                wp_enqueue_script('rankings.js', VICTORIOUS__PLUGIN_URL_JS . 'rankings.js');
                wp_enqueue_script('bothteamstoscore.js', VICTORIOUS__PLUGIN_URL_JS . 'bothteamstoscore.js');
                break;
            case VICTORIOUS_GAME_TYPE_PICKULTIMATE:
                wp_enqueue_script('pickultimate.js', VICTORIOUS__PLUGIN_URL_JS . 'pickultimate.js');
                break;
            case VICTORIOUS_GAME_TYPE_PICKMONEY:
            case VICTORIOUS_GAME_TYPE_PICKTIE:
            case VICTORIOUS_GAME_TYPE_PICKSPREAD:
            case VICTORIOUS_GAME_TYPE_HOWMANYGOALS:
            case VICTORIOUS_GAME_TYPE_PICKEM:
                wp_enqueue_script('rankings.js', VICTORIOUS__PLUGIN_URL_JS . 'rankings.js');
                wp_enqueue_script('pickem.js', VICTORIOUS__PLUGIN_URL_JS . 'pickem.js');
                break;
            case VICTORIOUS_GAME_TYPE_SURVIVAL:
                wp_enqueue_script('survival.js', VICTORIOUS__PLUGIN_URL_JS . 'survival.js');
                break;
            case VICTORIOUS_GAME_TYPE_TEAMDRAFT:
                wp_enqueue_script('teamdraft.js', VICTORIOUS__PLUGIN_URL_JS . 'teamdraft.js');
                break;
            case VICTORIOUS_GAME_TYPE_BEST5:
                wp_enqueue_script('best5.js', VICTORIOUS__PLUGIN_URL_JS . 'best5.js');
                break;
            case VICTORIOUS_GAME_TYPE_SPORTBOOK:
                wp_enqueue_script('sportbook.js', VICTORIOUS__PLUGIN_URL_JS . 'sportbook.js');
                break;
            case VICTORIOUS_GAME_TYPE_UPLOADPHOTO:
                wp_enqueue_script('rankings.js', VICTORIOUS__PLUGIN_URL_JS . 'rankings.js');
                wp_enqueue_script('uploadphoto.js', VICTORIOUS__PLUGIN_URL_JS . 'uploadphoto.js');
                wp_enqueue_script('fine-uploader.js', VICTORIOUS__PLUGIN_URL_JS.'fine-uploader.js');
                wp_enqueue_style('fine-uploader-new.css', VICTORIOUS__PLUGIN_URL_CSS.'fine-uploader-new.css');
                break;
            case VICTORIOUS_GAME_TYPE_PORTFOLIO:
                wp_enqueue_script('portfolio.js', VICTORIOUS__PLUGIN_URL_JS . 'portfolio.js');
                break;
            case VICTORIOUS_GAME_TYPE_OLDDRAFT:
                wp_enqueue_script('olddraft.js', VICTORIOUS__PLUGIN_URL_JS . 'olddraft.js');
                break;
            case VICTORIOUS_GAME_TYPE_NFL_PLAYOFF:
                wp_enqueue_script('playoff.js', VICTORIOUS__PLUGIN_URL_JS . 'playoff.js');
                break;
            default :
                wp_enqueue_script('playerdraft.js', VICTORIOUS__PLUGIN_URL_JS . 'playerdraft.js');
        }
    }

    public static function contest() {
        if (!in_the_loop()) {
            return;
        }

        $league_id = pageSegment(3);
        $entry_number = !empty($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;
        $league = self::$victorious->getLeagueDetail($league_id);
        $league = $league[0];

        switch ($league['gameType']) {
            case VICTORIOUS_GAME_TYPE_LIVEDRAFT:
                self::livedraft($league);
                break;
            case VICTORIOUS_GAME_TYPE_BRACKET:
                self::bracket($league);
                break;
            case VICTORIOUS_GAME_TYPE_MINIGOLIATH:
                self::minigoliath($league);
                break;
            case VICTORIOUS_GAME_TYPE_GOLIATH:
                self::goliath($league);
                break;
            case VICTORIOUS_GAME_TYPE_ROUNDPICKEM:
                self::roundPickem($league);
                break;
            case VICTORIOUS_GAME_TYPE_PICKSQUARES:
                self::pickSquares($league);
                break;
            case VICTORIOUS_GAME_TYPE_BOTHTEAMSTOSCORE:
                self::bothteamstoscore($league);
                break;
            case VICTORIOUS_GAME_TYPE_PICKULTIMATE:
                self::pickultimate($league);
                break;
            case VICTORIOUS_GAME_TYPE_PICKMONEY:
            case VICTORIOUS_GAME_TYPE_PICKTIE:
            case VICTORIOUS_GAME_TYPE_PICKSPREAD:
            case VICTORIOUS_GAME_TYPE_HOWMANYGOALS:
            case VICTORIOUS_GAME_TYPE_PICKEM:
                self::pickem($league);
                break;
            case VICTORIOUS_GAME_TYPE_SURVIVAL:
                self::survival($league);
                break;
            case VICTORIOUS_GAME_TYPE_TEAMDRAFT:
                self::teamdraft($league);
                break;
            case VICTORIOUS_GAME_TYPE_BEST5:
                self::best5($league);
                break;
            case VICTORIOUS_GAME_TYPE_SPORTBOOK:
                self::sportbook($league);
                break;
            case VICTORIOUS_GAME_TYPE_UPLOADPHOTO:
                self::uploadphoto($league);
                break;
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
                self::playerdraft($league);
        }
    }

    private static function playerdraft($league) {
        $league_id = $league['leagueID'];
        $entry_number = !empty($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;
        $user_id = VIC_GetUserId();
        $balance_type = $league['balance_type'];

        $result = self::$victorious->getContestResult($league_id);
        $scoringCats = !empty($result['scoring_cat']) ? $result['scoring_cat'] : array();
        $bonus = !empty($result['bonus']) ? $result['bonus'] : '';
        $leagueOptionType = $league['balance_type'];
        $is_motocross = false;
        $no_cash = get_option('victorious_no_cash');

        //live update
        if ($league['is_live']) {
            //self::$victorious->liveEntriesResult($league_id);
        }

        //scores
        $only_me = true;
        if($league['is_live'] || $league['is_complete']){
            $only_me = false;
        }
        $scores = self::$victorious->getScores($league_id, 0 , $only_me);
        if ($scores != null) {
            foreach ($scores as $k => $score) {
                $score[$k]['current'] = false;
                if ($score['userID'] == $user_id && $score['entry_number'] == $entry_number) {
                    $scores[$k]['current'] = true;
                }
            }
        }

        //score detail
        $result_detail = self::$victorious->getPlayerPicksResult($league_id, $user_id, $entry_number, null, null);
        $my_result_detail = self::$victorious->getPlayerPicksResult($league_id, $user_id, $entry_number, null, null);

        //fixture scores
        $data = self::$victorious->loadFixtureScores($league_id);
        $aFights = $data['fights'];
        $aRounds = $data['rounds'];

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'playerdraft/result.php';
    }

    private static function livedraft($league) {
        $leagueId = $league['leagueID'];
        $entry_number = !empty($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;

        $result = self::$victorious->getContestResult($leagueId);
        $scoringCats = $result['scoring_cat'];
        $bonus = $result['bonus'];
        $aRounds = $result['rounds'];
        $aFights = $result['fights'];

        $league = self::$victorious->parseLeagueData($league);

        $aFights = self::$pools->parseFightsDataDetail($aFights);
        $week = $result['week'];
        $week_select = $result['week_select'];
        $opponents = $result['opponents'];

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'result/livedraft.php';
    }

    private static function bracket($league) {
        $league_id = pageSegment(3);
        $entry_number = !empty($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;

        $result = self::$victorious->getContestResult($league_id);
        $bonus = $result['bonus'];
        $aFights = $result['fights'];
        $league = self::$victorious->parseLeagueData($league);

        $aFights = self::$pools->parseFightsDataDetail($aFights);
        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'result/bracket.php';
    }

    private static function roundPickem($league) {
        $league_id = pageSegment(3);
        $entry_number = !empty($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;

        $data = self::$victorious->getRoundPickemCurrentWeek($league_id, $entry_number);
        $current_week = $data['current_week'];

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'result/roundpickem.php';
    }

    private static function bothteamstoscore($league) {
        $league_id = pageSegment(3);
        $entry_number = !empty($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;

        $global_settings = self::$victorious->getGlobalSetting();
        $allow_pick_email = $global_settings['allow_pick_email'];
        $number_of_weeks = array();
        $number_of_months = array();

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'result/bothteamstoscore.php';
    }

    private static function pickultimate($league) {
        $league_id = pageSegment(3);
        $entry_number = !empty($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;
        $balance_type = $league['balance_type'];
        $user_id = VIC_GetUserId();

        //live update
        if ($league['is_live']) {
            //self::$victorious->liveEntriesResult($league_id);
        }

        $result = self::$victorious->getPickUltimateResult($league_id);
        $league = $result['league'];
        $standing = $result['standing'];
        $total_page = $result['total_page'];

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'pickultimate/result.php';
    }

    private static function pickem($league) {
        $league_id = pageSegment(3);
        $entry_number = !empty($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;
        $balance_type = $league['balance_type'];
        $user_id = VIC_GetUserId();

        $global_settings = self::$victorious->getGlobalSetting();
        $allow_pick_email = $global_settings['allow_pick_email'];
        $number_of_weeks = array();
        $number_of_months = array();

        $result = self::$victorious->getPickemResult($league_id);
        $league = $result['league'];
        $standing = $result['standing'];
        $total_page = $result['total_page'];

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'pickem/result.php';
    }

    private static function pickSquares($league) {
        $league_id = pageSegment(3);
        $entry_number = !empty($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;
        $balance_type = $league['balance_type'];

        $global_settings = self::$victorious->getGlobalSetting();
        $allow_pick_email = $global_settings['allow_pick_email'];
        $number_of_weeks = array();
        $number_of_months = array();

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'result/picksquares.php';
    }

    private static function goliath($league) {
        $league_id = pageSegment(3);
        $entry_number = !empty($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;

        //get data
        $data = self::$victorious->getGoliathContestResult($league_id);
        $fights = $data['fights'];

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'result/goliath.php';
    }

    private static function minigoliath($league) {
        $league_id = pageSegment(3);
        $entry_number = !empty($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;

        //get data
        $data = self::$victorious->getMiniGoliathContestResult($league_id);
        $fights = $data['fights'];

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'result/minigoliath.php';
    }

    private static function survival($league) {
        $league_id = pageSegment(3);
        $entry_number = !empty($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;

        $data = self::$victorious->getSurvivalCurrentWeek($league_id, $entry_number);
        $current_week = $data['current_week'];

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'result/survival.php';
    }

    private static function teamdraft($league) {
        $league_id = pageSegment(3);
        $entry_number = !empty($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;
        $balance_type = $league['balance_type'];

        $data = self::$victorious->getTeamDraftContestResult($league_id, $entry_number);
        $league = $data['league'];
        $fights = $data['fights'];
        $lineup_scorings = $data['lineup_scorings'];

        //live update
        if ($league['is_live']) {
            self::$victorious->liveEntriesResult($league_id);
        }

        //load standing
        $user_id = VIC_GetUserId();
        $current_page = 1;
        $standing_data = self::$victorious->getTeamDraftResult($league_id, $current_page);

        $standing = $standing_data['standing'];
        $total_page = $standing_data['total_page'];

        //load first user result
        if ($standing != null) {
            $result_detail = self::$victorious->getTeamDraftResultDetail($league_id, $standing[0]['userID'], $standing[0]['entry_number']);
            $score = $result_detail['score'];
            $score_detail = $result_detail['score_detail'];
        }

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'result/teamdraft.php';
    }

    private static function best5($league) {
        $league_id = pageSegment(3);
        $entry_number = !empty($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;

        $data = self::$victorious->getBest5ContestResult($league_id, $entry_number);
        $rounds = $data['rounds'];
        $scoring_categories = $data['scoring_categories'];

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'result/best5.php';
    }

    private static function sportbook($league) {
        $league_id = pageSegment(3);
        $entry_number = !empty($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;

        $balance_type = $league['balance_type'];
        $data = self::$sportbook->getSportbookContestResult($league_id, $entry_number);
        $league = $data['league'];
        $fights = $data['fights'];

        //live update
        if ($league['is_live']) {
            //self::$victorious->liveEntriesResult($league_id);
        }

        //load standing
        $user_id = VIC_GetUserId();
        $current_page = 1;
        $standing_data = self::$sportbook->getSportbookResult($league_id, $current_page);

        $standing = $standing_data['standing'];
        $total_page = $standing_data['total_page'];

        //load first user result
        if ($standing != null) {
            $result_detail = self::$sportbook->getSportbookResultDetail($league_id, $standing[0]['userID'], $standing[0]['entry_number']);
            if(empty($result_detail['picks'])){
                VIC_Redirect(VICTORIOUS_URL_LOBBY, __('No pick found', 'victorious'), true);
            }
            $picks = $result_detail['picks'];
            $score = $result_detail['score'];
            $fights = $result_detail['fights'];
        }

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'sportbook/result.php';
    }

    private static function uploadphoto($league) {
        $league_id = pageSegment(3);
        $entry_number = !empty($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;
        $balance_type = $league['balance_type'];

        $global_settings = self::$victorious->getGlobalSetting();
        $allow_pick_email = $global_settings['allow_pick_email'];
        $number_of_weeks = array();
        $number_of_months = array();

        $data = self::$uploadphoto->getContestResult($league_id);
        $league = $data['league'];
        $fights = $data['fights'];

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'result/uploadphoto.php';
    }

    private static function portfolio($league) {
        $league_id = $league['leagueID'];
        $entry_number = !empty($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;
        $balance_type = $league['balance_type'];

        $data = self::$portfolio->getPortfolioContestResult($league_id, $entry_number);
        $league = $data['league'];

        //load standing
        $user_id = VIC_GetUserId();
        $current_page = 1;
        $standing_data = self::$portfolio->getPortfolioResult($league_id, $current_page);

        $standing = $standing_data['standing'];
        $total_page = $standing_data['total_page'];

        //load first user result
        if ($standing != null) {
            $my_result_detail = self::$portfolio->getPortfolioResultDetail($league_id, $user_id, $entry_number);
            $result_detail = self::$portfolio->getPortfolioResultDetail($league_id, $standing[0]['userID'], $standing[0]['entry_number']);
            $my_score = $my_result_detail['score'];
            $my_score_detail = $my_result_detail['score_detail'];
            $score = !empty($result_detail['score']) ? $result_detail['score'] : array();
            $score_detail = !empty($result_detail['score_detail']) ? $result_detail['score_detail'] : array();
        }

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'portfolio/result.php';
    }

    private static function olddraft($league) {
        $league_id = $league['leagueID'];
        $entry_number = !empty($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;
        $balance_type = $league['balance_type'];

        $data = self::$olddraft->getOldDraftContestResult($league_id, $entry_number);
        $league = $data['league'];

        //load standing
        $user_id = VIC_GetUserId();
        $current_page = 1;
        $standing_data = self::$olddraft->getOldDraftResult($league_id, $current_page);

        $standing = $standing_data['standing'];
        $total_page = $standing_data['total_page'];

        //load first user result
        if ($standing != null) {
            $my_result_detail = self::$olddraft->getOldDraftResultDetail($league_id, $user_id, $entry_number);
            $result_detail = self::$olddraft->getOldDraftResultDetail($league_id, $standing[0]['userID'], $standing[0]['entry_number']);
            $my_score = $my_result_detail['score'];
            $my_score_detail = $my_result_detail['score_detail'];
            $score = !empty($result_detail['score']) ? $result_detail['score'] : array();
            $score_detail = !empty($result_detail['score_detail']) ? $result_detail['score_detail'] : array();
        }

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'olddraft/result.php';
    }

    private static function playoff($league) {
        $league_id = $league['leagueID'];
        $entry_number = !empty($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;

        $data = self::$playoff->getPlayoffContestResult($league_id);
        $league = $data['league'];
        $weeks = $data['weeks'];

        //load standing
        $user_id = VIC_GetUserId();
        $current_page = 1;
        $standing_data = self::$playoff->getPlayoffResult($league_id, $current_page);

        $standing = $standing_data['standing'];
        $total_page = $standing_data['total_page'];

        //load first user result
        if ($standing != null) {
            $my_result_detail = self::$playoff->getPlayoffResultDetail($league_id, $user_id, $entry_number, $weeks[0]['week']);
            $result_detail = self::$playoff->getPlayoffResultDetail($league_id, $standing[0]['userID'], $standing[0]['entry_number'], $weeks[0]['week']);
            $my_score = $my_result_detail['score'];
            $my_score_detail = $my_result_detail['score_detail'];
            $score = !empty($result_detail['score']) ? $result_detail['score'] : array();
            $score_detail = !empty($result_detail['score_detail']) ? $result_detail['score_detail'] : array();
        }

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'playoff/result.php';
    }
}

?>