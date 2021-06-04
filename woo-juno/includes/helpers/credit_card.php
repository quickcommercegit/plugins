<?php

namespace WC_Juno\functions;

function get_user_credit_cards ( $user_id ) {
	if ( ! get_userdata( $user_id ) ) {
		throw new \Exception( __( "Usuário com ID $user_id não existe", 'woo-juno' ) );
	}

	$result = \get_user_meta( $user_id, prefix( "user_{$user_id}_credit_cards" ), true );
	return is_array( $result ) ? $result : [];
}

function add_user_credit_card ( $user_id, $card_data ) {
	$cards = get_user_credit_cards( $user_id );
	$last_numbers = $card_data['last_numbers'];
	$brand = $card_data['brand'];

	$card_data['cvc_hash'] = generate_credit_card_cvc_hash( $card_data );

	$cards["$brand--$last_numbers"] = $card_data;

	\update_user_meta( $user_id, prefix( "user_{$user_id}_credit_cards" ), $cards );
}

function get_user_credit_card ( $user_id, $brand, $last_numbers ) {
	$cards = get_user_credit_cards( $user_id );
	$key = "$brand--$last_numbers";

	return isset( $cards[ $key ] ) ? $cards[ $key ] : false;
}

function generate_credit_card_cvc_hash ( $card_data ) {
	$card_id = $card_data['card_id']; // id gerado pelo juno
	$last_numbers = $card_data['last_numbers'];
	$brand = $card_data['brand'];
	$cvc = $card_data['cvc'];
	return md5( "$card_id--$cvc--$last_numbers--$brand" );
}

function delete_user_credit_card ( $user_id, $brand, $last_numbers ) {
	$cards = get_user_credit_cards( $user_id );
	$key = "$brand--$last_numbers";

	if ( isset( $cards[ $key ] ) ) {
		unset( $cards[ $key ] );
		\update_user_meta( $user_id, prefix( "user_{$user_id}_credit_cards" ), $cards );
		return true;
	}

	return false;
}

function get_numbers ( $string ) {
	return preg_replace( '([^0-9])', '', $string );
}
