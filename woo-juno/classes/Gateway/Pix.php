<?php

namespace WC_Juno\Gateway;

use Exception;
use WC_Juno\functions as h;

class Pix extends WC_Juno_Gateway {
	public function __construct () {
		$this->id                 = 'juno-pix';
		$this->payment_type       = 'BOLETO_PIX';
		$this->method_title       = __( 'Juno - Pix', 'woo-juno' );
		$this->method_description = __( 'Comece a receber dinheiro via Pix usando Juno.', 'woo-juno' );

		parent::__construct();

		$this->supports = [
			'products',
			'refunds',
		];
	}

	protected function setup() {
		$this->instructions     = $this->get_option( 'instructions', '' );
		$this->request_to_payer = $this->get_option( 'request_to_payer', '' );
		$this->expiration       = $this->get_option( 'expiration', 24 );
		$this->pixKey           = $this->get_option( 'pix_key' );
	}

	public function add_hooks() {
		parent::add_hooks();

		$this->add_action( 'woocommerce_thankyou_' . $this->id, 'thankyou_page' );

		$this->add_action( 'woocommerce_api_pix_details', 'pix_details_page' );

		// add buttons to print billet in my account page
		$this->add_filter( 'woocommerce_my_account_my_orders_actions', 'my_account_print_buttons', 10, 2 );

		// add buttons to print billet in my account page
		$this->add_filter( 'woocommerce_email_order_details', 'email_print_buttons', 1, 2 );
	}

	public function payment_fields () {
		h\include_wc_template(
			'checkout/juno/pix/checkout-form.php',
			[
				'id'            => $this->id,
				'description'   => wpautop( wptexturize( $this->get_description() ) ),
			]
		);
	}

	public function thankyou_page ( $order_id ) {
		$order = \wc_get_order( $order_id );

		$base64_payload = $order->get_meta( 'juno_qrcode_payload_base64' );

		if ( ! $base64_payload ) {
			return;
		}

		$payload = base64_decode( $base64_payload );

		h\include_wc_template(
			'checkout/juno/pix/thankyou.php',
			[
				'id'               => $this->id,
				'instructions'     => wpautop( wptexturize( $this->instructions ) ),
				'payload'          => $payload,
				'qrcode_image'	   => h\generate_qrcode( $payload ),
				'is_email'         => false,
			]
		);
	}

	/**
	 * email_print_buttons
	 *
	 * @param mixed $order
	 * @param mixed $sent_to_admin
	 * @return void
	 */
	public function email_print_buttons( $order, $sent_to_admin ) {
		if ( $sent_to_admin ) return;

		if ( $order->get_status() === 'on-hold' && $this->id === $order->get_payment_method() ) {
			$base64_payload = $order->get_meta( 'juno_qrcode_payload_base64' );

			if ( ! $base64_payload ) {
				return;
			}

			$payload = base64_decode( $base64_payload );

			h\include_wc_template(
				'checkout/juno/pix/thankyou.php',
				[
					'id'               => $this->id,
					'instructions'     => wpautop( wptexturize( $this->instructions ) ),
					'payload'          => $payload,
					'qrcode_image'	   => h\generate_qrcode( $payload ),
					'is_email'         => true,
				]
			);
		}
	}


	/**
	 * my_account_print_buttons
	 *
	 * @param array $actions
	 * @param WC_Order $order
	 * @return array
	 */
	public function my_account_print_buttons( $actions, $order ) {
		if ( $this->id !== $order->get_payment_method() ) {
			return $actions;
		}

		$actions[ $this->id . '-print' ] = array(
			'url'  => apply_filters(
				h\prefix( 'pay_pix_url' ),
				add_query_arg(
					array(
						'id'  => $order->get_id(),
						'key' => $order->get_order_key(),
					),
					WC()->api_request_url( 'pix_details' )
				),
				$order
			),
			'name' => __( 'Pagar Pix', 'woo-juno' ),
		);

		return $actions;
	}

	public function validate_fields () {
		// não requer validação extra
		if ( is_checkout_pay_page() ) {
			return true;
		}

		$error = '';
		try {
			$prefix = $this->id . '-';

			$this->checkout_data = [
				'person_type' => h\value( h\request_value( 'billing_persontype' ), 0 ),
			];

			// check if cpf/cnpj is present
			$cpf_cnpj = null;

			if ( $this->checkout_data['person_type'] > 0 ) {
				if ( $this->checkout_data['person_type'] == 1 ) {
					$cpf_cnpj = h\request_value( 'billing_cpf' );
				}
				elseif ( $this->checkout_data['person_type'] == 2 ) {
					$cpf_cnpj = h\request_value( 'billing_cnpj' );
				}
			} else {
				$cpf_cnpj = h\request_value( 'billing_cpf' );

				if ( empty( $cpf_cnpj ) ) {
					$cpf_cnpj = h\request_value( 'billing_cnpj' );
				}
			}

			h\throw_if(
				$this->needs_document() && empty( $cpf_cnpj ), null,
				__( 'Informe seu CPF ou CNPJ.', 'woo-juno' )
			);
		} catch (\Exception $e) {
			$error = $e->getMessage();
		}

		if ($error) {
			$error .= __( ' Caso este erro continue acontecendo, nos contate para assistência.', 'woo-juno' );
			\wc_add_notice( $error, 'error' );
			return false;
		}

		return true;
	}

	public function process_payment ( $order_id, $is_frontend = true ) {
		try {
			$order = wc_get_order( $order_id );

			list( $data, $response ) = $this->process_regular_payment( $order );

			$order->update_meta_data( 'juno_qrcode_id', $data->id );
			$order->update_meta_data( 'juno_charge_id', $data->chargeId );
			$order->update_meta_data( 'juno_qrcode_payload_base64', $data->qrcodeInBase64 );

			// save the juno api response
			$order->update_meta_data( '_juno_api_version', $this->api->version );
			$order->save();

			// Empty cart
			if ( $is_frontend ) {
				\WC()->cart->empty_cart();
			}

			// Change order status
			self::set_order_status( $order, 'AUTHORIZED' );

			return [
				'result'    => 'success',
				'redirect'  => $this->get_return_url( $order ),
			];
		} catch ( \Exception $e ) {
			$error = $e->getMessage();

			if ( $order->has_status( 'failed' ) ) {
				$order->add_order_note( 'Falha no pagamento com pix: ' . $error );
			} else {
				$order->update_status( 'failed', 'Falha no pagamento com pix: ' . $error );
			}
		}

		$error .= __( ' Caso este erro continue acontecendo, nos contate para assistência.', 'woo-juno' );

		// backend requests
		h\throw_if(
			! $is_frontend, null,
			$error
		);

		\wc_add_notice( $error, 'error' );
	}

	public function checkout_scripts () {
		if ( is_wc_endpoint_url( 'order-received' ) || isset( $_GET['id'], $_GET['key'] ) ) {
			$assets = h\assets();

			$assets->add(
				\home_url( '/' . WPINC . '/js/clipboard.min.js' ),
				[
					'handle' => 'clipboard.js'
				]
			);

			$assets->add(
				h\get_asset_url( 'dist/js/checkout-thankyou-pix.js' ),
				[
					'deps' => [ 'jquery', 'clipboard.js' ]
				]
			);
		}
	}


	public function get_payment_data( $order, $checkout_data ) {
		$cpf_cnpj = $order->get_meta( '_billing_cpf' ) ? $order->get_meta( '_billing_cpf' ) : $order->get_meta( '_billing_cnpj' );

		$params = [
      'key'            => $this->pixKey,
      'originalAmount' => $order->get_total(),
      'reference'      => 'wc-order-' . $order->get_id(),
      'requestToPayer' => $this->get_request_to_payer( $order ),
      'payerDocument'  => preg_replace( '/\D/', '', $cpf_cnpj ),
      'payerName'      => substr( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(), 0, 25 ),
      'expiration'     => $this->expiration * 60,
      'additionalData' => [
				[
					'name' => 'ID do Pedido',
					'value' => $order->get_order_number(),
				],
      ]
    ];

		return \apply_filters( h\prefix( 'payment_pix_api_params' ), $params, $order );
	}


	public function process_regular_payment( $order, $payment_data = [] ) {
    $slug = $this->get_method_slug();

    $this->log( [ 'API Version:', $this->api->version ] );

    $params = $this->get_payment_data( $order, $this->checkout_data );

    $this->log( [ 'Requesting payment for:', $params ] );

		$request = $this->api->generate_dynamic_qrcode( $params );

    if ( \is_wp_error( $request ) ) {
      $this->log( [ 'Response status (WP_Error):', $request->get_error_message() ] );
    }

    h\throw_if(
      \is_wp_error( $request ), null,
      __( 'Um erro ocorreu em nosso servidor ao processar seu pagamento. Por favor, tente novamente.', 'woo-juno' )
    );

    $this->log( [ 'Response status:', $request['response']['code'] ] );

    $response = \json_decode( $request['body'] );
    $response = \apply_filters( h\prefix( 'payment_' . $slug . '_api_response' ), $response, $order, $this->api );

    $this->log( [ 'Response received:', $response ] );

		if ( 200 !== $request['response']['code'] ) {
			$order->update_status( 'failed', 'Falha no pagamento: ' . $response->details[0]->message );
		}

		h\throw_if(
			200 !== $request['response']['code'], null,
			isset( $response->details[0]->message ) ? $response->details[0]->message : 'Ocorreu um erro ao processar seu pagamento. Tente novamente' . '.'
		);

		// just keep the array format
    return [ $response, $response ];
	}


	// we currently do not support pix fees :)
	public function get_final_cost( $total, $installments ) {
		return $total;
	}


	/**
	 * Check if the gateway is available for use.
	 *
	 * @return bool
	 */
  public function is_available() {
    if ( 1.50 > $this->get_order_total() || empty( $this->pixKey ) || 2 !== $this->api->version ) {
      return false;
    }

		return parent::is_available();
	}


	public function pix_details_page() {
		try {

			if ( ! isset( $_GET['id'], $_GET['key'] ) ) {
				throw new \Exception( 'URL inválida' );
			}

			$order_id = esc_attr( $_GET['id'] );
			$order_key = esc_attr( $_GET['key'] );

			$order = wc_get_order( $order_id );

			if ( ! $order ) {
				throw new \Exception( 'Pedido não encontrado' );
			}

			if ( $order->get_order_key() !== $order_key ) {
				throw new \Exception( 'Sem permissões para visualizar esta página' );
			}

			get_header();

			echo '<div class="woocommerce-thankyou-order-received"></div>';

			$this->thankyou_page( $order->get_id() );

			get_footer();

			exit;

		} catch (\Exception $e) {
			wp_die( $e->getMessage() );
		}
	}


	public function get_request_to_payer( $order ) {
		return apply_filters(
			h\prefix( 'pix_request_to_payer' ),
			str_replace(
				'{order_id}',
				$order->get_id(),
				$this->request_to_payer
			),
			$order,
			$this
		);
	}


	/**
	 * Process refund.
	 *
	 *
	 * @param  int    $order_id Order ID.
	 * @param  float  $amount Refund amount.
	 * @param  string $reason Refund reason.
	 * @return boolean True or false based on success, or a WP_Error object.
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		if ( '0.00' === $amount ) {
			// throw new Exception( __( 'Reembolso precisa ser maior que R$ 0,00.', 'woo-juno' ) );
		}

		$order = wc_get_order( $order_id );

		$qrcode_id = $order->get_meta( 'juno_qrcode_id' );

		if ( ! $qrcode_id ) {
			throw new Exception( __( 'QR Code não encontrado no pedido.', 'woo-juno' ) );
		}

		$response = $this->api->process_pix_refund( $qrcode_id, $amount, $reason );

		$this->log( print_r($response,true) );

		if ( is_wp_error( $response ) ) {
			throw new Exception( __( 'Erro interno: ', 'woo-juno' ) . $response->get_error_message() );
		} elseif ( 200 !== $response['response']['code'] ) {
			$this->log( 'Erro ao processar reembolso: ' . print_r( $body, true ) );

			$message = isset( $body->details[0]->message ) ? $body->details[0]->message : __( 'erro desconhecido. Veja os Logs', 'woo-juno' );

			throw new Exception( __( 'Solicitação inválida à Juno: ', 'woo-juno' ) . $message );
		} else {
			$body = json_decode( $response['body'] );

			$this->log( 'Reembolso processado com sucesso: ' . print_r( $body, true ) );
		}

		return true;
	}
}
