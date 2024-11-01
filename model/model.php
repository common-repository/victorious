<?php
require_once("admin/RestClient.php");
class VIC_Model
{
    public $api_debug = false;
    public $method = "POST";
    public function __construct() 
    {
        $this->selectField = null;
    }
    
    private function getRestClient($method, $url)
    {
        $ret = false;
        $ret = new VIC_RestClient($method, $url);
        return $ret;
    }
    
    public function selectField($params = array())
    {
        $this->selectField = $params;
    }
    
    public function sendRequest($task, $params = array(), $is_admin = true, $json = true)
    {
        $user_id = 0;
        $allowed_tasks = array("getListSports", "getLeagueLobby", "getPoolInfo", "getEntries", "leagueDetail", "userInfo", "loadStatsSport", "loadStatsSportInfo", "loadStatsData", "getSportFirstGame", "getSportbookContest");
        if(empty(VIC_GetUserId()) && !in_array($task, $allowed_tasks))
        {
            return "";
        }
        $user_id = !empty(VIC_GetUserId()) ? VIC_GetUserId() : '';
        $api_token = get_option('victorious_api_token');
        //$params['v2'] = true;
        //parse url
        if($is_admin)
        {
            $url = get_option('victorious_api_url_admin')."/$task/".$api_token;
        }
        else 
        {
            $url = get_option('victorious_api_url')."/$task/".$api_token.'/'.$user_id;
        }
        if($this->method == "GET" && !empty($params) && is_array($params))
        {
            $url .= "?".http_build_query($params);
        }
        
        if(isset($this->selectField) && $this->selectField != null)
        {
            $params['field'] = $this->selectField;
        }
        //send request
        $client = $this->getRestClient($this->method, $url);
        $data = $client->send($params);
        $this->selectField = null;
        if($json && !$this->api_debug)
        {
            $data = json_decode($data, true);
        }
        if (isset($data['code']) && isset($data['message'])) {
            $data = null;
        }
        return $data;
    }
    
    protected function send($task, $params = array(), $is_admin = false)
    {
        $result = $this->sendRequest($task, $params, $is_admin);
        if($this->api_debug)
        {
            echo esc_html($result);
            exit;
        }
        return $result;
    }
    
    public static function parseImageSuffix($image = null, $suf = null)
    {
        $suffix = '_80';
        if($suf != null)
        {
            $suffix = $suf;
        }
        if($image != null)
        {
            $img = explode('.', $image);
            $img[count($img) - 2] = $img[count($img) - 2].$suffix.".".$img[count($img) - 1];
			unset($img[count($img) - 1]);
			array_values($img);
            $img = implode('.', $img);
            return $img;
        }
        return null;
    }
    
    public static function replaceSuffix($image = null, $suf = 'suf')
    {
        $suffix = '_80';
        if($suf != 'suf')
        {
            $suffix = $suf;
        }
        if($image != null)
        {
            $image = sprintf($image, $suffix);
        }
        return $image;
    }
    
    public function deleteImage($sFileName = null)
    {
        if (!empty($sFileName))
        {
            $originalImagePath = VICTORIOUS_IMAGE_DIR.$this->replaceSuffix($sFileName, '');
            $thumbImagePath = VICTORIOUS_IMAGE_DIR.$this->replaceSuffix($sFileName);

            if(file_exists($originalImagePath))
            {
                unlink($originalImagePath);
            }
            if(file_exists($thumbImagePath))
            {
                unlink($thumbImagePath);
            }
        }
        return true;
    }
    
    public function uploadImage($resize = true)
    {
        if (!function_exists('wp_handle_upload')) 
        {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }
        $uploadedfile = $_FILES['image'];
        $upload_overrides = array( 'test_form' => false );
        $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
        if($movefile) 
        {
            if($resize)
            {
                $image = str_replace(VICTORIOUS_IMAGE_URL, '', $this->parseImageSuffix($movefile['url'], '%s'));

                //resize 
                $img = wp_get_image_editor($movefile['file']);
                if (!is_wp_error($img)) 
                {
                    $img->resize(80,80, true );
                    $img->save($this->parseImageSuffix($movefile['file'], '_80'));
                }
            }
            else 
            {
                $image = str_replace(VICTORIOUS_IMAGE_URL, '', $movefile['url']);;
            }
        }
        return $image;
    }

    public function checkAPITokenAdmin() {
        $api_token = get_option('victorious_api_token');
        $params['v2'] = true;

        $url = get_option('victorious_api_url_admin')."/getSports/".$api_token;

        if(isset($this->selectField) && $this->selectField != null)
        {
            $params['field'] = $this->selectField;
        }
        //send request
        $client = $this->getRestClient('POST', $url);
        $data = $client->send($params);
        $this->selectField = null;
        //$data = json_decode($data, true);
        $result = true;
        if ($data == 'code_505') {
            $result = $data;
            $result = str_replace('code_', '', $result);
        }
        return $result;
    }
    
    public function formatOffsetTimeZone($tz){
        if($tz != ""){
            if(preg_match('/\\d/', $tz)  && preg_match('/UTC/', $tz)){
               $tz = str_replace('UTC', "", $tz);
                if(strpos($tz, '.')){
                    $new_value = explode(".", $tz);
                   $minutes = $new_value[1] < 10 ? $new_value[1] * 10 : $new_value[1];
                   $minutes = round(($minutes/100)*60);
                   $new_tz = $new_value[0].":".$minutes;
                  $tz = $new_tz;
                }
            }
        }
		return $tz;
    }
    
    public function parseUserData($aDatas = null, $user_id = null)
    {
        if ($aDatas != null)
        {
            $single = false;
            if(isset($aDatas['id']) || isset($aDatas['userID']))
            {
                $single = true;
                $aDatas = array($aDatas);
            }
            foreach ($aDatas as $k => $aData)
            {
                $user = $this->get_user_by("id", $aData['userID']);
                if ($user != null)
                {
                    $aDatas[$k]['username'] = $user->user_login;
                    $aDatas[$k]['user_nicename'] = $user->user_nicename;
                    $aDatas[$k]['avatar'] = $this->get_avatar_url($this->get_avatar($aData['userID']));
                }
            }
            if($single)
            {
                $aDatas = $aDatas[0];
            }
        }
        if ($user_id > 0)
        {
            $user = $this->get_user_by("id", $user_id);
            $data = array();
            if ($user != null)
            {
                $data['username'] = $user->user_login;
                $data['user_nicename'] = $user->user_nicename;
                $data['avatar'] = $this->get_avatar_url($this->get_avatar($user_id));
            }
            return $data;
        }
        return $aDatas;
    }

    public function get_avatar_url($get_avatar)
    {
        preg_match("/src=['\"](.*?)['\"]/i", $get_avatar, $matches);
        return $matches[1];
    }
    
    public function get_user_by($field, $value)
    {
        $userdata = WP_User::get_data_by($field, $value);
        if (!$userdata)
        {
            return false;
        }
        $user = new WP_User;
        $user->init($userdata);
        return $user;
    }
    
    public function get_avatar($user_id, $size = 96)
    {
        if (!get_option('show_avatars'))
        {
            return false;
        }
        global $wpdb;
        $table_user = $wpdb->prefix . 'users';
        $sCond = " WHERE ID = " . $user_id;
        $sql = "Select user_email from $table_user $sCond";
        $data = $wpdb->get_row($sql, ARRAY_A);
        $email = $data['user_email'];
        $email_hash = '';
        $is_found_avatar = false;
        // email hash
        if (strpos($email, '@md5.gravatar.com'))
        {
            // md5 hash
            list( $email_hash ) = explode('@', $id_or_email);
        }

        if ($email_hash)
        {
            $is_found_avatar = true;
            $gravatar_server = hexdec($email_hash[0]) % 3;
        }
        else
        {
            $gravatar_server = rand(0, 2);
        }
        if (!$email_hash)
        {
            if ($email)
            {
                $email_hash = md5(strtolower(trim($email)));
            }
        }
        if (is_ssl())
        {
            $url = 'https://secure.gravatar.com/avatar/' . $email_hash;
        }
        else
        {
            $url = sprintf('http://%d.gravatar.com/avatar/%s', $gravatar_server, $email_hash);
        }
        $a_default = get_option('avatar_default');
        switch ($a_default)
        {
            case 'mm' :
            case 'mystery' :
            case 'mysteryman' :
                $default = 'mm';
                break;
            case 'gravatar_default' :
                $default = false;
                break;
        }
        $a_rating = strtolower(get_option('avatar_rating'));
        $url .= "?s=$size&#038;d=$a_default&#038;r=$a_rating";
        $avatar = sprintf(
                "<img alt='%s' src='%s' class='%s' height='%d' width='%d'/>", esc_attr(''), esc_url($url), esc_attr(""), esc_attr(( 'avatar avatar-' . $size . ' photo')), (int) $size, (int) $size
        );
        return $avatar;
    }

    public function groupArrayByKey($data, $keys = array(), $group_array = false, $implode = false)
    {
        if($data == null || $keys == null)
        {
            return $data;
        }
        $result = array();
        foreach($data as $item)
        {
            if($implode)
            {
                $result[] = $item[$keys[0]];
            }
            else
            {
                $item_key = array();
                foreach($keys as $key)
                {
                    if(!isset($item[$key]))
                    {
                        break;
                    }
                    $item_key[] = $item[$key];
                }
                if($item_key == null)
                {
                    break;
                }
                $item_key = implode("_", $item_key);
                if($group_array)
                {
                    $result[$item_key][] = $item;
                }
                else
                {
                    $result[$item_key] = $item;
                }
            }
        }
        return $result != null ? $result : $data;
    }
}
?>