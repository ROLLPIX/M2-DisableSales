/**
 * Handles add-to-cart hiding and banner visibility based on customer group.
 * Used when customer group filtering or show_on_login is active.
 */
define([
    'jquery',
    'Magento_Customer/js/customer-data'
], function ($, customerData) {
    'use strict';

    var applied = false;

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

    return function (config) {
        var restrictedGroups = config.restrictedGroups || [];
        var bannerEnabled = config.bannerEnabled || false;
        var bannerShowOnLogin = config.bannerShowOnLogin || false;

        function applyRestrictions(customerInfo) {
            var isLoggedIn = !!(customerInfo && customerInfo.firstname);
            var groupId = isLoggedIn ? parseInt(customerInfo.group_id, 10) : 0;

            // Determine if this customer's group is restricted
            var isRestricted = (restrictedGroups.length === 0) || (restrictedGroups.indexOf(groupId) !== -1);

            // --- Add-to-cart button hiding ---
            if (isRestricted && isLoggedIn) {
                addHideTocartStyle();
            } else {
                removeHideTocartStyle();
            }

            // --- Banner visibility ---
            if (bannerEnabled) {
                var $banner = $('#rollpix-disable-sales-banner');
                var $closeBtn = $('#rollpix-disable-sales-close');

                if ($banner.length) {
                    var showBanner = isRestricted && isLoggedIn;

                    if (showBanner && localStorage.getItem('rollpix_banner_closed') !== '1') {
                        $banner.show();
                    } else {
                        $banner.hide();
                    }

                    // Attach close handler once
                    if (!applied) {
                        $closeBtn.on('click', function () {
                            $banner.hide();
                            localStorage.setItem('rollpix_banner_closed', '1');
                        });
                    }
                }
            }

            applied = true;
        }

        var customer = customerData.get('customer');
        customer.subscribe(function (data) {
            applyRestrictions(data);
        });
        applyRestrictions(customer());
    };
});
