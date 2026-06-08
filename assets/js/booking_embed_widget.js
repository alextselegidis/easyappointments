/* ----------------------------------------------------------------------------
 * Easy!Appointments - Booking inline embed widget
 *
 * Calendly-style loader: creates an iframe and handles auto-resize via postMessage.
 * ---------------------------------------------------------------------------- */

(function (window, document) {
    'use strict';

    var MAX_HEIGHT = 1400;
    var MIN_HEIGHT = 320;

    function parseBookingOrigin(url) {
        try {
            return new URL(url, window.location.href).origin;
        } catch (error) {
            return null;
        }
    }

    function initInlineWidget(options) {
        options = options || {};

        var parent = options.parentElement;

        if (!parent) {
            throw new Error('EasyAppointments: Parent element not set.');
        }

        if (parent.jquery) {
            parent = parent[0];
        }

        var url = options.url || parent.getAttribute('data-url');

        if (!url) {
            throw new Error('EasyAppointments: Booking URL not set.');
        }

        var bookingOrigin = parseBookingOrigin(url);
        var lastHeight = 0;

        parent.style.position = parent.style.position || 'relative';
        parent.style.minWidth = parent.style.minWidth || '320px';
        parent.style.overflow = 'hidden';

        if (!parent.style.height) {
            parent.style.height = '650px';
        }

        var iframe = document.createElement('iframe');
        iframe.src = url;
        iframe.width = '100%';
        iframe.height = '100%';
        iframe.frameBorder = '0';
        iframe.style.border = '0';
        iframe.style.display = 'block';
        iframe.loading = 'lazy';
        iframe.title = 'Book an appointment';

        parent.appendChild(iframe);

        window.addEventListener('message', function (event) {
            if (!bookingOrigin || event.origin !== bookingOrigin) {
                return;
            }

            if (!event.data || event.data.event !== 'easyappointments.page_height') {
                return;
            }

            var height = event.data.payload && event.data.payload.height;

            if (!height || height < MIN_HEIGHT) {
                return;
            }

            height = Math.min(Math.round(height), MAX_HEIGHT);

            if (Math.abs(height - lastHeight) < 24) {
                return;
            }

            lastHeight = height;
            parent.style.height = height + 'px';
        });

        return iframe;
    }

    function initAllInlineWidgets() {
        var widgets = document.querySelectorAll('.ea-booking-inline-widget');

        widgets.forEach(function (widget) {
            if (widget.getAttribute('data-ea-initialized') === '1') {
                return;
            }

            widget.setAttribute('data-ea-initialized', '1');
            initInlineWidget({ parentElement: widget });
        });
    }

    window.EasyAppointments = window.EasyAppointments || {};
    window.EasyAppointments.initInlineWidget = initInlineWidget;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAllInlineWidgets);
    } else {
        initAllInlineWidgets();
    }
})(window, document);
