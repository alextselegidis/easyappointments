<?php extend('layouts/backend_layout') ?>

<?php section('content') ?>

<div id="integrations-page" class="container backend-page">
    <div class="row">
        <div class="col-sm-3 offset-sm-1">
            <?php component('settings_nav') ?>
        </div>
        <div id="integrations" class="col-sm-6">
            <h3 class="text-muted border-bottom py-2">
                <?= lang('integrations') ?>
            </h3>

            <p class="mb-5">
                <?= lang('integrations_info') ?>
            </p>

            <div class="row">
                <div class="col-sm-6">
                    <div class="card mb-5">
                        <div class="card-header">
                            <strong>
                                <?= lang('webhooks') ?>
                            </strong>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 integration-info">
                                <small>
                                    <?= lang('webhooks_info') ?>
                                </small>
                            </div>

                            <a href="<?= site_url('webhooks') ?>" class="btn btn-outline-primary">
                                <i class="fas fa-cogs me-2"></i>
                                <?= lang('configure') ?>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="card mb-5">
                        <div class="card-header">
                            <strong>
                                <?= lang('google_analytics') ?>
                            </strong>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 integration-info">
                                <small>
                                    <?= lang('google_analytics_info') ?>
                                </small>
                            </div>

                            <a href="<?= site_url('google_analytics_settings') ?>" class="btn btn-outline-primary">
                                <i class="fas fa-cogs me-2"></i>
                                <?= lang('configure') ?>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="card mb-5">
                        <div class="card-header">
                            <strong>
                                <?= lang('matomo_analytics') ?>
                            </strong>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 integration-info">
                                <small>
                                    <?= lang('matomo_analytics_info') ?>
                                </small>
                            </div>

                            <a href="<?= site_url('matomo_analytics_settings') ?>" class="btn btn-outline-primary">
                                <i class="fas fa-cogs me-2"></i>
                                <?= lang('configure') ?>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="card mb-5">
                        <div class="card-header">
                            <strong>
                                <?= lang('api') ?>
                            </strong>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 integration-info">
                                <small>
                                    <?= lang('api_info') ?>
                                </small>
                            </div>

                            <a href="<?= site_url('api_settings') ?>" class="btn btn-outline-primary">
                                <i class="fas fa-cogs me-2"></i>
                                <?= lang('configure') ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php section('content') ?>


<?php section('styles') ?>

<link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/pages/integrations.css') ?>">

<?php section('styles') ?>


