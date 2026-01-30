<?php extend('layouts/account_layout'); ?>

<?php section('content'); ?>

<div class="text-center mb-4">
    <img src="<?= asset_url('assets/img/logo.png') ?>" 
        alt="Easy!Appointments" class="shadow mb-3" width="72" height="72">
    <h4 class="text-primary fw-semibold mb-1"><?= lang('log_out') ?></h4>
    <p class=" small mb-0"><?= lang('logout_success') ?></p>
</div>

<div class="d-grid gap-2 mb-3">
    <a href="<?= site_url() ?>" class="btn btn-primary">
        <i class="fas fa-calendar-alt me-2"></i>
        <?= lang('book_appointment_title') ?>
    </a>
</div>

<div class="text-center">
    <a href="<?= site_url('login') ?>" class="text-decoration-none  small">
        <i class="fas fa-arrow-left me-1"></i>
        <?= lang('backend_section') ?>
    </a>
</div>

<?php end_section('content'); ?>


