<?php

namespace WC_Juno\Service;

use WC_Juno\functions as h;

abstract class Juno_REST_API_V1 {

  public    $version = 1;
  protected $token   = null;
  protected $sandbox = null;

  public function __construct ( $sandbox = null ) {
    $this->sandbox = null === $sandbox ? h\is_sandbox_enabled() : $sandbox;
    $this->token = h\get_private_token( $sandbox );
  }

  public function request_payment ( $params = [] ) {
    $endpoint = $this->get_api_endpoint( 'issue-charge' );
    $request_args = [
      'method' => 'POST',
      'timeout' => 30,
      'headers' => $this->get_headers(),
    ];

    $params['token'] = $this->token;
    $params['responseType'] = 'JSON';

    $response = \wp_remote_post(
      $endpoint . '?' . \http_build_query( $params ),
      $request_args
    );

    return $response;
  }

  public function fetch_payment_details ( $params = [] ) {
    $endpoint = $this->get_api_endpoint( 'fetch-payment-details' );
    $request_args = [
      'method' => 'GET',
      'timeout' => 30,
      'headers' => $this->get_headers(),
    ];

    $params['responseType'] = 'JSON';

    $response = \wp_remote_post(
      $endpoint . '?' . \http_build_query( $params ),
      $request_args
    );

    return $response;
  }

  public function fetch_balance ( $params = [] ) {
    $endpoint = $this->get_api_endpoint( 'fetch-balance' );
    $request_args = [
      'method' => 'GET',
      'timeout' => 30,
      'headers' => $this->get_headers(),
    ];

    $params['token'] = $this->token;
    $params['responseType'] = 'JSON';

    $response = \wp_remote_post(
      $endpoint . '?' . \http_build_query( $params ),
      $request_args
    );

    return $response;
  }

  public function request_transfer ( $params = [] ) {
    $endpoint = $this->get_api_endpoint( 'request-transfer' );
    $request_args = [
      'method' => 'GET',
      'timeout' => 30,
      'headers' => $this->get_headers(),
    ];

    $params['token'] = $this->token;
    $params['responseType'] = 'JSON';

    $response = \wp_remote_post(
      $endpoint . '?' . \http_build_query( $params ),
      $request_args
    );

    return $response;
  }

  public function get_card_token( $card_hash ) {
    $endpoint = $this->get_api_endpoint( 'card-tokenization' );
    $request_args = [
      'method' => 'POST',
      'timeout' => 30,
      'headers' => $this->get_headers(),
    ];

    $params['token']          = $this->token;
    $params['responseType']   = 'JSON';
    $params['creditCardHash'] = $card_hash;

    $response = \wp_remote_post(
      $endpoint . '?' . \http_build_query( $params ),
      $request_args
    );

    return $response;
  }

  protected function get_api_endpoint( $endpoint ) {
    $base = 'boletobancario.com/boletofacil/integration/api/v1';
    return 'https://' . ( $this->sandbox ? 'sandbox.' : '' ) . "$base/$endpoint";
  }

  protected function get_headers() {
    return [
      //'content-type' => 'application/json',
      'accept-charset' => 'utf-8',
      'x-platform'     => 'pluginwoocommerce',
    ];
  }
}
