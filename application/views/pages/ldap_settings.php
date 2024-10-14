<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>

<div id="ldap-settings-page" class="container backend-page">
    <div class="row">
        <div class="col-sm-3 offset-sm-1">
            <?php component('settings_nav'); ?>
        </div>
        <div id="ldap-settings" class="col-sm-6">
            <form>
                <fieldset>
                    <div class="d-flex justify-content-between align-items-center border-bottom mb-4 py-2">
                        <h4 class="text-black-50 mb-0 fw-light">
                            <?= lang('ldap') ?>
                        </h4>

                        <div>
                            <a href="<?= site_url('integrations') ?>" class="btn btn-outline-primary me-2">
                                <i class="fas fa-chevron-left me-2"></i>
                                <?= lang('back') ?>
                            </a>

                            <?php if (can('edit', PRIV_SYSTEM_SETTINGS)): ?>
                                <button type="button" id="save-settings" class="btn btn-primary">
                                    <i class="fas fa-check-square me-2"></i>
                                    <?= lang('save') ?>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!extension_loaded('ldap')): ?>
                        <div class="alert alert-warning">
                            <?= lang('ldap_extension_not_loaded') ?>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="ldap-is-active" data-field="ldap_is_active">
                                    <label class="form-check-label" for="ldap-is-active">
                                        <?= lang('active') ?>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label" for="ldap-host">
                                    <?= lang('host') ?>
                                </label>
                                <input id="ldap-host" class="form-control" data-field="ldap_host">
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="ldap-port">
                                    <?= lang('port') ?>
                                </label>
                                <input id="ldap-port" class="form-control" data-field="ldap_port">
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="ldap-user_dn">
                                    <?= lang('user_dn') ?>
                                </label>
                                <input id="ldap-user_dn" class="form-control" data-field="ldap_user_dn">
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="ldap-password">
                                    <?= lang('password') ?>
                                </label>
                                <input id="ldap-password" type="password" class="form-control" data-field="ldap_password">
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="ldap-base-dn">
                                    <?= lang('base_dn') ?>
                                </label>
                                <input id="ldap-base-dn" class="form-control" data-field="ldap_base_dn">
                            </div>

                            <div class="mb-3">
                                <div class="d-flex mb-2">
                                    <label class="form-label mb-0" for="ldap-filter">
                                        <?= lang('filter') ?>
                                    </label>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 ms-auto" id="ldap-reset-filter">
                                        <i class="fas fa-undo me-2"></i>
                                        <?= lang('reset') ?>
                                    </button>
                                </div>
                                <input id="ldap-filter" class="form-control" data-field="ldap_filter">
                            </div>

                            <div class="mb-3">
                                <div class="d-flex mb-2">
                                    <label class="form-label mb-0" for="ldap-field-mapping">
                                        <?= lang('field_mapping') ?>
                                    </label>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 ms-auto" id="ldap-reset-field-mapping">
                                        <i class="fas fa-undo me-2"></i>
                                        <?= lang('reset') ?>
                                    </button>
                                </div>
                                
                                <textarea id="ldap-field-mapping" class="form-control" rows="5" data-field="ldap_field_mapping"></textarea>
                            </div>
                        </div>
                    </div>

                    <?php slot('after_primary_appointment_fields'); ?>
                </fieldset>
            </form>

            <div class="d-flex justify-content-between align-items-center border-bottom mb-4 py-2">
                <h4 class="text-black-50 mb-0 fw-light">
                    <?= lang('search') ?>
                </h4>
            </div>
            
            <p class="text-muted small">
                <?= lang('ldap_search_hint') ?>
            </p>

            <form id="ldap-search-form" class="mb-3">
                <label class="form-label" for="ldap-search-keyword">
                    <?= lang('keyword') ?>
                </label>

                <div class="input-group">
                    <input id="ldap-search-keyword" class="form-control">
                    
                    <button type="submit" class="btn btn-outline-primary">
                        <?= lang('search') ?>
                    </button>
                </div>
            </form>

            <div id="ldap-search-results" class="mb-3">
                <!-- JS -->
            </div>
        </div>
    </div>
</div>

<?php component('ldap_import_modal', [
    'roles' => vars('roles'),
]); ?>

<?php end_section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/customers_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/providers_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/secretaries_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/admins_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/ldap_settings_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/ldap_settings.js') ?>"></script>

<?php end_section('scripts'); ?>
