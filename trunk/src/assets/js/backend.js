/**
 * Main javascript code for the backend section of Easy!Appointments.
 */
$(document).ready(function() {
    $(window).resize(function() {
        Backend.placeFooterToBottom();
    }).trigger('resize');
});


/**
 * This namespace contains functions that are used in the backend section of
 * the applications.
 * 
 * @namespace Backend
 */
var Backend = {
    /**
     * Place the backend footer always on the bottom of the page.
     */
    placeFooterToBottom: function() {
        var footerHandle = $('#footer');

        if (window.innerHeight > $('body').height()) {
            footerHandle.css({
                'position'  : 'absolute',
                'width'     : '100%',
                'bottom'    : '0px'
            });
        } else {
            footerHandle.css({
                'position'  : 'static'
            });
        }
    }
}