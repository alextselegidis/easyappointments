/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) 2013 - 2016, Alex Tselegidis
 * @license     http://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        http://easyappointments.org
 * @since       v1.2.0
 * ---------------------------------------------------------------------------- */

/**
 * Backend Calendar
 *
 * This module implements the table calendar view of backend.
 *
 * @module BackendCalendarTableView
 */
window.BackendCalendarTableView = window.BackendCalendarTableView || {};

(function(exports) {

    'use strict';

    function _bindEventHandlers() {
        var $calendarToolbar = $('#calendar-toolbar'); 
        var $calendar = $('#calendar'); 

        $calendar.on('click', '.calendar-header .btn.previous', function() {
            var endDate = new Date($('#calendar .date-column:first').data('date')).add({days: -1}); 
            var startDate = new Date(endDate.getTime()).add({days: -1 * (parseInt($('#select-filter-item').val()) - 1)});
            _createView(startDate, endDate);
        });

        $calendar.on('click', '.calendar-header .btn.next', function() {
            var startDate = new Date($('#calendar .date-column:last').data('date')).add({days: 1}); 
            var endDate = new Date(startDate.getTime()).add({days: parseInt($('#select-filter-item').val()) - 1}); 
            _createView(startDate, endDate);
        });

        $calendarToolbar.on('change', '#select-filter-item', function() {
            var startDate = new Date($('.calendar-view .date-column:first').data('date')); 
            var endDate = new Date(startDate.getTime()).add({days: parseInt($(this).val()) - 1}); 
            _createView(startDate, endDate);
        });

        $calendarToolbar.on('click', '#reload-appointments', function() {
            var startDate = new Date($('.calendar-view .date-column:first').data('date'));
            var endDate = new Date($('.calendar-view .date-column:last').data('date'));
            _createView(startDate, endDate);
        }); 
        
        $calendar.on('click', '.calendar-view table td', function() {
            if ($(this).index() === 0) {
                return; // Clicked on an hour slot. 
            }

            // Open the appointments modal in the selected hour. 
            var hour = $(this).parent().find('td:first').text().split(':'); 
            var date = new Date($(this).parents('.date-column').data('date'));
            date.set({hour: parseInt(hour[0]), minute: parseInt(hour[1])});

            // Open the appointments dialog. 
            $('#insert-appointment').trigger('click');

            // Update start date field.
            $('#start-datetime').datepicker('setDate', date); 

            // Select Service and provider. 
            var $providerColumn = $(this).parents('.provider-column');
            var serviceId = $providerColumn.find('thead tr:last th').eq($(this).index()).data('id');
            var providerId = $providerColumn.data('provider').id; 
            $('#select-service').val(serviceId).trigger('change');
            $('#select-provider').val(providerId).trigger('change');
        }); 
    }

    function _createHeader() {
        var $calendarFilter = $('#calendar-filter'); 

        $calendarFilter
            .find('select')
            .empty()
            .append(new Option('1 Days', 1))
            .append(new Option('4 Days', 4));
        
        var $calendarHeader = $('<div class="calendar-header" />').appendTo('#calendar'); 

        $calendarHeader
            .html(
                '<button class="btn btn-xs btn-default previous">' +
                    '<span class="glyphicon glyphicon-chevron-left"></span>' +
                '</button>' +
                '<button class="btn btn-xs btn-default next">' +
                    '<span class="glyphicon glyphicon-chevron-right"></span>' +
                '</button>'
            );
    }

    /**
     * Create table schedule view. 
     *
     * This method will destroy any previous instances and create a new view for displaying the appointments in 
     * a table format. 
     * 
     * @param {Date} startDate Start date to be displayed.
     * @param {Date} endDate End date to be displayed. 
     */
    function _createView(startDate, endDate) {
        $('#calendar .calendar-view').empty();

        var $calendarView = $('<div class="calendar-view" />').appendTo('#calendar'); 

        $calendarView.data({
            startDate: startDate.toString('yyyy-MM-dd'),
            endDate: endDate.toString('yyyy-MM-dd')
        });

        _getCalendarEvents(startDate, endDate)
            .done(function(response) {
                if (!GeneralFunctions.handleAjaxExceptions(response)) {
                    return;
                }

                var currentDate = startDate; 

                while(currentDate <= endDate) {
                    _createDateColumn($calendarView, currentDate, response); 
                    currentDate.add({days: 1}); 
                }
            })
            .fail(GeneralFunctions.ajaxFailureHandler);


    }

    function _createDateColumn($calendarView, date, events) {
        var $dateColumn = $('<div class="date-column" />').appendTo($calendarView); 

        $dateColumn
            .data('date', date.getTime())
            .append('<h3>' + GeneralFunctions.formatDate(date, GlobalVariables.dateFormat) + '</h3>');

        for (var index in GlobalVariables.availableProviders) {
            var provider = GlobalVariables.availableProviders[index]; 
            _createProviderColumn($dateColumn, date, provider, events);
        }
    }

    function _createProviderColumn($dateColumn, date, provider, events) {
        var $providerColumn = $('<div class="provider-column col-xs-12 col-sm-2" />').appendTo($dateColumn); 

        $providerColumn.data('provider', provider); 

        // Create the table slots. 
        _createSlots($providerColumn, date, provider); 

        // Add the appointments to the column. 
        _createAppointments($providerColumn, events.appointments); 
        
        // Add the unavailabilities to the column. 
        _createUnavailabilities($providerColumn, events.unavailabilities); 
    }

    function _createSlots($providerColumn, date, provider) {
        var day = date.toString('dddd').toLowerCase(); 
        var plan = JSON.parse(provider.settings.working_plan)[day]; 

        if (!plan) {
            $providerColumn.append('<div class="not-working">Not Working</div>');
            return;
        }

        var $table = $('<table class="slots" />')
            .appendTo($providerColumn)
            .addClass('table table-condensed table-responsive table-hover table-striped')
            .html(
                '<thead>' +
                    '<tr>' +
                        '<th colspan="' + (provider.services.length + 1) + '">' + 
                            (provider.first_name + ' ' + provider.last_name).trim() + 
                        '</th>' +
                    '</tr>' + 
                    '<tr>' +
                        '<th>Time</th>' +
                    '</tr>' + 
                '</thead>' +
                '<tbody></tbody>'
            );

        for (var index in GlobalVariables.availableServices) {
            var service = GlobalVariables.availableServices[index]; 

            if (provider.services.indexOf(service.id) > -1) {
                $table.find('thead tr:last').append('<th data-id="' + service.id + '">' + service.name + '</th>'); 
            }
        }

        var start = parseInt(plan.start.split(':')[0]); 
        var end = parseInt(plan.end.split(':')[0]) + 1;
        var current = start;
        var $tbody = $table.find('tbody'); 

        while(current <= end) {
            var $tr = $('<tr/>').appendTo($tbody); 

            var time = (current < 10 ? '0' + parseInt(current) : parseInt(current)) + (current % 1 === 0 ? ':00' : ':30'); 

            $tr
                .append('<td>' + time + '</td>')
                .append('<td/>'.repeat(provider.services.length)); 

            current += 0.5; 
        }
    }

    function _createAppointments($providerColumn, appointments) {
        if (appointments.length === 0) {
            return;
        }

        var currentDate = new Date($providerColumn.parents('.date-column').data('date')); 
        var $tbody = $providerColumn.find('table tbody');

        for (var index in appointments) {
            var appointment = appointments[index]; 

            if (appointment.id_users_provider !== $providerColumn.data('provider').id) {
                continue;
            }

            var eventDate = Date.parse(appointment.start_datetime); 
            var $event = $('<div class="event appointment" />'); 

            $event.html(
                appointment.service.name + ' - ' + 
                (appointment.customer.first_name + ' ' + appointment.customer.last_name).trim()
            );

            $tbody.find('tr').each(function(index, tr) {
                var $td = $(tr).find('td:first'); 

                var cellDate = new Date(currentDate.getTime()).set({
                    hour: parseInt($td.text().split(':')[0]),
                    minute: parseInt($td.text().split(':')[1])
                }); 

                if (eventDate < cellDate) {
                    var cellIndex = $providerColumn
                        .find('thead tr:last th[data-id="' + appointment.service.id + '"]')
                        .index();

                    $event.appendTo($(tr).prev().find('td').eq(cellIndex));
                    return false;
                }
            }); 
        }
    }

    function _createUnavailabilities($providerColumn, unavailabilities) {
        if (unavailabilities.length === 0) {
            return;
        }
    }

    /**
     * Get the calendar events. 
     * 
     * @param {Date} startDate
     * @param {Date} endDate
     * 
     * @return {jQuery.jqXHR}
     */
    function _getCalendarEvents(startDate, endDate) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_get_calendar_events'; 
        var data = {
            csrfToken: GlobalVariables.csrfToken,
            startDate: startDate.toString('yyyy-MM-dd'),
            endDate: endDate.toString('yyyy-MM-dd')
        };

        return $.post(url, data); 
    }

    exports.initialize = function() {
        _createHeader(); 
        _createView(Date.today(), Date.today().add({days: parseInt($('#select-filter-item').val() - 1)}));
        _bindEventHandlers();
    };

})(window.BackendCalendarTableView);
