<?php
/**
 * Wishlist loop item title template
 */

$title      = jet_cw_functions()->get_title( $_product );
$open_wrap  = '<' . $heading_tag . ' class="jet-cw-product-title" >';
$close_wrap = '</' . $heading_tag . '>';

if ( 'yes' !== $widget_settings['show_item_title'] || '' === $title ) {
	return;
}

echo $open_wrap . $title . $close_wrap;