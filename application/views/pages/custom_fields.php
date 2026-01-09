<?php extend('layouts/backend_layout'); ?>

<?php section('styles'); ?>
<style>
    .ui-state-highlight {
        height: 50px;
        background-color: #f8f9fa;
        border: 2px dashed #6c757d;
        margin-bottom: 0.5rem;
    }

    .drag-handle {
        user-select: none;
    }
</style>
<?php end_section('styles'); ?>

<?php section('content'); ?>

<div class="container-fluid backend-page" id="custom-fields-page">
    <div class="row" id="custom-fields">
        <div id="filter-custom-fields" class="filter-records col col-12 col-md-5">
            <form class="mb-4">
                <div class="input-group">
                    <input type="text" class="key form-control" placeholder="<?= lang('keyword') ?>" aria-label="keyword">

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
                <!-- JS -->
            </div>
        </div>

        <div class="record-details column col-12 col-md-7">
            <div class="btn-toolbar mb-4">
                <div class="add-edit-delete-group btn-group">
                    <button id="add-custom-field" class="btn btn-primary">
                        <i class="fas fa-plus-square me-2"></i>
                        <?= lang('add') ?>
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
                        <i class="fas fa-ban me-2"></i>
                        <?= lang('cancel') ?>
                    </button>
                </div>
            </div>

            <div class="form-message alert" style="display:none;"></div>

            <input type="hidden" id="id">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">
                        <?= lang('name') ?>
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="name" class="form-control required" maxlength="255">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="label" class="form-label">
                        <?= lang('label') ?>
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="label" class="form-control required" maxlength="255">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="type" class="form-label">
                        <?= lang('type') ?>
                        <span class="text-danger">*</span>
                    </label>
                    <select id="type" class="form-control required">
                        <option value="text"><?= lang('text') ?></option>
                        <option value="textarea"><?= lang('textarea') ?></option>
                        <option value="select"><?= lang('dropdown') ?></option>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="required">
                            <label class="form-check-label" for="required">
                                <?= lang('required') ?>
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="display_column">
                            <label class="form-check-label" for="display_column">
                                <?= lang('display_in_customer_list') ?>
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="active" checked>
                            <label class="form-check-label" for="active">
                                <?= lang('active') ?>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="column-position" class="form-label">
                        <?= lang('column_position') ?>
                    </label>
                    <select id="column-position" class="form-control">
                        <option value="left"><?= lang('left_column') ?></option>
                        <option value="right"><?= lang('right_column') ?></option>
                    </select>
                    <small class="form-text text-muted">
                        <?= lang('select_which_column_to_display_field') ?>
                    </small>
                </div>
            </div>

            <div id="options-section" style="display:none;">
                <hr>
                <h5 class="text-black-50 mb-3 fw-light"><?= lang('options') ?></h5>

                <div class="mb-3">
                    <button id="add-option" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        <?= lang('add_option') ?>
                    </button>
                </div>

                <div id="options-list" class="list-group mb-3">
                    <!-- Options will be dynamically added here -->
                </div>
            </div>
        </div>
    </div>
</div>

<?php end_section('content'); ?>

<?php section('scripts'); ?>

<script src="<?= asset_url('assets/js/utils/message.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/custom_fields_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/custom_fields.js') ?>"></script>

<?php end_section('scripts'); ?>
