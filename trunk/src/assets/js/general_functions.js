/**
 * This file contains the Genral Functions javascript namespace.
 * It contains functions that apply both on the front and back 
 * end of the application.
 * 
 * @namespace General javascript functions.
 */
var GeneralFunctions = {};

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
GeneralFunctions.displayMessageBox = function(title, message, messageButtons) {
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
}

