<?php

namespace Elemailer_Lite;

defined('ABSPATH') || exit;

/**
 * main plugin loaded final class
 * handle everything for initial plugin load
 *
 * @author elEmailer
 * @since 1.0.0
 */
final class Plugin
{

    /**
     * accesing for object of this class
     *
     * @var object
     */
    private static $instance;

    /**
     * construct function of this class
     *
     * @return void
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->define_constant();
        Autoloader::run();
    }

    /**
     * defining constant function
     *
     * @return void
     * @since 1.0.0
     */
    public function define_constant()
    {
        define('ELE_MAILER_LITE_VERSION', '1.0.0');
        define('ELE_MAILER_LITE_PACKAGE', 'free');
        define('ELE_MAILER_LITE_PLUGIN_URL', trailingslashit(plugin_dir_url(__FILE__)));
        define('ELE_MAILER_LITE_PLUGIN_DIR', trailingslashit(plugin_dir_path(__FILE__)));

        define('ELE_MAILER_LITE_PLUGIN_PUBLIC', ELE_MAILER_LITE_PLUGIN_URL . 'public');
        define('ELE_MAILER_LITE_PLUGIN_PUBLIC_DIR', ELE_MAILER_LITE_PLUGIN_DIR . 'public');
    }

    /**
     * plugin initialization function
     * calls on plugins_loaded action
     *
     * @return void
     * @since 1.0.0
     */
    public function init()
    {
        if (current_user_can('manage_options')) {
            add_action('admin_menu', [$this, 'add_admin_menu']);
        }
        // enqueue js and css on admin dashboard hook
        add_action('admin_enqueue_scripts', [$this, 'load_js_css_admin']);
        // remove third party js css in our template hook
        add_action('wp_enqueue_scripts', [$this, 'enqueue_dequeue_js_css_public'], 999);
        // enqueue css in elementor front end hook
        add_action('elementor/frontend/after_enqueue_styles', [$this, 'load_elementor_css_public']);

        // call notice initialize function
        Helpers\Notice::init();

        // limitation notice
        add_action('admin_notices', [$this, 'show_free_version_limitation']);

        // call everything for email template. this init will register cpt and everything related on template
        App\Form_Template\Base::instance()->init();

        // integrate elementor with this plugin
        Integrations\Elementor\Base::instance()->init();

        // minify elementor default css and assign it to a new file for sending it to the mail clients. 
        Helpers\Util::minify_css(ELE_MAILER_LITE_PLUGIN_DIR . 'public/assets/css/elemailer-mail.css', ELE_MAILER_LITE_PLUGIN_DIR . 'app/form-template/view/default-elementor-style.php');
    }

    public function after_wp_loaded_hooks()
    {
        // remove wp classic editor styles from themes
        remove_editor_styles();
    }

    /**
     * trigger this after activate plugin function
     *
     * @return void
     * @since 1.0.0
     */
    public function action_after_active_plugin()
    {
        $this->flush_rewrites();
        $this->plugin_active_info();
    }

    /**
     * store plugin activation information function
     *
     * @since 1.0.0
     */
    public function plugin_active_info()
    {

        $info = [
            'activation_version' => ELE_MAILER_LITE_VERSION,
            'activation_time' => date('d-m-Y H:i:s'),
        ];

        update_option('elemailer_lite_info', $info);
    }

    /**
     * plublic assets load function
     * used this function for removing thirdparty css and js from our template
     *
     * @return void
     * @since 1.0.0
     */
    public function enqueue_dequeue_js_css_public()
    {
        $post_type = get_post_type();

        if (in_array($post_type, ['em-form-template'])) {
            // get all queued scripts
            $wp_scripts = wp_scripts();
            // get all queued styles
            $wp_styles  = wp_styles();
            // get theme root uri
            $themes_uri = get_theme_root_uri();
            // get plugins root uri
            $current_plugin = plugin_dir_url(__FILE__);
            // get elementor plugin uri
            $elementor = WP_PLUGIN_URL . '/elementor/';
            // get elementor pro plugin uri
            $elementor_pro = WP_PLUGIN_URL . '/elementor-pro/';

            foreach ($wp_scripts->registered as $wp_script) {
                // check is this script comes from theme
                if (strpos($wp_script->src, $themes_uri) !== false) {
                    // prevent this script from loading in our template editor page
                    wp_dequeue_script($wp_script->handle);
                }
                // check is this script comes from plugin
                if (strpos($wp_script->src, WP_PLUGIN_URL) !== false) {
                    // check is this script comes from our plugin
                    if (strpos($wp_script->src, $current_plugin) !== false) {
                        // keep this script to load
                        continue;
                    }
                    // check is this script comes from elementor
                    else if (strpos($wp_script->src, $elementor) !== false) {
                        // keep this script to load
                        continue;
                    }
                    // check is this script comes from elementor pro
                    else if (strpos($wp_script->src, $elementor_pro) !== false) {
                        // keep this script to load
                        continue;
                    }
                    // doesn't meet any condition
                    else {
                        // prevent this script from loading in our template editor page
                        wp_dequeue_script($wp_script->handle);
                    }
                }
            }

            foreach ($wp_styles->registered as $wp_style) {
                // check is this style comes from theme
                if (strpos($wp_style->src, $themes_uri) !== false) {
                    // prevent this style from loading in our template editor page
                    wp_dequeue_style($wp_style->handle);
                }
                // check is this style comes from plugin
                if (strpos($wp_style->src, WP_PLUGIN_URL) !== false) {
                    // check is this style comes from our plugin
                    if (strpos($wp_style->src, $current_plugin) !== false) {
                        // keep this style to load
                        continue;
                    }
                    // check is this style comes from elementor
                    else if (strpos($wp_style->src, $elementor) !== false) {
                        // keep this style to load
                        continue;
                    }
                    // check is this style comes from elementor pro
                    else if (strpos($wp_style->src, $elementor_pro) !== false) {
                        // keep this style to load
                        continue;
                    }
                    // doesn't meet any condition
                    else {
                        // prevent this style from loading in our template editor page
                        wp_dequeue_style($wp_style->handle);
                    }
                }
            }
        }
    }

    /**
     * elementor enqueue style function
     *
     * @return void
     * @since 1.0.0
     */
    public function load_elementor_css_public()
    {
        $post_type = get_post_type();

        if (in_array($post_type, ['em-form-template'])) {

            wp_enqueue_style('elemailer-lite', ELE_MAILER_LITE_PLUGIN_PUBLIC . '/assets/css/style.css', false, ELE_MAILER_LITE_VERSION);
        }
    }

    /**
     * admin assets load function
     *
     * @return void
     * @since 1.0.0
     */
    public function load_js_css_admin()
    {

        $screen = get_current_screen();

        if (in_array($screen->id, ['edit-em-form-template'])) {

            wp_enqueue_style('elemailer-ui', ELE_MAILER_LITE_PLUGIN_PUBLIC . '/assets/css/admin-style.css', false, ELE_MAILER_LITE_VERSION);

            wp_enqueue_script('elemailer-form-template', ELE_MAILER_LITE_PLUGIN_PUBLIC . '/assets/js/admin/form-template-functions.js', array(), ELE_MAILER_LITE_VERSION, true);
            wp_localize_script('elemailer-form-template', 'elemailer_lite', ['restUrl' => rest_url('elemailer_lite/v1/'), 'nonce' => wp_create_nonce('wp_rest')]);
        }
    }


    /**
     * plugin menu add function
     *
     * @return void
     * @since 1.0.0
     */
    public function add_admin_menu()
    {
        // for main menu of this plugin in admin panel
        add_menu_page(
            esc_html__('elEmailer'),
            esc_html__('elEmailer'),
            'manage_options',
            'elemailer-menu',
            '',
            esc_url(ELE_MAILER_LITE_PLUGIN_PUBLIC . '/assets/img/elemailer-icon.png'),
            25
        );
    }

    /**
     * update permalink after register cpt function
     *
     * @return void
     * @since 1.0.0
     */
    public function flush_rewrites()
    {
        $template_cpt = new App\Form_Template\Cpt();
        $template_cpt->flush_rewrites();
    }

    /**
     * Show error notice for free version
     *
     * @return void
     * @since 1.0.0
     */
    public function show_free_version_limitation()
    {
        $screen = get_current_screen();
        if ($screen->id == 'edit-em-form-template') {
            \Elemailer_Lite\Helpers\Notice::push(
                [
                    'id'          => 'limitation-elemailer-free-version',
                    'type'        => 'error',
                    'dismissible' => false,
                    'message'     => sprintf(esc_html__('You are able to create only 3 templates with elemailer lite', 'elemailer-lite')),
                ]
            );
        }
    }

    public function action_after_deactivate_plugin()
    {
        // plugin deactivation actions
    }

    /**
     * singleton instance create function
     *
     * @return object
     * @since 1.0.0
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
