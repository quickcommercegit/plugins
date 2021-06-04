<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
?>

<div class="wc-any-shipping-tracking-code">
  <?php if ( ! empty( $tracking_codes ) ) : ?>
    <div class="wc-any-shipping-tracking-code__list">
      <strong><?php echo esc_html( _n( 'Código de rastreio:', 'Códigos de rastreio:', count( $tracking_codes ), 'wc-any-shipping-notify' ) ); ?></strong>
      <ul>
        <?php foreach ( $tracking_codes as $tracking_code => $shipping_company_slug ) : ?>
          <li><a href="<?php echo wc_any_shipping_notify_get_shipping_company_url( $shipping_company_slug, $tracking_code, $order ); ?>" target="_blank" aria-label="<?php esc_attr_e( 'Código de rastreio', 'wc-any-shipping-notify' ); ?>"><?php echo esc_html( $tracking_code ); ?></a> <a href="#" class="dashicons-dismiss" title="<?php esc_attr_e( 'Remover', 'wc-any-shipping-notify' ); ?>" aria-label="<?php esc_attr_e( 'Remover', 'wc-any-shipping-notify' ) ?>" data-code="<?php echo esc_attr( $tracking_code ); ?>"></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ( $available_companies ) { ?>
    <fieldset>
      <label for="wc-any-shipping-add-tracking-code-company"><?php esc_html_e( 'Empresa', 'wc-any-shipping-notify' ); ?></label>
      <select id="wc-any-shipping-add-tracking-code-company" name="wc-any-shipping-add-tracking-code-company">
        <?php foreach ( $available_companies as $company_key => $company ) {
          echo '<option value="' . $company_key . '">' . $company['name'] . '</option>';
        } ?>
      </select>

      <?php do_action( 'wcasn_form_fields', $post->ID ); ?>

      <label for="wc-any-shipping-add-tracking-code"><?php esc_html_e( 'Adicionar código de rastreio', 'wc-any-shipping-notify' ); ?></label>
      <input type="text" id="wc-any-shipping-add-tracking-code" name="shipping_company_tracking" value="" />
      <button type="button" class="button-secondary dashicons-plus" aria-label="<?php esc_attr_e( 'Novo código de rastreio', 'wc-any-shipping-notify' ); ?>"></button>
    </fieldset>
  <?php } else { ?>
    <div style="padding: 0 10px 15px;">
      <p>Não há transportadoras cadastradas.</p>
      <a class="button button-primary" href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=shipping&section=shipping_companies' ); ?>">Cadastrar</a>
    </div>
  <?php } ?>
</div>

<script type="text/html" id="tmpl-wc-any-shipping-tracking-code-list">
  <div class="wc-any-shipping-tracking-code__list">
    <# if ( 1 < data.trackingCodes.length ) { #>
      <strong><?php esc_html_e( 'Códigos de rastreio:', 'wc-any-shipping-notify' ); ?></strong>
    <# } else { #>
      <strong><?php esc_html_e( 'Código de rastreio:', 'wc-any-shipping-notify' ); ?></strong>
    <# } #>
    <ul>
      <# _.each( data.trackingCodes, function( trackingCode ) { #>
        <li><a href="{{trackingCode.url}}" target="_blank" aria-label="<?php esc_attr_e( 'Código de rastreio', 'wc-any-shipping-notify' ); ?>">{{trackingCode.code}}</a> <a href="#" class="dashicons-dismiss" title="<?php esc_attr_e( 'Remover', 'wc-any-shipping-notify' ) ?>" aria-label="<?php esc_attr_e( 'Remover', 'wc-any-shipping-notify' ) ?>" data-code="{{trackingCode}}"></a></li>
      <# }); #>
    </ul>
  </div>
</script>
