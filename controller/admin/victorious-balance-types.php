<?php
$Victorious_BalanceTypes = new Victorious_BalanceTypes();
class Victorious_BalanceTypes
{
    private static $balanceType;
    private static $url;
    private static $urladdnew;
    private static $urladd;
    public function __construct() 
    {
        self::$balanceType = new VIC_BalanceType();
        self::$url = admin_url().'admin.php?page=manage-balance-types';
        self::$urladdnew = admin_url().'admin.php?page=add-balance-type';
        self::$urladd = wp_get_referer();
    }
    
    public static function manageBalanceTypes()
    {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') , 'victorious');
        }

        //load css js
        wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS.'admin/admin.js');
        

        //task action delete
        if(isset($_POST["task"]) && $task = sanitize_text_field(_POST["task"]))
        {
            switch($task)
            {
                case "delete":
                    self::delete();
                    break;
            }
        }
        include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";
        include VICTORIOUS__PLUGIN_DIR_VIEW.'balancetypes/class.table-balance-types.php';
        $myListTable = new VIC_TableBalanceTypes();
        $myListTable->prepare_items(isset($_GET['s']) ? sanitize_text_field($_GET['s']) : null);
        include VICTORIOUS__PLUGIN_DIR_VIEW.'balancetypes/index.php';
    }
    
    public static function addBalanceType()
    {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') , 'victorious');
        }
        
        //load css js
        
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS.'admin/admin.js');
        wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');
        
        $bIsEdit = false;
        $id = !empty($_GET['id']) ? sanitize_text_field($_GET['id']) : 0;
        $bIsEdit = $id > 0 ? true : false;
        
        //save
		self::modify();
        
        //edit data
        $data = self::$balanceType->getBalanceTypeDetail($id);
        /*if($data != null && $data['is_core']){
            VIC_Redirect(self::$url, __('Unable to edit core item', 'victorious'));
        }*/

        include VICTORIOUS__PLUGIN_DIR_VIEW.'balancetypes/add.php';
    }

    private static function modify()
    {
        if(!isset($_POST['val'])){
            return;
        }
        $id = !empty($_POST['val']['id']) ? sanitize_text_field($_POST['val']['id']) : '';
        $name = !empty($_POST['val']['name']) ? sanitize_text_field($_POST['val']['name']) : '';
        $currency_code = !empty($_POST['val']['currency_code']) ? sanitize_text_field($_POST['val']['currency_code']) : '';
        $symbol = !empty($_POST['val']['symbol']) ? sanitize_text_field($_POST['val']['symbol']) : '';
        $image = '';

        //validate
        if(empty($name)){
            VIC_Redirect(self::$urladd, __('Name is required', 'victorious'));
        }
        if(empty($id) || $id != VICTORIOUS_DEFAULT_BALANCE_TYPE_ID){
            if(empty($currency_code)){
                VIC_Redirect(self::$urladd, __('Currency code is required', 'victorious'));
            }
            if(empty($symbol)){
                VIC_Redirect(self::$urladd, __('Currency symbol is required', 'victorious'));
            }
            if(empty($symbol)){
                VIC_Redirect(self::$urladd, __('Info is required', 'victorious'));
            }
            if(!empty($_FILES['image']['name'])) {
                $image = self::upload_image();
            }
        }
        $data = array(
            'id' => $id,
            'name' => $name,
            'currency_code' => $currency_code,
            'symbol' => $symbol,
            'image' => $image
        );

        //save
        if(!empty($id) && $id == VICTORIOUS_DEFAULT_BALANCE_TYPE_ID){
            self::$balanceType->saveCoreItem($data);
        }
        else{
            self::$balanceType->save($data);
        }
        VIC_Redirect(self::$url, 'Succesfully saved');
    }

    private  static function upload_image(){
        if(empty($_FILES['image']['name'])){
            return '';
        }
        $upload = wp_upload_bits(sanitize_text_field($_FILES['image']['name']), null, file_get_contents(sanitize_text_field($_FILES['image']['tmp_name'])));
        $upload_dir = wp_upload_dir();
        $image_path = str_replace($upload_dir['baseurl'], '', $upload['url']);
        return $image_path;
    }
    
    private static function delete()
	{
        if (!empty($_POST['id']))
        {
            $aIds = array_map('sanitize_text_field', $_POST['id']);
			$iDeleted = 0;
			foreach ($aIds as $iId)
			{
				if (self::$balanceType->delete($iId))
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