<?php
/**
*
*/
class WC_Any_Shipping_Notify_Api {

  function __construct() {
    add_action( 'rest_api_init', array( $this, 'register_tracking_code' ), 100 );
  }


	/**
	 * register_tracking_code
	 *
	 * @return void
	 */
	public function register_tracking_code() {
		if ( ! function_exists( 'register_rest_field' ) ) {
			return;
		}

		register_rest_field( 'shop_order',
			'shipping_tracking_code',
			array(
				'get_callback'    => array( $this, 'get_tracking_code_callback' ),
				'update_callback' => array( $this, 'update_tracking_code_callback' ),
				'schema'          => array(
					'description' => __( 'Tracking code.', 'wc-any-shipping-notify' ),
					'type'        => 'array',
					'context'     => array( 'view', 'edit' ),
				),
			)
		);
  }


	/**
	 * get_tracking_code_callback
	 *
	 * @param array           $data    Details of current response.
	 * @param string          $field   Name of field.
	 * @param WP_REST_Request $request Current request.
	 *
	 * @return string
	 */
	public function get_tracking_code_callback( $data, $field, $request ) {
    $codes  = wc_any_shipping_get_tracking_codes( $data['id'] );
    $result = [];

    if ( $codes ) {

      $available_companies = wc_any_shipping_notify_get_shipping_companies();
      foreach ( $codes as $code => $id ) {
        $company = isset( $available_companies[ $id ] ) ? $available_companies[ $id ] : null;

        $result[] = [
          'company_id'    => $id,
          'name'          => $company ? $company['name'] : 'Transportadora',
          'tracking_code' => $code
        ];
      }
    }

    return $result;
	}

	/**
	 * update_tracking_code_callback
	 *
	 * @param string  $value  The value of the field.
	 * @param WC_Order
	 *
	 * @return bool
	 */
	public function update_tracking_code_callback( $value, $object ) {
		if ( ! $value || ! is_array( $value ) || ! isset( $value['company_id'], $value['tracking_code'] ) ) {
			return;
		}

		$id = is_a( $object, 'WC_Order' ) ? $object->get_id() : $object->ID;

		return wc_any_shipping_notify_update_tracking_code( $id, $value['tracking_code'], $value['company_id'] );
	}
}

new WC_Any_Shipping_Notify_Api();
