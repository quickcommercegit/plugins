<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
* WC Additional Days Per Product Metabox
*/
class WC_Additional_Days_Per_Product_Shipping_Label {
  function __construct() {
    add_filter( 'woocommerce_package_rates', array( $this, 'filter_label' ), 10, 2 );
    add_filter( 'woocommerce_package_rates', array( $this, 'filter_package' ), 20, 2 );
  }

  function filter_label( $rates, $package ) {
    $methods = wc_adpp_allowed_methods();

    foreach ( $rates as $rate_id => $rate ) {
      if ( isset( $methods[ $rate->get_method_id() ] ) && $methods[ $rate->get_method_id() ] ) {
        if ( false !== ( $delivery_time = $this->get_delivery_time( $rate->get_id() ) ) ) {
          $label = $rate->get_label();

          if ( 'local_pickup' === $rate->get_method_id() ) {
            $rate->set_label( "$label (Disponível em $delivery_time dias úteis)" );
          } else {
            $rate->set_label( "$label (Entrega em $delivery_time dias úteis)" );
          }
        }
      }
    }

    return $rates;
  }


  public function filter_package( $rates, $package ) {
    if ( 'no' === get_option( 'wc_additional_days_sum', 'yes' ) ) {
      return $rates;
    }

    foreach ( $rates as $rate_id => $rate ) {
      $label = $rate->get_label();

      // integração com WooCommerce Correios
      $meta_data = $rate->get_meta_data();

      $matches = array();
      preg_match_all( '!\d+!', $label, $matches );
      $matches = array_shift( $matches );

      if ( isset( $meta_data['_delivery_forecast'] ) ) {
        $this->log( 'Prazo salvo em metadata: ' . $label );
        $new_time = $this->max_days( $meta_data['_delivery_forecast'], $package );
        $rate->add_meta_data( '_delivery_forecast', $new_time );

      } elseif ( false !== strpos( $rate->get_method_id(), 'melhorenvio' ) ) {
        $this->log( 'Método do Melhor Envio: ' . $label );

        if ( $matches && isset( $matches[0] ) ) {
          foreach ( $matches as $i => $match ) {
            if ( apply_filters( 'wc_additional_days_per_product_not_replace', false, $i, $rate ) ) {
              continue;
            }

            $new_time = $this->max_days( $match, $package );
            $label    = str_replace( $match . ' ', ' ' . $new_time . ' ', $label );
          }

          $rate->set_label( $label );
        }
      }


      elseif ( isset( $meta_data['delivery_time'] ) ) {
        $this->log( 'Prazo salvo em metadata: ' . $label );
        $new_time = $this->max_days( $meta_data['delivery_time'], $package );
        $rate->add_meta_data( 'delivery_time', $new_time );

      } elseif ( isset( $meta_data['_delivery_mandabem'] ) ) {
        $this->log( '(mandabem) Prazo salvo em metadata: ' . $label );
        $new_time = $this->max_days( $meta_data['_delivery_mandabem'], $package );
        $rate->add_meta_data( '_delivery_mandabem', $new_time );

      } elseif ( isset( $meta_data['Prazo de Entrega'] ) ) {
        $this->log( '(Loggi) Prazo salvo em metadata: ' . $label );
        $new_time = $this->max_days( $meta_data['Prazo de Entrega'], $package );
        $rate->add_meta_data( 'Prazo de Entrega', $new_time );

      } elseif ( $custom_meta = apply_filters( 'wc_adpp_additional_days_custom_metadata', false, $meta_data, $rate ) ) {
        $this->log( 'Prazo salvo em metadata: ' . $label );
        $new_time = $this->max_days( $meta_data[ $custom_meta ], $package );
        $rate->add_meta_data( $custom_meta, $new_time );

      } elseif ( $matches && isset( $matches[0] ) ) {
        $this->log( 'Matches: ' . print_r( $matches, true ) );

        foreach ( $matches as $i => $match ) {
          if ( apply_filters( 'wc_additional_days_per_product_not_replace', false, $i, $rate ) ) {
            continue;
          }

          $new_time = $this->max_days( $match, $package );
          $label    = str_replace( ' ' . $match . ' ', ' ' . $new_time . ' ', $label );
        }

        $rate->set_label( $label );

      } else {
        $this->log( 'Impossível encontrar o prazo neste método: ' . $label );
      }
    }

    return $rates;
  }


  public function get_delivery_time( $id ) {
    $id = filter_var( $id, FILTER_SANITIZE_NUMBER_INT );
    $shipping_method = WC_Shipping_Zones::get_shipping_method( $id );

    $delivery_time = ( array_key_exists( 'delivery_time', $shipping_method->instance_settings ) && '' !== $shipping_method->instance_settings['delivery_time'] ) ? $shipping_method->instance_settings['delivery_time'] : false;

    return $delivery_time;
  }

  // pegar o dia mais alto dentre os produtos para exibir no plugin
  private function max_days( $default_time, $package = array() ) {
    $default_time = (int) $default_time;

    if ( isset( $_GET['product_id'] ) ) {
      $this->log( 'Produto no simulador: ' . $_GET['product_id'] );

      $values = array();

      if ( ! empty( $package['contents'] ) ) {
        $values = array_shift( $package['contents'] );
        $product = $values['data'];
      } else {
        $product         = wc_get_product( $_GET['product_id'] );
      }

      $additional_days = (int) wc_adpp_get_product_additional_time( $product, $values );
    } else {
      $additional_days = $this->package_additional_days( $package );
    }

    $this->log( 'Prazo padrão: ' . $default_time );
    $this->log( 'Dias adicionais: ' . $additional_days );

    $result = ( $default_time + $additional_days ) <= 0 ? 1 : $default_time + $additional_days;

    $this->log( 'Resultado: ' . $result );

    return $result;
  }


  public function package_additional_days( $package = array() ) {

    $cart_items = isset( $package['contents'] ) ? $package['contents'] : WC()->cart->get_cart();
    $additional_days = array();

    foreach ( $cart_items as $item ) {
      $product    = $item['data'];
      if ( $days = wc_adpp_get_product_additional_time( $product, $item ) ) {
        $additional_days[] = (int) $days;
      }
    }

    $this->log( 'Dias adicionais por produto: ' . print_r( $additional_days, true ) );

    if ( 0 === count( $additional_days ) ) {
      return 0;
    }

    return max( $additional_days );
  }


  public function log( $message ) {
    if ( apply_filters( 'wc_additional_days_per_product_debug', false ) ) {
      $log = new WC_Logger();
      $log->add( 'wc-additiona-days-per-product', $message );
    }
  }
}

new WC_Additional_Days_Per_Product_Shipping_Label();
