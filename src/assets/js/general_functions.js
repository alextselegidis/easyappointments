/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
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
(function(exports) {

    'use strict';

    /**
     * General Functions Constants
     */
    exports.EXCEPTIONS_TITLE = EALang['unexpected_issues'];
    exports.EXCEPTIONS_MESSAGE = EALang['unexpected_issues_message'];
    exports.WARNINGS_TITLE = EALang['unexpected_warnings'];
    exports.WARNINGS_MESSAGE = EALang['unexpected_warnings_message'];

    /**
     * This functions displays a message box in the admin array. It is useful when user
     * decisions or verifications are needed.
     *
     * @param {String} title The title of the message box.
     * @param {String} message The message of the dialog.
     * @param {Array} messageButtons Contains the dialog buttons along with their functions.
     */
    exports.displayMessageBox = function(title, message, messageButtons) {
        // Check arguments integrity.
        if (title == undefined || title == '') {
            title = '<No Title Given>';
        }

        if (message == undefined || message == '') {
            message = '<No Message Given>';
        }

        if (messageButtons == undefined) {
            messageButtons = {};
            messageButtons[EALang['close']] = function() {
                $('#message_box').dialog('close');
            };
        }

        // Destroy previous dialog instances.
        $('#message_box').dialog('destroy');
        $('#message_box').remove();

        // Create the html of the message box.
        $('body').append(
            '<div id="message_box" title="' + title + '">' +
            '<p>' + message + '</p>' +
            '</div>'
        );

        $("#message_box").dialog({
            autoOpen: false,
            modal: true,
            resize: 'auto',
            width: 'auto',
            height: 'auto',
            resizable: false,
            buttons: messageButtons,
            closeOnEscape: true
        });

        $('#message_box').dialog('open');
        $('.ui-dialog .ui-dialog-buttonset button').addClass('btn btn-default');
        $('#message_box .ui-dialog-titlebar-close').hide();
    };

    /**
     * This method centers a DOM element vertically and horizontally on the page.
     *
     * @param {Object} elementHandle The object that is going to be centered.
     */
    exports.centerElementOnPage = function(elementHandle) {
        // Center main frame vertical middle
        $(window).resize(function() {
            var elementLeft = ($(window).width() - elementHandle.outerWidth()) / 2;
            var elementTop = ($(window).height() - elementHandle.outerHeight()) / 2;
            elementTop = (elementTop > 0 ) ? elementTop : 20;

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
     * {@link http://www.netlobo.com/url_query_string_javascript.html}
     *
     * @param {String} url The selected url.
     * @param {String} name The parameter name.

     * @return {String} Returns the parameter value.
     */
    exports.getUrlParameter = function(url, parameterName) {
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
    exports.ISODateString = function(date) {
        function pad(n) {
            return n < 10 ? '0' + n : n;
        }

        return date.getUTCFullYear()+'-'
             + pad(date.getUTCMonth()+1)+'-'
             + pad(date.getUTCDate())+'T'
             + pad(date.getUTCHours())+':'
             + pad(date.getUTCMinutes())+':'
             + pad(date.getUTCSeconds())+'Z';
    };

    /**
     * Clone JS Object
     *
     * This method creates and returns an exact copy of the provided object. It is very useful whenever
     * changes need to be made to an object without modifying the original data.
     *
     * {@link http://stackoverflow.com/questions/728360/most-elegant-way-to-clone-a-javascript-object}
     *
     * @param {Object} originalObject Object to be copied.

     * @return {Object} Returns an exact copy of the provided element.
     */
    exports.clone = function(originalObject) {
        // Handle the 3 simple types, and null or undefined
        if (null == originalObject || 'object' != typeof originalObject)
            return originalObject;

        // Handle Date
        if (originalObject instanceof Date) {
            var copy = new Date();
            copy.setTime(originalObject.getTime());
            return copy;
        }

        // Handle Array
        if (originalObject instanceof Array) {
            var copy = [];
            for (var i = 0, len = originalObject.length; i < len; i++) {
                copy[i] = GeneralFunctions.clone(originalObject[i]);
            }
            return copy;
        }

        // Handle Object
        if (originalObject instanceof Object) {
            var copy = {};
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
     * {@link http://badsyntax.co/post/javascript-email-validation-rfc822}
     *
     * @param {String} email The email address to be checked.

     * @return {Boolean} Returns the validation result.
     */
    exports.validateEmail = function (email) {
        var re = /^([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x22([^\x0d\x22\x5c\x80-\xff]|\x5c[\x00-\x7f])*\x22))*\x40([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d)(\x2e([^\x00-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f-\xff]+|\x5b([^\x0d\x5b-\x5d\x80-\xff]|\x5c[\x00-\x7f])*\x5d))*$/;
        return re.test(email);
    };

    /**
     * Convert AJAX exceptions to HTML.
     *
     * This method returns the exception HTML display for javascript ajax calls. It uses the Bootstrap collapse
     * module to show exception messages when the user opens the "Details" collapse component.
     *
     * @param {Array} exceptions Contains the exceptions to be displayed.
     *
     * @return {String} Returns the html markup for the exceptions.
     */
    exports.exceptionsToHtml = function(exceptions) {
        var html =
                '<div class="accordion" id="error-accordion">' +
                    '<div class="accordion-group">' +
                        '<div class="accordion-heading">' +
                            '<a class="accordion-toggle" data-toggle="collapse" ' +
                                    'data-parent="#error-accordion" href="#error-technical">' +
                                EALang['details'] +
                            '</a>' +
                        '</div>';

        $.each(exceptions, function(index, exception) {
            html +=
                    '<div id="error-technical" class="accordion-body collapse">' +
                        '<div class="accordion-inner">' +
                            '<pre>' + exception['message'] + '</pre>' +
                        '</div>' +
                    '</div>';
        });

        html += '</div></div>';

        return html;
    };

    /**
     * Parse AJAX Exceptions
     *
     * This method parse the JSON encoded strings that are fetched by AJAX calls.
     *
     * @param {Array} exceptions Exception array returned by an ajax call.
     *
     * @return {Array} Returns the parsed js objects.
     */
    exports.parseExceptions = function(exceptions) {
        var parsedExceptions = new Array();

        $.each(exceptions, function(index, exception) {
            parsedExceptions.push($.parseJSON(exception));
        });

        return parsedExceptions;
    };

    /**
     * Makes the first letter of the string upper case.
     *
     * @param {String} str The string to be converted.
     *
     * @return {String} Returns the capitalized string.
     */
    exports.ucaseFirstLetter = function(str){
        return str.charAt(0).toUpperCase() + str.slice(1);
    };

    /**
     * Handle AJAX Exceptions Callback
     *
     * All backend js code has the same way of dislaying exceptions that are raised on the
     * server during an ajax call.
     *
     * @param {Object} response Contains the server response. If exceptions or warnings are
     * found, user friendly messages are going to be displayed to the user.4
     *
     * @return {Boolean} Returns whether the the ajax callback should continue the execution or
     * stop, due to critical server exceptions.
     */
    exports.handleAjaxExceptions = function(response) {
        if (response.exceptions) {
            response.exceptions = GeneralFunctions.parseExceptions(response.exceptions);
            GeneralFunctions.displayMessageBox(GeneralFunctions.EXCEPTIONS_TITLE, GeneralFunctions.EXCEPTIONS_MESSAGE);
            $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.exceptions));
            return false;
        }

        if (response.warnings) {
            response.warnings = GeneralFunctions.parseExceptions(response.warnings);
            GeneralFunctions.displayMessageBox(GeneralFunctions.WARNINGS_TITLE, GeneralFunctions.WARNINGS_MESSAGE);
            $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.warnings));
        }

        return true;
    };

    /**
     * Enable Language Selection
     *
     * Enables the language selection functionality. Must be called on every page has a
     * language selection button. This method requires the global variable 'availableLanguages'
     * to be initialized before the execution.
     *
     * @param {Object} $element Selected element button for the language selection.
     */
    exports.enableLanguageSelection = function($element) {
    	// Select Language
        var html = '<ul id="language-list">';
        $.each(availableLanguages, function() {
        	html += '<li class="language" data-language="' + this + '">'
        			+ GeneralFunctions.ucaseFirstLetter(this) + '</li>';
        });
        html += '</ul>';

        $element.popover({
            placement: 'top',
            title: 'Select Language',
            content: html,
            html: true,
            container: 'body',
            trigger: 'manual'
        });

        $element.click(function() {
        	if ($('#language-list').length === 0) {
        		$(this).popover('show');
        	} else {
        		$(this).popover('hide');
        	}

            $(this).toggleClass('active');
        });

        $(document).on('click', 'li.language', function() {
        	// Change language with ajax call and refresh page.
        	var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_change_language';
            var postData = {
                csrfToken: GlobalVariables.csrfToken,
                language: $(this).attr('data-language'),
            };
        	$.post(postUrl, postData, function(response) {
        		if (!GeneralFunctions.handleAjaxExceptions(response)) {
                    return;
                }
        		document.location.reload(true);

        	}, 'json').fail(GeneralFunctions.ajaxFailureHandler);
        });
    };

    /**
     * AJAX Failure Handler
     *
     * @param {jqXHR} jqxhr
     * @param {String} textStatus
     * @param {Object} errorThrown
     */
    exports.ajaxFailureHandler = function(jqxhr, textStatus, errorThrown) {
        var exceptions = [
            {
                message: 'AJAX Error: ' + errorThrown
            }
        ];
        GeneralFunctions.displayMessageBox(GeneralFunctions.EXCEPTIONS_TITLE, GeneralFunctions.EXCEPTIONS_MESSAGE);
        $('#message_box').append(GeneralFunctions.exceptionsToHtml(exceptions));
    };

    /**
     * Escape JS HTML string values for XSS prevention.
     *
     * @param {String} str String to be escaped.
     *
     * @return {String} Returns the escaped string.
     */
    exports.escapeHtml = function(str) {
        return $('<div/>').text(str).html();
    };

    /**
     * Format a given date according to the date format setting.
     *
     * @param {Date} date The date to be formatted.
     * @param {String} dateFormatSetting The setting provided by PHP must be one of
     * the "DMY", "MDY" or "YMD".
     * @param {Boolean} addHours (optional) Whether to add hours to the result.

     * @return {String} Returns the formatted date string.
     */
    exports.formatDate = function(date, dateFormatSetting, addHours) {
        var format, result;
        var hours = addHours ? ' HH:mm' : '';

        switch(dateFormatSetting) {
            case 'DMY':
                result = Date.parse(date).toString('dd/MM/yyyy' + hours);
                break;
            case 'MDY':
                result = Date.parse(date).toString('MM/dd/yyyy' + hours);
                break;
            case 'YMD':
                result = Date.parse(date).toString('yyyy/MM/dd' + hours);
                break;
            default:
                throw new Error('Invalid date format setting provided!', dateFormatSetting);
        }

        return result;
    };

})(window.GeneralFunctions);
