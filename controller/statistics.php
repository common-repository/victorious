<?php
class VIC_StatisticsController
{
    private static $victorious;
    public function __construct() 
    {
        self::$victorious = new VIC_Victorious();
    }
    
	public static function process()
	{
		add_action( 'wp_enqueue_scripts', array('VIC_StatisticsController', 'stat_scripts') );
        add_filter('the_content', array('VIC_StatisticsController', 'addContent'));
	}
    
    public static function stat_scripts()
    {
        wp_enqueue_script('playerdraft.js', VICTORIOUS__PLUGIN_URL_JS . 'playerdraft.js', 5);
        wp_enqueue_script('stats.js', VICTORIOUS__PLUGIN_URL_JS.'stats.js', 5);
        wp_enqueue_style('font-awesome.css', VICTORIOUS__PLUGIN_URL_CSS.'font-awesome/css/font-awesome.css');
        wp_enqueue_style('stats.css', VICTORIOUS__PLUGIN_URL_CSS.'stats.css');
        wp_enqueue_style('playerdraft.css', VICTORIOUS__PLUGIN_URL_CSS . 'playerdraft.css');
    }

	public static function addContent()
	{
        if(!in_the_loop()){
            return;
        }

        if(!empty($_GET['rugby']))
        {
            $data = self::$victorious->rugbyLoadStatsInfo(array(
                'league_id' => sanitize_text_field($_GET['league_id'])
            ));
            if($data == null)
            {
                VIC_Redirect(VICTORIOUS_URL_LOBBY, __("You are not allowed to view this page", 'victorious'), true);
            }
            if($data['leagues'] == null)
            {
                VIC_Redirect(VICTORIOUS_URL_MY_LIVE_ENTRIES, __("Contest not found", 'victorious'), true);
            }
            $scoring_categories = $data['scoring_categories'];
            $player_positions = $data['player_positions'];
            $teams = $data['teams'];
            $leagues = $data['leagues'];
            include VICTORIOUS__PLUGIN_DIR_VIEW.'statistics_rugby.php';
        }
        else
        {
            $data = self::$victorious->loadStatsSport();
            $sports = $data['sports'];
            $allow_statistic = $data['allow_statistic'];
            $is_loggedin = (VIC_GetUserId() > 0) ? 1 : 0;
            include VICTORIOUS__PLUGIN_DIR_VIEW.'statistics.php';
        }
	}
}
?>