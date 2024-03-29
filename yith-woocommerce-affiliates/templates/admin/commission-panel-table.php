<?php
/**
 * Commission Table Admin Panel
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly
?>

<div id="yith_wcaf_panel_commission">
	<form id="plugin-fw-wc" class="commission-table" method="get">
		<input type="hidden" name="page" value="yith_wcaf_panel"/>
		<input type="hidden" name="tab" value="commissions"/>

		<h2><?php _e( 'Commissions', 'yith-woocommerce-affiliates' ) ?></h2>
		<div class="yith-affiliates-commissions">
		<?php
		$commissions_table->views();
		$commissions_table->display();
		?>
		</div>
	</form>
</div>