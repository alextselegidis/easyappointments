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
 * Business Settings HTTP client.
 *
 * This module implements the business settings related HTTP requests.
 */
App.Http.BusinessSettings = (function () {
    /**
     * Save business settings.
     *
     * @param {Object} businessSettings
     *
     * @return {Object}
     */
    function save(businessSettings) {
        const url = App.Utils.Url.siteUrl('business_settings/save');

        const data = {
            csrf_token: vars('csrf_token'),
            business_settings: businessSettings,
        };

        return $.post(url, data);
    }

    /**
     * Apply global working plan.
     *
     * @param {Object} workingPlan
     *
     * @return {Object}
     */
    function applyGlobalWorkingPlan(workingPlan) {
        const url = App.Utils.Url.siteUrl('business_settings/apply_global_working_plan');

        const data = {
            csrf_token: vars('csrf_token'),
            working_plan: JSON.stringify(workingPlan),
        };

        return $.post(url, data);
    }

    return {
        save,
        applyGlobalWorkingPlan,
    };
})();
