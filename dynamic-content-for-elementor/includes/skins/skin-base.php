<?php
namespace DynamicContentForElementor\Includes\Skins;

use Elementor\Skin_Base as Elementor_Skin_Base;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;

use DynamicContentForElementor\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class Skin_Base extends Elementor_Skin_Base {

	protected $current_permalink;

	protected $current_id;

	protected $counter = 0;

	protected function _register_controls_actions() {
		add_action( 'elementor/element/dce-dynamicposts-v2/section_query/after_section_end', [ $this, 'register_controls_layout' ] );
	}

	public function register_controls_layout( Widget_Base $widget ) {
		$this->parent = $widget;

		// BLOCKS generic style
		$this->register_style_controls();
		// PAGINATION style
		$this->register_style_pagination_controls();
		//INFINITE SCROLL style
		$this->register_style_infinitescroll_controls();
	}

	protected function register_style_pagination_controls() {
		$this->start_controls_section(
			'section_style_pagination', [
				'label' => __( 'Pagination', 'dynamic-content-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin!' => 'carousel',
					'pagination_enable' => 'yes',
					'infiniteScroll_enable' => '',
				],
			]
		);
		$this->add_control(
			'pagination_heading_style', [
				'label' => __( 'Pagination', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'pagination_align', [
				'label' => __( 'Alignment', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'options' => [
					'flex-start' => [
						'title' => __( 'Left', 'dynamic-content-for-elementor' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'dynamic-content-for-elementor' ),
						'icon' => 'eicon-h-align-center',
					],
					'flex-end' => [
						'title' => __( 'Right', 'dynamic-content-for-elementor' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .dce-pagination' => 'justify-content: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name' => 'pagination_typography',
				'label' => __( 'Typography', 'dynamic-content-for-elementor' ),
				'selector' => '{{WRAPPER}} .dce-pagination',
			]
		);
		$this->add_responsive_control(
			'pagination_space', [
				'label' => __( 'Space', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'max' => 100,
						'min' => 0,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .dce-pagination' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'pagination_spacing', [
				'label' => __( 'Horizontal Spacing', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'max' => 100,
						'min' => 0,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'pagination_padding', [
				'label' => __( 'Padding', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'pagination_radius', [
				'label' => __( 'Border Radius', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'pagination_heading_colors', [
				'label' => __( 'Colors', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'pagination_colors' );

		$this->start_controls_tab(
			'pagination_text_colors', [
				'label' => __( 'Normal', 'dynamic-content-for-elementor' ),
			]
		);

		$this->add_control(
			'pagination_text_color', [
				'label' => __( 'Text Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_background_color', [
				'label' => __( 'Background Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(), [
				'name' => 'pagination_border',
				'label' => __( 'Border', 'dynamic-content-for-elementor' ),
				'selector' => '{{WRAPPER}} .dce-pagination span, {{WRAPPER}} .dce-pagination a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_text_colors_hover', [
				'label' => __( 'Hover', 'dynamic-content-for-elementor' ),
			]
		);
		$this->add_control(
			'pagination_hover_color', [
				'label' => __( 'Text Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dce-pagination a:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'pagination_background_hover_color', [
				'label' => __( 'Background Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dce-pagination a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'pagination_hover_border_color', [
				'label' => __( 'Border Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'pagination_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .dce-pagination a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_text_colors_current', [
				'label' => __( 'Current', 'dynamic-content-for-elementor' ),
			]
		);
		$this->add_control(
			'pagination_current_color', [
				'label' => __( 'Text Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dce-pagination span.current' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'pagination_background_current_color', [
				'label' => __( 'Background Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dce-pagination span.current' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'pagination_heading_prevnext', [
				'label' => __( 'Prev/Next', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'pagination_show_prevnext' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'pagination_spacing_prevnext', [
				'label' => __( 'Spacing', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '',
				],
				'range' => [
					'px' => [
						'max' => 100,
						'min' => 0,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .pageprev' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dce-pagination .pagenext' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pagination_show_prevnext' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_icon_spacing_prevnext', [
				'label' => __( 'Icon Spacing', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '',
				],
				'range' => [
					'px' => [
						'max' => 50,
						'min' => 0,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .pageprev .fa' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dce-pagination .pagenext .fa' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pagination_show_prevnext' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'pagination_icon_size_prevnext', [
				'label' => __( 'Icon Size', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '',
				],
				'range' => [
					'px' => [
						'max' => 100,
						'min' => 0,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .pageprev .fa' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dce-pagination .pagenext .fa' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pagination_show_prevnext' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'pagination_prevnext_colors' );

		$this->start_controls_tab(
			'pagination_prevnext_text_colors', [
				'label' => __( 'Normal', 'dynamic-content-for-elementor' ),
				'condition' => [
					'pagination_show_prevnext' => 'yes',
				],
			]
		);

		$this->add_control(
			'pagination_prevnext_text_color', [
				'label' => __( 'Text Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .pageprev, {{WRAPPER}} .dce-pagination .pagenext' => 'color: {{VALUE}};',
				],
				'condition' => [
					'pagination_show_prevnext' => 'yes',
				],
			]
		);

		$this->add_control(
			'pagination_prevnext_background_color', [
				'label' => __( 'Background Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .pageprev, {{WRAPPER}} .dce-pagination .pagenext' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'pagination_show_prevnext' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(), [
				'name' => 'pagination_prevnext_border',
				'label' => __( 'Border', 'dynamic-content-for-elementor' ),
				'selector' => '{{WRAPPER}} .dce-pagination .pageprev, {{WRAPPER}} .dce-pagination .pagenext',
				'condition' => [
					'pagination_show_prevnext' => 'yes',
				],
			]
		);
		$this->add_control(
			'pagination_prevnext_radius', [
				'label' => __( 'Border Radius', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .pageprev, {{WRAPPER}} .dce-pagination .pagenext' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pagination_show_prevnext' => 'yes',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_prevnext_text_colors_hover', [
				'label' => __( 'Hover', 'dynamic-content-for-elementor' ),
				'condition' => [
					'pagination_show_prevnext' => 'yes',
				],
			]
		);
		$this->add_control(
			'pagination_prevnext_hover_color', [
				'label' => __( 'Text Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .pageprev:hover, {{WRAPPER}} .dce-pagination .pagenext:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'pagination_show_prevnext' => 'yes',
				],
			]
		);
		$this->add_control(
			'pagination_prevnext_background_hover_color', [
				'label' => __( 'Background Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .pageprev:hover, {{WRAPPER}} .dce-pagination .pagenext:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'pagination_show_prevnext' => 'yes',
				],
			]
		);
		$this->add_control(
			'pagination_prevnext_hover_border_color', [
				'label' => __( 'Border Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .pageprev:hover, {{WRAPPER}} .dce-pagination .pagenext:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'pagination_show_prevnext' => 'yes',
					'pagination_prevnext_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'pagination_heading_firstlast', [
				'label' => __( 'First/last', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'pagination_show_firstlast' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'pagination_spacing_firstlast', [
				'label' => __( 'Spacing', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '',
				],
				'range' => [
					'px' => [
						'max' => 100,
						'min' => 0,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .pagefirst' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dce-pagination .pagelast' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pagination_show_firstlast' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'pagination_firstlast_colors' );

		$this->start_controls_tab(
			'pagination_firstlast_text_colors', [
				'label' => __( 'Normal', 'dynamic-content-for-elementor' ),
				'condition' => [
					'pagination_show_firstlast' => 'yes',
				],
			]
		);

		$this->add_control(
			'pagination_firstlast_text_color', [
				'label' => __( 'Text Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .pagefirst, {{WRAPPER}} .dce-pagination .pagelast' => 'color: {{VALUE}};',
				],
				'condition' => [
					'pagination_show_firstlast' => 'yes',
				],
			]
		);

		$this->add_control(
			'pagination_firstlast_background_color', [
				'label' => __( 'Background Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .pagefirst, {{WRAPPER}} .dce-pagination .pagelast' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'pagination_show_firstlast' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(), [
				'name' => 'pagination_firstlast_border',
				'label' => __( 'Border', 'dynamic-content-for-elementor' ),
				'selector' => '{{WRAPPER}} .dce-pagination .pagefirst, {{WRAPPER}} .dce-pagination .pagelast',
				'condition' => [
					'pagination_show_firstlast' => 'yes',
				],
			]
		);
		$this->add_control(
			'pagination_firstlast_radius', [
				'label' => __( 'Border Radius', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .pagefirst, {{WRAPPER}} .dce-pagination .pagelast' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pagination_show_firstlast' => 'yes',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_firstlast_text_colors_hover', [
				'label' => __( 'Hover', 'dynamic-content-for-elementor' ),
				'condition' => [
					'pagination_show_firstlast' => 'yes',
				],
			]
		);
		$this->add_control(
			'pagination_firstlast_hover_color', [
				'label' => __( 'Text Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .pagefirst:hover, {{WRAPPER}} .dce-pagination .pagelast:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'pagination_show_firstlast' => 'yes',
				],
			]
		);
		$this->add_control(
			'pagination_firstlast_background_hover_color', [
				'label' => __( 'Background Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .pagefirst:hover, {{WRAPPER}} .dce-pagination .pagelast:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'pagination_show_firstlast' => 'yes',
				],
			]
		);
		$this->add_control(
			'pagination_firstlast_hover_border_color', [
				'label' => __( 'Border Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .pagefirst:hover, {{WRAPPER}} .dce-pagination .pagelast:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'pagination_show_firstlast' => 'yes',
					'pagination_firstlast_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'pagination_heading_progression', [
				'label' => __( 'Progression', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'pagination_show_progression' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'pagination_spacing_progression', [
				'label' => __( 'Spacing', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '',
				],
				'range' => [
					'px' => [
						'max' => 100,
						'min' => 0,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .progression' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'pagination_show_progression' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'pagination_progression_colors' );

		$this->start_controls_tab(
			'pagination_progression_text_colors', [
				'label' => __( 'Normal', 'dynamic-content-for-elementor' ),
				'condition' => [
					'pagination_show_progression' => 'yes',
				],
			]
		);

		$this->add_control(
			'pagination_progression_text_color', [
				'label' => __( 'Text Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .progression' => 'color: {{VALUE}};',
				],
				'condition' => [
					'pagination_show_progression' => 'yes',
				],
			]
		);

		$this->add_control(
			'pagination_progression_background_color', [
				'label' => __( 'Background Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .progression' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'pagination_show_progression' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(), [
				'name' => 'pagination_progression_border',
				'label' => __( 'Border', 'dynamic-content-for-elementor' ),
				'selector' => '{{WRAPPER}} .dce-pagination .progression',
				'condition' => [
					'pagination_show_progression' => 'yes',
				],
			]
		);
		$this->add_control(
			'pagination_progression_radius', [
				'label' => __( 'Border Radius', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .progression' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pagination_show_progression' => 'yes',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_progression_text_colors_hover', [
				'label' => __( 'Hover', 'dynamic-content-for-elementor' ),
				'condition' => [
					'pagination_show_progression' => 'yes',
				],
			]
		);
		$this->add_control(
			'pagination_progression_hover_color', [
				'label' => __( 'Text Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .progression' => 'color: {{VALUE}};',
				],
				'condition' => [
					'pagination_show_progression' => 'yes',
				],
			]
		);
		$this->add_control(
			'pagination_progression_background_hover_color', [
				'label' => __( 'Background Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .progression' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'pagination_show_progression' => 'yes',
				],
			]
		);
		$this->add_control(
			'pagination_progression_hover_border_color', [
				'label' => __( 'Border Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dce-pagination .progression' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'pagination_show_progression' => 'yes',
					'pagination_firstlast_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	protected function register_style_infinitescroll_controls() {
		$this->start_controls_section(
			'section_style_infiniteScroll', [
				'label' => __( 'Infinite Scroll', 'dynamic-content-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'infiniteScroll_enable' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'infiniteScroll_spacing', [
				'label' => __( 'Spacing status', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'max' => 100,
						'min' => 0,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .infiniteScroll' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'infiniteScroll_heading_button_style', [
				'label' => __( 'Button', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'infiniteScroll_trigger' => 'button',
				],
			]
		);

		$this->add_responsive_control(
			'infiniteScroll_button_align', [
				'label' => __( 'Alignment', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'options' => [
					'flex-start' => [
						'title' => __( 'Left', 'dynamic-content-for-elementor' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'dynamic-content-for-elementor' ),
						'icon' => 'eicon-h-align-center',
					],
					'flex-end' => [
						'title' => __( 'Right', 'dynamic-content-for-elementor' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} div.infiniteScroll' => 'justify-content: {{VALUE}};',
				],
				'condition' => [
					'infiniteScroll_trigger' => 'button',
				],
			]
		);
		$this->start_controls_tabs( 'infiniteScroll_button_colors' );

		$this->start_controls_tab(
			'infiniteScroll_button_text_colors', [
				'label' => __( 'Normal', 'dynamic-content-for-elementor' ),
				'condition' => [
					'infiniteScroll_trigger' => 'button',
				],
			]
		);

		$this->add_control(
			'infiniteScroll_button_text_color', [
				'label' => __( 'Text Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .infiniteScroll button' => 'color: {{VALUE}};',
				],
				'condition' => [
					'infiniteScroll_trigger' => 'button',
				],
			]
		);

		$this->add_control(
			'infiniteScroll_button_background_color', [
				'label' => __( 'Background Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .infiniteScroll button' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'infiniteScroll_trigger' => 'button',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'infiniteScroll_button_text_colors_hover', [
				'label' => __( 'Hover', 'dynamic-content-for-elementor' ),
				'condition' => [
					'infiniteScroll_trigger' => 'button',
				],
			]
		);
		$this->add_control(
			'infiniteScroll_button_hover_color', [
				'label' => __( 'Text Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .infiniteScroll button:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'infiniteScroll_trigger' => 'button',
				],
			]
		);
		$this->add_control(
			'infiniteScroll_button_background_hover_color', [
				'label' => __( 'Background Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .infiniteScroll button:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'infiniteScroll_trigger' => 'button',
				],
			]
		);
		$this->add_control(
			'infiniteScroll_button_hover_border_color', [
				'label' => __( 'Border Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .infiniteScroll button:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'infiniteScroll_trigger' => 'button',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'infiniteScroll_button_padding', [
				'label' => __( 'Padding', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .infiniteScroll button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'infiniteScroll_trigger' => 'button',
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'infiniteScroll_button_radius', [
				'label' => __( 'Border Radius', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .infiniteScroll button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'infiniteScroll_trigger' => 'button',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function register_style_controls() {
		// Blocks - Style
		$this->start_controls_section(
			'section_blocks_style',
			[
				'label' => __( 'Blocks Style', 'dynamic-content-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style_items!' => [ 'template' ],
				],
			]
		);
		$this->add_responsive_control(
			'blocks_align', [
				'label' => __( 'Text Alignment', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'dynamic-content-for-elementor' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'dynamic-content-for-elementor' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'dynamic-content-for-elementor' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'left',
				'prefix_class' => 'dce-align%s-',
				'selectors' => [
					'{{WRAPPER}} .dce-post-item' => 'text-align: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'blocks_align_v', [
				'label' => __( 'Vertical Alignment', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => __( 'Left', 'dynamic-content-for-elementor' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __( 'Center', 'dynamic-content-for-elementor' ),
						'icon' => 'eicon-v-align-middle',
					],
					'flex-end' => [
						'title' => __( 'Right', 'dynamic-content-for-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					],
					'space-between' => [
						'title' => __( 'Space Between', 'dynamic-content-for-elementor' ),
						'icon' => 'eicon-v-align-stretch',
					],
					'space-around' => [
						'title' => __( 'Space Around', 'dynamic-content-for-elementor' ),
						'icon' => 'eicon-v-align-stretch',
					],
				],
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .dce-post-block, {{WRAPPER}} .dce-item-area' => 'justify-content: {{VALUE}} !important;',
				],
				'condition' => [
					'v_pos_postitems' => [ '', 'stretch' ],
				],
			]
		);
		$this->add_control(
			'blocks_bgcolor', [
				'label' => __( 'Background Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dce-post-item .dce-post-block' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(), [
				'name' => 'blocks_border',
				'selector' => '{{WRAPPER}} .dce-post-item .dce-post-block',
			]
		);
		$this->add_responsive_control(
			'blocks_padding', [
				'label' => __( 'Padding', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .dce-post-item .dce-post-block' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'blocks_border_radius', [
				'label' => __( 'Border Radius', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .dce-post-item .dce-post-block' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(), [
				'name' => 'blocks_boxshadow',
				'selector' => '{{WRAPPER}} .dce-post-item .dce-post-block',
			]
		);
		// Vertical Alternate
		$this->add_control(
			'dis_alternate',
			[
				'type' => Controls_Manager::RAW_HTML,
				'show_label' => false,
				'separator' => 'before',
				'raw' => '<img src="' . DCE_URL . 'assets/img/skins/alternate.png" />',
				'content_classes' => 'dce-skin-dis',
				'condition' => [
					'grid_type' => [ 'flex' ],
				],
			]
		);
		$this->add_responsive_control(
			'blocks_alternate', [
				'label' => __( 'Vertical Alternate', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 100,
						'min' => 0,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.dce-col-3 .dce-post-item:nth-child(3n+2) .dce-post-block, {{WRAPPER}}:not(.dce-col-3) .dce-post-item:nth-child(even) .dce-post-block' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'grid_type' => [ 'flex' ],
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_fallback_style',
			[
				'label' => __( 'No Results Behaviour', 'dynamic-content-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'fallback!' => '',
					'fallback_type' => 'text',
				],
			]
		);
		$this->add_responsive_control(
			'fallback_align', [
				'label' => __( 'Text Alignment', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'dynamic-content-for-elementor' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'dynamic-content-for-elementor' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'dynamic-content-for-elementor' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'left',
				'selectors' => [
					'{{WRAPPER}} .dce-posts-fallback' => 'text-align: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'fallback_color', [
				'label' => __( 'Text Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dce-posts-fallback' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name' => 'fallback_typography',
				'selector' => '{{WRAPPER}} .dce-posts-fallback',
			]
		);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'fallback_text_shadow',
				'selector' => '{{WRAPPER}} .dce-posts-fallback',
			]
		);

		$this->end_controls_section();

	}

	// Render main
	public function render() {

		$this->parent->render();
		$this->parent->query_posts();
		$query = $this->parent->get_query();
		$this->counter = 0;
		$this->add_search_filter_class();
		$fallback = $this->parent->get_settings( 'fallback' );

		if ( 'masonry' === $this->get_instance_value( 'grid_type' ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				Helper::notice( '', __( 'This feature is not seen correctly in the Elementor editor due to technical limitations but works correctly in the frontend.', 'dynamic-content-for-elementor' ) );
			}
		}

		if ( ! $query->found_posts && $fallback ) {
			$this->render_fallback();
		} else {
			$this->render_loop_start();

			if ( $query->in_the_loop ) {
				$this->current_permalink = get_permalink();
				$this->current_id = get_the_ID();
				$this->render_post();
			} else {
				while ( $query->have_posts() ) {
					$query->the_post();

					$this->current_permalink = get_permalink();
					$this->current_id = get_the_ID();
					$this->render_post();
				}
			}

			wp_reset_postdata();

			$this->render_loop_end();
		}
	}

	protected function render_post() {
		$style_items = $this->parent->get_settings( 'style_items' );

		$this->render_post_start();

		if ( $style_items == 'template' ) {
			$this->render_post_template();
		} else {
			$this->render_post_items();
		}

		$this->render_post_end();

		$this->counter ++;
	}

	protected function render_post_template() {

		$template_id = $this->parent->get_settings( 'template_id' );
		$templatemode_enable_2 = $this->parent->get_settings( 'templatemode_enable_2' );
		$template_2_id = $this->parent->get_settings( 'template_2_id' );
		$native_templatemode_enable = $this->parent->get_settings( 'native_templatemode_enable' );

		if ( $native_templatemode_enable ) {

			$type_of_posts = get_post_type( $this->current_id );
			$cptaxonomy = get_post_taxonomies( $this->current_id );

			$options = get_option( DCE_OPTIONS );

			// 2 - Archive
			$templatesystem_template_key = 'dyncontel_field_archive' . $type_of_posts;
			$post_template_id = $options[ $templatesystem_template_key ];

			if ( isset( $cptaxonomy ) && count( $cptaxonomy ) > 0 ) {

				$key = $cptaxonomy[0];
				$archive_key = 'dyncontel_field_archive_taxonomy_' . $key;
				// 3 - Taxonomy
				if ( isset( $options[ $archive_key ] ) ) {
					$post_template_id_taxo = $options[ $archive_key ];
					if ( ! empty( $post_template_id_taxo ) && $post_template_id_taxo > 0 ) {
						$templatesystem_template_key = $archive_key;
					}
				}

				$post_template_id = $options[ $templatesystem_template_key ];

				//foreach ($cptaxonomy as $key) {
				// 4 - Terms
				$cptaxonomyterm = get_the_terms( $this->current_id, $cptaxonomy[0]/* $key */ );

				if ( isset( $cptaxonomyterm ) && $cptaxonomyterm ) {
					foreach ( $cptaxonomyterm as $cpterm ) {
						$term_id = $cpterm->term_id;
						$post_template_id_term = get_term_meta( $term_id, 'dynamic_content_block', true );
						if ( ! empty( $post_template_id_term ) ) {
							$post_template_id = $post_template_id_term;
						}
					}
				}
			}
		} elseif ( $templatemode_enable_2 ) {
			if ( $this->counter % 2 == 0 ) {
				// Even
				$post_template_id = $template_id;
			} else {
				// Odd
				$post_template_id = $template_2_id;
			}
		} else {
			$post_template_id = $template_id;
		}

		if ( $post_template_id ) {
			$this->render_template( $post_template_id );
		}
	}

	protected function render_template( $id_temp ) {
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$inlinecss = 'inlinecss="true"';
		} else {
			$inlinecss = '';
		}

		echo do_shortcode( '[dce-elementor-template id="' . $id_temp . '" post_id="' . $this->current_id . '" ' . $inlinecss . ']' );
	}

	protected function render_post_items() {
		$_skin = $this->parent->get_settings( '_skin' );
		$style_items = $this->parent->get_settings( 'style_items' );
		$post_items = $this->parent->get_settings( 'list_items' );

		$hover_animation = $this->parent->get_settings( 'hover_content_animation' );
		$animation_class = ! empty( $hover_animation ) && $style_items != 'float' && $_skin != 'gridtofullscreen3d' ? ' elementor-animation-' . $hover_animation : '';

		$hover_effects = $this->parent->get_settings( 'hover_text_effect' );
		$hoverEffects_class = ! empty( $hover_effects ) && $style_items == 'float' && $_skin != 'gridtofullscreen3d' ? ' dce-hover-effect-' . $hover_effects . ' dce-hover-effect-content dce-close' : '';

		$hoverEffects_start = ! empty( $hover_effects ) && $style_items == 'float' && $_skin != 'gridtofullscreen3d' ? '<div class="dce-hover-effect-' . $hover_effects . ' dce-hover-effect-content dce-close">' : '';
		$hoverEffects_end = ! empty( $hover_effects ) && $style_items == 'float' ? '</div>' : '';

		$imagearea_start = '';
		$contentarea_start = '';
		$area_end = '';

		if ( $style_items && $style_items != 'default' ) {
			$imagearea_start = '<div class="dce-image-area dce-item-area">';
			$contentarea_start = '<div class="dce-content-area dce-item-area' . $animation_class . '">';
			$area_end = '</div>';

			echo $imagearea_start;
			foreach ( $post_items as $item ) {
				$_id = $item['_id'];
				$show_item = $item['show_item'];

				if ( $_id == 'item_image' && $show_item == 'check' ) {
					$this->render_repeateritem_start( $_id );
					$this->render_image( $item );
					$this->render_repeateritem_end();
				}
			}
			echo $area_end;
		}
		echo $hoverEffects_start . $contentarea_start;
		foreach ( $post_items as $key => $item ) {
			$_id = $item['_id'];
			$show_item = $item['show_item'];

			if ( $show_item == 'check' ) {
				// se ci sono i 2 wrapper (image-area e content-area) escludo dal render immagine
				if ( $_id != 'item_image' && $imagearea_start ) {
					$this->render_repeateritem_start( $_id );
				}
				// se il layout è default renderizzo tutto
				if ( ! $imagearea_start ) {
					$this->render_repeateritem_start( $_id );
				}

				if ( $_id == 'item_image' && ! $imagearea_start ) {
					$this->render_image( $item );
				}
				if ( $_id == 'item_title' ) {
					$this->render_title( $item );
				}
				if ( $_id == 'item_date' ) {
					$this->render_date( $item );
				}
				if ( $_id == 'item_author' ) {
					$this->render_author( $item );
				}
				if ( $_id == 'item_termstaxonomy' ) {
					$this->render_termstaxonomy( $item );
				}
				if ( $_id == 'item_content' ) {
					$this->render_content( $item );
				}
				if ( $_id == 'item_custommeta' ) {
					$this->render_custommeta();
				}
				if ( $_id == 'item_readmore' ) {
					$this->render_readmore( $item );
				}
				if ( $_id == 'item_posttype' ) {
					$this->render_posttype( $item );
				}
				if ( $_id != 'item_image' && $imagearea_start ) {
					$this->render_repeateritem_end();
				}
				if ( ! $imagearea_start ) {
					$this->render_repeateritem_end();
				}
			}
		}
		echo $area_end . $hoverEffects_end;
	}

	protected function render_repeateritem_start( $id ) {
		$this->parent->add_render_attribute( 'dynposts_' . $id, [
			'class' => [
				'dce-item',
				'dce-' . $id,
				'elementor-repeater-item-' . $id,
			],
		]
		);

		echo '<div ' . $this->parent->get_render_attribute_string( 'dynposts_' . $id ) . '>';
	}

	protected function render_repeateritem_end() {
		echo '</div>';
	}

	// Render items
	protected function render_image( $settings ) {

		$use_bgimage = $settings['use_bgimage'];
		$use_overlay = $settings['use_overlay'];
		$use_overlay_hover = $this->parent->get_settings( 'use_overlay_hover' );
		$use_link = $settings['use_link'];

		$setting_key = $settings['thumbnail_size_size'];

		$image_attr = [
			'class' => $this->get_image_class(),
		];
		$image_url = Group_Control_Image_Size::get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail_size', $settings );
		$thumbnail_html = wp_get_attachment_image( get_post_thumbnail_id(), $setting_key, false, $image_attr );

		if ( empty( $thumbnail_html ) ) {
			return;
		}

		$bgimage = '';
		if ( $use_bgimage ) {
			$bgimage = ' dce-post-bgimage';
		}
		$overlayimage = '';
		if ( $use_overlay ) {
			$overlayimage = ' dce-post-overlayimage';
		}
		$overlayhover = '';
		if ( $use_overlay_hover ) {
			$overlayhover = ' dce-post-overlayhover';
		}
		$html_tag = 'div';
		$attribute_link = '';
		if ( $use_link ) {
			$html_tag = 'a';
			$attribute_link = ' href="' . $this->current_permalink . '"';
		}
		echo '<' . $html_tag . ' class="dce-post-image' . $bgimage . $overlayimage . $overlayhover . '"' . $attribute_link . '>';

		if ( $use_bgimage ) {
			echo '<figure class="dce-img dce-bgimage" style="background-image: url(' . $image_url . '); background-repeat: no-repeat; background-size: cover; display: block;"></figure>';
		} else {
			echo '<figure class="dce-img">' . $thumbnail_html . '</figure>';
		}

		echo '</' . $html_tag . '>';
	}

	protected function render_title( $settings ) {

		$html_tag = ! empty( \DynamicContentForElementor\Helper::validate_html_tag( $settings['html_tag'] ) ) ? \DynamicContentForElementor\Helper::validate_html_tag( $settings['html_tag'] ) : 'h3';
		$title_text = get_the_title() ? esc_html( get_the_title() ) : get_the_ID();

		$use_link = $settings['use_link'];
		$open_target_blank = $settings['open_target_blank'];
		echo sprintf( '<%1$s class="dce-post-title">', $html_tag );

		echo $this->render_item_link_text( $title_text, $use_link, $this->current_permalink, $open_target_blank );

		echo sprintf( '</%s>', $html_tag );

	}

	protected function render_readmore( $settings ) {
		$readmore_text = $settings['readmore_text'];
		$readmore_size = $settings['readmore_size'];
		$attribute_button = 'button_' . $this->counter;

		$open_target_blank = $settings['open_target_blank'];

		$this->parent->add_render_attribute( $attribute_button, 'href', $this->current_permalink );

		$this->parent->add_render_attribute( $attribute_button, 'class', [ 'elementor-button-link', 'elementor-button', 'dce-button' ] );
		$this->parent->add_render_attribute( $attribute_button, 'role', 'button' );

		if ( ! empty( $readmore_size ) ) {
			$this->parent->add_render_attribute( $attribute_button, 'class', 'elementor-size-' . $readmore_size );
		}
		?>
		<div class="dce-post-button">
			<a <?php echo $this->parent->get_render_attribute_string( $attribute_button ); ?> <?php if ( $open_target_blank ) {
				echo 'target="_blank"'; } ?>>
			<?php echo $readmore_text; ?>
			</a>
		</div>
		<?php
	}

	protected function render_author( $settings ) {
		$avatar_image_size = $settings['author_image_size'];
		$use_link = $settings['use_link'];
		$author_user_key = $settings['author_user_key'];

		$author = [];

		$avatar_args['size'] = $avatar_image_size;

		$user_id = get_the_author_meta( 'ID' );
		$author['avatar'] = get_avatar_url( $user_id, $avatar_args );
		$author['posts_url'] = get_author_posts_url( $user_id );
		?>
		  <div class="dce-post-author">
			<div class="dce-author-image">
				<?php foreach ( $author_user_key as $akey => $author_value ) {

					if ( $author_value == 'avatar' ) { ?>

						<div class="dce-author-avatar">
							<img class="dce-img" src="<?php echo $author['avatar']; ?>" alt="<?php echo get_the_author_meta( 'display_name' ); ?>" />
						</div>

					<?php }
				} ?>
			</div>
			<div class="dce-author-text">
				<?php foreach ( $author_user_key as $akey => $author_value ) {

					if ( $author_value != 'avatar' ) {

						echo '<div class="dce-author-' . $author_value . '">' . get_the_author_meta( $author_value ) . '</div>';

					}
				} ?>
			</div>
			<?php
			echo '</div>';
	}

	protected function render_content( $settings ) {
		$content_type = $settings['content_type'];
		$textcontent_limit = $settings['textcontent_limit'];
		$use_link = $settings['use_link'];

		echo '<div class="dce-post-content">';
		// Content
		if ( $content_type == 1 ) {
			if ( $textcontent_limit ) {
				echo $this->limit_content( $textcontent_limit );
			} else {
				echo wpautop( get_the_content() );
			}
		}
		// Excerpt
		if ( $content_type == 0 ) {
			$post = get_post();
			echo $post->post_excerpt;
		}
		echo '</div>';
	}

	protected function render_posttype( $settings ) {
		$posttype_label = $settings['posttype_label'];
		$type = get_post_type();
		$postTypeObj = get_post_type_object( $type );

		switch ( $posttype_label ) {
			case 'plural':
				$posttype = $postTypeObj->labels->name;
				break;
			case 'singular':
			default:
				$posttype = $postTypeObj->labels->singular_name;
				break;
		}

		echo '<div class="dce-post-ptype">';
		if ( isset( $postTypeObj->label ) ) {
			echo $posttype;
		}
		echo '</div>';
	}

	protected function render_date( $settings ) {
		$date_type = $settings['date_type'];
		$date_format = $settings['date_format'];
		$icon_enable = $settings['icon_enable'];

		$use_link = $settings['use_link'];
		if ( ! $date_format ) {
			$date_format = get_option( 'date_format' );
		}
		$icon = '';
		if ( $icon_enable ) {
			$icon = '<i class="dce-post-icon fa fa-calendar" aria-hidden="true"></i> ';
		}
		switch ( $date_type ) {
			case 'modified':
				$date = get_the_modified_date( $date_format, '' );

				break;

			case 'publish':
			default:
				$date = get_the_date( $date_format, '' );

				break;
		} ?>
		<div class="dce-post-date"><?php echo $icon . $date; ?></div><?php
	}

	protected function render_custommeta() {
		$custommeta_items = $this->parent->get_settings( 'custommeta_items' );
		if ( is_array( $custommeta_items ) && ! empty( $custommeta_items[0]['metafield_key'] ) ) {
			echo '<div class="dce-post-custommeta">';
			foreach ( $custommeta_items as $key => $item ) {
				$_id = $item['_id'];

				$metafield_key = $item['metafield_key'];
				$metafield_type = $item['metafield_type'];

				$image_size_key = $item['image_size_size'];

				$metafield_button_label = $item['metafield_button_label'];
				$metafield_button_size = $item['metafield_button_size'];

				$metafield_date_format_source = $item['metafield_date_format_source'];
				$metafield_date_format_display = $item['metafield_date_format_display'];

				$html_tag_item = $item['html_tag_item'];

				$link_to = $item['link_to'];
				$link = $item['link'];

				$attribute_a_link = 'a_link_' . $this->counter . '_' . $key;
				$attribute_custommeta_item = 'custommeta_item-' . $this->counter . '_' . $key;

				$meta_value = get_post_meta( $this->current_id, $metafield_key, true );
				$meta_html = '';

				switch ( $metafield_type ) {
					case 'date':
						if ( $metafield_date_format_source ) {
							if ( $metafield_date_format_source == 'timestamp' ) {
								$timestamp = $meta_value;
							} else {
								$d = \DateTime::createFromFormat( $metafield_date_format_source, $meta_value );
								if ( $d ) {
									$timestamp = $d->getTimestamp();
								} else {
									$timestamp = strtotime( $meta_value );
								}
							}
						} else {
							$timestamp = strtotime( $meta_value );
						}
						$meta_html = date_i18n( $metafield_date_format_display, $timestamp );
						break;
					case 'image':
						$image_attr = [
							'class' => 'dce-img',
						];
						if ( is_string( $meta_value ) ) {
							if ( is_numeric( $meta_value ) ) {
								$image_html = wp_get_attachment_image( $meta_value, $image_size_key, false, $image_attr );

							} else {
								$image_html = '<img src="' . $meta_value . '" />';
							}
						} elseif ( is_numeric( $meta_value ) ) {
							$image_html = wp_get_attachment_image( $meta_value, $image_size_key, false, $image_attr );

						} elseif ( is_array( $meta_value ) ) {
							// TODO ... da valutare come gestire il caso di un'array...

							$imageSrc = wp_get_attachment_image_src( $meta_value ['ID'], $thumbnail_size );
							$imageSrcUrl = $imageSrc[0];
						}
						$meta_html = $image_html;
						break;
					case 'button':
						$this->parent->add_render_attribute( $attribute_a_link, 'href', $meta_value );
						$this->parent->add_render_attribute( $attribute_a_link, 'role', 'button' );
						if ( ! empty( $metafield_button_size ) ) {
							$this->parent->add_render_attribute( $attribute_a_link, 'class', 'elementor-size-' . $metafield_button_size );
						}
						$this->parent->add_render_attribute( $attribute_a_link, 'class', [ 'elementor-button-link', 'elementor-button', 'dce-button' ] );

						$meta_html = $metafield_button_label;
						break;
					case 'textarea': //non esiste
						$meta_html = nl2br( $meta_value );
						break;
					case 'textfield': //non esiste
					case 'wysiwyg': //non esiste
						$meta_html = wpautop( $meta_value );
						break;
					case 'text':
						if ( $html_tag_item ) {
							$meta_html = '<' . $html_tag_item . '>' . $meta_value . '</' . $html_tag_item . '>';
						} else {
							$meta_html = $meta_value;
						}
						break;
					default:
						$meta_html = $meta_value;

				}

				switch ( $link_to ) {
					case 'home':
						$this->parent->add_render_attribute( $attribute_a_link, 'href', esc_url( get_home_url() ) );
						break;
					case 'post':
						$this->parent->add_render_attribute( $attribute_a_link, 'href', $this->current_permalink );
						break;
					case 'custom':
						if ( ! empty( $link ) ) {
							$this->parent->add_link_attributes( $attribute_a_link, $link );
						}
						break;
					default:
				}
				$linkOpen = '';
				$linkClose = '';
				if ( $link_to ) {
					$this->parent->add_render_attribute( $attribute_a_link, 'class', [ 'dce-link' ] );

					$linkOpen = '<a ' . $this->parent->get_render_attribute_string( $attribute_a_link ) . '>';
					$linkClose = '</a>';
				}
				if ( isset( $meta_html ) ) {
						$this->parent->add_render_attribute( $attribute_custommeta_item, [
							'class' => [
								'dce-meta-item',
								'dce-meta-' . $_id,
								'dce-meta-' . $metafield_type,
								'elementor-repeater-item-' . $_id,
							],
						]
					);
					echo '<div ' . $this->parent->get_render_attribute_string( $attribute_custommeta_item ) . '>' . $linkOpen . $meta_html . $linkClose . '</div>';
				}
			}
			echo '</div>';
		}
	}

	protected function render_termstaxonomy( $settings ) {
		$taxonomy_filter = $settings['taxonomy_filter'];
		$separator_chart = $settings['separator_chart'];
		$only_parent_terms = $settings['only_parent_terms'];
		$block_enable = $settings['block_enable']; //style
		$icon_enable = $settings['icon_enable'];

		$use_link = $settings['use_link'];
		$open_target_blank = $settings['open_target_blank'];

		$term_list = [];

		$taxonomy = get_post_taxonomies( $this->current_id );

		echo '<div class="dce-post-terms">';
		foreach ( $taxonomy as $tax ) {

			if ( isset( $taxonomy_filter ) && ! empty( $taxonomy_filter ) ) {
				if ( ! in_array( $tax, $taxonomy_filter ) ) {
					continue;
				}
			}
			if ( $tax != 'post_format' ) {

				$term_list = Helper::get_the_terms_ordered( $this->current_id, $tax );
				if ( $term_list && is_array( $term_list ) && count( $term_list ) > 0 ) {

					echo '<ul class="dce-terms-list dce-taxonomy-' . $tax . '">';

					// Ciclo i termini
					$cont = 1;
					$divider = '';
					foreach ( $term_list as $term ) {

						if ( ! empty( $only_parent_terms ) ) {
							if ( $only_parent_terms == 'yes' ) {
								if ( $term->parent ) {
									continue;
								}
							}
							if ( $only_parent_terms == 'children' ) {
								if ( ! $term->parent ) {
									continue;
								}
							}
						}
						if ( $icon_enable && $cont == 1 ) {
							$icon = '';
							if ( is_taxonomy_hierarchical( $tax ) ) {
								$icon = '<i class="dce-post-icon fa fa-folder-open" aria-hidden="true"></i> ';
							} else {
								$icon = '<i class="dce-post-icon fa fa-tags" aria-hidden="true"></i> ';
							}
							echo $icon;
						}
						$term_url = trailingslashit( get_term_link( $term ) );

						if ( $cont > 1 && ! $block_enable ) {
							$divider = '<span class="dce-separator">' . $separator_chart . '</span>';
						}
						echo '<li class="dce-term-item">';
						echo $divider . '<span class="dce-term dce-term-' . $term->term_id . '" data-dce-order="' . $term->term_order . '">' . $this->render_item_link_text( $term->name, $use_link, $term_url, $open_target_blank ) . '</span>';
						echo '</li>';
						//
						$cont++;
					} //end foreach terms
					echo '</ul>';
				} //end if termslist
			} //end exclusion
		} //end foreach taxonomy

		echo '</div>';
	}

	protected function render_item_link_text( $link_text = '', $use_link = '', $url = '', $open_target_blank = '' ) {
		if ( ! empty( $use_link ) && $url && $link_text ) {
			$open_target_blank = ! empty( $open_target_blank ) ? ' target="_blank"' : '';
			return '<a href="' . $url . '"' . $open_target_blank . '>' . $link_text . '</a>';
		} else {
			return $link_text ? $link_text : '';
		}
	}

	// Render post item
	protected function render_post_start() {
		$hover_animation = $this->parent->get_settings( 'hover_animation' );
		$animation_class = ! empty( $hover_animation ) ? ' elementor-animation-' . $hover_animation : '';

		$style_items = $this->parent->get_settings( 'style_items' );
		$hover_effects = $this->parent->get_settings( 'hover_text_effect' );
		$hoverEffects_class = ! empty( $hover_effects ) && $style_items == 'float' ? ' dce-hover-effects' : '';
		$data_post_id = ' data-dce-post-id="' . $this->current_id . '"';
		$data_post_id = ' data-dce-post-index="' . $this->counter . '"';
		$item_class = ' ' . $this->get_item_class();
		$data_post_link = '';

		if ( $this->parent->get_settings( 'templatemode_linkable' ) ) {
			$data_post_link = ' data-post-link="' . get_permalink( $this->current_id ) . '"';
		}

		?>
		<article <?php post_class( [ 'dce-post dce-post-item' . $item_class ] );
		echo $data_post_id . $data_post_link; ?> >
			<div class="dce-post-block<?php echo $hoverEffects_class . $animation_class; ?>">
		<?php
	}

	protected function render_post_end() {
		?>
			</div>
		</article>
		<?php
	}
	// -----

	protected function render_fallback() {
		$fallback_type = $this->parent->get_settings( 'fallback_type' );
		$fallback_text = $this->parent->get_settings( 'fallback_text' );
		$fallback_template = $this->parent->get_settings( 'fallback_template' );

		$this->parent->add_render_attribute( 'dynposts_container', [
			'class' => [
				'dce-posts-container',
				'dce-posts',
				$this->get_scrollreveal_class(),
				$this->get_container_class(),
			],
		] );

		$this->parent->add_render_attribute( 'dynposts_container_wrap', [
			'class' => [
				'dce-posts-fallback',
			],
		] );
		?>
		<div <?php echo $this->parent->get_render_attribute_string( 'dynposts_container' ); ?>>
			<div <?php echo $this->parent->get_render_attribute_string( 'dynposts_container_wrap' ); ?>>
			<?php
			if ( isset( $fallback_type ) && $fallback_type === 'template' ) {
				$fallback_content = '[dce-elementor-template id="' . $fallback_template . '"]';
			} else {
				$fallback_content = '<p>' . $fallback_text . '</p>';
			}
			echo $fallback_content;
			?>
		</div>
	</div>
		<?php
	}

	// Render loop wrapper -----------------------------------------
	protected function render_loop_start() {

		$this->parent->add_render_attribute( 'dynposts_container', [
			'class' => [
				'dce-posts-container is_infiniteScroll',
				'dce-posts',
				$this->get_scrollreveal_class(),
				$this->get_container_class(),
			],
		] );

		$this->parent->add_render_attribute( 'dynposts_container_wrap', [
			'class' => [
				'dce-posts-wrapper',
				$this->get_wrapper_class(),
			],
		] );

		?>
		<div <?php echo $this->parent->get_render_attribute_string( 'dynposts_container' ); ?>>
			<?php $this->render_posts_before(); ?>
			<div <?php echo $this->parent->get_render_attribute_string( 'dynposts_container_wrap' ); ?>>
		<?php
		$this->render_postsWrapper_before();
	}

	protected function render_loop_end() {

		$this->render_postsWrapper_after();
		?>
			</div>
			<?php
			$this->render_posts_after();
			?>
		</div>
		<?php

		$settings = $this->parent->get_settings_for_display();
		$p_query = $this->parent->get_query();
		$postlength = $p_query->post_count;
		$posts_per_page = $p_query->query_vars['posts_per_page'];

		// Pagination
		if ( $settings['pagination_enable'] ) {
			\DynamicContentForElementor\Helper::numeric_query_pagination( $p_query->max_num_pages, $settings );
		}

		// Infinite Scroll
		if ( ( $settings['infiniteScroll_enable']
			  && $settings['query_type'] != 'search_filter'
			  && $postlength >= $settings['num_posts']
			  && $settings['num_posts'] >= 0 )
			  || $settings['infiniteScroll_enable']
			  && $settings['query_type'] == 'search_filter'
			  && $postlength >= $posts_per_page
			  || \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$preview_mode = '';

			if ( $settings['infiniteScroll_enable_status'] ) {
				?>
				<nav class="infiniteScroll">
					<div class="page-load-status<?php echo $preview_mode; ?>">

						<?php
						if ( $settings['infiniteScroll_loading_type'] == 'text' ) {
							?>
							<div class="infinite-scroll-request status-text"><?php echo $settings['infiniteScroll_label_loading']; ?></div>
							<?php
						} elseif ( $settings['infiniteScroll_loading_type'] == 'ellips' ) {
							?>
							<div class="loader-ellips infinite-scroll-request">
								<span class="loader-ellips__dot"></span>
								<span class="loader-ellips__dot"></span>
								<span class="loader-ellips__dot"></span>
								<span class="loader-ellips__dot"></span>
							</div>
							<?php
						}
						?>
						<div class="infinite-scroll-last status-text"><?php echo $settings['infiniteScroll_label_last']; ?></div>
						<div class="infinite-scroll-error status-text"><?php echo $settings['infiniteScroll_label_error']; ?></div>

						<div class="pagination" role="navigation">
						  <?php if ( $settings['query_type'] != 'search_filter' ) { ?>
							<a class="pagination__next" href="<?php echo \DynamicContentForElementor\Helper::get_next_pagination(); ?>"></a>
						  <?php } else { ?>
							<a class="pagination__next" href="<?php echo \DynamicContentForElementor\Helper::get_next_pagination_sf(); ?>"></a>
						  <?php } ?>
						</div>
					</div>


				</nav>
				<?php
			}

			// Infinite Scroll - Button
			if ( $settings['infiniteScroll_trigger'] == 'button' ) {
				?>
				<div class="infiniteScroll">
					<button class="view-more-button"><?php echo $settings['infiniteScroll_label_button']; ?></button>
				</div>
				<?php
			}
		}
	}

	protected function render_posts_before(){}
	protected function render_posts_after(){}
	protected function render_postsWrapper_before(){}
	protected function render_postsWrapper_after(){}

	public function get_container_class() {
		return 'dce-skin-' . $this->get_id();
	}
	public function get_wrapper_class() {
		return 'dce-wrapper-' . $this->get_id();
	}
	public function get_item_class() {
		return 'dce-item-' . $this->get_id();
	}
	public function get_image_class() {}
	public function get_scrollreveal_class() {
		return '';
	}

	public function filter_excerpt_length() {
		return $this->get_instance_value( 'textcontent_limit' );
	}

	public function filter_excerpt_more( $more ) {
		return '';
	}
	protected function limit_content( $limit ) {
		$post = get_post();
		$content = $post->post_content;
		$content = mb_substr( wp_strip_all_tags( $content ), 0, $limit ) . '&hellip;';

		return $content;
	}

	protected function add_search_filter_class() {
		$search_filter_id = $this->parent->get_settings( 'search_filter_id' );

		if ( $this->parent->get_settings( 'query_type' ) === 'search_filter' || isset( $search_filter_id ) ) {
			$sfid = intval( $search_filter_id );

			$element_class = 'search-filter-results-' . $sfid;

			$args = array(
				'class' => array( $element_class ),
			);

			$this->parent->add_render_attribute( '_wrapper', $args );
		}
	}

}
