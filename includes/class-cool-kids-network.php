<?php
namespace CoolKidsNetwork;

// Prevent direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main Functionality File
 *
 * This class is to add functionality of plugin.
 *
*/
class Cool_Kids_Network {

	/**
	 * The instance of the class.
	 *
	 * @var null|object $instance
	 */

	private static $_instance = null;

	/**
	 * The function will return the instance of the class.
	 *
	 * @access public
	 * @since  0.1
	 * @return @var $instance
	 */
	public static function init() {
		if ( null === self::$_instance ) {
			self::$_instance = new Cool_Kids_Network();
		}
		return self::$_instance;
	}

	/**
	 * The constructor of the class.
	 *
	 * @access public
	 * @since  0.1
	 * @return void
	 */
	public function __construct() {
		$this->setup();
		// Enqueue JavaScript for AJAX
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue required Javascript and CSS files of the plugin
	 *
	 * @access public
	 * @since  0.1
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'ckn-style', CKN_URL . 'assets/css/style.css', array(), CKN_VERSION );
	}

	/**
	 * Setup the necessary files.
	 *
	 * @access private
	 * @since  0.1
	 * @return void
	 */
	private function setup() {
		new Roles();
		new Sign_Up();
		new Sign_In();
		new Character_Data();
	}
}

Cool_Kids_Network::init();
