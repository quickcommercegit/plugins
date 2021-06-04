<?php
/**
 * Admin bar logo field.
 *
 * @package Ultimate_Dashboard
 */

defined( 'ABSPATH' ) || die( "Can't access directly" );

return function () {
	?>

	<div class="setting-fields">

		<div class="setting-field">
			<input class="all-options" type="text" disabled />
			<a href="javascript:void(0)" class="button-secondary button-disabled disabled"><?php _e( 'Add or Upload File', 'ultimate-dashboard' ); ?></a>
		</div>

		<div class="setting-field">
			<label class="label checkbox-label">
				<?php _e( 'Remove Admin Bar Logo', 'ultimate-dashboard' ); ?>
				<input type="checkbox" disabled />
				<div class="indicator"></div>
			</label>
		</div>

	</div>

	<?php
};
