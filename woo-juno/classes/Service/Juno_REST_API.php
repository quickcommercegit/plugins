<?php

namespace WC_Juno\Service;

use WC_Juno\functions as h;

$version = get_option( h\prefix( 'api_version' ), 1 );

if  ( 2 === intval( $version ) ) {
	class Juno_REST_API extends Juno_REST_API_V2 {
		public function __construct ( $sandbox = null, $gateway = null ) {
			parent::__construct( $sandbox, $gateway );
		}
	}
} else {
	class Juno_REST_API extends Juno_REST_API_V1 {
		public function __construct ( $sandbox = null ) {
			parent::__construct( $sandbox );
		}
	}
}
