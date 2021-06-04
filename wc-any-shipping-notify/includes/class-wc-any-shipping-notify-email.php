<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Shipping Tracking code email.
 */
class WC_Any_Shipping_Notify_Email extends WC_Email {

  /**
   * Initialize tracking template.
   */
  public function __construct() {
    $this->id               = 'wc_any_shipping_notify';
    $this->title            = __( 'Código de rastreamento para transportadoras', 'wc-any-shipping-notify' );
    $this->customer_email   = true;
    $this->description      = __( 'Este e-mail é enviado quando configurado um código de rastreamento dentro de um pedido.', 'wc-any-shipping-notify' );
    $this->heading          = __( 'O seu pedido foi enviado', 'wc-any-shipping-notify' );
    $this->subject          = __( '[{site_title}] Seu pedido {order_number} foi enviado pela transportadora {shipping_company}', 'wc-any-shipping-notify' );
    $this->message          = __( 'Olá. Seu pedido recente em {site_title} foi enviado pela transportadora {shipping_company}.', 'wc-any-shipping-notify' )
                  . PHP_EOL . ' ' . PHP_EOL
                  . __( 'Para acompanhar a entrega, use o seguinte código(s) de rastreamento: {tracking_code}', 'wc-any-shipping-notify' )
                  . PHP_EOL . ' ' . PHP_EOL
                  . __( 'O rastreamento pode ser feito na seguinte URL: {tracking_url}', 'wc-any-shipping-notify' )
                  . PHP_EOL . ' ' . PHP_EOL
                  . __( 'O serviço de entrega é de responsabilidade de {shipping_company}, mas se você tiver alguma dúvida, entre em contato conosco.', 'wc-any-shipping-notify' );
    $this->tracking_message = $this->get_option( 'tracking_message', $this->message );
    $this->template_html    = 'emails/tracking-code.php';

    // Call parent constructor.
    parent::__construct();

    $this->template_base = WC_Any_Shipping_Notify::get_templates_path();
  }

  /**
   * Initialise settings form fields.
   */
  public function init_form_fields() {
    $this->form_fields = array(
      'enabled' => array(
        'title'   => __( 'Enable/Disable', 'wc-any-shipping-notify' ),
        'type'    => 'checkbox',
        'label'   => __( 'Habilitar este e-mail de notificação', 'wc-any-shipping-notify' ),
        'default' => 'yes',
      ),
      'subject' => array(
        'title'       => __( 'Assunto', 'wc-any-shipping-notify' ),
        'type'        => 'text',
        'description' => sprintf( __( 'Isto controla a linha de assunto do e-mail. Deixe em branco para utilizar o assunto padrão: <code>%s</code>.', 'wc-any-shipping-notify' ), $this->subject ),
        'placeholder' => $this->subject,
        'default'     => '',
        'desc_tip'    => true,
      ),
      'heading' => array(
        'title'       => __( 'Cabeçalho do e-mail', 'wc-any-shipping-notify' ),
        'type'        => 'text',
        'description' => sprintf( __( 'Isto controla o cabeçalho do conteúdo dentro do e-mail de notificação. Deixe em branco para utilizar o cabeçalho padrão: <code>%s</code>.', 'wc-any-shipping-notify' ), $this->heading ),
        'placeholder' => $this->heading,
        'default'     => '',
        'desc_tip'    => true,
      ),
      'tracking_message' => array(
        'title'       => __( 'Email Content', 'wc-any-shipping-notify' ),
        'type'        => 'textarea',
        'description' => sprintf( __( 'This controls the initial content of the email. Leave blank to use the default content: <code>%s</code>.', 'wc-any-shipping-notify' ), $this->message ),
        'placeholder' => $this->message,
        'default'     => '',
        'desc_tip'    => true,
      ),
      'email_type' => array(
        'title'       => __( 'Email type', 'wc-any-shipping-notify' ),
        'type'        => 'select',
        'description' => __( 'Choose which format of email to send.', 'wc-any-shipping-notify' ),
        'default'     => 'html',
        'class'       => 'email_type wc-enhanced-select',
        'options'     => $this->get_custom_email_type_options(),
        'desc_tip'    => true,
      ),
    );
  }

  /**
   * Email type options.
   *
   * @return array
   */
  protected function get_custom_email_type_options() {
    $types['html'] = __( 'HTML', 'wc-any-shipping-notify' );

    return $types;
  }

  /**
   * Get email tracking message.
   *
   * @return string
   */
  public function get_tracking_message() {
    return apply_filters( 'wc_any_shipping_notify_email_tracking_message', $this->format_string( $this->tracking_message ), $this->object, $this->tracking_code, $this->company_name );
  }

  /**
   * Get tracking codes HTML.
   *
   * @param  array $tracking_codes Tracking codes.
   *
   * @return string
   */
  public function get_tracking_codes( $tracking_codes ) {
    if ( empty( $tracking_codes ) ) {
      $tracking_codes = wc_any_shipping_get_tracking_codes( $order );
    }

    if ( ! is_array( $tracking_codes ) ) {
      return apply_filters( 'wc_any_shipping_get_tracking_codes_email', $tracking_codes, $this->company_name );
    }

    $html = '<ul>';

    foreach ( $tracking_codes as $tracking_code => $company_slug ) {
      $shipping_company_name = wc_any_shipping_notify_get_shipping_company_name( $company_slug );

      $html .= '<li>' . $tracking_code . '</li>';
    }

    $html .= '</ul>';

    return apply_filters( 'wc_any_shipping_get_tracking_codes_email_html', $html, $tracking_codes );
  }

  /**
   * Trigger email.
   *
   * @param  int      $order_id      Order ID.
   * @param  WC_Order $order         Order data.
   * @param  string   $tracking_code Tracking code.
   */
  public function trigger( $order_id, $order = false, $tracking_code = '', $shipping_company_slug = '' ) {
    // Get the order object while resending emails.
    if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
      $order = wc_get_order( $order_id );
    }

    $this->shipping_company_slug = $shipping_company_slug;
    $this->company_name          = wc_any_shipping_notify_get_shipping_company_name( $this->shipping_company_slug );

    $this->tracking_code         = $tracking_code;

    if ( is_object( $order ) ) {
      $this->object = $order;

      if ( method_exists( $order, 'get_billing_email' ) ) {
        $this->recipient = $order->get_billing_email();
      } else {
        $this->recipient = $order->billing_email;
      }

      $this->find['order_number']    = '{order_number}';
      $this->replace['order_number'] = $order->get_order_number();

      $this->find['order_date']    = '{date}';
      $this->replace['order_date'] = date_i18n( wc_date_format(), time() );

      $this->find['shipping_company']    = '{shipping_company}';
      $this->replace['shipping_company'] = $this->company_name;

      $this->find['codes']    = '{tracking_code}';
      $this->replace['codes'] = $this->get_tracking_codes( $this->tracking_code );


      $this->find['tracking_url']    = '{tracking_url}';
      $this->replace['tracking_url'] = wc_any_shipping_notify_get_shipping_company_url( $shipping_company_slug, $tracking_code, $this->object );
    }

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
      'tracking_message' => $this->get_tracking_message(),
      'sent_to_admin'    => false,
      'plain_text'       => false,
      'email'            => $this,
    ), '', $this->template_base );

    return ob_get_clean();
  }

}

return new WC_Any_Shipping_Notify_Email();
