<?php

namespace WC_Juno;

use WC_Juno\functions as h;
use WC_Juno\Common\Hooker_Trait;

class Plugin_Dependencies {
	use Hooker_Trait;

	protected $missing_dependencies = [];

	public function add_hooks () {
		$this->add_filter( h\prefix( 'has_dependencies' ), 'has_dependencies' );
		$this->add_filter( h\prefix( 'check_dependencies' ), 'check_dependencies' );
		$this->add_filter( h\prefix( 'missing_dependencies_error_message' ), 'add_instructions_to_error_message' );
	}

	public function has_dependencies ( $result ) {
		return true;
	}

	public function check_dependencies ( $result ) {
		$deps = [
			// requires PHP 7.1+
			'php' => $this->compare_php_version( '7.0' ),

			// requires WooCommerce
			'woocommerce' => function_exists( 'WC' ),

			// requires Extra Checkout Fields for Brazil
			'wc_brazil_checkout_fields' => class_exists( 'Extra_Checkout_Fields_For_Brazil' ),
		];
		$result = true;

		foreach ( $deps as $dep => $bool ) {
			if ( ! $bool ) {
				$result = false;
				h\log_error( 'Missing plugin dependency:', $dep );
				$this->missing_dependencies[] = $dep;
			}
		}

		return $result;
	}

	public function add_instructions_to_error_message ( $message ) {
		foreach ( $this->missing_dependencies as $dep ) {
			$errors[] = $this->get_error_by_dependency( $dep );
		}

		if ( ! empty( $errors ) ) {
			$margin = \str_repeat( '&nbsp;', 4 );
			$message .= ' ' . \__( 'Siga as instruções abaixo:', 'woo-juno' ) . '<br>';
			$message .= "$margin - ";
			$message .= \implode( "<br>$margin - ", $errors );
		}

		return $message;
	}

	protected function get_error_by_dependency ( $dep ) {
		$message = '';

		switch ( $dep ) {
			case 'php':
				$message = \__( 'Seu PHP precisa estar na versão 7.1 ou superior.', 'woo-juno' );
			break;

			case 'woocommerce':
				$message = \__( 'Instale e ative o plugin WooCommerce.', 'woo-juno' );
			break;

			case 'wc_brazil_checkout_fields':
				$message = \__( 'Instale e ative o plugin Brazilian Market on WooCommerce.', 'woo-juno' );
			break;

			default:
				//$message = \__( 'Unknow dependency', 'woo-juno' );
		}

		return $message;
	}

	protected function compare_version ( $version1, $version2, $operator = '>=' ) {
		return version_compare( strtolower( $version1 ), strtolower( $version2 ), $operator );
	}

	protected function compare_php_version ( $min_version ) {
		return $this->compare_version( PHP_VERSION, $min_version, '>=' );
	}

}
