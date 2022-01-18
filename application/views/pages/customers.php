<?php extend('layouts/backend_layout') ?>

<?php section('content') ?>

<div class="container-fluid backend-page" id="customers-page">
    <div class="row" id="customers">
        <div id="filter-customers" class="filter-records col col-12 col-md-5">
            <form class="mb-4">
                <div class="input-group mb-3">
                    <input type="text" class="key form-control">

                    <button class="filter btn btn-outline-secondary" type="submit"
                            data-tippy-content="<?= lang('filter') ?>">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <h3><?= lang('customers') ?></h3>
            <div class="results"></div>
        </div>

        <div class="record-details col-12 col-md-7">
            <div class="btn-toolbar mb-4">
                <div id="add-edit-delete-group" class="btn-group">
                    <?php if (can('add',PRIV_CUSTOMERS)): ?>
                        <button id="add-customer" class="btn btn-primary">
                            <i class="fas fa-plus-square me-2"></i>
                            <?= lang('add') ?>
                        </button>
                    <?php endif ?>

                    <?php if (can('edit',PRIV_CUSTOMERS)): ?>
                        <button id="edit-customer" class="btn btn-outline-secondary" disabled="disabled">
                            <i class="fas fa-edit me-2"></i>
                            <?= lang('edit') ?>
                        </button>
                    <?php endif ?>

                    <?php if (can('delete',PRIV_CUSTOMERS)): ?>
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
                            <?php if (vars('require_first_name')): ?>
                                <span class="text-danger">*</span>
                            <?php endif ?>
                        </label>
                        <input type="text" id="first-name"
                               class="<?= vars('require_first_name') ? 'required' : '' ?> form-control" maxlength="100"/>
                    </div>

                    <div class="mb-3">
                        <label for="last-name" class="form-label">
                            <?= lang('last_name') ?>
                            <?php if (vars('require_last_name')): ?>
                                <span class="text-danger">*</span>
                            <?php endif ?>
                        </label>
                        <input type="text" id="last-name"
                               class="<?= vars('require_last_name') ? 'required' : '' ?> form-control" maxlength="120"/>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <?= lang('email') ?>
                            <?php if (vars('require_email')): ?>
                                <span class="text-danger">*</span>
                            <?php endif ?>
                        </label>
                        <input type="text" id="email"
                               class="<?= vars('require_email') ? 'required' : '' ?> form-control" maxlength="120"/>
                    </div>

                    <div class="mb-3">
                        <label for="phone-number" class="form-label">
                            <?= lang('phone_number') ?>
                            <?php if (vars('require_phone_number')): ?>
                                <span class="text-danger">*</span>
                            <?php endif ?>
                        </label>
                        <input type="text" id="phone-number" maxlength="60"
                               class="<?= vars('require_phone_number') ? 'required' : '' ?> form-control"/>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">
                            <?= lang('address') ?>
                            <?php if (vars('require_address')): ?>
                                <span class="text-danger">*</span>
                            <?php endif ?>
                        </label>
                        <input type="text" id="address" class="<?= vars('require_address') ? 'required' : '' ?> form-control"
                               maxlength="120"/>
                    </div>

                    <div class="mb-3">
                        <label for="city" class="form-label">
                            <?= lang('city') ?>
                            <?php if (vars('require_city')): ?>
                                <span class="text-danger">*</span>
                            <?php endif ?>
                        </label>
                        <input type="text" id="city" class="<?= vars('require_city') ? 'required' : '' ?> form-control"
                               maxlength="120"/>
                    </div>

                    <div class="mb-3">
                        <label for="zip-code" class="form-label">
                            <?= lang('zip_code') ?>
                            <?php if (vars('require_zip_code')): ?>
                                <span class="text-danger">*</span>
                            <?php endif ?>
                        </label>
                        <input type="text" id="zip-code" class="<?= vars('require_zip_code') ? 'required' : '' ?> form-control"
                               maxlength="120"/>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="language">
                            <?= lang('language') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <select id="language" class="form-control required">
                            <?php foreach (vars('available_languages') as $available_language): ?>
                                <option value="<?= $available_language ?>">
                                    <?= $available_language ?>
                                </option>
                            <?php endforeach ?>
                        </select>
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

<script src="<?= asset_url('assets/js/utils/date.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/message.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/validation.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/customers_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/customers.js') ?>"></script>

<?php section('scripts') ?>
