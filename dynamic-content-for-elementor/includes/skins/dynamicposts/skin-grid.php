<?php
namespace DynamicContentForElementor\Includes\Skins;

use Elementor\Controls_Manager;
use DynamicContentForElementor\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Skin_Grid extends Skin_Base {

	public function get_script_depends() {
		return [];
	}

	public function get_style_depends() {
		return [];
	}

	public function get_id() {
		return 'grid';
	}

	public function get_title() {
		return __( 'Grid', 'dynamic-content-for-elementor' );
	}

	protected function _register_controls_actions() {

		parent::_register_controls_actions();

		add_action( 'elementor/element/dce-dynamicposts-v2/section_dynamicposts/after_section_end', [ $this, 'register_additional_grid_controls' ], 20 );

	}

	public function register_additional_grid_controls() {

		$this->start_controls_section(
			'section_grid', [
				'label' => '<i class="dynicon eicon-posts-grid"></i> ' . __( 'Grid', 'dynamic-content-for-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
		  'grid_type', [
			  'label' => __( 'Type', 'dynamic-content-for-elementor' ),
			  'type' => 'images_selector',
			  'toggle' => false,
			  'type_selector' => 'icon',
			  'columns_grid' => 3,
			  'options' => [
				  'flex' => [
					  'title' => __( 'Flex', 'dynamic-content-for-elementor' ),
					  'return_val' => 'val',
					  'icon' => 'eicon-gallery-grid',
				  ],
				  'masonry' => [
					  'title' => __( 'Masonry', 'dynamic-content-for-elementor' ),
					  'return_val' => 'val',
					  'icon' => 'eicon-gallery-masonry',
				  ],
				  /*'justified' => [
					  'title' => __('Justified','dynamic-content-for-elementor'),
					  'return_val' => 'val',
					  'icon' => 'eicon-gallery-justified',
				  ],*/
				  'blog' => [
					  'title' => __( 'Blog', 'dynamic-content-for-elementor' ),
					  'return_val' => 'val',
					  'icon' => 'eicon-gallery-group',
				  ],
			  ],
			  'default' => 'flex',
			  'frontend_available' => true,
		  ]
		);

		$this->add_control(
			'blog_template_id',
			[
				'label' => __( 'First item Template', 'dynamic-content-for-elementor' ),
				'type' => 'ooo_query',
				'placeholder' => __( 'Template Name', 'dynamic-content-for-elementor' ),
				'label_block' => true,
				'query_type' => 'posts',
				'object_type' => 'elementor_library',
				'separator' => 'after',
				'condition' => [
					$this->get_control_id( 'grid_type' ) => [ 'blog' ],
				],
			]
		);

		$this->add_responsive_control(
			'column_blog', [
				'label' => __( 'First item Column', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'tablet_default' => '3',
				'mobile_default' => '1',
				'options' => [
					'1'     => '1/1',
					'2'     => '1/2',
					'3'     => '1/3',
					'1.5'   => '2/3',
					'4'     => '1/4',
					'1.34'  => '3/4',
					'1.67'  => '3/5',
					'1.25'  => '4/5',
				],
				'selectors' => [
					'{{WRAPPER}} .dce-posts-container.dce-skin-grid-blog .dce-post-item:nth-child(1)' => 'width: calc(100% / {{VALUE}}); flex-basis: calc( 100% / {{VALUE}} );',
				],
				'condition' => [
					$this->get_control_id( 'grid_type' ) => [ 'blog' ],
				],
			]
		);
		$this->add_responsive_control(
			'columns_grid', [
				'label' => __( 'Columns', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '4',
				'tablet_default' => '3',
				'mobile_default' => '1',
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
					'8' => '8',
					'9' => '9',
					'10' => '10',
					'11' => '11',
					'12' => '12',
				],
				'prefix_class' => 'dce-col%s-',
				'selectors' => [
					'{{WRAPPER}} .dce-posts-container.dce-skin-grid .dce-post-item' => 'width: calc(100% / {{VALUE}}); flex: 0 1 calc( 100% / {{VALUE}} );',
				],
			]
		);

		$this->add_responsive_control(
			'grid_item_width', [
				'label' => __( 'Width', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vh' ],
				'default' => [
					'size' => '',
				],
				'range' => [
					'px' => [
						'max' => 800,
						'min' => 0,
						'step' => 1,
					],
				],
				'condition' => [
					$this->get_control_id( 'columns_grid' ) => '1',
					$this->get_control_id( 'grid_type' ) => 'flex',
				],
				'selectors' => [
					'{{WRAPPER}} .dce-posts-container.dce-skin-grid' => 'margin: 0 auto; width: {{SIZE}}{{UNIT}};',

				],
			]
		);
		$this->add_responsive_control(
			'grid_alternate', [
				'label' => __( 'Alternate', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vw' ],
				'default' => [
					'size' => '',
				],
				'range' => [
					'px' => [
						'max' => 400,
						'min' => 0,
						'step' => 1,
					],
				],
				'condition' => [
					$this->get_control_id( 'columns_grid' ) => '1',
					$this->get_control_id( 'grid_type' ) => 'flex',
				],
				'selectors' => [
					'{{WRAPPER}} .dce-posts-container.dce-skin-grid .dce-posts-wrapper .dce-post-item:nth-child(even)' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .dce-posts-container.dce-skin-grid .dce-posts-wrapper .dce-post-item:nth-child(odd)' => 'margin-left: {{SIZE}}{{UNIT}};',

				],
			]
		);

		$this->add_control(
			'flex_grow', [
				'label' => __( 'Flex grow', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'label_block' => false,
				'options' => [
					'1' => [
						'title' => __( '1', 'dynamic-content-for-elementor' ),
						'icon' => 'fa fa-check',
					],
					'0' => [
						'title' => __( '0', 'dynamic-content-for-elementor' ),
						'icon' => 'fa fa-ban',
					],
				],
				'default' => 1,
				'selectors' => [
					'{{WRAPPER}} .dce-posts-container.dce-skin-grid .dce-post-item' => 'flex-grow: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'grid_type!' ) => [ 'masonry' ],
				],
			]
		);

		$this->add_responsive_control(
			'h_pos_postitems', [
				'label' => __( 'Horizontal position', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
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
					'space-between' => [
						'title' => __( 'Space Between', 'dynamic-content-for-elementor' ),
						'icon' => 'eicon-h-align-stretch',
					],
					'space-around' => [
						'title' => __( 'Space Around', 'dynamic-content-for-elementor' ),
						'icon' => 'eicon-h-align-stretch',
					],
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .dce-posts-container.dce-skin-grid .dce-posts-wrapper' => 'justify-content: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'flex_grow' ) => '0',
					$this->get_control_id( 'grid_type!' ) => [ 'masonry' ],
				],
			]
		);
		$this->add_responsive_control(
			'v_pos_postitems', [
				'label' => __( 'Vertical position', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => __( 'Top', 'dynamic-content-for-elementor' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => __( 'Middle', 'dynamic-content-for-elementor' ),
						'icon' => 'eicon-v-align-middle',
					],
					'flex-end' => [
						'title' => __( 'Down', 'dynamic-content-for-elementor' ),
						'icon' => 'eicon-v-align-bottom',
					],
					'stretch' => [
						'title' => __( 'Stretch', 'dynamic-content-for-elementor' ),
						'icon' => 'eicon-v-align-stretch',
					],
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .dce-posts-container.dce-skin-grid .dce-posts-wrapper' => 'align-items: {{VALUE}};',
					'{{WRAPPER}} .dce-posts-container.dce-skin-grid .dce-item-area' => 'justify-content: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'grid_type!' ) => [ 'masonry' ],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_scrollreveal', [
				'label' => '<i class="dynicon eicon-scroll"></i> ' . __( 'Scroll Reveal', 'dynamic-content-for-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'infiniteScroll_enable' => '',
				],
			]
		);
		$this->add_control(
			'scrollreveal_effect_type', [
				'label' => __( 'Effect', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '0',

				'separator' => 'after',
				'options' => [
					'0' => 'None',
					'1' => 'Opacity',
					'2' => 'Move Up',
					'3' => 'Scale Up',
					'4' => 'Fall Perspective',
					'5' => 'Fly',
					'6' => 'Flip',
					'7' => 'Helix',
					'8' => 'Bounce',
				],
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		parent::register_style_controls();

		$this->start_controls_section(
			'section_style_grid',
			[
				'label' => __( 'Grid', 'dynamic-content-for-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label' => __( 'Columns Gap', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .dce-posts-container.dce-skin-grid .dce-post-item' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}} .dce-posts-container.dce-skin-grid .dce-posts-wrapper' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label' => __( 'Rows Gap', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 35,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .dce-post-item' => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render_post() {

		$style_items = $this->parent->get_settings( 'style_items' );
		$blog_template_id = $this->get_instance_value( 'blog_template_id' );
		$grid_type = $this->get_instance_value( 'grid_type' );

		$this->render_post_start();

		if ( $this->counter == 0 && $blog_template_id && $grid_type == 'blog' ) {
			$this->render_template( $blog_template_id );
		} else {
			if ( $style_items == 'template' ) {
				$this->render_post_template();
			} else {
				$this->render_post_items();
			}
		}
		$this->render_post_end();
		$this->counter ++;
	}

	public function get_container_class() {
		return 'dce-skin-' . $this->get_id() . ' dce-skin-' . $this->get_id() . '-' . $this->get_instance_value( 'grid_type' );
	}

	public function get_scrollreveal_class() {
		if ( $this->get_instance_value( 'scrollreveal_effect_type' ) ) {
			return 'reveal-effect reveal-effect-' . $this->get_instance_value( 'scrollreveal_effect_type' );
		}
	}

}
