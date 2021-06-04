<?php

return [
	'enabled' => [
		'title'   => __( 'Habilitar/Desabilitar Pix', 'woo-juno' ),
		'type'    => 'checkbox',
		'label'   => __( 'Habilitar', 'woo-juno' ),
		'default' => 'no'
	],
	'title' => [
		'title'       => __( 'Título', 'woo-juno' ),
		'type'        => 'text',
		'description' => __( 'Nome da forma de pagamento que aparecerá na tela de Finalizar Compra (Checkout).', 'woo-juno' ),
		'default'     => __( 'Pix', 'woo-juno' ),
		'desc_tip'    => true,
	],
	'description' => [
		'title'       => __( 'Descrição', 'woo-juno' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'Descrição que aparecerá na tela de Finalizar Compra (Checkout).', 'woo-juno' ),
		'default'     => __( 'Pague com QR Code usando Pix.' ),
	],
	'instructions' => [
		'title'       => __( 'Instruções de Pagamento', 'woo-juno' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'Descrição que aparecerá na tela de agradecimento.', 'woo-juno' ),
		'default'     => __( 'Faça o pagamento com QR Code no app do seu banco.' ),
	],
	'pix_key' => [
		'title'       => __( 'Chave Pix', 'woo-juno' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'Sua chave aleatória cadastrada na Juno.', 'woo-juno' ),
	],
	'expiration' => [
		'title'       => __( 'Expiração', 'woo-juno' ),
		'type'        => 'number',
		'desc_tip'    => true,
		'description' => __( 'Tempo máximo para a realização do pagamento, em minutos. Padrão 1440 (24 horas)', 'woo-juno' ),
		'default'     => '1440',
	],
	'request_to_payer' => [
		'title'       => __( 'Solicitação ao pagador', 'woo-juno' ),
		'type'        => 'text',
		'desc_tip'    => true,
		'description' => __( 'Pode ser utilizado para descrever o motivo da emissão do QR Code. Utilize {order_id} para exibir o número do pedido.', 'woo-juno' ),
		'default'     => __( 'Faça o pagamento do pedido #{order_id} com QR Code na sua carteira Pix.' ),
	],
	'debug' => [
		'title'       => __( 'Log de Depuração', 'woo-juno' ),
		'type'        => 'checkbox',
		'label'       => __( 'Habilitar', 'woo-juno' ),
		'default'     => 'no',
		'description' => sprintf( __( 'Os logs de eventos do Juno, assim como requisições de API, serão gravados neste arquivo %s', 'woo-juno' ), '<code>wp-content/uploads/wc-logs/' . $this->id . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.txt</code>' )
	],
];
