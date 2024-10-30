<?php
/**
 * Admin page : dashboard
 * @package ITRR_Page_Support
 */

/**
 * Page class that handles backend page <i>dashboard ( for admin )</i> with form generation and processing
 * @package ITRR_Page_Support
 */

if (!class_exists('ITRR_Page_Support')) {
  class ITRR_Page_Support {
    /**
     * Page slug
     */
    const page_id = 'itrr_page_support';

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
     * Constructs new page object and adds entry to Wordpress admin menu
     */
    function __construct() {
      $this->page_hook = add_submenu_page(ITRR_Page_Home::page_id, __('Support', 'itrr_lang'), __('Support', 'itrr_lang'), 'manage_options', self::page_id, array(&$this, 'generate'));
      add_action('load-'.$this->page_hook, array($this, 'init'));
      add_action('admin_print_scripts-' . $this->page_hook, array($this, 'enqueue_scripts'));
      add_action('admin_print_styles-' . $this->page_hook, array($this, 'enqueue_styles'));

    }

    /**
     * Init Process
     */
    function init() {

    }

    /**
     * enqueue scripts of plugin
     */
    function enqueue_scripts() {
        wp_enqueue_script('itrr-indget-angular-js', ITRR_Manager::$plugin_url . '/asset/js/angular.min.js');
    }

    /**
     * enqueue style sheets of plugin
     */
    function enqueue_styles() {
        wp_enqueue_style('itrr-other-css', ITRR_MANAGER::$plugin_url . '/asset/css/admin-other.css', array(), filemtime( ITRR_MANAGER::$plugin_dir . '/asset/css/admin-other.css'));
        wp_enqueue_style('itrr-indget-fontawesome-css', ITRR_Manager::$plugin_url . '/asset/css/fontawesome/css/font-awesome.min.css');
    }

    /** generate page script */
    function generate() {
?>
        <div class="wrap">
        <h2><?php echo __('Support', 'itrr_lang'); ?></h2>

        <table class="itrr_support_table" style="padding-top: 24px;">
            <tbody>
            <tr>
                <td>
                    <table class="widefat fixed itrr_faq_table">
                        <thead>
                        <tr>
                            <th scope="col" class=""><?php _e('FAQ/Common questions', 'itrr_lang'); ?></th>
                        </tr>
                        </thead>
                        <tbody class="">
                        <tr class="">
                            <td id="itrr_faqs">

                                <!--fa-caret-right-->
                                <li class="itrr_answer"><span class="itrr_arrow close"></span>&nbsp;<?php _e('How does it work?','itrr_lang'); ?></li>
                                <p><?php _e('The plugin allow you to create scenarios that display an indget when some conditions are met. For example, thanks to "Continue" scenario, you can display an email form inviting readers to subscribe for your newsletter in order to read full post.','itrr_lang'); ?></p>
                                <li class="itrr_answer"><span class="itrr_arrow close"></span>&nbsp;<?php _e('Will scenarios make my site load slower?','itrr_lang'); ?></li>
                                <p><?php _e('"No. When we add content to your site, we are doing it via JavaScript after the page loads. This means that all of your primary content will be loaded first and InTrigger will be triggered shortly after the page actually loads."','itrr_lang'); ?></p>
                                <li class="itrr_answer"><span class="itrr_arrow close"></span>&nbsp;<?php _e('Can I use an indget without a scenario?','itrr_lang'); ?></li>
                                <p><?php _e('Yes. To display anindget without including it in a scenario, you can use the shortcode [intrigger indget="XXX"] where XXX is the indget ID. You will find the ID and more details about the shortcode at the bottom of the indget Edit page.','itrr_lang'); ?></p>
                                <li class="itrr_answer"><span class="itrr_arrow close"></span>&nbsp;<?php _e('Can I apply several scenarios on the same page?','itrr_lang'); ?></li>
                                <p><?php _e('Yes, but only with different scenarios types. For example, you can apply a "Floating Bar" scenario and an "Inline" scenario on the same page, but you cannot apply two "Inline" scenarios. When you apply several scenarios with the same type on a page, only the lowest id scenario will apply.','itrr_lang'); ?></p>
                                <li class="itrr_answer"><span class="itrr_arrow close"></span>&nbsp;<?php _e('How do I add custom CSS for indgets?','itrr_lang'); ?></li>
                                <p><?php _e('There are two ways for customizing indgets. You can either create a custom indget and insert your own HTML / CSS, or use a template indget and apply your changes to the appropriate CSS class. For example, if you want to modify the template indget "Inline - Collect" with the default theme, you should use the class "int_indget_inline_collect_default".','itrr_lang'); ?></p>
                                <li class="itrr_answer"><span class="itrr_arrow close"></span>&nbsp;<?php _e('I can\'t find a way to do X...','itrr_lang'); ?></li>
                                <p><?php _e('The plugin is actively developed. If you can\'t find your favorite feature (or have a suggestion) contact us. We\'d love to hear from you.','itrr_lang'); ?></p>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td>
                    <table class="widefat fixed itrr_contact_table">
                        <thead>
                        <tr>
                            <th scope="col" class=""><?php _e('Contact us for any question', 'itrr_lang'); ?></th>
                        </tr>
                        </thead>
                        <tbody class="">
                        <tr class="">
                            <td class="itrr_contact_content">
                                <h3 style="margin-top: 0px;"><?php _e('We will be happy to help you for any question.', 'itrr_lang'); ?></h3>
                                <p><?php _e('Your email address', 'itrr_lang'); ?></p>
                                <input type="email" id="itrr_contact_email" class="itrr_input" required><br><br>
                                <p><?php _e('Your message', 'itrr_lang'); ?></p>
                                <textarea class="itrr_contact_msg"></textarea>
                                <input type="submit" name="send" id="itrr_contact_send" class="itrr_button <!--button button-primary-->" value="Send">
                                <input type="hidden" name="itrr_support_ajax_nonce" id="itrr_support_ajax_nonce" value="<?php echo(wp_create_nonce('itrr_support_ajax_nonce')); ?>"
                                <div id="success-alert" class="itrr_contact_success" style="display: none;">
                                    <?php _e('Your message has been sent. We will answer you very soon.', 'itrr_lang'); ?>
                                </div>
                                <div id="failure-alert" class="itrr_contact_fail" style="display: none;">
                                    <?php _e('Please fulfill all fields before sending the message.', 'itrr_lang'); ?>
                                </div>
                                <div id="curl_failure-alert" class="itrr_contact_fail" style="display: none;">
                                    <?php _e('To send email, you need to install curl.', 'itrr_lang'); ?>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table style>
        </div>
        <script type="text/javascript" >
            jQuery(function() {
                jQuery('#itrr_faqs').find('li').each(function (e) {
                    jQuery(this).next('p').css('display','none');

                });
                jQuery('#itrr_faqs').find('li').click(function (e) {
                    jQuery(this).next('p').toggle();
                    if (!jQuery(this).find('span').hasClass('open')) {
                        jQuery(this).find('span').removeClass('close').addClass('open');
                    }else{
                        jQuery(this).find('span').removeClass('open').addClass('close');
                    }
                });
            });
        </script>
<?php
    }
  }
}
