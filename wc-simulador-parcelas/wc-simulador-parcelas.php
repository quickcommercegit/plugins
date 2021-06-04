<?php
/**
 * Plugin Name: WC Simulador de parcelas e descontos
 * Description: Exibir o cálculo de parcelas e descontos diretamente na página do produto
 * Plugin URI: http://fernandoacosta.net
 * Author: Fernando Acosta
 * Author URI: http://fernandoacosta.net
 * Version: 1.6.14
 * WC requires at least: 4.0.0
 * WC tested up to:      5.3.0
 * Text Domain:          wc-simulador-parcelas
 * Domain Path:          /languages
 * License: GPL2
 */
/*
    Copyright (C) 2016  Fernando Acosta  contato@fernandoacosta.net
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class WC_Simulador_Parcelas {
  /**
   * Instance of this class.
   *
   * @var object
   */
  protected static $instance = null;
  /**
   * Initialize the plugin public actions.
   */
  function __construct() {
    add_action( 'init', array( __CLASS__, 'load_plugin_textdomain' ), -1 );

    if ( version_compare( phpversion(), '5.6', '<' ) ) {
      add_action( 'admin_notices', array( $this, 'php_version_notice' ) );
      return;
    }

    if ( version_compare( WC_VERSION, '2.7', '<' ) ) {
      add_action( 'admin_notices', array( $this, 'wc_version_notice' ) );
      return;
    }

    $this->includes();

    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_css' ), 999 );
    add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue_script' ), 999 );
    add_action( 'init', array( $this, 'extended_functions' ) );
  }

  /**
   * Load the plugin text domain for translation.
   */
  public static function load_plugin_textdomain() {
    load_plugin_textdomain( 'wc-simulador-parcelas', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
  }

  /**
   * Return an instance of this class.
   *
   * @return object A single instance of this class.
   */
  public static function get_instance() {
    // If the single instance hasn't been set, set it now.
    if ( null == self::$instance ) {
      self::$instance = new self;
    }
    return self::$instance;
  }

  private function includes() {
    include_once 'includes/updates.php';

    include_once 'includes/class-wcsp-deprecated.php';
    include_once 'includes/class-init.php';
    include_once 'includes/admin/class-admin-options.php';
    include_once 'includes/apply-rules.php';
    include_once 'includes/class-wcsp-replace-functions.php';
    include_once 'includes/class-calculate-values.php';

    if ( class_exists( 'WC_PagSeguro' ) ) {
      include_once 'extend/class-pagseguro-integration.php';
    }
  }

  /**
   * Get main file.
   *
   * @return string
   */
  public static function get_main_file() {
    return __FILE__;
  }

  /**
   * Get plugin path.
   *
   * @return string
   */

  public static function get_plugin_path() {
    return plugin_dir_path( __FILE__ );
  }

  /**
   * Enqueue the CSS
   *
   * @return void
   */
  public function enqueue_css() {
    if ( apply_filters( 'wcsp_load_css', true ) ) {
      wp_enqueue_style( 'wc-simulador-parcelas-css', plugins_url( '/assets/css/style-1.6.8.css', __FILE__ ), 999 );
    }
  }

  /**
   * Enqueue scripts
   *
   * @return void
   */
  public function maybe_enqueue_script() {
    if ( apply_filters( 'wcsp_use_dynamic_table', ( 'yes' === get_option( 'wc_simulador_parcelas_update_table_for_variations', 'no' ) && '' !== get_option( 'wc_simulador_parcelas_exibicao_tabela_completa', '' ) ) ) ) {
      wp_enqueue_script( 'wc-simulador-parcelas', plugins_url( '/assets/js/simulador-parcelas.js', __FILE__ ), array( 'jquery', 'accounting' ), '1.6.10', true );

      $default_fee      = WC_Simulador_Parcelas_Init::get_fee();
      $installments_fee = array();

      foreach ( range( 1, WC_Simulador_Parcelas_Init::get_installments_limit() ) as $i ) {
        $installments_fee[ $i ] = WC_Simulador_Parcelas_Init::get_fee( false, $i );
      }

      wp_localize_script( 'wc-simulador-parcelas', 'WCSimuladorParcelasParams', apply_filters( 'wcsp_dynamic_table_params', array(
        'currency_format_num_decimals'  => wc_get_price_decimals(),
        'currency_format_symbol'        => get_woocommerce_currency_symbol(),
        'currency_format_decimal_sep'   => esc_attr( wc_get_price_decimal_separator() ),
        'currency_format_thousand_sep'  => esc_attr( wc_get_price_thousand_separator() ),
        'currency_format'               => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ), // For accounting JS
        'rounding_precision'            => wc_get_rounding_precision(),

        'max_installments'              => WC_Simulador_Parcelas_Init::get_installments_limit(),
        'max_installments_no_fee'       => WC_Simulador_Parcelas_Init::get_max_installment_no_fee(),
        'min_installment'               => WC_Simulador_Parcelas_Init::get_min_installment(),
        'fees'                          => $installments_fee,
        'fee'                           => $default_fee,
        'without_fee_label'             => __( 'sem juros', 'wc-simulador-parcelas' ),
        'with_fee_label'                => __( 'com juros', 'wc-simulador-parcelas' ),
      ) ) );
    }
  }

  public function extended_functions() {

    if ( is_admin() ) {
      return;
    }

    if ( apply_filters( 'wcsp_extended_functions', true ) ) {
      include_once( 'extend/extended-functions.php' );
    }
  }

  /**
   * PHP version notice.
   */
  public function wc_version_notice() {
    include dirname( __FILE__ ) . '/includes/admin/views/html-notice-missing-woocommerce.php';
  }

  /**
   * PHP version notice.
   */
  public function php_version_notice() {
    include dirname( __FILE__ ) . '/includes/admin/views/html-notice-php-deprecated.php';
  }

}
add_action( 'plugins_loaded', array( 'WC_Simulador_Parcelas', 'get_instance' ) );
