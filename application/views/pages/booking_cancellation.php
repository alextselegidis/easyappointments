<?php extend('layouts/message_layout'); ?>

<?php section('content'); ?>

<div class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="text-center py-4 px-3">
        <div class="d-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10 mx-auto mb-4" style="width: 100px; height: 100px;">
            <i class="fas fa-calendar-times fa-3x text-danger"></i>
        </div>

        <h3 class="text-secondary fw-semibold mb-4"><?= lang('appointment_cancelled_title') ?></h3>

        <p class="fs-5 text-muted mb-1">
            <?= lang('appointment_cancelled') ?>
        </p>

        <div class="mt-4">
            <a href="<?= site_url() ?>" class="btn btn-primary px-4 py-2">
                <i class="fas fa-calendar-alt me-2"></i>
                <?= lang('go_to_booking_page') ?>
            </a>
        </div>
    </div>
</div>

<?php end_section('content'); ?>


<?php section('scripts'); ?>

<?php component('google_analytics_script', ['google_analytics_code' => vars('google_analytics_code')]); ?>
<?php component('matomo_analytics_script', [
    'matomo_analytics_url' => vars('matomo_analytics_url'),
    'matomo_analytics_site_id' => vars('matomo_analytics_site_id'),
]); ?>
<?php end_section('scripts'); ?>

