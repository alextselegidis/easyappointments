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

<?php section('content') ?>

<?php section('scripts') ?>

<script src="<?= asset_url('assets/js/utils/date.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/message.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/validation.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/booking_confirmation.js') ?>"></script>

<?php section('scripts') ?>

