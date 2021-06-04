<?php

namespace Elemailer_Lite\Integrations\Elementor\Widgets;

defined('ABSPATH') || exit;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Core\Schemes\Color;
use \Elementor\Utils;

/**
 * heading widget class for registering heading widget
 *
 * @author elEmailer 
 * @since 1.0.0
 */
class Elemailer_Widget_Footer extends Widget_Base
{

	public function get_name()
	{
		return 'elemailer-footer';
	}

	public function get_title()
	{
		return esc_html__('Footer', 'elemailer-lite');
	}

	public function get_icon()
	{
		return 'eicon-footer';
	}

	public function show_in_panel()
	{
		$post_type = get_post_type();
		return (in_array($post_type, ['em-form-template', 'em-emails-template']));
	}

	public function get_categories()
	{
		return array('elemailer-template-builder-fields');
	}

	public function get_keywords()
	{
		return ['void', 'template', 'footer'];
	}

	protected function _register_controls()
	{
		$this->start_controls_section(
			'section_title',
			[
				'label' => __('Text', 'elemailer-lite'),
			]
		);

		$this->add_control(
			'footer_address',
			[
				'label' => __('Address', 'elemailer-lite'),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => __('Enter your addrress', 'elemailer-lite'),
				'default' => __('BDBL Bhaban (19th Floor-East), 12, Kazi Nazrul Islam Avenue, Dhaka 1215', 'elemailer-lite'),
			]
		);

		$this->add_control(
			'text_align',
			[
				'label' => __('Alignment', 'elemailer-lite'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'elemailer-lite'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'elemailer-lite'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'elemailer-lite'),
						'icon' => 'eicon-text-align-right',
					],

				],
				'default' => 'center',
			]
		);

		$this->add_control(
			'ft_color',
			[
				'label' => __('Text Color', 'elemailer-lite'),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Color::get_type(),
					'value' => Color::COLOR_3,
				],
				'default' => '#000',
			]
		);

		$this->add_control(
			'ft_font_size',
			[
				'label' => __('Font Size (px)', 'elemailer-lite'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
				],
				'size_units' => ['px'],
				'range' => [

					'px' => [
						'min' => 0,
						'max' => 100,
					],

				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_subscription',
			[
				'label' => __('Subscription', 'elemailer-lite'),
			]

		);
		$this->add_control(
			'show_unsubscribe',
			[
				'label' => __( 'Show Unsubscribe', 'elemailer-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'elemailer-lite' ),
				'label_off' => __( 'Hide', 'elemailer-lite' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'unsbscribe_text',
			[
				'label' => __('Text', 'elemailer-lite'),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'placeholder' => __('Text Here', 'elemailer-lite'),
				'default' => __('Unsubscribe', 'elemailer-lite'),
				'condition' => ['show_unsubscribe' => 'yes'],
			]
		); 
		$this->add_control(
			'hr_ust',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);
		$this->add_control(
			'show_manage_sbscription',
			[
				'label' => __( 'Show Manage Sbscription', 'elemailer-lite' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'elemailer-lite' ),
				'label_off' => __( 'Hide', 'elemailer-lite' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);
		$this->add_control(
			'manage_sbscription_text',
			[
				'label' => __('Text', 'elemailer-lite'),
				'label_block' => true,
				'type' => Controls_Manager::TEXT,
				'placeholder' => __('Text Here', 'elemailer-lite'),
				'default' => __('Manage Subscription', 'elemailer-lite'),
				'condition' => ['show_manage_sbscription' => 'yes'],
			]
		);
		$this->add_control(
			'hr_mst',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
			]
		);
		$this->add_control(
			'unsubscribe_color',
			[
				'label' => __('Unsubscribe Color', 'elemailer-lite'),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Color::get_type(),
					'value' => Color::COLOR_3,
				],
				'default' => '#000',
			]
		);

		$this->add_control(
			'unsubscribe_font_size',
			[
				'label' => __('Unsubscribe Font Size (px)', 'elemailer-lite'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
				],
				'size_units' => ['px'],
				'range' => [

					'px' => [
						'min' => 0,
						'max' => 100,
					],

				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'advanced_section',
			[
				'label' => esc_html__('Advanced Style', 'elemailer-lite'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'advance_margin',
			[
				'label' => __('Margin (px)', 'elemailer-lite'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'default' => [
					'top' => '10',
					'right' => '10',
					'bottom' => '10',
					'left' => '10',
					'isLinked' => true,
				],
			]
		);

		$this->add_control(
			'advance_padding',
			[
				'label' => __('Padding (px)', 'elemailer-lite'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
			]
		);

		$this->add_control(
			'advance_background_type',
			[
				'label' => __('Background Type', 'elemailer-lite'),
				'type' => Controls_Manager::SELECT,
				'default' => 'color',
				'options' => [
					'color'  => __('Color', 'elemailer-lite'),
					'image' => __('Image', 'elemailer-lite'),
				],
			]
		);

		$this->add_control(
			'advance_background_color',
			[
				'label' => __('Background Color', 'elemailer-lite'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'advance_background_type' => 'color',
				],
			]
		);

		$this->add_control(
			'advance_background_image',
			[
				'label' => __('Choose Image', 'elemailer-lite'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'advance_background_type' => 'image',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render($instance = [])
	{

		$settings = $this->get_settings_for_display();

		$footer_address  = $settings['footer_address'];
		$unsubscribe_text  = $settings['unsbscribe_text'];
		$manage_subscription_text  = $settings['manage_sbscription_text'];

		$show_unsubscribe  = $settings['show_unsubscribe']; 
		$show_manage_sbscription  = $settings['show_manage_sbscription'];

		$alignment = isset($settings['text_align']) ? $settings['text_align'] : 'center';

		$ft_text_styles = 'color: ' . (($settings['ft_color'] != '') ? $settings['ft_color'] : '#000') . ';';
		$ft_text_styles .= 'font-size: ' . ((($settings['ft_font_size']['size'] != '') ? $settings['ft_font_size']['size'] : '12') . (($settings['ft_font_size']['unit'] != '') ? $settings['ft_font_size']['unit'] : 'px')) . ';';

		$ft_unsbscribe = 'color: ' . (($settings['unsubscribe_color'] != '') ? $settings['unsubscribe_color'] : '#000') . ';';
		$ft_unsbscribe .= 'font-size: ' . ((($settings['unsubscribe_font_size']['size'] != '') ? $settings['unsubscribe_font_size']['size'] : '12') . (($settings['unsubscribe_font_size']['unit'] != '') ? $settings['unsubscribe_font_size']['unit'] : 'px')) . ';';

		$advance_style = 'background: ' . (($settings['advance_background_type'] == 'color') ? (($settings['advance_background_color'] != '') ? $settings['advance_background_color'] . ';' : '#0000;') : 'url("' . esc_url($settings['advance_background_image']['url']) . '") no-repeat fixed center;');
		$advance_style .= ' margin: ' . (($settings['advance_margin']['top'] != '') ? $settings['advance_margin']['top'] . 'px ' . $settings['advance_margin']['right'] . 'px ' . $settings['advance_margin']['bottom'] . 'px ' . $settings['advance_margin']['left'] . 'px;' : '0px 0px 0px 0px;');
		$advance_style .= ' padding: ' . (($settings['advance_padding']['top'] != '') ? $settings['advance_padding']['top'] . 'px ' . $settings['advance_padding']['right'] . 'px ' . $settings['advance_padding']['bottom'] . 'px ' . $settings['advance_padding']['left'] . 'px;' : '0px 0px 0px 0px;');

?>

		<div style="text-align: <?php echo esc_attr($alignment); ?>;<?php echo esc_attr($advance_style); ?>" class="ele-footer-widget">
			<div class="ele-footer-text">
				<p style="line-height: initial;<?php echo esc_attr($ft_text_styles); ?>"><?php echo esc_html($footer_address); ?></p>
			</div>
			<div class="ele-unsubscribe">
				<a style="line-height: initial;text-decoration: underline;<?php echo esc_attr($ft_unsbscribe); ?>" href="#[ELEMAILER_UNSUBSCRIBE_LINK]"><?php echo esc_html($unsubscribe_text); ?></a> <?php if($show_unsubscribe == 'yes' && $show_manage_sbscription == 'yes') { echo ' || '; } ?><a style="line-height: initial;text-decoration: underline;<?php echo esc_attr($ft_unsbscribe); ?>" href="#[ELEMAILER_MANAGE_SUBSCRIPTION_LINK]"><?php echo esc_html($manage_subscription_text); ?></a>
			</div>
		</div>

<?php

	}
}
