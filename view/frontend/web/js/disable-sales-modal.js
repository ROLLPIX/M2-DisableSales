define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'Magento_Customer/js/customer-data'
], function ($, modal, customerData) {
    'use strict';

    function showModal() {
        if (sessionStorage.getItem('rollpix_modal_shown') === '1') {
            return;
        }

        var $modalEl = $('#rollpix-disable-sales-modal');
        if (!$modalEl.length) {
            return;
        }

        var options = {
            type: 'popup',
            responsive: true,
            title: '',
            modalClass: 'rollpix-disable-sales-modal',
            buttons: [{
                text: $.mage.__('Entendido'),
                class: 'action primary',
                click: function () {
                    this.closeModal();
                }
            }],
            closed: function () {
                sessionStorage.setItem('rollpix_modal_shown', '1');
            }
        };

        modal(options, $modalEl);
        $modalEl.modal('openModal');
    }

    return function (config) {
        config = config || {};

        if (!config.groupFilterEnabled) {
            // MODE 1: Legacy — show immediately
            showModal();
            return;
        }

        // MODE 2: Group filter — check customer group before showing
        var restrictedGroups = config.restrictedGroups || [];
        var showOnLogin = config.showOnLogin || false;
        var shown = false;

        function evaluate(customerInfo) {
            if (shown) {
                return;
            }

            var isLoggedIn = !!(customerInfo && customerInfo.firstname);
            var groupId = isLoggedIn ? parseInt(customerInfo.group_id, 10) : 0;

            // Show on login: only show for logged-in users
            if (showOnLogin && !isLoggedIn) {
                return;
            }

            // Check if customer's group is restricted
            var isRestricted = (restrictedGroups.length === 0) || (restrictedGroups.indexOf(groupId) !== -1);

            if (isRestricted) {
                shown = true;
                showModal();
            }
        }

        var customer = customerData.get('customer');
        customer.subscribe(function (data) {
            evaluate(data);
        });
        evaluate(customer());
    };
});
