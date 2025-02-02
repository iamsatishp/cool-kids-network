<?php
namespace CoolKidsNetwork;

// Prevent direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The class for will manage the roles related functions.
 * @since  0.1
 */
class Roles {

	/**
	 * The instance of the class.
	 *
	 * @var null|object $instance
	 */
	private static $_instance = null;

	/**
	 * Return the instance of the class.
	 *
	 * @access public
	 * @since  0.1
	 * @return @var $instance
	 */
	public static function init() {
		if ( null === self::$_instance ) {
			self::$_instance = new Roles();
		}
		return self::$_instance;
	}
	/**
	 * The constructor of the roles class.
	 *
	 * @access public
	 * @since  0.1
	 * @return void
	 */
	public function __construct() {
		// add custom roles
		add_action( 'init', array( $this, 'add_cool_kids_network_custom_roles' ) );

		$this->update_default_role( 'cool_kid' );
	}

	/**
	 * Add Custom roles for cool kid.
	 *
	 * @access public
	 * @since  0.1
	 * @return void
	 */
	public function add_cool_kids_network_custom_roles() {
		add_role( 'cool_kid', 'Cool Kid', array( 'read' => true ) );
		add_role( 'cooler_kid', 'Cooler Kid', array( 'read' => true ) );
		add_role( 'coolest_kid', 'Coolest Kid', array( 'read' => true ) );
	}

	/**
	 * Update the default role for the new WordPress user registration.
	 *
	 * @access public
	 * @param string $role role name to assign to new users.
	 * @since  0.1
	 * @return void
	 */
	public function update_default_role( $role ) {
		update_option( 'default_role', $role );
	}
}
