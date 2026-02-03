<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>

<div id="jitsi-settings-page" class="container backend-page py-3">
    <div class="row">
        <div class="col-sm-3 offset-sm-1">
            <?php component('settings_nav'); ?>
        </div>
        <div id="jitsi-settings" class="col-sm-6">
            <form>
                <fieldset>
                    <div class="d-flex justify-content-between align-items-center border-bottom mb-4 py-2">
                        <h4 class="text-black-50 mb-0 fw-light">
                            <?= lang('jitsi') ?>
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
                                    <?= lang('jitsi_info') ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="jitsi-enabled"
                                           data-field="jitsi_enabled">
                                    <label class="form-check-label" for="jitsi-enabled">
                                        <?= lang('active') ?>
                                    </label>
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

<script src="<?= asset_url('assets/js/http/jitsi_settings_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/jitsi_settings.js') ?>"></script>

<?php end_section('scripts'); ?>



