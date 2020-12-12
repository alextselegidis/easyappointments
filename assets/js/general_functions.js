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

window.GeneralFunctions = window.GeneralFunctions || {};

/**
 * General Functions Module
 *
 * It contains functions that apply both on the front and back end of the application.
 *
 * @module GeneralFunctions
 */
(function (exports) {

    'use strict';

    /**
     * Register global error handler.
     */
    document.addEventListener('DOMContentLoaded', function () {
        $(document).ajaxError(function (event, jqxhr, settings, thrownError) {
            GeneralFunctions.ajaxFailureHandler(jqxhr, settings, thrownError);
        });
    });

    /**
     * This functions displays a message box in the admin array. It is useful when user
     * decisions or verifications are needed.
     *
     * @param {String} title The title of the message box.
     * @param {String} message The message of the dialog.
     * @param {Array} buttons Contains the dialog buttons along with their functions.
     */
    exports.displayMessageBox = function (title, message, buttons) {
        if (!title) {
            title = '- No Title Provided -';
        }

        if (!message) {
            message = '- No Message Provided -';
        }

        if (!buttons) {
            buttons = [
                {
                    text: EALang.close,
                    click: function () {
                        $('#message-box').dialog('close');

                    }
                }
            ];
        }

        // Destroy previous dialog instances.
        $('#message-box')
            .dialog('destroy')
            .remove();

        // Create the html of the message box.
        $('<div/>', {
            'id': 'message-box',
            'title': title,
            'html': [
                $('<p/>', {
                    'html': message
                })
            ]
        })
            .appendTo('body');

        $("#message-box").dialog({
            autoOpen: false,
            modal: true,
            resize: 'auto',
            width: 'auto',
            height: 'auto',
            resizable: false,
            buttons: buttons,
            closeOnEscape: true
        });

        $('#message-box').dialog('open');
        $('.ui-dialog .ui-dialog-buttonset button').addClass('btn btn-outline-secondary');
        $('#message-box .ui-dialog-titlebar-close').hide();
    };

    /**
     * This method centers a DOM element vertically and horizontally on the page.
     *
     * @param {Object} elementHandle The object that is going to be centered.
     */
    exports.centerElementOnPage = function (elementHandle) {
        // Center main frame vertical middle
        $(window).resize(function () {
            var elementLeft = ($(window).width() - elementHandle.outerWidth()) / 2;
            var elementTop = ($(window).height() - elementHandle.outerHeight()) / 2;
            elementTop = elementTop > 0 ? elementTop : 20;

            elementHandle.css({
                position: 'absolute',
                left: elementLeft,
                top: elementTop
            });
        });
        $(window).resize();
    };

    /**
     * This function retrieves a parameter from a "GET" formed url.
     *
     * @link http://www.netlobo.com/url_query_string_javascript.html
     *
     * @param {String} url The selected url.
     * @param {String} parameterName The parameter name.

     * @return {String} Returns the parameter value.
     */
    exports.getUrlParameter = function (url, parameterName) {
        var parsedUrl = url.substr(url.indexOf('?')).slice(1).split('&');

        for (var index in parsedUrl) {
            var parsedValue = parsedUrl[index].split('=');

            if (parsedValue.length === 1 && parsedValue[0] === parameterName) {
                return '';
            }

            if (parsedValue.length === 2 && parsedValue[0] === parameterName) {
                return decodeURIComponent(parsedValue[1]);
            }
        }

        return '';
    };

    /**
     * Convert date to ISO date string.
     *
     * This function creates a RFC 3339 date string. This string is needed by the Google Calendar API
     * in order to pass dates as parameters.
     *
     * @param {Date} date The given date that will be transformed.

     * @return {String} Returns the transformed string.
     */
    exports.ISODateString = function (date) {
        function pad(n) {
            return n < 10 ? '0' + n : n;
        }

        return date.getUTCFullYear() + '-'
            + pad(date.getUTCMonth() + 1) + '-'
            + pad(date.getUTCDate()) + 'T'
            + pad(date.getUTCHours()) + ':'
            + pad(date.getUTCMinutes()) + ':'
            + pad(date.getUTCSeconds()) + 'Z';
    };

    /**
     * Clone JS Object
     *
     * This method creates and returns an exact copy of the provided object. It is very useful whenever changes need to
     * be made to an object without modifying the original data.
     *
     * @link https://stackoverflow.com/a/728694
     *
     * @param {Object} originalObject Object to be copied.

     * @return {Object} Returns an exact copy of the provided element.
     */
    exports.clone = function (originalObject) {
        // Handle the 3 simple types, and null or undefined
        if (!originalObject || typeof originalObject !== 'object') {
            return originalObject;
        }

        var copy;

        // Handle Date
        if (originalObject instanceof Date) {
            copy = new Date();
            copy.setTime(originalObject.getTime());
            return copy;
        }

        // Handle Array
        if (originalObject instanceof Array) {
            copy = [];
            for (var i = 0, len = originalObject.length; i < len; i++) {
                copy[i] = GeneralFunctions.clone(originalObject[i]);
            }
            return copy;
        }

        // Handle Object
        if (originalObject instanceof Object) {
            copy = {};
            for (var attr in originalObject) {
                if (originalObject.hasOwnProperty(attr))
                    copy[attr] = GeneralFunctions.clone(originalObject[attr]);
            }
            return copy;
        }

        throw new Error('Unable to copy obj! Its type isn\'t supported.');
    };

    /**
     * Validate Email Address
     *
     * This method validates an email address. If the address is not on the proper
     * form then the result is FALSE.
     *
     * @link http://badsyntax.co/post/javascript-email-validation-rfc822
     *
     * @param {String} email The email address to be checked.

     * @return {Boolean} Returns the validation result.
     */
    exports.validateEmail = function (email) {
        var re = /^([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22))*\x40([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d))*$/;
        return re.test(email);
    };


    /**
     * Makes the first letter of the string upper case.
     *
     * @param {String} value The string to be converted.
     *
     * @return {String} Returns the capitalized string.
     */
    exports.upperCaseFirstLetter = function (value) {
        return value.charAt(0).toUpperCase() + value.slice(1);
    };

    /**
     * Enable Language Selection
     *
     * Enables the language selection functionality. Must be called on every page has a language selection button.
     * This method requires the global variable 'availableLanguages' to be initialized before the execution.
     *
     * @param {Object} $element Selected element button for the language selection.
     */
    exports.enableLanguageSelection = function ($element) {
        // Select Language
        var $languageList = $('<ul/>', {
            'id': 'language-list',
            'html': availableLanguages.map(function (availableLanguage) {
                return $('<li/>', {
                    'class': 'language',
                    'data-language': availableLanguage,
                    'text': GeneralFunctions.upperCaseFirstLetter(availableLanguage)
                })
            })
        });

        $element.popover({
            placement: 'top',
            title: 'Select Language',
            content: $languageList[0],
            html: true,
            container: 'body',
            trigger: 'manual'
        });

        $element.on('click', function (event) {
            event.stopPropagation();

            if ($('#language-list').length === 0) {
                $(this).popover('show');
            } else {
                $(this).popover('hide');
            }

            $(this).toggleClass('active');
        });

        $(document).on('click', 'li.language', function () {
            // Change language with ajax call and refresh page.
            var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_change_language';

            var data = {
                csrfToken: GlobalVariables.csrfToken,
                language: $(this).attr('data-language')
            };

            $.post(url, data)
                .done(function () {
                    document.location.reload(true);
                });
        });

        $(document).on('click', function () {
            $element.popover('hide');
        });
    };

    /**
     * AJAX Failure Handler
     *
     * @param {jqXHR} jqXHR
     * @param {String} textStatus
     * @param {Object} errorThrown
     */
    exports.ajaxFailureHandler = function (jqXHR, textStatus, errorThrown) {
        console.error('Unexpected HTTP Error: ', jqXHR, textStatus, errorThrown);

        var response;

        try {
            response = JSON.parse(jqXHR.responseText); // JSON response
        } catch (error) {
            response = {message: jqXHR.responseText}; // String response
        }

        if (!response) {
            return;
        }

        GeneralFunctions.displayMessageBox(EALang.unexpected_issues, EALang.unexpected_issues_message);

        $('<div/>', {
            'class': 'card',
            'html': [
                $('<div/>', {
                    'class': 'card-body',
                    'html': response.message || '→ No error information provided.'
                })
            ]
        })
            .appendTo('#message-box');
    };

    /**
     * Escape JS HTML string values for XSS prevention.
     *
     * @param {String} content String to be escaped.
     *
     * @return {String} Returns the escaped string.
     */
    exports.escapeHtml = function (content) {
        return $('<div/>').text(content).html();
    };

    /**
     * Format a given date according to the date format setting.
     *
     * @param {String} date The date to be formatted.
     * @param {String} dateFormatSetting The setting provided by PHP must be one of the "DMY", "MDY" or "YMD".
     * @param {Boolean} addHours (optional) Whether to add hours to the result.

     * @return {String} Returns the formatted date string.
     */
    exports.formatDate = function (date, dateFormatSetting, addHours) {
        var timeFormat = GlobalVariables.timeFormat === 'regular' ? 'h:mm tt' : 'HH:mm';
        var hours = addHours ? ' ' + timeFormat : '';
        var result;
        var parsedDate = Date.parse(date);

        if (!parsedDate) {
            return date;
        }

        switch (dateFormatSetting) {
            case 'DMY':
                result = parsedDate.toString('dd/MM/yyyy' + hours);
                break;
            case 'MDY':
                result = parsedDate.toString('MM/dd/yyyy' + hours);
                break;
            case 'YMD':
                result = parsedDate.toString('yyyy/MM/dd' + hours);
                break;
            default:
                throw new Error('Invalid date format setting provided!', dateFormatSetting);
        }

        return result;
    };


    /**
     * Get the Id of a Weekday using the US week format and day names (Sunday=0) as used in the JS code of the
     * application, case insensitive, short and long names supported.
     *
     * @param {String} weekDayName The weekday name among Sunday, Monday, Tuesday, Wednesday, Thursday, Friday,
     * Saturday.

     * @return {Number} Returns the ID of the weekday.
     */
    exports.getWeekDayId = function (weekDayName) {
        var result;

        switch (weekDayName.toLowerCase()) {

            case 'sunday':
            case 'sun':
                result = 0;
                break;

            case 'monday':
            case 'mon':
                result = 1;
                break;

            case 'tuesday':
            case 'tue':
                result = 2;
                break;

            case 'wednesday':
            case 'wed':
                result = 3;
                break;

            case 'thursday':
            case 'thu':
                result = 4;
                break;

            case 'friday':
            case 'fri':
                result = 5;
                break;

            case 'saturday':
            case 'sat':
                result = 6;
                break;

            default:
                throw new Error('Invalid weekday name provided!', weekDayName);
        }

        return result;
    };

    /**
     * Get the name in lowercase of a Weekday using its Id.
     *
     * @param {Number} weekDayId The Id (From 0 for sunday to 6 for saturday).

     * @return {String} Returns the name of the weekday.
     */
    exports.getWeekdayName = function (weekDayId) {
        var result;

        switch (weekDayId) {

            case 0:
                result = 'sunday';
                break;

            case 1:
                result = 'monday';
                break;

            case 2:
                result = 'tuesday';
                break;

            case 3:
                result = 'wednesday';
                break;

            case 4:
                result = 'thursday';
                break;

            case 5:
                result = 'friday';
                break;

            case 6:
                result = 'saturday';
                break;

            default:
                throw new Error('Invalid weekday Id provided!', weekDayId);
        }

        return result;
    };

    /**
     * Sort a dictionary where keys are weekdays
     *
     * @param {Object} weekDictionary A dictionary with weekdays as keys.
     * @param {Number} startDayId Id of the first day to start sorting (From 0 for sunday to 6 for saturday).

     * @return {Object} Returns a sorted dictionary
     */
    exports.sortWeekDictionary = function (weekDictionary, startDayId) {
        var sortedWeekDictionary = {};

        for (var i = startDayId; i < startDayId + 7; i++) {
            var weekdayName = GeneralFunctions.getWeekdayName(i % 7);
            sortedWeekDictionary[weekdayName] = weekDictionary[weekdayName];
        }

        return sortedWeekDictionary;
    };

    /**
     * Render a map icon that links to Google maps.
     *
     * @param {Object} user Should have the address, city, etc properties.
     *
     * @return {string} The rendered HTML.
     */
    exports.renderMapIcon = function (user) {
        var data = [];

        if (user.address) {
            data.push(user.address);
        }

        if (user.city) {
            data.push(user.city);
        }

        if (user.state) {
            data.push(user.state);
        }

        if (user.zip_code) {
            data.push(user.zip_code);
        }

        if (!data.length) {
            return '';
        }

        return $('<div/>', {
            'html': [
                $('<a/>', {
                    'href': 'https://www.google.com/maps/place/' + data.join(','),
                    'target': '_blank',
                    'html': [
                        $('<span/>', {
                            'class': 'fas fa-map-marker-alt'
                        })
                    ]
                })
            ]
        })
            .html();
    };

    /**
     * Render a mail icon.
     *
     * @param {String} email
     *
     * @return {string} The rendered HTML.
     */
    exports.renderMailIcon = function (email) {
        return $('<div/>', {
            'html': [
                $('<a/>', {
                    'href': 'mailto:' + email,
                    'target': '_blank',
                    'html': [
                        $('<span/>', {
                            'class': 'fas fa-envelope'
                        })
                    ]
                })
            ]
        })
            .html();
    };

    /**
     * Render a phone icon.
     *
     * @param {String} phone
     *
     * @return {string} The rendered HTML.
     */
    exports.renderPhoneIcon = function (phone) {
        return $('<div/>', {
            'html': [
                $('<a/>', {
                    'href': 'tel:' + phone,
                    'target': '_blank',
                    'html': [
                        $('<span/>', {
                            'class': 'fas fa-phone-alt'
                        })
                    ]
                })
            ]
        })
            .html();
    };

    /**
     * Format a given date according to ISO 8601 date format string yyyy-mm-dd
     *
     * @param {String} date The date to be formatted.
     * @param {String} dateFormatSetting The setting provided by PHP must be one of the "DMY", "MDY" or "YMD".
     *
     * @return {String} Returns the formatted date string.
     */
    exports.ISO8601DateString = function (date, dateFormatSetting) {
        var dayArray;

        // It's necessary to manually parse the date because Date.parse() not support some formats tha instead are
        // supported by Easy!Appointments. The unsupported format is dd/MM/yyyy.
        switch (dateFormatSetting) {
            case 'DMY':
                dayArray = date.split('/');
                date = dayArray[2] + '-' + dayArray[1] + '-' + dayArray[0];
                break;
            case 'MDY':
                dayArray = date.split('/');
                date = dayArray[2] + '-' + dayArray[0] + '-' + dayArray[1];
                break;
            case 'YMD':
                date = date.replace('/', '-');
                break;
            default:
                throw new Error('Invalid date format setting provided:' + dateFormatSetting);
        }
        return date;
    }

})(window.GeneralFunctions);
