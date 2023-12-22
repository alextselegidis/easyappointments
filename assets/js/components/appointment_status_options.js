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
 * Appointment status options component.
 *
 * This module implements the appointment status options.
 */
App.Components.AppointmentStatusOptions = (function () {
    /**
     * Render an appointment status option.
     *
     * @param {String} [appointmentStatusOption]
     *
     * @return {jQuery} Returns a jQuery selector with the list group item markup.
     */
    function renderListGroupItem(appointmentStatusOption = '') {
        return $(`
            <li class="list-group-item d-flex justify-content-between align-items-center p-0 border-0 mb-3 appointment-status-option">
                <label class="w-100 me-2">
                    <input class="form-control" value="${appointmentStatusOption}">
                </label>
    
                <button type="button" class="btn btn-outline-danger delete-appointment-status-option">
                    <i class="fas fa-trash"></i>
                </button>
            </li>
        `);
    }

    /**
     * Event: Delete Appointment Status Option "Click"
     *
     * @param {jQuery.Event} event
     */
    function onDeleteAppointmentStatusOptionClick(event) {
        $(event.currentTarget).closest('li').remove();
    }

    /**
     * Event: Add Appointment Status Option "Click"
     *
     * @param {jQuery.Event} event
     */
    function onAddAppointmentStatusOptionClick(event) {
        const $target = $(event.currentTarget);

        const $listGroup = $target.closest('.appointment-status-options').find('.list-group');

        if (!$listGroup.length) {
            return;
        }

        renderListGroupItem().appendTo($listGroup);
    }

    /**
     * Get target options.
     *
     * @param {jQuery} $target Container element ".status-list" selector.
     *
     * @return {String[]}
     */
    function getOptions($target) {
        const $listGroup = $target.find('.list-group');

        const appointmentStatusOptions = [];

        $listGroup.find('li').each((index, listGroupItemEl) => {
            const $listGroupItem = $(listGroupItemEl);
            const appointmentStatusOption = $listGroupItem.find('input:text').val();
            appointmentStatusOptions.push(appointmentStatusOption);
        });

        return appointmentStatusOptions;
    }

    /**
     * Set target options.
     *
     * @param {jQuery} $target Container element ".status-list" selector.
     * @param {String[]} appointmentStatusOptions Appointment status options.
     */
    function setOptions($target, appointmentStatusOptions) {
        if (!$target.length || !appointmentStatusOptions || !appointmentStatusOptions.length) {
            return;
        }

        const $listGroup = $target.find('.list-group');

        if (!$listGroup.length) {
            return;
        }

        $listGroup.empty();

        appointmentStatusOptions.forEach((appointmentStatusOption) => {
            renderListGroupItem(appointmentStatusOption).appendTo($listGroup);
        });
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        $(document).on('click', '.delete-appointment-status-option', onDeleteAppointmentStatusOptionClick);
        $(document).on('click', '.add-appointment-status-option', onAddAppointmentStatusOptionClick);
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {
        getOptions,
        setOptions,
    };
})();
