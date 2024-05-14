<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>

<div class="container-fluid backend-page" id="webhooks-page">
    <div class="row" id="webhooks">
        <div id="filter-webhooks" class="filter-records col col-12 col-md-5">
            <form class="mb-4">
                <div class="input-group">
                    <input type="text" class="key form-control" aria-label="keyword">

                    <button class="filter btn btn-outline-secondary" type="submit"
                            data-tippy-content="<?= lang('filter') ?>">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <h4 class="text-black-50 mb-3 fw-light">
                <?= lang('webhooks') ?>
            </h4>

            <?php slot('after_page_title'); ?>

            <div class="results">
                <!-- JS -->
            </div>
        </div>

        <div class="record-details column col-12 col-md-5">
            <div class="btn-toolbar mb-4">
                <a href="<?= site_url('integrations') ?>" class="btn btn-outline-primary me-2">
                    <i class="fas fa-chevron-left me-2"></i>
                    <?= lang('back') ?>
                </a>
                
                <div class="add-edit-delete-group btn-group">
                    <button id="add-webhook" class="btn btn-primary">
                        <i class="fas fa-plus-square me-2"></i>
                        <?= lang('add') ?>
                    </button>
                    <button id="edit-webhook" class="btn btn-outline-secondary" disabled="disabled">
                        <i class="fas fa-edit me-2"></i>
                        <?= lang('edit') ?>
                    </button>
                    <button id="delete-webhook" class="btn btn-outline-secondary" disabled="disabled">
                        <i class="fas fa-trash-alt me-2"></i>
                        <?= lang('delete') ?>
                    </button>
                </div>

                <div class="save-cancel-group" style="display:none;">
                    <button id="save-webhook" class="btn btn-primary">
                        <i class="fas fa-check-square me-2"></i>
                        <?= lang('save') ?>
                    </button>
                    <button id="cancel-webhook" class="btn btn-secondary">
                        <?= lang('cancel') ?>
                    </button>
                </div>

                <?php slot('after_page_actions'); ?>
            </div>

            <h4 class="text-black-50 mb-3 fw-light">
                <?= lang('details') ?>
            </h4>

            <div class="form-message alert" style="display:none;"></div>

            <input type="hidden" id="id">

            <div class="mb-3">
                <label class="form-label" for="name">
                    <?= lang('name') ?>
                    <span class="text-danger" hidden>*</span>
                </label>
                <input id="name" class="form-control required" maxlength="128" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label" for="duration">
                    <?= lang('url') ?>
                    <span class="text-danger" hidden>*</span>
                </label>
                <input id="url" class="form-control required" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label" for="secret-token">
                    <?= lang('secret_token') ?>
                </label>

                <input id="secret-token" class="form-control" disabled>
            </div>

            <div>
                <label class="form-label mb-3" for="actions">
                    <?= lang('actions') ?>
                </label>
            </div>

            <div class="border rounded mb-3 p-3">
                <div id="actions">
                    <?php foreach (vars('available_actions') as $available_action): ?>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                       id="include-<?= str_replace('_', '-', $available_action) ?>"
                                       data-action="<?= $available_action ?>">

                                <label class="form-check-label"
                                       for="include-<?= str_replace('_', '-', $available_action) ?>">
                                    <?= lang($available_action) ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <label class="form-label mb-3">
                    <?= lang('options') ?>
                </label>
            </div>

            <div class="border rounded mb-3 p-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is-ssl-verified">

                    <label class="form-check-label" for="is-ssl-verified">
                        <?= lang('verify_ssl') ?>
                    </label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label" for="notes">
                    <?= lang('notes') ?>
                </label>
                <textarea id="notes" rows="4" class="form-control" disabled></textarea>
            </div>

            <?php slot('after_primary_fields'); ?>
        </div>
    </div>
</div>

<?php end_section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/js/utils/message.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/validation.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/webhooks_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/webhooks.js') ?>"></script>

<?php end_section('scripts'); ?>
