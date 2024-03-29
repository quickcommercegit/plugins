<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/woocommerce/myaccount/form-login.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.1.0
 */

defined( 'ABSPATH' ) || exit;

$template = jet_woo_builder_integration_woocommerce()->get_current_form_login_template();
$template = apply_filters( 'jet-woo-builder/current-template/template-id', $template );
?>

<div class="jet-woo-builder-woocommerce-myaccount-login-page">
	<?php do_action( 'woocommerce_before_customer_login_form' ); ?>
	<div id="customer_login">
		<?php echo jet_woo_builder_template_functions()->get_woo_builder_content( $template ); ?>
	</div>
	<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
</div>

