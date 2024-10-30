<?php
/**
Plugin Name: InTrigger
Plugin URI: http://intriggerapp.com/
Description: InTrigger Plugin allows webmasters to set up on-site personalization scenarios in order to generate more subscribers and conversions.
Version: 1.0.6
Author: InTrigger
Author URI: http://intriggerapp.com/
License: GPLv2 or later
 */

/**
 * Application entry point. Contains plugin startup class that loads on <i> intrigger_init </i> action.
 * @package ITRR
 */

if (!class_exists('ITRR_Manager')) {

    register_deactivation_hook( __FILE__, array('ITRR_Manager', 'deactivate'));
    register_activation_hook( __FILE__, array('ITRR_Manager', 'install'));
    register_uninstall_hook(__FILE__, array('ITRR_Manager', 'uninstall'));

    require_once('integration/intrigger-sendinblue.php');
    require_once('integration/intrigger-mailchimp.php');
    require_once('page/page-home.php');
    require_once('page/page-contact.php');
    require_once('page/page-support.php');
    require_once('page/page-setting.php');
    require_once('table/table-contact.php');
    require_once('inc/class-model-stats.php');
    require_once('inc/class-model-contacts.php');
    require_once('inc/class-intrigger-rule.php');
    require_once('inc/class-intrigger-indget-admin.php');
    require_once('inc/class-intrigger-indget-type.php');
    require_once('inc/class-intrigger-indget.php');
    require_once('inc/class-intrigger-scenario-admin.php');
    require_once('inc/class-intrigger-scenario.php');
    require_once('inc/class-intrigger-front.php');
    require_once('inc/class-mobile-detect.php');
    require_once('inc/class-bot-detect.php');


    class ITRR_Manager {
        /**
         * Plugin directory path value. set in constructor
         * @access public
         * @var string
         */
        public static $plugin_dir;

        /**
         * Plugin url. set in constructor
         * @access public
         * @var string
         */
        public static $plugin_url;

        /**
         * Plugin name. set in constructor
         * @access public
         * @var string
         */
        public static $plugin_name;

        /**
         * Impression flag
         */
        private $impression_flag = false;

        /** check if the page is single */
        private $is_single;
        /** current page id*/
        private $page_id;
        /** flag of bot detect */
        private $detect_bot;
        /** check if indget is applied */
        public $applied_indget;

        /** for scenario and indget settings */
        public static $allActiveScenarioIDs;
        public static $scenarioIndgetSettings;
        public static $allActiveIndgets;
        /**
         * Class constructor
         * Sets plugin url and directory and adds hooks to <i>init</i>. <i>admin_menu</i>
         */
        function __construct() {

            // get basic info
            self::$plugin_dir = plugin_dir_path(__FILE__);
            self::$plugin_url = plugins_url('', __FILE__);
            self::$plugin_name = plugin_basename(__FILE__);

            if( is_admin() && current_user_can('manage_options') ) {
                ITRR_Indget_Admin::getInstance();
                ITRR_Scenario_Admin::getInstance();
                add_action('admin_init', array(&$this, 'admin_init'), 9999);
                add_action('admin_menu', array(&$this, 'admin_menu'), 9);
                add_action('admin_notices', array(&$this, 'add_admin_notices'));
                add_action('admin_print_styles', array(&$this, 'remove_preview_button'));
                add_filter('post_row_actions', array(&$this, 'remove_quick_edit_actions'), 10, 2);

                add_action( 'admin_enqueue_scripts', array(&$this,'wp_head_admin'), 999);

                add_action('wp_ajax_itrr_date_action', array('ITRR_Page_Home', 'ajax_date_action'));
                add_action('wp_ajax_nopriv_itrr_date_action', array('ITRR_Page_Home', 'ajax_date_action'));

                add_action('wp_ajax_itrr_send_email_action', array('ITRR_Sendinblue', 'ajax_send_email_action'));
                add_action('wp_ajax_nopriv_itrr_send_email_action', array('ITRR_Sendinblue', 'ajax_send_email_action'));

                add_action('wp_ajax_itrr_login_sendinblue', array('ITRR_Sendinblue', 'ajax_login_sendinblue'));
                add_action('wp_ajax_nopriv_itrr_login_sendinblue', array('ITRR_Sendinblue', 'ajax_login_sendinblue'));

                add_action('wp_ajax_itrr_login_mailchimp', array('ITRR_Mailchimp', 'ajax_login_mailchimp'));
                add_action('wp_ajax_nopriv_itrr_login_mailchimp', array('ITRR_Mailchimp', 'ajax_login_mailchimp'));
                add_action('wp_ajax_clear_plugin_settings' , array('ITRR_Page_Setting' , 'clear_plugin_settings'));
            }

            //add_action('wp_print_scripts', array(&$this,'frontend_register_scripts'), 9999);
            add_action('wp_head', array(&$this, 'wp_head_ac'), 9);

            add_action('init', array(&$this, 'init'));

            add_action('wp_ajax_itrr_fr_action', array('ITRR_Front', 'ajax_process_action'));
            add_action('wp_ajax_nopriv_itrr_fr_action', array('ITRR_Front', 'ajax_process_action'));

        }
        /**
         * Initialize method. called on <i>init</i> action
         */
        function init() {

            ITRR_Scenario::register_trigger_post_type();
            ITRR_Indget::register_indget_post_type();

            add_action('wp_head', array(&$this, 'pr_the_head'));
            add_filter('the_content', array(&$this, 'pr_the_content'));

            if(current_user_can('manage_options') && isset( $_GET['csv_export'])) {
                ITRR_Contacts_List::csv_download();
            }

            if(current_user_can('manage_options') && (isset($_GET['itrr_sib_action'])) && ($_GET['itrr_sib_action'] == 'logout')) {
                ITRR_Page_Setting::sib_logout();
            }
            if(current_user_can('manage_options') && (isset($_GET['itrr_mcp_action'])) && ($_GET['itrr_mcp_action'] == 'logout')) {
                ITRR_Page_Setting::mcp_logout();
            }

           // get all activate scenario ids and settings , also indgets and indget settings.
            $allScenarioSetting = get_option('itrr_all_scenarios', array());
            if(count($allScenarioSetting) > 0)
            {
                self::$allActiveScenarioIDs = $allScenarioSetting['active'];
                self::$scenarioIndgetSettings = $allScenarioSetting['settings'];
            }

            self::$allActiveIndgets = get_option('itrr_all_indgets', array());

           // save all activated scenario settings and indget settings under admin page only
            if(is_admin()){
                // scenario
                self::$allActiveScenarioIDs = ITRR_Scenario_Admin::getAllActiveScenarioIDs();
                self::$scenarioIndgetSettings = ITRR_Scenario_Admin::getScenarioSettings();
                $allScenarioSetting = array(
                    'active' => self::$allActiveScenarioIDs,
                    'settings' => self::$scenarioIndgetSettings
                    );
                update_option('itrr_all_scenarios', $allScenarioSetting);

                // indget
                self::$allActiveIndgets = ITRR_Indget_Admin::getAllActiveIndget();
                update_option('itrr_all_indgets', self::$allActiveIndgets);
            }

        }

        /** hook admin_init */
        function admin_menu() {
            new ITRR_Page_Home();
            new ITRR_Page_Contacts();
            new ITRR_Page_Support();
            new ITRR_Page_Setting();
        }

        /** hook admin_init */
        function admin_init() {

        }

        /** hook admin_notices */
        function add_admin_notices() {

        }

        /**
         * Remove preview button in scenario and indget.
         */
        function remove_preview_button() {
            global $post_type;
            if( $post_type == 'itrr_indget' || $post_type == 'itrr_scenario' ) {
                ?>
                <style type="text/css">
                    #message.updated.below-h2{ display: none; }
                    #preview-action { display:none; }
                </style>
            <?php
            }
        }

        /**
         * Remove quick edit button in post row.
         */
        function remove_quick_edit_actions($actions, $post) {

            if (empty($post->post_type) || ($post->post_type != 'itrr_indget' && $post->post_type != 'itrr_scenario'))
                return $actions;

            unset($actions['inline hide-if-no-js']);
            unset($actions['view']);

            return $actions;
        }

        function pr_the_head(){

            // to check if the_content is applied already. such as Ninja popup
            $this->applied_indget = false;
            // check if page is single or not
            $this->is_single = true;
            if(is_front_page() || is_home() || is_search() || is_comments_popup() || is_archive()){
                $this->is_single = false;
            }

        }
        /**
         * Process of scenario and ingdet on posts, pages, custom posts...
         */
        function pr_the_content($content) {

            if(is_404() || $this->applied_indget){
                return $content;
            }
            // We ignore the triggers for woocommerce pages.
            if(class_exists( 'WooCommerce' )){
                if(is_woocommerce() || is_cart() || is_checkout() || is_account_page() || is_product_tag() || is_product_category())
                    return $content;
            }

            global $post;
            $intrigger_rule = ITRR_Rule::getInstance();
            // 1. Check where rule.
            // If the page is single then display float_bar only
            $results = $intrigger_rule->check_where_rule($content, $post->ID, $this->is_single);
            if(count($results) == 0){
                $content = ITRR_Front::remove_shortcode($content);
                return $content;
            }

            // to initialize for bot search
            $bot_allow = get_option('itrr_setting_seo_continue','0');
            $CrawlerDetect = new CrawlerDetect();
            $user_agent = $_SERVER['HTTP_USER_AGENT'];

            $scenarioSettings = self::$scenarioIndgetSettings;
            $content_length = strlen(strip_tags($content));

            $apply_indget = '';
            $scenario_id = 0;
            if (!empty($results) && is_array($results)) {

                foreach($results as $key=>$scenario) {

                    if ($scenario['type'] == 'scenario' && $scenario['shortcode_str'] == '') {

                        $scenario_id = $scenario['id'];
                        $scenario_rule = $scenarioSettings[$scenario_id]['rules'];//get_post_meta($scenario_id, ITRR_Scenario::cst_setting_rule, true);
                        // 2. Check when rule.
                        $is_valid_when = $intrigger_rule->check_when_rule($scenario_rule, $scenario_id);

                        // 3. Check user rule.
                        $is_valid_user = $intrigger_rule->check_user_rule($scenario_rule);

                        // 4. Check device rule.
                        $is_valid_device = $intrigger_rule->check_device_rule($scenario_rule);

                        // 5. Check retargeting rule.
                        $is_valid_retarget = $intrigger_rule->check_retargeting_rule($scenario_rule, $scenario_id, $post->ID);

                        if (!$is_valid_when || !$is_valid_user || !$is_valid_device || !$is_valid_retarget){
                            unset($results[$key]);
                        }
                    }elseif($scenario['type'] == 'indget' ){
                        unset($results[$key]);
                        $apply_indget = $scenario;
                    }
                }
                update_post_meta($post->ID,'itrr_another_apply', 'no');
                // $apply_scenarios must has max 3 scenarios that each type is one only.
                $apply_scenarios = array();
                // determine applied scenarios by comparing priority scores and scenario ids
                $priority = array(
                    'continue_reading' => 1,
                    'inline' => 1,
                    'float_bar' => 1,
                );
                foreach($results as $scenario){
                    if(isset($scenario['scenario_type'])) {
                        $type = $scenario['scenario_type'];
                        $scenario_indget_rules = get_post_meta($scenario['id'], ITRR_Scenario::cst_setting_rule, true);
                        $scenario_score = $scenario_indget_rules['priority']['score'];
                        if ( $scenario_score > $priority[$type] || $scenario_score == $priority[$type] )
                        {
                            $priority[$type] = $scenario_score ;
                            $apply_scenarios[$type] = $scenario;
                        }
                    }
                }
                // 6. Apply indgets.
                $indget_is_applied = false;
                foreach($apply_scenarios as $key => $scenario){

                    $indget_id = 0;
                    $indget_float_display = 'all_time';

                    //scenario
                    $scenario_id = $scenario['id'];
                    $scenario_indget = get_post_meta($scenario_id, ITRR_Scenario::cst_setting_indget, true);

                    if (!empty($scenario_indget) && is_array($scenario_indget)) {
                        $indget_id = $scenario_indget['id'];
                        $scenario_type = $scenario_indget['type'];
                        if($scenario_type == 'continue_reading') {
                            $indget_setting_position = $scenario_indget['continue_reading']['position'];
                            $indget_setting = $scenario_indget['setting'][$indget_setting_position];
                            //
                            if ($indget_setting_position == 'percent') {
                                $indget_setting = intval($content_length * $indget_setting / 100);
                            }

                        }else if($scenario_type == 'inline'){
                            // inline
                            $indget_setting = $scenario_indget['inline']['position']; // 'middle' or 'end'

                        }else if($scenario_type == 'float_bar'){
                            // float_bar
                            $indget_setting = $scenario_indget['float_bar']['position']; // 'top' or 'bottom'
                            if($scenario_indget['float_bar']['display'] == 'all_time'){
                                $indget_float_display = 'all_time';
                            }else{
                                $indget_float_display = $scenario_indget['setting']['scroll'];
                            }
                        }
                        // to count of Conversion and Impression of Indget
                        if(!$this->impression_flag){
                            // update trigger impression
                            ITRR_Stats::update_stats_trigger($scenario_id, 'impression');
                            ITRR_Stats::update_stats_trigger($indget_id, 'impression');

                        }
                        if ( is_front_page() && 'posts' == get_option( 'show_on_front' )) {
                            $this->impression_flag = true;
                        }
                    }
                    if ($indget_id != 0) {
                        if($scenario['scenario_type'] != '') {
                            $content = ITRR_Front::remove_shortcode($content);
                        }

                        $this->detect_bot=false;
                        $bot_allow=get_option('itrr_setting_seo_continue','0');
                        // to detect bot
                        if($scenario['scenario_type']=='continue_reading' && $bot_allow=='1')
                        {
                            $this->detect_bot= $CrawlerDetect->isCrawler($user_agent);
                        }
                        // bot is not detected
                        if($this->detect_bot==false)
                        {
                            $content = $intrigger_rule->apply_content_process($indget_id, $indget_setting, $content, $post->ID, $scenario_id, $indget_float_display );
                        }
                        // in case of bot detect
                        else{
                            if($scenario['scenario_type']!='continue_reading') {
                                $content = $intrigger_rule->apply_content_process($indget_id, $indget_setting, $content, $post->ID, $scenario_id, $indget_float_display );
                            }
                        }
                        $content = ITRR_Front::remove_shortcode($content);
                        $indget_is_applied = true;
                    }
                }


                // apply indget for shortcode
                $indget_float_display = 'all_time';
                if ( is_array($apply_indget) && !$indget_is_applied ) {
                    // when shortcode is inserted to post or page
                    $indget_id = $apply_indget['id'];
                    $indget_setting = $apply_indget['position'];

                    // to count of Conversion and Impression of Indget
                    // update trigger impression
                    ITRR_Stats::update_stats_trigger($indget_id, 'impression');
                    // initialize for detect bot
                    $this->detect_bot = false;
                    if ($indget_id != 0) {
                        $indget_type = get_post_meta($indget_id, ITRR_Indget::cst_type, true);
                        // to detect bot
                        if($indget_type == 'continue_reading' && $bot_allow == '1')
                        {
                            $this->detect_bot = $CrawlerDetect->isCrawler($user_agent);
                        }

                        // bot is not detected
                        if($this->detect_bot == false )
                        {
                            $content = $intrigger_rule->apply_content_process($indget_id, $indget_setting, $content, $post->ID, $scenario_id, $indget_float_display );
                        }
                        // in case of detect bot
                        else{
                            if($indget_type != 'continue_reading') {
                                $content = $intrigger_rule->apply_content_process($indget_id, $indget_setting, $content, $post->ID, $scenario_id, $indget_float_display );
                            }
                        }
                        $content = ITRR_Front::remove_shortcode($content);
                    }
                }
            }
            // set true when the_content is applied first
            $this->applied_indget = true;

            return $content;
        }

        function wp_head_ac() {
            wp_enqueue_script('itrr-front-js', self::$plugin_url . '/asset/js/front.js', array(), filemtime(self::$plugin_dir.'asset/js/front.js'));
            wp_localize_script('itrr-front-js', 'itrr_admin_ajax_url', admin_url('admin-ajax.php'));
            $ajax_nonce = wp_create_nonce("itrr-front-ajax-nonce");
            wp_localize_script('itrr-front-js', 'itrr_admin_ajax_nonce', $ajax_nonce);
            wp_enqueue_style('itrr-front-css', self::$plugin_url . '/asset/css/front-common.css' , array(), filemtime(self::$plugin_dir.'/asset/css/front-common.css'));
            wp_enqueue_style('itrr-template-css', self::$plugin_url . '/asset/css/template/default-templates.css' , array(), filemtime(self::$plugin_dir.'/asset/css/template/default-templates.css'));
        }

        function wp_head_admin() {
            wp_enqueue_script('itrr-tooltipster-js', self::$plugin_url . '/asset/js/jquery.tooltipster.min.js');
            wp_enqueue_style('itrr-indget-tooltipster-css', self::$plugin_url . '/asset/css/tooltipster.css');
            wp_enqueue_style('itrr-indget-tooltipster-light-css', self::$plugin_url . '/asset/css/themes/tooltipster-light.css');
            wp_enqueue_style('itrr-template-css', self::$plugin_url . '/asset/css/template/default-templates.css' , array(), filemtime(self::$plugin_dir.'/asset/css/template/default-templates.css'));

            wp_enqueue_script('itrr-back-js', self::$plugin_url . '/asset/js/back.js', array(), filemtime(self::$plugin_dir.'asset/js/front.js'));
            wp_localize_script('itrr-back-js', 'itrr_admin_ajax_url', admin_url('admin-ajax.php'));
            wp_localize_script('itrr-back-js' , 'itrr_site_url' , site_url());
            $ajax_nonce = wp_create_nonce("itrr-back-ajax-nonce");
            wp_localize_script('itrr-back-js', 'itrr_admin_ajax_nonce', $ajax_nonce);
        }

        /**
         * Install method is called once install this plugin.
         * create tables, default option ...
         */
        static function install() {
            ITRR_Indget::create_default_indgets();

            update_option('itrr_setting_seo_continue',true);
            update_option('itrr_setting_contacts' , true);
            update_option('itrr_setting_branding' , true);

            ITRR_Stats ::create_table();
            ITRR_Contacts::create_table();
        }

        /**
         * Uninstall method is called once uninstall this plugin
         * delete tables, options that used in plugin
         */
        static function uninstall() {

        }

        /**
         * Deactivate method is called once deactivate this plugin
         */
        static function deactivate() {
            $setting_val = '';
            update_option('itrr_mcp_access_key', $setting_val);
            update_option('itrr_sib_access_key', $setting_val);
            delete_option('itrr_setting_seo_continue');
        }

        /**
         * Load Text domain.
         */
        static function LoadTextDomain() {
            // load lang file
            $i18n_file_name = 'itrr_lang';
            $locale         = apply_filters('plugin_locale', get_locale(), $i18n_file_name);
            //$locale = 'fr_FR';
            $filename       = plugin_dir_path(__FILE__). '/lang/' .$i18n_file_name.'-'.$locale.'.mo';
            load_textdomain('itrr_lang', $filename);
        }
    }

}

/**
 * Plugin entry point Process.
 * */
function intrigger_init() {
    global $intrigger;
    ITRR_Manager::LoadTextDomain();
    $intrigger = new ITRR_Manager();
    do_action('intrigger_loaded');
}
add_action('plugins_loaded', 'intrigger_init');
