<?php

namespace Elemailer_Lite\Integrations\Elementor\Actions;

defined('ABSPATH') || exit;

use \Elementor\Controls_Manager;
use \Elementor\Utils;


/**
 * all hook related class for activating some extra functionality based on elementor
 *
 * @author elEmailer
 * @since 1.0.0
 */
class Hooks
{
    use \Elemailer_Lite\Traits\Singleton;

    /**
     * initial function of this class
     *
     * @since 1.0.0
     */
    public function trigger_elementor_free_actions()
    {
        // add template select control in elementor form widget
        add_action('elementor/element/form/section_email/before_section_end', [$this, 'email_template_selector'], 10, 2);

        // add template select control in elementor form widget
        add_action('elementor/element/form/section_email_2/before_section_end', [$this, 'email2_template_selector'], 10, 2);

        // add custom background control in section control
        add_action('elementor/element/section/section_background/before_section_end', [$this, 'section_background'], 10, 2);
    }

    /**
     * function for activate elementor pro functionality
     *
     * @return void
     * @since 1.0.0
     */
    public function trigger_elementor_pro_actions()
    {
        // elementor form submission catch hook
        add_action('elementor_pro/forms/form_submitted', [$this, 'trigger_elementor_form_submission']);
    }

    /**
     * catch elementor pro form submission function
     *
     * @param object $module
     * @since 1.0.0
     */
    public function trigger_elementor_form_submission($module)
    {
        // override email class of elementor pro
        $module->add_form_action('email', new Void_Email());

        // override email2 class of elementor pro
        $module->add_form_action('email2', new Void_Email2());
    }

    /**
     * option for selecting elemailer lite template function in form widget
     *
     * @param object $element
     * @param array $args
     * @since 1.0.0
     */
    public function email_template_selector($element, $args)
    {
        // add a control
        $element->add_control(
            'show_elemailer_email_template_selector',
            [
                'label' => esc_html__('Use elemailer lite', 'elemailer-lite'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'elemailer-lite'),
                'label_off' => esc_html__('No', 'elemailer-lite'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $element->add_control(
            'select_elemailer_email_template',
            [
                'label' => esc_html__('Select elemailer lite', 'elemailer-lite'),
                'type' => Controls_Manager::SELECT,
                'label_block' => true,
                'options' => \Elemailer_Lite\App\Form_Template\Action::instance()->get_all_template(),
                'condition' => [
                    'show_elemailer_email_template_selector' => 'yes',
                ],
            ]
        );
    }

    /**
     * option for selecting elemailer lite template function in form widget
     *
     * @param object $element
     * @param array $args
     * @since 1.0.0
     */
    public function email2_template_selector($element, $args)
    {
        // add a control
        $element->add_control(
            'show_elemailer_email_template_selector_2',
            [
                'label' => esc_html__('Use elemailer lite template', 'elemailer-lite'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'elemailer-lite'),
                'label_off' => esc_html__('No', 'elemailer-lite'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $element->add_control(
            'select_elemailer_email_template_2',
            [
                'label' => esc_html__('Select elemailer lite template', 'elemailer-lite'),
                'type' => Controls_Manager::SELECT,
                'label_block' => true,
                'options' => \Elemailer_Lite\App\Form_Template\Action::instance()->get_all_template(),
                'condition' => [
                    'show_elemailer_email_template_selector_2' => 'yes',
                ],
            ]
        );
    }
    /**
     * custom control add in section of elementor for adding background
     *
     * @param object $element
     * @param array $args
     * @return void
     * @since 1.0.0
     */
    public function section_background($element, $args)
    {

        $element->add_control(
            'section_background_type',
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

        $element->add_control(
            'section_background_color',
            [
                'label' => __('Background Color', 'elemailer-lite'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'section_background_type' => 'color',
                ],
                'selectors' => [
                    '{{WRAPPER}} > .elementor-container' => 'background: {{VALUE}};',
                ],
            ]
        );

        $element->add_control(
            'section_background_image',
            [
                'label' => __('Choose Image', 'elemailer-lite'),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'section_background_type' => 'image',
                ],
                'selectors' => [
                    '{{WRAPPER}} > .elementor-container' => 'background: url(\'{{URL}}\') no-repeat center;',
                ],
            ]
        );
    }
}
