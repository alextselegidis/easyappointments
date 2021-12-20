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

/**
 * Booking Settings
 *
 * Contains the functionality of the booking settings page.
 */
App.Pages.BookingSettings = (function () {
    const $bookingSettings = $('#booking-settings');
    const $saveSettings = $('#save-settings');

    /**
     * Check if the form has invalid values.
     *
     * @return {Boolean}
     */
    function isInvalid() {
        try {
            $('#booking-settings .is-invalid').removeClass('is-invalid');

            // Validate required fields.

            let missingRequiredFields = false;

            $('#booking-settings .required').each((index, requiredField) => {
                const $requiredField = $(requiredField);

                if (!$requiredField.val()) {
                    $requiredField.addClass('is-invalid');
                    missingRequiredFields = true;
                }
            });

            if (missingRequiredFields) {
                throw new Error(App.Lang.fields_are_required);
            }

            return false;
        } catch (error) {
            Backend.displayNotification(error.message);
            return true;
        }
    }

    /**
     * Apply the booking settings into the page.
     *
     * @param {Object} bookingSettings
     */
    function deserialize(bookingSettings) {
        bookingSettings.forEach((bookingSetting) => {
            const $field = $('[data-field="' + bookingSetting.name + '"]');

            if ($field.is(':checkbox')) {
                $field.prop('checked', Boolean(Number(bookingSetting.value)));
            } else {
                $field.val(bookingSetting.value);
            }
        });
    }

    /**
     * Serialize the page values into an array.
     *
     * @returns {Array}
     */
    function serialize() {
        const bookingSettings = [];

        $('[data-field]').each((index, field) => {
            const $field = $(field);

            bookingSettings.push({
                name: $field.data('field'),
                value: $field.is(':checkbox') ? Number($field.prop('checked')) : $field.val()
            });
        });

        return bookingSettings;
    }

    /**
     * Update the UI based on the display switch state.
     *
     * @param {jQuery} $displaySwitch
     */
    function updateDisplaySwitch($displaySwitch) {
        const isChecked = $displaySwitch.prop('checked');

        const $formGroup = $displaySwitch.closest('.form-group');

        $formGroup.find('.require-switch').prop('disabled', !isChecked);

        $formGroup.find('.form-label, .form-control').toggleClass('opacity-25', !isChecked);

        if (!isChecked) {
            $formGroup.find('.require-switch').prop('checked', false);
            $formGroup.find('.text-danger').hide();
        }
    }

    /**
     * Update the UI based on the require switch state.
     *
     * @param {jQuery} $requireSwitch
     */
    function updateRequireSwitch($requireSwitch) {
        const isChecked = $requireSwitch.prop('checked');

        const $formGroup = $requireSwitch.closest('.form-group');

        $formGroup.find('.text-danger').toggle(isChecked);
    }

    /**
     * Update the UI based on the initial values.
     */
    function applyInitialState() {
        $bookingSettings.find('.display-switch').each(function (index, displaySwitch) {
            const $displaySwitch = $(displaySwitch);

            updateDisplaySwitch($displaySwitch);
        });

        $bookingSettings.find('.require-switch').each(function (index, requireSwitch) {
            const $requireSwitch = $(requireSwitch);

            updateRequireSwitch($requireSwitch);
        });
    }

    /**
     * Save the account information.
     */
    function onSaveSettingsClick() {
        if (isInvalid()) {
            Backend.displayNotification(App.Lang.settings_are_invalid);

            return;
        }

        const bookingSettings = serialize();

        App.Http.BookingSettings.save(bookingSettings).done(() => {
            Backend.displayNotification(App.Lang.settings_saved);
        });
    }

    /**
     * Update the UI.
     *
     * @param {jQuery} event
     */
    function onDisplaySwitchClick(event) {
        const $displaySwitch = $(event.target);

        updateDisplaySwitch($displaySwitch);
    }

    /**
     * Update the UI.
     *
     * @param {Event} event
     */
    function onRequireSwitchClick(event) {
        const $requireSwitch = $(event.target);

        updateRequireSwitch($requireSwitch);
    }

    /**
     * Initialize on document ready.
     */
    function init() {
        const bookingSettings = App.Vars.booking_settings;

        deserialize(bookingSettings);

        $saveSettings.on('click', onSaveSettingsClick);

        $bookingSettings
            .on('click', '.display-switch', onDisplaySwitchClick)
            .on('click', '.require-switch', onRequireSwitchClick);

        applyInitialState();

        Backend.placeFooterToBottom();
    }

    document.addEventListener('DOMContentLoaded', init);

    return {};
})();
