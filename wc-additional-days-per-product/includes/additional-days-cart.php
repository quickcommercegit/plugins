<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
* WC Additional Days Per Product | Cart-Checkout-Emails
*/
class WC_Additional_Days_Per_Product_Display_Cart {
  private $visible_to_customer = false;

  function __construct() {
    if ( 'yes' === get_option( 'wc_additional_days_show_in_cart', 'no' ) ) {
      add_filter( 'woocommerce_get_item_data', array( $this, 'cart_name' ), 10, 2 );

      $this->visible_to_customer = true;
    }

    add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'save_item_data' ), 10, 4 );
    add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_order_data' ) );
  }


  public function cart_name( $item_data, $cart_item ) {
    $text = strip_tags( wc_adpp_product_page_info( $cart_item['data'], false, 'small', $cart_item ) );
    $text = explode( ':', $text );

    if ( 2 === count( $text ) ) {
      $item_data['_production_time'] = array(
        'key'     => $text[0],
        'display' => $text[1],
      );
    }

    return $item_data;
  }


  public function save_item_data( $item, $cart_item_key, $values, $order ) {
    $text = strip_tags( wc_adpp_product_page_info( $values['data'], false, 'small' ) );
    $text = explode( ':', $text );

    if ( 2 === count( $text ) ) {
      $key = $this->visible_to_customer ? $text[0] : '_Prazo de Produção';

      $item->update_meta_data( $key, $text[1] );
    }
  }


  public function save_order_data( $order_id ) {
    $additional_days = array();
    foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
      if ( $days = wc_adpp_get_product_additional_time( $values['data'], $values ) ) {
        $additional_days[] = (int) $days;
      }
    }

    if ( ! empty( $additional_days ) && 0 < count( $additional_days ) ) {
      $time = max( $additional_days );

      update_post_meta( $order_id, '_wc_order_additional_days', $time );
    }
  }
}

new WC_Additional_Days_Per_Product_Display_Cart();
