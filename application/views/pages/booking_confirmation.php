<?php extend('layouts/message_layout'); ?>

<?php section('content'); ?>

<div class="mb-5 text-start">
    <h3 class="mb-4"><?= lang('appointment_registered') ?></h3>
    
    <p><?= lang('appointment_details_was_sent_to_you') ?>
    
    <p>To secure your consultation, please complete the $150 consultation fee below. This fee reserves your session with our design team and will be credited in full toward any future design services or product purchases.</p>

    <p>We look forward to collaborating with you.</p>
    <p>&mdash; Inform Design Team</p>
    
    <?php
    $informCraftCheckoutBase = 'https://staging.inform.ca/shop/services/design-consultation/';
    $informCraftCheckoutQuery = http_build_query(
        [
            'appointmentId' => vars('appointment_id'),
            'googleCalendarUrl' => vars('add_to_google_url'),
        ],
        '',
        '&',
        PHP_QUERY_RFC3986,
    );
    ?>
    <div class="mt-4">
        <a class="btn btn-primary d-block" href="<?= e($informCraftCheckoutBase . '?' . $informCraftCheckoutQuery) ?>" target="_blank">Complete payment
            <?php //lang('go_to_booking_page') ?>
            <?php //vars('services_id'); ?>
        </a>
    </div>
    <p class="mt-3 text-center">
        <a href="<?= vars('add_to_google_url') ?>" id="add-to-google-calendar" target="_blank">
            <?= lang('add_to_google_calendar') ?>
        </a>
    </p>
</div>

<?php end_section('content'); ?>

<?php section('scripts'); ?>

<?php component('google_analytics_script', ['google_analytics_code' => vars('google_analytics_code')]); ?>
<?php component('matomo_analytics_script', [
    'matomo_analytics_url' => vars('matomo_analytics_url'),
    'matomo_analytics_site_id' => vars('matomo_analytics_site_id'),
]); ?>

<?php end_section('scripts'); ?>
