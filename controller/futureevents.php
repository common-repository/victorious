<?php
class VIC_FutureeventsController
{
    private static $victorious;
    public function __construct() 
    {
        self::$victorious = new VIC_Victorious();
    }
	public static function process()
	{       
        add_action('wp_enqueue_scripts', array('VIC_FutureeventsController', 'theme_name_scripts'));
        add_filter('the_content', array('VIC_FutureeventsController', 'addContent'));
	}
    
    public static function theme_name_scripts()
    {
        wp_enqueue_script('mycontests.js', VICTORIOUS__PLUGIN_URL_JS.'mycontests.js');
        wp_enqueue_script('victorious.js', VICTORIOUS__PLUGIN_URL_JS.'victorious.js');
        wp_enqueue_style('style.css', VICTORIOUS__PLUGIN_URL_CSS.'style.css');
        
        wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');
    }

    public static function addContent()
    {
        if(!in_the_loop())
        {
            return;
        }
        $sHeader = __("Future Events", 'victorious');
        $futureEvents = self::$victorious->getFutureEvents();
        $organizations = array();
        if($futureEvents != null)
        {
            foreach($futureEvents as $futureEvent)
            {
                if(!in_array($futureEvent['organization'], $organizations))
                {
                    $organizations[$futureEvent['org_id']] = $futureEvent['organization'];
                }
            }
            
            if(!empty($_GET['org']))
            {
                foreach($futureEvents as $k => $futureEvent)
                {
                    if(strtolower($futureEvent['org_id']) != sanitize_text_field($_GET['org']))
                    {
                        unset($futureEvents[$k]);
                    }
                }
                array_values($futureEvents);
            }
        }
        
        
        include VICTORIOUS__PLUGIN_DIR_VIEW.'futureevents.php';
    }
}
?>