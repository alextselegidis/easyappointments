<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">

    <title>Installation | Easy!Appointments</title>

    <link rel="icon" type="image/x-icon" href="<?= asset_url('assets/img/favicon.ico') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/vendor/jquery-ui-dist/jquery-ui.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/general.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/pages/installation.css') ?>">
</head>
<body>
<div id="loading" class="d-none">
    <img src="<?= base_url('assets/img/loading.gif') ?>" alt="loading">
</div>

<header>
    <div class="container">
        <h1 class="page-title">Easy!Appointments Installation</h1>
    </div>
</header>

<div class="content container">
    <div class="welcome">
        <h3>Welcome to the Easy!Appointments installation page.</h3>
        <p>
            This page will help you set the main settings of your Easy!Appointments installation. You will be able to 
            edit these settings and many more in the backend session of your system. Remember to use the 
            <strong class="text-primary"><?= site_url('calendar') ?></strong> URL to connect to the backend section 
            of Easy!Appointments.

            If you face any problems during the usage of Easy!Appointments you can always check the
            <a href="https://easyappointments.org/docs.html">Documentation</a> and 
            <a href="https://groups.google.com/group/easy-appointments">Support Group</a> for getting help. You may also 
            submit new issues on
            <a href="https://github.com/alextselegidis/easyappointments/issues">GitHub Issues</a>
            in order to help our development process.
        </p>
    </div>

    <div class="alert d-none"></div>

    <div class="row">
        <div class="admin-settings col-12 col-sm-5">
            <h3>Administrator</h3>

            <div class="mb-3">
                <label for="first-name" class="form-label">First Name</label>
                <input type="text" id="first-name" class="form-control"/>
            </div>

            <div class="mb-3">
                <label for="last-name" class="form-label">Last Name</label>
                <input type="text" id="last-name" class="form-control"/>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" id="email" class="form-control"/>
            </div>

            <div class="mb-3">
                <label for="phone-number" class="form-label">Phone Number</label>
                <input type="text" id="phone-number" class="form-control"/>
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" class="form-control"/>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" class="form-control"/>
            </div>

            <div class="mb-3">
                <label for="retype-password" class="form-label">Retype Password</label>
                <input type="password" id="retype-password" class="form-control"/>
            </div>
        </div>

        <div class="company-settings col-12 col-sm-5">
            <h3>Company</h3>

            <div class="mb-3">
                <label for="company-name" class="form-label">Company Name</label>
                <input type="text" id="company-name" class="form-control"/>
            </div>

            <div class="mb-3">
                <label for="company-email" class="form-label">Company Email</label>
                <input type="text" id="company-email" class="form-control"/>
            </div>

            <div class="mb-3">
                <label for="company-link" class="form-label">Company Link</label>
                <input type="text" id="company-link" class="form-control"/>
            </div>
        </div>
    </div>

    <br>

    <p>
        You will be able to set your business logic in the backend settings page after the installation is complete.
        <br>
        Press the following button to complete the installation process.
    </p>

    <br>

    <div class="mb-2">
        <h3>License</h3>
        Easy!Appointments is licensed under the <span class="badge bg-secondary">GPL-3.0 license</span>. By using the code 
        of Easy!Appointments in any way <br> you agree with the terms described in the following url:
        <a href="https://www.gnu.org/licenses/gpl-3.0.en.html">https://www.gnu.org/licenses/gpl-3.0.en.html</a>
    </div>

    <br>

    <button type="button" id="install" class="btn btn-success btn-large">
        <i class="icon-white icon-ok me-2"></i>
        Install Easy!Appointments
    </button>
</div>

<footer>
    Powered by <a href="https://easyappointments.org">Easy!Appointments</a>
</footer>

<script>
    const GlobalVariables = {
        csrfToken: <?= json_encode($this->security->get_csrf_hash()) ?>,
        baseUrl: <?= json_encode(config('base_url')) ?>
    };
</script>

<script src="<?= asset_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/jquery-ui-dist/jquery-ui.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/@popperjs-core/popper.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/bootstrap/bootstrap.min.js') ?>"></script>

<script src="<?= asset_url('assets/js/app.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/message.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/general_functions.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/installation.js') ?>"></script>

<?php component('config_script') ?>
<?php component('language_script') ?>

</body>
</html>
