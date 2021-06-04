<?php
/**
 * Admin Menu module.
 *
 * @package Ultimate_Dashboard
 */

namespace Udb\AdminMenu;

defined( 'ABSPATH' ) || die( "Can't access directly" );

use Udb\Base\Base_Module;

/**
 * Class to setup admin menu module.
 */
class Admin_Menu_Module extends Base_Module {

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

		$this->url = ULTIMATE_DASHBOARD_PLUGIN_URL . '/modules/admin-menu';

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

		add_action( 'admin_menu', array( self::get_instance(), 'submenu_page' ) );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( self::get_instance(), 'admin_scripts' ) );
		add_action( 'udb_ajax_get_admin_menu', array( self::get_instance(), 'get_admin_menu' ), 15, 2 );
		add_action( 'init', array( self::get_instance(), 'support_tablepress' ), 5 );

		$this->setup_ajax();

	}

	/**
	 * Setup ajax.
	 */
	public function setup_ajax() {

		require_once __DIR__ . '/ajax/class-get-menu.php';
		require_once __DIR__ . '/ajax/class-get-users.php';

		$get_menu  = new Ajax\Get_Menu();
		$get_users = new Ajax\Get_Users();

		add_action( 'wp_ajax_udb_admin_menu_get_menu', array( $get_menu, 'ajax' ) );
		add_action( 'wp_ajax_udb_admin_menu_get_users', array( $get_users, 'ajax' ) );

	}

	/**
	 * Add submenu page.
	 */
	public function submenu_page() {

		add_submenu_page( 'edit.php?post_type=udb_widgets', __( 'Admin Menu Editor', 'ultimate-dashboard' ), __( 'Admin Menu Editor', 'ultimate-dashboard' ), apply_filters( 'udb_settings_capability', 'manage_options' ), 'udb_admin_menu', array( $this, 'submenu_page_content' ) ); // are we using this filter everywhere?

	}

	/**
	 * Submenu page content.
	 */
	public function submenu_page_content() {

		require __DIR__ . '/templates/template.php';

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
	 * Get admin menu via ajax.
	 * This action will be called in "ajax" method in "class-get-menu.php".
	 *
	 * @see wp-content/plugins/ultimate-dashboard/helpers/class-user-helper.php
	 *
	 * @param object $ajax_handler The ajax handler class from the free version.
	 * @param string $role The role target to simulate.
	 */
	public function get_admin_menu( $ajax_handler, $role ) {

		$roles = wp_get_current_user()->roles;
		$roles = ! $roles || ! is_array( $roles ) ? array() : $roles;

		$simulate_role = in_array( $role, $roles, true ) ? false : true;

		// If current user role is different with the targetted role.
		if ( $simulate_role ) {
			$this->user()->simulate_role( $role, true );
		}

		$ajax_handler->load_menu();

		$response = $ajax_handler->format_response( $role );

		wp_send_json_success( $response );

	}

	/**
	 * TablePress has some controllers such as: frontend, backend, & ajax.
	 * They don't load frontend controller if it's inside admin area.
	 * Even they don't load backend controller if it's inside admin area but is doing ajax.
	 *
	 * Their admin page registration is inside backend controller.
	 * While we get the menu with role simulation through ajax.
	 *
	 * See "run" function inside class-tablepress.php
	 *
	 * @see wp-content/plugins/tablepress/classes/class-tablepress.php
	 */
	public function support_tablepress() {

		if ( ! defined( 'TABLEPRESS_ABSPATH' ) || ! is_admin() || ! wp_doing_ajax() ) {
			return;
		}

		if ( ! isset( $_POST['action'] ) || 'udb_admin_menu_get_menu' !== $_POST['action'] ) {
			return;
		}

		/**
		 * The value of `wp_doing_ajax` will be set back to `true` in class-get-menu.php file
		 * inside `load_menu` function.
		 *
		 * @see wp-content/plugins/ultimate-dashboard/modules/admin-menu/ajax/class-get-menu.php
		 */
		add_filter( 'wp_doing_ajax', '__return_false' );

	}

}
