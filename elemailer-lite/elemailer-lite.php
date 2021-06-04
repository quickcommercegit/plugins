<?php
defined('ABSPATH') || exit;

/**
 * Plugin Name: Elemailer Lite
 * Plugin URI:  https://elemailer.com/
 * Description: Elementor Email template & campaign builder
 * Version: 1.0.1
 * Author: elemailer
 * Author URI:  https://elemailer.com/
 * Text Domain: elemailer-lite
 * Elementor tested up to: 3.2.3
 * Elementor Pro tested up to: 3.2.2
 * License:  GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// run auto loader
require 'autoloader.php';

// run plugin initialization file
require 'plugin.php';

/**
 * plugin activation hook. will be call after activate the plugin.
 */
register_activation_hook(__FILE__, [Elemailer_Lite\Plugin::instance(), 'action_after_active_plugin']);

/**
 * plugin deactivation hook. will be call after deactivate the plugin.
 */
register_deactivation_hook(__FILE__, [Elemailer_Lite\Plugin::instance(), 'action_after_deactivate_plugin']);


/**
 * load plugin after initialize wordpress core
 */
add_action('plugins_loaded', function () {
    // load when elemailer is not present
    if (!did_action('elemailer/after_load')) {
        // active hook before plugin load
        do_action('elemailer_lite/before_load');
        // run global function file
        require 'core/global.php';
        // plugin activation function call
        Elemailer_Lite\Plugin::instance()->init();
        // active hook after plugin load
        do_action('elemailer_lite/after_load');
    }
}, 150);

/**
 * call wp_loaded hooks which will load after wp fully ready means after plugins_loaded, theme_setup
 */
add_action('wp_loaded', function () {
    // load when elemailer is not present
    if (!did_action('elemailer/after_load')) {
        // call some functionality after wp fully loaded
        Elemailer_Lite\Plugin::instance()->after_wp_loaded_hooks();
    }
});
