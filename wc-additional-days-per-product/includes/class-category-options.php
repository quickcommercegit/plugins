<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
* WC Additional Days Per Product Category Fields
*/
class WC_Additional_Days_Per_Product_Category_Fields {
  function __construct() {
    add_action( 'product_cat_add_form_fields', array( $this, 'add_category_fields' ) );
    add_action( 'product_cat_edit_form_fields', array( $this, 'edit_category_fields' ), 10 );

    add_action( 'created_term', array( $this, 'save_category_fields' ), 10, 3 );
    add_action( 'edit_term', array( $this, 'save_category_fields' ), 10, 3 );
  }

  /**
   * Category extra fields.
   */
  public function add_category_fields() {
    ?>
    <div class="form-field term-display-type-wrap">
      <label for="additional_time"><?php _e( 'Dias adicionais', 'wc-additiona-days-per-product' ); ?></label>
      <input type="number" min="0" id="additional_time" name="additional_time" class="postform" />
    </div>
    <?php
  }

  /**
   * Edit category extra field.
   *
   * @param mixed $term Term (category) being edited
   */
  public function edit_category_fields( $term ) {
    $additional_time = get_term_meta( $term->term_id, 'additional_time', true );
    ?>
    <tr class="form-field">
      <th scope="row" valign="top"><?php _e( 'Dias adicionais', 'wc-additiona-days-per-product' ); ?></th>
      <td>
        <input value="<?php echo $additional_time; ?>" type="number" min="0" id="additional_time" name="additional_time" class="postform" />
      </td>
    </tr>
    <?php
  }

  /**
   * save_category_fields function.
   *
   * @param mixed $term_id Term ID being saved
   * @param mixed $tt_id
   * @param string $taxonomy
   */
  public function save_category_fields( $term_id, $tt_id = '', $taxonomy = '' ) {
    if ( isset( $_POST['additional_time'] ) && 'product_cat' === $taxonomy ) {
      update_term_meta( $term_id, 'additional_time', esc_attr( $_POST['additional_time'] ) );
    }
  }
}

new WC_Additional_Days_Per_Product_Category_Fields();
