<?php

namespace DynamicContentForElementor\Extensions;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;
use DynamicContentForElementor\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class DCE_Extension_Form_Address_Autocomplete extends DCE_Extension_Prototype {

	private $is_common = false;
	public $has_action = false;

	public function get_name() {
		return 'dce_form_address_autocomplete';
	}

	protected function add_actions() {
		add_action( 'elementor/widget/render_content', array( $this, '_render_form' ), 10, 2 );

		add_action('elementor/widget/print_template', function( $template, $widget ) {
			if ( 'form' === $widget->get_name() ) {
				$template = false;
			}
			return $template;
		}, 10, 2);
	}

	public function _render_form( $content, $widget ) {
		if ( $widget->get_name() == 'form' ) {
			//https://developers.google.com/maps/documentation/javascript/examples/places-autocomplete-addressform
			$settings = $widget->get_settings_for_display();
			$has_address = false;
			$jkey = 'dce_' . $widget->get_type() . '_form_' . $widget->get_id() . '_address';
			if ( ! \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				ob_start();
				?>
			  <script id="<?php echo $jkey; ?>">
				  var placeSearch, autocomplete;

				  function dce_geolocate() {
					  if (navigator.geolocation) {
						  navigator.geolocation.getCurrentPosition(function (position) {
							  var geolocation = {
								  lat: position.coords.latitude,
								  lng: position.coords.longitude
							  };
							  var circle = new google.maps.Circle(
									  {center: geolocation, radius: position.coords.accuracy});
							  autocomplete.setBounds(circle.getBounds());
						  });
					  }
				  }

				  function dce_init_autocomplete() {
				<?php
				foreach ( $settings['form_fields'] as $key => $afield ) {
					if ( $afield['field_type'] == 'text' ) {
						if ( ! empty( $afield['field_address'] ) ) {
							$has_address = true;
							?>
						autocomplete = new google.maps.places.Autocomplete(document.getElementById('form-field-<?php echo $afield['custom_id']; ?>'), {types: ['geocode']});
						autocomplete.setFields(['address_component']);
							<?php
						}
					}
				}
				?>
				  }
			  </script>
				<?php
				$add_js = ob_get_clean();
				if ( $has_address ) {

					global $wp_scripts;
					if ( ! empty( $wp_scripts->registered['dce-google-maps-api'] ) ) {
						$wp_scripts->registered['dce-google-maps-api']->src .= '&libraries=places&callback=dce_init_autocomplete';
					}
					wp_enqueue_script( 'dce-google-maps-api' );

					return $content . $add_js;
				}
			}
		}
		return $content;
	}

	public static function _add_to_form( Controls_Stack $element, $control_id, $control_data, $options = [] ) {

		if ( $element->get_name() == 'form' && $control_id == 'form_fields' ) {
			$control_data['fields']['form_fields_enchanted_tab'] = array(
				'type' => 'tab',
				'tab' => 'enchanted',
				'label' => '<i class="dynicon icon-dyn-logo-dce" aria-hidden="true"></i>',
				'tabs_wrapper' => 'form_fields_tabs',
				'name' => 'form_fields_enchanted_tab',
				'condition' => [
					'field_type!' => 'step',
				],
			);
			$control_data['fields']['field_address'] = array(
				'name' => 'field_address',
				'label' => __( 'Enable Address Autocomplete', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'value' => 'text',
						],
					],
				],
				'description' => __( 'It requires Google Maps API.', 'dynamic-content-for-elementor' ),
				'tabs_wrapper' => 'form_fields_tabs',
				'inner_tab' => 'form_fields_enchanted_tab',
				'tab' => 'enchanted',
			);
		}

		return $control_data;
	}

}
