<?php extend('layouts/account_layout') ?>

<?php section('content') ?>

<h3><?= lang('log_out') ?></h3>

<p>
    <?= lang('logout_success') ?>
</p>

<br>

<a href="<?= site_url() ?>" class="btn btn-success btn-large">
    <i class="fas fa-calendar-alt me-2"></i>
    <?= lang('book_appointment_title') ?>
</a>

<a href="<?= site_url('backend') ?>" class="btn btn-outline-secondary btn-large">
    <i class="fas fa-wrench me-2"></i>
    <?= lang('backend_section') ?>
</a>

<?php section('content') ?>


