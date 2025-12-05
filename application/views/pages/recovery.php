<?php extend('layouts/account_layout'); ?>

<?php section('content'); ?>

<div class="text-center mb-4">
    <img src="<?= asset_url(
        'assets/img/logo.png',
    ) ?>" alt="Easy!Appointments" class="shadow mb-3" width="72" height="72">
    <h4 class="text-primary fw-semibold mb-1"><?= lang('forgot_your_password') ?></h4>
    <p class="text-secondary small mb-0"><?= lang('type_username_and_email_for_new_password') ?></p>
</div>

<div class="alert d-none"></div>

<form>
    <div class="mb-3">
        <label for="username" class="form-label fw-medium text-secondary">
            <?= lang('username') ?>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0">
                <i class="fas fa-user text-secondary"></i>
            </span>
            <input type="text" id="username" 
                   placeholder="<?= lang('enter_username_here') ?>" class="form-control border-start-0 ps-0"/>
        </div>
    </div>

    <div class="mb-4">
        <label for="email" class="form-label fw-medium text-secondary">
            <?= lang('email') ?>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0">
                <i class="fas fa-envelope text-secondary"></i>
            </span>
            <input type="text" id="email" 
                   placeholder="<?= lang('enter_email_here') ?>" class="form-control border-start-0 ps-0"/>
        </div>
    </div>

    <div class="d-grid gap-2 mb-3">
        <button type="submit" id="get-new-password" class="btn btn-primary">
            <i class="fas fa-unlock-alt me-2"></i>
            <?= lang('regenerate_password') ?>
        </button>
    </div>

    <div class="text-center">
        <a href="<?= site_url('login') ?>" class="text-decoration-none text-secondary small">
            <i class="fas fa-arrow-left me-1"></i>
            <?= lang('go_to_login') ?>
        </a>
    </div>
</form>

<?php end_section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/js/http/recovery_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/recovery.js') ?>"></script>

<?php end_section('scripts'); ?>
