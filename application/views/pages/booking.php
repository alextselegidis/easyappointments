<?php extend('layouts/booking_layout') ?>

<?php section('content') ?>

<!-- Booking Cancellation Frame -->

<?php component('booking_cancellation_frame') ?>

<!-- Select Service & Provider -->

<?php component('booking_type_step') ?>

<!-- Pick An Appointment Date -->

<?php component('booking_time_step') ?>

<!-- Enter Customer Information -->

<?php component('booking_info_step') ?>

<!-- Appointment Data Confirmation -->

<?php component('booking_final_step') ?>

<?php section('content') ?>

<?php section('scripts') ?>

<script src="<?= asset_url('assets/js/utils/date.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/lang.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/message.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/string.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/validation.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/booking_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/booking.js') ?>"></script>

<?php section('scripts') ?>
