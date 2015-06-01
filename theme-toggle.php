<?php
/**
 * Plugin Name: Theme Toggle
 * Plugin URI: http://scootah.com/
 * Description: Add an option to toggle between the active theme and another installed one.
 * Version: 1.0
 * Author: Scott Grant
 * Author URI: http://scootah.com/
 */
class WP_ThemeToggle {

	/**
	 * Store reference to singleton object.
	 */
	private static $instance = null;

	/**
	 * The domain for localization.
	 */
	const DOMAIN = 'wp-theme-toggle';

	/**
	 * Instantiate, if necessary, and add hooks.
	 */
	public function __construct() {
		if ( isset( self::$instance ) ) {
			wp_die( esc_html__(
				'WP_ThemeToggle is already instantiated!',
				self::DOMAIN ) );
		}

		self::$instance = $this;

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		add_filter( 'template', array( $this, 'change_theme' ) );
		add_filter( 'option_template', array( $this, 'change_theme' ) );
		add_filter( 'option_stylesheet', array( $this, 'change_theme' ) );
	}

	/**
	 * Add a link to a settings page.
	 */
	public function admin_menu() {
		$page = add_options_page(
			'Theme Toggle',
			'Theme Toggle',
			'manage_options',
			'theme_toggle_page',
			array( $this, 'theme_toggle_page' )
		);
	}

	public function theme_toggle_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', self::DOMAIN ) );
		}
?>
<h1>Theme Toggle</h1>
<?php
		$user_id = get_current_user_id();

		if ( isset( $_POST[ 'theme' ] ) ) {
			update_user_meta( $user_id, 'theme_toggle', $_POST[ 'theme' ] );
			echo( '<h2>Theme updated!</h2>' );
		}

		$theme = get_user_meta( $user_id, 'theme_toggle', true );
?>
<form method="post" action="options-general.php?page=theme_toggle_page">
<p>
  Current theme override:
  <input type="text" name="theme" value="<?php echo( $theme ); ?>">
  <input type="submit" value="Update">
</p>
</form>
<?php
	}

	public function change_theme( $theme ) {
		$user_id = get_current_user_id();

		if ( 0 == $user_id ) {
			return $theme;
		}

		$new_theme = get_user_meta( $user_id, 'theme_toggle', true );

		if ( strlen( $new_theme ) > 0 ) {
			return $new_theme;
		}

		return $theme;
	}
}

$wp_themetoggle = new WP_ThemeToggle();
