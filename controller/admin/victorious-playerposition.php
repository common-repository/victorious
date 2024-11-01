<?php
$Victorious_PlayerPosition = new Victorious_PlayerPosition();
class Victorious_PlayerPosition
{
    private static $victorious;
    private static $orgs;
    private static $playerposition;
    private static $url;
    private static $urladdnew;
    private static $urladd;
    public function __construct() 
    {
        self::$victorious = new VIC_Victorious();
        self::$orgs = new VIC_Organizations();
        self::$playerposition = new VIC_PlayerPosition();
        self::$url = admin_url().'admin.php?page=manage-playerposition';
        self::$urladdnew = admin_url().'admin.php?page=add-playerposition';
        self::$urladd = wp_get_referer();
    }
    
    public static function managePlayerPosition()
    {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') , 'victorious');
        }

        //load css js
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS.'admin/admin.js');
        
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
        include VICTORIOUS__PLUGIN_DIR_VIEW.'playerposition/class.table-playerposition.php';
        $myListTable = new VIC_TablePlayerPosition();
        $myListTable->prepare_items(isset($_GET['s']) ? sanitize_text_field($_GET['s']) : null);
        include VICTORIOUS__PLUGIN_DIR_VIEW.'playerposition/index.php';
    }
    
    public static function addPlayerPosition()
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
            $aForms = self::$playerposition->getplayerposition($iEditId);
		}
        else
        {
            $aForms = null;
            $aFights = array(null);
        }

        //add or update
		self::modify($bIsEdit);

        $aSports = self::$victorious->getListSports();
        include VICTORIOUS__PLUGIN_DIR_VIEW.'playerposition/add.php';
    }

    private static function validData($aVals)
    {
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
                if(self::$playerposition->isPlayerPositionExist($aVals['id'])) //update
                {
                    if (self::$playerposition->update($aVals))
                    {
                        VIC_Redirect(self::$urladd, 'Succesfully updated');
                    }
                }
                else //add
                {
                    if (self::$playerposition->add($aVals))
                    {
                        VIC_Redirect(self::$url, 'Succesfully added');
                    }
                }
                VIC_Redirect(self::$urladd, 'There is something wrong! Please_try_again.');
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
				if (self::$playerposition->delete($iId))
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