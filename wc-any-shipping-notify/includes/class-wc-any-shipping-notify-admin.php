<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}


/**
 * Admin options.
 */
class WC_Any_Shipping_Notify_Admin {

  /**
   * Initialize the order actions.
   */
  public function __construct() {
    add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
    add_action( 'wp_ajax_wc_any_shipping_notify_add_tracking_code', array( $this, 'ajax_add_tracking_code' ) );
    add_action( 'wp_ajax_wc_any_shipping_notify_remove_tracking_code', array( $this, 'ajax_remove_tracking_code' ) );

    if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
      add_action( 'manage_shop_order_posts_custom_column', array( $this, 'tracking_code_orders_list' ), 100 );
    }
  }

  /**
   * Display tracking code into orders list.
   *
   * @param string $column Current column.
   */
  public function tracking_code_orders_list( $column ) {
    global $post, $the_order;

    if ( 'shipping_address' === $column ) {
      if ( empty( $the_order ) || $the_order->get_id() !== $post->ID ) {
        $the_order = wc_get_order( $post->ID );
      }

      $codes = wc_any_shipping_get_tracking_codes( $the_order );
      if ( ! empty( $codes ) ) {
        $tracking_codes = array();
        foreach ( $codes as $code => $shipping_company_slug ) {
          $tracking_codes[] = '<a href="' . wc_any_shipping_notify_get_shipping_company_url( $shipping_company_slug, $code, $the_order ) . '" target="_blank">' . esc_html( $code ) . '</a>';
        }

        include dirname( __FILE__ ) . '/views/html-list-table-tracking-code.php';
      }
    }
  }

  /**
   * Register tracking code metabox.
   */
  public function register_metabox() {
    add_meta_box(
      'wc_any_shipping_notify',
      'WC Any Shipping Notify',
      array( $this, 'metabox_content' ),
      'shop_order',
      'side',
      'default'
    );
  }

  /**
   * Tracking code metabox content.
   *
   * @param WC_Post $post Post data.
   */
  public function metabox_content( $post ) {
    $tracking_codes = wc_any_shipping_get_tracking_codes( $post->ID );
    $order          = wc_get_order( $post->ID );

    $available_companies = wc_any_shipping_notify_get_shipping_companies();

    wp_enqueue_style( 'wc-any-shipping-notify', plugins_url( 'assets/css/wc-any-shipping-notify.css', WC_Any_Shipping_Notify::get_main_file() ), array(), 1.0 );
    wp_enqueue_script( 'wc-any-shipping-notify', plugins_url( 'assets/js/wc-any-shipping-notify.js', WC_Any_Shipping_Notify::get_main_file() ), array( 'jquery', 'jquery-blockui', 'wp-util' ), 1.1, true );
    wp_localize_script(
      'wc-any-shipping-notify',
      'WC_Any_Shipping_Notify_JS',
      array(
        'order_id' => $post->ID,
        'i18n'     => array(
          'removeQuestion' => esc_js( __( 'Você quer mesmo remover este código de rastreio?', 'wc-any-shipping-notify' ) ),
        ),
        'nonces'   => array(
          'add'    => wp_create_nonce( 'wc-any-shipping-notify-add-tracking-code' ),
          'remove' => wp_create_nonce( 'wc-any-shipping-notify-remove-tracking-code' ),
        ),
      )
    );

    include_once dirname( __FILE__ ) . '/views/html-meta-box-tracking-code.php';
  }

  /**
   * Ajax - Add tracking code.
   */
  public function ajax_add_tracking_code() {
    check_ajax_referer( 'wc-any-shipping-notify-add-tracking-code', 'security' );

    $args = filter_input_array( INPUT_POST, array(
      'order_id'         => FILTER_SANITIZE_NUMBER_INT,
      'tracking_code'    => FILTER_SANITIZE_STRING,
      'shipping_company' => FILTER_SANITIZE_STRING,
    ) );

    $order = wc_get_order( $args['order_id'] );

    $result = wc_any_shipping_notify_update_tracking_code( $order, $args['tracking_code'], $args['shipping_company'] );

    $tracking_codes = wc_any_shipping_get_tracking_codes( $order );
    $codes          = array();

    foreach ( $tracking_codes as $code => $shipping_company_slug ) {
      $codes[] = array(
        'code' => $code,
        'url'  => wc_any_shipping_notify_get_shipping_company_url( $shipping_company_slug, $code, $order )
      );
    }

    wp_send_json_success( array( 'codes' => $codes, 'result' => $result ) );
  }

  /**
   * Ajax - Remove tracking code.
   */
  public function ajax_remove_tracking_code() {
    check_ajax_referer( 'wc-any-shipping-notify-remove-tracking-code', 'security' );

    $args = filter_input_array( INPUT_POST, array(
      'order_id'         => FILTER_SANITIZE_NUMBER_INT,
      'tracking_code'    => FILTER_SANITIZE_STRING,
      'shipping_company' => FILTER_SANITIZE_STRING,
    ) );

    wc_any_shipping_notify_update_tracking_code( $args['order_id'], $args['tracking_code'], $args['shipping_company'], true );

    wp_send_json_success();
  }
}

new WC_Any_Shipping_Notify_Admin();
