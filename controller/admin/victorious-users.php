<?php
$Victorious_Users = new Victorious_Users();
class Victorious_Users
{
    private static $user;
    private static $url;
    public function __construct() 
    {
        self::$url = admin_url() . 'admin.php?page=user-multi-entries';
        self::$user = new VIC_User();
    }
    
    public static function manageUserMultiEntries()
    {
        //load css js
        wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS.'admin/admin.js');
        
        
        //task action delete
        if(isset($_POST['task']))
        {
            self::saveData();
        }
        
        //load users
        $result = self::$user->loadUserMultiEntries();
        $users = $result['users'];
        
        include VICTORIOUS__PLUGIN_DIR_VIEW.'users/multi_entries.php';
    }
    
    private static function saveData()
    {
        $result = self::$user->saveUserMultiEntries($_POST);
        if(!$result['success'])
        {
            switch ($result['code'])
            {
                case 1:
                    VIC_Redirect(self::$url, __('Please add at least a user', 'victorious'));
                    break;
                case 2:
                    VIC_Redirect(self::$url, __('User is required', 'victorious'));
                    break;
                case 3:
                    VIC_Redirect(self::$url, __('Number of entries is required', 'victorious'));
                    break;
            }
        }
        VIC_Redirect(self::$url, 'Succesfully added');
    }
}
?>