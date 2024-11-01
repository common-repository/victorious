<?php
class VIC_MyLiveEntriesController
{
    private static $victorious;
    public function __construct() 
    {
        self::$victorious = new VIC_Victorious();
    }
	public static function process()
	{       
        add_action('wp_enqueue_scripts', array('VIC_MyLiveEntriesController', 'theme_name_scripts'));
        add_filter('the_content', array('VIC_MyLiveEntriesController', 'addContent'));
	}
    
    public static function theme_name_scripts()
    {
        //wp_enqueue_script('mycontests.js', VICTORIOUS__PLUGIN_URL_JS.'mycontests.js');
        wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');
        
        wp_enqueue_script('victorious.js', VICTORIOUS__PLUGIN_URL_JS.'victorious.js');
        wp_enqueue_script('live_draft_trade_player.js', VICTORIOUS__PLUGIN_URL_JS.'live_draft_trade_player.js');

        wp_enqueue_style('style.css', VICTORIOUS__PLUGIN_URL_CSS.'style.css');
        wp_enqueue_style('flexslider.css', VICTORIOUS__PLUGIN_URL_CSS.'flexslider.css');
        wp_enqueue_style('bootstrap.css', VICTORIOUS__PLUGIN_URL_CSS.'bootstrap.css');
        wp_enqueue_style('Material', VICTORIOUS__PLUGIN_URL_CSS.'material_icons.css');
    }

    public static function addContent()
    {
        if(!in_the_loop())
        {
            return;
        }
        include VICTORIOUS__PLUGIN_DIR_VIEW.'myliveentries.php';
    }
}
?>