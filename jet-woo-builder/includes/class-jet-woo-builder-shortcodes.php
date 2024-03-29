<?php
/**
 * JetWooBuilder Shortcodes Class
 *
 * @package   JetWooBuilder
 * @author    Crocoblock
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Builder_Shortcodes' ) ) {

	/**
	 * Define Jet_Woo_Builder_Shortcodes class
	 */
	class Jet_Woo_Builder_Shortcodes {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Check if processing Elementor widget
		 *
		 * @var boolean
		 */
		private $shortcodes = array();

		/**
		 * Initialize integration hooks
		 *
		 * @return void
		 */
		public function init() {
			add_action( 'init', array( $this, 'register_shortcodes' ), 30 );
		}

		/**
		 * Register plugins shortcodes
		 *
		 * @return void
		 */
		public function register_shortcodes() {

			require jet_woo_builder()->plugin_path( 'includes/base/class-jet-woo-builder-shortcode-base.php' );

			foreach ( glob( jet_woo_builder()->plugin_path( 'includes/shortcodes/' ) . '*.php' ) as $file ) {
				$this->register_shortcode( $file );
			}

		}

		/**
		 * Call shortcode instance from passed file.
		 *
		 * @param $file
		 *
		 * @return void
		 */
		public function register_shortcode( $file ) {

			$base  = basename( str_replace( '.php', '', $file ) );
			$class = ucwords( str_replace( '-', ' ', $base ) );
			$class = str_replace( ' ', '_', $class );

			require $file;

			if ( ! class_exists( $class ) ) {
				return;
			}

			$shortcode = new $class;

			$this->shortcodes[ $shortcode->get_tag() ] = $shortcode;

		}

		/**
		 * Get shortcode class instance by tag
		 *
		 * @param  $tag
		 *
		 * @return bool|mixed
		 */
		public function get_shortcode( $tag ) {
			return isset( $this->shortcodes[ $tag ] ) ? $this->shortcodes[ $tag ] : false;
		}

		/**
		 * Return list query types
		 *
		 * @return array
		 */
		public function get_products_query_type() {

			$args = [
				'all'        => esc_html__( 'All', 'jet-woo-builder' ),
				'featured'   => esc_html__( 'Featured', 'jet-woo-builder' ),
				'sale'       => esc_html__( 'Sale', 'jet-woo-builder' ),
				'tag'        => esc_html__( 'Tag', 'jet-woo-builder' ),
				'category'   => esc_html__( 'Category', 'jet-woo-builder' ),
				'ids'        => esc_html__( 'Specific IDs', 'jet-woo-builder' ),
				'viewed'     => esc_html__( 'Recently Viewed', 'jet-woo-builder' ),
				'custom_tax' => esc_html__( 'Custom Taxonomy', 'jet-woo-builder' ),
			];

			$single_product_args = [
				'related'     => esc_html__( 'Related', 'jet-woo-builder' ),
				'up-sells'    => esc_html__( 'Up Sells', 'jet-woo-builder' ),
				'cross-sells' => esc_html__( 'Cross Sells', 'jet-woo-builder' ),
			];

			if ( is_product() ) {
				$args = wp_parse_args( $single_product_args, $args );
			}

			return $args;

		}

		/**
		 * Add WooCommerce catalog ordering args to current query
		 *
		 * @param $query_args
		 *
		 * @return array
		 */
		public function get_wc_catalog_ordering_args( $query_args ) {

			if ( ! isset( $query_args['orderby'] ) ) {
				$query_args['orderby'] = 'date';
			}

			$ordering_args         = WC()->query->get_catalog_ordering_args( $query_args['orderby'], $query_args['order'] );
			$query_args['orderby'] = $ordering_args['orderby'];
			$query_args['order']   = $ordering_args['order'];

			if ( $ordering_args['meta_key'] ) {
				$query_args['meta_key'] = $ordering_args['meta_key'];
			}

			return $query_args;

		}

		/**
		 * Returns the instance.
		 *
		 * @return object
		 * @since  1.0.0
		 */
		public static function get_instance( $shortcodes = array() ) {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self( $shortcodes );
			}
			return self::$instance;

		}
	}

}

/**
 * Returns instance of Jet_Woo_Builder_Shortcodes
 *
 * @return object
 */
function jet_woo_builder_shortcodes() {
	return Jet_Woo_Builder_Shortcodes::get_instance();
}
