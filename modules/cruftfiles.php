<?php
/*
 * Plugin name: Cruft files
 * Description: View and edit domains and DNS
 * Version: 1.0
 */

namespace Seravo;

if ( ! class_exists('Cruftfiles') ) {
  class Cruftfiles {

    public static function load() {
      add_action( 'admin_menu', array( __CLASS__, 'register_cruftfiles_page' ) );
      add_action('wp_ajax_seravo_cruftfiles', function() {
          require_once(dirname( __FILE__ ) . '/../lib/cruftfiles-ajax.php');
          wp_die();
      });
    }

    public static function register_cruftfiles_page() {
      add_submenu_page( 'tools.php', 'Cruft files', 'Cruft files', 'manage_options', 'cruftfiles_page', array( __CLASS__, 'load_cruftfiles_page' ) );
    }

    public static function load_cruftfiles_page() {
      require_once(dirname( __FILE__ ) . '/../lib/cruftfiles-page.php');
    }

  }

  cruftfiles::load();
}
