<?php

namespace WC_Juno\functions;

function get_juno_integration_id () {
	return 'juno-integration';
}

function get_private_token ( $sandbox = null ) {
	if ( null === $sandbox ) {
		$sandbox = is_sandbox_enabled();
	}

	$key = ( $sandbox ? 'sandbox_' : '' ) . 'token';

	return get_settings_option( $key );
}

function get_public_token () {
	return get_settings_option( 'public_token' );
}

function is_sandbox_enabled () {
	return 'yes' === get_settings_option( 'test_mode' );
}

function get_settings_option ( $option = '', $default = '' ) {
	$id = get_juno_integration_id();
	$settings = \get_option( "woocommerce_${id}_settings" );
	return ! empty( $option ) ? array_get( $settings, $option, $default ) : $settings;
}
