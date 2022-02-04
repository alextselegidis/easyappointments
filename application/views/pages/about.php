<?php extend('layouts/backend_layout') ?>

<?php section('content') ?>

<div id="about-page" class="container backend-page">
    <div id="about" class="col-lg-8 offset-lg-2">
        
        <div class="text-center my-5">
            <img src="<?= base_url('assets/img/logo.png') ?>" alt="Easy!Appointments Logo" class="mb-5">
            
            <h3>
                Easy!Appointments
            </h3>
            <h6 class="text-primary">
                Online Appointment Scheduler
            </h6>
        </div>

        <p class="mb-5">
            <?= lang('about_app_info') ?>
        </p>

        <div class="card mb-5">
            <div class="card-header">
                <?= lang('current_version') ?>
            </div>
            <div class="card-body">
                <strong>
                    <?= config('version') ?>
                </strong>
            </div>
        </div>
        
        <h4><?= lang('support') ?></h4>
        
        <p>
            <?= lang('about_app_support') ?>
        </p>

        <div class="d-lg-flex justify-content-start flex-wrap alight-items-center mb-5">
            <div class="text-center mb-3">
                <a class="btn btn-secondary d-block p-0 me-3 mb-3" href="https://easyappointments.org">
                    <i class="fas fa-external-link-alt me-2"></i>
                    <?= lang('official_website') ?>
                </a>    
            </div>

            <div class="text-center mb-3">
                <a class="btn btn-secondary d-block p-0 me-3 mb-3" href="https://groups.google.com/forum/#!forum/easy-appointments">
                    <i class="fas fa-external-link-alt me-2"></i>
                    <?= lang('support_group') ?>
                </a>
            </div>

            <div class="text-center mb-3">
                <a class="btn btn-secondary d-block p-0 me-3 mb-3" href="https://github.com/alextselegidis/easyappointments/issues">
                    <i class="fas fa-external-link-alt me-2"></i>
                    <?= lang('project_issues') ?>
                </a>
            </div>

            <div class="text-center mb-3">
                <a class="btn btn-secondary d-block p-0 me-3 mb-3" href="https://facebook.com/easyappts">
                    <i class="fas fa-external-link-alt me-2"></i>
                    Facebook
                </a>
            </div>

            <div class="text-center mb-3">
                <a class="btn btn-secondary d-block p-0 me-3 mb-3" href="https://twitter.com/easyappts">
                    <i class="fas fa-external-link-alt me-2"></i>
                    Twitter
                </a>
            </div>
        </div>

        <h4><?= lang('license') ?></h4>

        <p class="mb-5">
            <?= lang('about_app_license') ?>
            <a href="https://www.gnu.org/licenses/gpl-3.0.en.html">https://www.gnu.org/copyleft/gpl.html</a>
        </p>
    </div>
</div>

<?php section('content') ?>
