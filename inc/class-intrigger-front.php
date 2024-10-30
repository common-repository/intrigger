<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

/**
 * Intrigger Front class
 * @package ITRR_Front
 */

if (!class_exists('ITRR_Front')) {

    class ITRR_Front {

        function __construct() {
        }

        public static function ajax_process_action() {
            $indget_id = isset($_POST['indget_id'] ) ? intval($_POST['indget_id']) : 0;
            $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
            $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : "";
            $page = isset($_POST['page']) ? sanitize_text_field($_POST['page']) : "";
            $indget_type = get_post_meta($indget_id, ITRR_Indget::cst_type, true);
            $indget_subtype = get_post_meta($indget_id, ITRR_Indget::cst_subtype, true);
            $indget_setting_data = get_post_meta($indget_id, ITRR_Indget::cst_setting_data, true);
            // Process retargeting process.
            $scenario_id = intval($_POST['scenario_id']);
            if ($scenario_id > 0) {
                $scenario_rule = get_post_meta($scenario_id, ITRR_Scenario::cst_setting_rule, true);
                if (!empty($scenario_rule) && is_array($scenario_rule)) {
                    $retargeting_rule = isset($scenario_rule['retargeting']) ? $scenario_rule['retargeting'] : null;
                    if (isset($retargeting_rule) && is_array($retargeting_rule)) {

                        if ($retargeting_rule['conversion_opt'] == 'session') {
                            $expire_time = 0;
                        }
                        else if('+1 day') {
                            $expire_time = strtotime($retargeting_rule['conversion_opt'], mktime(0, 0, 0));
                        }
                        else if('ever') {
                            $expire_time = time() + (10 * 365 * 24 * 60 * 60);
                        }
                        $scenario_array = ITRR_Scenario_Admin::getAllActiveScenarioIDs();
                        if (isset($retargeting_rule['conversion']) && ($retargeting_rule['conversion'] == 'yes')) {
                            setcookie('itrr_generated_conversion_' . $scenario_id, 1, $expire_time, '/');
                            if(isset($retargeting_rule['another_apply_conversion']) && $retargeting_rule['another_apply_conversion'] == 'yes'){
                                // remove all itrr_applied_scenario_... cookies
                                foreach($scenario_array as $scenario){
                                    setcookie('itrr_applied_scenario_' . $scenario['id'], '', time() - 3600, '/');
                                    setcookie('itrr_history_visited_count', '', time() - 3600, '/');
                                    setcookie('itrr_history_visited_previous', '', time() - 3600, '/');
                                    setcookie('itrr_history_visited_starttime', '', time() - 3600, '/');
                                }
                            }else{
                                // remove itrr_applied_scenario_... cookie
                                setcookie('itrr_applied_scenario_' . $scenario_id, '', time() - 3600, '/');
                                setcookie('itrr_history_visited_count', '', time() - 3600, '/');
                                setcookie('itrr_history_visited_previous', '', time() - 3600, '/');
                                setcookie('itrr_history_visited_starttime', '', time() - 3600, '/');
                            }

                        }

                        if (isset($retargeting_rule['another_apply_conversion']) && ($retargeting_rule['another_apply_conversion'] == 'yes')) {
                            setcookie('itrr_generated_not_scenario_' . $post_id, 1, $expire_time, '/');
                        }
                    }
                }
                // contacts
                if($email != ""){

                    $added_contact = array(
                        'email' => $email,
                        'scenario' => get_the_title($scenario_id),
                        'indget' => get_the_title($indget_id),
                        'page' => $page,
                    );
                    ITRR_Contacts::addContact($added_contact);

                    // To add to a list of SendinBlue
                    if( get_option('itrr_contact_sib_list') != false && get_option('itrr_contact_sib_list') != '-1' ){

                        $list_id = intval(get_option('itrr_contact_sib_list')); // int
                        $response = ITRR_Sendinblue::sib_add_user($email, $list_id);
                    }
                    // To add to a list of MailChimp
                    if( get_option('itrr_contact_mcp_list') != false && get_option('itrr_contact_mcp_list') != '-1' ){

                        $list_id = get_option('itrr_contact_mcp_list'); // string
                        $response = ITRR_Mailchimp::mcp_add_subscriber($email, $list_id);
                    }

                }
                /**
                 * Conversion of scenario
                 */
                ITRR_Stats::update_stats_trigger($scenario_id, 'conversion');

            }
            /**
             * Conversion of indget
             */
            ITRR_Stats::update_stats_trigger($indget_id, 'conversion');

            if ($indget_type == 'continue_reading' || $indget_type == 'inline') {
                $post_content = get_post_field('post_content', $post_id);
                $post_content = apply_filters('the_content', $post_content);
                $post_content = str_replace(']]>', ']]&gt;', $post_content);
                $post_content = self::remove_shortcode($post_content);

                if($indget_type == 'inline' && isset($indget_setting_data['inline']['collect_email']['confirmation_message']) && $indget_subtype == 'collect_email'){
                    $font_size = $indget_setting_data['inline']['collect_email']['headline_fontsize'];
                    $background_color = $indget_setting_data['inline']['collect_email']['background_color'];
                    $response = array(
                        'type'=>'inline',
                        'content'=> '<div style="background-color: '.$background_color.';padding: 30px;border-radius: 10px;">
                        <div style="text-align: center; font-size: '.$font_size.'px; color: #000000; ">'.$indget_setting_data['inline']['collect_email']['confirmation_message'].
                            '</div>
                        <div class="clearfix"></div>
                        </div>',
                    );
                }else if($indget_type == 'inline'){
                    $response = array(
                        'type'=>'inline',
                        'content'=>'',
                    );
                }else{
                    $response = array(
                        'type'=>'continue',
                        'content'=>$post_content,
                    );
                }

                echo json_encode($response);
            }else if($indget_type == 'float_bar'){
                $confirm_msg = $content = '';
                if(isset($indget_setting_data['float_bar']['collect_email'])) {
                    $font_size = $indget_setting_data['float_bar']['collect_email']['headline_fontsize'];
                    $font_color = $indget_setting_data['float_bar']['collect_email']['headline_font_color'];
                    $confirm_msg = isset($indget_setting_data['float_bar']['collect_email']) ? $indget_setting_data['float_bar']['collect_email']['confirmation_message'] : '';
                    $confirm_msg = '<div class="int_float_confirm" style="display:inline-block; font-size: ' . $font_size . 'px; color: ' . $font_color . ' !important;">' . $confirm_msg . '</div>';
                }
                $response = array(
                    'type'=>'float_bar',
                    'content'=>$confirm_msg,
                    'confirmation'=> $confirm_msg,
                );

                echo json_encode($response);
            }
            die();
        }

        public static function remove_shortcode($content) {
            $is_shortcode_exist = strpos($content, '[intrigger');
            if (($is_shortcode_exist >= 0) && ($is_shortcode_exist != null)) {
                $start_pos = $is_shortcode_exist;
                $end_pos = strpos($content, ']', $is_shortcode_exist);
                $shortcode_str_length = $end_pos - $start_pos + 1;
                $shortcode_str = substr($content, $start_pos, $shortcode_str_length);
                $content = str_replace($shortcode_str, "", $content);
            }
            return $content;
        }

    }
}