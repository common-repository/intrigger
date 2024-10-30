<?php
/**
 * Mailchimp Integration
 * @package ITRR_Mailchimp
 */
if(!class_exists('ITRR_Mailchimp')) {

    class ITRR_Mailchimp
    {
        function __construct(){

		}
        /**
         * get account info on mailchimp
         */
        public static function mcp_get_data($key)
        {
            $mcp_info = self::mcp_get_account($key);
            $mcp_lists = self::mcp_get_lists($key);

            $dc = "us1";
            if (strstr($key, "-")){
                list($key, $dc) = explode("-", $key, 2);
                if (!$dc) {
                    $dc = "us1";
                }
            }
            $mcp_collection = array(
                'info' => $mcp_info,
                'lists' => $mcp_lists,
                'us' => $dc
            );
            return $mcp_collection;

        }
        static function mcp_get_account($key)
        {
            return self::do_request("users/profile", array(), $key);
        }

        static function mcp_get_lists($key)
        {
            return self::do_request("lists/list", array(), $key);
        }

        /** Add subscriber */
        public static function mcp_add_subscriber($email, $list_id){
            $params = array(
                'id' => $list_id,
                'email' => array(
                    'email'=>$email,
                ),
            );
            $key = get_option('itrr_mcp_access_key') != false ? get_option('itrr_mcp_access_key') : '';
            return self::do_request("lists/subscribe",$params, $key);
        }

        /**
         * ajax module for login to Mailchimp
         */
        public static function ajax_login_mailchimp()
        {
            check_ajax_referer('itrr_setting_ajax_nonce', 'security');
            $key = isset($_POST['key']) ? sanitize_text_field($_POST['key']) : '';
            if ($key != '') {
                $response = self::do_request("lists/list", array(), $key);
                if (is_array($response)) {
                    if (isset($response['status']) && $response['status'] == 'error') {
                        //$response['code'] = 'error'; ex, 104
                    }else{
                        $response['code'] = 'success';
                        update_option('itrr_mcp_access_key', $key);
                        update_option('itrr_activated_integration', 'mcp');
                    }
                }
            }
            echo wp_json_encode($response);
            die();
        }

        /**
         * Do CURL request with authorization
         */
        static function do_request($url='lists/list', $params=array('id'=>'8bd139733c'), $key='87bbc199e0a1aa47d6dc387b7f41bad8-us3')
        {
            if($key == '') return false;
            $dc = "us1";
            if (strstr($key, "-")){
                list($key, $dc) = explode("-", $key, 2);
                if (!$dc) {
                    $dc = "us1";
                }
            }
            $called_url = "https://".$dc.".api.mailchimp.com/2.0" . "/";
            $ch = curl_init();

            $params['apikey'] = $key;
            $params = json_encode($params);
            curl_setopt($ch, CURLOPT_USERAGENT, 'MailChimp-PHP/2.0.6');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $called_url . $url . '.json');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

            $data = curl_exec($ch);
            if (curl_errno($ch)) {
                $data = '{"status":"error", "code":"Curl error"}';
            }
            curl_close($ch);
            return json_decode($data, true);
        }


    }
}