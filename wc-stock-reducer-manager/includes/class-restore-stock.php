<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Class WC_Stock_Reducer_Restore.
 *
 * Initialize the main class.
 *
 * @class     WC_Stock_Reducer_Restore
 * @author    Fernando Acosta
 * @package   Core
 * @version   1.0.0
 */
class WC_Stock_Reducer_Restore {

  function __construct() {
    add_action( 'woocommerce_order_status_cancelled', array( $this, 'restore_order_stock' ), 5, 2 );
    add_action( 'woocommerce_order_status_refunded', array( $this, 'restore_order_stock' ), 5, 2 );
    add_action( 'woocommerce_order_status_failed', array( $this, 'restore_order_stock' ), 5, 2 );
    add_filter( 'woocommerce_can_restore_order_stock', array( $this, 'prevent_duplicate_stock_restore' ), 10, 2 );
  }

  public function restore_order_stock( $order_id, $order ) {
    $reduced = (bool) $order->get_data_store()->get_stock_reduced( $order_id );

    if ( $reduced ) {
      wc_stock_manager_log( 'O estoque do pedido ' . $order_id . ' foi reduzido e agora será restaurado: ' . current_action() );

      if ( function_exists( 'wc_increase_stock_levels' ) ) {
        wc_increase_stock_levels( $order );
      } else {
        foreach ( $order->get_items() as $item_id => $item ) {
          $product       = $item->get_product();

          if ( ! $product ) {
            continue;
          }

          $product_name  = $product->get_name();
          $product_stock = $product->get_stock_quantity();
          $qty           = $item->get_quantity();

          if ( $new_stock = wc_update_product_stock( $product, $qty, 'increase' ) ) {
            $order->add_order_note( sprintf( __( 'Estoque de %1$s restaurado de %2$s para %3$s.', 'woocommerce' ), $product_name, $product_stock, $new_stock ) );

            wc_stock_manager_log( 'O estoque do produto ' . $product_name . ' foi restaurado no pedido #' . $order_id . ' - ' . current_action() );
          }
        }
      }

      // remover a meta para permitir a redução de estoque caso o pedido seja pago novamente por qualquer motivo
      $order->get_data_store()->set_stock_reduced( $order_id, false );
    } else {
      wc_stock_manager_log( 'O estoque do pedido ' . $order_id . ' não foi reduzido e por isso não será restaurado: ' . current_action() );
    }
  }


  public function prevent_duplicate_stock_restore( $can_restore, $order ) {
    $order_id = $order->get_id();

    wc_stock_manager_log( 'Verificando se o estoque do pedido ' . $order_id . ' pode ser restaurado restaurado: ' . current_filter() );

    $reduced  = (bool) $order->get_data_store()->get_stock_reduced( $order_id );

    if ( ! $reduced ) {
      wc_stock_manager_log( 'O estoque do pedido ' . $order_id . ' não consta como reduzido e por isso não será restaurado: ' . current_filter() );

      $can_restore = false;
    }

    wc_stock_manager_log( 'O estoque do pedido ' . $order_id . ' pode ser restaurado restaurado? ' . wc_bool_to_string( $can_restore ) );

    return $can_restore;
  }
}

new WC_Stock_Reducer_Restore();
