<?php
/**
*
*/
class WC_Any_Shipping_Notify_Shipping_Options {

  function __construct() {
    add_filter( 'woocommerce_get_sections_shipping', array( $this, 'get_sections' ) );
    add_filter( 'woocommerce_sections_shipping', array( $this, 'settings' ), 20 );
  }


  public function get_sections( $settings ) {
    $settings['shipping_companies'] = __( 'Transportadoras', 'wc-any-shipping-notify' );

    return $settings;
  }

  public function settings() {
    global $current_section;

    if ( isset( $_POST['_fa_current_section'] ) && 'shipping_companies' == $_POST['_fa_current_section'] ) {

      $companies_name  = array_map( 'wc_clean', $_POST['company_name'] );
      $tracking_urls   = $_POST['tracking_url'];

      foreach ( $companies_name as $i => $name ) {
        if ( ! isset( $companies_name[ $i ] ) ) {
          continue;
        }

        $companies[] = array(
          'name'   => $companies_name[ $i ],
          'url'    => $tracking_urls[ $i ],
        );
      }

      update_option( 'wc_any_shipping_notify_available_companies', $companies );
      update_option( 'wc_any_shipping_notify_new_status', $_POST['shipping_companies_new_status'] );
    }

    $available = get_option( 'wc_any_shipping_notify_available_companies', array(
      array(
        'name'   => '',
        'url'    => '',
      ),
    ) );

    $current_status = get_option( 'wc_any_shipping_notify_new_status', 'none' );
    $order_statuses = wc_get_order_statuses();

    if ( 'shipping_companies' === $current_section ) { ?>

      <h2>Configuração de transportadoras</h2>

      <table class="form-table">
        <tr valign="top">
          <th scope="row" class="titledesc">
            <label for="shipping_companies_new_status">Status após inserir rastreio</label>
            <span class="woocommerce-help-tip" data-tip="Atualizar pedidos para este status após informar um novo código de rastreio."></span>
          </th>
          <td class="forminp forminp-select">
            <select
              name="shipping_companies_new_status"
              id="shipping_companies_new_status"
              style="min-width: 350px;"
              class="wc-enhanced-select">
                <option value="none">Não atualizar status</option>
                <?php foreach ( $order_statuses as $status => $status_name ) { ?>
                  <option value="<?php echo substr( $status, 3 ); ?>" <?php selected( $current_status, substr( $status, 3 ) ); ?>><?php echo $status_name; ?></option>
                <?php } ?>
            </select>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row" class="titledesc">Transportadoras disponíveis:</th>
          <td class="forminp" id="available_companies_table">
            <table class="widefat wc_input_table sortable" cellspacing="0">
              <thead>
                <tr>
                  <th class="sort">&nbsp;</th>
                  <th>Transportadora</th>
                  <th>URL de rastreamento</th>
                </tr>
              </thead>
              <tbody class="accounts">
                <?php
                $i = -1;
                if ( $available ) {
                  foreach ( $available as $company ) {
                    $i++;

                    echo '<tr class="account">
                      <td class="sort"></td>
                      <td><input style="width: 100%;" type="text" value="' . esc_attr( wp_unslash( $company['name'] ) ) . '" name="company_name[' . $i . ']" /></td>
                      <td><input style="width: 100%;" type="text" value="' . esc_attr( $company['url'] ) . '" placeholder="{tracking_code} pode ser inserido na URL"  name="tracking_url[' . $i . ']" /></td>
                    </tr>';
                  }
                }
                ?>
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="7"><a href="#" class="add button"><?php _e( '+ Adicionar transportadora', 'wc-any-shipping-notify' ); ?></a> <a href="#" class="remove_rows button"><?php _e( 'Remover transportadora selecionada', 'wc-any-shipping-notify' ); ?></a></th>
                </tr>
              </tfoot>
            </table>
            <script type="text/javascript">
              jQuery(function() {
                jQuery('#available_companies_table').on( 'click', 'a.add', function(){

                  var size = jQuery('#available_companies_table').find('tbody .account').length;

                  jQuery('<tr class="account">\
                      <td class="sort"></td>\
                      <td><input style="width: 100%;" type="text" name="company_name[' + size + ']" /></td>\
                      <td><input style="width: 100%;" type="text" placeholder="{tracking_code} pode ser inserido na URL" name="tracking_url[' + size + ']" /></td>\
                    </tr>').appendTo('#available_companies_table table tbody');

                  return false;
                });
              });
            </script>
          </td>
        </tr>
      </table>

      <input style="width: 100%;" type="hidden" name="_fa_current_section" value="<?php echo $current_section; ?>" />
    <?php
      add_filter( 'woocommerce_get_settings_shipping', '__return_empty_array' );
    }
  }
}

new WC_Any_Shipping_Notify_Shipping_Options();
