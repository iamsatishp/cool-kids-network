<?php
namespace CoolKidsNetwork;

// Prevent direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The Sing In class will manage the sign in process of user.
 */
class Sign_In {

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
			self::$_instance = new Sign_In();
		}
		return self::$_instance;
	}

	/**
	 * The constructor of the sign in class.
	 *
	 * @access public
	 * @since  0.1
	 * @return void
	 */
	public function __construct() {
		// Register shortcode
		add_shortcode( 'ckn_login_form', array( $this, 'render_ckn_login_form' ) );

		// Enqueue JavaScript and styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Handle AJAX request
		add_action( 'wp_ajax_handle_character_signin', array( $this, 'handle_character_signin' ) );
		add_action( 'wp_ajax_nopriv_handle_character_signin', array( $this, 'handle_character_signin' ) );
	}

	/**
	 * Render the sign-in form.
	 *
	 * @access public
	 * @since  0.1
	 * @return string The HTML content for the sign-in form.
	 */
	public function render_ckn_login_form() {
		if ( is_user_logged_in() ) {
			return '<p>You are already logged in. <a href="' . wp_logout_url( home_url() ) . '">Log out</a></p>';
		}

		ob_start();
		?>

		<div class="ckn-form-wrapper">
			<div class="ckn-form-title">
				Sign in Form
			</div>
			<form id="ckn-login-form" method="post" class="ckn-login-form">
				<?php wp_nonce_field( 'ckn_signin_nonce', 'signin_nonce' ); ?>
				<div class="ckn-form-field">
				<label for="email">Email Address:</label>
					<input type="email" id="ckn-email" name="email" required>
				</div>
				<div class="ckn-form-field">
					<button type="submit" name="ckn-login" class="ckn-button ckn-submit-button">Sign In</button>
					<div id="ckn-login-message" class="error"></div>
				</div>
				
				<div class="link">
				Not a member? <a href="<?php echo esc_url( home_url( '/sign-up' ) ); ?>">Sign up now</a>
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
		wp_enqueue_script( 'ckn-signin', CKN_PATH . 'assets/js/sign-in.js', array(), CKN_VERSION, array() );
		wp_localize_script(
			'ckn-signin',
			'signin_ajax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'ckn_signin_nonce' ), // Add nonce for AJAX
			)
		);
	}

	/**
	 * Handle the character sign in process via ajax.
	 *
	 * @access public
	 * @since  0.1
	 * @return void
	 */
	public function handle_character_signin() {
		// Verify nonce
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'ckn_signin_nonce' ) ) {
			wp_send_json(
				array(
					'is_error' => true,
					'message'  => 'Security check failed.',
				)
			);
		}

		if ( isset( $_POST['email'] ) ) {
			$email = sanitize_email( $_POST['email'] );

			// Check if the email is associated with an existing user
			$user = get_user_by( 'email', $email );
			if ( $user ) {
				// Log the user in
				wp_clear_auth_cookie();
				wp_set_current_user( $user->ID );
				wp_set_auth_cookie( $user->ID );

				wp_send_json(
					array(
						'is_error'     => false,
						'message'      => 'Login Sucessful.',
						'redirect_url' => home_url( '/character-data' ),
					)
				);
			} else {
				wp_send_json(
					array(
						'is_error' => true,
						'message'  => 'Email not found',
					)
				);
			}
		}
	}
}