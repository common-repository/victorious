<?php

/***

$url = someurl
                $client = new RestClient("GET", $url);
                $data = $client->send(false);
                return json_decode($data);
****/
class VIC_RestClient
{
	var $ch;
	var $url;
	var $pwd;
	var $http_code = 0;
	var $headers = false;
	var $cookies;
	function __construct($method, $url, $user=false, $pass=false)
	{
		$this->url = $url;
		/*$ch = curl_init($this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$method = strtoupper($method);
		switch($method)
		{
			case 'GET':
				curl_setopt($ch, CURLOPT_HTTPGET, 1);
				break;
			case 'POST':
				curl_setopt($ch, CURLOPT_POST, 1);
				break;
			default:
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
				break;
		}
		if ( $user )
		{
			if ( $pass )
				curl_setopt($ch, CURLOPT_USERPWD, ($this->pwd="$user:$pass"));
			else
				curl_setopt($ch, CURLOPT_USERPWD, ($this->pwd=$user));
		}*/

		$this->method = $method;
		//$this->ch = $ch;
	}

	/*function __destruct()
	{
		curl_close($this->ch);
	}*/

	function _header($data, $replace=true)
	{
		if ( !$replace && $this->headers[$data] )
			return false;
		$this->headers[$data] = true;

		return true;
	}
		
	function setCookie($cook)
	{
		curl_setopt($this->ch, CURLOPT_COOKIE, $cook);
	}	

	function cookieHandler($ch, $str)
	{
		if(!strncmp($str, "Set-Cookie:", 11))
		{
			header($str,false);
		}
		return strlen($str);
	}

	function send($post=false)
	{
        $response = array();
        if($this->method=="POST"){
            $args = array(
                'body'        => $post,
                'timeout'     => '100',
                'redirection' => '5',
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'     => array(),
                'cookies'     => array(),
            );
            $response = wp_remote_post( $this->url, $args);
        }
        else if($this->method=="GET"){
            $response = wp_remote_get( $this->url);
        }

        $data = wp_remote_retrieve_body($response);

        $this->checkResultExist($data);

        if ( !strlen($data) ){
            return false;
        }
        return $data;
	}
    
    private function checkResultExist(&$data)
    {
        if($this->isJSON($data))
        {
            $data = json_decode($data, true);
            /*if(isset($data['serverMessage']) && $data['serverMessage'] == 'YES')
            {
                $pool = new VIC_Pools();
                $pool->updateUserMoneyWon();
            }*/
            if(isset($data['serverMessage'])){
				unset($data['serverMessage']);
			}
            $data = json_encode($data);
        }
    }
    
    function isJSON($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
?>