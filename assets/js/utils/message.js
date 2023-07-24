/* ----------------------------------------------------------------------------
 * Easy!Appointments - Online Appointment Scheduler
 *
 * @package     EasyAppointments
 * @author      A.Tselegidis <alextselegidis@gmail.com>
 * @copyright   Copyright (c) Alex Tselegidis
 * @license     https://opensource.org/licenses/GPL-3.0 - GPLv3
 * @link        https://easyappointments.org
 * @since       v1.5.0
 * ---------------------------------------------------------------------------- */

/**
 * Messages utility.
 *
 * This module implements the functionality of messages.
 */
window.App.Utils.Message = (function () {
    let messageModal = null;

    /**
     * Show a message box to the user.
     *
     * This functions displays a message box in the admin array. It is useful when user
     * decisions or verifications are needed.
     *
     * @param {String} title The title of the message box.
     * @param {String} message The message of the dialog.
     * @param {Array} [buttons] Contains the dialog buttons along with their functions.
     * @param {Boolean} [isDismissible] If true, the button will show the close X in the header and close with the press of the Escape button.
     */
    function show(title, message, buttons = null, isDismissible = true) {
        if (!title || !message) {
            return;
        }

        if (!buttons) {
            buttons = [
                {
                    text: lang('close'),
                    className: 'btn btn-outline-primary',
                    click: function (event, messageModal) {
                        messageModal.dispose();
                    },
                },
            ];
        }

        if (messageModal?.dispose && messageModal?._element) {
            messageModal.dispose();
        }

        $('#message-modal').remove();

        const $messageModal = $(`
            <div class="modal" id="message-modal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                ${title}
                            </h5>
                            ${isDismissible ? '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' : ''}
                        </div>
                        <div class="modal-body">
                            <p>
                                ${message}
                            </p>
                        </div>
                        <div class="modal-footer">
                            <!-- * -->
                        </div>
                    </div>
                </div>
            </div>
        `).appendTo('body');

        buttons.forEach((button) => {
            if (!button) {
                return;
            }

            if (!button.className) {
                button.className = 'btn btn-outline-primary';
            }

            const $button = $(`
                <button type="button" class="${button.className}" data-bs-dismiss="modal">
                    ${button.text}
                </button>
            `).appendTo($messageModal.find('.modal-footer'));

            if (button.click) {
                $button.on('click', (event) => button.click(event, messageModal));
            }
        });

        messageModal = new bootstrap.Modal('#message-modal', {
            keyboard: isDismissible,
            backdrop: 'static',
        });

        messageModal.show();

        $('#message-modal').css('z-index', '99999').next().css('z-index', '9999');
    }

    return {
        show
    };
})();
