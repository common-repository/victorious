<?php
class VIC_GamesummaryController
{
    private static $victorious;
    public function __construct() 
    {
        self::$victorious = new VIC_Victorious();
    }
    
	public static function process()
	{       
        add_action('wp_enqueue_scripts', array('VIC_GamesummaryController', 'theme_name_scripts'));
        add_filter('the_content', array('VIC_GamesummaryController', 'addContent'));
	}
    
    public static function theme_name_scripts()
    {
        wp_enqueue_style('style.css', VICTORIOUS__PLUGIN_URL_CSS.'style.css');
        wp_enqueue_script('summary.js', VICTORIOUS__PLUGIN_URL_JS.'summary.js');
        wp_enqueue_style('font-awesome.css', VICTORIOUS__PLUGIN_URL_CSS.'font-awesome/css/font-awesome.css');
    }
    
    public static function addContent()
    {
        if(!in_the_loop())
        {
            return;
        }
        $aUsers = self::$victorious->getGamesummary();
        include VICTORIOUS__PLUGIN_DIR_VIEW.'gamesummary.php';
    }
}
?>