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

(function () {

    'use strict';

    /**
     * CategoriesHelper Class
     *
     * This class contains the core method implementations that belong to the categories tab
     * of the backend services page.
     *
     * @class CategoriesHelper
     */
    function CategoriesHelper() {
        this.filterResults = {};
        this.filterLimit = 20;
    }

    /**
     * Binds the default event handlers of the categories tab.
     */
    CategoriesHelper.prototype.bindEventHandlers = function () {
        var instance = this;

        /**
         * Event: Filter Categories Cancel Button "Click"
         */
        $('#categories').on('click', '#filter-categories .clear', function () {
            $('#filter-categories .key').val('');
            instance.filter('');
            instance.resetForm();
        });

        /**
         * Event: Filter Categories Form "Submit"
         *
         * @param {jQuery.Event} event
         */
        $('#categories').on('submit', '#filter-categories form', function (event) {
            event.preventDefault();
            var key = $('#filter-categories .key').val();
            $('.selected').removeClass('selected');
            instance.resetForm();
            instance.filter(key);
        });

        /**
         * Event: Filter Categories Row "Click"
         *
         * Displays the selected row data on the right side of the page.
         */
        $('#categories').on('click', '.category-row', function () {
            if ($('#filter-categories .filter').prop('disabled')) {
                $('#filter-categories .results').css('color', '#AAA');
                return; // exit because we are on edit mode
            }

            var categoryId = $(this).attr('data-id');

            var category = instance.filterResults.find(function (filterResult) {
                return Number(filterResult.id) === Number(categoryId);
            });

            instance.display(category);
            $('#filter-categories .selected').removeClass('selected');
            $(this).addClass('selected');
            $('#edit-category, #delete-category').prop('disabled', false);
        });

        /**
         * Event: Add Category Button "Click"
         */
        $('#categories').on('click', '#add-category', function () {
            instance.resetForm();
            $('#categories .add-edit-delete-group').hide();
            $('#categories .save-cancel-group').show();
            $('#categories .record-details').find('input, select, textarea').prop('disabled', false);
            $('#filter-categories button').prop('disabled', true);
            $('#filter-categories .results').css('color', '#AAA');
        });

        /**
         * Event: Edit Category Button "Click"
         */
        $('#categories').on('click', '#edit-category', function () {
            $('#categories .add-edit-delete-group').hide();
            $('#categories .save-cancel-group').show();
            $('#categories .record-details').find('input, select, textarea').prop('disabled', false);
            $('#filter-categories button').prop('disabled', true);
            $('#filter-categories .results').css('color', '#AAA');
        });

        /**
         * Event: Delete Category Button "Click"
         */
        $('#categories').on('click', '#delete-category', function () {
            var categoryId = $('#category-id').val();

            var buttons = [
                {
                    text: EALang.cancel,
                    click: function () {
                        $('#message-box').dialog('close');
                    }
                },
                {
                    text: EALang.delete,
                    click: function () {
                        instance.delete(categoryId);
                        $('#message-box').dialog('close');
                    }
                },
            ];

            GeneralFunctions.displayMessageBox(EALang.delete_category,
                EALang.delete_record_prompt, buttons);
        });

        /**
         * Event: Categories Save Button "Click"
         */
        $('#categories').on('click', '#save-category', function () {
            var category = {
                name: $('#category-name').val(),
                description: $('#category-description').val()
            };

            if ($('#category-id').val() !== '') {
                category.id = $('#category-id').val();
            }

            if (!instance.validate()) {
                return;
            }

            instance.save(category);
        });

        /**
         * Event: Cancel Category Button "Click"
         */
        $('#categories').on('click', '#cancel-category', function () {
            var id = $('#category-id').val();
            instance.resetForm();
            if (id !== '') {
                instance.select(id, true);
            }
        });
    };

    /**
     * Remove the previously registered event handlers.
     */
    CategoriesHelper.prototype.unbindEventHandlers = function () {
        $('#categories')
            .off('click', '#filter-categories .clear')
            .off('submit', '#filter-categories form')
            .off('click', '.category-row')
            .off('click', '#add-category')
            .off('click', '#edit-category')
            .off('click', '#delete-category')
            .off('click', '#save-category')
            .off('click', '#cancel-category');
    };

    /**
     * Filter service categories records.
     *
     * @param {String} key This key string is used to filter the category records.
     * @param {Number} selectId Optional, if set then after the filter operation the record with the given
     * ID will be selected (but not displayed).
     * @param {Boolean} display Optional (false), if true then the selected record will be displayed on the form.
     */
    CategoriesHelper.prototype.filter = function (key, selectId, display) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_filter_service_categories';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            key: key,
            limit: this.filterLimit
        };

        $.post(url, data)
            .done(function (response) {
                this.filterResults = response;

                $('#filter-categories .results').empty();

                response.forEach(function (category) {
                    $('#filter-categories .results')
                        .append(this.getFilterHtml(category))
                        .append($('<hr/>'));
                }.bind(this));

                if (response.length === 0) {
                    $('#filter-categories .results').append(
                        $('<em/>', {
                            'text': EALang.no_records_found
                        })
                    );
                } else if (response.length === this.filterLimit) {
                    $('<button/>', {
                        'type': 'button',
                        'class': 'btn btn-block btn-outline-secondary load-more text-center',
                        'text': EALang.load_more,
                        'click': function () {
                            this.filterLimit += 20;
                            this.filter(key, selectId, display);
                        }.bind(this)
                    })
                        .appendTo('#filter-categories .results');
                }

                if (selectId) {
                    this.select(selectId, display);
                }
            }.bind(this));
    };

    /**
     * Save a category record to the database (via AJAX post).
     *
     * @param {Object} category Contains the category data.
     */
    CategoriesHelper.prototype.save = function (category) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_service_category';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            category: JSON.stringify(category)
        };

        $.post(url, data)
            .done(function (response) {
                Backend.displayNotification(EALang.service_category_saved);
                this.resetForm();
                $('#filter-categories .key').val('');
                this.filter('', response.id, true);
                BackendServices.updateAvailableCategories();
            }.bind(this));
    };

    /**
     * Delete category record.
     *
     * @param Number} id Record ID to be deleted.
     */
    CategoriesHelper.prototype.delete = function (id) {
        var url = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_delete_service_category';

        var data = {
            csrfToken: GlobalVariables.csrfToken,
            category_id: id
        };

        $.post(url, data)
            .done(function () {
                Backend.displayNotification(EALang.service_category_deleted);

                this.resetForm();
                this.filter($('#filter-categories .key').val());
                BackendServices.updateAvailableCategories();
            }.bind(this));
    };

    /**
     * Display a category record on the form.
     *
     * @param {Object} category Contains the category data.
     */
    CategoriesHelper.prototype.display = function (category) {
        $('#category-id').val(category.id);
        $('#category-name').val(category.name);
        $('#category-description').val(category.description);
    };

    /**
     * Validate category data before save (insert or update).
     *
     * @return {Boolean} Returns the validation result.
     */
    CategoriesHelper.prototype.validate = function () {
        $('#categories .has-error').removeClass('has-error');
        $('#categories .form-message')
            .removeClass('alert-danger')
            .hide();

        try {
            var missingRequired = false;

            $('#categories .required').each(function (index, requiredField) {
                if (!$(requiredField).val()) {
                    $(requiredField).closest('.form-group').addClass('has-error');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error(EALang.fields_are_required);
            }

            return true;
        } catch (error) {
            $('#categories .form-message')
                .addClass('alert-danger')
                .text(error.message)
                .show();
            return false;
        }
    };

    /**
     * Bring the category form back to its initial state.
     */
    CategoriesHelper.prototype.resetForm = function () {
        $('#filter-categories .selected').removeClass('selected');
        $('#filter-categories button').prop('disabled', false);
        $('#filter-categories .results').css('color', '');

        $('#categories .add-edit-delete-group').show();
        $('#categories .save-cancel-group').hide();
        $('#categories .record-details')
            .find('input, select, textarea')
            .val('')
            .prop('disabled', true);
        $('#edit-category, #delete-category').prop('disabled', true);

        $('#categories .record-details .has-error').removeClass('has-error');
        $('#categories .record-details .form-message').hide();
    };

    /**
     * Get the filter results row HTML code.
     *
     * @param {Object} category Contains the category data.
     *
     * @return {String} Returns the record HTML code.
     */
    CategoriesHelper.prototype.getFilterHtml = function (category) {
        return $('<div/>', {
            'class': 'category-row entry',
            'data-id': category.id,
            'html': [
                $('<strong/>', {
                    'text': category.name
                }),
                $('<br/>'),
            ]
        });
    };

    /**
     * Select a specific record from the current filter results.
     *
     * If the category ID does not exist in the list then no record will be selected.
     *
     * @param {Number} id The record ID to be selected from the filter results.
     * @param {Boolean} display Optional (false), if true then the method will display the record
     * on the form.
     */
    CategoriesHelper.prototype.select = function (id, display) {
        display = display || false;

        $('#filter-categories .selected').removeClass('selected');

        $('#filter-categories .category-row[data-id="' + id + '"]').addClass('selected');

        if (display) {
            var category = this.filterResults.find(function (category) {
                return Number(category.id) === Number(id);
            }.bind(this));

            this.display(category);

            $('#edit-category, #delete-category').prop('disabled', false);
        }
    };

    window.CategoriesHelper = CategoriesHelper;
})();
