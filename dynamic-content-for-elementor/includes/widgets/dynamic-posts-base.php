<?php
namespace DynamicContentForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

use DynamicContentForElementor\Helper;

use DynamicContentForElementor\Controls\DCE_Group_Control_Transform_Element;
use DynamicContentForElementor\Controls\DCE_Group_Control_Filters_CSS;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class DCE_Widget_DynamicPosts_Base extends DCE_Widget_Prototype {

	protected $query = null;
	protected $query_args = null;

	protected $_has_template_content = false;

	public function get_name() {
		return 'dce-dynamicposts-base';
	}

	protected function _register_controls() {

		$this->register_base_controls();

		$this->register_pagination_controls();

		$this->register_infinitescroll_controls();
	}

	protected function register_base_controls() {

		$taxonomies = Helper::get_taxonomies();
		$types = Helper::get_post_types();

		$this->start_controls_section(
			'section_dynamicposts', [
				'label' => '<i class="dynicon icon-dynamic_posts" aria-hidden="true"></i> ' . __( 'Dynamic Posts v2', 'dynamic-content-for-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		// skin: Template
		$this->add_control(
			'skin_dis_customtemplate',
			[
				'type' => Controls_Manager::RAW_HTML,
				'show_label' => false,
				'raw' => '<img src="' . DCE_URL . 'assets/img/skins/template.png' . '" />',
				'content_classes' => 'dce-skin-dis dce-ect-dis',
				'condition' => [
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel' ],
					'style_items' => 'template',
				],
			]
		);
		// skin: Carousel
		$this->add_control(
			'skin_dis_default',
			[
				'type' => Controls_Manager::RAW_HTML,
				'show_label' => false,
				'raw' => '<img src="' . DCE_URL . 'assets/img/skins/default.png' . '" />',
				'content_classes' => 'dce-skin-dis',
				'condition' => [
					'_skin' => 'row',
				],
			]
		);
		// skin: Grid
		$this->add_control(
			'skin_dis_grid',
			[
				'type' => Controls_Manager::RAW_HTML,
				'show_label' => false,
				'raw' => '<img src="' . DCE_URL . 'assets/img/skins/grid.png' . '" />',
				'content_classes' => 'dce-skin-dis',
				'condition' => [
					'_skin' => 'grid',
				],
			]
		);
		// skin: Carousel
		$this->add_control(
			'skin_dis_carousel',
			[
				'type' => Controls_Manager::RAW_HTML,
				'show_label' => false,
				'raw' => '<img src="' . DCE_URL . 'assets/img/skins/carousel.png' . '" />',
				'content_classes' => 'dce-skin-dis',
				'condition' => [
					'_skin' => 'carousel',
				],
			]
		);
		// skin: Filters
		$this->add_control(
			'skin_dis_filters',
			[
				'type' => Controls_Manager::RAW_HTML,
				'show_label' => false,
				'raw' => '<img src="' . DCE_URL . 'assets/img/skins/filters.png' . '" />',
				'content_classes' => 'dce-skin-dis',
				'condition' => [
					'_skin' => 'filters',
				],
			]
		);
		// skin: Dual Carousel
		$this->add_control(
			'skin_dis_dualcarousel',
			[
				'type' => Controls_Manager::RAW_HTML,
				'show_label' => false,
				'raw' => '<img src="' . DCE_URL . 'assets/img/skins/dualcarousel.png' . '" />',
				'content_classes' => 'dce-skin-dis',
				'condition' => [
					'_skin' => 'dualcarousel',
				],
			]
		);
		// skin: Timeline
		$this->add_control(
			'skin_dis_timeline',
			[
				'type' => Controls_Manager::RAW_HTML,
				'show_label' => false,
				'raw' => '<img src="' . DCE_URL . 'assets/img/skins/timeline.png' . '" />',
				'content_classes' => 'dce-skin-dis',
				'condition' => [
					'_skin' => 'timeline',
				],
			]
		);
		// skin: smoothscroll
		$this->add_control(
			'skin_dis_smoothscroll',
			[
				'type' => Controls_Manager::RAW_HTML,
				'show_label' => false,
				'raw' => '<img src="' . DCE_URL . 'assets/img/skins/smoothscroll.png' . '" />',
				'content_classes' => 'dce-skin-dis',
				'condition' => [
					'_skin' => 'smoothscroll',
				],
			]
		);
		// skin: gridtofullscreen3d
		$this->add_control(
			'skin_dis_gridtofullscreen3d',
			[
				'type' => Controls_Manager::RAW_HTML,
				'show_label' => false,
				'raw' => '<img src="' . DCE_URL . 'assets/img/skins/gridtofullscreen3d.png' . '" />',
				'content_classes' => 'dce-skin-dis',
				'condition' => [
					'_skin' => 'gridtofullscreen3d',
				],
			]
		);
		// skin: crossroadsslideshow
		$this->add_control(
			'skin_dis_crossroadsslideshow',
			[
				'type' => Controls_Manager::RAW_HTML,
				'show_label' => false,
				'raw' => '<img src="' . DCE_URL . 'assets/img/skins/crossroadsslideshow.png' . '" />',
				'content_classes' => 'dce-skin-dis',
				'condition' => [
					'_skin' => 'crossroadsslideshow',
				],
			]
		);
		// skin: nextpost
		$this->add_control(
			'skin_dis_nextpost',
			[
				'type' => Controls_Manager::RAW_HTML,
				'show_label' => false,
				'raw' => '<img src="' . DCE_URL . 'assets/img/skins/nextpost.png' . '" />',
				'content_classes' => 'dce-skin-dis',
				'condition' => [
					'_skin' => 'nextpost',
				],
			]
		);

		// skin: 3d
		$this->add_control(
			'skin_dis_3d',
			[
				'type' => Controls_Manager::RAW_HTML,
				'show_label' => false,
				'raw' => '<img src="' . DCE_URL . 'assets/img/skins/3d.png' . '" />',
				'content_classes' => 'dce-skin-dis',
				'condition' => [
					'_skin' => '3d',
				],
			]
		);

		// skin: pagination classic
		$this->add_control(
			'skin_dis_pagination',
			[
				'type' => Controls_Manager::RAW_HTML,
				'show_label' => false,
				'raw' => '<img src="' . DCE_URL . 'assets/img/skins/pagination.png' . '" />',
				'content_classes' => 'dce-skin-dis dce-pagination-dis',
				'condition' => [
					'_skin' => [
						'',
						'grid',
						'filters',
						// 'timeline',
					],
					'pagination_enable' => 'yes',
					'infiniteScroll_enable' => '',
				],
			]
		);
		// skin: infinitescroll
		$this->add_control(
				'skin_dis_infinitescroll',
				[
					'type' => Controls_Manager::RAW_HTML,
					'show_label' => false,
					'raw' => '<img src="' . DCE_URL . 'assets/img/skins/infinitescroll.png' . '" />',
					'content_classes' => 'dce-skin-dis dce-pagination-dis',
					'condition' => [
						'_skin' => [ '', 'grid', 'filters', 'timeline' ],
						'pagination_enable' => 'yes',
						'infiniteScroll_enable' => 'yes',
					],
				]
		);
		// +********************* Pagination
		$this->add_control(
			'pagination_enable', [
				'label' => '<i class="dynicon eicon-post-navigation" aria-hidden="true"></i> ' . __( 'Pagination', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => [
					'_skin' => [ '', 'grid', 'filters', 'gridtofullscreen3d' ],
				],
			]
		);
		$this->add_control(
			'infiniteScroll_enable', [
				'label' => '<i class="dynicon eicon-navigation-horizontal" aria-hidden="true"></i> ' . __( 'Infinite Scroll', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'frontend_available' => true,
				'condition' => [
					'_skin' => [ '', 'grid', 'filters', 'gridtofullscreen3d' ],
					'pagination_enable!' => '',
				],
			]
		);

		// ------------------------------------
		$this->add_control(
		  'style_items', [
			  'label' => '<i class="dynicon eicon-info-box" aria-hidden="true"></i> ' . __( 'Items Style', 'dynamic-content-for-elementor' ),
			  'type' => 'images_selector',
			  'type_selector' => 'image',
			  'columns_grid' => 4,
			  'separator' => 'before',
			  'options' => [
				  'default' => [
					  'title' => __( 'Default', 'dynamic-content-for-elementor' ),
					  'return_val' => 'val',
					  'image' => DCE_URL . 'assets/img/layout/top.png',
				  ],
				  'left' => [
					  'title' => __( 'Left', 'dynamic-content-for-elementor' ),
					  'return_val' => 'val',
					  'image' => DCE_URL . 'assets/img/layout/left.png',
				  ],
				  'right' => [
					  'title' => __( 'Right', 'dynamic-content-for-elementor' ),
					  'return_val' => 'val',
					  'image' => DCE_URL . 'assets/img/layout/right.png',
				  ],
				  'alternate' => [
					  'title' => __( 'Alternate', 'dynamic-content-for-elementor' ),
					  'return_val' => 'val',
					  'image' => DCE_URL . 'assets/img/layout/alternate.png',
				  ],
				  'textzone' => [
					  'title' => __( 'Text Zone', 'dynamic-content-for-elementor' ),
					  'return_val' => 'val',
					  'image' => DCE_URL . 'assets/img/layout/textzone.png',
				  ],
				  'overlay' => [
					  'title' => __( 'Overlay', 'dynamic-content-for-elementor' ),
					  'return_val' => 'val',
					  'image' => DCE_URL . 'assets/img/layout/overlay.png',
				  ],
				  'float' => [
					  'title' => __( 'Float', 'dynamic-content-for-elementor' ),
					  'return_val' => 'val',
					  'image' => DCE_URL . 'assets/img/layout/float.png',
				  ],
				  'template' => [
					  'title' => __( 'Elementor Template', 'dynamic-content-for-elementor' ),
					  'return_val' => 'val',
					  'image' => DCE_URL . 'assets/img/layout/template.png',
				  ],
			  ],
			  'toggle' => false,
			  'render_type' => 'template',
			  'prefix_class' => 'dce-posts-layout-', // ....da cambiare ......
			  'default' => 'default',
			  'condition' => [
				  '_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
			  ],
		  ]
		);
		// +********************* Style: Left, Right, Alternate
		$this->add_responsive_control(
			'image_rate', [
				'label' => __( 'Distribution (%)', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '',
					'unit' => '%',
				],
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .dce-image-area' => 'width: {{SIZE}}%;',
					'{{WRAPPER}} .dce-content-area' => 'width: calc( 100% - {{SIZE}}% );',
				],
				'condition' => [
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items' => [ 'left', 'right', 'alternate' ],
				],
			]
		);
		// +********************* Float Hover style descripton:
		$this->add_control(
			'float_hoverstyle_description',
			[
				'type' => Controls_Manager::RAW_HTML,
				'show_label' => false,
				'raw' => '<i class="dynicon eicon-image-rollover" aria-hidden="true"></i> ' . __( 'The Float style allows you to create animations between the content and the underlying image, from the Hover effect panel you can set the features.', 'dynamic-content-for-elementor' ),
				'content_classes' => 'dce-document-settings',
				'condition' => [
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items' => [ 'float' ],
				],
			]
		);
		// +********************* Image Zone Style:
		$this->add_control(
			'heading_imagezone', [
				'label' => __( 'IMAGE:', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items!' => [ 'default', 'template' ],
				],
			]
		);

		// +********************* Image Zone: Mask
		$this->add_control(
			'imagemask_popover', [
				'label' => __( 'Mask', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'dynamic-content-for-elementor' ),
				'label_on' => __( 'Custom', 'dynamic-content-for-elementor' ),
				'return_value' => 'yes',
				'condition' => [
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items!' => [ 'default', 'template' ],
				],
			]
		);
		$this->start_popover();
		$this->add_control(
			'mask_heading',
			[
				'label' => __( 'Mask', 'dynamic-content-for-elementor' ),
				'description' => __( 'Shape parameters', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items!' => [ 'default', 'template' ],
					'imagemask_popover' => 'yes',
				],
			]
		);
		$this->add_control(
			'mask_shape_type', [
				'label' => __( 'Type', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'image'   => __( 'PNG Image', 'dynamic-content-for-elementor' ),
					'clippath'     => __( 'Clip Path', 'dynamic-content-for-elementor' ),
				],
				'default' => 'image',
				'render_type' => 'template',
				'condition' => [
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items!' => [ 'default', 'template' ],
					'imagemask_popover' => 'yes',
				],
			]
		);
		$this->add_control(
		  'images_mask', [
			  'label' => __( 'Select PNG mask', 'dynamic-content-for-elementor' ),
			  'type' => 'images_selector',
			  'toggle' => false,
			  'type_selector' => 'image',
			  'columns_grid' => 4,
			  'default' => DCE_URL . 'assets/img/mask/flower.png',
			  'options' => [
				  'mask1' => [
					  'title' => 'Flower',
					  'image' => DCE_URL . 'assets/img/mask/flower.png',
					  'image_preview' => DCE_URL . 'assets/img/mask/low/flower.jpg',
				  ],
				  'mask2' => [
					  'title' => 'Blob',
					  'image' => DCE_URL . 'assets/img/mask/blob.png',
					  'image_preview' => DCE_URL . 'assets/img/mask/low/blob.jpg',
				  ],
				  'mask3' => [
					  'title' => 'Diagonals',
					  'image' => DCE_URL . 'assets/img/mask/diagonal.png',
					  'image_preview' => DCE_URL . 'assets/img/mask/low/diagonal.jpg',
				  ],
				  'mask4' => [
					  'title' => 'Rhombus',
					  'image' => DCE_URL . 'assets/img/mask/rombs.png',
					  'image_preview' => DCE_URL . 'assets/img/mask/low/rombs.jpg',
				  ],
				  'mask5' => [
					  'title' => 'Waves',
					  'image' => DCE_URL . 'assets/img/mask/waves.png',
					  'image_preview' => DCE_URL . 'assets/img/mask/low/waves.jpg',
				  ],
				  'mask6' => [
					  'title' => 'Drawing',
					  'image' => DCE_URL . 'assets/img/mask/draw.png',
					  'image_preview' => DCE_URL . 'assets/img/mask/low/draw.jpg',
				  ],
				  'mask7' => [
					  'title' => 'Sketch',
					  'image' => DCE_URL . 'assets/img/mask/sketch.png',
					  'image_preview' => DCE_URL . 'assets/img/mask/low/sketch.jpg',
				  ],

				  'custom_mask' => [
					  'title' => 'Custom mask',
					  //'icon' => 'fa fa-list-ul',
					  'return_val' => 'val',
					  'image' => DCE_URL . 'assets/displacement/custom.jpg',
					  'image_preview' => DCE_URL . 'assets/displacement/custom.jpg',
				  ],
			  ],
			  'condition' => [
				  '_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
				  'style_items!' => [ 'default', 'template' ],
				  'imagemask_popover' => 'yes',

				  'mask_shape_type' => 'image',
			  ],
			  'selectors' => [
				  '{{WRAPPER}} .dce-posts-container .dce-post-image' => '-webkit-mask-image: url({{VALUE}}); mask-image: url({{VALUE}}); -webkit-mask-position: 50% 50%; mask-position: 50% 50%; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; -webkit-mask-size: contain; mask-size: contain;',
			  ],
		  ]
		);
		$this->add_control(
		  'custom_image_mask',
		  [
			  'label' => __( 'Select PNG:', 'dynamic-content-for-elementor' ),
			  'type' => Controls_Manager::MEDIA,
			  'dynamic' => [
				  'active' => true,
			  ],
			  'default' => [
				  'url' => \Elementor\Utils::get_placeholder_image_src(),
			  ],
			  'selectors' => [
				  '{{WRAPPER}} .dce-posts-container .dce-post-image' => '-webkit-mask-image: url({{URL}}); mask-image: url({{URL}}); -webkit-mask-position: 50% 50%; mask-position: 50% 50%; -webkit-mask-repeat: no-repeat; mask-repeat: no-repeat; -webkit-mask-size: contain; mask-size: contain;',
			  ],
			  'condition' => [
				  '_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
				  'style_items!' => [ 'default', 'template' ],
				  'imagemask_popover' => 'yes',

				  'images_mask' => 'custom_mask',
				  'mask_shape_type' => 'image',
			  ],
		  ]
		);
		/*$this->add_control(
			'svg_masking',
			[
				'label' => __( 'Icon', 'elementor-pro' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-circle',
					'library' => 'fa-solid',
				],
				'recommended' => [
					'fa-solid' => [
						'circle',
						'dot-circle',
						'square-full',
					],
					'fa-regular' => [
						'circle',
						'dot-circle',
						'square-full',
					],
				],
				'condition' => [
					'_skin' => ['', 'grid', 'carousel', 'filters', 'dualcarousel'],
					'style_items!' => ['default','template'],
					'imagemask_popover' => 'yes',

					'mask_shape_type' => 'svg'
				],
				//'skin' => 'inline',
				//'label_block' => false,
				//'exclude_inline_options' => [ 'icon' ],
			]
		);*/
		$this->add_control(
		  'clippath_mask', [
			  'label' => __( 'Predefined Clip-Path', 'dynamic-content-for-elementor' ),
			  'type' => 'images_selector',
			  'toggle' => false,
			  'type_selector' => 'image',
			  'columns_grid' => 5,
			  'options' => [
				  'polygon(50% 0%, 0% 100%, 100% 100%)' => [
					  'title' => 'Triangle',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/triangle.png',
				  ],
				  'polygon(20% 0%, 80% 0%, 100% 100%, 0% 100%)' => [
					  'title' => 'Trapezoid',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/trapezoid.png',
				  ],
				  'polygon(25% 0%, 100% 0%, 75% 100%, 0% 100%)' => [
					  'title' => 'Parallelogram',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/parallelogram.png',
				  ],
				  'polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%)' => [
					  'title' => 'Rombus',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/rombus.png',
				  ],
				  'polygon(50% 0%, 100% 38%, 82% 100%, 18% 100%, 0% 38%)' => [
					  'title' => 'Pentagon',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/pentagon.png',
				  ],
				  'polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%)' => [
					  'title' => 'Hexagon',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/hexagon.png',
				  ],
				  'polygon(50% 0%, 90% 20%, 100% 60%, 75% 100%, 25% 100%, 0% 60%, 10% 20%)' => [
					  'title' => 'Heptagon',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/heptagon.png',
				  ],
				  'polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%)' => [
					  'title' => 'Octagon',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/octagon.png',
				  ],
				  'polygon(50% 0%, 83% 12%, 100% 43%, 94% 78%, 68% 100%, 32% 100%, 6% 78%, 0% 43%, 17% 12%)' => [
					  'title' => 'Nonagon',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/nonagon.png',
				  ],
				  'polygon(50% 0%, 80% 10%, 100% 35%, 100% 70%, 80% 90%, 50% 100%, 20% 90%, 0% 70%, 0% 35%, 20% 10%)' => [
					  'title' => 'Decagon',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/decagon.png',
				  ],
				  'polygon(20% 0%, 80% 0%, 100% 20%, 100% 80%, 80% 100%, 20% 100%, 0% 80%, 0% 20%)' => [
					  'title' => 'Bevel',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/bevel.png',
				  ],
				  'polygon(0% 15%, 15% 15%, 15% 0%, 85% 0%, 85% 15%, 100% 15%, 100% 85%, 85% 85%, 85% 100%, 15% 100%, 15% 85%, 0% 85%)' => [
					  'title' => 'Rabbet',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/rabbet.png',
				  ],
				  'polygon(40% 0%, 40% 20%, 100% 20%, 100% 80%, 40% 80%, 40% 100%, 0% 50%)' => [
					  'title' => 'Left arrow',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/leftarrow.png',
				  ],
				  'polygon(0% 20%, 60% 20%, 60% 0%, 100% 50%, 60% 100%, 60% 80%, 0% 80%)' => [
					  'title' => 'Right arrow',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/rightarrow.png',
				  ],
				  'polygon(25% 0%, 100% 1%, 100% 100%, 25% 100%, 0% 50%)' => [
					  'title' => 'Left point',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/leftpoint.png',
				  ],
				  'polygon(0% 0%, 75% 0%, 100% 50%, 75% 100%, 0% 100%)' => [
					  'title' => 'Right point',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/rightpoint.png',
				  ],
				  'polygon(100% 0%, 75% 50%, 100% 100%, 25% 100%, 0% 50%, 25% 0%)' => [
					  'title' => 'Left chevron',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/leftchevron.png',
				  ],
				  'polygon(75% 0%, 100% 50%, 75% 100%, 0% 100%, 25% 50%, 0% 0%)' => [
					  'title' => 'Right Chevron',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/rightchevron.png',
				  ],
				  'polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%)' => [
					  'title' => 'Star',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/star.png',
				  ],
				  'polygon(10% 25%, 35% 25%, 35% 0%, 65% 0%, 65% 25%, 90% 25%, 90% 50%, 65% 50%, 65% 100%, 35% 100%, 35% 50%, 10% 50%)' => [
					  'title' => 'Cross',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/cross.png',
				  ],
				  'polygon(0% 0%, 100% 0%, 100% 75%, 75% 75%, 75% 100%, 50% 75%, 0% 75%)' => [
					  'title' => 'Message',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/message.png',
				  ],
				  'polygon(20% 0%, 0% 20%, 30% 50%, 0% 80%, 20% 100%, 50% 70%, 80% 100%, 100% 80%, 70% 50%, 100% 20%, 80% 0%, 50% 30%)' => [
					  'title' => 'Close',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/close.png',
				  ],
				  'polygon(0% 0%, 0% 100%, 25% 100%, 25% 25%, 75% 25%, 75% 75%, 25% 75%, 25% 100%, 100% 100%, 100% 0%)' => [
					  'title' => 'Frame',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/frame.png',
				  ],
				  'circle(50% at 50% 50%)' => [
					  'title' => 'Circle',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/circle.png',
				  ],
				  'ellipse(25% 40% at 50% 50%)' => [
					  'title' => 'Ellipse',
					  'return_val' => 'val',
					  'image_preview' => DCE_URL . 'assets/img/shapes/ellipse.png',
				  ],
			  ],
			  'condition' => [
				  '_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
				  'style_items!' => [ 'default', 'template' ],
				  'imagemask_popover' => 'yes',

				  'mask_shape_type' => 'clippath',
			  ],
			  'selectors' => [
				  '{{WRAPPER}} .dce-posts-container .dce-post-image' => '-webkit-clip-path: {{VALUE}}; clip-path: {{VALUE}};',
			  ],
		  ]
		);
		$this->end_popover();
		// +********************* Image Zone: Transforms
		$this->add_control(
				'imagetransforms_popover',
				[
					'label' => __( 'Transforms', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::POPOVER_TOGGLE,
					'return_value' => 'yes',
					'render_type' => 'ui',
					'condition' => [
						'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
						'style_items!' => [ 'default', 'template' ],
					],
				]
		);
		$this->start_popover();

		$this->add_group_control(
				DCE_Group_Control_Transform_Element::get_type(),
				[
					'name' => 'transform_image',
					'label' => 'Transform image',
					'selector' => '{{WRAPPER}} .dce-post-item .dce-image-area',
					'separator' => 'before',
					'condition' => [
						'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
						'style_items!' => [ 'default', 'template' ],
						'imagetransforms_popover' => 'yes',
					],
				]
		);
		$this->end_popover();
		// +********************* Image Zone: Filters
		$this->add_group_control(
			DCE_Group_Control_Filters_CSS::get_type(), [
				'name' => 'imagezone_filters',
				'label' => 'Filters',
				'render_type' => 'ui',
				'selector' => '{{WRAPPER}} .dce-post-block .dce-post-image img',
				'condition' => [
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items!' => [ 'default', 'template' ],
				],
			]
		);
		// +********************* Content Zone Style:
		$this->add_control(
			'heading_contentzone', [
				'label' => __( 'CONTENT:', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items!' => [ 'default', 'template' ],
				],
			]
		);
		// +********************* Content Zone: Style
		$this->add_control(
			'contentstyle_popover', [
				'label' => __( 'Style', 'dynamic-content-for-elementor' ),
				'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'dynamic-content-for-elementor' ),
				'label_on' => __( 'Custom', 'dynamic-content-for-elementor' ),
				'return_value' => 'yes',
				'render_type' => 'ui',
				'condition' => [
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll' ],
					'style_items!' => [ 'default', 'template' ],
				],
			]
		);
		$this->start_popover();
		$this->add_control(
			'contentzone_bgcolor', [
				'label' => __( 'Background Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .dce-post-item .dce-content-area' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items!' => [ 'default', 'template' ],
					'contentstyle_popover' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(), [
				'name' => 'contentzone_border',
				'selector' => '{{WRAPPER}} .dce-post-item .dce-content-area',
				'condition' => [
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items!' => [ 'default', 'template' ],
					'contentstyle_popover' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'contentzone_padding', [
				'label' => __( 'Padding', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .dce-post-item .dce-content-area' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items!' => [ 'default', 'template' ],
					'contentstyle_popover' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'contentzone_border_radius', [
				'label' => __( 'Border Radius', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				//'default' => '',
				'selectors' => [
					'{{WRAPPER}} .dce-post-item .dce-content-area' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items!' => [ 'default', 'template' ],
					'contentstyle_popover' => 'yes',
				],
			]
		);

		$this->end_popover();

		// +********************* Content Zone Transform: Overlay, TextZone, Float
		$this->add_control(
			'contenttransform_popover', [
				'label' => __( 'Transform', 'dynamic-content-for-elementor' ),
				'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'dynamic-content-for-elementor' ),
				'label_on' => __( 'Custom', 'dynamic-content-for-elementor' ),
				'return_value' => 'yes',
				'render_type' => 'ui',
				'condition' => [
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items' => [ 'overlay', 'textzone', 'float' ],
				],
			]
		);
		$this->start_popover();
		$this->add_responsive_control(
			'contentzone_x', [
				'label' => __( 'X', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'default' => [
					'size' => '',
					'unit' => '%',
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .dce-content-area' => 'margin-left: {{SIZE}}%;',
				],
				'condition' => [
					'contenttransform_popover' => 'yes',
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items' => [ 'overlay', 'textzone', 'float' ],
				],
			]
		);
		$this->add_responsive_control(
			'contentzone_y', [
				'label' => __( 'Y', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '',
					'unit' => '%',
				],
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .dce-content-area' => 'margin-top: {{SIZE}}%;',
				],
				'condition' => [
					'contenttransform_popover' => 'yes',
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items' => [ 'overlay', 'textzone', 'float' ],
				],
			]
		);
		$this->add_responsive_control(
			'contentzone_width', [
				'label' => __( 'Width (%)', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '',
					'unit' => '%',
				],
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .dce-content-area' => 'width: {{SIZE}}%;',
				],
				'condition' => [
					'contenttransform_popover' => 'yes',
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items' => [ 'overlay', 'textzone', 'float' ],
				],
			]
		);
		$this->add_responsive_control(
			'contentzone_height', [
				'label' => __( 'Height (%)', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '',
					'unit' => '%',
				],
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .dce-content-area' => 'height: {{SIZE}}%;',
				],
				'condition' => [
					'contenttransform_popover' => 'yes',
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items' => [ 'float' ],
				],
			]
		);
		$this->end_popover();
		// +********************* Content Zone: BoxShadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(), [
				'name' => 'contentzone_box_shadow',
				'selector' => '{{WRAPPER}} .dce-post-item .dce-content-area',
				'condition' => [
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items!' => [ 'default', 'template' ],
				],
				'popover' => true,
			]
		);

		/* Responsive --------------- */
		$this->add_control(
			'force_layout_default', [
				'label' => '<i class="dynicon eicon-device-mobile" aria-hidden="true"></i> ' . __( 'Force layout default on mobile', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'prefix_class' => 'force-default-mobile-',
				'condition' => [
					'_skin' => [ '', 'grid', 'filters', 'carousel', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items' => [ 'left', 'right', 'alternate' ],
				],
			]
		);
		// +********************* Style: Elementor TEMPLATE
		$this->add_control(
			'template_id',
			[
				'label' => __( 'Template', 'dynamic-content-for-elementor' ),
				'type' => 'ooo_query',
				'placeholder' => __( 'Template Name', 'dynamic-content-for-elementor' ),
				'label_block' => true,
				'query_type' => 'posts',
				'render_type' => 'template',
				'object_type' => 'elementor_library',
				'condition' => [
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items' => 'template',
					'native_templatemode_enable' => '',
				],
			]
		);
		$this->add_control(
			'templatemode_enable_2', [
				'label' => __( 'Template for odd posts', 'dynamic-content-for-elementor' ),
				'description' => __( 'Enable a template to manage the appearance of the odd elements.', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'render_type' => 'template',
				'condition' => [
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items' => 'template',
					'native_templatemode_enable' => '',
				],
			]
		);

		$this->add_control(
			'template_2_id',
			[
				'label' => __( 'Template for odd posts', 'dynamic-content-for-elementor' ),
				'type' => 'ooo_query',
				'placeholder' => __( 'Select Template', 'dynamic-content-for-elementor' ),
				'label_block' => true,
				'show_label' => false,
				'query_type' => 'posts',
				'object_type' => 'elementor_library',
				'render_type' => 'template',
				'condition' => [
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items' => 'template',
					'templatemode_enable_2!' => '',
					'native_templatemode_enable' => '',
				],
			]
		);

		$this->add_control(
			'native_templatemode_enable', [
				'label' => __( 'Template System Block', 'dynamic-content-for-elementor' ),
				'description' => __( 'Use the template associated with the type (Menu: Dynamic Content > Template System) to manage the appearance of the individual elements of the grid ', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'render_type' => 'template',
				'condition' => [
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items' => 'template',
					'templatemode_enable_2' => '',
				],
			]
		);
		$this->add_control(
			'templatemode_linkable', [
				'label' => __( 'Linkable', 'dynamic-content-for-elementor' ),
				'description' => __( 'Use the extended link on the template block.', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'frontend_available' => true,
				'render_type' => 'template',
				'condition' => [
					'_skin' => [ '', 'grid', 'carousel', 'filters', 'dualcarousel', 'smoothscroll', '3d' ],
					'style_items' => 'template',
				],
			]
		);
		$this->end_controls_section();

		// ------------------------------------------------------------------ [SECTION ITEMS]
		$this->start_controls_section(
			'section_items', [
				'label' => '<i class="dynicon eicon-radio" aria-hidden="true"></i> ' . __( 'Items', 'dynamic-content-for-elementor' ),
				'condition' => [
					'_skin!' => [ 'nextpost' ],
					'style_items!' => 'template',
				],

			]
		);

		////////////////////////////////////////////////////////////////////////////////
		// -------- ORDERING & VISIBILITY items
		$repeater = new Repeater();

		$repeater->add_control(
			'item_label', [
				'label' => __( 'Name', 'dynamic-content-for-elementor' ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'default' => __( 'List Name', 'dynamic-content-for-elementor' ),
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'show_item', [
				'label' => __( 'Show', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'toggle' => false,
				'options' => [
					'check' => [
						'title' => __( 'Visible', 'dynamic-content-for-elementor' ),
						'icon' => 'fa fa-check',
					],
					'ban' => [
						'title' => __( 'Hidden', 'dynamic-content-for-elementor' ),
						'icon' => 'fa fa-ban',
					],
				],
				'default' => 'check',
			]
		);

		// TABS ----------
		$repeater->start_controls_tabs( 'items_repeater_tab' );

		$repeater->start_controls_tab('tab_content', [
			'label' => __( 'Content', 'dynamic-content-for-elementor' ),
			'conditions' => [
				'terms' => [
					[
						'name' => 'show_item',
						'value' => 'check',
					],
					[
						'name' => '_id',
						'operator' => '!==',
						'value' => 'item_custommeta',
					],
				],
			],
		]);

			// CONTENT - TAB
			//
			// +********************* Image
			$repeater->add_group_control(
				Group_Control_Image_Size::get_type(), [
					'name' => 'thumbnail_size',
					'label' => __( 'Image Format', 'dynamic-content-for-elementor' ),
					'default' => 'large',
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_image',
							],
						],
					],
				]
			);
			$repeater->add_responsive_control(
				'ratio_image', [
					'label' => __( 'Image Ratio', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'range' => [
						'px' => [
							'min' => 0.1,
							'max' => 2,
							'step' => 0.1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .dce-img' => 'padding-bottom: calc( {{SIZE}} * 100% );',
						'{{WRAPPER}}:after' => 'content: "{{SIZE}}";',
					],
					'conditions' => [
						'terms' => [

							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_image',
							],
							[
								'name' => 'use_bgimage',
								'value' => '',
							],
						],
					],
				]
			);
			$repeater->add_responsive_control(
				'width_image', [
					'label' => __( 'Image Width', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ '%', 'px', 'vw' ],
					'range' => [
						'%' => [
							'min' => 1,
							'max' => 100,
							'step' => 1,
						],
						'vw' => [
							'min' => 1,
							'max' => 100,
							'step' => 1,
						],
						'px' => [
							'min' => 1,
							'max' => 800,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .dce-post-image' => 'width: {{SIZE}}{{UNIT}};',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_image',
							],
							[
								'name' => 'use_bgimage',
								'value' => '',
							],
						],
					],
				]
			);
			$repeater->add_control(
				'use_bgimage', [
					'label' => __( 'Background Image', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'separator' => 'before',
					'render_type' => 'template',
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_image',
							],
						],
					],
					'selectors' => [
						'{{WRAPPER}} .dce-image-area, {{WRAPPER}}.dce-posts-layout-default .dce-post-bgimage' => 'position: relative;',
					],
				]
			);
			$repeater->add_responsive_control(
				'height_bgimage', [
					'label' => __( 'Height', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'size_units' => [ 'px', '%' ],
					'range' => [
						'px' => [
							'min' => 1,
							'max' => 800,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .dce-post-image.dce-post-bgimage' => 'height: {{SIZE}}{{UNIT}};',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_image',
							],
							[
								'name' => 'use_bgimage',
								'operator' => '!=',
								'value' => '',
							],
						],
					],
				]
			);
			$repeater->add_responsive_control(
				'position_bgimage', [
					'label' => __( 'Background Position', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SELECT,
					'label_block' => true,
					'default' => '',
					'responsive' => true,
					'options' => [
						'' => __( 'Default (Center Center)', 'dynamic-content-for-elementor' ),
						'top center' => _x( 'Top Center', 'Background Control', 'dynamic-content-for-elementor' ),
						'bottom center' => _x( 'Bottom Center', 'Background Control', 'dynamic-content-for-elementor' ),
					],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .dce-post-image.dce-post-bgimage .dce-bgimage' => 'background-position: {{VALUE}};',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_image',
							],
							[
								'name' => 'use_bgimage',
								'operator' => '!=',
								'value' => '',
							],
						],
					],
				]
			);

			$repeater->add_control(
				'use_overlay', [
					'label' => __( 'Overlay', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'separator' => 'before',
					'prefix_class' => 'overlayimage-',
					'render_type' => 'template',
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_image',
							],
						],
					],
				]
			);
			$repeater->add_group_control(
				Group_Control_Background::get_type(), [
					'name' => 'overlay_color',
					'label' => __( 'Background', 'dynamic-content-for-elementor' ),
					'types' => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .dce-post-image.dce-post-overlayimage:after',
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_image',
							],
							[
								'name' => 'use_overlay',
								'operator' => '!==',
								'value' => '',
							],
						],
					],
				]
			);
			$repeater->add_responsive_control(
				'overlay_opacity', [
					'label' => __( 'Opacity (%)', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 0.7,
					],
					'range' => [
						'px' => [
							'max' => 1,
							'min' => 0.10,
							'step' => 0.01,
						],
					],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .dce-post-image.dce-post-overlayimage:after' => 'opacity: {{SIZE}};',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_image',
							],
							[
								'name' => 'use_overlay',
								'operator' => '!==',
								'value' => '',
							],
						],
					],
				]
			);
			// +********************* Title
			$repeater->add_control(
				'html_tag', [
					'label' => __( 'HTML Tag', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'h1' => __( 'H1', 'dynamic-content-for-elementor' ),
						'h2' => __( 'H2', 'dynamic-content-for-elementor' ),
						'h3' => __( 'H3', 'dynamic-content-for-elementor' ),
						'h4' => __( 'H4', 'dynamic-content-for-elementor' ),
						'h5' => __( 'H5', 'dynamic-content-for-elementor' ),
						'h6' => __( 'H6', 'dynamic-content-for-elementor' ),
						'p' => __( 'p', 'dynamic-content-for-elementor' ),
						'div' => __( 'div', 'dynamic-content-for-elementor' ),
						'span' => __( 'span', 'dynamic-content-for-elementor' ),
					],
					'default' => 'h3',
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_title',
							],
						],
					],
				]
			);
			// +********************* Date
			$repeater->add_control(
				'date_type', [
					'label' => __( 'Date Type', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'publish' => __( 'Publish Date', 'dynamic-content-for-elementor' ),
						'modified' => __( 'Last Modified Date', 'dynamic-content-for-elementor' ),
					],
					'default' => 'publish',
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_date',
							],
						],
					],
				]
			);
			// added block_enable
			$repeater->add_control(
				'date_format', [
					'label' => __( 'Format date', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::TEXT,
					'default' => 'd/<b>m</b>/y',
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_date',
							],
						],
					],
				]
			);
			// +********************* Terms of Taxonomy [metadata] (Category, Tag, CustomTax)
			$repeater->add_control(
				'separator_chart', [
					'label' => __( 'Separator', 'dynamic-content-for-elementor' ),
					//'description' => __('Separator caracters.','dynamic-content-for-elementor'),
					'type' => Controls_Manager::TEXT,
					'default' => '/',
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_termstaxonomy',
							],
						],
					],
				]
			);
			$repeater->add_control(
				'only_parent_terms', [
					'label' => __( 'Show only', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'both' => [
							'title' => __( 'Both', 'dynamic-content-for-elementor' ),
							'icon' => 'fa fa-sitemap',
						],
						'yes' => [
							'title' => __( 'Parents', 'dynamic-content-for-elementor' ),
							'icon' => 'fa fa-female',
						],
						'children' => [
							'title' => __( 'Children', 'dynamic-content-for-elementor' ),
							'icon' => 'fa fa-child',
						],
					],
					'toggle' => false,
					'default' => 'both',
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_termstaxonomy',
							],
						],
					],
				]
			);
			$repeater->add_control(
				'block_enable', [
					'label' => __( 'Block', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'return_value' => 'block',
					'render_type' => 'template',
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .dce-term-item' => 'display: {{VALUE}}',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'operator' => 'in',
								'value' => [
									'item_termstaxonomy',
									//'item_date',
								],
							],
						],
					],
				]
			);
			$repeater->add_control(
				'icon_enable', [
					'label' => __( 'Icon', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'operator' => 'in',
								'value' => [
									'item_termstaxonomy',
									'item_date',
								],
							],
						],
					],
				]
			);
			$repeater->add_control(
				'taxonomy_filter', [
					'label' => __( 'Filter Taxonomy', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SELECT2,
					'separator' => 'before',
					'label_block' => true,
					'multiple' => true,
					'options' => $taxonomies,
					'placeholder' => __( 'Auto', 'dynamic-content-for-elementor' ),
					'description' => __( 'Use only terms in selected taxonomies. If empty all terms will be used.', 'dynamic-content-for-elementor' ),
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_termstaxonomy',
							],
						],
					],
				]
			);
			// +********************* Content/Excerpt
			$repeater->add_control(
				'content_type', [
					'label' => __( 'Content type', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'toggle' => false,
					'label_block' => false,
					'options' => [
						'0' => [
							'title' => __( 'Excerpt', 'dynamic-content-for-elementor' ),
							'icon' => 'fa fa-ellipsis-h',
						],
						'1' => [
							'title' => __( 'Content', 'dynamic-content-for-elementor' ),
							'icon' => 'fa fa-align-left',
						],
					],
					'default' => '0',
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_content',
							],
						],
					],
				]
			);
			$repeater->add_control(
				'textcontent_limit', [
					'label' => __( 'Content Character Limit', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::NUMBER,
					'default' => '',
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_content',
							],
							[
								'name' => 'content_type',
								'value' => '1',
							],
						],
					],
				]
			);
			// +********************* ReadMore
			$repeater->add_control(
				'readmore_text', [
					'label' => __( 'Text', 'dynamic-content-for-elementor' ),
					//'description' => __('Separator caracters.','dynamic-content-for-elementor'),
					'type' => Controls_Manager::TEXT,
					'default' => __( 'Read More', 'dynamic-content-for-elementor' ),
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_readmore',
							],
						],
					],
				]
			);
			$repeater->add_control(
				'readmore_size',
				[
					'label' => __( 'Size', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'sm',
					'options' => Helper::get_button_sizes(),
					'style_transfer' => true,
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_readmore',
							],
						],
					],
				]
			);
			// +********************* Author user
			$repeater->add_control(
				'author_user_key',
				[
					'label' => __( 'User Key', 'dynamic-content-for-elementor' ),
					'type'      => 'ooo_query',
					'placeholder'   => __( 'Field key or Name', 'dynamic-content-for-elementor' ),
					'label_block'   => true,
					'multiple'      => true,
					'query_type'    => 'fields',
					'object_type'   => 'user',
					'default'           => [ 'avatar', 'display_name' ],
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_author',
							],
						],
					],
				]
			);
			/*$repeater->add_control(
				'author_image', [
					'label' => __('Show avatar image', 'dynamic-content-for-elementor'),
					'type' => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_author',
							]
						]
					]
				]
			);*/
			$repeater->add_control(
				'author_image_size', [
					'label' => __( 'Avatar size', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::NUMBER,
					'default' => '50',
					'render_type' => 'template',
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_author',
							],
							[
								'name' => 'author_user_key',
								'operator' => 'contains',
								'value' => 'avatar',
							],
							[
								'name' => 'author_user_key',
								'operator' => '!=',
								'value' => '',
							],
							[
								'name' => 'author_user_key',
								'operator' => '!=',
								'value' => [],
							],
						],
					],
				]
			);
			/*$repeater->add_control(
				'author_bio', [
					'label' => __('Show biography', 'dynamic-content-for-elementor'),
					'type' => Controls_Manager::SWITCHER,
					'default' => '',
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_author',
							]
						]
					]
				]
			);*/
			// +********************* Post Type
			$repeater->add_control(
				'posttype_label', [
					'label' => __( 'Label of post type', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'plural',
					'options' => [
						'plural' => __( 'Plural', 'dynamic-content-for-elementor' ),
						'singular' => __( 'singular', 'dynamic-content-for-elementor' ),
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'value' => 'item_posttype',
							],
						],
					],
				]
			);
			// +********************* CustoFields (ACF, Pods, Toolset, Metabox)
		$repeater->end_controls_tab();

		$repeater->start_controls_tab('tab_style', [
			'label' => __( 'Style', 'dynamic-content-for-elementor' ),
			'conditions' => [
				'terms' => [
					[
						'name' => 'show_item',
						'value' => 'check',
					],
					[
						'name' => '_id',
						'operator' => '!==',
						'value' => 'item_custommeta',
					],
				],
			],
		]);

			// STYLE - TAB
			// +********************* Image
			// +********************* Title
			// +********************* Date
			// +********************* Terms of Taxonomy (Category, Tag, CustomTax)
			// +********************* Content/Excerpt
			// +********************* ReadMore
			// +********************* Author user
			// +********************* Post Type
			// +********************* CustomFields (ACF, Pods, Toolset, Metabox)

			$repeater->add_responsive_control(
				'item_align',
				[
					'label' => __( 'Alignment', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'left' => [
							'title' => __( 'Left', 'dynamic-content-for-elementor' ),
							'icon' => 'eicon-text-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'dynamic-content-for-elementor' ),
							'icon' => 'eicon-text-align-center',
						],
						'right' => [
							'title' => __( 'Right', 'dynamic-content-for-elementor' ),
							'icon' => 'eicon-text-align-right',
						],
						/*'justify' => [
							'title' => __('Justified', 'dynamic-content-for-elementor'),
							'icon' => 'eicon-text-align-justify',
						],*/
					],
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}}' => 'text-align: {{VALUE}};',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'operator' => '!in',
								'value' => [ 'item_image' ],
							],
						],
					],
				]
			);
			$repeater->add_responsive_control(
				'image_align', [
					'label' => __( 'Image Alignment', 'dynamic-content-for-elementor' ),
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
					],
					'default' => 'top',
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .dce-post-author, {{WRAPPER}} {{CURRENT_ITEM}}.dce-item_image' => 'justify-content: {{VALUE}};',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'operator' => 'in',
								'value' => [ 'item_image', 'item_author' ],
							],
						],
					],
				]
			);
			// -------- TYPOGRAPHY
			$repeater->add_group_control(
			  Group_Control_Typography::get_type(), [
				  'name' => 'typography_item',
				  'label' => __( 'Typography', 'dynamic-content-for-elementor' ),
				  'render_type' => 'ui',
				  'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} > *',
				  'conditions' => [
					  'terms' => [
						  [
							  'name' => 'show_item',
							  'value' => 'check',
						  ],
						  [
							  'name' => '_id',
							  'operator' => '!in',
							  'value' => [ 'item_image', 'item_custommeta', 'item_readmore' ],
						  ],
					  ],
				  ],
			  ]
			);
			// Read More
			$repeater->add_group_control(
			  Group_Control_Typography::get_type(), [
				  'name' => 'typography_item_readmore',
				  'label' => __( 'Typography', 'dynamic-content-for-elementor' ),
				  'render_type' => 'ui',
				  'selector' => '{{WRAPPER}} .dce-item.dce-item_readmore .dce-post-button > *',
				  'conditions' => [
					  'terms' => [
						  [
							  'name' => 'show_item',
							  'value' => 'check',
						  ],
						  [
							  'name' => '_id',
							  'operator' => '==',
							  'value' => [ 'item_readmore' ],
						  ],
					  ],
				  ],
			  ]
			);

			// -------- COLORS
			$repeater->add_control(
				'color_item', [
					'label' => __( 'Text Color', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} > *' => 'color: {{VALUE}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} a' => 'color: {{VALUE}};',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'operator' => '!in',
								'value' => [ 'item_image', 'item_custommeta' ],
							],
						],
					],
				]
			);
			$repeater->add_control(
				'color_item_separator', [
					'label' => __( 'Separator Color', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .dce-term-item .dce-separator' => 'color: {{VALUE}};',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'operator' => 'in',
								'value' => [ 'item_termstaxonomy' ],
							],
							[
								'name' => 'block_enable',
								'value' => '',
							],
						],
					],
				]
			);
			$repeater->add_control(
				'color_item_icon', [
					'label' => __( 'Icon Color', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .dce-post-icon' => 'color: {{VALUE}};',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'operator' => 'in',
								'value' => [ 'item_termstaxonomy', 'item_date' ],
							],
							[
								'name' => 'icon_enable',
								'value' => 'yes',
							],
						],
					],
				]
			);
			$repeater->add_control(
				'bgcolor_item', [
					'label' => __( 'Background Color', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}}:not(.dce-item_readmore) > *' => 'background-color: {{VALUE}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} a.dce-button' => 'background-color: {{VALUE}};',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'operator' => '!in',
								'value' => [ 'item_image', 'item_author', 'item_custommeta' ],
							],
						],
					],
				]
			);
			$repeater->add_control(
				'hover_color_item', [
					'label' => __( 'Hover Color', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} > * a:hover' => 'color: {{VALUE}};',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'operator' => '!in',
								'value' => [ 'item_date', 'item_image', 'item_author', 'item_custommeta' ],
							],
							[
								'name' => 'use_link',
								'operator' => '==',
								'value' => 'yes',
							],
						],
					],

				]
			);
			$repeater->add_control(
				'hover_bgcolor_item', [
					'label' => __( 'Hover Background Color', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} a.dce-button:hover' => 'background-color: {{VALUE}};',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'operator' => '!in',
								'value' => [ 'item_date', 'item_image', 'item_author', 'item_custommeta' ],
							],
							[
								'name' => 'use_link',
								'operator' => '==',
								'value' => 'yes',
							],
						],
					],
				]
			);

			// ------------ SPACES
			$repeater->add_responsive_control(
				'item_padding', [
					'label' => __( 'Padding', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'rem' ],
					'separator' => 'before',
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}}:not(.dce-item_readmore) > *, {{WRAPPER}} {{CURRENT_ITEM}} a.dce-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
						],
					],
				]
			);
			$repeater->add_responsive_control(
				'item_margin', [
					'label' => __( 'Margin', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'rem' ],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
						],
					],
				]
			);
			/*$repeater->add_responsive_control(
				'item_space', [
					'label' => __('Space', 'dynamic-content-for-elementor'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px', '%'],
					'separator' => 'before',
					'range' => [
						'px' => [
							'max' => 100,
							'min' => -100,
							'step' => 1,
						],
						'%' => [
							'max' => 100,
							'min' => -100,
							'step' => 1,
						],
					],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}}' => 'margin-bottom: {{SIZE}}{{UNIT}};'
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							]
						]
					]
				]
			);*/
			$repeater->add_responsive_control(
				'item_border_radius', [
					'label' => __( 'Border Radius', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'operator' => '!in',
								'value' => [ 'item_image', 'item_readmore', 'item_custommeta' ],
							],
						],
					],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} > *' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$repeater->add_group_control(
				Group_Control_Border::get_type(), [
					'name' => 'item_border',
					'label' => __( 'Border', 'dynamic-content-for-elementor' ),
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'operator' => '!in',
								'value' => [ 'item_image', 'item_readmore', 'item_author', 'item_custommeta' ],
							],
						],
					],
					'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} > *',
				]
			);
			$repeater->add_group_control(
				Group_Control_Box_Shadow::get_type(), [
					'name' => 'box_shadow',
					'label' => __( 'Box Shadow', 'dynamic-content-for-elementor' ),
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'operator' => '!in',
								'value' => [ 'item_image', 'item_readmore', 'item_author', 'item_custommeta' ],
							],
						],
					],
					'selector' => '{{WsRAPPER}} {{CURRENT_ITEM}} > *',
				]
			);
			$repeater->add_responsive_control(
				'item_in_border_radius', [
					'label' => __( 'Border Radius', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%', 'em' ],
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'operator' => 'in',
								'value' => [ 'item_image', 'item_readmore' ],
							],
						],
					],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .dce-button, {{WRAPPER}} {{CURRENT_ITEM}} .dce-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$repeater->add_group_control(
				Group_Control_Border::get_type(), [
					'name' => 'item_in_border',
					'label' => __( 'Border', 'dynamic-content-for-elementor' ),
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'operator' => 'in',
								'value' => [ 'item_image', 'item_readmore', 'item_author' ],
							],
						],
					],
					'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .dce-button, {{WRAPPER}} {{CURRENT_ITEM}} .dce-img',
				]
			);
			$repeater->add_group_control(
				Group_Control_Box_Shadow::get_type(), [
					'name' => 'box_in_shadow',
					'label' => __( 'Box Shadow', 'dynamic-content-for-elementor' ),
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'operator' => 'in',
								'value' => [ 'item_image', 'item_readmore', 'item_author' ],
							],
						],
					],
					'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .dce-button, {{WRAPPER}} {{CURRENT_ITEM}} .dce-img',
				]
			);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab('tab_advamced', [
			'label' => __( 'Advanced', 'dynamic-content-for-elementor' ),
			'conditions' => [
				'terms' => [
					[
						'name' => 'show_item',
						'value' => 'check',
					],
					[
						'name' => '_id',
						'operator' => '!in',
						'value' => [ 'item_custommeta', 'item_author', 'item_custommeta' ],
					],
				],
			],
		]);

			// ADVANCED - TAB
			$repeater->add_control(
				'use_link', [
					'label' => __( 'Use link', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'operator' => '!in',
								'value' => [ 'item_custommeta', 'item_author', 'item_date', 'item_readmore', 'item_content', 'item_posttype' ],
							],
						],
					],
				]
			);
			$repeater->add_control(
				'open_target_blank', [
					'label' => __( 'Open link in a new window', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => 'use_link',
								'value' => 'yes',
							],
							[
								'name' => '_id',
								'operator' => '!in',
								'value' => [ 'item_custommeta', 'item_author', 'item_date', 'item_content', 'item_posttype' ],
							],
						],
					],
				]
			);
			$repeater->add_control(
				'display_inline', [
					'label' => __( 'Display', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'label_on' => 'Inline',
					'label_off' => 'Block',
					'return_value' => 'inline-block',
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} > *' => 'display: {{VALUE}};',
					],
					'conditions' => [
						'terms' => [
							[
								'name' => 'show_item',
								'value' => 'check',
							],
							[
								'name' => '_id',
								'operator' => 'in',
								'value' => [ 'item_title', 'item_posttype', 'item_date', 'item_content', 'item_termstaxonomy' ],
							],
						],
					],
				]
			);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control(
				'list_items',
				[
					'label' => __( 'VISIBILITY, ORDERING & STYLING', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::REPEATER,
					'fields' => $repeater->get_controls(),
					'item_actions' => [
						'add' => false,
						'duplicate' => false,
						'remove' => false,
						'sort' => true,
					],
					'default' => [
						[
							'item_label' => '<i class="fa fa-picture-o" aria-hidden="true"></i> ' . __( 'Image', 'dynamic-content-for-elementor' ),
							'list_html_content' => __( '<p>Image</p>', 'dynamic-content-for-elementor' ),
							'_id' => 'item_image',
						],
						[
							'item_label' => '<i class="fa fa-calendar" aria-hidden="true"></i> ' . __( 'Date', 'dynamic-content-for-elementor' ),
							'list_html_content' => __( '<p>Date</p>', 'dynamic-content-for-elementor' ),
							'_id' => 'item_date',
						],
						[
							'item_label' => '<i class="fa fa-font" aria-hidden="true"></i> ' . __( 'Title', 'dynamic-content-for-elementor' ),
							'list_html_content' => __( '<p>Title</p>', 'dynamic-content-for-elementor' ),
							'_id' => 'item_title',
						],
						[
							'item_label' => '<i class="fa fa-folder-open-o" aria-hidden="true"></i> ' . __( 'Terms of Taxonomy', 'dynamic-content-for-elementor' ),
							'list_html_content' => __( '<p>Meta data</p>', 'dynamic-content-for-elementor' ),
							'_id' => 'item_termstaxonomy',
						],
						[
							'item_label' => '<i class="fa fa-align-left" aria-hidden="true"></i> ' . __( 'Content', 'dynamic-content-for-elementor' ),
							'list_html_content' => __( '<p>>Content</p>', 'dynamic-content-for-elementor' ),
							'_id' => 'item_content',
						],
						[
							'item_label' => '<i class="fa fa-user-circle-o" aria-hidden="true"></i> ' . __( 'Author', 'dynamic-content-for-elementor' ),
							'list_html_content' => __( '<p>Author</p>', 'dynamic-content-for-elementor' ),
							'_id' => 'item_author',
						],
						[
							'item_label' => '<i class="fa fa-check-circle-o" aria-hidden="true"></i> ' . __( 'Custom Meta fields', 'dynamic-content-for-elementor' ),
							'list_html_content' => __( '<p>Custom Meta Items</p>', 'dynamic-content-for-elementor' ),
							'_id' => 'item_custommeta',
						],
						[
							'item_label' => '<i class="fa fa-hand-pointer-o" aria-hidden="true"></i> ' . __( 'Read More', 'dynamic-content-for-elementor' ),
							'list_html_content' => __( '<p>Read More</p>', 'dynamic-content-for-elementor' ),
							'_id' => 'item_readmore',
						],
						[
							'item_label' => '<i class="fa fa-thumb-tack" aria-hidden="true"></i> ' . __( 'Post Type', 'dynamic-content-for-elementor' ),
							'list_html_content' => __( '<p>Post Type</p>', 'dynamic-content-for-elementor' ),
							'_id' => 'item_posttype',
						],
					],
					'title_field' => '<b class="dce-item-name">{{{ item_label }}}</b> <i class="dce-item-icon fa fa-{{{ show_item }}}" aria-hidden="true"></i>',
				]
		);

		$this->end_controls_section();

		// ------------------------------------------------------------------ [SECTION CUSTOM META FIELD]
		$this->start_controls_section(
			'section_custommeta', [
				'label' => '<i class="dynicon eicon-check-circle" aria-hidden="true"></i> ' . __( 'Custom Meta Fields', 'dynamic-content-for-elementor' ),
				'condition' => [
					'_skin!' => [ 'timeline', 'nextpost' ],
					'style_items!' => 'template',
				],
			]
		);
		$repeater_metafield = new Repeater();

		$repeater_metafield->start_controls_tabs( 'metafield_repeater' );
		$repeater_metafield->start_controls_tab( 'tab_content', [ 'label' => __( 'Item', 'dynamic-content-for-elementor' ) ] );

		$repeater_metafield->add_control(
			'metafield_key', [
				'label' => __( 'Meta Field', 'dynamic-content-for-elementor' ),
				'type'      => 'ooo_query',
				'placeholder'   => __( 'Meta key or Name', 'dynamic-content-for-elementor' ),
				'label_block'   => true,
				'query_type'    => 'metas',
				'object_type'   => 'post',
				'default' => '',
			]
		);
		$repeater_metafield->add_control(
			'metafield_type', [
				'label' => __( 'Field type', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'text',
				'options' => [

					'image' => __( 'Image', 'dynamic-content-for-elementor' ),
					'date' => __( 'Date', 'dynamic-content-for-elementor' ),
					'text' => __( 'Text', 'dynamic-content-for-elementor' ),
					'textarea' => __( 'Textarea', 'dynamic-content-for-elementor' ),
					'textfield' => __( 'Textfield', 'dynamic-content-for-elementor' ),
					'button' => __( 'Button (URL)', 'dynamic-content-for-elementor' ),
				],
			]
		);
		$repeater_metafield->add_group_control(
				Group_Control_Image_Size::get_type(), [
					'name' => 'image_size',
					'label' => __( 'Image Format', 'dynamic-content-for-elementor' ),
					'default' => 'large',
					'condition' => [
						'metafield_type' => 'image',
					],
				]
			);
		$repeater_metafield->add_control(
			'metafield_date_format_source', [
				'label' => __( 'Date Format: SOURCE', 'dynamic-content-for-elementor' ),
				'description' => '<a target="_blank" href="https://www.php.net/manual/en/function.date.php">' . __( 'Use standard PHP format character', 'dynamic-content-for-elementor' ) . '</a>' . __( ', you can also use "timestamp"', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'F j, Y, g:i a',
				'placeholder' => __( 'YmdHis, d/m/Y, m-d-y', 'dynamic-content-for-elementor' ),
				'condition' => [
					'metafield_type' => 'date',
				],
			]
		);
		$repeater_metafield->add_control(
			'metafield_date_format_display', [
				'label' => __( 'Date Format: DISPLAY', 'dynamic-content-for-elementor' ),
				'placeholder' => __( 'YmdHis, d/m/Y, m-d-y', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'F j, Y, g:i a',
				'condition' => [
					'metafield_type' => 'date',
				],
			]
		);
		$repeater_metafield->add_control(
			'metafield_button_label', [
				'label' => __( 'Button Label', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Click me',
				'condition' => [
					'metafield_type' => 'button',
				],
			]
		);
		$repeater_metafield->add_control(
				'metafield_button_size',
				[
					'label' => __( 'Button Size', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'sm',
					'options' => Helper::get_button_sizes(),
					'style_transfer' => true,
					'condition' => [
						'metafield_type' => 'button',
					],
				]
			);
		$repeater_metafield->add_control(
			'html_tag_item', [
				'label' => __( 'HTML Tag', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __( 'None', 'dynamic-content-for-elementor' ),
					'h1' => __( 'H1', 'dynamic-content-for-elementor' ),
					'h2' => __( 'H2', 'dynamic-content-for-elementor' ),
					'h3' => __( 'H3', 'dynamic-content-for-elementor' ),
					'h4' => __( 'H4', 'dynamic-content-for-elementor' ),
					'h5' => __( 'H5', 'dynamic-content-for-elementor' ),
					'h6' => __( 'H6', 'dynamic-content-for-elementor' ),
					'p' => __( 'p', 'dynamic-content-for-elementor' ),
					'div' => __( 'div', 'dynamic-content-for-elementor' ),
					'span' => __( 'span', 'dynamic-content-for-elementor' ),
				],
				'condition' => [
					'metafield_type' => 'text',
				],
				'default' => '',
			]
		);
		$repeater_metafield->add_control(
			'link_to', [
				'label' => __( 'Link to', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'separator' => 'before',
				'options' => [
					'' => __( 'None', 'dynamic-content-for-elementor' ),
					'home' => __( 'Home URL', 'dynamic-content-for-elementor' ),
					'post' => __( 'Post URL', 'dynamic-content-for-elementor' ),
					'custom' => __( 'Custom URL', 'dynamic-content-for-elementor' ),
				],
				'condition' => [
					'metafield_type!' => 'button',
				],
			]
		);
		$repeater_metafield->add_control(
			'link', [
				'label' => __( 'Link', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'http://your-link.com', 'dynamic-content-for-elementor' ),
				'condition' => [
					'link_to' => 'custom',
				],
				'default' => [
					'url' => '',
				],
				'show_label' => false,
			]
		);
		/*$repeater_metafield->add_control(
			'taxonomy_metadata', [
				'label' => __('Taxonomy', 'dynamic-content-for-elementor'),
				'type' => Controls_Manager::SELECT,
				//'options' => get_post_taxonomies( $post->ID ),
				'options' => $taxonomies,
				'default' => 'category',
				'condition' => [
					'acf_field_item' => 'taxonomy',
				]
			]
		);*/
		$repeater_metafield->end_controls_tab();

		/*
		$repeater_metafield->start_controls_tab( 'tab_media', [ 'label' => __( 'Media', 'dynamic-content-for-elementor' ) ] );

		$repeater_metafield->end_controls_tab();
		*/

		$repeater_metafield->start_controls_tab( 'tab_style', [ 'label' => __( 'Style', 'dynamic-content-for-elementor' ) ] );

		$repeater_metafield->add_group_control(
			Group_Control_Typography::get_type(), [
				'name' => 'typography_item',
				'selector' => '{{WRAPPER}} .dce-posts-container {{CURRENT_ITEM}}, {{WRAPPER}} .dce-posts-container {{CURRENT_ITEM}} > *',
				'render_type' => 'ui',
				'condition' => [
					'metafield_type!' => 'image',
				],
			]
		);
		$repeater_metafield->add_control(
			'color_item', [
				'label' => __( 'Text Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dce-posts-container {{CURRENT_ITEM}}, {{WRAPPER}} .dce-posts-container {{CURRENT_ITEM}} a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'metafield_type!' => 'image',
				],
			]
		);
		$repeater_metafield->add_control(
			'bgcolor_item', [
				'label' => __( 'Background Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dce-posts-container {{CURRENT_ITEM}}, {{WRAPPER}} .dce-posts-container {{CURRENT_ITEM}} a' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'metafield_type' => 'button',
				],
			]
		);
		$repeater_metafield->add_control(
			'hover_color_item', [
				'label' => __( 'Hover Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} a:hover' => 'color: {{VALUE}};',
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'metafield_type',
							'operator' => '!=',
							'value' => 'image',
						],
						[
							'name' => 'link_to',
							'operator' => '!=',
							'value' => '',
						],
					],
				],

			]
		);
		$repeater_metafield->add_control(
			'hover_bgcolor_item', [
				'label' => __( 'Hover Background Color', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} a:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'metafield_type' => 'button',
				],
			]
		);
		/*$repeater_metafield->add_control(
			'block_enable', [
				'label' => __('Block', 'dynamic-content-for-elementor'),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before'
			]
		);*/
		$repeater_metafield->add_control(
			'padding_item', [
				'label' => __( 'Padding', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],

			]
		);
		$repeater_metafield->add_control(
			'heading_item_button', [
				'label' => __( 'Button', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'metafield_type' => 'button',
				],
			]
		);
		$repeater_metafield->add_control(
			'heading_item_image', [
				'label' => __( 'Image', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'metafield_type' => 'image',
				],
			]
		);
		$repeater_metafield->add_responsive_control(
			'border_radius_item', [
				'label' => __( 'Border Radius', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .dce-button, {{WRAPPER}} {{CURRENT_ITEM}} .dce-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'metafield_type' => [ 'button', 'image' ],
				],
			]
		);
		$repeater_metafield->add_group_control(
			Group_Control_Border::get_type(), [
				'name' => 'border_item',
				'label' => __( 'Border', 'dynamic-content-for-elementor' ),
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .dce-button',
				'condition' => [
					'metafield_type' => [ 'button', 'image' ],
				],
			]
		);
		$repeater_metafield->end_controls_tab();
		$repeater_metafield->end_controls_tabs();

		$this->add_control(
			'custommeta_items', [
				'label' => __( 'Custom Meta Items', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater_metafield->get_controls(),
				'title_field' => '{{{ metafield_key }}} [{{{ metafield_type }}}]',
				'prevent_empty' => false,
			]
		);
		$this->add_responsive_control(
				'custommeta_items_align',
				[
					'label' => __( 'Meta Block Alignment', 'dynamic-content-for-elementor' ),
					'type' => Controls_Manager::CHOOSE,
					'separator' => 'before',
					'options' => [
						'left' => [
							'title' => __( 'Left', 'dynamic-content-for-elementor' ),
							'icon' => 'eicon-text-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'dynamic-content-for-elementor' ),
							'icon' => 'eicon-text-align-center',
						],
						'right' => [
							'title' => __( 'Right', 'dynamic-content-for-elementor' ),
							'icon' => 'eicon-text-align-right',
						],
					],
					'default' => '',
					'selectors' => [
						'{{WRAPPER}} .dce-post-item .dce-post-custommeta' => 'text-align: {{VALUE}};',
					],
				]
			);
		$this->end_controls_section();
		// ------------------------------------------------------------ [SECTION Hover Effects]
		$this->start_controls_section(
			'section_hover_effect', [
				'label' => '<i class="dynicon eicon-image-rollover" aria-hidden="true"></i> ' . __( 'Hover effect', 'dynamic-content-for-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'_skin' => [ '', 'grid', 'filters', 'carousel', 'dualcarousel' ],
					'style_items!' => 'template',
				],
			]
		);
		$this->start_controls_tabs( 'items_this_tab' );

		$this->start_controls_tab('tab_hover_block', [
			'label' => __( 'Block', 'dynamic-content-for-elementor' ),

		]);
		$this->add_control(
			'hover_animation', [
				'label' => __( 'Hover Animation', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab('tab_hover_image', [
			'label' => __( 'Image', 'dynamic-content-for-elementor' ),
		]);
		$this->add_responsive_control(
			'hover_image_opacity', [
				'label' => __( 'Opacity', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .dce-post-block:not(.dce-hover-effects) a.dce-post-image:hover, {{WRAPPER}} .dce-post-block.dce-hover-effects:hover a.dce-post-image' => 'opacity: {{SIZE}};',
				],

			]
		);
		$this->add_group_control(
			DCE_Group_Control_Filters_CSS::get_type(), [
				'name' => 'hover_filters_image',
				'label' => 'Filters image',
				'selector' => '{{WRAPPER}} .dce-post-block:not(.dce-hover-effects) a.dce-post-image:hover img, {{WRAPPER}} .dce-post-block.dce-hover-effects:hover a.dce-post-image img',
			]
		);
		$this->add_control(
			'use_overlay_hover', [
				'label' => __( 'Overlay', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'label_block' => false,
				'separator' => 'before',
				'options' => [
					'1' => [
						'title' => __( 'Yes', 'dynamic-content-for-elementor' ),
						'icon' => 'fa fa-check',
					],
					'0' => [
						'title' => __( 'No', 'dynamic-content-for-elementor' ),
						'icon' => 'fa fa-ban',
					],
				],
				'default' => '0',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name' => 'overlay_color_hover',
				'label' => __( 'Background', 'dynamic-content-for-elementor' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} a.dce-post-image.dce-post-overlayhover:before',
				'condition' => [
					'use_overlay_hover' => '1',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab('tab_hover_content', [
			'label' => __( 'Content', 'dynamic-content-for-elementor' ),
			'condition' => [
				'style_items!' => 'default',
			],
		]);
		$this->add_control(
			'hover_content_animation', [
				'label' => __( 'Hover Animation', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
				'condition' => [
					'style_items!' => 'float',
				],
			]
		);
		/* ----------- */
		$this->add_control(
			'hover_text_heading_float', [
				'label' => __( 'Float Style', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'style_items' => 'float',
				],
			]
		);
		$this->add_control(
			'hover_text_effect',
			[
				'label' => __( 'TextZone Effect', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'None', 'dynamic-content-for-elementor' ),
					'fade' => 'Fade',
					'slidebottom' => 'Slide bottom',
					'slidetop' => 'Slide top',
					'slideleft' => 'Slide left',
					'slideright' => 'Slide right',
					'cssanimations' => 'CSS Animations',

				],
				'render_type' => 'template',
				'prefix_class' => 'dce-hovertexteffect-',
				'condition' => [
					'style_items' => 'float',
				],
			]
		);

		$this->add_control(
			'hover_text_effect_timingFunction', [
				'label' => __( 'Effect Timing function', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'groups' => Helper::get_anim_timing_functions(),
				'default' => 'ease-in-out',
				'selectors' => [
					'{{WRAPPER}} .dce-post-item .dce-hover-effect-content' => 'transition-timing-function: {{VALUE}}; -webkit-transition-timing-function: {{VALUE}};',
				],
				'condition' => [
					'hover_text_effect!' => [ '', 'cssanimations' ],
					'style_items' => 'float',
				],
			]
		);
		$this->add_control(
			'heading_hover_text_effect_in', [
				'label' => __( 'Animation IN', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'hover_text_effect' => 'cssanimations',
					'style_items' => 'float',
				],
			]
		);
		$this->add_control(
			'hover_text_effect_animation_in', [
				'label' => __( 'Animation effect', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'groups' => Helper::get_anim_in(),
				'default' => 'fadeIn',
				'frontend_available' => true,
				'render_type' => 'template',
				'condition' => [
					'hover_text_effect' => 'cssanimations',
					'style_items' => 'float',
				],
				'selectors' => [
					'{{WRAPPER}} .dce-post-item .dce-hover-effect-content.dce-open' => 'animation-name: {{VALUE}}; -webkit-animation-name: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'hover_text_effect_timingFunction_in', [
				'label' => __( 'Effect Timing function', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'groups' => Helper::get_anim_timing_functions(),
				'default' => 'ease-in-out',
				'selectors' => [
					'{{WRAPPER}} .dce-post-item:hover .dce-hover-effect-content.dce-open' => 'animation-timing-function: {{VALUE}}; -webkit-animation-timing-function: {{VALUE}};',
				],
				'condition' => [
					'hover_text_effect' => 'cssanimations',
					'style_items' => 'float',
				],
			]
		);
		$this->add_control(
			'hover_text_effect_speed_in', [
				'label' => __( 'Speed (sec.)', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 0.5,
				'min' => 0.1,
				'max' => 2,
				'step' => 0.1,
				'dynamic' => [
					'active' => false,
				],
				'selectors' => [
					'{{WRAPPER}} .dce-post-item:hover .dce-hover-effect-content.dce-open' => 'animation-duration: {{VALUE}}s; -webkit-animation-duration: {{VALUE}}s;',
				],
				'condition' => [
					'hover_text_effect' => 'cssanimations',
					'style_items' => 'float',
				],
			]
		);
		$this->add_control(
			'heading_hover_text_effect_out', [
				'label' => __( 'Animation OUT', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'hover_text_effect' => 'cssanimations',
					'style_items' => 'float',
				],
			]
		);
		$this->add_control(
			'hover_text_effect_animation_out', [
				'label' => __( 'Animation effect', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'groups' => Helper::get_anim_out(),
				'default' => 'fadeOut',
				'frontend_available' => true,
				'render_type' => 'template',
				'condition' => [
					'hover_text_effect' => 'cssanimations',
					'style_items' => 'float',
				],
				'selectors' => [
					'{{WRAPPER}} .dce-post-item .dce-hover-effect-content.dce-close' => 'animation-name: {{VALUE}}; -webkit-animation-name: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_text_effect_timingFunction_out', [
				'label' => __( 'Effect Timing function', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'groups' => Helper::get_anim_timing_functions(),
				'default' => 'ease-in-out',
				'selectors' => [
					'{{WRAPPER}} .dce-post-item .dce-hover-effect-content.dce-close' => 'animation-timing-function: {{VALUE}}; -webkit-animation-timing-function: {{VALUE}};',
				],
				'condition' => [
					'hover_text_effect' => 'cssanimations',
					'style_items' => 'float',
				],
			]
		);
		$this->add_control(
			'hover_text_effect_speed_out', [
				'label' => __( 'Speed (sec.)', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 0.5,
				'min' => 0.1,
				'max' => 2,
				'step' => 0.1,
				'dynamic' => [
					'active' => false,
				],
				'selectors' => [
					'{{WRAPPER}} .dce-post-item .dce-hover-effect-content.dce-close' => 'animation-duration: {{VALUE}}s; -webkit-animation-duration: {{VALUE}}s;',
				],
				'condition' => [
					'hover_text_effect' => 'cssanimations',
					'style_items' => 'float',
				],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_pagination_controls() {
		$this->start_controls_section(
			'section_pagination', [
				'label' => '<i class="dynicon eicon-post-navigation" aria-hidden="true"></i> ' . __( 'Pagination', 'dynamic-content-for-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'pagination_enable' => 'yes',
					'infiniteScroll_enable' => '',
					'_skin' => [
						'',
						'grid',
						'filters',
						'gridtofullscreen3d',
					],
				],
			]
		);
		$this->add_control(
			'pagination_show_numbers', [
				'label' => __( 'Show Numbers', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
		$this->add_control(
			'pagination_range', [
				'label' => __( 'Range of numbers', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 4,
				'condition' => [
					'pagination_show_numbers' => 'yes',
				],
			]
		);
		$this->add_control(
			'pagination_show_prevnext', [
				'label' => __( 'Show Prev/Next', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'pagination_icon_prevnext', [
				'label' => __( 'Icon Prev/Next', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-long-arrow-right',
				'include' => [
					'fa fa-arrow-right',
					'fa fa-angle-right',
					'fa fa-chevron-circle-right',
					'fa fa-caret-square-o-right',
					'fa fa-chevron-right',
					'fa fa-caret-right',
					'fa fa-angle-double-right',
					'fa fa-hand-o-right',
					'fa fa-arrow-circle-right',
					'fa fa-long-arrow-right',
					'fa fa-arrow-circle-o-right',
				],
				'condition' => [
					'pagination_show_prevnext' => 'yes',
				],
			]
		);
		$this->add_control(
			'pagination_prev_label', [
				'label' => __( 'Previous Label', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Previous', 'dynamic-content-for-elementor' ),
				'condition' => [
					'pagination_show_prevnext' => 'yes',
				],
			]
		);
		$this->add_control(
			'pagination_next_label', [
				'label' => __( 'Next Label', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Next', 'dynamic-content-for-elementor' ),
				'condition' => [
					'pagination_show_prevnext' => 'yes',
				],
			]
		);
		$this->add_control(
			'pagination_show_firstlast', [
				'label' => __( 'Show First/Last', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'pagination_icon_firstlast', [
				'label' => __( 'Icon First/Last', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-long-arrow-right',
				'include' => [
					'fa fa-arrow-right',
					'fa fa-angle-right',
					'fa fa-chevron-circle-right',
					'fa fa-caret-square-o-right',
					'fa fa-chevron-right',
					'fa fa-caret-right',
					'fa fa-angle-double-right',
					'fa fa-hand-o-right',
					'fa fa-arrow-circle-right',
					'fa fa-long-arrow-right',
					'fa fa-arrow-circle-o-right',
				],
				'condition' => [
					'pagination_show_firstlast' => 'yes',
				],
			]
		);
		$this->add_control(
			'pagination_first_label', [
				'label' => __( 'Previous Label', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'First', 'dynamic-content-for-elementor' ),
				'condition' => [
					'pagination_show_firstlast' => 'yes',
				],
			]
		);
		$this->add_control(
			'pagination_last_label', [
				'label' => __( 'Next Label', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Last', 'dynamic-content-for-elementor' ),
				'condition' => [
					'pagination_show_firstlast' => 'yes',
				],
			]
		);
		$this->add_control(
			'pagination_show_progression', [
				'label' => __( 'Show Progression', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
	}

	protected function register_infinitescroll_controls() {
		$this->start_controls_section(
			'section_infinitescroll', [
				'label' => '<i class="dynicon eicon-navigation-horizontal" aria-hidden="true"></i> ' . __( 'Infinite Scroll', 'dynamic-content-for-elementor' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'pagination_enable' => 'yes',
					'infiniteScroll_enable' => 'yes',
				],
			]
		);
		$this->add_control(
			'infiniteScroll_trigger', [
				'label' => __( 'Trigger', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'scroll',
				'frontend_available' => true,
				'options' => [
					'scroll' => __( 'On Scroll Page', 'dynamic-content-for-elementor' ),
					'button' => __( 'On Click Button', 'dynamic-content-for-elementor' ),
				],
			]
		);
		$this->add_control(
			'infiniteScroll_label_button', [
				'label' => __( 'Label Button', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'View more', 'dynamic-content-for-elementor' ),
				'condition' => [
					'infiniteScroll_trigger' => 'button',
				],
			]
		);
		$this->add_control(
			'infiniteScroll_enable_status', [
				'label' => __( 'Enable Status', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'infiniteScroll_loading_type', [
				'label' => __( 'Loading Type', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'options' => [
					'ellips' => [
						'title' => __( 'Ellips', 'dynamic-content-for-elementor' ),
						'icon' => 'fa fa-ellipsis-h',
					],
					'text' => [
						'title' => __( 'Label Text', 'dynamic-content-for-elementor' ),
						'icon' => 'fa fa-font',
					],
				],
				'default' => 'ellips',
				'separator' => 'before',
				'condition' => [
					'infiniteScroll_enable_status' => 'yes',
				],
			]
		);
		$this->add_control(
			'infiniteScroll_label_loading', [
				'label' => __( 'Label Loading', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Loading...', 'dynamic-content-for-elementor' ),
				'condition' => [
					'infiniteScroll_enable_status' => 'yes',
					'infiniteScroll_loading_type' => 'text',
				],
			]
		);
		$this->add_control(
			'infiniteScroll_label_last', [
				'label' => __( 'Label Last', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'End of content', 'dynamic-content-for-elementor' ),
				'condition' => [
					'infiniteScroll_enable_status' => 'yes',
				],
			]
		);
		$this->add_control(
			'infiniteScroll_label_error', [
				'label' => __( 'Label Error', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'No more articles to load', 'dynamic-content-for-elementor' ),
				'condition' => [
					'infiniteScroll_enable_status' => 'yes',
				],
			]
		);
		$this->add_control(
			'infiniteScroll_enable_history', [
				'label' => __( 'Enable History', 'dynamic-content-for-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();
	}

	public function render() {}

	public function get_query() {
		return $this->query;
	}
	public function get_query_args() {
		return $this->query_args;
	}

	public function query_posts() {}

	public function get_current_page() {
		if ( '' === $this->get_settings( 'pagination_enable' ) && '' === $this->get_settings( 'infiniteScroll_enable' ) ) {
			return 1;
		}
		return max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
	}

	protected function render_svg_mask( $mask_shape_type ) {
		$widgetId = $this->get_id();
		$shape_numbers = $this->get_settings( 'shape_numbers' );
		$image_masking_url = $this->get_settings( 'image_masking' )['url'];
	}

	protected function limit_content( $limit ) {}

	protected function limit_excerpt( $limit ) {}

	public function get_terms_query( $settings = null, $id_page = null ) {}

}
