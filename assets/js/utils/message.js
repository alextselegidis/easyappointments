/* ----------------------------------------------------------------------------
 * Easy!Appointments - Open Source Web Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

window.App.Utils.Message = (function () {
    /**
     * Show a message box to the user.
     *
     * This functions displays a message box in the admin array. It is useful when user
     * decisions or verifications are needed.
     *
     * @param {String} title The title of the message box.
     * @param {String} message The message of the dialog.
     * @param {Array} [buttons] Contains the dialog buttons along with their functions.
     */
    function show(title, message, buttons = null) {
        if (!buttons) {
            buttons = [
                {
                    text: App.Lang.close,
                    click: function () {
                        $('#message-box').dialog('close');
                    }
                }
            ];
        }

        // Destroy previous dialog instances.

        $('#message-box').dialog('destroy').remove();

        // Create the HTML of the message box.

        const $messageBox = $('<div/>', {
            'id': 'message-box',
            'title': title,
            'html': [
                $('<p/>', {
                    'html': message
                })
            ]
        }).appendTo('body');

        $messageBox.dialog({
            autoOpen: false,
            modal: true,
            resize: 'auto',
            width: 'auto',
            height: 'auto',
            resizable: false,
            buttons: buttons,
            closeOnEscape: true
        });

        $messageBox.dialog('open');

        $('.ui-dialog .ui-dialog-buttonset button').addClass('btn btn-outline-secondary');

        $('#message-box .ui-dialog-titlebar-close').hide();
    }

    return {
        show
    };
})();
