<?php
$Victorious_Pools = new Victorious_Pools();
class Victorious_Pools
{
    private static $pools;
    private static $victorious;
    private static $sports;
    private static $fighters;
    private static $playerposition;
    private static $url;
    private static $urladdnew;
    private static $urladd;
    public function __construct() 
    {
        self::$pools = new VIC_Pools();
        self::$victorious = new VIC_Victorious();
        self::$sports = new VIC_Sports();
        self::$fighters = new VIC_Fighters();
        self::$playerposition = new VIC_PlayerPosition();
        self::$url = admin_url().'admin.php?page=manage-pools';
        self::$urladdnew = admin_url().'admin.php?page=add-pools';
        self::$urladd = wp_get_referer();
    }
    public static function managePools()
    {
        //load css js
        wp_enqueue_style('admin.css', VICTORIOUS__PLUGIN_URL_CSS.'admin.css');
        wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS.'admin/admin.js');
        wp_enqueue_script('fight.js', VICTORIOUS__PLUGIN_URL_JS.'admin/fight.js');
        wp_enqueue_script('jquery-ui-dialog');
        
        //task action delete
        if(isset($_POST["task"]) && $task = sanitize_text_field($_POST["task"]))
        {
            switch($task)
            {
                case "delete":
                    self::delete();
                    break;
                case 'upload':
                    self::upload();
                    break;
            }
        }
        include VICTORIOUS__PLUGIN_DIR_VIEW."Elements/check-api-token.php";
        include VICTORIOUS__PLUGIN_DIR_VIEW.'pools/class.table-pools.php';
        $myListTable = new VIC_TablePools();
        $myListTable->prepare_items(isset($_GET['s']) ? sanitize_text_field($_GET['s']) : null);
        include VICTORIOUS__PLUGIN_DIR_VIEW.'pools/index.php';
    }
    
    public static function addPools()
    {
        //must check that the user has the required capability 
        if (!current_user_can('manage_options'))
        {
            wp_die( __('You do not have sufficient permissions to access this page.') , 'victorious');
        }
        
        //load css js
        wp_enqueue_style('admin.css', VICTORIOUS__PLUGIN_URL_CSS.'admin.css');
        wp_enqueue_style('ui.css', VICTORIOUS__PLUGIN_URL_CSS.'ui/ui.css');
        wp_enqueue_script('admin.js', VICTORIOUS__PLUGIN_URL_JS.'admin/admin.js');
        wp_enqueue_script('fight.js', VICTORIOUS__PLUGIN_URL_JS.'admin/fight.js');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('init_add.js', VICTORIOUS__PLUGIN_URL_JS.'admin/init_add.js');
        wp_enqueue_script('accounting.js', VICTORIOUS__PLUGIN_URL_JS.'accounting.js');

          $aMotocross = self::$victorious->getListMotocrossSports();
          $aListMotocross = $aMotocross['result'];
          $aSelectedMotocross= array();
        //edit data
        $bIsEdit = false;
		if (isset($_GET['id']) && $iEditId = sanitize_text_field($_GET['id']))
		{
            $bIsEdit = true;
            $aForms = self::$pools->getPools($iEditId);
            
            if($aListMotocross && in_array($aForms['organization'],$aListMotocross) ){
                $aSelectedMotocross = self::$pools->getMotoCross($iEditId);
                
            }else{
                $aFights = self::$pools->getFights($iEditId);
                $aFights = $aFights == null ? array(array()) : $aFights;
            }
		}
        else
        {
            $aForms = null;
            $aFights = array(null);
        }
        $aSelectedMotocross = !empty($aSelectedMotocross)?count($aSelectedMotocross):'2';
        //create valid
		//$oValidator = $this->createValid();
		
        //add or update
		self::modify($aListMotocross);
        $aSports = self::$victorious->getListSports(array('is_motocross'=>true));
        $aGameTypeSoccers = self::$victorious->getListGameTypeSoccer();
        $aGameTypeSoccers = json_encode($aGameTypeSoccers);
        $aPoolHours = self::$pools->getPoolHours();
        $aPoolMinutes = self::$pools->getPoolMinutes();
        $aPositions = self::$playerposition->getPlayerPosition();
        $aPositions = json_encode($aPositions);
        $aRounds = self::$fighters->getRounds();
        $aListMotocross = json_encode($aMotocross['result']);
        $motocross_id = $aMotocross['motocross_id'];
        $allow_motocross = $aMotocross['allow_motocross'];
        
        include VICTORIOUS__PLUGIN_DIR_VIEW.'pools/add.php';
    }

    private static function validData($aVals)
    {
        $sport = self::$sports->getSportById($aVals['organization']);

        if(empty($aVals['poolName']))
        {
            VIC_Redirect(self::$urladd, 'Provide a name');
        }
        else if($sport == null)
        {
            VIC_Redirect(self::$urladd, 'Please select organization');
        }
        else if(empty($aVals['startDate']))
        {
            VIC_Redirect(self::$urladd, 'Provide start date');
        }
        else if(empty($aVals['cutDate']))
        {
            VIC_Redirect(self::$urladd, 'Provide cut date');
        }else if($aVals['is_motocross']){
            if(!is_numeric($aVals['rounds'])){
                  VIC_Redirect(self::$urladd, 'Laps must be number');
            }
        }
        if(!$sport[0]['upload_photo'] && $sport[0]['only_playerdraft'] == 0)
        {
            //valid fight
            foreach($aVals['fighterID1'] as $item)
            {
                if(empty($item))
                {
                    VIC_Redirect(self::$urladd, 'Please select fighter 1');
                }
            }
            foreach($aVals['fighterID2'] as $item)
            {
                if(empty($item))
                {
                    VIC_Redirect(self::$urladd, 'Please select fighter 2');
                }
            }
            foreach($aVals['fight_name'] as $item)
            {
                if(empty($item))
                {
                    VIC_Redirect(self::$urladd, 'Provide fixture name');
                }
            }
        }
        return true;
    }
    
    private static function modify($motocross_orgs)
    {
       
        if (isset($_POST['val']) && $aVals = $_POST['val']) {

            $is_motocross = false;
       
            if($motocross_orgs && in_array($aVals['organization'],$motocross_orgs)){
                $is_motocross = true;
            }
            $aVals['is_motocross'] = $is_motocross;
            
           
            if (self::validData($aVals)) {
                if (self::$pools->isPoolExist($aVals['poolID'])) { //update

                    if (self::$pools->update($aVals)) {
                        VIC_Redirect(self::$urladd, 'Succesfully updated');
                    }
                } else { //add
                    if (self::$pools->add($aVals)) {
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
				if (self::$pools->delete($iId))
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
    private static function upload(){
        if(empty($_FILES)){
            return;
        }
        $aFiles = $_FILES;
        $info = sanitize_text_field($_POST['info']);
        foreach($aFiles as $key=>$file){
            if($file['error'] >0){
                continue;
            }
            $poolID = explode('_',$key);
            $poolID = $poolID[2];
            if(!isset($info[$poolID])){
                continue;
            }
            // check extension
            $extension = end(explode('.', $file['name']));
            if(strtolower($extension) != 'csv'){
                continue;
            }
            $detailInfo = $info[$poolID];
            $detailInfo = json_decode(stripslashes($detailInfo),true);
            $data['file_name'] = $file['name'];
            $data['poolID'] = $poolID;
            $data['org_id'] = $detailInfo['org_id'];
            $data['startDate'] = $detailInfo['startDate'];
            $tool_url = self::$victorious->createFolderCustomSport($data);

            $upload['filename'] = $file['name'];
            $upload['upload_file'] = '@'.$file['tmp_name'];
            $upload['dir_path'] = $tool_url[0];
            $upload['filesize'] = $file['size'];
            self::sendFile($upload);

            
        }
    }
    
    public static function sendFile($file){
    $url = get_option('victorious_api_url_admin').'/upload_file.php';
        $headers = array("Content-Type:multipart/form-data"); // cURL headers for file uploading
        $postfields = array("filedata" => $file['upload_file'], "filename" => $file['filename'],'dir_path'=>$file['dir_path'], 'filesize' => $file['filesize']);

        $args = array(
            'body'        => $postfields,
            'blocking'    => true,
            'headers'     => $headers,
            'cookies'     => array(),
        );
        wp_remote_post($url, $args);
    }
}
?>