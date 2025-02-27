<!doctype html>
<html lang="<?= config('language_code') ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="theme-color" content="#35A768">
    <meta name="google" content="notranslate">

    <?php slot('meta'); ?>

    <title><?= vars('page_title') ?? lang('backend_section') ?> | Easy!Appointments</title>

    <link rel="icon" type="image/x-icon" href="<?= asset_url('assets/img/favicon.ico') ?>">
    <link rel="icon" sizes="192x192" href="<?= asset_url('assets/img/logo.png') ?>">

    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/vendor/trumbowyg/trumbowyg.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/vendor/select2/select2.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/vendor/flatpickr/flatpickr.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/vendor/flatpickr/material_green.min.css') ?>">
    <link rel="stylesheet" type="text/css"
          href="<?= asset_url('assets/css/themes/' . setting('theme', 'default') . '.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/general.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/layouts/backend_layout.css') ?>">

    <?php component('company_color_style', ['company_color' => setting('company_color')]); ?>

    <?php slot('styles'); ?>
</head>
<body class="d-flex flex-column h-100">

<main class="flex-shrink-0">

    <?php component('backend_header', ['active_menu' => vars('active_menu')]); ?>

    <?php slot('content'); ?>

</main>

<?php component('backend_footer', ['user_display_name' => vars('user_display_name')]); ?>

<script src="<?= asset_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/@popperjs-core/popper.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/bootstrap/bootstrap.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/moment/moment.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/moment-timezone/moment-timezone-with-data.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/@fortawesome-fontawesome-free/fontawesome.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/@fortawesome-fontawesome-free/solid.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/tippy.js/tippy-bundle.umd.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/trumbowyg/trumbowyg.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/select2/select2.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/flatpickr/flatpickr.min.js') ?>"></script>

<script src="<?= asset_url('assets/js/app.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/date.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/file.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/http.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/lang.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/message.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/string.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/validation.js') ?>"></script>
<script src="<?= asset_url('assets/js/layouts/backend_layout.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/localization_http_client.js') ?>"></script>

<?php component('js_vars_script'); ?>
<?php component('js_lang_script'); ?>

<?php slot('scripts'); ?>

</body>
</html>
