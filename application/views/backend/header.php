<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $company_name ?> | Easy!Appointments</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <link rel="icon" type="image/x-icon" href="<?= asset_url('assets/img/favicon.ico') ?>">

    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/jquery-ui/jquery-ui.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/jquery-qtip/jquery.qtip.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/trumbowyg/ui/trumbowyg.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/backend.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/general.css') ?>">

    <script src="<?= asset_url('assets/ext/jquery/jquery.min.js') ?>"></script>
    <script src="<?= asset_url('assets/ext/bootstrap/js/bootstrap.min.js') ?>"></script>
    <script src="<?= asset_url('assets/ext/jquery-ui/jquery-ui.min.js') ?>"></script>
    <script src="<?= asset_url('assets/ext/jquery-qtip/jquery.qtip.min.js') ?>"></script>
    <script src="<?= asset_url('assets/ext/datejs/date.min.js') ?>"></script>
    <script src="<?= asset_url('assets/ext/jquery-mousewheel/jquery.mousewheel.min.js') ?>"></script>
    <script src="<?= asset_url('assets/ext/trumbowyg/trumbowyg.min.js') ?>"></script>

    <script>
    	// Global JavaScript Variables - Used in all backend pages.
    	var availableLanguages = <?= json_encode(config('available_languages')) ?>;
    	var EALang = <?= json_encode($this->lang->language) ?>;
    </script>
</head>

<body>
<nav id="header" class="navbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <div id="header-logo" class="navbar-brand">
                <img src="<?= base_url('assets/img/logo.png') ?>">
                <span><?= $company_name ?></span>
            </div>

            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#header-menu"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>

        <div id="header-menu" class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <?php $hidden = ($privileges[PRIV_APPOINTMENTS]['view'] == TRUE) ? '' : 'hidden' ?>
                <?php $active = ($active_menu == PRIV_APPOINTMENTS) ? 'active' : '' ?>
                <li class="<?= $active . $hidden ?>">
                    <a href="<?= site_url('backend') ?>" class="menu-item"
                            title="<?= lang('manage_appointment_record_hint') ?>">
                        <?= lang('calendar') ?>
                    </a>
                </li>

                <?php $hidden = ($privileges[PRIV_CUSTOMERS]['view'] == TRUE) ? '' : 'hidden' ?>
                <?php $active = ($active_menu == PRIV_CUSTOMERS) ? 'active' : '' ?>
                <li class="<?= $active . $hidden ?>">
                    <a href="<?= site_url('backend/customers') ?>" class="menu-item"
                            title="<?= lang('manage_customers_hint') ?>">
                        <?= lang('customers') ?>
                    </a>
                </li>

                <?php $hidden = ($privileges[PRIV_SERVICES]['view'] == TRUE) ? '' : 'hidden' ?>
                <?php $active = ($active_menu == PRIV_SERVICES) ? 'active' : '' ?>
                <li class="<?= $active . $hidden ?>">
                    <a href="<?= site_url('backend/services') ?>" class="menu-item"
                            title="<?= lang('manage_services_hint') ?>">
                        <?= lang('services') ?>
                    </a>
                </li>

                <?php $hidden = ($privileges[PRIV_USERS]['view'] ==  TRUE) ? '' : 'hidden' ?>
                <?php $active = ($active_menu == PRIV_USERS) ? 'active' : '' ?>
                <li class="<?= $active . $hidden ?>">
                    <a href="<?= site_url('backend/users') ?>" class="menu-item"
                            title="<?= lang('manage_users_hint') ?>">
                        <?= lang('users') ?>
                    </a>
                </li>

                <?php $hidden = ($privileges[PRIV_SYSTEM_SETTINGS]['view'] == TRUE
                        || $privileges[PRIV_USER_SETTINGS]['view'] == TRUE) ? '' : 'hidden' ?>
                <?php $active = ($active_menu == PRIV_SYSTEM_SETTINGS) ? 'active' : '' ?>
                <li class="<?= $active . $hidden ?>">
                    <a href="<?= site_url('backend/settings') ?>" class="menu-item"
                            title="<?= lang('settings_hint') ?>">
                        <?= lang('settings') ?>
                    </a>
                </li>

                <li>
                    <a href="<?= site_url('user/logout') ?>" class="menu-item"
                            title="<?= lang('log_out_hint') ?>">
                        <?= lang('log_out') ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div id="notification" style="display: none;"></div>

<div id="loading" style="display: none;">
    <div class="any-element animation is-loading">
        &nbsp;
    </div>
</div>
