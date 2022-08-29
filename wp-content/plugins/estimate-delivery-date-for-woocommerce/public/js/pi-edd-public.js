(function ($) {
  "use strict";

  /**
   * All of the code for your public-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */

  jQuery(document).ready(function ($) {
    /*
    $(document).on("change", ".shipping_method", function () {
      setTimeout(function () {
        jQuery("[name='update_cart']")
          .removeAttr("disabled")
          .trigger("click")
          .attr("disabled");
      }, 1000);
    });*/

    $(document).ajaxComplete(function (event, jqxhr, settings) {
      if (settings.url.includes("wc-ajax=update_shipping_method")) {
        jQuery("[name='update_cart']")
          .removeAttr("disabled")
          .trigger("click")
          .attr("disabled");
      }
    });

    /**
     * This is made so it create 2 cart refresh simultaneously and 
     */
    /*
    window.pisol_trigger_counter = 1;
    $(document).ajaxComplete(function (event, jqxhr, settings) {
      var counter = window.pisol_trigger_counter;
      if (settings.url.includes("wc-ajax=update_order_review") && window.pisol_trigger_counter % 2 == 1) {
        $(document.body).trigger("update_checkout");
        window.pisol_trigger_counter++;
      } else {
        window.pisol_trigger_counter++;
      }
    });
    */
    /* This is to refresh cart when address is added in the cart page */
    /*
    $(document).ajaxComplete(function (event, jqxhr, settings) {
      if ((settings.data.includes('woocommerce-shipping-calculator-nonce'))) {
        jQuery("[name='update_cart']").removeAttr("disabled").trigger("click");
      }
    });
    */



  });
})(jQuery);
