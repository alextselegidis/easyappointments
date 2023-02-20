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
 * Categories page.
 *
 * This module implements the functionality of the categories page.
 */
App.Pages.Categories = (function () {
    const $categories = $('#categories');
    const $filterCategories = $('#filter-categories');
    const $id = $('#id');
    const $name = $('#name');
    const $description = $('#description');
    let filterResults = {};
    let filterLimit = 20;

    /**
     * Add the page event listeners.
     */
    function addEventListeners() {
        /**
         * Event: Filter Categories Form "Submit"
         *
         * @param {jQuery.Event} event
         */
        $categories.on('submit', '#filter-categories form', (event) => {
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
         *
         * @param {jQuery.Event} event
         */
        $categories.on('click', '.category-row', (event) => {
            if ($('#filter-categories .filter').prop('disabled')) {
                $('#filter-categories .results').css('color', '#AAA');
                return; // exit because we are on edit mode
            }

            const categoryId = $(event.currentTarget).attr('data-id');

            const category = filterResults.find((filterResult) => Number(filterResult.id) === Number(categoryId));

            display(category);
            $('#filter-categories .selected').removeClass('selected');
            $(event.currentTarget).addClass('selected');
            $('#edit-category, #delete-category').prop('disabled', false);
        });

        /**
         * Event: Add Category Button "Click"
         */
        $categories.on('click', '#add-category', () => {
            resetForm();
            $categories.find('.add-edit-delete-group').hide();
            $categories.find('.save-cancel-group').show();
            $categories.find('.record-details').find('input, select, textarea').prop('disabled', false);
            $categories.find('.record-details .form-label span').prop('hidden', false);
            $filterCategories.find('button').prop('disabled', true);
            $filterCategories.find('.results').css('color', '#AAA');
        });

        /**
         * Event: Edit Category Button "Click"
         */
        $categories.on('click', '#edit-category', () => {
            $categories.find('.add-edit-delete-group').hide();
            $categories.find('.save-cancel-group').show();
            $categories.find('.record-details').find('input, select, textarea').prop('disabled', false);
            $categories.find('.record-details .form-label span').prop('hidden', false);
            $filterCategories.find('button').prop('disabled', true);
            $filterCategories.find('.results').css('color', '#AAA');
        });

        /**
         * Event: Delete Category Button "Click"
         */
        $categories.on('click', '#delete-category', () => {
            const categoryId = $id.val();

            const buttons = [
                {
                    text: lang('cancel'),
                    click: (event, messageModal) => {
                        messageModal.dispose();
                    }
                },
                {
                    text: lang('delete'),
                    click: (event, messageModal) => {
                        remove(categoryId);
                        messageModal.dispose();
                    }
                }
            ];

            App.Utils.Message.show(lang('delete_category'), lang('delete_record_prompt'), buttons);
        });

        /**
         * Event: Categories Save Button "Click"
         */
        $categories.on('click', '#save-category', () => {
            const category = {
                name: $name.val(),
                description: $description.val()
            };

            if ($id.val() !== '') {
                category.id = $id.val();
            }

            if (!validate()) {
                return;
            }

            save(category);
        });

        /**
         * Event: Cancel Category Button "Click"
         */
        $categories.on('click', '#cancel-category', () => {
            const id = $id.val();
            resetForm();
            if (id !== '') {
                select(id, true);
            }
        });
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

            response.forEach((category) => {
                $('#filter-categories .results').append(getFilterHtml(category)).append($('<hr/>'));
            });

            if (response.length === 0) {
                $('#filter-categories .results').append(
                    $('<em/>', {
                        'text': lang('no_records_found')
                    })
                );
            } else if (response.length === filterLimit) {
                $('<button/>', {
                    'type': 'button',
                    'class': 'btn btn-outline-secondary w-100 load-more text-center',
                    'text': lang('load_more'),
                    'click': () => {
                        filterLimit += 20;
                        filter(keyword, selectId, show);
                    }
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
            App.Layouts.Backend.displayNotification(lang('category_saved'));
            resetForm();
            $filterCategories.find('.key').val('');
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
            App.Layouts.Backend.displayNotification(lang('category_deleted'));
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
        $id.val(category.id);
        $name.val(category.name);
        $description.val(category.description);
    }

    /**
     * Validate category data before save (insert or update).
     *
     * @return {Boolean} Returns the validation result.
     */
    function validate() {
        $categories.find('.is-invalid').removeClass('is-invalid');
        $categories.find('.form-message').removeClass('alert-danger').hide();

        try {
            let missingRequired = false;

            $categories.find('.required').each((index, fieldEl) => {
                if (!$(fieldEl).val()) {
                    $(fieldEl).addClass('is-invalid');
                    missingRequired = true;
                }
            });

            if (missingRequired) {
                throw new Error(lang('fields_are_required'));
            }

            return true;
        } catch (error) {
            $categories.find('.form-message').addClass('alert-danger').text(error.message).show();
            return false;
        }
    }

    /**
     * Bring the category form back to its initial state.
     */
    function resetForm() {
        $filterCategories.find('.selected').removeClass('selected');
        $filterCategories.find('button').prop('disabled', false);
        $filterCategories.find('.results').css('color', '');

        $categories.find('.add-edit-delete-group').show();
        $categories.find('.save-cancel-group').hide();
        $categories.find('.record-details').find('input, select, textarea').val('').prop('disabled', true);
        $categories.find('.record-details .form-label span').prop('hidden', true);
        $('#edit-category, #delete-category').prop('disabled', true);

        $categories.find('.record-details .is-invalid').removeClass('is-invalid');
        $categories.find('.record-details .form-message').hide();
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
     * @param {Boolean} show Optional (false), if true then the method will display the record on the form.
     */
    function select(id, show = false) {
        $filterCategories.find('.selected').removeClass('selected');

        $filterCategories.find('.category-row[data-id="' + id + '"]').addClass('selected');

        if (show) {
            const category = filterResults.find((category) => Number(category.id) === Number(id));

            display(category);

            $('#edit-category, #delete-category').prop('disabled', false);
        }
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        resetForm();
        filter('');
        addEventListeners();
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {
        filter,
        save,
        remove,
        getFilterHtml,
        resetForm,
        select
    };
})();
