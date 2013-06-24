/**
 * This file contains the General Functions javascript namespace.
 * It contains functions that apply both on the front and back 
 * end of the application.
 * 
 * @namespace GeneralFunctions
 */
var GeneralFunctions = {
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
            messageButtons = {
                Close: function() {
                    jQuery("#message_box").dialog("close");
                }
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
            autoOpen        : false,
            modal           : true,
            resize          : "auto",
            width           : 400,
            height          : "auto",
            resizable       : false,
            buttons         : messageButtons,
            closeOnEscape   : false
        });

        jQuery("#message_box").dialog("open"); 
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
                position    : 'absolute',
                left        : elementLeft,
                top         : elementTop
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
    }
};