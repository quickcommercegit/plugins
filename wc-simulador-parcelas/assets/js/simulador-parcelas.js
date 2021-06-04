/* global WCSimuladorParcelasParams */
/*!
 * WC Simulador de Parcelas.
 *
 * Version: 1.6.1
 */

jQuery( function( $ ) {

  /**
   * Init plugin.
   *
   * @type {Object}
   */
  var WCSimuladorParcelas = {

    /**
     * Initialize actions.
     */
    init: function() {
      // Initial load.
      $( document.body ).on( 'show_variation', function( event, variation, purchasable ) {
        WCSimuladorParcelas.updateTable( event, variation, purchasable );
      });
    },

    /**
     * Block checkout.
     */
    block: function() {
      $( 'form.checkout, form#order_review' )
        .addClass( 'processing' )
        .block({
          message: null,
          overlayCSS: {
          background: '#fff',
          opacity: 0.6
          }
        });
    },

    /**
     * Unblock checkout.
     */
    unblock: function() {
      $( 'form.checkout, form#order_review' )
        .removeClass( 'processing' )
        .unblock();
    },

    /**
     * Autocomplate address.
     *
     * @param {String} field Target.
     * @param {Boolean} copy
     */
    updateTable: function( event, variation, purchasable ) {
      var tbody = $( '.wcsp-table' ).find( 'tbody' );
      tbody.html( '<tr style="display: none !important;"></tr>' );

      var i    = 1;
      var fees = WCSimuladorParcelasParams.fees;
      var pagseguroFees = WCSimuladorParcelasParams.pagseguro_fees;
      while ( i <= WCSimuladorParcelasParams.max_installments ) {
        var fee = fees.hasOwnProperty( i ) ? fees[i] : WCSimuladorParcelasParams.fee;
        let pagseguroFee = pagseguroFees && pagseguroFees.hasOwnProperty( i ) ? pagseguroFees[i] : null;

        if ( i <= WCSimuladorParcelasParams.max_installments_no_fee ) {
          var price = variation.display_price / i;

          if ( price < WCSimuladorParcelasParams.min_installment ) {
            break;
          }

          tbody.append( '<tr class="fee-included"><th>' + tbody.data( 'default-text' ).replace( '{{ parcelas }}', i ).replace( '{{ valor }}', WCSimuladorParcelas.getFormattedPrice( price ) ).replace( '{{ juros }}', WCSimuladorParcelasParams.without_fee_label ) + '</th><th>' + WCSimuladorParcelas.getFormattedPrice( variation.display_price ) + '</th></tr>' );
        } else {
          if ( pagseguroFee ) {
            // pagseguro custom fees
            var final_cost = variation.display_price * pagseguroFee;
            var price      = final_cost / i;

          } else if ( WCSimuladorParcelasParams.fee !== fee ) {
            // custom fees
            var fee        = fee.replace( ',', '.' ) / 100;
            var final_cost = variation.display_price + ( variation.display_price * fee );
            var price      = final_cost / i;
          } else {
            var fee         = fee.replace( ',', '.' ) / 100;
            var exp         = Math.pow( 1 + fee, i );
            var price       = variation.display_price * fee * exp / ( exp - 1 );
            var final_cost  = price * i;
          }

          if ( price < WCSimuladorParcelasParams.min_installment ) {
            break;
          }

          tbody.append( '<tr class="fee-included"><th>' + tbody.data( 'default-text' ).replace( '{{ parcelas }}', i ).replace( '{{ valor }}', WCSimuladorParcelas.getFormattedPrice( price ) ).replace( '{{ juros }}', WCSimuladorParcelasParams.with_fee_label ) + '</th><th>' + WCSimuladorParcelas.getFormattedPrice( final_cost ) + '</th></tr>' );
        }

        i++;
      }
    },

    /**
     * Formatted Price.
     *
     * @param {String} price
     */
    getFormattedPrice: function( price ) {
      'use strict';

      var formatted_price = accounting.formatMoney( price, {
        symbol      : WCSimuladorParcelasParams.currency_format_symbol,
        decimal     : WCSimuladorParcelasParams.currency_format_decimal_sep,
        thousand    : WCSimuladorParcelasParams.currency_format_thousand_sep,
        precision   : WCSimuladorParcelasParams.currency_format_num_decimals,
        format      : WCSimuladorParcelasParams.currency_format
      } );

      return formatted_price;
    }
  };

  WCSimuladorParcelas.init();
});
