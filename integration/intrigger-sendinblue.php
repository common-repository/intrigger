<?php
/**
 * Sendinblue Integration
 * @package ITRR_Sendinblue
 */
if(!class_exists('ITRR_Sendinblue')) {

    class ITRR_Sendinblue
    {

        // API key of admin
        const sib_admin_key = 'JQIdYyc2x7hgfn9t';

        /**
         * get account info on sendinblue
         */
        public static function sib_get_data($key)
        {

            $sib_info = self::sib_get_account($key);
            $sib_lists = self::sib_get_lists($key);
            $sib_contacts = self::sib_get_contacts($sib_lists, $key);
            $sib_collection = array(
                'info' => $sib_info,
                'lists' => $sib_lists,
                'contacts' => $sib_contacts,
            );
            return $sib_collection;

        }

        static function sib_get_account($key)
        {
            return self::do_request("account", "GET", "", $key);
        }

        static function sib_get_lists($key)
        {
            return self::do_request("list", "GET", array(), $key);
        }

        static function sib_get_contacts($list_response, $key)
        {
            if ($list_response['code'] != 'success') {
                $total_subscribers = 0;
            } else {
                $list_datas = $list_response['data'];
                $list_ids = array();
                if (isset($list_datas) && is_array($list_datas)) {
                    foreach ($list_datas as $list_data) {
                        $list_ids[] = $list_data['id'];
                    }
                }
                $data = array(
                    "listids" => $list_ids,
                    "page" => 1,
                    "page_limit" => 500
                );
                $users_response = self::do_request("list/display", "POST", json_encode($data), $key);
                $total_subscribers = intval($users_response['data']['total_list_records']);
            }
            return $total_subscribers;

        }

        /**
         * add user to list of sendinblue
         */
        public static function sib_add_user($email, $list_id)
        {

            $response = self::validation_email($email, $list_id);

            if ($response['code'] != 'already_exist') {

                $listid = $response['listid'];
                if (!in_array($list_id, $listid)) {
                    array_push($listid, $list_id);
                }
                $listid_unlink = null;

                $data = array(
                    "email" => $email,
                    "attributes" => array(),
                    "blacklisted" => 0,
                    "listid" => $listid,
                    "listid_unlink" => null,
                    "blacklisted_sms" => 0
                );
                $key = get_option('itrr_sib_access_key') != false ? get_option('itrr_sib_access_key') : '';
                $response = self::do_request("user/createdituser", "POST", json_encode($data), $key);
            }

            return $response;
        }

        /**
         * Validation email on sendinblue
         */
        static function validation_email($email, $list_id)
        {
            $key = get_option('itrr_sib_access_key') != false ? get_option('itrr_sib_access_key') : '';

            $response = self::do_request("user/" . $email, "GET", "", $key);

            if ($response['code'] == 'failure') {
                $ret = array(
                    'code' => 'success',
                    'listid' => array()
                );
                return $ret;
            }

            $listid = $response['data']['listid'];
            if (!isset($listid) || !is_array($listid)) {
                $listid = array();
            }
            if ($response['data']['blacklisted'] == 1) {
                $ret = array(
                    'code' => 'update',
                    'listid' => $listid
                );
            } else {
                if (!in_array($list_id, $listid)) {
                    $ret = array(
                        'code' => 'success',
                        'listid' => $listid
                    );
                } else {
                    $ret = array(
                        'code' => 'already_exist',
                        'listid' => $listid
                    );
                }
            }
            return $ret;
        }

        /**
         * ajax module for login to sendinblue
         */
        public static function ajax_login_sendinblue()
        {
            check_ajax_referer('itrr_setting_ajax_nonce' , 'security');
            $key = isset($_POST['key']) ? sanitize_text_field($_POST['key']) : '';
            if ($key != '') {
                $response = self::do_request("account/token", "GET", "", $key);
                if (is_array($response)) {
                    if ($response['code'] == 'success') {
                        update_option('itrr_sib_access_key', $key);
                        update_option('itrr_activated_integration', 'sib');
                    }
                }
            }
            echo wp_json_encode($response);
            die();
        }

        /**
         * ajax module for send test email
         */
        public static function ajax_send_email_action()
        {
            check_ajax_referer('itrr_support_ajax_nonce' , 'security');
            global $display_name, $user_email;
            get_currentuserinfo();

            $to = array('contact@intriggerapp.com' => '');
            $subject = __('INT - Support', 'itrr_lang');
            $from_name = $display_name;
            $from_email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
            $from = array($from_email, $from_name);
            $headers = array();
            $text = isset($_POST['content']) ? $_POST['content'] : '';
            $html = isset($_POST['content']) ? $_POST['content'] : '';
            $data = array(
                "to" => $to,
                "subject" => $subject,
                "from" => $from,
                "text" => $text,
                "html" => $html,
                "headers" => $headers
            );
            $result = self::do_request("email", "POST", json_encode($data));
            echo wp_json_encode($result);
            die();
        }

        /**
         * Do CURL request with authorization
         */
        static function do_request($resource, $method, $input, $key = '')
        {
            $called_url = "https://api.sendinblue.com/v2.0" . "/" . $resource;
            $ch = curl_init($called_url);
            if ($key == '')
                $key = self::sib_admin_key;
            $auth_header = 'api-key:' . $key;
            $content_header = "Content-Type:application/json";
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows only over-ride
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, array($auth_header, $content_header));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $input);
            $data = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Curl error: ' . curl_error($ch) . '\n';
            }
            curl_close($ch);
            return json_decode($data, true);
        }
    }
}