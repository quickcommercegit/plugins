<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
* WC Additional Days Per Product Metabox
*/
class WC_Additional_Days_Per_Product_Metabox {

  function __construct() {
    add_action( 'woocommerce_product_options_shipping', array( $this, 'custom_fields' ) );
    add_action( 'woocommerce_process_product_meta', array( $this, 'save_data' ) );
  }

  public function custom_fields() {
    echo '<div class="options_group">';
    woocommerce_wp_text_input(
      array(
        'id'                => '_wc_additional_days_per_product',
        'label'             => 'Dias adicionais para entrega',
        'placeholder'       => '0',
        'desc_tip'          => 'true',
        'description'       => 'Total de dias a serem somados ao prazo de entrega deste produto.',
        'type'              => 'number',
        'custom_attributes' => array(
          'step'      => '1',
          'min'       => '0'
        )
      )
    );

    foreach ( wc_adpp_get_custom_rules() as $rule_id => $rule ) {
      woocommerce_wp_text_input(
        array(
          'id'                => '_wc_additional_days_per_product_' . $rule_id,
          'label'             => 'Dias adicionais: ' . $rule['label_append'],
          'placeholder'       => '0',
          'desc_tip'          => 'true',
          'description'       => $rule['description'],
          'type'              => 'number',
          'custom_attributes' => array(
            'step'      => '1',
          )
        )
      );
    }

    echo '</div>';
  }


  /**
   * Save meta box content.
   *
   * @param int $post_id Post ID
   */
  public function save_data( $post_id ) {

    if ( isset( $_POST['_wc_additional_days_per_product'] ) ) {
      update_post_meta( $post_id, '_wc_additional_days_per_product', $_POST['_wc_additional_days_per_product'] );
    }

    foreach ( wc_adpp_get_custom_rules() as $rule_id => $rule ) {
      if ( isset( $_POST['_wc_additional_days_per_product_' . $rule_id ] ) ) {
        update_post_meta( $post_id, '_wc_additional_days_per_product_' . $rule_id , $_POST['_wc_additional_days_per_product_' . $rule_id ] );
      }
    }
  }

}

function init_wc_adpp_metabox() {
  return new WC_Additional_Days_Per_Product_Metabox();
}

add_action( 'admin_init', 'init_wc_adpp_metabox' );
