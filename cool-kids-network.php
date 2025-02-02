<?php
/**
 * Plugin Name:       Cool Kids Network
 * Plugin URI:        https://coolkidsnetwork.com/
 * Description:       This plugin manage cool kids network.
 * Version:           0.1
 * Text Domain:       ckn
 */


 // Prevent direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! defined( 'CKN_PATH' ) ) {
	define( 'CKN_VERSION', '0.1' );
	define( 'CKN_URL', plugins_url( '/', __FILE__ ) );
	define( 'CKN_PATH', plugin_dir_path( __FILE__ ) );
}

// Require Composer's autoloader
require_once CKN_PATH . '/vendor/autoload.php';
require_once CKN_PATH . 'includes/class-cool-kids-network.php';