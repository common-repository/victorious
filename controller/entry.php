<?php
class VIC_EntryController
{
    private static $players;
    private static $victorious;
    private static $portfolio;
    private static $olddraft;

    public function __construct()
    {
        self::$players = new VIC_Players();
        self::$victorious = new VIC_Victorious();
        self::$portfolio = new VIC_Portfolio();
        self::$olddraft = new VIC_OldDraft();
    }

    public static function process()
    {
        if (!isset($_GET['league_id'])) {
            $lid = pageSegment(3);
            $queries = array('league_id' => $lid);
            if (!empty($_SERVER['QUERY_STRING'])) {
                $tmp = array();
                parse_str(sanitize_text_field($_SERVER['QUERY_STRING']), $tmp);
                $queries = array_merge($queries, $tmp);
            }
            VIC_Redirect(VICTORIOUS_URL_ENTRY . '?' . http_build_query($queries));
        }
        add_action('wp_enqueue_scripts', array('VIC_EntryController', 'loadCssJs'));
        add_filter('the_content', array('VIC_EntryController', 'entry'));
    }
    
    public static function loadCssJs()
    {
        $league_id = pageSegment(3);
        
        //league
        $league = self::$victorious->getLeagueDetail($league_id);
        if ($league == null)
        {
            VIC_Redirect(VICTORIOUS_URL_CREATE_CONTEST, __('Contest does not exist', 'victorious'), true);
        }
        $league = $league[0];
        
        wp_enqueue_script('global.js', VICTORIOUS__PLUGIN_URL_JS . 'global.js');
        switch (strtolower($league['gameType']))
        {
            case "bracket":
                wp_enqueue_style('bracket.css', VICTORIOUS__PLUGIN_URL_CSS . 'bracket.css');
                wp_enqueue_script('bracket.js', VICTORIOUS__PLUGIN_URL_JS . 'bracket.js');
                break;
            default :
                wp_enqueue_script('playerdraft.js', VICTORIOUS__PLUGIN_URL_JS . 'playerdraft.js');

                wp_enqueue_style('bootstrap.css', VICTORIOUS__PLUGIN_URL_CSS.'bootstrap.css');
                wp_enqueue_style('style.css', VICTORIOUS__PLUGIN_URL_CSS.'style.css');
                wp_enqueue_style('Material', VICTORIOUS__PLUGIN_URL_CSS.'material_icons.css');
        }
    }

    public static function entry()
    {
        if (!in_the_loop())
        {
            return;
        }

        $league_id = pageSegment(3);
        $entry_number = !empty($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;

        //league
        $league = self::$victorious->getLeagueDetail($league_id);
        if ($league == null)
        {
            VIC_Redirect(VICTORIOUS_URL_CREATE_CONTEST, __('Contest does not exist', 'victorious'), true);
        }
        $league = $league[0];
        if (!$league['is_mixing'] && $league['pool_status'] != 'NEW')
        {
            VIC_Redirect(VICTORIOUS_URL_CONTEST . $league_id, null, true);
        }
        else if ($league['is_mixing'] && $league['is_mixing_complete'])
        {
            VIC_Redirect(VICTORIOUS_URL_CONTEST . $league_id, null, true);
        }
        
        switch ($league['gameType'])
        {
            case VICTORIOUS_GAME_TYPE_BRACKET:
                self::bracket($league);
                break;
            case VICTORIOUS_GAME_TYPE_UPLOADPHOTO:
                self::uploadPhoto($league);
                break;
            case VICTORIOUS_GAME_TYPE_PORTFOLIO:
                self::portfolio($league);
                break;
            case VICTORIOUS_GAME_TYPE_OLDDRAFT:
                self::olddraft($league);
                break;
            default :
                if ($league['is_racing'])
                {
                    self::racing($league);
                }
                /*else if ($league['is_horse'])
                {
                    self::horse($league);
                }*/
                else if ($league['is_mixing'])
                {
                    self::mixing($league);
                }
                else
                {
                    self::playerdraft($league);
                }
        }
    }

    private static function playerdraft($league)
    {
        //get prams
        $leagueID = $league['leagueID'];
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;
        $balance_type = $league['balance_type'];

        $data = self::$victorious->getGameEntryData($leagueID, $entry_number);
        if (empty($data['players']))
        {
            VIC_Redirect(VICTORIOUS_URL_LOBBY, __("You haven't picked any player yet", 'victorious'), true);
        }
        $is_lineup_scoccer_field = isset($data['is_soccer']) && $data['is_lineup_scoccer_field'] ? $data['is_soccer'] : false;
        $league = $data['league'];
        $aPool = $data['pool'];
        $aFights = $data['fights'];
        $players = $data['players'];
        $allow_pick_email = $data['allow_pick_email'];

        //cur user
        $current_user = wp_get_current_user();
        $user_avatar = self::$victorious->get_avatar_url(self::$victorious->get_avatar(VIC_GetUserId(), 32));

        // get image
        $aNewPlayers = array();
        if ($is_lineup_scoccer_field)
        {
            foreach ($players as $player)
            {
                $aNewPlayers[$player['position']][] = $player;
            }
        }
        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . '/playerdraft/entry.php';
    }

    private static function mixing($league)
    {
        //get prams
        $leagueID = $league['leagueID'];
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;

        $data = self::$victorious->getMixingGameEntryData($leagueID, $entry_number);
        $league = $data['league'];
        $aPool = $data['pool'];
        $aFights = $data['fights'];
        $aPlayers = $data['players'];
        $aPlayers = self::$players->parsePlayersData($aPlayers);

        //cur user
        $current_user = wp_get_current_user();
        $user_avatar = self::$victorious->get_avatar_url(self::$victorious->get_avatar(VIC_GetUserId(), 32));

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'entry/mixing.php';
    }

    private static function horse($league)
    {
        //get prams
        $leagueID = $league['leagueID'];
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;
        $balance_type = $league['balance_type'];

        $data = self::$victorious->getGameEntryData($leagueID, $entry_number);
        $aPool = $data['pool'];
        $aFights = $data['fights'];
        $aPlayers = $data['players'];
        $aPlayers = self::$players->parsePlayersData($aPlayers);

        //cur user
        $current_user = wp_get_current_user();
        $user_avatar = self::$victorious->get_avatar_url(self::$victorious->get_avatar(VIC_GetUserId(), 32));

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'entry/horse.php';
    }

    private static function racing($league)
    {
        //get prams
        $leagueID = $league['leagueID'];
        $entry_number = isset($_GET['num']) ? sanitize_text_field($_GET['num']) : 1;

        $data = self::$victorious->getGameEntryData($leagueID, $entry_number);
        $aPool = $data['pool'];
        $aFights = $data['fights'];
        $aPlayers = $data['players'];
        $aPlayers = self::$players->parsePlayersData($aPlayers);

        //cur user
        $current_user = wp_get_current_user();
        $user_avatar = self::$victorious->get_avatar_url(self::$victorious->get_avatar(VIC_GetUserId(), 32));

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'entry/racing.php';
    }

    private static function bracket($league)
    {
        //get prams
        $entry_number = !empty($_GET['entry_number']) ? sanitize_text_field($_GET['entry_number']) : 1;

        //load data
        $data = self::$victorious->getBracketGame($league['leagueID'], $entry_number);
        $team_groups = $data['team_groups'];
        $my_picks = $data['my_picks'];
        list($group_left, $group_right) = self::$victorious->bracketGroupTeam($team_groups);

        //view
        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'entry/bracket.php';
    }
    
    private static function uploadPhoto($league)
    {
        $entry_number = !empty($_GET['entry_number']) ? sanitize_text_field($_GET['entry_number'])  : 1;
        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'entry/uploadphoto.php';
    }

    private static function portfolio($league)
    {
        $entry_number = !empty($_GET['entry_number']) ? sanitize_text_field($_GET['entry_number'])  : 1;
        $balance_type = $league['balance_type'];

        $data = self::$portfolio->getPortfolioEntry($league['leagueID'], $entry_number);
        $players = $data['players'];

        //cur user
        $current_user = wp_get_current_user();
        $user_avatar = self::$victorious->get_avatar_url(self::$victorious->get_avatar(VIC_GetUserId(), 32));

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'portfolio/entry.php';
    }

    private static function olddraft($league)
    {
        $entry_number = !empty($_GET['entry_number']) ? sanitize_text_field($_GET['entry_number'])  : 1;
        $balance_type = $league['balance_type'];

        $data = self::$olddraft->getOldDraftEntry($league['leagueID'], $entry_number);
        $players = $data['players'];

        //cur user
        $current_user = wp_get_current_user();
        $user_avatar = self::$victorious->get_avatar_url(self::$victorious->get_avatar(VIC_GetUserId(), 32));

        include VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT . 'olddraft/entry.php';
    }
}

?>