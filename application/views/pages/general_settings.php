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

<script src="<?= asset_url('assets/js/pages/backend_settings_general_helper.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/backend_settings_general.js') ?>"></script>
<script>
    var GlobalVariables = {
        csrfToken: <?= json_encode($this->security->get_csrf_hash()) ?>,
        baseUrl: <?= json_encode(config('base_url')) ?>,
        dateFormat: <?= json_encode(setting('date_format')) ?>,
        timeFormat: <?= json_encode(setting('time_format')) ?>,
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
        BackendSettingsGeneral.initialize(true);
    });
</script>

<div id="general-page" class="container-fluid backend-page">
    <div id="general">
            <form>
                <fieldset>
                    <legend class="border-bottom mb-4">
                        <?= lang('General_settings') ?>
                        <?php if ($privileges[PRIV_SYSTEM_SETTINGS]['edit'] == TRUE): ?>
                            <button type="button" class="save-settings btn btn-primary btn-sm mb-2"
                                    data-tippy-content="<?= lang('save') ?>">
                                <i class="fas fa-check-square me-2"></i>
                                <?= lang('save') ?>
                            </button>
                        <?php endif ?>
                    </legend>

                    <div class="wrapper row">
                        <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label class="form-label" for="company-name">
                                    <?= lang('company_name') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <input id="company-name" data-field="company_name" class="required form-control">
                                <div class="form-text text-muted">
                                    <?= lang('company_name_hint') ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="company-email">
                                    <?= lang('company_email') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <input id="company-email" data-field="company_email" class="required form-control">
                                <div class="form-text text-muted">
                                    <?= lang('company_email_hint') ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="company-link">
                                    <?= lang('company_link') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <input id="company-link" data-field="company_link" class="required form-control">
                                <div class="form-text text-muted">
                                    <?= lang('company_link_hint') ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="date-format">
                                    <?= lang('date_format') ?>
                                </label>
                                <select class="form-control" id="date-format" data-field="date_format">
                                    <option value="DMY">DMY</option>
                                    <option value="MDY">MDY</option>
                                    <option value="YMD">YMD</option>
                                </select>
                                <div class="form-text text-muted">
                                    <?= lang('date_format_hint') ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="time-format">
                                    <?= lang('time_format') ?>
                                </label>
                                <select class="form-control" id="time-format" data-field="time_format">
                                    <option value="<?= TIME_FORMAT_REGULAR ?>">H:MM AM/PM</option>
                                    <option value="<?= TIME_FORMAT_MILITARY ?>">HH:MM</option>
                                </select>
                                <div class="form-text text-muted">
                                    <?= lang('time_format_hint') ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="first-weekday">
                                    <?= lang('first_weekday') ?>
                                </label>
                                <select class="form-control" id="first-weekday" data-field="first_weekday">
                                    <option value="sunday"><?= lang('sunday') ?></option>
                                    <option value="monday"><?= lang('monday') ?></option>
                                    <option value="tuesday"><?= lang('tuesday') ?></option>
                                    <option value="wednesday"><?= lang('wednesday') ?></option>
                                    <option value="thursday"><?= lang('thursday') ?></option>
                                    <option value="friday"><?= lang('friday') ?></option>
                                    <option value="saturday"><?= lang('saturday') ?></option>
                                </select>
                                <div class="form-text text-muted">
                                    <?= lang('first_weekday_hint') ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label class="form-label" for="google-analytics-code">
                                    Google Analytics ID</label>
                                <input id="google-analytics-code" placeholder="UA-XXXXXXXX-XX or G-XXXXXXXXXX"
                                       data-field="google_analytics_code" class="form-control">
                                <div class="form-text text-muted">
                                    <?= lang('google_analytics_code_hint') ?>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="api-token">API Token</label>
                                <input id="api-token" data-field="api_token" class="form-control">
                                <div class="form-text text-muted">
                                    <?= lang('api_token_hint') ?>
                                </div>
                            </div>


                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
</div>

<?php section('content') ?>
