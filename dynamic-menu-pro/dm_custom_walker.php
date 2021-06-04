<?php
/**
 * Custom Walker
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
//ini_set('display_errors', 1);
class dm_walker extends Walker_Nav_Menu {
    

    /*static $aMenuItems = NULL;

    function setup_menu_items($iMenuId) {
        if(empty(self::$aMenuItems)){
            self::$aMenuItems = wp_get_nav_menu_items($iMenuId);
        }
    }

    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        $menuData =get_option('dm_menu_html_code');
        global $wp_query;
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $class_names = $value = '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;

        if (isset($args->menu->term_id)) {
            $this->setup_menu_items($args->menu->term_id);
        }  
        $attributes = '';    
        $aChildrenMenuTypes = get_nav_menu_item_children($item->ID, self::$aMenuItems);

        if((is_tax('product_cat') && in_array('dynamic-cat', $aChildrenMenuTypes)) || (is_tax('product_tag') && in_array('dynamic-tag', $aChildrenMenuTypes))) {
            $classes[] = 'current-menu-ancestor';
            $classes[] = 'current-menu-parent';
            $classes[] = 'current_page_parent';
            $classes[] = 'current_page_ancestor';
        }


        $class_names =  join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );

        $class_names = $class_names ? esc_attr( $class_names ): '';

        if (($item->type == 'dynamic-cat' || $item->type == 'dynamic-tag' ) && $item->menu_item_parent != '0' ) {
            $output .= $indent;
        } else {
            if($item->type == 'dynamic-cat') {
             $sTaxonomyType = 'product_cat';
             $countchildren = count (get_term_children( $item->object_id, 'product_cat' ));
             $childClass =  $item->item_group == 0 || $countchildren>0 ?'menu-item-has-children has-dropdown':'';
             $class_names = $class_names ? esc_attr( $class_names ) . $sTaxonomyType.'  '.$childClass : '';
         } else if(($item->item_group !=1 && $item->type =='dynamic-cat') || ($item->type =='taxonomy' && $item->object =='product_cat' && $item->item_dynamic_child==1) ) {
             $sTaxonomyType = 'product_cat';
             $countchildren = count (get_term_children( $item->object_id, 'product_cat' ));
             if($item->type =='taxonomy' && $item->object =='product_cat' && $item->item_dynamic_child==1 && $countchildren>0 ) {
                 $class_names = $class_names ? esc_attr( $class_names ) . $sTaxonomyType.' menu-item-has-children has-dropdown' : '';
             }

         } else if($item->type == 'dynamic-tag') {
            $sTaxonomyType = 'product_tag';
            $class_names = $class_names ? esc_attr( $class_names ) . $sTaxonomyType.' menu-item-has-children has-dropdown' : '';
        }
        if(!empty($sTaxonomyType) && is_tax( $sTaxonomyType ) ) {
            $class_names .= " current-menu-ancestor current-menu-parent current_page_parent current_page_ancestor";
        }

        $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . 'class="'.$class_names .'   menu-item-'. $item->ID . '">';
        $attributes .= ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
        $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
        $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
        $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

        
        
    }

    $prepend = '';
    $append = '';
    $description  = ! empty( $item->description ) ? '<span>'.esc_attr( $item->description ).'</span>' : '';
    $aftertitle = '';
    if($depth != 0)
    {
        $description = $append = $prepend = '';
    } else {
        $attributes .= ' class="nav-top-link" ';
        $aftertitle = ''; // ' <i class="icon-angle-down"></i> ';
    }
    $link_before = (isset($args->link_before))?$args->link_before:'';
    $link_after = (isset($args->link_after))?$args->link_after:'';
    $subtitle = (isset($item->subtitle))?$item->subtitle:'';
    $after = (isset($args->after))?$args->after:'';
    $before = (isset($args->before))?$args->before:'';
    $item_output = '';
    if (($item->type == 'dynamic-cat' || $item->type == 'dynamic-tag') && $item->menu_item_parent != '0' ) {
        $item_output = $before;  
        $item_output .= $after;        
    } else {
        if($item->post_name !='dynamic-product-categories' || ($item->item_group !=1 && $item->post_name =='dynamic-product-categories') || ($item->type =='taxonomy' && $item->object =='product_cat' && $item->item_dynamic_child==1)) {
            $item_output = $before;
            $item_output .= '<a'. $attributes .'>';

            $item_output .= $link_before.$prepend.apply_filters( 'the_title', $item->title, $item->ID ).$append;
            $item_output .= $description.$link_after;
            $item_output .= ' '.$subtitle.$aftertitle.'</a>';
            $item_output .= $after;
        }  
    }

    $aSettings = [];
    $aSettings['order_by'] = $item->order_by;
    $aSettings['order'] = $item->order;
    $aSettings['product_count'] = $item->product_count;
    $aSettings['menu_item_parent'] = $item->menu_item_parent;
    $aSettings['hide_empty_menu'] = $item->hide_empty;
    $aSettings['iPadCount'] = $item->pad_count;
    $aSettings['item_dynamic_child'] = $item->item_dynamic_child;
    $aSettings['item_group'] = $item->item_group;
    $aSettings['object'] = $item->object;
    $aSettings['object_id'] = $item->object_id;
    $obj = new Dynamic_Menus();  
    if(!isset($menuData[$args->menu->term_id][$item->ID]['status']) || $menuData[$args->menu->term_id][$item->ID]['status']=='edited') {
        $item_output .= $obj->generate_dynamic_menu($item->type,$aSettings);
        $menuData[$args->menu->term_id][$item->ID]['html'] = $obj->generate_dynamic_menu($item->type,$aSettings);    
        $menuData[$args->menu->term_id][$item->ID]['status']='updated';    
        update_option('dm_menu_html_code',$menuData);
    } else { 
        $item_output .= $menuData[$args->menu->term_id][$item->ID]['html'];
    }

    $item->classes[] ='has-dropdown';

    $output .=  apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

}*/
}