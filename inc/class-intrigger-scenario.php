<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

/**
 * Intrigger Indget class
 * @package ITRR_Indget
 */

if (!class_exists('ITRR_Scenario')) {

    class ITRR_Scenario {

        var $_post;
        var $title;
        var $indget;
        var $rules;

        const cst_post_type = 'itrr_scenario';
        const cst_setting_indget = 'itrr_scenario_setting_indget';
        const cst_setting_rule = 'itrr_scenario_setting_rule';
        const cst_stat_impressions = 'itrr_scenario_stat_impression';
        const cst_stat_clicks = 'itrr_scenario_stat_click';

        function __construct($scenario_id = '') {
            if (!empty($scenario_id)) {
                $this->_post = get_post($scenario_id);
                $this->title = $this->_post->post_title;
                $this->indget = get_post_meta($this->_post->ID, ITRR_Scenario::cst_setting_indget, true);
                $this->rules = get_post_meta($this->_post->ID, ITRR_Scenario::cst_setting_rule, true);
            }
        }

        public static function getDefaultRule() {
            $result = array (
                "where" => array (
                    "home" => "yes",
                    "page" => "yes",
                    "page_opt" => "all_pages",
                    "selected_pages" => array(),
                    "post" => "yes",
                    "specific_urls" => array (
                    ),
                    "specific_urls_excluded" => array(
                    ),
                ),
                "when" => array (
                    "opt" => "always",
                    "visited_num" => "3",
                    "spent_min" => "1",
                ),
                "who" => array(
                    "opt" => "all",
                    "user_roles" => array(),
                ),
                "device" => array(
                    "desktop" => "yes",
                    "tablets" => "yes",
                    "mobile" => "yes",
                ),
                "retargeting" => array (
                    "session" => "no",
                    "session_num" => "3",
                    "conversion" => "yes",
                    "conversion_opt" => "session",
                    "another_apply" => "no",
                    "another_apply_conversion" => "yes",
                ),
            );
            return $result;
        }

        public static function getDefaultDisplay() {
            $result = array (
                "setting" => array(
                    "percent" => 20,
                    "character" => 400,
                    "scroll" => 30,
                ),
                "id" => "",
                "type" => "continue_reading", // inline & float
                "continue_reading" => array(
                    "position" => "percent",// character
                    "display" => "",
                ),
                "inline" => array(
                    "position" => "middle",// end
                    "display" => "",
                ),
                "float_bar" => array(
                    "position" => "top",// bottom
                    "display" => "all_time",// scroll
                ),
            );
            return $result;
        }
        /**
         * Register custom post type for scenarios.
         */
        public static function register_trigger_post_type() {
            $labels = array(
                'name'               => __( 'Scenarios', 'itrr_lang' ),
                'singular_name'      => __( 'Scenario', 'itrr_lang' ),
                'add_new'            => __( 'Add New Scenario', 'itrr_lang' ),
                'add_new_item'       => __( 'Add New Scenario', 'itrr_lang' ),
                'edit_item'          => __( 'Edit Scenario', 'itrr_lang' ),
                'new_item'           => __( 'New Scenario', 'itrr_lang' ),
                'all_items'          => __( 'Scenarios', 'itrr_lang' ),
                'view_item'          => __( 'View Scenario', 'itrr_lang' ),
                'search_items'       => __( 'Search Scenarios', 'itrr_lang' ),
                'not_found'          => __( 'No scenarios found', 'itrr_lang' ),
                'not_found_in_trash' => __( 'No scenarios found in Trash', 'itrr_lang' ),
                'parent_item_colon'  => __( '', 'itrr_lang' ),
                'menu_name'          => __( 'InTrigger', 'itrr_lang' )
            );

            $args = array(
                'labels'             => $labels,
                'menu_icon'          => 'dashicons-info',
                'public'             => false,
                //'publicly_queryable' => false,
                'show_ui'            => true,
                'show_in_menu'       => false,
                'query_var'          => false,
                'rewrite'            => array('slug' => ITRR_Scenario::cst_post_type),
                'capability_type'    => 'post',
                'has_archive'        => false,
                'hierarchical'       => false,
                'menu_position'      => null,
                'supports'           => array('title')

            );

            register_post_type(ITRR_Scenario::cst_post_type, $args );
        }

    }
}