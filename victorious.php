<?php
/**
 * Plugin Name: Victorious
 * Plugin URI: https://victorious.club
 * Description: This plugin will convert your Wordpress site into a fully fledged fantasy/betting sports or event gaming platform. You can create you own contests with different game styles such as fantasy draft, pick em, fantasy betting and so many more game styles. Use it for any reason you would want to run a contest, it doens't need to be related to only sports. This plugin comes with all the functionality needed for users to create contests themselves, deposit funds into the site balance and request withdrawls.  There is also a LIVE scoring page where end uers can watch in REAL TIME LIVE updates as the event is happening. They can watch themselves climb up the LIVE LEADERBOARD. Crypto Currencies are supported as well.
 * Version: 1.4
 * Author: Lucci Club
 * Author URI: https://lucci.club
 * License: GPL2
 */

/*  Copyright 2022 Lucci Club  (email : support@victorious.club)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

//init
define('VICTORIOUS__PLUGIN_DIR', plugin_dir_path(__FILE__));
require_once(VICTORIOUS__PLUGIN_DIR . 'constants.php');
require_once(VICTORIOUS__PLUGIN_DIR . 'class.init.php');
register_activation_hook(__FILE__, array('VictoriousInit', 'active'));
register_deactivation_hook(__FILE__, array('VictoriousInit', 'deactivate'));
register_uninstall_hook(__FILE__, array('VictoriousInit', 'uninstall'));
add_action('plugins_loaded', array('VictoriousInit', 'upgrade'));
require_once(plugin_dir_path(__FILE__) . "/languages/js-pt_PT.php");

//add_action('init', 'session_start');

function vic_init() {
    load_plugin_textdomain('victorious', false, dirname(plugin_basename(__FILE__)) . "/languages/");
}

add_action('plugins_loaded', 'vic_init');
$victorious_allow_change_background = victoriousCheckAllowChangebackground();

function victorious_require_class() {
    require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'admin/pools.php');
    require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'admin/fighters.php');
    require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'admin/teams.php');
    require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'admin/organizations.php');
    require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'admin/user.php');
    require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'admin/statistic.php');
    require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'payment.php');
    require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'victorious.php');
    require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'mypaypal.php');
    require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'admin/sports.php');
    require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'admin/autocontest.php');
    require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'admin/playerposition.php');
    require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'admin/scoringcategory.php');
    require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'admin/players.php');
    require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'admin/leagues.php');
    require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'admin/playernews.php');
    require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'CouponModel.php');
}

// add field when sign up
if ($victorious_allow_change_background) {
    add_action('register_form', 'victorious_add_registration_fields');
}

function victoriousShowTeamsField() {
    $mSports = new VIC_Sports();
    $aSports = $mSports->getSports();
    $aSportTeams = array();
    foreach ($aSports as $aSport) {
        if ($aSport['is_team'] != 1 || $aSport['is_active'] != 1 || $aSport['siteID'] != 0) {
            continue;
        }
        $aSport = $aSport['child'];
        foreach ($aSport as $sport) {
            if ($sport['is_team'] != 1 || $sport['is_active'] != 1 || $sport['siteID'] != 0) {
                continue;
            }
            $aSportTeams[$sport["id"]] = $sport["name"];
        }
    }

    if (empty($aSportTeams)) {
        return;
    }
    $victorious = new VIC_Victorious();
    $listSports = $victorious->getTeamsBySports(array_keys($aSportTeams));
    return array($listSports, $aSportTeams);
}

function victorious_add_registration_fields() {
    require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'admin/sports.php');
    require_once(VICTORIOUS__PLUGIN_DIR_MODEL . 'victorious.php');
    list($listSports, $aSportTeams) = victoriousShowTeamsField();
    if (!empty($listSports)) {
        echo '<p><label for="team_info">' . esc_html(__('Teams', 'victorious')) . '</label>';
        echo '<select  name="team_info" id="team-info" class="input">';
        foreach ($listSports as $sport_id => $sports) {
            echo '<optgroup  label="' . esc_html($aSportTeams[$sport_id]) . '">';
            foreach ($sports as $item) {
                echo '<option value="' . esc_html($sport_id) . '_' . esc_html($item['teamID']) . '">' . esc_html($item['name']) . '</option>';
            }
            echo '</optgroup>';
        }
        echo '</select></p>';
    }
}

// for user profile
if ($victorious_allow_change_background) {
    add_action('show_user_profile', 'victorious_add_custom_profile_fields');
}

function victorious_add_custom_profile_fields($user) {
    victorious_require_class();
    global $wpdb;
    $tb_user_teams = $wpdb->prefix . 'user_teams';
    $user_id = $user->data->ID;
    $team = $wpdb->get_row("SELECT * FROM $tb_user_teams WHERE user_id=$user_id", ARRAY_A);

    $team_id = 0;
    $id = 0;
    if (!empty($team)) {
        $team_id = $team['team_id'];
    }
    list($aSports, $aSportsName) = victoriousShowTeamsField();
    if (empty($aSports)) {
        return;
    }
    ?>

    <table class="form-table">
        <tr>
            <th><label for="teams-info"><?php echo esc_html(__('Teams', 'victorious')) ?></label></th>
            <td>
                <select id="teams-info" name="teams_info">
                    <?php foreach ($aSports as $sport_id => $aSport): ?>
                        <optgroup label="<?php echo esc_html($aSportsName[$sport_id]) ?>">
                            <?php foreach ($aSport as $sport): ?>
                                <option <?php echo ($sport['teamID'] == $team_id) ? 'selected' : '' ?> value="<?php echo esc_html($sport_id) . '_' . esc_html($sport['teamID']) ?>"><?php echo esc_html($sport['name']); ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

// change background image
function victoriousCheckAllowChangebackground() {
    victorious_require_class();
    $fv = new VIC_Victorious();
    $is_allow = $fv->checkAllowChangeBackground();
    //return $is_allow['is_allow'];
    return false;
}

add_filter('body_class', 'vic_hardcore_background');

function vic_hardcore_background($classes) {
    $classes[] = 'fv_background_class';
    return $classes;
}

if ($victorious_allow_change_background) {
    //add_action('wp_enqueue_scripts', 'vic_change_background_by_user');
}

function vic_change_background_by_user() {
    $is_allow = victoriousCheckAllowChangebackground();
    if (!$is_allow) {
        return false;
    }

    global $wpdb;
    $tbl_user_teams = $wpdb->prefix . 'user_teams';
    $user_id = get_current_user_id();
    $team = $wpdb->get_row("SELECT * FROM $tbl_user_teams WHERE user_id=$user_id", ARRAY_A);
    if (!empty($team)) {
        $f_img_path = VICTORIOUS__PLUGIN_DIR . 'assets/teams/' . $team['team_id'];
        $file_info = glob($f_img_path . '.*');
        if (!empty($file_info) && file_exists($file_info[0])) {
            $abs_path_img = $file_info[0];
            $aPath = explode('/', $abs_path_img);
            $file_name = $aPath[count($aPath) - 1];
            $url_background = VICTORIOUS__PLUGIN_URL . "assets/teams/$file_name";
            echo "<style>body.fv_background_class{background-image:url('" . esc_html($url_background) . "') !important;}</style>";
        }
    }
}

//add country field to register form and profile form
/*add_action('register_form', 'VIC_RegisterField');
add_action( 'user_register', 'VIC_SaveRegisterField');
add_action( 'personal_options_update', 'VIC_SaveUserProfileField');
add_action( 'edit_user_profile_update', 'VIC_SaveUserProfileField');
add_action( 'show_user_profile', 'VIC_UserProfileField');
add_action( 'edit_user_profile', 'VIC_UserProfileField');*/
function VIC_RegisterField(){
    wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');
    
    echo '<script>
            jQuery(window).load(function(){
                jQuery( "#date_of_birth" ).datepicker({maxDate: "0", dateFormat: "mm/dd/yy", changeMonth: true});
            })
          </script>';
    
    $victorious = new VIC_Victorious();
    $citys = $victorious->getCountryList();
    echo '<p>';
    echo '<label for="city">Country</label>';
    echo '<select class="input" name="city" style="padding:5px" >';
    foreach($citys as $k => $city){
        echo '<option value='. esc_html($k) .'>' . esc_html($city) . '</option>';
    }
    echo '</select>';
    echo '</p>';
	
	echo '<p>';
    echo '<label for="date_of_birth">Date of Birth</label>';
    echo '<input type="text" name="date_of_birth" id="date_of_birth" />';
    echo '</p>';
}
    
function VIC_UserProfileField( $user ) {
    wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');
    
    echo '<script>
            jQuery(window).load(function(){
                jQuery( "#date_of_birth" ).datepicker({maxDate: "0", dateFormat: "mm/dd/yy", changeMonth: true});
            })
          </script>';
    
    $victorious = new VIC_Victorious();
    $citys = $victorious->getCountryList();
    global $wpdb;
    $prefix = $wpdb->prefix;
    $table = $prefix . "user_extended";
    $user_id = $user->ID;
    $sql = "SELECT * FROM $table WHERE user_id = $user_id ";
    $user_extended = $wpdb->get_row($sql);
    $cur_city = !empty($user_extended->city) ? $user_extended->city : "";
	$date_of_birth = !empty($user_extended->date_of_birth) ? date("m/d/Y", strtotime($user_extended->date_of_birth)) : "";
    echo '<table class="form-table">';
    echo '<tbody>';
    echo '<tr class="user-email-wrap">';
    echo '<th><label for="city">Country</label></th>';
    echo '<td>';
    echo '<select name="city" id="city">';
    foreach($citys as $k => $city){
        $f = ($k == trim($cur_city))?"selected":"";
        echo '<option ' . esc_attr($f) . ' value='. esc_html($k) .'>' . esc_html($city) . '</option>';
    }
    echo '</select>';
	echo '</td>';
    echo '</tr>';
	
	echo '<tr class="user-email-wrap">';
    echo '<th><label for="date_of_birth">Date of Birth</label></th>';
    echo '<td>';
    echo '<input type="text" name="date_of_birth" id="date_of_birth" value="'.esc_html($date_of_birth).'" />';
	echo '</td>';
    echo '</tr>';
	
    echo '</tbody>';
    echo '</table>';

}

//update user info to server
add_action('wp_login', 'VIC_UpdateUserInfo', 10, 2 );
add_action('profile_update', 'VIC_UpdateUserInfo', 10, 2 );
function VIC_UpdateUserInfo($user_login, $user)
{
    try{
        $victorious = new VIC_Victorious();
        $victorious->postUserInfo($user->ID);
    } catch (Exception $ex) {

    }
}

function load_admin_dashboard_style() {
    wp_enqueue_style('vc_admin', VICTORIOUS__PLUGIN_URL_CSS.'ui/vc_admin.css');
    wp_enqueue_style('bootstrap', VICTORIOUS__PLUGIN_URL_CSS.'ui/bootstrap.min.css');
}
add_action( 'admin_enqueue_scripts', 'load_admin_dashboard_style' );

function load_theme_style() {
    wp_enqueue_style('vc_theme', VICTORIOUS__PLUGIN_URL_CSS.'ui/vc_theme.css');
}
add_action( 'wp_enqueue_scripts', 'load_theme_style' );

function victorious_plugin_body_class($classes) {
    $classes[] = 'victorious-body';
    return $classes;
}
add_filter('body_class', 'victorious_plugin_body_class');

?>