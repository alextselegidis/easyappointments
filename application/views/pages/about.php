<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>

<div id="about-page" class="container backend-page">
    <div id="about" class="col-lg-8 offset-lg-2">

        <div class="text-center my-5">
            <?php if (setting('company_logo')): ?>
                <?php if (strpos(setting('company_logo'), 'data:image') === 0): ?>
                    <img src="<?= setting('company_logo') ?>" alt="<?= e(setting('company_name')) ?>" class="mb-5" style="max-width: 200px;">
                <?php else: ?>
                    <img src="<?= base_url('storage/uploads/' . setting('company_logo')) ?>" alt="<?= e(setting('company_name')) ?>" class="mb-5" style="max-width: 200px;">
                <?php endif; ?>
            <?php else: ?>
                <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo" class="mb-5">
            <?php endif; ?>

            <h3>
                <?= e(setting('company_name')) ?: 'Appointment System' ?>
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
                <h5 class="fw-light text-black-50 mb-0">
                    <?= lang('current_version') ?>
                </h5>
            </div>
            <div class="card-body">
                <strong>
                    <?= config('version') ?>
                </strong>
            </div>
        </div>

        <h4 class="fw-light text-black-50 mb-3">
            <?= lang('license') ?>
        </h4>

        <p>
            <?= lang('about_app_license') ?>
        </p>

        <div class="mb-5">
            <a class="btn btn-outline-secondary d-block w-50 m-auto" href="https://www.gnu.org/licenses/gpl-3.0.en.html"
               target="_blank">
                <i class="fas fa-external-link-alt me-2"></i>
                GPL-3.0
            </a>
        </div>
    </div>
</div>

<?php end_section('content'); ?>
