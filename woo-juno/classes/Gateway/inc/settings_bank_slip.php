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
		'default'     => __( 'Boleto Bancário', 'woo-juno' ),
		'desc_tip'    => true,
	],
	'description' => [
		'title'       => __( 'Descrição', 'woo-juno' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'Descrição que aparecerá na tela de Finalizar Compra (Checkout).', 'woo-juno' ),
		'default'     => __( 'Pague sua compra com boleto bancário.' ),
	],
	'instructions' => [
		'title'       => __( 'Instruções de Pagamento', 'woo-juno' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'Descrição que aparecerá na tela de agradecimento.', 'woo-juno' ),
		'default'     => __( 'Após o pagamento do boleto seu pedido será liberado.' ),
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
			'13'      => __( '13', 'woo-juno' ),
			'14'      => __( '14x', 'woo-juno' ),
			'15'      => __( '15x', 'woo-juno' ),
			'16'      => __( '16x', 'woo-juno' ),
			'17'      => __( '17x', 'woo-juno' ),
			'18'      => __( '18x', 'woo-juno' ),
			'19'      => __( '19x', 'woo-juno' ),
			'20'      => __( '20x', 'woo-juno' ),
			'21'      => __( '21x', 'woo-juno' ),
			'22'      => __( '22x', 'woo-juno' ),
			'23'      => __( '23x', 'woo-juno' ),
			'24'      => __( '24x', 'woo-juno' ),
		],
		'default'     => '1',
		'description' => __( 'Máximo de parcelas disponíveis', 'woo-juno' ),
	],
	'due_days' => [
		'title'       => __( 'Quantidade de dias para vencimento.', 'woo-juno' ),
		'type'        => 'text',
		'description' => __( 'Informe o número de dias que o cliente terá para pagar o boleto.', 'woo-juno' ),
		'default'     => '3'
	],
	'max_overdue_days' => [
		'title'       => __( 'Quantidade de dias para pagamento após o vencimento', 'woo-juno' ),
		'type'        => 'text',
		'description' => __( 'Número máximo de dias que o boleto poderá ser pago após o vencimento. Zero (0) significa que o boleto não poderá ser pago após o vencimento.', 'woo-juno' ),
		'default'     => '0'
	],
	'fine' => array(
		'title'       => __( 'Multa', 'woo-juno' ),
		'type'        => 'number',
		'description' => __( 'Multa para pagamento após o vencimento. Maior ou igual a 0,00 e menor ou igual a 2,00% (máximo permitido por lei).', 'woo-juno' ),
		'default'     => '0.0'
	),
	'interest' => [
		'title'       => __( 'Juros', 'woo-juno' ),
		'type'        => 'number',
		'description' => __( 'Juro para pagamento após o vencimento. Maior ou igual a 0,00 e menor ou igual a 1,00% (máximo permitido por lei).', 'woo-juno' ),
		'default'     => '0.0'
	],
	'debug' => [
		'title'       => __( 'Log de Depuração', 'woo-juno' ),
		'type'        => 'checkbox',
		'label'       => __( 'Habilitar', 'woo-juno' ),
		'default'     => 'no',
		'description' => sprintf( __( 'Os logs de eventos do Juno, assim como requisições de API, serão gravados neste arquivo %s', 'woo-juno' ), '<code>wp-content/uploads/wc-logs/' . $this->id . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.txt</code>' )
	],
];
