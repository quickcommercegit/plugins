<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/woocommerce/content-product.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$template = jet_woo_builder_integration_woocommerce()->get_current_archive_template();
$template = apply_filters( 'jet-woo-builder/current-template/template-id', $template );
$content  = jet_woo_builder()->parser->get_template_content( $template );
?>

<li <?php wc_product_class( ' jet-woo-builder-product jet-woo-builder-archive-item-' . $product->get_id(), $product ); ?> data-product-id="<?php echo $product->get_id(); ?>">
	<?php echo apply_filters( 'jet-woo-builder/elementor-views/frontend/archive-item-content', $content, $template, $product ); ?>
</li>
