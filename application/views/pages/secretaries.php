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

            <input type="hidden" id="id" class="record-id">

            <div class="row">
                <div class="details col-12 col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="first-name">
                            <?= lang('first_name') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="first-name" class="form-control required" maxlength="256">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="last-name">
                            <?= lang('last_name') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="last-name" class="form-control required" maxlength="512">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="email">
                            <?= lang('email') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="email" class="form-control required" maxlength="512">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="phone-number">
                            <?= lang('phone_number') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="phone-number" class="form-control required" maxlength="128">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="mobile-number">
                            <?= lang('mobile_number') ?>

                        </label>
                        <input id="mobile-number" class="form-control" maxlength="128">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="address">
                            <?= lang('address') ?>
                        </label>
                        <input id="address" class="form-control" maxlength="256">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="city">
                            <?= lang('city') ?>
                        </label>
                        <input id="city" class="form-control" maxlength="256">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="state">
                            <?= lang('state') ?>
                        </label>
                        <input id="state" class="form-control" maxlength="128">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="zip-code">
                            <?= lang('zip_code') ?>
                        </label>
                        <input id="zip-code" class="form-control" maxlength="64">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="notes">
                            <?= lang('notes') ?>
                        </label>
                        <textarea id="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="settings col-12 col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="username">
                            <?= lang('username') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="username" class="form-control required" maxlength="256">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">
                            <?= lang('password') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input type="password" id="password" class="form-control required"
                               maxlength="512" autocomplete="new-password">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password-confirm">
                            <?= lang('retype_password') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input type="password" id="password-confirm" class="form-control required"
                               maxlength="512" autocomplete="new-password">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="calendar-view">
                            <?= lang('calendar') ?>
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
                            <span class="text-danger">*</span>
                        </label>
                        <?= render_timezone_dropdown('id="timezone" class="form-control required"') ?>
                    </div>

                    <br>

                    <div class="form-check form-switch me-4">
                        <input class="form-check-input" type="checkbox" id="notifications">
                        <label class="form-check-label" for="notifications">
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

<script src="<?= asset_url('assets/js/utils/message.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/validation.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/account_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/secretaries_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/secretaries.js') ?>"></script>

<?php section('scripts') ?>

