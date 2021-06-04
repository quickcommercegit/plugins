<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
* WC Additional Days Per Product Category Fields
*/
class WC_Additional_Days_Per_Product_Global_Fields {
  function __construct() {
    add_filter( 'woocommerce_get_settings_shipping', array( $this, 'admin_options' ), 200, 2 );
  }


  public function admin_options( $settings, $current_section = '' ) {
    $options = array();

    if ( '' !== $current_section ) {
      return $settings;
    }

    $settings[] = array(
      'title' => 'Dias adicionais',
      'type'  => 'title',
      'id'    => 'wc_additional_days',
    );

    $settings[] = array(
      'title'    => 'Texto sobre o prazo',
      'desc_tip' => 'Este é o texto que será exibido na página do produto. Deixe em branco para ocultar essa informação (o prazo será somado sem nenhum aviso).',
      'id'       => 'wc_additional_days_label',
      'default'  => 'Prazo de produção',
      'label'    => 'Prazo de produção',
      'type'     => 'text',
    );

    $settings[] = array(
      'title'    => 'Somar prazo',
      'desc'     => 'Prazo de produção',
      'desc_tip' => 'Se ativo, irá somar os dias adicionais ao prazo de entrega de todos os métodos de envio. Desative para apenas exibir nos produtos',
      'id'       => 'wc_additional_days_sum',
      'default'  => 'yes',
      'type'     => 'checkbox',
    );

    $settings[] = array(
      'title'    => 'Carrinho, checkout e e-mails',
      'desc'     => 'Exibir prazo junto do nome do produto',
      'desc_tip' => 'Se ativo, exibir o prazo de cada produto junto do nome no carrinho, checkout e e-mails.',
      'id'       => 'wc_additional_days_show_in_cart',
      'default'  => 'no',
      'type'     => 'checkbox',
    );

    $settings[] = array(
      'title'    => 'Dias adicionais de entrega',
      'desc_tip' => 'Adicione este prazo a todos os produtos. Deixe em branco para desativar.',
      'id'       => 'wc_additional_days_global',
      'default'  => '',
      'type'     => 'text',
    );

    $settings[] = array(
      'type' => 'sectionend',
      'id'   => 'wc_additional_days',
    );

    return $settings;
  }
}

new WC_Additional_Days_Per_Product_Global_Fields();
