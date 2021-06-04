<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Class WC_Stock_Manager_Cancel_Orders.
 *
 * Initialize the main class.
 *
 * @class     WC_Stock_Manager_Cancel_Orders
 * @author    Fernando Acosta
 * @package   Core
 * @version   1.0.0
 */
class WC_Stock_Manager_Cancel_Orders {

  function __construct() {
    add_action( 'wc_stock_manager_cancel_orders', array( $this, 'cancel_orders' ) );
    add_action( 'wc_stock_manager_cancel_orders_execute', array( $this, 'execute_cancel_orders' ) );
  }

  public function cancel_orders() {
    $held_duration = get_option( 'wc_stock_reduce_manager_days_to_cancel', '' );

    if ( '' === $held_duration ) {
      wc_stock_manager_log( 'O prazo para cancelamento automático de pedidos não foi definido e por isso o cron não será executado.' );
      return false;
    }

    $unpaid_orders = $this->get_unpaid_orders( strtotime( '-' . absint( $held_duration ) . ' DAYS', current_time( 'timestamp' ) ) );

    if ( $unpaid_orders ) {

      wc_stock_manager_log( 'Pedidos que serão cancelados: ' . print_r( $unpaid_orders, true ) );

      $paged_unpaid_orders = array_chunk( $unpaid_orders, 35 );

      foreach ( $paged_unpaid_orders as $minutes => $unpaid_orders_piece ) {
        $extra = ( $minutes % 2 == 0 ) ? 3 : 0;

        wp_schedule_single_event( time() + ( $minutes + $extra ) * MINUTE_IN_SECONDS, 'wc_stock_manager_cancel_orders_execute', array( $unpaid_orders_piece ) );
      }
    }
  }

  /**
   * Get unpaid orders after a certain date,
   *
   * @param  int $date Timestamp.
   * @return array
   */
  public function get_unpaid_orders( $date ) {
    global $wpdb;

    $unpaid_orders = $wpdb->get_col(
      $wpdb->prepare(
        "SELECT posts.ID
        FROM {$wpdb->posts} AS posts
        WHERE   posts.post_type   IN ('" . implode( "','", wc_get_order_types() ) . "')
        AND     posts.post_status IN ('" . implode( "','", get_option( 'wc_stock_reduce_manager_status_to_cancel', array( 'wc-on-hold' ) ) ) . "')
        AND     posts.post_modified < %s",
        // @codingStandardsIgnoreEnd
        date( 'Y-m-d H:i:s', absint( $date ) )
      )
    );

    return $unpaid_orders;
  }


  public function execute_cancel_orders( $unpaid_orders ) {
    foreach ( $unpaid_orders as $unpaid_order ) {
      $order = wc_get_order( $unpaid_order );

      $order->update_status( 'cancelled', __( 'Unpaid order cancelled - time limit reached.', 'woocommerce' ) );

      $log = new WC_Logger();
      $log->add( 'wc-cancel-pending-orders', 'Cancelado o pedido ' . $order->get_id() );
    }
  }
}

new WC_Stock_Manager_Cancel_Orders();
