<?php
/**
 * Admin Menu module.
 *
 * @package Ultimate_Dashboard
 */

namespace UdbPro\AdminBar;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use Udb\Base\Base_Module;
use Udb\Helpers\Screen_Helper;
use UdbPro\Helpers\Multisite_Helper;

/**
 * Class to setup admin menu module.
 */
class Admin_Bar_Module extends Base_Module {

	/**
	 * The class instance.
	 *
	 * @var object
	 */
	public static $instance;

	/**
	 * The current module url.
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Module constructor.
	 */
	public function __construct() {

		$this->url = ULTIMATE_DASHBOARD_PRO_PLUGIN_URL . '/modules/admin-bar';

	}

	/**
	 * Get instance of the class.
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Setup admin menu module.
	 */
	public function setup() {

		add_action( 'admin_notices', array( self::get_instance(), 'super_admin_notice' ) );

		// add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_scripts' ) );
		add_action( 'udb_admin_bar_form_footer', array( self::get_instance(), 'form_footer' ) );

		add_action( 'udb_admin_bar_add_menu_button', array( self::get_instance(), 'add_menu_button' ) );
		add_action( 'udb_admin_bar_add_submenu_button', array( self::get_instance(), 'add_submenu_button' ) );

		require __DIR__ . '/class-admin-bar-output.php';
		Admin_Bar_Output::init();

		$this->setup_ajax();

	}

	/**
	 * Setup ajax.
	 */
	public function setup_ajax() {

		require_once __DIR__ . '/ajax/class-reset-menu.php';
		require_once __DIR__ . '/ajax/class-save-menu.php';

		add_action( 'wp_ajax_udb_admin_bar_reset_menu', array( Ajax\Reset_Menu::get_instance(), 'reset' ) );
		add_action( 'wp_ajax_udb_admin_bar_save_menu', array( Ajax\Save_Menu::get_instance(), 'save' ) );

	}

	/**
	 * Admin notice to give an info about super admin not being affected by admin bar editor changes on multisite.
	 */
	public function super_admin_notice() {

		$ms_helper     = new Multisite_Helper();
		$screen_helper = new Screen_Helper();

		if ( ! $screen_helper->is_admin_bar() || ! $ms_helper->multisite_supported() || ! is_super_admin() ) {
			return;
		}

		$description = '<strong>' . __( 'Info:', 'ultimatedashboard' ) . '</strong>';
		$description = $description . ' ' . __( 'Changes made to the <strong>Admin Bar</strong> will not affect super admins. Super admins will always see the full admin bar for maximum control.', 'ultimatedashboard' );
		$description = '<p>' . $description . '</p>';

		printf( '<div class="notice notice-info is-dismissible udb-admin-notice">%1s</div>', $description );

	}

	/**
	 * Enqueue admin styles.
	 */
	public function admin_styles() {

		$enqueue = require __DIR__ . '/inc/css-enqueue.php';
		$enqueue( $this );

	}

	/**
	 * Enqueue admin scripts.
	 */
	public function admin_scripts() {

		$enqueue = require __DIR__ . '/inc/js-enqueue.php';
		$enqueue( $this );

	}

	/**
	 * Add output to admin menu's form footer.
	 */
	public function form_footer() {

		$template = require __DIR__ . '/templates/form-footer.php';
		$template();

	}

	/**
	 * Add new menu button under the menu list.
	 */
	public function add_menu_button() {

		$template = require __DIR__ . '/templates/add-menu-button.php';
		$template();

	}

	/**
	 * Add new submenu button under the submenu list.
	 */
	public function add_submenu_button() {

		$template = require __DIR__ . '/templates/add-submenu-button.php';
		$template();

	}

}
