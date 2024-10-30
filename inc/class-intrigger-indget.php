<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

/**
 * Intrigger Indget class
 * @package ITRR_Indget
 */

if (!class_exists('ITRR_Indget')) {

    class ITRR_Indget {

        var $_post;
        var $title;
        var $type;
        var $subType;
        var $settingData;

        const cst_post_type = 'itrr_indget';
        const cst_type = 'itrr_indget_type';
        const cst_subtype = 'itrr_indget_subtype';
        const cst_setting_data = 'itrr_indget_setting_data';
        const cst_thumb_data = 'itrr_indget_thumbnail';
        const cst_stat_impressions = 'itrr_indget_stat_impression';
        const cst_stat_clicks = 'itrr_indget_stat_click';

        function __construct($indget_id = '') {
            if (!empty($indget_id)) {
                $this->_post = get_post($indget_id);
                $this->title = $this->_post->post_title;
                $this->type = get_post_meta($this->_post->ID, ITRR_Indget::cst_type, true);
                $this->subType = get_post_meta($this->_post->ID, ITRR_Indget::cst_subtype, true);
                $this->settingData = get_post_meta($this->_post->ID, ITRR_Indget::cst_setting_data, true);
            }
        }
        /**
         * Register custom post type for indgets.
         */
        public static function register_indget_post_type() {
            $labels = array(
                'name'               => __('Indgets', 'itrr_lang'),
                'singular_name'      => __('Indget', 'itrr_lang'),
                'add_new'            => __('Add New Indget', 'itrr_lang'),
                'add_new_item'       => __('Add New Indget', 'itrr_lang'),
                'edit_item'          => __('Edit Indget', 'itrr_lang'),
                'new_item'           => __('New Indget', 'itrr_lang'),
                'all_items'          => __('Indgets', 'itrr_lang'),
                'view_item'          => __('View Indget', 'itrr_lang'),
                'search_items'       => __('Search Indgets', 'itrr_lang'),
                'not_found'          => __('No indgets found', 'itrr_lang'),
                'not_found_in_trash' => __('No indgets found in Trash', 'itrr_lang'),
                'parent_item_colon'  => __('', 'icegram'),
                'menu_name'          => __('Indget', 'itrr_lang')
            );

            $args = array(
                'labels'             => $labels,
                'menu_icon'          => 'dashicons-info',
                'public'             => false,
                'publicly_queryable' => false,
                'show_ui'            => true,
                'show_in_menu'       => false,
                'query_var'          => true,
                'rewrite'            => array('slug' => ITRR_Indget::cst_post_type),
                'capability_type'    => 'post',
                'has_archive'        => false,
                'hierarchical'       => false,
                'menu_position'      => null,
                'supports'           => array('title')
            );

            register_post_type(ITRR_Indget::cst_post_type, $args);
        }
        /**
         * Create default custom posts - Indget
         */
        public static function create_default_indgets(){
            global $wpdb;
            $default_names = array(
                array("main_type" => "continue_reading","sub_type" => "collect_email", "name" => "Continue - Email form"),
                array("main_type" => "continue_reading","sub_type" => "drive_traffic", "name" => "Continue - Drive traffic"),
                array("main_type" => "inline",          "sub_type" => "collect_email", "name" => "Inline - Email form"),
                array("main_type" => "inline",          "sub_type" => "drive_traffic", "name" => "Inline - Drive traffic"),
                array("main_type" => "float_bar",       "sub_type" => "collect_email", "name" => "Floating bar - Email form"),
                array("main_type" => "float_bar",       "sub_type" => "drive_traffic", "name" => "Floating bar - Drive traffic"),
            );
            // insert the posts
            foreach($default_names as $key=>$default_itrr) {
                $posttitle = $default_itrr['name'];
                $postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title = '" . $posttitle . "'" );
                if($postid != '') continue;
                $post_id = wp_insert_post(array(
                    'post_type' => ITRR_Indget::cst_post_type,
                    'post_title' => $default_itrr['name'],
                    'post_content' => '',
                    'post_status' => 'publish',
                    'comment_status' => 'closed',
                    'ping_status' => 'closed',
                ));

                $main_type  = $default_itrr['main_type'];
                $sub_type   = $default_itrr['sub_type'];
                $indget_settings = array();
                $indget_settings[$main_type][$sub_type] = ITRR_Indget_Type::getInstance()->indget_settings[$main_type.'-'.$sub_type];
                $indget_thumbnail = plugins_url() .'/intrigger/asset/img/default_'.$main_type.'-'.$sub_type.'.png';
                if ($post_id) {
                    // insert post meta
                    add_post_meta($post_id, ITRR_Indget::cst_type, $main_type);
                    add_post_meta($post_id, ITRR_Indget::cst_subtype, $sub_type);
                    add_post_meta($post_id, ITRR_Indget::cst_setting_data, $indget_settings);
                    add_post_meta($post_id, ITRR_Indget::cst_thumb_data, $indget_thumbnail);
                }
            }
        }

    }
}