<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

/**
 * Intrigger Rule class
 * @package ITRR_Rule
 */

if (!class_exists('ITRR_Rule')) {

    class ITRR_Rule {

        function __construct() {
        }
        public static function getInstance() {
            static $intrigger_rule = null;
            if (null === $intrigger_rule) {
                $intrigger_rule = new ITRR_Rule();
            }
            return $intrigger_rule;
        }

        // check if the float_bar scenario is applied on the page
        function check_where_rule_on_page($page_id) {

            // Get active scenarios.
            $scenario_array = ITRR_Manager::$allActiveScenarioIDs; //ITRR_Scenario_Admin::getAllActiveScenarioIDs();
            $scenarioSettings = ITRR_Manager::$scenarioIndgetSettings;
            $result = '';
            $priority_score_all = 1;
            $priority_score_select = 1;
            $apply_scenario_all = '';
            $apply_scenario_select = '';
            foreach ($scenario_array as $scenario) {
                //
                $scenario_id = $scenario['id'];
                $scenario_type = $scenario['type'];

                $generated_conversion = isset($_COOKIE['itrr_generated_conversion_' . $scenario_id]) ? intval($_COOKIE['itrr_generated_conversion_' . $scenario_id]) : '';

                if ($generated_conversion != '' || $scenario_type != 'float_bar')
                    continue;

                if(!isset($scenarioSettings[$scenario['id']])) continue;

                $scenario_where_rule = $scenarioSettings[$scenario['id']]['rules']['where'];

                // for priority score
                $scenario_score = $scenarioSettings[$scenario['id']]['rules']['priority']['score'];

                // pages
                if (isset($scenario_where_rule['page']) && ($scenario_where_rule['page'] == 'yes')) {

                    if ($scenario_where_rule['page_opt'] == 'all_pages') {
                        if ($scenario_score > $priority_score_all || $scenario_score == $priority_score_all) {
                            $priority_score_all = $scenario_score;
                            $apply_scenario_all = $scenario_id;
                        }
                    } else if ($scenario_where_rule['page_opt'] == 'selected') {
                        if ($scenario_score > $priority_score_select || $scenario_score == $priority_score_select) {
                            $selected_pages = isset($scenario_where_rule['selected_pages']) ? $scenario_where_rule['selected_pages'] : array();
                            if (isset($selected_pages) && is_array($selected_pages)) {
                                if (in_array($page_id, $selected_pages)) {
                                    $apply_scenario_select = $scenario_id;
                                    $priority_score_select = $scenario_score;
                                }
                            }
                        }
                    }
                }
            }
            if ( $apply_scenario_all != '')
            {
                return $apply_scenario_all;
            }
            elseif ( $apply_scenario_select != '')
            {
                return $apply_scenario_select;
            }
            return $result; // empty
        }

        // check rule on posts
        function check_where_rule($content, $post_id, $is_single = true ) {

            // Get active scenarios.
            $scenario_array = ITRR_Manager::$allActiveScenarioIDs; //ITRR_Scenario_Admin::getAllActiveScenarioIDs();
            $scenarioSettings = ITRR_Manager::$scenarioIndgetSettings;

            $result = array();
            foreach ($scenario_array as $scenario) {
                //
                $scenario_id = $scenario['id'];
                $scenario_type = $scenario['type'];

                // If the page is single then it will return float_bar only.
                if(!$is_single && $scenario_type != 'float_bar') continue;

                $generated_conversion = isset($_COOKIE['itrr_generated_conversion_'.$scenario_id]) ? intval($_COOKIE['itrr_generated_conversion_'.$scenario_id]) : '';

                if($generated_conversion != '')
                    continue;
                if(!isset($scenarioSettings[$scenario['id']])) continue;

                $scenario_where_rule = $scenarioSettings[$scenario['id']]['rules']['where'];

                $post_type = get_post_type($post_id);

                // exclude specific urls
                $check_excluded = 0;     // flag for checking excluded url or not
                if (isset($scenario_where_rule['specific_excluded']) && ($scenario_where_rule['specific_excluded'] == 'yes')) {
                    $specific_urls_excluded = isset($scenario_where_rule['specific_urls_excluded']) ? $scenario_where_rule['specific_urls_excluded'] : array();

                    if (is_array($specific_urls_excluded) && count($specific_urls_excluded) > 0) {
                        $current_url = $this->get_current_page_url();
                        foreach ($specific_urls_excluded as $specific_url_excluded) {
                            if ($this->is_valid_url($specific_url_excluded, $current_url) && $specific_url_excluded != '') {
                                $check_excluded++;
                                continue;
                            }
                        }
                    }
                    if($check_excluded > 0) continue;
                }
                // pages
                if (($post_type == 'page') && isset($scenario_where_rule['page']) && ($scenario_where_rule['page'] == 'yes')) {

                    if ($scenario_where_rule['page_opt'] == 'all_pages') {
                        $ret = array(
                            'type'  => 'scenario',
                            'id'    => $scenario_id,
                            'shortcode_str' => '',
                            'scenario_type' => $scenario_type,
                        );
                        array_push($result, $ret);
                        continue;
                    }
                    else if ($scenario_where_rule['page_opt'] == 'selected') {
                        $selected_pages = isset($scenario_where_rule['selected_pages']) ? $scenario_where_rule['selected_pages'] : array();
                        if (isset($selected_pages) && is_array($selected_pages)) {
                            if (in_array($post_id, $selected_pages)) {
                                $ret = array(
                                    'type'  => 'scenario',
                                    'id'    => $scenario_id,
                                    'shortcode_str' => '',
                                    'scenario_type' => $scenario_type,
                                );
                                array_push($result, $ret);
                                continue;
                            }
                        }
                    }
                }

                // categories
                $cat = get_the_category($post_id);
                if(count($cat) != 0) {
                    $cat_id = $cat[0]->cat_ID;
                    if (($post_type == 'post') && (isset($scenario_where_rule['post_cat'][$cat_id]) && ($scenario_where_rule['post_cat'][$cat_id] == 'yes'))) {
                        $ret = array(
                            'type' => 'scenario',
                            'id' => $scenario_id,
                            'shortcode_str' => '',
                            'scenario_type' => $scenario_type,
                        );
                        array_push($result, $ret);
                        continue;
                    }
                }

                // custom posts
                $cpt_apply = false;

                global $wp_post_types;
                $cpt = $wp_post_types[$post_type];
                $post_name = $cpt->labels->name;
                $taxonomies = get_object_taxonomies($post_type);
                if(count($taxonomies) != 0) {
                    $taxonomy = $taxonomies[0]; // for now, we suppose if we have one taxonomy per one CPT...
                    $terms = get_the_terms($post_id, $taxonomy);
                    if (isset($scenario_where_rule['cpt'][$post_name])) {

                        foreach ($terms as $term) {
                            if (!isset($scenario_where_rule['cpt'][$post_name][$term->name]))
                                continue;
                            $cpt_apply = true;
                            break;
                        }
                    }
                    if ($cpt_apply) {
                        $ret = array(
                            'type' => 'scenario',
                            'id' => $scenario_id,
                            'shortcode_str' => '',
                            'scenario_type' => $scenario_type,
                        );
                        array_push($result, $ret);
                        continue;
                    }
                }

                // custom urls
                if (isset($scenario_where_rule['specific']) && ($scenario_where_rule['specific'] == 'yes')) {
                    $specific_urls = isset($scenario_where_rule['specific_urls']) ? $scenario_where_rule['specific_urls'] : array();

                    if (is_array($specific_urls) && count($specific_urls) > 0) {
                        $current_url = $this->get_current_page_url();
                        foreach ($specific_urls as $specific_url) {
                            if ($this->is_valid_url($specific_url, $current_url) && $specific_url != '') {
                                $ret = array(
                                    'type'  => 'scenario',
                                    'id'    => $scenario_id,
                                    'shortcode_str' => '',
                                    'scenario_type' => $scenario_type,
                                );
                                array_push($result, $ret);
                                continue;
                            }
                        }
                    }
                }

            }

            // Get active shortcode
            $is_shortcode_exist = strpos($content, '[intrigger');
            if (($is_shortcode_exist >= 0) && ($is_shortcode_exist != null)) {
                $start_pos = $is_shortcode_exist;
                $end_pos = strpos($content, ']', $is_shortcode_exist);
                $shortcode_str_length = $end_pos - $start_pos + 1;
                $shortcode_str = substr($content, $start_pos, $shortcode_str_length);

                // Get shortcode attr.

                $atts = shortcode_parse_atts($shortcode_str);

                if (isset($atts['indget'])) {
                    $indget_atts = $atts['indget'];
                    $indget_atts = str_replace(' ', '', $indget_atts);
                    $indget_atts = str_replace(']', '', $indget_atts);
                    $indget_atts = str_replace('"', '', $indget_atts);
                    $indget_atts = str_replace('&#8221;', "", $indget_atts);
                    $indget_atts = str_replace('&#8243;', "", $indget_atts);
                    $indget_id = intval($indget_atts);
                    $generated_conversion = isset($_COOKIE['itrr_generated_conversion_'.$indget_id]) ? intval($_COOKIE['itrr_generated_conversion_'.$indget_id]) : '';
                    if($generated_conversion == '')
                    {
                        if (isset($atts['position'])) {
                            $start_pos = $atts['position'];
                            $start_pos = str_replace(' ', '', $start_pos);
                            $start_pos = str_replace(']', '', $start_pos);
                            $start_pos = str_replace('"', '', $start_pos);
                            $start_pos = str_replace('&#8221;', "", $start_pos);
                            $start_pos = str_replace('&#8243;', "", $start_pos);
                        }
                        //
                        $ret = array(
                            'type' => 'indget',
                            'id' => $indget_id,
                            'shortcode_str' => $shortcode_str,
                            'position' => $start_pos,
                        );
                        array_push($result, $ret);
                        //return $ret;
                    }
                }

                if (isset($atts['scenario'])) {
                    $scenario_atts = $atts['scenario'];
                    $scenario_atts = str_replace(' ', '', $scenario_atts);
                    $scenario_atts = str_replace(']', '', $scenario_atts);
                    $scenario_atts = str_replace('"', '', $scenario_atts);
                    $scenario_atts = str_replace('&#8221;', "", $scenario_atts);
                    $scenario_atts = str_replace('&#8243;', "", $scenario_atts);
                    $scenario_id = intval($scenario_atts);
                    $generated_conversion = isset($_COOKIE['itrr_generated_conversion_'.$scenario_id]) ? intval($_COOKIE['itrr_generated_conversion_'.$scenario_id]) : '';
                    if($generated_conversion == '') {
                        $scenario_setting = get_post_meta($scenario_id, ITRR_Scenario::cst_setting_indget, true);
                        if (is_array($scenario_setting)) {
                            $scenario_type = $scenario_setting['type'];
                            $ret = array(
                                'type' => 'scenario',
                                'id' => $scenario_id,
                                'shortcode_str' => $shortcode_str,
                                'scenario_type' => $scenario_type,
                            );
                            array_push($result, $ret);
                        } else {
                            // remove shortcode when don't have any scenario.
                        }
                    }
                }
            }
            return $result;
        }

        function is_valid_url($pattern, $current_page_url){

            $pattern = str_replace('/', '\/', $pattern);
            preg_match_all('/'.$pattern.'/i',$current_page_url, $matches);
            if(count($matches[0]) == 0)
                return false;
            return true;
        }

        function check_when_rule($setting, $scenario_id) {
            if (!empty($setting) && (is_array($setting))) {
                $when_rule = isset($setting['when']['opt']) ? $setting['when']['opt'] : 'always';

                if ($when_rule == 'already_spent') {
                    $setting_spent_time = isset($setting['when']['spent_min']) ? floatval($setting['when']['spent_min']) : 1;
                    $setting_spent_time = $setting_spent_time * 60 * 1000; // convert into microsecond.
                    $setting_spent_time = floatval($setting_spent_time);
                    $start_time = isset($_COOKIE['itrr_history_starttime']) ? intval($_COOKIE['itrr_history_starttime']) : time();
                    $current_time = time();
                    $spent_time = $current_time - $start_time;
                    if ($spent_time < $setting_spent_time) {
                        return false;
                    }
                }
                else if ($when_rule == 'already_visited') {
                    $setting_visited_num = isset($setting['when']['visited_num']) ? intval($setting['when']['visited_num']) : 1;
                    $history_visited_count = isset($_COOKIE['itrr_history_visited_count']) ? intval($_COOKIE['itrr_history_visited_count']) : 0;
                    if ($history_visited_count < $setting_visited_num) {
                        return false;
                    }
                }
            }

            return true;
        }

        function check_user_rule($setting) {
            if (!empty($setting) && (is_array($setting))) {
                $who_rule = isset($setting['who']['opt']) ? $setting['who']['opt'] : 'all';

                if ($who_rule == 'logged') {
                    if (!is_user_logged_in()) {
                        return false;
                    }

                    $user_roles = isset($setting['who']['user_roles']) ? $setting['who']['user_roles'] : array();
                    if (!is_array($user_roles)) {
                        return false;
                    }

                    $current_user_role = $this->get_current_user_role();
                    if (!in_array($current_user_role, $user_roles)) {
                        return false;
                    }

                }
                else if ($who_rule == 'not_logged') {
                    if (is_user_logged_in()) {
                        return false;
                    }
                }
            }

            return true;
        }

        function check_device_rule($setting) {
            if (!empty($setting) && (is_array($setting))) {
                $device_rule = isset($setting['device']) ? $setting['device'] : array('desktop' => 'yes', 'tablets' => 'yes', 'mobile' => 'yes');

                if (is_array($device_rule)) {
                    $current_device = $this->get_platform();

                    if ($current_device == 'desktop') {
                        if (!isset($device_rule['desktop']) || ($device_rule['desktop'] != 'yes')) {
                            return false;
                        }
                    }
                    else if ($current_device == 'tablets') {
                        if (!isset($device_rule['tablets']) || ($device_rule['tablets'] != 'yes')) {
                            return false;
                        }
                    }
                    else if ($current_device == 'mobile') {
                        if (!isset($device_rule['mobile']) || ($device_rule['mobile'] != 'yes')) {
                            return false;
                        }
                    }
                }
            }
            return true;
        }

        function check_retargeting_rule($setting, $scenario_id, $post_id = null) {

            if (!empty($setting) && (is_array($setting))) {
                //
                $retargeting_rule = isset($setting['retargeting']) ? $setting['retargeting'] : array("session" => "yes", "session_num" => "1", "conversion" => "yes", "conversion_opt" => "session", "another_apply_conversion" => "yes");
                $another_apply = get_post_meta($post_id, 'itrr_another_apply', true);
                if ($another_apply == 'yes') {
                    return false;
                }


                // Rule : Apply the scenario only  [value]   time per session
                if (isset($retargeting_rule['session']) && ($retargeting_rule['session'] == 'yes')) {
                    $applied_scenario_count = isset($_COOKIE['itrr_applied_scenario_' . $scenario_id]) ? intval($_COOKIE['itrr_applied_scenario_' . $scenario_id]) : 0;
                    $setting_session_num = intval($retargeting_rule['session_num']);
                    if ($setting_session_num <= $applied_scenario_count) {
                        return false;
                    }
                }

                // Rule : After conversion, do not apply the scenario again for [value].
                if (isset($retargeting_rule['conversion']) && ($retargeting_rule['conversion'] == 'yes')) {
                    $cookie_generated_conversion = isset($_COOKIE['itrr_generated_conversion_' . $scenario_id]) ? intval($_COOKIE['itrr_generated_conversion_' . $scenario_id]) : 0;
                    if ($cookie_generated_conversion == 1) {
                        return false;
                    }
                }


                // Rule : Do not apply another scenario after this scenario is applied.
                if (isset($retargeting_rule['another_apply']) && ($retargeting_rule['another_apply'] == 'yes')) {
                    update_post_meta($post_id,'itrr_another_apply', 'yes');
                }

                // Rule : Do not apply another scenario after this scenario generates a conversion.
                $cookie_another_apply_conversion = isset($_COOKIE['itrr_generated_not_scenario_' . $post_id]) ? intval($_COOKIE['itrr_generated_not_scenario_' . $post_id]) : 0;
                if ($cookie_another_apply_conversion == 1) {
                    return false;
                }
            }
            return true;
        }

        function apply_content_process($indget_id, $indget_limit, $content, $post_id, $scenario_id = 0, $indget_float_display = 'all_time') {
            $ret_content = $content;

            $scenarioSettings = ITRR_Manager::$scenarioIndgetSettings;
            $allIndgets = ITRR_Manager::$allActiveIndgets;
            $indget_type = $allIndgets[$indget_id]['type'];
            $indget_subtype = $allIndgets[$indget_id]['subtype'];
            $indget_setting = $allIndgets[$indget_id]['setting'];

            if ($indget_type == 'continue_reading') {
                // If float bar is applied then ...
                $messages = explode('<!-- float_bar -->', $ret_content);
                $float_content = count($messages) == 1 ? '' : '<!-- float_bar -->'.$messages[1];
                $ret_content = ITRR_Indget_Type::get_continue_reading_content($indget_id, $indget_limit, $post_id, $messages[0], $indget_subtype, $indget_setting );
                $ret_content .= $float_content;

            } else if($indget_type == 'inline') {
                // $indget_limit is 'middle' or 'end'.
                $ret_content = ITRR_Indget_Type::get_inline_content($indget_id, $indget_limit, $post_id, $content, $indget_subtype, $indget_setting );
            } else if($indget_type == 'float_bar') {
                // default pos
                if( $indget_limit != 'top' && $indget_limit != 'bottom'){
                    $indget_limit = 'top';
                }
                $float_content = ITRR_Indget_Type::get_float_content($indget_id, $indget_limit, $post_id, $indget_subtype, $indget_setting );
                $float_content = '<!-- float_bar -->'.$float_content;
                //JS
                if($indget_float_display != 'all_time'){
                    wp_localize_script('itrr-front-js', 'ITRR_FLOAT_SCROLL', json_encode($indget_float_display));
                    wp_localize_script('itrr-front-js', 'ITRR_FLOAT_POS', json_encode($indget_limit));
                }
                $ret_content .= $float_content;
            }
            $ret_content = str_replace('{{scenario_id}}', $scenario_id, $ret_content);
            $is_applied_scenario = 0;
            $is_applied_not_scenario = 0;
            if (isset($scenario_id) && intval($scenario_id)) {
                $scenario_rule = $scenarioSettings[$scenario_id]['rules'];
                if (!empty($scenario_rule) && is_array($scenario_rule)) {
                    $retargeting_rule = isset($scenario_rule['retargeting']) ? $scenario_rule['retargeting'] : array("session" => "yes", "session_num" => "1", "conversion" => "yes", "conversion_opt" => "session", "another_apply_conversion" => "yes");
                    if (isset($retargeting_rule['session']) && ($retargeting_rule['session'] == 'yes')) {
                        $is_applied_scenario = 1;
                    }
                    if (isset($retargeting_rule['another_apply']) && ($retargeting_rule['another_apply'] == 'yes')) {
                        $is_applied_not_scenario = 1;
                    }
                    if (!isset($_COOKIE['itrr_generated_conversion_'.$scenario_id]))
                    {
                        $is_applied_scenario = 1;
                    }
                }
            }
            $ret_content = str_replace('{{is_applied_scenario}}', $is_applied_scenario, $ret_content);
            $ret_content = str_replace('{{is_applied_not_scenario}}', $is_applied_not_scenario, $ret_content);
            return $ret_content;
        }

        function get_current_page_url() {
            $pageURL = 'http';
            if (isset($_SERVER["HTTPS"])) {
                if ($_SERVER["HTTPS"] == "on") {
                    $pageURL .= "s";
                }
            }
            $pageURL .= "://";
            if ($_SERVER["SERVER_PORT"] != "80") {
                $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
            }
            else {
                $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
            }
            return $pageURL;
        }
        function get_platform() {
            $mobile_detect = new ITRR_Mobile_Detect();
            if($mobile_detect->isMobile()){
                if ($mobile_detect->isTablet()) {
                    return 'tablets';
                }
                else {
                    return 'mobile';
                }
            }
            else if ($mobile_detect->isTablet()){
                return 'tablets';
            }
            return 'desktop';
        }
        /**
         * Returns the translated role of the current user. If that user has
         * no role for the current blog, it returns false.
         *
         * @return string The name of the current role
         **/
        function get_current_user_role() {
            $current_user = wp_get_current_user();
            $roles = $current_user->roles;
            $role = array_shift($roles);
            return $role;
        }
    }
}