<?php
/**
*
*/
class WC_Simulador_Parcelas_Calculate_Values {
  /**
   * Calcular o valor total de uma parcela com juros.
   *
   * @since 2.0
   * @param float $value Valor base para o cálculo
   * @param float $fee Taxa de juros
   * @param int $installments Total de parcelas
   * @return float valor da parcela
   */
  public static function calculate_installment_with_fee( $value, $fee, $installments ) {
    $percentage = wc_format_decimal( $fee ) / 100.00;

    $installment_price = $value * $percentage * ( ( 1 + $percentage ) ** $installments ) / ( ( ( 1 + $percentage ) ** $installments ) - 1 );

    return apply_filters( 'wcsp_installment_with_fee', $installment_price, $value, $fee, $installments );
  }

  /**
   * Calcular o valor total de uma parcela SEM juros.
   *
   * @since 2.0
   * @param float $value Valor base para o cálculo
   * @param int $installments Total de parcelas
   * @return float valor da parcela
   */
  public static function calculate_installment_no_fee( $value, $installments ) {
    $installment_price = $value / $installments;
    return apply_filters( 'wcsp_installment_no_fee', $installment_price, $value, $installments );
  }

  /**
   * Calcular o valor final de um parcelamento
   *
   * @since 2.0
   * @param float $value o valor da parcela
   * @param int $installments_total o número total de parcelas
   * @return float valor total que será pago no parcelamento sem juros
   */
  public static function calculate_final_price( $value, $installments_total ) {
    return apply_filters( 'wcsp_final_price', $value * $installments_total, $value, $installments_total );
  }

  /**
   * Calcular o valor de um produto APÓS aplicar um desconto
   *
   * @since 2.0
   * @param float $value Valor original
   * @param int|float $discount Valor do desconto oferecido
   * @return float valor final com desconto
   */
  public static function calculate_discounted_price( $value, $discount, $product = false ) {
    // it's not an empty value
    if ( $discount ) {
      $price = $value * ( ( 100 - $discount ) / 100 );
    } else {
      $price = $value;
    }

    return apply_filters( 'wcsp_discounted_price', $price, $value, $discount, $product );
  }
}
