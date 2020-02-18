<?php
/**
 * Plugin Name: Neuralab Branding
 * Description: Adds Neuralab branding to WordPress dashboard and login.
 * Version: 1.0.4
 * License: MIT License
 * Author: development@neuralab.net
 * Author URI: http://www.neuralab.net
 * Text Domain: nrlb-branding
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

// Define plugin root file.
if ( ! defined( 'NRLB_BRANDING_ROOT_FILE' ) ) {
  define( 'NRLB_BRANDING_ROOT_FILE', __FILE__ );
}

// Include the main plugin class.
if ( ! class_exists( 'NRLB_Branding' ) ) {
  include_once dirname( __FILE__ ) . '/includes/class-nrlb-branding.php';
}

/**
 * Init the plugin.
 *
 * @return object Instance of NRLB_Branding class.
 */
function nrlb_branding() {
  return NRLB_Branding::get_instance();
}

nrlb_branding();
