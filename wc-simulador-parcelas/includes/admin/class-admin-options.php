<?php
/**
 * WooCommerce Webhooks Table List
 *
 * @author   Fernando Acosta
 * @category Admin
 * @package  Includes/Admin
 * @version  1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

/**
* Admin Options
*/
class WC_Simulador_Admin_Options {

  function __construct() {
    add_filter( 'woocommerce_get_sections_products', array( $this, 'add_products_section' ), 10, 1 );
    add_filter( 'woocommerce_get_settings_products', array( $this, 'get_settings' ), 10, 2 );
  }

  public function add_products_section( $sections ) {
    $sections['wc-simulador-parcelas'] = __( 'Simulador de parcelas', 'wc-simulador-parcelas' );

    return $sections;
  }


  public function get_settings( $settings, $current_section ) {
    if ( 'wc-simulador-parcelas' == $current_section ) {

      $wc_simulador_parcelas_settings = array(
        array(
          'name' => __( 'WC Simulador de Parcelas', 'wc-simulador-parcelas' ),
          'type' => 'title',
          'desc' => __( 'Utilize essa seção para definir preços customizados e formas de parcelamento.', 'wc-simulador-parcelas' ),
        ),

        array(
          'title'    => __( 'Desconto no preço principal', 'wc-simulador-parcelas' ),
          'desc'     => __( 'Se preenchido, o valor com desconto irá subtituir o valor principal do produto', 'wc-simulador-parcelas' ),
          'id'       => 'wc_simulador_parcelas_main_price',
          'type'     => 'number',
          'custom_attributes' => array(
            'min'  => 0,
            'max'  => 100,
            'step' => 'any'
          ),
          'default'  => 0,
          'css'      => 'width: 250px;',
          'autoload' => false,
          'desc_tip' => true
        ),

        array(
          'title'    => __( 'Texto depois do preço', 'wc-simulador-parcelas' ),
          'desc'     => __( 'Exibido apenas se o campo anterior for preenchido. Ex.: "no boleto" irá retornar "R$XX,xx no boleto.', 'wc-simulador-parcelas' ),
          'id'       => 'wc_simulador_parcelas_depois_preco',
          'type'     => 'text',
          'default'  => __( 'no boleto', 'wc-simulador-parcelas' ),
          'css'      => 'width: 250px;',
          'autoload' => false,
          'desc_tip' => true
        ),

        array(
          'title'   => __( 'Sempre exibir o preço do boleto', 'wc-simulador-parcelas' ),
          'desc'    => __( 'Por padrão, o valor no boleto é exibido apenas quando há um desconto. Marque para exibir até mesmo quando nenhum desconto é definido.', 'wc-simulador-parcelas' ),
          'id'      => 'wc_simulador_parcelas_always_show_boleto',
          'default' => 'no',
          'type'    => 'checkbox'
        ),

        array(
          'title'   => __( 'Exibir no carrinho', 'wc-simulador-parcelas' ),
          'desc'    => __( 'Se ativo, irá exibir o valor do preço com desconto também no carrinho', 'wc-simulador-parcelas' ),
          'id'      => 'wcsp_show_in_cart',
          'default' => 'no',
          'type'    => 'checkbox'
        ),

        array(
          'title'    => __( 'Exibição do preço com desconto', 'wc-simulador-parcelas' ),
          'desc'     => __( 'Selecione onde o preço com desconto deve ser exibido', 'wc-simulador-parcelas' ),
          'id'       => 'wc_simulador_parcelas_ticket_visibility',
          'class'    => 'wc-enhanced-select',
          'css'      => 'min-width:300px;',
          'default'  => '',
          'type'     => 'select',
          'options'  => array(
            'both'         => __( 'Página do produto e listagem', 'wc-simulador-parcelas' ),
            'main_price'   => __( 'Apenas página do produto', 'wc-simulador-parcelas' ),
            'loop'         => __( 'Apenas listagem', 'wc-simulador-parcelas' ),
          ),
          'desc_tip' =>  true,
        ),

        array(
          'title'    => __( 'Taxa de juros no parcelamento', 'wc-simulador-parcelas' ),
          'desc'     => __( 'Informe a taxa de juros para o parcelamento dos produtos', 'wc-simulador-parcelas' ),
          'id'       => 'wc_simulador_parcelas_taxa_de_juros',
          'type'     => 'number',
          'custom_attributes' => array(
            'min'  => 0,
            'max'  => 100,
            'step' => 'any'
          ),
          'default'  => 0,
          'css'      => 'width: 250px;',
          'autoload' => false,
          'desc_tip' => true
        ),

        array(
          'title'    => __( 'Máximo de parcelas', 'wc-simulador-parcelas' ),
          'desc'     => __( 'Informe o máximo de vezes que é possível parcelar as compras', 'wc-simulador-parcelas' ),
          'id'       => 'wc_simulador_parcelas_maximo_parcelas',
          'type'     => 'number',
          'custom_attributes' => array(
            'min'  => 1,
            'max'  => 100,
            'step' => 1
          ),
          'default'  => 1,
          'css'      => 'width: 250px;',
          'autoload' => false,
          'desc_tip' => true
        ),

        array(
          'title'    => __( 'Máximo de parcelas sem juros', 'wc-simulador-parcelas' ),
          'desc'     => __( 'Informe em até quantas vezes o usuário pode comprar sem juros', 'wc-simulador-parcelas' ),
          'id'       => 'wc_simulador_parcelas_maximo_parcelas_sem_juros',
          'type'     => 'number',
          'custom_attributes' => array(
            'min'  => 1,
            'max'  => 100,
            'step' => 1
          ),
          'default'  => 0,
          'css'      => 'width: 250px;',
          'autoload' => false,
          'desc_tip' => true
        ),

        array(
          'title'    => __( 'Parcela mínima', 'wc-simulador-parcelas' ),
          'desc'     => __( 'Informe o valor mínimo de cada parcela', 'wc-simulador-parcelas' ),
          'id'       => 'wc_simulador_parcelas_valor_minimo_parcela',
          'type'     => 'number',
          'custom_attributes' => array(
            'min'  => 0,
            'step' => 'any'
          ),
          'default'  => 0,
          'css'      => 'width: 250px;',
          'autoload' => false,
          'desc_tip' => true
        ),

        array(
          'title'    => __( 'Exibição na listagem de produtos', 'wc-simulador-parcelas' ),
          'desc'     => __( 'Selecione as opções de parcelas que você deseja exibir na listagem de produtos', 'wc-simulador-parcelas' ),
          'id'       => 'wc_simulador_parcelas_exibicao_shop_page',
          'class'    => 'wc-enhanced-select',
          'css'      => 'min-width:300px;',
          'default'  => '',
          'type'     => 'select',
          'options'  => array(
            ''              => __( 'Não exibir nada', 'wc-simulador-parcelas' ),
            'best_no_fee'   => __( 'Melhor parcela sem juros', 'wc-simulador-parcelas' ),
            'best_with_fee' => __( 'Melhor parcela com juros', 'wc-simulador-parcelas' ),
            'both'          => __( 'Melhor parcela com e sem juros', 'wc-simulador-parcelas' ),
          ),
          'desc_tip' =>  true,
        ),

        array(
          'title'    => __( 'Exibição na página interna do produto', 'wc-simulador-parcelas' ),
          'desc'     => __( 'Selecione onde você pretende exibir o parcelamento dos produtos', 'wc-simulador-parcelas' ),
          'id'       => 'wc_simulador_parcelas_exibicao_single_page',
          'class'    => 'wc-enhanced-select',
          'css'      => 'min-width:300px;',
          'default'  => '',
          'type'     => 'select',
          'options'  => array(
            ''              => __( 'Não exibir nada', 'wc-simulador-parcelas' ),
            'best_no_fee'   => __( 'Melhor parcela sem juros', 'wc-simulador-parcelas' ),
            'best_with_fee' => __( 'Melhor parcela com juros', 'wc-simulador-parcelas' ),
            'both'          => __( 'Melhor parcela com e sem juros', 'wc-simulador-parcelas' ),
          ),
          'desc_tip' =>  true,
        ),

        array(
          'title'    => __( 'Exibição da tabela completa', 'wc-simulador-parcelas' ),
          'desc'     => __( 'Selecione onde você pretende exibir a tabela completa de parcelamento', 'wc-simulador-parcelas' ),
          'id'       => 'wc_simulador_parcelas_exibicao_tabela_completa',
          'class'    => 'wc-enhanced-select',
          'css'      => 'min-width:300px;',
          'default'  => '',
          'type'     => 'select',
          'options'  => array(
            ''                       => __( 'Não exibir', 'wc-simulador-parcelas' ),
            'abaixo_botao_comprar'   => __( 'Abaixo do botão Adicionar ao Carrinho', 'wc-simulador-parcelas' ),
            'exibir_como_aba'        => __( 'Como uma aba do produto', 'wc-simulador-parcelas' ),
          ),
          'desc_tip' =>  true,
        ),

        array(
          'title'    => __( 'Texto de exibição das parcelas (tabela)', 'wc-simulador-parcelas' ),
          'desc'     => __( 'Utilize as variáveis {{ valor }}, {{ parcelas }}, {{ total }} e {{ juros }}', 'wc-simulador-parcelas' ),
          'id'       => apply_filters( 'wcsp_show_new_table', true ) ? 'wcsp_table_formatted_text' : 'wc_simulador_parcelas_texto_formatado',
          'type'     => 'text',
          'default'  => apply_filters( 'wcsp_show_new_table', true ) ? '{{ parcelas }}x de {{ valor }} {{ juros }}' : 'Em {{ parcelas }}x de {{ valor }} {{ juros }} no cartão. Total: {{ total }}',
          'css'      => 'width: 500px;',
          'autoload' => false,
          'desc_tip' => true
        ),

        array(
          'title'    => __( 'Texto de exibição das parcelas (listagem)', 'wc-simulador-parcelas' ),
          'desc'     => __( 'Utilize as variáveis {{ valor }}, {{ parcelas }}, {{ total }} e {{ juros }}', 'wc-simulador-parcelas' ),
          'id'       => 'wc_simulador_parcelas_texto_formatado_loop',
          'type'     => 'text',
          'default'  => 'Em até {{ parcelas }}x de {{ valor }}',
          'css'      => 'width: 500px;',
          'autoload' => false,
          'desc_tip' => true
        ),

        array(
          'title'    => __( 'Texto de exibição das parcelas (individual)', 'wc-simulador-parcelas' ),
          'desc'     => __( 'Utilize as variáveis {{ valor }}, {{ parcelas }}, {{ total }} e {{ juros }}', 'wc-simulador-parcelas' ),
          'id'       => 'wc_simulador_parcelas_texto_formatado_main_price',
          'type'     => 'text',
          'default'  => 'Em até {{ parcelas }}x de {{ valor }} {{ juros }}',
          'css'      => 'width: 500px;',
          'autoload' => false,
          'desc_tip' => true
        ),

        array(
          'title'   => __( 'Exibir preço menor no schema', 'wc-simulador-parcelas' ),
          'desc'    => __( 'Com isso, irá exibir o preço com desconto em serviços agregadores que fazem leitura de schema', 'wc-simulador-parcelas' ),
          'id'      => 'wc_simulador_parcelas_alterar_schema',
          'default' => 'no',
          'type'    => 'checkbox'
        ),

        array(
          'title'   => __( 'Atualizar valores da tabela ao mudar de variação', 'wc-simulador-parcelas' ),
          'desc'    => __( 'Marque esta opção se você vende variações com preços diferentes. Assim, a tabela será atualizada.', 'wc-simulador-parcelas' ),
          'id'      => 'wc_simulador_parcelas_update_table_for_variations',
          'default' => 'no',
          'type'    => 'checkbox'
        ),

        array(
          'type' => 'sectionend',
          'id' => 'wc-simulador-parcelas'
        ),
      );

      return apply_filters( 'wc_simulador_parcelas_settings', $wc_simulador_parcelas_settings );

    } else {
      return $settings;
    }
  }
} // WC_Simulador_Admin_Options()

new WC_Simulador_Admin_Options();
