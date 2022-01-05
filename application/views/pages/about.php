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

<div id="about-page" class="container backend-page">
    <div id="about" class="col-lg-8 offset-lg-2">
        <h3>Easy!Appointments</h3>

        <p>
            <?= lang('about_app_info') ?>
        </p>

        <div class="card mb-3">
            <div class="card-header">
                <?= lang('current_version') ?>
            </div>
            <div class="card-body">
                <strong>
                    <?= config('version') ?>
                </strong>
            </div>
        </div>
        
        <h3><?= lang('support') ?></h3>
        
        <p>
            <?= lang('about_app_support') ?>
        </p>

        <div class="d-lg-flex justify-content-between flex-wrap alight-items-center">
            <div class="text-center mb-3">
                <a class="btn btn-secondary d-block" href="https://easyappointments.org">
                    <i class="fas fa-external-link-alt me-2"></i>
                    <?= lang('official_website') ?>
                </a>    
            </div>

            <div class="text-center mb-3">
                <a class="btn btn-secondary d-block" href="https://groups.google.com/forum/#!forum/easy-appointments">
                    <i class="fas fa-external-link-alt me-2"></i>
                    <?= lang('support_group') ?>
                </a>
            </div>

            <div class="text-center mb-3">
                <a class="btn btn-secondary d-block" href="https://github.com/alextselegidis/easyappointments/issues">
                    <i class="fas fa-external-link-alt me-2"></i>
                    <?= lang('project_issues') ?>
                </a>
            </div>

            <div class="text-center mb-3">
                <a class="btn btn-secondary d-block" href="https://facebook.com/easyappts">
                    <i class="fas fa-external-link-alt me-2"></i>
                    Facebook
                </a>
            </div>

            <div class="text-center mb-3">
                <a class="btn btn-secondary d-block" href="https://twitter.com/easyappts">
                    <i class="fas fa-external-link-alt me-2"></i>
                    Twitter
                </a>
            </div>
        </div>

        <h3><?= lang('license') ?></h3>

        <p>
            <?= lang('about_app_license') ?>
            <a href="http://www.gnu.org/copyleft/gpl.html">http://www.gnu.org/copyleft/gpl.html</a>
        </p>
    </div>
</div>

<?php section('content') ?>
