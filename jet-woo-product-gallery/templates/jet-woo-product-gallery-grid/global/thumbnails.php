<?php
/**
 * Product Gallery Grid thumbnails template
 */

$image_src = wp_get_attachment_image_src( $attachment_id, 'full' );
$image     = wp_get_attachment_image( $attachment_id, $images_size, false, array(
	'class'                   => 'wp-post-gallery',
	'title'                   => get_post_field( 'post_title', $attachment_id ),
	'data-caption'            => get_post_field( 'post_excerpt', $attachment_id ),
	'data-src'                => $image_src[0],
	'data-large_image'        => $image_src[0],
	'data-large_image_width'  => $image_src[1],
	'data-large_image_height' => $image_src[2],
) );

$this->set_render_attribute( 'image_link', 'class', 'jet-woo-product-gallery__image-link' );
$this->set_render_attribute( 'image_link', 'href', esc_url( $image_src[0] ) );
$this->set_render_attribute( 'image_link', 'itemprop', 'image' );
$this->set_render_attribute( 'image_link', 'title', get_post_field( 'post_title', $attachment_id ) );
$this->set_render_attribute( 'image_link', 'rel', 'prettyPhoto' . $gallery );
?>

<div class="jet-woo-product-gallery__image-item <?php echo implode( ' ', $column_classes ); ?>">
	<div class="jet-woo-product-gallery__image <?php echo $zoom ?>">
		<?php if ( $enable_gallery ) {
			jet_woo_product_gallery_functions()->get_gallery_trigger_button( $this->__render_icon( 'gallery_button_icon', '%s', '', false ) );
		} ?>
		<a <?php $this->print_render_attribute_string( 'image_link' ); ?>>
			<?php echo $image; ?>
		</a>
	</div>
</div>