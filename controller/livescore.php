<?php
class VIC_LivescoreController
{
    private static $victorious;
    public function __construct() 
    {
        self::$victorious = new VIC_Victorious();
    }
    
	public static function process()
	{
		add_action( 'wp_enqueue_scripts', array('VIC_LivescoreController', 'stat_scripts') );
        add_filter('the_content', array('VIC_LivescoreController', 'addContent'));
	}
    
    public static function stat_scripts()
    {
        wp_enqueue_script('livescore.js', VICTORIOUS__PLUGIN_URL_JS.'livescore.js');
        wp_enqueue_script('jquery.flexslider.js', VICTORIOUS__PLUGIN_URL_JS.'jquery.flexslider.js');
        wp_enqueue_style('live_score.css', VICTORIOUS__PLUGIN_URL_CSS . 'live_score.css');
    }

	public static function addContent()
	{
        if(!in_the_loop()){
            return;
        }

        if(isset($_GET['detail']))
        {
            self::liveScoreDetail();
        }
        else
        {
            self::liveScore();
        }
	}
    
    private static function liveScore()
    {
        $sports = self::$victorious->getSportTree(true);
        include VICTORIOUS__PLUGIN_DIR_VIEW.'live_score.php';
    }
    
    private static function liveScoreDetail()
    {
        if(empty($_GET['team_id']))
        {
            VIC_Redirect(VICTORIOUS_URL_LIVESCORE, __('Team not found', 'victorious'), true);
        }
        $team_id = sanitize_text_field($_GET['team_id']);

        //get data
        $data = self::$victorious->liveScoreTeamSchedule($team_id);
        $team = $data['team'];
        $nearest_fixtures = $data['nearest_fixtures'];
        
        include VICTORIOUS__PLUGIN_DIR_VIEW.'live_score_detail.php';
    }
    
    public static function showLivePoint(){
        wp_enqueue_script('livepoint.js', VICTORIOUS__PLUGIN_URL_JS.'livepoint.js');
        wp_enqueue_style('live_score.css', VICTORIOUS__PLUGIN_URL_CSS . 'live_score.css');
        
        self::$victorious = new VIC_Victorious();
        $gameType = self::$victorious->getGameType();
        $citys = self::$victorious->getCountryList();
        include VICTORIOUS__PLUGIN_DIR_VIEW.'live_point.php';
    }
}
?>