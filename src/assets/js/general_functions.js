/**
 * This file contains the General Functions javascript namespace.
 * It contains functions that apply both on the front and back 
 * end of the application.
 * 
 * @namespace GeneralFunctions
 */
var GeneralFunctions = {
    /**
     * General Functions Constants
     */
    EXCEPTIONS_TITLE: EALang['unexpected_issues'],
    EXCEPTIONS_MESSAGE: EALang['unexpected_issues_message'],
    WARNINGS_TITLE: EALang['unexpected_warnings'],
    WARNINGS_MESSAGE: EALang['unexpected_warnings_message'],
    
    /**
     * This functions displays a message box in
     * the admin array. It is usefull when user
     * decisions or verifications are needed.
     *  
     * @param {string} title The title of the message box.
     * @param {string} message The message of the dialog.
     * @param {array} messageButtons Contains the dialog  
     * buttons along with their functions.
     */
    displayMessageBox: function(title, message, messageButtons) {
        // Check arguments integrity.
        if (title == undefined || title == "") {
            title = "<No Title Given>";
        }   

        if (message == undefined || message == "") {
            message = "<No Message Given>";
        } 

        if (messageButtons == undefined) {
            messageButtons = {};
            messageButtons[EALang['close']] = function() {
                jQuery("#message_box").dialog("close");
            };
        }

        // Destroy previous dialog instances.
        jQuery("#message_box").dialog("destroy");
        jQuery("#message_box").remove();

        // Create the html of the message box.
        jQuery("body").append(
            "<div id='message_box' title='" + title + "'>" +
            "<p>" + message + "</p>" +
            "</div>"
        );    

        jQuery("#message_box").dialog({
            autoOpen: false,
            modal: true,
            resize: "auto",
            width: 400,
            height: "auto",
            resizable: false,
            buttons: messageButtons,
            closeOnEscape: true
        });

        jQuery("#message_box").dialog("open"); 
        jQuery(".ui-dialog .ui-dialog-buttonset button").addClass('btn');
        jQuery("#message_box .ui-dialog-titlebar-close").hide();
    },

    /**
     * This method centers a DOM element vertically and horizontally
     * on the page.
     * 
     * @param {object} elementHandle The object that is going to be 
     * centered.
     */
    centerElementOnPage: function(elementHandle) {
        // Center main frame vertical middle
        $(window).resize(function() {
            var elementLeft = ($(window).width() - elementHandle.outerWidth()) / 2;
            var elementTop = ($(window).height() - elementHandle.outerHeight()) / 2;
            elementTop = (elementTop > 0 ) ? elementTop : 20;

            elementHandle.css({
                position: 'absolute',
                left: elementLeft,
                top: elementTop
            }); 
        });
        $(window).resize();
    },

    /**
     * This function retrieves a parameter from a "GET" formed url. 
     * 
     * @link http://www.netlobo.com/url_query_string_javascript.html
     * 
     * @param {string} url The selected url.
     * @param {string} name The parameter name.
     * @returns {String} Returns the parameter value.         
     */
    getUrlParameter: function(url, parameterName) {
       parameterName = parameterName.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
       var regexS = "[\\#&]"+parameterName+"=([^&#]*)";
       var regex = new RegExp( regexS );
       var results = regex.exec( url );
       if( results == null )
           return "";
       else
           return results[1];
    },

    /**
     * This function creates a RFC 3339 date string. This string is needed
     * by the Google Calendar API in order to pass dates as parameters.
     * 
     * @param {date} dt The given date that will be transformed
     * @returns {String} Returns the transformed string.
     */
    ISODateString: function(dt){
        function pad(n){return n<10 ? '0'+n : n;}

        return dt.getUTCFullYear()+'-'
             + pad(dt.getUTCMonth()+1)+'-'
             + pad(dt.getUTCDate())+'T'
             + pad(dt.getUTCHours())+':'
             + pad(dt.getUTCMinutes())+':'
             + pad(dt.getUTCSeconds())+'Z';
    },
    
    /**
     * This method creates and returns an exact copy of the provided object. 
     * It is very usefull whenever changes need to be made to an object without
     * modyfing the original data.
     * 
     * @link http://stackoverflow.com/questions/728360/most-elegant-way-to-clone-a-javascript-object
     * 
     * @param {object} originalObject Object to be copied.
     * @returns {object} Returns an exact copy of the provided element.
     */
    clone: function(originalObject) {
        // Handle the 3 simple types, and null or undefined
        if (null == originalObject || "object" != typeof originalObject) 
            return originalObject;

        // Handle Date
        if (originalObject instanceof Date) {
            var copy = new Date();
            copy.setTime(originalObject.getTime());
            return copy;
        }

        // Handle Array
        if (originalObject instanceof Array) {
            var copy = [];
            for (var i = 0, len = originalObject.length; i < len; i++) {
                copy[i] = GeneralFunctions.clone(originalObject[i]);
            }
            return copy;
        }

        // Handle Object
        if (originalObject instanceof Object) {
            var copy = {};
            for (var attr in originalObject) {
                if (originalObject.hasOwnProperty(attr)) 
                    copy[attr] = GeneralFunctions.clone(originalObject[attr]);
            }
            return copy;
        }

        throw new Error("Unable to copy obj! Its type isn't supported.");
    },
    
    /**
     * This method validates an email address. If the address is not on the proper
     * form then the result is FALSE.
     * 
     * @param {string} email The email address to be checked.
     * @returns {bool} Returns the validation result.
     */
    validateEmail: function(email) {
        var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        return reg.test(email);
    },
           
    /**
     * This method returns the exception html display for javascript ajax calls.
     * It uses the Bootstrap collapse module to show exception messages when the
     * user opens the "Details" collapse component.
     * 
     * @param {array} exceptions Contains the exceptions to be displayed.
     * @returns {string} Returns the html markup for the exceptions.
     */
    exceptionsToHtml: function(exceptions) {
        var html = 
                '<div class="accordion" id="error-accordion">' + 
                    '<div class="accordion-group">' + 
                        '<div class="accordion-heading">' +
                            '<a class="accordion-toggle" data-toggle="collapse" ' +
                                    'data-parent="#error-accordion" href="#error-technical">' + 
                                EALang['details'] + 
                            '</a>' +
                        '</div>';
        
        $.each(exceptions, function(index, exception) {
            html +=
                    '<div id="error-technical" class="accordion-body collapse">' +
                        '<div class="accordion-inner">' + 
                            '<pre>' + exception['message'] + '</pre>' +
                        '</div>' +
                    '</div>';
        });
        
        html += '</div></div>';
        
        return html;
    },
            
    /**
     * This method parse the json encoded strings that are fetched by ajax calls.
     * 
     * @param {array} exceptions Exception array returned by an ajax call.
     * @returns {array} Returns the parsed js objects.
     */
    parseExceptions: function(exceptions) {
        var parsedExceptions = new Array();
        
        $.each(exceptions, function(index, exception) {
            parsedExceptions.push(jQuery.parseJSON(exception));
        });
        
        return parsedExceptions;
    },
       
    /**
     * Makes the first letter of the string upper case.
     * 
     * @param {string} str The string to be converted.
     * @returns {string} Returns the capitalized string.
     */
    ucaseFirstLetter: function(str){
        return str.charAt(0).toUpperCase() + str.slice(1);
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
            GeneralFunctions.displayMessageBox(GeneralFunctions.EXCEPTIONS_TITLE, GeneralFunctions.EXCEPTIONS_MESSAGE);
            $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.exceptions));
            return false;
        }

        if (response.warnings) {
            response.warnings = GeneralFunctions.parseExceptions(response.warnings);
            GeneralFunctions.displayMessageBox(GeneralFunctions.WARNINGS_TITLE, GeneralFunctions.WARNINGS_MESSAGE);
            $('#message_box').append(GeneralFunctions.exceptionsToHtml(response.warnings));
        }
        
        return true;
    },
    
    /**
     * Enables the language selection functionality. Must be called on every page has a
     * language selection button. This method requires the global variable 'availableLanguages'
     * to be initialized before the execution.
     * 
     * @param {object} $element Selected element button for the language selection.
     */
    enableLanguageSelection: function($element) {
    	// Select Language
        var html = '<ul id="language-list">';
        $.each(availableLanguages, function() {
        	html += '<li class="language" data-language="' + this + '">' 
        			+ GeneralFunctions.ucaseFirstLetter(this) + '</li>';
        });
        html += '</ul>';
        
        $element.popover({
            'placement': 'top',
            'title': 'Select Language',
            'content': html,
            'html': true,
            'container': 'body',
            'trigger': 'manual'
        });
        
        $element.click(function() {
        	if ($('#language-list').length == 0) {
        		$(this).popover('show');
        	} else {
        		$(this).popover('hide');
        	}
        });
        
        $(document).on('click', 'li.language', function() {
        	// Change language with ajax call and refresh page.
        	var postUrl = GlobalVariables.baseUrl + 'backend_api/ajax_change_language';
        	var postData = { 'language': $(this).attr('data-language') };
        	$.post(postUrl, postData, function(response) {
        		////////////////////////////////////////////////////
        		console.log('Change Language Response', response);
    			////////////////////////////////////////////////////
        		
        		if (!GeneralFunctions.handleAjaxExceptions(response)) return;
        		document.location.reload(true);
        		
        	}, 'json');
        });
    }
};