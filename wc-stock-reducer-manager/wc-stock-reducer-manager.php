<?php
/**
 * Plugin Name:          WC Redução e restauração de estoque
 * Plugin URI:           https://fernandoacosta.net
 * Description:          Reduzir estoque automaticamente ao criar um pedido e restaurá-lo ao cancelar ou reembolsar.
 * Author:               Fernando Acosta
 * Author URI:           https://fernandoacosta.net
 * Version:              1.2.6
 * License:              GPLv2 or later
 * WC requires at least: 3.0.0
 * WC tested up to:      4.2.5
 *
 * This plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * This plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this plugin. If not, see
 * <https://www.gnu.org/licenses/gpl-2.0.txt>.
 *
 */

class WC_Stock_Reducer_Manager {
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

    /**
     * Require core functions.
     */
    require_once plugin_dir_path( __FILE__ ) . 'includes/core-functions.php';

    /**
     * Require admin settings.
     */
    require_once plugin_dir_path( __FILE__ ) . 'includes/admin/class-admin-options.php';

    /**
     * Require rules.
     */
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-reduce-stock.php';

    /**
     * Require restore stock class.
     */
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-restore-stock.php';

    /**
     * Require Cancel Orders class.
     */
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-cancel-orders.php';
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

add_action( 'plugins_loaded', array( 'WC_Stock_Reducer_Manager', 'get_instance' ) );
