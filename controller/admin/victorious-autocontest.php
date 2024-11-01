<?php 
$Victorious_AutoContest = new Victorious_AutoContest();
class Victorious_AutoContest
{
	private static $urladdnewautocontest;
    private static $urlmanageautocontest;
    private static $victorious;
    private static $autocontest;
    private static $sports;

	function __construct()
	{
	    self::$autocontest = new VIC_AutoContest();
        self::$victorious = new VIC_Victorious();
        self::$sports = new VIC_Sports();
        self::$urladdnewautocontest = admin_url().'admin.php?page=add-auto-contest';
        self::$urlmanageautocontest = admin_url().'admin.php?page=manage-auto-contests';
	}

    public static function manageAutoContest(){
        //check allow auto contest
        $global_setting = self::$victorious->getGlobalSetting();
        if($global_setting['allow_auto_create_contest'] == 0)
        {
            VIC_Redirect(admin_url().'admin.php?page=manage-dashboard', 'You are not allowed to view this page');
        }
        
        //load css js
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS.'admin/admin.js');
        wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');

        include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";
        include VICTORIOUS__PLUGIN_DIR_VIEW.'autocontest/class.table-autocontests.php';

        $myListTable = new VIC_TableAutoContests();
        $myListTable->prepare_items(); 

        if(isset($_POST['id'])){
            $iDeleted = 0;
            foreach (sanitize_text_field($_POST['id']) as $id)
            {
                if (self::$autocontest->delete(sanitize_text_field($id)))
                {
                    $iDeleted++;
                }
            }

            if ($iDeleted > 0)
            {
                VIC_Redirect(self::$urlmanageautocontest, 'Succesfully deleted');
            }
        }

        include VICTORIOUS__PLUGIN_DIR_VIEW.'autocontest/index.php';
    }

    public static function addAutoContest(){
        //check allow auto contest
        $global_setting = self::$victorious->getGlobalSetting();
        if($global_setting['allow_auto_create_contest'] == 0)
        {
            VIC_Redirect(admin_url().'admin.php?page=manage-dashboard', 'You are not allowed to view this page');
        }
        
        //load css js
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS.'admin/admin.js');
        wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');

        $aSports = self::$sports->getSportsBySite();
        
        $aLeagueSizes = get_option('victorious_league_size');
        $aEntryFees = get_option('victorious_entry_fee');
        $game_type = array(
            "playerdraft" => "Player Draft",
            "pickem" => "Pick 'Em",
            VICTORIOUS_GAME_TYPE_SPORTBOOK => "Sportbook",
        );
        
        //edit 
        $auto_contest = array();
        $bIsEdit = false;
        $id = "";
        if(!empty($_GET['id']))
        {
            $id = sanitize_text_field($_GET['id']);
            $bIsEdit = true;
            $auto_contest = self::$autocontest->getAutoContest($id);
            if($auto_contest == null)
            {
                VIC_Redirect(self::$urlmanageautocontest, __('Item could not be found', 'victorious'));
            }
        }

        if(!empty($_POST)){
            if(self::validData($_POST['val'], $id)){
                $aResults = self::$autocontest->saveAutoContests($_POST['val']);
                if($aResults) VIC_Redirect(self::$urlmanageautocontest, __('Succesfully added', 'victorious'));
            }
            
        }

        include VICTORIOUS__PLUGIN_DIR_VIEW.'autocontest/add.php';
    }

    public static function validData($data, $id = 0){
        $url = self::$urladdnewautocontest;
        if($id > 0)
        {
            $url = $url."&action=edit&id=".$id;
        }
        if(empty($data['sports'])){
            VIC_Redirect($url, __('Pick Your Sports is required.', 'victorious'));
            exit;
        }
        if(empty($data['game_type'])){
            VIC_Redirect($url, __('Game Type is required.', 'victorious'));
            exit;
        }
        if(empty($data['name'])){
            VIC_Redirect($url, __('Name is required.', 'victorious'));
            exit;
        }
        if($data['prize_structure'] == "WINNER" && empty($data['winner_percent'])){
            VIC_Redirect($url, __('Winner percent is required.', 'victorious'));
            exit;
        }
        if($data['prize_structure'] == "TOP_3" && (empty($data['first_percent']) || empty($data['second_percent']) || empty($data['third_percent']))){
            VIC_Redirect($url, __('Top 3 percent is required.', 'victorious'));
            exit;
        }
        if($data['prize_structure'] == "MULTI_PAYOUT" && (empty($data['payouts_from']) || empty($data['payouts_to']) || empty($data['percentage']))){
            VIC_Redirect($url, __('Multi payout percent is required.', 'victorious'));
            exit;
        }
        foreach($data['payouts_from'] as $value)
        {
            if(empty($value))
            {
                VIC_Redirect($url, __('Multi payout percent is required.', 'victorious'));
                exit;
            }
        }
        foreach($data['payouts_to'] as $value)
        {
            if(empty($value))
            {
                VIC_Redirect($url, __('Multi payout percent is required.', 'victorious'));
                exit;
            }
        }
        foreach($data['percentage'] as $value)
        {
            if(empty($value))
            {
                VIC_Redirect($url, __('Multi payout percent is required.', 'victorious'));
                exit;
            }
        }
        return true;
    }
}

 ?>