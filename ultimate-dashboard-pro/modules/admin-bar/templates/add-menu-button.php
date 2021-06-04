<?php
/**
 * Add menu button template.
 *
 * @package Ultimate_Dashboard_Pro
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {
	?>

	<button type="button" class="udb-admin-bar--add-item udb-admin-bar--add-new-menu">
		<i class="dashicons dashicons-plus"></i>
		<?php _e( 'Add Item', 'ultimatedashboard' ); ?>
	</button>

	<?php
};
