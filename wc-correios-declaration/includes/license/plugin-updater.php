<?php
if( ! class_exists( 'FA_WC_Plugin_Updater' ) ) {
  include_once 'api.php';
}

if ( ! function_exists( 'get_plugin_data' ) ) {
  include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

$plugin_data = get_plugin_data( $this->plugin_file );

$fa_updater = new FA_WC_Plugin_Updater( $this->store_url,
  $this->plugin,
  array(
  'version'   => $plugin_data['Version'],   // current version number
  'license'   => $this->license_key,  // license key (used get_option above to retrieve from DB)
  'item_id'   => $this->product_id,  // id of this plugin
  'beta'      => false // set to true if you wish customers to receive update notifications of beta releases
) );
