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
 * Calendar event popover utility.
 *
 * This module implements the functionality of calendar event popovers,
 * providing shared UI builders for appointment, unavailability, working plan
 * exception, and blocked period popovers.
 */
App.Utils.CalendarEventPopover = (function () {
    const moment = window.moment;

    // Icon Rendering Functions

    /**
     * Render a map icon that links to Google maps.
     *
     * @param {Object} user - Should have the address, city, etc properties.
     * @returns {string|null} The rendered HTML or null if no address data.
     */
    function renderMapIcon(user) {
        const data = [user.address, user.city, user.state, user.zip_code].filter(Boolean);
        if (!data.length) {
            return null;
        }
        return $('<div/>', {
            html: [
                $('<a/>', {
                    href: 'https://google.com/maps/place/' + data.join(','),
                    target: '_blank',
                    html: [$('<span/>', {class: 'fas fa-map-marker-alt'})],
                }),
            ],
        }).html();
    }

    /**
     * Render a mail icon.
     *
     * @param {string} email - Email address.
     * @returns {string|null} The rendered HTML or null if no email.
     */
    function renderMailIcon(email) {
        if (!email) {
            return null;
        }
        return $('<div/>', {
            html: [
                $('<a/>', {
                    href: 'mailto:' + email,
                    target: '_blank',
                    html: [$('<span/>', {class: 'fas fa-envelope'})],
                }),
            ],
        }).html();
    }

    /**
     * Render a phone icon.
     *
     * @param {string} phone - Phone number.
     * @returns {string|null} The rendered HTML or null if no phone.
     */
    function renderPhoneIcon(phone) {
        if (!phone) {
            return null;
        }
        return $('<div/>', {
            html: [
                $('<a/>', {
                    href: 'tel:' + phone,
                    target: '_blank',
                    html: [$('<span/>', {class: 'fas fa-phone-alt'})],
                }),
            ],
        }).html();
    }

    /**
     * Render custom content into the popover of events.
     *
     * @param {Object} info - The info object as passed from FullCalendar.
     * @returns {Object|string|null} Return HTML string, a jQuery selector or null for nothing.
     */
    function renderCustomContent(info) {
        return null; // Default behavior - can be overridden
    }

    // Helper Functions

    /**
     * Format a datetime string for display.
     *
     * @param {Date|string} datetime - The datetime to format.
     * @returns {string} Formatted datetime string.
     */
    function formatDateTime(datetime) {
        return App.Utils.Date.format(
            moment(datetime).format('YYYY-MM-DD HH:mm:ss'),
            vars('date_format'),
            vars('time_format'),
            true,
        );
    }

    /**
     * Get truncated notes for popover display.
     *
     * @param {Object} event - Calendar event object.
     * @returns {string} Notes text (truncated to 100 chars) or '-'.
     */
    function getEventNotes(event) {
        const notes = event.extendedProps?.data?.notes;
        if (!notes) {
            return '-';
        }

        return notes.length > 100 ? notes.substring(0, 100) + '...' : notes;
    }

    // Popover UI Element Builders

    /**
     * Create a popover button element.
     *
     * @param {string} className - CSS class names.
     * @param {string} iconClass - Font Awesome icon class.
     * @param {string} labelKey - Language key for button text.
     * @returns {jQuery} Button element.
     */
    function createPopoverButton(className, iconClass, labelKey) {
        return $('<button/>', {
            class: className,
            html: [$('<i/>', {class: iconClass + ' me-2'}), $('<span/>', {text: lang(labelKey)})],
        });
    }

    /**
     * Create the standard popover action buttons.
     *
     * @param {string} displayEdit - CSS class to show/hide edit button.
     * @param {string} displayDelete - CSS class to show/hide delete button.
     * @returns {jQuery} Button container element.
     */
    function createPopoverButtons(displayEdit, displayDelete) {
        return $('<div/>', {
            class: 'd-flex justify-content-center',
            html: [
                createPopoverButton('close-popover btn btn-outline-secondary me-2', 'fas fa-ban', 'close'),
                createPopoverButton(
                    'delete-popover btn btn-outline-secondary ' + displayDelete,
                    'fas fa-trash-alt',
                    'delete',
                ),
                createPopoverButton('edit-popover btn btn-primary ' + displayEdit, 'fas fa-edit', 'edit'),
            ],
        });
    }

    /**
     * Create a labeled text row for popover content.
     *
     * @param {string} labelKey - Language key for label.
     * @param {string} text - Text content.
     * @returns {Array<jQuery>} Array of jQuery elements.
     */
    function createPopoverRow(labelKey, text) {
        return [
            $('<strong/>', {class: 'd-inline-block me-2', text: lang(labelKey)}),
            $('<span/>', {text: text}),
            $('<br/>'),
        ];
    }

    // Popover Content Builders

    /**
     * Build popover content for unavailability events.
     *
     * @param {Object} info - FullCalendar event info.
     * @param {string} displayEdit - CSS class for edit visibility.
     * @param {string} displayDelete - CSS class for delete visibility.
     * @returns {jQuery} Popover content element.
     */
    function buildUnavailabilityPopover(info, displayEdit, displayDelete) {
        const data = info.event.extendedProps.data;
        const provider = data.provider;
        let startDateTime = info.event.start;
        let endDateTime = info.event.end || info.event.start;

        if (data.start_datetime) {
            startDateTime = new Date(data.start_datetime);
            endDateTime = new Date(data.end_datetime);
        }
        return $('<div/>', {
            html: [
                ...createPopoverRow('provider', provider.first_name + ' ' + provider.last_name),
                ...createPopoverRow('start', formatDateTime(startDateTime)),
                ...createPopoverRow('end', formatDateTime(endDateTime)),
                ...createPopoverRow('notes', getEventNotes(info.event)),
                renderCustomContent(info),
                $('<hr/>'),
                createPopoverButtons(displayEdit, displayDelete),
            ],
        });
    }

    /**
     * Build popover content for working plan exception events.
     *
     * @param {Object} info - FullCalendar event info.
     * @param {string} displayEdit - CSS class for edit visibility.
     * @param {string} displayDelete - CSS class for delete visibility.
     * @returns {jQuery} Popover content element.
     */
    function buildWorkingPlanExceptionPopover(info, displayEdit, displayDelete) {
        const data = info.event.extendedProps.data;
        const date = data.date;
        const workingPlanException = data.workingPlanException;
        const provider = data.provider;
        const startTime = workingPlanException?.start;
        const endTime = workingPlanException?.end;

        const formatTimeOrDash = function (time) {
            if (!time) {
                return '-';
            }
            return App.Utils.Date.format(date + ' ' + time, vars('date_format'), vars('time_format'), true);
        };
        return $('<div/>', {
            html: [
                ...createPopoverRow('provider', provider.first_name + ' ' + provider.last_name),
                ...createPopoverRow('start', formatTimeOrDash(startTime)),
                ...createPopoverRow('end', formatTimeOrDash(endTime)),
                ...createPopoverRow('timezone', startTime ? vars('timezones')[provider.timezone] : '-'),
                renderCustomContent(info),
                $('<hr/>'),
                createPopoverButtons(displayEdit, displayDelete),
            ],
        });
    }

    /**
     * Build popover content for appointment events.
     *
     * @param {Object} info - FullCalendar event info.
     * @param {string} displayEdit - CSS class for edit visibility.
     * @param {string} displayDelete - CSS class for delete visibility.
     * @returns {jQuery} Popover content element.
     */
    function buildAppointmentPopover(info, displayEdit, displayDelete) {
        const data = info.event.extendedProps.data;
        const customer = data.customer;
        const provider = data.provider;
        const customerName = [customer.first_name, customer.last_name].filter(Boolean).join(' ') || '-';
        const meetingLinkElements = data.meeting_link
            ? [
                  $('<strong/>', {class: 'd-inline-block me-2', text: lang('meeting_link')}),
                  $('<a/>', {href: data.meeting_link, target: '_blank', text: data.meeting_link}),
                  $('<br/>'),
              ]
            : [];
        return $('<div/>', {
            html: [
                ...createPopoverRow('start', formatDateTime(info.event.start)),
                ...createPopoverRow('end', formatDateTime(info.event.end)),
                ...createPopoverRow('timezone', vars('timezones')[provider.timezone]),
                ...createPopoverRow('status', data.status || '-'),
                ...createPopoverRow('service', data.service.name),
                $('<strong/>', {class: 'd-inline-block me-2', text: lang('provider')}),
                renderMapIcon(provider),
                $('<span/>', {text: provider.first_name + ' ' + provider.last_name}),
                $('<br/>'),
                $('<strong/>', {class: 'd-inline-block me-2', text: lang('customer')}),
                renderMapIcon(customer),
                $('<span/>', {class: 'd-inline-block', text: customerName}),
                $('<br/>'),
                $('<strong/>', {class: 'd-inline-block me-2', text: lang('email')}),
                renderMailIcon(customer.email),
                $('<span/>', {class: 'd-inline-block', text: customer.email || '-'}),
                $('<br/>'),
                $('<strong/>', {class: 'd-inline-block me-2', text: lang('phone')}),
                renderPhoneIcon(customer.phone_number),
                $('<span/>', {class: 'd-inline-block', text: customer.phone_number || '-'}),
                $('<br/>'),
                ...meetingLinkElements,
                ...createPopoverRow('notes', getEventNotes(info.event)),
                renderCustomContent(info),
                $('<hr/>'),
                createPopoverButtons(displayEdit, displayDelete),
            ],
        });
    }

    /**
     * Build popover content for blocked period events.
     *
     * @param {Object} info - FullCalendar event info.
     * @returns {jQuery} Popover content element.
     */
    function buildBlockedPeriodPopover(info) {
        const data = info.event.extendedProps.data;

        return $('<div/>', {
            html: [
                ...createPopoverRow('name', data.name || '-'),
                ...createPopoverRow('start', formatDateTime(info.event.start)),
                ...createPopoverRow('end', formatDateTime(info.event.end)),
                ...createPopoverRow('notes', data.notes || '-'),
                $('<hr/>'),
                $('<div/>', {
                    class: 'd-flex justify-content-center',
                    html: [createPopoverButton('close-popover btn btn-outline-secondary', 'fas fa-ban', 'close')],
                }),
            ],
        });
    }

    // Public API

    return {
        renderPhoneIcon,
        renderMapIcon,
        renderMailIcon,
        renderCustomContent,
        formatDateTime,
        getEventNotes,
        createPopoverButton,
        createPopoverButtons,
        createPopoverRow,
        buildUnavailabilityPopover,
        buildWorkingPlanExceptionPopover,
        buildAppointmentPopover,
        buildBlockedPeriodPopover,
    };
})();
