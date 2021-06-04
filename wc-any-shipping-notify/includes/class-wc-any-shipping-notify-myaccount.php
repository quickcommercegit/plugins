<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}


/**
 * My Account page.
 */
class WC_Any_Shipping_Notify_My_Account {
  function __construct() {
    add_action( 'woocommerce_order_details_after_order_table', array( $this, 'tracking_codes' ), 1 );
  }

  public function tracking_codes( $order ) {
    if ( is_checkout() ) {
      return;
    }

    $tracking_codes = wc_any_shipping_get_tracking_codes( $order );
    $tracking_codes = ! is_array( $tracking_codes ) ? array() : $tracking_codes;
    $codes          = array();

    foreach ( $tracking_codes as $tracking_code => $shipping_company_slug ) {
      $codes[] = array(
        'code' => $tracking_code,
        'company' => wc_any_shipping_notify_get_shipping_company_name( $shipping_company_slug ),
        'url' => wc_any_shipping_notify_get_shipping_company_url( $shipping_company_slug, $tracking_code, $order ),
      );
    }

    if ( $codes ) {
      wc_get_template(
        'myaccount/shipping-notify-tracking-codes.php',
        array(
          'title' => apply_filters( 'wcasn_tracking_title', 'Acompanhe sua entrega' ),
          'codes' => $codes,
        ),
        '',
        WC_Any_Shipping_Notify::get_templates_path()
      );
    }
  }

}

new WC_Any_Shipping_Notify_My_Account();
