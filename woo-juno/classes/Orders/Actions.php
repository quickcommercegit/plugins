<?php
namespace WC_Juno\Orders;

use WC_Juno\Service\Juno_REST_API;
use WC_Juno\Emails\New_Bank_Slip;

/**
 * Juno Order Actioins
 *
 * General class to handle order Actions
 *
 * @package WC_Juno/Classes/Orders
 * @since   1.1.0
 */

defined( 'ABSPATH' ) || exit;

class Actions {
  public function add_hooks() {
    add_action( 'woocommerce_order_get_payment_method_title', array( $this, 'extra_payment_info_orders_list' ), 10, 2 );

    add_filter( 'woocommerce_order_actions', [ $this, 'new_bank_slip_action' ], 20 );
    add_action( 'woocommerce_order_action_juno_new_bank_slip', [ $this, 'process_new_bank_slip' ] );

    add_filter( 'woocommerce_email_classes', array( __CLASS__, 'include_emails' ) );
  }


  /**
   * Display credit card info on orders list
   *
   * @param mixed $title
   * @param mixed $order
   * @return void
   */
  public function extra_payment_info_orders_list( $title, $order ) {
    global $current_screen;

    if ( isset( $current_screen->id ) && 'edit-shop_order' === $current_screen->id && $data = $order->get_meta( '_juno_v2_credit_card_info' ) ) {
      $title .= sprintf(
        ' (%s, %sx)',
        ucfirst( $data['brand'] ),
        $data['installments']
      );
    }

    return $title;
  }


  /**
   * new_bank_slip_action
   *
   * @param mixed $actions
   * @return void
   */
  public function new_bank_slip_action( $actions ) {
    global $theorder;

    if ( $theorder && 'juno-bank-slip' === $theorder->get_payment_method() ) {
      $actions['juno_new_bank_slip'] = __( 'Novo boleto Juno', 'woo-juno' );
    }

    return $actions;
  }


  public function process_new_bank_slip( $order ) {
    try {
      $gateway = wc_get_payment_gateway_by_order( $order );
      $gateway->process_payment( $order->get_id(), false );

      $mailer       = WC()->mailer();
      $notification = $mailer->emails['juno_new_bank_slip'];

      if ( 'yes' === $notification->enabled ) {
        $notification->trigger( $order );
      }

    } catch ( \Exception $e ) {
      $order->add_order_note( sprintf( __( 'Erro ao gerar boleto: %s', 'woo-juno' ), $e->getMessage() ) );
    }
  }


	/**
	 * Include emails.
	 *
	 * @param  array $emails Default emails.
	 *
	 * @return array
	 */
	public static function include_emails( $emails ) {
		if ( ! isset( $emails['juno_new_bank_slip'] ) ) {
			$emails['juno_new_bank_slip'] = new New_Bank_Slip();
		}

		return $emails;
	}
}
