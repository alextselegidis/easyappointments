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
 * LDAP settings page.
 *
 * This module implements the functionality of the LDAP settings page.
 */
App.Pages.LdapSettings = (function () {
    const $saveSettings = $('#save-settings');
    const $searchForm = $('#ldap-search-form');
    const $searchKeyword = $('#ldap-search-keyword');
    const $searchResults = $('#ldap-search-results');
    const $ldapFilter = $('#ldap-filter');
    const $ldapFieldMapping = $('#ldap-field-mapping');
    const $resetFilter = $('#ldap-reset-filter');
    const $resetFieldMapping = $('#ldap-reset-field-mapping');

    /**
     * Check if the form has invalid values.
     *
     * @return {Boolean}
     */
    function isInvalid() {
        try {
            $('#ldap-settings .is-invalid').removeClass('is-invalid');

            // Validate required fields.

            let missingRequiredFields = false;

            $('#ldap-settings .required').each((index, requiredField) => {
                const $requiredField = $(requiredField);

                if (!$requiredField.val()) {
                    $requiredField.addClass('is-invalid');
                    missingRequiredFields = true;
                }
            });

            if (missingRequiredFields) {
                throw new Error(lang('fields_are_required'));
            }

            return false;
        } catch (error) {
            App.Layouts.Backend.displayNotification(error.message);
            return true;
        }
    }

    /**
     * Apply the setting values to the UI form.
     *
     * @param {Array} ldapSettings
     */
    function deserialize(ldapSettings) {
        ldapSettings.forEach((ldapSetting) => {
            const $field = $('[data-field="' + ldapSetting.name + '"]');

            $field.is(':checkbox')
                ? $field.prop('checked', Boolean(Number(ldapSetting.value)))
                : $field.val(ldapSetting.value);
        });
    }

    /**
     * Prepare an array of setting values based on the UI form.
     *
     * @return {Array}
     */
    function serialize() {
        const ldapSettings = [];

        $('[data-field]').each((index, field) => {
            const $field = $(field);

            ldapSettings.push({
                name: $field.data('field'),
                value: $field.is(':checkbox') ? Number($field.prop('checked')) : $field.val(),
            });
        });

        return ldapSettings;
    }

    function getLdapFieldMapping() {
        const jsonLdapFieldMapping = $ldapFieldMapping.val();
        return JSON.parse(jsonLdapFieldMapping);
    }

    /**
     * Save the current server settings.
     */
    function saveSettings() {
        if (isInvalid()) {
            App.Layouts.Backend.displayNotification(lang('settings_are_invalid'));

            return;
        }

        const ldapSettings = serialize();

        return App.Http.LdapSettings.save(ldapSettings);
    }

    /**
     * Search the LDAP server based on a keyword.
     */
    function searchServer() {
        $searchResults.empty();

        const keyword = $searchKeyword.val();

        if (!keyword) {
            return;
        }

        App.Http.LdapSettings.search(keyword).done((entries) => {
            $searchResults.empty();

            if (!entries?.length) {
                renderNoRecordsFound().appendTo($searchResults);
                return;
            }

            entries.forEach((entry) => {
                renderEntry(entry).appendTo($searchResults);
            });
        });
    }

    /**
     * Save the account information.
     */
    function onSaveSettingsClick() {
        saveSettings().done(() => {
            App.Layouts.Backend.displayNotification(lang('settings_saved'));
        });
    }

    /**
     * Set the field value back to the original state.
     */
    function onResetFilterClick() {
        $ldapFilter.val(vars('ldap_default_filter'));
    }

    /**
     * Set the field value back to the original state.
     */
    function onResetFieldMappingClick() {
        const defaultFieldMapping = vars('ldap_default_field_mapping');
        const jsonDefaultFieldMapping = JSON.stringify(defaultFieldMapping, null, 2);
        $ldapFieldMapping.val(jsonDefaultFieldMapping);
    }

    /**
     * Handle the LDAP import button click
     */
    function onLdapImportClick(event) {
        const $target = $(event.target);
        const $card = $target.closest('.card');
        const entry = $card.data('entry');
        const ldapFieldMapping = getLdapFieldMapping();

        App.Components.LdapImportModal.open(entry, ldapFieldMapping).done(() => {
            App.Layouts.Backend.displayNotification(lang('user_imported'));
        });
    }

    /**
     * Render the no-records-found message
     */
    function renderNoRecordsFound() {
        return $(`
            <div class="text-muted fst-italic">
                ${lang('no_records_found')}
            </div>
        `);
    }

    /**
     * Render the LDAP entry data on screen
     *
     * @param {Object} entry
     */
    function renderEntry(entry) {
        if (!entry?.dn) {
            return;
        }

        const $entry = $(`
            <div class="card small mb-2">
                <div class="card-header p-2 fw-bold">
                    ${entry.dn}
                </div>
                <div class="card-body p-2">
                    <p class="d-block mb-2">${lang('content')}</p>
                    
                    <pre class="overflow-y-auto bg-light rounded p-2" style="max-height: 200px">${JSON.stringify(entry, null, 2)}</pre>
                    
                    <div class="d-lg-flex">
                        <button class="btn btn-outline-primary btn-sm px-4 ldap-import ms-lg-auto">
                            ${lang('import')}
                        </button>
                    </div>
                </div>
            </div>
        `);

        $entry.data('entry', entry);

        return $entry;
    }

    /**
     * Save the current connection settings and then search the directory based on the provided keyword.
     *
     * @param {Object} event
     */
    function onSearchFormSubmit(event) {
        event.preventDefault();

        saveSettings().done(() => {
            searchServer();
        });
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        $saveSettings.on('click', onSaveSettingsClick);
        $resetFilter.on('click', onResetFilterClick);
        $resetFieldMapping.on('click', onResetFieldMappingClick);
        $searchForm.on('submit', onSearchFormSubmit);
        $searchResults.on('click', '.ldap-import', onLdapImportClick);

        const ldapSettings = vars('ldap_settings');

        deserialize(ldapSettings);
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {};
})();
