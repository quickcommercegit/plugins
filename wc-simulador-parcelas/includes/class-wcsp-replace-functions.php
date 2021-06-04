<?php
/**
 * WC Simulador Apply Rules
 *
 * @author   Fernando Acosta
 * @category Admin
 * @package  Includes
 * @version  1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

/**
* Parcelas
*/
class WCSP_Replace_Functions extends WCSP_Apply_Rules {
  /**
   * Função obsoleta
   *
   * Formatar a exibição do parcelamento completo
  */
  public function parcelamento_completo( $product_id = false ) {
    if ( $product_id ) {
      $product = wc_get_product( $product_id );
    } else {
      global $product;
    }

    if ( ! $product ) {
      return;
    }

    $price = $product->get_price( 'view' );

    $parcelamento = $this->set_values( 'all', $price, $product, false );

    if ( ! $parcelamento ) {
      return;
    }

    do_action( 'wc_simulador_parcelas_before_installments_table' );

    $formatted_table = '<ul class="wc-simulador-parcelas-payment-options">';
      foreach ( $parcelamento as $key => $parcela ) {
        $find = array_keys( $this->strings_to_replace( $parcela ) );
        $replace = array_values( $this->strings_to_replace( $parcela ) );
        $final_text = '<li class="' . $parcela['class'] . '">' . str_replace( $find, $replace, self::get_table_formatted_text( $product ) ) . '</li>';

        $formatted_table .= apply_filters( 'wc_simulador_parcelas_formatted_text', $final_text, $parcela, $find, $replace, $parcelamento );
      }
    $formatted_table .= '</ul>';

    echo apply_filters( 'wc_simulador_parcelas_formatted_installments', $formatted_table, $parcelamento );

    do_action( 'wc_simulador_parcelas_after_installments_table' );
  }
}
