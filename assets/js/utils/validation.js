/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * Validation utility.
 *
 * This module implements the functionality of validation.
 */
window.App.Utils.Validation = (function () {
    /**
     * Validate the provided email.
     *
     * @param {String} value
     *
     * @return {Boolean}
     */
    function email(value) {
        const re =
            /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

        return re.test(value);
    }

    /**
     * Validate the provided phone.
     *
     * @param {String} value
     *
     * @return {Boolean}
     */
    function phone(value) {
        const re = /^[+]?([0-9]*[\.\s\-\(\)]|[0-9]+){3,24}$/;

        return re.test(value);
    }

    return {
        email,
        phone,
    };
})();
