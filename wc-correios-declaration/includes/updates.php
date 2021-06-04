<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class WC_Correios_Shipping_Declaration_Updates {
  function __construct() {
    add_action( 'init', array( $this, 'license' ) );
  }

  public function license() {
    include_once 'license/license.php';

    if ( is_admin() && class_exists( 'FA_Licensing_Framework_New' ) ) {
      new FA_Licensing_Framework_New(
        'WCD',
        'WC Correios - Declaração de conteúdo',
        WC_Correios_Shipping_Declaration::get_main_file()
      );
    }
  }
}

new WC_Correios_Shipping_Declaration_Updates();
