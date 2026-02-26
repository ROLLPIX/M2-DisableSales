define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function ($, modal) {
    'use strict';

    return function () {
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
    };
});
