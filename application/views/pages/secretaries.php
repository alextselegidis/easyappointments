<?php
/**
 * @var array $providers
 * @var string $timezones
 * @var array $privileges
 */
?>

<?php extend('layouts/backend_layout') ?>

<?php section('content') ?>

<div class="container-fluid backend-page" id="secretaries-page">
    <div class="row" id="secretaries">
        <div id="filter-secretaries" class="filter-records column col-12 col-md-5">
            <form class="mb-4">
                <div class="input-group">
                    <input type="text" class="key form-control">

                    <button class="filter btn btn-outline-secondary" type="submit"
                            data-tippy-content="<?= lang('filter') ?>">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <h3><?= lang('secretaries') ?></h3>

            <div class="results"></div>
        </div>

        <div class="record-details column col-12 col-md-7">
            <div class="btn-toolbar mb-4">
                <div class="add-edit-delete-group btn-group">
                    <button id="add-secretary" class="btn btn-primary">
                        <i class="fas fa-plus-square me-2"></i>
                        <?= lang('add') ?>
                    </button>
                    <button id="edit-secretary" class="btn btn-outline-secondary" disabled="disabled">
                        <i class="fas fa-edit me-2"></i>
                        <?= lang('edit') ?>
                    </button>
                    <button id="delete-secretary" class="btn btn-outline-secondary" disabled="disabled">
                        <i class="fas fa-trash-alt me-2"></i>
                        <?= lang('delete') ?>
                    </button>
                </div>

                <div class="save-cancel-group btn-group" style="display:none;">
                    <button id="save-secretary" class="btn btn-primary">
                        <i class="fas fa-check-square me-2"></i>
                        <?= lang('save') ?>
                    </button>
                    <button id="cancel-secretary" class="btn btn-outline-secondary">
                        <i class="fas fa-ban me-2"></i>
                        <?= lang('cancel') ?>
                    </button>
                </div>
            </div>

            <h3><?= lang('details') ?></h3>

            <div class="form-message alert" style="display:none;"></div>

            <input type="hidden" id="secretary-id" class="record-id">

            <div class="row">
                <div class="secretary-details col-12 col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="secretary-first-name">
                            <?= lang('first_name') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="secretary-first-name" class="form-control required" maxlength="256">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="secretary-last-name">
                            <?= lang('last_name') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="secretary-last-name" class="form-control required" maxlength="512">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="secretary-email">
                            <?= lang('email') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="secretary-email" class="form-control required" maxlength="512">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="secretary-phone-number">
                            <?= lang('phone_number') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="secretary-phone-number" class="form-control required" maxlength="128">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="secretary-mobile-number">
                            <?= lang('mobile_number') ?>

                        </label>
                        <input id="secretary-mobile-number" class="form-control" maxlength="128">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="secretary-address">
                            <?= lang('address') ?>
                        </label>
                        <input id="secretary-address" class="form-control" maxlength="256">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="secretary-city">
                            <?= lang('city') ?>
                        </label>
                        <input id="secretary-city" class="form-control" maxlength="256">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="secretary-state">
                            <?= lang('state') ?>
                        </label>
                        <input id="secretary-state" class="form-control" maxlength="128">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="secretary-zip-code">
                            <?= lang('zip_code') ?>
                        </label>
                        <input id="secretary-zip-code" class="form-control" maxlength="64">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="secretary-notes">
                            <?= lang('notes') ?>
                        </label>
                        <textarea id="secretary-notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="secretary-settings col-12 col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="secretary-username">
                            <?= lang('username') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="secretary-username" class="form-control required" maxlength="256">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="secretary-password">
                            <?= lang('password') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input type="password" id="secretary-password" class="form-control required"
                               maxlength="512" autocomplete="new-password">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="secretary-password-confirm">
                            <?= lang('retype_password') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input type="password" id="secretary-password-confirm" class="form-control required"
                               maxlength="512" autocomplete="new-password">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="secretary-calendar-view">
                            <?= lang('calendar') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <select id="secretary-calendar-view" class="form-control required">
                            <option value="default">Default</option>
                            <option value="table">Table</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="secretary-timezone">
                            <?= lang('timezone') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <?= render_timezone_dropdown('id="secretary-timezone" class="form-control required"') ?>
                    </div>

                    <br>

                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="secretary-notifications">
                        <label class="custom-form-label" for="secretary-notifications">
                            <?= lang('receive_notifications') ?>
                        </label>
                    </div>

                    <br>

                    <h4><?= lang('providers') ?></h4>
                    <div id="secretary-providers" class="card card-body bg-light border-light"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php section('content') ?>

<?php section('scripts') ?>

<script src="<?= asset_url('assets/js/pages/backend_secretaries_helper.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/backend_secretaries.js') ?>"></script>
<script>
    var GlobalVariables = {
        csrfToken: <?= json_encode($this->security->get_csrf_hash()) ?>,
        baseUrl: <?= json_encode(config('base_url')) ?>,
        dateFormat: <?= json_encode(setting('date_format')) ?>,
        timeFormat: <?= json_encode(setting('time_format')) ?>,
        firstWeekday: <?= json_encode(setting('first_weekday')) ?>,
        providers: <?= json_encode($providers) ?>,
        timezones: <?= json_encode($timezones) ?>,
        user: {
            id: <?= session('user_id') ?>,
            email: <?= json_encode(session('user_email')) ?>,
            timezone: <?= json_encode(session('timezone')) ?>,
            role_slug: <?= json_encode(session('role_slug')) ?>,
            privileges: <?= json_encode($privileges) ?>
        }
    };

    $(function () {
        BackendSecretaries.initialize(true);
    });
</script>

<?php section('scripts') ?>

