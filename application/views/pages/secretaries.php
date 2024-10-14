<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>

<div class="container-fluid backend-page" id="secretaries-page">
    <div class="row" id="secretaries">
        <div id="filter-secretaries" class="filter-records column col-12 col-md-5">
            <form class="mb-4">
                <div class="input-group">
                    <input type="text" class="key form-control" aria-label="keyword">

                    <button class="filter btn btn-outline-secondary" type="submit"
                            data-tippy-content="<?= lang('filter') ?>">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <h4 class="text-black-50 mb-3 fw-light">
                <?= lang('secretaries') ?>
            </h4>

            <?php slot('after_page_title'); ?>
            
            <div class="results">
                <!-- JS -->
            </div>
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

                <div class="save-cancel-group" style="display:none;">
                    <button id="save-secretary" class="btn btn-primary">
                        <i class="fas fa-check-square me-2"></i>
                        <?= lang('save') ?>
                    </button>
                    <button id="cancel-secretary" class="btn btn-secondary">
                        <?= lang('cancel') ?>
                    </button>
                </div>

                <?php slot('after_page_actions'); ?>
            </div>

            <h4 class="text-black-50 mb-3 fw-light">
                <?= lang('details') ?>
            </h4>

            <div class="form-message alert" style="display:none;"></div>

            <input type="hidden" id="id" class="record-id">

            <div class="row">
                <div class="details col-12 col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="first-name">
                            <?= lang('first_name') ?>
                            <span class="text-danger" hidden>*</span>
                        </label>
                        <input id="first-name" class="form-control required" maxlength="256" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="last-name">
                            <?= lang('last_name') ?>
                            <span class="text-danger" hidden>*</span>
                        </label>
                        <input id="last-name" class="form-control required" maxlength="512" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="email">
                            <?= lang('email') ?>
                            <span class="text-danger" hidden>*</span>
                        </label>
                        <input id="email" class="form-control required" maxlength="512" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="phone-number">
                            <?= lang('phone_number') ?>
                            <span class="text-danger" hidden>*</span>
                        </label>
                        <input id="phone-number" class="form-control required" maxlength="128" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="mobile-number">
                            <?= lang('mobile_number') ?>

                        </label>
                        <input id="mobile-number" class="form-control" maxlength="128" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="address">
                            <?= lang('address') ?>
                        </label>
                        <input id="address" class="form-control" maxlength="256" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="city">
                            <?= lang('city') ?>
                        </label>
                        <input id="city" class="form-control" maxlength="256" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="state">
                            <?= lang('state') ?>
                        </label>
                        <input id="state" class="form-control" maxlength="128" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="zip-code">
                            <?= lang('zip_code') ?>
                        </label>
                        <input id="zip-code" class="form-control" maxlength="64" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="notes">
                            <?= lang('notes') ?>
                        </label>
                        <textarea id="notes" class="form-control" rows="3" disabled></textarea>
                    </div>

                    <?php slot('after_primary_fields'); ?>
                </div>
                <div class="settings col-12 col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="username">
                            <?= lang('username') ?>
                            <span class="text-danger" hidden>*</span>
                        </label>
                        <input id="username" class="form-control required" maxlength="256" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">
                            <?= lang('password') ?>
                            <span class="text-danger" hidden>*</span>
                        </label>
                        <input type="password" id="password" class="form-control required"
                               maxlength="512" autocomplete="new-password" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password-confirm">
                            <?= lang('retype_password') ?>
                            <span class="text-danger" hidden>*</span>
                        </label>
                        <input type="password" id="password-confirm" class="form-control required"
                               maxlength="512" autocomplete="new-password" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="calendar-view">
                            <?= lang('calendar') ?>
                            <span class="text-danger" hidden>*</span>
                        </label>
                        <select id="calendar-view" class="form-select required" disabled>
                            <option value="default"><?= lang('default') ?></option>
                            <option value="table"><?= lang('table') ?></option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="language">
                            <?= lang('language') ?>
                            <span class="text-danger" hidden>*</span>
                        </label>
                        <select id="language" class="form-select required" disabled>
                            <?php foreach (vars('available_languages') as $available_language): ?>
                                <option value="<?= $available_language ?>">
                                    <?= ucfirst($available_language) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="timezone">
                            <?= lang('timezone') ?>
                            <span class="text-danger" hidden>*</span>
                        </label>
                        <?php component('timezone_dropdown', [
                            'attributes' => 'id="timezone" class="form-control required" disabled',
                            'grouped_timezones' => vars('grouped_timezones'),
                        ]); ?>
                    </div>

                    <?php if (setting('ldap_is_active')): ?>
                        <div class="mb-3">
                            <label for="ldap-dn" class="form-label">
                                <?= lang('ldap_dn') ?>
                            </label>
                            <input type="text" id="ldap-dn" class="form-control" maxlength="100" disabled/>
                        </div>
                    <?php endif; ?>

                    <div>
                        <label class="form-label mb-3">
                            <?= lang('options') ?>
                        </label>
                    </div>

                    <div class="border rounded mb-3 p-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="notifications" disabled>
                            <label class="form-check-label" for="notifications">
                                <?= lang('receive_notifications') ?>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="form-label mb-3">
                            <?= lang('providers') ?>
                        </label>
                    </div>

                    <div id="secretary-providers" class="card card-body bg-white border">
                        <!-- JS -->
                    </div>

                    <?php slot('after_secondary_fields'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php end_section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/js/utils/message.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/validation.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/account_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/secretaries_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/secretaries.js') ?>"></script>

<?php end_section('scripts'); ?>

