<?php extend('layouts/account_layout'); ?>

<?php section('content'); ?>

<h2><?= lang('backend_section') ?></h2>

<p>
    <small>
        <?= lang('you_need_to_login') ?>
    </small>
</p>

<hr>
<div class="alert d-none"></div>

<form id="login-form">
    <div class="mb-3 mt-5">
        <label for="username" class="form-label">
            <?= lang('username') ?>
        </label>
        <input type="text" id="username" placeholder="<?= lang(
            'enter_username_here',
        ) ?>" class="form-control" required/>
    </div>

    <div class="mb-5">
        <label for="password" class="form-label">
            <?= lang('password') ?>
        </label>
        <input type="password" id="password" placeholder="<?= lang(
            'enter_password_here',
        ) ?>" class="form-control" required/>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-5">
        <a href="<?= site_url('recovery') ?>" class="forgot-password"><?= lang('forgot_your_password') ?></a>

        <button type="submit" id="login" class="btn btn-primary">
            <i class="fas fa-sign-in-alt me-2"></i>
            <?= lang('login') ?>
        </button>
    </div>
</form>
<?php end_section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/vendor/@fortawesome-fontawesome-free/fontawesome.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/@fortawesome-fontawesome-free/solid.min.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/login_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/login.js') ?>"></script>

<?php end_section('scripts'); ?>
