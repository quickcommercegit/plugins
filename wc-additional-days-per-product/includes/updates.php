<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class WC_Additional_Days_Per_Product_Updates {
  function __construct() {
    add_action( 'init', array( $this, 'license' ) );
  }

  public function license() {
    include_once 'license/license.php';

    if ( is_admin() && class_exists( 'FA_Licensing_Framework_New' ) ) {
      new FA_Licensing_Framework_New(
        'wc-additional-days-per-product',
        'WC Dias adicionais de entrega por produto',
        WC_Additional_Days_Per_Product::get_main_file()
      );
    }
  }
}

new WC_Additional_Days_Per_Product_Updates();
