<?php
/**
 * JS Enqueue.
 *
 * @package Ultimate_Dashboard_PRO
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function ( $module ) {

	if ( $module->screen()->is_branding() ) {

		// Color pickers.
		wp_enqueue_script( 'wp-color-picker' );

		// Settings page.
		wp_enqueue_script( 'udb-settings', ULTIMATE_DASHBOARD_PLUGIN_URL . '/assets/js/settings.js', array( 'jquery' ), ULTIMATE_DASHBOARD_PLUGIN_VERSION, true );

		// Branding settings.
		wp_enqueue_script( 'udb-branding', ULTIMATE_DASHBOARD_PRO_PLUGIN_URL . '/modules/branding/assets/js/white-label.js', array( 'udb-settings' ), ULTIMATE_DASHBOARD_PRO_PLUGIN_VERSION, true );

	}

};
