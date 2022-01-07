<?php
/**
 * @var string $timezones
 * @var array $privileges
 */
?>

<?php extend('layouts/backend_layout') ?>

<?php section('content') ?>

<div class="container-fluid backend-page" id="admins-page">
    <div class="row" id="admins">
        <div id="filter-admins" class="filter-records column col-12 col-md-5">
            <form class="mb-4">
                <div class="input-group">
                    <input type="text" class="key form-control">
                    
                    <button class="filter btn btn-outline-secondary" type="submit"
                            data-tippy-content="<?= lang('filter') ?>">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <h3><?= lang('admins') ?></h3>

            <div class="results"></div>
        </div>

        <div class="record-details column col-12 col-md-7">
            <div class="btn-toolbar mb-4">
                <div class="add-edit-delete-group btn-group">
                    <button id="add-admin" class="btn btn-primary">
                        <i class="fas fa-plus-square me-2"></i>
                        <?= lang('add') ?>
                    </button>
                    <button id="edit-admin" class="btn btn-outline-secondary" disabled="disabled">
                        <i class="fas fa-edit me-2"></i>
                        <?= lang('edit') ?>
                    </button>
                    <button id="delete-admin" class="btn btn-outline-secondary" disabled="disabled">
                        <i class="fas fa-trash-alt me-2"></i>
                        <?= lang('delete') ?>
                    </button>
                </div>

                <div class="save-cancel-group btn-group" style="display:none;">
                    <button id="save-admin" class="btn btn-primary">
                        <i class="fas fa-check-square me-2"></i>
                        <?= lang('save') ?>
                    </button>
                    <button id="cancel-admin" class="btn btn-outline-secondary">
                        <i class="fas fa-ban me-2"></i>
                        <?= lang('cancel') ?>
                    </button>
                </div>
            </div>

            <h3><?= lang('details') ?></h3>

            <div class="form-message alert" style="display:none;"></div>

            <input type="hidden" id="admin-id" class="record-id">

            <div class="row">
                <div class="admin-details col-12 col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="admin-first-name">
                            <?= lang('first_name') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="admin-first-name" class="form-control required" maxlength="256">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="admin-last-name">
                            <?= lang('last_name') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="admin-last-name" class="form-control required" maxlength="512">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="admin-email">
                            <?= lang('email') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="admin-email" class="form-control required" maxlength="512">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="admin-phone-number">
                            <?= lang('phone_number') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="admin-phone-number" class="form-control required" maxlength="128">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="admin-mobile-number">
                            <?= lang('mobile_number') ?>

                        </label>
                        <input id="admin-mobile-number" class="form-control" maxlength="128">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="admin-address">
                            <?= lang('address') ?>
                        </label>
                        <input id="admin-address" class="form-control" maxlength="256">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="admin-city">
                            <?= lang('city') ?>

                        </label>
                        <input id="admin-city" class="form-control" maxlength="256">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="admin-state">
                            <?= lang('state') ?>
                        </label>
                        <input id="admin-state" class="form-control" maxlength="128">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="admin-zip-code">
                            <?= lang('zip_code') ?>
                        </label>
                        <input id="admin-zip-code" class="form-control" maxlength="64">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="admin-notes">
                            <?= lang('notes') ?>
                        </label>
                        <textarea id="admin-notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="admin-settings col-12 col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="admin-username">
                            <?= lang('username') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="admin-username" class="form-control required" maxlength="256">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="admin-password">
                            <?= lang('password') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input type="password" id="admin-password" class="form-control required" maxlength="512"
                               autocomplete="new-password">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="admin-password-confirm">
                            <?= lang('retype_password') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input type="password" id="admin-password-confirm" class="form-control required"
                               maxlength="512" autocomplete="new-password">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="admin-calendar-view">
                            <?= lang('calendar') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <select id="admin-calendar-view" class="form-control required">
                            <option value="default">Default</option>
                            <option value="table">Table</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="admin-timezone">
                            <?= lang('timezone') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <?= render_timezone_dropdown('id="admin-timezone" class="form-control required"') ?>
                    </div>

                    <br>

                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="admin-notifications">
                        <label class="custom-form-label" for="admin-notifications">
                            <?= lang('receive_notifications') ?>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php section('content') ?>

<?php section('scripts') ?>

<script src="<?= asset_url('assets/js/pages/backend_admins_helper.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/backend_admins.js') ?>"></script>
<script>
    var GlobalVariables = {
        csrfToken: <?= json_encode($this->security->get_csrf_hash()) ?>,
        baseUrl: <?= json_encode(config('base_url')) ?>,
        dateFormat: <?= json_encode(setting('date_format')) ?>,
        timeFormat: <?= json_encode(setting('time_format')) ?>,
        firstWeekday: <?= json_encode(setting('first_weekday')); ?>,
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
        BackendAdmins.initialize(true);
    });
</script>

<?php section('scripts') ?>

