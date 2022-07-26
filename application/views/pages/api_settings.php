<?php extend('layouts/backend_layout') ?>

<?php section('content') ?>

<div id="api-settings-page" class="container backend-page">
    <div id="api-settings">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <form>
                    <fieldset>
                        <legend class="d-flex justify-content-between align-items-center border-bottom mb-4 py-2">
                            <?= lang('api') ?>

                            <?php if (can('edit', PRIV_SYSTEM_SETTINGS)): ?>
                                <button type="button" id="save-settings" class="btn btn-primary">
                                    <i class="fas fa-check-square me-2"></i>
                                    <?= lang('save') ?>
                                </button>
                            <?php endif ?>
                        </legend>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label" for="api-token">
                                        <?= lang('api_token') ?>
                                    </label>
                                    <input id="api-token" class="form-control" data-field="api_token">
                                    <div class="form-text text-muted">
                                        <small>
                                            <?= lang('api_token_hint') ?>
                                        </small>
                                    </div>
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
<script src="<?= asset_url('assets/js/http/api_settings_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/api_settings.js') ?>"></script>

<?php section('scripts') ?>
