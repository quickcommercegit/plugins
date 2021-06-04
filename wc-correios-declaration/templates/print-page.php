<!DOCTYPE html>
<html>
<head>
  <title>Declaração de conteúdo</title>
  <link rel="stylesheet" type="text/css" href="<?php echo apply_filters( 'wc_correios_declaration_css', WC_Correios_Shipping_Declaration::plugin_dir_url() . 'assets/css/style.css' ); ?>?v=1.4.6" />

  <?php global $wp_scripts; $wp_scripts->do_item( 'jquery-core' ); ?>

  <?php if ( count( $orders ) > 1 ) {
    echo '<style type="text/css">
    @media print {
      @page {
        margin-top: 0.6cm;
      }

      .print-wrapper {
        margin-top: 1cm;
      }
    }</style>';
  } ?>

  <?php do_action( 'wc_correios_declaration_head' ); ?>
</head>
<body>

  <?php
    foreach ( $orders as $order ) {
      wc_get_template( 'order-content.php', array(
        'order'              => $order,
        'show_weight_column' => $show_weight_column,
        'show_price_column'  => $show_price_column,
        'show_total'         => $show_total,
      ), '', WC_Correios_Shipping_Declaration::get_templates_path() );
    }
  ?>

  <a href="#nogo" onclick="window.print();" class="print-button">
    Imprimir página
  </a>

  <script type="text/javascript">
    <?php if ( apply_filters( 'wc_correios_declaration_auto_open_print_page', true ) ) { ?>
    document.addEventListener('DOMContentLoaded', function() {
      window.print();
    });
    <?php } ?>

    jQuery( document ).ready( function( $ ) {
      $( '.hide-column' ).on( 'change', function( event ) {
        event.preventDefault();

        if ( $( this ).is( ':checked' ) ) {
          $( this ).closest( '.line-item' ).addClass( 'hide' );
        } else {
          $( this ).closest( '.line-item' ).removeClass( 'hide' );
        }
      });
    } );
  </script>
</body>
</html>
