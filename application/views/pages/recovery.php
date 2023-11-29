<?php extend('layouts/account_layout'); ?>

<?php section('content'); ?>

<h2><?= lang('forgot_your_password') ?></h2>

<p>
    <small>
        <?= lang('type_username_and_email_for_new_password') ?>
    </small>
</p>

<hr>

<div class="alert d-none"></div>

<form class="mb-5">
    <div class="mb-3 mt-5">
        <label for="username" class="form-label">
            <?= lang('username') ?>
        </label>
        <input type="text" id="username" placeholder="<?= lang('enter_username_here') ?>" class="form-control"/>
    </div>

    <div class="mb-5">
        <label for="email" class="form-label">
            <?= lang('email') ?>
        </label>
        <input type="text" id="email" placeholder="<?= lang('enter_email_here') ?>" class="form-control"/>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-5">
        <a href="<?= site_url('login') ?>" class="user-login">
            <?= lang('go_to_login') ?>
        </a>

        <button type="submit" id="get-new-password" class="btn btn-primary btn-large">
            <i class="fas fa-unlock-alt me-2"></i>
            <?= lang('regenerate_password') ?>
        </button>
    </div>
</form>

<?php end_section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/vendor/@fortawesome-fontawesome-free/fontawesome.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/@fortawesome-fontawesome-free/solid.min.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/recovery_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/recovery.js') ?>"></script>

<?php end_section('scripts'); ?>
