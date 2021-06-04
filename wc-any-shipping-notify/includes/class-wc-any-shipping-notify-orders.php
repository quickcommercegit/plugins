<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}


/**
 * Admin Orders.
 */
class WC_Any_Shipping_Notify_Admin_Orders {

  /**
   * Initialize the order actions.
   */
  public function __construct() {
    add_filter( 'woocommerce_admin_order_actions', array( $this, 'add_tracking' ), 10, 2 );
    add_action( 'woocommerce_admin_order_actions_end', array( $this, 'add_tracking_field' ), 10, 1 );
    add_action( 'wp_ajax_wc_any_shipping_notify_orders_list', array( $this, 'ajax_action' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'orders_list_scripts' ) );
  }


  public function add_tracking( $actions, $the_order ) {
    $available_companies = wc_any_shipping_notify_get_shipping_companies();

    if ( $available_companies ) {
      $actions['wc-any-shipping-notify'] = array(
        'action' => 'wc-any-shipping-notify',
        'url'    => admin_url( 'post.php?post=' . $the_order->get_id() . '&action=edit' ),
        'name'   => __( 'Novo rastreio para transportadora', 'wc-any-shipping-notify' ),
      );
    }


    return $actions;
  }


  public function add_tracking_field( $the_order ) {
    $available_companies = wc_any_shipping_notify_get_shipping_companies();
    $order_id            = $the_order->get_id();

    include 'views/html-list-table-add-tracking-code.php';
  }


  public function orders_list_scripts() {
    $screen = get_current_screen();

    if ( 'edit-shop_order' === $screen->id ) {
      wp_enqueue_style( 'wc-any-shipping-notify-orders', plugins_url( 'assets/css/wc-any-shipping-notify-orders.css', WC_Any_Shipping_Notify::get_main_file() ), array(), 1.0 );
      wp_enqueue_script( 'wc-any-shipping-notify-orders', plugins_url( 'assets/js/wc-any-shipping-notify-orders.js', WC_Any_Shipping_Notify::get_main_file() ), array( 'jquery', 'jquery-blockui', 'wp-util' ), '1.0.1', true );
    }
  }


  public function ajax_action() {
    $order_id      = $_POST['order_id'];
    $tracking_code = $_POST['tracking_code'];
    $company       = $_POST['company'];

    if ( wc_any_shipping_notify_update_tracking_code( $order_id, $tracking_code, $company ) ) {
      wp_send_json_success( 'Novo código adicionado com sucesso.' );
    } else {
      wp_send_json_error( 'Ocorreu um erro ao adicionar o código de rastreio. Adicione diretamente na página do pedido.' );
    }
  }
}

new WC_Any_Shipping_Notify_Admin_Orders();
