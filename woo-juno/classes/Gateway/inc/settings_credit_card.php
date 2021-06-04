<?php

return [
	'enabled' => [
		'title'   => __( 'Habilitar/Desabilitar', 'woo-juno' ),
		'type'    => 'checkbox',
		'label'   => __( 'Habilitar', 'woo-juno' ),
		'default' => 'yes'
	],
	'title' => [
		'title'       => __( 'Título', 'woo-juno' ),
		'type'        => 'text',
		'description' => __( 'Nome da forma de pagamento que aparecerá na tela de Finalizar Compra (Checkout).', 'woo-juno' ),
		'default'     => __( 'Cartão de Crédito', 'woo-juno' ),
		'desc_tip'    => true,
	],
	'description' => [
		'title'       => __( 'Descrição', 'woo-juno' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'Descrição que aparecerá na tela de Finalizar Compra (Checkout).', 'woo-juno' ),
		'default'     => __( 'Pague sua compra com cartão de crédito.' ),
	],
	'integration_method' => [
		'title'       => __( 'Método de integração', 'woo-juno' ),
		'type'        => 'select',
		'description' => __( 'Escolha como seu cliente vai interagir com a Juno: diretamente da sua loja (de forma transparente) ou com redirecionamento. Se optar por redirecionamento, é necessáiro configurar a URL de notificações no painel Juno em Integração -> Notificações de pagamento com a seguinte URL:', 'woo-juno' ) . ' <code>' . WC()->api_request_url( 'woo_juno_notifications' ) . '</code>',
		'default'     => 'direct',
		'class'       => 'wc-enhanced-select',
		'options'     => [
			'direct' => __( 'Dentro da sua loja (transparente)', 'woo-juno' ),
			'redirect'    => __( 'Redirecionar para o ambiente seguro Juno', 'woo-juno' ),
		],
	],
	'show_visual_card' => [
		'title'       => __( 'Mostrar cartão virtual', 'woo-juno' ),
		'type'        => 'checkbox',
		'label'       => __( 'Habilitar', 'woo-juno' ),
		'default'     => 'yes',
		'description' => __( 'Essa opção quando habilitada, mostrará um cartão de crédito para auxiliar no preenchimento dos dados do cartão do cliente.', 'woo-juno' ),
	],
	'store_user_cards' => [
		'title'       => __( 'Guardar cartão de crédito do cliente', 'woo-juno' ),
		'type'        => 'checkbox',
		'label'       => __( 'Habilitar', 'woo-juno' ),
		'default'     => 'yes',
		'description' => __( 'Essa opção quando habilitada, permitirá que a loja armazene informações do cartão de crédito do cliente para agilizar o pagamento de futuras compras. <br>A loja vai armazenar apenas os 4 últimos digitos e a bandeira do cartão. O restante ficará armazenado nos servidores da Juno.', 'woo-juno' ),
	],
	'max_installments' => [
		'title'       => __( 'Número Máximo de Parcelas', 'woo-juno' ),
		'type'        => 'select',
		'options'     => [
			'1'       => __( 'Sem parcelamento', 'woo-juno' ),
			'2'       => __( '2x', 'woo-juno' ),
			'3'       => __( '3x', 'woo-juno' ),
			'4'       => __( '4x', 'woo-juno' ),
			'5'       => __( '5x', 'woo-juno' ),
			'6'       => __( '6x', 'woo-juno' ),
			'7'       => __( '7x', 'woo-juno' ),
			'8'       => __( '8x', 'woo-juno' ),
			'9'       => __( '9x', 'woo-juno' ),
			'10'      => __( '10x', 'woo-juno' ),
			'11'      => __( '11x', 'woo-juno' ),
			'12'      => __( '12x', 'woo-juno' ),
		],
		'default'     => '1',
		'description' => __( 'Máximo de parcelas disponíveis', 'woo-juno' ),
	],
	'smallest_installment' => [
		'title'             => __( 'Parcela mínima', 'woo-juno' ),
		'type'              => 'number',
		'default'           => '5',
		'custom_attributes' => array(
			'min' => 5
		),
		'description'       => __( 'Valor mínimo das parcelas.', 'woo-juno' ),
	],
	'payment_advance' => [
		'title'        => 'Antecipar parcelas',
		'type'        => 'checkbox',
		'label'       => 'Habilitar antecipação das parcelas',
		'default'     => '',
		'description' => __( 'Se você escolher antecipar as parcelas das cobranças, haverá cobrança de <a href="https://juno.com.br/quanto-custa.html" target="_blank">taxas extras</a> ou acesse sua conta Juno no menu "Configurações" > "Taxas" para descobrir as taxas da sua conta.', 'woo-juno' ),
	],
  'installments_fee_header' => array(
    'title'        => __( 'Parcelamento', 'woo-juno' ),
    'type'         => 'title',
    'description'  => '<div id="installments_fee_header">' . __( 'Por padrão, o parcelamento é exibido sem juros. Na tabela abaixo você pode definir os juros em porcentagem por parcela. Cada parcela terá os juros totais que você irá repassar (ex.: se você quiser adicionar 1% por parcela acima de 3x, irá adicionar 4% para 4x, 5% para 5x, 6% para 6x e assim por diante). Caso as outras parcelas estejam em branco, serão consideradas sem juros.', 'woo-juno' ) . '</div>'
  ),
  'installments_fee' => array(
    'type' => 'installments_table',
    'title' => __( 'Parcelas', 'woo-juno' ),
  ),
	'debug' => [
		'title'       => __( 'Log de Depuração', 'woo-juno' ),
		'type'        => 'checkbox',
		'label'       => __( 'Habilitar', 'woo-juno' ),
		'default'     => 'no',
		'description' => sprintf( __( 'Os logs de eventos do Juno, assim como requisições de API, serão gravados neste arquivo %s', 'woo-juno' ), '<code>wp-content/uploads/wc-logs/' . $this->id . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.txt</code>' )
	],
];
