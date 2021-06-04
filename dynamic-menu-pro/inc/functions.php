<?php
function dm_admin_notice__error() {
	$class = 'notice notice-error is-dismissible';
	$message = __( 'Dynamic Menu is enabled but not effective. It requires WooCommerce in order to work.', 'dynamic-menu' );

	printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 
}
/**
 * Functions used by plugins
 */
if ( ! class_exists( 'WC_Dependencies' ) )
	require_once 'class-wc-dependencies.php';

/**
 * WC Detection
 */
if ( ! function_exists( 'is_woocommerce_active' ) ) {
	function is_woocommerce_active() {
		return WC_Dependencies::woocommerce_active_check();
	}
}

/*if ( ! function_exists( 'pr' ) ) {
	function pr($arr) {
		echo "<pre>";print_r($arr); echo "</pre>";
	}
}*/

/**
* Returns all child nav_menu_items under a specific parent
*
* @param int the parent nav_menu_item ID
* @param array nav_menu_items
* @param bool gives all children or direct children only
* @return array returns filtered array of nav_menu_items
*/
function get_nav_menu_item_children( $parent_id, $nav_menu_items, $depth = true ) {
	//pr($parent_id);
	$nav_menu_item_list = array();
	foreach ( (array) $nav_menu_items as $nav_menu_item ) {

		if (isset($nav_menu_item->menu_item_parent) && $nav_menu_item->menu_item_parent == $parent_id ) {
			$nav_menu_item_list[] = $nav_menu_item->type;
			if ( $depth ) {
				if ( $children = get_nav_menu_item_children( $nav_menu_item->ID, $nav_menu_items ) )
					$nav_menu_item_list = array_merge( $nav_menu_item_list, $children );
			}
		}
	}
	return $nav_menu_item_list;
}
?>