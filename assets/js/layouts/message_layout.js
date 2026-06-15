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
 * Message layout.
 *
 * This module implements the message layout functionality.
 */
window.App.Layouts.Message = (function () {
    const $ = window.jQuery;
    let lastEmbedHeight = 0;
    let embedHeightTimer = null;

    /**
     * Notify the parent page (embed widget) about content height changes.
     */
    function notifyEmbedHeight() {
        if (!$('body').hasClass('booking-embedded') || window.parent === window) {
            return;
        }

        if (embedHeightTimer) {
            clearTimeout(embedHeightTimer);
        }

        embedHeightTimer = setTimeout(() => {
            const height = Math.min(
                Math.max(
                    document.body.scrollHeight,
                    document.documentElement.scrollHeight,
                    $('#message-frame').outerHeight() || 0,
                ),
                1400,
            );

            if (Math.abs(height - lastEmbedHeight) < 24) {
                return;
            }

            lastEmbedHeight = height;

            let targetOrigin = '*';

            try {
                if (document.referrer) {
                    targetOrigin = new URL(document.referrer).origin;
                }
            } catch (error) {
                // Fall back to wildcard when the referrer is unavailable.
            }

            window.parent.postMessage(
                {
                    event: 'easyappointments.page_height',
                    payload: { height: height },
                },
                targetOrigin,
            );
        }, 150);
    }

    $(document).ready(() => {
        if ($('body').hasClass('booking-embedded')) {
            notifyEmbedHeight();
            $(window).on('resize', notifyEmbedHeight);
        }
    });

    return {};
})();
