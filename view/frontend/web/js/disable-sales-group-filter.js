/**
 * Handles add-to-cart hiding and banner visibility based on customer group.
 * Used when customer group filtering or show_on_login is active.
 */
define([
    'jquery',
    'Magento_Customer/js/customer-data'
], function ($, customerData) {
    'use strict';

    var bannerCloseHandlerAttached = false;

    function addHideTocartStyle() {
        if (!$('#rollpix-hide-tocart-style').length) {
            $('<style id="rollpix-hide-tocart-style">' +
                '.action.tocart, #product-addtocart-button, ' +
                'button.action.tocart, .product-item-actions .action.tocart ' +
                '{ display: none !important; }' +
                '</style>').appendTo('head');
        }
    }

    function removeHideTocartStyle() {
        $('#rollpix-hide-tocart-style').remove();
    }

    function showBanner() {
        var $banner = $('#rollpix-disable-sales-banner');
        if ($banner.length && localStorage.getItem('rollpix_banner_closed') !== '1') {
            $banner.show();
        }
    }

    function attachBannerCloseHandler() {
        if (bannerCloseHandlerAttached) {
            return;
        }
        bannerCloseHandlerAttached = true;
        $(document).on('click', '#rollpix-disable-sales-close', function () {
            $('#rollpix-disable-sales-banner').hide();
            localStorage.setItem('rollpix_banner_closed', '1');
        });
    }

    return function (config) {
        var restrictedGroups = config.restrictedGroups || [];
        var bannerEnabled = config.bannerEnabled || false;
        var showOnLogin = config.bannerShowOnLogin || false;

        function applyRestrictions(customerInfo) {
            var isLoggedIn = !!(customerInfo && customerInfo.firstname);
            var groupId = isLoggedIn ? parseInt(customerInfo.group_id, 10) : 0;

            // Guard against NaN from missing group_id in cached section data
            if (isNaN(groupId)) {
                groupId = 0;
            }

            // Determine if this customer's group is restricted
            var isRestricted = (restrictedGroups.length === 0) || (restrictedGroups.indexOf(groupId) !== -1);

            // --- Add-to-cart button hiding ---
            if (isRestricted) {
                addHideTocartStyle();
            } else {
                removeHideTocartStyle();
            }

            // --- Banner visibility ---
            // Only show, never actively hide. The banner starts hidden in JS mode
            // (display:none in HTML). Login/logout triggers a page reload so it resets.
            // The close button (X) is the only way to dismiss it.
            if (bannerEnabled && isRestricted && (!showOnLogin || isLoggedIn)) {
                attachBannerCloseHandler();
                showBanner();
            }
        }

        var customer = customerData.get('customer');

        // React to section data changes (login/logout)
        customer.subscribe(function (data) {
            applyRestrictions(data);
        });

        // Initial evaluation
        applyRestrictions(customer());

        // Re-evaluate after Magento finishes loading/refreshing section data
        if (typeof customerData.getInitCustomerData === 'function') {
            customerData.getInitCustomerData().done(function () {
                applyRestrictions(customer());
            });
        }

        // Handle browser back-forward cache restoration
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                applyRestrictions(customer());
            }
        });
    };
});
