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

window.BackendCategories = window.BackendCategories || {};

/**
 * Backend Categories
 *
 * This namespace handles the js functionality of the backend categories page.
 *
 * @module BackendCategories
 */
(function (exports) {
    'use strict';

    /**
     * Contains the basic record methods for the page.
     *
     * @type {CategoriesHelper}
     */
    var helper;

    var categoriesHelper = new CategoriesHelper();

    /**
     * Default initialize method of the page.
     *
     * @param {Boolean} [defaultEventHandlers] Optional (true), determines whether to bind the  default event handlers.
     */
    exports.initialize = function (defaultEventHandlers) {
        defaultEventHandlers = defaultEventHandlers || true;

        // Instantiate helper object (category helper by default).
        helper = categoriesHelper;
        helper.resetForm();
        helper.filter('');
        helper.bindEventHandlers();

        if (defaultEventHandlers) {
            bindEventHandlers();
        }

        Backend.placeFooterToBottom();
    };

    /**
     * Binds the default event handlers of the backend categories page.
     *
     * Do not use this method if you include the "BackendCategories" namespace on another page.
     */
    function bindEventHandlers() {
        //
    }
})(window.BackendCategories);
