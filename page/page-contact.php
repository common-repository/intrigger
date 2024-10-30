<?php
/**
 * Admin page : dashboard
 * @package ITRR_Page_Contacts
 */

/**
 * Page class that handles backend page <i>dashboard ( for admin )</i> with form generation and processing
 * @package ITRR_Page_Contacts
 */

if (!class_exists('ITRR_Page_Contacts')) {
  class ITRR_Page_Contacts{
    /**
     * Page slug
     */
    const page_id = 'itrr_page_contact';

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

    // customer WP_List_Table object
    public $contacts;

    /**
     * Constructs new page object and adds entry to Wordpress admin menu
     */
    function __construct() {
      $this->page_hook = add_submenu_page(ITRR_Page_Home::page_id, __('Contacts', 'itrr_lang'), __('Contacts', 'itrr_lang'), 'manage_options', self::page_id, array(&$this, 'generate'));
      add_submenu_page(ITRR_Page_Home::page_id . '-hidden', __('Download','itrr_lang'), __('Download','itrr_lang'), 'manage_options', 'csv-download', array( $this, 'csv_download' ) );
      add_action('load-'.$this->page_hook, array($this, 'init'));
      add_action('admin_print_scripts-' . $this->page_hook, array($this, 'enqueue_scripts'));
      add_action('admin_print_styles-' . $this->page_hook, array($this, 'enqueue_styles'));
    }

    /**
     * Init Process
     */
    function init() {
      $this->contacts = new ITRR_Contacts_List();
      $this->contacts->prepare_items();
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
      wp_enqueue_style('itrr-indget-fontawesome-css', ITRR_Manager::$plugin_url . '/asset/css/fontawesome/css/font-awesome.min.css');
      wp_enqueue_style('itrr-indget-admin-css', ITRR_Manager::$plugin_url . '/asset/css/admin-common.css', array(), filemtime(ITRR_Manager::$plugin_dir . '/asset/css/admin-common.css'));
    }
    /** generate page script */
    function generate() {
      ?>
      <div class="wrap">

        <h2><?php echo __('Contacts', 'itrr_lang'); ?></h2>

        <div id="poststuff">
          <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
              <div class="meta-box-sortables ui-sortable">
                <form method="post">
                  <?php

                  $this->contacts->display(); ?>
                </form>
              </div>
            </div>
          </div>
          <br class="clear">
        </div>
      </div>
      <?php
    }
  }
}
