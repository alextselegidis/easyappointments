<?php
/**
 * @var array $system_settings
 * @var array $user_settings
 * @var string $timezones
 * @var array $privileges
 */
?>

<?php extend('layouts/backend_layout') ?>

<?php section('content') ?>

<div id="current-user-page" class="container-fluid backend-page">
    <div id="current-user">
        <form>
            <div class="row">
                <fieldset class="col-12 col-sm-6 personal-info-wrapper">
                    <legend class="border-bottom mb-4">
                        <?= lang('personal_information') ?>
                        <?php if ($privileges[PRIV_USER_SETTINGS]['edit'] == TRUE): ?>
                            <button type="button" class="save-settings btn btn-primary btn-sm mb-2"
                                    data-tippy-content="<?= lang('save') ?>">
                                <i class="fas fa-check-square me-2"></i>
                                <?= lang('save') ?>
                            </button>
                        <?php endif ?>
                    </legend>

                    <input type="hidden" id="user-id">

                    <div class="mb-3">
                        <label class="form-label" for="first-name">
                            <?= lang('first_name') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="first-name" class="form-control required">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="last-name">
                            <?= lang('last_name') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="last-name" class="form-control required">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="email">
                            <?= lang('email') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="email" class="form-control required">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="phone-number">
                            <?= lang('phone_number') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="phone-number" class="form-control required">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="mobile-number">
                            <?= lang('mobile_number') ?>
                        </label>
                        <input id="mobile-number" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="address">
                            <?= lang('address') ?>
                        </label>
                        <input id="address" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="city">
                            <?= lang('city') ?>
                        </label>
                        <input id="city" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="state">
                            <?= lang('state') ?>
                        </label>
                        <input id="state" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="zip-code">
                            <?= lang('zip_code') ?>
                        </label>
                        <input id="zip-code" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="notes">
                            <?= lang('notes') ?>
                        </label>
                        <textarea id="notes" class="form-control" rows="3"></textarea>
                    </div>
                </fieldset>

                <fieldset class="col-12 col-sm-6 miscellaneous-wrapper">
                    <legend class="border-bottom mb-4">
                        <?= lang('system_login') ?>
                    </legend>

                    <div class="mb-3">
                        <label class="form-label" for="username">
                            <?= lang('username') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="username" class="form-control required">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">
                            <?= lang('password') ?>
                        </label>
                        <input type="password" id="password" class="form-control" autocomplete="new-password">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="retype-password">
                            <?= lang('retype_password') ?>
                        </label>
                        <input type="password" id="retype-password" class="form-control"
                               autocomplete="new-password">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="calendar-view"><?= lang('calendar') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <select id="calendar-view" class="form-control required">
                            <option value="default">Default</option>
                            <option value="table">Table</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="timezone">
                            <?= lang('timezone') ?>
                        </label>
                        <?= render_timezone_dropdown('id="timezone" class="form-control"') ?>
                    </div>

                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="user-notifications">
                        <label class="custom-form-label" for="user-notifications">
                            <?= lang('receive_notifications') ?>
                        </label>
                    </div>
                </fieldset>
            </div>
        </form>
    </div>
</div>

<?php section('content') ?>

<?php section('scripts') ?>

<script src="<?= asset_url('assets/js/pages/backend_settings_current_user_helper.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/backend_settings_current_user.js') ?>"></script>
<script>
    var GlobalVariables = {
        csrfToken: <?= json_encode($this->security->get_csrf_hash()) ?>,
        baseUrl: <?= json_encode(config('base_url')) ?>,
        dateFormat: <?= json_encode(setting('date_format')) ?>,
        timeFormat: <?= json_encode(setting('time_format')) ?>,
        firstWeekday: <?= json_encode(setting('first_weekday')) ?>,
        timezones: <?= json_encode($timezones) ?>,
        settings: {
            user: <?= json_encode($user_settings) ?>,
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
        BackendSettingsCurrentUser.initialize(true);
    });
</script>

<?php section('scripts') ?>
