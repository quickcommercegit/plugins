<?php
/**
 * Abstract deprecated hooks
 *
 * @package WC_Simulador_Parcelas
 * @since   2.0.0
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * WCSP_Deprecated_Hooks class maps old actions and filters to new ones. This is the base class for handling those deprecated hooks.
 *
 */
class WCSP_Deprecated {
  function __construct() {

    if ( ! function_exists( 'apply_filters_deprecated' ) ) {
      return false;
    }

    add_filter( 'wcsp_max_installments_no_fee', array( $this, 'wcsp_max_installments_no_fee' ), 10, 2 );

    add_filter( 'wcsp_get_main_price_discount', array( $this, 'product_get_main_price_discount' ), 10, 2 );
    add_filter( 'wcsp_get_main_price_discount', array( $this, 'category_discount' ), 10, 2 );

    add_filter( 'wcsp_text_after_price', array( $this, 'wcsp_text_after_price' ), 10, 2 );

    add_filter( 'wcsp_table_visibility', array( $this, 'wcsp_table_visibility' ), 10, 2 );

    add_filter( 'wcsp_shop_page', array( $this, 'wcsp_shop_page' ), 10, 2 );

    add_filter( 'wcsp_single_page', array( $this, 'wcsp_single_page' ), 10, 2 );

    add_filter( 'wcsp_ticket_visibilty', array( $this, 'wcsp_ticket_visibilty' ), 10, 2 );

    add_filter( 'wcsp_min_installment', array( $this, 'wcsp_min_installment' ), 10, 2 );

    add_filter( 'wcsp_installments_limit', array( $this, 'wcsp_installments_limit' ), 10, 2 );

    add_filter( 'wcsp_fee', array( $this, 'wcsp_fee' ), 10, 2 );


    add_filter( 'wcsp_load_css', array( $this, 'wcsp_load_css' ), 10 );
    add_filter( 'wcsp_extended_functions', array( $this, 'wcsp_extended_functions' ), 10, 1 );
    add_filter( 'wcsp_installment_with_fee', array( $this, 'wcsp_installment_with_fee' ), 10, 4 );

    add_filter( 'wcsp_show_original_price', array( $this, 'wcsp_show_original_price' ), 10, 3 );

    add_filter( 'wcsp_card_price_before_ticket_loop', array( $this, 'wcsp_card_price_before_ticket_loop' ), 10 );
    add_filter( 'wcsp_card_price_before_ticket_main_price', array( $this, 'wcsp_card_price_before_ticket_main_price' ), 10 );

    add_filter( 'wcsp_card_price_after_ticket_loop', array( $this, 'wcsp_card_price_after_ticket_loop' ), 10 );
    add_filter( 'wcsp_card_price_after_ticket_main_price', array( $this, 'wcsp_card_price_after_ticket_main_price' ), 10 );

    add_filter( 'wcsp_all_installments', array( $this, 'wcsp_all_installments' ), 10 );

    add_filter( 'wcsp_before_variation_text', array( $this, 'wcsp_before_variation_text' ), 10 );


    add_filter( 'wcsp_best_no_fee_main_price', array( $this, 'wcsp_best_no_fee_main_price' ), 10, 3 );
    add_filter( 'wcsp_best_no_fee_loop', array( $this, 'wcsp_best_no_fee_loop' ), 10, 3 );

    add_filter( 'wcsp_best_with_fee_main_price', array( $this, 'wcsp_best_with_fee_main_price' ), 10, 3 );
    add_filter( 'wcsp_best_with_fee_loop', array( $this, 'wcsp_best_with_fee_loop' ), 10, 3 );
  }


  public function wcsp_max_installments_no_fee( $installments, $product ) {
    if ( $product ) {
      $product_id   = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
      $product_max  = get_post_meta( $product_id, 'wsp_max_installment_no_fee', true );
      $installments = 0 < $product_max ? $product_max : $installments;
    }

    return $installments;
  }


  public function category_discount( $discount, $product ) {
    return apply_filters_deprecated( 'wc_simulador_parcelas_product_main_price_discount', array( $discount, $product ), 1.5, 'wcsp_get_main_price_discount' );
  }

  public function product_get_main_price_discount( $discount, $product ) {
    if ( $product ) {
      $product_id       = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
      $product_discount = get_post_meta( $product_id, 'wsp_main_price_discount', true );
      $discount         = 0 < $product_discount ? $product_discount : $discount;
    }

    return $discount;
  }


  public function wcsp_text_after_price( $text, $product ) {
    // old filter
    $text = apply_filters_deprecated( 'wc_simulador_parcelas_product_text_after_price', array( $text, $product ), 1.5, 'wcsp_text_after_price' );

    if ( $product ) {
      $product_id   = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
      $product_text = get_post_meta( $product_id, 'wsp_text_after_price', true );
      $text         = '' != $product_text ? $product_text : $text;
    }

    return $text;
  }


  public function wcsp_table_visibility( $visibility, $product ) {
    if ( $product ) {
      $product_id         = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
      $product_visibility = get_post_meta( $product_id, 'wsp_table_visibility', true );
      $visibility         = '' != $product_visibility ? $product_visibility : $visibility;
    }

    return $visibility;
  }


  public function wcsp_shop_page( $shop_page, $product ) {
    if ( $product ) {
      $product_id        = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
      $product_shop_page = get_post_meta( $product_id, 'wsp_shop_page_view', true );
      $shop_page         = '' != $product_shop_page ? $product_shop_page : $shop_page;
    }

    return $shop_page;
  }


  public function wcsp_single_page( $single_page, $product ) {
    if ( $product ) {
      $product_id          = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
      $product_single_page = get_post_meta( $product_id, 'wsp_single_page_view', true );
      $single_page         = '' != $product_single_page ? $product_single_page : $single_page;
    }

    return $single_page;
  }


  public function wcsp_ticket_visibilty( $visibility, $product ) {
    if ( $product ) {
      $product_id         = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
      $product_visibility = get_post_meta( $product_id, 'wsp_ticket_visibility', true );
      $visibility         = '' != $product_visibility ? $product_visibility : $visibility;
    }

    return $visibility;
  }


  public function wcsp_min_installment( $min, $product ) {
    if ( $product ) {
      $product_id  = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
      $product_min = get_post_meta( $product_id, 'wsp_min_installment', true );
      $min         = '' != $product_min ? $product_min : $min;
    }

    return $min;
  }


  public function wcsp_installments_limit( $limit, $product ) {
    if ( $product ) {
      $product_id    = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
      $product_limit = get_post_meta( $product_id, 'wsp_installments_limit', true );
      $limit         = '' != $product_limit ? $product_limit : $limit;
    }

    return $limit;
  }


  public function wcsp_fee( $fee, $product ) {
    if ( $product ) {
      $product_id  = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
      $product_fee = get_post_meta( $product_id, 'wsp_fee', true );
      $fee         = '' != $product_fee ? $product_fee : $fee;
    }

    return $fee;
  }


  /////////

  public function wcsp_load_css( $load_css ) {
    return apply_filters_deprecated( 'wc_simulador_parcelas_use_css', array( $load_css ), 1.5, 'wcsp_load_css' );
  }

  public function wcsp_extended_functions( $is_extended ) {
    return apply_filters_deprecated( 'wc_simulador_parcelas_use_extended_functions', array( $is_extended ), 1.5, 'wcsp_extended_functions' );
  }

  public function wcsp_installment_with_fee( $installment_price, $value, $fee, $installments ) {
    return apply_filters_deprecated( 'wc_simulador_parcelas_installment_with_fee', array( $installment_price, $value, $fee, $installments ), 1.5, 'wcsp_installment_with_fee' );
  }


  // apply-rules.php

  public function wcsp_show_original_price( $show, $product, $hook ) {
    return apply_filters_deprecated( 'wc_simulador_parcelas_display_original_price', array( $show, $product, $hook ), 1.5, 'wcsp_show_original_price' );
  }


  public function wcsp_card_price_before_ticket_main_price( $status ) {
    return apply_filters_deprecated( 'wc_simulador_parcelas_card_price_before_ticket_bank_main_price', array( $status ), 1.5, 'wcsp_card_price_before_ticket_main_price' );
  }

  public function wcsp_card_price_before_ticket_loop( $status ) {
    return apply_filters_deprecated( 'wc_simulador_parcelas_card_price_before_ticket_bank_loop', array( $status ), 1.5, 'wcsp_card_price_before_ticket_loop' );
  }

  public function wcsp_card_price_after_ticket_main_price( $status ) {
    return apply_filters_deprecated( 'wc_simulador_parcelas_card_price_after_ticket_bank_main_price', array( $status ), 1.5, 'wcsp_card_price_after_ticket_main_price' );
  }

  public function wcsp_card_price_after_ticket_loop( $status ) {
    return apply_filters_deprecated( 'wc_simulador_parcelas_card_price_after_ticket_bank_loop', array( $status ), 1.5, 'wcsp_card_price_after_ticket_loop' );
  }


  public function wcsp_all_installments( $installments ) {
    return apply_filters_deprecated( 'wc_simulador_parcelas_all_installments', array( $installments, '' ), 1.5, 'wcsp_all_installments' );
  }


  public function wcsp_before_variation_text( $text ) {
    return apply_filters_deprecated( 'wc_simulador_parcelas_before_variation_price_text', array( $text, '' ), 1.5, 'wcsp_before_variation_text' );
  }

  public function wcsp_best_no_fee_loop( $text, $best_no_fee, $product ) {
    return apply_filters_deprecated( 'wc_simulador_parcelas_best_no_fee_loop', array( $text, $best_no_fee, $product ), 1.5, 'wcsp_best_no_fee_loop' );
  }
  public function wcsp_best_no_fee_main_price( $text, $best_no_fee, $product ) {
    return apply_filters_deprecated( 'wc_simulador_parcelas_best_no_fee_main_price', array( $text, $best_no_fee, $product ), 1.5, 'wcsp_best_no_fee_main_price' );
  }


  public function wcsp_best_with_fee_loop( $text, $best_no_fee, $product ) {
    return apply_filters_deprecated( 'wc_simulador_parcelas_best_with_fee_loop', array( $text, $best_no_fee, $product ), 1.5, 'wcsp_best_with_fee_loop' );
  }
  public function wcsp_best_with_fee_main_price( $text, $best_no_fee, $product ) {
    return apply_filters_deprecated( 'wc_simulador_parcelas_best_with_fee_main_price', array( $text, $best_no_fee, $product ), 1.5, 'wcsp_best_with_fee_main_price' );
  }


}

new WCSP_Deprecated();
