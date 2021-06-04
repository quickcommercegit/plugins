<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
?>

<div class="shipping-companies-tracking-code">
  <small class="meta">
    <?php echo esc_html( _n( 'Código de rastreamento:', 'Códigos de rastreamento:', count( $tracking_codes ), 'wc-any-shipping-notify' ) ); ?>
    <?php echo implode( ' | ', $tracking_codes ); ?>
  </small>
</div>
