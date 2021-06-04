<?php

namespace WC_Juno\Gateway;

use Exception;
use WC_Juno\functions as h;

class Bank_Slip_Subscription extends Bank_Slip {

  /**
   * Bank slip expiration time
   *
   * @var null
   */
  private $expires_at = null;

  /**
   * the current subscription ID being processed
   *
   * @var null
   */
  private $subscription_id = null;

	public function __construct () {
    parent::__construct();

    if ( 2 > $this->api->version ) {
      add_action( 'admin_notices', [ $this, 'api_version_not_compatible' ] );
      return;
    }

    if ( class_exists( 'WC_Subscriptions_Order' ) ) {
      add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, [ $this, 'scheduled_subscription_payment' ], 10, 2 );

      add_action( 'woocommerce_subscription_date_updated', [ $this, 'subscription_date_updated' ], 200, 3 );

      add_action( 'woocommerce_subscription_date_deleted', [ $this, 'subscription_date_deleted' ], 20, 2 );

      add_filter( h\prefix( 'bank_slip_subscription_schedule' ), [ $this, 'subscription_schedule_invoice' ], 10, 1 );

      add_action( h\prefix( 'notification_processed' ), [ $this, 'notification_processed' ], 10, 2 );

      $this->supports = [
        'products',
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

  public function process_payment( $order_id, $is_frontend = true ) {
    // process only subscriptions!
		if ( ! $this->is_subscription() ) {
      return parent::process_payment( $order_id, $is_frontend );
    }

    // just get the card hash
    if ( 0 === $this->get_order_total() ) {
      $order = wc_get_order( $order_id );

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
    }

    return parent::process_payment( $order_id, $is_frontend );
  }


  /**
   * Charge recurring subscription
   *
   * @param float $amount_to_charge
   * @param WC_Order $renewal_order
   * @return void
   */
  public function scheduled_subscription_payment( $amount_to_charge, $renewal_order ) {
    $subscription_id = $renewal_order->get_meta( '_subscription_renewal' );

    if ( ! $subscription_id ) {
      $renewal_order->add_order_note( __( 'Assinatura não encontrada.', 'woo-juno' ) );
      $renewal_order->payment_failed( 'cancelled' );
      return;
    }

    $subscription = new \WC_Subscription( $subscription_id );

    // bank slip already sent to customer
    if ( $subscription->get_meta( '_juno_payment_response' ) ) {
      return;
    }

    try {
      $this->subscription_schedule_invoice( $subscription_id, true );

      $renewal_order->add_order_note( __( 'Novo boleto enviado ao cliente.', 'woo-juno' ) );
    } catch ( \Exception $e ) {
      // if the order was processed, don't do anything
      // await the webhooks!
      $renewal_order->update_status( 'on-hold', sprintf( __( 'Falha na cobrança recorrente com boleto: %s', 'woo-juno' ), $e->getMessage() ) );
    }
  }


  /**
   * Schedule the bank slip notification before the
   * subscription expires and prevent suspensions time
   *
   * @param WC_Subscription $subscription
   * @param string $date_type
   * @param string $datetime
   * @return void
   */
  public function subscription_date_updated( $subscription, $date_type, $datetime ) {
    if ( 'next_payment' !== $date_type ) {
      return;
    }

    if ( $this->id !== $subscription->get_payment_method() ) {
      return;
    }

    // TODO
    // check if the subscription interval is less than biweekly
    // doesn't make sense before that
    // if ( '' ) {
    //   // return;
    // }


    /**
     * interval before the subscription expires
     * to send the first copy of the bank slip
     */
    $days_before_expire = apply_filters( h\prefix( 'bank_slip_subscription_days_before_expire' ), 5, $subscription );

    // first, delete any possible old actions
    as_unschedule_all_actions(
      h\prefix( 'bank_slip_subscription_schedule' ),
      [
        'subscription_id' => $subscription->get_id()
      ]
    );

    // schedule the new bank slip to be processed
    // $days_before_expire before the actual expiration time
    as_schedule_single_action(
      strtotime( "-$days_before_expire days", strtotime( $datetime ) ),
      h\prefix( 'bank_slip_subscription_schedule' ),
      [
        'subscription_id' => $subscription->get_id(),
      ],
      'juno_bank_slip_subscription'
    );
  }


  /**
   * if WooCommerce Subscription removes the scheduled event
   * we should do the same
   *
   * @param WC_Subscription $subscription
   * @param string $date_type
   * @return void
   */
  public function subscription_date_deleted( $subscription, $date_type ) {
    if ( 'next_payment' !== $date_type ) {
      return;
    }

    if ( $this->id !== $subscription->get_payment_method() ) {
      return;
    }

    // cancelar todos os boletos da juno desse pedido
    // posteriormente eles todos recriados quando o novo hook for criado!
    as_unschedule_all_actions(
      h\prefix( 'bank_slip_subscription_schedule' ),
      [
        'subscription_id' => $subscription->get_id()
      ]
    );
  }


  /**
   * Generate new bank slip for subscription
   *
   * @param int $subscription_id
   * @param bool $retry if it's the first attempt or not
   * @return void
   */
  public function subscription_schedule_invoice( $subscription_id, $retry = false ) {
    $subscription = new \WC_Subscription( $subscription_id );
    $next_payment_date = $subscription->get_date( 'next_payment' );
    $slug = $this->get_method_slug();

    // if it's being processed by WooCommerce Subscriptions
    // we can use the default deadline
    if ( ! $retry ) {
      // boleto vence 1 dia antes de processar a cobrança normal da Juno
      $this->expires_at = date( 'Y-m-d', strtotime( '-1 days', strtotime( $next_payment_date ) ) );
    }

    // set the current subscription ID
    $this->subscription_id = $subscription->get_id();

    // WooCommerce Subscription next payment interval
    $next_payment_date = $subscription->get_date( 'next_payment' );

    // create the renewal order
    $renewal_order = wcs_create_renewal_order( $subscription );

    // update payment method
    $renewal_order->set_payment_method( wc_get_payment_gateway_by_order( $subscription ) );

    $renewal_order->add_order_note( __( 'Assinatura Juno: enviado primeiro boleto. Assinatura expira em ' . $next_payment_date, 'woo-juno' ) );

    // Juno bank slip specific reference
    $renewal_order->update_meta_data( '_juno_bank_slip_subscription_renewal', true );

    $renewal_order->save();

    // generate bank slip renewal order
    try {
      add_filter( h\prefix( 'payment_' . $slug . '_api_params' ), [ $this, 'subscription_schedule_invoice_params' ], 100, 1 );

      $result = parent::process_payment( $renewal_order->get_id(), false );

      // get fresh data from database
      $response = get_post_meta( $renewal_order->get_id(), '_juno_payment_response', true );

      // TODO: notify customer! and disable the forced Juno notification

      remove_filter( h\prefix( 'payment_' . $slug . '_api_params' ), [ $this, 'subscription_schedule_invoice_params' ], 100 );
    } catch (\Exception $e) {
      // $e->getMessage();
    }
  }



  /**
   * Custom invoice params
   *
   * @param mixed $params
   * @return void
   */
  public function subscription_schedule_invoice_params( $params ) {
    // should not be used outside the right scope
    if ( empty( $this->subscription_id ) ) {
      return $params;
    }

    $params['charge']['description'] .= __(
      sprintf(
        '. Renovação #%s.',
        $this->subscription_id
      ),
      'woo-juno'
    );
    $params['charge']['dueDate'] = $this->expires_at;

    // it's mandatory in this first version
    $params['billing']['notify'] = true;

    return $params;
  }



  /**
   * Process custom webhook data
   *
   * @param WC_Order $order
   * @param object $notification
   * @return void
   */
  public function notification_processed( $order, $notification ) {
    if ( $this->id !== $order->get_payment_method() ) {
      return;
    }

    $subscription_id = $order->get_meta( '_subscription_renewal' );

    // regular payment
    if ( ! $subscription_id ) {
      return;
    }

    // is Juno bank slip renewal?
    if ( ! $order->get_meta( '_juno_bank_slip_subscription_renewal' ) ) {
      return;
    }

    $subscription = new \WC_Subscription( $subscription_id );

    if ( ! $subscription ) {
      return;
    }

    if ( ! isset( $notification[0]->attributes->status ) || 'CONFIRMED' !== $notification[0]->attributes->status ) {
      return;
    }

    // next payment - if shop is not using the "sync" method, the customer may lost some days
    $calculated_next_payment = $subscription->calculate_date( 'next_payment' );

    $subscription->update_dates( array(
    	'next_payment' => $calculated_next_payment,
    ) );

    $subscription->add_order_note( __( 'Boleto da Juno Pago. Assinatura renovada' , 'woo-juno' ) );

    $subscription->save();
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
}
