<?php
ob_start();

$upload_dir = wp_upload_dir();
define('VICTORIOUS_VERSION', '1.4');
define('VICTORIOUS__PLUGIN_URL', plugin_dir_url(__FILE__));
define('VICTORIOUS__PLUGIN_DIR_MODEL', VICTORIOUS__PLUGIN_DIR . 'model/');
define('VICTORIOUS__PLUGIN_DIR_VIEW', VICTORIOUS__PLUGIN_DIR . 'views/');
define('VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT', VICTORIOUS__PLUGIN_DIR . 'views/Elements/');
define('VICTORIOUS__PLUGIN_DIR_CONTROLLER', VICTORIOUS__PLUGIN_DIR . 'controller/');
define('VICTORIOUS__PLUGIN_DIR_LIB', VICTORIOUS__PLUGIN_DIR . 'lib/');
define('VICTORIOUS__PLUGIN_DIR_LOG', VICTORIOUS__PLUGIN_DIR . 'logs/');
define('VICTORIOUS__PLUGIN_URL_IMAGE', VICTORIOUS__PLUGIN_URL . '_inc/image/');
define('VICTORIOUS__PLUGIN_URL_CSS', VICTORIOUS__PLUGIN_URL . '_inc/css/');
define('VICTORIOUS__PLUGIN_URL_JS', VICTORIOUS__PLUGIN_URL . '_inc/jscript/');
define('VICTORIOUS__PLUGIN_URL_AJAX', VICTORIOUS__PLUGIN_URL . 'victorious.php');
define('VICTORIOUS_IMAGE_URL', $upload_dir['baseurl'] . '/');
define('VICTORIOUS_IMAGE_DIR', $upload_dir['basedir'] . '/');
define('VICTORIOUS_EMAIL_SUPPORT', 'support@victorious.club');
define('VICTORIOUS_PAYPAL_TYPE_NORMAL', 0);
define('VICTORIOUS_PAYPAL_TYPE_PRO', 1);
define('VICTORIOUS_GATEWAY_PAYPAL', 'PAYPAL');
define('VICTORIOUS_GATEWAY_PAYPAL_PRO', 'PAYPAL_PRO');
define('VICTORIOUS_GATEWAY_PAYSIMPLE', 'PAYSIMPLE');
define('VICTORIOUS_FEATURE_IMAGE_WIDTH', 210);
define('VICTORIOUS_FEATURE_IMAGE_HEIGHT', 158);
define('VICTORIOUS_NORMAL_IMAGE_WIDTH', 800);
define('VICTORIOUS_NORMAL_IMAGE_HEIGHT', 600);

$permalink_structure = get_option('permalink_structure');
if ($permalink_structure == '') {
    $mypage = get_page_by_title('Create Contest');
    define('VICTORIOUS_URL_CREATE_CONTEST', home_url() . '/?page_id=' . $mypage->ID);

    $mypage = get_page_by_title('Submit Picks');
    define('VICTORIOUS_URL_SUBMIT_PICKS', home_url() . '/?page_id=' . $mypage->ID);

    $mypage = get_page_by_title('Add Funds');
    define('VICTORIOUS_URL_ADD_FUNDS', home_url() . '/?page_id=' . $mypage->ID);

    $mypage = get_page_by_title('Notify Add Funds');
    define('VICTORIOUS_URL_NOTIFY_ADD_FUNDS', home_url() . '/?page_id=' . $mypage->ID);

    $mypage = get_page_by_title('Success Add Funds');
    define('VICTORIOUS_URL_SUCCESS_ADD_FUNDS', home_url() . '/?page_id=' . $mypage->ID);

    $mypage = get_page_by_title('Rankings');
    define('VICTORIOUS_URL_CONTEST', home_url() . '/?page_id=' . $mypage->ID);

    $mypage = get_page_by_title('Withdrawal History');
    define('VICTORIOUS_URL_REQUEST_HISTORY', home_url() . '/?page_id=' . $mypage->ID);

    $mypage = get_page_by_title('Transactions');
    define('VICTORIOUS_URL_TRANSACTIONS', home_url() . '/?page_id=' . $mypage->ID);

    $mypage = get_page_by_title('Success Withdrawls');
    define('VICTORIOUS_URL_SUCCESS_WITHDRAWLS', home_url() . '/?page_id=' . $mypage->ID);

    $mypage = get_page_by_title('Notify Withdrawls');
    define('VICTORIOUS_URL_NOTIFY_WITHDRAWLS', home_url() . '/?page_id=' . $mypage->ID);

    $mypage = get_page_by_title('Game');
    define('VICTORIOUS_URL_GAME', home_url() . '/?page_id=' . $mypage->ID);

    $mypage = get_page_by_title('Entry');
    define('VICTORIOUS_URL_ENTRY', home_url() . '/?page_id=' . $mypage->ID);

    $mypage = get_page_by_title('Contest');
    define('VICTORIOUS_URL_CONTEST', home_url() . '/?page_id=' . $mypage->ID);
} else {
    define('VICTORIOUS_URL_CREATE_CONTEST', home_url() . '/fantasy/create-contest/');
    define('VICTORIOUS_URL_SUBMIT_PICKS', home_url() . '/fantasy/game/');
    define('VICTORIOUS_URL_GAME', home_url() . '/fantasy/game/');
    define('VICTORIOUS_URL_ENTRY', home_url() . '/fantasy/entry/');
    define('VICTORIOUS_URL_CONTEST', home_url() . '/fantasy/contest/');
    define('VICTORIOUS_URL_LOBBY', home_url() . '/fantasy/lobby/');
    define('VICTORIOUS_URL_ADD_FUNDS', home_url() . '/fantasy/add-funds');
    define('VICTORIOUS_URL_NOTIFY_ADD_FUNDS', home_url() . '/fantasy/notify-add-funds/');
    define('VICTORIOUS_URL_SUCCESS_ADD_FUNDS', home_url() . '/fantasy/success-add-funds/');
    define('VICTORIOUS_URL_REQUEST_HISTORY', home_url() . '/fantasy/withdrawal-history/');
    define('VICTORIOUS_URL_TRANSACTIONS', home_url() . '/fantasy/transactions/');
    define('VICTORIOUS_URL_SUCCESS_WITHDRAWLS', home_url() . '/fantasy/success-withdrawls/');
    define('VICTORIOUS_URL_NOTIFY_WITHDRAWLS', home_url() . '/fantasy/notify-withdrawls/');
    define('VICTORIOUS_URL_FUTURE_EVENT', home_url() . '/fantasy/future-events/');
    define('VICTORIOUS_URL_MY_UPCOMING_ENTRIES', home_url() . '/fantasy/my-upcoming-entries/');
    define('VICTORIOUS_URL_MY_LIVE_ENTRIES', home_url() . '/fantasy/my-live-entries/');
    define('VICTORIOUS_URL_MY_HISTORY_ENTRIES', home_url() . '/fantasy/my-hisoty-entries/');
    define('VICTORIOUS_URL_STATISTIC', home_url() . '/fantasy/statistic/');
    define('VICTORIOUS_URL_LIVESCORE', home_url() . '/fantasy/live-score/');
    define('VICTORIOUS_URL_PAYMENT_CALLBACK', home_url() . '/fantasy/payment_callback');
}



class VictoriousInit
{
    static function active()
    {
        if(!get_option('victorious_done_installed',false))
        {
            self::installDb();
            self::installOptions();
            self::installPages();

            //self::install_widget();
            add_option('victorious_version', VICTORIOUS_VERSION);
            update_option('victorious_done_installed',true);
			self::sendUserInfo();
        }
        else 
        {
            $file = VICTORIOUS__PLUGIN_DIR.'install/install.xml';
            $xml = '';
            if(file_exists($file))
            {
                $xml = simplexml_load_file($file);
            }

            if($xml != null && isset($xml->pages))
            {
                $curPage = get_page_by_title("Fantasy");
                wp_update_post(array(
                    'ID' => $curPage->ID,
                    'post_status' => 'publish',
                    'ping_status' => 'open'
                ));
                foreach($xml->pages->page as $page)
                {
                    $curPage = get_page_by_title((string)$page->name);
                    wp_update_post(array(
                        'ID' => $curPage->ID,
                        'post_status' => 'publish',
                        'ping_status' => 'open'
                    ));
                }
            }
        }
    }
    
    static function deactivate()
    {
        $file = VICTORIOUS__PLUGIN_DIR.'install/install.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }

        if($xml != null && isset($xml->pages))
        {
            $curPage = get_page_by_title("Fantasy");
            wp_update_post(array(
                'ID' => $curPage->ID,
                'post_status' => 'draft',
                'ping_status' => 'closed'
            ));
            foreach($xml->pages->page as $page)
            {
                $curPage = get_page_by_title((string)$page->name);
                wp_update_post(array(
                    'ID' => $curPage->ID,
                    'post_status' => 'draft',
                    'ping_status' => 'open'
                ));
            }
        }
    }

    static function upgrade()
    {
        $file = VICTORIOUS__PLUGIN_DIR.'install/upgrade.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }
        
        if($xml != null && isset($xml->version))
        {
            $curVersion = get_option('victorious_version',false);
            foreach($xml->version as $version)
            {
                $version = (string)$version->number;
                if(version_compare($version, $curVersion, '>'))
                {
					if(file_exists(VICTORIOUS__PLUGIN_DIR.'class.table-credits.php'))
					{
						unlink(VICTORIOUS__PLUGIN_DIR.'class.table-credits.php');
						unlink(VICTORIOUS__PLUGIN_DIR.'class.table-fighters.php');
						unlink(VICTORIOUS__PLUGIN_DIR.'class.table-organizations.php');
						unlink(VICTORIOUS__PLUGIN_DIR.'class.table-pools.php');
						unlink(VICTORIOUS__PLUGIN_DIR.'class.table-statistic.php');
						unlink(VICTORIOUS__PLUGIN_DIR.'class.table-teams.php');
						unlink(VICTORIOUS__PLUGIN_DIR.'class.table-withdrawls.php');
					}
                    self::upgradeDb($version);
                    self::upgradeOptions($version);
                    self::upgradePages($version);
                    self::uninstall_widget();
                    update_option('victorious_version', $version);
                    self::upgradeCallback($version);
                }
            }
        }
    }

    static function uninstall()
    {
        self::uninstallDb();
        self::uninstallOptions();
        self::uninstallPages();
        self::uninstall_widget();
        delete_option('victorious_version');
        delete_option('victorious_done_installed');
    }
	
	static function xml_attribute($object, $attribute)
	{
		if(isset($object[$attribute]))
		{
			return (string) $object[$attribute];
		}
		return null;	
	}
    
    static function sendUserInfo()
    {
        $user = wp_get_current_user();
        $website = "http://".sanitize_url($_SERVER['SERVER_NAME']);
        $to      = VICTORIOUS_EMAIL_SUPPORT;
        $subject = "FanVictor's Client Email";
        $message = 
"Website: ".$website."
Email: ".$user->user_email."
User login: ".$user->user_login;
        $headers = "From: ".$website;
        try 
        {
            wp_mail($to, $subject, $message, $headers);
        } 
        catch (Exception $ex) 
        {
        }
    }

    ////////////////////////////widget////////////////////////////
    static function install_widget()
    {
        $add_to_sidebar = 'victorious_home_sidebar';
        $widget_name = 'lobby_widget';
        $sidebar_options = get_option('sidebars_widgets');
        if(!isset($sidebar_options[$add_to_sidebar]))
        {
            $sidebar_options[$add_to_sidebar] = array('_multiwidget'=>1);
        }

        $homepagewidget = array();
        $count = count($homepagewidget)+1;
        // add first widget to sidebar:
        $sidebar_options[$add_to_sidebar][] = $widget_name.'-'.$count;
        $homepagewidget[$count] = array();
        $homepagewidget['_multiwidget'] = 1;
        $count++;

        update_option('sidebars_widgets',$sidebar_options);
        update_option('widget_'.$widget_name,$homepagewidget);
    }

    static function uninstall_widget()
    {
        delete_option('widget_lobby_widget');
        $add_to_sidebar = 'victorious_home_sidebar';
        $widget_name = 'lobby_widget';
        $sidebar_options = get_option('sidebars_widgets');
        if(isset($sidebar_options[$add_to_sidebar]))
        {
            unset($sidebar_options[$add_to_sidebar]);
            update_option('sidebars_widgets',$sidebar_options);
        }
    }

    static function home_sidebar()
    {
        echo '<div id="victorious_home_sidebar">';
        dynamic_sidebar('victorious_home_sidebar');;
        echo "</div>";
    }

    /*static function init_home_sidebar_area()
    {
        require_once(VICTORIOUS__PLUGIN_DIR_MODEL.'model.php');
        require_once(VICTORIOUS__PLUGIN_DIR_MODEL.'admin/fighters.php');
        require_once(VICTORIOUS__PLUGIN_DIR_MODEL.'admin/teams.php');
        require_once(VICTORIOUS__PLUGIN_DIR_MODEL.'payment.php');
        require_once(VICTORIOUS__PLUGIN_DIR_MODEL.'admin/pools.php');
        require_once(VICTORIOUS__PLUGIN_DIR_MODEL.'admin/sports.php');
        require_once(VICTORIOUS__PLUGIN_DIR_MODEL.'admin/scoringcategory.php');
        require_once(VICTORIOUS__PLUGIN_DIR_MODEL.'victorious.php');
        require_once(VICTORIOUS__PLUGIN_DIR_CONTROLLER."lobby.php");
        add_action( 'widgets_init', function(){
            register_sidebar( array(
                'name' => __('Victorious Home Sidebar', 'victorious'),
                'id' => 'victorious_home_sidebar',
            ));
            register_widget( 'Lobby' );
        });
    }*/

    ////////////////////////////data tables////////////////////////////
    static function installDb()
    {
        global $wpdb;
        $file = VICTORIOUS__PLUGIN_DIR.'install/install.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }

        if($xml != null && isset($xml->queries))
        {
            require_once(ABSPATH.'wp-admin/includes/upgrade.php');
            foreach($xml->queries->query as $query)
            {
                $query = str_replace('{PREFIX}', $wpdb->prefix, (string)$query).";";
                $wpdb->query($query);
            }
        }
    }

    static function upgradeDb($version)
    {
        global $wpdb;
        $file = VICTORIOUS__PLUGIN_DIR.'install/upgrade.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }

        if($xml != null)
        {
            require_once(ABSPATH.'wp-admin/includes/upgrade.php');
            foreach($xml->version as $ver)
            {
                if((string)$ver->number == $version && isset($ver->queries))
                {
                    foreach($ver->queries->query as $query)
                    {
                        $query = str_replace('{PREFIX}', $wpdb->prefix, (string)$query);
                        $wpdb->query($query);
                    }
                }
            }
        }
    }
    
    static function upgradeCallback($version)
    {
        $version = str_replace(".", "_", $version);
        require_once(VICTORIOUS__PLUGIN_DIR.'install/upgrade_callback.php');
        $callback = new VIC_UpgradeCallback();
        $callbak_name = "callback_".$version;
        if(method_exists('VIC_UpgradeCallback', $callbak_name))
        {
            $callback->$callbak_name();
        }
    }

    static function uninstallDb()
    {
        global $wpdb;
        $file = VICTORIOUS__PLUGIN_DIR.'install/uninstall.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }

        if($xml != null && isset($xml->queries))
        {
            require_once(ABSPATH.'wp-admin/includes/upgrade.php');
            foreach($xml->queries->query as $query)
            {
                $query = str_replace('{PREFIX}', $wpdb->prefix, (string)$query);
                $wpdb->query($query);
            }
        }
    }

    ////////////////////////////data options////////////////////////////
    static function installOptions()
    {
        $file = VICTORIOUS__PLUGIN_DIR.'install/install.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }

        if($xml != null && isset($xml->options))
        {
            foreach($xml->options->option as $option)
            {
				$attr = $option->value->attributes();
				$attr = self::xml_attribute($attr, 'type');
				$value = '';
				switch($attr)
				{
					case 'array':
						$value = explode(',', (string)$option->value);
						break;
					default:
						$value = (string)$option->value;
				}
                add_option((string)$option->name, $value);
            }
        }
    }
    
    static function upgradeOptions($version)
    {
        $file = VICTORIOUS__PLUGIN_DIR.'install/upgrade.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }

        if($xml != null && isset($xml->version->options))
        {
            foreach($xml->version as $ver)
            {
                if((string)$ver->number == $version && isset($ver->options))
                {
                    foreach($ver->options->option as $option)
                    {
                        $attr = $option->value->attributes();
                        $attr = self::xml_attribute($attr, 'type');
                        $value = '';
                        switch($attr)
				{
					case 'array':
						$value = explode(',', (string)$option->value);
						break;
					default:
						$value = (string)$option->value;
				}
                        if(isset($option->delete) && !empty($option->delete)){
                            delete_option((string)$option->name);
                        }else{
                             add_option((string)$option->name, $value);
                        }        
                       
                    }
                }
            }
        }
    }

    static function uninstallOptions()
    {
        $file = VICTORIOUS__PLUGIN_DIR.'install/uninstall.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }

        if($xml != null && isset($xml->options))
        {
            foreach($xml->options->option as $option)
            {
                delete_option((string)$option->name);
            }
        }
    }

    ////////////////////////////data pages////////////////////////////
    static function installPages($upgrade = false)
    {
        $file = VICTORIOUS__PLUGIN_DIR.'install/install.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }

        if($xml != null && isset($xml->pages))
        {
            $parent_id = self::insert_page("Fantasy", 'publish', 20);
            $count = 0;
            foreach($xml->pages->page as $page)
            {
                $count += 1;
                $post_parent = $parent_id;
                if(isset($page->parent) && (string)$page->parent == 0)
                {
                    $post_parent = 0;
                }
                $id = self::insert_page((string)$page->name, 'publish', $count, $post_parent, (string)$page->content);
                /*if((string)$page->menu == 1)
                {
                    $post_id = insert_primary_menu(1, $parent_id);
                    add_term_relationship($post_id, $menu_id);
                    add_menu_post_meta($post_id, $p1, $item_parent);
                }*/
            }
        }
    }
    
    static function upgradePages($version)
    {
		global $wpdb;
        $file = VICTORIOUS__PLUGIN_DIR.'install/upgrade.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }

        if($xml != null)
        {
            foreach($xml->version as $ver)
            {
                if((string)$ver->number == $version && isset($ver->pages))
                {
                    $page = get_page_by_title('Fantasy');
                    $parent_id = $page->ID;
                    $count = 0;
                    foreach($ver->pages->page as $page)
                    {
                        $count += 1;
						$name = (string)$page->name;
						$post_name = strtolower($name);
                        $post_parent = $parent_id;
                        $post_content = '';
                        if(isset($page->parent) && (string)$page->parent == 0)
                        {
                            $post_parent = 0;
                        }
                        if(isset($page->content))
                        {
                            $post_content = (string)$page->content;
                        }
                        if (!get_page_by_title($name, 'OBJECT', $post_name))
                        {
                            $post_name = str_replace(" ", "-", $post_name);
                            $query = "INSERT INTO ".$wpdb->prefix."posts(post_title,post_status, post_type, post_parent,post_author, menu_order, post_name, post_content)
                                VALUES('$name', 'publish', 'page', '$post_parent', '".get_current_user_id()."', '$count', '$post_name', '$post_content')";
                            $wpdb->query($query);
                        }
                    }
                    break;
                }
            }
        }
    }

    static function uninstallPages()
    {
        self::delete_menu("Fantasy");
        self::delete_page("Fantasy");
        $file = VICTORIOUS__PLUGIN_DIR.'install/uninstall.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }

        if($xml != null && isset($xml->pages))
        {
            foreach($xml->pages->page as $page)
            {
                self::delete_menu((string)$page->name);
                self::delete_page((string)$page->name);
            }
        }
    }

    ////////////////////////////frontend pages////////////////////////////
    static function showMenu()
    {
        $exclude = array();
        
        //read from xml
        $file = VICTORIOUS__PLUGIN_DIR.'install/install.xml';
        $xml = '';
        if(file_exists($file))
        {
            $xml = simplexml_load_file($file);
        }

        if($xml != null && isset($xml->pages))
        {
            foreach($xml->pages->page as $page)
            {
                $curPage = get_page_by_title((string)$page->name);
                if((get_current_user_id() > 0 && (string)$page->menu_loggedin == 0) ||
                   (get_current_user_id() == 0 && (string)$page->menu == 0))
                {
                    $exclude[] = $curPage->ID;
                }
                if(!get_option('victorious_create_contest') && $curPage->post_title == "Create Contest")
                {
                    $exclude[] = $curPage->ID;
                }
            }
        }
        
        //select menu to show
        if($exclude != null)
        {
            $exclude = implode(',', $exclude);
        }

        $args = array(
        'exclude'      => $exclude,
        'echo'         => 1,
        'menu_class' => 'nav-menu nav');
        return $args;
    }
    
    static function initPage()
    {
        if(!empty($_GET['redirect']))
        {
            return;
        }
		//check jquery loadded
        if(pageSegment(1) == 'fantasy' && pageSegment(2) == "payment_callback")
        {
            require_once(VICTORIOUS__PLUGIN_DIR_CONTROLLER."payment_callback.php");
            $func = pageSegment(3);
            if($func != null && method_exists('VIC_PaymentCallbackController', $func))
            {
                VIC_PaymentCallbackController::$func();
            }
        }
        else if(pageSegment(1) == 'fantasy' && pageSegment(2) == "global")
        {
            require_once(VICTORIOUS__PLUGIN_DIR_CONTROLLER."global.php");
            $func = pageSegment(3);
            if($func != null && method_exists('VIC_GlobalController', $func))
            {
                VIC_GlobalController::$func();
            }
        }
        else if(pageSegment(1) == 'fantasy' || isset($_GET['page_id']))
        {
            if(pageSegment(2) == '')
            {
                wp_redirect(VICTORIOUS_URL_CREATE_CONTEST);exit;
            }
            
            if(!empty($_GET['fv_invitedby']))
            {
                if (session_status() === PHP_SESSION_NONE){session_start();}
                $_SESSION['fv_invitedby'] = sanitize_text_field($_GET['fv_invitedby']);
            }
        
            //require login 
            if(get_current_user_id() == 0)
            {
                $file = VICTORIOUS__PLUGIN_DIR.'install/install.xml';
                $xml = '';
                if(file_exists($file))
                {
                    $xml = simplexml_load_file($file);
                }
                
                if($xml != null && isset($xml->pages))
                {
                    if(isset($_GET['page_id']))
                    {
                        $curPage = get_page(sanitize_text_field($_GET['page_id']));
                    }
                    else 
                    {
                        $curPage = get_page_by_path(pageSegment(1).'/'.pageSegment(2));
                    }
                    
                    foreach($xml->pages->page as $page)
                    {
                        if((string)$page->menu == 0 && (string)$page->public != 1 &&
                           (string)$page->name == $curPage->post_title)
                        {
                            wp_redirect(wp_login_url());exit;
                        }
                    }
                }
            }

            //request page
            if(isset($_GET['page_id']))
            {
                $curPage = get_page(sanitize_text_field($_GET['page_id']));
            }
            else 
            {
                $curPage = get_page_by_path(pageSegment(1).'/'.pageSegment(2));
            }
            if(!get_option('victorious_create_contest') && $curPage->post_title == "Create Contest")
            {
                wp_redirect(home_url());exit;
            }
            if($curPage != null)
            {
                VIC_AutoLoad();
                self::call_page($curPage->post_name);
                
            }
        }

        /*if(pageSegment(1) == "")
        {
            add_filter('the_content', array('VictoriousInit', 'addlobby'));
        }*/
        
        //set top menu no link
        add_action('wp_enqueue_scripts',array('VictoriousInit', 'top_menu_no_link'));
    }

    static function top_menu_no_link()
    {
        $page = get_page_by_path('/fantasy/');
        ?>
        <script type="text/javascript">
            var topmenuid = '<?php echo $page->ID;?>';
        </script>
        <?php
        wp_enqueue_script( 'jquery', 'https://code.jquery.com/jquery-1.12.4.min.js', '','' , true);
        wp_enqueue_script('nolink.js', VICTORIOUS__PLUGIN_URL_JS.'nolink.js');
    }
    
    static function call_page($name)
    {
        $name = trim($name);
        $name = str_replace('-', '', $name);
        if(file_exists(VICTORIOUS__PLUGIN_DIR_CONTROLLER."$name.php"))
        {
            require_once(VICTORIOUS__PLUGIN_DIR_CONTROLLER."$name.php");
            $name = "VIC_".$name."Controller";
            if(class_exists($name))
            {
                $$name = new $name();
                if(method_exists($name, 'process'))
                {
                    add_action( 'wp_loaded', array($name, 'process'));
                }
            }
        }
    }
    
    static function addlobby($content)
    {
        return self::home_sidebar().$content;
    }

    static function insert_page($name, $status, $menu_order = 0, $parent = null, $post_content= '')
    {
        if(!get_page_by_title($name))
        {
            $my_post = array(
                'post_title'    => $name,
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'post_parent'   => $parent,
                'post_author'   => get_current_user_id(),
                'menu_order'    => $menu_order,
                'post_content'  => $post_content
            );
            $id = wp_insert_post( $my_post );
            if($status == 'pending')
            {
                $my_post = array(
                    'ID'            => $id,
                    'post_status'   => 'pending',
                );
                wp_update_post($my_post);
            }
            return $id;
        }
        return null;
    }

    static function delete_page($name, $status = null, $menu_order = 0, $parent = null)
    {
        if(get_page_by_title($name))
        {
            global $wpdb;
            $wpdb->delete($wpdb->prefix."posts", array('post_title' => $name));
        }
    }

    static function delete_menu($name)
    {
        global $wpdb;
        $id = get_page_by_title($name)->ID;
        $table = $wpdb->prefix.'postmeta';
        $sql = "SELECT post_id FROM $table WHERE meta_key = '_menu_item_object_id' AND meta_value = $id";
        $post_id = $wpdb->get_var($sql);

        $wpdb->delete($wpdb->prefix."postmeta", array('post_id' => $post_id));
        $wpdb->delete($wpdb->prefix."term_relationships", array('object_id' => $post_id));
        $wpdb->delete($wpdb->prefix."posts", array('ID' => $post_id));
    }
}

function pluginname_ajaxurl() 
{
    //for qtranslate
    global $q_config;
    ?>
    <script type="text/javascript">
        var current_lang = '<?php echo !empty($q_config['language']) ? esc_html($q_config['language']) : ''; ?>';
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        var no_cash = '<?php echo esc_attr(get_option('victorious_no_cash'));?>';
        var currency_symbol = '<?php echo VIC_GetCurrencySymbol();?>';
        var currency_position = '<?php echo esc_attr(get_option('victorious_currency_position'));?>';
        function VIC_FormatMoney(value, victorious_currency, victorious_currency_position, shorten){
            var is_negative_value = 0;
            if(typeof shorten == 'undefined'){
                value = parseFloat(value);
                if(value < 0)
                {
                    is_negative_value = 1;
                    value = Math.abs(value);
                }
                if (value < 1000)
                {
                    value = value.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
                }
                else if (value < 1000000)
                {
                    value = (value / 1000).toFixed(2).replace(/\.0$/, '') + 'K';
                }
                else if (value < 1000000000)
                {
                    value = (value / 1000000).toFixed(2).replace(/\.0$/, '') + 'M';
                }
                else
                {
                    value = (value / 1000000000).toFixed(2).replace(/\.0$/, '') + 'B';
                }
            }

            if(is_negative_value == 1)
            {
                value = '-' + value;
            }
            if(typeof victorious_currency != 'undefined' && victorious_currency != '')
			{
				currency_symbol = victorious_currency;
			}
			if(typeof victorious_currency_position != 'undefined' && victorious_currency_position != '')
			{
				currency_position = victorious_currency_position;
			}
            <?php
                if(get_option( 'victorious_global_currency_enable' ) != null &&
                   get_option( 'victorious_global_currency_name' ) != null &&
                   get_option( 'victorious_global_currency_symbol' ) != null):
            ?>
                currency_symbol = '<?php echo VIC_GetCurrencySymbol();?>';
            <?php endif;?>
            if(currency_position == 'before')
            {
                return currency_symbol + value;
            }
            else{
                return value + currency_symbol;
            }
        }
        function VIC_DateTranslate(value)
        {
            var victorious_date_format = '<?php echo esc_attr(get_option("victorious_date_format"));?>';
            if(victorious_date_format.trim() != "")
            {
                var temp = value.split(" ");
                var temp2 = temp[1];
                temp[1] = temp[2];
                temp[2] = temp2;
                value = temp.join(" ");
            }
            value = value.toLowerCase();
            value = value.split(" ");
            var texts = {
                'mon' : '<?php echo esc_html(__('mon', 'victorious'));?>',
                'tue' : '<?php echo esc_html(__('tue', 'victorious'));?>',
                'wed' : '<?php echo esc_html(__('wed', 'victorious'));?>',
                'thu' : '<?php echo esc_html(__('thu', 'victorious'));?>',
                'fri' : '<?php echo esc_html(__('fri', 'victorious'));?>',
                'sat' : '<?php echo esc_html(__('sat', 'victorious'));?>',
                'sun' : '<?php echo esc_html(__('sun', 'victorious'));?>',
                'jan' : '<?php echo esc_html(__('jan', 'victorious'));?>',
                'feb' : '<?php echo esc_html(__('feb', 'victorious'));?>',
                'mar' : '<?php echo esc_html(__('mar', 'victorious'));?>',
                'apr' : '<?php echo esc_html(__('apr', 'victorious'));?>',
                'may' : '<?php echo esc_html(__('may', 'victorious'));?>',
                'jun' : '<?php echo esc_html(__('jun', 'victorious'));?>',
                'jul' : '<?php echo esc_html(__('jul', 'victorious'));?>',
                'aug' : '<?php echo esc_html(__('aug', 'victorious'));?>',
                'sep' : '<?php echo esc_html(__('sep', 'victorious'));?>',
                'oct' : '<?php echo esc_html(__('oct', 'victorious'));?>',
                'nov' : '<?php echo esc_html(__('nov', 'victorious'));?>',
                'dec' : '<?php echo esc_html(__('dec', 'victorious'));?>'
            };
            if(value != null)
            {
                for(var k in value)
                {
                    var item = value[k];
                    value[k] = typeof texts[item] != 'undefined' ? texts[item].replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();}) : item;
                }
                value = value.join(" ");
            }
            return value;
        }
        
        function VIC_ParseGameTypeName(gametype)
		{
            var gametypes = JSON.parse('<?php $gametypes = VIC_GameTypeList(); $gametypes = json_encode($gametypes); echo str_replace("'", "\'", $gametypes);?>');
			if(gametypes[gametype] !== undefined )
            {
                return gametypes[gametype];
            }
			return gametype;
		}
    </script>
    <?php
}
function pageSegment($pos = 0)
{
	if($pos == 3 && isset($_GET['league_id'])){
        return sanitize_text_field($_GET['league_id']);
    }
    $siteUrl = explode('/', get_site_url().'/');
	$siteUrl = array_filter($siteUrl);
	$siteUrl = array_values($siteUrl);
	$offset = count($siteUrl) - 2;
    $url =  sanitize_url($_SERVER['REQUEST_URI']);
    $url = explode('/', $url);
    $offset_url = 0;
    if($url != null)
    {
        foreach($url as $k => $item)
        {
            if($item == 'fantasy')
            {
                break;
            }
            else if($item != '' && $k > $offset)
            {
                $offset_url += 1;
            }
        }
    }
    $offset += $offset_url;
    if(isset($url[$pos + $offset]))
    {
        return $url[$pos + $offset];
    }
    return null;
}

function VIC_Redirect($url, $msg = null, $blank = false)
{
    if($msg != null && function_exists('add_settings_error'))
    {
        add_settings_error('general', 'settings-updated', __($msg), 'updated', 'victorious');
        set_transient('settings_errors', get_settings_errors(), 30);
    }
    else if($msg != null)
    {
        VIC_SetMessage($msg);
    }
    if(!$blank)
    {
        $url = add_query_arg( 'settings-updated', 'true', $url);
    }
    wp_redirect($url);
    exit;
}
function VIC_SetMessage($msg)
{
    if (session_status() === PHP_SESSION_NONE){session_start();} 
    $_SESSION['vic_msg'] = $msg;
}

function VIC_GetMessage()
{
    if (session_status() === PHP_SESSION_NONE){session_start();} 
    if(isset($_SESSION['vic_msg']))
    {
        $msg = sanitize_text_field($_SESSION['vic_msg']);
        unset($_SESSION['vic_msg']);
        echo '<p class="alert alert-warning public_message" style="display: block;">'.esc_html($msg).'</p>';
    }
}

function initVictoriousCookies(){
    setcookie( 'victorious_user_id', get_current_user_id(),time()+86400, COOKIEPATH, COOKIE_DOMAIN );
}
add_action('init','initVictoriousCookies');
require_once(VICTORIOUS__PLUGIN_DIR_MODEL.'model.php');
define("CP_ACTION_ADD_MONEY", "ADD_MONEY");
define("CP_ACTION_EXTRA_DEPOSIT", "EXTRA_DEPOSIT");
define("CP_DISCOUNT_PERCENT", "PERCENT");
define("CP_DISCOUNT_PRICE", "PRICE");

if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']))
{
    VIC_AutoLoad();

    //ajax page
    require_once(VICTORIOUS__PLUGIN_DIR.'class.ajax.php');
}
else
{
    if (is_admin()) 
    {
        $fv_menu = array('manage-dashboard', 'manage-auto-contests', 'add-auto-contest', 'manage-sports', 'add-sports', 'manage-pools', 'add-pools',
                      'manage-contests', 'add-contests', 'manage-fighters', 'add-fighters',
                      'manage-teams', 'add-teams', 'statistic', 'credits',
                      'withdrawls', 'manage-playerposition', 'add-playerposition', 'manage-scoringcategory',
                      'add-scoringcategory', 'manage-players', 'add-players', 'manage-playernews',
                      'add-playernews', 'transactions','teams-background','user-multi-entries', 'manage-balance-types', 'add-balance-type');
        //admin page
        require_once(VICTORIOUS__PLUGIN_DIR.'class.victorious-admin.php');
        $victorious = new Victorious_Admin();
        $victorious->init();

        if((empty($_GET['page']) || !in_array(sanitize_text_field($_GET['page']), $fv_menu)) && pageSegment(2) != 'admin-ajax.php')
        {
            return;
        }

        VIC_AutoLoad();

        //controller
        VIC_AutoLoadAdminController();
        
        add_action('admin_enqueue_scripts', 'pluginname_ajaxurl');
    }
    else
    {
        VIC_AutoLoad();
        require_once(VICTORIOUS__PLUGIN_DIR_CONTROLLER."lobby.php");
        add_action('wp_enqueue_scripts','pluginname_ajaxurl'); 

        //request page
        add_action('init', array('VictoriousInit', 'initPage'));

        //select menu to show
        add_filter( 'wp_page_menu_args', array('VictoriousInit', 'showMenu'));
    }
}

function VIC_Currency(){
    $data = array(
        "USD|$" => __('U.S. Dollar', 'victorious'),
        "EUR|€" => __('Euro', 'victorious'),
        "GBP|£" => __('Pound Sterling', 'victorious'),
        "JPY|¥" => __('Japanese Yen', 'victorious'),
        "CAD|C$" => __('Canadian Dollar', 'victorious'),
        "AUD|AU$" => __('Australian Dollar', 'victorious'),
        "CHF|Fr" => __('Swiss Franc', 'victorious'),
        "SGD|SGD$" => __('Singapore Dollar', 'victorious'),
        "HKD|HK$" => __('Hong Kong Dollar', 'victorious'),
        "XOF|XOF" => __('CFA Franc BCEAO', 'victorious'),
        "NGN|₦" => __('Nigeria Naira', 'victorious'),
        "GHS|GH₵" => __('Ghanaian New Cedi', 'victorious'),
        "ZAR|R" => __('South African Rand', 'victorious'),
        "KES|KSh" => __('Kenyan Shilling', 'victorious'),
        "HTG|G" => __('Haitian Gourde', 'victorious'),
        "XFT|XFT" => __('Footy Cash', 'victorious'),
    );
    return $data;
}

function VIC_FormatMoney($value, $victorious_currency = null, $victorious_currency_position = null, $shorten = true){
    if($value == ''){
        $value = 0;
    }
    if($shorten){
        $value = VIC_FormatMoney_Shorten($value, 1);
    }
    $currency = get_option('victorious_currency') != null ? get_option('victorious_currency') : "USD|$";
    $currency_posiiton = get_option('victorious_currency_position') != null ? get_option('victorious_currency_position') : "before";
    if($victorious_currency != null)
	{
		$currency = $victorious_currency;
	}
	if($victorious_currency_position != null)
	{
		$currency_posiiton = $victorious_currency_position;
	}
    if(get_option( 'victorious_global_currency_enable' ) != null &&
       get_option( 'victorious_global_currency_name' ) != null &&
       get_option( 'victorious_global_currency_symbol' ) != null)
	{
		$currency = get_option( 'victorious_global_currency_name' ).'|'.get_option( 'victorious_global_currency_symbol' );
	}
    $currency = explode("|", $currency);
    $currency_code = $currency[0];
    $currency_symbol = $currency[1];
    if($currency_posiiton == "before"){
        return $currency_symbol.$value;
    }
    else{
        return $value.$currency_symbol;
    }
}

function VIC_FormatMoney_Shorten($n, $precision = 3) {
    if ($n < 1000) {
        // Anything less than a million
        $n_format = VIC_NumberFormatPrecision($n);
    } 
    else if ($n < 1000000) {
        // Anything less than a thousand
        $n_format = VIC_NumberFormatPrecision($n / 1000, $precision) . 'K';
    } 
    else if ($n < 1000000000) {
        // Anything less than a billion
        $n_format = VIC_NumberFormatPrecision($n / 1000000, $precision) . 'M';
    } 
    else {
        // At least a billion
        $n_format = VIC_NumberFormatPrecision($n / 1000000000, $precision) . 'B';
    }

    return $n_format;
}

function VIC_NumberFormatPrecision($number, $precision = 2, $separator = '.')
{
    $numberParts = explode($separator, $number);
    $response = $numberParts[0];
    if(count($numberParts)>1){
        $response .= $separator;
        $response .= substr($numberParts[1], 0, $precision);
    }
    return $response;
}

function VIC_GetCurrencySymbol(){
    $currency = get_option('victorious_currency') != null ? get_option('victorious_currency') : "USD|$";
    if(get_option( 'victorious_global_currency_enable' ) != null &&
       get_option( 'victorious_global_currency_name' ) != null &&
       get_option( 'victorious_global_currency_symbol' ) != null)
	{
		$currency = get_option( 'victorious_global_currency_name' ).'|'.get_option( 'victorious_global_currency_symbol' );
	}
    $currency = explode("|", $currency);
    return $currency[1];
}

function VIC_GetCurrencyCode(){
    $currency = get_option('victorious_currency') != null ? get_option('victorious_currency') : "USD|$";
    $currency = explode("|", $currency);
    return $currency[0];
}

function VIC_ScoringTranslate($value){
    $texts = array(
        'goals' => esc_html(__('goals', 'victorious')),
        'assists' => esc_html(__('assists', 'victorious')),
        'clean sheet' => esc_html(__('clean sheet', 'victorious')),
        'saves' => esc_html(__('saves', 'victorious')),
        'shots on goal' => esc_html(__('shots on goal', 'victorious')),
        'minutes played' => esc_html(__('minutes played', 'victorious')),
        'fouls drawn' => esc_html(__('fouls drawn', 'victorious')),
        'own goal' => esc_html(__('own goal', 'victorious')),
        'yellow cards' => esc_html(__('yellow cards', 'victorious')),
        'goals conceded' => esc_html(__('goals conceded', 'victorious')),
        'offsides' => esc_html(__('offsides', 'victorious')),
        'red cards' => esc_html(__('red cards', 'victorious')),
        'pen miss' => esc_html(__('pen miss', 'victorious')),
        'shots' => esc_html(__('shots', 'victorious')),
		'pen score' => esc_html(__('pen score', 'victorious')),
		'60 minutes played' => esc_html(__('60 minutes played', 'victorious')),
        
        'mon' => esc_html(__('mon', 'victorious')),
        'tue' => esc_html(__('tue', 'victorious')),
        'wed' => esc_html(__('wed', 'victorious')),
        'thu' => esc_html(__('thu', 'victorious')),
        'fri' => esc_html(__('fri', 'victorious')),
        'sat' => esc_html(__('sat', 'victorious')),
        'sun' => esc_html(__('sun', 'victorious')),
        'jan' => esc_html(__('jan', 'victorious')),
        'feb' => esc_html(__('feb', 'victorious')),
        'mar' => esc_html(__('mar', 'victorious')),
        'apr' => esc_html(__('apr', 'victorious')),
        'may' => esc_html(__('may', 'victorious')),
        'jun' => esc_html(__('jun', 'victorious')),
        'jul' => esc_html(__('jul', 'victorious')),
        'aug' => esc_html(__('aug', 'victorious')),
        'sep' => esc_html(__('sep', 'victorious')),
        'oct' => esc_html(__('oct', 'victorious')),
        'nov' => esc_html(__('nov', 'victorious')),
        'dec' => esc_html(__('dec', 'victorious')),
    );
    return isset($texts[$value]) ? $texts[$value] : $value;
}

function VIC_DateTranslate($value, $format = true){
    $victorious_date_format = get_option("victorious_date_format");
    if(trim($victorious_date_format) != "" && $format)
    {
        $temp = explode(" ", $value);
        if(count($temp) <= 3)
        {
            $value = date($victorious_date_format, strtotime($value));
        }
        else 
        {
            $value = date($victorious_date_format." g:i a", strtotime($value));
        }
    }
    $value = strtolower($value);
    $value = explode(" ", $value);
    $texts = array(
        'mon' => esc_html(__('mon', 'victorious')),
        'tue' => esc_html(__('tue', 'victorious')),
        'wed' => esc_html(__('wed', 'victorious')),
        'thu' => esc_html(__('thu', 'victorious')),
        'fri' => esc_html(__('fri', 'victorious')),
        'sat' => esc_html(__('sat', 'victorious')),
        'sun' => esc_html(__('sun', 'victorious')),
        'jan' => esc_html(__('jan', 'victorious')),
        'feb' => esc_html(__('feb', 'victorious')),
        'mar' => esc_html(__('mar', 'victorious')),
        'apr' => esc_html(__('apr', 'victorious')),
        'may' => esc_html(__('may', 'victorious')),
        'jun' => esc_html(__('jun', 'victorious')),
        'jul' => esc_html(__('jul', 'victorious')),
        'aug' => esc_html(__('aug', 'victorious')),
        'sep' => esc_html(__('sep', 'victorious')),
        'oct' => esc_html(__('oct', 'victorious')),
        'nov' => esc_html(__('nov', 'victorious')),
        'dec' => esc_html(__('dec', 'victorious')),
    );
    if($value != null)
    {
        foreach($value as $k => $item)
        {
            $value[$k] = isset($texts[$item]) ? ucfirst($texts[$item]) : $item;
        }
        $value = implode(" ", $value);
    }
    return $value;
}

//change language
function VIC_ChangeLanaguage($locale)
{
    $cookie_name = "fvlang";
    if(isset($_GET[$cookie_name]))
    {
        if(sanitize_text_field($_GET[$cookie_name]) != "")
        {
            setcookie($cookie_name, "", 0);
            setcookie($cookie_name, "", 0, '/');
            setcookie($cookie_name, sanitize_text_field($_GET[$cookie_name]),time() + (86400 * 30), '/');
            $locale = sanitize_text_field($_GET[$cookie_name]);
        }
        else
        {
            setcookie($cookie_name, "", 0);
            setcookie($cookie_name, "", 0, '/');
            setcookie($cookie_name, 'en_US',time() + (86400 * 30), '/');
            $locale = 'en_US';
        }
    }
    else if(!empty($_COOKIE[$cookie_name]))
    {
        $locale = sanitize_text_field($_COOKIE[$cookie_name]);
    }
    return $locale;
}
add_filter( 'locale', 'VIC_ChangeLanaguage');

//redirect after login
function VIC_RedirectAfterLogin( $user_login, $user ) {
    $payment = new VIC_Payment();
    $payment->initUserBalance($user->ID);
    if(!empty($_GET['redirect']))
    {
        wp_redirect(sanitize_text_field($_GET['redirect']));exit;
    }
}
add_action('wp_login', 'VIC_RedirectAfterLogin', 10, 2);

//balance short code
function VIC_GetUserBalance($user_id = null)
{
    if($user_id == null)
    {
        $user_id = (int)get_current_user_id();
    }
    if((int)$user_id > 0)
    {
        $victorious = new VIC_Victorious();
        $global_setting = $victorious->getGlobalSetting();
        $default_only = empty($global_setting['allow_multiple_balances']) ? true : false;

        $balanceType = new VIC_BalanceType();
        $payment = new VIC_Payment();
        $balance_types = $balanceType->getBalanceTypeList(array(
            'default_only' => $default_only
        ));
        $user_balances = $payment->getUserBalanceList($user_id);
        $user_balances = $payment->groupArrayByKey($user_balances, array('balance_type_id'));

        $balance_content = array();
        if($balance_types != null){
            foreach($balance_types as $balance_type){
                $code = $balance_type['currency_code'].'|'.$balance_type['symbol'];
                $balance = isset($user_balances[$balance_type['id']]) ? $user_balances[$balance_type['id']]['balance'] : 0;
                $balance_content[] = VIC_FormatMoney($balance, $code);
            }
        }
        $balance_content = implode(' - ', $balance_content);
        return $balance_content;
    }
    return VIC_FormatMoney(0);
}
add_shortcode('victorious_balance', 'VIC_GetUserBalance');

//change game type name
function VIC_ParseGameTypeName($gametype)
{
    $gametypes = VIC_GameTypeList();
    return isset($gametypes[$gametype]) ? esc_html($gametypes[$gametype]) : esc_html($gametype);
}

function VIC_GameTypeList()
{
    return array(
        VICTORIOUS_GAME_TYPE_PLAYERDRAFT => __("Player Draft", "victorious"),
        VICTORIOUS_GAME_TYPE_LIVEDRAFT => __("Live Draft", "victorious"),
        VICTORIOUS_GAME_TYPE_PICKEM => __("Pick 'em", "victorious"),
        VICTORIOUS_GAME_TYPE_PICKSPREAD => __("Pick 'em Against Spread", "victorious"),
        VICTORIOUS_GAME_TYPE_PICKMONEY => __("Pick 'em Against Money Line", "victorious"),
        VICTORIOUS_GAME_TYPE_PICKTIE => __("Pick 'em / Tie breaker", "victorious"),
        VICTORIOUS_GAME_TYPE_PICKULTIMATE => __("Ultimate Pickem", "victorious"),
        VICTORIOUS_GAME_TYPE_PICKSQUARES => __("Pick Squares", "victorious"),
        VICTORIOUS_GAME_TYPE_GOLFSKIN => __("Skin", "victorious"),
        VICTORIOUS_GAME_TYPE_HOWMANYGOALS => __("How many goals?", "victorious"),
        VICTORIOUS_GAME_TYPE_BOTHTEAMSTOSCORE => __("Both teams to score?", "victorious"),
        VICTORIOUS_GAME_TYPE_ROUNDPICKEM => __("Round Pick'em", "victorious"),
        VICTORIOUS_GAME_TYPE_BRACKET => __("Bracket", "victorious"),
        VICTORIOUS_GAME_TYPE_GOLIATH => __("Goliath", "victorious"),
        VICTORIOUS_GAME_TYPE_MINIGOLIATH => __("Mini Goliath", "victorious"),
        VICTORIOUS_GAME_TYPE_SURVIVAL => __("Survival Pool", "victorious"),
        VICTORIOUS_GAME_TYPE_TEAMDRAFT => __("Draft", "victorious"),
        VICTORIOUS_GAME_TYPE_SPORTBOOK => __("Sportbook", "victorious"),
        VICTORIOUS_GAME_TYPE_UPLOADPHOTO => __("Upload Photo", "victorious"),
        VICTORIOUS_GAME_TYPE_PORTFOLIO => __("Portfolio", "victorious"),
        VICTORIOUS_GAME_TYPE_OLDDRAFT => __("Old Draft", "victorious"),
        VICTORIOUS_GAME_TYPE_NFL_PLAYOFF => __("NFL Playoff", "victorious"),
    );
}

//parse fixture status
function VIC_ParseFixtureStatusName($type)
{
    $types = VIC_FixtureStatusList();
    $select_type = strtolower($type);
    return isset($types[$select_type]) ? $types[$select_type] : $type;
}

function VIC_FixtureStatusList()
{
    return array(
        "scheduled" => __("Scheduled", "victorious"),
        "inprogress" => __("In Progress", "victorious"),
        "final" => __("Final", "victorious"),
        "suspended" => __("Suspended", "victorious"),
        "postponed" => __("Postponed", "victorious"),
        "canceled" => __("Canceled", "victorious")
    );
}

//add footer
function VIC_Footer() {
    echo '<div style="background-color:#FFFFFF;color:#333333;padding:20px;">';
    echo '<div style="text-align:center;font-size:12px;">';
    echo esc_html(__('This site is a Game of Skill and is 100% LEGAL in the United States. This site operates in full compliance with the Unlawful Internet Gambling Enforcement Act of 2006.', 'victorious'));
    echo '</div>';
    echo '<div style="text-align:center;margin-top:5px;font-size:12px;">';
    echo sprintf(esc_html(__('Due to state and provincial laws residents of Arizona, Iowa, Louisiana, Montana, Vermont, New York and Quebec may only play in free competitions. All other trademarks and logos belong to their respective owners. %s Fantasy Sports', 'victorious')), '<a style="color:#000000" href="http://victorious.club" target="_blank">victorious.club</a>');
    echo '</div>';
    echo '</div>';
}

//add_action('wp_footer', 'VIC_Footer');

//lobby
function VIC_LobbyShortcode($atts, $content = null) {
    if(!is_admin())
	{
		return VIC_LobbyController::show($content);
	}
}
add_shortcode('victorious_lobby', 'VIC_LobbyShortcode');

//global leaderboard
function VIC_LivepointShortcode(){
    if(!is_admin()){
        require_once(VICTORIOUS__PLUGIN_DIR_CONTROLLER.'livescore.php');
        return VIC_LivescoreController::showLivePoint();
    }
}
add_shortcode('victorious_livepoint', 'VIC_LivepointShortcode');

//b icon
function VIC_addBiConNextAccount(){
    if(get_option('victorious_b_icon'))
    {
        victorious_require_class();
        $fv = new VIC_Victorious();
        $result = $fv->countUserJoinedContest();
        $result = json_decode($result,true);

        if(isset($result['is_allow']) && $result['is_allow']){
            ?>
            <script type="text/javascript">
                window.onload = function(){
                    var username =  jQuery('.menu-right .current-user span').text();
                    username = username+"(B)";
                    jQuery('.menu-right .current-user span').html(username);
                }
            </script>
            <?php
        }
    }
}
add_action('wp_enqueue_scripts','VIC_addBiConNextAccount'); 

//empty image
function VIC_parseTeamImage($image = "")
{
    if(empty($image))
    {
        return VICTORIOUS__PLUGIN_URL_IMAGE."no_team_image.png";
    }
    return $image;
}

function VIC_parsePlayerImage($image = "")
{
    if(empty($image))
    {
        return VICTORIOUS__PLUGIN_URL_IMAGE."no_player_image.png";
    }
    return $image;
}

//injury status
function VIC_InjuryStatus($status = "")
{
    $data = array(
        1 => array('id' => 1, 'name' => __("Injured Reserve", "victorious"), 'alias' => 'IR'),
        2 => array('id' => 2, 'name' => __("Out", "victorious"), 'alias' => 'O'),
        3 => array('id' => 3, 'name' => __("Doubtful", "victorious"), 'alias' => 'D'),
        4 => array('id' => 4, 'name' => __("Questionable", "victorious"), 'alias' => 'Q'),
        5 => array('id' => 5, 'name' => __("Probable", "victorious"), 'alias' => 'P'),
        6 => array('id' => 6, 'name' => __("Not Active", "victorious"), 'alias' => 'NA'),
        7 => array('id' => 7, 'name' => __("Day to Day", "victorious"), 'alias' => 'DtD'),
        8 => array('id' => 8, 'name' => __("Sidelined", "victorious"), 'alias' => 'S')
    );
    if($status != "")
    {
        return isset($data[$status]) ? $data[$status] : "";
    }
    return $data;
}

//parse player indicator
function VIC_PlayerIndicator($indicator_alias = "", $is_pitcher = false)
{
    $html_indicator = '';
    switch ($indicator_alias)
    {
        case 'IR':
            $html_indicator = '<span class="f-player-badge f-player-badge-injured-out">IR</span>';
            break;
        case 'O':
            $html_indicator = '<span class="f-player-badge f-player-badge-injured-out">O</span>';
            break;
        case 'D':
            $html_indicator = '<span class="f-player-badge f-player-badge-injured-possible">D</span>';
            break;
        case 'Q':
            $html_indicator = '<span class="f-player-badge f-player-badge-injured-possible">Q</span>';
            break;
        case 'P':
            $html_indicator = '<span class="f-player-badge f-player-badge-injured-probable">P</span>';
            break;
        case 'NA':
            $html_indicator = '<span class="f-player-badge f-player-badge-injured-out">NA</span>';
            break;
        case 'DtD':
            $html_indicator = '<span class="f-player-badge f-player-badge-injured-out">DtD</span>';
            break;
        case 'S':
            $html_indicator = '<span class="f-player-badge f-player-badge-injured-possible">S</span>';
            break;
    }
    $html_pitcher = '';
    if ($is_pitcher)
    {
        $html_pitcher = ' <span class="f-player-badge f-player-badge-injured-possible">St</span> ';
    }
    return $html_pitcher.$html_indicator;
}

function VIC_IpAddress()
{
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';

    return $ipaddress;
}

function VIC_WithdrawalStatus($select = "")
{
    $data = array(
        'NEW' => __('New', 'victorious'),
        'APPROVED' => __('Approved', 'victorious'),
        'PENDING' => __('Pending', 'victorious'),
        'DECLINED' => __('Declined', 'victorious'),
        'SENT_EMAIL' => __('Sent email', 'victorious'),
    );
    if($select != "")
    {
        return isset($data[$select]) ? $data[$select] : "";
    }
    return $data;
}

function VIC_CanStartSportBook(){
    return true;
    $start = strtotime('monday this week');
    $start = date('Y-m-d', $start).' 19:00:00';
    $current = date('Y-m-d H:i:s');
    if(strtotime($current) >= strtotime($start)){
        return true;
    }
    return false;
}

function VIC_AutoLoad(){
    $dir = VICTORIOUS__PLUGIN_DIR_MODEL.'admin';
    $admin_models = scandir($dir);
    if($admin_models != null){
        foreach($admin_models as $admin_model) {
            if ($admin_model == "." || $admin_model == ".." || strpos($admin_model, '.php') === false) {
                continue;
            }

            require_once($dir.'/'.$admin_model);
        }
    }

    $dir = VICTORIOUS__PLUGIN_DIR_MODEL;
    $models = scandir($dir);
    if($models != null){
        foreach($models as $model) {
            if ($model == "." || $model == ".." || strpos($model, '.php') === false) {
                continue;
            }

            require_once($dir.$model);
        }
    }
}

function VIC_AutoLoadAdminController(){
    $dir = VICTORIOUS__PLUGIN_DIR_CONTROLLER.'admin';
    $controllers = scandir($dir);
    if($controllers != null){
        foreach($controllers as $controller) {
            if ($controller == "." || $controller == ".." || strpos($controller, '.php') === false) {
                continue;
            }

            require_once($dir.'/'.$controller);
        }
    }
}

function VIC_GetTableName($table_name){
    global $wpdb;
    return $wpdb->prefix.VICTORIOUS_TABLE_PREFIX.$table_name;
}

function VIC_CurrencyPositionList($select = ''){
    $data = array(
        VICTORIOUS_CURRENCY_POS_BEFORE => __('Before value', 'victorious'),
        VICTORIOUS_CURRENCY_POS_AFTER => __('After value', 'victorious'),
    );
    return $select != '' && isset($data[$select]) ? $data[$select] : $data;
}

function VIC_ParseRank($number)
{
    $number = trim($number);
    if($number == 0 || $number == null || !is_numeric($number)){
        return '-';
    }
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13))
    {
        return $number. 'th';
    }
    else
    {
        return $number. $ends[$number % 10];
    }
}

function VIC_GetUserId(){
    return !empty($_COOKIE['victorious_user_id']) ? sanitize_text_field($_COOKIE['victorious_user_id']) : '';
}
?>