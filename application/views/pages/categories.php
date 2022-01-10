<?php
/**
 * @var string $timezones
 * @var array $privileges
 */
?>

<?php extend('layouts/backend_layout') ?>

<?php section('content') ?>

<div class="container-fluid backend-page" id="service-categories-page">

    <div class="row" id="categories">
        <div id="filter-categories" class="filter-records column col-12 col-md-5">
            <form class="input-append mb-4">
                <div class="input-group">
                    <input type="text" class="key form-control">

                    <button class="filter btn btn-outline-secondary" type="submit"
                            data-tippy-content="<?= lang('filter') ?>">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <h3><?= lang('categories') ?></h3>
            <div class="results"></div>
        </div>

        <div class="record-details col-12 col-md-5">
            <div class="btn-toolbar mb-4">
                <div class="add-edit-delete-group btn-group">
                    <button id="add-category" class="btn btn-primary">
                        <i class="fas fa-plus-square me-2"></i>
                        <?= lang('add') ?>
                    </button>
                    <button id="edit-category" class="btn btn-outline-secondary" disabled="disabled">
                        <i class="fas fa-edit me-2"></i>
                        <?= lang('edit') ?>
                    </button>
                    <button id="delete-category" class="btn btn-outline-secondary" disabled="disabled">
                        <i class="fas fa-trash-alt me-2"></i>
                        <?= lang('delete') ?>
                    </button>
                </div>

                <div class="save-cancel-group btn-group" style="display:none;">
                    <button id="save-category" class="btn btn-primary">
                        <i class="fas fa-check-square me-2"></i>
                        <?= lang('save') ?>
                    </button>
                    <button id="cancel-category" class="btn btn-outline-secondary">
                        <i class="fas fa-ban me-2"></i>
                        <?= lang('cancel') ?>
                    </button>
                </div>
            </div>

            <h3><?= lang('details') ?></h3>

            <div class="form-message alert" style="display:none;"></div>

            <input type="hidden" id="category-id">

            <div class="mb-3">
                <label class="form-label" for="category-name">
                    <?= lang('name') ?>
                    <span class="text-danger">*</span>
                </label>
                <input id="category-name" class="form-control required">
            </div>

            <div class="mb-3">
                <label class="form-label" for="category-description">
                    <?= lang('description') ?>

                </label>
                <textarea id="category-description" rows="4" class="form-control"></textarea>
            </div>
        </div>
    </div>
    
</div>

<?php section('content') ?>

<?php section('scripts') ?>

<script src="<?= asset_url('assets/js/utils/message.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/validation.js') ?>"></script>
<script src="<?= asset_url('assets/js/utils/url.js') ?>"></script>
<script src="<?= asset_url('assets/js/http/categories_http_client.js') ?>"></script>
<script src="<?= asset_url('assets/js/pages/categories.js') ?>"></script>

<?php section('scripts') ?>
