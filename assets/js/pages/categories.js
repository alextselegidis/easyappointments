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

App.Pages.Categories = (function () {
    const $categories = $('#categories');
    let filterResults = {};
    let filterLimit = 20;

    /**
     * Binds the default event handlers of the categories tab.
     */
    function bindEventHandlers() {
        /**
         * Event: Filter Categories Form "Submit"
         *
         * @param {jQuery.Event} event
         */
        $categories.on('submit', '#filter-categories form', function (event) {
            event.preventDefault();
            const key = $('#filter-categories .key').val();
            $('.selected').removeClass('selected');
            resetForm();
            filter(key);
        });

        /**
         * Event: Filter Categories Row "Click"
         *
         * Displays the selected row data on the right side of the page.
         */
        $categories.on('click', '.category-row', function () {
            if ($('#filter-categories .filter').prop('disabled')) {
                $('#filter-categories .results').css('color', '#AAA');
                return; // exit because we are on edit mode
            }

            const categoryId = $(this).attr('data-id');

            const category = filterResults.find(function (filterResult) {
                return Number(filterResult.id) === Number(categoryId);
            });

            display(category);
            $('#filter-categories .selected').removeClass('selected');
            $(this).addClass('selected');
            $('#edit-category, #delete-category').prop('disabled', false);
        });

        /**
         * Event: Add Category Button "Click"
         */
        $categories.on('click', '#add-category', function () {
            resetForm();
            $('#categories .add-edit-delete-group').hide();
            $('#categories .save-cancel-group').show();
            $('#categories .record-details').find('input, select, textarea').prop('disabled', false);
            $('#filter-categories button').prop('disabled', true);
            $('#filter-categories .results').css('color', '#AAA');
        });

        /**
         * Event: Edit Category Button "Click"
         */
        $categories.on('click', '#edit-category', function () {
            $('#categories .add-edit-delete-group').hide();
            $('#categories .save-cancel-group').show();
            $('#categories .record-details').find('input, select, textarea').prop('disabled', false);
            $('#filter-categories button').prop('disabled', true);
            $('#filter-categories .results').css('color', '#AAA');
        });

        /**
         * Event: Delete Category Button "Click"
         */
        $categories.on('click', '#delete-category', function () {
            const categoryId = $('#category-id').val();

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
                        remove(categoryId);
                        $('#message-box').dialog('close');
                    }
                }
            ];

            App.Utils.Message.show(App.Lang.delete_category, App.Lang.delete_record_prompt, buttons);
        });

        /**
         * Event: Categories Save Button "Click"
         */
        $categories.on('click', '#save-category', function () {
            const category = {
                name: $('#category-name').val(),
                description: $('#category-description').val()
            };

            if ($('#category-id').val() !== '') {
                category.id = $('#category-id').val();
            }

            if (!validate()) {
                return;
            }

            save(category);
        });

        /**
         * Event: Cancel Category Button "Click"
         */
        $categories.on('click', '#cancel-category', function () {
            const id = $('#category-id').val();
            resetForm();
            if (id !== '') {
                select(id, true);
            }
        });
    }

    /**
     * Remove the previously registered event handlers.
     */
    function unbindEventHandlers() {
        $categories
            .off('click', '#filter-categories .clear')
            .off('submit', '#filter-categories form')
            .off('click', '.category-row')
            .off('click', '#add-category')
            .off('click', '#edit-category')
            .off('click', '#delete-category')
            .off('click', '#save-category')
            .off('click', '#cancel-category');
    }

    /**
     * Filter service categories records.
     *
     * @param {String} keyword This key string is used to filter the category records.
     * @param {Number} selectId Optional, if set then after the filter operation the record with the given
     * ID will be selected (but not displayed).
     * @param {Boolean} show Optional (false), if true then the selected record will be displayed on the form.
     */
    function filter(keyword, selectId = null, show = false) {
        App.Http.Categories.search(keyword, filterLimit).then((response) => {
            filterResults = response;

            $('#filter-categories .results').empty();

            response.forEach(
                function (category) {
                    $('#filter-categories .results').append(getFilterHtml(category)).append($('<hr/>'));
                }.bind(this)
            );

            if (response.length === 0) {
                $('#filter-categories .results').append(
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
                }).appendTo('#filter-categories .results');
            }

            if (selectId) {
                select(selectId, show);
            }
        });
    }

    /**
     * Save a category record to the database (via AJAX post).
     *
     * @param {Object} category Contains the category data.
     */
    function save(category) {
        App.Http.Categories.save(category).then((response) => {
            Backend.displayNotification(App.Lang.category_saved);
            resetForm();
            $('#filter-categories .key').val('');
            filter('', response.id, true);
        });
    }

    /**
     * Delete category record.
     *
     * @param {Number} id Record ID to be deleted.
     */
    function remove(id) {
        App.Http.Categories.destroy(id).then(() => {
            Backend.displayNotification(App.Lang.category_deleted);
            resetForm();
            filter($('#filter-categories .key').val());
        });
    }

    /**
     * Display a category record on the form.
     *
     * @param {Object} category Contains the category data.
     */
    function display(category) {
        $('#category-id').val(category.id);
        $('#category-name').val(category.name);
        $('#category-description').val(category.description);
    }

    /**
     * Validate category data before save (insert or update).
     *
     * @return {Boolean} Returns the validation result.
     */
    function validate() {
        $('#categories .is-invalid').removeClass('is-invalid');
        $('#categories .form-message').removeClass('alert-danger').hide();

        try {
            let missingRequired = false;

            $('#categories .required').each(function (index, requiredField) {
                if (!$(requiredField).val()) {
                    $(requiredField).addClass('is-invalid');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error(App.Lang.fields_are_required);
            }

            return true;
        } catch (error) {
            $('#categories .form-message').addClass('alert-danger').text(error.message).show();
            return false;
        }
    }

    /**
     * Bring the category form back to its initial state.
     */
    function resetForm() {
        $('#filter-categories .selected').removeClass('selected');
        $('#filter-categories button').prop('disabled', false);
        $('#filter-categories .results').css('color', '');

        $('#categories .add-edit-delete-group').show();
        $('#categories .save-cancel-group').hide();
        $('#categories .record-details').find('input, select, textarea').val('').prop('disabled', true);
        $('#edit-category, #delete-category').prop('disabled', true);

        $('#categories .record-details .is-invalid').removeClass('is-invalid');
        $('#categories .record-details .form-message').hide();
    }

    /**
     * Get the filter results row HTML code.
     *
     * @param {Object} category Contains the category data.
     *
     * @return {String} Returns the record HTML code.
     */
    function getFilterHtml(category) {
        return $('<div/>', {
            'class': 'category-row entry',
            'data-id': category.id,
            'html': [
                $('<strong/>', {
                    'text': category.name
                }),
                $('<br/>')
            ]
        });
    }

    /**
     * Select a specific record from the current filter results.
     *
     * If the category ID does not exist in the list then no record will be selected.
     *
     * @param {Number} id The record ID to be selected from the filter results.
     * @param {Boolean} show Optional (false), if true then the method will display the record
     * on the form.
     */
    function select(id, show = false) {
        $('#filter-categories .selected').removeClass('selected');

        $('#filter-categories .category-row[data-id="' + id + '"]').addClass('selected');

        if (show) {
            const category = filterResults.find(
                function (category) {
                    return Number(category.id) === Number(id);
                }.bind(this)
            );

            display(category);

            $('#edit-category, #delete-category').prop('disabled', false);
        }
    }

    function init() {
        resetForm();
        filter('');
        bindEventHandlers();
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
