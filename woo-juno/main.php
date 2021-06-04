<?php
/**
 * Plugin Name:          Juno para WooCommerce
 * Plugin URI:           https://wordpress.org/plugins/woo-juno/
 * Description:          Plugin para WordPress e WooCommerce adiciona o gateway Juno ao WooCommerce.
 * Author:               Juno Gateway de Pagamentos para ecommerces
 * Author URI:           https://juno.com.br
 * Version:              2.3.1
 * License:              GPLv3
 * License URI:          http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:          woo-juno
 * Domain Path:          /languages
 * WC requires at least: 4.0.0
 * WC tested up to:      5.2.0
 *
 * WooCommerce Juno is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WooCommerce Juno is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

if ( ! defined( 'WPINC' ) ) die();

define( 'WOO_JUNO_PLUGIN_VERSION', '2.3.1' );

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/helpers.php';

WC_Juno\Core\Config::setup( __FILE__ );

require_once __DIR__ . '/includes/boot.php';

WC_Juno\Core\Plugin::run();
