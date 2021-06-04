<?php
/*
Plugin Name: WC Dias adicionais por produto
Description: Adicione prazos de entrega adicionais a cada produto. Tenha controle sobre como exibir e se quer somar ao prazo padrão.
Plugin URI: http://fernandoacosta.net
Author: Fernando Acosta
Author URI: http://fernandoacosta.net
Version: 1.4.13
WC requires at least: 3.5.0
WC tested up to:      4.8.0
License: GPL2
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
/**
* WC Additional Days Per Product Wrapper
*/
class WC_Additional_Days_Per_Product {
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
    include_once 'includes/updates.php';
    include_once 'includes/register-shipping-fields.php';
    include_once 'includes/register-metabox.php';
    include_once 'includes/additional-days-method-label.php';
    include_once 'includes/plugin-functions.php';
    include_once 'includes/class-global-options.php';
    include_once 'includes/class-category-options.php';
    include_once 'includes/additional-days-cart.php';

    include_once 'includes/integrations/melhor-envio.php';
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

add_action( 'plugins_loaded', array( 'WC_Additional_Days_Per_Product', 'get_instance' ) );
