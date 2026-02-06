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
 * HTTP requests utility.
 *
 * This module implements the functionality of HTTP requests.
 */
window.App.Utils.Http = (function () {
    /**
     * Perform an HTTP request.
     *
     * @param {String} method
     * @param {String} url
     * @param {Object} data
     *
     * @return {Promise}
     */
    function request(method, url, data) {
        return new Promise((resolve, reject) => {
            fetch(App.Utils.Url.siteUrl(url), {
                method,
                mode: 'cors',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                },
                redirect: 'follow',
                referrer: 'no-referrer',
                body: data ? JSON.stringify(data) : undefined,
            })
                .then((response) => {
                    if (!response.ok) {
                        response
                            .text()
                            .then((message) => {
                                const error = new Error(message);
                                error.status = response.status;
                                throw error;
                            })
                            .catch((error) => {
                                console.error(error);
                                reject(error);
                            });
                    }

                    return response;
                })
                .then((response) => {
                    return response.json();
                })
                .then((json) => {
                    resolve(json);
                })
                .catch((error) => {
                    console.error(error);
                    reject(error);
                });
        });
    }

    /**
     * Upload the provided file.
     *
     * @param {String} method
     * @param {String} url
     * @param {File} file
     *
     * @return {Promise}
     */
    function upload(method, url, file) {
        const formData = new FormData();

        formData.append('file', file, file.name);

        return new Promise((resolve, reject) => {
            fetch(App.Utils.Url.siteUrl(url), {
                method,
                redirect: 'follow',
                referrer: 'no-referrer',
                body: formData,
            })
                .then((response) => {
                    if (!response.ok) {
                        response
                            .text()
                            .then((message) => {
                                const error = new Error(message);
                                error.status = response.status;
                                throw error;
                            })
                            .catch((error) => {
                                console.error(error);
                                reject(error);
                            });
                    }

                    return response;
                })
                .then((response) => {
                    return response.json();
                })
                .then((json) => {
                    resolve(json);
                })
                .catch((error) => {
                    console.error(error);
                    reject(error);
                });
        });
    }

    /**
     * Download the requested URL.
     *
     * @param {String} method
     * @param {String} url
     *
     * @return {Promise}
     */
    function download(method, url) {
        return new Promise((resolve, reject) => {
            fetch(App.Utils.Url.siteUrl(url), {
                method,
                mode: 'cors',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                },
                redirect: 'follow',
                referrer: 'no-referrer',
            })
                .then((response) => {
                    if (!response.ok) {
                        response
                            .text()
                            .then((message) => {
                                const error = new Error(message);
                                error.status = response.status;
                                throw error;
                            })
                            .catch((error) => {
                                console.error(error);
                                reject(error);
                            });
                    }

                    return response;
                })
                .then((response) => {
                    return response.arrayBuffer();
                })
                .then((json) => {
                    resolve(json);
                })
                .catch((error) => {
                    console.error(error);
                    reject(error);
                });
        });
    }

    return {
        request,
        upload,
        download,
    };
})();
