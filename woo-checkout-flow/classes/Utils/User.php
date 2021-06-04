<?php

namespace FlowDigital\WC_Checkout_Flow\Utils;

use FlowDigital\WC_Checkout_Flow\functions as h;

class User {

	/**
	 * @return \WP_User object on success, \WP_Error on failure.
	 */
	public static function register( $email, $login_on_success = true ) {
		if ( \is_email( $email ) ) {
			$username        = self::get_username_by_email( $email );
			$username_exists = \username_exists( $username );
			$count           = 2;
			$user            = null;

			while ( $username_exists ) {
				if ( ! \username_exists( $username + $count ) ) {
					$username = $username + $count;
					break;
				}
				$count += 1;
			}

			$password = self::generate_password();

			$result = wp_create_user(
				$username,
				$password,
				$email
			);

			if ( ! \is_wp_error( $result ) ) {
				$user_id = $result;

				wp_update_user( [
					'ID'   => $user_id,
					'role' => 'customer'
				] );

				if ( $login_on_success ) {
					$result = self::login( $email, $password );
				} else {
					$result = get_user_by( 'id', $user_id );
				}

				self::send_email_new_user( $username, $password, $email );
			}

			return $result;
		}

		return new \WP_Error( 'invalid_email', __( 'E-mail inválido.' ) );
	}

	public static function login( $email, $password ) {
		$creds = array(
			'user_login'    => $email,
			'user_password' => $password,
			'remember'      => true
		);

		$user = \wp_signon( $creds, \is_ssl() );

		return $user;
	}

	public static function change_password( $email, $password ) {
		$user = get_user_by( 'email', $email );

		if ( $user ) {
			$user_id = $user->ID;
			wp_set_password( $password, $user_id );

			return true;
		}

		return false;
	}

	public static function email_exists( $email ) {
		return \email_exists( $email );
	}

	public static function generate_password( $size = 12 ) {
		$size = \apply_filters( h\prefix( 'password_length' ), $size );

		return \wp_generate_password( $size, false, false );
	}

	public static function send_email_new_user( $username, $password, $email ) {
		$shop_name = get_bloginfo( 'name' );
		$title     = esc_html__( 'Seja bem-vindo', 'wc-checkout-flow' );
		$subject   = sprintf( esc_html__( 'Sua conta em %1$s foi criada', 'wc-checkout-flow' ), esc_html( $shop_name ) );
		$message   = wc_get_template_html(
			'pre-checkout/email-new-user.php',
			[
				'username'            => $username,
				'password'            => $password,
				'shop_name'           => $shop_name,
				'my_account_page_url' => get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ),
			],
			'',
			h\config_get( 'WC_TEMPLATE_PATH' )
		);

		WC()->mailer()->send(
			$email,
			$subject,
			WC()->mailer()->wrap_message( $title, $message )
		);
	}

	public static function send_email_reset_password( $email ) {
		$shop_name    = get_bloginfo( 'name' );
		$user         = \get_user_by( 'email', $email );
		$title        = esc_html__( 'Pedido de redefinição de senha', 'wc-checkout-flow' );
		$subject      = sprintf( esc_html__( 'Pedido de redefinição de senha em %1$s', 'wc-checkout-flow' ), esc_html( $shop_name ) );
		$hash         = self::create_email_hash( $email );
		$checkout_url = get_permalink( get_option( 'woocommerce_checkout_page_id' ) );

		$message = wc_get_template_html(
			'pre-checkout/email-reset-password.php',
			[
				'username'           => $user->user_login,
				'shop_name'          => $shop_name,
				'reset_password_url' => add_query_arg( h\prefix( 'hash' ), $hash, $checkout_url ),
			],
			'',
			h\config_get( 'WC_TEMPLATE_PATH' )
		);

		WC()->mailer()->send(
			$email,
			$subject,
			WC()->mailer()->wrap_message( $title, $message )
		);
	}

	public static function validate_email_hash( $hash ) {
		$email = \get_transient( h\prefix( "email_hash_$hash" ) );

		if ( \is_email( $email ) ) {
			return $email;
		}

		return false;
	}

	public static function delete_email_hash( $hash ) {
		\delete_transient( h\prefix( "email_hash_$hash" ) );
	}

	protected static function create_email_hash( $email ) {
		$time   = \time();
		$hash   = \hash( 'sha1', "$email:$time", false );
		$expire = 24 * \HOUR_IN_SECONDS;

		\set_transient( h\prefix( "email_hash_$hash" ), $email, $expire );

		return $hash;
	}

	protected static function get_username_by_email( $email ) {
		$parts = explode( '@', $email );

		return $parts[0];
	}


	public static function persistent_cart_content( $user_id ) {
		update_user_meta(
			$user_id,
			'_woocommerce_persistent_cart_' . get_current_blog_id(),
			array(
				'cart' => WC()->session->get( 'cart' ),
			)
		);
	}


	public static function restore_cart( $user_id ) {
		$saved_cart = get_user_meta( $user_id, '_woocommerce_persistent_cart_' . get_current_blog_id(), true );

		if ( ! $saved_cart ) {
			return;
		}

		$cart_contents = array();

		foreach ( $saved_cart['cart'] as $key => $values ) {
			$product = wc_get_product( $values['variation_id'] ? $values['variation_id'] : $values['product_id'] );

			if ( empty( $product ) || ! $product->exists() || 0 >= $values['quantity'] ) {
				continue;
			}

			if ( ! $product->is_purchasable() ) {
				$update_cart_session = true;
				/* translators: %s: product name */
				$message = sprintf( __( '%s has been removed from your cart because it can no longer be purchased. Please contact us if you need assistance.', 'woocommerce' ), $product->get_name() );
				/**
				 * Filter message about item removed from the cart.
				 *
				 * @since 3.8.0
				 * @param string     $message Message.
				 * @param WC_Product $product Product data.
				 */
				$message = apply_filters( 'woocommerce_cart_item_removed_message', $message, $product );
				wc_add_notice( $message, 'error' );
				do_action( 'woocommerce_remove_cart_item_from_session', $key, $values );

			} elseif ( ! empty( $values['data_hash'] ) && ! hash_equals( $values['data_hash'], wc_get_cart_item_data_hash( $product ) ) ) { // phpcs:ignore PHPCompatibility.PHP.NewFunctions.hash_equalsFound
				$update_cart_session = true;
				/* translators: %1$s: product name. %2$s product permalink */
				wc_add_notice( sprintf( __( '%1$s has been removed from your cart because it has since been modified. You can add it back to your cart <a href="%2$s">here</a>.', 'woocommerce' ), $product->get_name(), $product->get_permalink() ), 'notice' );
				do_action( 'woocommerce_remove_cart_item_from_session', $key, $values );

			} else {
				// Put session data into array. Run through filter so other plugins can load their own session data.
				$session_data = array_merge(
					$values,
					array(
						'data' => $product,
					)
				);

				$cart_contents[ $key ] = apply_filters( 'woocommerce_get_cart_item_from_session', $session_data, $values, $key );
			}
		}

		// Add to cart right away so the product is visible in woocommerce_get_cart_item_from_session hook.
		WC()->cart->set_cart_contents( $cart_contents );

		WC()->session->set( 'cart', $saved_cart['cart'] );
		WC()->cart->calculate_totals();
	}
}
