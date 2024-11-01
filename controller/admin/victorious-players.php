<?php

$Victorious_Players = new Victorious_Players();

class Victorious_Players
{

    private static $victorious;
    private static $teams;
    private static $playerposition;
    private static $players;
    private static $url_select;
    private static $url;
    private static $urladdnew;
    private static $urladd;

    public function __construct()
    {
        self::$victorious = new VIC_Victorious();
        self::$teams = new VIC_Teams();
        self::$playerposition = new VIC_PlayerPosition();
        self::$players = new VIC_Players();
        self::$url_select = admin_url() . 'admin.php?page=manage-players';
        self::$url = admin_url() . 'admin.php?page=manage-players' . (!empty($_REQUEST['sport_id']) ? '&sport_id=' . sanitize_text_field($_REQUEST['sport_id']) : '');
        self::$urladdnew = admin_url() . 'admin.php?page=add-players' . (!empty($_REQUEST['sport_id']) ? '&sport_id=' . sanitize_text_field($_REQUEST['sport_id']) : '');
        self::$urladd = wp_get_referer();
    }

    public static function managePlayers()
    {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die(__('You do not have sufficient permissions to access this page.'), 'victorious');
        }

        //load css js
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS . 'admin/admin.js');
        wp_enqueue_script('accounting.js', VICTORIOUS__PLUGIN_URL_JS . 'accounting.js');
        if (empty($_GET['sport_id']))
        {
            $aSports = self::$victorious->getListSports(array('is_motocross' => true));
            include VICTORIOUS__PLUGIN_DIR_VIEW . 'Elements/select_sport.php';
        }
        else
        {
            //task action delete
            if (isset($_POST["task"]) && $task = sanitize_text_field($_POST["task"]))
            {
                switch ($task)
                {
                    case "delete":
                        self::delete();
                        break;
                    case "save":
                        self::saveChanges();
                        break;
                }
            }
            include VICTORIOUS__PLUGIN_DIR_VIEW . "Elements/check-api-token.php";
            include VICTORIOUS__PLUGIN_DIR_VIEW . 'players/class.table-players.php';
            $myListTable = new VIC_TablePlayers();
            $myListTable->prepare_items(isset($_GET['s']) ? sanitize_text_field($_GET['s']) : null);
            include VICTORIOUS__PLUGIN_DIR_VIEW . 'players/index.php';
        }
    }

    public static function addPlayers()
    {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die(__('You do not have sufficient permissions to access this page.'), 'victorious');
        }

        //load css js
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS . 'admin/admin.js');
        wp_enqueue_script('players.js', VICTORIOUS__PLUGIN_URL_JS . 'admin/players.js');
        wp_enqueue_script('accounting.js', VICTORIOUS__PLUGIN_URL_JS . 'accounting.js');


        $bIsEdit = !empty($_GET['id']) ? true : false;
        $iEditId = !empty($_GET['id']) ? sanitize_text_field($_GET['id']) : 0;
        $sport_id = !empty($_GET['sport_id']) ? sanitize_text_field($_GET['sport_id']) : 0;

        //add or update
        self::modify();

        //data
        $data = self::$players->getAddPlayer($iEditId, $sport_id);
        $aForms = $data['player'];
        $aForms = self::$players->parsePlayersData($aForms, false);
        $aSports = $data['sports'];
        $aTeams = $data['teams'];
        $aPositions = $data['player_positions'];
        $indicators = self::$players->getIndicator();
        $aMotocross = self::$victorious->getListMotocrossSports();
        $aListMotocross = $aMotocross['result'];
        include VICTORIOUS__PLUGIN_DIR_VIEW . 'players/add.php';
    }

    private static function modify()
    {
        if (isset($_POST['val']) && $aVals = $_POST['val'])
        {
            $valid = self::$players->add($aVals);
            switch ($valid)
            {
                case 'v1':
                    VIC_Redirect(self::$urladd, __('Please select an organization ', 'victorious'));
                    break;
                case 'v2':
                    VIC_Redirect(self::$urladd, __('Please select a team', 'victorious'));
                    break;
                case 'v3':
                    VIC_Redirect(self::$urladd, __('Please select a position ', 'victorious'));
                    break;
                case 'v4':
                    VIC_Redirect(self::$urladd, __('Provide name', 'victorious'));
                    break;
                case 'v5':
                    VIC_Redirect(self::$urladd, __('Provide salary', 'victorious'));
                    break;
                case 'u1':
                    VIC_Redirect(self::$urladd, __('Succesfully updated', 'victorious'));
                    break;
                case 'u1':
                    VIC_Redirect(self::$urladd, __('Something went wrong! Please try again.', 'victorious'));
                    break;
            }
            VIC_Redirect(self::$url, 'Succesfully added');
        }
    }

    private static function delete()
    {
        if (!empty($_POST['id']))
        {
            $aIds = array_map('sanitize_text_field', $_POST['id']);
            $iDeleted = 0;
            foreach ($aIds as $iId)
            {
                if (self::$players->delete($iId))
                {
                    $iDeleted++;
                }
            }

            if ($iDeleted > 0)
            {
                VIC_Redirect(self::$url, 'Succesfully deleted');
            }
        }
        VIC_Redirect(self::$url);
    }

    private static function saveChanges()
    {
        $salaries = !empty($_POST['salary']) ? sanitize_text_field($_POST['salary']) : array();
        $sport_ids = !empty($_POST['salary_sport_id']) ? sanitize_text_field($_POST['salary_sport_id']) : array();
        if($salaries != null)
        {
            $data = array();
            foreach($salaries as $player_id => $salary)
            {
                $salary = str_replace(',', '', $salary);
                $data[] = array(
                    'id' => $player_id,
                    'salary' => $salary,
                    'sport_id' => isset($sport_ids[$player_id]) ? $sport_ids[$player_id] : ""
                );
            }
            self::$players->updatePlayerSalary($data);
        }
        VIC_Redirect(self::$url, 'Succesfully updated');
    }
}

?>