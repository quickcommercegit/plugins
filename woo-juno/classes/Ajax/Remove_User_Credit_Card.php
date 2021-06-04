<?php

namespace WC_Juno\Ajax;

use WC_Juno\Common\Abstract_Ajax_Action;
use WC_Juno\functions as h;

class Remove_User_Credit_Card extends Abstract_Ajax_Action {
	public function get_action_name () {
		return h\prefix( 'ajax_remove_user_credit_card' );
	}

	public function callback () {
		$card_info = h\request_value( 'card_info' );
		$data = [];
		$parts = explode( '--', $card_info );

		$data['brand'] = trim( $parts[0] );
		$data['last_numbers'] = trim( $parts[1] );

		$deleted = h\delete_user_credit_card(
			\get_current_user_id(),
			$data['brand'],
			$data['last_numbers']
		);

		if ( $deleted ) {
			$this->send_json(null, 200);
		}

		$this->send_json(null, 404);
	}

	public function get_accepted_methods () {
		return [ 'POST' ];
	}
}
