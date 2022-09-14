<?php extend('layouts/backend_layout') ?>

<?php section('content') ?>

<div id="google-analytics-settings-page" class="container backend-page">
    <div id="google-analytics-settings">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <form>
                    <fieldset>
                        <div class="d-flex justify-content-between align-items-center border-bottom mb-4 py-2">
                            <h4 class="text-black-50 mb-0 fw-light">
                                <?= lang('google_analytics') ?>
                            </h4>

                            <?php if (can('edit', PRIV_SYSTEM_SETTINGS)): ?>
                                <button type="button" id="save-settings" class="btn btn-primary">
                                    <i class="fas fa-check-square me-2"></i>
                                    <?= lang('save') ?>
                                </button>
                            <?php endif ?>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label" for="google-analytics-code">
                                        <?= lang('google_analytics_code') ?>
                                    </label>
                                    <input id="google-analytics-code" placeholder="UA-XXXXXXXX-XX or G-XXXXXXXXXX"
                                           class="form-control" data-field="google_analytics_code">
                                    <div class="form-text text-muted">
                                        <small>
                                            <?= lang('google_analytics_code_hint') ?>
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
<script src="<?= asset_url('assets/js/http/google_analytics_settings_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/google_analytics_settings.js') ?>"></script>

<?php section('scripts') ?>
