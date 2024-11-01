<?php
$Victorious_Fighters = new Victorious_Fighters();
class Victorious_Fighters
{
    private static $victorious;
    private static $fighters;
    private static $url;
    private static $urladdnew;
    private static $urladd;
    public function __construct() 
    {
        self::$victorious = new VIC_Victorious();
        self::$fighters = new VIC_Fighters();
        self::$url = admin_url().'admin.php?page=manage-fighters';
        self::$urladdnew = admin_url().'admin.php?page=add-fighters';
        self::$urladd = wp_get_referer();
    }
    
    public static function manageFighters()
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
        include VICTORIOUS__PLUGIN_DIR_VIEW.'fighters/class.table-fighters.php';
        $myListTable = new VIC_TableFighters();
        $myListTable->prepare_items(isset($_GET['s']) ? sanitize_text_field($_GET['s']) : null);
        include VICTORIOUS__PLUGIN_DIR_VIEW.'fighters/index.php';
    }
    
    public static function addFighters()
    {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') , 'victorious');
        }
        
        //load css js
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS.'admin/admin.js');
        wp_enqueue_script('jquery-ui-datepicker');
        //load css js
        wp_enqueue_style('admin.css', VICTORIOUS__PLUGIN_URL_CSS.'admin.css');
        wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS.'admin/admin.js');
        wp_enqueue_script('fight.js', VICTORIOUS__PLUGIN_URL_JS.'admin/fight.js');
        
        wp_enqueue_script('init_add.js', VICTORIOUS__PLUGIN_URL_JS.'admin/init_add.js');
        
        //edit data
        $bIsEdit = false;
		if (isset($_GET['id']) && $iEditId = sanitize_text_field($_GET['id']))
		{
            $bIsEdit = true;
            $aForms = self::$fighters->getfighters($iEditId);
		}
        else
        {
            $aForms = null;
            $aFights = array(null);
        }

        //add or update
		self::modify($bIsEdit);

        $aSports = self::$victorious->getListSports();
        include VICTORIOUS__PLUGIN_DIR_VIEW.'fighters/add.php';
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
                if(self::$fighters->isFighterExist($aVals['fighterID'])) //update
                {
                    if (self::$fighters->update($aVals))
                    {
                        VIC_Redirect(self::$urladd, 'Succesfully updated');
                    }
                }
                else //add
                {
                    if (self::$fighters->add($aVals))
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
				if (self::$fighters->delete($iId))
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