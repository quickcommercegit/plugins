<?php

namespace WC_Juno\Settings;
use WC_Juno\Notification_Handler_Api_V2;
use WC_Juno\Service\Juno_REST_API;
use WC_Juno\functions as h;

class General extends \WC_Integration {
	public function __construct() {
		// Setup general properties
		$this->setup();

		// Load the settings
		$this->init_form_fields();
		$this->init_settings();

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->api_key          = $this->get_option( 'api_key' );
		$this->debug            = $this->get_option( 'debug' );

		// Actions.
		add_action(
			'woocommerce_update_options_integration_' .  $this->id,
			[ $this, 'process_admin_options' ]
		);

		add_action(
			'woocommerce_update_options_integration_' .  $this->id,
			[ $this, 'create_v2_notifications' ],
			20
		);

		// Add settings link to plugins page
		add_filter(
			'plugin_action_links_' . \plugin_basename( h\config_get( 'MAIN_FILE' ) ),
			[ $this, 'add_settings_link' ]
		);
	}

	public function add_settings_link ( $links ) {
		$page_id = $this->id;
		$url = admin_url( "/admin.php?page=wc-settings&tab=integration&section=$page_id" );
		$label = esc_html__( 'Configurações', 'woo-juno' );
		$link = "<a href='$url'>$label</a>";
		\array_unshift( $links, $link );
  		return $links;
	}

	protected function setup () {
		$this->id                 = h\get_juno_integration_id();
		$this->method_title       = __( 'Juno', 'woo-juno' );
		$this->method_description = __( 'Configure seus tokens para poder usar os meios de pagamento da Juno.', 'woo-juno' );
	}

	public function init_form_fields () {
		$this->form_fields = require 'inc/settings_general.php';
		$this->form_fields = \apply_filters( h\prefix( 'general_settings' ), $this->form_fields, $this );
	}

	public function admin_options() {
		parent::admin_options();
		?>
		<script>
			// simple script to toogle the token fields
			window.jQuery(function ($) {
				var id = '<?= esc_js( $this->id ); ?>';
				$api_version       = $('#woocommerce_' + id + '_api_version');
				$test_mode         = $('#woocommerce_' + id + '_test_mode');
				$token_production  = $('#woocommerce_' + id + '_token').parents('tr');
				$token_sandbox     = $('#woocommerce_' + id + '_sandbox_token').parents('tr');
				$client_id         = $('#woocommerce_' + id + '_client_id').parents('tr');
				$client_secret     = $('#woocommerce_' + id + '_client_secret').parents('tr');
				$notifications     = $('#woocommerce_' + id + '_notifications').parents('tr');
				$notifications_log = $('#woocommerce_' + id + '_notifications_log').parents('tr');

				toogle_inputs();
				$test_mode.on('change', toogle_inputs);
				function toogle_inputs () {
					if ($test_mode.prop('checked')) {
						$token_sandbox.show();
						$token_production.hide();
					}
					else {
						$token_production.show();
						$token_sandbox.hide();
					}
				}

				toogle_api_inputs();
				$api_version.on('change', toogle_api_inputs);
				function toogle_api_inputs () {
					if ( 2 == $api_version.val() ) {
						$client_id.show();
						$client_secret.show();
						$notifications.show();
						$notifications_log.show();
					}
					else {
						$client_id.hide();
						$client_secret.hide();
						$notifications.hide();
						$notifications_log.hide();
					}
				}
			});
		</script>
		<?php
	}


	public function validate_api_version_field( $key, $value ) {
		update_option( h\prefix( 'api_version' ), $value, true );

		return $value;
	}


	public function process_admin_options() {
		// reset access token
		delete_transient( h\prefix( 'access_token_sandbox' ) );
		delete_transient( h\prefix( 'access_token_production' ) );

		parent::process_admin_options();
	}


	public function create_v2_notifications() {
    if ( intval( get_option( h\prefix( 'api_version' ) ) ) === 2 ) {
      $notifications = new Notification_Handler_Api_V2();
      $result        = $notifications->create_notifications();

			if ( is_string( $result ) || ! isset( $result->secret ) ) {
				update_option( h\prefix( 'notification_api_v2' ), '', 'no' );
			} else {
				update_option( h\prefix( 'notification_api_v2' ), '1', 'no' );
				update_option( h\prefix( 'notification_api_v2_secret' ), $result->secret, 'no' );
			}

    } else {
      delete_option( h\prefix( 'notification_api_v2' ) );
    }
	}



	/**
	 * Validate client ID special chars
	 *
	 * @param string $key
	 * @param string $value
	 * @return string
	 */
	public function validate_client_id_field( $key, $value ) {
		return trim( stripslashes( $value ) );
	}

	/**
	 * Validate client ID special chars
	 *
	 * @param string $key
	 * @param string $value
	 * @return string
	 */
	public function validate_client_secret_field( $key, $value ) {
		return trim( stripslashes( $value ) );
	}
}
