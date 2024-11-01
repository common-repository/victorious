<?php

$Victorious_Teams = new Victorious_Teams();

class Victorious_Teams {

    private static $victorious;
    private static $teams;
    private static $url;
    private static $urladdnew;
    private static $urladd;
    private static $url_select;

    public function __construct() {
        self::$victorious = new VIC_Victorious();
        self::$teams = new VIC_Teams();
        self::$url_select = admin_url() . 'admin.php?page=manage-teams';
        self::$url = admin_url() . 'admin.php?page=manage-teams' . (!empty($_REQUEST['sport_id']) ? '&sport_id=' . sanitize_text_field($_REQUEST['sport_id']) : '');
        self::$urladdnew = admin_url() . 'admin.php?page=add-teams' . (!empty($_REQUEST['sport_id']) ? '&sport_id=' . sanitize_text_field($_REQUEST['sport_id']) : '');
        self::$urladd = wp_get_referer();
    }

    public static function manageTeams() {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'), 'victorious');
        }

        //load css js
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS . 'admin/admin.js');
        if (empty($_GET['sport_id']))
        {
            $aSports = self::$victorious->getListSports(array('is_motocross' => true));
            include VICTORIOUS__PLUGIN_DIR_VIEW . 'Elements/select_sport.php';
        }
        else
        {
            //task action delete
            if (isset($_POST["task"]) && $task = sanitize_text_field($_POST["task"])) {
                switch ($task) {
                    case "delete":
                        self::delete();
                        break;
                    case "save":
                        self::saveChanges();
                        break;
                }
            }
            include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";
            include VICTORIOUS__PLUGIN_DIR_VIEW . 'teams/class.table-teams.php';
            $myListTable = new VIC_TableTeams();
            $myListTable->prepare_items(isset($_GET['s']) ? sanitize_text_field($_GET['s']) : null);
            include VICTORIOUS__PLUGIN_DIR_VIEW . 'teams/index.php';
        }
    }

    public static function addTeams() {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'), 'victorious');
        }

        //load css js
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS . 'admin/admin.js');

        //edit data
        $bIsEdit = false;
        $sport_id = !empty($_GET['sport_id']) ? sanitize_text_field($_GET['sport_id']) : 0;
        if (isset($_GET['id']) && $iEditId = sanitize_text_field($_GET['id'])) {
            $bIsEdit = true;
            $aForms = self::$teams->getteams($iEditId);
        } else {
            $aForms = null;
            $aFights = array(null);
        }

        //add or update
        self::modify($bIsEdit);

        $aSports = self::$victorious->getListSports();
        include VICTORIOUS__PLUGIN_DIR_VIEW . 'teams/add.php';
    }

    private static function validData($aVals) {
        if (empty($aVals['name'])) {
            VIC_Redirect(self::$urladd, 'Provide a name');
        }
        return true;
    }

    private static function modify() {
        if (isset($_POST['val']) && $aVals = $_POST['val']) {
            if (self::validData($aVals)) {
                if (self::$teams->isTeamExist($aVals['teamID'])) { //update
                    if (self::$teams->update($aVals)) {
                        VIC_Redirect(self::$urladd, 'Succesfully updated');
                    }
                } else { //add
                    if (self::$teams->add($aVals)) {
                        VIC_Redirect(self::$url, 'Succesfully added');
                    }
                }
                VIC_Redirect(self::$urladd, 'Something went wrong! Please try again.');
            }
        }
    }

    private static function delete() {
        if (!empty($_POST['id']))
        {
            $aIds = array_map('sanitize_text_field', $_POST['id']);
            $iDeleted = 0;
            foreach ($aIds as $iId) {
                if (self::$teams->delete($iId)) {
                    $iDeleted++;
                }
            }

            if ($iDeleted > 0) {
                VIC_Redirect(self::$url, 'Succesfully deleted');
            }
        }
        VIC_Redirect(self::$url);
    }

    public function manageBgTeams() {
        wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');
        
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS.'admin/admin.js');

        if (isset($_POST['action_upload'])) {
            self::upload_team_background();
        }

        include VICTORIOUS__PLUGIN_DIR_VIEW . 'teams/background/class.table-teams.php';
        $myListTable = new VIC_TableTeams();
        $myListTable->prepare_items(isset($_GET['s']) ? sanitize_text_field($_GET['s']) : null);
        include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";
        include VICTORIOUS__PLUGIN_DIR_VIEW . 'teams/background/index.php';
    }
    
    private  static function upload_team_background(){
        $aData= $_FILES['upload_image'];
        $aTeamIDs = sanitize_text_field($_POST['teamID']);
        $aFiles = array();

        foreach($aData['error'] as $k=>$file){
            if($file == 0){
                $aFiles[] = array(
                   'team_id'=>$aTeamIDs[$k],
                   'tmp_path'=>$aData['tmp_name'][$k],
                   'name'=>$aData['name'][$k]
                );
            }
        }
        if($aFiles){
            $dir_image =  VICTORIOUS__PLUGIN_DIR . 'assets/teams/';
            foreach($aFiles as $file){
                // delete old image
                $old_image = $dir_image .$file['team_id'].'.*';
                $glob = glob($old_image);
                if(!empty($glob)){
                    foreach($glob as $item){
                        unlink($item);
                    }
                }        
                
                $file_name = explode('.',$file['name']);
                $extension = $file_name[count($file_name)-1];
                $new_name = $file['team_id'].'.'.$extension;
                $path = $dir_image.$new_name;

                move_uploaded_file($file['tmp_path'],$path);
            }
        }
    }

    private static function saveChanges()
    {
        $salaries = !empty($_POST['salary']) ? sanitize_text_field($_POST['salary']) : array();
        if($salaries != null)
        {
            foreach($salaries as $team_id => $salary)
            {
                $salary = str_replace(',', '', $salary);
                self::$teams->updateTeam(array(
                    'teamID' => $team_id,
                    'salary' => $salary
                ));
            }
        }
        VIC_Redirect(self::$url, 'Succesfully updated');
    }
}

?>