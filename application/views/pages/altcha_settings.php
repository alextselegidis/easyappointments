<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>

<div id="altcha-settings-page" class="container backend-page py-3">
    <div class="row">
        <div class="col-sm-3">
            <?php component('settings_nav'); ?>
        </div>
        <div id="altcha-settings" class="col-sm-9">
            <form>
                <fieldset>
                    <div class="d-flex justify-content-between align-items-center border-bottom mb-4 py-2">
                        <h4 class="text-black-50 mb-0 fw-light">
                            <?= lang('altcha') ?>
                        </h4>

                        <div>
                            <a href="<?= site_url('integrations') ?>" class="btn btn-outline-primary me-2">
                                <i class="fas fa-chevron-left me-2"></i>
                                <?= lang('back') ?>
                            </a>

                            <?php if (can('edit', PRIV_SYSTEM_SETTINGS)): ?>
                                <button type="button" id="save-settings" class="btn btn-primary">
                                    <i class="fas fa-check-square me-2"></i>
                                    <?= lang('save') ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="form-text text-muted mb-4">
                                    <?= lang('altcha_info') ?>
                                </div>
                            </div>

                            <?php if (!vars('require_captcha')): ?>
                                <div class="alert alert-warning mb-4">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <?= lang('altcha_captcha_not_active_warning') ?>
                                    <a href="<?= site_url('booking_settings') ?>">
                                        <?= lang('booking_settings') ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="altcha-enabled"
                                           data-field="altcha_enabled">
                                    <label class="form-check-label" for="altcha-enabled">
                                        <?= lang('active') ?>
                                    </label>
                                </div>
                                <div class="form-text text-muted small">
                                    <?= lang('altcha_enabled_hint') ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="altcha-hmac-key">
                                    <?= lang('altcha_hmac_key') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" id="altcha-hmac-key" class="form-control"
                                           data-field="altcha_hmac_key">
                                    <button type="button" id="generate-hmac-key" class="btn btn-outline-secondary">
                                        <i class="fas fa-sync-alt me-2"></i>
                                        <?= lang('generate') ?>
                                    </button>
                                </div>
                                <div class="form-text text-muted small">
                                    <?= lang('altcha_hmac_key_hint') ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="altcha-max-number">
                                    <?= lang('altcha_max_number') ?>
                                </label>
                                <input type="number" id="altcha-max-number" class="form-control"
                                       data-field="altcha_max_number" min="10000" max="1000000">
                                <div class="form-text text-muted small">
                                    <?= lang('altcha_max_number_hint') ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="altcha-expires">
                                    <?= lang('altcha_expires') ?>
                                </label>
                                <input type="number" id="altcha-expires" class="form-control"
                                       data-field="altcha_expires" min="60" max="3600">
                                <div class="form-text text-muted small">
                                    <?= lang('altcha_expires_hint') ?>
                                </div>
                            </div>

                        </div>
                    </div>

                </fieldset>
            </form>
        </div>
    </div>
</div>

<?php end_section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/js/http/altcha_settings_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/altcha_settings.js') ?>"></script>

<?php end_section('scripts'); ?>
