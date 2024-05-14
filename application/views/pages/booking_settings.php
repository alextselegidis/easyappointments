<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>

<div id="booking-settings-page" class="container backend-page">
    <div id="booking-settings">
        <div class="row">
            <div class="col-sm-3 offset-sm-1">
                <?php component('settings_nav'); ?>
            </div>
            <div class="col-sm-6">
                <form>
                    <fieldset>
                        <div class="d-flex justify-content-between align-items-center border-bottom mb-4 py-2">
                            <h4 class="text-black-50 mb-0 fw-light">
                                <?= lang('booking_settings') ?>
                            </h4>

                            <?php if (can('edit', PRIV_SYSTEM_SETTINGS)): ?>
                                <button type="button" id="save-settings" class="btn btn-primary">
                                    <i class="fas fa-check-square me-2"></i>
                                    <?= lang('save') ?>
                                </button>
                            <?php endif; ?>
                        </div>

                        <h5 class="text-black-50 mb-3 fw-light">
                            <?= lang('fields') ?>
                        </h5>

                        <div class="row mb-5 fields-row">
                            <div class="col-lg-6">
                                <div class="form-group mb-5">
                                    <label for="first-name" class="form-label">
                                        <?= lang('first_name') ?>
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input type="text" id="first-name" class="form-control mb-2" readonly/>

                                    <div class="d-flex">
                                        <div class="form-check form-switch me-4">
                                            <input class="form-check-input display-switch" type="checkbox"
                                                   id="display-first-name"
                                                   data-field="display_first_name">
                                            <label class="form-check-label" for="display-first-name">
                                                <?= lang('display') ?>
                                            </label>
                                        </div>

                                        <div class="form-check form-switch">
                                            <input class="form-check-input require-switch" type="checkbox"
                                                   id="require-first-name"
                                                   data-field="require_first_name">
                                            <label class="form-check-label" for="require-first-name">
                                                <?= lang('require') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-5">
                                    <label for="last-name" class="form-label">
                                        <?= lang('last_name') ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="last-name" class="form-control mb-2" readonly/>
                                    <div class="d-flex">
                                        <div class="form-check form-switch me-4">
                                            <input class="form-check-input display-switch" type="checkbox"
                                                   id="display-last-name"
                                                   data-field="display_last_name">
                                            <label class="form-check-label" for="display-last-name">
                                                <?= lang('display') ?>
                                            </label>
                                        </div>

                                        <div class="form-check form-switch">
                                            <input class="form-check-input require-switch" type="checkbox"
                                                   id="require-last-name"
                                                   data-field="require_last_name">
                                            <label class="form-check-label" for="require-last-name">
                                                <?= lang('require') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-5">
                                    <label for="email" class="form-label">
                                        <?= lang('email') ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="email" class="form-control mb-2" readonly/>
                                    <div class="d-flex">
                                        <div class="form-check form-switch me-4">
                                            <input class="form-check-input display-switch" type="checkbox"
                                                   id="display-email"
                                                   data-field="display_email">
                                            <label class="form-check-label" for="display-email">
                                                <?= lang('display') ?>
                                            </label>
                                        </div>

                                        <div class="form-check form-switch">
                                            <input class="form-check-input require-switch" type="checkbox"
                                                   id="require-email"
                                                   data-field="require_email">
                                            <label class="form-check-label" for="require-email">
                                                <?= lang('require') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="phone-number" class="form-label">
                                        <?= lang('phone_number') ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="phone-number" class="form-control mb-2" readonly/>
                                    <div class="d-flex">
                                        <div class="form-check form-switch me-4">
                                            <input class="form-check-input display-switch" type="checkbox"
                                                   id="display-phone-number"
                                                   data-field="display_phone_number">
                                            <label class="form-check-label" for="display-phone-number">
                                                <?= lang('display') ?>
                                            </label>
                                        </div>

                                        <div class="form-check form-switch">
                                            <input class="form-check-input require-switch" type="checkbox"
                                                   id="require-phone-number"
                                                   data-field="require_phone_number">
                                            <label class="form-check-label" for="require-phone-number">
                                                <?= lang('require') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group mb-5">
                                    <label for="address" class="form-label">
                                        <?= lang('address') ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="address" class="form-control mb-2" readonly/>
                                    <div class="d-flex">
                                        <div class="form-check form-switch me-4">
                                            <input class="form-check-input display-switch" type="checkbox"
                                                   id="display-address"
                                                   data-field="display_address">
                                            <label class="form-check-label" for="display-address">
                                                <?= lang('display') ?>
                                            </label>
                                        </div>

                                        <div class="form-check form-switch">
                                            <input class="form-check-input require-switch" type="checkbox"
                                                   id="require-address"
                                                   data-field="require_address">
                                            <label class="form-check-label" for="require-address">
                                                <?= lang('require') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-5">
                                    <label for="city" class="form-label">
                                        <?= lang('city') ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="city" class="form-control mb-2" readonly/>
                                    <div class="d-flex">
                                        <div class="form-check form-switch me-4">
                                            <input class="form-check-input display-switch" type="checkbox"
                                                   id="display-city"
                                                   data-field="display_city">
                                            <label class="form-check-label" for="display-city">
                                                <?= lang('display') ?>
                                            </label>
                                        </div>

                                        <div class="form-check form-switch">
                                            <input class="form-check-input require-switch" type="checkbox"
                                                   id="require-city"
                                                   data-field="require_city">
                                            <label class="form-check-label" for="require-city">
                                                <?= lang('require') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-5">
                                    <label for="zip-code" class="form-label">
                                        <?= lang('zip_code') ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="zip-code" class="form-control mb-2" readonly/>
                                    <div class="d-flex">
                                        <div class="form-check form-switch me-4">
                                            <input class="form-check-input display-switch" type="checkbox"
                                                   id="display-zip-code"
                                                   data-field="display_zip_code">
                                            <label class="form-check-label" for="display-zip-code">
                                                <?= lang('display') ?>
                                            </label>
                                        </div>

                                        <div class="form-check form-switch">
                                            <input class="form-check-input require-switch" type="checkbox"
                                                   id="require-zip-code"
                                                   data-field="require_zip_code">
                                            <label class="form-check-label" for="require-zip-code">
                                                <?= lang('require') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="notes" class="form-label">
                                        <?= lang('notes') ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea id="notes" class="form-control mb-2" rows="1" readonly></textarea>
                                    <div class="d-flex">
                                        <div class="form-check form-switch me-4">
                                            <input class="form-check-input display-switch" type="checkbox"
                                                   id="display-notes"
                                                   data-field="display_notes">
                                            <label class="form-check-label" for="display-notes">
                                                <?= lang('display') ?>
                                            </label>
                                        </div>

                                        <div class="form-check form-switch">
                                            <input class="form-check-input require-switch" type="checkbox"
                                                   id="require-notes"
                                                   data-field="require_notes">
                                            <label class="form-check-label" for="require-notes">
                                                <?= lang('require') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h5 class="text-black-50 mb-3 fw-light">
                            <?= lang('custom_fields') ?>
                        </h5>

                        <div class="row mb-5 fields-row">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <div class="col-sm-6">
                                    <div class="form-group mb-5">
                                        <label for="first-name" class="form-label">
                                            <?= lang('custom_field') ?> #<?= $i ?>
                                            <span class="text-danger">*</span>
                                        </label>

                                        <input type="text" id="custom-field-<?= $i ?>" class="form-control mb-2"
                                               placeholder="<?= lang('label') ?>"
                                               data-field="label_custom_field_<?= $i ?>"
                                               aria-label="label"
                                        />

                                        <div class="d-flex">
                                            <div class="form-check form-switch me-4">
                                                <input class="form-check-input display-switch" type="checkbox"
                                                       id="display-custom-field-<?= $i ?>"
                                                       data-field="display_custom_field_<?= $i ?>">
                                                <label class="form-check-label" for="display-custom-field-<?= $i ?>">
                                                    <?= lang('display') ?>
                                                </label>
                                            </div>

                                            <div class="form-check form-switch">
                                                <input class="form-check-input require-switch" type="checkbox"
                                                       id="require-custom-field-<?= $i ?>"
                                                       data-field="require_custom_field_<?= $i ?>">
                                                <label class="form-check-label" for="require-custom-field-<?= $i ?>">
                                                    <?= lang('require') ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>


                        <h5 class="text-black-50 mb-3 fw-light">
                            <?= lang('options') ?>
                        </h5>

                        <div class="row">
                            <div class="col-12">
                                <div class="border rounded mb-3 p-3">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="customer-notifications"
                                                   data-field="customer_notifications">
                                            <label class="form-check-label" for="customer-notifications">
                                                <?= lang('customer_notifications') ?>
                                            </label>
                                        </div>

                                        <div class="form-text text-muted">
                                            <small>
                                                <?= lang('customer_notifications_hint') ?>
                                            </small>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="limit-customer-access"
                                                   data-field="limit_customer_access">
                                            <label class="form-check-label" for="limit-customer-access">
                                                <?= lang('limit_customer_access') ?>
                                            </label>
                                        </div>

                                        <div class="form-text text-muted">
                                            <small>
                                                <?= lang('limit_customer_access_hint') ?>
                                            </small>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="require-captcha"
                                                   data-field="require_captcha">
                                            <label class="form-check-label" for="require-captcha">
                                                CAPTCHA
                                            </label>
                                        </div>

                                        <div class="form-text text-muted">
                                            <small>
                                                <?= lang('require_captcha_hint') ?>
                                            </small>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="display-any-provider"
                                                   data-field="display_any_provider">
                                            <label class="form-check-label" for="display-any-provider">
                                                <?= lang('any_provider') ?>
                                            </label>
                                        </div>

                                        <div class="form-text text-muted">
                                            <small>
                                                <?= lang('display_any_provider_hint') ?>
                                            </small>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="display-login-button"
                                                   data-field="display_login_button">
                                            <label class="form-check-label" for="display-login-button">
                                                <?= lang('login_button') ?>
                                            </label>
                                        </div>

                                        <div class="form-text text-muted">
                                            <small>
                                                <?= lang('display_login_button_hint') ?>
                                            </small>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox"
                                                   id="display-delete-personal-information"
                                                   data-field="display_delete_personal_information">
                                            <label class="form-check-label" for="display-delete-personal-information">
                                                <?= lang('delete_personal_information') ?>
                                            </label>
                                        </div>

                                        <div class="form-text text-muted">
                                            <small>
                                                <?= lang('delete_personal_information_hint') ?>
                                            </small>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="disable-booking"
                                                   data-field="disable_booking">
                                            <label class="form-check-label" for="disable-booking">
                                                <?= lang('disable_booking') ?>
                                            </label>
                                        </div>

                                        <div class="form-text text-muted">
                                            <small>
                                                <?= lang('disable_booking_hint') ?>
                                            </small>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3" hidden>
                                        <label class="form-label" for="disable-booking-message">
                                            <?= lang('display_message') ?>
                                        </label>
                                        <textarea id="disable-booking-message" cols="30" rows="10"
                                                  class="mb-3"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php slot('after_primary_fields'); ?>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>

<?php end_section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/booking_settings_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/booking_settings.js') ?>"></script>

<?php end_section('scripts'); ?>



