<?php

namespace WC_Juno\functions;

/**
 * A helper to include PHP files with custom data
 */
function include_php_template ( $template_path, $data = [] ) {
	$template_base_path = config_get( 'ROOT_DIR' ) . '/' . config_get( 'TEMPLATES_DIR' );
	$template_path = "$template_base_path/$template_path";
	$data = apply_filters( prefix( 'template_default_data' ), $data );

	extract( $data, \EXTR_OVERWRITE );
	include $template_path;
}

function include_wc_template ( $template_path, $data = [] ) {
	$wc_templates_path = config_get( 'ROOT_DIR' ) . '/' . config_get( 'TEMPLATES_DIR' ) . '/woocommerce/';
	wc_get_template(
		$template_path,
		$data,
		'',
		$wc_templates_path
	);
}
