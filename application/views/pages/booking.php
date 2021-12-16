<?php
/**
 * @var string $company_name
 * @var string $customer_token
 * @var string $date_format
 * @var string $time_format
 * @var string $first_weekday
 * @var bool $manage_mode
 * @var array $appointment_data
 * @var array $provider_data
 * @var array $customer_data
 * @var array $available_services
 * @var array $available_providers
 * @var array $show_field
 * @var bool $require_phone_number
 * @var string $display_any_provider
 * @var string $display_terms_and_conditions
 * @var string $display_privacy_policy
 * @var string $display_cookie_notice
 */
?>

<?php extend('layouts/booking_layout') ?>

<?php section('content') ?>

<!-- Booking Cancellation Frame -->

<?php component('booking_cancellation_section') ?>

<!-- Select Service & Provider -->

<?php component('booking_step_1_section') ?>

<!-- Pick An Appointment Date -->

<?php component('booking_step_2_section') ?>

<!-- Enter Customer Information -->

<?php component('booking_step_3_section') ?>

<!-- Appointment Data Confirmation -->

<?php component('booking_step_4_section') ?>

<?php section('content') ?>


<?php section('scripts') ?>

<script src="<?= asset_url('assets/js/pages/frontend_book_api.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/frontend_book.js') ?>"></script>

<?php section('scripts') ?>
