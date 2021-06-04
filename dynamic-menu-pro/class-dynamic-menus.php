<?php

/**

 * Custom Class

 *

 * @access public

 * @return void

 */



class Dynamic_Menus

{

    public $sOrder;

    public $numbers_array = array();

    public function __construct()

    {



        // add custom meta box in menu

        add_action('admin_init', array($this, 'add_nav_menu_meta_boxes'));



        add_action('init', array($this, 'add_language_domain'));



        // add custom menu fields to menu

        add_filter('wp_setup_nav_menu_item', array($this, 'dm_add_custom_nav_fields'), 20, 3);



        // save menu custom fields

        add_action('wp_update_nav_menu_item', array($this, 'dm_update_custom_nav_fields'), 10, 3);



        // edit menu walker

        add_filter('wp_edit_nav_menu_walker', array($this, 'dm_edit_walker'), 9999999999, 2);



        //add_action( 'wp_nav_menu_args', array( $this,'customize_menu_args'), 13, 4 );



        add_action('wp_enqueue_scripts', array($this, 'load_custom_scripts'));



        add_action('admin_enqueue_scripts', array($this, 'load_custom_admin_style'));



        add_filter('nav_menu_css_class', array($this, 'special_nav_class'), 10, 2);



        add_action('wp_ajax_avia_ajax_switch_menu_walker', array($this, 'dm_switch_menu_walker'), 1, 2);



        add_filter("wp_get_nav_menu_items", array($this, "wp_get_nav_menu_items_custom"), 100, 3);



    }



    public function dm_switch_menu_walker()

    {



        check_ajax_referer('add-menu_item', 'menu-settings-column-nonce');



        if (!current_user_can('edit_theme_options')) {

            wp_die(-1);

        }



        require_once ABSPATH . 'wp-admin/includes/nav-menu.php';



        // For performance reasons, we omit some object properties from the checklist.

        // The following is a hacky way to restore them when adding non-custom items.



        $menu_items_data = array();

        foreach ((array) $_POST['menu-item'] as $menu_item_data) {

            if (

                !empty($menu_item_data['menu-item-type']) &&

                'custom' != $menu_item_data['menu-item-type'] &&

                !empty($menu_item_data['menu-item-object-id'])

            ) {

                switch ($menu_item_data['menu-item-type']) {

                    case 'post_type':

                        $_object = get_post($menu_item_data['menu-item-object-id']);

                        break;



                    case 'post_type_archive':

                        $_object = get_post_type_object($menu_item_data['menu-item-object']);

                        break;



                    case 'taxonomy':

                        $_object = get_term($menu_item_data['menu-item-object-id'], $menu_item_data['menu-item-object']);

                        break;

                }



                $_menu_items = array_map('wp_setup_nav_menu_item', array($_object));

                $_menu_item = reset($_menu_items);



                // Restore the missing menu item properties

                $menu_item_data['menu-item-description'] = $_menu_item->description;

            }



            $menu_items_data[] = $menu_item_data;

        }



        $item_ids = wp_save_nav_menu_items(0, $menu_items_data);

        if (is_wp_error($item_ids)) {

            wp_die(0);

        }



        $menu_items = array();



        foreach ((array) $item_ids as $menu_item_id) {

            $menu_obj = get_post($menu_item_id);

            if (!empty($menu_obj->ID)) {

                $menu_obj = wp_setup_nav_menu_item($menu_obj);

                $menu_obj->label = $menu_obj->title; // don't show "(pending)" in ajax-added items

                $menu_items[] = $menu_obj;

            }

        }



        /** This filter is documented in wp-admin/includes/nav-menu.php */

        $walker_class_name = apply_filters('wp_edit_nav_menu_walker', 'Walker_Nav_Menu_Edit', $_POST['menu']);



        if (!class_exists($walker_class_name)) {

            wp_die(0);

        }



        if (!empty($menu_items)) {

            $args = array(

                'after' => '',

                'before' => '',

                'link_after' => '',

                'link_before' => '',

                'walker' => new $walker_class_name,

            );

            echo walk_nav_menu_tree($menu_items, 0, (object) $args);

        }

        wp_die();

    }



    public function special_nav_class($classes, $item)

    {

        //pr($item);die;

        if ($item->title == 'Cats') {

            $classes[] = 'current-menu-item';

        }

        return $classes;

    }



    /**

     * Change Language of plugin.

     */

    public function add_language_domain()

    {

        $lang = load_theme_textdomain('dynamic-menu', DM_PLUGIN_PATH . '/languages');

        global $pagenow;

        if ($pagenow == 'plugins.php') {

            add_action('admin_notices', array($this, 'setup_plugins_page_notices'));

        }

    }



    public function setup_plugins_page_notices()

    {



        $plugins = get_plugins();



        foreach ($plugins as $plugin_id => $plugin) {



            $slug = dirname($plugin_id);



            if ($slug === 'dynamic-menu-pro') {

                $license_key = get_option('DM_license_key');

                if (!empty($license_key)) {

                    $api_params = array(

                        'slm_action' => 'slm_check',

                        'secret_key' => DM_SPECIAL_SECRET_KEY,

                        'license_key' => $license_key,

                    );

                    //var_dump(add_query_arg($api_params, DM_LICENSE_SERVER_URL));

                    $response = wp_remote_get(add_query_arg($api_params, DM_LICENSE_SERVER_URL), array('timeout' => 20, 'sslverify' => false));

                    $data = json_decode($response['body'], true);

                    if ($data['status'] != 'active') {

                        add_action("after_plugin_row_" . $plugin_id, array($this, 'show_purchase_notice_under_plugin'), 10, 3);

                    }

                } else {

                    add_action("after_plugin_row_" . $plugin_id, array($this, 'show_purchase_notice_under_plugin'), 10, 3);

                }



            }

        }



    }



    public function show_purchase_notice_under_plugin($plugin_file, $plugin_data, $status)

    {

        $wp_list_table = _get_list_table('WP_Plugins_List_Table');

        $wp_version = preg_replace('/-(.+)$/', '', $GLOBALS['wp_version']);?>

		<tr class="plugin-update-tr installer-plugin-update-tr">

			<td colspan="<?php echo $wp_list_table->get_column_count(); ?>" class="plugin-update colspanchange">

				<div class="notice inline notice-warning notice-alt">

					<p class="installer-q-icon">

						<?php printf(__('You must have a valid subscription in order to get upgrades or support for this plugin. %sPurchase a subscription or enter an existing site key%s.', 'installer'),

            '<a href="https://togidata.dk/en/dynamic-menu-product-categories/" target="_blank">', '</a>');?>

					</p>

				</div>

			</td>

		</tr>

		<?php



    }



    /**

     * Add CSS to Admin Side.

     */

    public function load_custom_admin_style()

    {

        wp_enqueue_style('multiselect_css', plugins_url('css/jquery.multiselect.css', __FILE__));

        wp_enqueue_style('multiselect_filter_css', plugins_url('css/jquery.multiselect.filter.css', __FILE__));

        wp_enqueue_style('custom_admin_css', plugins_url('css/custom.css?'.rand(), __FILE__));



        wp_enqueue_script('jquery-ui-core');

        wp_enqueue_script( 'jquery-ui-widget' );

        wp_register_style('jquery-ui', plugins_url('css/jquery-ui/jquery-ui.css', __FILE__));

        wp_enqueue_style('jquery-ui');

        //Updated and deprecated functions replaced version of jquery ui core for widgets
        wp_enqueue_script('jquery-ui-core-updated', plugins_url('js/jquery-ui-core-updated.js?'.time(), __FILE__), [], '', true);

        wp_enqueue_script('multiselect-js', plugins_url('js/jquery.multiselect.js', __FILE__), [], '', true);

        wp_enqueue_script('multiselect-filter-js', plugins_url('js/jquery.multiselect.filter.js', __FILE__), [], '', true);
        
        wp_enqueue_script('my_custom_script', plugins_url('js/custom-admin.js?'.time(), __FILE__), [], '', true);

    }



    public function load_custom_scripts()

    {

        wp_enqueue_script( 'custom_js', plugins_url( 'js/custom.js', __FILE__ ), array('jquery'), 1, FALSE );

    }



    /**

     * Call walker class

     * @param $args: wp core menu args

     */

    public function customize_menu_args($args)

    {

        $args['walker'] = new dm_walker();

        //print_r($args);die;



        return $args;

    }



    /**

     * Add Custome meta box At Admin Side Menus Page

     */

    public function add_nav_menu_meta_boxes()

    {



        add_meta_box('dynamic_nav_link', __('Dynamic Menus'), array($this, 'nav_menu_link'), 'nav-menus', 'side', 'low');

    }



    /**

     * Callback Function for Meta box markup.

     */

    public function nav_menu_link()

    {

        ?>

		<div id="dynamic-cat-tag" class="posttypediv">

			<div id="tabs-panel-wishlist-login" class="tabs-panel tabs-panel-active">

				<ul id ="wishlist-login-checklist" class="categorychecklist form-no-clear">

					<li>

						<label class="menu-item-title">

							<input type="checkbox" class="menu-item-checkbox" name="menu-item[-1][menu-item-object-id]" value="-1"> <?php _e('Dynamic Product Categories', 'dynamic-menu');?>

						</label>

						<input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="dynamic-cat">

						<input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" value="Dynamic Product Categories">

					</li>

					<li>

						<label class="menu-item-title">

							<input type="checkbox" class="menu-item-checkbox" name="menu-item[-2][menu-item-object-id]" value="-2"><?php _e(' Dynamic Product Tags', 'dynamic-menu');?>

						</label>

						<input type="hidden" class="menu-item-type" name="menu-item[-2][menu-item-type]" value="dynamic-tag">

						<input type="hidden" class="menu-item-title" name="menu-item[-2][menu-item-title]" value="Dynamic Product Tags">

					</li>

				</ul>

			</div>

			<p class="button-controls">

				<span class="list-controls">

					<a href="<?php home_url();?>/wp-admin/nav-menus.php?post-tab=all&selectall=1#dynamic-cat-tag" class="select-all"><?php _e('Select All', 'dynamic-menu');?></a>

				</span>

				<span class="add-to-menu">

					<input type="submit" class="button-secondary submit-add-to-menu right" value="Add to Menu" name="add-post-type-menu-item" id="submit-dynamic-cat-tag">

					<span class="spinner"></span>

				</span>

			</p>

		</div>

		<?php

}



    /**

     * Insert Menu fields values into database.

     */

    public function dm_add_custom_nav_fields($menu_item)

    {

        if (get_post_meta($menu_item->ID, '_menu_item_hide_empty', true)) {

            $menu_item->hide_empty = get_post_meta($menu_item->ID, '_menu_item_hide_empty', true);

        }

        if (get_post_meta($menu_item->ID, '_menu_item_product_count', true)) {

            $menu_item->product_count = get_post_meta($menu_item->ID, '_menu_item_product_count', true);

        }

        if (get_post_meta($menu_item->ID, '_menu_item_pad_count', true)) {

            $menu_item->pad_count = get_post_meta($menu_item->ID, '_menu_item_pad_count', true);

        }

        if (get_post_meta($menu_item->ID, '_menu_item_order_by', true)) {

            $menu_item->order_by = get_post_meta($menu_item->ID, '_menu_item_order_by', true);

        }

        if (get_post_meta($menu_item->ID, '_menu_item_order', true)) {

            $menu_item->order = get_post_meta($menu_item->ID, '_menu_item_order', true);

        }

        if (get_post_meta($menu_item->ID, '_menu_item_group', true)) {

            $menu_item->item_group = get_post_meta($menu_item->ID, '_menu_item_group', true);

        }

        if (get_post_meta($menu_item->ID, '_menu_item_dynamic_child', true)) {

            $menu_item->item_dynamic_child = get_post_meta($menu_item->ID, '_menu_item_dynamic_child', true);

        }

        if (get_post_meta($menu_item->ID, '_menu_item_exclude', true)) {

            $menu_item->exclude = unserialize(get_post_meta($menu_item->ID, '_menu_item_exclude', true));

        }



        if (get_post_meta($menu_item->ID, '_menu_item_submenu_order_by', true)) {

            $menu_item->submenu_order_by = get_post_meta($menu_item->ID, '_menu_item_submenu_order_by', true);

        }

        if (get_post_meta($menu_item->ID, '_menu_item_submenu_order', true)) {

            $menu_item->submenu_order = get_post_meta($menu_item->ID, '_menu_item_submenu_order', true);

        }

        if (get_post_meta($menu_item->ID, '_menu_item_hide_outofstock', true)) {

            $menu_item->hide_outofstock = get_post_meta($menu_item->ID, '_menu_item_hide_outofstock', true);

        }

        if (get_post_meta($menu_item->ID, '_menu_item_include', true)) {

            $menu_item->include = unserialize(get_post_meta($menu_item->ID, '_menu_item_include', true));

        }

        return $menu_item;

    }



    /**

     * Update Menu fields values into database.

     */

    public function dm_update_custom_nav_fields($menu_id, $menu_item_db_id, $args)

    {

        // Check if element is properly sent



        if (isset($_REQUEST['menu-item-product-count']) && is_array($_REQUEST['menu-item-product-count']) && isset($_REQUEST['menu-item-product-count'][$menu_item_db_id])) {

            $product_count_value = $_REQUEST['menu-item-product-count'][$menu_item_db_id];

            update_post_meta($menu_item_db_id, '_menu_item_product_count', $product_count_value);

        } else {

            $product_count_value = 'uncheck';

            update_post_meta($menu_item_db_id, '_menu_item_product_count', $product_count_value);

        }

        if (isset($_REQUEST['menu-item-pad-count']) && is_array($_REQUEST['menu-item-pad-count']) && isset($_REQUEST['menu-item-pad-count'][$menu_item_db_id])) {

            $pad_count_value = $_REQUEST['menu-item-pad-count'][$menu_item_db_id];

            update_post_meta($menu_item_db_id, '_menu_item_pad_count', $pad_count_value);

        } else {

            $pad_count_value = '0';

            update_post_meta($menu_item_db_id, '_menu_item_pad_count', $pad_count_value);

        }



        if (isset($_REQUEST['menu-item-hide-empty']) && is_array($_REQUEST['menu-item-hide-empty']) && isset($_REQUEST['menu-item-hide-empty'][$menu_item_db_id])) {

            $hide_empty_value = $_REQUEST['menu-item-hide-empty'][$menu_item_db_id];

            update_post_meta($menu_item_db_id, '_menu_item_hide_empty', $hide_empty_value);

        } else {

            $hide_empty_value = 'uncheck';

            update_post_meta($menu_item_db_id, '_menu_item_hide_empty', $hide_empty_value);

        }



        if (isset($_REQUEST['menu-item-order-by']) && is_array($_REQUEST['menu-item-order-by']) && isset($_REQUEST['menu-item-order-by'][$menu_item_db_id])) {

            $order_by_value = $_REQUEST['menu-item-order-by'][$menu_item_db_id];



            update_post_meta($menu_item_db_id, '_menu_item_order_by', $order_by_value);

        }

        if (isset($_REQUEST['menu-item-order']) && is_array($_REQUEST['menu-item-order']) && isset($_REQUEST['menu-item-order'][$menu_item_db_id])) {

            $order_value = $_REQUEST['menu-item-order'][$menu_item_db_id];

            update_post_meta($menu_item_db_id, '_menu_item_order', $order_value);

        }

        if (isset($_REQUEST['menu-item-group']) && is_array($_REQUEST['menu-item-group']) && isset($_REQUEST['menu-item-group'][$menu_item_db_id])) {

            $_menu_item_group = $_REQUEST['menu-item-group'][$menu_item_db_id];

            update_post_meta($menu_item_db_id, '_menu_item_group', $_menu_item_group);

        } else {

            update_post_meta($menu_item_db_id, '_menu_item_group', 0);

        }



        if (isset($_REQUEST['menu-item-child']) && is_array($_REQUEST['menu-item-child']) && isset($_REQUEST['menu-item-child'][$menu_item_db_id])) {

            $_menu_item_dynamic_child = $_REQUEST['menu-item-child'][$menu_item_db_id];

            update_post_meta($menu_item_db_id, '_menu_item_dynamic_child', $_menu_item_dynamic_child);

        } else {

            update_post_meta($menu_item_db_id, '_menu_item_dynamic_child', 0);

        }

        if (isset($_REQUEST['menu-item-exclude']) && is_array($_REQUEST['menu-item-exclude']) && isset($_REQUEST['menu-item-exclude'][$menu_item_db_id])) {

            $_menu_item_exclude = $_REQUEST['menu-item-exclude'];

            update_post_meta($menu_item_db_id, '_menu_item_exclude', serialize($_menu_item_exclude));

        } else {

            update_post_meta($menu_item_db_id, '_menu_item_exclude', serialize(array()));

        }



        if (isset($_REQUEST['menu-item-submenu-order-by']) && is_array($_REQUEST['menu-item-submenu-order-by']) && isset($_REQUEST['menu-item-submenu-order-by'][$menu_item_db_id])) {

            $order_by_value = $_REQUEST['menu-item-submenu-order-by'][$menu_item_db_id];



            update_post_meta($menu_item_db_id, '_menu_item_submenu_order_by', $order_by_value);

        }

        if (isset($_REQUEST['menu-item-submenu-order']) && is_array($_REQUEST['menu-item-submenu-order']) && isset($_REQUEST['menu-item-submenu-order'][$menu_item_db_id])) {

            $order_value = $_REQUEST['menu-item-submenu-order'][$menu_item_db_id];

            update_post_meta($menu_item_db_id, '_menu_item_submenu_order', $order_value);

        }



        if (isset($_REQUEST['menu-item-hide-outofstock']) && is_array($_REQUEST['menu-item-hide-outofstock']) && isset($_REQUEST['menu-item-hide-outofstock'][$menu_item_db_id])) {

            $hide_outofstock_value = $_REQUEST['menu-item-hide-outofstock'][$menu_item_db_id];

            update_post_meta($menu_item_db_id, '_menu_item_hide_outofstock', $hide_outofstock_value);

        } else {

            $hide_outofstock_value = 'uncheck';

            update_post_meta($menu_item_db_id, '_menu_item_hide_outofstock', $hide_outofstock_value);

        }

        if (isset($_REQUEST['menu-item-include']) && is_array($_REQUEST['menu-item-include']) && isset($_REQUEST['menu-item-include'][$menu_item_db_id])) {

            $_menu_item_include = $_REQUEST['menu-item-include'];

            update_post_meta($menu_item_db_id, '_menu_item_include', serialize($_menu_item_include));

        } else {

            update_post_meta($menu_item_db_id, '_menu_item_include', serialize(array()));

        }
        
        update_option('dm_update_status', 'pending');

        $languages = apply_filters( 'wpml_active_languages', NULL, 'orderby=id&order=desc' );
        if (!empty($languages)) {
            $currentLanguage = ICL_LANGUAGE_CODE;
            update_option('dm_update_status_wpml_lang_'.$currentLanguage, 'pending');
        }
    }



    /**

     * Edit Menu fields values from database.

     * @param $menu_id : Id of menu field.

     */

    public function dm_edit_walker($walker, $menu_id)

    {

        return 'Walker_Nav_Menu_Edit_Custom_DM';



    }



    /**

     *

     * @param $aSettings : Parameters fill in menu field.

     * @param $sType : Accept type dynamic-cat/dynamic-tag.

     */

    public function generate_dynamic_menu($sType, $aSettings)

    {



        $this->sOrder = $aSettings['order'];

        $sType = $sType == 'post_type' ? $sType : (($sType == "dynamic-cat") ? 'category' : ($sType == "dynamic-cat" ? 'dynamic-tag' : (($sType == "taxonomy" && $aSettings['object'] == 'product_cat' && $aSettings['item_dynamic_child'] == 1) ? 'taxonomy' : 'tag')));

        $aMenus = $this->get_woocommerce_terms($sType, $aSettings);

        $sResult = $this->generate_dynamic_menu_markup($aMenus, $aSettings);



        if ($aSettings['menu_item_parent'] == 0) {

            if ($aSettings['item_group']) {

                if ($sType == 'category') {

                    $sResult = '<li class="sub-menu nav-dropdown nav-dropdown-bold">' . $sResult . '</li>';

                } else {

                    $sResult = '<ul class="sub-menu nav-dropdown nav-dropdown-bold">' . $sResult . '</ul>';

                }

            } else {

                $sResult = '<ul class="sub-menu nav-dropdown nav-dropdown-bold">' . $sResult . '</ul>';

            }

        }

        return $sResult;

    }



    /**

     *

     * @param $aSettings : Parameters fill in menu field.

     * @param $sTaxonomy : Accept type dynamic-cat/dynamic-tag.

     * @param $iParentTermId : Parent Term Id.

     */

    public function hierarchical_term_tree($sTaxonomy, $iParentTermId, $aSettings)

    {

        $aSettings['iPadCount'] = (isset($aSettings['iPadCount']) && $aSettings['iPadCount'] == '1') ? 1 : 0;



        $args = array(

            'taxonomy' => $sTaxonomy,

            'parent' => $iParentTermId,

            'show_count' => 1,

            'pad_counts' => $aSettings['iPadCount'],

            'item_group' => $aSettings['item_group'],

            'item_dynamic_child' => $aSettings['item_dynamic_child'],

            'hierarchical' => 1,

            'title_li' => '',

            'hide_empty' => $aSettings['iHideEmpty'],

        );



        if ($aSettings['order_by'] == 'default') {

            $args['menu_order'] = $aSettings['order'];

        } else {

            $args['menu_order'] = false;

            $args['orderby'] = $aSettings['order_by'];

            $args['order'] = $aSettings['order'];

        }



        $aCategories = get_categories($args);



        if (isset($aSettings['iPadCount']) && $aSettings['iPadCount'] == 0) {



            foreach ($aCategories as $value) {



                $value->count = $value->count > 0 ? $value->count : 0;

            }

        }



        if (isset($aSettings['order_by']) && $aSettings['order_by'] == 'count') {

            $aCategories = $this->custom_sort($aCategories, $aSettings['order']);

        }



        $aTerms = [];

        if ($aCategories):

            foreach ($aCategories as $oCat):

                $aTerm = [];

                $aTerm['title'] = $oCat->name;

                $aTerm['link'] = get_term_link($oCat->slug, $sTaxonomy);

                $aTerm['type'] = $sTaxonomy;

                $aTerm['id'] = $oCat->term_id;

                $aTerm['extra'] = $oCat;

                $aTerm['count'] = $oCat->count;

                $aChild = $this->hierarchical_term_tree($sTaxonomy, $oCat->term_id, $aSettings);

                if (!empty($aChild)) {

                    $aTerm['child'] = $aChild;

                }

                $aTerms[] = $aTerm;

            endforeach;

        endif;



        return $aTerms;

    }



    /**

     * Callback function of usort.

     */

    public function cmpCatrgories($a, $b)

    {



        if ($a->count == $b->count) {

            return 0;

        }



        switch ($this->sOrder) {

            case 'ASC':

                $aSort = ($a->count < $b->count) ? -1 : 1;

                break;



            case 'DESC':

                $aSort = ($a->count < $b->count) ? 1 : -1;

                break;

        }

        return $aSort;

    }



    /**

     * Custom sorting on 'count'.

     * @param $aCategories : Array of Categories.

     * @param $sOrder : Order ASC/DESC.

     */

    public function custom_sort($aCategories, $sOrder)

    {



        usort($aCategories, array($this, "cmpCatrgories"));



        return $aCategories;

    }



    /**

     *

     * @param $aSettings : Parameters fill in menu field.

     * @param $sType : Accept type dynamic-cat/dynamic-tag.

     */

    public function get_woocommerce_terms($sType, $aSettings)

    {



        $iHideEmpty = $aSettings['iHideEmpty'] = ($aSettings['hide_empty_menu'] == 'check') ? 1 : 0;



        switch ($sType) {

            case 'taxonomy':

                $aTerms = $this->hierarchical_term_tree('product_cat', $aSettings['object_id'], $aSettings);

                break;



            case 'category':

                $aTerms = $this->hierarchical_term_tree('product_cat', 0, $aSettings);

                break;



            case 'tag':

                $aTerms = $this->hierarchical_term_tree('product_tag', 0, $aSettings);

                break;



            default:

                $aTerms = [];

        }



        return $aTerms;

    }



    /**

     * @param $aSettings : Parameters fill in menu field.

     * @param $aMenus : array of items.

     */

    public function generate_dynamic_menu_markup($aMenus, $aSettings)

    {



        global $wp_query;

        $sOutput = '';

        if ($aMenus):

            foreach ($aMenus as $aMenu):



                $aChildIds = [];

                $sClass = '';

                $aClass = '';



                // Only run if we are on product category/tag page



                if (is_tax(array('product_cat', 'product_tag'))) {



                    $iCurrentTaxId = (int) $wp_query->queried_object_id;

                    if ($aMenu['type'] == 'product_cat') {

                        $aChildIds = get_term_children($aMenu['id'], $aMenu['type']);

                    }



                    if (is_tax($aMenu['type'], $aMenu['id'])) {

                        $sClass = 'current-menu-item';

                    } elseif (in_array($iCurrentTaxId, $aChildIds)) {

                    $sClass = 'current-menu-ancestor current-menu-parent current_page_parent current_page_ancestor';

                }

            }



            if (isset($aMenu['child']) && !empty($aMenu['child'])) {

                $sClass .= ' menu-item-has-children has-dropdown';

                $aClass .= ' nav-top-link ';

            }



            $cat_count = ($aSettings['product_count'] == 'check') ? ' (' . $aMenu['count'] . ')' : '';

            $sOutput .= '<li id="menu-item-' . $aMenu['id'] . '" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-' . $aMenu['id'] . ' ' . $sClass . '"><a href="' . $aMenu['link'] . '" class="' . $aClass . '" >' . $aMenu['title'] . $cat_count . '</a>';



            if (isset($aMenu['child'])) {

                $sOutput .= '<ul class="sub-menu nav-dropdown nav-dropdown-bold">' . $this->generate_dynamic_menu_markup($aMenu['child'], $aSettings) . '</ul>';

            }



            $sOutput .= '</li>';

        endforeach;



        endif;

        return $sOutput;



    }



    public function _custom_nav_menu_item($id = 0, $properties)

    {

        $titleExtention = '';

        if ($properties['product_count']) {

            $term = get_term($id, $properties['type']);

            $titleExtention .= ' (' . $term->count . ')';

        }

        $item = new stdClass();

        $n = 1000000;

        $number = ($n) + $id;

        if(in_array($number, $this->numbers_array)){

            $n += 10000;

            $number = ($n) + $id;

        }

        

        $this->numbers_array[] = $number;

        $item->ID = $number;

        $item->db_id = $item->ID;

        $item->title = $properties['title'] . $titleExtention;

        $item->url = $properties['url'];

        $item->menu_order = $item->ID;

        $item->menu_item_parent = $properties['parent'];

        $item->type = '';

        $item->object = '';

        $item->object_id = '';

        $item->classes = array('menu-item-slug-'.$properties['slug']);

        $item->target = '';

        $item->attr_title = '';

        $item->description = '';

        $item->xfn = '';

        $item->status = '';

        return $item;

    }



    public function wp_get_nav_menu_items_custom($items, $menu, $args)

    {



        if (is_admin()) {

            return $items;

        }

        $ctr = 1000000;

        $orderCount = 10000;

        $newItems = [];

        $menuHaveDynamicMenu = false;

        foreach ($items as $index => $i) {

            if ("dynamic-cat" !== $i->type && "dynamic-tag" !== $i->type) {

                $newItems[] = $i;
                
                continue;

            }

            if (isset($i->item_group) && $i->item_group == 1) {

                $menu_parent = 0;

            } else {

                $menu_parent = $i->ID;

            }



            if (isset($i->hide_empty) && $i->hide_empty == 'check') {

                $hide_empty = true;

            } else {

                $hide_empty = false;

            }



            if (isset($i->product_count) && $i->product_count == 'check') {

                $product_count = true;

            } else {

                $product_count = false;

            }

            $orderby = 'name';

            $order = 'ASC';

            $submenuOrderby = 'name';

            $submenuOrder = 'ASC';

            if (isset($i->order_by) && $i->order_by != 'default') {

                $orderby = $i->order_by;

            }

            if (isset($i->order)) {

                $order = $i->order;

            }

            if (isset($i->submenu_order_by) && $i->submenu_order_by != 'default') {

                $submenuOrderby = $i->submenu_order_by;

            }

            if (isset($i->submenu_order)) {

                $submenuOrder = $i->submenu_order;

            }



            $properties = array();

            $types = ($i->type == 'dynamic-cat' ? 'product_cat' : ($i->type == 'dynamic-tag' ? 'product_tag' : ''));

            $properties['hide_empty'] = $hide_empty;

            $properties['product_count'] = $product_count;

            $properties['type'] = $types;

            $properties['orderby'] = $orderby;

            $properties['order'] = $order;

            $properties['submenu_orderby'] = $submenuOrderby;

            $properties['submenu_order'] = $submenuOrder;

            if (isset($i->exclude[$i->ID])) {

                $properties['exclude'] = $i->exclude[$i->ID];

            }

            if (isset($i->include[$i->ID])) {

                $properties['include'] = $i->include[$i->ID];

            }

            if (isset($i->hide_outofstock) && $i->hide_outofstock == 'check') {

                $properties['hide_outofstock'] = true;

            }

            //Checking if WPML plugin active and selecting the active langiages
            $wpmlActive = false;
            $languages = apply_filters( 'wpml_active_languages', NULL, 'orderby=id&order=desc' );
            if (!empty($languages)) {
                $wpmlActive = true;
                $currentLanguage = ICL_LANGUAGE_CODE;
            }

            //If update type is set to daily 
            if (!empty(get_option('dm_update_type')) && get_option('dm_update_type') == 'daily') {

                if ($wpmlActive) {
                    if (!empty(get_option('dm_last_update_time_wpml_lang_'.$currentLanguage))) {
                        $lastUpdateTime = get_option('dm_last_update_time_wpml_lang_'.$currentLanguage);
                        $nextUpdateTime = $lastUpdateTime + (24 * 60 * 60);
                        $currentTime = time();

                        if ($currentTime > $nextUpdateTime) {
                            update_option('dm_update_status_wpml_lang_'.$currentLanguage, 'pending');
                            update_option('dm_last_update_time_wpml_lang_'.$currentLanguage, time());
                        }
                    } else {
                        update_option('dm_update_status_wpml_lang_'.$currentLanguage, 'pending');
                        update_option('dm_last_update_time_wpml_lang_'.$currentLanguage, time());
                    }
                } else {
                    if (!empty(get_option('dm_last_update_time'))) {
                        $lastUpdateTime = get_option('dm_last_update_time');
                        $nextUpdateTime = $lastUpdateTime + (24 * 60 * 60);
                        $currentTime = time();

                        if ($currentTime > $nextUpdateTime) {
                            update_option('dm_update_status', 'pending');
                            update_option('dm_last_update_time', time());
                        }
                    } else {
                        update_option('dm_update_status', 'pending');
                        update_option('dm_last_update_time', time());
                    }
                }
            }

            if ("dynamic-cat" == $i->type) {

                $newItems[] = $i; //Adding dynamic category item

                //check if order by tree available

                $categoryTreePluginAvail = false;

                

                if ( ! function_exists( 'is_plugin_active' ) ){

                    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

                }



                if (is_plugin_active('product-category-tree/init.php') || (is_plugin_active('product-category-tree-pro/product-category-tree-pro.php') && get_option('WCDC_license_key'))) {

                    $categoryTreePluginAvail = true;

                }

                if ($wpmlActive) {

                    if (get_option('dm_update_status_wpml_lang_'.$currentLanguage) && get_option('dm_update_status_wpml_lang_'.$currentLanguage) == 'updated' && !empty(get_option('dm_custom_menu_product_categories_wpml_lang_'.$currentLanguage))) {

                        $categs = get_option('dm_custom_menu_product_categories_wpml_lang_'.$currentLanguage);
                        
                    } else {
                        if( $orderby == 'tree' && $categoryTreePluginAvail){
                            
                            $categs = $this->load_categ_items_loop_from_tree(0, $ctr, $menu_parent, array(), $i, $properties);

                        } else {

                            $categs = $this->load_categ_items_loop(0, $ctr, $menu_parent, array(), $i, $properties);

                        }

                        update_option('dm_custom_menu_product_categories_wpml_lang_'.$currentLanguage, $categs);

                    }

                } else {

                    if (get_option('dm_update_status') && get_option('dm_update_status') == 'updated' && !empty(get_option('dm_custom_menu_product_categories'))) {

                        $categs = get_option('dm_custom_menu_product_categories');
                        
                    } else {
                        if( $orderby == 'tree' && $categoryTreePluginAvail){
                            
                            $categs = $this->load_categ_items_loop_from_tree(0, $ctr, $menu_parent, array(), $i, $properties);

                        } else {

                            $categs = $this->load_categ_items_loop(0, $ctr, $menu_parent, array(), $i, $properties);

                        }
                    }
                }

                if (get_option('dm_update_status') == 'pending') {
                    update_option('dm_custom_menu_product_categories', $categs);
                }

                foreach ($categs as $row) {

                    $row->menu_order = $orderCount++;

                    $items[] = $row;

                    $newItems[] = $row; //Adding category items

                }

            }

            if ("dynamic-tag" == $i->type) {

                $newItems[] = $i; //Adding dynamic tag element menu items

                if ($wpmlActive) {
                   
                    if (get_option('dm_update_status_wpml_lang_'.$currentLanguage) && get_option('dm_update_status_wpml_lang_'.$currentLanguage) == 'updated' && !empty(get_option('dm_custom_menu_product_tags_wpml_lang_'.$currentLanguage))) {
                        $terms = get_option('dm_custom_menu_product_tags_wpml_lang_'.$currentLanguage);
                    } else {
                        $terms = $this->load_term_items_loop(0, $ctr, $menu_parent, array(), $i, $properties);
                        update_option('dm_custom_menu_product_tags_wpml_lang_'.$currentLanguage, $terms);
                    }

                } else {
                    if (get_option('dm_update_status') && get_option('dm_update_status') == 'updated' && !empty(get_option('dm_custom_menu_product_tags'))) {
                        $terms = get_option('dm_custom_menu_product_tags');
                    } else {
                        $terms = $this->load_term_items_loop(0, $ctr, $menu_parent, array(), $i, $properties);
                    }
                }

                if (get_option('dm_update_status') == 'pending') {
                    update_option('dm_custom_menu_product_tags', $terms);
                }

                foreach ($terms as $row) {

                    $row->menu_order = $orderCount++;

                    $items[] = $row;

                    $newItems[] = $row; //Adding tags to menu items

                }



            }

            if (isset($i->item_group) && $i->item_group == 1) {

                unset($items[$index]);

            }

            $menuHaveDynamicMenu = true;
        }

        if ($menuHaveDynamicMenu) {
            update_option('dm_update_status', 'updated');

            if ($wpmlActive) {
                update_option('dm_update_status_wpml_lang_'.$currentLanguage, 'updated');
            }
        }

        //If remove groping enabled then remove the dynamic menu parent item
        foreach ($newItems as $index => $i) {

            if (isset($i->item_group) && $i->item_group == 1) {
                unset($newItems[$index]);
            }

        }
        
        return $newItems;

    }



    public function load_categ_items_loop_from_tree( $parent = 0, $ctr, $menu_parent, $item = array(), $i, $properties = array() )

    {

        $args = array(

            'taxonomy' => 'product_cat',

            'parent' => $parent,

            'hide_empty' => $properties['hide_empty'],

            'post_count' => -1,

        );



        if (!empty($properties['exclude'])) {

            $args['exclude'] = $properties['exclude'];

        }

        if (!empty($properties['include'])) {

            $args['include'] = $properties['include'];

        }



        $terms = get_terms($args);

        foreach ($terms as $term) {

            $prop = array();

            $prop['title'] = $term->name;
            
            $prop['slug'] = $term->slug;

            $prop['parent'] = $menu_parent;

            $prop['url'] = get_term_link($term);

            $properties = array_merge($properties, $prop);

            

            if (isset($properties['hide_outofstock'])) {

                $instockProductCount = $this->get_instock_product_count($term->term_id);

                

                if ($instockProductCount  == 0) {

                    continue;

                }

            }



            $new_item = $this->_custom_nav_menu_item($term->term_id, $properties);

            if (isset($i->target) && $i->target != '') {

                $new_item->target = $i->target;

            }

            $item[] = $new_item;

            $new_id = $new_item->ID;

            $ctr++;

            $args1 = array(

                'taxonomy' => 'product_cat',

                'parent' => $term->term_id,

                'hide_empty' => false,

            );

            if (!empty($properties['exclude'])) {

                $args1['exclude'] = $properties['exclude'];

            }

            if (!empty($properties['include'])) {

                $args1['include'] = $properties['include'];

            }

            $terms_child = get_terms($args1);

            if (!empty($terms_child)) {

                $item = $this->load_categ_items_loop_from_tree($term->term_id, $ctr, $new_id, $item, $i, $properties);

            }



        }

        return $item;

    }

    

    public function load_categ_items_loop($parent = 0, $ctr, $menu_parent, $item = array(), $i, $properties = array())

    {

        $args = array(

            'taxonomy' => 'product_cat',

            'parent' => $parent,

            'hide_empty' => $properties['hide_empty'],

            'post_count' => -1,

            'orderby' => $properties['orderby'],

            'order' => $properties['order'],

        );

        if (!empty($properties['exclude'])) {

            $args['exclude'] = $properties['exclude'];

        }

        if (!empty($properties['include'])) {

            $args['include'] = $properties['include'];

        }


        $terms = get_terms($args);

        foreach ($terms as $term) {

            $prop = array();

            $prop['title'] = $term->name;

            $prop['slug'] = $term->slug;

            $prop['order'] = $ctr;

            $prop['parent'] = $menu_parent;

            $prop['url'] = get_term_link($term);

            $properties = array_merge($properties, $prop);



            

            if (isset($properties['hide_outofstock'])) {

                $instockProductCount = $this->get_instock_product_count($term->term_id);

                

                if ($instockProductCount  == 0) {

                    continue;

                }

            }



            $new_item = $this->_custom_nav_menu_item($term->term_id, $properties);

            if (isset($i->target) && $i->target != '') {

                $new_item->target = $i->target;

            }

            $item[] = $new_item;

            $new_id = $new_item->ID;

            $ctr++;

            $args1 = array(

                'taxonomy' => 'product_cat',

                'parent' => $term->term_id,

                'hide_empty' => false,

                'orderby' => $properties['submenu_orderby'],

                'order' => $properties['submenu_order'],

            );

            if (!empty($properties['exclude'])) {

                $args1['exclude'] = $properties['exclude'];

            }

            if (!empty($properties['include'])) {

                $args1['include'] = $properties['include'];

            }

            $terms_child = get_terms($args1);

            if (!empty($terms_child)) {

                $properties['orderby'] = $properties['submenu_orderby'];

                $properties['order'] = $properties['submenu_order'];

                $item = $this->load_categ_items_loop($term->term_id, $ctr, $new_id, $item, $i, $properties);

            }



        }

        return $item;

    }

    public function load_term_items_loop($parent = 0, $ctr, $menu_parent, $item = array(), $i, $properties = array())

    {

        $args = array('taxonomy' => 'product_tag', 'parent' => $parent, 'hide_empty' => $properties['hide_empty'], 'post_count' => -1,'orderby' => $properties['orderby'],'order' => $properties['order']);

        if (!empty($properties['exclude'])) {

            $args['exclude'] = $properties['exclude'];

        }

        if (!empty($properties['include'])) {

            $args['include'] = $properties['include'];

        }

        $terms = get_terms($args);

        foreach ($terms as $term) {

            $prop = array();

            $prop['title'] = $term->name;

            $prop['slug'] = $term->slug;

            $prop['order'] = $ctr;

            $prop['parent'] = $menu_parent;

            $prop['url'] = get_term_link($term);

            $properties = array_merge($properties, $prop);



            if (isset($properties['hide_outofstock'])) {

                $instockProductCount = $this->get_instock_product_count_by_tag($term->term_id);

                

                if ($instockProductCount  == 0) {

                    continue;

                }

            }



            $new_item = $this->_custom_nav_menu_item($term->term_id, $properties);

            if (isset($i->target) && $i->target != '') {

                $new_item->target = $i->target;

            }

            $item[] = $new_item;

            $new_id = $new_item->ID;

            $ctr++;

            $args1 = array('taxonomy' => 'product_tag', 'parent' => $term->term_id, 'hide_empty' => false,'orderby' => $properties['orderby'],'order' => $properties['order']);

            if (!empty($properties['exclude'])) {

                $args1['exclude'] = $properties['exclude'];

            }

            if (!empty($properties['include'])) {

                $args1['include'] = $properties['include'];

            }

            $terms_child = get_terms($args1);

            if (!empty($terms_child)) {

                $item = $this->load_term_items_loop($term->term_id, $ctr, $new_id, $item, $i, $properties);

            }



        }

        return $item;

    }



    public function get_instock_product_count($categoryTermId)

    {

        $productQueryArgs = array(

            'posts_per_page' => -1,

            'post_type'      => 'product',

            'hide_empty'     => 1,        

            'tax_query' => array(

                array(

                    'taxonomy' => 'product_cat',

                    'field' => 'term_id',

                    'terms' => [$categoryTermId],

                    'operator' => 'IN'

                ),

            ),

            'meta_query' => array(

                    array(

                    'key' => '_stock_status',

                    'value' => 'instock',

                )

            )

        );

        $productQuery = new WP_Query( $productQueryArgs );



        return $productQuery->post_count;

    }



    public function get_instock_product_count_by_tag($tagId)

    {

        $productQueryArgs = array(

            'posts_per_page' => -1,

            'post_type'      => 'product',

            'hide_empty'     => 1,        

            'tax_query' => array(

                array(

                    'taxonomy' => 'product_tag',

                    'field' => 'term_id',

                    'terms' => [$tagId],

                    'operator' => 'IN'

                ),

            ),

            'meta_query' => array(

                    array(

                    'key' => '_stock_status',

                    'value' => 'instock',

                )

            )

        );

        $productQuery = new WP_Query( $productQueryArgs );



        return $productQuery->post_count;

    }

}

