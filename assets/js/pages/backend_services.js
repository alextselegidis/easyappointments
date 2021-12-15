/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2020, Alex Tselegidis
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
     * @type {ServicesHelper}
     */
    var helper;

    var servicesHelper = new ServicesHelper();

    /**
     * Default initialize method of the page.
     *
     * @param {Boolean} [defaultEventHandlers] Optional (true), determines whether to bind the  default event handlers.
     */
    exports.initialize = function (defaultEventHandlers) {
        defaultEventHandlers = defaultEventHandlers || true;

        // Instantiate helper object (service helper by default).
        helper = servicesHelper;
        helper.resetForm();
        helper.filter('');
        helper.bindEventHandlers();

        if (defaultEventHandlers) {
            bindEventHandlers();
        }

        BackendServices.updateAvailableCategories();

        Backend.placeFooterToBottom();
    };

    /**
     * Binds the default event handlers of the backend services page.
     *
     * Do not use this method if you include the "BackendServices" namespace on another page.
     */
    function bindEventHandlers() {
        //
    }

    /**
     * Update the service category list box.
     *
     * Use this method every time a change is made to the service categories db table.
     */
    exports.updateAvailableCategories = function () {
        var url = GlobalVariables.baseUrl + '/index.php/categories/search';

        var data = {
            csrf_token: GlobalVariables.csrfToken,
            key: ''
        };

        $.post(url, data).done(function (response) {
            GlobalVariables.categories = response;
            var $select = $('#service-category');

            $select.empty();

            response.forEach(function (category) {
                $select.append(new Option(category.name, category.id));
            });

            $select.append(new Option('- ' + App.Lang.no_category + ' -', null)).val('null');
        });
    };
})(window.BackendServices);
