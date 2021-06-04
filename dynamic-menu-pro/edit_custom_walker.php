<?php

/**

 *  /!\ This is a copy of Walker_Nav_Menu_Edit class in core

 * 

 * Create HTML list of nav menu input items.

 *

 * @package WordPress

 * @since 3.0.0

 * @uses Walker_Nav_Menu

 */

class Walker_Nav_Menu_Edit_Custom_DM extends Walker_Nav_Menu {

    /**

     * Starts the list before the elements are added.

     *

     * @see Walker_Nav_Menu::start_lvl()

     *

     * @since 3.0.0

     *

     * @param string $output Passed by reference.

     * @param int    $depth  Depth of menu item. Used for padding.

     * @param array  $args   Not used.

     */

    public function start_lvl( &$output, $depth = 0, $args = array() ) {}

 

    /**

     * Ends the list of after the elements are added.

     *

     * @see Walker_Nav_Menu::end_lvl()

     *

     * @since 3.0.0

     *

     * @param string $output Passed by reference.

     * @param int    $depth  Depth of menu item. Used for padding.

     * @param array  $args   Not used.

     */

    public function end_lvl( &$output, $depth = 0, $args = array() ) {}

 

    /**

     * Start the element output.

     *

     * @see Walker_Nav_Menu::start_el()

     * @since 3.0.0

     *

     * @global int $_wp_nav_menu_max_depth

     *

     * @param string $output Used to append additional content (passed by reference).

     * @param object $item   Menu item data object.

     * @param int    $depth  Depth of menu item. Used for padding.

     * @param array  $args   Not used.

     * @param int    $id     Not used.

     */

    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {



        if($item->type != 'dynamic-cat' && $item->type != 'dynamic-tag' ):

            if (class_exists( 'Avada_Nav_Walker_Megamenu' ) ){

                $avada = new Avada_Nav_Walker_Megamenu();

                return $avada->start_el($output, $item,$depth,$args,$id);

            }



            if (class_exists( 'avia_backend_walker' ) ){

                $avia_backend_walker = new avia_backend_walker();

                return $avia_backend_walker->start_el($output, $item,$depth,$args,$id);

            }

            if (class_exists( 'NMI_Walker_Nav_Menu_Edit' ) ){

                $nmi_walker = new NMI_Walker_Nav_Menu_Edit();

                return $nmi_walker->start_el($output, $item,$depth,$args,$id);

            }

            if (class_exists( 'Porto_Walker_Nav_Menu_Edit' ) ){
                
                $porto_walker = new Porto_Walker_Nav_Menu_Edit();

                return $porto_walker->start_el($output, $item,$depth,$args,$id);

            }

            if (class_exists( 'Walker_Nav_Menu_Edit_Custom_Fields' ) ){
                
                $pum_walker = new Walker_Nav_Menu_Edit_Custom_Fields();

                return $pum_walker->start_el($output, $item,$depth,$args,$id);

            }


        endif;

        

        global $_wp_nav_menu_max_depth;

        $_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

 

        ob_start();

        $item_id      = esc_attr( $item->ID );

        $removed_args = array(

            'action',

            'customlink-tab',

            'edit-menu-item',

            'menu-item',

            'page-tab',

            '_wpnonce',

        );

 

        $original_title = false;

 

        if ( 'taxonomy' == $item->type ) {

            $original_object = get_term( (int) $item->object_id, $item->object );

            if ( $original_object && ! is_wp_error( $original_title ) ) {

                $original_title = $original_object->name;

            }

        } elseif ( 'post_type' == $item->type ) {

            $original_object = get_post( $item->object_id );

            if ( $original_object ) {

                $original_title = get_the_title( $original_object->ID );

            }

        } elseif ( 'post_type_archive' == $item->type ) {

            $original_object = get_post_type_object( $item->object );

            if ( $original_object ) {

                $original_title = $original_object->labels->archives;

            }

        } elseif ( 'dynamic-cat' == $item->type ) {

            $original_title = ' Dynamic Product Categories'; 

        } elseif ( 'dynamic-tag' == $item->type ) {

            $original_object = get_post_type_object( $item->object );

            $original_title = ' Dynamic Product Tags'; 

        }

 

        $classes = array(

            'menu-item menu-item-depth-' . $depth,

            'menu-item-' . esc_attr( $item->object ),

            'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive' ),

        );

 

        $title = $item->title;

 

        if ( ! empty( $item->_invalid ) ) {

            $classes[] = 'menu-item-invalid';

            /* translators: %s: Title of an invalid menu item. */

            $title = sprintf( __( '%s (Invalid)' ), $item->title );

        } elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {

            $classes[] = 'pending';

            /* translators: %s: Title of a menu item in draft status. */

            $title = sprintf( __( '%s (Pending)' ), $item->title );

        }

 

        $title = ( ! isset( $item->label ) || '' == $item->label ) ? $title : $item->label;

 

        $submenu_text = '';

        if ( 0 == $depth ) {

            $submenu_text = 'style="display: none;"';

        }

        $sMenuLable = ( $item->type == 'dynamic-cat' || $item->type == 'dynamic-tag' )?'Dynamic Menu Item':$item->type_label;

 
        //initiate tree functionalities for categories.
        $isTreeOption = false;
        $customfieldNotTreeClass = "";
        $categoryTreePluginAvail = false;

        $customfieldOrderByTreeClass = "";
        if ($item->type == 'dynamic-cat' ) {

            if ( ! function_exists( 'is_plugin_active' ) ){
                require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
            }
            
            //check if category plugin tree is active.
            if (is_plugin_active('product-category-tree/init.php') || (is_plugin_active('product-category-tree-pro/product-category-tree-pro.php') && get_option('WCDC_license_key'))) 
            {
                $categoryTreePluginAvail = true;
                $customfieldNotTreeClass = "field-custom-not-tree";
                $customfieldOrderByTreeClass = "field-custom-orderby";
            }
        }
        ?>

        <li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode( ' ', $classes ); ?>">

            <div class="menu-item-bar">

                <div class="menu-item-handle">

                    <span class="item-title"><span class="menu-item-title"><?php echo esc_html( $title ); ?></span> <span class="is-submenu" <?php echo $submenu_text; ?>><?php _e( 'sub item' ); ?></span></span>

                    <span class="item-controls">

                        <span class="item-type"><?php echo esc_html( $sMenuLable ); ?></span>

                        <span class="item-order hide-if-js">

                            <?php

                            printf(

                                '<a href="%s" class="item-move-up" aria-label="%s">&#8593;</a>',

                                wp_nonce_url(

                                    add_query_arg(

                                        array(

                                            'action'    => 'move-up-menu-item',

                                            'menu-item' => $item_id,

                                        ),

                                        remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )

                                    ),

                                    'move-menu_item'

                                ),

                                esc_attr__( 'Move up' )

                            );

                            ?>

                            |

                            <?php

                            printf(

                                '<a href="%s" class="item-move-down" aria-label="%s">&#8595;</a>',

                                wp_nonce_url(

                                    add_query_arg(

                                        array(

                                            'action'    => 'move-down-menu-item',

                                            'menu-item' => $item_id,

                                        ),

                                        remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )

                                    ),

                                    'move-menu_item'

                                ),

                                esc_attr__( 'Move down' )

                            );

                            ?>

                        </span>

                        <?php

                        if ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) {

                            $edit_url = admin_url( 'nav-menus.php' );

                        } else {

                            $edit_url = add_query_arg(

                                array(

                                    'edit-menu-item' => $item_id,

                                ),

                                remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) )

                            );

                        }

 

                        printf(

                            '<a class="item-edit" id="edit-%s" href="%s" aria-label="%s"><span class="screen-reader-text">%s</span></a>',

                            $item_id,

                            $edit_url,

                            esc_attr__( 'Edit menu item' ),

                            __( 'Edit' )

                        );

                        ?>

                    </span>

                </div>

            </div>

 

            <div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">

                <?php if( 'custom' == $item->type ) : ?>

                    <p class="field-url description description-wide">

                        <label for="edit-menu-item-url-<?php echo $item_id; ?>">

                            <?php _e( 'URL' ); ?><br />

                            <input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->url ); ?>" />

                        </label>

                    </p>

                <?php endif; ?>

                <p class="description description-thin">

                    <label for="edit-menu-item-title-<?php echo $item_id; ?>">

                        <?php _e( 'Navigation Label' ); ?><br />

                        <input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->title ); ?>" />

                    </label>

                </p>

                <p class="description description-thin">

                    <label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">

                        <?php _e( 'Title Attribute' ); ?><br />

                        <input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />

                    </label>

                </p>

                <p class="field-link-target description">

                    <label for="edit-menu-item-target-<?php echo $item_id; ?>">

                        <input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank" name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />

                        <?php _e( 'Open link in a new window/tab' ); ?>

                    </label>

                </p>

                <p class="field-css-classes description description-thin">

                    <label for="edit-menu-item-classes-<?php echo $item_id; ?>">

                        <?php _e( 'CSS Classes (optional)' ); ?><br />

                        <input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />

                    </label>

                </p>

                <p class="field-xfn description description-thin">

                    <label for="edit-menu-item-xfn-<?php echo $item_id; ?>">

                        <?php _e( 'Link Relationship (XFN)' ); ?><br />

                        <input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />

                    </label>

                </p>

                <p class="field-description description description-wide">

                    <label for="edit-menu-item-description-<?php echo $item_id; ?>">

                        <?php _e( 'Description' ); ?><br />

                        <textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>

                        <span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.'); ?></span>

                    </label>

                </p>        

                <?php

                /* New fields insertion starts here */

                if ($item->type == 'dynamic-tag' || $item->type == 'dynamic-cat' ) {

                    $sHideEmpty = ($item->type == 'dynamic-tag')?'Hide Empty Tags':'Hide Empty Categories';

                    ?>   

                    <p class="field-custom description description-wide">

                        <label for="edit-menu-item-product-count-<?php echo $item_id; ?>">

                            <input type="checkbox" id="edit-menu-item-product-count-<?php echo $item_id; ?>" class="widefat code edit-menu-item-custom" name="menu-item-product-count[<?php echo $item_id; ?>]" value="check" <?php echo ($item->product_count == 'check')?'checked':'';?>/> Product Count

                        </label>

                        <label for="edit-menu-item-hide-empty-<?php echo $item_id; ?>">

                            <input type="checkbox" id="edit-menu-item-hide-empty-<?php echo $item_id; ?>" class="widefat code edit-menu-item-custom" name="menu-item-hide-empty[<?php echo $item_id; ?>]" value="check" <?php echo ($item->hide_empty == 'check')?'checked':'';?>/> <?php echo $sHideEmpty; ?>

                        </label>

                    </p>   

                    <p class="field-custom description description-wide">
                        <?php
                        if ($item->type == 'dynamic-cat' ) {

                            ?>

                                <label for="edit-menu-item-pad-count-<?php echo $item_id; ?>">

                                    <input type="checkbox" id="edit-menu-item-group-<?php echo $item_id; ?>" class="widefat code edit-menu-item-custom" name="menu-item-group[<?php echo $item_id; ?>]" value="1" <?php echo ($item->item_group == '1' )?'checked':'';?>/> Remove Grouping

                                </label>

                                <a href="javascript:void(0)" class="dm-tooltip">

                                    <label class="dm-info-icon"></label>

                                    <span>

                                        <img class="callout" src="<?php echo DM_RELATIVE_PATH; ?>/images/callout.gif" />

                                        Show the parent menu directly in the Menu (Without grouping)

                                    </span>

                                </a>

                            <?php 

                        } ?>
                        
                        <?php $labelRemoveOutOfStock = ($item->type == 'dynamic-tag') ? 'Remove Out of Stock Tags' : 'Remove Out of Stock Categories'; ?>
                        
                        <label for="edit-menu-item-hide-outofstock-<?php echo $item_id; ?>">

                            <input type="checkbox" id="edit-menu-item-hide-outofstock-<?php echo $item_id; ?>" class="widefat code edit-menu-item-custom" name="menu-item-hide-outofstock[<?php echo $item_id; ?>]" value="check" <?php echo ($item->hide_outofstock == 'check')?'checked':'';?>/> <?php echo $labelRemoveOutOfStock; ?>

                        </label>
                    </p> 

                    <p class="field-custom description description-wide <?php echo $customfieldOrderByTreeClass; ?>">

                        <label for="edit-menu-item-product-count-<?php echo $item_id; ?>">

                            Order by :

                            <?php

                            $OrderbyTitle = '';

                            if ($item->type == 'dynamic-cat' ) {

                                $OrderbyTitle = 'Category';

                            }

                            else if($item->type == 'dynamic-tag' ){

                                $OrderbyTitle = 'Tag';

                            }
                            
                            if ( !$categoryTreePluginAvail && $item->type == 'dynamic-cat') {
                                _e( "</br><span style='color:red;'>Order using 'Product Category Tree' will be available,if Product Category Tree Plugin is activated!!</span>");
                            }
                            ?> 

                            <select id="edit-menu-item-order-by-<?php echo $item_id; ?>" class="widefat code edit-menu-item-custom select-field-custom-orderby" name="menu-item-order-by[<?php echo $item_id; ?>]">

                            <option value="name" <?php echo ($item->order_by =='name')?'selected':'';?>>Default(Name)</option>

                                <option value="count" <?php echo ($item->order_by =='count')?'selected':'';?>>Count</option>

                                <option value="id" <?php echo ($item->order_by =='id')?'selected':'';?>><?php echo $OrderbyTitle; ?> ID</option>
                                <?php 
                                if( $categoryTreePluginAvail ) { ?>
                                    <option value="tree" <?php echo ($item->order_by =='tree')?'selected':'';?>> Category Tree </option>
                                <?php }
                                 ?>
                            </select>

                        </label>

                    </p>

                    <p class="field-custom description description-wide <?php echo $customfieldNotTreeClass; ?>">

                        <label for="edit-menu-item-product-count-<?php echo $item_id; ?>">

                            Order : 

                            <select id="edit-menu-item-order-<?php echo $item_id; ?>" class="widefat code edit-menu-item-custom" name="menu-item-order[<?php echo $item_id; ?>]">

                                <option value="ASC" <?php echo ($item->order == 'ASC')?'selected':'';?>>ASC</option>

                                <option value="DESC" <?php echo ($item->order == 'DESC')?'selected':'';?>>DESC</option>

                            </select>

                        </label>

                    </p>

                    <?php if ($item->type == 'dynamic-cat' ) { ?>

                        <p class="field-custom description description-wide <?php echo $customfieldNotTreeClass; ?>">

                            <label for="edit-menu-item-product-count-<?php echo $item_id; ?>">

                                Submenu order by :

                                <?php

                                $OrderbyTitle = '';

                                if ($item->type == 'dynamic-cat' ) {

                                    $OrderbyTitle = 'Category';

                                }

                                else if($item->type == 'dynamic-tag' ){

                                    $OrderbyTitle = 'Tag';

                                }

                                ?> 

                                <select id="edit-menu-item-submenu-order-by-<?php echo $item_id; ?>" class="widefat code edit-menu-item-custom" name="menu-item-submenu-order-by[<?php echo $item_id; ?>]">

                                <option value="name" <?php echo ($item->submenu_order_by =='name')?'selected':'';?>>Default(Name)</option>

                                    <option value="count" <?php echo ($item->submenu_order_by =='count')?'selected':'';?>>Count</option>

                                    <option value="id" <?php echo ($item->submenu_order_by =='id')?'selected':'';?>><?php echo $OrderbyTitle; ?> ID</option>

                                </select>

                            </label>

                        </p>

                        <p class="field-custom description description-wide <?php echo $customfieldNotTreeClass; ?>">

                            <label for="edit-menu-item-product-count-<?php echo $item_id; ?>">

                                Submenu Order : 

                                <select id="edit-menu-item-submenu-order-<?php echo $item_id; ?>" class="widefat code edit-menu-item-custom" name="menu-item-submenu-order[<?php echo $item_id; ?>]">

                                    <option value="ASC" <?php echo ($item->submenu_order == 'ASC')?'selected':'';?>>ASC</option>

                                    <option value="DESC" <?php echo ($item->submenu_order == 'DESC')?'selected':'';?>>DESC</option>

                                </select>

                            </label>

                        </p>

                    <?php } ?>

                    <?php

                    if ($item->type == 'dynamic-cat' ) 

                    { 

                        $exclude =array();
                        $include =array();

                        if (!empty($item->exclude[$item_id])) {

                            $exclude = $item->exclude[$item_id];

                        }

                        if (!empty($item->include[$item_id])) {

                            $include = $item->include[$item_id];

                        }

                        ?>

                        <p class="field-custom description description-wide exclude-multiselect-holder">

                        <label for="edit-menu-item-exclude-<?php echo $item_id; ?>">

                            Exlude : 

                            <select id="edit-menu-item-exclude-<?php echo $item_id; ?>" class="widefat code edit-menu-item-exclude" name="menu-item-exclude[<?php echo $item_id; ?>][]" multiple>

                                <option value="">Select Categories</option>

                                <?php

                                $taxonomy     = 'product_cat';

                                $orderby      = 'name';  

                                $show_count   = 0;      // 1 for yes, 0 for no

                                $pad_counts   = 0;      // 1 for yes, 0 for no

                                $hierarchical = 1;      // 1 for yes, 0 for no  

                                $title        = '';  

                                $empty        = 0;

                              

                                $args = array(

                                       'taxonomy'     => $taxonomy,

                                       'orderby'      => $orderby,

                                       'show_count'   => $show_count,

                                       'pad_counts'   => $pad_counts,

                                       'hierarchical' => $hierarchical,

                                       'title_li'     => $title,

                                       'hide_empty'   => $empty

                                );

                               $all_categories = get_categories( $args );

                               foreach ($all_categories as $cat) {

                                    ?>

                                          <option value="<?php echo $cat->term_id; ?>" <?php

                                          if(in_array($cat->term_id, $exclude)){ echo 'selected';}

                                          ?> ><?php echo $cat->name; ?></option>

                                    <?php

                                       }?>

                            </select>

                        </label>

                    </p>
                    
                    <!-- Include Only -->
                    <p class="field-custom description description-wide include-multiselect-holder">

                        <label for="edit-menu-item-include-<?php echo $item_id; ?>">

                            Include Only : 

                            <select id="edit-menu-item-include-<?php echo $item_id; ?>" class="widefat code edit-menu-item-include" name="menu-item-include[<?php echo $item_id; ?>][]" multiple>

                                <option value="">Select Categories</option>

                                <?php
                                if (!empty($all_categories)) {
                                    foreach ($all_categories as $cat) { ?>

                                        <option value="<?php echo $cat->term_id; ?>" <?php

                                        if(in_array($cat->term_id, $include)){ echo 'selected';}

                                        ?> ><?php echo $cat->name; ?></option>

                                        <?php 
                                    }
                                } ?>

                            </select>

                        </label>

                    </p>

                    <?php }

                    else if($item->type == 'dynamic-tag' )

                    { 

                        $exclude =array();
                        $include =array();

                        if (!empty($item->exclude[$item_id])) {

                            $exclude = $item->exclude[$item_id];

                        }
                        
                        if (!empty($item->include[$item_id])) {

                            $include = $item->include[$item_id];

                        }
                        ?>

                    <p class="field-custom description description-wide">

                        <label for="edit-menu-item-exclude-<?php echo $item_id; ?>">

                            Exlude : 

                            <select id="edit-menu-item-exclude-<?php echo $item_id; ?>" class="widefat code edit-menu-item-exclude" name="menu-item-exclude[<?php echo $item_id; ?>][]" multiple>

                            <option value="">Select Tags</option>

                            <?php

                                $taxonomy     = 'product_tag';

                                $orderby      = 'name';  

                                $show_count   = 0;      // 1 for yes, 0 for no

                                $pad_counts   = 0;      // 1 for yes, 0 for no

                                $hierarchical = 1;      // 1 for yes, 0 for no  

                                $title        = '';  

                                $empty        = 0;

                              

                                $args = array(

                                       'taxonomy'     => $taxonomy,

                                       'orderby'      => $orderby,

                                       'show_count'   => $show_count,

                                       'pad_counts'   => $pad_counts,

                                       'hierarchical' => $hierarchical,

                                       'title_li'     => $title,

                                       'hide_empty'   => $empty

                                );

                               $all_categories = get_categories( $args );

                               foreach ($all_categories as $cat) {

                                    ?>

                                          <option value="<?php echo $cat->term_id; ?>" <?php

                                          if(in_array($cat->term_id, $exclude)){ echo 'selected';}

                                          ?> ><?php echo $cat->name; ?></option>

                                    <?php

                                       }?>

                                </select>

                        </label>

                    </p>
                    
                    <!-- Include only for tags -->
                    <p class="field-custom description description-wide">

                        <label for="edit-menu-item-include-<?php echo $item_id; ?>">

                            Include Only : 

                            <select id="edit-menu-item-include-<?php echo $item_id; ?>" class="widefat code edit-menu-item-include" name="menu-item-include[<?php echo $item_id; ?>][]" multiple>

                                <option value="">Select Tags</option>

                                <?php

                               foreach ($all_categories as $cat) { ?>

                                    <option value="<?php echo $cat->term_id; ?>" <?php

                                    if(in_array($cat->term_id, $include)){ echo 'selected';}

                                    ?> ><?php echo $cat->name; ?></option>

                                    <?php

                                }?>

                            </select>

                        </label>

                    </p>

                    <?php }

                }

                /* New fields insertion ends here */

                if($item->object == 'product_cat') {

                    ?>

                    <p class="field-custom description description-wide">

                            <label for="edit-menu-item-pad-count-<?php echo $item_id; ?>">

                                <input type="checkbox" id="edit-menu-item-child-<?php echo $item_id; ?>" class="widefat code edit-menu-item-custom" name="menu-item-child[<?php echo $item_id; ?>]" value="1" <?php echo ($item->item_dynamic_child == '1' )?'checked':'';?>/> Show children dynamically

                            </label>

                            <a href="javascript:void(0)" class="dm-tooltip">

                                <label class="dm-info-icon"></label>

                                <span>

                                    <img class="callout" src="<?php echo DM_RELATIVE_PATH; ?>/images/callout.gif" />

                                    Show child categoris dynamically

                                </span>

                            </a>

                        </p> 

                    <?php

                }

                ?>



                <div class="menu-item-actions description-wide submitbox">

                    <?php if( 'custom' != $item->type && $original_title !== false ) : ?>

                        <p class="link-to-original">

                            <?php printf( __('Original: %s'), ('<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>')); ?>

                        </p>

                    <?php endif; ?>

                    <a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php

                    echo wp_nonce_url(

                        add_query_arg(

                            array(

                                'action' => 'delete-menu-item',

                                'menu-item' => $item_id,

                            ),

                            remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )

                        ),

                        'delete-menu_item_' . $item_id

                    ); ?>"><?php _e('Remove'); ?></a> <span class="meta-sep"> | </span> <a class="item-cancel submitcancel" id="cancel-<?php echo $item_id; ?>" href="<?php echo esc_url( add_query_arg( array('edit-menu-item' => $item_id, 'cancel' => time()), remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) ) ) );

                    ?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e('Cancel'); ?></a>

                </div>



                <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />

                <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />

                <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />

                <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />

                <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />

                <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />

            </div><!-- .menu-item-settings-->

            <ul class="menu-item-transport"></ul>

        <?php

        $output .= ob_get_clean();

    }

 

} // Walker_Nav_Menu_Edit