<?php

namespace WC_Juno\Gateway;

use WC_Juno\Service\Juno_REST_API;
use WC_Juno\Notification_Handler;
use WC_Juno\functions as h;

class Credit_Card extends WC_Juno_Gateway {
	public function __construct () {
		$this->id                 = 'juno-credit-card';
		$this->payment_type       = 'CREDIT_CARD';
		$this->method_title       = __( 'Juno - Cartão de Crédito', 'woo-juno' );
		$this->method_description = __( 'Comece a receber dinheiro via cartão de crédito usando Juno.', 'woo-juno' );

		parent::__construct();

		$this->supports = [
			'products',
			'refunds'
		];

		$this->add_action( 'woocommerce_admin_order_totals_after_total', 'display_order_fee' );
	}

	protected function setup() {
		$this->integration_method     = $this->get_option( 'integration_method', 'direct' );
		$this->store_user_cards       = 'yes' === $this->get_option( 'store_user_cards', 'yes' );
		$this->show_visual_card       = 'yes' === $this->get_option( 'show_visual_card', 'yes' );
		$this->max_installments       = $this->get_option( 'max_installments', 1 );
		$this->smallest_installment   = $this->get_option( 'smallest_installment', 5 );
		$this->payment_advance        = 'yes' === $this->get_option( 'payment_advance' );

		$this->public_token       = h\get_public_token();
		$this->notify_payer       = 'yes' === h\get_settings_option( 'notify_payer' );
	}

	public function admin_options() {
		parent::admin_options();

		?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$( document.body ).on( 'change', '#woocommerce_juno-credit-card_integration_method', function( event ) {
					if ( 'redirect' === $(this).val() ) {
						$( '#woocommerce_juno-credit-card_show_visual_card, #woocommerce_juno-credit-card_store_user_cards, #woocommerce_juno-credit-card_smallest_installment, #woocommerce_juno-credit-card_payment_advance' ).closest( 'tr' ).hide();

						$( '#woocommerce_juno-credit-card_installments_fee_header, #installments_fee_header' ).hide();

						$( '#woocommerce_juno-credit-card_installments_fee_1' ).closest( 'table' ).hide();
					} else {
						$( '#woocommerce_juno-credit-card_show_visual_card, #woocommerce_juno-credit-card_store_user_cards, #woocommerce_juno-credit-card_smallest_installment, #woocommerce_juno-credit-card_payment_advance' ).closest( 'tr' ).show();

						$( '#woocommerce_juno-credit-card_installments_fee_header, #installments_fee_header' ).show();

						$( '#woocommerce_juno-credit-card_installments_fee_1' ).closest( 'table' ).show();
					}
				});

				$( '#woocommerce_juno-credit-card_integration_method' ).change();
			});
		</script>
		<?php
	}

	public function payment_fields () {
		h\include_wc_template(
			'checkout/juno/credit-card/checkout-form.php',
			[
				'id' => $this->id,
				'description' => wpautop( wptexturize( $this->get_description() ) ),
				'installments' => $this->get_installments_options(),
			]
		);
	}

	public function validate_fields () {
		$error = '';

		try {
			if ( 'direct' === $this->integration_method ) {
				$prefix = $this->id . '-';
				$mode = h\request_value( $prefix . 'checkout-mode' );

				h\throw_if(
					! in_array( $mode, [ 'list', 'form' ] ),
					null,
					'Não foi possível processar sua compra.'
				);

				$this->checkout_data = [
					'card_info' => h\request_value( $prefix . 'card-info' ),
					'card_hash' => h\request_value( $prefix . 'card-hash' ),
					'card_cvc' => h\request_value( $prefix . 'cvc' ),
					'save_card' => 'yes' == h\request_value( $prefix . 'save-card' ),
					'installments' => h\value( h\request_value( $prefix . 'installments'), 1 ),
					'mode' => $mode,
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

				// check credit card informations
				$user_id = \get_current_user_id();
				$card_data = $this->get_card_data( $this->checkout_data['card_info'] );

				h\throw_if(
					! $card_data, null,
					__( 'Preencha corretamente todas as informações do cartão de crédito.', 'woo-juno' )
				);

				h\throw_if(
					! $this->validate_installments( $this->checkout_data['installments'] ), null,
					__( 'Número de parcelas inválido.', 'woo-juno' )
				);

				if ( 'list' === $mode ) {
					if ( $user_id ) {
						$stored_card = h\get_user_credit_card( $user_id, $card_data['brand'], $card_data['last_numbers'] );

						if ( $stored_card && $stored_card['card_id'] ) {
							$stored_hash = $stored_card['cvc_hash'];

							$stored_card['cvc'] = $card_data['cvc'];
							$current_hash = h\generate_credit_card_cvc_hash( $stored_card );

							if ( $current_hash != $stored_hash ) {
								h\throw_if(
									true, null,
									__( 'O código de segurança do cartão está incorreto.', 'woo-juno' )
								);
							}
						} else {
							h\throw_if(
								true, null,
								__( 'Seu cartão não foi encontrado. Por favor, cadastre um novo cartão de crédito.', 'woo-juno' )
							);
						}
					} else {
						h\throw_if(
							! $card_data, null,
							__( 'Sua sessão expirou. Por favor, recarregue a página e faça entre na sua conta novamente.', 'woo-juno' )
						);
					}
				} else {
					h\throw_if(
						! $this->checkout_data['card_hash'], null,
						__( 'Não foi possível processar seu cartão.', 'woo-juno' )
					);
				}
			} else {

			}
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

	public function process_payment( $order_id ) {
		try {
			$order = wc_get_order( $order_id );

			$charge_id = $order->get_meta( 'juno_charge_id' );

			// cancel previous charge
			if ( $charge_id && 2 === $this->api->version ) {
				$this->api->cancel_charge( $charge_id );
			}

			list( $data, $response ) = $this->process_regular_payment( $order );

			foreach ( $data->charges as $charge ) {
				// 2.0
				if ( isset( $charge->id ) ) {
					$order->update_meta_data( 'juno_charge_id', $charge->id );
				}

				$order->update_meta_data( 'juno_charge_code', $charge->code );
				$order->update_meta_data( 'juno_checkout_url', $charge->checkoutUrl );

				$payment_url = $charge->checkoutUrl;
			}

			// save the juno api response
			$order->update_meta_data( '_juno_payment_response', $data );
			$order->update_meta_data( '_juno_api_version', $this->api->version );

			$order->save();

			$redirect_url = $this->get_return_url( $order );

			if ( 'direct' === $this->integration_method ) {
				if ( 2 === $this->api->version ) {

					$request = $this->api->process_credit_card_payments( $order, $charge->id, $this->checkout_data );

					h\throw_if(
						\is_wp_error( $request ), null,
						__( 'Um erro ocorreu em nosso servidor enquanto processavamos seu pagamento. Por favor, tente novamente.', 'woo-juno' )
					);

					$response = \json_decode( $request['body'] );

					$this->log( [ 'Payment response received:', $response ] );

					$order->update_meta_data( '_juno_v2_payment_response', $response );

					if ( 200 !== $request['response']['code'] ) {
						if ( $order->has_status( 'failed' ) ) {
							$order->add_order_note( __( 'Falha em nova tentativa de pagamento com cartão: ', 'woo-juno' ) . $response->details[0]->message );
						} else {
							$order->update_status( 'failed', __( 'Falha no pagamento com cartão: ', 'woo-juno' ) . $response->details[0]->message );
						}
					}

					h\throw_if(
						200 !== $request['response']['code'], null,
						isset( $response->details[0]->message ) ? $response->details[0]->message : __( 'Ocorreu um erro ao processar seu pagamento. Tente novamente', 'woo-juno' ) . '.'
					);

					$card_data = $this->get_card_data( $this->checkout_data['card_info'] );

					$order->update_meta_data( '_juno_v2_credit_card_info', [
						'installments' => $response->installments,
						'amount' => $response->payments[0]->amount,
						'brand' => $card_data['brand'],
						'last_numbers' => $card_data['last_numbers'],
					] );

					if ( isset( $response->payments[0]->fee ) ) {
						$fee = array_sum( wp_list_pluck( $response->payments, 'fee' ) );
						$order->update_meta_data( '_juno_fee', $fee );
					}

					$order->add_order_note( sprintf(
						__( 'Compra feita em %sx com um cartão %s. Valor total: %s', 'woo-juno' ),
						$response->installments,
						ucfirst( $card_data['brand'] ),
						wc_price( $response->payments[0]->amount )
					) );

				// v1
				} elseif ( 'form' === $this->checkout_data['mode'] && $this->customer_can_store_cards() && $this->checkout_data['save_card'] ) {
					$card_data = $this->get_card_data( $this->checkout_data['card_info'] );
					$card_data['card_id'] = $this->get_card_id_from_response( $response );

					if ( $card_data['card_id'] ) {
						$this->log( [
							'Saving credit card', $card_data['brand'],
							'with last numbers', $card_data['last_numbers']
						] );
						h\add_user_credit_card( \get_current_user_id(), $card_data );
						$this->log( ['Credit Card STORED' ] );
					} else {
						$this->log( [ 'Credit Card NOT STORED' ] );
					}
				}
			} else {
				h\throw_if(
					! isset( $payment_url ),
					null,
					'Não foi recuperar a URL de pagamento. Tente novamente'
				);

				$redirect_url = $payment_url;
			}

			// Empty cart
			\WC()->cart->empty_cart();

			// Change order status
			$juno_status = $this->get_order_status_from_response( $response, $this->api->version );

			if ( $juno_status ) {
				self::set_order_status( $order, $juno_status );
			} else {
				self::set_order_status( $order, 'AUTHORIZED' );
			}

			// reduce stock if has paid
			if ( 'processing' === $order->get_status() ) {
				$order->payment_complete();
				\wc_reduce_stock_levels( $order_id );
			}

			return [
				'result'    => 'success',
				'redirect'  => $redirect_url,
			];
		} catch ( \Exception $e ) {
			$error = $e->getMessage();
		}

		$error .= __( ' Caso este erro continue acontecendo, nos contate para assistência.', 'woo-juno' );
		\wc_add_notice( $error, 'error' );
	}

	protected function get_payment_data( $order, $checkout_data ) {
		$person_type = intval( $order->get_meta( '_billing_persontype' ) );
		$installments = intval( $checkout_data['installments'] );
		$total        = $order->get_total();
		$total        = $this->get_final_cost( $total, $installments );

		if ( 'redirect' === $this->integration_method ) {
      $installments = $this->get_max_installments();
		}

		$installments = 1 > $installments ? 1 : $installments;
		$amount       = $total / $installments;
		$notication_query_args = [
			'log' => self::$log_enabled ? '1' : '0',
			'sandbox' => $this->test_mode ? '1' : '0',
		];
		$notication_query_args[ Notification_Handler::get_query_arg() ] = '';

		$params = [
			'amount'      => number_format( $amount, 2, '.', '' ),
			'reference'   => 'wc-order-' . $order->get_id(),
			'description' => sprintf( 'Pedido %s do %s  (com Cartão de Crédito)', $order->get_id(), \home_url( '/' ) ),

			'installments' => $installments,

			'payerName'   => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
			'payerEmail'  => strtolower( $order->get_billing_email() ),
			'payerPhone'  => h\get_numbers( $order->get_billing_phone() ),

			'billingAddressStreet'       => $order->get_billing_address_1(),
			'billingAddressNumber'       => $order->get_meta( '_billing_number' ),
			'billingAddressComplement'   => $order->get_billing_address_2(),
			'billingAddressNeighborhood' => $order->get_meta( '_billing_neighborhood' ),
			'billingAddressCity'         => $order->get_billing_city(),
			'billingAddressState'        => $order->get_billing_state(),
			'billingAddressPostcode'     => $order->get_billing_postcode(),

			'paymentAdvance'  => $this->payment_advance ? 'true' : 'false',
			'notifyPayer'     => $this->notify_payer ? 'true' : 'false',
			'paymentTypes'    => 'CREDIT_CARD',
			'notificationUrl' => \add_query_arg( $notication_query_args, \home_url( '/' ) ),
		];

		// add cpf if it's a person or cpng if it's company
		$cpf_cnpj = null;

		if ( 1 === $person_type ) {
			$cpf_cnpj = $order->get_meta( '_billing_cpf' );
		} elseif ( 2 === $person_type ) {
			$cpf_cnpj = $order->get_meta( '_billing_cnpj' );
		} else {
			$cpf_cnpj = $order->get_meta( '_billing_cpf' );

			if ( empty ( $cpf_cnpj ) ) {
				$cpf_cnpj = $order->get_meta( '_billing_cnpj' );
			}
		}
		$params['payerCpfCnpj'] = preg_replace( '/\D/', '', $cpf_cnpj );

		// add card id or card hash
		if ( 'list' === $checkout_data['mode'] ) {
			$card_data = $this->get_card_data( $checkout_data['card_info'] );
			$stored_card = h\get_user_credit_card(
				$order->get_customer_id(),
				$card_data['brand'],
				$card_data['last_numbers']
			);
			$params['creditCardId'] = $stored_card['card_id'];
		} else {
			$params['creditCardHash'] = $checkout_data['card_hash'];
			if ( $this->customer_can_store_cards() && $checkout_data['save_card'] ) {
				$params['creditCardStore'] = 'true';
			} else {
				$params['creditCardStore'] = 'false';
			}
		}

		return \apply_filters( h\prefix( 'payment_credit_card_api_params' ), $params, $order );
	}

	public function checkout_scripts () {
		if ( $this->is_available() && 'direct' === $this->integration_method ) {

			$assets = h\assets();
			$deps = [ 'juno/direct-checkout' ];

			$assets->add_global_dependency( 'wc-credit-card-form' );

			$assets->add(
				'https://www.boletobancario.com/boletofacil/wro/direct-checkout.min.js',
				[
					'handle' => 'juno/direct-checkout'
				]
			);

			if ( $this->show_visual_card ) {
				$assets->add(
					h\get_asset_url( 'dist/js/lib/card.min-2.5.1.js' ),
					[
						'handle' => 'jessepollak/card',
						'deps' => [ 'jquery' ],
					]
				);
				$deps[] = 'jessepollak/card';
			}

			$user_id = \get_current_user_id();
			$ajax_remove_card = h\config_get( '$ajax_remove_user_credit_card' );

			$script_data = [
				'id' => $this->id,
				'user_id' => $user_id,
				'credit_cards' => $this->get_user_credit_cards( $user_id ),
				'sandbox' => $this->test_mode,
				'public_token' => $this->public_token,
				'show_visual_card' => $this->show_visual_card,
				'store_user_cards' => $this->customer_can_store_cards(),
				'ajax' => [
					'remove_user_credit_card' => [
						'action' => $ajax_remove_card->get_action_name(),
						'nonce' => [
							'param' => $ajax_remove_card->get_nonce_query_arg(),
							'value' => \wp_create_nonce(
								$ajax_remove_card->get_nonce_action()
							),
						]
					]
				],
				'ajax_url' => \admin_url( 'admin-ajax.php' ),
			];

			$assets->add(
				h\get_asset_url( 'dist/js/checkout-credit-card-2.1.3.js' ),
				[
					'handle' => 'wc_juno_checkout_credit_card',
					'deps' => $deps,
					'script_data' => $script_data
				]
			);

			$assets->add(
				h\get_asset_url( 'dist/css/lib/card.min.css' ),
				[
					'handle' => 'jessepollak/card-css',
				]
			);

			$assets->add(
				h\get_asset_url( 'dist/css/checkout-credit-card.css' ),
				[
					'deps' => [ 'jessepollak/card-css' ]
				]
			);
		}
	}

	protected function get_user_credit_cards ( $user_id ) {
		$result = [];
		if ( $user_id ) {
			$cards = h\get_user_credit_cards( $user_id );

			foreach ( $cards as $card ) {
				$result[] = [
					'brand' => $card['brand'],
					'last_numbers' => $card['last_numbers'],
					'value' => $card['brand'] . '--' . $card['last_numbers'],
					'version' => isset( $card['version'] ) ? $card['version'] : 1,
				];
			}
		}

		return $result;
	}

	public function get_card_data ( $card_info ) {
		$data = [];
		$parts = explode( '--', $card_info );

		$data['brand'] = trim( $parts[0] );
		$data['last_numbers'] = trim( $parts[1] );
		$data['cvc'] = trim( h\request_value( $this->id . '-cvc' ) );

		foreach ( $data as $value) {
			if ( empty( $value ) ) return false;
		}

		return $data;
	}

	protected function validate_installments ( $value ) {
		$max_installments = $this->get_max_installments();
		$value = intval( $value );

		if ( $max_installments < 0 ) {
			$max_installments = 1;
		} elseif ( $max_installments > 12 ) {
			$max_installments = 12;
		}

		if ( $value < 1 || $value > $max_installments ) {
			return false;
		}

		return true;
	}

	protected function get_card_id_from_response ( $res ) {
		return $res->data->charges[0]->payments[0]->creditCardId;
	}

	protected function get_order_status_from_response ( $res, $api_version = 1 ) {
		if ( 'direct' !== $this->integration_method ) {
			return false;
		}

		if ( 2 === $api_version ) {
			return $res->payments[0]->status;
		}

		return $res->data->charges[0]->payments[0]->status;
	}


	public function get_max_installments( $total = 0 ) {
		$total    = $total ? $total : $this->get_order_total();
		$max      = intval( $this->max_installments );
		$limit    = 1;
		$smallest = $this->get_smallest_installment();

		foreach ( range( 1, $max ) as $number ) {
			$value = $total / $number;

			if ( $smallest > $value ) {
				break;
			}

			$limit = $number;
		}

		return $limit;
	}


	/**
	 * Get the smallest installment amount.
	 *
	 * @return int
	 */
	public function get_smallest_installment() {
		return ( 5 > $this->smallest_installment ) ? 5 : wc_format_decimal( $this->smallest_installment, 2 );
	}


	protected function get_installments_options () {
		$max_installments = $this->get_max_installments();

		if ( $this->validate_installments( $max_installments ) && $max_installments > 1 ) {
			$total = $this->get_order_total();
			$options = [];
			$label_singular = __( '%s parcela de %s %s', 'woo-juno' );
			$label_plural = __( '%s parcelas de %s %s', 'woo-juno' );

			foreach ( \range(1, $max_installments ) as $i ) {
				$fees   = $this->get_option( 'installments_fee' );
				$fee    = ! empty( $fees[ $i ] ) ? wc_format_decimal( $fees[ $i ] ) : 0;

				$amount     = $total / $i;
				$final_cost = $this->get_final_cost( $amount, $i );
				$options[ $i ] = \wptexturize( \sprintf(
					$i == 1 ? $label_singular : $label_plural,
					$i,
					\strip_tags( \wc_price( $final_cost ) ),
					0 === $fee ? 'sem juros' : ''
				) );
			}

			return apply_filters( h\prefix( 'installments_list' ), $options, $this );
		}

		return false;
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
		$this->api   = new Juno_REST_API();
		$errors     = new \WP_Error();
		$result     = false;
		$order      = wc_get_order( $order_id );
		$data       = $order->get_meta( '_juno_v2_payment_response' );
		$payment_id = isset( $data->payments[0]->id ) ? $data->payments[0]->id : null;

		if ( 2 !== $this->api->version ) {
			$errors->add( 'juno_invalid_api_version', __( 'Reembolso disponível apenas na API 2.0. Atualize nas configurações.', 'woo-juno' ) );
			$result = $errors;
		} elseif ( '2' !== $order->get_meta('_juno_api_version') ) {
			$errors->add( 'juno_invalid_api_order_version', __( 'Reembolso disponível apenas para pedidos criados usando a integração com API 2.0. Para o pedido atual o reembolso deve ser feito diretamente no painel Juno.', 'woo-juno' ) );
			$result = $errors;
		} elseif ( null === $payment_id ) {
			$errors->add( 'juno_charge_id_missing', __( 'ID do pagamento não encontrado. O reembolso deve ser processado diretamente pelo painel da Juno.', 'woo-juno' ) );

			$result = $errors;
		} else {
			// juno_charge_code
			$params = array();
			if ( $amount ) {
				$params['amount'] = wc_format_decimal( $amount, 2 );
			}

			$params = \apply_filters( h\prefix( 'request_refund_params' ), $params, $order, $amount, $reason );
			$request = $this->api->request_refund( $payment_id, $params );

			if ( is_wp_error( $request ) ) {
				return $request;
			}

			$body = json_decode( $request['body'] );

			if ( 200 !== $request['response']['code'] ) {
				$message = isset( $body->details[0]->message ) ? $body->details[0]->message . '. ' . __( 'Acesse o painel Juno para mais detalhes', 'woo-juno' ) : __( 'Ocorreu um erro ao processar sua solicitação. Por favor, processe este reembolso diretamente pelo painel da Juno.', 'woo-juno' );

				return new \WP_Error( 'juno_invalid_response', \apply_filters( h\prefix( 'refund_fail_message' ), $message, $request ) );
			}

			$order->add_order_note( __( 'Reembolso processado com sucesso na Juno.', 'woo-juno' ) );

			return true;
		}

		return false;
	}


	public function get_final_cost( $total, $installments ) {
		$fees   = $this->get_option( 'installments_fee' );
		$fee    = isset( $fees[ $installments ] ) ? wc_format_decimal( $fees[ $installments ] ) : 0;

		if ( $fee > 0 ) {
			$total += $total * ( $fee / 100 );
		}

		return wc_format_decimal( $total, 2, true );
	}


	public function generate_installments_table_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$value     = $this->get_option( $key );
		$defaults  = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html( $data ); // WPCS: XSS ok. ?></label>
			</th>
			<td class="forminp">
        <fieldset>
          <?php
            for ( $i = 1; $i <= 12; $i++ ) :
              $interest = isset( $value[ $i ] ) ? $value[ $i ] : '';
          ?>
          <p data-installment="<?php echo $i; ?>">
            <input class="small-input" type="text" value="<?php echo $i; ?>"
              <?php disabled( 1, true ); ?> />
            <input class="small-input wc_input_price" type="text"
              placeholder="0,00 (valor em porcentagem, somente números)"
              name="<?php echo esc_attr( $field_key ); ?>[<?php echo $i; ?>]"
              id="<?php echo esc_attr( $field_key ); ?>_<?php echo $i; ?>" value="<?php echo wc_format_localized_price( $interest ) ?>" />
          </p>
          <?php endfor; ?>
        </fieldset>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}


	public function validate_installments_table_field( $key, $value ) {
		return $value;
	}

	/**
	 * Check if credit card can be stored
	 *
	 * @return bool
	 */
	public function customer_can_store_cards() {
		return $this->store_user_cards;
	}



  /**
   * Add credit card to customer account
   *
   * @param array $card_data
	 * @param array $checkout_data
   * @param WC_Order $order
   * @return void
   */
  public function save_customer_card( $card_data, $checkout_data, $order ) {
    $card_info = explode( '--', $checkout_data['card_info'] );

    $this->log( [
      'Saving credit card', $card_info[0],
      'with last numbers', $card_data['last4CardNumber']
    ] );

    // the same format as V1
    $card_details =  [
      'card_id'      => $card_data['creditCardId'],
      'brand'        => $card_info[0],
      'last_numbers' => $card_data['last4CardNumber'],
      'cvc'          => '999', // backwards compatibilty
      'version'      => 2
    ];

    h\add_user_credit_card( \get_current_user_id(), $card_details );
    $this->log( ['Credit Card STORED' ] );
  }


	/**
	 * Display order fee
	 *
	 * @param int $order_id WooCommerce Order ID
	 * @return void
	 */
	public function display_order_fee( $order_id ) {
		if ( apply_filters( h\prefix( 'display_order_fee' ), true, $order_id ) ) {
			$order = wc_get_order( $order_id );
			$fee   = $order->get_meta( '_juno_fee' );

			if ( ! $fee ) {
				return;
			}
			?>

			<tr>
				<td class="label juno-fee">
					<?php echo wc_help_tip( __( 'Taxa cobrada pela Juno', 'woo-juno' ) ); // wpcs: xss ok. ?>
					<?php esc_html_e( 'Taxa da Juno:', 'woo-juno' ); ?>
				</td>
				<td width="1%"></td>
				<td class="total">
					<?php echo wc_price( $fee * -1 ); // wpcs: xss ok. ?>
				</td>
			</tr>

			<?php
		}
	}
}
