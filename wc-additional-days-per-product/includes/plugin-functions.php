<?php
function wc_adpp_allowed_methods() {
  return apply_filters( 'wc_additional_days_per_product_methods', array(
    'flat_rate'       => true,
    'local_pickup'    => true,
    'free_shipping'   => true,
  ));
}

add_action( 'woocommerce_single_product_summary', 'wc_adpp_product_page_info', 35 );
function wc_adpp_product_page_info( $product = false, $echo = true, $tag = 'p', $cart_item = null ) {
  if ( ! $product ) {
    global $product;
  }

  $message = '';
  $days    = wc_adpp_get_product_additional_time( $product, $cart_item );
  $label   = get_option( 'wc_additional_days_label', 'Prazo de produção' );

  if ( ! $label ) {
    return false;
  }

  if ( 0 === $days ) {
    $message = '<' . $tag . ' class="wc-adpp-info"><span>' . $label . '</span>: imediato</' . $tag . '>';
  } elseif ( 0 < $days ) {
    $message = sprintf( '<' . $tag . ' class="wc-adpp-info"><span>' . $label . '</span>: %s</' . $tag . '>', sprintf( _n( '%s dia útil', '%s dias úteis', $days ), $days ) );
  }

  $result = apply_filters( 'wc_adpp_product_message', $message, $days, $product );

  if ( $echo ) {
    echo $result;
  }

  return $result;
}


function wc_adpp_get_product_additional_time( $product, $cart_item = null ) {
  if ( ! $product ) {
    return false;
  }

  $variation = null;

  if ( $product->is_type( 'variation' ) ) {
    $variation = $product;
    $product   = wc_get_product( $product->get_parent_id() );
  }

  $extra_time = false;

  $cat_days = array( 0 );
  foreach ( $product->get_category_ids() as $cat_id ) {
    $cat_days[] = (int) get_term_meta( $cat_id, 'additional_time', true );
  }

  $global_time     = intval( get_option( 'wc_additional_days_global', 0 ) );
  $additional_days = get_post_meta( $product->get_id(), '_wc_additional_days_per_product', true );

  if ( $global_time > 0 && 0 === max( $cat_days ) && '' === $additional_days ) {
    $extra_time = $global_time;
  } elseif ( 0 < max( $cat_days ) && 0 === intval( $additional_days ) ) {
    $extra_time = max( $cat_days );
  } elseif ( 0 < intval( $additional_days ) ) {
    $extra_time = intval( $additional_days );
  } elseif ( apply_filters( 'wc_adpp_per_product_always_show_message', false ) ) {
    $extra_time = 0;
  }

  if ( has_filter( 'wc_adpp_additional_days_custom_rules' ) ) {
    $extra_time = apply_filters( 'wc_adpp_additional_days_custom_rules', $extra_time, $product, wc_adpp_get_custom_rules() );
  }

  return apply_filters( 'wc_adpp_additional_days', $extra_time, $product, $variation, $cart_item );
}



function wc_adpp_get_custom_rules() {
  return apply_filters( 'wc_adpp_custom_rules', array() );
}
