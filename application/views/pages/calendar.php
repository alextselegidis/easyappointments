<?php extend('layouts/backend_layout') ?>

<?php section('styles') ?>

<link rel="stylesheet" type="text/css" href="<?= asset_url('/assets/vendor/fullcalendar/main.min.css') ?>">

<?php section('styles') ?>

<?php section('content') ?>

<div class="container-fluid backend-page" id="calendar-page">
    <div class="row" id="calendar-toolbar">
        <div id="calendar-filter" class="col-md-4">
            <div class="calendar-filter-items">
                <select id="select-filter-item" class="form-control col"
                        data-tippy-content="<?= lang('select_filter_item_hint') ?>">
                </select>
            </div>
        </div>

        <div id="calendar-actions" class="col-md-8">
            <?php if ((session('role_slug') == DB_SLUG_ADMIN || session('role_slug') == DB_SLUG_PROVIDER)
                && config('google_sync_feature') == TRUE): ?>
                <button id="google-sync" class="btn btn-primary"
                        data-tippy-content="<?= lang('trigger_google_sync_hint') ?>">
                    <i class="fas fa-sync-alt me-2"></i>
                    <span><?= lang('synchronize') ?></span>
                </button>

                <button id="enable-sync" class="btn btn-light" data-bs-toggle="button"
                        data-tippy-content="<?= lang('enable_appointment_sync_hint') ?>">
                    <i class="fas fa-calendar-alt me-2"></i>
                    <span><?= lang('enable_sync') ?></span>
                </button>
            <?php endif ?>

            <?php if (can('add', PRIV_APPOINTMENTS)): ?>
                <div class="btn-group">
                    <button class="btn btn-light" id="insert-appointment">
                        <i class="fas fa-plus-square me-2"></i>
                        <?= lang('appointment') ?>
                    </button>

                    <button class="btn btn-light dropdown-toggle" id="insert-dropdown" data-bs-toggle="dropdown">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>

                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#" id="insert-unavailability">
                            <i class="fas fa-plus-square me-2"></i>
                            <?= lang('unavailability') ?>
                        </a>
                        <a class="dropdown-item" href="#" id="insert-working-plan-exception"
                            <?= session('role_slug') !== DB_SLUG_ADMIN ? 'hidden' : '' ?>>
                            <i class="fas fa-plus-square me-2"></i>
                            <?= lang('working_plan_exception') ?>
                        </a>
                    </div>
                </div>
            <?php endif ?>

            <button id="reload-appointments" class="btn btn-light"
                    data-tippy-content="<?= lang('reload_appointments_hint') ?>">
                <i class="fas fa-sync-alt"></i>
            </button>

            <?php if (vars('calendar_view') === 'default'): ?>
                <a class="btn btn-light" href="<?= site_url('calendar?view=table') ?>"
                   data-tippy-content="<?= lang('table') ?>">
                    <i class="fas fa-table"></i>
                </a>
            <?php endif ?>

            <?php if (vars('calendar_view') === 'table'): ?>
                <a class="btn btn-light" href="<?= site_url('calendar?view=default') ?>"
                   data-tippy-content="<?= lang('default') ?>">
                    <i class="fas fa-calendar-alt"></i>
                </a>
            <?php endif ?>
        </div>
    </div>

    <div id="calendar">
        <!-- Dynamically Generated Content -->
    </div>
</div>

<!-- Page Components -->

<?php component(
    'appointments_modal',
    [
        'available_services' => vars('available_services'),
        'appointment_status_options' => vars('appointment_status_options'),
        'timezones' => vars('timezones'),
        'require_first_name' => vars('require_first_name'),
        'require_last_name' => vars('require_last_name'),
        'require_email' => vars('require_email'),
        'require_phone_number' => vars('require_phone_number'),
        'require_address' => vars('require_address'),
        'require_city' => vars('require_city'),
        'require_zip_code' => vars('require_zip_code')
    ]
) ?>

<?php component(
    'unavailabilities_modal',
    [
        'timezones' => vars('timezones'),
        'timezone' => vars('timezone')
    ]
) ?>

<?php component('select_google_calendar_modal') ?>

<?php component('working_plan_exceptions_modal') ?>

<?php section('content') ?>

<?php section('scripts') ?>

<script src="<?= asset_url('assets/vendor/fullcalendar/main.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/fullcalendar-moment/main.global.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/jquery-jeditable/jquery.jeditable.min.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/date.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/message.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/validation.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/ui.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/calendar_default_view.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/calendar_table_view.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/calendar_google_sync.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/calendar_event_popover.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/calendar_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/google_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/customers_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/calendar.js') ?>"></script>

<?php section('scripts') ?>

