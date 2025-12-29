<?php extend('layouts/backend_layout'); ?>

<?php section('content'); ?>

<div class="container-fluid backend-page" id="custom-fields-page">
    <div class="row" id="custom-fields">
        <div id="filter-custom-fields" class="filter-records col col-12 col-md-5">
            <form class="mb-4">
                <div class="input-group">
                    <input type="text" class="key form-control" placeholder="<?= lang('search') ?>"
                           aria-label="keyword">
                    <button class="filter btn btn-outline-secondary" type="submit"
                            data-tippy-content="<?= lang('filter') ?>">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <h4 class="text-black-50 mb-3 fw-light">
                <?= lang('custom_fields') ?>
            </h4>

            <div class="results">
                <!-- Populated by JavaScript -->
            </div>
        </div>

        <div class="record-details column col-12 col-md-7">
            <div class="btn-toolbar mb-4">
                <div class="add-edit-delete-group btn-group">
                    <button id="add-custom-field" class="btn btn-primary">
                        <i class="fas fa-plus-square me-2"></i>
                        <?= lang('add_field') ?>
                    </button>
                    <button id="edit-custom-field" class="btn btn-outline-secondary" disabled="disabled">
                        <i class="fas fa-edit me-2"></i>
                        <?= lang('edit') ?>
                    </button>
                    <button id="delete-custom-field" class="btn btn-outline-secondary" disabled="disabled">
                        <i class="fas fa-trash-alt me-2"></i>
                        <?= lang('delete') ?>
                    </button>
                </div>

                <div class="save-cancel-group" style="display:none;">
                    <button id="save-custom-field" class="btn btn-primary">
                        <i class="fas fa-check-square me-2"></i>
                        <?= lang('save') ?>
                    </button>
                    <button id="cancel-custom-field" class="btn btn-secondary">
                        <?= lang('cancel') ?>
                    </button>
                </div>
            </div>

            <h4 class="text-black-50 mb-3 fw-light">
                <?= lang('details') ?>
            </h4>

            <div class="form-message alert" style="display:none;"></div>

            <input type="hidden" id="id">

            <div class="mb-3">
                <label class="form-label" for="name">
                    <?= lang('name') ?>
                    <span class="text-danger">*</span>
                </label>
                <input id="name" class="form-control required" maxlength="255" disabled
                       placeholder="e.g., preferred_payment_method">
                <small class="form-text text-muted">
                    Internal field name (lowercase, use underscores)
                </small>
            </div>

            <div class="mb-3">
                <label class="form-label" for="label">
                    <?= lang('label') ?>
                    <span class="text-danger">*</span>
                </label>
                <input id="label" class="form-control required" maxlength="255" disabled
                       placeholder="e.g., Preferred Payment Method">
                <small class="form-text text-muted">
                    Label shown to users
                </small>
            </div>

            <div class="mb-3">
                <label class="form-label" for="type">
                    <?= lang('custom_field_type') ?>
                    <span class="text-danger">*</span>
                </label>
                <select id="type" class="form-select required" disabled>
                    <option value="text"><?= lang('text') ?></option>
                    <option value="textarea"><?= lang('textarea') ?></option>
                    <option value="select"><?= lang('select') ?></option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label" for="display-column">
                    <?= lang('display_in_column') ?>
                    <span class="text-danger">*</span>
                </label>
                <select id="display-column" class="form-select required" disabled>
                    <option value="1"><?= lang('column_1') ?></option>
                    <option value="2"><?= lang('column_2') ?></option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label" for="sort-order">
                    <?= lang('priority') ?>
                    <span class="text-danger">*</span>
                </label>
                <input id="sort-order" class="form-control required" type="number" min="0" value="0" disabled>
                <small class="form-text text-muted">
                    Lower numbers appear first
                </small>
            </div>

            <div class="border rounded mb-3 p-3">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="required" disabled>
                    <label class="form-check-label" for="required">
                        <?= lang('require') ?>
                    </label>
                </div>

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="active" disabled>
                    <label class="form-check-label" for="active">
                        <?= lang('active') ?>
                    </label>
                </div>
            </div>

            <!-- Options Section (only for select fields) -->
            <div id="options-section" style="display:none;">
                <hr class="my-4">

                <h4 class="text-black-50 mb-3 fw-light">
                    <?= lang('field_options') ?>
                </h4>

                <div class="btn-toolbar mb-3">
                    <button id="add-option" class="btn btn-sm btn-success">
                        <i class="fas fa-plus me-2"></i>
                        <?= lang('add_option') ?>
                    </button>
                </div>

                <div id="options-list" class="list-group mb-3">
                    <!-- Populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Option Modal -->
<div id="option-modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= lang('add_option') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="option-id">
                <input type="hidden" id="option-field-id">

                <div class="mb-3">
                    <label class="form-label" for="option-value">
                        <?= lang('option_value') ?>
                        <span class="text-danger">*</span>
                    </label>
                    <input id="option-value" class="form-control required" maxlength="255"
                           placeholder="e.g., cash">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="option-label">
                        <?= lang('option_label') ?>
                        <span class="text-danger">*</span>
                    </label>
                    <input id="option-label" class="form-control required" maxlength="255"
                           placeholder="e.g., Cash">
                </div>

                <div class="mb-3">
                    <label class="form-label" for="option-sort-order">
                        <?= lang('priority') ?>
                    </label>
                    <input id="option-sort-order" class="form-control" type="number" min="0" value="0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <?= lang('cancel') ?>
                </button>
                <button type="button" id="save-option" class="btn btn-primary">
                    <?= lang('save') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<?php end_section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/js/http/custom_fields_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/custom_fields.js') ?>"></script>

<?php end_section('scripts'); ?>
