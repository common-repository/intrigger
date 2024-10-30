<?php
/**
 * Admin page : dashboard
 * @package ITRR_Page_Setting
 */

/**
 * Page class that handles backend page <i>dashboard ( for admin )</i> with form generation and processing
 * @package ITRR_Page_Setting
 */

if (!class_exists('ITRR_Page_Setting')) {
  class ITRR_Page_Setting {
    /**
     * Page slug
     */
    const page_id = 'itrr_page_setting';

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
       * contact_setting
       */
      private $contacts_setting;
      /**
       * Cache friendly
       */
      private $cache_friendly;
      /**
       * Activated Integration
       */
      private $activated_int;
      /**
       * Sendinblue account info
       */
      private $sib_info;
      private $sib_lists;
      private $sib_contacts;
      private $sib_logged;
      /**
       * Mailchimp account info
       */
      private $mcp_info;
      private $mcp_lists;
      private $mcp_us;
      private $mcp_logged;


    /**
     * Constructs new page object and adds entry to Wordpress admin menu
     */
    function __construct() {
      $this->page_hook = add_submenu_page(ITRR_Page_Home::page_id, __('Settings', 'itrr_lang'), __('Settings', 'itrr_lang'), 'manage_options', self::page_id, array(&$this, 'generate'));
      add_action('load-'.$this->page_hook, array($this, 'init'));
      add_action('admin_print_scripts-' . $this->page_hook, array($this, 'enqueue_scripts'));
      add_action('admin_print_styles-' . $this->page_hook, array($this, 'enqueue_styles'));
      add_action( 'admin_init', array($this,'register_my_settings') );
    }

    /**
     * Init Process
     */
    function init() {

        /* Sendinblue */
        $sib_key = get_option('itrr_sib_access_key') != false ? get_option('itrr_sib_access_key') : '';
        $this->sib_logged = $sib_key == '' ? false : true;
        if ($this->sib_logged) {
            $sib_collection = get_transient('itrr_sib_collection');

            if (!$sib_collection) {
                $sib_collection = ITRR_Sendinblue::sib_get_data($sib_key);
                set_transient('itrr_sib_collection', $sib_collection, 12 * HOUR_IN_SECONDS);
            }
            $this->sib_info = $sib_collection['info'];
            $this->sib_lists = $sib_collection['lists'];
            $this->sib_contacts = $sib_collection['contacts'];
        }

        /* Mailchimp */
        $mcp_key = get_option('itrr_mcp_access_key') != false ? get_option('itrr_mcp_access_key') : '';
        $this->mcp_logged = $mcp_key == '' ? false : true;
        if ($this->mcp_logged) {
            $mcp_collection = get_transient('itrr_mcp_collection');

            if (!$mcp_collection) {
                $mcp_collection = ITRR_Mailchimp::mcp_get_data($mcp_key);
                set_transient('itrr_mcp_collection', $mcp_collection, 12 * HOUR_IN_SECONDS);
            }
            $this->mcp_info = $mcp_collection['info'];
            $this->mcp_lists = $mcp_collection['lists'];
            $this->mcp_us = $mcp_collection['us'];
        }

        $this->activated_int = (get_option('itrr_activated_integration') != false || get_option('itrr_activated_integration') != '') ? get_option('itrr_activated_integration') : 'sib'; // sib or mcp
    }

    /**
     * enqueue scripts of plugin
     */
    function enqueue_scripts() {

    }

    /**
     * enqueue style sheets of plugin
     */
    function enqueue_styles() {
        wp_enqueue_style('itrr-other-css', ITRR_MANAGER::$plugin_url . '/asset/css/admin-other.css', array(), filemtime( ITRR_MANAGER::$plugin_dir . '/asset/css/admin-other.css'));
        wp_enqueue_style('itrr-indget-fontawesome-css', ITRR_Manager::$plugin_url . '/asset/css/fontawesome/css/font-awesome.min.css');
    }
      function register_my_settings() {
          //register our settings
          register_setting( 'plugin-settings-group', 'itrr_activation_key' );
          register_setting( 'plugin-settings-group', 'itrr_setting_contacts' );
		  register_setting( 'plugin-settings-group', 'itrr_setting_seo_continue');
          register_setting( 'plugin-settings-group', 'itrr_setting_branding');
          register_setting( 'plugin-settings-group', 'itrr_setting_cache' );
          register_setting( 'plugin-settings-group', 'itrr_contact_sib_list' );
          register_setting( 'plugin-settings-group', 'itrr_contact_mcp_list' );
      }
    /** generate page script */
    function generate() {
?>

        <form method="post" action="options.php">
            <?php settings_fields( 'plugin-settings-group' ); ?>
            <?php do_settings_sections( 'plugin-settings-group' ); ?>
        <div class="wrap itrr_support_table">
        <h2 class="itrr_settings_label"><?php _e('Settings','itrr_lang'); ?>
            <input type="submit" class="itrr_button top_button" style="float: right;" value="<?php _e('Save settings','itrr_lang');?>"> </h2>

        <table class="widefat fixed itrr_general_table">
            <thead>
            <tr>
                <th scope="col" class=""><?php _e('General', 'itrr_lang'); ?></th>
            </tr>
            </thead>
            <tbody class="">
            <tr class="">
                <td class="itrr_setting_content">
                    <div class="itrr_setting_">
                        <div class="itrr_setting_title"><?php _e('Contacts','itrr_lang'); ?></div>
                        <div class="itrr_setting_field">
                            <input type="checkbox" class = "itrr_input" name = "itrr_setting_contacts" value = "1" <?php checked(true, get_option('itrr_setting_contacts') != false ? "1" : get_option('itrr_setting_contacts') ); ?> />
                            <span><?php _e('Contacts are automatically deduped with last connection details.','itrr_lang');?></span>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="itrr_setting_" id="itrr_setting_branding">
                        <div class="itrr_setting_title"><?php _e('Branding' , 'itrr_lang'); ?></div>
                        <div class="itrr_setting_field">
                            <input type="checkbox" class="itrr_input" name="itrr_setting_branding" value="1" <?php checked(true, get_option('itrr_setting_branding') != false ? "1" : get_option('itrr_setting_branding'));?>/>
                            <span><?php _e('Hide the "Powered by InTrigger" link in default indgets' , 'itrr_lang');?></span>
                        </div>
                    </div>

				    <div class = "itrr_setting_" id = "itrr_setting_bot_allow">
                        <div class = "itrr_setting_title"><?php _e('SEO','itrr_lang'); ?></div>
                        <div class = "itrr_setting_field">
                            <input type = "checkbox" class = "itrr_input" name = "itrr_setting_seo_continue" value = "1" <?php checked(true, get_option('itrr_setting_seo_continue') != false ? "1" : get_option('itrr_setting_seo_continue') ); ?> />
                            <span><?php _e('Do not apply Continue scenarios on Search Engine bots.','itrr_lang');?></span>
                        </div>
                    </div>
                    <div class="itrr_setting_" id="itrr_setting_clear">
                        <div class="itrr_setting_title"><?php _e('Cleaning' , 'itrr_lang');?></div>
                        <div class="itrr_setting_field">
                            <input type ="button"  class ="itrr_clear_setting button" name ="itrr_clear_setting" id="itrr_clear_setting" value="<?php _e('Delete Plugin Settings, Contacts & Scenarios', 'itrr_lang'); ?>"/>
                        </div>
                        <div class="itrr_setting_title">&nbsp;</div>
                        <div class="itrr_setting_field" id="itrr_clear_setting_panel" style="display: none">
                            <div class="itrr_setting_confirm_title">
                                <p><b><?php _e('Comfirmation','itrr_lang');?></b></p>
                            </div>
                            <div class="itrr_setting_confirm_message">
                                <p><?php _e('Are you sure that you want to delete completely InTrigger contacts, scenarios and settings?','itrr_lang');?></p>
                            </div>
                            <div class="itrr_setting_confirm_buttons">
                                <button id="itrr_setting_confirm_yes" class="itrr_button"><?php _e('Yes' , 'itrr_lang');?></button>
                                <button id="itrr_setting_confirm_cancel" class="itrr_button"><?php _e('No' , 'itrr_lang'); ?></button>
                            </div>
                        </div>
                        <div id="itrr_setting_clear_success_message" class="itrr_setting_field" style="display: none;">
                            <p><?php _e('All plugin settings are successfully removed.' , 'itrr_lang'); ?></p>
                        </div>
                        <div id="itrr_setting_clear_fail_message" class="itrr_setting_field" style="display: none;">
                            <p><?php _e("The plugin settings can't be removed." ,"itrr_lang"); ?></p>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>

        <!-- integration -->
        <br><br>
        <table class="widefat fixed itrr_integration_table">
            <thead>
            <tr>
                <th scope="col" class=""><?php _e('Integration', 'itrr_lang'); ?></th>
            </tr>
            </thead>
            <tbody class="">
            <tr class="">
                <td class="itrr_setting_content">
                    <div class="">
                        <div class="itrr_setting_integration">
                            <!-- SendinBlue -->
                            <h2 class="itrr_sib_tab"><span class="itrr_arrow <?php echo $this->activated_int == 'sib' ? 'open' : 'close'; ?>"></span>
                                <?php _e('Sendinblue','itrr_lang'); ?></h2>
                            <div class="itrr_setting_content itrr_sendinblue_content" style="display: <?php echo $this->activated_int == 'sib' ? 'block;' : 'none;'; ?>">
                                <div class="itrr_setting_left">
                                    <?php if( !$this->sib_logged ){?>

                                        <h3><?php _e('Create a free SendinBlue account','itrr_lang'); ?></h3>
                                        <div class="itrr_setting_text">
                                            <?php _e('Sign up for free and send up to 9,000 emails/month. <br> SendinBlue is one of the best all-in-one marketing platform.','itrr_lang'); ?><br><br>

                                            <p><span class="dashicons dashicons-yes"></span><?php _e('Send email newsletters, transactional messages and even text messages','itrr_lang'); ?></p>
                                            <p><span class="dashicons dashicons-yes"></span><?php _e('Collect and manage your contacts with powerful segmentation','itrr_lang'); ?></p>
                                            <p><span class="dashicons dashicons-yes"></span><?php _e('Configure scenarios targeting specific contact behaviors','itrr_lang'); ?></p>
                                            <p><span class="dashicons dashicons-yes"></span><?php _e('Build your emails easily with the responsive design builder','itrr_lang'); ?></p>

                                            <p><span class="dashicons dashicons-arrow-right-alt2"></span><a href="https://www.sendinblue.com/features/?ae=206" target="_blank"><?php _e('See all features','itrr_lang'); ?></a></p>
                                            <p><span class="dashicons dashicons-arrow-right-alt2"></span><a href="https://www.sendinblue.com/pricing/?ae=206" target="_blank"><?php _e('See pricing for more than 9,000 emails/month','itrr_lang'); ?></a></p>
                                        </div>
                                        <h3><?php _e('Synchronization','itrr_lang'); ?></h3>
                                        <div class="itrr_setting_text">
                                            <p><?php _e('To active contact\'s synchronization with SendinBlue, please enter your API Access key:','itrr_lang'); ?></p>
                                            <input type="text" placeholder="API key" name="itrr_sib_access_key" class="itrr_input itrr_sendinblue_access_key" value="<?php ?>" />
                                            <i id="" class="fa fa-cog fa-spin fa-2x itrr_loading_bar" style="display:none; vertical-align: top;color: #808080;"></i>
                                            <a href="javascript:void(0)" id="itrr_sib_login_button" class="itrr_button" style="margin-left: 12px" ><?php _e('Validate','itrr_lang');?></a>
                                            <br>
                                            <a href="https://my.sendinblue.com/advanced/apikey/?utm_source=wordpress_plugin&amp;utm_medium=plugin&amp;utm_campaign=module_link" target="_blank"><span class="dashicons dashicons-arrow-right-alt2"></span><?php _e('Get the API key from my SendinBlue account','itrr_lang'); ?></a>
                                        </div>
                                    <?php } else {
                                        $account_info = $this->sib_info;
                                        $count = count($account_info['data']);
                                        $sib_acc_name = $account_info['data'][$count - 1]['first_name'] . ' ' . $account_info['data'][$count - 1]['last_name'];
                                        $sib_acc_email = $account_info['data'][$count - 1]['email'];

                                        ?>
                                        <h3><?php _e('You are currently logged as','itrr_lang'); ?></h3>
                                        <div class="itrr_setting_text">
                                            <?php echo $sib_acc_name.' - '.$sib_acc_email;?><br><br>
                                            <p><span class="dashicons dashicons-arrow-right-alt2"></span><a href="<?php echo esc_url(add_query_arg('itrr_sib_action', 'logout')); ?>" ><?php _e('Logout','itrr_lang'); ?></a></p>
                                        </div>
                                        <br>
                                        <h3><?php _e('Contacts','itrr_lang'); ?></h3>
                                        <div class="itrr_setting_text">
                                            <span><?php _e('Contacts from plugin are automatically saved in the list','itrr_lang'); ?></span>
                                            <select name="itrr_contact_sib_list" id="itrr_contact_list" ><!--<option value="0">Select the list...</option>-->
                                                <?php
                                                $lists = $this->sib_lists;
                                                echo '<option '.selected('-1',esc_attr( get_option('itrr_contact_sib_list'))).'value="-1">'.__('--please select a list--','itrr_lang').'</option>';
                                                foreach( $lists['data'] as $list){
                                                    echo '<option '.selected($list['id'],esc_attr( get_option('itrr_contact_sib_list'))).'value="'.$list['id'].'">'.$list['name'].'</option>';
                                                }
                                                ?>
                                            </select>
                                            <input type="submit" id="itrr_sib_confirm_button" class="itrr_green_button" value="<?php _e('Confirm','itrr_lang');?>" />
                                            <input type="hidden" placeholder="API key" name="itrr_sib_access_key" class="" value="<?php echo esc_attr( get_option('itrr_sib_access_key')); ?>" />
                                            <p><?php echo sprintf(__('In total, you have %s contacts saved in SendinBlue','itrr_lang'), $this->sib_contacts); ?></p>
                                            <a href="https://my.sendinblue.com/lists" target="_blank"><span class="dashicons dashicons-arrow-right-alt2"></span><?php _e('See my list of contacts in SendinBlue','itrr_lang'); ?></a>
                                        </div>
                                    <?php } ?>
                                </div>

                                <div class="itrr_setting_right" style="display: <?php echo $this->sib_logged ? 'none' : 'inline-block';?>">
                                    <div style="text-align: center;">
                                        <a href="https://www.sendinblue.com/users/signup/?ae=206" id="itrr_signup_button" target="_blank"><?php _e('Sign up for free','itrr_lang');?></a>
                                        <p><?php _e('No credit card, No commitment <br> Up to 9,000 emails per month, free of charge.','itrr_lang');?></p>
                                    </div>
                                </div>
                            </div>

                        <br>
                        <!-- Mailchimp -->
                        <h2 class="itrr_mcp_tab"><span class="itrr_arrow <?php echo $this->activated_int == 'mcp' ? 'open' : 'close'; ?>"></span>
                            <?php _e('Mailchimp','itrr_lang'); ?></h2>
                        <div class="itrr_setting_content itrr_mcp_content" style="display: <?php echo $this->activated_int == 'mcp' ? 'block;' : 'none;'; ?>">
                            <div class="itrr_setting_left">
                            <?php if( !$this->mcp_logged ){?>
                                <div class="itrr_setting_text">
                                    <p><?php _e('To active contact\'s synchronization with Mailchimp, please enter your API Access key:','itrr_lang'); ?></p>
                                    <input type="text" placeholder="API key" name="itrr_mcp_access_key" class="itrr_input itrr_mailchimp_access_key" value="<?php ?>" />
                                    <i id="" class="fa fa-cog fa-spin fa-2x itrr_loading_bar" style="display:none; vertical-align: top;color: #808080;"></i>
                                    <a href="javascript:void(0)" id="itrr_mcp_login_button" class="itrr_button" style="margin-left: 12px" ><?php _e('Validate','itrr_lang');?></a>
                                    <br>
                                    <a href="https://us3.admin.mailchimp.com/account/api/" target="_blank"><span class="dashicons dashicons-arrow-right-alt2"></span><?php _e('Get the API key from my Mailchimp account','itrr_lang'); ?></a>
                                </div>
                            <?php } else {
                                $account_info = $this->mcp_info;
                                $mcp_acc_name = $account_info['name'];
                                $mcp_acc_email = $account_info['email'];

                                ?>
                                <h3><?php _e('You are currently logged as','itrr_lang'); ?></h3>
                                <div class="itrr_setting_text">
                                    <?php echo $mcp_acc_name.' - '.$mcp_acc_email;?><br>
                                    <p><span class="dashicons dashicons-arrow-right-alt2"></span><a href="<?php echo esc_url(add_query_arg('itrr_mcp_action', 'logout')); ?>" ><?php _e('Logout','itrr_lang'); ?></a></p>
                                </div>

                                <h3><?php _e('Contacts','itrr_lang'); ?></h3>
                                <div class="itrr_setting_text">
                                    <span><?php _e('Contacts from plugin are automatically saved in the list','itrr_lang'); ?></span>
                                    <select name="itrr_contact_mcp_list" id="itrr_contact_list" >
                                        <?php
                                        echo '<option '.selected('-1',esc_attr( get_option('itrr_contact_mcp_list'))).'value="-1">'.__('-- please select a list--','itrr_lang').'</option>';
                                        $lists = $this->mcp_lists;
                                        foreach( $lists['data'] as $list){
                                            echo '<option '.selected($list['id'],esc_attr( get_option('itrr_contact_mcp_list'))).'value="'.$list['id'].'">'.$list['name'].'</option>';
                                        }
                                        ?>
                                    </select>
                                    <!--<a href="javascript:void(0)" id="itrr_mcp_confirm_button" class="itrr_green_button" style="margin-left: 12px" ><?php /*_e('Confirm','itrr_lang');*/?></a>-->
                                    <input type="submit" id="itrr_mcp_confirm_button"  class="itrr_green_button" value="<?php _e('Confirm','itrr_lang');?>" />
                                    <input type="hidden" placeholder="API key" name="itrr_mcp_access_key" class="" value="<?php echo esc_attr( get_option('itrr_mcp_access_key')); ?>" /><br>
                                    <a href="https://<?php echo $this->mcp_us; ?>.admin.mailchimp.com/lists/" target="_blank"><span class="dashicons dashicons-arrow-right-alt2"></span><?php _e('See my list of contacts in Mailchimp','itrr_lang'); ?></a>
                                </div>
                            <?php } ?>
                            </div>
                            <div class="itrr_setting_right" style="display: <?php echo $this->mcp_logged ? 'none' : 'inline-block';?>">
                                
                            </div>
                        </div>
                        </div>
                        <input type="hidden" id="itrr_setting_ajax_nonce" name="itrr_setting_ajax_nonce" value="<?php echo (wp_create_nonce('itrr_setting_ajax_nonce'));?>"
                        <br>
                        <!-- Other -->
                        <div class="itrr_setting_integration">
                        <h2><span class="itrr_arrow close"></span>
                            <?php _e('Other','itrr_lang'); ?></h2>
                        <div class="itrr_setting_content itrr_setting_text itrr_other_content" style="display: none;">
                            <?php _e('If you need to synchronize your contacts with another tool, please send us an email to ','itrr_lang'); ?><a href="mailto:contact@intriggerapp.com">contact@intriggerapp.com</a>
                        </div>
                        </div>

                    </div>

                </td>
            </tr>
            </tbody>
        </table><br>
            <input type="submit" class="itrr_button" style="float: right;" value="<?php _e('Save settings','itrr_lang');?>" />
        </div>
        </form>
        <script type="text/javascript" >

            jQuery(function() {
                jQuery('.itrr_setting_integration').find('h2').click(function (e) {
                    var wrap = jQuery(this).closest('.itrr_setting_integration');
                    if (jQuery(this).children('span').hasClass('close')) {
                        wrap.find('span').removeClass('open').addClass('close');
                        wrap.find('.itrr_setting_content').hide();
                        jQuery(this).next('.itrr_setting_content').show();
                        jQuery(this).children('span').removeClass('close').addClass('open');
                    }else{
                        wrap.find('span').removeClass('close').addClass('open');
                        wrap.find('.itrr_setting_content').show();
                        jQuery(this).next('.itrr_setting_content').hide();
                        jQuery(this).children('span').removeClass('open').addClass('close');
                    }
                });
            });
        </script>
    <?php
        }
      /** sib_logout process */
      public static function sib_logout()
      {
          update_option('itrr_sib_access_key', '');
          update_option('itrr_contact_sib_list', '-1');
          $mcp_key = get_option('itrr_mcp_access_key') != false ? get_option('itrr_mcp_access_key') : '';
          $activated_int = $mcp_key == '' ? '' : 'mcp';
          update_option('itrr_activated_integration', $activated_int);
          delete_transient('itrr_sib_collection');
          wp_redirect(add_query_arg('page', 'itrr_page_setting', admin_url('admin.php')));
          exit;
      }
      /** mcp_logout process */
      public static function mcp_logout()
      {
          update_option('itrr_mcp_access_key', '');
          update_option('itrr_contact_mcp_list', '');
          $sib_key = get_option('itrr_sib_access_key') != false ? get_option('itrr_sib_access_key') : '';
          $activated_int = $sib_key == '' ? '' : 'sib';
          update_option('itrr_activated_integration', $activated_int);
          delete_transient('itrr_mcp_collection');
          wp_redirect(add_query_arg('page', 'itrr_page_setting', admin_url('admin.php')));
          exit;
      }
      public static function clear_plugin_settings()
      {
          check_ajax_referer('itrr_setting_ajax_nonce' , 'security');
          if(isset($_POST['clear']) && $_POST['clear'] == 'yes')
          {
             // delete all plugin settings
              global $wpdb;
              delete_option('itrr_contact_mcp_list');
              delete_option('itrr_contact_sib_list');
              delete_option('itrr_default_indgets');
              delete_option('itrr_setting_cache');
              delete_option('itrr_mcp_access_key');
              delete_option('itrr_sib_access_key');
              delete_option('itrr_activated_integration');
              delete_option('itrr_post_triggered');
              delete_option('itrr_setting_branding');
              update_option('itrr_all_indgets', array());
              update_option('itrr_all_scenarios', array('active' => array(), 'settings' => array()));
              delete_transient('itrr_sib_collection');
              delete_transient('itrr_mcp_collection');
              update_option('itrr_setting_seo_continue',true);
              update_option('itrr_setting_contacts' , true);
              update_option('itrr_setting_branding' , true);
              // delete all indgets and metadata
              $indgets = get_posts( array('post_type' => 'itrr_indget' , 'posts_per_page' => -1 , 'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash')));
              foreach ($indgets as $indget)
              {
                  wp_delete_post($indget->ID, true) ;
                  delete_post_meta($indget->ID , ITRR_Indget::cst_post_type);
                  delete_post_meta( $indget->ID , ITRR_Indget::cst_subtype);
                  delete_post_meta($indget->ID , ITRR_Indget::cst_setting_data);
                  delete_post_meta($indget->ID , ITRR_Indget::cst_thumb_data);
              }
              //delete all scenarios and metadata
              $scenarios = get_posts( array ( 'post_type' => 'itrr_scenario' , 'posts_per_page' => -1 , 'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash')));
              foreach ( $scenarios as $scenario )
              {
                  wp_delete_post( $scenario -> ID , true) ;
                  delete_post_meta($scenario -> ID , ITRR_Scenario::cst_setting_indget);
                  delete_post_meta($scenario -> ID , ITRR_Scenario::cst_post_type);
                  delete_post_meta($scenario -> ID , ITRR_Scenario::cst_setting_rule);
              }
              // clear trigger stats and contacts table
              ITRR_Stats::clear_table();
              ITRR_Contacts::clear_table();
              // delete itrr_another_apply setting from each pages and posts
              $query = "DELETE FROM ".$wpdb->prefix."postmeta  WHERE meta_key = 'itrr_another_apply'";
              $wpdb->query($query);
              $query = "DELETE FROM ".$wpdb->prefix."usermeta WHERE meta_key LIKE 'itrr_post_triggered_%'";
              $wpdb->query($query);
              echo('success');
              die();
          }
      }
  }
}

