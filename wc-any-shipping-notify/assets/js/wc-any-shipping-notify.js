/* global wp, ajaxurl, WC_Any_Shipping_Notify_JS */
jQuery( function( $ ) {
  /**
   * Admin class.
   *
   * @type {Object}
   */
  var WCAnyShippingNotifyJS = {

    /**
     * Initialize actions.
     */
    init: function() {
      $( document.body )
        .on( 'click', '.wc-any-shipping-tracking-code .dashicons-dismiss', this.removeTrackingCode )
        .on( 'click', '.wc-any-shipping-tracking-code .button-secondary', this.addTrackingCode );

      this.submitOnClick();
    },

    /**
     * Block meta boxes.
     */
    block: function() {
      $( '#wc_any_shipping_notify' ).block({
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
    unblock: function() {
      $( '#wc_any_shipping_notify' ).unblock();
    },

    /**
     * Add tracking fields.
     *
     * @param {Object} $el Current element.
     */
    addTrackingFields: function( trackingCodes ) {
      var $wrap = $( 'body #wc_any_shipping_notify .wc-any-shipping-tracking-code' );
      var template = wp.template( 'wc-any-shipping-tracking-code-list' );

      $( '.wc-any-shipping-tracking-code__list', $wrap ).remove();
      $wrap.prepend( template( { 'trackingCodes': trackingCodes } ) );
    },

    /**
     * Add tracking code.
     *
     * @param {Object} evt Current event.
     */
    addTrackingCode: function( evt ) {
      evt.preventDefault();

      var $el          = $( '#wc-any-shipping-add-tracking-code' );
      var trackingCode = $el.val();

      var shippingCompany = $( '#wc-any-shipping-add-tracking-code-company' ).val();

      if ( '' === trackingCode ) {
        return;
      }

      var self = WCAnyShippingNotifyJS;
      var data = {
        action: 'wc_any_shipping_notify_add_tracking_code',
        security: WC_Any_Shipping_Notify_JS.nonces.add,
        order_id: WC_Any_Shipping_Notify_JS.order_id,
        tracking_code: trackingCode,
        shipping_company: shippingCompany,
      };

      if ( $( '.wcasn-custom-fields' ).length ) {
        var custom_fields = {};
        $( '.wcasn-custom-fields' ).each( function( index, el ) {
          custom_fields[ $( this ).attr( 'name' ) ] = $( this ).val();
        });

        data['custom_fields'] = custom_fields;
      }

      self.block();

      // Clean input.
      $el.val( '' );

      // Add tracking code.
      $.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        success: function( response ) {
          if ( 'no' !== response.data.result.status_updated ) {
            $( '#order_status' ).val( 'wc-' + response.data.result.status_updated ).trigger( 'change' );
          }

          self.addTrackingFields( response.data.codes );
          self.unblock();
        }
      });
    },

    /**
     * Remove tracking fields.
     *
     * @param {Object} $el Current element.
     */
    removeTrackingFields: function( $el ) {
      var $wrap = $( 'body #wc_any_shipping_notify .wc-any-shipping-tracking-code__list' );

      if ( 1 === $( 'li', $wrap ).length ) {
        $wrap.remove();
      } else {
        $el.closest( 'li' ).remove();
      }
    },

    /**
     * Remove tracking code.
     *
     * @param {Object} evt Current event.
     */
    removeTrackingCode: function( evt ) {
      evt.preventDefault();

      // Ask if really want remove the tracking code.
      if ( ! window.confirm( WC_Any_Shipping_Notify_JS.i18n.removeQuestion ) ) {
        return;
      }

      var shippingCompany = $( '#wc-any-shipping-add-tracking-code-company' ).val();

      var self = WCAnyShippingNotifyJS;
      var $el  = $( this );
      var data = {
        action: 'wc_any_shipping_notify_remove_tracking_code',
        security: WC_Any_Shipping_Notify_JS.nonces.remove,
        order_id: WC_Any_Shipping_Notify_JS.order_id,
        tracking_code: $el.data( 'code' ),
        shipping_company: shippingCompany,
      };

      self.block();

      // Remove tracking code.
      $.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        success: function() {
          self.removeTrackingFields( $el );
          self.unblock();
        }
      });
    },


    submitOnClick: function() {
      $( '#wc-any-shipping-add-tracking-code' ).bind( 'keypress', function( e ) {
        if ( e.keyCode === 13 ) {
          e.preventDefault();
          $( '.wc-any-shipping-tracking-code .button-secondary' ).click();
        }
      });
    }
  };

  WCAnyShippingNotifyJS.init();
});
