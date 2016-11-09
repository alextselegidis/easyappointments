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

(function() {

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
    };

    /**
     * Binds the default event handlers of the categories tab.
     */
    CategoriesHelper.prototype.bindEventHandlers = function() {
        var instance = this;

        /**
         * Event: Filter Categories Cancel Button "Click"
         */
        $('#filter-categories .clear').click(function() {
            $('#filter-categories .key').val('');
            instance.filter('');
            instance.resetForm();
        });

        /**
         * Event: Filter Categories Form "Submit"
         */
        $('#filter-categories form').submit(function() {
            var key = $('#filter-categories .key').val();
            $('.selected').removeClass('selected');
            instance.resetForm();
            instance.filter(key);
            return false;
        });

        /**
         * Event: Filter Categories Row "Click"
         *
         * Displays the selected row data on the right side of the page.
         */
        $(document).on('click', '.category-row', function() {
            if ($('#filter-categories .filter').prop('disabled')) {
                $('#filter-categories .results').css('color', '#AAA');
                return; // exit because we are on edit mode
            }

            var categoryId = $(this).attr('data-id');
            var category = {};
            $.each(instance.filterResults, function(index, item) {
                if (item.id === categoryId) {
                    category = item;
                    return false;
                }
            });

            instance.display(category);
            $('#filter-categories .selected').removeClass('selected');
            $(this).addClass('selected');
            $('#edit-category, #delete-category').prop('disabled', false);
        });

        /**
         * Event: Add Category Button "Click"
         */
        $('#add-category').click(function() {
            instance.resetForm();
            $('#categories .add-edit-delete-group').hide();
            $('#categories .save-cancel-group').show();
            $('#categories .record-details').find('input, textarea').prop('readonly', false);
            $('#filter-categories button').prop('disabled', true);
            $('#filter-categories .results').css('color', '#AAA');
        });

        /**
         * Event: Edit Category Button "Click"
         */
        $('#edit-category').click(function() {
            $('#categories .add-edit-delete-group').hide();
            $('#categories .save-cancel-group').show();
            $('#categories .record-details').find('input, textarea').prop('readonly', false);

            $('#filter-categories button').prop('disabled', true);
            $('#filter-categories .results').css('color', '#AAA');
        });

        /**
         * Event: Delete Category Button "Click"
         */
        $('#delete-category').click(function() {
            var categoryId = $('#category-id').val();

            var messageBtns = {};
            messageBtns[EALang['delete']] = function() {
                instance.delete(categoryId);
                $('#message_box').dialog('close');
            };
            messageBtns[EALang['cancel']] = function() {
                $('#message_box').dialog('close');
            };

            GeneralFunctions.displayMessageBox(EALang['delete_category'],
                    EALang['delete_record_prompt'], messageBtns);
        });

        /**
         * Event: Categories Save Button "Click"
         */
        $('#save-category').click(function() {
            var category = {
                name: $('#category-name').val(),
                description: $('#category-description').val()
            };

            if ($('#category-id').val() !== '') {
                category.id = $('#category-id').val();
            }

            if (!instance.validate(category)) {
                return;
            }

            instance.save(category);
        });

        /**
         * Event: Cancel Category Button "Click"
         */
        $('#cancel-category').click(function() {
            var id = $('#category-id').val();
            instance.resetForm();
            if (id !== '') {
                instance.select(id, true);
            }
        });
    };

    /**
     * Filter service categories records.
     *
     * @param {String} key This key string is used to filter the category records.
     * @param {Number} selectId Optional, if set then after the filter operation the record with the given
     * ID will be selected (but not displayed).
     * @param {Boolean} display Optional (false), if true then the selected record will be displayed on the form.
     */
    CategoriesHelper.prototype.filter = function(key, selectId, display) {
        var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_filter_service_categories';
        var postData = {
            csrfToken: GlobalVariables.csrfToken,
            key: key
        };

        $.post(postUrl, postData, function(response) {
            if (!GeneralFunctions.handleAjaxExceptions(response)) {
                return;
            }

            this.filterResults = response;

            $('#filter-categories .results').data('jsp').destroy();
            $('#filter-categories .results').html('');
            $.each(response, function(index, category) {
                var html = this.getFilterHtml(category);
                $('#filter-categories .results').append(html);
            }.bind(this));
            $('#filter-categories .results').jScrollPane({ mouseWheelSpeed: 70 });

            if (response.length === 0) {
                $('#filter-categories .results').html('<em>' + EALang['no_records_found'] + '</em>');
            }

            if (selectId !== undefined) {
                this.select(selectId, display);
            }
        }.bind(this), 'json').fail(GeneralFunctions.ajaxFailureHandler);
    };

    /**
     * Save a category record to the database (via AJAX post).
     *
     * @param {Object} category Contains the category data.
     */
    CategoriesHelper.prototype.save = function(category) {
        var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_save_service_category';
        var postData = {
            csrfToken: GlobalVariables.csrfToken,
            category: JSON.stringify(category)
        };

        $.post(postUrl, postData, function(response) {
            if (!GeneralFunctions.handleAjaxExceptions(response)) {
                return;
            }

            Backend.displayNotification(EALang['service_category_saved']);
            this.resetForm();
            $('#filter-categories .key').val('');
            this.filter('', response.id, true);
            BackendServices.updateAvailableCategories();
        }. bind(this), 'json').fail(GeneralFunctions.ajaxFailureHandler);
    };

    /**
     * Delete category record.
     *
     * @param Number} id Record ID to be deleted.
     */
    CategoriesHelper.prototype.delete = function(id) {
        var postUrl = GlobalVariables.baseUrl + '/index.php/backend_api/ajax_delete_service_category';
        var postData = {
            csrfToken: GlobalVariables.csrfToken,
            category_id: id
        };

        $.post(postUrl, postData, function(response) {
            if (!GeneralFunctions.handleAjaxExceptions(response)) {
                return;
            }

            Backend.displayNotification(EALang['service_category_deleted']);

            this.resetForm();
            this.filter($('#filter-categories .key').val());
            BackendServices.updateAvailableCategories();
        }.bind(this), 'json').fail(GeneralFunctions.ajaxFailureHandler);
    };

    /**
     * Display a category record on the form.
     *
     * @param {Object} category Contains the category data.
     */
    CategoriesHelper.prototype.display = function(category) {
        $('#category-id').val(category.id);
        $('#category-name').val(category.name);
        $('#category-description').val(category.description);
    };

    /**
     * Validate category data before save (insert or update).
     *
     * @param {Object} category Contains the category data.
     */
    CategoriesHelper.prototype.validate = function(category) {
        $('#categories .record-details').find('input, textarea').css('border', '');

        try {
            var missingRequired = false;

            $('#categories .required').each(function() {
                if ($(this).val() === '' || $(this).val() === undefined) {
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
     * Bring the category form back to its initial state.
     */
    CategoriesHelper.prototype.resetForm = function() {
        $('#categories .add-edit-delete-group').show();
        $('#categories .save-cancel-group').hide();
        $('#categories .record-details').find('input, textarea').val('');
        $('#categories .record-details').find('input, textarea').prop('readonly', true);
        $('#edit-category, #delete-category').prop('disabled', true);

        $('#filter-categories .selected').removeClass('selected');
        $('#filter-categories .results').css('color', '');
        $('#filter-categories button').prop('disabled', false);
    };

    /**
     * Get the filter results row HTML code.
     *
     * @param {Object} category Contains the category data.
     *
     * @return {String} Returns the record HTML code.
     */
    CategoriesHelper.prototype.getFilterHtml = function(category) {
        var html =
                '<div class="category-row entry" data-id="' + category.id + '">' +
                    '<strong>' + category.name + '</strong>' +
                '</div><hr>';

        return html;
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
    CategoriesHelper.prototype.select = function(id, display) {
        display = display || false;

        $('#filter-categories .selected').removeClass('selected');

        $('#filter-categories .category-row').each(function() {
            if ($(this).attr('data-id') === id) {
                $(this).addClass('selected');
                return false;
            }
        });

        if (display) {
            $.each(this.filterResults, function(index, category) {
                if (category.id === id) {
                    this.display(category);
                    $('#edit-category, #delete-category').prop('disabled', false);
                    return false;
                }
            }.bind(this));
        }
    };

    window.CategoriesHelper = CategoriesHelper;
})();
