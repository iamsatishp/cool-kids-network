<?php
namespace CoolKidsNetwork;

// Prevent direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The Sing Up class will manage the registration functionality of user.
 * @since  0.1
 */

class Sign_Up {

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
			self::$_instance = new Sign_Up();
		}
		return self::$_instance;
	}

	/**
	 * The constructor of the sign up class.
	 *
	 * @access public
	 * @since  0.1
	 * @return void
	 */
	public function __construct() {
		// Register sign up shortcode
		add_shortcode( 'ckn_signup_form', array( $this, 'render_signup_form' ) );

		// Enqueue Scrpits and styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Handle AJAX request
		add_action( 'wp_ajax_handle_character_signup', array( $this, 'handle_character_signup' ) );
		add_action( 'wp_ajax_nopriv_handle_character_signup', array( $this, 'handle_character_signup' ) );
	}

	/**
	 * Render the sign-up form.
	 *
	 * @access public
	 * @since  0.1
	 * @return string The HTML content for the sign-up form.
	 */
	public function render_signup_form() {
		ob_start();
		?>
		<div class="ckn-form-wrapper">
			<div class="ckn-form-title">
				Sign Up Form
			</div>
			<form id="ckn-signup-form" method="post" class="ckn-signup-form">
				<?php wp_nonce_field( 'ckn_signup_nonce', 'signup_nonce' ); ?>
				<div class="ckn-form-field">
				<label for="ckn-email">Email Address:</label>
					<input type="email" id="ckn-email" name="email" required>
				</div>
				<div class="ckn-form-field">
					<button type="submit" id="ckn-signup" class="ckn-button ckn-submit-button">Sign Up</button>
					<div id="ckn-signup-message"></div>
				</div>
				
				<div class="ckn-link">
				Already a member? <a href="<?php echo esc_url( home_url( '/sign-in' ) ); ?>">Sign in now</a>
				</div>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Enqueue JavaScript for sign up form.
	 *
	 * @access public
	 * @since  0.1
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'ckn-signup', CKN_PATH . 'assets/js/sign-up.js', array(), CKN_VERSION, array() );
		wp_localize_script(
			'ckn-signup',
			'signup_ajax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'ckn_signup_nonce' ),
			)
		);
	}

	/**
	 * Handle the sign up process using ajax.
	 *
	 * @access public
	 * @since  0.1
	 * @return void
	 */
	public function handle_character_signup() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'ckn_signup_nonce' ) ) {
			wp_send_json(
				array(
					'is_error' => true,
					'message'  => 'Security check failed.',
				)
			);
		}

		if ( isset( $_POST['email'] ) && ! empty( $_POST['email'] ) ) {
			$email = sanitize_email( $_POST['email'] );

			// Check if the email is already registered
			if ( ! email_exists( $email ) ) {
				$this->register_user( $email );
			} else {
				wp_send_json(
					array(
						'is_error' => true,
						'message'  => 'Email already registered.',
					)
				);
			}
		} else {
			wp_send_json(
				array(
					'is_error' => true,
					'message'  => 'Email not provided.',
				)
			);
		}
	}

	/**
	 * Register a user with given email and rest data will get from randomuser API..
	 *
	 * @param string $email The user's email address.
	 * @return void
	 */
	private function register_user( $email ) {
		// Generate fake identity using randomuser.me API
		$response = wp_remote_get( 'https://randomuser.me/api/' );
		if ( ! is_wp_error( $response ) ) {
			$body      = json_decode( $response['body'], true );
			$user_data = $body['results'][0];

			// Extract user details
			$first_name = sanitize_text_field( $user_data['name']['first'] );
			$last_name  = sanitize_text_field( $user_data['name']['last'] );
			$country    = sanitize_text_field( $user_data['location']['country'] );
			$role       = 'Cool Kid'; // Default role

			$username = strtolower( $first_name . '_' . $last_name ); // Generate username
			$password = wp_generate_password(); // Generate a random password

			$username_exist = username_exists( $username );

			if ( $username_exist ) {
				$username = strtolower( $first_name . '_' . $last_name . '_' . wp_rand( 1, 1000 ) );
			}
			$user_id = wp_create_user( $username, $password, $email );

			if ( ! is_wp_error( $user_id ) ) {
				update_user_meta( $user_id, 'first_name', $first_name );
				update_user_meta( $user_id, 'last_name', $last_name );
				update_user_meta( $user_id, 'country', $country );
				update_user_meta( $user_id, 'role', $role );

				$user = new \WP_User( $user_id );

				// Set the user role
				$user->set_role( 'cool_kid' );

				wp_send_json(
					array(
						'is_error' => false,
						'message'  => 'Account created successfully!',
					)
				);
			} else {
				wp_send_json(
					array(
						'is_error' => true,
						'message'  => 'Error creating account.',
					)
				);
			}
		} else {
			wp_send_json(
				array(
					'is_error' => true,
					'message'  => 'Error generating fake identity.',
				)
			);
		}
	}
}