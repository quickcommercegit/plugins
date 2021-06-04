<?php

namespace WC_Juno\Ajax;

use WC_Juno\Common\Abstract_Ajax_Action;
use WC_Juno\functions as h;
use WC_Juno\Service\Juno_REST_API;

class Transfer_Balance extends Abstract_Ajax_Action {
	public function get_action_name () {
		return h\prefix( 'ajax_transfer_balance' );
	}

	public function callback () {
		$amount = floatval( h\request_value( 'transfer_amount' ) );
		$error = false;
		$result = null;

		try {
			if ( $amount <= 0 ) {
				throw new \Exception( 'O valor deve ser maior que zero.' );
			}

			$juno_api = new Juno_REST_API();

			$request = $juno_api->request_transfer( [
				'amount' => $amount,
			] );

			if ( \is_wp_error( $request ) ) {
				throw new \Exception( 'Não foi possível fazer sua solicitação nesse momento.' );
			} else {
				$response = \json_decode( $request['body'] );

				// v1
				if ( isset( $response->success, $response->data ) && true === $response->success ) {
					$result = (array) $response->data;
				} elseif ( isset( $response->id ) ) { // v2
					$result = (array) $response;
				} elseif ( isset( $response->details[0]->message ) ) { // v2
					throw new \Exception( $response->details[0]->message );
				} else {
					throw new \Exception( $response->errorMessage );
				}
			}
		} catch ( \Exception $e ) {
			$error = $e->getMessage();
		}

		if ( $error ) {
			$this->send_json([
				'error_message' => $error
			], 400);
		} else {
			$this->send_json($result, 200);
		}
	}

	public function get_accepted_methods () {
		return [ 'POST' ];
	}
}
