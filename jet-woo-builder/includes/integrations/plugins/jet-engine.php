<?php
/**
 * JetEngine compatibility package
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Builder_Engine_Package' ) ) {

	// Define class
	class Jet_Woo_Builder_Engine_Package {

		// Class constructor.
		public function __construct() {
			add_filter( 'jet-engine/listing/item-classes', [ $this, 'thumbnail_effect_class' ] );
		}

		/**
		 * Push thumbnail effect class to listing grid item wrapper.
		 *
		 * @param $classes
		 *
		 * @return mixed
		 */
		public function thumbnail_effect_class( $classes ) {
			if ( filter_var( jet_woo_builder_settings()->get( 'enable_product_thumb_effect' ), FILTER_VALIDATE_BOOLEAN ) ) {
				$classes[] = 'jet-woo-thumb-with-effect';
			}

			return $classes;
		}

	}

}

new Jet_Woo_Builder_Engine_Package();
