<?php
/**
 * @var string $timezones
 * @var array $privileges
 */
?>

<?php extend('layouts/backend/backend_layout') ?>

<?php section('content') ?>

<script src="<?= asset_url('assets/js/backend_categories_helper.js') ?>"></script>
<script src="<?= asset_url('assets/js/backend_categories.js') ?>"></script>
<script>
    var GlobalVariables = {
        csrfToken: <?= json_encode($this->security->get_csrf_hash()) ?>,
        baseUrl: <?= json_encode(config('base_url')) ?>,
        dateFormat: <?= json_encode(setting('date_format')) ?>,
        timeFormat: <?= json_encode(setting('time_format')) ?>,
        timezones: <?= json_encode($timezones) ?>,
        user: {
            id: <?= session('user_id') ?>,
            email: <?= json_encode(session('user_email')) ?>,
            timezone: <?= json_encode(session('timezone')) ?>,
            role_slug: <?= json_encode(session('role_slug')) ?>,
            privileges: <?= json_encode($privileges) ?>
        }
    };

    $(function () {
        BackendCategories.initialize(true);
    });
</script>

<div class="container-fluid backend-page" id="service-page">

    <div class="row" id="categories">
        <div id="filter-categories" class="filter-records column col-12 col-md-5">
            <form class="input-append mb-4">
                <div class="input-group">
                    <input type="text" class="key form-control">

                    <div class="input-group-addon">
                        <div>
                            <button class="filter btn btn-outline-secondary" type="submit"
                                    data-tippy-content="<?= lang('filter') ?>">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="clear btn btn-outline-secondary" type="button"
                                    data-tippy-content="<?= lang('clear') ?>">
                                <i class="fas fa-redo-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <h3><?= lang('categories') ?></h3>
            <div class="results"></div>
        </div>

        <div class="record-details col-12 col-md-5">
            <div class="btn-toolbar mb-4">
                <div class="add-edit-delete-group btn-group">
                    <button id="add-category" class="btn btn-primary">
                        <i class="fas fa-plus-square mr-2"></i>
                        <?= lang('add') ?>
                    </button>
                    <button id="edit-category" class="btn btn-outline-secondary" disabled="disabled">
                        <i class="fas fa-edit mr-2"></i>
                        <?= lang('edit') ?>
                    </button>
                    <button id="delete-category" class="btn btn-outline-secondary" disabled="disabled">
                        <i class="fas fa-trash-alt mr-2"></i>
                        <?= lang('delete') ?>
                    </button>
                </div>

                <div class="save-cancel-group btn-group" style="display:none;">
                    <button id="save-category" class="btn btn-primary">
                        <i class="fas fa-check-square mr-2"></i>
                        <?= lang('save') ?>
                    </button>
                    <button id="cancel-category" class="btn btn-outline-secondary">
                        <i class="fas fa-ban mr-2"></i>
                        <?= lang('cancel') ?>
                    </button>
                </div>
            </div>

            <h3><?= lang('details') ?></h3>

            <div class="form-message alert" style="display:none;"></div>

            <input type="hidden" id="category-id">

            <div class="form-group">
                <label for="category-name">
                    <?= lang('name') ?>
                    <span class="text-danger">*</span>
                </label>
                <input id="category-name" class="form-control required">
            </div>

            <div class="form-group">
                <label for="category-description">
                    <?= lang('description') ?>

                </label>
                <textarea id="category-description" rows="4" class="form-control"></textarea>
            </div>
        </div>
    </div>
    
</div>

<?php section('content') ?>
