=== Juno para WooCommerce ===
Contributors: marcofrasson, amgnando, luizbills, luismatias
Donate link: https://juno.com.br
Tags: boleto, boleto bancario, cartão de crédito, gateway, pagamento, woocommerce, assinaturas, subscriptions
Requires at least: 5.0
Tested up to: 5.7
Stable tag: trunk
Requires PHP: 7.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Emita cobranças via cartão de crédito e boleto bancário de forma integrada com o seu e-commerce.

== Description ==

Com o plugin da Juno, você pode emitir cobranças, via cartão de crédito e boleto bancário, de forma integrada com a sua loja no WooCommerce!

Funciona assim: seu cliente faz o pagamento no checkout da sua loja e o valor aparece no painel da sua Conta Juno. Depois disso é possível realizar transferências para qualquer conta bancária de sua escolha.

Veja a [demonstração](https://goflow.digital/demo/juno/) com o tema Storefront.

Saiba todas as vantagens de utilizar o Juno para WooCommerce:

- Checkout 100% transparente
- Checkout com redirecionamento (somente v2 da API)
- Widget no admin com o saldo da sua conta na Juno
- Cartão de Crédito
 - Efeito visual para o cliente
 - Possibilidade do cliente salvar o cartão para compras futuras
 - Configure a quantidade de parcelas disponíveis
 - Configure o título e descrição do meio de pagamento
 - Configure valor de parcela mínima (somente v2 da API)
 - Configure repasse de juros para o cliente (somente v2 da API)
 - Informações do pagamento nas notas do pedido (somente v2 da API)
 - Integrado ao WooCommerce Subscriptions (somente v2 da API)
- Boleto Bancário
 - Disponibilidade de uma página de agradecimento com código de barras visual e linha digitável para o cliente
 - Gerar novo boleto para o cliente (somente v2 da API)
 - Link e código do boleto nas notas do pedido (somente v2 da API)
 - Configure boleto à vista ou parcelado (o status do pedido é alterado com o primeiro boleto pago)
 - Configure a quantidade de parcelas disponíveis
 - Configure vencimento, multa, juros e demais detalhes do boleto
 - Configure o título e descrição do meio de pagamento
 - Integrado ao WooCommerce Subscriptions (somente v2 da API)
- Reembolso nativo do WooCommerce para cartão (somente v2 da API)
- Pagamentos recorrentes e assinaturas com o WooCommerce Subscriptions para cartão e boleto (somente v2 da API)

Para saber como instalar o Juno para WooCommerce, confira a aba [Installation](http://wordpress.org/extend/plugins/woo-juno/installation/).

= Sobre a Juno =

A Juno vai ajudar você a cuidar da saúde financeira do seu negócio. Você pode emitir e receber cobranças por boleto e cartão de crédito, gerenciar clientes e ainda pagar suas contas com o dinheiro que receber.

Conte com a solução completa para emitir cobranças e receber pagamentos, fácil e sem burocracia. A Juno é pra todo mundo: MEIs, e-commerces, marketplaces, empresas de qualquer tamanho.

Conheça a [Juno](https://juno.com.br/)!

= Instalação =

Confira o nosso guia de instalação e configuração na aba [Installation](http://wordpress.org/extend/plugins/woo-juno/installation/).

= Compatibilidade =

Requer WooCommerce 3.0 ou posterior para funcionar.
Requer Brazilian Market on Woocommerce para funcionar.
Para assinaturas requer o WooCommerce Subscriptions para funcionar.

= Dúvidas? =

Você pode esclarecer suas dúvidas usando:

- A nossa sessão de [FAQ](http://wordpress.org/extend/plugins/woo-juno/faq/);
- Criando um tópico no [fórum de ajuda do WordPress](http://wordpress.org/support/plugin/woo-juno);
- Você pode entrar em contato com a gente pelo nosso chat no [site](https://juno.com.br/contato/) ou telefone 41 3013-9650.

== Installation ==

[Vídeo tutorial de instalação.](https://www.youtube.com/watch?v=P4CaVsjR9T0)

Para instalar o nosso plugin são apenas 2 passos simples:

- Envie os arquivos do plugin para a pasta wp-content/plugins, ou instale usando o instalador de plugins do WordPress.
- Ative o plugin.

= Como fazer a ativação =

Na Juno

- Crie sua Conta Juno em app.juno.com.br, é grátis e fácil!
- Gere o token privado, que será a sua chave de acesso para identificar seu usuário;
- Gere o token público, que é a chave de criptografia para uso de cartão de crédito;
- Gerar o Cliente ID e Secret dentro da dashboard da Juno.
- Para fazer uma conta de testes, o token privado e o token público  precisam ser criados em sandbox.juno.com.br

No WooCommerce

- Configure o plugin na aba WooCommerce > Configurações > Integração > Juno;
- Selecione a versão 2.0;
- Insira suas chaves (tokens privado e público);
- Na Juno, temos o ambiente para testes, chamado Sandbox, e o ambiente de Produção, que é o definitivo. Se você estiver com os tokens de produção (feitos em app.juno.com.br), precisa desmarcar a caixinha de sandbox no Wordpress, para começar a vender!
- Você encontra o passo a passo para gerar as [credenciais aqui](https://www.youtube.com/watch?v=P4CaVsjR9T0).

= Requerimentos: =

Para garantir a compatibilidade do seu e-commerce com o plugin da Juno, você precisa dos seguintes requerimentos:

- Requer WooCommerce 3.0 ou posterior para funcionar.
- Requer Brazilian Market on Woocommerce para funcionar.
- PHP 7.0 ou superior.

= Configurações do plugin: =

Para configurar as chaves de integração da Juno, acesse a aba do "WooCommerce" > "Configurações" > "Integração" > "Juno"

== Frequently Asked Questions ==

= Qual é a licença do plugin? =

Este plugin esta licenciado como GPL.

= O que eu preciso para utilizar este plugin? =

* WooCommerce 3.0 ou posterior.
* Brazilian Market on Woocommerce.
* Para assianturas, é necessário o WooCommerce Subscriptions.

= Funciona com pagamento recorrentes e assinaturas? =

Sim, o plugin da Juno tem integração com o WooCommerce Subscriptions para pagamento via cartão de crédito ou boleto bancário.

= Consigo usar o ambiente sandbox =

Sim, nas configurações de integração você poderá selecionar o sandbox em "WooCommerce" > "Configurações" > "Integração" > "Juno"

= Onde posso gerar meu token v2 da Juno? =

Você encontra o passo a passo para gerar as [credenciais aqui](https://www.youtube.com/watch?v=P4CaVsjR9T0).

= Consigo remover o widget de saldo da conta Juno? =

Sim, você pode altera nas configurações de integração em "WooCommerce" > "Configurações" > "Integração" > "Juno"

= Quais usuários administrativos verão o widget de saldo? =

Os usuários que poderão visualizar o widget de saldo da conta Juno são os administradores e gerentes de loja.

= Consigo habilitar somente o cartão de crédito ou boleto? =

Sim, você poderá habilitar os dois meios de pagamento ou apenas um deles, na configuração padrão do WooCommerce em "WooCommerce" > "Configurações" > "Pagamentos"

= Possui mais dúvidas sobre a Juno? =

Você poderá entrar em contato diretamente conosco pelo [nosso site](https://juno.com.br/contato/) ou telefone 41 3013-9650.

== Screenshots ==

1. Checkout transparente para cartão de crédito com efeito visual.
2. Possibilidade do cliente selecionar cartão salvo no checkout.
3. Boleto bancário à vista ou parcelado.
4. Página de agradecimento com código de barras visual e linha digitável para o cliente.
5. Página de configuração da integração com a Juno.
6. Configuração dos meios de pagamento.
7. Página de configuração do boleto bancário da Juno.
8. Página de configuração do cartão de crédito da Juno.
9. Widget no admin com o saldo da sua conta na Juno.

== Changelog ==

= 2.3.1 - 2021/05/13 =

- Prevenir erro com nomes longos no Pix
- Remover parâmetros desnecessários da requisição Pix

= 2.3.0 - 2021/05/09 =

- Integração para receber pagamentos via Pix.
- Prevenir erros fatais em falhas de pagamento de assinaturas

= 2.2.1 - 2021/03/29 =

- Desativar parcelamento em novas assinaturas

- Ajuste na troca de métodos de pagamento em assinaturas (boleto e cartão)

= 2.2.0 - 2021/03/13 =

- Ajuste na troca de métodos de pagamento em assinaturas (boleto e cartão)

= 2.1.5 - 2021/03/08 =

- Prevenir boletos com vencimento sábado e domingo
- Exibição da taxa da Juno para compras via cartão (novos pedidos)

= 2.1.4 - 2021/02/10 =

- Prevenir erro ao salvar client ID e client secret com caracteres especiais

= 2.1.3 - 2021/02/08 =

- Melhoria na validação de datas de vencimento do cartão de crédito
- Salvar nota quando houver mais de falha de pagamento no mesmo pedido

= 2.1.2 - 2021/01/11 =

- Correção na ação de gerar boleto pelo painel quando WooCommerce Subscriptions está ativo

= 2.1.1 - 2021/01/06 =

- Ajuste no reembolso para cartão de crédito

= 2.1.0 - 2020/12/25 🎅 =

- NOVO: integração com WooCommerce Subscriptions
- Adicionar bandeira do cartão às notas do pedido
- Exibir forma de pagamento na listagem de pedidos
- Melhorando a tratativa de erros
- Prevenir que o número de parcelas volte a 1 após erro
- Ajuste nas configurações de antecipação
- Enviar apenas números de documento na API
- Permitir colar dados do cartão de crédito
- Cancelar cobrança duplicadas de um mesmo pedido
- Corrigir exibição do link para boletos gerados pelo painel

= 2.0.4 - 2020/08/28 =

- Correções de bugs.

= 2.0.3 - 2020/08/24 =

- Correções de bugs.

= 2.0.2 - 2020/08/19 =

- Correções de bugs.

= 2.0.1 - 2020/06/22 =

- Corrigido pagamento no endpoint de pagamento pendente.

- Corrigido nome do método de pagamento dentro do WooCommerce.

- Corrigido função para ativar e desativar o "salvar cartão".

- Corrigido status para pagamentos de produtos digitais.

- Corrigido status de falha de pagamento.

- Corrigido campos do checkout sem cartão virtual.

- Novo: adicionado validação no campos do checkout sem cartão virtual.

- Novo: ocultar métodos de pagamento no admin ao selecionar checkout de redirecionamento.

- Melhorias de CSS no cartão virtual para mobile.

= 2.0.0 - 2020/04/29 =

- Adicionado integração com API v2 - solicite suas credenciais para o email implantacao@juno.com.br

Com a v2 ativa, os recursos abaixam foram adicionados:

- Adicionado informações da transação nas notas do pedido (pagamento feito, parcelas, URL de pagamento, falhas na transação, etc.).

- Adicionado possibilidade de repassar juros por parcelas para o cliente final.

- Adicionado reembolso total e parcial dentro do WooCommerce (para ter reembolso parcial na sua conta Juno, entre em contato com eles solicitando liberação).

- Adicionado o admin criar um novo boleto do pedido para o cliente (Pedido -> Ações -> Boleto Juno) (é disparado um email para o cliente com o novo boleto, ideal para prazos vencidos).

- Adicionado opção para checkout de redirecionamento.

- Adicionado parcela mínima (se for R$ 50, a compra de R$ 120,00 vai permitir até 2x apenas) e (só funciona para checkout transparente).

- Adicionado ao checkout transparente possibilidade de preencher o ano completo ou o final, exemplo: 2024 ou 24.

- Adicionado retorno de mensagens com falha na transação para o cliente final no checkout.

E correções pontuais:

- Corrigido bug ao acessar endpoint de pagar um pedido já criado.

- Não deleta mais o pedido do WooCommerce quando o mesmo dá falha de pagamento.

= 1.0.8 - 2019/12/04 =

- Pequenos ajustes.

= 1.0.7 - 2019/12/02 =

- Pequenos ajustes.

= 1.0.6 - 2019/12/02 =

- Corrigido o parâmetro responsável pela data de vencimento do boleto.
- Pequenos ajustes.

= 1.0.5 - 2019/11/19 =

- Pequenos ajustes para cartão virtual desabilitado.

= 1.0.4 - 2019/11/07 =

- Adicionado parâmetro para exibir linha digitável.
- Alterado dizeres de "imprimir" para "visualizar" boleto na tela thankyou.
- Corrigido código da linha digitável.

= 1.0.3 - 2019/08/29 =

- Corrigido CSS no mobile para código do boleto.
- Adicionado parâmetro na consulta da API.

= 1.0.2 - 2019/07/10 =

- Corrigido pequenos bugs com plugin Extra Checkout Fields for Brazil.
- Corrigido CSS no mobile para cartão visual.

= 1.0.1 - 2019/07/08 =

- Pequenos bug fixes.

= 1.0.0 - 2019/07/08 =

- Lançamento do plugin.

== Upgrade Notice ==

= 1.0.0 - 2019/07/08 =

- Lançamento do plugin.

