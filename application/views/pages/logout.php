<?php extend('layouts/account_layout'); ?>

<?php section('content'); ?>

<h3><?= lang('log_out') ?></h3>

<p>
    <small>
        <?= lang('logout_success') ?>
    </small>
</p>

<div class="d-flex justify-content-between my-5">
    <a href="<?= site_url('login') ?>" class="btn btn-outline-secondary btn-large">
        <i class="fas fa-wrench me-2"></i>
        <?= lang('backend_section') ?>
    </a>

    <a href="<?= site_url() ?>" class="btn btn-primary btn-large">
        <i class="fas fa-calendar-alt me-2"></i>
        <?= lang('book_appointment_title') ?>
    </a>
</div>

<?php end_section('content'); ?>


