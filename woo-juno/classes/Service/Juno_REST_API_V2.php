<?php

namespace WC_Juno\Service;

use WC_Juno\functions as h;

abstract class Juno_REST_API_V2 {
  public    $version = 2;
  protected $token   = null;
  public    $sandbox = null;

  public function __construct ( $sandbox = null, $gateway = null ) {
    $this->sandbox      = null === $sandbox ? h\is_sandbox_enabled() : $sandbox;
    $this->token        = h\get_private_token( $sandbox );
    $this->public_token = h\get_public_token();

    $this->client_id     = htmlspecialchars_decode( h\get_settings_option( 'client_id' ) );
    $this->client_secret = htmlspecialchars_decode( h\get_settings_option( 'client_secret' ) );

    $this->gateway = $gateway;
  }

  // charge
  public function request_payment ( $params = [], $method = 'credit_card' ) {
    $response = $this->do_request( 'charges', 'POST', $params );

    return $response;
  }


  public function process_credit_card_payments( $order, $charge_id, $checkout_data ) {
    $card_id = $this->get_card_id( $checkout_data, $order );

    if ( ! is_string( $card_id ) ) {
      return $card_id;
    }

    $params   = array(
      'chargeId'          => $charge_id,
      'billing'           => array(
        'email'   => strtolower( $order->get_billing_email() ),
        'address' => array(
          'street'       => $order->get_billing_address_1(),
          'number'       => $order->get_meta( '_billing_number' ),
          'complement'   => $order->get_billing_address_2(),
          'neighborhood' => $order->get_meta( '_billing_neighborhood' ),
          'city'         => $order->get_billing_city(),
          'state'        => $order->get_billing_state(),
          'postCode'     => h\get_numbers( $order->get_billing_postcode() ),
        )
      ),
      'creditCardDetails' => array(
        'creditCardId'        => $card_id,
      )
    );
    $response = $this->do_request( 'payments', 'POST', $params );

    return $response;
  }


  public function get_card_id( $checkout_data, $order ) {
    if ( isset( $checkout_data['card_id'] ) ) {
      return $checkout_data['card_id'];
    } elseif ( $checkout_data['mode'] === 'list' ) {

      $card_info = $this->gateway->get_card_data( $checkout_data['card_info'] );

      $card_data = h\get_user_credit_card(
        $order->get_customer_id(),
        $card_info['brand'],
        $card_info['last_numbers']
      );

      $card_data['creditCardId'] = $card_data['card_id'];

    } else {
      $card_hash = $this->get_card_token( $checkout_data['card_hash'] );

      if ( is_wp_error( $card_hash ) || 200 !== $card_hash['response']['code'] ) {
        return $card_hash;
      }

      $card_data = json_decode( $card_hash['body'], true );

      if ( $checkout_data['save_card'] && $this->gateway ) {
        $this->gateway->save_customer_card( $card_data, $checkout_data, $order );
      } else {
        $this->log( [ 'Credit Card NOT STORED' ] );
      }
    }

    if ( isset( $card_data['creditCardId'] ) ) {
      return $card_data['creditCardId'];
    }

    return $card_data;
  }


  public function get_card_token( $card_hash ) {
    $data     = array(
      'creditCardHash' => $card_hash
    );
    $response = $this->do_request( 'credit-cards/tokenization', 'POST', $data );

    return $response;
  }


  public function fetch_payment_details ( $charge_id ) {
    $response = $this->do_request( 'charges/' . $charge_id, 'GET' );

    return $response;
  }

  public function fetch_balance ( $params = [] ) {
    $response = $this->do_request( 'balance', 'GET' );

    return $response;
  }

  public function request_transfer ( $params = [] ) {
    $params['type'] = 'DEFAULT_BANK_ACCOUNT'; // amount from params

    $response = $this->do_request( 'transfers', 'POST', $params );

    return $response;
  }

  protected function get_api_endpoint( $endpoint, $authorization = false ) {
    $base = $this->sandbox ? 'sandbox.boletobancario.com/api-integration' : 'api.juno.com.br';

    if ( $authorization ) {
      $base = $this->sandbox ? 'sandbox.boletobancario.com/authorization-server' : 'api.juno.com.br/authorization-server';
    }

    return "https://$base/$endpoint";
  }



  public function get_payment_data ( $order, $checkout_data, $gateway ) {
    $person_type  = intval( $order->get_meta( '_billing_persontype' ) );
    $cpf_cnpj     = 2 === $person_type ? $order->get_meta( '_billing_cnpj' ) : $order->get_meta( '_billing_cpf' );
    $cpf_cnpj     = preg_replace( '/\D/', '', $cpf_cnpj );
    $installments = intval( $checkout_data['installments'] );

    if ( 'CREDIT_CARD' === $gateway->payment_type && 'redirect' === $gateway->integration_method ) {
      $installments = intval( $gateway->max_installments );
    }

    $installments = 1 > $installments ? 1 : $installments;
    $order_total  = $order->get_total();
    $order_total  = $gateway->get_final_cost( $order_total, $installments );
    $amount       = $order_total / $installments;

    $references   = array();
    foreach ( range( 1, $installments ) as $installment ) {
      $references[] = 'wc-order-' . $order->get_id();
    }

    $method = 'BOLETO' === $gateway->payment_type ? 'Boleto' : 'Cartão de crédito';
    $params = array(
      'charge' => array(
        'description'    => sprintf( 'Pedido %s do %s (com %s)', $order->get_id(), \home_url( '/' ), $method ),
        'references'     => $references,
        // 'totalAmount'    => $order_total,
        'amount'         => wc_format_decimal( $amount, 2 ),
        'installments'   => $installments,
        // 'discountAmount' => 0,
        // 'discountDays'   => -1,
        'paymentTypes'   => array(
          $gateway->payment_type
        ),
        'paymentAdvance' => isset( $gateway->payment_advance ) && $gateway->payment_advance ? 'true' : 'false',
      ),
      'billing' => array(
        'name'           => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
        'document'       => $cpf_cnpj,
        'email'          => strtolower( $order->get_billing_email() ),
        'phone'          => $order->get_billing_phone(),
        'notify'         => $gateway->notify_payer
      )
    );

    if ( 'juno-bank-slip' === $gateway->id ) {
      $bank_slip_extra = array(
        'dueDate'        => $this->gateway->get_due_date(),
        'maxOverdueDays' => intval( $gateway->max_overdue_days ),
        'fine'           => intval( $gateway->fine ),
        'interest'       => intval( $gateway->interest ),
      );

      $params['charge'] = wp_parse_args( $bank_slip_extra, $params['charge'] );
    }

    return $params;
  }



  public function generate_dynamic_qrcode( $args ) {
    $request = $this->do_request( 'pix/qrcodes/dynamic', 'POST', $args );

    return $request;
  }



  public function process_pix_refund( $qrcode_id, $amount, $reason = 'Reembolso' ) {
    $request = $this->do_request(
      sprintf( 'pix/qrcodes/dynamic/%s/refunds', $qrcode_id ),
      'POST',
      array(
        'amount'            => $amount,
        'refundCode'        => 'PAYER_REQUEST',
        'refundDescription' => $reason,
      )
    );

    return $request;
  }




  public function get_authorization_token() {
    $transient_name = h\prefix( 'access_token_' . ( $this->sandbox ? 'sandbox' : 'production' ) );

    if ( false === ( $token = get_transient( $transient_name ) ) ) {

      $endpoint = $this->get_api_endpoint( 'oauth/token', true );
      $response = wp_remote_post( $endpoint, array(
        'method' => 'POST',
        'timeout' => 30,
        'headers' => array(
          'Authorization' => 'Basic ' . base64_encode( $this->client_id . ':' . $this->client_secret ),
          'content-type' => 'application/x-www-form-urlencoded'
        ),
        'body'    => array(
          'grant_type' => 'client_credentials'
        )
      ) );

      if ( is_wp_error( $response ) ) {
        return false;
      } elseif ( 200 === $response['response']['code'] ) {
        $data  = json_decode( $response['body'] );
        $token = $data->access_token;
        set_transient( $transient_name, $token, $data->expires_in - 600 ); // 10 minutes
      }
    }

    return $token;
  }


  // Process Refund
  public function request_refund ( $payment_id, $params = [] ) {
    $response = $this->do_request( sprintf( 'payments/%s/refunds', $payment_id ), 'POST', $params );

    return $response;
  }


  public function get_events_list() {
    $response = $this->do_request( 'notifications/event-types', 'GET' );

    return $response;
  }


  public function get_webhooks() {
    $response = $this->do_request( 'notifications/webhooks', 'GET' );

    return $response;
  }


  public function create_webhook() {
    $data     = array(
      'url'        => WC()->api_request_url( 'woo_juno_notifications' ),
      'eventTypes' => array(
        'PAYMENT_NOTIFICATION'
      )
    );
    $response = $this->do_request( 'notifications/webhooks', 'POST', $data );

    return $response;
  }


  public function delete_all_webhooks() {
    $request = $this->get_webhooks();

    if ( \is_wp_error( $request ) ) {
      return false;
    }

    $result   = true;
    $response = \json_decode( $request['body'] );

    if ( isset( $response->_embedded->webhooks ) ) {
      foreach ( $response->_embedded->webhooks as $webhook ) {
        foreach ( $webhook->eventTypes as $event ) {
          if ( 'PAYMENT_NOTIFICATION' === $event->name ) {
            $result = $this->delete_webhook( $webhook->id );
            break;
          }
        }
      }
    }

    return is_wp_error( $result ) ? false : $result;
  }


  public function delete_webhook( $webhook_id ) {
    $response = $this->do_request( 'notifications/webhooks/' . $webhook_id, 'DELETE' );

    return $response;
  }


  public function get_charge( $charge_id ) {
    $response = $this->do_request( sprintf( 'charges/%s', $charge_id ), 'GET' );

    return $response;
  }


  public function cancel_charge( $charge_id ) {
    $response = $this->do_request( sprintf( 'charges/%s/cancelation', $charge_id ), 'PUT' );

    return $response;
  }


  /**
   * Do requests in the Juno API.
   *
   * @param  string $url      URL.
   * @param  string $method   Request method.
   * @param  array  $data     Request data.
   * @param  array  $headers  Request headers.
   *
   * @return array            Request response.
   */
  protected function do_request( $endpoint, $method = 'POST', $data = array(), $headers = array() ) {
    $url    = $this->get_api_endpoint( $endpoint );
    $params = array(
      'method'  => $method,
      'timeout' => 60,
      'headers' => array(
        'content-type'     => 'application/json;charset=UTF-8',
        'accept-charset'   => 'utf-8',
        'x-platform'       => 'pluginwoocommerce',
        'X-Api-Version'    => 2,
        'X-Resource-Token' => $this->token,
        'X-Idempotency-Key' => wp_generate_uuid4(),
        'Authorization'    => 'Bearer ' . $this->get_authorization_token()
      )
    );

    if ( in_array( $method, array( 'POST', 'DELETE' ) ) && ! empty( $data ) ) {
      $params['body'] = json_encode( $data );
    }

    if ( ! empty( $headers ) ) {
      $params['headers'] = wp_parse_args( $headers, $params['headers'] );
    }

    $params = apply_filters( h\prefix( 'api_request_args' ), $params, $url, $this );

    return wp_safe_remote_post( $url, $params );
  }


  public function log( $message ) {
    if ( $this->gateway ) {
      $this->gateway->log( $message );
    }
  }
}
