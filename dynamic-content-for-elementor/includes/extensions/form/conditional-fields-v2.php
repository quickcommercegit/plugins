<?php

namespace DynamicContentForElementor\Extensions;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use DynamicContentForElementor\Helper;
use DynamicContentForElementor\Tokens;
use ElementorPro\Plugin;
use ElementorPro\Modules\Forms\Fields\Field_Base;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class ConditionalFieldsV2 extends DCE_Extension_Prototype {
	private $is_common = false;
	public $has_action = false;
	public $depended_scripts = [ 'dce-conditional-fields' ];
	public $depended_styles = [ 'dce-conditional-fields' ];
	private static $actions_added = false;

	public function get_name() {
		return 'dce_conditional_fields_v2';
	}

	public function get_label() {
		return __( 'Conditional Fields v2', 'dynamic-content-for-elementor' );
	}

	/**
	 * Rewrite the expression so that each line are logically connected
	 *  with an `and`.
	 */
	private static function and_join_lines( $expr ) {
		$lines = preg_split( '/\r\n|\r|\n/', $expr );
		$lines = array_filter( $lines, function( $l ) {
			return ! preg_match( '/^\s*$/', $l ); // filter empty lines
		} );
		return '(' . implode( ')&&(', $lines ) . ')';
	}

	private static function are_conditions_enabled( $field ) {
		$enabled = $field['dce_field_conditions_mode'] === 'show' || $field['dce_field_conditions_mode'] == 'hide';
		return $enabled && ! preg_match( '/^\s*$/', $field['dce_conditions_expression'] );
	}

	public function add_assets_depends( $instance, $form ) {
		// fetch all the settings data we need to pass to the JavaScript code:
		$field_ids = [];
		$conditions = [];
		foreach ( $instance['form_fields'] as $field ) {
			$field_ids[] = $field['custom_id'];
			if ( self::are_conditions_enabled( $field ) ) {
				$conditions[] = [
					'id' => $field['custom_id'],
					'condition' => self::and_join_lines( $field['dce_conditions_expression'] ),
					'mode' => $field['dce_field_conditions_mode'],
					'disableOnly' => $field['dce_conditions_disable_only'] === 'yes',
				];
			}
		}
		if ( ! empty( $conditions ) ) {
			// pass the data as HTML data attributes:
			$form->add_render_attribute( 'wrapper', 'data-conditions-expressions', wp_json_encode( $conditions ) );
			$form->add_render_attribute( 'wrapper', 'data-field-ids', wp_json_encode( $field_ids ) );
			foreach ( $this->depended_scripts as $script ) {
				$form->add_script_depends( $script );
			}
			foreach ( $this->depended_styles as $style ) {
				$form->add_style_depends( $style );
			}
		}
	}

	protected function add_actions() {
		if ( self::$actions_added ) {
			return;
		}
		self::$actions_added = true;
		add_action( 'elementor/element/form/section_form_fields/before_section_end',
					[ $this, 'update_controls' ] );
		add_action( 'elementor-pro/forms/pre_render',
					[ $this, 'add_assets_depends' ], 10, 2 );
		// very low priority because it needs to fix validation of other validation hooks.
		add_action( 'elementor_pro/forms/validation',
					[ $this, 'fix_validation' ], 1000, 2 );
	}

	public function update_controls( $widget ) {
		$elementor = Plugin::elementor();
		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );
		if ( is_wp_error( $control_data ) ) {
			return;
		}
		$field_controls = [
			'form_fields_conditions_tab' => [
				'type' => 'tab',
				'tab' => 'content',
				'label' => __( 'Conditions', 'dynamic-content-for-elementor' ),
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => '!in',
							'value' => [
								'hidden',
								'step',
							],
						],
					],
				],
				'tabs_wrapper' => 'form_fields_tabs',
				'name' => 'form_fields_conditions_tab',
			],
			'dce_field_conditions_mode' => [
				'name' => 'dce_field_conditions_mode',
				'label' => __( 'Condition', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'visible' => [
						'title' => __( 'Always Visible', 'dynamic-content-for-elementor' ),
						'icon' => 'fa fa-check-square-o',
					],
					'show' => [
						'title' => __( 'Show IF', 'dynamic-content-for-elementor' ),
						'icon' => 'fa fa-eye',
					],
					'hide' => [
						'title' => __( 'Hide IF', 'dynamic-content-for-elementor' ),
						'icon' => 'fa fa-eye-slash',
					],
				],
				'toggle' => false,
				'default' => 'visible',
				'tab' => 'content',
				'tabs_wrapper' => 'form_fields_tabs',
				'inner_tab' => 'form_fields_conditions_tab',
			],
			'dce_conditions_expression' => [
				'name' => 'dce_conditions_expression',
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'label' => __( 'Conditions Expressions', 'dynamic-content-for-elementor' ),
				'description' => __( 'One condition per line. All conditions are and-connected. Conditions are expressions that can also use the or operator and much more! Please check the documentation.', 'dynamic-content-for-elementor' ),
				'placeholder' => "name == 'Joe'",
				'condition' => [ 'dce_field_conditions_mode!' => 'visible' ],
				'tab' => 'content',
				'tabs_wrapper' => 'form_fields_tabs',
				'inner_tab' => 'form_fields_conditions_tab',
			],
			'dce_conditions_disable_only' => [
				'name' => 'dce_conditions_disable_only',
				'label' => __( 'Disable only', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'tab' => 'content',
				'tabs_wrapper' => 'form_fields_tabs',
				'inner_tab' => 'form_fields_conditions_tab',
				'condition' => [
					'dce_field_conditions_mode!' => 'visible',
				],
			],
		];
		$control_data['fields'] = array_merge( $control_data['fields'], $field_controls );
		$widget->update_control( 'form_fields', $control_data );
	}

	/**
	 * Determine all field visibilities based on the conditions.
	 */
	public function determine_visibilities( $conditions, $values ) {
		$lang = new ExpressionLanguage();
		$visibility = [];
		// Assume they are all visible at the beginning:
		foreach ( $conditions as $id => $_ ) {
			$visibility[ $id ] = true;
		}
		foreach ( $conditions as $id => $condition ) {
			$res = $lang->evaluate( $condition['condition'], $values );
			$res = ( $condition['mode'] === 'show' ) ? $res : ! $res;
			if ( ! $res ) {
				// we don't want an inactive field value to influence
				// further conditions:
				$values[ $id ] = '';
			}
			$visibility[ $id ] = $res;
		}
		return $visibility;
	}

	/**
	 * Remove validation errors related to fields that are required but
	 * that have been hidden by a condition.
	 */
	public function fix_validation( $record, $ajax_handler ) {
		$raw_fields = $record->get_field( [] );
		$values = [];
		foreach ( $raw_fields as $field ) {
			$values[ $field['id'] ] = $field['raw_value'];
		}
		$conditions = [];
		foreach ( $record->get_form_settings( 'form_fields' ) as $field ) {
			if ( self::are_conditions_enabled( $field ) ) {
				$conditions[ $field['custom_id'] ] = [
					'condition' => self::and_join_lines( $field['dce_conditions_expression'] ),
					'mode' => $field['dce_field_conditions_mode'],
				];
			}
		}

		$visibilities = $this->determine_visibilities( $conditions, $values );
		foreach ( $visibilities as $id => $visible ) {
			if ( ! $visible ) {
				if ( ! empty( $values[ $id ] ) ) {
					$ajax_handler->add_error( $id, __( "Conditional Fields V2 was expecting this field to be invisibile, instead it found a value. Notice that if you have a checkbox field, you cannot compare it to a string value like this 'Yes' == checkboxField, instead use: 'Yes' in checkboxField.", 'dynamic-content-for-elementor' ) );
				} else {
					// Remove potential validation error related to the field:
					unset( $ajax_handler->errors[ $id ] );
				}
			}
		}

		// if there are no errors then the form is actually good.
		if ( empty( $ajax_handler->errors ) ) {
			$ajax_handler->set_success( true );
		}
	}
}
