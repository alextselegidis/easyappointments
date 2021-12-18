<?php extend('layouts/backend_layout') ?>

<?php section('content') ?>

<div id="account-page" class="container backend-page">
    <div id="account">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <form>
                    <fieldset>
                        <legend class="d-flex justify-content-between align-items-center border-bottom mb-4 py-2">
                            <?= lang('personal_information') ?>

                            <?php if (can('edit', PRIV_USER_SETTINGS)): ?>
                                <button type="button" id="save-settings" class="btn btn-primary btn-sm">
                                    <i class="fas fa-check-square me-2"></i>
                                    <?= lang('save') ?>
                                </button>
                            <?php endif ?>
                        </legend>

                        <div class="row">
                            <div class="col-lg-6">
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
                            </div>

                            <div class="col-lg-6">
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

                                <div class="form-check form-switch">
                                    <input class="form-check-input" id="notifications" type="checkbox">
                                    <label class="form-check-label" for="notifications">
                                        <?= lang('receive_notifications') ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
        
    </div>
</div>

<?php section('content') ?>

<?php section('scripts') ?>

<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/validation.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/account_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/account.js') ?>"></script>

<?php section('scripts') ?>
