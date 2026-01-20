<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>

<div id="configuration-settings-page" class="container backend-page">
    <div id="configuration-settings">
        <div class="row">
            <div class="col-sm-3 offset-sm-1">
                <?php component('settings_nav'); ?>
            </div>
            <div class="col-sm-6">
                <form>
                    <fieldset>
                        <div class="d-flex justify-content-between align-items-center border-bottom mb-4 py-2">
                            <h4 class="text-black-50 mb-0 fw-light">
                                <?= lang('configuration') ?>
                            </h4>

                            <?php if (can('edit', PRIV_CONFIG_SETTINGS)): ?>
                                <button type="button" id="reset-config" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-rotate-right me-2"></i>
                                    <?= lang('reset') ?>
                                </button>
                                <button type="button" id="save-config" class="btn btn-primary">
                                    <i class="fas fa-check-square me-2"></i>
                                    <?= lang('save') ?>
                                </button>
                            <?php endif; ?>
                        </div>

                        <table class="configs-table table">
                            <thead>
                            <tr>
                                <th><?= lang('name') ?></th>
                                <th><?= lang('value') ?></th>
                                <th><?= lang('description') ?></th>
                                <th/>
                            </tr>
                            </thead>
                            <tbody><!-- Dynamic Content --></tbody>
                        </table>

                        <div class="text-end mb-5">
                            <button class="btn btn-outline-secondary" id="add-row" type="button">
                                <i class="fas fa-plus"></i>
                                <?= lang('add') ?>
                            </button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>

<?php end_section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/js/http/configuration_settings_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/configuration_settings.js') ?>"></script>

<?php end_section('scripts'); ?>