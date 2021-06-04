<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Class WC_Stock_Reducer_Admin_Settings.
 *
 * Initialize the Admin Settings.
 *
 * @class     WC_Stock_Reducer_Admin_Settings
 * @author    Fernando Acosta
 * @package   Admin
 * @version   1.0.0
 */
class WC_Stock_Reducer_Admin_Settings {
  public function __construct() {
    add_filter( 'woocommerce_get_settings_checkout', array( $this, 'settings' ), 20, 2 );
    add_filter( 'woocommerce_admin_settings_sanitize_option_wc_stock_reduce_manager_days_to_cancel', array( $this, 'format_option' ), 10, 3 );
    add_action( 'admin_init', array( $this, 'cron_exists' ) );
  }

  public function settings( $settings, $current_section ) {
    if ( '' !== $current_section ) {
      return $settings;
    }

    $new_settings = array(
      array(
        'title' => 'Cancelamento de pedidos',
        'desc'  => 'Defina as opções para cancelamento de pedidos e restauração de estoque.',
        'type'  => 'title',
        'id'    => 'stock_manager_settings',
      ),
      array(
        'title'             => 'Dias até cancelar pedido',
        'desc'              => 'Informe quantos dias devem se passar até que o pedido seja cancelado e o estoque restaurado.',
        'id'                => 'wc_stock_reduce_manager_days_to_cancel',
        'type'              => 'number',
        'custom_attributes' => array(
          'min' => 1
        ),
        'default'           => '',
        'desc_tip'          => true,
      ),
      array(
        'title'             => 'Status para cancelar',
        'desc'              => 'Selecione os status de pedidos que devem ser cancelados após o prazo para pagamento esgotar.',
        'id'                => 'wc_stock_reduce_manager_status_to_cancel',
        'default'           => '',
        'class'             => 'wc-enhanced-select',
        'css'               => 'min-width:300px;',
        'type'              => 'multiselect',
        'options'           => wc_get_order_statuses(),
        'desc_tip'          => true,
        'autoload'          => false,
        'custom_attributes' => array(
          'data-placeholder' => 'Escolha os status',
        ),
      ),
      array(
        'title'         => 'Ativar Log',
        'desc'          => 'Registrar atividades do plugin <strong>WC Redução e restauração de estoque</strong>',
        'id'            => 'wc_reduce_stock_manager_log',
        'default'       => 'no',
        'type'          => 'checkbox',
        'autoload'      => true,
      ),
      array(
        'type' => 'sectionend',
        'id' => 'stock_manager_settings',
      ),
    );

    return array_merge( $settings, $new_settings );
  }


  public function format_option( $value, $option, $raw_value ) {
    $value = ! empty( $raw_value ) ? absint( $raw_value ) : ''; // Allow > 0 or set to ''.

    wp_clear_scheduled_hook( 'wc_stock_manager_cancel_orders' );

    return $value;
  }


  public function cron_exists() {
    if ( ! wp_next_scheduled( 'wc_stock_manager_cancel_orders' ) ) {
      $this->create_cron();
    }
  }


  public function create_cron() {
    wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'wc_stock_manager_cancel_orders' );
  }
}

new WC_Stock_Reducer_Admin_Settings();
