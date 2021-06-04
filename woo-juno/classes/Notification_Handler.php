<?php

namespace WC_Juno;

use WC_Juno\Common\Hooker_Trait;
use WC_Juno\Common\WC_Logger_Trait;
use WC_Juno\functions as h;

class Notification_Handler {
	use Hooker_Trait;
	use WC_Logger_Trait;

	public function add_hooks () {
		$this->add_action( 'parse_request', 'validate_request' );
	}

	public function validate_request () {
		if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
			return;
		}

		$post_data = $_POST;
		$payment_token = h\array_get( $post_data, 'paymentToken', '' );

		if ( ! isset( $_GET[ self::get_query_arg() ] ) ) {
			return;
		}
		if ( '' === $payment_token ) {
			return;
		}

		$sandbox = isset( $_GET['sandbox'] );
		$log_enabled = $sandbox || isset( $_GET['log'] );

		if ( $log_enabled ) {
			$this->set_wc_logger_source( h\config_get( 'SLUG' ) . '-api-notifications' );
		}

		$juno_api = new Service\Juno_REST_API();
		$params = [
			'paymentToken' => $payment_token,
		];

		$this->log( [ 'Receiving notification from Juno:', $post_data ] );

		$this->log( 'Requesting notification payment details.' );

		$request = $juno_api->fetch_payment_details( $params );

		if ( \is_wp_error( $request ) ) {
			$this->log( [ 'WP Error:', $request ] );
		}

		$response = \json_decode( $request['body'] );

		$this->log( [ 'Response received:', $response ] );

		if ( $response->success ) {
			$this->handle_request( $response->data );
			\status_header( 200 );
			die();
		}

		\status_header( 400 );
		die('-1');
	}

	protected function handle_request ( $data ) {
		$status = $data->payment->status;
		$reference = $data->payment->charge->reference;
		$payment_code = $data->payment->charge->code;
		$order = null;
		$payment_type = $data->payment->type;

		try {
			if ( h\str_starts_with( $reference, 'wc-order-' ) ) {
				$order_id = intval( h\str_after( $reference, 'wc-order-' ) );
				$order = \wc_get_order( $order_id );

				if ( ! in_array( $payment_type, [ 'BOLETO', 'CREDIT_CARD' ] ) ) {
					throw new \Exception(
						__( 'Tipo de pagamento inválido', 'woo-juno' ) . ": $payment_type"
					);
				}

				if ( $order ) {
					$order_id = $order->get_id();
					$meta_key = '_' . h\prefix( 'order_handled' );

					if ( 1 == $order->get_meta( $meta_key ) ) {
						throw new \Exception(
							__(
								sprintf( "Já foi recebida uma notificação para o pedido #%s", $order_id ),
								'woo-juno'
							)
						);
						return;
					}

					$order->update_meta_data( $meta_key, 1 );

					// update order status
					if ( 'BOLETO' == $payment_type ) {
						Gateway\Bank_Slip::set_order_status( $order, $status );
					} else {
						Gateway\Credit_Card::set_order_status( $order, $status );
					}
				} else {
					throw new \Exception(
						__( 'Número de pedido inválido', 'woo-juno' ) . ": $order_id"
					);
				}
			} else {
				throw new \Exception(
					__( 'Código de referência recebido não é válido', 'woo-juno' ) . ": $reference"
				);
			}
		} catch ( \Exception $e ) {
			$this->log( $e->getMessage(), 'error' );
		}
	}

	public static function get_query_arg () {
		$arg = h\prefix( 'api_notification' );
		return $arg;
	}
}
