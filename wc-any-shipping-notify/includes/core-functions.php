<?php

/**
 * Get tracking codes.
 *
 * @param  WC_Order|int $order Order ID or order data.
 *
 * @return array
 */
function wc_any_shipping_get_tracking_codes( $order ) {
  if ( is_numeric( $order ) ) {
    $order = wc_get_order( $order );
  }

  $codes = $order->get_meta( '_wc_any_shipping_notify_tracking_code' );

  // se for apenas um código antigo, usar a primeira transportadora
  if ( ! is_array( $codes ) && '' !== $codes ) {
    $codes = array( $codes => 0 );
  }

  return apply_filters( 'wc_any_shipping_get_tracking_codes', $codes );
}



/**
 * Update tracking code.
 *
 * @param  WC_Order|int $order         Order ID or order data.
 * @param  string       $tracking_code Tracking code.
 * @param  bool         remove         If should remove the tracking code.
 *
 * @return bool
 */
function wc_any_shipping_notify_update_tracking_code( $order, $tracking_code, $shipping_company_slug = '', $remove = false ) {
  $tracking_code = sanitize_text_field( $tracking_code );

  $result = array(
    'failed'         => 'no',
    'removed'        => 'no',
    'added'          => 'no',
    'status_updated' => 'no'
  );

  // Get order instance.
  if ( is_numeric( $order ) ) {
    $order = wc_get_order( $order );
  }

  $tracking_codes = $order->get_meta( '_wc_any_shipping_notify_tracking_code' );

  // $tracking_codes = array_filter( explode( ',', $tracking_codes ) );

  if ( '' === $tracking_code ) {
    if ( method_exists( $order, 'delete_meta_data' ) ) {
      $order->delete_meta_data( '_wc_any_shipping_notify_tracking_code' );
      $order->save();
    } else {
      delete_post_meta( $order->id, '_wc_any_shipping_notify_tracking_code' );
    }

    $result['failed'] = 'yes';

    return $result;

  } elseif ( ! $remove && ! isset( $tracking_codes[ $tracking_code ] ) ) {
    $tracking_codes = ! is_array( $tracking_codes ) ? array() : $tracking_codes;
    $tracking_codes[ $tracking_code ] = $shipping_company_slug;
    $shipping_company_name = wc_any_shipping_notify_get_shipping_company_name( $shipping_company_slug );

    if ( method_exists( $order, 'update_meta_data' ) ) {
      $order->update_meta_data( '_wc_any_shipping_notify_tracking_code', $tracking_codes );
      $order->save();
    } else {
      update_post_meta( $order->id, '_wc_any_shipping_notify_tracking_code', $tracking_codes );
    }

    do_action( 'wcasn_tracking_added', $order, $tracking_code );

    $result['added'] = 'yes';

    $new_status = get_option( 'wc_any_shipping_notify_new_status', 'none' );

    if ( 'none' === $new_status || 'none' !== $new_status && $order->get_status() === $new_status ) {
      // Add order note only.
      $order->add_order_note( sprintf( __( 'Adicionado um código de rastreio para %s: %s.', 'wc-any-shipping-notify' ), $shipping_company_name, $tracking_code ) );
    } else {
      $order->update_status( $new_status, sprintf( __( 'Adicionado um código de rastreio para %s: %s', 'wc-any-shipping-notify' ), $shipping_company_name, $tracking_code ) );

      $result['status_updated'] = $new_status;
    }

    // Send email notification.
    wc_any_shipping_notify_trigger_tracking_code_email( $order, $tracking_code, $shipping_company_slug );

    return $result;

  } elseif ( $remove && isset( $tracking_codes[ $tracking_code ] ) ) {
    $shipping_company_name = wc_any_shipping_notify_get_shipping_company_name( $tracking_codes[ $tracking_code ] );

    unset( $tracking_codes[ $tracking_code ] );

    if ( method_exists( $order, 'update_meta_data' ) ) {
      $order->update_meta_data( '_wc_any_shipping_notify_tracking_code', $tracking_codes );
      $order->save();
    } else {
      update_post_meta( $order->id, '_wc_any_shipping_notify_tracking_code', $tracking_codes );
    }

    $result['removed'] = 'yes';

    // Add order note.
    $order->add_order_note( sprintf( __( 'Removido código de rastreio da transportadora %s: %s', 'wc-any-shipping-notify' ), $shipping_company_name, $tracking_code ) );

    return $result;
  }

  return false;
}


/**
 * Trigger tracking code email notification.
 *
 * @param WC_Order $order         Order data.
 */
function wc_any_shipping_notify_trigger_tracking_code_email( $order, $tracking_code, $shipping_company_slug = '' ) {
  $mailer       = WC()->mailer();
  $notification = $mailer->emails['WC_Any_Shipping_Notify_Email'];

  if ( 'yes' === $notification->enabled ) {
    if ( method_exists( $order, 'get_id' ) ) {
      $notification->trigger( $order->get_id(), $order, $tracking_code, $shipping_company_slug );
    } else {
      $notification->trigger( $order->id, $order, $tracking_code, $shipping_company_slug );
    }
  }
}


function wc_any_shipping_notify_get_shipping_companies() {
  $available = get_option( 'wc_any_shipping_notify_available_companies', array() );

  return apply_filters( 'wc_any_shipping_notify_available_companies', $available );
}

function wc_any_shipping_notify_get_shipping_company_name( $slug ) {
  $available_companies = wc_any_shipping_notify_get_shipping_companies();
  $name = isset( $available_companies[ $slug ] ) ? $available_companies[ $slug ]['name'] : 'Transportadora';

  return apply_filters( 'wc_any_shipping_notify_shipping_company_name', $name );
}


function wc_any_shipping_notify_get_shipping_company_url( $slug, $tracking_code = false, $order = false ) {
  $available_companies = wc_any_shipping_notify_get_shipping_companies();
  $url = isset( $available_companies[ $slug ]['url'] ) ? $available_companies[ $slug ]['url'] : wc_get_page_permalink( 'myaccount' );

  if ( $tracking_code ) {
    $url = str_replace( '{tracking_code}', $tracking_code, $url );
  }

  return apply_filters( 'wc_any_shipping_notify_shipping_company_url', $url, $slug, $tracking_code, $order );
}
