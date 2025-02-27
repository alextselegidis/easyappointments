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
 * Lang utility.
 *
 * This module implements the functionality of translations.
 */
window.App.Utils.Lang = (function () {
    /**
     * Enable Language Selection
     *
     * Enables the language selection functionality. Must be called on every page has a language selection button.
     * This method requires the global variable "vars('available_variables')" to be initialized before the execution.
     *
     * @param {Object} $target Selected element button for the language selection.
     */
    function enableLanguageSelection($target) {
        // Select Language
        const $languageList = $('<ul/>', {
            'id': 'language-list',
            'html': vars('available_languages').map((availableLanguage) =>
                $('<li/>', {
                    'class': 'language',
                    'data-language': availableLanguage,
                    'text': App.Utils.String.upperCaseFirstLetter(availableLanguage),
                }),
            ),
        });

        $target.popover({
            placement: 'top',
            title: 'Select Language',
            content: $languageList[0],
            html: true,
            container: 'body',
            trigger: 'manual',
        });

        $target.on('click', function (event) {
            event.stopPropagation();

            const $target = $(event.target);

            if ($('#language-list').length === 0) {
                $target.popover('show');
            } else {
                $target.popover('hide');
            }

            $target.toggleClass('active');
        });

        $(document).on('click', 'li.language', (event) => {
            // Change language with HTTP request and refresh page.

            const language = $(event.target).data('language');

            App.Http.Localization.changeLanguage(language).done(() => document.location.reload());
        });

        $(document).on('click', () => {
            $target.popover('hide');
        });
    }

    return {
        enableLanguageSelection,
    };
})();
