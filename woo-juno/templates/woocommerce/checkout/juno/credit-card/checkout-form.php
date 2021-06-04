<fieldset id="<?= esc_attr( $id ); ?>-form" class="wc-payment-form" data-cart-total="<?= esc_attr( \json_encode( $installments ) ); ?>">
	<div class="payment-method-description">
		<?= $description; ?>
	</div>
	<div class="fields-wrapper">
		<?php
		// os campos de cartão de crédito são criados no client-side pelo javascript
		// veja o arquivo: assets/src/js/checkout-credit-card.js
		?>
	</div>
</fieldset>
