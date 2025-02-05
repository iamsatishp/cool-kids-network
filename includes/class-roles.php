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

		add_action( 'rest_api_init', array( $this, 'register_update_character_role' ) );
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

	/**
	 * Register api endpoint for changing user role.
	 *
	 * @access public
	 * @since  0.1
	 * @return void
	 */
	public function register_update_character_role() {
		register_rest_route(
			'coolkidsnetwork/v1',
			'/changerole',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'change_character_role' ),
				'permission_callback' => array( $this, 'check_permission' ),
			)
		);
	}

	/**
	 * Check if the user has the permission to update user role
	 * @access public
	 * @since 0.1
	 * @return bool|\WP_Error True on has permission, or WP_Error object on failure.
	 */
	public function check_permission() {
		// Restrict endpoint to only users who have the edit_posts capability.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new \WP_Error( 'rest_forbidden', __( 'OMG you can not update user role.', 'ckn' ), array( 'status' => 401 ) );
		}

		return true;
	}

	/**
	 * Change user role based on rest api request.
	 *
	 * @access public
	 * @since  0.1
	 * @param  WP_REST_Request $request The REST API request object.
	 * @return WP_REST_Response The REST API response object.
	 */
	public function change_character_role( \WP_REST_Request $request ) {
		// Verify nonce
		if ( ! empty( $nonce ) && ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new \WP_REST_Response(
				array(
					'status'   => 403,
					'response' => __( 'OMG something went wrong.', 'ckn' ),
				)
			);
		}

		$email      = $request->get_param( 'email' );
		$first_name = $request->get_param( 'first_name' );
		$last_name  = $request->get_param( 'last_name' );
		$new_role   = $request->get_param( 'role' );

		// Ensure only valid roles are accepted
		$valid_roles = array( 'cool_kid', 'cooler_kid', 'coolest_kid' );
		if ( ! in_array( $new_role, $valid_roles, true ) ) {
			return new \WP_REST_Response(
				array(
					'status'   => 400,
					'response' => __( 'Invalid role specified', 'ckn' ),
				)
			);
		}

		// Find user by email or first/last name
		$user = null;
		if ( $email ) {
			$user = get_user_by( 'email', $email );
		} elseif ( $first_name && $last_name ) {
			$users = get_users(
				array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'key'   => 'first_name',
							'value' => $first_name,
						),
						array(
							'key'   => 'last_name',
							'value' => $last_name,
						),
					),
				)
			);
			if ( ! empty( $users ) ) {
				$user = $users[0];
			}
		}

		// Update user role if found
		if ( $user ) {
			$user->set_role( $new_role );
			return new \WP_REST_Response(
				array(
					'status'   => 200,
					'response' => __( 'Role updated successfully', 'ckn' ),
				)
			);
		} else {
			return new \WP_REST_Response( __( 'User not found', 'ckn' ), 404 );
		}
	}
}
