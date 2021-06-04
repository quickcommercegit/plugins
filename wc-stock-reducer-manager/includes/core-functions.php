<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function wc_stock_manager_cron_interval() {
  return ( 360 * MINUTE_IN_SECONDS );
}

function wc_stock_manager_log( $message ) {
  if ( 'yes' === get_option( 'wc_reduce_stock_manager_log', 'no' ) ) {
    $log = new WC_Logger();
    $log->add( 'wc-stock-reducer-manager', $message );
  }
}
