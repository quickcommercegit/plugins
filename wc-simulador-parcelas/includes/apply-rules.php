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
class WCSP_Apply_Rules extends WC_Simulador_Parcelas_Init {

  public static $count = 0;

  function __construct() {
    parent::__construct();
    $this->init();
    $this->change_schema();
  }


  // apply filters
  private function init() {
    add_action( 'template_redirect', array( $this, 'display_table' ), 10 );
    add_filter( 'woocommerce_get_price_html', array( $this, 'woocommerce_get_price_html' ), 999, 2 );
    add_action( 'woocommerce_single_product_summary', array( __CLASS__, 'clear_product_function' ), 9999 );

    // manual handler for loop count
    add_shortcode( 'wcsp_open_single_product', array( __CLASS__, 'open_single_product' ) );
    add_shortcode( 'wcsp_close_single_product', array( __CLASS__, 'close_single_product' ) );

    add_shortcode( 'wcsp_table', array( $this, 'render_full_installment_shortcode' ) );

    add_action( 'woocommerce_cart_totals_before_order_total', array( $this, 'display_discount_on_cart' ) );
  }

  /**
   * https://www3.bcb.gov.br/CALCIDADAO/publico/exibirFormFinanciamentoPrestacoesFixas.do?method=exibirFormFinanciamentoPrestacoesFixas
   *
   * Calcular o parcelamento
   *
  */
  protected function set_values( $return, $price = false, $product = false, $echo = true ) {
    $installments_info = array();

    if ( ! $price ) {
      global $product;

      if ( ! $product ) {
        return $return;
      }

      $price = wc_get_price_to_display( $product );
    }

    $price = apply_filters( 'wcsp_set_values_price', $price, $product );

    if ( ! $this->is_available( $product ) ) {
      return false;
    }

    $installments_limit = self::get_installments_limit( $product );

    // get all installments options till the limit
    for ( $i = 1; $i <= $installments_limit; $i++ ) {

      $fee = self::get_fee( $product, $i );

      // Se o juros for zero, utilza uma só fórmula para tudo
      if ( 0 == $fee ) {
        $installments_info[] = self::get_installment_details_without_interest( $price, $i );
        continue;
      }

      $max_installment_interest_free = self::get_max_installment_no_fee( $product );

      // set the installments with no fee
      if ( $i <= $max_installment_interest_free ) {
        // return values for this installment
        $installments_info[] = self::get_installment_details_without_interest( $price, $i );
      } else {
        $installments_info[] = self::get_installment_details_with_interest( $price, $fee, $i );
      }

    }

    $min_installment = self::get_min_installment( $product );

    foreach ( $installments_info as $key => $installment ) {
      if ( $installment['installment_price'] < $min_installment && 0 < $key ) {
        unset( $installments_info[ $key ] );
      }
    }

    return $this->formatting_display( $installments_info, $return, $echo );
  }

  private function change_schema() {
    if ( 'yes' === self::$change_schema && ! is_admin() ) {
      include_once 'class-wcsp-schema.php';
    }
  }


  /**
   * Exibir o parcelamento na listagem de produtos
  */
  public function loop_price() {
    echo '<span class="wc-simulador-parcelas-parcelamento-loop">';
      $this->set_values( $this->exibicao_shop_page );
    echo '</span>';
  }

  public static function hook() {
    if ( self::is_main_product_price() ) {
      $action = 'main_price';
    } else {
      $action = 'loop';
    }

    return $action;
  }

  public static function is_main_product_price() {
    if ( is_product() ) {
      return ( 0 == self::$count );
    }
    return false;
  }


  public static function clear_product_function() {
    self::$count++;
  }

  /**
   * Exibir o parcelamento na página interna do produto
  */
  public function single_product_price( $product, $original_price = false ) {
    if ( null !== ( $pre_value = apply_filters( 'wcsp_pre_installments_price', null, $product, $original_price ) ) ) {
      return $pre_value;
    }


    if ( self::is_main_product_price() ) {
      $is_product = true;
    } else {
      $is_product = false;
    }

    $html = '';

    if ( $original_price && apply_filters( 'wcsp_original_with_credit_card', false ) ) {
      $html .= apply_filters( 'wcsp_show_original_price_credit_card', $original_price, $product );
    }

    $exibicao_single_page = self::get_single_page_view( $product );
    $exibicao_shop_page   = self::get_shop_page_view( $product );

    $exibicao = ( $is_product ) ? $exibicao_single_page : $exibicao_shop_page;
    $price    = $this->set_values( $exibicao, wc_get_price_to_display( $product ), $product, false );

    if ( '' != $price ) {

      $html .= ' <span class="wc-simulador-parcelas-parcelamento-info-container">';
        $html .= $price;
      $html .= ' </span>';

    }

    return $html;
  }

  /**
   * Exibir todas as opções de parcelamento na página do produto
  */
  public function display_table() {
    global $post;

    if ( $post && 'product' === get_post_type( $post ) && is_singular( get_post_type( $post ) ) ) {
      $product = wc_get_product( $post->ID );
      $exibicao_tabela_completa = self::get_table_visibility( $product );
      $table_hook = apply_filters( 'wcsp_table_hook', 'woocommerce_single_product_summary', apply_filters( 'wcsp_table_priority', 100 ), $product );

      switch ( $exibicao_tabela_completa ) {
        case 'abaixo_botao_comprar':
          add_action( $table_hook, array( $this, 'render_full_installment' ), 30 );
          break;
        case 'exibir_como_aba':
          add_filter( 'woocommerce_product_tabs', array( $this, 'custom_tab' ) );
          break;
        default:
          return;
          break;
      }
    }
  }

  /**
   * Definir uma tab customizada, se necessário
  */
  public function custom_tab( $tabs ) {

    $installment = $this->set_values( 'all', false, false, false );

    if ( ! $installment ) {
      return $tabs;
    }

    $tabs['wc-simulador-parcelas'] = array(
      'title'    => apply_filters( 'wc_simulador_parcelas_tab_name', 'Parcelamento' ),
      'priority' => apply_filters( 'wc_simulador_parcelas_tab_priority', 50 ),
      'callback' => array( $this, 'render_full_installment' )
    );

    return $tabs;
  }


  public function render_full_installment() {
    if ( apply_filters( 'wcsp_show_new_table', true ) ) {
      $this->full_installment();
    } else {
      $old_functions = new WCSP_Replace_Functions();
      $old_functions->parcelamento_completo();
    }
  }


  public function render_full_installment_shortcode( $atts = array() ) {
    $atts = shortcode_atts( array(
      'product_id' => false,
    ), $atts, 'wcsp_table' );

    ob_start();

    if ( apply_filters( 'wcsp_show_new_table', true ) ) {
      $this->full_installment( $atts['product_id'] );
    } else {
      $old_functions = new WCSP_Replace_Functions();
      $old_functions->parcelamento_completo( $atts['product_id'] );
    }

    return ob_get_clean();
  }


  /**
   * Formatar a exibição do parcelamento completo
  */
  public function full_installment( $product_id = false ) {
    if ( $product_id ) {
      $product = wc_get_product( $product_id );
    } else {
      global $product;
    }

    $product = apply_filters( 'wcsp_full_installment_product', $product );

    if ( ! $product ) {
      return false;
    }

    $price = wc_get_price_to_display( $product );

    $all_installments = $this->set_values( 'all', $price, $product, false );
    if ( ! $all_installments ) {
      return;
    }

    do_action( 'wcsp_before_installments_table' );


    $table = '<table class="wcsp-table">
    <thead>
      <tr>
        <th colspan="2">' . __( 'Parcelamento', 'wc-simulador-parcelas' ) . '</th>
      </tr>
    </thead>
    <tbody data-default-text="' . self::get_table_formatted_text( $product ) . '">';

      foreach ( $all_installments as $installment ) {
        $find       = array_keys( self::strings_to_replace( $installment ) );
        $replace    = array_values( self::strings_to_replace( $installment ) );
        $final_text = str_replace( $find, $replace, self::get_table_formatted_text( $product ) );

        $table .= '<tr class="' . $installment['class'] . '">';
        $table .= '<th>' . $final_text . '</th>';
        $table .= '<th>' . wc_price( $installment['final_price'] )  . '</th>';
        $table .= '</tr>';
      }

    $table .= '</tbody></table>';

    echo apply_filters( 'wcsp_table', $table, $all_installments );

    do_action( 'wcsp_after_installments_table' );
  }

  public static function strings_to_replace( $values ) {
    return array(
      '{{ parcelas }}' => $values['installments_total'],
      '{{ valor }}' => wc_price( $values['installment_price'] ),
      '{{ total }}' => wc_price( $values['final_price'] ),
      '{{ juros }}' => self::get_fee_info( $values ),
    );
  }


  /**
   * Exibir UM DOS preços no lugar do preço padrão
  */
  public function woocommerce_get_price_html( $price, $product ) {

    if ( ! $this->is_available( $product ) ) {
      return $price;
    }

    $hook = self::hook();
    $html = '';

    $main_price_discount = self::get_main_price_discount( $product );

    if ( apply_filters( 'wcsp_show_original_price', true, $product, $hook ) ) {
      $html .= $price;
    }

    if ( apply_filters( 'wcsp_card_price_before_ticket_' . $hook, true ) ) {
      $html .= $this->single_product_price( $product, $price );
    }

    $ticket_visibility = self::get_ticket_visibility( $product );

    if ( 0 < self::get_main_price_discount( $product ) && in_array( $ticket_visibility, array( 'both', $hook ) )
         || 'yes' == self::$always_show_boleto && in_array( $ticket_visibility, array( 'both', $hook ) ) ) {

      $html .= '<span class="wc-simulador-parcelas-offer">';

      if ( $product->is_type( 'variable', 'variation' ) && ! $this->variable_has_same_price( $product ) ) {
        $html .= apply_filters( 'wcsp_before_variation_text', '<span class="wc-simulador-parcelas-a-partir-de">' . __( 'A partir de', 'wc-simulador-parcelas' ) . ' </span>' );
      }

      $custom_price = WC_Simulador_Parcelas_Calculate_Values::calculate_discounted_price( wc_get_price_to_display( $product ), $main_price_discount, $product );

      $html .= wc_price( $custom_price );

      $html .= ' <span class="wc-simulador-parcelas-detalhes-valor">';
      $html .= self::get_text_after_price( $product );
      $html .= ' </span>';

      $html .= '</span>';

    }

    if ( apply_filters( 'wcsp_card_price_after_ticket_' . $hook, false ) ) {
      $html .= $this->single_product_price( $product, $price );
    }

    return $html;
  }

  /**
   * Formatar a exibição dos preços de acordo com escolhas do painel
  */
  private function formatting_display( $installments, $return, $echo = true ) {

    global $product;

    if ( 0 === count( $installments ) ) {
      return;
    }

    switch ( $return ) {
      case 'all':
        $return = apply_filters( 'wcsp_all_installments', $installments );
        break;
      case 'best_no_fee':
        $return = $this->best_no_fee( $installments, $product );
        break;
      case 'best_with_fee':
        $return = $this->best_with_fee( $installments, $product );
        break;
      case 'both':
        if ( $this->best_no_fee( $installments, $product ) === $this->best_with_fee( $installments, $product ) ) {
          $return  = $this->best_no_fee( $installments, $product );
        } else {
          $return  = $this->best_no_fee( $installments, $product );
          $return .= $this->best_with_fee( $installments, $product );
        }
        break;

      default:
        return;
        break;
    }

    if ( $echo ) {
      echo $return;
    } else {
      return $return;
    }
  }

  /**
   * pegar a melhor parcela sem juros
  */
  private function best_no_fee( $installments, $product ) {

    $hook = self::hook();

    foreach ( $installments as $key => $installment ) {
      if ( 'no-fee' != $installment['class'] ) {
        unset( $installments[ $key ] );
      }
    }

    $best_no_fee = end( $installments );

    if ( false === $best_no_fee ) {
      return;
    }

    if ( 'main_price' == $hook ) {
      $text = self::$exibicao_texto_formatado_main_price;
    } else {
      $text = self::$exibicao_texto_formatado_loop;
    }

    $find = array_keys( self::strings_to_replace( $best_no_fee ) );
    $replace = array_values( self::strings_to_replace( $best_no_fee ) );
    $text = str_replace( $find, $replace, $text );

    return '<span class="wc-simulador-parcelas-parcelamento-info best-value ' . $best_no_fee['class'] . '">' . apply_filters( 'wcsp_best_no_fee_' . $hook, $text, $best_no_fee, $product ) . '</span>';

  }

  /**
   * Pegar a melhor parcela com juros
  */
  private function best_with_fee( $installments, $product ) {

    $hook = self::hook();

    $best_with_fee = end( $installments );

    if ( false === $best_with_fee ) {
      return;
    }

    if ( 'main_price' == $hook ) {
      $text = self::$exibicao_texto_formatado_main_price;
    } else {
      $text = self::$exibicao_texto_formatado_loop;
    }

    $find = array_keys( self::strings_to_replace( $best_with_fee ) );
    $replace = array_values( self::strings_to_replace( $best_with_fee ) );
    $text = str_replace( $find, $replace, $text );

    return '<span class="wc-simulador-parcelas-parcelamento-info best-value ' . $best_with_fee['class'] . '">' . apply_filters( 'wcsp_best_with_fee_' . $hook, $text, $best_with_fee, $product ) . '</span>';

  }

  /**
   * Pegar a informação do juros, se é com ou sem
  */
  public static function get_fee_info( $installment ) {
    $hook = self::hook();

    $text = ( $installment['interest_fee'] ) ? ' ' . __( 'com juros', 'wc-simulador-parcelas' ) : ' ' . __( 'sem juros', 'wc-simulador-parcelas' );
    return apply_filters( 'wcsp_fee_label', $text, $installment['interest_fee'], $hook );
  }

  /**
   * Verificar se as variações possuem o mesmo valor
   * Caso contrário exibir "A partir de"
  */
  private function variable_has_same_price( $product ) {
    return ( $product->is_type( 'variable', 'variation' ) && $product->get_variation_price( 'min' ) === $product->get_variation_price( 'max' ) );
  }

  /**
   * Salvar um array com todos os detalhes do parcelamento
  */
  public static function set_installment_info( $price, $final_price, $interest_fee, $class, $i ) {
    $installment_info = array(
      'installment_price'  => $price,
      'installments_total' => $i,
      'final_price'        => $final_price,
      'interest_fee'       => $interest_fee,
      'class'              => $class,
    );

    return apply_filters( 'wcsp_installment_info', $installment_info );
  }

  /**
   * Calcular o valor da parcela SEM juros
  */
  public static function get_installment_details_without_interest( $total, $i ) {
    $price = WC_Simulador_Parcelas_Calculate_Values::calculate_installment_no_fee( $total, $i );
    $final_price = WC_Simulador_Parcelas_Calculate_Values::calculate_final_price( $price, $i );
    $fee = false;
    $class = 'no-fee';

    $installment_info = self::set_installment_info( $price, $final_price, $fee, $class, $i );

    return $installment_info;

  }

  /**
   * Calcular o valor da parcela COM juros
  */
  public static function get_installment_details_with_interest( $total, $fee, $i ) {
    $price = WC_Simulador_Parcelas_Calculate_Values::calculate_installment_with_fee( $total, $fee, $i );
    $final_price = WC_Simulador_Parcelas_Calculate_Values::calculate_final_price( $price, $i );
    $fee = true;
    $class = 'fee-included';

    $installment_info = self::set_installment_info( $price, $final_price, $fee, $class, $i );

    return $installment_info;
  }


  public function is_available( $product = false ) {
    $is_available = true;

    $price = wc_get_price_to_display( $product );

    if ( is_admin() && ! is_ajax() || $product && empty( $price ) || $product && 0 === $price ) {
      $is_available = false;
    }

    return apply_filters( 'wcsp_is_available', $is_available, $product );
  }


  public function display_discount_on_cart() {
    if ( 'yes' !== self::show_in_cart() ) {
      return false;
    }

    $main_price_discount = self::get_main_price_discount();

    if ( $main_price_discount > 0 ) {
      $custom_price        = WC_Simulador_Parcelas_Calculate_Values::calculate_discounted_price( WC()->cart->get_total( 'edit' ), $main_price_discount );
      ?>
      <tr class="order-discount-total">
        <th><?php echo apply_filters( 'wcsp_cart_total_title', sprintf( __( 'Total %s', 'wc-simulador-parcelas' ), self::get_text_after_price() ) ); ?></th>
        <td data-title="<?php echo esc_attr( apply_filters( 'wcsp_cart_total_title', sprintf( __( 'Total %s', 'wc-simulador-parcelas' ), self::get_text_after_price() ) ) ); ?>"><?php echo wc_price( $custom_price ); ?></td>
      </tr>
      <?php
    }
  }




  public static function open_single_product() {
    self::$count = 0;
  }


  public static function close_single_product() {
    self::$count++;
  }
}

new WCSP_Apply_Rules();
