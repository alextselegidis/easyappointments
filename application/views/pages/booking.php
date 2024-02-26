<?php extend('layouts/booking_layout'); ?>

<?php section('content'); ?>

<!-- Booking Cancellation Frame -->

<?php component('booking_cancellation_frame', [
    'manage_mode' => vars('manage_mode'),
    'appointment_data' => vars('appointment_data'),
    'display_delete_personal_information' => vars('display_delete_personal_information'),
]); ?>

<!-- Select Service & Provider -->

<?php component('booking_type_step', ['available_services' => vars('available_services')]); ?>

<!-- Pick An Appointment Date -->

<?php component('booking_time_step', ['grouped_timezones' => vars('grouped_timezones')]); ?>

<!-- Enter Customer Information -->

<?php component('booking_info_step', [
    'display_first_name' => vars('display_first_name'),
    'require_first_name' => vars('require_first_name'),
    'display_last_name' => vars('display_last_name'),
    'require_last_name' => vars('require_last_name'),
    'display_email' => vars('display_email'),
    'require_email' => vars('require_email'),
    'display_phone_number' => vars('display_phone_number'),
    'require_phone_number' => vars('require_phone_number'),
    'display_address' => vars('display_address'),
    'require_address' => vars('require_address'),
    'display_city' => vars('display_city'),
    'require_city' => vars('require_city'),
    'display_zip_code' => vars('display_zip_code'),
    'require_zip_code' => vars('require_zip_code'),
    'display_notes' => vars('display_notes'),
    'require_notes' => vars('require_notes'),
]); ?>

<!-- Appointment Data Confirmation -->

<?php component('booking_final_step', [
    'manage_mode' => vars('manage_mode'),
    'display_terms_and_conditions' => vars('display_terms_and_conditions'),
    'display_privacy_policy' => vars('display_privacy_policy'),
]); ?>

<?php end_section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/js/utils/date.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/lang.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/message.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/string.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/validation.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/ui.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/booking_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/booking.js') ?>"></script>

<?php end_section('scripts'); ?>
