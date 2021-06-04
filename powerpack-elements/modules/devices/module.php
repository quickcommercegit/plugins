<?php
namespace PowerpackElements\Modules\Devices;

use PowerpackElements\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Module extends Module_Base {

	/**
	 * Module is active or not.
	 *
	 * @since 1.3.3
	 *
	 * @access public
	 *
	 * @return bool true|false.
	 */
	public static function is_active() {
		return true;
	}

	/**
	 * Get Module Name.
	 *
	 * @since 1.3.3
	 *
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'pp-devices';
	}

	/**
	 * Get Widgets.
	 *
	 * @since 1.3.3
	 *
	 * @access public
	 *
	 * @return array Widgets.
	 */
	public function get_widgets() {
		return [
			'Devices',
		];
	}

	/**
	 * Get Image Caption.
	 *
	 * @since 1.3.3
	 *
	 * @access public
	 *
	 * @return string image caption.
	 */
	public static function get_image_caption( $id, $caption_type ) {

		$attachment = get_post( $id );

		if ( 'title' === $caption_type ) {
			$attachment_caption = $attachment->post_title;
		} elseif ( 'caption' === $caption_type ) {
			$attachment_caption = $attachment->post_excerpt;
		}

		return $attachment_caption;

	}
}
