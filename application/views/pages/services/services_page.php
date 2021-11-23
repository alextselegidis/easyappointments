<?php
/**
 * @var string $timezones
 * @var array $privileges
 */
?>

<?php extend('layouts/backend/backend_layout') ?>

<?php section('content') ?>

<script src="<?= asset_url('assets/js/backend_services_helper.js') ?>"></script>
<script src="<?= asset_url('assets/js/backend_services.js') ?>"></script>
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
        BackendServices.initialize(true);
    });
</script>

<div class="container-fluid backend-page" id="services-page">
    <div class="row" id="services">
        <div id="filter-services" class="filter-records col col-12 col-md-5">
            <form class="mb-4">
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

            <h3><?= lang('services') ?></h3>
            <div class="results"></div>
        </div>

        <div class="record-details column col-12 col-md-5">
            <div class="btn-toolbar mb-4">
                <div class="add-edit-delete-group btn-group">
                    <button id="add-service" class="btn btn-primary">
                        <i class="fas fa-plus-square me-2"></i>
                        <?= lang('add') ?>
                    </button>
                    <button id="edit-service" class="btn btn-outline-secondary" disabled="disabled">
                        <i class="fas fa-edit me-2"></i>
                        <?= lang('edit') ?>
                    </button>
                    <button id="delete-service" class="btn btn-outline-secondary" disabled="disabled">
                        <i class="fas fa-trash-alt me-2"></i>
                        <?= lang('delete') ?>
                    </button>
                </div>

                <div class="save-cancel-group btn-group" style="display:none;">
                    <button id="save-service" class="btn btn-primary">
                        <i class="fas fa-check-square me-2"></i>
                        <?= lang('save') ?>
                    </button>
                    <button id="cancel-service" class="btn btn-outline-secondary">
                        <i class="fas fa-ban me-2"></i>
                        <?= lang('cancel') ?>
                    </button>
                </div>
            </div>

            <h3><?= lang('details') ?></h3>

            <div class="form-message alert" style="display:none;"></div>

            <input type="hidden" id="service-id">

            <div class="form-group">
                <label for="service-name">
                    <?= lang('name') ?>
                    <span class="text-danger">*</span>
                </label>
                <input id="service-name" class="form-control required" maxlength="128">
            </div>

            <div class="form-group">
                <label for="service-duration">
                    <?= lang('duration_minutes') ?>
                    <span class="text-danger">*</span>
                </label>
                <input id="service-duration" class="form-control required" type="number" min="<?= EVENT_MINIMUM_DURATION ?>">
            </div>

            <div class="form-group">
                <label for="service-price">
                    <?= lang('price') ?>
                    <span class="text-danger">*</span>
                </label>
                <input id="service-price" class="form-control required">
            </div>

            <div class="form-group">
                <label for="service-currency">
                    <?= lang('currency') ?>

                </label>
                <input id="service-currency" class="form-control" maxlength="32">
            </div>

            <div class="form-group">
                <label for="service-category">
                    <?= lang('category') ?>
                </label>
                <select id="service-category" class="form-control"></select>
            </div>

            <div class="form-group">
                <label for="service-availabilities-type">
                    <?= lang('availabilities_type') ?>

                </label>
                <select id="service-availabilities-type" class="form-control">
                    <option value="<?= AVAILABILITIES_TYPE_FLEXIBLE ?>">
                        <?= lang('flexible') ?>
                    </option>
                    <option value="<?= AVAILABILITIES_TYPE_FIXED ?>">
                        <?= lang('fixed') ?>
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label for="service-attendants-number">
                    <?= lang('attendants_number') ?>
                    <span class="text-danger">*</span>
                </label>
                <input id="service-attendants-number" class="form-control required" type="number" min="1">
            </div>

            <div class="form-group">
                <label for="service-location">
                    <?= lang('location') ?>

                </label>
                <input id="service-location" class="form-control">
            </div>

            <div class="form-group">
                <label for="service-description">
                    <?= lang('description') ?>
                </label>
                <textarea id="service-description" rows="4" class="form-control"></textarea>
            </div>
        </div>
    </div>
</div>

<?php section('content') ?>
