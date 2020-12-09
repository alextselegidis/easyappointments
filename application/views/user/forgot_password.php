<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#35A768">

    <title><?= lang('forgot_your_password') ?> | Easy!Appointments</title>

    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/jquery-ui/jquery-ui.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/forgot_password.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/general.css') ?>">

    <link rel="icon" type="image/x-icon" href="<?= asset_url('assets/img/favicon.ico') ?>">
    <link rel="icon" sizes="192x192" href="<?= asset_url('assets/img/logo.png') ?>">

    <script>
        var GlobalVariables = {
            csrfToken: <?= json_encode($this->security->get_csrf_hash()) ?>,
            baseUrl: <?= json_encode(config('base_url')) ?>,
            AJAX_SUCCESS: 'SUCCESS',
            AJAX_FAILURE: 'FAILURE'
        };

        var EALang = <?= json_encode($this->lang->language) ?>;
    </script>

    <script src="<?= asset_url('assets/ext/jquery/jquery.min.js') ?>"></script>
    <script src="<?= asset_url('assets/ext/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= asset_url('assets/ext/jquery-ui/jquery-ui.min.js') ?>"></script>
    <script src="<?= asset_url('assets/ext/datejs/date.min.js') ?>"></script>
</head>
<body>
<div id="forgot-password-frame" class="frame-container">
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

    <div class="mt-4">
        <small>
            Powered by
            <a href="https://easyappointments.org">Easy!Appointments</a>
        </small>
    </div>
</div>
<script src="<?= asset_url('assets/ext/fontawesome/js/fontawesome.min.js') ?>"></script>
<script src="<?= asset_url('assets/ext/fontawesome/js/solid.min.js') ?>"></script>
<script src="<?= asset_url('assets/js/polyfill.js') ?>"></script>
<script src="<?= asset_url('assets/js/general_functions.js') ?>"></script>
<script src="<?= asset_url('assets/js/forgot_password.js') ?>"></script>
</body>
</html>
