<?php

use WC_Juno\functions as h;

?>
<div class="updated woocommerce-message inline">
	<p>
		<?= wp_kses_post( __( 'Você ainda não configurou nenhum <strong>Token de Pagamento</strong>. Por favor, acesse a página de configurações gerais do Juno e adicione seus tokens.', 'woo-juno' ) ); ?>
	</p>
	<p class="submit">
		<a class="button-secondary docs" href="<?= esc_url( admin_url( 'admin.php?page=wc-settings&tab=integration&section=' . h\get_juno_integration_id() ) );?>" target="_blank">
			<?= esc_html__( 'Página de Configuração', 'woo-juno' ); ?>
		</a>
	</p>
</div>
