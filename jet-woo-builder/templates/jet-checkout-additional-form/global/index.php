<?php
/**
 * Checkout Additional Form Template
 */

$checkout    = WC()->checkout();
$settings    = $this->get_settings_for_display();
$heading     = $settings['checkout_additional_form_title_text'];
$label       = $settings['checkout_additional_form_label_text'];
$placeholder = $settings['checkout_additional_form_placeholder_text'];

if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>
	<div class="woocommerce-additional-fields">
		<?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>

		<?php if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' === get_option( 'woocommerce_enable_order_comments', 'yes' ) ) ) : ?>
			<h3><?php esc_html_e( $heading, 'jet-woo-builder' ); ?></h3>
			<div class="woocommerce-additional-fields__field-wrapper">
				<?php foreach ( $checkout->get_checkout_fields( 'order' ) as $key => $field ) : ?>
					<?php
					if ( ! empty( $label ) ) {
						$field['label'] = $label;
					}

					if ( ! empty( $placeholder ) ) {
						$field['placeholder'] = $placeholder;
					}
					?>
					<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>
	</div>
<?php endif;
