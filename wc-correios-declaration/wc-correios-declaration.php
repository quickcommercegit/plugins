<?php
/*
Plugin Name: WC Correios - Declaração de conteúdo
Description: Imprima a declaração de conteúdo do WooCommerce em um só clique
Plugin URI: http://fernandoacosta.net
Author: Fernando Acosta
Author URI: http://fernandoacosta.net
Version: 1.4.6
License: GPL2
*/
/*
    Copyright (C) 2017  Fernando Acosta  contato@fernandoacosta.net
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

/**
* My Default Class Description
*/
class WC_Correios_Shipping_Declaration {


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
    $this->includes();
  }

  public function includes() {
    // framework
    include_once 'magic-fw/magic-fw.php';
    include_once 'includes/updates.php';

    include_once 'includes/class-wc-correios-declaration-admin.php';
    include_once 'includes/class-wc-correios-declaration-customer-extra-fields.php';
    include_once 'includes/class-wc-correios-declaration-product-options.php';
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
   * Get the plugin url.
   * @return string
   */
  public static function plugin_url() {
    return untrailingslashit( plugins_url( '/', __FILE__ ) );
  }

  /**
   * Get the plugin dir url.
   * @return string
   */
  public static function plugin_dir_url() {
    return plugin_dir_url( __FILE__ );
  }

  /**
   * Get templates path.
   *
   * @return string
   */
  public static function get_templates_path() {
    return self::get_plugin_path() . 'templates/';
  }
}

add_action( 'plugins_loaded', array( 'WC_Correios_Shipping_Declaration', 'get_instance' ) );
