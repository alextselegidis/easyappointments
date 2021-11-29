<?php
/**
 * @var string $base_url
 * @var string $dest_url
 */
?>

<?php extend('layouts/account/account_layout') ?>

<?php section('content') ?>

<h2><?= lang('backend_section') ?></h2>

<p><?= lang('you_need_to_login') ?></p>

<hr>
<div class="alert d-none"></div>

<form id="login-form">
    <div class="mb-3">
        <label for="username"><?= lang('username') ?></label>
        <input type="text" id="username"
               placeholder="<?= lang('enter_username_here') ?>"
               class="form-control"/>
    </div>
    <div class="mb-3">
        <label for="password"><?= lang('password') ?></label>
        <input type="password" id="password"
               placeholder="<?= lang('enter_password_here') ?>"
               class="form-control"/>
    </div>

    <div class="mb-3">
        <button type="submit" id="login" class="btn btn-primary">
            <i class="fas fa-sign-in-alt me-2"></i>
            <?= lang('login') ?>
        </button>
    </div>

    <a href="<?= site_url('user/forgot_password') ?>" class="forgot-password"><?= lang('forgot_your_password') ?></a>
    |
    <span id="select-language" class="badge bg-success">
        <?= ucfirst(config('language')) ?>
    </span>

</form>
<?php section('content') ?>

<?php section('scripts') ?>

<script src="<?= asset_url('assets/vendor/@fortawesome-fontawesome-free/fontawesome.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/@fortawesome-fontawesome-free/solid.min.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/general_functions.js') ?>"></script>
<script src="<?= asset_url('assets/js/login.js') ?>"></script>

<?php section('scripts') ?>
