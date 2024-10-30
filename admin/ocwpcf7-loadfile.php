<?php

if (!defined('ABSPATH'))
  exit;

if (!class_exists('OCWPCF7_Loader')) {

  class OCWPCF7_Loader {

    protected static $OCWPCF7_instance;
    /**
     * Registers Products Shortcode.
     */

    //Add JS and CSS on Backend
    function OCWPCF7_load_admin_script_style() {
      wp_enqueue_script( 'ocwpcf7_admin_js', OCWPCF7_PLUGIN_DIR . '/js/admin-ocwpcf7-js.js', false, '1.0.0' );
    }

    function init() {
       add_action('admin_enqueue_scripts', array($this, 'OCWPCF7_load_admin_script_style'));
    }

    //Load all includes files
    function includes() {
      //Admn site Layout
      include_once('admin/ocwpcf7-backend.php');
      //Admn site Layout
      include_once('admin/ocwpcf7-product-control.php');

    }

    public static function OCWPCF7_instance() {
      if (!isset(self::$OCWPCF7_instance)) {
        self::$OCWPCF7_instance = new self();
        self::$OCWPCF7_instance->init();
        self::$OCWPCF7_instance->includes();
      }
      return self::$OCWPCF7_instance;
    }

  }

  OCWPCF7_Loader::OCWPCF7_instance();
}

