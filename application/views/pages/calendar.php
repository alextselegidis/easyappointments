<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>

<div class="container-fluid backend-page" id="calendar-page">
    <div class="row" id="calendar-toolbar">
        <div id="calendar-filter" class="col-md-3">
            <div class="calendar-filter-items">
                <select id="select-filter-item"
                        class="form-select col"
                        data-tippy-content="<?= lang('select_filter_item_hint') ?>"
                        aria-label="Filter">
                    <!-- JS -->
                </select>
            </div>
        </div>

        <div id="calendar-actions" class="col-md-9">
            <?php if (vars('calendar_view') === CALENDAR_VIEW_DEFAULT): ?>
                <button
                    id="enable-sync"
                    class="btn btn-light"
                    data-tippy-content="<?= lang('enable_appointment_sync_hint') ?>"
                    hidden>
                    <i class="fas fa-rotate me-2"></i>
                    <?= lang('enable_sync') ?>
                </button>

                <div class="btn-group" id="sync-button-group" hidden>
                    <button type="button" class="btn btn-light" id="trigger-sync" data-tippy-content="<?= lang(
                        'trigger_sync_hint',
                    ) ?>">
                        <i class="fas fa-rotate me-2"></i>
                        <?= lang('synchronize') ?>
                    </button>
                    <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="visually-hidden">
                            Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#" id="disable-sync">
                                <?= lang('disable_sync') ?>
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (can('add', PRIV_APPOINTMENTS)): ?>
                <div class="dropdown d-sm-inline-block">
                    <button class="btn btn-light" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-plus-square"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="#" id="insert-appointment">
                                <?= lang('appointment') ?>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" id="insert-unavailability">
                                <?= lang('unavailability') ?>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#"
                               id="insert-working-plan-exception" <?= session('role_slug') !== DB_SLUG_ADMIN
                                   ? 'hidden'
                                   : '' ?>>
                                <?= lang('working_plan_exception') ?>
                            </a>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>

            <button id="reload-appointments" class="btn btn-light"
                    data-tippy-content="<?= lang('reload_appointments_hint') ?>">
                <i class="fas fa-sync-alt"></i>
            </button>

            <?php if (vars('calendar_view') === CALENDAR_VIEW_DEFAULT): ?>
                <a class="btn btn-light mb-0" href="<?= site_url('calendar?view=table') ?>"
                   data-tippy-content="<?= lang('table') ?>">
                    <i class="fas fa-table"></i>
                </a>
            <?php endif; ?>

            <?php if (vars('calendar_view') === CALENDAR_VIEW_TABLE): ?>
                <a class="btn btn-light mb-0" href="<?= site_url('calendar?view=default') ?>"
                   data-tippy-content="<?= lang('default') ?>">
                    <i class="fas fa-calendar-alt"></i>
                </a>
            <?php endif; ?>

            <?php slot('after_calendar_actions'); ?>
        </div>
    </div>

    <div id="calendar">
        <!-- Dynamically Generated Content -->
    </div>
</div>

<!-- Page Components -->

<?php component('appointments_modal', [
    'available_services' => vars('available_services'),
    'appointment_status_options' => vars('appointment_status_options'),
    'timezones' => vars('timezones'),
    'require_first_name' => vars('require_first_name'),
    'require_last_name' => vars('require_last_name'),
    'require_email' => vars('require_email'),
    'require_phone_number' => vars('require_phone_number'),
    'require_address' => vars('require_address'),
    'require_city' => vars('require_city'),
    'require_zip_code' => vars('require_zip_code'),
    'require_notes' => vars('require_notes'),
]); ?>

<?php component('unavailabilities_modal', [
    'timezones' => vars('timezones'),
    'timezone' => vars('timezone'),
]); ?>

<?php component('working_plan_exceptions_modal'); ?>

<?php end_section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/vendor/fullcalendar/index.global.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/fullcalendar-moment/index.global.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/jquery-jeditable/jquery.jeditable.min.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/date.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/message.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/validation.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/ui.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/calendar_default_view.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/calendar_table_view.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/calendar_event_popover.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/calendar_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/customers_http_client.js') ?>"></script>
<?php if (vars('calendar_view') === CALENDAR_VIEW_DEFAULT): ?>
    <script src="<?= asset_url('assets/js/utils/calendar_sync.js') ?>"></script>
    <script src="<?= asset_url('assets/js/http/google_http_client.js') ?>"></script>
    <script src="<?= asset_url('assets/js/http/caldav_http_client.js') ?>"></script>
<?php endif; ?>
<script src="<?= asset_url('assets/js/pages/calendar.js') ?>"></script>

<?php end_section('scripts'); ?>

