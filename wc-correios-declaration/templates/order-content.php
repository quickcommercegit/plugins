<div class="print-wrapper">
  <?php do_action( 'wc_correios_declaration_before_header' ); ?>

  <header>
    <?php if ( apply_filters( 'wc_correios_declaration_logo', false ) ) { ?>
      <div class="correios-logo">
        <img src="<?php echo apply_filters( 'wc_correios_declaration_logo_url', WC_Correios_Shipping_Declaration::plugin_dir_url() . 'assets/images/correios-logo.png' ); ?>" title="Correios" />
      </div>

      <div class="header-title">
        <h1>Declaração de Conteúdo</h1>
      </div>
    <?php } else { ?>
      <h1>Declaração de Conteúdo</h1>
    <?php } ?>
  </header>

  <?php do_action( 'wc_correios_declaration_after_header' ); ?>

  <section class="addresses">
    <div class="shipping from">
      <h3>Remetente</h3>
      <address>
        <?php if ( method_exists( WC()->countries, 'get_base_postcode' ) && method_exists( WC()->countries, 'get_base_address' ) ) {

          if ( has_filter( 'wc_correios_declaration_store_address' ) ) {

            $store_address = apply_filters(
             'wc_correios_declaration_store_address',
              sprintf(
                '<strong>%s</strong><br />
                %s<br />
                %s<br />
                %s<br />
                %s - %s
                ',
                apply_filters(
                  'wc_correios_declaration_store_name',
                  get_bloginfo( 'name' )
                ),
                WC()->countries->get_base_address(),
                WC()->countries->get_base_address_2(),
                WC()->countries->get_base_postcode(),
                WC()->countries->get_base_city(),
                WC()->countries->get_base_state()
              ),
              $order
            );

            echo $store_address;

          } else {
            $store_address = apply_filters( 'wc_correios_declaration_sender_address', array(
              'first_name'   => apply_filters( 'wc_correios_declaration_store_name', get_bloginfo( 'name' ) ),
              'last_name'    => '',
              'company'      => '',
              'billing_cpf'  => '',
              'address_1'    => WC()->countries->get_base_address(),
              'address_2'    => WC()->countries->get_base_address_2(),
              'city'         => WC()->countries->get_base_city(),
              'state'        => WC()->countries->get_base_state(),
              'postcode'     => WC()->countries->get_base_postcode(),
              'country'      => 'BR',
              'number'       => '',
              'neighborhood' => '',
            ) );

            $store_address = WC()->countries->get_formatted_address( $store_address );

            echo $store_address;
          }
        } else {

          if ( $store_address = apply_filters( 'wc_correios_declaration_store_address', false ) ) {
            echo $store_address;
          } else {
            $store_address = '<div style="background: #F9F9F9; padding: 1em 1em 1em 2em; border: 1px solid #a00; border-left: 4px solid #a00; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.125); clear: both;"><p>Este plugin requer pelo menos a versão 3.2 do WooCommerce para funcionar corretamente. Para não ser necessário atualizar você pode <a href="https://fernandoacosta.net/blog/docs/como-modificar-o-endereco-padrao-do-remetente/" target="_blank">seguir este passo a passo</a>.</p>';
          }
        } ?>
      </address>
    </div>

    <div class="shipping to">
      <h3>Destinatário</h3>
      <address>
        <?php if ( $order->get_formatted_shipping_address() ) {
          $order_address = '<p>' . wp_kses( $order->get_formatted_shipping_address(), array( 'br' => array() ) ) . '</p>';
        } elseif ( $order->get_formatted_billing_address() ) {
          $order_address = '<p>' . wp_kses( $order->get_formatted_billing_address(), array( 'br' => array() ) ) . '</p>';
        } else {
          $order_address = '<p class="none_set"><strong>Nenhum endereço de entrega foi encontrado</strong>.</p>';
        }

        $order_address = apply_filters( 'wc_correios_declaration_to_address', $order_address );

        echo $order_address;

        ?>
      </address>
    </div>
  </section>

  <?php if ( ! $show_price_column && $show_weight_column || $show_price_column && ! $show_weight_column ) { ?>
    <style type="text/css">
      .table .line-item .product-name {
        width: 74%;
      }

      @media screen {
        .table .line-item .product-name {
          width: 68%;
        }
      }
    </style>
  <?php } ?>

  <?php if ( ! $show_weight_column && ! $show_price_column ) { ?>
    <style type="text/css">
      .table .line-item .product-name {
        width: 87%;
      }

      .table .line-item .product-quantity {
        border-right: none;
      }

      @media screen {
        .table .line-item .product-name {
          width: 80%;
        }
      }
    </style>
  <?php } ?>


  <section class="table">
    <h3 class="title">Identificação dos bens</h3>

    <div class="line-item header">
      <div class="product-name">
        Discriminação do Conteúdo
      </div>
      <div class="product-quantity">
        Quantidade
      </div>
      <?php if ( $show_weight_column ) { ?>
        <div class="product-weight">
          Peso
        </div>
      <?php } ?>
      <?php if ( $show_price_column ) { ?>
        <div class="product-price">
          Preço
        </div>
      <?php } ?>

      <div class="line-options">
        Ocultar
      </div>
    </div>

    <?php do_action( 'wc_correios_declaration_before_items', $order ); ?>

    <?php
    $items = apply_filters( 'wc_correios_declaration_items', $order->get_items(), $order );
    foreach ( $items as $key => $item ) {
      $product = $item->get_product();
    ?>
      <div class="line-item">
        <div class="product-name" contenteditable="true">
          <?php echo apply_filters( 'wc_correios_declaration_product_name', $item->get_name(), $item, $order );

            if ( $item->get_variation_id() && apply_filters( 'wc_correios_declaration_product_name_attributes', true ) ) {
              $data = $item->get_formatted_meta_data();

              foreach ( $data as $key => $value ) {
                if ( false === strpos( $value->key, 'pa_' ) ) {
                  unset( $data[ $key ] );
                }

                else {
                  $data[ $key ] = $value->value;
                }
              }

              echo ' ' . implode( ' / ', $data );
            }
          ?>
        </div>
        <div class="product-quantity" contenteditable="true">
          <?php echo apply_filters( 'wc_correios_declaration_product_quantity', $quantity = $item->get_quantity(), $item, $order ); ?>
        </div>

        <?php if ( $show_weight_column ) { ?>
          <div class="product-weight" contenteditable="true">
            <?php $weight = method_exists( $product, 'get_weight' ) ? $product->get_weight() : 0;
              echo apply_filters( 'wc_correios_declaration_product_weight', 0 == ( $weight ) ? '-' : ( $weight * $quantity ) . get_option( 'woocommerce_weight_unit' ), $item, $order ); ?>
          </div>
        <?php } ?>

        <?php if ( $show_price_column ) { ?>
          <div class="product-price" contenteditable="true">
            <?php echo wc_price( $order->get_line_total( $item ) ); ?>
          </div>
        <?php } ?>

        <div class="line-options">
          <input type="checkbox" class="hide-column" />
        </div>
      </div>
    <?php } ?>

    <?php do_action( 'wc_correios_declaration_after_items', $order ); ?>

    <?php if ( $show_total ) { ?>
      <div class="line-item footer">
        <div class="order-total">
          Valor total
        </div>
        <div class="order-price" contenteditable="true">
          <?php echo apply_filters( 'wc_correios_declaration_total', wc_price( $order->get_subtotal() ), $order ); ?>
        </div>
      </div>
    <?php } ?>
  </section>

  <?php if ( apply_filters( 'wc_correios_declaration_text', true ) ) { ?>
    <section class="table disclaimer">
      <h3 class="title">Declaração</h3>
      <p><?php echo apply_filters( 'wc_correios_declaration_text', 'Declaro que não me enquadro no conceito de contribuinte previsto no art. 4º da Lei Complementar nº 87/1996, uma vez que não realizo, com habitualidade ou em volume que caracterize intuito comercial, operações de circulação de mercadoria, ainda que se iniciem no exterior, ou estou dispensado da emissão da nota fiscal por força da legislação tributária vigente, responsabilizando-me, nos termos da lei e a quem de direito, por informações inverídicas.' ) ?></p>
    </section>
  <?php } ?>

  <section class="table signature">
    <div class="date">
      <?php echo apply_filters( 'wc_correios_declaration_city', WC()->countries->get_base_city() ); ?>, <?php echo date_i18n( wc_date_format(), time() ); ?>
    </div>

    <div class="sign">
      <span class="label">Assinatura do declarante/remetente</span>
    </div>
  </section>

  <?php if ( apply_filters( 'wc_correios_declaration_observations', true ) ) { ?>
    <section class="table disclaimer">
      <h3 class="title">Observações</h3>
      <?php echo apply_filters( 'wc_correios_declaration_observations', '<p>Constitui crime contra a ordem tributária suprimir ou reduzir tributo, ou contribuição social e qualquer acessório (Lei 8.137/90 Art. 1º, V).</p>' ); ?>
    </section>
  <?php } ?>

  <?php if ( apply_filters( 'wc_correios_declaration_print_labels', false ) ) { ?>
    <div class="declaration-label">
      <div class="single-label" contenteditable="true">
        <div class="label-content destinatario">
          <h4>Destinatário</h4>
          <?php if ( apply_filters( 'wc_correios_declaration_destination_display_order_id', true ) ) { ?>
            <h4><?php echo sprintf( '#%s', $order->get_id() ); ?></h4>
          <?php } ?>
          <?php echo $order_address; ?>
        </div>

        <div class="label-content remetente">
          <h4>Remetente</h4>
          <?php echo $store_address; ?>
        </div>
      </div>
    </div>
  <?php } ?>
</div>
