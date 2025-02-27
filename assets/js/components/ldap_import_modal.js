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
 * LDAP import modal component.
 *
 * This module implements the LDAP import modal functionality.
 *
 * This module requires the following scripts:
 *
 *   - App.Http.Customers
 *   - App.Http.Providers
 *   - App.Http.Secretaries
 *   - App.Http.Admins
 */
App.Components.LdapImportModal = (function () {
    const $modal = $('#ldap-import-modal');
    const $save = $('#ldap-import-save');
    const $firstName = $('#ldap-import-first-name');
    const $lastName = $('#ldap-import-last-name');
    const $email = $('#ldap-import-email');
    const $phoneNumber = $('#ldap-import-phone-number');
    const $username = $('#ldap-import-username');
    const $ldapDn = $('#ldap-import-ldap-dn');
    const $roleSlug = $('#ldap-import-role-slug');
    const $password = $('#ldap-import-password');

    let deferred;

    /**
     * Validate the import modal form.
     *
     * @return {Boolean}
     */
    function validate() {
        $modal.find('.is-invalid').removeClass('is-invalid');

        // Check required fields.
        let missingRequiredField = false;

        $modal.find('.required').each((index, requiredField) => {
            if ($(requiredField).val() === '' || $(requiredField).val() === null) {
                $(requiredField).addClass('is-invalid');
                missingRequiredField = true;
            }
        });

        return !missingRequiredField;
    }

    /**
     * Get the right HTTP client for the create-user request.
     *
     * @param {String} roleSlug
     *
     * @return {Object}
     */
    function getHttpClient(roleSlug) {
        switch (roleSlug) {
            case App.Layouts.Backend.DB_SLUG_CUSTOMER:
                return App.Http.Customers;
            case App.Layouts.Backend.DB_SLUG_PROVIDER:
                return App.Http.Providers;
            case App.Layouts.Backend.DB_SLUG_SECRETARY:
                return App.Http.Secretaries;
            case App.Layouts.Backend.DB_SLUG_ADMIN:
                return App.Http.Admins;
            default:
                throw new Error(`Unsupported role slug provided: ${roleSlug}`);
        }
    }

    /**
     * Get the user data object, based on the form values the user provided.
     *
     * @param {String} roleSlug
     *
     * @return {Object}
     */
    function getUser(roleSlug) {
        const user = {
            first_name: $firstName.val(),
            last_name: $lastName.val(),
            email: $email.val(),
            phone_number: $phoneNumber.val(),
            ldap_dn: $ldapDn.val(),
        };

        if (roleSlug !== App.Layouts.Backend.DB_SLUG_CUSTOMER) {
            user.settings = {
                username: $username.val(),
                password: $password.val(),
                notification: 1,
            };
        }

        return user;
    }

    /**
     * Go through the available values and try to load as many as possible based on the provided mapping.
     *
     * @param {Object} entry
     * @param {Object} ldapFieldMapping
     */
    function loadEntry(entry, ldapFieldMapping) {
        $ldapDn.val(entry.dn);
        $firstName.val(entry?.[ldapFieldMapping?.first_name] ?? '');
        $lastName.val(entry?.[ldapFieldMapping?.last_name] ?? '');
        $email.val(entry?.[ldapFieldMapping?.email] ?? '');
        $phoneNumber.val(entry?.[ldapFieldMapping?.phone_number] ?? '');
        $username.val(entry?.[ldapFieldMapping?.username] ?? '');
        $password.val('');
    }

    /**
     * Save the current user data.
     */
    function onSaveClick() {
        if (!validate()) {
            return;
        }

        const roleSlug = $roleSlug.val();

        const user = getUser(roleSlug);

        const httpClient = getHttpClient(roleSlug);

        httpClient.store(user).done(() => {
            deferred.resolve();
            deferred = undefined;
            $modal.modal('hide');
        });
    }

    /**
     * Reset the modal every time it's hidden.
     */
    function onModalHidden() {
        resetModal();

        if (deferred) {
            deferred.reject();
        }
    }

    /**
     * Reset the modal fields and state.
     */
    function resetModal() {
        $modal.find('input, select, textarea').val('');
        $modal.find(':checkbox').prop('checked', false);
        $roleSlug.val(App.Layouts.Backend.DB_SLUG_PROVIDER);
    }

    /**
     * Open the import modal for an LDAP entry.
     *
     * @param {Object} entry
     * @param {Object} ldapFieldMapping
     *
     * @return {Object} $.Deferred
     */
    function open(entry, ldapFieldMapping) {
        resetModal();

        deferred = $.Deferred();

        loadEntry(entry, ldapFieldMapping);

        $modal.modal('show');

        return deferred.promise();
    }

    /**
     * Initialize the module.
     */
    function initialize() {
        $save.on('click', onSaveClick);
        $modal.on('hidden.bs.modal', onModalHidden);
    }

    document.addEventListener('DOMContentLoaded', initialize);

    return {
        open,
    };
})();
