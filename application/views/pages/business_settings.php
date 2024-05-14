<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>

<div id="business-logic-page" class="container backend-page">
    <div id="business-logic">
        <div class="row">
            <div class="col-sm-3 offset-sm-1">
                <?php component('settings_nav'); ?>
            </div>
            <div class="col-sm-6">
                <form>
                    <fieldset>
                        <div class="d-flex justify-content-between align-items-center border-bottom mb-4 py-2">
                            <h4 class="text-black-50 mb-0 fw-light">
                                <?= lang('business_logic') ?>
                            </h4>

                            <?php if (can('edit', PRIV_SYSTEM_SETTINGS)): ?>
                                <button type="button" id="save-settings" class="btn btn-primary">
                                    <i class="fas fa-check-square me-2"></i>
                                    <?= lang('save') ?>
                                </button>
                            <?php endif; ?>
                        </div>

                        <h5 class="text-black-50 mb-3 fw-light"><?= lang('working_plan') ?></h5>

                        <p class="form-text text-muted mb-4">
                            <?= lang('edit_working_plan_hint') ?>
                        </p>

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

                        <div class="text-end mb-5">
                            <button class="btn btn-outline-secondary" id="apply-global-working-plan" type="button">
                                <i class="fas fa-check"></i>
                                <?= lang('apply_to_all_providers') ?>
                            </button>
                        </div>

                        <h5 class="text-black-50 mb-3 fw-light"><?= lang('breaks') ?></h5>

                        <p class="form-text text-muted">
                            <?= lang('edit_breaks_hint') ?>
                        </p>

                        <div class="mt-2">
                            <button type="button" class="add-break btn btn-primary">
                                <i class="fas fa-plus-square me-2"></i>
                                <?= lang('add_break') ?>
                            </button>
                        </div>

                        <br>

                        <table class="breaks table table-striped mb-5">
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

                        <?php if (can('view', PRIV_BLOCKED_PERIODS)): ?>
                            <h5 class="text-black-50 mb-3 fw-light"><?= lang('blocked_periods') ?></h5>

                            <p class="form-text text-muted">
                                <?= lang('blocked_periods_hint') ?>
                            </p>

                            <div class="mb-5">
                                <a href="<?= site_url('blocked_periods') ?>" class="btn btn-primary">
                                    <i class="fas fa-cogs me-2"></i>
                                    <?= lang('configure') ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <h5 class="text-black-50 mb-3 fw-light"><?= lang(
                            'allow_rescheduling_cancellation_before',
                        ) ?></h5>

                        <div class="mb-5">
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

                        <h5 class="text-black-50 mb-3 fw-light"><?= lang('future_booking_limit') ?></h5>

                        <div class="mb-5">
                            <label for="future-booking-limit" class="form-label">
                                <?= lang('limit_days') ?>
                            </label>
                            <input id="future-booking-limit" data-field="future_booking_limit" class="form-control"
                                   type="number" min="15">
                            <div class="form-text text-muted">
                                <small>
                                    <?= lang('future_booking_limit_hint') ?>
                                </small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-start align-items-center mb-3">
                            <h5 class="text-black-50 mb-0 me-3 fw-light">
                                <?= lang('appointment_status_options') ?>
                            </h5>
                        </div>

                        <p class="form-text text-muted mb-4">
                            <?= lang('appointment_status_options_info') ?>
                        </p>

                        <?php component('appointment_status_options', [
                            'attributes' => 'id="appointment-status-options"',
                        ]); ?>

                        <?php slot('after_primary_fields'); ?>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>

<?php end_section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/vendor/jquery-jeditable/jquery.jeditable.min.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/date.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/string.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/ui.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/working_plan.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/business_settings_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/business_settings.js') ?>"></script>

<?php end_section('scripts'); ?>

