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
 * URLs utility.
 *
 * This module implements the functionality of URLs.
 */
window.App.Utils.Url = (function () {
    /**
     * Get complete URL of the provided URI segment.
     *
     * @param {String} uri
     *
     * @return {String}
     */
    function baseUrl(uri) {
        return `${vars('base_url')}/${uri}`;
    }

    /**
     * Get the complete site URL (including the index.php) of the provided URI segment.
     *
     * @param {String} uri
     *
     * @returns {String}
     */
    function siteUrl(uri) {
        return `${vars('base_url')}${vars('index_page') ? '/' + vars('index_page') : ''}/${uri}`;
    }

    /**
     * Retrieve a query parameter from the current request.
     *
     * @link http://www.netlobo.com/url_query_string_javascript.html
     *
     * @param {String} name Parameter name.

     * @return {String} Returns the parameter value.
     */
    function queryParam(name) {
        const url = location.href;

        const parsedUrl = url.substr(url.indexOf('?')).slice(1).split('&');

        for (let index in parsedUrl) {
            const parsedValue = parsedUrl[index].split('=');

            if (parsedValue.length === 1 && parsedValue[0] === name) {
                return '';
            }

            if (parsedValue.length === 2 && parsedValue[0] === name) {
                return decodeURIComponent(parsedValue[1]);
            }
        }

        return null;
    }

    return {
        baseUrl,
        siteUrl,
        queryParam,
    };
})();
