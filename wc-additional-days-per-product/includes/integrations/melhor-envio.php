<?php
add_action( 'woocommerce_shipping_method_add_rate_args', 'wcadpp_melhorenvio_product' );
function wcadpp_melhorenvio_product( $args ) {
  if ( ! isset( $args['meta_data']['delivery_time'] ) ) {
    return $args;
  }

  if ( isset( $_POST['data']['id_produto'], $_POST['action'] ) && 'cotation_product_page' === $_POST['action'] ) {

    $product_id = intval( $_POST['data']['id_produto'] );
    $product    = wc_get_product( $product_id );

    if ( ! $product ) {
      return $args;
    }

    $time = wc_adpp_get_product_additional_time( $product );

    if ( ! $time ) {
      return $args;
    }

    $label = $args['label'];
    $delivery_time = isset( $args['meta_data']['delivery_time'] ) ? $args['meta_data']['delivery_time'] : '';

    $matches = array();
    preg_match_all( '!\d+!', $label, $matches );
    $matches = array_shift( $matches );

    if ( $matches && isset( $matches[0] ) ) {
      foreach ( $matches as $i => $match ) {
        $new_time = $match + $time;
        $label = str_replace( $match . ' ', ' ' . $new_time . ' ', $label );
        $delivery_time = str_replace( $match . ' ', ' ' . $new_time . ' ', $delivery_time );
      }

      $args['label'] = $label;

      if ( $delivery_time ) {
        $args['meta_data']['delivery_time'] = $delivery_time;
      }
    }
  }

  return $args;
}
