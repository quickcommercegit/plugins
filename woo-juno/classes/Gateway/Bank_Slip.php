<?php

namespace WC_Juno\Gateway;

use WC_Juno\Service\Juno_REST_API;
use WC_Juno\Notification_Handler;
use WC_Juno\functions as h;

class Bank_Slip extends WC_Juno_Gateway {
	public function __construct () {
		$this->id                 = 'juno-bank-slip';
		$this->payment_type       = 'BOLETO';
		$this->method_title       = __( 'Juno - Boleto Bancário', 'woo-juno' );
		$this->method_description = __( 'Comece a receber dinheiro via boleto bancário usando Juno.', 'woo-juno' );

		parent::__construct();
	}

	protected function setup() {
		$this->instructions       = $this->get_option( 'instructions', '' );
		$this->max_installments   = $this->get_option( 'max_installments', 1 );
		$this->due_days           = $this->get_option( 'due_days', 3 );
		$this->max_overdue_days   = $this->get_option( 'max_overdue_days', 0 );
		$this->fine               = $this->get_option( 'fine', 0 );
		$this->interest           = $this->get_option( 'interest', 0 );
		$this->notify_payer       = 'yes' === h\get_settings_option( 'notify_payer' );
	}

	public function add_hooks() {
		parent::add_hooks();

		$this->add_action( 'woocommerce_thankyou_' . $this->id, 'thankyou_page' );

		// add buttons to print billet in my account page
		$this->add_filter( 'woocommerce_my_account_my_orders_actions', 'my_account_print_buttons', 10, 2 );

		// add buttons to print billet in my account page
		$this->add_filter( 'woocommerce_email_order_details', 'email_print_buttons', 1, 2 );
	}

	public function payment_fields () {
		h\include_wc_template(
			'checkout/juno/bank-slip/checkout-form.php',
			[
				'id'            => $this->id,
				'description'   => wpautop( wptexturize( $this->get_description() ) ),
				'installments'  => $this->get_installments_options()
			]
		);
	}

	public function thankyou_page ( $order_id ) {
		$order = \wc_get_order( $order_id );
		$response = $order->get_meta( '_juno_payment_response' );

		$boleto_link = $response->charges[0]->installmentLink;
		$carne_link  = $response->charges[0]->link;
		$due_date    = $response->charges[0]->dueDate;
		$has_installments = count( $response->charges ) > 1;
		$pay_number  = $response->charges[0]->payNumber;
		$api_version = intval( $order->get_meta( '_juno_api_version' ) );

		if ( 2 === $api_version ) {
			$due_date = new \DateTime( $due_date );
			$due_date = $due_date->format( 'd/m/Y' );
		}

		h\include_wc_template(
			'checkout/juno/bank-slip/thankyou.php',
			[
				'id'               => $this->id,
				'instructions'     => wpautop( wptexturize( $this->instructions ) ),
				'barcode'          => $response->charges[0]->billetDetails->barcodeNumber,
				'pay_number'	   => $pay_number,
				'first_billet'     => $boleto_link,
				'all_billets'      => $carne_link,
				'due_date'         => $due_date,
				'has_installments' => $has_installments,
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
			$response = $order->get_meta( '_juno_payment_response' );
			$data = [];
			if ( $response->charges[0]->installmentLink != '' ) {
				if ( count( $response->charges ) == 1 ) {
					$link = $response->charges[0]->installmentLink;
					$data['url'] = esc_url( $link );
					$data['label'] = esc_html__( 'Imprimir Boleto', 'woo-juno' );
				}
				elseif ( count( $response->charges ) > 1 ) {
					$link = $response->charges[0]->link;
					$data['url'] = esc_url( $link );
					$data['label'] = esc_html__( 'Imprimir Carnê', 'woo-juno' );
				}

				if ( ! empty( $data ) ) {
					$data['color'] = \apply_filters(
						h\prefix( 'email_print_button_color' ),
						\get_option( 'woocommerce_email_base_color', '#3EA901' )
					);
					h\include_wc_template( 'emails/juno/print-billet-button.php', $data );
				}
			}
		}
	}


	/**
	 * my_account_print_buttons
	 *
	 * @param mixed $actions
	 * @param mixed $order
	 * @return void
	 */
	public function my_account_print_buttons( $actions, $order ) {
		if ( $this->id !== $order->get_payment_method() ) {
			return $actions;
		}

		$response = $order->get_meta( '_juno_payment_response' );

		if ( $response->charges[0]->installmentLink != '' ) {
			if ( count( $response->charges ) == 1 ) {
				$link = $response->charges[0]->installmentLink;
				$actions[ $this->id . '-print' ] = array(
					'url'  => \esc_url( $link ),
					'name' => __( 'Imprimir boleto', 'woo-juno' ),
				);
			}
			elseif ( count( $response->charges ) > 1 ) {
				$link = $response->charges[0]->link;
				$actions[ $this->id . '-print' ] = array(
					'url'  => \esc_url( $link ),
					'name' => __( 'Imprimir carnê', 'woo-juno' ),
				);
			}
		}

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
				'installments' => h\value( h\request_value( $prefix . 'installments'), 1 ),
				'person_type' => h\value( h\request_value( 'billing_persontype' ), 0 ),
			];

			// check installments
			h\throw_if(
				! $this->validate_installments( $this->checkout_data['installments'] ), null,
				__( 'Número de parcelas inválido.', 'woo-juno' )
			);

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

			foreach ( $data->charges as $charge ) {
				// 2.0
				if ( isset( $charge->id ) ) {
					$order->update_meta_data( 'juno_charge_id', $charge->id );
				}

				$order->update_meta_data( 'juno_charge_code', $charge->code );
				$order->update_meta_data( 'juno_billet_url', $charge->link );
				$order->update_meta_data( 'juno_billet_barcode', $charge->billetDetails->barcodeNumber );

				$due_date = $charge->dueDate;

				if ( 2 === $this->api->version ) {
					$due_date = new \DateTime( $due_date );
					$due_date = $due_date->format( 'd/m/Y' );
				}

				$order->update_meta_data( 'billet_due_date', $due_date );

				$order->add_order_note( '<strong>Vencimento ' . $due_date . '</strong><br /><a target="_blank" href="' . $charge->link . '">Ver boleto</a><br /><code>' . $charge->billetDetails->barcodeNumber . '</code>' );
			}

			// save the juno api response
			$order->update_meta_data( '_juno_payment_response', $data );
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

			$order->update_status( 'failed', 'Falha no pagamento com boleto: ' . $error );
		}

		$error .= __( ' Caso este erro continue acontecendo, nos contate para assistência.', 'woo-juno' );

		// backend requests
		h\throw_if(
			! $is_frontend, null,
			$error
		);

		\wc_add_notice( $error, 'error' );
	}

	protected function get_payment_data( $order, $checkout_data ) {
		$person_type = intval( $order->get_meta( '_billing_persontype' ) );
		$installments = intval( $checkout_data['installments'] );
		$installments = 0 >= $installments ? 1 : $installments;
		$amount = $order->get_total() / $installments;
		$notication_query_args = [
			'log' => self::$log_enabled ? '1' : '0',
			'sandbox' => $this->test_mode ? '1' : '0',
		];
		$notication_query_args[ Notification_Handler::get_query_arg() ] = '';

		$params = [
			'amount'      => number_format( $amount, 2, '.', '' ),
			'reference'   => 'wc-order-' . $order->get_id(),
			'description' => sprintf( 'Pedido %s do %s (com Boleto)', $order->get_id(), \home_url( '/' ) ),

			'dueDate'       => \date( 'd/m/Y', \strtotime( '+' . intval( $this->due_days ) . ' days' ) ),
			'installments'   => $installments,
			'maxOverdueDays' => intval( $this->max_overdue_days ),
			'fine'           => intval( $this->fine ),
			'interest'       => intval( $this->interest ),

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
			'notifyPayer'     => $this->notify_payer ? 'true' : 'false',
			'paymentTypes'    => 'BOLETO',
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

		return \apply_filters( h\prefix( 'payment_bank_slip_api_params' ), $params, $order );
	}

	public function checkout_scripts () {
		if ( is_wc_endpoint_url( 'order-received' ) ) {
			$assets = h\assets();

			$assets->add(
				\home_url( '/' . WPINC . '/js/clipboard.min.js' ),
				[
					'handle' => 'clipboard.js'
				]
			);

			$assets->add(
				h\get_asset_url( 'dist/js/checkout-thankyou-bank-slip.js' ),
				[
					'deps' => [ 'jquery', 'clipboard.js' ]
				]
			);
		}
	}

	protected function validate_installments ( $value ) {
		$max_installments = intval( $this->max_installments );
		$value = intval( $value );

		if ( $max_installments < 0 ) {
			$max_installments = 1;
		} elseif ( $max_installments > 24 ) {
			$max_installments = 24;
		}

		if ( $value < 1 || $value > $max_installments ) {
			return false;
		}

		return true;
	}

	protected function get_installments_options () {
		$max_installments = intval( $this->max_installments );
		if ( $this->validate_installments( $max_installments ) && $max_installments > 1 ) {
			$total = $this->get_order_total();
			$options = [];
			$label_singular = __( '%s parcela de %s', 'woo-juno' );
			$label_plural = __( '%s parcelas de %s', 'woo-juno' );

			foreach ( \range(1, $max_installments ) as $i ) {
				$amount = $total / $i;
				$options[ $i ] = \wptexturize( \sprintf(
					$i == 1 ? $label_singular : $label_plural,
					$i,
					\strip_tags( \wc_price( $amount ) )
				) );
			}

			return $options;
		}
		return false;
	}

	protected function get_order_status_from_response ( $res ) {
		return $res->data->charges[0]->payments[0]->status;
	}

	/**
	 * API V2: get due date - weekdays only
	 *
	 * @return string
	 */
	public function get_due_date() {
		$due_date = \strtotime( '+' . intval( $this->due_days ) . ' days' );

		switch ( date( 'w', $due_date ) ) {
			case '6':
				$additional_days = 2;
				break;

			case '0':
				$additional_days = 1;
				break;

			default:
			$additional_days = 0;
				break;
		}

		return \date( 'Y-m-d', strtotime( '+' . intval( $this->due_days + $additional_days ) . ' days' ) );
	}

	// we don't support bank slip fees for now :)
	public function get_final_cost( $total, $installments ) {
		return $total;
	}
}
