<?php
/**
 * @var array $privileges
 */
?>

<?php extend('layouts/backend_layout') ?>

<?php section('content') ?>

<script>
    var GlobalVariables = {
        csrfToken: <?= json_encode($this->security->get_csrf_hash()) ?>,
        baseUrl: <?= json_encode(config('base_url')) ?>,
        dateFormat: <?= json_encode(setting('date_format')) ?>,
        timeFormat: <?= json_encode(setting('time_format')) ?>,
        user: {
            id: <?= session('user_id') ?>,
            email: <?= json_encode(session('user_email')) ?>,
            timezone: <?= json_encode(session('timezone')) ?>,
            role_slug: <?= json_encode(session('role_slug')) ?>,
            privileges: <?= json_encode($privileges) ?>
        }
    };
</script>

<div id="about-page" class="container-fluid backend-page">
    <div id="about">
        <h3>Easy!Appointments</h3>

        <p>
            <?= lang('about_app_info') ?>
        </p>

        <div class="current-version card card-body bg-light border-light mb-3">
            <?= lang('current_version') ?>
            <?= config('version') ?>
        </div>

        <h3><?= lang('support') ?></h3>
        <p>
            <?= lang('about_app_support') ?>

            <br><br>

            <a href="https://easyappointments.org">
                <?= lang('official_website') ?>
            </a>
            |
            <a href="https://groups.google.com/forum/#!forum/easy-appointments">
                <?= lang('support_group') ?>
            </a>
            |
            <a href="https://github.com/alextselegidis/easyappointments/issues">
                <?= lang('project_issues') ?>
            </a>
            |
            <a href="https://facebook.com/easyappts">
                Facebook
            </a>
            |
            <a href="https://twitter.com/easyappts">
                Twitter
            </a>
        </p>

        <h3><?= lang('license') ?></h3>

        <p>
            <?= lang('about_app_license') ?>
            <a href="http://www.gnu.org/copyleft/gpl.html">http://www.gnu.org/copyleft/gpl.html</a>
        </p>
    </div>
</div>

<?php section('content') ?>
