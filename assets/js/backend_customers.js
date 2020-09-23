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

window.BackendCustomers = window.BackendCustomers || {};

/**
 * Backend Customers
 *
 * Backend Customers javascript namespace. Contains the main functionality of the backend customers
 * page. If you need to use this namespace in a different page, do not bind the default event handlers
 * during initialization.
 *
 * @module BackendCustomers
 */
(function (exports) {

    'use strict';

    /**
     * The page helper contains methods that implement each record type functionality
     * (for now there is only the CustomersHelper).
     *
     * @type {Object}
     */
    var helper = {};

    /**
     * This method initializes the backend customers page. If you use this namespace
     * in a different page do not use this method.
     *
     * @param {Boolean} defaultEventHandlers Optional (false), whether to bind the default
     * event handlers or not.
     */
    exports.initialize = function (defaultEventHandlers) {
        defaultEventHandlers = defaultEventHandlers || false;

        // Fill available service categories listbox.
        availableLanguages.forEach(function (language) {
            $('#language').append(new Option(language, language));
        });

        $('#language').append(new Option('- ' + EALang.no_language + ' -', null)).val('null');

        helper = new CustomersHelper();
        helper.resetForm();
        helper.filter('');

        if (defaultEventHandlers) {
            bindEventHandlers();
        }
    };

    /**
     * Default event handlers declaration for backend customers page.
     */
    function bindEventHandlers() {
        helper.bindEventHandlers();
    }

})(window.BackendCustomers);
