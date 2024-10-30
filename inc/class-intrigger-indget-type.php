<?php
if ( ! defined( 'ABSPATH' ) ) {
    die( 'Access denied.' );
}

/**
 * Intrigger Indget Type class
 * @package ITRR_Indget_Type
 */

if (!class_exists('ITRR_Indget_Type')) {
    class ITRR_Indget_Type {

        var $main_types = null;
        var $sub_types = null;
        var $indget_settings = null;

        private function __construct() {

            // Define Main Types.
            $this->main_types = array(
                array(
                    'id'                  => 'continue_reading',
                    'name'                => __('Continue Reading', 'itrr_lang'),
                    'description'         => __('Post content is hidden. Visitors have to click/sign up to read the full post.','itrr_lang'),
                ),
                array(
                    'id'                  => 'inline',
                    'name'                => __('Inline', 'itrr_lang'),
                    'description'         => __('An Indget is inserted inside some targeted posts.','itrr_lang')
                ),
                array(
                    'id'                  => 'float_bar',
                    'name'                => __('Floating Bar', 'itrr_lang'),
                    'description'         => __('A floating bar appears at the top or at the bottom of screen on scrolling.','itrr_lang'),
                ),
            );

            // Define Sub Types.
            $this->sub_types = array(
                array(
                    'id'                  => 'collect_email',
                    'name'                => __('Collect emails', 'itrr_lang'),
                    'description'         => __('Collect your visitors email addresses.','itrr_lang')
                ),
                array(
                    'id'                  => 'drive_traffic',
                    'name'                => __('Drive traffic', 'itrr_lang'),
                    'description'         => __('Drive traffic to a targeted page','itrr_lang')
                ),
                array(
                    'id'                  => 'custom',
                    'name'                => __('Custom Indget', 'itrr_lang'),
                    'description'         => __('Create an indget in HTML/CSS.','itrr_lang')
                ),
            );

            $this->indget_settings = array(
                "continue_reading-collect_email" => array(
                    'theme' => 'default',
                    'headline' => __('To continue reading, please first sign up to our great newsletters!', 'itrr_lang'),
                    'headline_fontsize' => 17,
                    'input_preview' => 'your@email.com',
                    'button_label' => __('SIGN UP & CONTINUE READING', 'itrr_lang'),
                    'background_color' => '#EEEEEE',
                    'headline_font_color' => '#333333',
                    'button_background_color' => '#fa6e4f',
                    'button_font_color' => '#ffffff',
                ),
                "continue_reading-drive_traffic" => array(
                    'theme' => 'default',
                    'headline' => __('Please visit our services page to continue reading. Thanks!', 'itrr_lang'),
                    'headline_fontsize' => 17,
                    'message_body' => '',
                    'button_label' => __('VISIT & CONTINUE READING', 'itrr_lang'),
                    'button_link' => 'http://example.com',
                    'background_color' => '#EEEEEE',
                    'headline_font_color' => '#333333',
                    'button_background_color' => '#fa6e4f',
                    'button_font_color' => '#ffffff',
                ),
                "inline-collect_email" => array(
                    'theme' => 'default',
                    'headline' => __('Sign up for our newsletter to receive great offer!', 'itrr_lang'),
                    'headline_fontsize' => 17,
                    'input_preview' => 'your@email.com',
                    'button_label' => __('SIGN UP', 'itrr_lang'),
                    'background_color' => '#EEEEEE',
                    'headline_font_color' => '#333333',
                    'button_background_color' => '#fa6e4f',
                    'button_font_color' => '#ffffff',
                    'confirmation_message' => __('Thank you! You have been successfully subscribed to our newsletter.', 'itrr_lang'),
                ),
                "inline-drive_traffic" => array(
                    'theme' => 'default',
                    'headline' => __('Please visit our services page to discover great products. Thanks!', 'itrr_lang'),
                    'headline_fontsize' => 17,
                    'message_body' => '',
                    'button_label' => __('VISIT', 'itrr_lang'),
                    'button_link' => 'http://example.com',
                    'background_color' => '#EEEEEE',
                    'headline_font_color' => '#333333',
                    'button_background_color' => '#fa6e4f',
                    'button_font_color' => '#ffffff',
                ),
                "float_bar-collect_email" => array(
                    'theme' => 'default',
                    'headline' => __('Sign up for our newsletter to receive great offer!', 'itrr_lang'),
                    'headline_fontsize' => 15,
                    'input_preview' => 'your@email.com',
                    'button_label' => __('Sign up', 'itrr_lang'),
                    'background_color' => '#fa6e4f',
                    'headline_font_color' => '#ffffff',
                    'button_background_color' => '#111111',
                    'button_font_color' => '#ffffff',
                    'confirmation_message' => __('Thank you! You have been successfully subscribed to our newsletter.', 'itrr_lang'),
                ),
                "float_bar-drive_traffic" => array(
                    'theme' => 'default',
                    'headline' => __('Please visit our services page to discover great products. Thanks!', 'itrr_lang'),
                    'headline_fontsize' => 15,
                    'message_body' => '',
                    'button_label' => __('Visit', 'itrr_lang'),
                    'button_link' => 'http://example.com',
                    'background_color' => '#fa6e4f',
                    'headline_font_color' => '#ffffff',
                    'button_background_color' => '#111111',
                    'button_font_color' => '#ffffff',
                ),

            );
        }

        function getTypeNameFromID($typs_id) {
            $ret_val = '';
            foreach ($this->main_types as $indget_type) {
                if ($indget_type['id'] == $typs_id) {
                    $ret_val = $indget_type['name'];
                }
            }
            return $ret_val;
        }

        function getSubTypeNameFromID($subtype_id) {
            $ret_val = '';
            foreach ($this->sub_types as $indget_subtype) {
                if ($indget_subtype['id'] == $subtype_id) {
                    $ret_val = $indget_subtype['name'];
                }
            }
            return $ret_val;
        }

        /**
         * Singleton for ITRR_Indget_Admin
         * @return ITRR_Indget_Type
         */
        public static function getInstance() {
            static $intrigger_indget_type = null;
            if (null === $intrigger_indget_type) {
                $intrigger_indget_type = new ITRR_Indget_Type();
            }
            return $intrigger_indget_type;
        }

        function getMainTypes() {
            return $this->main_types;
        }

        function getSubTypes() {
            return $this->sub_types;
        }

        public static function generate_admin_setting_fields($type, $sub_type, $post_id) {
            $func_name = 'gasf_' . $type . '_' . $sub_type;
            ITRR_Indget_Type::$func_name($post_id);
        }

        public static function gasf_continue_reading_collect_email($post_id) {
            $indgetSetting =ITRR_Manager::$allActiveIndgets;
            $setting_value = isset($indgetSetting[$post_id]) ? $indgetSetting[$post_id]['setting'] : null;
            $main_settings = null;
            if (isset($setting_value['continue_reading']['collect_email']) && is_array($setting_value)) {
                $main_settings = $setting_value['continue_reading']['collect_email'];
            }

            if (!isset($main_settings) || !is_array($main_settings)) {
                // Default setting.
                $main_settings = self::getInstance()->indget_settings['continue_reading-collect_email'];
                $setting_value['continue_reading']['collect_email'] = $main_settings;
            }
            $themes = self::getThemeNames('continue_reading/collect_email');
            ?>
            <div class="indget_title_area">
                <b><?php _e('Theme', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <select id="con_collect_theme" style="width: 20%;" name="indget_setting[continue_reading][collect_email][theme]">
                    <?php
                    foreach ($themes as $theme) {
                        $theme_name = ucwords(str_replace('-', ' ', $theme));
                        ?>
                        <option value="<?php echo $theme; ?>" <?php selected($main_settings['theme'], $theme); ?>><?php echo $theme_name; ?></option>
                    <?php
                    }
                    ?>
                </select><br>
                <span class="fa-stack fa-1x"><i class="fa fa-circle-thin fa-stack-2x"></i><i class="fa fa-exclamation fa-stack-1x"></i></span>&nbsp;&nbsp;
                <?php _e('This indget CSS class is', 'itrr_lang'); ?> "int_indget_continue_collect_<span id="con_collect_theme_name"><?php echo $main_settings['theme'];?></span>"
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Preview', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area" style="margin-bottom: 20px;">
                <div id="indget_continue_collect_preview" class="indget_preview_img">
                    <?php
                    $theme_content = self::get_theme_content('continue_reading', 'collect_email', $setting_value, $post_id);
                    echo $theme_content;
                    ?>
                </div>
                <div><a href="javascript:void(0);" id="indget_continue_collect_preview_refresh"><i class="fa fa-repeat"></i>&nbsp;<?php _e('Refresh Indget preview', 'itrr_lang'); ?></a> </div>
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Headline', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="text" class="fullwidth" id="indget_continue_collect_headline" name="indget_setting[continue_reading][collect_email][headline]" value="<?php echo $main_settings['headline']; ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Headline fontsize', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="number" min="10" max="100" id="indget_continue_collect_headline_fontsize" name="indget_setting[continue_reading][collect_email][headline_fontsize]" value="<?php echo intval($main_settings['headline_fontsize']); ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Input prevalue', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="text" class="fullwidth" id="indget_continue_collect_input_preview" name="indget_setting[continue_reading][collect_email][input_preview]" value="<?php echo $main_settings['input_preview']; ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Button label', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="text" class="fullwidth" maxlength="30" id="indget_continue_collect_button_label"  name="indget_setting[continue_reading][collect_email][button_label]" value="<?php echo $main_settings['button_label']; ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Colors', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Background', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_continue_collect_background_color" name="indget_setting[continue_reading][collect_email][background_color]" value="<?php echo $main_settings['background_color']; ?>">
                    </div>
                </div>
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Headline Font', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_continue_collect_headline_font_color" name="indget_setting[continue_reading][collect_email][headline_font_color]" value="<?php echo $main_settings['headline_font_color']; ?>">
                    </div>
                </div>
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Button Background', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_continue_collect_button_background_color" name="indget_setting[continue_reading][collect_email][button_background_color]" value="<?php echo $main_settings['button_background_color']; ?>">
                    </div>
                </div>
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Button Font', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_continue_collect_button_font_color" name="indget_setting[continue_reading][collect_email][button_font_color]" value="<?php echo $main_settings['button_font_color']; ?>">
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Shortcode', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <?php echo __('To display this indget without including it in a scenario, you can use the shortcode', 'itrr_lang') . ' [intrigger indget="' . $post_id . '"]'; ?>
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Contacts', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <?php _e('All contacts generated from this indget are saved in the section "Contacts".', 'itrr_lang'); ?>
            </div>
            <div class="clearfix"></div>
        <?php
        }

        public static function gasf_continue_reading_drive_traffic($post_id) {
            $indgetSetting =ITRR_Manager::$allActiveIndgets;
            $setting_value = isset($indgetSetting[$post_id]) ? $indgetSetting[$post_id]['setting'] : null;
            $main_settings = null;
            if (isset($setting_value['continue_reading']['drive_traffic']) && is_array($setting_value)) {
                $main_settings = $setting_value['continue_reading']['drive_traffic'];
            }

            if (!isset($main_settings) || !is_array($main_settings)) {
                // Default setting.
                $main_settings = $main_settings = self::getInstance()->indget_settings['continue_reading-drive_traffic'];
                $setting_value['continue_reading']['drive_traffic'] = $main_settings;
            }
            $themes = self::getThemeNames('continue_reading/drive_traffic');
            ?>
            <div class="indget_title_area">
                <b><?php _e('Theme', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <select id="con_drive_theme" style="width: 20%;" name="indget_setting[continue_reading][drive_traffic][theme]">
                    <?php
                    foreach ($themes as $theme) {
                        $theme_name = ucwords(str_replace('-', ' ', $theme));
                        ?>
                        <option value="<?php echo $theme; ?>" <?php selected($main_settings['theme'], $theme); ?>><?php echo $theme_name; ?></option>
                    <?php
                    }
                    ?>
                </select><br>
                <span class="fa-stack fa-1x"><i class="fa fa-circle-thin fa-stack-2x"></i><i class="fa fa-exclamation fa-stack-1x"></i></span>&nbsp;&nbsp;
                <?php _e('This indget CSS class is', 'itrr_lang'); ?> "int_indget_continue_drive_<span id="con_drive_theme_name"><?php echo $main_settings['theme'];?></span>"
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Preview', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <div id="indget_continue_drive_preview" class="indget_preview_img">
                    <?php
                    $theme_content = self::get_theme_content('continue_reading', 'drive_traffic', $setting_value, $post_id);
                    echo $theme_content;
                    ?>
                </div>
                <div><a href="javascript:void(0);" id="indget_continue_drive_preview_refresh"><i class="fa fa-repeat"></i>&nbsp;<?php _e('Refresh Indget preview', 'itrr_lang'); ?></a> </div>
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Headline', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="text" class="fullwidth" id="indget_continue_drive_headline" name="indget_setting[continue_reading][drive_traffic][headline]" value="<?php echo $main_settings['headline']; ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Headline fontsize', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="number" min="10" max="100" id="indget_continue_drive_headline_fontsize" name="indget_setting[continue_reading][drive_traffic][headline_fontsize]" value="<?php echo intval($main_settings['headline_fontsize']); ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Message body', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <?php
                $editor_args = array(
                    'textarea_name' => 'indget_setting[continue_reading][drive_traffic][message_body]',
                    'textarea_rows' => 10,
                    'editor_class' 	=> 'wp-editor-message',
                    'media_buttons' => true,
                    'tinymce' 		=> true
                );
                wp_editor($main_settings['message_body'], 'indget_continue_drive_message_body', $editor_args );
                ?>
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Button label', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="text" class="fullwidth" id="indget_continue_drive_button_label" name="indget_setting[continue_reading][drive_traffic][button_label]" maxlength="25" value="<?php echo $main_settings['button_label']; ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Button link', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="url" class="fullwidth" id="indget_continue_drive_button_link" name="indget_setting[continue_reading][drive_traffic][button_link]" value="<?php echo $main_settings['button_link']; ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Colors', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Background', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_continue_drive_background_color" name="indget_setting[continue_reading][drive_traffic][background_color]" value="<?php echo $main_settings['background_color']; ?>">
                    </div>
                </div>
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Headline Font', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_continue_drive_headline_font_color" name="indget_setting[continue_reading][drive_traffic][headline_font_color]" value="<?php echo $main_settings['headline_font_color']; ?>">
                    </div>
                </div>
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Button Background', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_continue_drive_button_background_color" name="indget_setting[continue_reading][drive_traffic][button_background_color]" value="<?php echo $main_settings['button_background_color']; ?>">
                    </div>
                </div>
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Button Font', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_continue_drive_button_font_color" name="indget_setting[continue_reading][drive_traffic][button_font_color]" value="<?php echo $main_settings['button_font_color']; ?>">
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Shortcode', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <?php echo __('To display this indget without including it in a scenario, you can use the shortcode', 'itrr_lang') . ' [intrigger indget="' . $post_id . '"]'; ?>
            </div>
            <div class="clearfix"></div>
        <?php
        }
        public static function gasf_continue_reading_custom($post_id) {
            $indgetSetting =ITRR_Manager::$allActiveIndgets;
            $setting_value = isset($indgetSetting[$post_id]) ? $indgetSetting[$post_id]['setting'] : null;
            ?>
            <div class="indget_title_area">
                <b><?php _e('Custom Message', 'itrr_lang'); ?></b><br>
                <?php _e('HTML, CSS, Javascript', 'itrr_lang'); ?>
            </div>
            <div class="indget_field_area">
                <textarea class="indget_field_textarea fullwidth" name="indget_setting[continue_reading][custom][custom_message]"><?php
                    if (isset($setting_value['continue_reading']['custom']['custom_message']) && is_array($setting_value)) {
                        echo $setting_value['continue_reading']['custom']['custom_message'];
                    }
                    ?></textarea>
                <p><span class="fa-stack fa-1x"><i class="fa fa-circle-thin fa-stack-2x"></i><i class="fa fa-exclamation fa-stack-1x"></i></span> <?php _e('Please insert the shortcode [continue] in one href attribute included in your custom message. When click on this link, the end of the post will appear', 'itrr_lang'); ?></p>
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Shortcode', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area" id="textline-test">
                <?php echo __('To display this indget without including it in a scenario, you can use the shortcode', 'itrr_lang') . ' [intrigger indget="' . $post_id . '"]'; ?>
            </div>
            <div class="clearfix"></div>
        <?php
        }
        public static function gasf_inline_collect_email($post_id) {
            $indgetSetting =ITRR_Manager::$allActiveIndgets;
            $setting_value = isset($indgetSetting[$post_id]) ? $indgetSetting[$post_id]['setting'] : null;
            $main_settings = null;
            if (isset($setting_value['inline']['collect_email']) && is_array($setting_value)) {
                $main_settings = $setting_value['inline']['collect_email'];
            }

            if (!isset($main_settings) || !is_array($main_settings)) {
                // Default setting.
                $main_settings = $main_settings = self::getInstance()->indget_settings['inline-collect_email'];
                $setting_value['inline']['collect_email'] = $main_settings;
            }
            $themes = self::getThemeNames('inline/collect_email');
            ?>
            <div class="indget_title_area">
                <b><?php _e('Theme', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <select id="con_collect_theme" style="width: 20%;" name="indget_setting[inline][collect_email][theme]">
                    <?php
                    foreach ($themes as $theme) {
                        $theme_name = ucwords(str_replace('-', ' ', $theme));
                        ?>
                        <option value="<?php echo $theme; ?>" <?php selected($main_settings['theme'], $theme); ?>><?php echo $theme_name; ?></option>
                    <?php
                    }
                    ?>
                </select><br>
                <span class="fa-stack fa-1x"><i class="fa fa-circle-thin fa-stack-2x"></i><i class="fa fa-exclamation fa-stack-1x"></i></span>&nbsp;&nbsp;
                <?php _e('This indget CSS class is', 'itrr_lang'); ?> "int_indget_inline_collect_<span id="con_collect_theme_name"><?php echo $main_settings['theme'];?></span>"
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Preview', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area" style="margin-bottom: 20px;">
                <div id="indget_inline_collect_preview" class="indget_preview_img">
                    <?php
                    $theme_content = self::get_theme_content('inline', 'collect_email', $setting_value, $post_id);
                    echo $theme_content;
                    ?>
                </div>
                <div><a href="javascript:void(0);" id="indget_inline_collect_preview_refresh"><i class="fa fa-repeat"></i>&nbsp;<?php _e('Refresh Indget preview', 'itrr_lang'); ?></a> </div>
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Headline', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="text" class="fullwidth" id="indget_inline_collect_headline" name="indget_setting[inline][collect_email][headline]" value="<?php echo $main_settings['headline']; ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Headline fontsize', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="number" min="10" max="100" id="indget_inline_collect_headline_fontsize" name="indget_setting[inline][collect_email][headline_fontsize]" value="<?php echo intval($main_settings['headline_fontsize']); ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Input prevalue', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="text" class="fullwidth" id="indget_inline_collect_input_preview" name="indget_setting[inline][collect_email][input_preview]" value="<?php echo $main_settings['input_preview']; ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Button label', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="text" class="fullwidth" maxlength="30" id="indget_inline_collect_button_label"  name="indget_setting[inline][collect_email][button_label]" value="<?php echo $main_settings['button_label']; ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Confirmation message', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="text" class="fullwidth" id="indget_inline_collect_confirmation_message"  name="indget_setting[inline][collect_email][confirmation_message]" value="<?php echo isset($main_settings['confirmation_message']) ? $main_settings['confirmation_message'] : ""; ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Colors', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Background', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_inline_collect_background_color" name="indget_setting[inline][collect_email][background_color]" value="<?php echo $main_settings['background_color']; ?>">
                    </div>
                </div>
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Headline Font', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_inline_collect_headline_font_color" name="indget_setting[inline][collect_email][headline_font_color]" value="<?php echo $main_settings['headline_font_color']; ?>">
                    </div>
                </div>
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Button Background', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_inline_collect_button_background_color" name="indget_setting[inline][collect_email][button_background_color]" value="<?php echo $main_settings['button_background_color']; ?>">
                    </div>
                </div>
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Button Font', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_inline_collect_button_font_color" name="indget_setting[inline][collect_email][button_font_color]" value="<?php echo $main_settings['button_font_color']; ?>">
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Shortcode', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <?php echo __('To display this indget without including it in a scenario, you can use the shortcode', 'itrr_lang') . ' [intrigger indget="' . $post_id . '"]'; ?>
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Contacts', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <?php _e('All contacts generated from this indget are saved in the section "Contacts".', 'itrr_lang'); ?>
            </div>
            <div class="clearfix"></div>
        <?php
        }
        public static function gasf_inline_drive_traffic($post_id) {
            $indgetSetting =ITRR_Manager::$allActiveIndgets;
            $setting_value = isset($indgetSetting[$post_id]) ? $indgetSetting[$post_id]['setting'] : null;
            $main_settings = null;
            if (isset($setting_value['inline']['drive_traffic']) && is_array($setting_value)) {
                $main_settings = $setting_value['inline']['drive_traffic'];
            }

            if (!isset($main_settings) || !is_array($main_settings)) {
                // Default setting.
                $main_settings = self::getInstance()->indget_settings['inline-drive_traffic'];
                $setting_value['inline']['drive_traffic'] = $main_settings;
            }
            $themes = self::getThemeNames('inline/drive_traffic');
            ?>
            <div class="indget_title_area">
                <b><?php _e('Theme', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <select id="con_drive_theme" style="width: 20%;" name="indget_setting[inline][drive_traffic][theme]">
                    <?php
                    foreach ($themes as $theme) {
                        $theme_name = ucwords(str_replace('-', ' ', $theme));
                        ?>
                        <option value="<?php echo $theme; ?>" <?php selected($main_settings['theme'], $theme); ?>><?php echo $theme_name; ?></option>
                    <?php
                    }
                    ?>
                </select><br>
                <span class="fa-stack fa-1x"><i class="fa fa-circle-thin fa-stack-2x"></i><i class="fa fa-exclamation fa-stack-1x"></i></span>&nbsp;&nbsp;
                <?php _e('This indget CSS class is', 'itrr_lang'); ?> "int_indget_inline_drive_<span id="con_drive_theme_name"><?php echo $main_settings['theme'];?></span>"
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Preview', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <div id="indget_inline_drive_preview" class="indget_preview_img">
                    <?php
                    $theme_content = self::get_theme_content('inline', 'drive_traffic', $setting_value, $post_id);
                    echo $theme_content;
                    ?>
                </div>
                <div><a href="javascript:void(0);" id="indget_inline_drive_preview_refresh"><i class="fa fa-repeat"></i>&nbsp;<?php _e('Refresh Indget preview', 'itrr_lang'); ?></a> </div>
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Headline', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="text" class="fullwidth" id="indget_inline_drive_headline" name="indget_setting[inline][drive_traffic][headline]" value="<?php echo $main_settings['headline']; ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Headline fontsize', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="number" min="10" max="100" id="indget_inline_drive_headline_fontsize" name="indget_setting[inline][drive_traffic][headline_fontsize]" value="<?php echo intval($main_settings['headline_fontsize']); ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Message body', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <?php
                $editor_args = array(
                    'textarea_name' => 'indget_setting[inline][drive_traffic][message_body]',
                    'textarea_rows' => 10,
                    'editor_class' 	=> 'wp-editor-message',
                    'media_buttons' => true,
                    'tinymce' 		=> true
                );
                wp_editor($main_settings['message_body'], 'indget_inline_drive_message_body', $editor_args );
                ?>
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Button label', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="text" class="fullwidth" id="indget_inline_drive_button_label" name="indget_setting[inline][drive_traffic][button_label]" maxlength="25" value="<?php echo $main_settings['button_label']; ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Button link', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="url" class="fullwidth" id="indget_inline_drive_button_link" name="indget_setting[inline][drive_traffic][button_link]" value="<?php echo $main_settings['button_link']; ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Colors', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Background', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_inline_drive_background_color" name="indget_setting[inline][drive_traffic][background_color]" value="<?php echo $main_settings['background_color']; ?>">
                    </div>
                </div>
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Headline Font', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_inline_drive_headline_font_color" name="indget_setting[inline][drive_traffic][headline_font_color]" value="<?php echo $main_settings['headline_font_color']; ?>">
                    </div>
                </div>
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Button Background', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_inline_drive_button_background_color" name="indget_setting[inline][drive_traffic][button_background_color]" value="<?php echo $main_settings['button_background_color']; ?>">
                    </div>
                </div>
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Button Font', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_inline_drive_button_font_color" name="indget_setting[inline][drive_traffic][button_font_color]" value="<?php echo $main_settings['button_font_color']; ?>">
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Shortcode', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <?php echo __('To display this indget without including it in a scenario, you can use the shortcode', 'itrr_lang') . ' [intrigger indget="' . $post_id . '"]'; ?>
            </div>
            <div class="clearfix"></div>
        <?php
        }
        public static function gasf_inline_custom($post_id) {
            $indgetSetting =ITRR_Manager::$allActiveIndgets;
            $setting_value = isset($indgetSetting[$post_id]) ? $indgetSetting[$post_id]['setting'] : null;
            ?>
            <div class="indget_title_area">
                <b><?php _e('Custom Message', 'itrr_lang'); ?></b><br>
                <?php _e('HTML, CSS, Javascript', 'itrr_lang'); ?>
            </div>
            <div class="indget_field_area">
                <textarea class="indget_field_textarea fullwidth" name="indget_setting[inline][custom][custom_message]"><?php
                    if (isset($setting_value['inline']['custom'])) {
                        echo $setting_value['inline']['custom']['custom_message'];
                    }
                    ?></textarea>
                <p><span class="fa-stack fa-1x"><i class="fa fa-circle-thin fa-stack-2x"></i><i class="fa fa-exclamation fa-stack-1x"></i></span> <?php echo( __('Please use the [CONVERSION] shortcode in order to track conversion. Example: ', 'itrr_lang') . htmlentities('<a href="" onclick="[CONVERSION]">') );?></p>
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Shortcode', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area" id="textline-test">
                <?php echo __('To display this indget without including it in a scenario, you can use the shortcode', 'itrr_lang') . ' [intrigger indget="' . $post_id . '"]'; ?>
            </div>
            <div class="clearfix"></div>
        <?php
        }
        public static function gasf_float_bar_collect_email($post_id) {
            $indgetSetting =ITRR_Manager::$allActiveIndgets;
            $setting_value = isset($indgetSetting[$post_id]) ? $indgetSetting[$post_id]['setting'] : null;
            $main_settings = null;
            if (isset($setting_value['float_bar']['collect_email']) && is_array($setting_value)) {
                $main_settings = $setting_value['float_bar']['collect_email'];
            }

            if (!isset($main_settings) || !is_array($main_settings)) {
                // Default setting.
                $main_settings = self::getInstance()->indget_settings['float_bar-collect_email'];
                $setting_value['float_bar']['collect_email'] = $main_settings;
            }
            $themes = self::getThemeNames('float_bar/collect_email');
            ?>
            <div class="indget_title_area">
                <b><?php _e('Theme', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <select id="con_collect_theme" style="width: 20%;" name="indget_setting[float_bar][collect_email][theme]">
                    <?php
                    foreach ($themes as $theme) {
                        $theme_name = ucwords(str_replace('-', ' ', $theme));
                        ?>
                        <option value="<?php echo $theme; ?>" <?php selected($main_settings['theme'], $theme); ?>><?php echo $theme_name; ?></option>
                    <?php
                    }
                    ?>
                </select><br>
                <span class="fa-stack fa-1x"><i class="fa fa-circle-thin fa-stack-2x"></i><i class="fa fa-exclamation fa-stack-1x"></i></span>&nbsp;&nbsp;
                <?php _e('This indget CSS class is', 'itrr_lang'); ?> "int_indget_float_collect_<span id="con_collect_theme_name"><?php echo $main_settings['theme'];?></span>"
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Preview', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area" style="margin-bottom: 20px;">
                <div id="indget_float_collect_preview" class="indget_preview_img float_preview">
                    <?php
                    $theme_content = self::get_theme_content('float_bar', 'collect_email', $setting_value, $post_id);
                    echo $theme_content;
                    ?>
                    <style>
                        .preview_hidden{
                            display: none;
                        }
                        #float_bar_wrap{
                            display: block;
                        }
                    </style>
                </div>
                <div><a href="javascript:void(0);" id="indget_float_collect_preview_refresh"><i class="fa fa-repeat"></i>&nbsp;<?php _e('Refresh Indget preview', 'itrr_lang'); ?></a> </div>
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Headline', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="text" class="fullwidth" id="indget_float_collect_headline" name="indget_setting[float_bar][collect_email][headline]" value="<?php echo $main_settings['headline']; ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Headline fontsize', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="number" min="10" max="100" id="indget_float_collect_headline_fontsize" name="indget_setting[float_bar][collect_email][headline_fontsize]" value="<?php echo intval($main_settings['headline_fontsize']); ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Input prevalue', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="text" class="fullwidth" id="indget_float_collect_input_preview" name="indget_setting[float_bar][collect_email][input_preview]" value="<?php echo $main_settings['input_preview']; ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Button label', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="text" class="fullwidth" maxlength="30" id="indget_float_collect_button_label"  name="indget_setting[float_bar][collect_email][button_label]" value="<?php echo $main_settings['button_label']; ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Confirmation message', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="text" class="fullwidth" id="indget_float_collect_confirmation_message"  name="indget_setting[float_bar][collect_email][confirmation_message]" value="<?php echo isset($main_settings['confirmation_message']) ? htmlentities($main_settings['confirmation_message']) : ""; //htmlentities?>">
            </div>
            <div class="clearfix"></div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Colors', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Background', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_float_collect_background_color" name="indget_setting[float_bar][collect_email][background_color]" value="<?php echo $main_settings['background_color']; ?>">
                    </div>
                </div>
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Headline Font', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_float_collect_headline_font_color" name="indget_setting[float_bar][collect_email][headline_font_color]" value="<?php echo $main_settings['headline_font_color']; ?>">
                    </div>
                </div>
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Button Background', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_float_collect_button_background_color" name="indget_setting[float_bar][collect_email][button_background_color]" value="<?php echo $main_settings['button_background_color']; ?>">
                    </div>
                </div>
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Button Font', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_float_collect_button_font_color" name="indget_setting[float_bar][collect_email][button_font_color]" value="<?php echo $main_settings['button_font_color']; ?>">
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Shortcode', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <?php echo __('To display this indget without including it in a scenario, you can use the shortcode', 'itrr_lang') . ' [intrigger indget="' . $post_id . '" position="top"]'; ?>
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Contacts', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <?php _e('All contacts generated from this indget are saved in the section "Contacts".', 'itrr_lang'); ?>
            </div>
            <div class="clearfix"></div>
        <?php
        }
        public static function gasf_float_bar_drive_traffic($post_id) {
            $indgetSetting =ITRR_Manager::$allActiveIndgets;
            $setting_value = isset($indgetSetting[$post_id]) ? $indgetSetting[$post_id]['setting'] : null;
            $main_settings = null;
            if (isset($setting_value['float_bar']['drive_traffic']) && is_array($setting_value)) {
                $main_settings = $setting_value['float_bar']['drive_traffic'];
            }

            if (!isset($main_settings) || !is_array($main_settings)) {
                // Default setting.
                $main_settings = self::getInstance()->indget_settings['float_bar-drive_traffic'];
                $setting_value['float_bar']['drive_traffic'] = $main_settings;
            }
            $themes = self::getThemeNames('float_bar/drive_traffic');
            ?>
            <div class="indget_title_area">
                <b><?php _e('Theme', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <select id="con_drive_theme" style="width: 20%;" name="indget_setting[float_bar][drive_traffic][theme]">
                    <?php
                    foreach ($themes as $theme) {
                        $theme_name = ucwords(str_replace('-', ' ', $theme));
                        ?>
                        <option value="<?php echo $theme; ?>" <?php selected($main_settings['theme'], $theme); ?>><?php echo $theme_name; ?></option>
                    <?php
                    }
                    ?>
                </select><br>
                <span class="fa-stack fa-1x"><i class="fa fa-circle-thin fa-stack-2x"></i><i class="fa fa-exclamation fa-stack-1x"></i></span>&nbsp;&nbsp;
                <?php _e('This indget CSS class is', 'itrr_lang'); ?> "int_indget_float_drive_<span id="con_drive_theme_name"><?php echo $main_settings['theme'];?></span>"
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Preview', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <div id="indget_float_drive_preview" class="indget_preview_img float_preview">
                    <?php
                    $theme_content = self::get_theme_content('float_bar', 'drive_traffic', $setting_value, $post_id);
                    echo $theme_content;
                    ?>
                    <style>
                        .preview_hidden{
                            display: none;
                        }
                        #float_bar_wrap{
                            display: block;
                        }
                    </style>
                </div>
                <div><a href="javascript:void(0);" id="indget_float_drive_preview_refresh"><i class="fa fa-repeat"></i>&nbsp;<?php _e('Refresh Indget preview', 'itrr_lang'); ?></a> </div>
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Headline', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="text" class="fullwidth" id="indget_continue_drive_headline" name="indget_setting[float_bar][drive_traffic][headline]" value="<?php echo $main_settings['headline']; ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Headline fontsize', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="number" min="10" max="100" id="indget_continue_drive_headline_fontsize" name="indget_setting[float_bar][drive_traffic][headline_fontsize]" value="<?php echo intval($main_settings['headline_fontsize']); ?>">
            </div>
            <div class="clearfix"></div>

            <div class="indget_title_area">
                <b><?php _e('Button label', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="text" class="fullwidth" id="indget_float_drive_button_label" name="indget_setting[float_bar][drive_traffic][button_label]" maxlength="25" value="<?php echo $main_settings['button_label']; ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Button link', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <input type="url" class="fullwidth" id="indget_float_drive_button_link" name="indget_setting[float_bar][drive_traffic][button_link]" value="<?php echo $main_settings['button_link']; ?>">
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Colors', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Background', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_float_drive_background_color" name="indget_setting[float_bar][drive_traffic][background_color]" value="<?php echo $main_settings['background_color']; ?>">
                    </div>
                </div>
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Headline Font', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_float_drive_headline_font_color" name="indget_setting[float_bar][drive_traffic][headline_font_color]" value="<?php echo $main_settings['headline_font_color']; ?>">
                    </div>
                </div>
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Button Background', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_float_drive_button_background_color" name="indget_setting[float_bar][drive_traffic][button_background_color]" value="<?php echo $main_settings['button_background_color']; ?>">
                    </div>
                </div>
                <div class="indget_field_child">
                    <div class="indget_filed_child_title">
                        <?php _e('Button Font', 'itrr_lang'); ?>
                    </div>
                    <div class="indget_filed_child_ele">
                        <input class="my-color-picker" id="indget_float_drive_button_font_color" name="indget_setting[float_bar][drive_traffic][button_font_color]" value="<?php echo $main_settings['button_font_color']; ?>">
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Shortcode', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area">
                <?php echo __('To display this indget without including it in a scenario, you can use the shortcode', 'itrr_lang') . ' [intrigger indget="' . $post_id . '"]'; ?>
            </div>
            <div class="clearfix"></div>
        <?php
        }
        public static function gasf_float_bar_custom($post_id) {
            $indgetSetting =ITRR_Manager::$allActiveIndgets;
            $setting_value = isset($indgetSetting[$post_id]) ? $indgetSetting[$post_id]['setting'] : null;
            ?>
            <div class="indget_title_area">
                <b><?php _e('Custom Message', 'itrr_lang'); ?></b><br>
                <?php _e('HTML, CSS, Javascript', 'itrr_lang'); ?>
            </div>
            <div class="indget_field_area">
                <textarea class="indget_field_textarea fullwidth" name="indget_setting[float_bar][custom][custom_message]"><?php
                    if (isset($setting_value['float_bar']['custom'])) {
                        echo $setting_value['float_bar']['custom']['custom_message'];
                    }
                    ?></textarea>
                <p><span class="fa-stack fa-1x"><i class="fa fa-circle-thin fa-stack-2x"></i><i class="fa fa-exclamation fa-stack-1x"></i></span> <?php echo( __('Please use the [CONVERSION] shortcode in order to track conversion. Example: ', 'itrr_lang') . htmlentities('<a href="" onclick="[CONVERSION]">') ); ?></p>
            </div>
            <div class="clearfix"></div>
            <div class="indget_title_area">
                <b><?php _e('Shortcode', 'itrr_lang'); ?></b>
            </div>
            <div class="indget_field_area" id="textline-test">
                <?php echo __('To display this indget without including it in a scenario, you can use the shortcode', 'itrr_lang') . ' [intrigger indget="' . $post_id . '" position="top"]'; ?>
            </div>
            <div class="clearfix"></div>
        <?php
        }

        public static function getThemeNames($pattern) {
            $theme_paths = glob(ITRR_Manager::$plugin_dir . 'templates/' . $pattern . '/themes/*.tpl');
            $ret = array();
            foreach ($theme_paths as $theme_path) {
                $theme_name = str_replace(ITRR_Manager::$plugin_dir . 'templates/' . $pattern . '/themes/', '', $theme_path);
                $theme_name = str_replace('.tpl', '', $theme_name);
                $ret[] = $theme_name;
            }
            return $ret;
        }

        /**
         * Get excerpt of content. (include html content too.)
         * @param $content
         * @param $count
         *
         * @return bool|string
         */
        public static function get_excerpt($content, $count){

            $plain_content = strip_shortcodes($content);
            if( strlen($plain_content) <= 1)
                return $plain_content;
            $body_contents = explode('<', $plain_content);
            $pos = 0;
            $except_content = '';
            $tag_stack = array();
            $exist_table = 0;
            foreach($body_contents as $body){
                if($body == '') continue;
                // ex: $body = 'p>hello' or '/p>'
                $html_contents = explode('>', $body);
                $plain_text = $html_contents[1];
                $tag = $html_contents[0];
                // for table tag
                if(strpos($tag , 'table') == true)
                {
                    $exist_table = 1;
                }
                //special tag ex: img,
                if(strpos($tag,'img ') !== false) {
                    $except_content .= "<$tag>";
                    continue;
                }elseif (strpos($tag,'br') !== false && strpos($tag,'br') == 0) {
                    $except_content .= "<br />";
                    continue;
                }elseif(strpos($tag,'hr ') !== false){
                    $except_content .= "<$tag>";
                    continue;
                }else{
                    if (substr($tag,0,1) != '/') {
                        $tag_str = explode(' ', $tag);
                        $tag_stack[] = $tag_str[0];
                    } else {
                        //array_pop($tag_stack);
                        // pop last tag
                        $tag_str = explode('/', $tag);
                        $tag_stack = array_reverse($tag_stack);
                        foreach($tag_stack as $key=>$rt){
                            if($rt == $tag_str[1]) {
                                unset($tag_stack[$key]);
                                // if </table> tag exist
                                if($rt == 'table')
                                {
                                    $exist_table = 0 ;
                                }
                                break;
                            }
                        }
                        $tag_stack = array_reverse($tag_stack);
                    }
                }
                $pos += strlen($plain_text);

                if($pos >= $count && $exist_table == 0){
                    $last_tag = count($tag_stack) - 1;
                    if($tag_stack[$last_tag] == 'h2' || $tag_stack[$last_tag] == 'h3' || $tag_stack[$last_tag] == 'h4'){
                        $rest_text = $plain_text;
                    }else{
                        $rest_text = substr($plain_text,0,$pos-$count);
                    }

                    $except_content .= "<$tag_stack[$last_tag]>$rest_text";
                    $tag_stack = array_reverse($tag_stack, true);

                    // closing tags
                    foreach($tag_stack as $tag){
                        $except_content .= "</$tag>";
                    }
                    break;
                }
                $except_content .= "<$body";
            }
            return $except_content;

            /**/
        }
        public static function get_excerpt_inline($content, $count)
        {
            $the_excerpt = $content;
            $excerpt_length = $count;

            if(strlen($the_excerpt) > $excerpt_length) {
                $the_excerpt = substr($the_excerpt, 0, $excerpt_length);// . ' ...';
            }
            else {
                return false;
            }
            return $the_excerpt;
        }
        /**
         * Remove a solo tag after get except from post
         */
        public static function remove_solo_tag($content, $tag, $count){

            if($count == 0)
                return $content;
            $the_except = explode("<$tag", $content);
            $ret = $the_except[0];
            for($i = 1; $i<$count; $i++){
                $ret .= $the_except[$i]."</$tag" ;
            }
            return $ret;
        }
        /**
         * Generate html for continue-reading indget.
         *
         * @param $indget_id
         * @param $indget_limit
         * @param $post_id
         * @param $content
         * @param $sub_type
         * @param $indget_setting
         *
         * @return string
         */
        public static function get_continue_reading_content($indget_id, $indget_limit, $post_id, $content, $sub_type, $indget_setting) {

            $excerpt_content = self::get_excerpt($content, $indget_limit);
            if ($excerpt_content !== false) {
                $loading_gif_html = '<div id="itrr-loading-gif-' . $post_id . '-' . $indget_id . '" style="position: absolute;z-index: 9999;display: none;">';
                $loading_gif_html .= '<img src="' . ITRR_Manager::$plugin_url . '/asset/img/loading.gif" style="display: block;margin-left: auto;margin-right: auto;position: relative;top: 40%;">';
                $loading_gif_html .= '</div>';

                $excerpt_gradient = '<div id="itrr-continue-excerpt-gradient-' . $post_id . '-' .  $indget_id . '" class="itrr-excerpt-gradient" style="position: absolute; bottom: 0px;width: 100%;"></div>';
                $excerpt_area_html = '<div id="itrr-continue-excerpt-area-' . $post_id . '-' .  $indget_id . '"  class="itrr-excerpt-area" style="position:relative;">' . $excerpt_content . $excerpt_gradient . '</div>';

                if ($sub_type == 'custom') {
                    $custom_message = $indget_setting['continue_reading']['custom']['custom_message'];
                    // Process [Continue] tag.
                    $custom_message = str_replace('"[continue]"', '"javascript:void(0);" itrr-btn="continue" itrr-indget-id="'. $indget_id . '" itrr-post-id="' . $post_id . '" ' . 'itrr_scenario_id="{{scenario_id}}"', $custom_message);
                }
                else {
                    $custom_message = self::get_theme_content('continue_reading', $sub_type, $indget_setting, $indget_id);
                    $custom_message = str_replace('{{post_id}}', $post_id, $custom_message);
                }

                $custom_message = '<div id="intrigger-continue-' . $post_id . '-' . $indget_id . '" class="int-indget" itrr_scenario_id="{{scenario_id}}" itrr_is_applied_scenario="{{is_applied_scenario}}" itrr_is_applied_not_scenario="{{is_applied_not_scenario}}" itrr_post_id="' . $post_id . '" style="margin-bottom: 50px;">' . $custom_message . '</div>';
                $ret_content = $excerpt_area_html . $custom_message;
                $ret_content = '<div id="intrigger-' . $post_id . '-' . $indget_id . '" >' . $ret_content . '</div>';
                $ret_content = $loading_gif_html . $ret_content;
                return $ret_content;
            }
            else {
                return $content;
            }
        }
        /**
         * Generate html for inline indget.
         *
         * @param $indget_id
         * @param $indget_limit
         * @param $post_id
         * @param $content
         * @param $sub_type
         * @param $indget_setting
         *
         * @return string
         */
        public static function get_inline_content($indget_id, $indget_limit, $post_id, $content, $sub_type, $indget_setting) {

            if ($indget_limit != "") {

                if ($sub_type == 'custom') {
                    $custom_message = $indget_setting['inline']['custom']['custom_message'];
                    // Process [Continue] tag.
                    $custom_message = str_replace('"[CONVERSION]"', '"javascript:void(0);" itrr-btn="inline" itrr-indget-id="'. $indget_id . '" itrr-post-id="' . $post_id . '" ' . 'itrr_scenario_id="{{scenario_id}}" target="_blank"', $custom_message);
                }
                else {
                    $custom_message = self::get_theme_content('inline', $sub_type, $indget_setting, $indget_id);
                    $custom_message = str_replace('{{post_id}}', $post_id, $custom_message);
                }

                $indget_message = '<div id="intrigger-inline-' . $post_id . '-' . $indget_id . '" class="int-indget" itrr_scenario_id="{{scenario_id}}" itrr_is_applied_scenario="{{is_applied_scenario}}" itrr_is_applied_not_scenario="{{is_applied_not_scenario}}" itrr_post_id="' . $post_id . '"></div>';

                if($indget_limit == 'middle'){
                    $inline_position = self::get_careful_middle_position($content);
                    $excerpt_content = self::get_excerpt_inline($content, $inline_position);
                    $rest_content = substr($content, $inline_position);
                    $ret_content = $indget_message.$excerpt_content . $custom_message . $rest_content;

                }else if($indget_limit == 'end'){
                    $ret_content = $indget_message.$content . $custom_message;
                }else{
                    // indget is inserted as shortcode
                    $excerpt_content = self::get_excerpt_inline($content, $indget_limit);
                    $rest_content = substr($content, $indget_limit);
                    $ret_content = $indget_message . $excerpt_content . $custom_message . $rest_content;
                }

                return $ret_content;
            }
            else {
                return $content;
            }
        }
        static function get_careful_middle_position($content){
            /*
             * Suppose we have N <h2> in the post
             * If N>2 insert the indget before the ENT((N/2))+1 <h2>
               If N<=2, suppose we have M <h3> in the post (M>2), insert the indget before the ENT((N/2))+1 <h3>
               If M<=2, suppose we have P <p> in the post (P>2), insert the indget before the ENT((N/2))+1 <p>
               If P<2, then insert the widget at the end.
             * */
            $h2_count = substr_count($content, '<h2');
            $h3_count = substr_count($content, '<h3');
            $p_count = substr_count($content, '<p');
            $position = 0;
            if($h2_count > 2){
                //insert the indget before the ENT(N/2)+1<h2>
                $position = self::get_position_tag('<h2', $content);
            }else{
                if($h3_count > 2){
                    //insert the indget before the ENT((M/2))+1 <h3>
                    $position = self::get_position_tag('<h3', $content);
                }else{
                    if($p_count > 2){
                        //insert the indget before the ENT((P/2))+1 <p>
                        $position = self::get_position_tag('<p', $content);
                    }else{
                        // at the end
                        $position = strlen($content);
                    }
                }
            }
            // for <table> tag
            // if the position is in <table> tag then ignore it
            $tables = array();
            if( strpos($content, '<table') )
            {
                $table_starts = explode( '<table' , $content );
                $table_ends = explode( '</table>' , $content);
                $len = count($table_starts);
                $start = $end = 0;
                for($i=0;$i<$len;$i++){
                    $start +=  strlen($table_starts[$i]);
                    $end += strlen($table_ends[$i]);
                    $tables[$i] = array(
                        'start' => $start + $i*6, // "<table"
                        'end' => $end + ($i+1)*8 // "</table>"
                    );
                }
            }
            foreach($tables as $table){
                if($position > $table['start'] && $position < $table['end']){
                    $position = $table['start'];
                }
            }
            return $position;
        }
        static function get_position_tag($tag, $content){
            $content_arr = explode($tag, $content);
            $count = count($content_arr) - 1;
            $index = floor($count / 2) + 1;
            $position = 0;
            for($i = 0; $i<$index; $i++){
                $position += strlen($content_arr[$i]);
                $position += strlen($tag); // </h2>
            }
            $position -= strlen($tag);
            return $position;
        }

        /**
         * Generate html for floating bar indget.
         *
         * @param $indget_id
         * @param $indget_limit
         * @param $post_id
         * @param $content
         * @param $sub_type
         * @param $indget_setting
         *
         * @return string
         */
        public static function get_float_content($indget_id, $pos, $post_id, $sub_type, $indget_setting) {

            if ($pos != "") {

                if ($sub_type == 'custom') {
                    $custom_body = self::get_theme_content('float_bar', $sub_type, $indget_setting, $indget_id);
                    $custom_message = $indget_setting['float_bar']['custom']['custom_message'];
                    $custom_message = str_replace('{{intrigger_float_custom}}',$custom_message,$custom_body);
                    // Process [Continue] tag.
                    $custom_message = str_replace('"[CONVERSION]"', '"javascript:void(0);" itrr-btn="float_bar" itrr-indget-id="'. $indget_id . '" itrr-post-id="' . $post_id . '" ' . 'itrr_scenario_id="{{scenario_id}}"', $custom_message);
                }
                else {
                    $custom_message = self::get_theme_content('float_bar', $sub_type, $indget_setting, $indget_id);
                }
                $custom_message = str_replace('{{post_id}}', $post_id, $custom_message);
                $custom_message = str_replace('{{float_bar_pos}}', 'int_indget_float_'.$pos, $custom_message);
                $custom_message = str_replace('{{float_close_pos}}', 'int_indget_float_close_'.$pos, $custom_message);
                $custom_message = str_replace('{{float_open_pos}}', 'int_indget_float_open_'.$pos, $custom_message);
                $custom_message = str_replace('{{pos}}', $pos, $custom_message);

                $custom_message .= '<div id="intrigger-float-' . $post_id . '-' . $indget_id . '" class="int-indget" itrr_scenario_id="{{scenario_id}}" itrr_is_applied_scenario="{{is_applied_scenario}}" itrr_is_applied_not_scenario="{{is_applied_not_scenario}}" itrr_post_id="' . $post_id . '"></div>';

                if($pos == 'top') {
                    $close_button_url = ITRR_Manager::$plugin_url . '/asset/img/bar-arrow-top.png';
                    $open_button_url = ITRR_Manager::$plugin_url . '/asset/img/bar-arrow-bottom.png';
                }else if($pos == 'bottom') {
                    $close_button_url = ITRR_Manager::$plugin_url . '/asset/img/bar-arrow-bottom.png';
                    $open_button_url = ITRR_Manager::$plugin_url . '/asset/img/bar-arrow-top.png';
                }
                $close_button_img = '<img src="'.$close_button_url.'" alt="Placeholder" width="21" height="23">';
                $open_button_img = '<img src="'.$open_button_url.'" alt="Placeholder" width="24" height="23">';
                $custom_message = str_replace('{{float_bar_close}}', $close_button_img, $custom_message);
                $custom_message = str_replace('{{float_bar_open}}', $open_button_img, $custom_message);
                return $custom_message;
            }
        }
        /**
         * Generate theme content of indget.
         *
         * @param $main_type
         * @param $sub_type
         * @param $indget_setting
         *
         * @return string
         */
        public static function get_theme_content($main_type, $sub_type, $indget_setting, $indget_id) {
            // 1. Get file content of indget theme.
            if ($sub_type == 'custom' && $main_type != 'float_bar') {
                $file_content = $indget_setting[$main_type][$sub_type]['custom_message'];
                return $file_content;
            }else if($sub_type == 'custom' && $main_type == 'float_bar'){
                $file_path = ITRR_Manager::$plugin_dir . 'templates/float_bar/custom/themes/default.tpl';
                $file_content = file_get_contents($file_path);
                $content = $indget_setting['float_bar']['custom']['custom_message'];
                $file_content = str_replace('{{intrigger_float_custom}}', $content, $file_content);
            }else {
                $theme_id = $indget_setting[$main_type][$sub_type]['theme'];
                $file_path = ITRR_Manager::$plugin_dir . 'templates/' . $main_type . '/' . $sub_type . '/themes/' . $theme_id . '.tpl';
                $file_content = file_get_contents($file_path);
            }
            // 2. Apply params into raw content.
            $theme_setting = $indget_setting[$main_type][$sub_type];
            foreach ($theme_setting as $key => $value) {
                $file_content = str_replace('{{' . $key . '}}', $value, $file_content);
            }
            $file_content = str_replace('{{indget_id}}', $indget_id, $file_content);
            //for brand icon/url
            $set_brand = get_option('itrr_setting_branding' ,'0');
            $background_image_url =  ITRR_Manager::$plugin_url . '/asset/img/int-logo-white-small.png';
            $brand_url_bar = "http://intriggerapp.com/?utm_source=intrigger&utm_medium=referral&utm_campaign=RE_powered-by&utm_content=bar";
            $brand_url = "http://intriggerapp.com/?utm_source=intrigger&utm_medium=referral&utm_campaign=RE_powered-by&utm_content=box";
            // hide or show the brand url/icon
            if( $set_brand == '1')
            {
                $file_content = str_replace('{{show_brand}}' , 'none' , $file_content);
            }
            else
            {
                $file_content = str_replace('{{show_brand}}' , 'block' , $file_content);
            }
            if( $main_type == "float_bar" && $sub_type !="custom")
            {
                $file_content = str_replace('{{branding_image_url}}' , $background_image_url , $file_content);
                $file_content = str_replace('{{branding_url}}' , $brand_url_bar , $file_content);
            }
            else if( $main_type != "float_bar" && $sub_type != "custom" )
            {
                $file_content = str_replace('{{brand_url}}' , $brand_url , $file_content);
                $file_content = str_replace('{{brand_title}}' , 'Discover InTrigger plugin and boost your conversion!' , $file_content);
            }
            return $file_content;

        }
    }
}
