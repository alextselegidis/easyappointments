<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>

<div id="api-settings-page" class="container backend-page">
    <div class="row">
        <div class="col-sm-3 offset-sm-1">
            <?php component('settings_nav'); ?>
        </div>
        <div id="api-settings" class="col-sm-6">
            <form>
                <fieldset>
                    <div class="d-flex justify-content-between align-items-center border-bottom mb-4 py-2">
                        <h4 class="text-black-50 mb-0 fw-light">
                            <?= lang('api') ?>
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
                            <div class=" mb-3">
                                <label class="form-label" for="api-token">
                                    <?= lang('api_token') ?>
                                </label>
                                <div class="input-group">
                                    <input id="api-token" type="password" class="form-control" data-field="api_token">
                                    <button type="button" id="hide-token" class="btn btn-outline-primary">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                    <button type="button" id="copy-token" class="btn btn-outline-primary">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <button type="button" id="clear-token" class="btn btn-outline-primary">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button type="button" id="generate-token" class="btn btn-primary">
                                        <i class="fas fa-rotate"></i>
                                    </button>
                                    <div class="form-text text-muted">
                                        <small>
                                            <?= lang('api_token_hint') ?>
                                        </small>
                                    </div>
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

<script src="<?= asset_url('assets/js/http/api_settings_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/api_settings.js') ?>"></script>

<?php end_section('scripts'); ?>
