<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/woocommerce/archive-product.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

do_action( 'jet-woo-builder/woocommerce/before-main-content' );

$wc_data = new WC_Structured_Data;

$wc_data->generate_product_data();

$taxonomy_custom_template = get_term_meta( get_queried_object_id(), 'jet_woo_builder_template', true );
$template                 = ( is_product_taxonomy() && ! empty( $taxonomy_custom_template ) ) ? apply_filters( 'jet-woo-builder/current-template/template-id', jet_woo_builder_integration_woocommerce()->get_custom_taxonomy_template() ) : apply_filters( 'jet-woo-builder/current-template/template-id', jet_woo_builder_integration_woocommerce()->get_custom_shop_template() );

echo jet_woo_builder_template_functions()->get_woo_builder_content( $template );

do_action( 'jet-woo-builder/woocommerce/after-main-content' );

get_footer( 'shop' );
