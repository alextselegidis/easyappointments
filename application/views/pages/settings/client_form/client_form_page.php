<?php
/**
 * @var array $system_settings
 * @var array $user_settings
 * @var string $timezones
 * @var array $privileges
 */
?>

<?php extend('layouts/backend/backend_layout') ?>

<?php section('content') ?>

<script src="<?= asset_url('assets/js/pages/settings/backend_settings_client_form_helper.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/settings/backend_settings_client_form.js') ?>"></script>
<script>
    var GlobalVariables = {
        csrfToken: <?= json_encode($this->security->get_csrf_hash()) ?>,
        baseUrl: <?= json_encode(config('base_url')) ?>,
        dateFormat: <?= json_encode(setting('date_format')) ?>,
        timeFormat: <?= json_encode(setting('time_format')) ?>,
        firstWeekday: <?= json_encode(setting('first_weekday')) ?>,
        timezones: <?= json_encode($timezones) ?>,
        settings: {
            system: <?= json_encode($system_settings) ?>,
        },
        user: {
            id: <?= session('user_id') ?>,
            email: <?= json_encode(session('user_email')) ?>,
            timezone: <?= json_encode(session('timezone')) ?>,
            role_slug: <?= json_encode(session('role_slug')) ?>,
            privileges: <?= json_encode($privileges) ?>
        }
    };

    $(function () {
        BackendSettingsClientForm.initialize(true);
    });
</script>

<div id="client-form-page" class="container-fluid backend-page">
    <div id="client-form">
        <form>
            <fieldset>
                <legend class="border-bottom mb-4">
                    <?= lang('client_form') ?>
                    
                    <?php if ($privileges[PRIV_SYSTEM_SETTINGS]['edit'] == TRUE): ?>
                        <button type="button" class="save-settings btn btn-primary btn-sm mb-2"
                                data-tippy-content="<?= lang('save') ?>">
                            <i class="fas fa-check-square me-2"></i>
                            <?= lang('save') ?>
                        </button>
                    <?php endif ?>
                </legend>

                <div class="wrapper row">
                    <div class="col-12 col-sm-3">
                        <div class="mb-3">
                            <label class="form-label" for="show-phone-number">
                                <?= lang('phone_number') ?>
                            </label>
                            <button id="show-phone-number" data-field="show_phone_number" type="button"
                                    class="hide-toggle form-control form-sub-button">
                                <span class="hide-toggle-visible" hidden>
                                    <img src="<?= base_url('assets/img/eye.svg') ?>" alt="eye"/>
                                    <?= lang('visible') ?>
                                </span>
                                <span class="hide-toggle-hidden">
                                    <img src="<?= base_url('assets/img/eye-hidden.svg') ?>" alt="eye-hidden"/> 
                                    <?= lang('hidden') ?>
                                </span>
                            </button>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="show-address">
                                <?= lang('address') ?>
                            </label>
                            <button id="show-address" data-field="show_address" type="button"
                                    class="hide-toggle form-control form-sub-button">
                                <span class="hide-toggle-visible" hidden>
                                    <img src="<?= base_url('assets/img/eye.svg') ?>" alt="eye"/>
                                    <?= lang('visible') ?>
                                </span>
                                <span class="hide-toggle-hidden">
                                    <img src="<?= base_url('assets/img/eye-hidden.svg') ?>" alt="eye-hidden"/> 
                                    <?= lang('hidden') ?>
                                </span>
                            </button>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="show-city">
                                <?= lang('city') ?>
                            </label>
                            <button id="show-city" data-field="show_city" type="button"
                                    class="hide-toggle form-control form-sub-button">
                                <span class="hide-toggle-visible" hidden>
                                    <img src="<?= base_url('assets/img/eye.svg') ?>" alt="eye"/>
                                    <?= lang('visible') ?>
                                </span>
                                <span class="hide-toggle-hidden">
                                    <img src="<?= base_url('assets/img/eye-hidden.svg') ?>" alt="eye-hidden"/> 
                                    <?= lang('hidden') ?>
                                </span>
                            </button>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="show-zip-code">
                                <?= lang('zip_code') ?>
                            </label>
                            <button id="show-zip-code" data-field="show_zip_code" type="button"
                                    class="hide-toggle form-control form-sub-button">
                                <span class="hide-toggle-visible" hidden>
                                    <img src="<?= base_url('assets/img/eye.svg') ?>" alt="eye"/>
                                    <?= lang('visible') ?>
                                </span>
                                <span class="hide-toggle-hidden">
                                    <img src="<?= base_url('assets/img/eye-hidden.svg') ?>" alt="eye-hidden"/> 
                                    <?= lang('hidden') ?>
                                </span>
                            </button>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="show-notes">
                                <?= lang('notes') ?>
                            </label>
                            <button id="show-notes" data-field="show_notes" type="button"
                                    class="hide-toggle form-control form-sub-button">
                                <span class="hide-toggle-visible" hidden>
                                    <img src="<?= base_url('assets/img/eye.svg') ?>" alt="eye"/>
                                    <?= lang('visible') ?>
                                </span>
                                <span class="hide-toggle-hidden">
                                    <img src="<?= base_url('assets/img/eye-hidden.svg') ?>" alt="eye-hidden"/> 
                                    <?= lang('hidden') ?>
                                </span>
                            </button>
                        </div>
                    </div>

                    <div class="col-12 col-sm-9">
                        <div class="mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="customer-notifications">
                                <label class="form-label" class="custom-form-label" for="customer-notifications">
                                    <?= lang('customer_notifications') ?>
                                </label>
                            </div>
                            <span class="form-text text-muted">
                                    <?= lang('customer_notifications_hint') ?>
                                </span>
                        </div>
                        <div class="mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="require-captcha">
                                <label class="form-label" class="custom-form-label" for="require-captcha">
                                    CAPTCHA
                                </label>
                            </div>
                            <span class="form-text text-muted">
                                    <?= lang('require_captcha_hint') ?>
                                </span>
                        </div>
                        <div class="mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="require-phone-number">
                                <label class="form-label" class="custom-form-label" for="require-phone-number">
                                    <?= lang('phone_number') ?>
                                </label>
                            </div>
                            <span class="form-text text-muted">
                                    <?= lang('require_phone_number_hint') ?>
                                </span>
                        </div>
                        <div class="mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="display-any-provider">
                                <label class="form-label" class="custom-form-label" for="display-any-provider">
                                    <?= lang('any_provider') ?>
                                </label>
                            </div>
                            <span class="form-text text-muted">
                                    <?= lang('display_any_provider_hint') ?>
                                </span>
                        </div>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>

<?php section('content') ?>
