<?php extend('layouts/backend_layout') ?>

<?php section('content') ?>

<div id="business-logic-page" class="container backend-page">
    <div id="business-logic">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <form>
                    <fieldset>
                        <legend class="d-flex justify-content-between align-items-center border-bottom mb-4 py-2">
                            <?= lang('business_logic') ?>

                            <?php if (can('edit', PRIV_SYSTEM_SETTINGS)): ?>
                                <button type="button" id="save-settings" class="btn btn-primary btn-sm">
                                    <i class="fas fa-check-square me-2"></i>
                                    <?= lang('save') ?>
                                </button>
                            <?php endif ?>
                        </legend>

                        <h4><?= lang('working_plan') ?></h4>

                        <div class="form-text text-muted mb-4">
                            <?= lang('edit_working_plan_hint') ?>
                        </div>

                        <table class="working-plan table table-striped">
                            <thead>
                                <tr>
                                    <th><?= lang('day') ?></th>
                                    <th><?= lang('start') ?></th>
                                    <th><?= lang('end') ?></th>
                                </tr>
                            </thead>
                            <tbody><!-- Dynamic Content --></tbody>
                        </table>

                        <div class="text-end">
                            <button class="btn btn-outline-secondary" id="apply-global-working-plan" type="button">
                                <i class="fas fa-check"></i>
                                <?= lang('apply_to_all_providers') ?>
                            </button>
                        </div>

                        <h4><?= lang('breaks') ?></h4>

                        <span class="form-text text-muted">
                            <?= lang('edit_breaks_hint') ?>
                        </span>

                        <div class="mt-2">
                            <button type="button" class="add-break btn btn-primary">
                                <i class="fas fa-plus-square"></i>
                                <?= lang('add_break'); ?>
                            </button>
                        </div>

                        <br>

                        <table class="breaks table table-striped mb-4">
                            <thead>
                            <tr>
                                <th><?= lang('day') ?></th>
                                <th><?= lang('start') ?></th>
                                <th><?= lang('end') ?></th>
                                <th><?= lang('actions') ?></th>
                            </tr>
                            </thead>
                            <tbody><!-- Dynamic Content --></tbody>
                        </table>
                        
                        <h4><?= lang('book_advance_timeout') ?></h4>
                        
                        <div class="mb-3">
                            <label for="book-advance-timeout" class="form-label">
                                <?= lang('timeout_minutes') ?>
                            </label>
                            <input id="book-advance-timeout" data-field="book_advance_timeout" class="form-control"
                                   type="number" min="15">
                            <div class="form-text text-muted">
                                <small>
                                    <?= lang('book_advance_timeout_hint') ?>
                                </small>
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

<script src="<?= asset_url('assets/vendor/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/jquery-jeditable/jquery.jeditable.min.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/working_plan.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/business_settings_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/business_settings.js') ?>"></script>

<?php section('scripts') ?>

