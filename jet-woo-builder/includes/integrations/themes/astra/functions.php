<?php
/**
 * Astra integration
 */

add_action( 'jet-smart-filters/providers/woocommerce-archive/before-ajax-content', 'jet_woo_astra_compatibility', 1 );
add_action( 'wp_enqueue_scripts', 'jet_woo_astra_enqueue_styles' );

/**
 * Astra theme compatibility
 */
function jet_woo_astra_compatibility() {

	if ( class_exists( 'Astra_Woocommerce' ) ) {
		$astra = new Astra_Woocommerce();

		if ( ! apply_filters( 'astra_woo_shop_product_structure_override', false ) ) {
			$astra->shop_customization();
		}

		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
	}

}

/**
 * Enqueue Astra integration stylesheets.
 *
 * @return void
 * @since  1.0.0
 * @access public
 */
function jet_woo_astra_enqueue_styles() {
	wp_enqueue_style(
		'jet-woo-builder-astra',
		jet_woo_builder()->plugin_url( 'includes/integrations/themes/astra/assets/css/style.css' ),
		false,
		jet_woo_builder()->get_version()
	);
}