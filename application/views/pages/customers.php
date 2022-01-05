<?php
/**
 * @var string $timezones
 * @var array $privileges
 * @var array $require_first_name
 * @var array $require_last_name
 * @var array $require_email
 * @var array $require_phone_number
 * @var array $require_address
 * @var array $require_city
 * @var array $require_zip_code
 */
?>

<?php extend('layouts/backend_layout') ?>

<?php section('content') ?>

<div class="container-fluid backend-page" id="customers-page">
    <div class="row" id="customers">
        <div id="filter-customers" class="filter-records col col-12 col-md-5">
            <form class="mb-4">
                <div class="input-group">
                    <input type="text" class="key form-control">

                    <div class="input-group-addon">
                        <div>
                            <button class="filter btn btn-outline-secondary" type="submit"
                                    data-tippy-content="<?= lang('filter') ?>">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="clear btn btn-outline-secondary" type="button"
                                    data-tippy-content="<?= lang('clear') ?>">
                                <i class="fas fa-redo-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <h3><?= lang('customers') ?></h3>
            <div class="results"></div>
        </div>

        <div class="record-details col-12 col-md-7">
            <div class="btn-toolbar mb-4">
                <div id="add-edit-delete-group" class="btn-group">
                    <?php if ($privileges[PRIV_CUSTOMERS]['add'] === TRUE): ?>
                        <button id="add-customer" class="btn btn-primary">
                            <i class="fas fa-plus-square me-2"></i>
                            <?= lang('add') ?>
                        </button>
                    <?php endif ?>

                    <?php if ($privileges[PRIV_CUSTOMERS]['edit'] === TRUE): ?>
                        <button id="edit-customer" class="btn btn-outline-secondary" disabled="disabled">
                            <i class="fas fa-edit me-2"></i>
                            <?= lang('edit') ?>
                        </button>
                    <?php endif ?>

                    <?php if ($privileges[PRIV_CUSTOMERS]['delete'] === TRUE): ?>
                        <button id="delete-customer" class="btn btn-outline-secondary" disabled="disabled">
                            <i class="fas fa-trash-alt me-2"></i>
                            <?= lang('delete') ?>
                        </button>
                    <?php endif ?>
                </div>

                <div id="save-cancel-group" class="btn-group" style="display:none;">
                    <button id="save-customer" class="btn btn-primary">
                        <i class="fas fa-check-square me-2"></i>
                        <?= lang('save') ?>
                    </button>
                    <button id="cancel-customer" class="btn btn-outline-secondary">
                        <i class="fas fa-ban me-2"></i>
                        <?= lang('cancel') ?>
                    </button>
                </div>
            </div>

            <input id="customer-id" type="hidden">

            <div class="row">
                <div class="col-12 col-md-6" style="margin-left: 0;">
                    <h3><?= lang('details') ?></h3>

                    <div id="form-message" class="alert" style="display:none;"></div>

                    <div class="mb-3">
                        <label for="first-name" class="form-label">
                            <?= lang('first_name') ?>
                            <?php if ($require_first_name): ?>
                                <span class="text-danger">*</span>
                            <?php endif ?>
                        </label>
                        <input type="text" id="first-name"
                               class="<?= $require_first_name ? 'required' : '' ?> form-control" maxlength="100"/>
                    </div>

                    <div class="mb-3">
                        <label for="last-name" class="form-label">
                            <?= lang('last_name') ?>
                            <?php if ($require_last_name): ?>
                                <span class="text-danger">*</span>
                            <?php endif ?>
                        </label>
                        <input type="text" id="last-name"
                               class="<?= $require_last_name ? 'required' : '' ?> form-control" maxlength="120"/>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <?= lang('email') ?>
                            <?php if ($require_email): ?>
                                <span class="text-danger">*</span>
                            <?php endif ?>
                        </label>
                        <input type="text" id="email"
                               class="<?= $require_email ? 'required' : '' ?> form-control" maxlength="120"/>
                    </div>

                    <div class="mb-3">
                        <label for="phone-number" class="form-label">
                            <?= lang('phone_number') ?>
                            <?php if ($require_phone_number): ?>
                                <span class="text-danger">*</span>
                            <?php endif ?>
                        </label>
                        <input type="text" id="phone-number" maxlength="60"
                               class="<?= $require_phone_number ? 'required' : '' ?> form-control"/>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">
                            <?= lang('address') ?>
                            <?php if ($require_address): ?>
                                <span class="text-danger">*</span>
                            <?php endif ?>
                        </label>
                        <input type="text" id="address" class="<?= $require_address ? 'required' : '' ?> form-control"
                               maxlength="120"/>
                    </div>

                    <div class="mb-3">
                        <label for="city" class="form-label">
                            <?= lang('city') ?>
                            <?php if ($require_city): ?>
                                <span class="text-danger">*</span>
                            <?php endif ?>
                        </label>
                        <input type="text" id="city" class="<?= $require_city ? 'required' : '' ?> form-control"
                               maxlength="120"/>
                    </div>

                    <div class="mb-3">
                        <label for="zip-code" class="form-label">
                            <?= lang('zip_code') ?>
                            <?php if ($require_zip_code): ?>
                                <span class="text-danger">*</span>
                            <?php endif ?>
                        </label>
                        <input type="text" id="zip-code" class="<?= $require_zip_code ? 'required' : '' ?> form-control"
                               maxlength="120"/>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="language">
                            <?= lang('language') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <select id="language" class="form-control required"></select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="timezone">
                            <?= lang('timezone') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <?php component('timezone_dropdown', 'id="timezone" class="form-control required"') ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="notes">
                            <?= lang('notes') ?>
                        </label>
                        <textarea id="notes" rows="4" class="form-control"></textarea>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <h3><?= lang('appointments') ?></h3>
                    <div id="customer-appointments" class="card bg-light border-light"></div>
                    <div id="appointment-details" class="card bg-light border-light d-none"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php section('content') ?>

<?php section('scripts') ?>

<script src="<?= asset_url('assets/vendor/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.min.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/backend_customers_helper.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/backend_customers.js') ?>"></script>
<script>
    var GlobalVariables = {
        csrfToken: <?= json_encode($this->security->get_csrf_hash()) ?>,
        baseUrl: <?= json_encode(config('base_url')) ?>,
        dateFormat: <?= json_encode(setting('date_format')) ?>,
        timeFormat: <?= json_encode(setting('time_format')) ?>,
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
        BackendCustomers.initialize(true);
    });
</script>

<?php section('scripts') ?>
