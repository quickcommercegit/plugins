<?php

namespace WC_Juno;

use WC_Juno\functions as h;

// register woocommerce payment gateways
add_filter( 'woocommerce_payment_gateways', function ( $gateways ) {
	if ( class_exists( 'WC_Subscriptions_Order' ) && function_exists( 'wcs_create_renewal_order' ) ) {
		$gateways[] = __NAMESPACE__ . '\\Gateway\\Credit_Card_Subscription';
		$gateways[] = __NAMESPACE__ . '\\Gateway\\Bank_Slip_Subscription';
	} else {
		$gateways[] = __NAMESPACE__ . '\\Gateway\\Credit_Card';
		$gateways[] = __NAMESPACE__ . '\\Gateway\\Bank_Slip';
	}

	$gateways[] = __NAMESPACE__ . '\\Gateway\\Pix';

	return $gateways;
});

add_action( 'admin_init', function() {
	$installed_version = get_option( 'juno_installed_version', null );

	if ( WOO_JUNO_PLUGIN_VERSION === $installed_version )  {
		return;
	}

	$callbacks = array(
		'2.3.0' => array( Install::class, 'install_version_230' ),
	);

	foreach ( $callbacks as $version => $update_callback ) {
		if ( version_compare( WOO_JUNO_PLUGIN_VERSION, $installed_version ) ) {
			call_user_func( $update_callback );
		}
	}

	update_option( 'juno_installed_version', WOO_JUNO_PLUGIN_VERSION );
});

// register the integration page
add_filter( 'woocommerce_integrations', function ( $integrations ) {
	$integrations[] = __NAMESPACE__ . '\\Settings\\General';
	return $integrations;
} );

// disable cache during development
\add_filter( h\prefix( 'remember_cache_disabled' ), '__return_true' );

// add other hooks
$ajax_remove_user_credit_card = h\config_set( '$ajax_remove_user_credit_card', new Ajax\Remove_User_Credit_Card() );
$ajax_remove_user_credit_card->add_hooks();

$ajax_transfer_balance = h\config_set( '$ajax_transfer_balance', new Ajax\Transfer_Balance() );
$ajax_transfer_balance->add_hooks();

$notification_handler = h\config_set( '$notification_handler', new Notification_Handler() );
$notification_handler->add_hooks();

if ( class_exists( '\WC_Juno\Notification_Handler_Api_V2' ) ) {
  $notification_handler_v2 = h\config_set( '$notification_handler_v2', new Notification_Handler_Api_V2() );
  $notification_handler_v2->add_hooks();
}

$order_actions = h\config_set( '$order_actions', new Orders\Actions() );
$order_actions->add_hooks();

$widget_balance = h\config_set( '$widget_balance', new Dashboard_Widget\Balance() );
$widget_balance->add_hooks();
