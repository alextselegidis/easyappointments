<!DOCTYPE html>
<html lang="en">
<head>
    <title>Easy!Appointments - Update</title>

    <meta http-equiv="content-type" content="text/html; charset=UTF-8">

    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="icon" type="image/x-icon" href="<?= asset_url('assets/img/favicon.ico') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url ('assets/ext/jquery-ui/jquery-ui.min.css')?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/general.css') ?>">

    <style>
        html {
            position: relative;
            min-height: 100%;
        }

        .header {
            background: #DAFFEB;
        }

        h3 {
            margin-bottom: 40px;
        }

        .content {
            margin-top: 30px;
        }

        footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            background-color: #f5f5f5;
            padding: 15px 40px;
        }
    </style>
</head>
<body>
    <div class="container-fluid header">
        <div>
            <a href="https://easyappointments.org" target="_blank">
                <img src="<?= base_url('assets/img/installation-banner.png') ?>"
                     alt="Easy!Appointments Installation Banner">
            </a>
        </div>
    </div>

    <div class="container content">
        <?php if ($success): ?>
            <h4>
                The database was updated successfully!
            </h4>

            <p>
                You can now use the latest Easy!Appointments version.
            </p>
        <?php else: ?>
            <h4>
                There was an error during the update process ...
            </h4>

            <p>
                Please restore your database backup.
            </p>

            <div class="well text-left">
                Error Message: <?= $exception ?>
            </div>
        <?php endif; ?>

        <div>
            <a href="<?= site_url('backend') ?>" class="btn btn-default btn-large">
                <span class="glyphicon glyphicon-wrench"></span>
                <?= lang('backend_section') ?>
            </a>
        </div>
    </div>

    <footer>
        Powered by <a href="https://easyappointments.org">Easy!Appointments</a>
    </footer>
</body>
</html>
