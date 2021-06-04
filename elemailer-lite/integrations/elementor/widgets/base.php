<?php

namespace Elemailer_Lite\Integrations\Elementor\Widgets;

defined('ABSPATH') || exit;

/**
 * base class for widgets class
 * used to load all widget
 *
 * @author elEmailer 
 * @since 1.0.0
 */
class Base
{
	use \Elemailer_Lite\Traits\Singleton;

	/**
	 * initialization function of this class
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function init()
	{

		// register category hook for elementor editor
		add_action('elementor/elements/categories_registered', [$this, 'add_elementor_widget_categories']);

		// register widgets hook for elementor editor
		add_action('elementor/widgets/widgets_registered', [$this, 'on_widgets_registered']);
	}

	/**
	 * get all widget list of this plugin function
	 *
	 * @since 1.0.0
	 */
	public function get_all_widgets()
	{

		$all_widgets = [
			'free' => [
				'heading',
				'image',
				'image-box',
				'video',
				'button',
				'divider',
				'spacer',
				'text-editor',
				'social',
				'shortcode',
				'latest-posts',
				'selected-posts',
				'footer',
			],
		];

		return apply_filters('elemailer_lite/onload/include_widgets', $all_widgets);
	}

	/**
	 * On Widgets Registered
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function on_widgets_registered()
	{
		$this->includes();
		$this->register_widget();
	}

	/**
	 * Includes
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function includes()
	{

		$all_widgets = $this->get_all_widgets();

		$includes = (is_array($all_widgets['free']) ? $all_widgets['free'] : []);

		foreach ($includes as $widget) {
			require_once ELE_MAILER_LITE_PLUGIN_DIR . 'integrations/elementor/widgets/' . $widget . '/' . $widget . '.php';
		}
	}

	/**
	 * Register Widget
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 */
	private function register_widget()
	{
		//this is where we create objects for each widget the above  ->use voidquery\Widgets\Hello_World; is needed
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Elemailer_Widget_Heading());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Elemailer_Widget_Image());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Elemailer_Widget_Video());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Elemailer_Widget_Button());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Elemailer_Widget_Divider());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Elemailer_Widget_Spacer());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Elemailer_Widget_Text_Editor());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Elemailer_Widget_Image_Box());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Elemailer_Widget_Social());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Elemailer_Widget_Shortcode());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Elemailer_Widget_Latest_Posts());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Elemailer_Widget_Selected_Posts());
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Elemailer_Widget_Footer());
	}

	/**
	 * create category for our widget on elementor editor panel
	 *
	 * @param object $elements_manager
	 * @return void
	 * @since 1.0.0
	 */
	function add_elementor_widget_categories($elements_manager)
	{

		$elements_manager->add_category(
			'elemailer-template-builder-fields',
			[
				'title' => __('Elemailer Template Builder Fields', 'elemailer-lite'),
				'icon' => 'fa fa-plug',
			]
		);
	}
}
