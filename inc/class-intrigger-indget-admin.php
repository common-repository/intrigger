<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

/**
 * Intrigger Indget Admin class
 * @package ITRR_Indget_Admin
 */

if (!class_exists('ITRR_Indget_Admin')) {

    class ITRR_Indget_Admin {

        private function __construct() {

            add_action('add_meta_boxes', array(&$this, 'add_indget_meta_boxes'));
            add_action('post_edit_form_tag' , array(&$this, 'post_edit_form_tag'));
            add_action('save_post', array(&$this, 'save_indget_settings'), 10, 2);
            add_filter('manage_edit-itrr_indget_columns', array($this, 'edit_columns') );
            add_action('manage_itrr_indget_posts_custom_column', array(&$this, 'custom_columns'), 2 );
            add_action('admin_print_scripts-post-new.php', array(&$this, 'enqueue_script'), 11 );
            add_action('admin_print_scripts-post.php', array(&$this, 'enqueue_script'), 11 );
            add_action('admin_head',  array(&$this, 'thumb_column_width'));
        }
        function thumb_column_width() {
            echo '<style type="text/css">';
            echo '#indget_thumbnail { width:450px; }';
            echo '</style>';
        }
        /**
         * Singleton for ITRR_Indget_Admin
         * @return ITRR_Indget_Admin
         */
        public static  function getInstance(){
            static $intrigger_indget_admin = null;
            if (null === $intrigger_indget_admin) {
                $intrigger_indget_admin = new ITRR_Indget_Admin();
            }
            return $intrigger_indget_admin;
        }

        /**
         * Add metaboxes for Indget.
         */
        function add_indget_meta_boxes() {
            add_meta_box('indget-type', __('Indget Type', 'itrr_lang'), array(&$this, 'render_indget_form_fields_type'), ITRR_Indget::cst_post_type, 'normal', 'high');
            add_meta_box('indget-setting', __('Indget Setting', 'itrr_lang'), array(&$this, 'render_indget_form_fields_setting'), ITRR_Indget::cst_post_type, 'normal', 'high');
        }

        /**
         * Display metabox for Indget Type.
         * @param string $post
         * @param array $action
         */
        function render_indget_form_fields_type($post = '', $action = array()) {
            wp_nonce_field('intrigger_indget_meta_box_type', 'intrigger_indget_meta_box_type_nonce');

            $type = get_post_meta($post->ID, ITRR_Indget::cst_type, true);
            if (!isset($type) || $type == '') {
                $type = "continue_reading";
            }
            $subType = get_post_meta($post->ID, ITRR_Indget::cst_subtype, true);
            if (!isset($subType) || $subType == '') {
                $subType = "collect_email";
            }
            ?>
            <div class="indget_wrap">
                <div ng-controller="itrrIndgetAdminTypeCtrl">
                    <input type="hidden" name="intrigger_indget_type" id="intrigger_indget_type" ng-model="indget_type" value="<?php echo $type; ?>">
                    <input type="hidden" name="intrigger_indget_subtype" id="intrigger_indget_subtype" ng-model="indget_subtype" value="<?php echo $subType; ?>">
                    <div class="indget_type_area">
                        <h3 class="itrr_help"><?php _e('What is indget format?', 'itrr_lang'); ?></h3>
                        <div class="indget_ele indget_type_ele" ng-repeat="indget_type in indget_types" id="{{indget_type.id}}" ng-class="indget_type.id=='<?php echo $type; ?>' ? 'selected' : ''" ng-click="onChangeIndgetType(indget_type.id)">
                            <div class="indget_ele_name" >{{indget_type.name}}</div>
                            <div class="indget_ele_preview" id="{{indget_type.id}}_img" ng-class="indget_type.id=='<?php echo $type; ?>' ? 'selected' : ''"></div>
                            <div class="indget_ele_desc">{{indget_type.description}}</div>
                        </div>
                    </div>
                    <div class="clearfix"></div><br>
                    <div class="indget_subtype_area">
                        <h3 class="itrr_help" ><?php _e('What is the indget purpose?', 'itrr_lang'); ?></h3>
                        <div class="indget_ele indget_subtype_ele" ng-repeat="indget_subtype in indget_subtypes" id="{{indget_subtype.id}}" ng-class="(indget_subtype.id=='<?php echo $subType; ?>') ? 'selected' : ''" ng-click="onChangeIndgetSubType(indget_subtype.id)" >
                            <div class="indget_ele_name">{{indget_subtype.name}}</div>
                            <div class="indget_subtype_ele_preview" id="{{indget_subtype.id}}_img" ng-class="(indget_subtype.id=='<?php echo $subType; ?>') ? 'selected' : ''"></div>
                            <div class="indget_ele_desc">{{indget_subtype.description}}</div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        <?php
        }

        /**
         * Display metabox for Indget Setting.
         * @param string $post
         * @param array $action
         */
        function render_indget_form_fields_setting($post = '', $action = array()) {
            global $intrigger;

            $indget_type_object = ITRR_Indget_Type::getInstance();
            $indget_types = $indget_type_object->main_types;
            $indget_subtypes = $indget_type_object->sub_types;

            // Add a nonce field so we can check for it later.
            wp_nonce_field('intrigger_indget_meta_box_setting', 'intrigger_indget_meta_box_setting_nonce');
            ?>
            <div class="indget_wrap">
                <div ng-controller="itrrIndgetAdminCtrl">
                    <?php
                    foreach ($indget_types as $indget_type) {
                        foreach ($indget_subtypes as $indget_subtype) {
                            ?>
                            <div id="ind-<?php echo $indget_type['id'] . '-' . $indget_subtype['id']; ?>" class="ind-type-area hide">
                                <?php
                                ITRR_Indget_Type::generate_admin_setting_fields($indget_type['id'], $indget_subtype['id'], $post->ID);
                                ?>
                            </div>
                        <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <!--canvas-->
            <input type="hidden" id="indget_preview_img_base" name="indget_thumbnail" value="" />
            <script>
                jQuery(document).ready(function($){$('#publish').removeClass('button button-primary button-large').addClass('itrr_button');});
            </script>
        <?php
        }

        /**
         * Save setting data of indget.
         * @param $post_id
         */
        function save_indget_settings($post_id) {

            // Check if our nonce is set.
            if (!isset($_POST['intrigger_indget_meta_box_type_nonce'])) {
                return;
            }

            // Verify that the nonce is valid.
            if (!wp_verify_nonce($_POST['intrigger_indget_meta_box_type_nonce'], 'intrigger_indget_meta_box_type')) {
                return;
            }

            // Check if our nonce is set.
            if (!isset($_POST['intrigger_indget_meta_box_setting_nonce'])) {
                return;
            }

            // Verify that the nonce is valid.
            if (!wp_verify_nonce($_POST['intrigger_indget_meta_box_setting_nonce'], 'intrigger_indget_meta_box_setting')) {
                return;
            }

            // If this is an autosave, our form has not been submitted, so we don't want to do anything.
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return;
            }

            // check user role
            if( !current_user_can( 'manage_options' ) )
                wp_die('Not allowed');

            $main_type  = $_POST['intrigger_indget_type'];
            $sub_type   = $_POST['intrigger_indget_subtype'];
            $indget_settings_temp = $_POST['indget_setting'];// There are continue , inline and actionBar together
            $indget_settings = array();
            $indget_settings[$main_type] = $indget_settings_temp[$main_type];
            $indget_thumbnail = $_POST['indget_thumbnail'];

            update_post_meta($post_id, ITRR_Indget::cst_type, $main_type);
            update_post_meta($post_id, ITRR_Indget::cst_subtype, $sub_type);
            update_post_meta($post_id, ITRR_Indget::cst_setting_data, $indget_settings);
            update_post_meta($post_id, ITRR_Indget::cst_thumb_data, $indget_thumbnail);

        }

        /**
         * Add css & js in indget setting page.
         */
        function enqueue_script() {
            global $post_type;
            if ($post_type == ITRR_Indget::cst_post_type) {
                $indget_type_object = ITRR_Indget_Type::getInstance();
                wp_enqueue_script('itrr-indget-angular-js', ITRR_Manager::$plugin_url . '/asset/js/angular.min.js');
                wp_enqueue_script('itrr-indget-canvas-js', ITRR_Manager::$plugin_url . '/asset/js/dist/html2canvas.js');
                wp_enqueue_script('itrr-indget-admin-js', ITRR_Manager::$plugin_url . '/asset/js/angular.indget-admin.js', array(), filemtime(ITRR_Manager::$plugin_dir . '/asset/js/angular.indget-admin.js'));
                wp_enqueue_script('itrr-colorpicker-js', ITRR_Manager::$plugin_url . '/asset/js/spectrum.js');

                wp_enqueue_style('itrr-colorpicker-css', ITRR_Manager::$plugin_url . '/asset/css/spectrum.css');
                wp_enqueue_style('itrr-indget-admin-css', ITRR_Manager::$plugin_url . '/asset/css/admin-indget.css', array(), filemtime(ITRR_Manager::$plugin_dir . '/asset/css/admin-indget.css'));
                wp_enqueue_style('itrr-indget-fontawesome-css', ITRR_Manager::$plugin_url . '/asset/css/fontawesome/css/font-awesome.min.css');

                wp_localize_script('itrr-indget-admin-js', 'ITRR_PLUGIN_URL', ITRR_Manager::$plugin_url);
                wp_localize_script('itrr-indget-admin-js', 'ITRR_INDGET_TYPES', json_encode($indget_type_object->main_types));
                wp_localize_script('itrr-indget-admin-js', 'ITRR_INDGET_SUBTYPES', json_encode($indget_type_object->sub_types));
            }
        }

        /**
         * Add AngularJs app tag for use of AngularJS.
         * @param $post
         */
        function post_edit_form_tag($post) {
            global $post_type;
            if ($post_type == ITRR_Indget::cst_post_type) {
                echo ' ng-app="itrrIndgetApp"';
            }
        }

        /**
         * Customize the column of Indget list table.
         * @param $existing_columns
         * @return mixed|void
         */
        function edit_columns($existing_columns) {

            $date = $existing_columns['date'];
            unset($existing_columns['date']);

            $existing_columns['indget_type'] = __('Type', 'itrr_lang');
            $existing_columns['indget_status'] = __('Stats', 'itrr_lang');
            $existing_columns['indget_thumbnail'] = __('Thumbnail', 'itrr_lang');
            $existing_columns['date'] = $date;

            return apply_filters('intrigger_indget_columns', $existing_columns);
        }

        function custom_columns($column) {
            global $post;

            $indget_type = get_post_meta($post->ID, ITRR_Indget::cst_type, true);
            $indget_thumb = get_post_meta($post->ID, ITRR_Indget::cst_thumb_data, true);
            $indget_type_name = ITRR_Indget_Type::getInstance()->getTypeNameFromID($indget_type);

            $indget_stats = ITRR_Stats::get_stats_trigger(($post->ID)); // array
            $indget_clicks = $indget_stats['conversion'];
            $indget_impressions = $indget_stats['impression'];
            $indget_rate = $indget_stats['rate'];

            $indget_rate = '<a href="#" class="int_stats_rate" style="font-weight: bold; font-size: 15px; color: #238E67;" title="Conversion rate">'.$indget_rate.'</a>';
            $indget_clicks = '<a href="#" class="int_stats_clicks" style="font-weight: bold; font-size: 13px; color: #356BE8;" title="Number of conversions">'.$indget_clicks.'</a>';
            $indget_impressions = '<a href="#" class="int_stats_impressions" style="font-weight: normal; font-size: 13px; color: #DE2D2D;" title="Number of impressions">'.$indget_impressions.'</a>';
            switch ($column) {
                case 'indget_type':
                    echo $indget_type_name;
                    break;

                case 'indget_status':
                    echo $indget_rate. '<br>';
                    echo $indget_clicks . ' - ' . $indget_impressions;
                    break;

                case 'indget_thumbnail':
                    if ($indget_thumb == '') {
                        //echo 'No Preview';
                        echo '<img src="' . ITRR_Manager::$plugin_url . '/templates/custom.png" style="width:400px;">';
                    }
                    else {
                        echo '<img src="' . $indget_thumb.'" style="width:400px;">';
                    }
                    break;

                default:
                    break;

            }
        }

        public static function getAllActiveIndget() {
            $args = array(
                'posts_per_page'   => -1,
                'orderby'          => 'date',
                'order'            => 'DESC',
                'post_type'        => ITRR_Indget::cst_post_type,
                'post_status'      => 'publish',
            );
            $posts_array = get_posts( $args );
            $results = array();
            foreach ($posts_array as $post) {
                $indget_id = $post->ID;
                $indget_title = $post->post_title;
                $indget_type = get_post_meta($indget_id, ITRR_Indget::cst_type, true);
                $indget_subtype = get_post_meta($indget_id, ITRR_Indget::cst_subtype, true);
                $indget_setting = get_post_meta($indget_id, ITRR_Indget::cst_setting_data, true);
                $results[$indget_id] = array(
                    'type' => $indget_type,
                    'subtype' => $indget_subtype,
                    'setting' => $indget_setting,
                    'name' => $indget_title,
                );
            }

            return $results;
        }

        public static function getAllActivePages() {
            $args = array(
                'posts_per_page'   => -1,
                'orderby'          => 'date',
                'order'            => 'DESC',
                'post_type'        => 'page',
                'post_status'      => 'publish',
            );
            $posts_array = get_posts( $args );
            $results = array();
            foreach ($posts_array as $post) {
                $results[] = array(
                    'id' => $post->ID,
                    'title' => $post->post_title,
                );
            }

            return $results;
        }
    }
}

