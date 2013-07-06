/**
 * Backend Customers javasript namespace. Contains the main functionality 
 * of the backend customers page. If you need to use this namespace in a 
 * different page, do not bind the default event handlers during initialization.
 *
 * @namespace Backend Customers
 */

var BackendCustomers = {
	/**
	 * This method initializes the backend customers page. If you use this namespace
	 * in a different page do not use this method. 
	 * 
	 * @param {bool} bindDefaultEventHandlers Whether to bind the default event handlers
	 * or not.  
	 */
	initialize: function(bindDefaultEventHandlers) {
		if (bindDefaultEventHandlers === undefined) {
			bindDefaultEventHandlers = false; // default value
		}
		
		// :: INITIALIZE BACKEND CUSTOMERS PAGE
		BackendCustomers.filterCustomers('');
		
		// :: BIND DEFAULT EVENT HANDLERS (IF NEEDED)
		if (bindDefaultEventHandlers) {
			BackendCustomers.bindEventHandlers();
		}
	},
    
    /**
     * Default event handlers declaration for backend customers page.
     */
	bindEventHandlers: function() {

	},
	
	/**
	 * This method displays the customer data on the right part of the page. 
	 * When a customer is selected the user can make changes and update the 
	 * customer record.
	 * 
	 * @param {int} customerId Selected customer's record id.
	 */
	displayCustomer: function(customerId) {
		
	},
	
	/**
	 * This method makes an ajax call to the server and save the changes of 
	 * an existing customer record, or inserts a new customer row when on insert 
	 * mode.
	 * 
	 * NOTICE: User the "deleteCustomer" method to delete a customer record.
	 * 
	 * @param {object} customerData Contains the customer data. If "id" is not 
	 * provided then the record is going to be inserted.
	 */
	saveCustomer: function(customerData) {
		
	},
	
	/**
	 * This method makes an ajax call to the server and deletes the selected
	 * customer record.
	 * 
	 * @param {int} customerId The customer record id to be deleted.
	 */
	deleteCustomer: function(customerId) {
		
	},
	
	/**
	 * This method filters the system registered customers. Pass an empty string
	 * to display all customers.
	 * 
	 * @param {string} key The filter key string.
	 */
	filterCustomers: function(key) {
		var postUrl = GlobalVariables.baseUrl + 'backend/ajax_filter_customers';
		var postData = { 'key': key };
		$.post(postUrl, postData, function(response) {
			if (response.exceptions) {
				response.exceptions = GeneralFunctions.parseExcpetions(response.exceptions);
				GeneralFunctions.displayMessageBox('Unexpected Issues', 'Unfortunately the '
						+ 'filter operation could not complete successfully. The following '
						+ 'issues occured.');
				$('#message_box').append(GeneralFunctions.exceptionsToHtml(response.exceptions));
				return;
			}
			
			if (response.warnings) {
				response.warnings = GeneralFunctions.parseExcpetions(response.warnings);
				GeneralFunctions.displayMessageBox('Unexpected Warnings', 'The filter operation '
						+ 'complete with the following warnings.');
				$('#message_box').append(GeneralFunctions.exceptionsToHtml(response.warnings));
			}
			
			$.each(response, function(index, customer) {
				var html = 
					'<div class="customer-data" data-id="' + customer['id'] + '">' +
						'<strong>' + 
							customer['first_name'] + ' ' + customer['last_name'] + 
						'</strong><br>' + 
						'<span>' + customer['email'] + '</span> | ' + 
						'<span>' + customer['phone_number'] + '</span><hr>' + 
					'</div>';
				$('#filter-results').append(html);
			});
		}, 'json');
	},
	
	/**
	 * This method validates the main customer form of the page. There are certain 
	 * rules that the record must fullfil before getting into the system database.
	 * 
	 * @return {bool} Returns the validation result.
	 */
	validateCustomerForm: function() {
		
	}
};