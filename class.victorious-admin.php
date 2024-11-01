<?php
class Victorious_Admin
{
    public function __construct() 
    {
        add_action( 'admin_head', array( &$this, 'admin_header' ) );
    }
    public static function init()
    {
        add_action('admin_menu', array('Victorious_Admin', 'loadMenuSetting'));
        add_action('admin_menu', array('Victorious_Admin', 'loadMenuBar'));
    }
    
    static function admin_header() {
        echo '<style type="text/css">';
        echo '.wp-list-table .column-ID, .wp-list-table .column-uID  { width: 60px; }';
        echo '.wp-list-table .column-payment_request_pending,'
           . '.wp-list-table .column-action { width: 200px; }';
        echo '.wp-list-table .column-balance { width: 300px; }';
        echo '.wp-list-table .column-startDate { width: 150px; }';
        echo '.wp-list-table .column-status, '
           . '.wp-list-table .column-action2 { width: 132px; }';
        echo '.wp-list-table .column-result, '
           . '.wp-list-table .column-image, '
		   . '.wp-list-table .column-requestDate, '
           . '.wp-list-table .column-active, '
           . '.wp-list-table .column-edit, '
           . '.wp-list-table .column-detail { width: 70px; }';
        echo '.wp-list-table .column-new_balance, '
           . '.wp-list-table .column-amount, '
           . '.wp-list-table .column-playerdraft_result, '
           . '.wp-list-table .column-reverse_points, '
           . '.wp-list-table .column-real_amount { width: 90px; } ';
        echo '</style>';
    }

    //setting
    public static function loadMenuSetting()
    {
        add_options_page( 'Victorious Settings', 'Victorious', 'manage_options', 'victorious', array( 'Victorious_Admin', 'options'));
        add_action('admin_init', array( 'Victorious_Admin', 'registerSettings'));
        wp_enqueue_script('option.js', VICTORIOUS__PLUGIN_URL_JS.'admin/option.js');
    }
    
    public static function options() 
    {
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) , 'victorious');
        }
        wp_enqueue_script('jquery-ui-tabs');
        include VICTORIOUS__PLUGIN_DIR_VIEW.'settings.php';
    }
    
    public static function registerSettings()
    { 
        register_setting('victorious-settings-group', 'victorious_api_token');
        register_setting('victorious-settings-group', 'victorious_api_url');
        register_setting('victorious-settings-group', 'victorious_api_url_admin');
        register_setting('victorious-settings-group', 'victorious_image_dir');
        register_setting('victorious-settings-group', 'victorious_image_thumb_size');
        register_setting('victorious-settings-group', 'victorious_entry_fee');
        register_setting('victorious-settings-group', 'victorious_league_size');
        register_setting('victorious-settings-group', 'victorious_winner_percent');
        register_setting('victorious-settings-group', 'victorious_first_place_percent');
        register_setting('victorious-settings-group', 'victorious_second_place_percent');
        register_setting('victorious-settings-group', 'victorious_third_place_percent');
        register_setting('victorious-settings-group', 'victorious_cash_to_credit');
        register_setting('victorious-settings-group', 'victorious_credit_to_cash');
        register_setting('victorious-settings-group', 'victorious_create_contest');
        register_setting('victorious-settings-group', 'victorious_payout_gateway');
        register_setting('victorious-settings-group', 'victorious_payout_method');
        register_setting('victorious-settings-group', 'paypal_test');
        register_setting('victorious-settings-group', 'paypal_email_account');
        register_setting('victorious-settings-group', 'victorious_minimum_deposit');
        register_setting('victorious-settings-group', 'victorious_fee_percentage');
        register_setting('victorious-settings-group', 'victorious_no_cash');
        register_setting('victorious-settings-group', 'victorious_no_invite_user_list');
        register_setting('victorious-settings-group', 'victorious_show_import_pick');
        register_setting('victorious-settings-group', 'victorious_paypal_type');
        register_setting('victorious-settings-group', 'victorious_paypal_pro_username');
        register_setting('victorious-settings-group', 'victorious_paypal_pro_password');
        register_setting('victorious-settings-group', 'victorious_paypal_pro_signature');
        register_setting('victorious-settings-group', 'victorious_get_email_from_better_join_contest');
        register_setting('victorious-settings-group', 'victorious_paypal_client_id');
        register_setting('victorious-settings-group', 'victorious_currency');
        register_setting('victorious-settings-group', 'victorious_currency_position');
        register_setting('victorious-settings-group', 'victorious_share_teams_players');
        register_setting('victorious-settings-group', 'victorious_facebook_app_id');
        register_setting('victorious-settings-group', 'victorious_b_icon');
        register_setting('victorious-settings-group', 'victorious_timezone');
        register_setting('victorious-settings-group', 'victorious_date_format');
        register_setting('victorious-settings-group', 'victorious_weekly_statistic_email');
        register_setting('victorious-settings-group', 'victorious_last_report_date');
        register_setting('victorious-settings-group', 'victorious_global_currency_enable');
        register_setting('victorious-settings-group', 'victorious_global_currency_name');
        register_setting('victorious-settings-group', 'victorious_global_currency_symbol');
        register_setting('victorious-settings-group', 'victorious_firebase_apikey');
        register_setting('victorious-settings-group', 'victorious_firebase_senderid');
        register_setting('victorious-settings-group', 'victorious_bracket_point_group_winner');
        register_setting('victorious-settings-group', 'victorious_bracket_point_group_runnerup');
        register_setting('victorious-settings-group', 'victorious_bracket_point_16');
        register_setting('victorious-settings-group', 'victorious_bracket_point_8');
        register_setting('victorious-settings-group', 'victorious_bracket_point_4');
        register_setting('victorious-settings-group', 'victorious_bracket_point_first');
        register_setting('victorious-settings-group', 'victorious_bracket_point_second');
        register_setting('victorious-settings-group', 'victorious_bracket_point_third');
        register_setting('victorious-settings-group', 'victorious_email_from_name');
		register_setting('victorious-settings-group', 'victorious_email_from_email');
        register_setting('victorious-settings-group', 'victorious_dfscoin_api_key');
        register_setting('victorious-settings-group', 'victorious_dfscoin_wallet_address');
        register_setting('victorious-settings-group', 'victorious_dfscoin_exchange_rate');
        register_setting('victorious-settings-group', 'victorious_paysimple_test');
        register_setting('victorious-settings-group', 'victorious_paysimple_username');
        register_setting('victorious-settings-group', 'victorious_paysimple_api_key');
        register_setting('victorious-settings-group', 'victorious_playoff_change_turn');
        if(isset($_GET['page']) && $_GET['page'] == 'victorious')
		{
			wp_enqueue_script('option.js', VICTORIOUS__PLUGIN_URL_JS . 'admin/option.js', 5);
		}
        
        //update setting to server
        add_action("updated_option", function($option_name, $option_value){
            if (strpos($option_name, 'victorious_') !== false) {
                $victorious = new VIC_Victorious();
                $victorious->postUserInfo();
            }
        }, 10, 2);
    }
    
    //menu bar
    public static function loadMenuBar()
    {
        $victorious = new VIC_Victorious();
        $global_setting = $victorious->getGlobalSetting();
        add_menu_page("Victorious Pages", "Victorious", '', 'victorious_page', '');

        $hook = add_submenu_page('victorious_page', __('DashBoard', 'victorious'), __('DashBoard', 'victorious'), 'manage_options', 'manage-dashboard', array('Victorious_Sports', 'manageDashboard'));
        add_action("load-$hook", array('Victorious_Admin', "dashboard_screen"));
        add_submenu_page('', __('Add Sport', 'victorious'), __('Add Sport', 'victorious'), 'manage_options', 'add-sports', array('Victorious_Sports', 'addSports'));

        if(isset($global_setting['allow_auto_create_contest']) && $global_setting['allow_auto_create_contest'] == 1)
        {
            $hook = add_submenu_page('victorious_page', __('Auto Contests', 'victorious'), __('Auto Contests', 'victorious'), 'manage_options', 'manage-auto-contests', array('Victorious_AutoContest', 'manageAutoContest'));
            add_action("load-$hook", array('Victorious_Admin', "auto_contest_screen"));
            add_submenu_page('manage-auto-contests', __('Add Auto Contests', 'victorious'), __('Add Auto Contests', 'victorious'), 'manage_options', 'add-auto-contest', array('Victorious_AutoContest', 'addAutoContest'));
        }
        
        $hook = add_submenu_page('victorious_page', __('Sports', 'victorious'), __('Sports', 'victorious'), 'manage_options', 'manage-sports', array('Victorious_Sports', 'manageSports'));
        add_action("load-$hook", array('Victorious_Admin', "sports_screen"));
        add_submenu_page('', __('Add Sport', 'victorious'), __('Add Sport', 'victorious'), 'manage_options', 'add-sports', array('Victorious_Sports', 'addSports'));
        
        $hook = add_submenu_page('victorious_page', __('Events', 'victorious'), __('Events', 'victorious'), 'manage_options', 'manage-pools', array('Victorious_Pools', 'managePools'));
        add_action("load-$hook", array('Victorious_Admin', "pools_screen"));
        add_submenu_page('', __('Add Events', 'victorious'), __('Add Events', 'victorious'), 'manage_options', 'add-pools', array('Victorious_Pools', 'addPools'));
        
        $hook = add_submenu_page('victorious_page', __('Contests', 'victorious'), __('Contests', 'victorious'), 'manage_options', 'manage-contests', array('Victorious_Contests', 'manageContests'));
        add_action("load-$hook", array('Victorious_Admin', "contests_screen"));
        add_submenu_page('', __('Add Contests', 'victorious'), __('Add Contests', 'victorious'), 'manage_options', 'add-contests', array('Victorious_Contests', 'addContests'));
        
        $hook = add_submenu_page('victorious_page', __('Fighters', 'victorious'), __('Fighters', 'victorious'), 'manage_options', 'manage-fighters', array('Victorious_Fighters', 'manageFighters'));
        add_action("load-$hook", array('Victorious_Admin', "fighters_screen"));
        add_submenu_page('', __('Add Fighters', 'victorious'), __('Add Fighters', 'victorious'), 'manage_options', 'add-fighters', array('Victorious_Fighters', 'addFighters'));
        
        $hook = add_submenu_page('victorious_page', __('Teams', 'victorious'), __('Teams', 'victorious'), 'manage_options', 'manage-teams', array('Victorious_Teams', 'manageTeams'));
        add_action("load-$hook", array('Victorious_Admin', "team_screen"));
        add_submenu_page('', __('Add Teams', 'victorious'), __('Add Teams', 'victorious'), 'manage_options', 'add-teams', array('Victorious_Teams', 'addTeams'));
        
        $hook = add_submenu_page('victorious_page', __('Player Positions', 'victorious'), __('Player Positions', 'victorious'), 'manage_options', 'manage-playerposition', array('Victorious_PlayerPosition', 'managePlayerPosition'));
        add_action("load-$hook", array('Victorious_Admin', "playerposition_screen"));
        add_submenu_page('', __('Add Player Position', 'victorious'), __('Add Player Position', 'victorious'), 'manage_options', 'add-playerposition', array('Victorious_PlayerPosition', 'addPlayerPosition'));
        
        $hook = add_submenu_page('victorious_page', __('Scoring Categories', 'victorious'), __('Scoring Categories', 'victorious'), 'manage_options', 'manage-scoringcategory', array('Victorious_ScoringCategory', 'manageScoringCategory'));
        add_action("load-$hook", array('Victorious_Admin', "scoringcategory_screen"));
        add_submenu_page('', __('Add Scoring Category', 'victorious'), __('Add Scoring Category', 'victorious'), 'manage_options', 'add-scoringcategory', array('Victorious_ScoringCategory', 'addScoringCategory'));

        $hook = add_submenu_page('victorious_page', __('Players', 'victorious'), __('Players', 'victorious'), 'manage_options', 'manage-players', array('Victorious_Players', 'managePlayers'));
        add_action("load-$hook", array('Victorious_Admin', "players_screen"));
        add_submenu_page('', __('Add Players', 'victorious'), __('Add Players', 'victorious'), 'manage_options', 'add-players', array('Victorious_Players', 'addPlayers'));
    
        $hook = add_submenu_page('victorious_page', __('Player News', 'victorious'), __('Player News', 'victorious'), 'manage_options', 'manage-playernews', array('Victorious_PlayerNews', 'managePlayerNews'));
        add_action("load-$hook", array('Victorious_Admin', "playernews_screen"));
        add_submenu_page('', __('Add Player News', 'victorious'), __('Add Player News', 'victorious'), 'manage_options', 'add-playernews', array('Victorious_PlayerNews', 'addPlayerNews'));

        if(!empty($global_setting['allow_multiple_balances'])){
            $hook = add_submenu_page('victorious_page', __('Balance Types', 'victorious'), __('Balance Types', 'victorious'), 'manage_options', 'manage-balance-types', array('Victorious_BalanceTypes', 'manageBalanceTypes'));
            add_action("load-$hook", array('Victorious_Admin', "balancetypes_screen"));
            add_submenu_page('', __('Add Balance Type', 'victorious'), __('Add Balance Type', 'victorious'), 'manage_options', 'add-balance-type', array('Victorious_BalanceTypes', 'addBalanceType'));
        }

        $hook = add_submenu_page('victorious_page', __('Credits', 'victorious'), __('Credits', 'victorious'), 'manage_options', 'credits', array('Victorious_Credits', 'manageCredits'));
        add_action("load-$hook", array('Victorious_Admin', "credits_screen"));
        
        $hook = add_submenu_page('victorious_page', __('Withdrawls', 'victorious'), __('Withdrawls', 'victorious'), 'manage_options', 'withdrawls', array('Victorious_Withdrawls', 'manageWithdrawls'));
        add_action("load-$hook", array('Victorious_Admin', "withdrawls_screen"));

        $hook = add_submenu_page('victorious_page', __('Transactions', 'victorious'), __('Transactions', 'victorious'), 'manage_options', 'transactions', array('Victorious_Transactions', 'manageTransactions'));
        add_action("load-$hook", array('Victorious_Admin', "transactions_screen"));
        
        $hook = add_submenu_page('victorious_page', __('Event Statistics', 'victorious'), __('Event Statistics', 'victorious'), 'manage_options', 'statistic', array('Victorious_Statistic', 'manageStatistic'));
        add_action("load-$hook", array('Victorious_Admin', "event_statistics_screen"));
        
        if(victoriousCheckAllowChangebackground()){
            $hook = add_submenu_page('victorious_page', __('Teams Backgournd', 'victorious'), __('Teams Backgournd', 'victorious'), 'manage_options', 'teams-background', array('Victorious_Teams', 'manageBgTeams'));
            add_action("load-$hook", array('Victorious_Admin', "team_backgrounds_screen"));
        }

        if(isset($global_setting['allow_user_entry_limitation']) && $global_setting['allow_user_entry_limitation'] == 1)
        {
            add_submenu_page('victorious_page', __('User Multi entries', 'victorious'), __('User Multi entries', 'victorious'), 'manage_options', 'user-multi-entries', array('Victorious_Users', 'manageUserMultiEntries'));
        }
    }

    static function auto_contest_screen(){
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'manage_sports_per_page'
        );
        add_screen_option( $option, $args );
    }

    static function dashboard_screen(){
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'manage_sports_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    static function sports_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'manage_sports_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    static function pools_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'manage_pools_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    static function team_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'manage_team_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    static function fighters_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'manage_fighters_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    static function event_statistics_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'event_statistics_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    static function credits_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'credits_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    static function withdrawls_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'withdrawls_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    //v2
    static function playerposition_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'manage_playerposition_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    static function scoringcategory_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'manage_scoringcategory_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    static function players_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'manage_players_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    static function playernews_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'manage_player_news_per_page'
        );
        add_screen_option( $option, $args );
    }

    static function balancetypes_screen()
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'manage_balance_type_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    static function contests_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'manage_contests_per_page'
        );
        add_screen_option( $option, $args );
    }
    
    static function transactions_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'transactions_per_page'
        );
        add_screen_option( $option, $args );
    }
    static function team_backgrounds_screen() 
    {
        $option = 'per_page';
        $args = array(
            'label' => 'Pages',
            'default' => 15,
            'option' => 'transactions_per_page'
        );
        add_screen_option( $option, $args );
    }
}
?>