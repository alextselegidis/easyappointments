<?php extend('layouts/backend_layout') ?>

<?php section('content') ?>

<div id="general-settings-page" class="container backend-page">
    <div id="general-settings">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <form>
                    <fieldset>
                        <legend class="d-flex justify-content-between align-items-center border-bottom mb-4 py-2">
                            <?= lang('general_settings') ?>
                            
                            <?php if (can('edit', PRIV_SYSTEM_SETTINGS)): ?>
                                <button type="button" id="save-settings" class="btn btn-primary btn-sm">
                                    <i class="fas fa-check-square me-2"></i>
                                    <?= lang('save') ?>
                                </button>
                            <?php endif ?>
                        </legend>

                        <div class="row">
                            <div class="col-lg-6">
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
                                <div class="mb-3">
                                    <label class="form-label" for="date-format">
                                        <?= lang('date_format') ?>
                                    </label>
                                    <select class="form-control" id="date-format" data-field="date_format">
                                        <option value="DMY">DMY</option>
                                        <option value="MDY">MDY</option>
                                        <option value="YMD">YMD</option>
                                    </select>
                                    <div class="form-text text-muted">
                                        <small>
                                            <?= lang('date_format_hint') ?>
                                        </small>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="time-format">
                                        <?= lang('time_format') ?>
                                    </label>
                                    <select class="form-control" id="time-format" data-field="time_format">
                                        <option value="<?= TIME_FORMAT_REGULAR ?>">H:MM AM/PM</option>
                                        <option value="<?= TIME_FORMAT_MILITARY ?>">HH:MM</option>
                                    </select>
                                    <div class="form-text text-muted">
                                        <small>
                                            <?= lang('time_format_hint') ?>
                                        </small>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="first-weekday">
                                        <?= lang('first_weekday') ?>
                                    </label>
                                    <select class="form-control" id="first-weekday" data-field="first_weekday">
                                        <option value="sunday"><?= lang('sunday') ?></option>
                                        <option value="monday"><?= lang('monday') ?></option>
                                        <option value="tuesday"><?= lang('tuesday') ?></option>
                                        <option value="wednesday"><?= lang('wednesday') ?></option>
                                        <option value="thursday"><?= lang('thursday') ?></option>
                                        <option value="friday"><?= lang('friday') ?></option>
                                        <option value="saturday"><?= lang('saturday') ?></option>
                                    </select>
                                    <div class="form-text text-muted">
                                        <small>
                                            <?= lang('first_weekday_hint') ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label" for="google-analytics-code">
                                        Google Analytics ID</label>
                                    <input id="google-analytics-code" placeholder="UA-XXXXXXXX-XX or G-XXXXXXXXXX"
                                           data-field="google_analytics_code" class="form-control">
                                    <div class="form-text text-muted">
                                        <small>
                                            <?= lang('google_analytics_code_hint') ?>
                                        </small>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="api-token">API Token</label>
                                    <input id="api-token" data-field="api_token" class="form-control">
                                    <div class="form-text text-muted">
                                        <small>
                                            <?= lang('api_token_hint') ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
        
        
    </div>
</div>

<?php section('content') ?>

<?php section('scripts') ?>

<script src="<?= asset_url('assets/js/utils/validation.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/general_settings_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/general_settings.js') ?>"></script>

<?php section('scripts') ?>
