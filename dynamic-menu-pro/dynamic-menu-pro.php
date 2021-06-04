<?php



/*

Plugin Name: Dynamic Menu Pro

Plugin URI: https://awesometogi.com/dynamic-menu-product-categories/

Description: This plugin is used to create dynamic menus of woocommerce product categories and tags.

Author: AWESOME TOGI

Author URI: https://awesometogi.com

Version: 4.5.4

*/

if (!defined('ABSPATH')) {

    exit; // Exit if accessed directly

}

require_once('inc/functions.php');

/**

 * Check WooCommerce exists

 */

if (is_woocommerce_active()) {



 

define('DM_VERSION', '4.5.4');

define('DM_MINIMUM_WP_VERSION', '3.7');

define('DM_RELATIVE_PATH', plugin_dir_url(__FILE__));

define('DM_ABS_PATH', plugin_dir_path(__FILE__));

define('DM_PLUGIN_PATH', plugin_dir_path(__FILE__));

define('DM_SPECIAL_SECRET_KEY', '591c4669bbfe84.08596709');

define('DM_LICENSE_SERVER_URL', 'https://awesometogi.com/');

define('DM_ITEM_REFERENCE', 'DMPlugin');

require_once('plugin-authentication.php');

    //echo get_option('DM_license_key');



register_deactivation_hook( __FILE__, 'DM_plugin_deactivation' );

function my_plugin_deactivation() {

 delete_option('last_DM_update_check_data');

 delete_option('last_DM_update_check');

}



 



$api_params = array(

    'slm_action' => 'slm_check',

    'secret_key' => DM_SPECIAL_SECRET_KEY,

    'license_key' => get_option('DM_license_key'),

    'registered_domain' => isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '',

    'item_reference' => urlencode(DM_ITEM_REFERENCE),

    'time' => time()

);



$last_update_check = get_option('last_DM_update_check');

$start_date        = new DateTime(date('Y-m-d H:i:s', $last_update_check));

$since_start       = $start_date->diff(new DateTime(date('Y-m-d H:i:s')));

global $pagenow; 



if (($since_start->days >= 5 || $last_update_check == '') && $pagenow == 'plugins.php') {



    update_option('last_DM_update_check', time());

    $response = wp_remote_get(add_query_arg($api_params, DM_LICENSE_SERVER_URL), array(

        'timeout' => 20,

        'sslverify' => false

    ));

    update_option('last_DM_update_check_data', $response);

} else {

    $response = get_option('last_DM_update_check_data');



}



if (is_wp_error($response)) {



    add_action('admin_notices', 'dm_activation_notice');



    function dm_activation_notice()

    {

        $class   = 'notice notice-error is-dismissible';

        $message = __('Unexpected Error! The query returned with an error.', 'dynamic-menu');



        printf('<div class="%1$s" ><p>%2$s</p></div>', $class, $message);

    }



} else {



    $data = json_decode($response['body'], true);



    $license_key = get_option('DM_license_key');

	

    if (isset($data['status']) && $data['status'] == 'active') {

        require 'plugin-update-checker/plugin-update-checker.php';



        $MyUpdateChecker = Puc_v4_Factory::buildUpdateChecker(DM_LICENSE_SERVER_URL . "/wp-content/uploads/wp-update-server/?action=get_metadata&license_key=$license_key&slug=dynamic-menu-pro", __FILE__, 'dynamic-menu-pro');



        require_once('class-dynamic-menus.php');

        require_once('edit_custom_walker.php');

        require_once('dm_custom_walker.php');

        require_once('dynamic-menu-settings.php');



        $GLOBALS['dynamic_menu'] = new Dynamic_Menus();



    } else {

        if (isset($data['status']) && $data['status'] == 'expired') {

            require_once('class-dynamic-menus.php');
            require_once('edit_custom_walker.php');
            require_once('dm_custom_walker.php');
            require_once('dynamic-menu-settings.php');

            $GLOBALS['dynamic_menu'] = new Dynamic_Menus();

            if (isset($data['subscr_expired']) && $data['subscr_expired'] == '1') {
                add_action('admin_notices', 'dm_subscription_expired_notice');
            } else {
                add_action('admin_notices', 'dm_licence_expired_notice');
            }

        } else {
            add_action('admin_notices', 'dm_activation_notice');
        }


        function dm_activation_notice()

        {

            $class   = 'notice notice-error is-dismissible dm_license_error_link';

            $message = __('Dynamic Menu is enabled but not effective.', 'dynamic-menu');

            $url     = esc_url(home_url() . '/wp-admin/options-general.php?page=dynamic-menu-pro/plugin-authentication.php');

            printf('<div class="%1$s"><p>%2$s <a href="' . $url . '">You need to enter a license key.</a></p></div>', $class, $message);

        }

        function dm_licence_expired_notice()
        {
            $class   = 'notice notice-error is-dismissible dm_license_error_link';
            $message = __('Your licence key for  Dynamic Menu Pro plugin has expired.  Renew it to get future updates.', 'dynamic-menu');
            printf('<div class="%1$s"><p>%2$s </p></div>', $class, $message);
        }

        function dm_subscription_expired_notice()
        {
            $class   = 'notice notice-error is-dismissible dm_license_error_link';
            $message = __('Your subscription for Dynamic Menu Pro plugin has expired. Renew it to get future updates.', 'dynamic-menu');
            printf('<div class="%1$s"><p>%2$s </p></div>', $class, $message);
        }

        function DM_plugin_action_links($links)

        {

            $links = array_merge(array(

                '<a href="' . esc_url(admin_url('/options-general.php?page=dynamic-menu-pro%2Fplugin-authentication.php')) . '">' . __('Settings', 'dynamic-menu') . '</a>'

            ), $links);

            update_option('plugin_option', $links);



            foreach ($links as $index => $option) {

                $link_slug = strtolower(strip_tags($option));

                if ($link_slug != 'deactivate' && $link_slug != 'activate' && $link_slug != 'translate') {

                    $license_key = get_option('DM_license_key');

                    if (!empty($license_key)) {



                        unset($links[$index]);

                    } else {

                        unset($links[$index]);

                    }

                }

            }



            return $links;

        }



        add_action('plugin_action_links_' . plugin_basename(__FILE__), 'DM_plugin_action_links');



        function filter_DM_plugin_updates($value)

        {

            $license_key = get_option('DM_license_key');

            if (!empty($license_key)) {

                if (isset($value->response['dynamic-menu-pro/dynamic-menu-pro.php']))

                    unset($value->response['dynamic-menu-pro/dynamic-menu-pro.php']);

            } else {

                if (isset($value->response['dynamic-menu-pro/dynamic-menu-pro.php']))

                    unset($value->response['dynamic-menu-pro/dynamic-menu-pro.php']);

            }



            return $value;

        }



        add_filter('site_transient_update_plugins', 'filter_DM_plugin_updates');







        $path = plugin_basename(__FILE__);

        add_action("after_plugin_row_{$path}", function($plugin_file, $plugin_data, $status)

        {

            $license_key = get_option('DM_license_key');

            if (!empty($license_key)) {
                $response = get_option('last_DM_update_check_data');
                $data = json_decode($response['body'], true);
                
                if (isset($data['status']) && $data['status'] == 'expired') {
                    if (isset($data['subscr_expired']) && $data['subscr_expired'] == '1') {
                        $class   = 'notice notice-error is-dismissible dm_license_error_link';
                        $message = __('Your subscription for Dynamic Menu Pro plugin has expired. Renew it to get future updates.', 'dynamic-menu');
                        echo '<tr class="active"><td>&nbsp;</td><td colspan="2"><div class="notice inline notice-warning notice-alt"><p> ' . $message . ' </p></div></td></tr>';
                    } else {
                        $class   = 'notice notice-error is-dismissible dm_license_error_link';
                        $message = __('Your licence key for  Dynamic Menu Pro plugin has expired.  Renew it to get future updates.', 'dynamic-menu');
                        echo '<tr class="active"><td>&nbsp;</td><td colspan="2"><div class="notice inline notice-warning notice-alt"><p> ' . $message . ' </p></div></td></tr>';
                    }
                } else {
                    $class   = 'notice notice-error';

                    $message = __('Dynamic Menu is enabled but not effective.', 'dynamic-menu');

                    $url     = esc_url(home_url() . '/wp-admin/options-general.php?page=dynamic-menu-pro/plugin-authentication.php');

                    echo '<tr class="active"><td>&nbsp;</td><td colspan="2">

                    <div class="notice inline notice-warning notice-alt"><p> ' . $message . ' <a href="' . $url . '">You need to enter a license key.</a></p></div></td></tr>';
                }
            }

        }, 10, 3);

    }



}

} else {

    //add_action('admin_notices', 'dm_admin_notice__error');

}

/**
 * Show a notification message until the cache settings enabled.
 */
if (empty(get_option('dm_update_type'))) {
    add_action('admin_notices', 'dm_caching_acitvation_notice');


    function dm_caching_acitvation_notice()
    {

        $class   = 'notice notice-success is-dismissible';

        $message = __('You can enable dynamic menu caching for better performance. Please enable the dynamic menu settings ', 'dynamic-menu');

        $url     = esc_url(home_url() . '/wp-admin/admin.php?page=dm-settings');

        printf('<div class="%1$s"><p>%2$s <a href="' . $url . '">here.</a></p></div>', $class, $message);

    }
}