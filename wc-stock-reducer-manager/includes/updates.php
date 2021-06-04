<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class WC_Stock_Reducer_Manager_Updates {
  function __construct() {
    add_action( 'init', array( $this, 'license' ) );
  }

  public function license() {
    include_once 'license/license.php';

    if ( is_admin() && class_exists( 'FA_Licensing_Framework_New' ) ) {
      new FA_Licensing_Framework_New (
        'wc-stock-reducer',
        'WC Redução e restauração de estoque',
        WC_Stock_Reducer_Manager::get_main_file()
      );
    }
  }
}

new WC_Stock_Reducer_Manager_Updates();
