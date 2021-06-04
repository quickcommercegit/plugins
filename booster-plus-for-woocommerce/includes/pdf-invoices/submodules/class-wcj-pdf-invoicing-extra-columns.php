<?php
/**
 * Booster for WooCommerce - PDF Invoicing - Extra Columns
 *
 * @version 5.4.1
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_PDF_Invoicing_Extra_columns' ) ) :

class WCJ_PDF_Invoicing_Extra_columns extends WCJ_Module {

	/**
	 * Constructor.
	 *
	 * @version 5.4.1
	 */
	function __construct() { 

		$this->id         = 'pdf_invoicing_extra_columns';
		$this->parent_id  = 'pdf_invoicing';
		$this->short_desc = __( 'How to add extra columns(info) to pdf', 'woocommerce-jetpack' );
		$this->desc       = '';
		parent::__construct( 'submodule' );
    }
}

endif;

return new WCJ_PDF_Invoicing_Extra_columns();