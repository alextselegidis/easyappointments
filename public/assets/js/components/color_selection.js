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
 * Color selection component.
 *
 * This module implements the color selection functionality.
 */
App.Components.ColorSelection = (function () {
    /**
     * Event: Color Selection Option "Click"
     *
     * @param {jQuery.Event} event
     */
    function onColorSelectionOptionClick(event) {
        const $target = $(event.currentTarget);

        const $colorSelection = $target.closest('.color-selection');

        $colorSelection.find('.color-selection-option.selected').removeClass('selected');

        $target.addClass('selected');
    }

    /**
     * Get target color.
     *
     * @param {jQuery} $target Container element ".color-selection" selector.
     */
    function getColor($target) {
        return $target.find('.color-selection-option.selected').data('value');
    }

    /**
     * Set target color.
     *
     * @param {jQuery} $target Container element ".color-selection" selector.
     * @param {String} color Color value.
     */
    function setColor($target, color) {
        $target
            .find('.color-selection-option')
            .removeClass('selected')
            .each((index, colorSelectionOptionEl) => {
                var $colorSelectionOption = $(colorSelectionOptionEl);

                if ($colorSelectionOption.data('value') === color) {
                    $colorSelectionOption.addClass('selected');
                    return false;
                }
            });
    }

    /**
     * Disable the color selection for the target element.
     *
     * @param {jQuery} $target
     */
    function disable($target) {
        $target.find('.color-selection-option').prop('disabled', true).removeClass('selected');
        $target.find('.color-selection-option:first').addClass('selected');
    }

    /**
     * Enable the color selection for the target element.
     *
     * @param {jQuery} $target
     */
    function enable($target) {
        $target.find('.color-selection-option').prop('disabled', false);
    }

    function applyBackgroundColors() {
        $(document)
            .find('.color-selection-option')
            .each((index, colorSelectionOptionEl) => {
                const $colorSelectionOption = $(colorSelectionOptionEl);

                const color = $colorSelectionOption.data('value');

                $colorSelectionOption.css('background-color', color);
            });
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        $(document).on('click', '.color-selection-option', onColorSelectionOptionClick);

        applyBackgroundColors();
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {
        disable,
        enable,
        getColor,
        setColor,
    };
})();
