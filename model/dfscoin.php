<?php

class VIC_Dfscoin{
	var $User;
    var $Password;
    var $ServiceCode;
    var $SubscriberID;
    var $Reference;
    var $Balance;
    var $TextMessage;
    var $Token;
    var $ImmediateReply;
    var $ImmediatePayment;
    
    public function __construct()
    {
        $this->base_url = "https://api.etherscan.io/api";
        $this->api_key = esc_attr(get_option('victorious_dfscoin_api_key'));
    }
    
    public function curl($url, $data = array()){
        $args = array(
            'body'        => $data,
            'blocking'    => true,
            'headers'     => array(),
            'cookies'     => array(),
        );
        $response = wp_remote_post($url, $args);
        $result = wp_remote_retrieve_body($response);
        return json_decode($result, true);
    }
    
    public function transactionDetail($transaction_id, $wallet_address)
    {
        $url = $this->base_url."?module=proxy&action=eth_getTransactionByHash&txhash={txid}&apikey={api_key}";
        $url = str_replace('{txid}', $transaction_id, $url);
        $url = str_replace('{api_key}', $this->api_key, $url);
        $transaction = $this->curl($url);
        if(empty($transaction['result'])){
            $result = array(
                'success' => 0,
                'message' => __('This transaction has not confirmed yet.', 'victorious')
            );
        }
        else{
            if(empty($transaction['result']['to']) || $transaction['result']['to'] != $wallet_address){
                $result = array(
                    'success' => 0,
                    'message' => __('Incorrect wallet address. You need to pay for this wallet address '.$wallet_address, 'victorious')
                );
            }
            else{
                $input = $transaction['result']['input'];
                $input = str_replace("0x", "", $input);
                $id = substr($input,0, 8);
                $address = substr($input,8, 72);
                $amount = substr($input,72);
                $amount = hexdec($amount) / 1000000000000000000;

                $result = array(
                    'success' => 1,
                    'amount' => $amount,
                    'txid' => $transaction_id
                );
            }
        }

        //check valid wallet address
        $valid_wallet_address = false;
        $amount = 0;
        /*if(!empty($transaction['help']['vout']))
        {
            foreach($transaction['help']['vout'] as $vout)
            {
                foreach($vout['scriptPubKey']['addresses'] as $address)
                {
                    if($wallet_address == $address)
                    {
                        $valid_wallet_address = true;
                        $amount = $vout['value'];
                    }
                }
            }
        }
        if(!empty($transaction['err']))
        {
            $result = array(
                'success' => 0,
                'message' => $transaction['err']['message'],
                'name' => $transaction['err']['name'],
                'code' => $transaction['err']['code'],
            );
        }
        else if(!empty($transaction['error']))
        {
            $result = array(
                'success' => 0,
                'message' => $transaction['message']
            );
        }
        else if(!empty($transaction['help']))
        {
            if(!$valid_wallet_address)
            {
                $result = array(
                    'success' => 0,
                    'message' => __('Incorrect wallet address. You need to pay for this wallet address '.$wallet_address, 'victorious')
                );
            }
            else if($transaction['help']['confirmations'] == 0)
            {
                $result = array(
                    'success' => 0,
                    'message' => __('This transaction is not confirmed yet.', 'victorious')
                );
            }
            else if($amount > 0)
            {
                $result = array(
                    'success' => 1,
                    'amount' => $amount,
                    'txid' => $transaction_id
                );
            }
            else
            {
                $result = array(
                    'success' => 0,
                    'message' => __('No amount data', 'victorious')
                );
            }
        }
        else
        {
            $result = array(
                'success' => 0,
                'message' => __('No data', 'victorious')
            );
        }*/
        return $result;
    }
}

?>