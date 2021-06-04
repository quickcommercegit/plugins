<?php

namespace WC_Juno\Core;

use WC_Juno\Utils\Immutable_Data_Store;
use WC_Juno\functions as h;

class Config {
	protected static $options = null;

	public static function get_options () {
		if ( null === self::$options ) {
			self::$options = new Immutable_Data_Store();
		}
		return self::$options;
	}

	public static function setup ( $MAIN_FILE ) {
		if ( ! null === self::$options ) return;

		$root = \dirname( $MAIN_FILE );
		$plugin_config = require $root . '/config.php';

		if ( ! isset( $plugin_config['SLUG'] ) ) {
			$plugin_config['SLUG'] = h\str_slug( $plugin_config['NAME'] );
		}

		if ( ! isset( $plugin_config['PREFIX'] ) ) {
			$plugin_config['PREFIX'] = h\str_slug( $plugin_config['SLUG'], '_' ) . '_';
		}

		$options = self::get_options();

		$options->set( 'MAIN_FILE', $MAIN_FILE );
		$options->set( 'ROOT_DIR', $root );

		foreach ( $plugin_config as $key => $value ) {
			$options->set( $key, $value );
		}
	}

	public static function set ( $key, $value ) {
		return self::get_options()->set( $key, $value );
	}

	public static function get ( $key, $default = null ) {
		if ( self::get_options()->has( $key ) ) {
			return self::get_options()->get( $key );
		}
		h\throw_if( null === $default, \Exception::class, "not found \"$key\"" );
		return $default;
	}
}
