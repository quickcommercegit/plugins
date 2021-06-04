<?php

namespace Elemailer_Lite\App\Form_Template;

defined('ABSPATH') || exit;

/**
 * template related base class for initialization
 * used for creating cpt, api
 * 
 * @author elEmailer
 * @since 1.0.0
 */
class Base
{

    use \Elemailer_Lite\Traits\Singleton;

    public $template;
    public $api;

    /**
     * initialization function for all of the form template related functionality
     *
     * @return void
     * @since 1.0.0
     */
    public function init()
    {
        $this->template = new Cpt();
        $this->api = new Api();

        add_action('admin_footer', [$this, 'modal_view']);
    }

    /**
     * show admin modal function
     * pass all page id and title to modal
     *
     * @return void
     * @since 1.0.0
     */
    public function modal_view()
    {

        $screen = get_current_screen();

        if ($screen->id == 'edit-em-form-template') {

            include_once ELE_MAILER_LITE_PLUGIN_PUBLIC_DIR . '/views/template-create-modal.php';
        }
    }
}
