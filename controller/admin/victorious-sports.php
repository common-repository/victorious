<?php
$Victorious_Sports = new Victorious_Sports();
class Victorious_Sports
{
    private static $sports;
    private static $payment;
    private static $leagues;
    private static $url;
    private static $urladd;
    private static $urladdnew;
    private static $victorious;

    public function __construct() 
    {
        self::$sports = new VIC_Sports();
        self::$payment = new VIC_Payment();
        self::$leagues = new VIC_Leagues();
        self::$victorious = new VIC_Victorious();
        self::$url = admin_url().'admin.php?page=manage-sports';
        self::$urladd = wp_get_referer();
        self::$urladdnew = admin_url().'admin.php?page=add-sports';
    }

    public static function manageDashboard(){
        //load css js
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS.'admin/admin.js');
        wp_enqueue_script('loader.js', VICTORIOUS__PLUGIN_URL_JS.'chart_loader.js');
        wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('jquery-ui-dialog');

        $list_sport     = self::$sports->getSportsBySite();
        $list_premium_feature = self::$victorious->getPremiumFeatures();
        $site_setting   = self::$sports->getSportBySiteSetting();

        $stats_type = isset($_GET['stats_type']) ? sanitize_text_field($_GET['stats_type']) : 'total_contest';
        $stats_year = isset($_GET['stats_year']) ? sanitize_text_field($_GET['stats_year']) : date('Y');
        switch ($stats_type) {
            case 'total_money':
                $cond = 'YEAR(date) = '.$stats_year;
                $list_all_fundhistory = self::$payment->getAllFundhistory($cond);
                $str_chart = "['Month', 'Total Money']";

                if(count($list_all_fundhistory) > 0){
                    foreach ($list_all_fundhistory as $key => $value) {
                        $str_chart .= ",['".$key."', ".$value."]";
                    }
                }else{
                    $str_chart .= ",['January', 0],['February', 0],['March', 0],['April', 0],['May', 0],['June', 0],['July', 0],['August', 0],['September', 0],['October', 0],['November', 0],['December', 0]"; 
                }
                break;

            case 'total_users_played':
                $total_users_played  = self::$sports->getUserPlayed($stats_year);
                $str_chart = "['Month', 'Total Users Played']";

                if(count($total_users_played) > 0){
                    foreach ($total_users_played as $key => $value) {
                        $str_chart .= ",['".$key."', ".$value."]";
                    }
                }else{
                    $str_chart .= ",['January', 0],['February', 0],['March', 0],['April', 0],['May', 0],['June', 0],['July', 0],['August', 0],['September', 0],['October', 0],['November', 0],['December', 0]"; 
                }
                break;
            
            default:
                $total_contest  = self::$leagues->getLeagueByYear($stats_year);
                $str_chart = "['Month', 'Total Contests']";
                if($total_contest != null && count($total_contest) > 0){
                    foreach ($total_contest as $key => $value) {
                        $str_chart .= ",['".$key."', ".$value."]";
                    }
                }else{
                    $str_chart .= ",['January', 0],['February', 0],['March', 0],['April', 0],['May', 0],['June', 0],['July', 0],['August', 0],['September', 0],['October', 0],['November', 0],['December', 0]"; 
                }                
                break;
        }        

        include VICTORIOUS__PLUGIN_DIR_VIEW.'sports/dashboard.php';
    }
    
    public static function manageSports()
    {
        //load css js
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS.'admin/admin.js');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');
        
        
        //task action delete
        if(isset($_POST["task"]) && $task = sanitize_text_field($_POST["task"]))
        {
            switch($task)
            {
                case "delete":
                    self::delete();
                    break;
            }
        }
        include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";
        include VICTORIOUS__PLUGIN_DIR_VIEW.'sports/class.table-sports.php';
        $myListTable = new VIC_TableSports();
        $aSports = $myListTable->prepare_items();
        include VICTORIOUS__PLUGIN_DIR_VIEW.'sports/index.php';
    }
    
    public static function addSports()
    {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') , 'victorious');
        }
        
        //load css js
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS.'admin/admin.js');
        
        //edit data
        $bIsEdit = false;
		if (isset($_GET['id']) && $iEditId = sanitize_text_field($_GET['id']))
		{
            $bIsEdit = true;
            $aForms = self::$sports->getSportById($iEditId);
            $aForms = self::$sports->parseSportsData($aForms);
            $aForms = $aForms[0];

            if (!empty($aForms['parent_id'])) {
                $aForms['organisation'] = 1;
            }
		} else if (isset($_GET['parent_id']) && $iParentId = sanitize_text_field($_GET['parent_id']))
		{
            $aForms = array(
                'parent_id' => $iParentId,
                'organisation' => 1
            );
        } else
        {
            $aForms = null;
        }

        //add or update
		self::modify($bIsEdit);

        $aSports = self::$sports->getSports();
        include VICTORIOUS__PLUGIN_DIR_VIEW.'sports/add.php';
    }

    private static function validData($aVals)
    {
        if (!empty($aVals['organisation']) && empty($aVals['parent_id'])) {
            VIC_Redirect(self::$urladd, 'Select a Sport');
        }

        if(empty($aVals['name']))
        {
            VIC_Redirect(self::$urladd, 'Provide a name');
        }
        return true;
    }
    
    private static function modify()
    {
        if (isset($_POST['val']) && $aVals = $_POST['val'])
		{
			if (self::validData($aVals))
			{
                if(self::$sports->isSportExist($aVals['id'])) //update
                {
                    if (self::$sports->update($aVals))
                    {
                        VIC_Redirect(self::$urladd, 'Succesfully updated');
                    }
                }
                else //add
                {
                    if ($sport_id = self::$sports->add($aVals))
                    {
                        if (empty($aVals['organisation'])) {
                            VIC_Redirect(self::$urladdnew . "&parent_id={$sport_id}");
                        } else {
                            VIC_Redirect(self::$url, 'Succesfully added');
                        }
                    }
                }
                VIC_Redirect(self::$urladd, 'Something went wrong! Please try again.');
			}
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
				if (self::$sports->delete($iId))
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
}
?>