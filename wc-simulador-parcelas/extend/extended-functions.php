<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

// se tiver apenas uma parcela, não exibe a tabela
add_filter( 'wc_simulador_parcelas_formatted_text', 'custom_formatted_installments', 10, 5 );
function custom_formatted_installments( $final_text, $parcela, $find, $replace, $parcelamento ) {
  if ( 1 === count( $parcelamento ) ) {
    return;
  }

  return $final_text;
}


// Integração com plugin wc-dynamic-pricing-and-discounts
add_filter( 'wcsp_set_values_price', 'wcsp_wc_dynamic_pricing_and_discounts_integration', 10, 2 );
function wcsp_wc_dynamic_pricing_and_discounts_integration( $price, $product ) {

  if ( ! class_exists( 'RP_WCDPD', false ) ) {
    return $price;
  }

  // if ( 'variation' === $product->get_type() ) {

  // }

  if ( 'variable' === $product->get_type() ) {
    $prices = array();
    foreach ( $product->get_available_variations() as $var ) {
      // Load variation
      $variation = wc_get_product( $var['variation_id'] );
      $prices[]  = $variation->get_price();
    }

    return min( $prices );
  }

  return $price;
}
