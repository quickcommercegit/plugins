<?php
use WC_Juno\functions as h;
?>

<section id="<?= esc_attr( $id ); ?>-thankyou">
	<h2><?= esc_html__( 'Seu boleto foi gerado com sucesso', 'woo-juno' ); ?></h2>

	<div class="instruction">
		<?= $instructions; ?>

		<p>Seu <?= $has_installments ? 'primeiro' : '' ?> boleto vai vencer no dia <?= esc_html( $due_date ); ?>.</p>

		<p><?= esc_html__( 'Abaixo você poderá imprimir seu boleto ou copiar o código de barras para facilitar o seu pagamento.', 'woo-juno' ); ?></p>
	</div>

	<div class="<?= esc_attr( $id ); ?>-barcode">
		<div class="barcode-image">
			<?= h\generate_barcode( $barcode, 2, 45, '#202020' ); ?>
		</div>
		<div class="barcode-code">
			<span id="juno-barcode"><?= esc_html( $pay_number ); ?></span>
			<button type="button" class="button button-copy" data-clipboard-target="#juno-barcode"><?= esc_html__( 'Copiar' ) ?></button>
		</div>
	</div>

	<div class="<?= esc_attr( $id ); ?>-print-buttons">
		<a class="button first-installment" href="<?= ( $first_billet ); ?>" target="_blank" rel="nofollow noopener"><?= esc_html__( 'Visualizar Boleto', 'woo-juno' ); ?></a>

		<?php if ( $has_installments ) : ?>
			<a class="button all-installments" href="<?= ( $all_billets ); ?>" target="_blank" rel="nofollow noopener"><?= esc_html__( 'Visualizar Carnê', 'woo-juno' ); ?></a>
		<?php endif; ?>
	</div>
</section>
