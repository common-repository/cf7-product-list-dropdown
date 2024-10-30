<?php
/**
* Plugin Name: Cf7 Product And Custom Post List Dropdown
* Description: Cf7 in get post and woocommerce product list. 
* Version: 3.0
* Author: Ajay Radadiya
* License: A "GNUGPLv3" license name 
*/

if (!defined('ABSPATH')) {
  die('-1');
}
if (!defined('OCWPCF7_PLUGIN_NAME')) {
  define('OCWPCF7_PLUGIN_NAME', 'Cf7 Product And Custom Post List Dropdown');
}
if (!defined('OCWPCF7_PLUGIN_VERSION')) {
  define('OCWPCF7_PLUGIN_VERSION', '3.0.0');
}
if (!defined('OCWPCF7_PLUGIN_FILE')) {
  define('OCWPCF7_PLUGIN_FILE', __FILE__);
}
if (!defined('OCWPCF7_PLUGIN_DIR')) {
  define('OCWPCF7_PLUGIN_DIR',plugins_url('', __FILE__));
}

if (!defined('OCWPCF7_DOMAIN')) {
  define('OCWPCF7_DOMAIN', 'ocwpcf7');
}

//Main class
//Load required js,css and other files

if (!class_exists('OCWPCF7_main')) {

  class OCWPCF7_main {

    protected static $OCWPCF7_instance;

           /**
       * Constructor.
       *
       * @version 3.2.3
       */
     function __construct() {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        //check plugin activted or not
        add_action('admin_init', array($this, 'OCWPCF7_check_plugin_state'));
    }

    //Add JS and CSS on Backend
    function OCWPCF7_load_admin_script_style() {
      wp_enqueue_script( 'ocwpcf7_admin_js', OCWPCF7_PLUGIN_DIR . '/js/admin-ocwpcf7-js.js', false, '1.0.0' );
      wp_enqueue_style( 'ocwpcf7_admin_css', OCWPCF7_PLUGIN_DIR . '/css/admin-ocwpcf7-css.css', false, '1.0.0' );
    }

    //Add JS and CSS on Frontend
    function OCWPCF7_load_frontend_script_style() {
      wp_enqueue_script( 'ocwpcf7_frontend_js', OCWPCF7_PLUGIN_DIR . '/js/frontend-ocwpcf7-js.js', false, '1.0.0' );
      wp_enqueue_script( 'ocwpcf7_select2_js', OCWPCF7_PLUGIN_DIR . '/js/select2.js', false, '1.0.0' );
      wp_enqueue_script( 'ocwpcf7_select2min_js', OCWPCF7_PLUGIN_DIR . '/js/select2.min.js', false, '1.0.0' );
      wp_enqueue_style( 'ocwpcf7_select2_css', OCWPCF7_PLUGIN_DIR . '/css/select2.css', false, '1.0.0' );
      wp_enqueue_style( 'ocwpcf7_select2min_css', OCWPCF7_PLUGIN_DIR . '/css/select2.min.css', false, '1.0.0' );
      wp_enqueue_style( 'ocwpcf7_frontend_css', OCWPCF7_PLUGIN_DIR . '/css/frontend-ocwpcf7-css.css', false, '1.0.0' );
    }

    function OCWPCF7_show_notice() {

        if ( get_transient( get_current_user_id() . 'ocwpcf7error' ) ) {

          deactivate_plugins( plugin_basename( __FILE__ ) );

          delete_transient( get_current_user_id() . 'ocwpcf7error' );

         echo '<div class="error"><p> This plugin is deactivated because it require <a href="plugin-install.php?tab=search&s=contact+form+7">Contact Form 7</a> plugin installed and activated.</p></div>';

        }

    }

    function OCWPCF7_check_plugin_state(){
      if ( ! ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) ) {
        set_transient( get_current_user_id() . 'ocwpcf7error', 'message' );
      }
    }

    function init() {
      add_action( 'admin_notices', array($this, 'OCWPCF7_show_notice'));
      add_action('admin_enqueue_scripts', array($this, 'OCWPCF7_load_admin_script_style'));
      add_action( 'wp_enqueue_scripts', array($this, 'OCWPCF7_load_frontend_script_style') );
      if ( is_plugin_active( 'woocommerce/woocommerce.php' ) )  {
          //Product Control
          include_once('admin/ocwpcf7-product-control.php');
          //Product drop-down on Frontend
          include_once('frontend/ocwpcf7-product.php');
      }
      //Post Control
      include_once('admin/ocwpcf7-post-control.php');
      //Post drop-down on Frontend
      include_once('frontend/ocwpcf7-post.php');
    }


    //Plugin Rating
    public static function OCWPCF7_do_activation() {
      set_transient('ocwpcf7-first-rating', true, MONTH_IN_SECONDS);
    }

    public static function OCWPCF7_instance() {
      if (!isset(self::$OCWPCF7_instance)) {
        self::$OCWPCF7_instance = new self();
        self::$OCWPCF7_instance->init();
      }
      return self::$OCWPCF7_instance;
    }

  }

  add_action('plugins_loaded', array('OCWPCF7_main', 'OCWPCF7_instance'));

  register_activation_hook(OCWPCF7_PLUGIN_FILE, array('OCWPCF7_main', 'OCWPCF7_do_activation'));
}
