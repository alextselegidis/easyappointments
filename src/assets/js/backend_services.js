/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2018, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

window.BackendServices = window.BackendServices || {};

/**
 * Backend Services
 *
 * This namespace handles the js functionality of the backend services page.
 *
 * @module BackendServices
 */
(function (exports) {

    'use strict';

    /**
     * Contains the basic record methods for the page.
     *
     * @type {ServicesHelper|CategoriesHelper}
     */
    var helper;

    var servicesHelper = new ServicesHelper();
    var categoriesHelper = new CategoriesHelper();

    /**
     * Default initialize method of the page.
     *
     * @param {Boolean} bindEventHandlers Optional (true), determines whether to bind the  default event handlers.
     */
    exports.initialize = function (bindEventHandlers) {
        bindEventHandlers = bindEventHandlers || true;

        // Fill available service categories listbox.
        $.each(GlobalVariables.categories, function (index, category) {
            var option = new Option(category.name, category.id);
            $('#service-category').append(option);
        });
        $('#service-category').append(new Option('- ' + EALang.no_category + ' -', null)).val('null');

        // Instantiate helper object (service helper by default).
        helper = servicesHelper;
        helper.resetForm();
        helper.filter('');

        if (bindEventHandlers) {
            _bindEventHandlers();
        }
    };

    /**
     * Binds the default event handlers of the backend services page.
     *
     * Do not use this method if you include the "BackendServices" namespace on another page.
     */
    function _bindEventHandlers() {
        /**
         * Event: Page Tab Button "Click"
         *
         * Changes the displayed tab.
         */
        $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
            if ($(this).attr('href') === '#services') {
                helper = servicesHelper;
            } else if ($(this).attr('href') === '#categories') {
                helper = categoriesHelper;
            }

            helper.resetForm();
            helper.filter('');
            $('.filter-key').val('');
            Backend.placeFooterToBottom();
        });

        servicesHelper.bindEventHandlers();
        categoriesHelper.bindEventHandlers();
    }

    /**
     * Update the service category listbox.
     *
     * Use this method every time a change is made to the service categories db table.
     */
    exports.updateAvailableCategories = function () {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_filter_service_categories';
        var data = {
            csrfToken: GlobalVariables.csrfToken,
            key: ''
        };

        $.post(url, data, function (response) {
            if (!GeneralFunctions.handleAjaxExceptions(response)) {
                return;
            }

            GlobalVariables.categories = response;
            var $select = $('#service-category');
            $select.empty();
            $.each(response, function (index, category) {
                var option = new Option(category.name, category.id);
                $select.append(option);
            });
            $select.append(new Option('- ' + EALang.no_category + ' -', null)).val('null');
        }, 'json').fail(GeneralFunctions.ajaxFailureHandler);
    };
})(window.BackendServices);
