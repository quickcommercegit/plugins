<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class WC_Simulador_Parcelas_Updates {
  function __construct() {
    add_action( 'init', array( $this, 'license' ) );
  }

  public function license() {
    include_once 'license/license.php';

    if ( is_admin() && class_exists( 'FA_Licensing_Framework_New' ) ) {
      new FA_Licensing_Framework_New(
        'wc-simulador-parcelas',
        __( 'WC Simulador de Parcelas', 'wc-simulador-parcelas' ),
        WC_Simulador_Parcelas::get_main_file()
      );
    }
  }
}

new WC_Simulador_Parcelas_Updates();
