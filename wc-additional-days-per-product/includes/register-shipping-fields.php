<?php
/**
*
*/
class WC_Additional_Days_Per_Product_Register_Shipping_Fields {

  function __construct() {
    $this->init();
  }

  public function init() {
    $methods = wc_adpp_allowed_methods();

    foreach ( $methods as $method_id => $add_label ) {
      add_filter( 'woocommerce_shipping_instance_form_fields_' . $method_id, array( $this, 'custom_fields' ), 10 );
    }
  }

  public function custom_fields( $fields ) {
    $fields['delivery_time'] = array(
      'title' => 'Prazo de entrega',
      'type' => 'number',
      'placeholder' => 10,
      'description' => 'Informe o prazo padrão para entrega neste método.',
      'default' => 10,
      'desc_tip' => true,
    );

    return $fields;
  }
}

function init_wc_adpp_register_shipping_fields() {
  return new WC_Additional_Days_Per_Product_Register_Shipping_Fields();
}

add_action( 'admin_init', 'init_wc_adpp_register_shipping_fields' );
