/**
 * Main javascript code for the backend section of Easy!Appointments.
 */
$(document).ready(function() {
    $(window).resize(function() {
        placeFooterToBottom();
    }).trigger('resize');
});

/**
 * Place the backend footer always on the bottom of the page.
 */
function placeFooterToBottom() {
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
