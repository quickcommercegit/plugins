<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'FA_Licensing_Framework_New' ) ) {

  /**
   * Handles license input and validation
   */
  class FA_Licensing_Framework_New {

    /**
     * @var string  The license key for this installation
     */
    private $license_key;

    /**
     * @var string  API URL
     */
    public $store_url;

    /**
     * The product id (slug) used for this product on the License Manager site.
     * Configured through the class's constructor.
     *
     * @var int     The product id of the related product in the license manager.
     */
    private $product_id;

    /**
     * The text domain of the plugin or theme using this class.
     * Populated in the class's constructor.
     *
     * @var String  The text domain of the plugin / theme.
     */
    private $text_domain;

    /**
     * The name of the product using this class. Configured in the class's constructor.
     *
     * @var int     The name of the product (plugin / theme) using this class.
     */
    private $product_name;

    /**
     * Initializes the license manager client.
     */
    public function __construct( $product_id, $product_name, $plugin ) {
      // Store setup data
      $this->store_url      = 'https://fernandoacosta.net/';
      $this->product_id     = $product_id;
      $this->text_domain    = basename( $plugin, '.php' );
      $this->plugin_file    = $plugin;
      $this->plugin         = plugin_basename( $plugin );
      $this->license_key    = $this->get_license_key();
      $this->license_status = $this->get_license_status();
      $this->product_name   = $product_name;

      // Init
      $this->add_actions();

      $this->plugin_updates();
    }

    /**
     * Adds actions required for class functionality
     */
    public function add_actions() {
      // Add the menu screen for inserting license information
      add_action( 'admin_menu',     array( $this, 'add_license_settings_page' ), 150 );
      add_action( 'admin_init',     array( $this, 'register_license_settings' ) );
      add_action( 'admin_init',     array( $this, 'activate_license' ) );
      add_action( 'admin_init',     array( $this, 'deactivate_license' ) );
      add_action( 'admin_init',     array( $this, 'delete_license' ) );
      add_action( 'admin_notices',  array( $this, 'admin_notices' ) );

      add_action( 'fa_render_licenses_form', array( $this, 'render_license_form' ) );

      add_action( "site_transient_update_plugins", array( $this, 'update_plugins' ), 10, 2 );
      add_action( "in_plugin_update_message-{$this->plugin}", array( $this, 'update_notice' ) );
    }


    public function plugin_updates() {
      include 'plugin-updater.php';
    }


    /**
     * Creates the settings items for entering license information (email + license key).
     *
     * NOTE:
     * If you want to move the license settings somewhere else (e.g. your theme / plugin
     * settings page), we suggest you override this function in a subclass and
     * initialize the settings fields yourself. Just make sure to use the same
     * settings fields so that Nmg_License_Manager_Client can still find the settings values.
     */
    public function add_license_settings_page() {
      $title = 'Licenças';

      global $submenu;

      if ( isset( $submenu['options-general.php'] ) ) {
        foreach ( $submenu['options-general.php'] as $options ) {
          if ( in_array( 'fa_licenses', $options ) ) {
            return;
          }
        }
      }

      add_options_page(
        $title,
        $title,
        'manage_options',
        $this->get_settings_page_slug(),
        array( $this, 'render_licenses_page' )
      );

    }

    /**
     * Creates the settings fields needed for the license settings menu.
     */
    function register_license_settings() {
      // creates our settings in the options table
      register_setting( $this->get_settings_page_slug(), $this->product_id . '_license_key', 'sanitize_license' );
    }

    function sanitize_license( $new ) {
      $old = get_option( $this->product_id . '_license_key' );
      if ( $old && $old != $new ) {
        delete_transient( $this->product_id . '_license_status' ); // new license has been entered, so must reactivate
      }
      return $new;
    }


    public function render_licenses_page() {
      do_action( 'fa_render_licenses_form' );
    }

    /**
     * Renders the settings page for entering license information.
     */
    public function render_license_form() {
      $license_key        = $this->get_license_key();
      $status             = $this->get_license_status();
      $title              = sprintf( __( 'Licença para %s', $this->text_domain ), $this->product_name );

      ?>
      <div class="wrap">
        <form method="post" action="options.php">

          <?php settings_fields( $this->get_settings_page_slug() ); ?>

          <h1><?php echo $title; ?></h1>

          <p><?php _e( 'Adicione as informações da sua licença para obter suporte e atualizações automáticas.', $this->text_domain ); ?></p>

          <table class="form-table">
            <tbody>
              <tr valign="top">
                <th scope="row" valign="top">
                  <label for="<?php echo $this->product_id; ?>_license_key"><?php _e( 'Licença', $this->text_domain ); ?>:</label>
                </th>
                <td>
                  <input <?php echo ( $status !== false && $status == 'success' ) ? 'disabled' : ''; ?> id="<?php echo $this->product_id; ?>_license_key" name="<?php echo $this->product_id; ?>_license_key" type="text" class="regular-text" value="<?php echo esc_attr( self::get_hidden_license_key() ); ?>" />
                  <p class="description"><a href="//fernandoacosta.net/minha-conta/" target="_blank">Clique aqui</a> para acessar sua licença</p>
                </td>
              </tr>
              <tr valign="top">
                <th scope="row" valign="top"></th>
                <td>
                  <?php wp_nonce_field( $this->product_id . '_license_nonce', $this->product_id . '_license_nonce' ); ?>


                  <?php if( $status !== false && $status == 'success' ) { ?>
                    <input type="hidden" name="<?php echo $this->product_id; ?>_license_deactivate" />
                    <?php submit_button( __( 'Desativar licença', $this->text_domain ), 'button-primary button-large', 'submit', false, array( 'class' => 'button button-primary' ) ); ?>
                  <?php } else { ?>
                    <input type="hidden" name="<?php echo $this->product_id; ?>_license_activate" />
                    <?php submit_button( __( 'Ativar licença', $this->text_domain ), 'button-primary button-large', 'submit', false, array( 'class' => 'button button-primary' ) ); ?>
                  <?php } ?>

                  <?php if ( isset( $_GET['advanced'] ) ) {
                    $base_url   = admin_url( 'options-general.php?page=' . $this->get_settings_page_slug() );
                    $delete_url = add_query_arg( array( $this->product_id . '_license_delete' => 'true', ), $base_url );
                    echo '<a href="' . $delete_url . '" class="button button-large">Deletar licenças</a>';
                  } ?>
                </td>
              </tr>
            </tbody>
          </table>

        </form>
      </div>
    <?php
    }

    /**
     * Renders the description for the settings section.
     */
    public function render_settings_section() {
      printf( __( 'Insert your %s license information to enable future updates (including bug fixes and new features) and gain access to support.', $this->text_domain ), $this->product_name );
    }

    /**
     * Renders the license key settings field on the license settings page.
     */
    public function render_license_key_settings_field() {
      $settings_field_name = $this->get_settings_field_name();
      $options = get_option( $settings_field_name );
      ?>
      <input type='text' name='<?php echo $settings_field_name; ?>[license_key]' value='<?php echo $options['license_key']; ?>' class='regular-text' />
    <?php
    }

    /**
     * Renders the license key settings field on the license settings page.
     */
    public function render_license_status_settings_field() {
      $settings_field_name = $this->get_settings_field_name();
      $options = get_option( $settings_field_name );
      $license_status = $options['license_status'];

      ?>
      <!-- <input type="hidden" name="<?php echo $settings_field_name; ?>[license_status]" value='' -->

      <?php
      if ( $license_status !== false && $license_status === 'success' ) { ?>
        <span class="title-count" style="background-color:#41DCAB"><?php _e( 'Active', $this->text_domain ); ?></span>
      <?php } else { ?>
        <span class="title-count" style="background-color:#d54e21"><?php _e( 'Inactive', $this->text_domain ); ?></span>
      <?php }
    }

    /**
     * @return string   The slug id of the licenses settings page.
     */
    protected function get_settings_page_slug() {
      return 'fa_licenses';
    }

    /**
     * @return string   The name of the settings field storing all license manager settings.
     */
    protected function get_settings_field_name() {
      return $this->product_id . '-license-settings';
    }

    /**
     * Gets the currently set license key
     *
     * @return bool|string   The product license key, or false if not set
     */
    public function get_license_key() {

      $license = get_option( $this->product_id . '_license_key' );

      if ( ! $license ) {
        // User hasn't saved the license to settings yet. No use making the call.
        return false;
      }

      return trim( $license );
    }

    /**
     * Updates the license key option
     *
     * @return bool|string   The product license key, or false if not set
     */
    public function set_license_key( $license_key ) {
      return update_option( $this->product_id . '_license_key', $license_key );
    }

    /**
     * Gets the current license status
     *
     * @return bool|string   The product license key, or false if not set
     */
    public function get_license_status() {
      if ( false === ( $status = get_transient( $this->product_id . '_license_status' ) ) ) {
        $license_key = $this->get_license_key();

        if ( ! $license_key ) {
          return 'error';
        }

        // data to send in our API request
        $api_params = array(
          'woo_sl_action' => 'status-check',
          'licence_key'   => $license_key,
        );

        // Send query to the license manager server
        $response = $this->do_request( $api_params );

        // make sure the response came back okay
        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
          // return success in this case...
          $status = 'success';

        } else {

          $license_data = json_decode( wp_remote_retrieve_body( $response ) );
          $license_data = array_shift( $license_data );

          if ( 'error' === $license_data->status ) {
            delete_transient( $this->product_id . '_license_status' );
            delete_option( $this->product_id . '_license_key' );

            return false;
          } else {
            $status = 'success';
          }
        }

        $this->set_license_status( $status );
      }

      if ( ! $status ) {
        // User hasn't saved the license to settings yet. No use making the call.
        return false;
      }

      return trim( $status );
    }

    private function get_hidden_license_key() {
      $input_string = $this->get_license_key();

      $start = 4;
      $length = mb_strlen( $input_string ) - $start - 4;

      $mask_string = preg_replace( '/\S/', '*', $input_string );
      $mask_string = mb_substr( $mask_string, $start, $length );
      $input_string = substr_replace( $input_string, $mask_string, $start, $length );

      return $input_string;
    }

    /**
     * Updates the license status option
     *
     * @return bool|string   The product license key, or false if not set
     */
    public function set_license_status( $license_status ) {
      return set_transient( $this->product_id . '_license_status', $license_status, WEEK_IN_SECONDS );
    }

    /**
     * Validates the license and saves the license key in the database
     *
     * @return object|bool   The product data, or false if API call fails.
     */
    public function activate_license() {

      // listen for our activate button to be clicked
      if( isset( $_POST[ $this->product_id . '_license_activate' ] ) ) {

        // run a quick security check
        if( ! check_admin_referer( $this->product_id . '_license_nonce', $this->product_id . '_license_nonce' ) )
          return; // get out if we didn't click the Activate button

        // retrieve the license from the database
        $license_key = $_POST[ $this->product_id . '_license_key' ];

        // data to send in our API request
        $api_params = array(
          'woo_sl_action' => 'activate',
          'licence_key'   => $license_key,
        );

        // Send query to the license manager server
        $response = $this->do_request( $api_params );

        // make sure the response came back okay
        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

          if ( is_wp_error( $response ) ) {
            $message = $response->get_error_message();
          } else {
            $message = __( 'Ocorreu um erro ao processar sua solicitação. Tente novamente.' );
          }

        } else {

          $license_data = json_decode( wp_remote_retrieve_body( $response ) );
          $license_data = array_shift( $license_data );

          if ( 'error' === $license_data->status ) {
            $message = $license_data->message;
          }
        }

        // Check if anything passed on a message constituting a failure
        if ( ! empty( $message ) ) {
          $base_url = admin_url( 'options-general.php?page=' . $this->get_settings_page_slug() );
          $redirect = add_query_arg( array( 'fa_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

          wp_redirect( $redirect );
          exit();
        }

        // $license_data->license will be either "valid" or "invalid"
        $this->set_license_key( $license_key );
        $this->set_license_status( $license_data->status );

        $base_url = admin_url( 'options-general.php?page=' . $this->get_settings_page_slug() );
        $redirect = add_query_arg( array( 'fa_activation' => 'true', 'message' => 'Licença ativada com sucesso' ), $base_url );

        wp_redirect( $redirect );
        exit();
      }
    }

    /**
     * Removed the license validation
     *
     * @return object|bool   The product data, or false if API call fails.
     */
    function deactivate_license() {

      // listen for our activate button to be clicked
      if( isset( $_POST[ $this->product_id . '_license_deactivate' ] ) ) {
        // run a quick security check
        if( ! check_admin_referer( $this->product_id . '_license_nonce', $this->product_id . '_license_nonce' ) ) {
          return; // get out if we didn't click the Activate button
        }

        // retrieve the license from the database
        $license_key = $this->get_license_key();

        // data to send in our API request
        $api_params = array(
          'woo_sl_action' => 'deactivate',
          'licence_key'   => $license_key,
        );

        // Send query to the license manager server
        $response = $this->do_request( $api_params );

        // make sure the response came back okay
        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

          if ( is_wp_error( $response ) ) {
            $message = $response->get_error_message();
          } else {
            $message = __( 'Ocorreu um erro ao processar sua solicitação. Tente novamente.' );
          }

          $base_url = admin_url( 'options-general.php?page=' . $this->get_settings_page_slug() );
          $redirect = add_query_arg( array( 'fa_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

          wp_redirect( $redirect );
          exit();
        } else {

          $license_data = json_decode( wp_remote_retrieve_body( $response ) );
          $license_data = array_shift( $license_data );

          if ( 'error' === $license_data->status ) {
            $message = $license_data->message;
          }


          // Check if anything passed on a message constituting a failure
          if ( ! empty( $message ) ) {
            $base_url = admin_url( 'options-general.php?page=' . $this->get_settings_page_slug() );
            $redirect = add_query_arg( array( 'fa_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

            wp_redirect( $redirect );
            exit();
          }

          // $license_data->license will be either "deactivated" or "failed"
          if( 'success' === $license_data->status ) {
            delete_transient( $this->product_id . '_license_status' );
            delete_option( $this->product_id . '_license_key' );
          }

          wp_redirect( admin_url( 'options-general.php?page=' . $this->get_settings_page_slug() ) );
          exit();

        }

      }
    }



    function delete_license() {
      if( isset( $_GET[ $this->product_id . '_license_delete' ] ) ) {
        delete_transient( $this->product_id . '_license_status' );
        delete_option( $this->product_id . '_license_key' );

        $base_url = admin_url( 'options-general.php?page=' . $this->get_settings_page_slug() );
        wp_redirect( $base_url );
        exit;
      }
    }


    /**
     * Handles admin notices for errors and license activation
     *
     * @since 0.1.0
     */

    function admin_notices() {
      $status = $this->get_license_status();

      if ( $status === false || $status !== 'success' ) {
        $msg = __( 'Você está usando uma versão não verificada do plugin %3$s. %1$sAtive sua licença%2$s do  para obter suporte e atualizações.', $this->text_domain );
        $msg = sprintf( $msg, '<a href="' . admin_url( 'options-general.php?page=' . $this->get_settings_page_slug() ) . '">', '</a>', '<strong>' . $this->product_name . '</strong>' );
        ?>
        <div class="notice notice-error">
          <p><?php echo $msg; ?></p>
        </div>
      <?php
      }

      if ( isset( $_GET['fa_activation'] ) && ! empty( $_GET['message'] ) ) {

        switch( $_GET['fa_activation'] ) {

          case 'false':
            $message = urldecode( $_GET['message'] );
            ?>
            <div class="error">
              <p><?php echo $message; ?></p>
            </div>
            <?php
            break;

          case 'true':
          default: /* ?>

            <div class="updated notice notice-success is-dismissible">
              <p>Sua licença de <?php echo $this->product_name; ?> foi ativada com sucesso.</p>
            </div>

            <?php */ break;

        }
      }
    }


    public function update_plugins( $value, $transient ) {
      if ( 'success' != $this->get_license_status() && isset( $value->response[ $this->plugin ] ) ) {
        $value->response[ $this->plugin ]->package = false;
      }

      return $value;
    }


    public function update_notice() {
      if ( 'success' != $this->get_license_status() ) {
        echo ' <strong>Informe uma licença válida para prosseguir.</strong>';
      }
    }



    public function do_request( $data = array(), $method = 'POST', $headers = array() ) {
      $params = array(
        'method'  => $method,
        'timeout' => 60,
      );

      if ( ! empty( $data ) ) {
        $defaults = array(
          'domain'             => str_replace( array( 'https://', 'http://' ), array( '', '' ), home_url() ),
          'product_unique_id'  => $this->product_id,
          'licence_key'        => $this->license_key,
        );

        $params['body'] = wp_parse_args( $data, $defaults );
      }

      if ( ! empty( $headers ) ) {
        $params['headers'] = $headers;
      }

      return wp_safe_remote_post( $this->store_url, $params );

    }

  }
}
