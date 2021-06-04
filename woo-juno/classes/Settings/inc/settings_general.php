<?php
$notifications_helper = 'yes' === get_option( 'woo_juno_has_v2_notifications', 'no' ) ? __( '<br />Você já criou notificações anteriormente. Ao clicar no botão acima elas serão substituídas.', 'woo-juno' )  : '';

return [
	'test_mode' => [
		'title'       => __( 'Ambiente Sandbox', 'woo-juno' ),
		'type'        => 'checkbox',
		'label'       => __( 'Habilitar', 'woo-juno' ),
		'default'     => 'no',
		'description' => __( 'No ambiente Sandbox você poderá simular pagamentos para testar vendas.<br><strong>Não esqueça de desativar essa opção quando sua loja estiver em produção</strong>.', 'woo-juno' )
	],
	'api_version' => [
		'title'       => __( 'Versão da API', 'woo-juno' ),
		'type'        => 'select',
		'default'     => 1,
		'description' => __( 'A versão 2.0 da API possui mais recursos e estabilidade. <br />Ao escolher a opção você deverá preencher novos dados de configuração e ao clicar em salvar as configurações, será gerado automaticamente a URL de notificação. Caso dê falha na geração dessa URL, será apresentado uma mensagem de erro.', 'woo-juno' ),
		'options'     => array(
			1 => 'Versão 1',
			2 => 'Versão 2'
		)
	],
	'client_id' => [
		'title'       => __( 'Client ID', 'woo-juno' ),
		'type'        => 'text',
		'description' => __( 'Por favor, insira seu Client ID da Juno. Isto é necessário para acessar a API com segurança. Você pode gerá-lo no painel da sua conta, na aba Integração ou clicando <a href="https://app.juno.com.br/#/integration" target="_blank"> aqui</a>.', 'woo-juno' ),
		'default'     => ''
	],
	'client_secret' => [
		'title'       => __( 'Client Secret', 'woo-juno' ),
		'type'        => 'text',
		'description' => __( 'Por favor, insira seu Client Secret da Juno.', 'woo-juno' ),
		'default'     => ''
	],
	'token' => array(
		'title'       => __( 'Token Privado', 'woo-juno' ),
		'type'        => 'text',
		'description' => __( 'Por favor, insira seu Token (Privado) do Juno. Isto é necessário para processo de pagamento.<br>Você pode gerar seu Token realizando login na Juno e ir no menu Integração ou clicando <a href="https://app.juno.com.br/#/integration" target="_blank"> aqui</a>.', 'woo-juno' ),
		'default'     => ''
	),
	'sandbox_token' => [
		'title'       => __( 'Token Privado (Sandbox)', 'woo-juno' ),
		'type'        => 'text',
		'description' => __( 'Por favor, insira seu Token (Privado) do Juno. Isto é necessário para processo de pagamento.<br>Você pode gerar seu Token realizando login no ambiente Sandbox da Juno e ir no menu Integração ou clicando <a href="https://app.juno.com.br/#/integration" target="_blank">aqui</a>.', 'woo-juno' ),
		'default'     => ''
	],
	'public_token' => [
		'title'       => __( 'Token Público', 'woo-juno' ),
		'type'        => 'text',
		'description' => __( 'Por favor, insira seu Token (Público) da Juno. Isto é necessário para processo de pagamento.<br>Você pode gerar seu Token realizando login na Juno e ir no menu Integração ou clicando <a href="https://app.juno.com.br/#/integration" target="_blank">aqui</a>.', 'woo-juno' ),
		'default'     => ''
	],
	'widget_balance_enabled' => [
		'title'       => __( 'Widget de Saldo', 'woo-juno' ),
		'type'        => 'checkbox',
		'label'       => __( 'Habilitar', 'woo-juno' ),
 		'description' => __( 'Mostra um widget na página Painel onde o dono da loja poderá conferir seu saldo na Juno e fazer solicitações de transfência. Esse widget só é visível para usuários com função Administrador ou Gerente de Loja.', 'woo-juno' ),
		'default'     => 'yes'
	],
	'notify_payer' => [
		'title'       => __( 'Notificar comprador por email', 'woo-juno' ),
		'type'        => 'checkbox',
		'label'       => __( 'Notificar', 'woo-juno' ),
		'description' => __( 'Define se o Juno enviará emails de notificação sobre a cobrança para o comprador. O email com o boleto só será enviado ao comprador, se esta opção estiver marcada. O lembrete de vencimento também só será enviado se está opção estiver marcada.', 'woo-juno' ),
		'default'     => 'yes'
	],
	// 'notifications' => [
	// 	'title'        => __( 'Notificações', 'woo-juno' ),
	// 	'type'         => 'button',
	// 	'label'        => __( 'Criar notificações', 'woo-juno' ),
 // 		'description'  => __( 'É necessário registrar as notificações Juno na versão 2 da API. <strong>Salve seus dados antes de executar esta ação</strong>.', 'woo-juno' ) . $notifications_helper,
 // 		'button_class' => 'juno-generate-notifications'
	// ],
	'notifications_log' => [
		'title'       => __( 'Log de notificações', 'woo-juno' ),
		'type'        => 'checkbox',
		'label'       => __( 'Habilitar', 'woo-juno' ),
 		'description' => __( 'Registrar os eventos de notificação da API. Apenas para a versão 2 da API.', 'woo-juno' ),
		'default'     => 'no'
	],
];
