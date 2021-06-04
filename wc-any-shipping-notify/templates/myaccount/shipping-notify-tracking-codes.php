<h3><?php echo $title; ?></h3>

<table class="wc-any-shipping-notify-table woocommerce-table shop_table shop_table_responsive">
  <tbody>
  <tr>
    <th>Código de rastreio</th>
    <td>Ações</td>
  </tr>

  <?php foreach ( $codes as $details ) { ?>
    <tr>
      <th><?php echo $details['code']; ?></th>
      <td>
        <a class="tracking-button" href="<?php echo $details['url']; ?>" target="_blank"><?php echo apply_filters( 'wc_any_shipping_notify_myaccount_button', 'Acompanhar entrega', $details ) ?></a>
      </td>
    </tr>
  <?php } ?>
  </tbody>
</table>
