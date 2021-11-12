<?php
/**
 * @var string $title
 * @var array $styles
 * @var string $template
 * @var array $scripts
 * @var array $global_variables
 */
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="theme-color" content="#35A768">
    <link rel="icon" href="<?= asset_url('assets/img/favicon.ico') ?>">
    <link rel="icon" href="<?= asset_url('assets/img/logo.png') ?>" sizes="192x192" type="image/png">
    <title><?= $page_title ?> | Easy!Appointments</title>
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/frontend.css') ?>">
    <?php foreach ($styles as $style): ?>
        <link rel="stylesheet" type="text/css" href="<?= $style ?>">
    <?php endforeach ?>
</head>
<body>
<div id="main" class="container">
    <div class="row wrapper">
        <div id="message-frame" class="col-12 border my-auto frame-container">
            
            <?php require $page_path ?>
            
            <div class="mt-2">
                <small>
                    Powered by
                    <a href="https://easyappointments.org">Easy!Appointments</a>
                </small>
            </div>
            
        </div>
    </div>
</div>

<script>
    var EALang = <?= json_encode($this->lang->language) ?>;
</script>

<script src="<?= asset_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/@popperjs-core/popper.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/bootstrap/bootstrap.min.js') ?>"></script>
<script src="<?= asset_url('assets/js/general_functions.js') ?>"></script>

<?php foreach ($scripts as $script): ?>
    <script src="<?= $script ?>"></script>
<?php endforeach ?>

<?php google_analytics_script() ?>
</body>
</html>
