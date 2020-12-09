<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#35A768">

    <title><?= lang('log_out') ?> | Easy!Appointments</title>

    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/logout.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/general.css') ?>">

    <link rel="icon" type="image/x-icon" href="<?= asset_url('assets/img/favicon.ico') ?>">
    <link rel="icon" sizes="192x192" href="<?= asset_url('assets/img/logo.png') ?>">

    <script>
        var EALang = <?= json_encode($this->lang->language) ?>;
    </script>

    <script src="<?= asset_url('assets/ext/jquery/jquery.min.js') ?>"></script>
    <script src="<?= asset_url('assets/ext/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= asset_url('assets/ext/fontawesome/js/fontawesome.min.js') ?>"></script>
    <script src="<?= asset_url('assets/ext/fontawesome/js/solid.min.js') ?>"></script>
</head>
<body>
<div id="logout-frame" class="frame-container">
    <h3><?= lang('log_out') ?></h3>
    <p>
        <?= lang('logout_success') ?>
    </p>

    <br>

    <a href="<?= site_url() ?>" class="btn btn-success btn-large">
        <i class="fas fa-calendar-alt mr-2"></i>
        <?= lang('book_appointment_title') ?>
    </a>

    <a href="<?= site_url('backend') ?>" class="btn btn-outline-secondary btn-large">
        <i class="fas fa-wrench mr-2"></i>
        <?= lang('backend_section') ?>
    </a>

    <div class="mt-4">
        <small>
            Powered by
            <a href="https://easyappointments.org">Easy!Appointments</a>
        </small>
    </div>
</div>
</body>
</html>
