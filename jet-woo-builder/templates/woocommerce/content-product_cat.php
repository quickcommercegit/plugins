<?php
/**
 * The template for displaying product category thumbnails within loops
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/woocommerce/content-product-cat.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.7.0
 */

$template = jet_woo_builder_integration_woocommerce()->get_current_archive_category_template();
$template = apply_filters( 'jet-woo-builder/current-template/template-id', $template );
$content  = jet_woo_builder()->parser->get_template_content( $template );
?>

<li <?php wc_product_cat_class( ' jet-woo-builder-archive-item-' . $category->term_id, $category ); ?>>
	<?php echo apply_filters( 'jet-woo-builder/elementor-views/frontend/archive-item-content', $content, $template, $category ); ?>
</li>