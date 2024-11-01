<?php
$Victorious_ScoringCategory = new Victorious_ScoringCategory();
class Victorious_ScoringCategory
{
    private static $victorious;
    private static $scoringcategory;
    private static $url;
    private static $urladdnew;
    private static $urladd;
    private static $url_select;
    public function __construct() 
    {
        self::$victorious = new VIC_Victorious();
        self::$scoringcategory = new VIC_ScoringCategory();
        self::$url = admin_url().'admin.php?page=manage-scoringcategory' . (!empty($_REQUEST['sport_id']) ? '&sport_id=' . sanitize_text_field($_REQUEST['sport_id']) : '');
        self::$urladdnew = admin_url().'admin.php?page=add-scoringcategory' . (!empty($_REQUEST['sport_id']) ? '&sport_id=' . sanitize_text_field($_REQUEST['sport_id']) : '');
        self::$url_select = admin_url() . 'admin.php?page=manage-scoringcategory';
        self::$urladd = wp_get_referer();
    }
    
    public static function manageScoringCategory()
    {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') , 'victorious');
        }

        //load css js
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS.'admin/admin.js');
        if (!isset($_GET['sport_id']))
        {
            $aSports = self::$victorious->getListSports(array('is_motocross' => true));
            $all_sport = true;
            include VICTORIOUS__PLUGIN_DIR_VIEW . 'Elements/select_sport.php';
        }
        else
        {
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
            include VICTORIOUS__PLUGIN_DIR_VIEW.'scoringcategory/class.table-scoringcategory.php';
            $myListTable = new VIC_TableScoringCategory();
            $myListTable->prepare_items(isset($_GET['s']) ? sanitize_text_field($_GET['s']) : null);
            include VICTORIOUS__PLUGIN_DIR_VIEW.'scoringcategory/index.php';
        }
    }
    
    public static function addScoringCategory()
    {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') , 'victorious');
        }
        
        //load css js
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS.'admin/admin.js');
        wp_enqueue_script('scoringcat.js', VICTORIOUS__PLUGIN_URL_JS.'admin/scoringcat.js');
        
        //edit data
        $bIsEdit = false;
		if (isset($_GET['id']) && $iEditId = sanitize_text_field($_GET['id']))
		{
            $bIsEdit = true;
            $aForms = self::$scoringcategory->getScoringCategory($iEditId);
		}
        else
        {
            $aForms = null;
            $aFights = array(null);
        }

        //add or update
		self::modify($bIsEdit);

        $aSports = self::$victorious->getListSports();
        $aScoringTypes = self::$scoringcategory->getScoringType();
        include VICTORIOUS__PLUGIN_DIR_VIEW.'scoringcategory/add.php';
    }

    private static function validData($aVals)
    {
        $aScoringCat = self::$scoringcategory->getScoringCategory($aVals['id']);
        if($aScoringCat[0]['siteID'] > 0 || $aVals['id'] == '')
        {
            if(empty($aVals['name']))
            {
                VIC_Redirect(self::$urladd, 'Provide a name');
            }
        }
        return true;
    }
    
    private static function modify()
    {
        if (isset($_POST['val']) && $aVals = $_POST['val'])
		{
			if (self::validData($aVals))
			{
                if(self::$scoringcategory->isScoringCategoryExist($aVals['id'])) //update
                {
                    if (self::$scoringcategory->update($aVals))
                    {
                        VIC_Redirect(self::$urladd, 'Succesfully updated');
                    }
                }
                else //add
                {
                    if (self::$scoringcategory->add($aVals))
                    {
                        VIC_Redirect(self::$url, 'Succesfully added');
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
				if (self::$scoringcategory->delete($iId))
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