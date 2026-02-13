<?php extend('layouts/account_layout'); ?>

<?php section('content'); ?>

<div class="text-center mb-4">
    <img src="<?= asset_url('assets/img/logo.png') ?>" alt="Easy!Appointments" 
         class="shadow mb-3" width="72" height="72">
    
    <h4 class="text-primary fw-semibold mb-1"><?= lang('reset_password') ?></h4>
    
    <?php if (!empty(vars('token_valid')) && vars('token_valid')): ?>
        <p class="small mb-0">
            <?= lang('enter_new_password') ?>
        </p>
    <?php endif; ?>
</div>

<div class="alert d-none"></div>

<?php if (!empty(vars('token_valid')) && vars('token_valid')): ?>

    <!-- Password Reset Form -->

    <form id="password-reset-form">
        <input type="hidden" id="token" value="<?= vars('token') ?>">
        
        <div class="mb-3">
            <label for="password" class="form-label fw-medium">
                <?= lang('new_password') ?>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="fas fa-lock"></i>
                </span>
                <input type="password" id="password"
                       placeholder="<?= lang('enter_password_here') ?>" class="form-control border-start-0 ps-0"/>
            </div>
            <small class="text-muted">
                <?= str_replace('$number', MIN_PASSWORD_LENGTH, lang('password_length_notice')) ?>
            </small>
        </div>
        
        <div class="mb-4">
            <label for="password-confirm" class="form-label fw-medium">
                <?= lang('retype_password') ?>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <i class="fas fa-lock"></i>
                </span>
                <input type="password" id="password-confirm"
                       placeholder="<?= lang('enter_password_here') ?>" class="form-control border-start-0 ps-0"/>
            </div>
        </div>
        
        <div class="d-grid gap-2 mb-3">
            <button type="submit" id="reset-password" class="btn btn-primary">
                <i class="fas fa-key me-2"></i>
                <?= lang('reset_password') ?>
            </button>
        </div>
        
    </form>

<?php else: ?>
    <!-- Invalid or Expired Token Message -->
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <?= vars('error_message') ?? lang('invalid_or_expired_token') ?>
    </div>
<?php endif; ?>

<div class="text-center">
    <a href="<?= site_url('login') ?>" class="text-decoration-none small">
        <i class="fas fa-arrow-left me-1"></i>
        <?= lang('go_to_login') ?>
    </a>
</div>

<?php end_section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/js/http/password_reset_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/password_reset.js') ?>"></script>

<?php end_section('scripts'); ?>
