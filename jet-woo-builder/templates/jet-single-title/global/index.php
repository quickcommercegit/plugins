<?php
/**
 * Product title template
 */
$settings = $this->get_settings();

$full_title    = jet_woo_builder_template_functions()->get_product_title();
$title         = jet_woo_builder_tools()->trim_text(
	$full_title,
	isset( $settings['title_length'] ) ? $settings['title_length'] : 1,
	$settings['title_trim_type'],
	'...'
);
$title_tooltip = '';

if ( -1 !== $settings['title_length'] && 'yes' === $settings['title_tooltip'] ) {
	$title_tooltip = 'title="' . $full_title . '"';
}

echo '<h1 class="product_title entry-title" ' . $title_tooltip . '>' . $title . '</h1>';
