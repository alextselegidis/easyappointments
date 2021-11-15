<?php extend('layouts/account/account_layout') ?>

<?php section('content') ?>

<h2><?= lang('forgot_your_password') ?></h2>
<p><?= lang('type_username_and_email_for_new_password') ?></p>
<hr>
<div class="alert d-none"></div>
<form>
    <div class="form-group">
        <label for="username"><?= lang('username') ?></label>
        <input type="text" id="username" placeholder="<?= lang('enter_username_here') ?>" class="form-control"/>
    </div>
    <div class="form-group">
        <label for="email"><?= lang('email') ?></label>
        <input type="text" id="email" placeholder="<?= lang('enter_email_here') ?>" class="form-control"/>
    </div>

    <br>

    <button type="submit" id="get-new-password" class="btn btn-primary btn-large">
        <i class="fas fa-unlock-alt mr-2"></i>
        <?= lang('regenerate_password') ?>
    </button>

    <a href="<?= site_url('user/login') ?>" class="user-login">
        <?= lang('go_to_login') ?></a>
</form>

<?php section('content') ?>
