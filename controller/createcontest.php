<?php
class VIC_CreatecontestController
{
    private static $orgs;
    private static $pools;
    private static $payment;
    private static $victorious;
    private static $balanceType;
    public function __construct() 
    {
        self::$orgs = new VIC_Organizations();
        self::$pools = new VIC_Pools();
        self::$payment = new VIC_Payment();
        self::$victorious = new VIC_Victorious();
        self::$balanceType = new VIC_BalanceType();
    }
    
	public static function process()
	{    
        if(isset($_POST) && isset($_POST["submitContest"]))
        {
            add_action( 'wp_enqueue_scripts', array('VIC_CreatecontestController', 'theme_name_scripts') );
            add_filter('the_content', array('VIC_CreatecontestController', 'submitPick'));
        }
        else 
        {
            add_action( 'wp_enqueue_scripts', array('VIC_CreatecontestController', 'theme_name_scripts') );
            add_filter('the_content', array('VIC_CreatecontestController', 'addContent'));
        }
	}
    
    public static function theme_name_scripts()
    {
        wp_enqueue_script('createcontest.js', VICTORIOUS__PLUGIN_URL_JS.'createcontest.js', 5);
        wp_enqueue_script('jquery-ui-datepicker');

        wp_enqueue_style('bootstrap.css', VICTORIOUS__PLUGIN_URL_CSS.'bootstrap.css');
        wp_enqueue_style('style.css', VICTORIOUS__PLUGIN_URL_CSS.'style.css');
        wp_enqueue_style('Material', VICTORIOUS__PLUGIN_URL_CSS.'material_icons.css');
        wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');
    }

    public static function addContent()
    {
        if(!in_the_loop())
        {
            return;
        }    
        
        if(!get_option('victorious_create_contest'))
        {
            VIC_Redirect(VICTORIOUS_URL_LOBBY, __('You are not allowed to create contest', 'victorious'), true);
        }

        $global_setting = self::$victorious->getGlobalSetting();
        $aDatas = self::$victorious->loadCreateLeagueForm();
        $aPools = $aDatas['pools'];
        $aFights = $aDatas['fights'];
        $aRounds = $aDatas['rounds'];
        $aSports = $aDatas['sports'];
        $aLeagueSizes = get_option('victorious_league_size');
        $aEntryFees = get_option('victorious_entry_fee');
        $allow_mixing_sport = $aDatas['allow_mixing_sport'];
        $aDates = $aDatas['mixing_pools'];
        $aMixingPools = $aDatas['mixing_pools'];
        $allow_motocross = $aDatas['allow_motocross'];
        $list_motocross_sports = $aDatas['list_motocross_org'];
        $motocross_id = $aDatas['motocross_id'];
        $is_single_game = $aDatas['is_single_game'];
        $is_mixing_game = $aDatas['is_mixing_game'];
        $is_motocross_game = $aDatas['is_motocross_game'];
        $is_show_weekly_pick = $aDatas['is_show_weekly_pick'];
        $contest_only_rookies = $aDatas['contest_only_rookies'];
        $balance_types = self::$balanceType->getBalanceTypeList(array(
            'enabled' => 1
        ));
        $hours = self::$pools->getPoolHours();
        $minutes = self::$pools->getPoolMinutes();
        
        if(isset($_GET['event_id']))
        {
            //select event
			$pools = $aDatas['pools'];
            $select_sport_id = '';
            foreach($pools as $k => $pool)
            {
                if(sanitize_text_field($_GET['event_id']) != $pool['poolID'])
                {
                    unset($pools[$k]);
                }
                else 
                {
                    $select_sport_id = $pool['organization'];
                }
            }
            $aPools = array_values($pools);
            
            //select sport
            foreach($aSports as $k => $sport)
            {
                if(empty($sport['child']))
                {
                    continue;
                }
                foreach($sport['child'] as $k2 => $child_sport)
                {
                    if($select_sport_id != $child_sport['id'])
                    {
                        unset($aSports[$k]['child'][$k2]);
                    }
                }
                $aSports[$k]['child'] = array_values($aSports[$k]['child']);
                if($aSports[$k]['child'] == null)
                {
                    unset($aSports[$k]);
                }
            }
            $aSports = array_values($aSports);
        }

        include VICTORIOUS__PLUGIN_DIR_VIEW.'createcontest.php';
    }
}
?>