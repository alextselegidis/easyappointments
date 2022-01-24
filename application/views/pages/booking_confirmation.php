<?php extend('layouts/message_layout') ?>

<?php section('content') ?>

<div>
    <img id="success-icon" class="mt-0 mb-5" src="<?= base_url('assets/img/success.png') ?>" alt="success"/>
</div>

<div class="mb-5">
    <h4 class="mb-5"><?= lang('appointment_registered') ?></h4>

    <p>
        <?= lang('appointment_details_was_sent_to_you') ?>
    </p>

    <p class="mb-5 text-muted">
        <small>
            <?= lang('check_spam_folder') ?>
        </small>
    </p>

    <a href="<?= site_url() ?>" class="btn btn-primary btn-large">
        <i class="fas fa-calendar-alt me-2"></i>
        <?= lang('go_to_booking_page') ?>
    </a>

    <?php if (config('google_sync_feature')): ?>
        <button id="add-to-google-calendar" class="btn btn-outline-secondary">
            <i class="fas fa-plus me-2"></i>
            <?= lang('add_to_google_calendar') ?>
        </button>
    <?php endif ?>
</div>

<?php section('content') ?>

<?php section('scripts') ?>

<script src="<?= asset_url('assets/js/utils/date.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/message.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/validation.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/booking_confirmation.js') ?>"></script>

<?php section('scripts') ?>

