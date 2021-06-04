<?php

namespace WC_Juno;

use WC_Juno\functions as h;

class Install {
	public static function install_version_230() {
		try {

			if ( intval( get_option( h\prefix( 'api_version' ) ) ) === 2 ) {
				$notifications = new Notification_Handler_Api_V2();
				$result        = $notifications->create_notifications();

				if ( is_string( $result ) || ! isset( $result->secret ) ) {
					update_option( h\prefix( 'notification_api_v2' ), '', 'no' );
				} else {
					update_option( h\prefix( 'notification_api_v2' ), '1', 'no' );
					update_option( h\prefix( 'notification_api_v2_secret' ), $result->secret, 'no' );
				}
			}

		} catch (\Exception $e) {
			// oops
		}
	}
}
