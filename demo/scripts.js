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
    exports.validateEmail = function(email) {
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
    exports.ucaseFirstLetter = function(str) {
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
            return;

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

        switch (dateFormatSetting) {
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

window.FrontendBookApi = window.FrontendBookApi || {};

/**
 * Frontend Book API
 *
 * This module serves as the API consumer for the booking wizard of the app.
 *
 * @module FrontendBookApi
 */
(function(exports) {

    'use strict';

    var unavailableDatesBackup;
    var selectedDateStringBackup;
    var processingUnavailabilities = false;

    /**
     * Get Available Hours
     *
     * This function makes an AJAX call and returns the available hours for the selected service,
     * provider and date.
     *
     * @param {String} selDate The selected date of which the available hours we need to receive.
     */
    exports.getAvailableHours = function(selDate) {
        $('#available-hours').empty();

        // Find the selected service duration (it is going to be send within the "postData" object).
        var selServiceDuration = 15; // Default value of duration (in minutes).
        $.each(GlobalVariables.availableServices, function(index, service) {
            if (service['id'] == $('#select-service').val()) {
                selServiceDuration = service['duration'];
            }
        });

        // If the manage mode is true then the appointment's start date should return as available too.
        var appointmentId = FrontendBook.manageMode ? GlobalVariables.appointmentData['id'] : undefined;

        var response = [
            "09:00",
            "10:00",
            "10:15",
            "10:30",
            "11:30",
            "11:45",
            "12:00",
            "12:15",
            "12:30",
            "12:45",
            "13:00",
            "15:30",
            "15:45",
            "16:00",
            "16:15",
            "16:30",
            "16:45",
            "17:00",
            "17:15",
            "17:30"
        ];

        var currColumn = 1;
        $('#available-hours').html('<div style="width:50px; float:left;"></div>');

        $.each(response, function(index, availableHour) {
            if ((currColumn * 10) < (index + 1)) {
                currColumn++;
                $('#available-hours').append('<div style="width:50px; float:left;"></div>');
            }

            $('#available-hours div:eq(' + (currColumn - 1) + ')').append(
                '<span class="available-hour">' + availableHour + '</span><br/>');
        });

        if (FrontendBook.manageMode) {
            // Set the appointment's start time as the default selection.
            $('.available-hour').removeClass('selected-hour');
            $('.available-hour').filter(function() {
                return $(this).text() === Date.parseExact(
                        GlobalVariables.appointmentData['start_datetime'],
                        'yyyy-MM-dd HH:mm:ss').toString('HH:mm');
            }).addClass('selected-hour');
        } else {
            // Set the first available hour as the default selection.
            $('.available-hour:eq(0)').addClass('selected-hour');
        }

        FrontendBook.updateConfirmFrame();


    };

    /**
     * Register an appointment to the database.
     *
     * This method will make an ajax call to the appointments controller that will register
     * the appointment to the database.
     */
    exports.registerAppointment = function() {
        location.href = 'success.html';
    };

    /**
     * Get the unavailable dates of a provider.
     *
     * This method will fetch the unavailable dates of the selected provider and service and then it will
     * select the first available date (if any). It uses the "FrontendBookApi.getAvailableHours" method to
     * fetch the appointment* hours of the selected date.
     *
     * @param {Number} providerId The selected provider ID.
     * @param {Number} serviceId The selected service ID.
     * @param {String} selectedDateString Y-m-d value of the selected date.
     */
    exports.getUnavailableDates = function(providerId, serviceId, selectedDateString) {
        return;

        if (processingUnavailabilities) {
            return;
        }

        var url = 'ajax/getAvailableHours.json';
        var data = {
            provider_id: providerId,
            service_id: serviceId,
            selected_date: encodeURIComponent(selectedDateString),
            csrfToken: GlobalVariables.csrfToken
        };

        $.ajax({
            url: url,
            type: 'GET',
            data: data,
            dataType: 'json'
        })
            .done(function(response) {
                unavailableDatesBackup = response;
                selectedDateStringBackup = selectedDateString;
                _applyUnavailableDates(response, selectedDateString, true);
            })
            .fail(GeneralFunctions.ajaxFailureHandler);
    };

    exports.applyPreviousUnavailableDates = function() {
        _applyUnavailableDates(unavailableDatesBackup, selectedDateStringBackup);
    };

    function _applyUnavailableDates(unavailableDates, selectedDateString, setDate) {
        return;

        setDate = setDate || false;

        processingUnavailabilities = true;

        // Select first enabled date.
        var selectedDate = Date.parse(selectedDateString);
        var numberOfDays = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + 1, 0).getDate();

        if (setDate) {
            for (var i = 1; i <= numberOfDays; i++) {
                var currentDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), i);
                if (unavailableDates.indexOf(currentDate.toString('yyyy-MM-dd')) === -1) {
                    $('#select-date').datepicker('setDate', currentDate);
                    FrontendBookApi.getAvailableHours(currentDate.toString('yyyy-MM-dd'));
                    break;
                }
            }
        }

        // If all the days are unavailable then hide the appointments hours.
        if (unavailableDates.length === numberOfDays) {
            $('#available-hours').text(EALang['no_available_hours']);
        }

        // Grey out unavailable dates.
        $('#select-date .ui-datepicker-calendar td:not(.ui-datepicker-other-month)').each(function(index, td) {
            selectedDate.set({day: index + 1});
            if ($.inArray(selectedDate.toString('yyyy-MM-dd'), unavailableDates) != -1) {
                $(td).addClass('ui-datepicker-unselectable ui-state-disabled');
            }
        });

        processingUnavailabilities = false;
    }

})(window.FrontendBookApi);

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

window.FrontendBook = window.FrontendBook || {};

/**
 * Frontend Book
 *
 * This module contains functions that implement the book appointment page functionality. Once the
 * initialize() method is called the page is fully functional and can serve the appointment booking
 * process.
 *
 * @module FrontendBook
 */
(function(exports) {

    'use strict';

    /**
     * Determines the functionality of the page.
     *
     * @type {Boolean}
     */
    exports.manageMode = false;

    /**
     * This method initializes the book appointment page.
     *
     * @param {Boolean} bindEventHandlers (OPTIONAL) Determines whether the default
     * event handlers will be bound to the dom elements.
     * @param {Boolean} manageMode (OPTIONAL) Determines whether the customer is going
     * to make  changes to an existing appointment rather than booking a new one.
     */
    exports.initialize = function(bindEventHandlers, manageMode) {
        bindEventHandlers = bindEventHandlers || true;
        manageMode = manageMode || false;

        if (window.console === undefined) {
            window.console = function() {
            }; // IE compatibility
        }

        FrontendBook.manageMode = manageMode;

        // Initialize page's components (tooltips, datepickers etc).
        $('.book-step').qtip({
            position: {
                my: 'top center',
                at: 'bottom center'
            },
            style: {
                classes: 'qtip-green qtip-shadow custom-qtip'
            }
        });

        $('#select-date').datepicker({
            dateFormat: 'dd-mm-yy',
            firstDay: 1, // Monday
            minDate: 0,
            defaultDate: Date.today(),

            dayNames: [
                EALang['sunday'], EALang['monday'], EALang['tuesday'], EALang['wednesday'],
                EALang['thursday'], EALang['friday'], EALang['saturday']],
            dayNamesShort: [EALang['sunday'].substr(0, 3), EALang['monday'].substr(0, 3),
                EALang['tuesday'].substr(0, 3), EALang['wednesday'].substr(0, 3),
                EALang['thursday'].substr(0, 3), EALang['friday'].substr(0, 3),
                EALang['saturday'].substr(0, 3)],
            dayNamesMin: [EALang['sunday'].substr(0, 2), EALang['monday'].substr(0, 2),
                EALang['tuesday'].substr(0, 2), EALang['wednesday'].substr(0, 2),
                EALang['thursday'].substr(0, 2), EALang['friday'].substr(0, 2),
                EALang['saturday'].substr(0, 2)],
            monthNames: [EALang['january'], EALang['february'], EALang['march'], EALang['april'],
                EALang['may'], EALang['june'], EALang['july'], EALang['august'], EALang['september'],
                EALang['october'], EALang['november'], EALang['december']],
            prevText: EALang['previous'],
            nextText: EALang['next'],
            currentText: EALang['now'],
            closeText: EALang['close'],

            onSelect: function(dateText, instance) {
                FrontendBookApi.getAvailableHours(dateText);
                FrontendBook.updateConfirmFrame();
            },

            onChangeMonthYear: function(year, month, instance) {
                var currentDate = new Date(year, month - 1, 1);
                FrontendBookApi.getUnavailableDates($('#select-provider').val(), $('#select-service').val(),
                    currentDate.toString('yyyy-MM-dd'));
            }
        });

        // Bind the event handlers (might not be necessary every time we use this class).
        if (bindEventHandlers) {
            _bindEventHandlers();
        }

        // If the manage mode is true, the appointments data should be loaded by default.
        if (FrontendBook.manageMode) {
            _applyAppointmentData(GlobalVariables.appointmentData,
                GlobalVariables.providerData, GlobalVariables.customerData);
        } else {
            var $selectProvider = $('#select-provider');
            var $selectService = $('#select-service');

            // Check if a specific service was selected (via URL parameter).
            var selectedServiceId = GeneralFunctions.getUrlParameter(location.href, 'service');

            if (selectedServiceId && $selectService.find('option[value="' + selectedServiceId + '"]').length > 0) {
                $selectService
                    .val(selectedServiceId)
                    .prop('disabled', true)
                    .css('opacity', '0.5');
            }

            $selectService.trigger('change'); // Load the available hours.

            // Check if a specific provider was selected.
            var selectedProviderId = GeneralFunctions.getUrlParameter(location.href, 'provider');

            if (selectedProviderId && $selectProvider.find('option[value="' + selectedProviderId + '"]').length === 0) {
                // Select a service of this provider in order to make the provider available in the select box.
                for (var index in GlobalVariables.availableProviders) {
                    var provider = GlobalVariables.availableProviders[index];

                    if (provider.id === selectedProviderId && provider.services.length > 0) {
                        $selectService
                            .val(provider.services[0])
                            .trigger('change');
                    }
                }
            }

            if (selectedProviderId && $selectProvider.find('option[value="' + selectedProviderId + '"]').length > 0) {
                $selectProvider
                    .val(selectedProviderId)
                    .prop('disabled', true)
                    .css('opacity', '0.5')
                    .trigger('change');
            }

        }

        FrontendBookApi.getAvailableHours($('#select-date').datepicker('getDate').toString('yyyy-MM-dd'));
    };

    /**
     * This method binds the necessary event handlers for the book appointments page.
     */
    function _bindEventHandlers() {
        /**
         * Event: Selected Provider "Changed"
         *
         * Whenever the provider changes the available appointment date - time periods must be updated.
         */
        $('#select-provider').change(function() {
            FrontendBookApi.getUnavailableDates($(this).val(), $('#select-service').val(),
                $('#select-date').datepicker('getDate').toString('yyyy-MM-dd'));
            FrontendBook.updateConfirmFrame();
        });

        /**
         * Event: Selected Service "Changed"
         *
         * When the user clicks on a service, its available providers should
         * become visible.
         */
        $('#select-service').change(function() {
            var currServiceId = $('#select-service').val();
            $('#select-provider').empty();

            $.each(GlobalVariables.availableProviders, function(indexProvider, provider) {
                $.each(provider['services'], function(indexService, serviceId) {
                    // If the current provider is able to provide the selected service,
                    // add him to the listbox.
                    if (serviceId == currServiceId) {
                        var optionHtml = '<option value="' + provider['id'] + '">'
                            + provider['first_name'] + ' ' + provider['last_name']
                            + '</option>';
                        $('#select-provider').append(optionHtml);
                    }
                });
            });

            // Add the "Any Provider" entry.
            if ($('#select-provider option').length >= 1) {
                $('#select-provider').append(new Option('- ' + EALang['any_provider'] + ' -', 'any-provider'));
            }

            FrontendBookApi.getUnavailableDates($('#select-provider').val(), $(this).val(),
                $('#select-date').datepicker('getDate').toString('yyyy-MM-dd'));
            FrontendBook.updateConfirmFrame();
            _updateServiceDescription($('#select-service').val(), $('#service-description'));
        });

        /**
         * Event: Next Step Button "Clicked"
         *
         * This handler is triggered every time the user pressed the "next" button on the book wizard.
         * Some special tasks might be performed, depending the current wizard step.
         */
        $('.button-next').click(function() {
            // If we are on the first step and there is not provider selected do not continue
            // with the next step.
            if ($(this).attr('data-step_index') === '1' && $('#select-provider').val() == null) {
                return;
            }

            // If we are on the 2nd tab then the user should have an appointment hour
            // selected.
            if ($(this).attr('data-step_index') === '2') {
                if ($('.selected-hour').length == 0) {
                    if ($('#select-hour-prompt').length == 0) {
                        $('#available-hours').append('<br><br>'
                            + '<span id="select-hour-prompt" class="text-danger">'
                            + EALang['appointment_hour_missing']
                            + '</span>');
                    }
                    return;
                }
            }

            // If we are on the 3rd tab then we will need to validate the user's
            // input before proceeding to the next step.
            if ($(this).attr('data-step_index') === '3') {
                if (!_validateCustomerForm()) {
                    return; // Validation failed, do not continue.
                } else {
                    FrontendBook.updateConfirmFrame();
                }
            }

            // Display the next step tab (uses jquery animation effect).
            var nextTabIndex = parseInt($(this).attr('data-step_index')) + 1;

            $(this).parents().eq(1).hide('fade', function() {
                $('.active-step').removeClass('active-step');
                $('#step-' + nextTabIndex).addClass('active-step');
                $('#wizard-frame-' + nextTabIndex).show('fade');
            });
        });

        /**
         * Event: Back Step Button "Clicked"
         *
         * This handler is triggered every time the user pressed the "back" button on the
         * book wizard.
         */
        $('.button-back').click(function() {
            var prevTabIndex = parseInt($(this).attr('data-step_index')) - 1;

            $(this).parents().eq(1).hide('fade', function() {
                $('.active-step').removeClass('active-step');
                $('#step-' + prevTabIndex).addClass('active-step');
                $('#wizard-frame-' + prevTabIndex).show('fade');
            });
        });

        /**
         * Event: Available Hour "Click"
         *
         * Triggered whenever the user clicks on an available hour
         * for his appointment.
         */
        $('#available-hours').on('click', '.available-hour', function() {
            $('.selected-hour').removeClass('selected-hour');
            $(this).addClass('selected-hour');
            FrontendBook.updateConfirmFrame();
        });

        if (FrontendBook.manageMode) {
            /**
             * Event: Cancel Appointment Button "Click"
             *
             * When the user clicks the "Cancel" button this form is going to be submitted. We need
             * the user to confirm this action because once the appointment is cancelled, it will be
             * delete from the database.
             *
             * @param {jQuery.Event} event
             */
            $('#cancel-appointment').click(function(event) {
                var dialogButtons = {};
                dialogButtons['OK'] = function() {
                    if ($('#cancel-reason').val() === '') {
                        $('#cancel-reason').css('border', '2px solid red');
                        return;
                    }
                    $('#cancel-appointment-form textarea').val($('#cancel-reason').val());
                    $('#cancel-appointment-form').submit();
                };

                dialogButtons[EALang['cancel']] = function() {
                    $('#message_box').dialog('close');
                };

                GeneralFunctions.displayMessageBox(EALang['cancel_appointment_title'],
                    EALang['write_appointment_removal_reason'], dialogButtons);

                $('#message_box').append('<textarea id="cancel-reason" rows="3"></textarea>');
                $('#cancel-reason').css('width', '100%');
                return false;
            });
        }

        /**
         * Event: Book Appointment Form "Submit"
         *
         * Before the form is submitted to the server we need to make sure that
         * in the meantime the selected appointment date/time wasn't reserved by
         * another customer or event.
         *
         * @param {jQuery.Event} event
         */
        $('#book-appointment-submit').click(function(event) {
            FrontendBookApi.registerAppointment();
        });

        /**
         * Event: Refresh captcha image.
         *
         * @param {jQuery.Event} event
         */
        $('.captcha-title small').click(function(event) {
            $('.captcha-image').attr('src', GlobalVariables.baseUrl + '/index.php/captcha?' + Date.now());
        });


        $('#select-date').on('mousedown', '.ui-datepicker-calendar td', function(event) {
            setTimeout(function() {
                FrontendBookApi.applyPreviousUnavailableDates(); // New jQuery UI version will replace the td elements.
            }, 300); // There is no draw event unfortunately.
        })
    };

    /**
     * This function validates the customer's data input. The user cannot continue
     * without passing all the validation checks.
     *
     * @return {Boolean} Returns the validation result.
     */
    function _validateCustomerForm() {
        $('#wizard-frame-3 input').css('border', '');

        try {
            // Validate required fields.
            var missingRequiredField = false;
            $('.required').each(function() {
                if ($(this).val() == '') {
                    $(this).parents('.form-group').addClass('has-error');
                    // $(this).css('border', '2px solid red');
                    missingRequiredField = true;
                }
            });
            if (missingRequiredField) {
                throw EALang['fields_are_required'];
            }

            // Validate email address.
            if (!GeneralFunctions.validateEmail($('#email').val())) {
                $('#email').parents('.form-group').addClass('has-error');
                // $('#email').css('border', '2px solid red');
                throw EALang['invalid_email'];
            }

            return true;
        } catch (exc) {
            $('#form-message').text(exc);
            return false;
        }
    }

    /**
     * Every time this function is executed, it updates the confirmation page with the latest
     * customer settings and input for the appointment booking.
     */
    exports.updateConfirmFrame = function() {
        // Appointment Details
        var selectedDate = $('#select-date').datepicker('getDate');

        if (selectedDate !== null) {
            selectedDate = GeneralFunctions.formatDate(selectedDate, GlobalVariables.dateFormat);
        }

        var selServiceId = $('#select-service').val();
        var servicePrice;
        var serviceCurrency;

        $.each(GlobalVariables.availableServices, function(index, service) {
            if (service.id == selServiceId) {
                servicePrice = '<br>' + service.price;
                serviceCurrency = service.currency;
                return false; // break loop
            }
        });

        var html =
            '<h4>' + $('#select-service option:selected').text() + '</h4>' +
            '<p>'
            + '<strong class="text-primary">'
            + $('#select-provider option:selected').text() + '<br>'
            + selectedDate + ' ' + $('.selected-hour').text()
            + servicePrice + ' ' + serviceCurrency
            + '</strong>' +
            '</p>';

        $('#appointment-details').html(html);

        // Customer Details
        var firstName = GeneralFunctions.escapeHtml($('#first-name').val());
        var lastName = GeneralFunctions.escapeHtml($('#last-name').val());
        var phoneNumber = GeneralFunctions.escapeHtml($('#phone-number').val());
        var email = GeneralFunctions.escapeHtml($('#email').val());
        var address = GeneralFunctions.escapeHtml($('#address').val());
        var city = GeneralFunctions.escapeHtml($('#city').val());
        var zipCode = GeneralFunctions.escapeHtml($('#zip-code').val());

        html =
            '<h4>' + firstName + ' ' + lastName + '</h4>' +
            '<p>' +
            EALang['phone'] + ': ' + phoneNumber +
            '<br/>' +
            EALang['email'] + ': ' + email +
            '<br/>' +
            EALang['address'] + ': ' + address +
            '<br/>' +
            EALang['city'] + ': ' + city +
            '<br/>' +
            EALang['zip_code'] + ': ' + zipCode +
            '</p>';

        $('#customer-details').html(html);

        // Update appointment form data for submission to server when the user confirms
        // the appointment.
        var postData = {};

        postData['customer'] = {
            last_name: $('#last-name').val(),
            first_name: $('#first-name').val(),
            email: $('#email').val(),
            phone_number: $('#phone-number').val(),
            address: $('#address').val(),
            city: $('#city').val(),
            zip_code: $('#zip-code').val()
        };

        postData['appointment'] = {
            start_datetime: $('#select-date').datepicker('getDate').toString('yyyy-MM-dd')
            + ' ' + $('.selected-hour').text() + ':00',
            end_datetime: _calcEndDatetime(),
            notes: $('#notes').val(),
            is_unavailable: false,
            id_users_provider: $('#select-provider').val(),
            id_services: $('#select-service').val()
        };

        postData['manage_mode'] = FrontendBook.manageMode;

        if (FrontendBook.manageMode) {
            postData['appointment']['id'] = GlobalVariables.appointmentData['id'];
            postData['customer']['id'] = GlobalVariables.customerData['id'];
        }
        $('input[name="csrfToken"]').val(GlobalVariables.csrfToken);
        $('input[name="post_data"]').val(JSON.stringify(postData));
    };

    /**
     * This method calculates the end datetime of the current appointment.
     * End datetime is depending on the service and start datetime fields.
     *
     * @return {String} Returns the end datetime in string format.
     */
    function _calcEndDatetime() {
        // Find selected service duration.
        var selServiceDuration = undefined;

        $.each(GlobalVariables.availableServices, function(index, service) {
            if (service.id == $('#select-service').val()) {
                selServiceDuration = service.duration;
                return false; // Stop searching ...
            }
        });

        // Add the duration to the start datetime.
        var startDatetime = $('#select-date').datepicker('getDate').toString('dd-MM-yyyy')
            + ' ' + $('.selected-hour').text();
        startDatetime = Date.parseExact(startDatetime, 'dd-MM-yyyy HH:mm');
        var endDatetime = undefined;

        if (selServiceDuration !== undefined && startDatetime !== null) {
            endDatetime = startDatetime.add({'minutes': parseInt(selServiceDuration)});
        } else {
            endDatetime = new Date();
        }

        return endDatetime.toString('yyyy-MM-dd HH:mm:ss');
    }

    /**
     * This method applies the appointment's data to the wizard so
     * that the user can start making changes on an existing record.
     *
     * @param {Object} appointment Selected appointment's data.
     * @param {Object} provider Selected provider's data.
     * @param {Object} customer Selected customer's data.
     *
     * @return {Boolean} Returns the operation result.
     */
    function _applyAppointmentData(appointment, provider, customer) {
        try {
            // Select Service & Provider
            $('#select-service').val(appointment['id_services']).trigger('change');
            $('#select-provider').val(appointment['id_users_provider']);

            // Set Appointment Date
            $('#select-date').datepicker('setDate',
                Date.parseExact(appointment['start_datetime'], 'yyyy-MM-dd HH:mm:ss'));
            FrontendBookApi.getAvailableHours($('#select-date').val());

            // Apply Customer's Data
            $('#last-name').val(customer['last_name']);
            $('#first-name').val(customer['first_name']);
            $('#email').val(customer['email']);
            $('#phone-number').val(customer['phone_number']);
            $('#address').val(customer['address']);
            $('#city').val(customer['city']);
            $('#zip-code').val(customer['zip_code']);
            var appointmentNotes = (appointment['notes'] !== null)
                ? appointment['notes'] : '';
            $('#notes').val(appointmentNotes);

            FrontendBook.updateConfirmFrame();

            return true;
        } catch (exc) {
            return false;
        }
    }

    /**
     * This method updates a div's html content with a brief description of the
     * user selected service (only if available in db). This is useful for the
     * customers upon selecting the correct service.
     *
     * @param {Number} serviceId The selected service record id.
     * @param {Object} $div The destination div jquery object (e.g. provide $('#div-id')
     * object as value).
     */
    function _updateServiceDescription(serviceId, $div) {
        var html = '';

        $.each(GlobalVariables.availableServices, function(index, service) {
            if (service.id == serviceId) { // Just found the service.
                html = '<strong>' + service.name + ' </strong>';

                if (service.description != '' && service.description != null) {
                    html += '<br>' + service.description + '<br>';
                }

                if (service.duration != '' && service.duration != null) {
                    html += '[' + EALang['duration'] + ' ' + service.duration
                        + ' ' + EALang['minutes'] + '] ';
                }

                if (service.price != '' && service.price != null) {
                    html += '[' + EALang['price'] + ' ' + service.price + ' ' + service.currency + ']';
                }

                html += '<br>';

                return false;
            }
        });

        $div.html(html);

        if (html != '') {
            $div.show();
        } else {
            $div.hide();
        }
    }

})(window.FrontendBook);



