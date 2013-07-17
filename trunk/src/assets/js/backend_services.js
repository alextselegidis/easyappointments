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
            var option = new Option(category.name, category.value);
            $('#service-category').append(option);
        });
        $('#service-category').append(new Option('- No Category -', null)).val('null');
        
        // Instantiate helper object (service helper by default).
        BackendServices.helper = new ServicesHelper();
        BackendServices.helper.resetForm();
        BackendServices.helper.filter('');
        
        $('#service-duration').spinner({
            'min': 0,
            'numberFormat': 'n Minutes'
        });
        
        
        if (bindEventHandlers) BackendServices.bindEventHandlers();        
    },
        
    /**
     * Binds the default event handlers of the backend services page. Do not use this method
     * if you include the "BackendServices" namespace on another page.
     * 
     * @returns {undefined}
     */
    bindEventHandlers: function() {
        /**
         * Event: Page Tab Button "Click"
         * 
         * Changes the displayed tab.
         */
        $('.tab').click(function() {
            $('.active').removeClass('active');
            $(this).addClass('active');
            $('.tab-content').hide();
            
            if ($(this).hasClass('services-tab')) { // display services tab
                $('#services').show();
                BackendServices.helper = new ServicesHelper();
            } else if ($(this).hasClass('categories-tab')) { // display categories tab
                $('#categories').show();
                BackendServices.helper = new CategoriesHelper();
            }
        });
        
        /**
         * Event: Filter Services Button "Click"
         */
        $(' .filter-services').click(function() {
            var key = $('#services .filter-key').val();
            $('.selected-row').removeClass('selected-row');
            BackendServices.helper.resetForm();
            BackendServices.helper.filter(key);
        });
        
        /**
         * Event: Filter Service Row "Click"
         * 
         * Display the selected service data to the user.
         */
        $(document).on('click', '.service-row', function() {
            var service = { 'id': $(this).attr('data-id') };
            
            $.each(BackendServices.helper.filterResults, function(index, item) {
                if (item.id === service.id) {
                    service = item;
                    return;
                }
            });
            
            BackendServices.helper.display(service);
            
            $('.selected-row').removeClass('selected-row');
            $(this).addClass('selected-row');
        });
    }
};

/**
 * This class contains the methods that will be used by the "Services" tab of the page.
 * @class ServicesHelper
 */
var ServicesHelper = function() {
    this.filterResults = {};
};

/**
 * Save service record to database.
 * 
 * @param {object} service Contains the service record data. If an 'id' value is provided
 * then the update operation is going to be executed.
 */
ServicesHelper.prototype.save = function(service) {
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_save_service';
    var postData = { 'service': JSON.stringify(service) };
    
    $.post(postUrl, postData, function(response) {
        console.log('Save Service Response:', response);
        if (!Backend.handleAjaxExceptions(response)) return;
        
        $('#services .add-edit-delete-group').show();
        $('#services .save-cancel-group').hide();
    });
};

/**
 * Delete a service records from database.
 * 
 * @param {int} id Record id to be deleted. 
 */
ServicesHelper.prototype.delete = function(id) {
    
};

/**
 * Validates a service record.
 * 
 * @param {object} service Contains the service data.
 * @returns {bool} Returns the validation result.
 */
ServicesHelper.prototype.validate = function(service) {
    
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
};

/**
 * Display a service record into the service form.
 * 
 * @param {object} service Contains the service record data.
 */
ServicesHelper.prototype.display = function(service) {
    $('#service-id').val(service.id);
    $('#service-name').val(service.name);
    $('#service-duration').val(service.duration + ' min');
    $('#service-price').val(service.price);
    $('#service-currency').val(service.currency);
    $('#service-description').val(service.description);
    $('#service-category').val(service.id_service_categories);
};

/**
 * Filters service records depending a string key.
 * 
 * @param {string} key This is used to filter the service records of the database.
 */
ServicesHelper.prototype.filter = function(key) {
    var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_filter_services';
    var postData = { 'key': key };
    
    $.post(postUrl, postData, function(response) {
        /////////////////////////////////////////////////////
        console.log('Filter services response:', response);
        /////////////////////////////////////////////////////
        
        if (!Backend.handleAjaxExceptions(response)) return;

        BackendServices.helper.filterResults = response;
        $('#services .filter-results').html('');
        
        $.each(response, function(index, service) {
            var html = ServicesHelper.prototype.getFilterHtml(service);
            $('#services .filter-results').append(html);
        });
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
            '</div>';

    return html;
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