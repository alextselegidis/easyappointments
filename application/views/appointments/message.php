<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#35A768">

    <title><?= $message_title ?> | Easy!Appointments</title>

    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/frontend.css') ?>">

    <link rel="icon" type="image/x-icon" href="<?= asset_url('assets/img/favicon.ico') ?>">
    <link rel="icon" sizes="192x192" href="<?= asset_url('assets/img/logo.png') ?>">

    <script>
        var EALang = <?= json_encode($this->lang->language) ?>;
    </script>

    <script src="<?= asset_url('assets/ext/jquery/jquery.min.js') ?>"></script>
    <script src="<?= asset_url('assets/ext/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= asset_url('assets/ext/datejs/date.min.js') ?>"></script>
    <script src="<?= asset_url('assets/js/polyfill.js') ?>"></script>
    <script src="<?= asset_url('assets/js/general_functions.js') ?>"></script>
</head>

<body>
<div id="main" class="container">
    <div class="row wrapper">
        <div id="message-frame" class="col-12 border my-auto frame-container">
            <div>
                <img id="message-icon" src="<?= $message_icon ?>" alt="warning">
            </div>

            <div>
                <h3><?= $message_title ?></h3>
                <p><?= $message_text ?></p>

                <?php if (isset($exceptions) && config('debug')): ?>
                    <div>
                        <h4><?= lang('unexpected_issues') ?></h4>
                        <?php foreach ($exceptions as $exception): ?>
                            <?= exceptionToHtml($exception) ?>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </div>

            <div class="mt-2">
                <small>
                    Powered by
                    <a href="https://easyappointments.org">Easy!Appointments</a>
                </small>
            </div>
        </div>
    </div>
</div>

<?php google_analytics_script() ?>
</body>
</html>
