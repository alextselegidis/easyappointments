<?php
/**
 * @var array $available_providers
 * @var array $available_services
 * @var string $base_url
 * @var string $date_format
 * @var string $time_format
 * @var string $first_weekday
 * @var array $edit_appointment
 * @var array $customers
 * @var array $secretary_providers
 * @var string $calendar_view
 * @var string $timezones
 * @var string $user_id
 * @var string $user_email
 * @var string $timezone
 * @var string $role_slug
 * @var array $privileges
 * @var array $available_services
 * @var array $timezones
 * @var array $require_first_name
 * @var array $require_last_name
 * @var array $require_email
 * @var array $require_phone_number
 * @var array $require_address
 * @var array $require_city
 * @var array $require_zip_code
 */
?>

<?php extend('layouts/backend_layout') ?>

<?php section('styles') ?>

<link rel="stylesheet" type="text/css" href="<?= asset_url('/assets/vendor/fullcalendar/fullcalendar.min.css') ?>">

<?php section('styles') ?>

<?php section('scripts') ?>

<script src="<?= asset_url('assets/vendor/fullcalendar/fullcalendar.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/jquery-jeditable/jquery.jeditable.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.min.js') ?>"></script>
<script src="<?= asset_url('assets/js/components/working_plan_exceptions_modal.js') ?>"></script>
<script src="<?= asset_url('assets/js/components/manage_appointments_modal.js') ?>"></script>
<script src="<?= asset_url('assets/js/components/manage_unavailabilities_modal.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/date.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/message.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/validation.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/calendar_default_view.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/calendar_table_view.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/calendar_google_sync.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/calendar_event_popover.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/calendar_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/customers_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/calendar.js') ?>"></script>

<?php section('scripts') ?>

<?php section('content') ?>

<div class="container-fluid backend-page" id="calendar-page">
    <div class="row" id="calendar-toolbar">
        <div id="calendar-filter" class="col-12 col-sm-5">
            <div class="calendar-filter-items">
                <select id="select-filter-item" class="form-control col"
                        data-tippy-content="<?= lang('select_filter_item_hint') ?>">
                </select>
            </div>
        </div>

        <div id="calendar-actions" class="col-12 col-sm-7">
            <?php if ((session('role_slug') == DB_SLUG_ADMIN || session('role_slug') == DB_SLUG_PROVIDER)
                && config('google_sync_feature') == TRUE): ?>
                <button id="google-sync" class="btn btn-primary"
                        data-tippy-content="<?= lang('trigger_google_sync_hint') ?>">
                    <i class="fas fa-sync-alt"></i>
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
                        <a class="dropdown-item" href="#" id="insert-unavailable">
                            <i class="fas fa-plus-square me-2"></i>
                            <?= lang('unavailable') ?>
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

            <?php if ($calendar_view === 'default'): ?>
                <a class="btn btn-light" href="<?= site_url('backend?view=table') ?>"
                   data-tippy-content="<?= lang('table') ?>">
                    <i class="fas fa-table"></i>
                </a>
            <?php endif ?>

            <?php if ($calendar_view === 'table'): ?>
                <a class="btn btn-light" href="<?= site_url('backend?view=default') ?>"
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

<?php 
    component(
        'manage_appointment_modal', 
        '',
        [
            'available_services' => $available_services,
            'timezones' => $timezones,
            'require_first_name' => $require_first_name,
            'require_last_name' => $require_last_name,
            'require_email' => $require_email,
            'require_phone_number' => $require_phone_number,
            'require_address' => $require_address,
            'require_city' => $require_city,
            'require_zip_code' => $require_zip_code
        ]
    ) 
?>

<?php 
    component(
        'manage_unavailable_modal', 
        '',
        [
            'timezones' => $timezones,
            'timezone' => $timezone
        ]
    ) 
?>

<?php component('select_google_calendar_modal') ?>

<?php component('working_plan_exceptions_modal') ?>

<?php section('content') ?> 

