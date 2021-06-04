<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Class WC_Stock_Reducer_Apply.
 *
 * Initialize the Stock Manager.
 *
 * @class     WC_Stock_Reducer_Apply
 * @author    Fernando Acosta
 * @package   Backend
 * @version   1.0.0
 */
class WC_Stock_Reducer_Apply {

  function __construct() {
    add_action( 'woocommerce_checkout_order_processed', array( $this, 'reduce_stock_levels' ), 10, 3 );
    add_filter( 'woocommerce_can_reduce_order_stock', array( $this, 'wc_can_reduce_stock' ), 10, 2 );
  }


  public function reduce_stock_levels( $order_id, $data, $order ) {
    wc_stock_manager_log( 'Novo pedido! O estoque do pedido ' . $order_id . ' será reduzido: ' . current_action() );

    $reduced  = (bool) $order->get_data_store()->get_stock_reduced( $order_id );
    if ( $reduced ) {
      wc_stock_manager_log( 'O estoque do pedido ' . $order_id . ' já foi reduzido e por isso não será reduzido novamente.' );
    } else {
      wc_reduce_stock_levels( $order_id );

      // Ensure stock is marked as "reduced" in case payment complete or other stock actions are called.
      $order->get_data_store()->set_stock_reduced( $order_id, true );
    }
  }


  public function wc_can_reduce_stock( $can_reduce, $order ) {
    $order_id = $order->get_id();
    $reduced  = (bool) $order->get_data_store()->get_stock_reduced( $order_id );

    if ( $reduced ) {
      wc_stock_manager_log( 'O estoque do pedido ' . $order_id . ' já foi reduzido e por isso não será reduzido outra vez: ' . current_filter() );

      $can_reduce = false;

      if ( apply_filters( 'wcsrm_log_attempts', false ) ) {
        $order->add_order_note( 'O estoque já foi reduzido. Bloqueada tentativa de redução duplicada.' );
      }
    } else {
      wc_stock_manager_log( 'O estoque do pedido ' . $order_id . ' ainda não foi reduzido e por isso será reduzido agora: ' . current_filter() );

      $can_reduce = true;
    }

    return $can_reduce;
  }
}

new WC_Stock_Reducer_Apply();
