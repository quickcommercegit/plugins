<?php
/**
*
*/
class WC_Simulador_Parcelas_Init {

  public static $exibicao_texto_formatado;
  public static $exibicao_texto_formatado_loop;
  public static $exibicao_texto_formatado_main_price;

  public static $change_schema;

  public static $always_show_boleto;


  function __construct() {
    self::set_variables();
  }


  public static function set_variables() {
    self::set_global_variables();
  }


  public static function set_global_variables() {
    self::$exibicao_texto_formatado      = get_option( 'wc_simulador_parcelas_texto_formatado', '' );
    self::$exibicao_texto_formatado_loop = get_option( 'wc_simulador_parcelas_texto_formatado_loop', '' );
    self::$exibicao_texto_formatado_main_price = get_option( 'wc_simulador_parcelas_texto_formatado_main_price', '' );
    self::$change_schema                 = get_option( 'wc_simulador_parcelas_alterar_schema', 'no' );

    self::$always_show_boleto            = get_option( 'wc_simulador_parcelas_always_show_boleto', 'no' );
  }

  /**
   *
  */
  public static function get_main_price_discount( $product = false ) {
    // default discount
    $discount = get_option( 'wc_simulador_parcelas_main_price', 0 );
    return apply_filters( 'wcsp_get_main_price_discount', $discount, $product );
  }


  /**
   *
  */
  public static function get_text_after_price( $product = false ) {
    // default value
    $text = get_option( 'wc_simulador_parcelas_depois_preco', __( 'no boleto', 'wc-simulador-parcelas' ) );
    return apply_filters( 'wcsp_text_after_price', $text, $product );
  }


  /**
   *
  */
  public static function get_table_visibility( $product = false ) {
    $table_visibility = get_option( 'wc_simulador_parcelas_exibicao_tabela_completa', 'hide' );
    return apply_filters( 'wcsp_table_visibility', $table_visibility, $product );
  }


  /**
   *
  */
  public static function get_table_formatted_text( $product = false ) {
    $text = get_option( 'wcsp_table_formatted_text', '{{ parcelas }}x de {{ valor }} {{ juros }}' );
    return apply_filters( 'wcsp_table_formatted_text', $text, $product );
  }


  /**
   *
  */
  public static function get_shop_page_view( $product = false ) {
    $shop_page = get_option( 'wc_simulador_parcelas_exibicao_shop_page', 'hide' );
    return apply_filters( 'wcsp_shop_page', $shop_page, $product );
  }

  /**
   *
  */
  public static function get_single_page_view( $product = false ) {
    $single_page = get_option( 'wc_simulador_parcelas_exibicao_single_page', 'hide' );
    return apply_filters( 'wcsp_single_page', $single_page, $product );
  }

  /**
   *
  */
  public static function get_ticket_visibility( $product = false ) {
    $visibility = get_option( 'wc_simulador_parcelas_ticket_visibility', 'both' );
    return apply_filters( 'wcsp_ticket_visibilty', $visibility, $product );
  }

  /**
   *
  */
  public static function get_min_installment( $product = false ) {
    $min = get_option( 'wc_simulador_parcelas_valor_minimo_parcela', 0 );
    return apply_filters( 'wcsp_min_installment', $min, $product );
  }

  /**
   *
  */
  public static function get_max_installment_no_fee( $product = false ) {
    $default = get_option( 'wc_simulador_parcelas_maximo_parcelas_sem_juros', 0 );
    return apply_filters( 'wcsp_max_installments_no_fee', $default, $product );
  }

  /**
   *
  */
  public static function get_installments_limit( $product = false ) {
    $limit = get_option( 'wc_simulador_parcelas_maximo_parcelas', 0 );
    return apply_filters( 'wcsp_installments_limit', $limit, $product );
  }

  /**
   *
  */
  public static function get_fee( $product = false, $installments = 1 ) {
    $fee = get_option( 'wc_simulador_parcelas_taxa_de_juros', 0 );
    return apply_filters( 'wcsp_fee', $fee, $product, $installments );
  }

  /**
   *
  */
  public static function show_in_cart() {
    return get_option( 'wcsp_show_in_cart', 'no' );
  }
}
