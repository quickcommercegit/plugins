<?php
/*
Plugin Name: Notificação de rastreio por transportadora
Description: Envio notificações para qualquer transportadora
Plugin URI: http://fernandoacosta.net
Author: Fernando Acosta
Author URI: http://fernandoacosta.net
Version: 1.5.8
License: GPL2
Text Domain: wc-any-shipping-notify
Domain Path: /languages
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
class WC_Any_Shipping_Notify {
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
    add_filter( 'woocommerce_email_classes', array( $this, 'include_emails' ) );

    include_once 'includes/class-wc-any-shipping-notify-shipping-options.php';
    include_once 'includes/class-wc-any-shipping-notify-admin.php';
    include_once 'includes/class-wc-any-shipping-notify-myaccount.php';
    include_once 'includes/class-wc-any-shipping-notify-orders.php';
    include_once 'includes/class-wc-any-shipping-notify-api.php';
    include_once 'includes/core-functions.php';

    include_once 'includes/updates.php';
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


  /**
   * Include emails.
   *
   * @param  array $emails Default emails.
   *
   * @return array
   */
  public function include_emails( $emails ) {
    if ( ! isset( $emails['WC_Any_Shipping_Notify_Email'] ) ) {
      $emails['WC_Any_Shipping_Notify_Email'] = include( dirname( __FILE__ ) . '/includes/class-wc-any-shipping-notify-email.php' );
    }

    return $emails;
  }
}

add_action( 'plugins_loaded', array( 'WC_Any_Shipping_Notify', 'get_instance' ), 20 );
