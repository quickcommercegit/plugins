<?php
namespace DynamicContentForElementor;

use DynamicContentForElementor\PageSettings\PageSettings_Scrollify;
use DynamicContentForElementor\PageSettings\PageSettings_InertiaScroll;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Core\Upgrade\Manager as UpgradeManager;

/**
 * Main Plugin Class
 *
 * @since 0.0.1
 */
class Plugin {

	/**
	 * @var UpgradeManager
	 */
	public $upgrade;

	/**
	 * Constructor
	 *
	 * @since 0.0.1
	 *
	 * @access public
	 */
	public function __construct() {
		$this->init();
	}

	public function init() {

		// Instance classes
		$this->instances();

		// Add WPML Compatibility
		add_action( 'wpml_loaded', [ $this, 'init_wpml_compatibility' ] );

		add_action( 'admin_menu', [ $this, 'add_dce_menu' ], 200 );

		// fire actions
		add_action( 'elementor/init', [ $this, 'add_dce_to_elementor' ], 0 );

		add_filter( 'plugin_action_links_' . DCE_PLUGIN_BASE, [ $this, 'plugin_action_links' ] );
		add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 2 );

		add_filter( 'pre_handle_404', [ $this, 'dce_allow_posts_pagination' ], 999, 2 );

	}

	public function instances() {
		$this->controls = new Controls();
		$this->extensions = new Extensions();
		$this->page_settings = new PageSettings();
		$this->settings = new Settings();
		// Dashboard
		$this->api = new Dashboard\Api();
		$this->templatesystem = new Dashboard\TemplateSystem();
		$this->license = new Dashboard\License();

		$this->info = new Info();
		$this->widgets = new Widgets();
		new Ajax();
		new Assets();
		new Dashboard\Dashboard();
		new LicenseSystem();
		new TemplateSystem();
		new Elements();

		new DCE_Query();
	}

	private function init_wpml_compatibility() {
		new Compatibility\WPML();
	}

	/**
	 * Add Actions
	 *
	 * @since 0.0.1
	 *
	 * @access private
	 */
	public function add_dce_to_elementor() {

		// Global Settings Panel
		\DynamicContentForElementor\GlobalSettings::init();

		$this->upgrade = UpgradeManager::instance();
		// Controls
		add_action( 'elementor/controls/controls_registered', [ $this->controls, 'on_controls_registered' ] );

		// Controls Manager
		\Elementor\Plugin::$instance->controls_manager = new DCE_Controls_Manager( \Elementor\Plugin::$instance->controls_manager );

		// Extensions
		$this->extensions->on_extensions_registered();

		// Page Settings
		$this->page_settings->on_page_settings_registered();

		// Widgets
		add_action( 'elementor/widgets/widgets_registered', [ $this->widgets, 'on_widgets_registered' ] );

	}

	public function add_dce_menu() {

		// Dynamic Content - Menu
		add_menu_page(
			'Dynamic Content for Elementor',
			'Dynamic Content',
			'manage_options',
			'dce-features',
			[
				$this->settings,
				'dce_setting_page',
			],
			'data:image/svg+xml;base64,' . $this->dce_get_icon_svg(),
			'58.6'
		);

		// Dynamic Content - Features
		add_submenu_page(
			'dce-features',
			'Dynamic Content for Elementor - ' . __( 'Features', 'dynamic-content-for-elementor' ),
			__( 'Features', 'dynamic-content-for-elementor' ),
			'manage_options',
			'dce-features',
			[
				$this->settings,
				'dce_setting_page',
			]
		);

		// Dynamic Content - Template System
		add_submenu_page(
			'dce-features',
			'Dynamic Content for Elementor - ' . __( 'Template System', 'dynamic-content-for-elementor' ),
			__( 'Template System', 'dynamic-content-for-elementor' ),
			'manage_options',
			'dce-templatesystem',
			[
				$this->templatesystem,
				'display_form',
			]
		);

		// Dynamic Content - APIs
		add_submenu_page(
			'dce-features',
			'Dynamic Content for Elementor - ' . __( 'APIs', 'dynamic-content-for-elementor' ),
			__( 'APIs', 'dynamic-content-for-elementor' ),
			'install_plugins',
			'dce-apis',
			[
				$this->api,
				'display_form',
			]
		);

		// Dynamic Content - License
		add_submenu_page(
			'dce-features',
			'Dynamic Content for Elementor - ' . __( 'License', 'dynamic-content-for-elementor' ),
			__( 'License', 'dynamic-content-for-elementor' ),
			'install_plugins',
			'dce-license',
			[
				$this->license,
				'show_license_form',
			]
		);
	}

	public static function plugin_action_links( $links ) {
		$links['config'] = '<a title="Configuration" href="' . admin_url() . 'admin.php?page=dce-features">' . __( 'Configuration', 'dynamic-content-for-elementor' ) . '</a>';
		return $links;
	}

	public function plugin_row_meta( $plugin_meta, $plugin_file ) {
		if ( 'dynamic-content-for-elementor/dynamic-content-for-elementor.php' === $plugin_file ) {
			$row_meta = [
				'docs' => '<a href="https://help.dynamic.ooo/" aria-label="' . esc_attr( __( 'View Documentation', 'dynamic-content-for-elementor' ) ) . '" target="_blank">' . __( 'Docs', 'dynamic-content-for-elementor' ) . '</a>',
				'community' => '<a href="http://facebook.com/groups/dynamic.ooo" aria-label="' . esc_attr( __( 'Facebook Community', 'dynamic-content-for-elementor' ) ) . '" target="_blank">' . __( 'FB Community', 'dynamic-content-for-elementor' ) . '</a>',
			];

			$plugin_meta = array_merge( $plugin_meta, $row_meta );
		}

		return $plugin_meta;
	}

	public static function dce_get_icon_svg( $base64 = true ) {
		$svg = '<?xml version="1.0" encoding="utf-8"?>
					<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
					viewBox="0 0 330.3 246.9" style="enable-background:new 0 0 330.3 246.9;" xml:space="preserve">
					<style type="text/css">
						.st0{fill-rule:evenodd;clip-rule:evenodd;fill:#ffffff;}
					</style>
					<g id="Livello_2">
						<g id="Livello_1-2">
							<path class="st0" d="M298.4,155.1c-17.5-0.1-31.7-14.4-31.6-31.9s14.4-31.7,31.9-31.6c17.5,0.1,31.6,14.3,31.6,31.7
								C330.2,140.9,315.9,155.1,298.4,155.1z M298.4,63.7c-17.5-0.1-31.7-14.4-31.6-31.9c0.1-17.5,14.4-31.7,31.9-31.6
								c17.5,0.1,31.6,14.3,31.6,31.8C330.2,49.6,315.9,63.7,298.4,63.7z M114.2,246.9H0V0h91.8c83.3,0,147.1,36.3,147.1,127
								C238.9,200.4,186.9,246.9,114.2,246.9z M121.5,66.7L73.1,183.1h18.1l48.4-116.4H121.5z M298.4,183c17.5,0.1,31.7,14.4,31.6,31.9
								c-0.1,17.5-14.4,31.7-31.9,31.6c-17.5-0.1-31.6-14.3-31.6-31.7C266.5,197.2,280.8,183,298.4,183C298.4,183,298.4,183,298.4,183z"
								/>
						</g>
					</g>
					</svg>';
		return base64_encode( $svg );
	}


	public function dce_allow_posts_pagination( $preempt, $wp_query ) {

		if ( $preempt || empty( $wp_query->query_vars['page'] ) || empty( $wp_query->post ) || ! is_singular() ) {
			return $preempt;
		}

		$allow_pagination = false;
		$document = '';
		$current_post_id = $wp_query->post->ID;
		$dce_posts_widgets = [ 'dyncontel-acfposts', 'dce-dynamicposts-v2', 'dyncontel-dynamicusers' ];

		// Check if current post/page is built with Elementor and check for DCE posts pagination
		if ( \Elementor\Plugin::$instance->db->is_built_with_elementor( $current_post_id ) && ! $allow_pagination ) {
			$allow_pagination = $this->dce_check_posts_pagination( $current_post_id, $dce_posts_widgets );
		}

		$dce_template = get_option( 'dce_template' );

		// Check if single DCE template is active and check for DCE posts pagination in template
		if ( isset( $dce_template ) && 'active' == $dce_template && ! $allow_pagination ) {
			$options = get_option( 'dyncontel_options' );
			$post_type = get_post_type( $current_post_id );

			if ( $options[ 'dyncontel_field_single' . $post_type ] ) {
				$allow_pagination = $this->dce_check_posts_pagination( $options[ 'dyncontel_field_single' . $post_type ], $dce_posts_widgets );
			}
		}

		// Check if single Elementor Pro template is active and check for DCE posts pagination in template
		if ( Helper::is_elementorpro_active() && ! $allow_pagination ) {
			$locations = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_locations_manager()->get_locations();

			if ( isset( $locations['single'] ) ) {
				$location_docs = \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager()->get_documents_for_location( 'single' );
				if ( ! empty( $location_docs ) ) {
					foreach ( $location_docs as $location_doc_id => $settings ) {
						if ( ( $wp_query->post->ID !== $location_doc_id ) && ! $allow_pagination ) {
							$allow_pagination = $this->dce_check_posts_pagination( $location_doc_id, $dce_posts_widgets );
							break;
						}
					}
				}
			}
		}

		if ( $allow_pagination ) {
			return $allow_pagination;
		} else {
			return $preempt;
		}

	}

	protected function dce_check_posts_pagination( $post_id, $dce_posts_widgets, $current_page = null ) {
		$pagination = false;

		if ( $post_id ) {
			$document = \Elementor\Plugin::$instance->documents->get( $post_id );
			$document_elements = $document->get_elements_data();

			// Check if DCE posts widgets are present and if pagination or infinite scroll is active
			\Elementor\Plugin::$instance->db->iterate_data( $document_elements, function( $element ) use ( &$pagination, $dce_posts_widgets ) {
				if ( isset( $element['widgetType'] ) && in_array( $element['widgetType'], $dce_posts_widgets, true )
				) {
					if ( isset( $element['settings']['pagination_enable'] ) ) {
						if ( $element['settings']['pagination_enable'] ) {
							$pagination = true;
						}
					}
					if ( isset( $element['settings']['infiniteScroll_enable'] ) ) {
						if ( $element['settings']['infiniteScroll_enable'] ) {
							$pagination = true;
						}
					}
				}
			});
		}

		return $pagination;
	}

}
