<?php

namespace WC_Juno\Emails;

use WC_Email;
use WC_Juno\functions as h;

class New_Bank_Slip extends WC_Email {

	/**
	 * Initialize tracking template.
	 */
	public function __construct() {
		$this->id               = 'juno_new_bank_slip';
		$this->title            = __( 'Novo Boleto Juno', 'woo-juno' );
		$this->customer_email   = true;
		$this->description      = __( 'E-mail enviado ao gerar um novo boleto para um pedido.', 'woo-juno' );
		$this->heading          = __( 'Garanta seus produtos', 'woo-juno' );
		$this->subject          = __( '[{site_title}] O boleto do pedido #{order_number} ainda está disponível', 'woo-juno' );
		$this->message          = __( 'Olá, foi gerado um novo boleto para seu pedido em {site_title}.', 'woo-juno' )
									. PHP_EOL . ' ' . PHP_EOL
									. __( 'Para imprimir ou pagar via internet banking, basta clicar no botão abaixo.', 'woo-juno' );
		$this->email_message    = $this->get_option( 'email_message', $this->message );
		$this->template_html    = 'emails/new-bank-slip.php';

		// Call parent constructor.
		parent::__construct();
		$this->template_base = h\config_get( 'ROOT_DIR' ) . '/templates/';
		$this->email_type    = 'html';
	}

	/**
	 * Initialise settings form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Ativar/Desativar', 'woo-juno' ),
				'type'    => 'checkbox',
				'label'   => __( 'Habilitar este e-mail de notificação', 'woo-juno' ),
				'default' => 'yes',
			),
			'subject' => array(
				'title'       => __( 'Assunto', 'woo-juno' ),
				'type'        => 'text',
				'description' => sprintf( __( 'Assunto do e-mail', 'woo-juno' ), $this->subject ),
				'placeholder' => $this->subject,
				'default'     => '',
				'desc_tip'    => true,
			),
			'heading' => array(
				'title'       => __( 'Cabeçalho', 'woo-juno' ),
				'type'        => 'text',
				'description' => sprintf( __( 'Cabeçalho exibido no e-mail.', 'woo-juno' ), $this->heading ),
				'placeholder' => $this->heading,
				'default'     => '',
				'desc_tip'    => true,
			),
			'email_message' => array(
				'title'       => __( 'Mensagem', 'woo-juno' ),
				'type'        => 'textarea',
				'description' => sprintf( __( 'Mensagem padrão do e-mail. Utilize {button} para exibir o botão de pagamento (exibido automaticamente 1 vez).', 'woo-juno' ), $this->message ),
				'placeholder' => $this->message,
				'default'     => '',
				'desc_tip'    => true,
			),
		);
	}

	/**
	 * Get email tracking message.
	 *
	 * @return string
	 */
	public function get_email_message() {
		return $this->format_string( $this->email_message );
	}

	/**
	 * Trigger email.
	 *
	 * @param  int      $order_id      Order ID.
	 * @param  WC_Order $order         Order data.
	 * @param  string   $tracking_code Tracking code.
	 */
	public function trigger( $order ) {
		// Get the order object while resending emails.
		if ( ! is_a( $order, 'WC_Order' ) ) {
			return;
		}

		// refresh from DB
		$order = wc_get_order( $order->get_id() );

		$this->object    = $order;
		$this->recipient = $order->get_billing_email();

		$this->placeholders['{order_number}']  = $this->object->get_order_number();
		$this->placeholders['{button}']        = $this->get_payment_button();

		if ( ! $this->get_recipient() ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * Get content HTML.
	 *
	 * @return string
	 */
	public function get_content_html() {
		ob_start();

		wc_get_template( $this->template_html, array(
			'order'            => $this->object,
			'email_heading'    => $this->get_heading(),
			'email_message'    => $this->get_email_message(),
			'display_details'  => \apply_filters(
				h\prefix( 'email_display_order_details' ), true ),
			'sent_to_admin'    => false,
			'plain_text'       => false,
			'email'            => $this,
		), '', $this->template_base );

		return ob_get_clean();
	}


	private function get_payment_button(){
		$response = $this->object->get_meta( '_juno_payment_response' );
		$data     = [];

		ob_start();

		if ( $response->charges[0]->installmentLink != '' ) {
			if ( count( $response->charges ) == 1 ) {
				$link = $response->charges[0]->installmentLink;
				$data['url']   = esc_url( $link );
				$data['label'] = esc_html__( 'Imprimir Boleto', 'woo-juno' );
			}
			elseif ( count( $response->charges ) > 1 ) {
				$link = $response->charges[0]->link;
				$data['url']   = esc_url( $link );
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

		return ob_get_clean();
	}
}
