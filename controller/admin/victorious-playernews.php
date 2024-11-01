<?php
$Victorious_PlayerNews = new Victorious_PlayerNews();
class Victorious_PlayerNews
{
    private static $victorious;
    private static $orgs;
    private static $players;
    private static $playernews;
    private static $url;
    private static $urladdnew;
    private static $urladd;
    public function __construct() 
    {
        self::$victorious = new VIC_Victorious();
        self::$orgs = new VIC_Organizations();
        self::$players = new VIC_Players();
        self::$playernews = new VIC_PlayerNews();
        self::$url = admin_url().'admin.php?page=manage-playernews';
        self::$urladdnew = admin_url().'admin.php?page=add-playernews';
        self::$urladd = wp_get_referer();
    }
    
    public static function managePlayerNews()
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
        include VICTORIOUS__PLUGIN_DIR_VIEW.'playernews/class.table-playernews.php';
        $myListTable = new VIC_TablePlayerNews();
        $myListTable->prepare_items(isset($_GET['s']) ? sanitize_text_field($_GET['s']) : null);
        include VICTORIOUS__PLUGIN_DIR_VIEW.'playernews/index.php';
    }
    
    public static function addPlayerNews()
    {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') , 'victorious');
        }
        
        //load css js
        
        wp_enqueue_script('playernews.js', VICTORIOUS__PLUGIN_URL_JS.'admin/playernews.js');
        wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');
        wp_enqueue_script('jquery-ui-datepicker');
        $bIsEdit = false;
        $id = !empty($_GET['id']) ? sanitize_text_field($_GET['id']) : 0;
        if($id > 0)
        {
            $bIsEdit = true;
        }
        
        //add or update
		self::modify($bIsEdit);
        
        //edit data
        $data = self::$playernews->getPlayerNewsForm($id);
        $aPlayers = $data['players'];
        $aForms = $data['player_news'];

        include VICTORIOUS__PLUGIN_DIR_VIEW.'playernews/add.php';
    }

    private static function modify()
    {
        if (isset($_POST['val']) && $aVals = $_POST['val'])
		{
            $valid = self::$playernews->add($aVals);
            switch ($valid)
            {
                case 'i2':
                    VIC_Redirect(self::$urladd, __('Please select player', 'victorious'));
                    break;
                case 'i3':
                    VIC_Redirect(self::$urladd, __('Provide date', 'victorious'));
                    break;
                case 'i4':
                    VIC_Redirect(self::$urladd, __('Provide title', 'victorious'));
                    break;
                case 'i5':
                    VIC_Redirect(self::$urladd, __('Provide content', 'victorious'));
                    break;
                case 'u1':
                    VIC_Redirect(self::$urladd, 'Succesfully updated');
                    break;
                case 'u0':
                    VIC_Redirect(self::$urladd, 'There is something wrong! Please_try_again.');
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
				if (self::$playernews->delete($iId))
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