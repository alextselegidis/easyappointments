/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.0.0
 * ---------------------------------------------------------------------------- */

App.Pages.Services = (function () {
    const $services = $('#services');
    let filterResults = {};
    let filterLimit = 20;

    function bindEventHandlers() {
        /**
         * Event: Filter Services Form "Submit"
         *
         * @param {jQuery.Event} event
         */
        $services.on('submit', '#filter-services form', function (event) {
            event.preventDefault();
            const key = $('#filter-services .key').val();
            $('#filter-services .selected').removeClass('selected');
            resetForm();
            filter(key);
        });

        /**
         * Event: Filter Service Row "Click"
         *
         * Display the selected service data to the user.
         */
        $services.on('click', '.service-row', function () {
            if ($('#filter-services .filter').prop('disabled')) {
                $('#filter-services .results').css('color', '#AAA');
                return; // exit because we are on edit mode
            }

            const serviceId = $(this).attr('data-id');

            const service = filterResults.find(function (filterResult) {
                return Number(filterResult.id) === Number(serviceId);
            });

            // Add dedicated provider link.
            const dedicatedUrl = App.Utils.Url.siteUrl('?service=' + encodeURIComponent(service.id));

            const $link = $('<a/>', {
                'href': dedicatedUrl,
                'html': [
                    $('<span/>', {
                        'class': 'fas fa-link'
                    })
                ]
            });

            $('#services .record-details h3').find('a').remove().end().append($link);

            display(service);
            $('#filter-services .selected').removeClass('selected');
            $(this).addClass('selected');
            $('#edit-service, #delete-service').prop('disabled', false);
        });

        /**
         * Event: Add New Service Button "Click"
         */
        $services.on('click', '#add-service', function () {
            resetForm();
            $('#services .add-edit-delete-group').hide();
            $('#services .save-cancel-group').show();
            $('#services .record-details').find('input, select, textarea').prop('disabled', false);
            $('#filter-services button').prop('disabled', true);
            $('#filter-services .results').css('color', '#AAA');

            // Default values
            $('#service-name').val('Service');
            $('#service-duration').val('30');
            $('#service-price').val('0');
            $('#service-currency').val('');
            $('#service-category').val('');
            $('#service-availabilities-type').val('flexible');
            $('#service-attendants-number').val('1');
        });

        /**
         * Event: Cancel Service Button "Click"
         *
         * Cancel add or edit of a service record.
         */
        $services.on('click', '#cancel-service', function () {
            const id = $('#service-id').val();
            resetForm();
            if (id !== '') {
                select(id, true);
            }
        });

        /**
         * Event: Save Service Button "Click"
         */
        $services.on('click', '#save-service', function () {
            const service = {
                name: $('#service-name').val(),
                duration: $('#service-duration').val(),
                price: $('#service-price').val(),
                currency: $('#service-currency').val(),
                description: $('#service-description').val(),
                location: $('#service-location').val(),
                availabilities_type: $('#service-availabilities-type').val(),
                attendants_number: $('#service-attendants-number').val(),
                id_categories: $('#service-category').val() || null
            };

            if ($('#service-id').val() !== '') {
                service.id = $('#service-id').val();
            }

            if (!validate()) {
                return;
            }

            save(service);
        });

        /**
         * Event: Edit Service Button "Click"
         */
        $services.on('click', '#edit-service', function () {
            $('#services .add-edit-delete-group').hide();
            $('#services .save-cancel-group').show();
            $('#services .record-details').find('input, select, textarea').prop('disabled', false);
            $('#filter-services button').prop('disabled', true);
            $('#filter-services .results').css('color', '#AAA');
        });

        /**
         * Event: Delete Service Button "Click"
         */
        $services.on('click', '#delete-service', function () {
            const serviceId = $('#service-id').val();
            const buttons = [
                {
                    text: App.Lang.cancel,
                    click: function () {
                        $('#message-box').dialog('close');
                    }
                },
                {
                    text: App.Lang.delete,
                    click: function () {
                        remove(serviceId);
                        $('#message-box').dialog('close');
                    }
                }
            ];

            App.Utils.Message.show(App.Lang.delete_service, App.Lang.delete_record_prompt, buttons);
        });
    }

    /**
     * Save service record to database.
     *
     * @param {Object} service Contains the service record data. If an 'id' value is provided
     * then the update operation is going to be executed.
     */
    function save(service) {
        App.Http.Services.save(service).then((response) => {
            Backend.displayNotification(App.Lang.service_saved);
            resetForm();
            $('#filter-services .key').val('');
            filter('', response.id, true);
        });
    }

    /**
     * Delete a service record from database.
     *
     * @param {Number} id Record ID to be deleted.
     */
    function remove(id) {
        App.Http.Services.destroy(id).then(() => {
            Backend.displayNotification(App.Lang.service_deleted);
            resetForm();
            filter($('#filter-services .key').val());
        });
    }

    /**
     * Validates a service record.
     *
     * @return {Boolean} Returns the validation result.
     */
    function validate() {
        $('#services .is-invalid').removeClass('is-invalid');
        $('#services .form-message').removeClass('alert-danger').hide();

        try {
            // Validate required fields.
            let missingRequired = false;

            $('#services .required').each(function (index, requiredField) {
                if (!$(requiredField).val()) {
                    $(requiredField).addClass('is-invalid');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error(App.Lang.fields_are_required);
            }

            // Validate the duration.
            if (Number($('#service-duration').val()) < 5) {
                $('#service-duration').addClass('is-invalid');
                throw new Error(App.Lang.invalid_duration);
            }

            return true;
        } catch (error) {
            $('#services .form-message').addClass('alert-danger').text(error.message).show();
            return false;
        }
    }

    /**
     * Resets the service tab form back to its initial state.
     */
    function resetForm() {
        $('#filter-services .selected').removeClass('selected');
        $('#filter-services button').prop('disabled', false);
        $('#filter-services .results').css('color', '');

        $('#services .record-details').find('input, select, textarea').val('').prop('disabled', true);
        $('#services .record-details h3 a').remove();

        $('#services .add-edit-delete-group').show();
        $('#services .save-cancel-group').hide();
        $('#edit-service, #delete-service').prop('disabled', true);

        $('#services .record-details .is-invalid').removeClass('is-invalid');
        $('#services .record-details .form-message').hide();
    }

    /**
     * Display a service record into the service form.
     *
     * @param {Object} service Contains the service record data.
     */
    function display(service) {
        $('#service-id').val(service.id);
        $('#service-name').val(service.name);
        $('#service-duration').val(service.duration);
        $('#service-price').val(service.price);
        $('#service-currency').val(service.currency);
        $('#service-description').val(service.description);
        $('#service-location').val(service.location);
        $('#service-availabilities-type').val(service.availabilities_type);
        $('#service-attendants-number').val(service.attendants_number);

        const categoryId = service.id_categories !== null ? service.id_categories : '';
        $('#service-category').val(categoryId);
    }

    /**
     * Filters service records depending a string keyword.
     *
     * @param {String} keyword This is used to filter the service records of the database.
     * @param {Number} selectId Optional, if set then after the filter operation the record with this
     * ID will be selected (but not displayed).
     * @param {Boolean} show Optional (false), if true then the selected record will be displayed on the form.
     */
    function filter(keyword, selectId = null, show = false) {
        App.Http.Services.search(keyword, filterLimit).then((response) => {
            filterResults = response;

            $('#filter-services .results').empty();

            response.forEach(function (service, index) {
                $('#filter-services .results').append(getFilterHtml(service)).append($('<hr/>'));
            });

            if (response.length === 0) {
                $('#filter-services .results').append(
                    $('<em/>', {
                        'text': App.Lang.no_records_found
                    })
                );
            } else if (response.length === filterLimit) {
                $('<button/>', {
                    'type': 'button',
                    'class': 'btn btn-outline-secondary w-100 load-more text-center',
                    'text': App.Lang.load_more,
                    'click': function () {
                        filterLimit += 20;
                        filter(keyword, selectId, show);
                    }.bind(this)
                }).appendTo('#filter-services .results');
            }

            if (selectId) {
                select(selectId, show);
            }
        });
    }

    /**
     * Get Filter HTML
     *
     * Get a service row HTML code that is going to be displayed on the filter results list.
     *
     * @param {Object} service Contains the service record data.
     *
     * @return {String} The HTML code that represents the record on the filter results list.
     */
    function getFilterHtml(service) {
        const name = service.name;

        const info = service.duration + ' min - ' + service.price + ' ' + service.currency;

        return $('<div/>', {
            'class': 'service-row entry',
            'data-id': service.id,
            'html': [
                $('<strong/>', {
                    'text': name
                }),
                $('<br/>'),
                $('<span/>', {
                    'text': info
                }),
                $('<br/>')
            ]
        });
    }

    /**
     * Select a specific record from the current filter results. If the service id does not exist
     * in the list then no record will be selected.
     *
     * @param {Number} id The record id to be selected from the filter results.
     * @param {Boolean} show Optional (false), if true then the method will display the record on the form.
     */
    function select(id, show = false) {
        $('#filter-services .selected').removeClass('selected');

        $('#filter-services .service-row[data-id="' + id + '"]').addClass('selected');

        if (show) {
            const service = filterResults.find(function (filterResult) {
                return Number(filterResult.id) === Number(id);
            });

            display(service);

            $('#edit-service, #delete-service').prop('disabled', false);
        }
    }

    /**
     * Update the service category list box.
     *
     * Use this method every time a change is made to the service categories db table.
     */
    function updateAvailableCategories() {
        App.Http.Categories.search('', 999).then((response) => {
            var $select = $('#service-category');

            $select.empty();

            response.forEach(function (category) {
                $select.append(new Option(category.name, category.id));
            });

            $select.append(new Option('- ' + App.Lang.no_category + ' -', '')).val('');
        });
    }

    function init() {
        resetForm();
        filter('');
        bindEventHandlers();
        updateAvailableCategories();
    }

    document.addEventListener('DOMContentLoaded', init);

    return {
        filter,
        save,
        remove,
        getFilterHtml,
        resetForm,
        select
    };
})();
