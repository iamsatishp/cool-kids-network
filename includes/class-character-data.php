<?php
namespace CoolKidsNetwork;

// Prevent direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * The class will manage to display data on the basis of current login user.
 */
class Character_Data {

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
			self::$_instance = new Character_data();
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
		add_shortcode( 'ckn_character_data', array( $this, 'render_character_data' ) );
	}
	/**
	 * Render the character data.
	 * @access public
	 * @since  0.1
	 * @return @var string html content of character data
	 */
	public function render_character_data() {
		if ( ! is_user_logged_in() ) {
			return '<p>Please <a href="' . esc_url( home_url( '/sign-in' ) ) . '">log in</a> to view character data.</p>';
		}

		$user = wp_get_current_user();
		$role = $user->roles[0];

		ob_start();

		if ( 'cool_kid' === $role ) {
			$this->single_character_data( $user );
		} else {
			$this->all_characters_data();
		}

		return ob_get_clean();
	}

	/**
	 * Render the current login user character data.
	 * @access public
	 * @since  0.1
	 * @return string html content of current login user character data
	 */
	public function single_character_data( $user ) {

		$role = $user->roles[0];

		global $wp_roles;

		$first_name = get_user_meta( $user->ID, 'first_name', true );
		$last_name  = get_user_meta( $user->ID, 'last_name', true );
		$country    = get_user_meta( $user->ID, 'country', true );
		$role_name  = $wp_roles->roles[ $role ]['name'];
		?>
		<div class="character-data">
			<h2>Your Character Data</h2>
			<p><strong>First Name:</strong> <?php echo esc_html( $first_name ); ?></p>
			<p><strong>Last Name:</strong> <?php echo esc_html( $last_name ); ?></p>
			<p><strong>Country:</strong> <?php echo esc_html( $country ); ?></p>
			<p><strong>Role:</strong> <?php echo esc_html( $role_name ); ?></p>
			<p><a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>">Log Out</a></p>
		</div>
		<?php
	}
	/**
	 * Render all character data.
	 * @access public
	 * @since  0.1
	 * @return string html content for all character data
	 */
	public function all_characters_data() {
		if ( is_user_logged_in() ) {
			$current_user_role = wp_get_current_user()->roles[0];
			if ( in_array( $current_user_role, array( 'cooler_kid', 'coolest_kid', 'administrator' ), true ) ) {
				$users = get_users(
					array(
						'role__in' => array(
							'cool_kid',
							'cooler_kid',
							'coolest_kid',
						),
					)
				);
				if ( $users ) {
					?>
					<h2>All Character Data</h2>
					<div class="ckn-all-character-data">
					<table>
						<tr>
							<th>First Name</th>
							<th>Last Name</th>
							<th> Country</th>
					<?php
					if ( 'coolest_kid' === $current_user_role ) {
						?>
						<th>Email</th>
						<th>Role</th>
						<?php
					}
					?>
						</tr>
					<?php
				}
				global $wp_roles;
				foreach ( $users as $user ) {
					$first_name = get_user_meta( $user->ID, 'first_name', true );
					$last_name  = get_user_meta( $user->ID, 'last_name', true );
					$country    = get_user_meta( $user->ID, 'country', true );
					?>
					<tr>
						<td><?php echo esc_html( $first_name ); ?></td>
						<td><?php echo esc_html( $last_name ); ?></td>
						<td><?php echo esc_html( $country ); ?></td>
					<?php
					if ( 'coolest_kid' === $current_user_role ) {
						$email     = $user->user_email;
						$role      = $user->roles[0];
						$role_name = $wp_roles->roles[ $role ]['name'];
						?>
						<td><?php echo esc_html( $email ); ?></td>
						<td><?php echo esc_html( $role_name ); ?></td>
						<?php
					}
					echo '</tr>';
				}
				if ( $users ) {
					?>
					</table>
					</div>
					<?php
				}
			}
		}
	}
}