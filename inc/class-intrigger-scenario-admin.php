<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

/**
 * Intrigger Scenario Admin class
 * @package ITRR_Scenario_Admin
 */

if (!class_exists('ITRR_Scenario_Admin')) {

    class ITRR_Scenario_Admin
    {

        /**
         * current indget
         */
        private $scenario_indget_type;

        private function __construct()
        {
            add_action('add_meta_boxes', array(&$this, 'add_scenario_meta_boxes'));
            add_action('post_edit_form_tag', array(&$this, 'post_edit_form_tag'));
            add_action('save_post', array(&$this, 'save_scenario_settings'), 10, 2);
            add_filter('manage_edit-itrr_scenario_columns', array($this, 'edit_columns'));
            add_action('manage_itrr_scenario_posts_custom_column', array(&$this, 'custom_columns'), 2);
            add_action('admin_print_scripts-post-new.php', array(&$this, 'enqueue_script'), 11);
            add_action('admin_print_scripts-post.php', array(&$this, 'enqueue_script'), 11);
            add_action('admin_print_scripts-edit.php', array(&$this, 'enqueue_script_edit'), 11);
            add_action('wp_ajax_get_preview_indget', array('ITRR_Scenario_Admin', 'get_preview_indget'));
            // for insert / edit link dialogue
            add_action('wp_ajax_itrr-link-ajax', array($this, "ajax_search_posts"));
            add_action('wp_ajax_itrr_search_page_link', array($this, 'search_all_pages'));
        }

        /**
         * Singleton for ITRR_Indget_Admin
         * @return ITRR_Indget_Admin
         */
        public static function getInstance()
        {
            static $intrigger_scenario_admin = null;
            if (null === $intrigger_scenario_admin) {
                $intrigger_scenario_admin = new ITRR_Scenario_Admin();
            }
            return $intrigger_scenario_admin;
        }

        /**
         * Add metabox for scenario setting.
         */
        function add_scenario_meta_boxes()
        {
            add_meta_box('scenario-type', __('1. Scenario Type', 'itrr_lang'), array(&$this, 'render_scenario_meta_type'), ITRR_Scenario::cst_post_type, 'normal', 'high');
            add_meta_box('scenario-indget', __('2. Indget to display', 'itrr_lang'), array(&$this, 'render_scenario_meta_indget'), ITRR_Scenario::cst_post_type, 'normal', 'high');
            add_meta_box('scenario-rules', __('3. Scenario rules', 'itrr_lang'), array(&$this, 'render_scenario_meta_rules'), ITRR_Scenario::cst_post_type, 'normal', 'high');
        }

        function render_scenario_meta_type($post = '', $action = array())
        {
            $scenario_indget = get_post_meta($post->ID, ITRR_Scenario::cst_setting_indget, true);
            $scenario_indget_type = null;
            if (!empty($scenario_indget) && is_array($scenario_indget)) {
                $scenario_indget_id = intval($scenario_indget['id']);
                if ($scenario_indget_id > 0) {
                    $scenario_indget_type = get_post_meta($scenario_indget_id, ITRR_Indget::cst_type, true);
                }
            }
            if (($scenario_indget_type == null) && ($scenario_indget_type == '')) {
                $scenario_indget_type = 'continue_reading';
            }
            if (isset($_GET['sel_type'])) {
                $scenario_indget_type = $_GET['sel_type'];
            }
            $this->scenario_indget_type = $scenario_indget_type;
            ?>
            <div class="scenario_wrap">
                <div ng-controller="itrrScenarioTypeCtrl">
                    <input type="hidden" name="intrigger_scenario_type" id="intrigger_scenario_type" ng-model="scenario_type" value="<?php echo $scenario_indget_type; ?>">

                    <div class="scenario_form_section">
                        <div class="scenario_type_area">
                            <div class="scenario_ele scenario_type_ele" ng-repeat="indget_type in indget_types" id="{{indget_type.id}}" ng-class="indget_type.id=='<?php echo $scenario_indget_type; ?>' ? 'selected' : ''"  ng-click="onChangeType(indget_type.id)">
                                <div class="scenario_ele_name">{{indget_type.name}}</div>
                                <div class="scenario_ele_preview" id="{{indget_type.id}}_img" ng-class="indget_type.id=='<?php echo $scenario_indget_type; ?>' ? 'selected' : ''"></div>
                                <div class="scenario_ele_desc">{{indget_type.description}}</div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        <?php
        }

        // on home page
        public static function render_scenario_create()
        {
            ?>
            <div ng-app="itrrScenarioApp">
                <div class="scenario_wrap">
                    <div ng-controller="itrrScenarioTypeCtrl">
                        <input type="hidden" name="intrigger_scenario_type" id="intrigger_scenario_type"
                               ng-model="scenario_type" value="continue_reading">

                        <div class="scenario_form_section">
                            <div class="scenario_type_area">
                                <div class="scenario_ele scenario_type_ele" ng-repeat="indget_type in indget_types"
                                     id="{{indget_type.id}}"
                                     ng-class="indget_type.id=='continue_reading' ? 'selected' : ''"
                                     ng-click="onChangeType(indget_type.id)">
                                    <div class="scenario_ele_name">{{indget_type.name}}</div>
                                    <div class="scenario_ele_preview" id="{{indget_type.id}}_img"
                                         ng-class="indget_type.id=='continue_reading' ? 'selected' : ''"></div>
                                    <div class="scenario_ele_desc">{{indget_type.description}}</div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }

        function render_scenario_meta_indget($post = '', $action = array())
        {
            $scenario_indget = get_post_meta($post->ID, ITRR_Scenario::cst_setting_indget, true);
            if (empty($scenario_indget) || !is_array($scenario_indget)) {
                $scenario_indget = ITRR_Scenario::getDefaultDisplay();
            }

            $scenario_indget_id = $scenario_indget['id'];
            $scenario_continue_position = $scenario_indget['continue_reading']['position'];
            $scenario_inline_position = $scenario_indget['inline']['position'];
            $scenario_float_position = $scenario_indget['float_bar']['position'];
            $scenario_float_display = $scenario_indget['float_bar']['display'];
            $scenario_indget_setting = $scenario_indget['setting'];

            wp_nonce_field('intrigger_scenario_indget', 'intrigger_scenario_indget_nonce');
            ?>
            <div class="scenario_wrap">
                <div ng-controller="itrrScenarioIndgetCtrl">
                    <div class="scenario_form_section">
                        <div class="scenario_form_ele">
                            <div class="scenario_title_area">
                                <b><?php _e('Indget', 'itrr_lang'); ?></b>
                            </div>
                            <div class="scenario_field_area">
                                <input type="hidden" id="scenario_indget_id" value="<?php echo $scenario_indget_id; ?>">
                                <select class="fullwidth" name="scenario_indget" ng-model="indget_id"
                                        ng-options="indget.name for indget in indgets track by indget.id"
                                        ng-change="onChangeIndget(indget_id.id)" ng-required="true">
                                    <option value=""><?php _e('Choose Indget', 'itrr_lang'); ?></option>
                                </select>
                                <br>
                                <a ng-href="<?php echo admin_url('post.php'); ?>?post={{indget_id.id}}&action=edit"
                                   target="_blank" ng-if="indget_id"><i
                                        class="fa fa-angle-right"></i> <?php _e('Edit this Indget', 'itrr_lang'); ?></a>
                            </div>
                        </div>
                        <div class="scenario_form_ele" ng-if="indget_id">
                            <div class="scenario_title_area">
                                <b><?php _e('Preview', 'itrr_lang'); ?></b>
                            </div>
                            <div class="scenario_field_area">
                                <div id="scenario_indget_preview">
                                    <?php
                                    if ($scenario_indget_id != "")
                                        echo self::get_preview_indget_theme($scenario_indget_id);
                                    ?>
                                </div>
                            </div>
                            <style>
                                .preview_hidden {
                                    display: none;
                                }

                                #float_bar_wrap {
                                    display: block;
                                }
                            </style>
                        </div>
                        <div class="clearfix"></div>
                        <!-- continue reading -->
                        <div class="scenario_form_ele" ng-if="scenario_type=='continue_reading'">
                            <div class="scenario_title_area">
                                <b><?php _e('Settings', 'itrr_lang'); ?></b>
                            </div>
                            <div class="scenario_field_area">
                                <div
                                    style="display: inline-block;vertical-align: top;"><?php _e('Hide the text and display the "Continue" indget after ', 'itrr_lang'); ?></div>
                                <div style="display: inline-block;margin-left: 10px;">
                                    <input type="radio" name="scenario_indget_position[continue_reading]"
                                           value="percent" style="display: inline-block;" <?php
                                    if ($scenario_continue_position == 'percent') {
                                        echo 'checked';
                                    }
                                    ?>>
                                    <input type="number" name="scenario_indget_setting[percent]" min="20" max="100"
                                           value="<?php echo $scenario_indget_setting['percent']; ?>" required>
                                    &nbsp;<?php _e('% of content', 'itrr_lang'); ?><br>
                                    <input type="radio" name="scenario_indget_position[continue_reading]"
                                           value="character" style="display: inline-block;" <?php
                                    if ($scenario_continue_position == 'character') {
                                        echo 'checked';
                                    }
                                    ?>>
                                    <input type="number" name="scenario_indget_setting[character]" min="50"
                                           maxlength="5" value="<?php echo $scenario_indget_setting['character']; ?>"
                                           required>
                                    &nbsp;<?php _e('characters', 'itrr_lang'); ?>
                                </div>
                                <br>
                            </div>
                        </div>
                        <!-- Inline -->
                        <div class="scenario_form_ele" ng-if="scenario_type=='inline'">
                            <div class="scenario_title_area">
                                <b><?php _e('Settings', 'itrr_lang'); ?></b>
                            </div>
                            <div class="scenario_field_area">
                                <?php _e('Display the indget at the ', 'itrr_lang'); ?>
                                <select name="scenario_indget_position[inline]" style="width: 150px;">
                                    <option
                                        value="middle" <?php selected($scenario_inline_position, 'middle'); ?>><?php _e('Middle', 'itrr_lang'); ?></option>
                                    <option
                                        value="end" <?php selected($scenario_inline_position, 'end'); ?>><?php _e('End', 'itrr_lang'); ?></option>
                                </select>
                                <br>
                            </div>
                        </div>
                        <!-- Float -->
                        <div class="scenario_form_ele" ng-if="scenario_type=='float_bar'" style="margin-top: 15px;">
                            <div class="scenario_title_area">
                                <b><?php _e('Settings', 'itrr_lang'); ?></b>
                            </div>
                            <div class="scenario_field_area">
                                <div
                                    style="width:20%; display: inline-block;vertical-align: top;"><?php _e('Display the float bar at the', 'itrr_lang'); ?></div>
                                <div style="width:40%; display: inline-block;margin-left: 10px;">
                                    <div>
                                        <label>
                                            <input type="radio" name="scenario_indget_position[float_bar]" value="top"
                                                   style="display: inline-block;" <?php
                                            if ($scenario_float_position == 'top') {
                                                echo 'checked';
                                            }
                                            ?>>&nbsp;<?php _e('top of the screen', 'itrr_lang'); ?>
                                        </label>
                                    </div>
                                    <div style="margin-top: 10px;">
                                        <label style="padding-top: 20px;">
                                            <input type="radio" name="scenario_indget_position[float_bar]"
                                                   value="bottom" style="display: inline-block;" <?php
                                            if ($scenario_float_position == 'bottom') {
                                                echo 'checked';
                                            }
                                            ?>>&nbsp;<?php _e('bottom of the screen', 'itrr_lang'); ?>
                                        </label>
                                    </div>
                                </div>
                                <br>
                            </div>
                            <div class="scenario_title_area">
                            </div>
                            <div class="scenario_field_area">
                                <div
                                    style="width:20%; display: inline-block;vertical-align: top;"><?php _e('Display the float bar', 'itrr_lang'); ?></div>
                                <div style="width:40%; display: inline-block;margin-left: 10px;">
                                    <div>
                                        <label>
                                            <input type="radio" name="scenario_indget_display[float_bar]"
                                                   value="all_time" style="display: inline-block;" <?php
                                            if ($scenario_float_display == 'all_time') {
                                                echo 'checked';
                                            }
                                            ?>>&nbsp;<?php _e('all the time', 'itrr_lang'); ?>
                                        </label>
                                    </div>
                                    <div style="margin-top: 5px;">
                                        <label style="padding-top: 20px;">
                                            <input type="radio" name="scenario_indget_display[float_bar]" value="scroll"
                                                   style="display: inline-block;" <?php
                                            if ($scenario_float_display == 'scroll') {
                                                echo 'checked';
                                            }
                                            ?>>&nbsp;<?php _e('after', 'itrr_lang'); ?>
                                        </label>
                                        <input type="number" name="scenario_indget_setting[scroll]" min="20" max="80"
                                               value="<?php echo $scenario_indget_setting['scroll']; ?>" required>
                                        &nbsp;<?php _e('% of scroll', 'itrr_lang'); ?>
                                    </div>
                                </div>
                                <br>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        <?php
        }

        function render_scenario_meta_rules($post = '', $action = array())
        {
            $pages = ITRR_Indget_Admin::getAllActivePages();
            $total_user_roles = array_reverse(get_editable_roles());
            $scenario_rules = get_post_meta($post->ID, ITRR_Scenario::cst_setting_rule, true);
            $is_new_scenario = false;
            if (empty($scenario_rules) || !is_array($scenario_rules)) {
                $scenario_rules = ITRR_Scenario::getDefaultRule();
                $is_new_scenario = true;
            }
            wp_nonce_field('intrigger_scenario_rule', 'intrigger_scenario_rule_nonce');
            ?>
            <div class="scenario_wrap">
            <div ng-controller="itrrScenarioRulesCtrl">
            <!-- Start : Where section -->
            <div class="scenario_form_section where_section odd">
            <!-- Start: Page Area -->
            <div class="scenario_form_ele">
            <div class="scenario_title_area">
                <b><?php _e('Where?', 'itrr_lang'); ?></b>
            </div>
            <!--          <div class="scenario_title_area">&nbsp;</div>-->
            <div id="itrr_tabs" ng-controller="TabsCtrl">
                <ul class="itrrScenarioTabView">
                    <li class="itrrScenariolist" ng-repeat="tab in tabs"
                        ng-class="{active:isActiveTab(tab.url)}"
                        ng-click="onClickTab(tab)">{{tab.title}}
                    </li>
                </ul>
                <div class="itrrScenarioTabMainView">
                    <div ng-include="currentTab"></div>
                </div>
            </div>
            <script type="text/ng-template" id="one.tpl.html">

                <div class="scenario_field_area">
                    <label>
                        <input type="checkbox" name="sc_rule[where][page]" ng-model="rule_where_page" value="yes"
                               ng-change="pageChanged" <?php
                        if (isset($scenario_rules['where']['page']) && ($scenario_rules['where']['page'] == 'yes')) {
                            echo 'ng-init="rule_where_page=true"';
                        }
                        ?>>&nbsp;<?php _e('Pages', 'itrr_lang'); ?>
                        <i id="itrr_scenario_page_tip" class="fa fa-question-circle itrr-tooltip"
                           style="margin-left: 10px;color: #999;font-size: larger; display: <?php echo $this->scenario_indget_type == 'float_bar' ? 'none' : 'inline-block'; ?>;"
                           title="<?php _e('This scenario will only apply on single pages, and not archive pages like Blog.', 'itrr_lang'); ?>"></i>
                    </label><br>

                    <div class="scenario_subs" ng-hide="!rule_where_page">
                        <div class="scenario_sub">
                            <label>
                                &nbsp;&nbsp;<input type="radio" name="sc_rule[where][page_opt]" value="all_pages"
                                                   ng-disabled="!rule_where_page"  <?php
                                if (isset($scenario_rules['where']['page_opt']) && ($scenario_rules['where']['page_opt'] == 'all_pages') && isset($scenario_rules['where']['page'])) {
                                    echo 'checked';
                                }
                                ?>> &nbsp;<?php _e('All pages', 'itrr_lang'); ?>
                            </label></div>
                        <div class="scenario_sub">
                            <label>
                                &nbsp;&nbsp;<input type="radio" name="sc_rule[where][page_opt]" value="selected"
                                                   ng-disabled="!rule_where_page"  <?php
                                if (isset($scenario_rules['where']['page_opt']) && ($scenario_rules['where']['page_opt'] == 'selected') && isset($scenario_rules['where']['page'])) {
                                    echo 'checked';
                                }
                                ?>> &nbsp;<?php _e('Selected pages', 'itrr_lang'); ?>
                            </label></div>
                        <div class="scenario_sub">
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <select name="sc_rule[where][selected_pages][]" data-placeholder="Please select page"
                                    chosen-select multiple tabindex="8" style="width: 80%;">
                                <?php
                                foreach ($pages as $page) {
                                    ?>
                                    <option value="<?php echo $page['id']; ?>" <?php
                                    if (isset($scenario_rules['where']['selected_pages']) && in_array($page['id'], $scenario_rules['where']['selected_pages'])) {
                                        echo 'selected="selected"';
                                    }
                                    ?>><?php echo $page['title']; ?></option>
                                <?php
                                }
                                ?>
                            </select></div>
                    </div>
                </div>
                </div>

                <!-- End: Page Area -->

                <!-- Start: Post Area -->
                <?php
                $args = array(
                    'orderby' => 'name',
                    'order' => 'ASC'
                );
                $cats = get_categories($args);
                $cat_model = '';
                foreach ($cats as $cat) {
                    $cat_model .= 'cat_' . $cat->cat_ID . ' || ';
                    if ($is_new_scenario) {
                        $scenario_rules['where']['post_cat'][$cat->cat_ID] = 'yes';
                    }
                }
                $cat_model = chop($cat_model, ' || ');
                ?>
                <div class="scenario_form_ele">
                    <div class="scenario_field_area">
                        <label>
                            <input type="checkbox" name="sc_rule[where][post]" class="itrr_post"
                                   ng-checked="<?php echo $cat_model; ?>" ng-model="rule_where_post" value="yes"
                                   ng-click="updatePost()" <?php
                            if (isset($scenario_rules['where']['post']) && ($scenario_rules['where']['post'] == 'yes')) {
                                echo 'ng-init="rule_where_post=true"';
                            }
                            ?>>&nbsp;<?php _e('Posts', 'itrr_lang'); ?>
                        </label><br>
                        <!-- categories -->
                        <div class="scenario_subs scenario_categories" ng-hide="!rule_where_post">
                            <?php
                            foreach ($cats as $cat) {
                                ?>
                                <div class="scenario_sub">
                                    <label>
                                        &nbsp;&nbsp;<input type="checkbox" class="itrr_cats"
                                                           name="sc_rule[where][post_cat][<?php echo $cat->cat_ID; ?>]"
                                                           ng-model="<?php echo 'cat_' . $cat->cat_ID; ?>"
                                                           value="yes" ng-disabled="!rule_where_post"
                                                           ng-click="updateSubPost()" <?php
                                        if (isset($scenario_rules['where']['post_cat'][$cat->cat_ID]) && ($scenario_rules['where']['post_cat'][$cat->cat_ID] == 'yes')) {
                                            echo 'ng-init="' . 'cat_' . $cat->cat_ID . '=true"';
                                        }
                                        ?>> &nbsp;<?php echo $cat->name; ?>
                                    </label></div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- Custom post types -->
                <div class="scenario_form_ele" ng-repeat="(cpt, terms) in cpts">
                    <div class="scenario_field_area">
                        <label>
                            <input type="checkbox" name="sc_rule[where][cpt][{{cpt}}]" class="cpt_{{cpt}}"
                                   value="yes" ng-click="updateCPT(cpt)" ng-model="terms.select">&nbsp;{{cpt}}
                        </label>

                        <div class="scenario_subs scenario_terms" ng-if="terms.select">
                            <div class="scenario_sub" ng-repeat="(key, term) in terms.data">
                                <label>
                                    &nbsp;&nbsp;<input type="checkbox"
                                                       name="sc_rule[where][cpt][{{cpt}}][{{term.name}}]"
                                                       class={{cpt}} value="yes" ng-click="updateTerm(cpt)"
                                                       ng-model="term.select">
                                    &nbsp;{{term.name}}<br>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End: Post Area -->

                <!-- Start: Specific URL Area -->
            </script>
            <div id="itrr_scenario_form_group">

                <div class="scenario_form_ele">
                    <div class="scenario_title_area">&nbsp;</div>
                    <div class="scenario_field_area specific_url_area">
                        <label>
                            <input type="checkbox" id="sc_rule_where_specific" name="sc_rule[where][specific]"
                                   ng-model="rule_where_specific" value="yes" <?php
                            if (isset($scenario_rules['where']['specific']) && ($scenario_rules['where']['specific'] == 'yes')) {
                                echo 'ng-init="rule_where_specific=true"';
                            }
                            ?> >&nbsp;<?php _e('Specific URLs (Regex)', 'itrr_lang'); ?>
                        </label>
                    </div>
                </div>

                <div class="scenario_form_ele specific_url_group">
                    <?php
                    if (isset($scenario_rules['where']['specific_urls']) && is_array($scenario_rules['where']['specific_urls'])) {
                        foreach ($scenario_rules['where']['specific_urls'] as $index => $specific_url) {
                            ?>
                            <div class="scenario_form_ele specific_url_ele"
                                 id="sc_rule_specific_url_<?php echo($index + 1); ?>">
                                <div class="scenario_title_area">&nbsp;</div>
                                <div class="scenario_field_area specific_url_area">
                                    &nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="sc_rule_where_first_url"
                                                                   name="sc_rule[where][specific_urls][]"
                                                                   class="specific_url"
                                                                   value="<?php echo $specific_url; ?>"
                                                                   placeholder="<?php _e('Example: /blog/.*', 'itrr_lang'); ?>">
                                    &nbsp;&nbsp;<span class="remove_specfic_url_btn"
                                                      onclick="removeSpecificURLElement(<?php echo($index + 1); ?>);"><i
                                            class="fa fa-trash-o fa-lg"></i></span>
                                </div>
                            </div>
                        <?php
                        }
                    }
                    ?>
                </div>
                <div class="scenario_form_ele specific_url_action"> <!-- ng-if="rule_where_specific"> -->
                    <div class="scenario_title_area">&nbsp;&nbsp;</div>
                    <input type="hidden" id="specific_url_account" value="<?php
                    if (isset($scenario_rules['where']['specific_urls']) && is_array($scenario_rules['where']['specific_urls'])) {
                        echo count($scenario_rules['where']['specific_urls']);
                    } else {
                        echo '0';
                    }
                    ?>">

                    <div class="scenario_field_area specific_url_area">
                        &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" ng-click="addSpecificURL();"><i
                                class="fa fa-angle-right"></i>&nbsp;<?php _e('Add new URL', 'itrr_lang'); ?> </a>
                    </div>
                </div>
                <!-- End: Specific URL Area -->
                <!-- Start: Specific url excluded -->
                <div class="scenario_form_ele">
                    <div class="scenario_title_area">&nbsp;</div>
                    <div class="scenario_field_area specific_url_area">
                        <label>
                            <input type="checkbox" id="sc_rule_where_specific_excluded" name="sc_rule[where][specific_excluded]"
                                   ng-model="rule_where_specific_excluded" value="yes" <?php
                            if (isset($scenario_rules['where']['specific_excluded']) && ($scenario_rules['where']['specific_excluded'] == 'yes')) {
                                echo 'ng-init="rule_where_specific_excluded=true"';
                            }
                            ?> >&nbsp;<?php _e('URLs excluded (Regex)', 'itrr_lang'); ?>
                        </label>
                    </div>
                </div>
                <div class="scenario_form_ele specific_url_group_excluded">
                    <?php
                    if (isset($scenario_rules['where']['specific_urls_excluded']) && is_array($scenario_rules['where']['specific_urls_excluded'])) {
                        foreach ($scenario_rules['where']['specific_urls_excluded'] as $index => $specific_url) {
                            ?>
                            <div class="scenario_form_ele specific_url_ele"
                                 id="sc_rule_specific_url_excluded_<?php echo($index + 1); ?>">
                                <div class="scenario_title_area">&nbsp;</div>
                                <div class="scenario_field_area specific_url_area">
                                    &nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="sc_rule_where_first_url_excluded"
                                                                   name="sc_rule[where][specific_urls_excluded][]"
                                                                   class="specific_url"
                                                                   value="<?php echo $specific_url; ?>"
                                                                   placeholder="<?php _e('Example: /blog/.*', 'itrr_lang'); ?>">
                                    &nbsp;&nbsp;<span class="remove_specfic_url_btn"
                                                      onclick="removeSpecificExcludedURLElement(<?php echo($index + 1); ?>);"><i
                                            class="fa fa-trash-o fa-lg"></i></span>
                                </div>
                            </div>
                        <?php
                        }
                    }
                    ?>
                </div>
                <div class="scenario_form_ele specific_url_action"> <!-- ng-if="rule_where_specific"> -->
                    <div class="scenario_title_area">&nbsp;&nbsp;</div>
                    <input type="hidden" id="specific_url_account_excluded" value="<?php
                    if (isset($scenario_rules['where']['specific_urls_excluded']) && is_array($scenario_rules['where']['specific_urls_excluded'])) {
                        echo count($scenario_rules['where']['specific_urls_excluded']);
                    } else {
                        echo '0';
                    }
                    ?>">

                    <div class="scenario_field_area specific_url_area">
                        &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" ng-click="addSpecificURLExcluded();"><i
                                class="fa fa-angle-right"></i>&nbsp;<?php _e('Add new URL', 'itrr_lang'); ?> </a>
                    </div>
                </div>
                    <!-- End: Specific url excluded -->
                <!-- Start: Description Area -->
                <div class="scenario_form_ele">
                    <div class="scenario_title_area">&nbsp;</div>
                    <div class="scenario_field_area">
                            <span class="fa-stack fa-1x"><i class="fa fa-circle-thin fa-stack-2x"></i><i
                                    class="fa fa-exclamation fa-stack-1x"></i></span>&nbsp;
                        <?php _e('Additionally you can insert ', 'itrr_lang'); ?>
                        [intrigger scenario="<?php echo $post->ID; ?>"]
                        <?php _e('wherever you want to activate this trigger.', 'itrr_lang'); ?>
                    </div>
                </div>
            </div>
            <!-- End: Description Area -->
            </div>
            <!-- End : Where section -->
            <script type="text/ng-template" id="two.tpl.html">
                <div id="viewTwo">
                    <div class="scenario_form_ele">
                        <div class="scenario_field_area">
                            <p class="scenario_field_description" style="margin-top: 10px;"> <?php _e("Let's search all the pages / posts containing a specific keyword.", "itrr_lang"); ?> </p>
                            <p class="scenario_field_description" style="margin-bottom: 20px;"> <?php _e("You will be able to select among results on which pages / posts apply the scenario.", "itrr_lang") ?> </p>
                        </div>
                    </div>
                    <div class="scenario_form_ele">
                        <div class="scenario_field_area">
                            <input type="text" id="itrr_search_posts_key" placeholder="Enter a keyword">
                            <button role="presentation" class="button" type="button" id="itrr_search_posts"><?php _e("Search", "itrr_lang"); ?></button>
                        </div>
                    </div>
                    <div class="scenario_form_ele" id="itrr_search_posts_results" style="display: none;">
                        <div class="scenario_field_area">
                            <div class="scenario_field_subarea" id="itrr_search_result_message">
                            </div>
                            <table class="scenario_field_subarea" id="itrr_search_result_table">
                                <thead>
                                <tr class="itrr_search_result_table_head">
                                    <th class="itrr_srt_first"><input type="checkbox" id="itrr_search_result_list_all" value="all"/></th>
                                    <th class="itrr_srt_second"><?php _e("Title", "itrr_lang"); ?></th>
                                    <th class="itrr_srt_third"><?php _e("Nature", "itrr_lang"); ?></th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <div class="scenario_field_subarea" id="itrr_search_select_message">
                            </div>
                            <div class="scenaio_field_subarea" id="itrr_search_confirm_area">
                                <button class="button" id="itrr_search_confirm_button"><?php _e("confirm", "itrr_lang"); ?></button>
                                <a href="#" id="itrr_search_cancel_button"><?php _e("cancel", "itrr_lang"); ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="scenario_form_ele" id="itrr_search_posts_results_message" style="display: none;">
                    </div>
                </div>
            </script>
            </div>


            <!-- Start : When section -->
            <div class="scenario_form_section when_section even">
                <div class="scenario_form_ele">
                    <div class="scenario_title_area"><b><?php _e('When?', 'itrr_lang'); ?></b></div>
                    <div class="scenario_field_area">
                        <label>
                            <input type="radio" name="sc_rule[when][opt]" ng-model="rule_when_opt" value="always" <?php
                            if (isset($scenario_rules['when']['opt'])) {
                                echo 'ng-init="rule_when_opt=' . "'" . $scenario_rules['when']['opt'] . "'" . '"';
                            }
                            ?>>&nbsp;<?php _e('Always', 'itrr_lang'); ?>
                        </label>
                    </div>
                </div>
                <div class="scenario_form_ele">
                    <div class="scenario_title_area">&nbsp;</div>
                    <div class="scenario_field_area">
                        <label>
                            <input type="radio" name="sc_rule[when][opt]" ng-model="rule_when_opt"
                                   value="already_visited">&nbsp;<?php _e('Visitors have already visited ', 'itrr_lang'); ?>
                            &nbsp;
                            <input type="number" name="sc_rule[when][visited_num]" min="1" max="100" value="<?php
                            if (isset($scenario_rules['when']['visited_num'])) {
                                echo $scenario_rules['when']['visited_num'];
                            }
                            ?>">&nbsp;<?php _e('pages', 'itrr_lang'); ?>
                        </label>
                    </div>
                </div>
                <div class="scenario_form_ele">
                    <div class="scenario_title_area">&nbsp;</div>
                    <div class="scenario_field_area">
                        <label>
                            <input type="radio" name="sc_rule[when][opt]" ng-model="rule_when_opt"
                                   value="already_spent">&nbsp;<?php _e('Visitors have already spent ', 'itrr_lang'); ?>
                            &nbsp;
                            <input type="number" name="sc_rule[when][spent_min]" min="1" max="100" step="0.1"
                                   value="<?php
                                   if (isset($scenario_rules['when']['spent_min'])) {
                                       echo $scenario_rules['when']['spent_min'];
                                   }
                                   ?>">&nbsp;<?php _e('minutes on the website', 'itrr_lang'); ?>
                        </label>
                    </div>
                </div>
            </div>
            <!-- End : When section -->

            <!-- Start: Who section -->
            <div class="scenario_form_section who_section odd">
                <div class="scenario_form_ele">
                    <div class="scenario_title_area"><b><?php _e('Who?', 'itrr_lang'); ?></b></div>
                    <div class="scenario_field_area">
                        <label>
                            <input type="radio" name="sc_rule[who][opt]" ng-model="rule_who_opt" value="all" <?php
                            if (isset($scenario_rules['who']['opt'])) {
                                echo 'ng-init="rule_who_opt=' . "'" . $scenario_rules['who']['opt'] . "'" . '"';
                            }
                            ?>>&nbsp;<?php _e('All users', 'itrr_lang'); ?>
                        </label>
                    </div>
                </div>
                <div class="scenario_form_ele">
                    <div class="scenario_title_area">&nbsp;</div>
                    <div class="scenario_field_area">
                        <label>
                            <input type="radio" name="sc_rule[who][opt]" ng-model="rule_who_opt"
                                   value="logged">&nbsp;<?php _e('Logged in users', 'itrr_lang'); ?>
                        </label>
                    </div>
                </div>
                <div class="scenario_form_ele" ng-if="rule_who_opt == 'logged'">
                    <div class="scenario_title_area">&nbsp;</div>
                    <div class="scenario_field_area">
                        &nbsp;&nbsp;&nbsp;&nbsp;<select name="sc_rule[who][user_roles][]"
                                                        data-placeholder="Please select role of user" chosen-select
                                                        multiple tabindex="8" style="width: 80%;">
                            <?php
                            foreach ($total_user_roles as $role => $details) {
                                $name = translate_user_role($details['name']);
                                ?>
                                <option value="<?php echo esc_attr($role); ?>" <?php
                                if (isset($scenario_rules['who']['user_roles']) && is_array($scenario_rules['who']['user_roles'])) {
                                    if (in_array(esc_attr($role), $scenario_rules['who']['user_roles'])) {
                                        echo 'selected="selected"';
                                    }
                                }
                                ?>><?php echo $name; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="scenario_form_ele">
                    <div class="scenario_title_area">&nbsp;</div>
                    <div class="scenario_field_area">
                        <label>
                            <input type="radio" name="sc_rule[who][opt]" ng-model="rule_who_opt" value="not_logged">&nbsp;<?php _e('Not logged in users', 'itrr_lang'); ?>
                        </label>
                    </div>
                </div>
            </div>
            <!-- End: Who section -->

            <!-- Start: Device section -->
            <div class="scenario_form_section device_section even">
                <div class="scenario_form_ele">
                    <div class="scenario_title_area"><b><?php _e('Device', 'itrr_lang'); ?></b></div>
                    <div class="scenario_field_area">
                        <label>
                            <input type="checkbox" name="sc_rule[device][desktop]" value="yes" <?php
                            if (isset($scenario_rules['device']['desktop']) && ($scenario_rules['device']['desktop'] == 'yes')) {
                                echo 'checked';
                            }
                            ?>>&nbsp;<?php _e('Desktop', 'itrr_lang'); ?>
                        </label>
                    </div>
                </div>
                <div class="scenario_form_ele">
                    <div class="scenario_title_area">&nbsp;</div>
                    <div class="scenario_field_area">
                        <label>
                            <input type="checkbox" name="sc_rule[device][tablets]" value="yes" <?php
                            if (isset($scenario_rules['device']['tablets']) && ($scenario_rules['device']['tablets'] == 'yes')) {
                                echo 'checked';
                            }
                            ?>>&nbsp;<?php _e('Tablets', 'itrr_lang'); ?>
                        </label>
                    </div>
                </div>
                <div class="scenario_form_ele">
                    <div class="scenario_title_area">&nbsp;</div>
                    <div class="scenario_field_area">
                        <label>
                            <input type="checkbox" name="sc_rule[device][mobile]" value="yes" <?php
                            if (isset($scenario_rules['device']['mobile']) && ($scenario_rules['device']['mobile'] == 'yes')) {
                                echo 'checked';
                            }
                            ?>>&nbsp;<?php _e('Mobile', 'itrr_lang'); ?>
                        </label>
                    </div>
                </div>
            </div>
            <!-- End : Device section -->

            <!-- Start: Retargeting section -->
            <div class="scenario_form_section retargeting_section odd">
                <div class="scenario_form_ele">
                    <div class="scenario_title_area"><b><?php _e('Retargeting', 'itrr_lang'); ?></b></div>
                    <div class="scenario_field_area">
                        <label>
                            <input type="checkbox" name="sc_rule[retargeting][session]" value="yes" <?php
                            if (isset($scenario_rules['retargeting']['session']) && ($scenario_rules['retargeting']['session'] == 'yes')) {
                                echo 'checked';
                            }
                            ?>>&nbsp;<?php _e('Apply the scenario only ', 'itrr_lang'); ?>&nbsp;
                            <input type="number" name="sc_rule[retargeting][session_num]" min="1" max="100" value="<?php
                            if (isset($scenario_rules['retargeting']['session_num'])) {
                                echo $scenario_rules['retargeting']['session_num'];
                            }
                            ?>">&nbsp;<?php _e('time per session', 'itrr_lang'); ?>
                        </label>
                    </div>
                </div>
                <div class="scenario_form_ele">
                    <div class="scenario_title_area">&nbsp;</div>
                    <div class="scenario_field_area">
                        <label>
                            <input type="checkbox" name="sc_rule[retargeting][conversion]" value="yes" <?php
                            if (isset($scenario_rules['retargeting']['conversion']) && ($scenario_rules['retargeting']['conversion'] == 'yes')) {
                                echo 'checked';
                            }
                            ?>>&nbsp;<?php _e('After conversion, do not apply the scenario again for ', 'itrr_lang'); ?>
                            &nbsp;
                            <select name="sc_rule[retargeting][conversion_opt]" style="width: 150px;">
                                <option
                                    value="session" <?php selected($scenario_rules['retargeting']['conversion_opt'], 'session'); ?>><?php _e('During session', 'itrr_lang'); ?></option>
                                <option
                                    value="+1 day" <?php selected($scenario_rules['retargeting']['conversion_opt'], '+1 day'); ?>><?php _e('Today', 'itrr_lang'); ?></option>
                                <option
                                    value="ever" <?php selected($scenario_rules['retargeting']['conversion_opt'], 'ever'); ?>><?php _e('Ever', 'itrr_lang'); ?></option>
                            </select>
                        </label>
                    </div>
                </div>
                <div class="scenario_form_ele">
                    <div class="scenario_title_area">&nbsp;</div>
                    <div class="scenario_field_area">
                        <label>
                            <input type="checkbox" name="sc_rule[retargeting][another_apply]" value="yes" <?php
                            if (isset($scenario_rules['retargeting']['another_apply']) && ($scenario_rules['retargeting']['another_apply'] == 'yes')) {
                                echo 'checked';
                            }
                            ?>>&nbsp;<?php _e('Do not apply another scenario after this scenario is applied.', 'itrr_lang'); ?>
                        </label>
                    </div>
                </div>
                <div class="scenario_form_ele">
                    <div class="scenario_title_area">&nbsp;</div>
                    <div class="scenario_field_area">
                        <label>
                            <input type="checkbox" name="sc_rule[retargeting][another_apply_conversion]"
                                   value="yes" <?php
                            if (isset($scenario_rules['retargeting']['another_apply_conversion']) && ($scenario_rules['retargeting']['another_apply_conversion'] == 'yes')) {
                                echo 'checked';
                            }
                            ?>>&nbsp;<?php _e('Do not apply another scenario after this scenario generates a conversion.', 'itrr_lang'); ?>
                        </label>
                    </div>
                </div>
            </div>
            <!-- End: Retargeting section -->

            <!-- start: Priority Score section -->
            <div class="scenario_form_section retargeting_section even">
                <div class="scenario_form_ele">
                    <div class="scenario_title_area"><b><?php _e('Priority', 'itrr_lang'); ?></b></div>
                    <div class="scenario_field_area">
                        <label>
                            <?php _e('Apply this scenario with a priority score of ') ?> &nbsp;
                            <input type="number" name="sc_rule[priority][score]" min="1" max="10" value="<?php echo (isset($scenario_rules['priority']['score']) ? $scenario_rules['priority']['score'] : 1 )?>"/>&nbsp;
                            <?php _e('if several scenarios apply on the same page.'); ?>
                             <span class="fa-stack fa-1x" id="itrr_priority_comment"><i class="fa fa-circle-thin fa-stack-2x"></i>
                                 <i class="fa fa-question fa-stack-1x itrr-tooltip" title="  <?php _e('When several scenarios apply on a page, the scenario with the highest priority score will apply. If equal, the scenario with the highest ID.' , 'itrr_lang');?>"></i>
                             </span>
                        </label>
                    </div>
                </div>
            </div>
            </div>
            <script>
                jQuery(document).ready(function ($) {
                    $('#publish').removeClass('button button-primary button-large').addClass('itrr_button');
                });
            </script>
        <?php
        }

        /**
         * Save setting data of scenario.
         * @param $post_id
         */
        function save_scenario_settings($post_id)
        {
            // Check if our nonce is set.
            if (!isset($_POST['intrigger_scenario_rule_nonce'])) {
                return;
            }

            // Verify that the nonce is valid.
            if (!wp_verify_nonce($_POST['intrigger_scenario_rule_nonce'], 'intrigger_scenario_rule')) {
                return;
            }

            // Check if our nonce is set.
            if (!isset($_POST['intrigger_scenario_indget_nonce'])) {
                return;
            }

            // Verify that the nonce is valid.
            if (!wp_verify_nonce($_POST['intrigger_scenario_indget_nonce'], 'intrigger_scenario_indget')) {
                return;
            }

            // If this is an autosave, our form has not been submitted, so we don't want to do anything.
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }

            // check user role
            if (!current_user_can('manage_options'))
                wp_die('Not allowed');

            // Save indget setting.
            $indget_setting_temp = get_post_meta($post_id, ITRR_Scenario::cst_setting_indget, true);
            if (empty($indget_setting_temp) || !is_array($indget_setting_temp)) {
                $indget_setting_temp = ITRR_Scenario::getDefaultDisplay();
            }
            $scenario_type = $_POST['intrigger_scenario_type'];
            $indget_id = intval($_POST['scenario_indget']);
            $indget_setting_val = isset($_POST['scenario_indget_setting']) ? $_POST['scenario_indget_setting'] : array();
            $indget_setting_display = isset($_POST['scenario_indget_display']) ? $_POST['scenario_indget_display'] : '';
            $indget_setting_position = isset($_POST['scenario_indget_position']) ? $_POST['scenario_indget_position'] : '';

            $indget_setting_temp['id'] = $indget_id;
            $indget_setting_temp['type'] = $scenario_type;
            $indget_setting_temp[$scenario_type] = array(
                'position' => $indget_setting_position != '' ? $indget_setting_position[$scenario_type] : '',
                'display' => $indget_setting_display != '' ? $indget_setting_display[$scenario_type] : '',
            );
            foreach ($indget_setting_val as $key => $val) {
                if ($val != null) {
                    $indget_setting_temp['setting'][$key] = $val;
                }
            }
            $indget_setting = $indget_setting_temp;

            update_post_meta($post_id, ITRR_Scenario::cst_setting_indget, $indget_setting);
            update_post_meta($post_id, ITRR_Scenario::cst_post_type, $scenario_type);

            // Save rule setting.
            $scenario_rule = $_POST['sc_rule'];
            update_post_meta($post_id, ITRR_Scenario::cst_setting_rule, $scenario_rule);


        }

        /**
         * Add agualarJS app tag for use of angularJS
         * @param $post
         */
        function post_edit_form_tag($post)
        {
            global $post_type;
            if ($post_type == ITRR_Scenario::cst_post_type) {
                echo ' ng-app="itrrScenarioApp"';
            }
        }

        /**
         * Customize list table of scenario.
         * @param $existing_columns
         */
        function edit_columns($existing_columns)
        {
            $date = $existing_columns['date'];
            unset($existing_columns['date']);

            $existing_columns['trigger_type'] = __('Type', 'itrr_lang');
            $existing_columns['trigger_status'] = __('Stats', 'itrr_lang');
            $existing_columns['indget_type'] = __('Indget', 'itrr_lang');
            $existing_columns['date'] = $date;

            return apply_filters('intrigger_scenario_columns', $existing_columns);
        }

        /**
         * Customize the content of new column.
         * @param $column
         */
        function custom_columns($column)
        {

            global $post;

            $trigger_type = get_post_meta($post->ID, ITRR_Scenario::cst_post_type, true);
            $indget_data = get_post_meta($post->ID, ITRR_Scenario::cst_setting_indget, true);
            if (!isset($indget_data)) {
                $indget_name = __('None', 'itrr_lang');
            } else {
                $indget_name = get_the_title($indget_data['id']);
            }

            $indget_stats = ITRR_Stats::get_stats_trigger(($post->ID)); // array
            $indget_clicks = $indget_stats['conversion'];
            $indget_impressions = $indget_stats['impression'];
            $indget_rate = $indget_stats['rate'];

            $indget_rate = '<a href="#" class="int_stats_rate" style="font-weight: bold; font-size: 15px; color: #238E67;" title="Conversion rate">' . $indget_rate . '</a>';
            $indget_clicks = '<a href="#" class="int_stats_clicks" style="font-weight: bold; font-size: 13px; color: #356BE8;" title="Number of conversions">' . $indget_clicks . '</a>';
            $indget_impressions = '<a href="#" class="int_stats_impressions" style="font-weight: normal; font-size: 13px; color: #DE2D2D;" title="Number of impressions">' . $indget_impressions . '</a>';

            switch ($column) {
                case 'trigger_type':
                    echo '<span class="itrr_scenario_' . $trigger_type . '">' . ucfirst(str_replace('_', ' ', $trigger_type)) . '</span>';
                    break;
                case 'indget_type':
                    echo $indget_name;
                    break;
                case 'trigger_status':
                    echo $indget_rate . '<br>';
                    echo $indget_clicks . ' - ' . $indget_impressions;
                    break;

                default:
                    break;

            }
        }

        /**
         * Add css & js for custom layout of admin metabox area.
         */
        function enqueue_script()
        {
            global $post_type;
            if ($post_type == ITRR_Scenario::cst_post_type) {
                $indget_type_object = ITRR_Indget_Type::getInstance();
                $indgets = ITRR_Indget_Admin::getAllActiveIndget();
                wp_enqueue_script('itrr-indget-angular-js', ITRR_Manager::$plugin_url . '/asset/js/angular.min.js');
                wp_enqueue_script('itrr-indget-chosen-js', ITRR_Manager::$plugin_url . '/asset/js/chosen.jquery.min.js');
                wp_enqueue_script('itrr-indget-admin-js', ITRR_Manager::$plugin_url . '/asset/js/angular.scenario-admin.js', array(), filemtime(ITRR_Manager::$plugin_dir . '/asset/js/angular.scenario-admin.js'));
                wp_localize_script('itrr-indget-admin-js', 'itrr_admin_ajax_url', admin_url('admin-ajax.php'));
                wp_enqueue_style('itrr-indget-admin-css', ITRR_Manager::$plugin_url . '/asset/css/admin-scenario.css', array(), filemtime(ITRR_Manager::$plugin_dir . '/asset/css/admin-scenario.css'));
                wp_enqueue_style('itrr-indget-fontawesome-css', ITRR_Manager::$plugin_url . '/asset/css/fontawesome/css/font-awesome.min.css');
                wp_enqueue_style('itrr-indget-chosen-css', ITRR_Manager::$plugin_url . '/asset/css/chosen.css');
                wp_localize_script('itrr-indget-admin-js', 'ITRR_PLUGIN_URL', ITRR_Manager::$plugin_url);
                wp_localize_script('itrr-indget-admin-js', 'ITRR_INDGET_TYPES', json_encode($indget_type_object->main_types));
                wp_localize_script('itrr-indget-admin-js', 'ITRR_INDGET_LISTS', json_encode($indgets));
                // get custom posts and it's taxonomies
                $cpts = self::get_cpt_taxonomies();
                wp_localize_script('itrr-indget-admin-js', 'ITRR_CPTS', json_encode($cpts));
            }
        }

        function enqueue_script_edit()
        {
            global $post_type;
            if ($post_type == ITRR_Scenario::cst_post_type) {
                wp_enqueue_style('itrr-indget-admin-css', ITRR_Manager::$plugin_url . '/asset/css/admin-common.css', array(), filemtime(ITRR_Manager::$plugin_dir . '/asset/css/admin-common.css'));
            }
        }

        /**
         * Custom post type and Taxonomies
         * create a json collection for scenario rule - 'where > custom posts > taxonomies '
         */
        public static function get_cpt_taxonomies()
        {
            global $post;
            $scenario_rules = get_post_meta($post->ID, ITRR_Scenario::cst_setting_rule, true);
            if (empty($scenario_rules) || !is_array($scenario_rules)) {
                $scenario_rules = ITRR_Scenario::getDefaultRule();
            }
            global $wp_post_types;
            $args = array(
                'public' => true,
                '_builtin' => false
            );
            $post_types = get_post_types($args);
            $cpts = array();
            foreach ($post_types as $post_key => $post_type) {
                if ($post_key == 'product' || $post_key == 'itrr_scenario' || $post_key == 'itrr_indget')
                    continue;
                $cpt = $wp_post_types[$post_key];
                $post_name = $cpt->labels->name;
                $taxonomies = get_object_taxonomies($post_key);
                if (count($taxonomies) == 0) continue;
                $args = array(
                    'orderby' => 'name',
                    'order' => 'ASC',
                );
                $terms = get_terms($taxonomies[0], $args); // it's element is object
                // set check if it is checked
                $terms_array = array();
                $cpts[$post_name]['select'] = false;
                foreach ($terms as $term) {
                    $term = (array)$term;
                    $term['select'] = false;
                    if (isset($scenario_rules['where']['cpt'][$post_name][$term['name']]) && $scenario_rules['where']['cpt'][$post_name][$term['name']] == 'yes') {
                        $term['select'] = true;
                        $cpts[$post_name]['select'] = true;
                    }
                    $terms_array[] = $term;
                }
                $cpts[$post_name]['data'] = $terms_array;
            }
            return $cpts;
        }

        public static function getAllActiveScenarioIDs()
        {
            $args = array(
                'posts_per_page' => -1,
                'orderby' => 'id',
                'order' => 'ASC',
                'post_type' => ITRR_Scenario::cst_post_type,
                'post_status' => 'publish',
            );
            $posts_array = get_posts($args);
            $results = $results_inline = $results_other = array();
            foreach ($posts_array as $post) {
                $scenario_setting = get_post_meta($post->ID, ITRR_Scenario::cst_setting_indget, true);
                // for continue and inline, we have to resort...
                if( isset( $scenario_setting['type'] ))
                {
                    if ($scenario_setting['type'] == 'inline') {
                        $results_inline[] = array(
                            'id' => $post->ID,
                            'type' => $scenario_setting['type'] // inline only
                        );
                    } else {
                        $results_other[] = array(
                            'id' => $post->ID,
                            'type' => $scenario_setting['type'] // continue_reading, float_bar
                        );
                    }
                }
            }

            $results = array_merge($results_inline, $results_other);

            return $results;
        }


        /**
         * Ajax process for preview indget.
         */
        public static function get_preview_indget()
        {
            if (!isset($_POST['indget_id'])) {
                die();
            }
            $indget_id = intval($_POST['indget_id']);
            $theme_content = self::get_preview_indget_theme($indget_id);
            echo $theme_content;
            die();
        }

        public static function get_preview_indget_theme($indget_id)
        {
            $setting_value = get_post_meta($indget_id, ITRR_Indget::cst_setting_data, true);
            $indget_type = get_post_meta($indget_id, ITRR_Indget::cst_type, true);
            $indget_subtype = get_post_meta($indget_id, ITRR_Indget::cst_subtype, true);
            $theme_content = ITRR_Indget_Type::get_theme_content($indget_type, $indget_subtype, $setting_value, $indget_id);
            return $theme_content;
        }

        // to search posts and pages by keyword
        function search_all_pages()
        {
            $args = array();
            if (isset($_POST['keyword']))
                $args['s'] = wp_unslash($_POST['keyword']);
            require(ABSPATH . WPINC . '/class-wp-editor.php');
            add_filter('wp_link_query_args' , array( $this , 'itrr_query_args') , 100, 1);
            $results = _WP_Editors::wp_link_query($args);
            if (!isset($results))
                wp_die(0);
            echo wp_json_encode($results);
            echo "\n";
            wp_die();
        }

        // for custom args
        public function itrr_query_args( $query)
        {
            $args = $query;
            $args['posts_per_page'] = -1;
            return $args;
        }

        public static function getScenarioSettings(){

            $scenario_array = ITRR_Manager::$allActiveScenarioIDs;
            $settings = array();
            foreach ($scenario_array as $scenario) {
                //
                $scenario_id = $scenario['id'];

                $scenario_indget_setting = get_post_meta($scenario_id, ITRR_Scenario::cst_setting_indget, true);
                if ((empty($scenario_indget_setting)) || (!is_array($scenario_indget_setting)))
                    continue;
                $scenario_indget_id = intval($scenario_indget_setting['id']);
                if ($scenario_indget_id <= 0)
                    continue;
                $scenario_indget_type = get_post_meta($scenario_indget_id, ITRR_Indget::cst_type, true);
                if ((empty($scenario_indget_type)) || ($scenario_indget_type == ''))
                    continue;

                $scenario_rules = get_post_meta($scenario_id, ITRR_Scenario::cst_setting_rule, true);
                if ((empty($scenario_rules)) || (!is_array($scenario_rules)))
                    continue;

                $settings[$scenario_id]['indget_id'] = $scenario_indget_id;
                $settings[$scenario_id]['indget_type'] = $scenario_indget_type;
                $settings[$scenario_id]['rules'] = $scenario_rules;
                $settings[$scenario_id]['setting'] = $scenario_indget_setting;
            }
            return $settings;
        }
    }
}



