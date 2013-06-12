/**
 * This namespace contains functions that are used by the backend calendar page.
 * 
 * @namespace Handles the backend calendar page functionality.
 */
var BackendCalendar = {
    /**
     * This function makes the necessary initialization for the default backend
     * calendar page. If this namespace is used in another page then this function
     * might not be needed.
     * 
     * @param {bool} defaultEventHandlers (OPTIONAL = TRUE) Determines whether the
     * default event handlers will be set for the current page.
     */
    initialize: function(defaultEventHandlers) {
        if (defaultEventHandlers === undefined) defaultEventHandlers = true;
        
        // :: INITIALIZE THE DOM ELEMENTS OF THE PAGE
        $('#calendar').fullCalendar({
            defaultView     : 'basicWeek',
            height          : BackendCalendar.getCalendarHeight(),
            windowResize    : function(view) {
                $('#calendar').fullCalendar('option', 'height', 
                        BackendCalendar.getCalendarHeight());
            }
        });
        
        // :: FILL THE SELECT ELEMENTS OF THE PAGE
        $.each(GlobalVariables.availableProviders, function(index, provider) {
            var option = new Option(provider['first_name'] + ' ' 
                    + provider['last_name'], provider['id']);
            $('#select-provider').append(option);
        });
        
        $.each(GlobalVariables.availableServices, function(index, service) {
            var option = new Option(service['name'], service['id']);
            $('#select-service').append(option);
        });
        
        // :: BIND THE DEFAULT EVENT HANDLERS
        if (defaultEventHandlers === true) {
            BackendCalendar.bindEventHandlers();
        }
    },
    
    /**
     * This method binds the default event handlers for the backend calendar
     * page. If you do not need the default handlers then initialize the page
     * by setting the "defaultEventHandlers" argument to "false".
     */
    bindEventHandlers: function() {
        
    },
            
    /**
     * This method calculates the proper calendar height, in order to be displayed
     * correctly, even when the browser window is resizing.
     * 
     * @return {int} Returns the calendar element height in pixels.
     */
    getCalendarHeight: function () {
        var result = window.innerHeight - $('#footer').height() - $('#header').height() 
                - $('#calendar-toolbar').height() - 80; // 80 for fine tuning
        return (result > 500) ? result : 500; // Minimum height is 500px
    }
};