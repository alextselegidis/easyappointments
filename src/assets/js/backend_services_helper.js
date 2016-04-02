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

 /**
  * ServicesHelper
  *
  * This class contains the methods that will be used by the "Services" tab of the page.
  *
  * @class ServicesHelper
  */
(function() {

    'use strict';

    function ServicesHelper() {
        this.filterResults = {};
    };

    ServicesHelper.prototype.bindEventHandlers = function() {
        var instance = this;

        /**
         * Event: Filter Services Form "Submit"
         */
        $('#filter-services form').submit(function(event) {
            var key = $('#filter-services .key').val();
            $('#filter-services .selected-row').removeClass('selected-row');
            instance.resetForm();
            instance.filter(key);
            return false;
        });

        /**
         * Event: Filter Service Cancel Button "Click"
         */
        $('#filter-services .clear').click(function() {
            $('#filter-services .key').val('');
            instance.filter('');
            instance.resetForm();
        });

        /**
         * Event: Filter Service Row "Click"
         *
         * Display the selected service data to the user.
         */
        $(document).on('click', '.service-row', function() {
            if ($('#filter-services .filter').prop('disabled')) {
                $('#filter-services .results').css('color', '#AAA');
                return; // exit because we are on edit mode
            }

            var serviceId = $(this).attr('data-id'),
                service = {};
            $.each(instance.filterResults, function(index, item) {
                if (item.id === serviceId) {
                    service = item;
                    return false;
                }
            });

            instance.display(service);
            $('#filter-services .selected-row').removeClass('selected-row');
            $(this).addClass('selected-row');
            $('#edit-service, #delete-service').prop('disabled', false);
        });

        /**
         * Event: Add New Service Button "Click"
         */
        $('#add-service').click(function() {
            instance.resetForm();
            $('#services .add-edit-delete-group').hide();
            $('#services .save-cancel-group').show();
            $('#services .details').find('input, textarea').prop('readonly', false);
            $('#services .details').find('select').prop('disabled', false);
            $('#service-duration').spinner('enable');

            $('#filter-services button').prop('disabled', true);
            $('#filter-services .results').css('color', '#AAA');
        });

        /**
         * Event: Cancel Service Button "Click"
         *
         * Cancel add or edit of a service record.
         */
        $('#cancel-service').click(function() {
            var id = $('#service-id').val();
            instance.resetForm();
            if (id !== '') {
                instance.select(id, true);
            }
        });

        /**
         * Event: Save Service Button "Click"
         */
        $('#save-service').click(function() {
            var service = {
                name: $('#service-name').val(),
                duration: $('#service-duration').val(),
                price: $('#service-price').val(),
                currency: $('#service-currency').val(),
                description: $('#service-description').val()
            };

            if ($('#service-category').val() !== 'null') {
                service.id_service_categories = $('#service-category').val();
            } else {
                service.id_service_categories = null;
            }

            if ($('#service-id').val() !== '') {
                service.id = $('#service-id').val();
            }

            if (!instance.validate(service)) {
                return;
            }

            instance.save(service);
        });

        /**
         * Event: Edit Service Button "Click"
         */
        $('#edit-service').click(function() {
            $('#services .add-edit-delete-group').hide();
            $('#services .save-cancel-group').show();
            $('#services .details').find('input, textarea').prop('readonly', false);
            $('#services .details select').prop('disabled', false);
            $('#service-duration').spinner('enable');

            $('#filter-services button').prop('disabled', true);
            $('#filter-services .results').css('color', '#AAA');
        });

        /**
         * Event: Delete Service Button "Click"
         */
        $('#delete-service').click(function() {
            var serviceId = $('#service-id').val(),
                messageBtns = {};

            messageBtns[EALang['delete']] = function() {
                instance.delete(serviceId);
                $('#message_box').dialog('close');
            };

            messageBtns[EALang['cancel']] = function() {
                $('#message_box').dialog('close');
            };

            GeneralFunctions.displayMessageBox(EALang['delete_service'],
                    EALang['delete_record_prompt'], messageBtns);
        });
    };

    /**
     * Save service record to database.
     *
     * @param {object} service Contains the service record data. If an 'id' value is provided
     * then the update operation is going to be executed.
     */
    ServicesHelper.prototype.save = function(service) {
        var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_service',
            postData = {
                'csrfToken': GlobalVariables.csrfToken,
                'service': JSON.stringify(service)
            };

        $.post(postUrl, postData, function(response) {
            if (!GeneralFunctions.handleAjaxExceptions(response)) {
                return;
            }

            Backend.displayNotification(EALang['service_saved']);
            this.resetForm();
            $('#filter-services .key').val('');
            this.filter('', response.id, true);
        }.bind(this), 'json').fail(GeneralFunctions.ajaxFailureHandler);
    };

    /**
     * Delete a service record from database.
     *
     * @param {numeric} id Record id to be deleted.
     */
    ServicesHelper.prototype.delete = function(id) {
        var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_delete_service',
            postData = {
                'csrfToken': GlobalVariables.csrfToken,
                'service_id': id
            };

        $.post(postUrl, postData, function(response) {
            if (!GeneralFunctions.handleAjaxExceptions(response)) {
                return;
            }

            Backend.displayNotification(EALang['service_deleted']);

            this.resetForm();
            this.filter($('#filter-services .key').val());
        }.bind(this), 'json').fail(GeneralFunctions.ajaxFailureHandler);
    };

    /**
     * Validates a service record.
     *
     * @param {object} service Contains the service data.
     * @returns {bool} Returns the validation result.
     */
    ServicesHelper.prototype.validate = function(service) {
        $('#services .required').css('border', '');

        try {
            // validate required fields.
            var missingRequired = false;

            $('#services .required').each(function() {
                if ($(this).val() == '' || $(this).val() == undefined) {
                    $(this).css('border', '2px solid red');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw EALang['fields_are_required'];
            }

            return true;
        } catch(exc) {
            return false;
        }
    };

    /**
     * Resets the service tab form back to its initial state.
     */
    ServicesHelper.prototype.resetForm = function() {
        $('#services .details').find('input, textarea').val('');
        $('#service-category').val('null');
        $('#services .add-edit-delete-group').show();
        $('#services .save-cancel-group').hide();
        $('#edit-service, #delete-service').prop('disabled', true);
        $('#services .details').find('input, textarea').prop('readonly', true);
        $('#service-category').prop('disabled', true);
        $('#service-duration').spinner('disable');

        $('#filter-services .selected-row').removeClass('selected-row');
        $('#filter-services button').prop('disabled', false);
        $('#filter-services .results').css('color', '');
    };

    /**
     * Display a service record into the service form.
     *
     * @param {object} service Contains the service record data.
     */
    ServicesHelper.prototype.display = function(service) {
        $('#service-id').val(service.id);
        $('#service-name').val(service.name);
        $('#service-duration').val(service.duration);
        $('#service-price').val(service.price);
        $('#service-currency').val(service.currency);
        $('#service-description').val(service.description);

        var categoryId = (service.id_service_categories !== null) ? service.id_service_categories : 'null';
        $('#service-category').val(categoryId);
    };

    /**
     * Filters service records depending a string key.
     *
     * @param {string} key This is used to filter the service records of the database.
     * @param {numeric} selectId (OPTIONAL = undefined) If set then after the filter
     * operation the record with this id will be selected (but not displayed).
     * @param {bool} display (OPTIONAL = false) If true then the selected record will
     * be displayed on the form.
     */
    ServicesHelper.prototype.filter = function(key, selectId, display) {
        display = display || false;

        var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_filter_services',
            postData = {
                'csrfToken': GlobalVariables.csrfToken,
                'key': key
            };

        $.post(postUrl, postData, function(response) {
            if (!GeneralFunctions.handleAjaxExceptions(response)) {
                return;
            }

            this.filterResults = response;

            $('#filter-services .results').data('jsp').destroy();
            $('#filter-services .results').html('');
            $.each(response, function(index, service) {
                var html = ServicesHelper.prototype.getFilterHtml(service);
                $('#filter-services .results').append(html);
            });
            $('#filter-services .results').jScrollPane({ mouseWheelSpeed: 70 });

            if (response.length === 0) {
                $('#filter-services .results').html('<em>' + EALang['no_records_found'] + '</em>');
            }

            if (selectId !== undefined) {
                this.select(selectId, display);
            }
        }.bind(this), 'json').fail(GeneralFunctions.ajaxFailureHandler);
    };

    /**
     * Get a service row html code that is going to be displayed on the filter results list.
     *
     * @param {object} service Contains the service record data.
     * @returns {string} The html code that represents the record on the filter results list.
     */
    ServicesHelper.prototype.getFilterHtml = function(service) {
        var html =
                '<div class="service-row" data-id="' + service.id + '">' +
                    '<strong>' + service.name + '</strong><br>' +
                    service.duration + ' min - ' +
                    service.price + ' ' + service.currency + '<br>' +
                '</div><hr>';

        return html;
    };

    /**
     * Select a specific record from the current filter results. If the service id does not exist
     * in the list then no record will be selected.
     *
     * @param {numeric} id The record id to be selected from the filter results.
     * @param {bool} display (OPTIONAL = false) If true then the method will display the record
     * on the form.
     */
    ServicesHelper.prototype.select = function(id, display) {
        display = display || false;

        $('#filter-services .selected-row').removeClass('selected-row');

        $('#filter-services .service-row').each(function() {
            if ($(this).attr('data-id') === id) {
                $(this).addClass('selected-row');
                return false;
            }
        });

        if (display) {
            $.each(this.filterResults, function(index, service) {
                if (service.id === id) {
                    this.display(service);
                    $('#edit-service, #delete-service').prop('disabled', false);
                    return false;
                }
            }.bind(this));
        }
    };

    window.ServicesHelper = ServicesHelper;
})();
