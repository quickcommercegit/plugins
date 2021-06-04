<?php

namespace WC_Juno;

use WC_Juno\Common\Hooker_Trait;
use WC_Juno\Common\WC_Logger_Trait;
use WC_Juno\functions as h;

class Notification_Handler_Api_V2 {
  use Hooker_Trait;
  use WC_Logger_Trait;

  public function __construct() {
    $this->set_wc_logger_source( 'woo-juno-notifications-log' );
  }

  public function add_hooks () {
    add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    add_action( 'woocommerce_api_woo_juno_notifications', array( $this, 'validate_request' ) );
  }

  public function admin_notices() {
    $alert = get_option( h\prefix( 'notification_api_v2' ) );

    if ( $alert && is_string( $alert ) && '1' !== $alert ) {
      h\include_php_template( 'admin-notice.php', [
        'message' => $alert,
        'class'   => 'error juno-urgent'
      ] );
    }
  }


  public function create_notifications() {
    $juno_api = new Service\Juno_REST_API();

    if ( 2 !== $juno_api->version ) {
      return null;
    }

    $juno_api->delete_all_webhooks();

    $request  = $juno_api->create_webhook();

    if ( \is_wp_error( $request ) ) {
      $this->log( 'Erro ao criar notificação: '  .  $request->get_error_message() );

      return 'Não foi possível criar webhook. Tente novamente.';
    } elseif ( 401 === $request['response']['code'] ) {
      $this->log( 'Credenciais inválidas para criar notificações.' );

      return 'Erro ao tentar criar notificações da Juno: credenciais inválidas. <br />Volte à tela de configurações e revise seus dados para receber pagamentos da Juno.';
    } elseif ( 200 !== $request['response']['code'] ) {
      $body    = json_decode( $request['body'] );
      $message = isset( $body->details[0]->message ) ? $body->details[0]->message : 'Tente novamente';

      $this->log( 'Erro ao criar notificações: ' . print_r( $body, true ) );

      return 'Erro ao tentar criar notificações da Juno: ' . $message . '. Enquanto isso os pedidos pagos não serão atualizados automaticamente.<br />Volte à tela de configurações no menu de Integrações e clique em "Salvar" para tentar novamente. Se o problema persistir, entre em contato para obter assistência.';
    }

    $body = json_decode( $request['body'] );

    return $body;
  }


  public function validate_request() {
    if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
      \status_header( 400 );
      echo __( 'Requisição inválida.', 'woo-juno' );
      exit;
    }

    $notification = file_get_contents( 'php://input' );

    // $this->check_signature( $notification );

    $notification = json_decode( $notification );

    if ( $secret && '' ) {
      \status_header( 400 );
      echo __( 'Webhook inválido!', 'woo-juno' );
      exit;
    }

    if ( ! isset( $notification->eventType ) || 'PAYMENT_NOTIFICATION' !== $notification->eventType ) {
      \status_header( 400 );
      echo __( 'Evento inválido.', 'woo-juno' );
      exit;
    }

    $charge_id = isset( $notification->data[0]->attributes->charge->id ) ? $notification->data[0]->attributes->charge->id : false;

    if ( ! $charge_id ) {
      \status_header( 400 );
      echo __( 'Cobrança não encontrada.', 'woo-juno' );
      exit;
    }

    if ( 'yes' === h\get_settings_option( 'notifications_log', 'no' ) ) {
      $this->set_wc_logger_source( h\config_get( 'SLUG' ) . '-api-notifications-v2' );
    }

    $juno_api = new Service\Juno_REST_API();

    if ( 2 !== $juno_api->version ) {
      \status_header( 400 );
      echo __( 'A versão atual da API é 1. Operação cancelada.', 'woo-juno' );
      exit;
    }

    $this->log( [ 'Receiving notification from Juno:', $notification ] );

    $this->handle_request( $notification->data );
    \status_header( 200 );
    die();
  }

  protected function handle_request ( $data ) {
    $status       = $data[0]->attributes->status;
    $reference    = $data[0]->attributes->charge->reference;
    $payment_code = $data[0]->attributes->charge->code;
    $order        = null;
    $payment_type = $data[0]->attributes->type;

    try {
      if ( h\str_starts_with( $reference, 'wc-order-' ) ) {
        $order_id = intval( h\str_after( $reference, 'wc-order-' ) );
        $order = \wc_get_order( $order_id );

        $this->log( [ 'Order ID:', $order_id ] );

        if ( ! in_array( $payment_type, [ 'BOLETO', 'CREDIT_CARD', 'PIX_DYNAMIC_QRCODE' ] ) ) {
          throw new \Exception(
            __( 'Tipo de pagamento inválido', 'woo-juno' ) . ": $payment_type"
          );
        }

        if ( $order ) {
          $order_id   = $order->get_id();
          $meta_key   = '_' . h\prefix( 'api_status' );
          $old_status = $order->get_meta( $meta_key );

          if ( ! $this->is_status_changing_permitted( $status, $old_status ) ) {
            throw new \Exception(
              __(
                sprintf( "Mudança de status não permitida no pedido #%s: de '%s' para '%s'", $order_id, $old_status, $status
                ),
                'woo-juno'
              )
            );
            return;
          }

          $order->update_meta_data( $meta_key, $status );

          // update order status
          if ( 'BOLETO' === $payment_type ) {
            Gateway\Bank_Slip::set_order_status( $order, $status );
          } elseif ( 'PIX_DYNAMIC_QRCODE' === $payment_type ) {
            Gateway\Pix::set_order_status( $order, $status );

            $order->update_meta_data( '_juno_pix', $data[0]->attributes->pix );
            $order->update_meta_data( '_juno_pix_txid', $data[0]->attributes->pix->txid );
            $order->update_meta_data( '_juno_pix_endToEndId', $data[0]->attributes->pix->endToEndId );
            $order->save();

          } else {
            Gateway\Credit_Card::set_order_status( $order, $status );
          }

          do_action( h\prefix( 'notification_processed' ), $order, $data );

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

  public function is_status_changing_permitted( $new_status, $old_status = '' ) {
    $capabilities = array(
      'DECLINED'           => array( 'FAILED', 'CONFIRMED' ),
      'FAILED'             => array( 'DECLINED', 'NOT_AUTHORIZED', 'CONFIRMED' ),
      'NOT_AUTHORIZED'     => array( 'FAILED', 'CONFIRMED', 'DECLINED' ),
      'CONFIRMED'          => array( 'CUSTOMER_PAID_BACK', 'BANK_PAID_BACK', 'PARTIALLY_REFUNDED' ),
      'CUSTOMER_PAID_BACK' => array(),
      'BANK_PAID_BACK'     => array(),
      'PARTIALLY_REFUNDED' => array(),
    );

    return empty( $old_status ) || $new_status !== $old_status && in_array( $new_status, $capabilities[ $old_status ] );
  }



  private function check_signature( $body ) {
    $secret = get_option( h\prefix( 'notification_api_v2_secret' ) );

    // secret not set yet
    if ( ! $secret ) {
      return true;
    }

    $signature = $this->get_signature_header();

    if ( ! $signature ) {
      if ( 'yes' === h\get_settings_option( 'notifications_log', 'no' ) ) {
        $this->set_wc_logger_source( h\config_get( 'SLUG' ) . '-api-notifications-v2' );
      }

      $this->log( 'ASSINATURA AUSENTE NO WEBHOOK: ' . print_r( $_SERVER, true ) );

      \status_header( 400 );
      echo __( 'Assinatura ausente.', 'woo-juno' );
      exit;
    }

    if ( ! hash_equals( hash_hmac( 'sha256', $secret, $body ), $signature ) ) {
      if ( 'yes' === h\get_settings_option( 'notifications_log', 'no' ) ) {
        $this->set_wc_logger_source( h\config_get( 'SLUG' ) . '-api-notifications-v2' );
      }

      $this->log( 'ASSINATURA WEBHOOK INVÁLIDA. Assinatura: ' . print_r( $signature, true ) . '. Secret: ' . $secret . '. Body: ' . $body );

      \status_header( 400 );
      echo __( 'Assinatura inválida.', 'woo-juno' );
      exit;
    }

    return true;
  }


	private function get_signature_header() {
		if ( ! empty( $_SERVER['HTTP_X_SIGNATURE'] ) ) {
			// return wp_unslash( $_SERVER['HTTP_X_SIGNATURE'] ); // WPCS: sanitization ok.
		}

		if ( function_exists( 'getallheaders' ) ) {
			$headers = getallheaders();
			// Check for the authoization header case-insensitively.
			foreach ( $headers as $key => $value ) {
				if ( 'x-signature' === strtolower( $key ) ) {
					return $value;
				}
			}
		}

		return '';
	}
}
