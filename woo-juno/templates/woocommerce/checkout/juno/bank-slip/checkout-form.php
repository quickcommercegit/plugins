<fieldset id="<?= esc_attr( $id ); ?>-form" class="wc-payment-form">
	<div class="payment-method-description">
		<?= $description; ?>
	</div>

	<?php if ( empty( $installments ) ) : ?>
		<input type="hidden" name="<?= esc_attr( $id ); ?>-installments" value="1">
	<?php else : ?>
		<section class="wc-payment-form-fields">
			<p class="form-row form-row-wide">
				<label for="<?= esc_attr( $id ); ?>-installments">
					<?= esc_html__( 'Número de parcelas' ); ?>
				</label>
				<select id="<?= esc_attr( $id ); ?>-installments" name="<?= esc_attr( $id ); ?>-installments" class="input-select">
					<?php foreach ( $installments as $value => $label ) : ?>
						<option value="<?= esc_attr( $value ); ?>">
							<?= $label; ?>
						</option>
					<?php endforeach; ?>
				</select>
			</p>
		</section>
	<?php endif; ?>
</fieldset>

