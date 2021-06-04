<?php
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class DCE_Widget_DoShortcode extends DCE_Widget_Prototype {

	public function show_in_panel() {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return false;
		}
		return true;
	}

	protected function _register_controls() {
		if ( current_user_can( 'install_plugins' ) || ! is_admin() ) {
			$this->_register_controls_content();
		} elseif ( ! current_user_can( 'install_plugins' ) && is_admin() ) {
			$this->register_controls_non_admin_notice();
		}
	}

	protected function _register_controls_content() {

		$this->start_controls_section(
			'section_doshortcode',
			[
				'label' => __( 'DoShortcode', 'dynamic-content-for-elementor' ),
			]
		);
		$this->add_control(
		   'doshortcode_string',
		   [
			   'label'   => __( 'Shortcode', 'dynamic-content-for-elementor' ),
			   'type'    => Controls_Manager::TEXTAREA,
			   'description' => 'Example: [gallery ids="66,67,28"]',
		   ]
		);
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$doshortcode_string = $settings['doshortcode_string'];
		if ( $doshortcode_string != '' ) {
			echo do_shortcode( $doshortcode_string );
		}
	}
}
