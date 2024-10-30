<?php
/**
 * Admin page : dashboard
 * @package ITRR_Page_Home
 */

/**
 * Page class that handles backend page <i>dashboard ( for admin )</i> with form generation and processing
 * @package ITRR_Page_Home
 */

if (!class_exists('ITRR_Page_Home')) {
    class ITRR_Page_Home
    {
        /**
         * Page slug
         */
        const page_id = 'itrr_page_home';
        /*
         * Status
         */
        private static $has_active_scenario = false;
        private static $impression = 0;
        private static $conversion = 0;
        private static $conversion_rate = '0.0';
        /**
         * Page hook
         * @var string
         */
        protected $page_hook;

        /**
         * page tabs
         * @var mixed
         */
        protected $tabs;

        /**
         * scenario IDs
         */
        public static $itrr_scenarios;

        /**
         * Constructs new page object and adds entry to Wordpress admin menu
         */
        function __construct()
        {
            add_menu_page(__('InTrigger', 'itrr_lang'), __('InTrigger', 'itrr_lang'), 'manage_options', self::page_id, array(&$this, 'generate'), ITRR_Manager::$plugin_url . '/asset/img/favicon.ico');
            $this->page_hook = add_submenu_page(self::page_id, __('Home', 'itrr_lang'), __('Home', 'itrr_lang'), 'manage_options', self::page_id, array(&$this, 'generate'));
            add_action('load-' . $this->page_hook, array($this, 'init'));
            add_action('admin_print_scripts-' . $this->page_hook, array($this, 'enqueue_scripts'));
            add_action('admin_print_styles-' . $this->page_hook, array($this, 'enqueue_styles'));
            add_submenu_page(self::page_id, __('Scenarios', 'itrr_lang'), __('Scenarios', 'itrr_lang'), 'manage_options', 'edit.php?post_type=itrr_scenario');
            add_submenu_page(self::page_id, __('Indgets', 'itrr_lang'), __('Indgets', 'itrr_lang'), 'manage_options', 'edit.php?post_type=itrr_indget');
        }

        /**
         * Init Process
         */
        function init()
        {
            self::get_status();
        }

        // get status data according to date range
        public static function get_status($begin = null, $end = null)
        {

            if ($begin == null && $end == null) {
                $begin = date("Y-m-d");
                $end = Date("Y-m-d", strtotime("+1 days"));
            }
            $args = array(
                'post_type' => ITRR_Scenario::cst_post_type,
                'post_status' => 'publish'
            );
            $my_query = new WP_Query($args);
            self::$itrr_scenarios = array();
            if ($my_query->have_posts()) {
                self::$has_active_scenario = true;
                $trigger_clicks_total = 0;
                $trigger_impressions_total = 0;
                while ($my_query->have_posts()) : $my_query->the_post();
                    $scenario_id = get_the_ID();
                    $trigger_stats = ITRR_Stats::get_stats_period($scenario_id, $begin, $end);
                    $trigger_type = get_post_meta($scenario_id, ITRR_Scenario::cst_post_type, true);
                    $trigger_name = get_the_title($scenario_id);
                    $post_link = get_edit_post_link($scenario_id, 'edit');
                    $scenario = array(
                        'trigger_name' => $trigger_name,
                        'trigger_type' => $trigger_type,
                        'impression' => $trigger_stats['impression'],
                        'conversion' => $trigger_stats['conversion'],
                        'rate' => $trigger_stats['rate'],
                        'link' => $post_link
                    );

                    array_push(self::$itrr_scenarios, $scenario);

                    $trigger_clicks_total += $trigger_stats['conversion'];
                    $trigger_impressions_total += $trigger_stats['impression'];

                endwhile;

                // All stats of scenarios
                if ($trigger_impressions_total == 0 || $trigger_clicks_total == 0) {
                    $trigger_rate = '0.0';
                } else {
                    $trigger_rate = number_format(floatval($trigger_clicks_total * 100 / $trigger_impressions_total), 1);
                }

                self::$impression = $trigger_impressions_total;
                self::$conversion = $trigger_clicks_total;
                self::$conversion_rate = $trigger_rate;
            }
        }

        /**
         * enqueue scripts of plugin
         */
        function enqueue_scripts()
        {
            $indget_type_object = ITRR_Indget_Type::getInstance();

            wp_enqueue_script('itrr-indget-angular-js', ITRR_Manager::$plugin_url . '/asset/js/angular.min.js', array(), false, true);
            wp_enqueue_script('itrr-indget-admin-js', ITRR_Manager::$plugin_url . '/asset/js/angular.scenario-admin.js' , array(), filemtime(ITRR_Manager::$plugin_dir . '/asset/js/angular.scenario-admin.js'),true);

            // for daterangepicker js
            wp_enqueue_script('jquery-ui-autocomplete');
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script('jquery-ui-dialog');
            wp_enqueue_script('itrr-moment-js', ITRR_Manager::$plugin_url . '/asset/js/moment.js', array(),false,true);
            wp_enqueue_script('itrr-date-js', ITRR_Manager::$plugin_url . '/asset/js/jquery.comiseo.daterangepicker.min.js', array(),false,true);

            wp_localize_script('itrr-indget-admin-js', 'ITRR_INDGET_TYPES', json_encode($indget_type_object->main_types));
            wp_localize_script('itrr-indget-admin-js', 'ITRR_HOME_LISTS', 'is_home');
        }

        /**
         * enqueue style sheets of plugin
         */
        function enqueue_styles()
        {
            wp_enqueue_style('itrr-date-css', ITRR_MANAGER::$plugin_url . '/asset/css/jquery.comiseo.daterangepicker.css');
            wp_enqueue_style('itrr-ui-css', ITRR_MANAGER::$plugin_url . '/asset/css/jquery-ui.css');
            wp_enqueue_style('itrr-common-css', ITRR_MANAGER::$plugin_url . '/asset/css/admin-common.css', array(), filemtime(ITRR_MANAGER::$plugin_dir . '/asset/css/admin-common.css'));
            wp_enqueue_style('itrr-indget-admin-css', ITRR_Manager::$plugin_url . '/asset/css/admin-scenario.css', array(), filemtime(ITRR_Manager::$plugin_dir . '/asset/css/admin-scenario.css'));
            wp_enqueue_style('itrr-indget-fontawesome-css', ITRR_Manager::$plugin_url . '/asset/css/fontawesome/css/font-awesome.min.css');
        }

        /** generate page script */
        function generate()
        {
            ?>
            <div class="wrap itrr_home">
                <h2 class="itrr_home_title"><?php _e('Home', 'itrr_lang'); ?></h2>
                <?php
                if (self::$has_active_scenario) {
                    ?>
                    <div class="itrr_date_picker">
                        <input id="itrr_date_picker" name="itrr_date_picker">
                        <input id="itrr_homepage_ajax_nonce" type="hidden" name="itrr_homepage_ajax_nonce" value="<?php echo(wp_create_nonce('itrr_homepage_ajax_nonce'));?>">
                    </div>
                    <div class="itrr_home_table">
                        <table class="itrr_stats_table" style="width: 100%;">
                            <tbody>
                            <tr>
                                <td class="itrr_stats_td">
                                    <div class="itrr_stats itrr_impression" style="">
                                        Impressions<a href="#" style="float: right; color: #fff;"><i
                                                class="fa fa-question-circle itrr-tooltip"
                                                title="<?php _e('Number of impressions. We count one impression each time a scenario is applied on a page, for example when a form is loaded inside a post.', 'itrr_lang');?>"></i></a>
                                    </div>
                                    <div class="itrr_stats_detail" style="">
                                        <span id="itrr_stats_impression"><?php echo self::$impression; ?></span>
                                    </div>
                                </td>
                                <td style="width: 5%;"></td>
                                <td class="itrr_stats_td">
                                    <div class="itrr_stats itrr_conversion">
                                        Conversions<a href="#" style="float: right; color: #fff;"><i
                                                class="fa fa-question-circle itrr-tooltip"
                                                title="<?php _e('Number of conversions. We count one conversion each time a visitor subscribes or click on a button from one indget.', 'itrr_lang');?>"></i></a>
                                    </div>
                                    <div class="itrr_stats_detail">
                                        <span id="itrr_stats_conversion"><?php echo self::$conversion; ?></span>
                                    </div>
                                </td>
                                <td style="width: 5%;"></td>
                                <td class="itrr_stats_td">
                                    <div class="itrr_stats itrr_conversation_rate">
                                        Conversion rate<a href="#" style="float: right; color: #fff;"><i
                                                class="fa fa-question-circle itrr-tooltip"
                                                title="Conversion rate"></i></a>
                                    </div>
                                    <div class="itrr_stats_detail">
                                            <span
                                                id="itrr_stats_rate"><?php echo self::$conversion_rate . "%"; ?></span>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <br>

                    <div class="itrr_home_table">
                        <h3><?php _e('Active scenarios', 'itrr_lang'); ?></h3>
                        <table class="itrr_active_scenario_table">
                            <thead>
                            <tr>
                                <th><?php _e('Name', 'itrr_lang'); ?></th>
                                <th><?php _e('Type', 'itrr_lang'); ?></th>
                                <th><?php _e('Impressions', 'itrr_lang'); ?></th>
                                <th><?php _e('Conversions', 'itrr_lang'); ?></th>
                                <th><?php _e('Conversion rate', 'itrr_lang'); ?></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php foreach (self::$itrr_scenarios as $itrr_scenario) { ?>
                                <tr id="" class="itrr_active_triggers">
                                    <td class="itrr_scenario_name"><?php echo $itrr_scenario['trigger_name']; ?></td>
                                    <td><span
                                            class="itrr_scenario_<?php echo $itrr_scenario['trigger_type']; ?>"><?php echo ucfirst(str_replace('_',' ',$itrr_scenario['trigger_type'])); ?></span>
                                    </td>
                                    <td class="itrr_impression_val"><?php echo $itrr_scenario['impression']; ?></td>
                                    <td class="itrr_conversion_val"><?php echo $itrr_scenario['conversion']; ?></td>
                                    <td class="itrr_rate_val"><?php echo $itrr_scenario['rate'] . '%'; ?></td>
                                    <td><a href="<?php echo $itrr_scenario['link']; ?>" title="Edit the scenario"><i
                                                class="fa fa-pencil"></i></a></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <script type="text/javascript">


                    </script>
                <?php
                } else {
                    ?>
                    <div class="itrr_home_row clear">
                        <h2 class="itrr_home_tag">
                        <span class=""><?php _e('Getting started', 'itrr_lang'); ?></span>
                        </h2>


                        <div class="clear"
                             style="-webkit-box-sizing: border-box; -moz-box-sizing: border-box;  box-sizing: border-box;">
                            <h3><?php _e('Use the tips below to get started using InTrigger plugin. You will be up and running in no time!', 'itrr_lang'); ?></h3>
                            <div class="itrr_home_col_6">
                                <h4><?php _e('Create scenarios', 'itrr_lang'); ?></h4>
                                <?php _e('Basically, a scenario is a set of rules displaying an indget in some pages to some visitors. For example,
                            you can build a scenario "Continue - Collect" which hide post content after 3 pages visited. The visitor is invited
                            to sign up in order to read full post.', 'itrr_lang');?><br><br>
                                <h4><?php _e('Design indgets', 'itrr_lang'); ?></h4>
                                <?php _e('A scenario displays what we call an "Indget". Similarly to widgets, an indget could be a floating bar,
                            a form or just an inline message. We have already created some indgets so that you have some examples.', 'itrr_lang');?>
                                <br><br>
                                <h4><?php _e('Define settings', 'itrr_lang'); ?></h4>
                                <?php _e('Most settings are defined on the level of each scenario to target specifically some users: pages URL,
                            time spent on the website, device used, etc In the menu, you can modify global settings, in', 'itrr_lang');?>
                            </div>
                            <div class="itrr_home_col_6">
                                <img class="itrr_home_intro"
                                     src="<?php echo ITRR_MANAGER::$plugin_url . '/asset/img/getting_shot.png'; ?>">
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div
                            style="-webkit-box-sizing: border-box; -moz-box-sizing: border-box;  box-sizing: border-box;">
                            <h3><?php _e('To go further', 'itrr_lang'); ?></h3>

                            <div class="itrr_home_col_6">
                                <h4><?php _e('Shortcodes', 'itrr_lang'); ?></h4>
                                <?php _e('Place "[intrigger indget="XX"]" in any area that accepts shortcodes to display an indget anywhere you like.
                        Shortcodes are also available for scenarios, for example: "intrigger scenario="XX"]" ', 'itrr_lang');?>
                            </div>
                            <div class="itrr_home_col_6">
                                <h4><?php _e('Need help?', 'itrr_lang'); ?></h4>
                                <?php _e('We do all we can to provide every InTrigger user with the best support possible. If you encounter a problem or
                        have a question, please', 'itrr_lang');?>
                                <a href="<?php echo add_query_arg(array('page' => 'itrr_page_support'), admin_url('admin.php'));?>" class=""><?php _e('contact us', 'itrr_lang'); ?></a>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>
                    <div class="itrr_home_row itrr_home_create" style="text-align: center;">
                        <h1><?php _e('Create your first scenario!', 'itrr_lang'); ?></h1>
                        <?php
                        ITRR_Scenario_Admin::render_scenario_create();
                        ?>
                        <a href="<?php echo add_query_arg(array('post_type' => 'itrr_scenario'), admin_url('post-new.php')); ?>"
                           id="itrr_home_continue_btn"><?php _e('Continue', 'itrr_lang'); ?></a>
                    </div>

                <?php
                }
                ?> </div> <?php
        }

        /**
         * refresh status
         */
        public static function ajax_date_action(){
            check_ajax_referer('itrr_homepage_ajax_nonce' , 'security');
            $date_begin = isset($_POST['begin']) ? sanitize_text_field($_POST['begin']) : '';
            $date_end = isset($_POST['end']) ? sanitize_text_field($_POST['end']) : '' ;
            $begin = date('Y-m-d',strtotime($date_begin.'+1day'));
            $end = date('Y-m-d',strtotime($date_end.'+1day'));
            self::get_status($begin, $end);
            $date_range = array(
                'total_impression' => self::$impression,
                'total_conversion' => self::$conversion,
                'total_rate' => self::$conversion_rate,
                'triggers' => self::$itrr_scenarios
            );
            echo json_encode($date_range);

            die();
        }
    }
}

