/**
 * This namespace handles the js functionality of the backend services page.
 * 
 * @namespace BackendServices
 */
var BackendServices = {
    /**
     * Contains the basic record methods for the page.
     * 
     * @type ServicesHelper|CategoriesHelper
     */
    helper: {},
    
    /**
     * Default initialize method of the page.
     * 
     * @param {bool} bindEventHandlers (OPTIONAL) Determines whether to bind the 
     * default event handlers (default: true).
     */
    initialize: function(bindEventHandlers) {
        if (bindEventHandlers === undefined) bindEventHandlers = true;
        
        // Fill available service categories listbox.
        $.each(GlobalVariables.categories, function(index, category) {
            var option = new Option(category.name, category.id);
            $('#service-category').append(option);
        });
        $('#service-category').append(new Option('- ' + EALang['no_category'] + ' -', null)).val('null');
        
        $('#service-duration').spinner({
            'min': 0,
            'disabled': true //default
        });
        
        // Instantiate helper object (service helper by default).
        BackendServices.helper = new ServicesHelper();
        BackendServices.helper.resetForm();
        BackendServices.helper.filter('');
        
        $('#filter-services .results').jScrollPane();
        $('#filter-categories .results').jScrollPane();
        
        if (bindEventHandlers) BackendServices.bindEventHandlers();        
    },
        
    /**
     * Binds the default event handlers of the backend services page. Do not use this method
     * if you include the "BackendServices" namespace on another page.
     */
    bindEventHandlers: function() {
        /**
         * Event: Page Tab Button "Click"
         * 
         * Changes the displayed tab.
         */
        $('.tab').click(function() {
            $(this).parent().find('.active').removeClass('active');
            $(this).addClass('active');
            $('.tab-content').hide();
            
            if ($(this).hasClass('services-tab')) { // display services tab
                $('#services').show();
                BackendServices.helper = new ServicesHelper();
            } else if ($(this).hasClass('categories-tab')) { // display categories tab
                $('#categories').show();
                BackendServices.helper = new CategoriesHelper();
            }
            
            BackendServices.helper.resetForm();
            BackendServices.helper.filter('');
            $('.filter-key').val('');
            Backend.placeFooterToBottom();
        });
        
        ServicesHelper.prototype.bindEventHandlers();
        CategoriesHelper.prototype.bindEventHandlers();
        
    },
    
    /**
     * Update the service category listbox. Use this method every time a change is made
     * to the service categories db table.
     */
    updateAvailableCategories: function() {
        var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_filter_service_categories';
        var postData = { 'key': '' };
        
        $.post(postUrl, postData, function(response) {
            ///////////////////////////////////////////////////////////////
            console.log('Update Available Categories Response:', response);
            ///////////////////////////////////////////////////////////////
            
            if (!GeneralFunctions.handleAjaxExceptions(response)) return;
            
            GlobalVariables.categories = response;
            var $select = $('#service-category');
            $select.empty();
            $.each(response, function(index, category) {
                var option = new Option(category.name, category.id);
                $select.append(option);
            });
            $select.append(new Option('- ' + EALang['no_category'] + ' -', null)).val('null');
        }, 'json');
    }
};

/**
 * This class contains the methods that will be used by the "Services" tab of the page.
 * @class ServicesHelper
 */
var ServicesHelper = function() {
    this.filterResults = {};
};

ServicesHelper.prototype.bindEventHandlers = function() {
    /**
     * Event: Filter Services Form "Submit"
     */
    $('#filter-services form').submit(function(event) {
        var key = $('#filter-services .key').val();
        $('#filter-services .selected-row').removeClass('selected-row');
        BackendServices.helper.resetForm();
        BackendServices.helper.filter(key);
        return false;
    });

    /**
     * Event: Filter Service Cancel Button "Click"
     */
    $('#filter-services .clear').click(function() {
        $('#filter-services .key').val('');
        BackendServices.helper.filter('');
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

        var serviceId = $(this).attr('data-id'); 
        var service = {};
        $.each(BackendServices.helper.filterResults, function(index, item) {
            if (item.id === serviceId) {
                service = item;
                return false;
            }
        });

        BackendServices.helper.display(service);
        $('#filter-services .selected-row').removeClass('selected-row');
        $(this).addClass('selected-row');
        $('#edit-service, #delete-service').prop('disabled', false);
    });

    /**
     * Event: Add New Service Button "Click"
     */
    $('#add-service').click(function() {
        BackendServices.helper.resetForm();
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
        BackendServices.helper.resetForm();
        if (id != '') {
            BackendServices.helper.select(id, true);
        }
    });

    /**
     * Event: Save Service Button "Click"
     */
    $('#save-service').click(function() {
        var service = {
            'name': $('#service-name').val(),
            'duration': $('#service-duration').val(),
            'price': $('#service-price').val(),
            'currency': $('#service-currency').val(),
            'description': $('#service-description').val()
        };

        if ($('#service-category').val() !== 'null') {
            service.id_service_categories = $('#service-category').val();
        } else {
            service.id_service_categories = null;
        }

        if ($('#service-id').val() !== '') {
            service.id = $('#service-id').val();
        }

        if (!BackendServices.helper.validate(service)) return;

        BackendServices.helper.save(service);
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
        var serviceId = $('#service-id').val();

        var messageBtns = {};
        messageBtns[EALang['delete']] = function() {
            BackendServices.helper.delete(serviceId);
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
    ////////////////////////////////////////////////
    //console.log('Service data to save:', service);
    ////////////////////////////////////////////////
    
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_save_service';
    var postData = { 'service': JSON.stringify(service) };
    
    $.post(postUrl, postData, function(response) {
        //////////////////////////////////////////////////
        //console.log('Save Service Response:', response);
        //////////////////////////////////////////////////
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        
        Backend.displayNotification(EALang['service_saved']);
        BackendServices.helper.resetForm();
        $('#filter-services .key').val('');
        BackendServices.helper.filter('', response.id, true);
    }, 'json');
};

/**
 * Delete a service record from database.
 * 
 * @param {numeric} id Record id to be deleted. 
 */
ServicesHelper.prototype.delete = function(id) {
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_delete_service';
    var postData = { 'service_id': id };
    
    $.post(postUrl, postData, function(response) {
        ////////////////////////////////////////////////////
        //console.log('Delete service response:', response);
        ////////////////////////////////////////////////////
        
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        
        Backend.displayNotification(EALang['service_deleted']);
        
        BackendServices.helper.resetForm();
        BackendServices.helper.filter($('#filter-services .key').val());
    }, 'json');
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
        
        if (missingRequired) 
            throw EALang['fields_are_required'];
        
        
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
    
    var categoryId = (service.id_service_categories != null) ? service.id_service_categories : 'null';
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
    if (display == undefined) display = false;
    
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_filter_services';
    var postData = { 'key': key };
    
    $.post(postUrl, postData, function(response) {
        /////////////////////////////////////////////////////
        //console.log('Filter services response:', response);
        /////////////////////////////////////////////////////
        
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;

        BackendServices.helper.filterResults = response;
        
        $('#filter-services .results').data('jsp').destroy();
        $('#filter-services .results').html('');
        $.each(response, function(index, service) {
            var html = ServicesHelper.prototype.getFilterHtml(service);
            $('#filter-services .results').append(html);
        });        
        $('#filter-services .results').jScrollPane({ mouseWheelSpeed: 70 });
        
        if (response.length == 0) {
            $('#filter-services .results').html('<em>' + EALang['no_records_found'] + '</em>');
        }
        
        if (selectId != undefined) {
            BackendServices.helper.select(selectId, display);
        }
    }, 'json');
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
    if (display == undefined) display = false;
    
    $('#filter-services .selected-row').removeClass('selected-row');
    
    $('#filter-services .service-row').each(function() {
        if ($(this).attr('data-id') == id) {
            $(this).addClass('selected-row');
            return false;
        }
    });
    
    if (display) { 
        $.each(BackendServices.helper.filterResults, function(index, service) {
            if (service.id == id) {
                BackendServices.helper.display(service);
                $('#edit-service, #delete-service').prop('disabled', false);
                return false;
            }
        });
    }
};

/**
 * This class contains the core method implementations that belong to the categories tab
 * of the backend services page.
 * 
 * @class CategoriesHelper
 */
var CategoriesHelper = function() {
    this.filterResults = {};
};

/**
 * Binds the default event handlers of the categories tab.
 */
CategoriesHelper.prototype.bindEventHandlers = function() {
    /**
     * Event: Filter Categories Cancel Button "Click"
     */
    $('#filter-categories .clear').click(function() {
        $('#filter-categories .key').val('');
        BackendServices.helper.filter('');
    });

    /**
     * Event: Filter Categories Form "Submit"
     */
    $('#filter-categories form').submit(function(event) {
        var key = $('#filter-categories .key').val();
        $('.selected-row').removeClass('selected-row');
        BackendServices.helper.resetForm();
        BackendServices.helper.filter(key);
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
        $.each(BackendServices.helper.filterResults, function(index, item) {
            if (item.id === categoryId) {
                category = item;
                return false;
            }
        });

        BackendServices.helper.display(category);
        $('#filter-categories .selected-row').removeClass('selected-row');
        $(this).addClass('selected-row');
        $('#edit-category, #delete-category').prop('disabled', false);
    });
    
    /**
     * Event: Add Category Button "Click"
     */
    $('#add-category').click(function() {
        BackendServices.helper.resetForm();
        $('#categories .add-edit-delete-group').hide();
        $('#categories .save-cancel-group').show();
        $('#categories .details').find('input, textarea').prop('readonly', false);
        $('#filter-categories button').prop('disabled', true);
        $('#filter-categories .results').css('color', '#AAA');
    });
    
    /**
     * Event: Edit Category Button "Click"
     */
    $('#edit-category').click(function() {
        $('#categories .add-edit-delete-group').hide();
        $('#categories .save-cancel-group').show();
        $('#categories .details').find('input, textarea').prop('readonly', false);

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
            BackendServices.helper.delete(categoryId);
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
            'name': $('#category-name').val(),
            'description': $('#category-description').val()
        };

        if ($('#category-id').val() !== '') {
            category.id = $('#category-id').val();
        }

        if (!BackendServices.helper.validate(category)) return;

        BackendServices.helper.save(category);
    });
    
    /**
     * Event: Cancel Category Button "Click"
     */
    $('#cancel-category').click(function() {
        var id = $('#category-id').val();
        BackendServices.helper.resetForm();
        if (id != '') {
            BackendServices.helper.select(id, true);
        }
    });
};

/**
 * Filter service categories records.
 * 
 * @param {string} key This key string is used to filter the category records.
 * @param {numeric} selectId (OPTIONAL = undefined) If set then after the filter 
 * operation the record with the given id will be selected (but not displayed).
 * @param {bool} display (OPTIONAL = false) If true then the selected record will
 * be displayed on the form.
 */
CategoriesHelper.prototype.filter = function(key, selectId, display) {
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_filter_service_categories';
    var postData = { 'key': key };
    
    $.post(postUrl, postData, function(response) {
        ///////////////////////////////////////////////////////
        console.log('Filter Categories Response:', response);
        ///////////////////////////////////////////////////////
        
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        
        BackendServices.helper.filterResults = response;
        
        $('#filter-categories .results').data('jsp').destroy();
        $('#filter-categories .results').html('');
        $.each(response, function(index, category) {
           var html = BackendServices.helper.getFilterHtml(category);
           $('#filter-categories .results').append(html);
        });
        $('#filter-categories .results').jScrollPane({ mouseWheelSpeed: 70 });
        
        if (response.length == 0) {
            $('#filter-categories .results').html('<em>' + EALang['no_records_found'] + '</em>');
        }
        
        if (selectId != undefined) {
            BackendServices.helper.select(selectId, display);
        }
        
    }, 'json');
};

/**
 * Save a category record to the database (via ajax post).
 * 
 * @param {object} category Contains the category data.
 */
CategoriesHelper.prototype.save = function(category) {
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_save_service_category';
    var postData = { 'category': JSON.stringify(category) };
    
    $.post(postUrl, postData, function(response) {
        ///////////////////////////////////////////////////////////
        console.log('Save Service Category Response:', response);
        ///////////////////////////////////////////////////////////
        
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        
        Backend.displayNotification(EALang['service_category_saved']);
        BackendServices.helper.resetForm();
        $('#filter-categories .key').val('');
        BackendServices.helper.filter('', response.id, true);
        BackendServices.updateAvailableCategories();
    }, 'json');
};

/**
 * Delete category record.
 * 
 * @param {int} id Record id to be deleted.
 */
CategoriesHelper.prototype.delete = function(id) {
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_delete_service_category';
    var postData = { 'category_id': id };
    
    $.post(postUrl, postData, function(response) {
        ////////////////////////////////////////////////////
        console.log('Delete category response:', response);
        ////////////////////////////////////////////////////
        
        if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        
        Backend.displayNotification(EALang['service_category_deleted']);
        
        BackendServices.helper.resetForm();
        BackendServices.helper.filter($('#filter-categories .key').val());
        BackendServices.updateAvailableCategories();
    }, 'json');
};

/**
 * Display a category record on the form.
 * 
 * @param {object} category Contains the category data.
 */
CategoriesHelper.prototype.display = function(category) {
    $('#category-id').val(category.id);
    $('#category-name').val(category.name);
    $('#category-description').val(category.description);
};

/**
 * Validate category data before save (insert or update).
 * 
 * @param {object} category Contains the category data.
 */
CategoriesHelper.prototype.validate = function(category) {
    $('#categories .details').find('input, textarea').css('border', '');
    
    try {
        var missingRequired = false;
        $('#categories .required').each(function() {
            if ($(this).val() == '' || $(this).val() == undefined) {
                $(this).css('border', '2px solid red');
                missingRequired = true;
            }
        });
        if (missingRequired) throw EALang['fields_are_required'];
        
        return true;
        
    } catch(exc) {
        console.log('Category Record Validation Exc:', exc);
        return false;
    }
};

/**
 * Bring the category form back to its initial state.
 */
CategoriesHelper.prototype.resetForm = function() {
    $('#categories .add-edit-delete-group').show();
    $('#categories .save-cancel-group').hide();
    $('#categories .details').find('input, textarea').val('');
    $('#categories .details').find('input, textarea').prop('readonly', true);
    $('#edit-category, #delete-category').prop('disabled', true);
    
    $('#filter-categories .selected-row').removeClass('selected-row');
    $('#filter-categories .results').css('color', '');
    $('#filter-categories button').prop('disabled', false);
};

/**
 * Get the filter results row html code.
 * 
 * @param {object} category Contains the category data.
 * @return {string} Returns the record html code.
 */
CategoriesHelper.prototype.getFilterHtml = function(category) {    
    var html =
            '<div class="category-row" data-id="' + category.id + '">' + 
                '<strong>' + category.name + '</strong>' +
            '</div><hr>';

    return html;
};

/**
 * Select a specific record from the current filter results. If the category id does not exist 
 * in the list then no record will be selected. 
 * 
 * @param {numeric} id The record id to be selected from the filter results.
 * @param {bool} display (OPTIONAL = false) If true then the method will display the record
 * on the form.
 */
CategoriesHelper.prototype.select = function(id, display) {
    if (display == undefined) display = false;
    
    $('#filter-categories .selected-row').removeClass('selected-row');
    
    $('#filter-categories .category-row').each(function() {
        if ($(this).attr('data-id') == id) {
            $(this).addClass('selected-row');
            return false;
        }
    });
    
    if (display) { 
        $.each(BackendServices.helper.filterResults, function(index, category) {
            if (category.id == id) {
                BackendServices.helper.display(category);
                $('#edit-category, #delete-category').prop('disabled', false);
                return false;
            }
        });
    }
};