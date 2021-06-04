<?php
/**
*
*/
class WC_Correios_Shipping_Declaration_Customer_Extra_Fields {
  function __construct() {
    add_filter( 'woocommerce_order_formatted_billing_address', array( $this, 'new_fields' ), 10, 2 );
    add_filter( 'woocommerce_order_formatted_shipping_address', array( $this, 'new_fields' ), 10, 2 );

    add_filter( 'woocommerce_localisation_address_formats', array( $this, 'address_format' ), 20 );
    add_filter( 'woocommerce_formatted_address_replacements', array( $this, 'address_replacements' ), 10, 2 );
  }


  public function new_fields( $address, $order ) {
    if ( isset( $_GET['action'] ) && 'print_declaration' === $_GET['action'] ) {
      $address['first_name']  = $address['first_name'];
      $address['billing_cpf'] = $order->get_meta( '_billing_cpf' );
    }

    return $address;
  }


  public function address_format( $formats ) {
    if ( isset( $_GET['action'] ) && 'print_declaration' === $_GET['action'] ) {

      // Integration with Extra Checkout Fields For Brazil
      if ( class_exists( 'Extra_Checkout_Fields_For_Brazil' ) ) {
        $formats['BR'] = "<strong>{name}</strong>\n{billing_cpf}\n{company}\n{address_1}, {number}{address_2}\n{neighborhood}{city}\n{state}\n<span class=\"shipping-postcode\">CEP: {postcode}</span>\n{country}";
      } else {
        $formats['BR'] = "<strong>{name}</strong>\n{billing_cpf}\n{company}\n{address_1}\n{address_2}\n{city}\n{state}\n<span class=\"shipping-postcode\">CEP: {postcode}</span>\n{country}";
      }

    }

    // remove CPF
    if ( ! apply_filters( 'wc_correios_declaration_show_cpf', true ) ) {
      $formats['BR'] = str_replace( "\n{billing_cpf}", '', $formats['BR'] );
    }

    return $formats;
  }


  public function address_replacements( $replacements, $args ) {
    if ( isset( $_GET['action'], $args['billing_cpf'] ) && 'print_declaration' === $_GET['action'] ) {
      $replacements['{billing_cpf}'] = '' === $args['billing_cpf'] ? '' : 'CPF: ' . $args['billing_cpf'];
      $replacements['{address_2}']   = ( ! empty( $args['address_2'] ) ) ? ' | ' . $args['address_2'] : $args['address_2'];
      $replacements['{neighborhood}']   = ( ! empty( $args['neighborhood'] ) ) ? $args['neighborhood'] . ' - ' : $args['neighborhood'];
    }

    return $replacements;
  }
}

new WC_Correios_Shipping_Declaration_Customer_Extra_Fields();
