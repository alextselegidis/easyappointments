<?php
/**
 * @var array $system_settings
 * @var array $user_settings
 * @var string $timezones
 * @var array $privileges
 */
?>

<?php extend('layouts/backend/backend_layout') ?>

<?php section('content') ?>

<script src="<?= asset_url('assets/js/backend_settings_business_logic_helper.js') ?>"></script>
<script src="<?= asset_url('assets/js/backend_settings_business_logic.js') ?>"></script>
<script src="<?= asset_url('assets/js/working_plan.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/jquery-jeditable/jquery.jeditable.min.js') ?>"></script>
<script>
    var GlobalVariables = {
        csrfToken: <?= json_encode($this->security->get_csrf_hash()) ?>,
        baseUrl: <?= json_encode(config('base_url')) ?>,
        dateFormat: <?= json_encode(setting('date_format')) ?>,
        timeFormat: <?= json_encode(setting('time_format')) ?>,
        firstWeekday: <?= json_encode(setting('first_weekday')) ?>,
        timezones: <?= json_encode($timezones) ?>,
        settings: {
            system: <?= json_encode($system_settings) ?>,
        },
        user: {
            id: <?= session('user_id') ?>,
            email: <?= json_encode(session('user_email')) ?>,
            timezone: <?= json_encode(session('timezone')) ?>,
            role_slug: <?= json_encode(session('role_slug')) ?>,
            privileges: <?= json_encode($privileges) ?>
        }
    };

    $(function () {
        BackendSettingsBusinessLogic.initialize(true);
    });
</script>

<div id="business-logic-page" class="container-fluid backend-page">
    <div id="business-logic">
        <form>
            <fieldset>
                <legend class="border-bottom mb-4">
                    <?= lang('business_logic') ?>
                    <?php if ($privileges[PRIV_SYSTEM_SETTINGS]['edit'] == TRUE): ?>
                        <button type="button" class="save-settings btn btn-primary btn-sm mb-2"
                                data-tippy-content="<?= lang('save') ?>">
                            <i class="fas fa-check-square me-2"></i>
                            <?= lang('save') ?>
                        </button>
                    <?php endif ?>
                </legend>

                <div class="row">
                    <div class="col-12 col-sm-7 working-plan-wrapper">
                        <h4><?= lang('working_plan') ?></h4>
                        <span class="form-text text-muted mb-4">
                                <?= lang('edit_working_plan_hint') ?>
                            </span>

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

                        <br>

                        <h4><?= lang('book_advance_timeout') ?></h4>
                        <div class="form-group">
                            <label for="book-advance-timeout"
                                   class="control-label"><?= lang('timeout_minutes') ?></label>
                            <input id="book-advance-timeout" data-field="book_advance_timeout" class="form-control"
                                   type="number" min="15">
                            <p class="form-text text-muted">
                                <?= lang('book_advance_timeout_hint') ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-12 col-sm-5 breaks-wrapper">
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

                        <table class="breaks table table-striped">
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
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>

<?php section('content') ?>
