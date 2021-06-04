<?php
/**
 * Settings output.
 *
 * @package Ultimate_Dashboard
 */

namespace Udb\Setting;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use WP_Query;
use Udb\Base\Base_Output;

/**
 * Class to setup setting output.
 */
class Setting_Output extends Base_Output {

	/**
	 * The class instance.
	 *
	 * @var object
	 */
	public static $instance = null;

	/**
	 * The current module url.
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Get instance of the class.
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Module constructor.
	 */
	public function __construct() {

		$this->url = ULTIMATE_DASHBOARD_PLUGIN_URL . '/modules/setting';

	}

	/**
	 * Init the class setup.
	 */
	public static function init() {

		$class = new self();
		$class->setup();

	}

	/**
	 * Setup widgets output.
	 */
	public function setup() {

		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'dashboard_custom_css' ), 200 );
		add_action( 'admin_head', array( self::get_instance(), 'admin_custom_css' ), 200 );
		add_action( 'admin_head', array( self::get_instance(), 'change_dashboard_headline' ) );
		add_action( 'admin_head', array( self::get_instance(), 'remove_help_tab' ) );
		add_filter( 'screen_options_show_screen', array( self::get_instance(), 'remove_screen_options_tab' ) );
		add_action( 'init', array( self::get_instance(), 'remove_admin_bar' ) );
		add_action( 'init', array( self::get_instance(), 'remove_font_awesome' ) );

	}

	/**
	 * Add dashboard custom CSS.
	 */
	public function dashboard_custom_css() {

		$settings = get_option( 'udb_settings' );

		if ( ! isset( $settings['custom_css'] ) || empty( $settings['custom_css'] ) ) {
			return;
		}

		wp_add_inline_style( 'udb-dashboard', $settings['custom_css'] );

	}

	/**
	 * Add admin custom CSS.
	 */
	public function admin_custom_css() {

		$settings = get_option( 'udb_settings' );

		if ( ! isset( $settings['custom_admin_css'] ) || empty( $settings['custom_admin_css'] ) ) {
			return;
		}
		?>

		<style>
			<?php echo $settings['custom_admin_css']; ?>
		</style>

		<?php

	}

	/**
	 * Change Dashboard's headline.
	 */
	public function change_dashboard_headline() {

		if ( isset( $GLOBALS['title'] ) && 'Dashboard' !== $GLOBALS['title'] ) {
			return;
		}

		$settings = get_option( 'udb_settings' );

		if ( ! isset( $settings['dashboard_headline'] ) || empty( $settings['dashboard_headline'] ) ) {
			return;
		}

		$GLOBALS['title'] = $settings['dashboard_headline'];

	}

	/**
	 * Remove help tab on admin area.
	 */
	public function remove_help_tab() {

		$current_screen = get_current_screen();

		$settings = get_option( 'udb_settings' );

		if ( ! isset( $settings['remove_help_tab'] ) ) {
			return;
		}

		if ( $current_screen ) {
			$current_screen->remove_help_tabs();
		}

	}

	/**
	 * Remove screen options on admin area.
	 */
	public function remove_screen_options_tab() {
		$settings = get_option( 'udb_settings' );

		return ( isset( $settings['remove_screen_options'] ) ? false : true );
	}

	/**
	 * Remove admin bar from frontend.
	 */
	public function remove_admin_bar() {
		$settings = get_option( 'udb_settings' );

		if ( isset( $settings['remove_admin_bar'] ) ) {
			add_filter( 'show_admin_bar', '__return_false' );
		}
	}

	/**
	 * Remove Font Awesome.
	 */
	public function remove_font_awesome() {
		$settings = get_option( 'udb_settings' );

		if ( isset( $settings['remove_font_awesome'] ) ) {
			add_filter( 'udb_font_awesome', '__return_false' );
		}
	}

}
