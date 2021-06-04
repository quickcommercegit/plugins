<?php

/**
 * Abstract payment gateway
 *
 * Hanldes generic payment gateway functionality which is extended by idividual payment gateways.
 *
 * @class WC_Juno_Gateway
 * @version 2.1.0
 * @package WooCommerce/Abstracts
 */

namespace WC_Juno\Gateway;

use WC_Juno\Common\Hooker_Trait;
use WC_Juno\Common\WC_Logger_Trait;
use WC_Juno\functions as h;
use WC_Juno\Service\Juno_REST_API;
use WC_Payment_Gateway;
use WC_Subscriptions_Cart;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


abstract class WC_Juno_Gateway extends WC_Payment_Gateway {
	use Hooker_Trait;
	use WC_Logger_Trait;

	protected static $log_enabled = false;

	protected $checkout_data = null;

  function __construct() {
		$this->icon               = '';
		$this->has_fields         = true;
		$this->test_mode          = h\is_sandbox_enabled();
    $this->token              = h\get_private_token();
		$this->supports           = [
			'products',
    ];

		// Setup general properties
		$this->setup();

		// Load the settings
		$this->init_form_fields();
    $this->init_settings();

		// Define user set variables
		$this->enabled            = $this->get_option( 'enabled' );
		$this->title              = $this->get_option( 'title' );
    $this->description        = $this->get_option( 'description' );

		self::$log_enabled        = 'yes' === $this->get_option( 'debug', 'no' );

		if ( self::$log_enabled ) {
			$this->set_wc_logger_source( $this->id );
		}

		// register hooks
    $this->add_hooks();

    $this->api = new Juno_REST_API( $this->test_mode, $this );
  }

  /**
	 * Return whether or not this gateway still requires setup to function.
	 *
	 * When this gateway is toggled on via AJAX, if this returns true a
	 * redirect will occur to the settings page instead.
	 *
	 * @return bool
	 */
	public function needs_setup() {
		return false;
  }

	/**
	 * Output the gateway settings screen.
	 */
	public function admin_options() {
		if ( empty( $this->form_fields ) ) {
			h\include_php_template(
				'need-setup.php'
			);

			return;
		}

		parent::admin_options();
  }


  /**
   * Get gateway slug without prefx
   *
   * @return string
   */
  protected function get_method_slug() {
    return str_replace( [ 'juno-', '-' ], [ '', '_' ], $this->id );
  }

	/**
	 * Initialise settings form fields.
	 *
	 */
	public function init_form_fields() {
    $slug = $this->get_method_slug();
		if ( empty( $this->token ) ) {
			$this->form_fields = [];
		} else {
			$this->form_fields = require 'inc/settings_' . $slug . '.php';
			$this->form_fields = \apply_filters( h\prefix( $slug . '_settings' ), $this->form_fields, $this );
		}
  }


  /**
   * Register gateway specific hooks
   *
   * @return void
   */
  public function add_hooks() {
    $this->checkout_scripts();

		$this->add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, 'process_admin_options' );
  }

  /**
   * Register gateway initial setup
   *
   * @return void
   */
  protected function setup() {}

  /**
   * Register gateway checkout script
   *
   * @return void
   */
  protected function checkout_scripts() {}


	/**
	 * Check if the gateway is available for use.
	 *
	 * @return bool
	 */
  public function is_available() {
    if ( empty( $this->token ) || ! $this->using_supported_currency() ) {
      return false;
    }

		return parent::is_available();
	}


  /**
  * Payment data for current order
  *
  * @param WC_Order $order
  * @param array $checkout_data
  * @return voidarray
  */
  abstract protected function get_payment_data( $order, $checkout_data );

	/**
	 * Check if is using Brazilian currency
	 *
	 * @return bool
	 */
	protected function using_supported_currency() {
		return 'BRL' === get_woocommerce_currency();
	}


  /**
   * Process juno default charge body
   *
   * @param WC_Order $order
   * @return array
   */
  protected function process_regular_payment( $order, $payment_data = [] ) {
    $slug = $this->get_method_slug();

    $this->log( [ 'API Version:', $this->api->version ] );

    if ( $payment_data ) {
			$params = \apply_filters( h\prefix( 'payment_' . $slug . '_api_params' ), $payment_data, $order );
		} if ( 2 === $this->api->version ) {
      $params = $this->api->get_payment_data( $order, $this->checkout_data, $this );
      $params = \apply_filters( h\prefix( 'payment_' . $slug . '_api_params' ), $params, $order );
    } else {
      $params = $this->get_payment_data( $order, $this->checkout_data );
		}

    $this->log( [ 'Requesting payment for:', $params ] );

		$request = $this->api->request_payment( $params, $slug );

    if ( \is_wp_error( $request ) ) {
      $this->log( [ 'Response status (WP_Error):', $request->get_error_message() ] );
    }

    h\throw_if(
      \is_wp_error( $request ), null,
      __( 'Um erro ocorreu em nosso servidor enquanto processavamos seu pagamento. Por favor, tente novamente.', 'woo-juno' )
    );

    $this->log( [ 'Response status:', $request['response']['code'] ] );

    $response = \json_decode( $request['body'] );
    $response = \apply_filters( h\prefix( 'payment_' . $slug . '_api_response' ), $response, $order, $this->api );

    $this->log( [ 'Response received:', $response ] );

    if ( 2 === $this->api->version ) {
      if ( 200 !== $request['response']['code'] ) {
        $order->update_status( 'failed', 'Falha no pagamento: ' . $response->details[0]->message );
      }

      h\throw_if(
        200 !== $request['response']['code'], null,
        isset( $response->details[0]->message ) ? $response->details[0]->message : 'Ocorreu um erro ao processar seu pagamento. Tente novamente' . '.'
      );
    } else {
      $error_message = isset( $response->errorMessage ) ? $response->errorMessage : 'Ocorreu um erro ao processar seu pedido';

      if ( ! $response->success ) {
        $order->update_status( 'failed', 'Falha no pagamento: ' . $error_message );
      }

      h\throw_if(
        ! $response->success, null,
        $error_message . '.'
      );
    }

    $data = isset( $response->data ) ? $response->data : $response->_embedded;

    return [ $data, $response ];
  }





	/**
	 * Set order status based on Juno Status
	 *
	 * @param WC_Order $order
	 * @param string $juno_status
	 * @return bool
	 */
	public static function set_order_status( $order, $juno_status ) {
		$statuses = [
			'AUTHORIZED'         => 'on-hold',
			'DECLINED'           => 'failed',
			'FAILED'             => 'failed',
			'NOT_AUTHORIZED'     => 'failed',
			'CONFIRMED'          => 'processing',
			'CUSTOMER_PAID_BACK' => 'refunded',
			'BANK_PAID_BACK'     => 'refunded',
			'PARTIALLY_REFUNDED' => 'refunded',
		];

		$statuses  = \apply_filters( h\prefix( 'order_statuses' ), $statuses, $order );
		$wc_status = isset( $statuses[ $juno_status ] ) ? $statuses[ $juno_status ] : false;

		if ( ! $wc_status ) return false;

		$note = '';
		switch ( $juno_status ) {
			case 'AUTHORIZED':
				$note = __( 'Aguardando pagamento.', 'woo-juno' );
			break;

			case 'DECLINED':
				$note = __( 'Pagamento rejeitado pela análise de risco.', 'woo-juno' );
			break;

			case 'FAILED':
				$note = __( 'Pagamento não realizado.', 'woo-juno' );
			break;

			case 'NOT_AUTHORIZED':
				$note = __( 'Pagamento não autorizado pela instituição responsável pelo cartão de crédito.', 'woo-juno' );
			break;

			case 'CONFIRMED':
				$note = __( 'Pagamento confirmado.', 'woo-juno' );
			break;

			default:
		}

		if ( 'processing' === $wc_status ) {
			$order->add_order_note( __( 'Juno: Pagamento confirmado.', 'woo-juno' ) );
			$order->payment_complete();
		} else {
			$order->update_status( $wc_status, $note );
		}

		return true;
	}


  /**
   * Add credit card to customer account
   *
   * @param array $card_data
	 * @param array $checkout_data
   * @param WC_Order $order
   * @return void
   */
	public function save_customer_card( $card_data, $checkout_data, $order ) {}


  /**
   * Check if current cart is a subscription
   *
   * @return void
   */
  public function is_subscription() {
    return isset( $_POST['woocommerce_change_payment'] ) || class_exists( 'WC_Subscriptions_Cart' ) && WC_Subscriptions_Cart::cart_contains_subscription();
  }

	/**
	 * Document is not required on order-pay endpoint
	 *
	 * @return bool
	 */
	public function needs_document() {
		return ! isset( $_POST['woocommerce_change_payment'] ) && ! is_checkout_pay_page();
	}
}
