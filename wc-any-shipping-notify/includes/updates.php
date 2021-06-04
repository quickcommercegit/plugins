<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class WC_Any_Shipping_Notify_Updates {
  function __construct() {
    add_action( 'init', array( $this, 'license' ) );
  }

  public function license() {
    include_once 'license/license.php';

    if ( is_admin() && class_exists( 'FA_Licensing_Framework_New' ) ) {
      new FA_Licensing_Framework_New(
        'WOO_Shipping_Notify',
        'WC Any Shipping Notify',
        WC_Any_Shipping_Notify::get_main_file()
      );
    }
  }
}

new WC_Any_Shipping_Notify_Updates();
