<?php
/**
 * @var string $company_name
 * @var array $appointment_data
 * @var array $provider_data
 * @var array $customer_data
 * @var array $service_data
 */
?>

<?php extend('layouts/message_layout') ?>

<?php section('content') ?>

<div>
    <img id="success-icon" class="mt-0 mb-2" src="<?= base_url('assets/img/success.png') ?>" alt="success"/>
</div>

<div>
    <h3><?= lang('appointment_registered') ?></h3>

    <p>
        <?= lang('appointment_details_was_sent_to_you') ?>
    </p>

    <p>
        <strong>
            <?= lang('check_spam_folder') ?>
        </strong>
    </p>

    <a href="<?= site_url() ?>" class="btn btn-success btn-large">
        <i class="fas fa-calendar-alt"></i>
        <?= lang('go_to_booking_page') ?>
    </a>

    <?php if (config('google_sync_feature')): ?>
        <button id="add-to-google-calendar" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            <?= lang('add_to_google_calendar') ?>
        </button>
    <?php endif ?>
</div>

<script>
    var GlobalVariables = {
        csrfToken: <?= json_encode($this->security->get_csrf_hash()) ?>,
        appointmentData: <?= json_encode($appointment_data) ?>,
        providerData: <?= json_encode($provider_data) ?>,
        customerData: <?= json_encode($customer_data) ?>,
        serviceData: <?= json_encode($service_data) ?>,
        companyName: <?= json_encode($company_name) ?>,
        googleApiKey: <?= json_encode(config('google_api_key')) ?>,
        googleClientId: <?= json_encode(config('google_client_id')) ?>,
        googleApiScope: 'https://www.googleapis.com/auth/calendar'
    };
</script>

<?php section('content') ?>

