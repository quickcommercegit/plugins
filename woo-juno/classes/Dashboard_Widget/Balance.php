<?php

namespace WC_Juno\Dashboard_Widget;

use WC_Juno\Common\Hooker_Trait;
use WC_Juno\Common\WC_Logger_Trait;
use WC_Juno\functions as h;
use WC_Juno\Service\Juno_REST_API;

class Balance {
	use Hooker_Trait;
	use WC_Logger_Trait;

	public function __construct () {
		$this->token = h\get_private_token();
		$this->set_wc_logger_source( 'juno-balance-widget' );
	}

	public function add_hooks () {
		$widget_enabled = 'yes' === h\get_settings_option( 'widget_balance_enabled', 'yes' ) && ! empty( $this->token );

		if ( ! $widget_enabled ) return;
		if ( h\user_is_admin() || h\user_has_role( 'shop_manager' ) ) {
			$this->add_action( 'wp_dashboard_setup', 'register_widget' );
			$this->add_action( 'current_screen', 'add_assets' );
		}
	}

	public function register_widget () {
		wp_add_dashboard_widget(
			$this->get_id(),
			$this->get_title(),
			[ $this, 'callback' ]
		);
	}

	public function get_id () {
		return h\prefix( 'juno_balance_widget' );
	}

	public function get_title () {
		return esc_html__( 'Saldo no Juno', 'woo-juno' );
	}

	public function callback () {
		// do nothing
		// the template is rendered in frontend
		// see: assets/src/js/components/widget-balance.svelte
	}

	protected function get_balance () {
		$cache_key = h\prefix( 'cache_balance' );
		$cache_expiry = \HOUR_IN_SECONDS * 6;
		$balance = h\remember_cache( $cache_key, function () {
			$data = [];
			$juno_api = new Juno_REST_API();

			try {
				$request = $juno_api->fetch_balance();
				$data['request'] = $request;

				if ( \is_wp_error( $request ) ) {
					throw new \Exception( '' );
				} else {
					$response = \json_decode( $request['body'] );

					// v1
					if ( isset( $response->success, $response->data ) && true === $response->success ) {
						$data = (array) $response->data;
					} elseif ( isset( $response->balance ) ) { // v2
						$data = (array) $response;
					} else {
						throw new \Exception( '' );
					}
				}
			} catch ( \Exception $e ) {
				$data['error'] = 'Não foi possível solicitar o seu saldo. Para mais detalhes, verifique os logs.';
				$this->log( 'Erro ao solicitar o saldo.' );
				$this->log( $data['request'] );
				unset( $data['request'] );
			}

			return $data;
		}, $cache_expiry);

		// don't cache errors
		if ( isset( $data['error'] ) ) {
			h\forget_cache( $cache_key );
		}

		return $balance;
	}

	public function add_assets () {
		$screen = \get_current_screen();

		if ( $screen && 'dashboard' == $screen->base ) {
			$assets = h\assets();
			$data = [];
			$ajax_transfer_balance = h\config_get( '$ajax_transfer_balance' );

			$data['id'] = $this->get_id();
			$data['ajax'] = [
				'action' => $ajax_transfer_balance->get_action_name(),
				'nonce' => \wp_create_nonce( $ajax_transfer_balance->get_nonce_action() ),
				'nonce_arg' => $ajax_transfer_balance->get_nonce_query_arg(),
			];
			$data['balance'] = $this->get_balance();

			$assets->add(
				h\get_asset_url( 'dist/js/dashboard-balance-widget.js' ),
				[
					'handle' => 'juno_balance_widget',
					'deps' => [ 'jquery' ],
					'in_admin' => true,
					'script_data' => $data
				]
			);
		}
	}
}
