<?php extend('layouts/account_layout'); ?>

<?php section('content'); ?>

<div class="text-center mb-4">
    <img src="<?= asset_url('assets/img/logo.png') ?>" 
         alt="Easy!Appointments" class="shadow mb-3" width="72" height="72">
    <h4 class="text-primary fw-semibold mb-1"><?= lang('backend_section') ?></h4>
    <p class="text-secondary small mb-0"><?= lang('you_need_to_login') ?></p>
</div>

<div class="alert d-none"></div>

<form id="login-form">
    <div class="mb-3">
        <label for="username" class="form-label fw-medium text-secondary">
            <?= lang('username') ?>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0">
                <i class="fas fa-user text-secondary"></i>
            </span>
            <input type="text" id="username" 
                   placeholder="<?= lang('enter_username_here') ?>" class="form-control border-start-0 ps-0" required/>
        </div>
    </div>

    <div class="mb-4">
        <label for="password" class="form-label fw-medium text-secondary">
            <?= lang('password') ?>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0">
                <i class="fas fa-lock text-secondary"></i>
            </span>
            <input type="password" id="password" 
                   placeholder="<?= lang('enter_password_here') ?>" class="form-control border-start-0 ps-0" required/>
        </div>
    </div>

    <div class="d-grid gap-2 mb-3">
        <button type="submit" id="login" class="btn btn-primary">
            <i class="fas fa-sign-in-alt me-2"></i>
            <?= lang('login') ?>
        </button>
    </div>

    <div class="text-center">
        <a href="<?= site_url('recovery') ?>" class="text-decoration-none text-secondary small">
            <i class="fas fa-key me-1"></i>
            <?= lang('forgot_your_password') ?>
        </a>
    </div>
</form>
<?php end_section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/js/http/login_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/login.js') ?>"></script>

<?php end_section('scripts'); ?>
