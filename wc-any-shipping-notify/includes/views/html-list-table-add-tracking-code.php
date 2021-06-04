<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
?>

<?php if ( $available_companies ) { ?>
  <div class="list-table-new-tracking-code no-link">
    <label for="wc-any-shipping-add-tracking-code-company-<?php echo $order_id; ?>"><?php esc_html_e( 'Empresa', 'wc-any-shipping-notify' ); ?></label>
    <select
      id="wc-any-shipping-add-tracking-code-company-<?php echo $order_id; ?>"
      class="wc-any-shipping-add-tracking-code-company">
      <?php foreach ( $available_companies as $company_key => $company ) {
        echo '<option value="' . $company_key . '">' . $company['name'] . '</option>';
      } ?>
    </select>
    <label for="wc-any-shipping-add-tracking-code-<?php echo $order_id; ?>"><?php esc_html_e( 'Código de rastreio', 'wc-any-shipping-notify' ); ?></label>
    <input
      type="text"
      id="wc-any-shipping-add-tracking-code-<?php echo $order_id; ?>"
      class="wc-any-shipping-add-tracking-code" />

    <button type="button" data-order-id="<?php echo $order_id; ?>" class="wc-any-shipping-notify-list-button button-secondary dashicons-plus" aria-label="<?php esc_attr_e( 'Novo código de rastreio', 'wc-any-shipping-notify' ); ?>"></button>
  </div>
<?php } ?>
