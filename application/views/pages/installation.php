<!doctype html>
<html lang="<?= config('language_code') ?>">
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">

    <title><?= lang('installation_heading') ?> | Easy!Appointments</title>

    <link rel="icon" type="image/x-icon" href="<?= asset_url('assets/img/favicon.ico') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/themes/default.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/general.css') ?>">
</head>
<body class="d-flex flex-column min-vh-100">

<div id="loading" class="position-fixed top-0 start-0 w-100 vh-100 d-flex justify-content-center align-items-center d-none bg-white">
    <img src="<?= base_url('assets/img/loading.gif') ?>" alt="<?= lang('loading') ?>">
</div>

<header class="bg-success-subtle mb-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 offset-lg-1">
                <h1 class="text-dark-emphasis fw-light py-5">
                    <?= lang('installation_heading') ?>
                </h1>
            </div>
        </div>    
    </div>
</header>

<div class="container flex-grow-1">
    <div class="row">
        <div class="col-lg-9 offset-lg-1">

            <div>
                <h3><?= lang('installation_welcome') ?></h3>

                <p class="text-break">
                    <?= lang('installation_info') ?>
                </p>
            </div>

            <div class="alert" hidden></div>

            <div class="row">
                <div class="admin-settings col-lg-6">
                    <h3 class="mb-3 fw-light"><?= lang('administrator') ?></h3>

                    <div class="mb-3">
                        <label class="form-label" for="first-name">
                            <?= lang('first_name') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="first-name" class="form-control required" maxlength="256">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="last-name">
                            <?= lang('last_name') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="last-name" class="form-control required" maxlength="512">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="email">
                            <?= lang('email') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="email" class="form-control required" maxlength="512">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="username">
                            <?= lang('username') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="username" class="form-control required" maxlength="256">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">
                            <?= lang('password') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input type="password" id="password" class="form-control required" maxlength="512">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password-confirm">
                            <?= lang('retype_password') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input type="password" id="password-confirm" class="form-control required" maxlength="512">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="language">
                            <?= lang('language') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <select id="language" class="form-select required">
                            <?php
                            $config_lang = config('language');
                            foreach (vars('available_languages') as $lang): ?>
                                <option value="<?= $lang ?>"<?= $lang == $config_lang ? ' selected' : '' ?>>
                                    <?= ucfirst($lang) ?>
                                </option>
                            <?php endforeach;
                            ?>
                        </select>
                    </div>

                </div>

                <div class="company-settings col-lg-6">
                    <h3 class="mb-3 fw-light"><?= lang('company') ?></h3>

                    <div class="mb-3">
                        <label class="form-label" for="company-name">
                            <?= lang('company_name') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="company-name" data-field="company_name" class="required form-control">
                        <div class="form-text text-muted">
                            <small>
                                <?= lang('company_name_hint') ?>
                            </small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="company-email">
                            <?= lang('company_email') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="company-email" data-field="company_email" class="required form-control">
                        <div class="form-text text-muted">
                            <small>
                                <?= lang('company_email_hint') ?>
                            </small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="company-link">
                            <?= lang('company_link') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <input id="company-link" data-field="company_link" class="required form-control">
                        <div class="form-text text-muted">
                            <small>
                                <?= lang('company_link_hint') ?>
                            </small>
                        </div>
                    </div>

                </div>
            </div>

            <p class="mb-5">
                <?= lang('installation_business_logic_hint') ?>
                <br>
                <?= lang('installation_complete_hint') ?>
            </p>


            <div class="mb-3">
                <h3><?= lang('license') ?></h3>
                <?= lang('installation_license_text') ?>
                <a href="https://www.gnu.org/licenses/gpl-3.0.en.html">https://www.gnu.org/licenses/gpl-3.0.en.html</a>
            </div>

            <br>

            <button type="button" id="install" class="btn btn-primary mb-3">
                <i class="icon-white icon-ok me-2"></i>
                <?= lang('install') ?>
            </button>
            
            
        </div>
    </div>

    
</div>

<footer class="bg-light mt-auto">
    <div class="container">
        <div class="row">
            <div class="col-lg-9 offset-lg-1 py-3">
                <?= lang('powered_by') ?> <a href="https://easyappointments.org">Easy!Appointments</a>        
            </div>
        </div>
    </div>
    
</footer>

<?php component('js_vars_script'); ?>
<?php component('js_lang_script'); ?>

<script src="<?= asset_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/@popperjs-core/popper.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/bootstrap/bootstrap.min.js') ?>"></script>

<script src="<?= asset_url('assets/js/app.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/message.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/validation.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/installation.js') ?>"></script>

</body>
</html>
