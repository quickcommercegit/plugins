jQuery( function( $ ) {
  /**
   * Admin class.
   *
   * @type {Object}
   */
  var WC_Any_Shipping_Notify_Orders = {

    /**
     * Initialize actions.
     */
    init: function() {
      $( document.body )
        .on( 'click', '.wc-action-button-wc-any-shipping-notify', this.showForm )
        .on( 'click', '.wc-any-shipping-notify-list-button', this.addTrackingCode );

      this.submitOnEnter();
    },

    /**
     * Block meta boxes.
     */
    block: function( el ) {
      el.block({
        message: null,
        overlayCSS: {
          background: '#fff',
          opacity: 0.6
        }
      });
    },

    /**
     * Unblock meta boxes.
     */
    unblock: function( el ) {
      el.unblock();
    },


    /**
     * Show form.
     */
    showForm: function( evt ) {
      evt.preventDefault();
      $( this ).parent().parent().find( '.list-table-new-tracking-code' ).toggle();
    },

    /**
     * Add tracking code
    */
    addTrackingCode: function() {
      var self   = WC_Any_Shipping_Notify_Orders;
      var line   = $( this ).closest( 'tr' );
      var data   = {
        action:        'wc_any_shipping_notify_orders_list',
        order_id:      $( this ).data( 'order-id' ),
        company:       line.find( '.wc-any-shipping-add-tracking-code-company' ).val(),
        tracking_code: line.find( '.wc-any-shipping-add-tracking-code' ).val(),
      };

      if ( '' === data.tracking_code ) {
        alert( 'Informe o código de rastreio.' );
        return;
      }

      self.block( line.find( '.list-table-new-tracking-code' ) );
      // Add tracking code.
      $.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        success: function( response ) {
          // console.log(response);
          if ( true === response.success ) {
            line.find( '.list-table-new-tracking-code' ).addClass( 'success' );
            line.find( '.wc-any-shipping-add-tracking-code' ).val( '' );
          } else {
          }

          self.unblock( line.find( '.list-table-new-tracking-code' ) );
        }
      });

      return true;
    },


    /**
     * Submit on enter
    */
    submitOnEnter: function() {
      $( '.wc-any-shipping-add-tracking-code' ).bind( 'keypress', function( e ) {
        if ( e.keyCode === 13 ) {
          e.preventDefault();
          $( this ).addClass( 'efffe' );
          $( this ).parent().find( '.button-secondary' ).click();
        }
      });
    }
  };

  WC_Any_Shipping_Notify_Orders.init();
});
