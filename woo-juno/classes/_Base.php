<?php

namespace WC_Juno;

use WC_Juno\Common\Hooker_Trait;
use WC_Juno\functions as h;

class Base {
	use Hooker_Trait;

	public function add_hooks () {
		$this->add_action( 'foo', 'bar' );
	}
}
