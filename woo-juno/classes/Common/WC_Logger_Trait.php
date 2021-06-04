<?php

namespace WC_Juno\Common;

use WC_Juno\functions as h;

trait WC_Logger_Trait {

	protected $wc_logger_source = null;

	public function log ( $data, $type = 'info' ) {
		if ( null == $this->wc_logger_source ) return;

		$logger = \wc_get_logger();

		if ( ! \method_exists( $logger, $type ) ) {
			throw new RuntimeExpection( "undefined \"$type\" log type" );
		}

		if ( ! \is_array( $data ) ) $data = [ $data ];

		$message = '';
		foreach( $data as $part ) {
			if ( null === $part ) {
				$message .= 'Null';
			}
			elseif ( \is_bool( $part ) ) {
				$message .= $part ? 'True' : 'False';
			}
			elseif ( ! \is_string( $part ) ) {
				$message .= \print_r( $part, true );
			}
			else {
				$message .= $part;
			}
			$message .= ' ';
		}

		$context = [
			'source' => $this->wc_logger_source,
		];

		$logger->$type( \trim( $message ), $context );

		h\log_debug( 'Added log entry to ' . \WC_Log_Handler_File::get_log_file_path( $context['source'] ) );
	}

	public function set_wc_logger_source ( $source ) {
		$this->wc_logger_source = $source;
	}
}
