<?php

namespace WC_Juno\Gateway;

use Exception;
use WC_Juno\functions as h;
use WC_Subscriptions_Cart;

class Credit_Card_Subscription extends Credit_Card {
	public function __construct () {
    parent::__construct();

    if ( 2 > $this->api->version ) {
      add_action( 'admin_notices', [ $this, 'api_version_not_compatible' ] );
      return;
    }

    if ( 'direct' !== $this->integration_method ) {
      add_action( 'admin_notices', [ $this, 'integration_method_not_compatible' ] );
      return;
    }

    if ( class_exists( 'WC_Subscriptions_Order' ) ) {
      add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, [ $this, 'scheduled_subscription_payment' ], 10, 2 );

      $this->supports = [
        'products',
        'refunds',
        'subscriptions',
        'subscription_cancellation',
        'subscription_suspension',
        'subscription_reactivation',
        'subscription_amount_changes',
        'subscription_date_changes',
        'subscription_payment_method_change',
        'subscription_payment_method_change_customer',
        'subscription_payment_method_change_admin',
        'multiple_subscriptions',
      ];
    }
  }

  public function process_payment( $order_id ) {
    // must be transparent checkout
    if ( 'direct' !== $this->integration_method ) {
      return parent::process_payment( $order_id );
    }

    // process only subscriptions!
		if ( ! $this->is_subscription() ) {
      return parent::process_payment( $order_id );
    }

    // just get the card hash
    if ( 0 === $this->get_order_total() ) {
      try {
        $prefix = $this->id . '-';
        $mode = h\request_value( $prefix . 'checkout-mode' );

        $checkout_data = [
          'card_info' => h\request_value( $prefix . 'card-info' ),
          'card_hash' => h\request_value( $prefix . 'card-hash' ),
          'card_cvc' => h\request_value( $prefix . 'cvc' ),
          'save_card' => 'yes' == h\request_value( $prefix . 'save-card' ),
          'installments' => h\value( h\request_value( $prefix . 'installments'), 1 ),
          'mode' => $mode,
          'person_type' => h\value( h\request_value( 'billing_persontype' ), 0 ),
        ];

        $order = wc_get_order( $order_id );

        $card_id = $this->api->get_card_id( $checkout_data, $order );

        h\throw_if(
          ! is_string( $card_id ), null,
          __( 'Ocorreu um erro ao processar seu cartão de crédito.', 'woo-juno' )
        );

        $card_data = [
          'creditCardId' => $card_id
        ];

        $this->save_customer_card( $card_data, $checkout_data, $order );

        // Remove cart.
        WC()->cart->empty_cart();

        if ( is_a( $order, '\WC_Subscription' ) ) {
          $order->add_order_note( 'Pagamento atualizado sem cobrança.' );
        } else {
          $order->update_status( 'processing', 'Compra recorrente sem valor inicial.' );
        }

        // Return thankyou redirect.
        return array(
          'result'   => 'success',
          'redirect' => $this->get_return_url( $order ),
        );
      } catch (\Exception $e) {
        \wc_add_notice( $e->getMessage(), 'error' );
        return;
      }
    }

    // always save customer card
    if ( isset( $this->checkout_data ) ) {
      $this->checkout_data['save_card'] = true;
    }

    return parent::process_payment( $order_id );
  }




  /**
   * Charge recurring subscription
   *
   * @param float $amount_to_charge
   * @param WC_Order $renewal_order
   * @return void
   */
  public function scheduled_subscription_payment( $amount_to_charge, $renewal_order ) {
    $subscription_id = $renewal_order->get_meta('_subscription_renewal');

    if ( ! $subscription_id ) {
      $renewal_order->add_order_note( __( 'Assinatura não encontrada.', 'woo-juno' ) );
      $renewal_order->payment_failed( 'cancelled' );
      return;
    }

    $subscription = new \WC_Subscription( $subscription_id );
    $main_order = $subscription->get_parent();
    $card_id = $main_order->get_meta( '_juno_subscriptions_card_id' );

    // there's no way to retry
    if ( ! $card_id ) {
      $subscription->payment_failed( 'cancelled' );
      return;
    }

    // let's try the payment!
    try {
			list( $data, $response ) = $this->process_regular_payment( $renewal_order );

			foreach ( $data->charges as $charge ) {
				// 2.0
				if ( isset( $charge->id ) ) {
					$renewal_order->update_meta_data( 'juno_charge_id', $charge->id );
				}

				$renewal_order->update_meta_data( 'juno_charge_code', $charge->code );
				$renewal_order->update_meta_data( 'juno_checkout_url', $charge->checkoutUrl );
			}


      $request = $this->api->process_credit_card_payments( $renewal_order, $charge->id, [
        'card_id' => $card_id,
      ] );

      h\throw_if(
        \is_wp_error( $request ), null,
        __( 'Um erro ocorreu em nosso servidor enquanto processavamos seu pagamento. Por favor, tente novamente.', 'woo-juno' )
      );

      $response = \json_decode( $request['body'] );

      $this->log( [ 'Payment response received:', $response ] );

      $renewal_order->update_meta_data( '_juno_v2_payment_response', $response );

      h\throw_if(
        200 !== $request['response']['code'], null,
        isset( $response->details[0]->message ) ? $response->details[0]->message : 'Ocorreu um erro ao processar seu pagamento. Tente novamente' . '.'
      );
    } catch (\Exception $e) {
      // if the order was processed, don't do anything
      // await the webhooks!
      $renewal_order->update_status( 'on-hold', sprintf( __( 'Falha na cobrança recorrente com cartão: %s', 'woo-juno' ), $e->getMessage() ) );
    }
  }


  /**
   * Only API V2 or greater is allowed
   *
   * @return void
   */
  public function api_version_not_compatible() {
    h\include_php_template( 'admin-notice.php', [
      'message' => __( 'Juno para WooCommerce requer a API na versão 2 para funcionar com assinaturas. Atualize suas configurações.', 'woo-juno' ),
      'class'   => 'error juno-urgent'
    ] );
  }

  /**
   * Checkout transparent only
   *
   * @return void
   */
  public function integration_method_not_compatible() {
    h\include_php_template( 'admin-notice.php', [
      'message' => __( 'Juno para WooCommerce precisa estar configurado como checkout transparente para funcionar com assinaturas. Atualize suas configurações.', 'woo-juno' ),
      'class'   => 'error juno-urgent'
    ] );
  }


	/**
	 * Customer can't choose it for subscriptions,
   * we'll always save it!
	 *
	 * @return bool
	 */
	public function customer_can_store_cards() {
		if ( $this->is_subscription() ) {
 			return false;
		}

		return parent::customer_can_store_cards();
  }


  public function get_max_installments( $total = 0 ) {
    if ( apply_filters( h\prefix( 'disable_subscription_installments' ), $this->is_subscription(), $this ) ) {
      return 1;
    }

    return parent::get_max_installments();
  }


  /**
   * For subscriptions, we'll store the card ID in the
   * main order, so we can charge them later
   *
   * @param array $card_data
	 * @param array $checkout_data
   * @param WC_Order $order
   * @return void
   */
  public function save_customer_card( $card_data, $checkout_data, $order ) {
		if ( $this->is_subscription() ) {
      $order->update_meta_data( '_juno_subscriptions_card_id', $card_data['creditCardId'] );
      return true;
   }

    return parent::save_customer_card( $card_data, $checkout_data, $order );
  }
}
