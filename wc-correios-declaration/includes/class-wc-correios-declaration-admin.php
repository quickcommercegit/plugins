<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class WC_Correios_Shipping_Declaration_Admin {

  function __construct() {
    add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'print_declaration' ) );
    add_action( 'wp_ajax_print_declaration', array( $this, 'page' ) );
    add_action( 'woocommerce_admin_order_actions', array( $this, 'action_print_declaration' ), 10, 2 );
    add_action( 'admin_head', array( $this, 'action_print_declaration_css' ) );

    add_filter( 'bulk_actions-edit-shop_order', array( $this, 'shop_order_bulk_actions' ), 20 );
    add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'handle_shop_order_bulk_actions' ), 20, 3 );
    add_action( 'admin_notices', array( $this, 'bulk_admin_notices' ) );
  }

  public function print_declaration( $order ) {
    $url = add_query_arg( array( 'action' => 'print_declaration', 'order_ids' => $order->get_id() ), admin_url( 'admin-ajax.php' ) );
    echo '<a href="' . $url . '" class="button" target="_blank" style="margin-top: 15px;">Imprimir declaração de conteúdo</a>';
  }


  public function action_print_declaration( $actions, $order ) {
    $actions['print_declaration'] = array(
      'url'       => add_query_arg( array( 'action' => 'print_declaration', 'order_ids' => $order->get_id() ), admin_url( 'admin-ajax.php' ) ),
      'name'      => __( 'Imprimir Declaração', 'woocommerce' ),
      'action'    => "print_declaration",
    );

    return $actions;
  }

  public function action_print_declaration_css() {
    echo '<style type="text/css">
      .widefat .column-order_actions a.print_declaration,
      .post-type-shop_order .wp-list-table .column-wc_actions a.button.print_declaration {
        display: inline-block;
        text-indent: 9999px;
        position: relative;
        padding: 0!important;
        height: 2em!important;
        width: 2em;
      }

      .order_actions .print_declaration::after,
      .post-type-shop_order .wp-list-table .column-wc_actions a.button.print_declaration::after {
        font-family: Dashicons;
        speak: none;
        font-weight: 400;
        font-variant: normal;
        text-transform: none;
        line-height: 1;
        -webkit-font-smoothing: antialiased;
        margin: 0;
        text-indent: 0;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        text-align: center;
        content: "\f210";
        line-height: 1.85;
      }
    </style>';
  }


  public function shop_order_bulk_actions( $actions ) {
    $actions['print_declaration'] = 'Imprimir declarações';

    return $actions;
  }

  /**
   * Handle shop order bulk actions.
   *
   * @since  3.0.0
   * @param  string $redirect_to URL to redirect to.
   * @param  string $action      Action name.
   * @param  array  $ids         List of ids.
   * @return string
   */
  public function handle_shop_order_bulk_actions( $redirect_to, $action, $ids ) {
    // Bail out if this is not a print declaration action.
    if ( 'print_declaration' !== $action ) {
      return $redirect_to;
    }

    $ids = array_map( 'absint', $ids );

    $redirect_to = add_query_arg( array(
      'post_type'          => 'shop_order',
      'print_declarations' => 'yes',
      'ids'                => join( ',', $ids ),
    ), $redirect_to );

    return esc_url_raw( $redirect_to );
  }

  /**
   * Show confirmation message that order status changed for number of orders.
   */
  public function bulk_admin_notices() {
    global $post_type, $pagenow;

    // Bail out if not on shop order list page
    if ( 'edit.php' !== $pagenow || 'shop_order' !== $post_type ) {
      return;
    }

    if ( isset( $_REQUEST['print_declarations'] ) ) {
      $url = add_query_arg( array( 'action' => 'print_declaration', 'order_ids' => $_REQUEST['ids'] ), admin_url( 'admin-ajax.php' ) );
      echo '<div class="updated" style="display: block !important;"><p>Declarações prontas. Para imprimi-las, <a href="' . $url . '" target="_blank">clique aqui</a>.</p></div>';
    }
  }


  public function page() {
    if ( ! isset( $_GET['order_ids'] ) || empty( $_GET['order_ids'] ) ) {
      wp_die( 'Selecione pelo menos um pedido para imprimir a declaração de conteúdo.', 'Pedidos inválidos.' );
    }

    $order_ids = explode( ',', $_GET['order_ids'] );

    if ( ! apply_filters( 'wc_correios_declaration_permissions', current_user_can( 'edit_shop_orders' ), $order_ids ) ) {
      wp_die( 'Trapaceando, é?', 'Sem permissões para acessar essa página.' );
    }

    $orders = wc_get_orders( array(
      'post__in' => $order_ids,
      'limit'    => -1
    ));

    $show_price_column  = apply_filters( 'wc_correios_declaration_show_price_column', true );
    $show_weight_column = apply_filters( 'wc_correios_declaration_show_weight_column', true );
    $show_total         = apply_filters( 'wc_correios_declaration_show_total', true );

    wc_get_template( 'print-page.php', array(
      'orders'             => $orders,
      'show_weight_column' => $show_weight_column,
      'show_price_column'  => $show_price_column,
      'show_total'         => $show_total,
    ), '', WC_Correios_Shipping_Declaration::get_templates_path() );

    exit;
  }
}

new WC_Correios_Shipping_Declaration_Admin();
