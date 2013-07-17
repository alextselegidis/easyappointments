/**
 * Main javascript code for the backend of Easy!Appointments.
 */
$(document).ready(function() {
    $(window).resize(function() {
        Backend.placeFooterToBottom();
    }).trigger('resize');
    
    $(document).ajaxStart(function() {
        $('#loading').show();
    });
    
    $(document).ajaxStop(function() {
        $('#loading').hide();
    });
});

/**
 * This namespace contains functions that are used in the backend section of
 * the applications.
 * 
 * @namespace Backend
 */
var Backend = {
    /**
     * Backend Constants
     */
    EXCEPTIONS_TITLE: 'Unexpected Issues',
    EXCEPTIONS_MESSAGE: 'The operation could not complete due to unexpected issues. ',
    WARNINGS_TITLE: 'Unexpected Warnings',
    WARNINGS_MESSAGE: 'The operation completed but some warnings appeared. ',
    
    /**
     * Place the backend footer always on the bottom of the page.
     */
    placeFooterToBottom: function() {
        var $footer = $('#footer');

        if (window.innerHeight > $('body').height()) {
            $footer.css({
                'position': 'absolute',
                'width': '100%',
                'bottom': '0px'
            });
        } else {
            $footer.css({
                'position': 'static'
            });
        }
    },

    /**
     * Display backend notifications to user. 
     * 
     * Using this method you can display notifications to the use with custom
     * messages. If the 'actions' array is provided then an action link will 
     * be displayed too.
     * 
     * @param {string} message Notification message
     * @param {array} actions An array with custom actions that will be available
     * to the user. Every array item is an object that contains the 'label' and 
     * 'function' key values.
     */
    displayNotification: function(message, actions) {
        if (message === undefined) {
            message = 'NO MESSAGE PROVIDED FOR THIS NOTIFICATION';
        }
        
        if (actions === undefined) {
            actions = [];
        }
        
        var notificationHtml = 
                '<div class="notification alert">' + 
                '<strong>' + message + '</strong>';
        
        $.each(actions, function(index, action) {
            var actionId = action['label'].toLowerCase().replace(' ', '-');
            notificationHtml += '<button id="' + actionId + '" class="btn">' 
                    + action['label'] + '</button>';
            
            $(document).off('click', '#' + actionId);
            $(document).on('click', '#' + actionId, action['function']);
        });
        
        notificationHtml += '</div>';
        
        $('#notification').html(notificationHtml);
        $('#notification').show('blind');
    },
            
    /**
     * All backend js code has the same way of dislaying exceptions that are raised on the
     * server during an ajax call. 
     * 
     * @param {object} response Contains the server response. If exceptions or warnings are 
     * found, user friendly messages are going to be displayed to the user.
     * @returns {bool} Returns whether the the ajax callback should continue the execution or
     * stop, due to critical server exceptions.
     */
    handleAjaxExceptions: function(response) {
        if (response.exceptions) {
            response.exceptions = GeneralFunctions.parseExceptions(response.exceptions);
            GeneralFunctions.displayMessageBox(Backend.EXCEPTIONS_TITLE, Backend.EXCEPTIONS_MESSAGE);
            $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.exceptions));
            return false;
        }

        if (response.warnings) {
            response.warnings = GeneralFunctions.parseExceptions(response.warnings);
            GeneralFunctions.displayMessageBox(Backend.WARNINGS_TITLE, Backend.WARNINGS_MESSAGE);
            $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.warnings));
        }
        
        return true;
    }
};