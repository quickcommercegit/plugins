<?php
/**
 * Twenty Sixteen integration
 */

add_action( 'elementor/page_templates/canvas/before_content', 'jet_woo_twenty_sixteen_open_site_main_wrap', -999 );
add_action( 'jet-woo-builder/blank-page/before-content', 'jet_woo_twenty_sixteen_open_site_main_wrap', -999 );
add_action( 'elementor/page_templates/header-footer/before_content', 'jet_woo_twenty_sixteen_open_site_main_wrap', -999 );
add_action( 'jet-woo-builder/full-width-page/before-content', 'jet_woo_twenty_sixteen_open_site_main_wrap', -999 );

add_action( 'elementor/page_templates/canvas/after_content', 'jet_woo_twenty_sixteen_close_site_main_wrap', 999 );
add_action( 'jet-woo-builder/blank-page/after_content', 'jet_woo_twenty_sixteen_close_site_main_wrap', 999 );
add_action( 'elementor/page_templates/header-footer/after_content', 'jet_woo_twenty_sixteen_close_site_main_wrap', 999 );
add_action( 'jet-woo-builder/full-width-page/after_content', 'jet_woo_twenty_sixteen_close_site_main_wrap', 999 );

add_action( 'wp_enqueue_scripts', 'jet_woo_twenty_sixteen_enqueue_styles' );


/**
 * Open .site-main wrapper for products
 *
 * @return void
 */
function jet_woo_twenty_sixteen_open_site_main_wrap() {

	if ( ! is_singular( array( jet_woo_builder_post_type()->slug(), 'product' ) ) ) {
		return;
	}

	echo '<div class="site-main">';

}

/**
 * Close .site-main wrapper for products
 *
 * @return void
 */
function jet_woo_twenty_sixteen_close_site_main_wrap() {

	if ( ! is_singular( array( jet_woo_builder_post_type()->slug(), 'product' ) ) ) {
		return;
	}

	echo '</div>';

}


/**
 * Enqueue Twenty Fifteen integration stylesheets.
 *
 * @return void
 * @since  1.0.0
 * @access public
 */
function jet_woo_twenty_sixteen_enqueue_styles() {
	wp_enqueue_style(
		'jet-woo-builder-twentysixteen',
		jet_woo_builder()->plugin_url( 'includes/integrations/themes/twentysixteen/assets/css/style.css' ),
		false,
		jet_woo_builder()->get_version()
	);
}