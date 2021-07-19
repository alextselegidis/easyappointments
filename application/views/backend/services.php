<script src="<?= asset_url('assets/js/backend_services_helper.js') ?>"></script>
<script src="<?= asset_url('assets/js/backend_categories_helper.js') ?>"></script>
<script src="<?= asset_url('assets/js/backend_services.js') ?>"></script>
<script>
    var GlobalVariables = {
        csrfToken: <?= json_encode($this->security->get_csrf_hash()) ?>,
        baseUrl: <?= json_encode($base_url) ?>,
        dateFormat: <?= json_encode($date_format) ?>,
        timeFormat: <?= json_encode($time_format) ?>,
        services: <?= json_encode($services) ?>,
        categories: <?= json_encode($categories) ?>,
        timezones: <?= json_encode($timezones) ?>,
        user: {
            id: <?= $user_id ?>,
            email: <?= json_encode($user_email) ?>,
            timezone: <?= json_encode($timezone) ?>,
            role_slug: <?= json_encode($role_slug) ?>,
            privileges: <?= json_encode($privileges) ?>
        }
    };

    $(function () {
        BackendServices.initialize(true);
    });
</script>

<div class="container-fluid backend-page" id="services-page">
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link active" href="#services" data-toggle="tab">
                <?= lang('services') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#categories" data-toggle="tab">
                <?= lang('categories') ?>
            </a>
        </li>
    </ul>

    <div class="tab-content">

        <!-- SERVICES TAB -->

        <div class="tab-pane active" id="services">
            <div class="row">
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
                                <i class="fas fa-plus-square mr-2"></i>
                                <?= lang('add') ?>
                            </button>
                            <button id="edit-service" class="btn btn-outline-secondary" disabled="disabled">
                                <i class="fas fa-edit mr-2"></i>
                                <?= lang('edit') ?>
                            </button>
                            <button id="delete-service" class="btn btn-outline-secondary" disabled="disabled">
                                <i class="fas fa-trash-alt mr-2"></i>
                                <?= lang('delete') ?>
                            </button>
                        </div>

                        <div class="save-cancel-group btn-group" style="display:none;">
                            <button id="save-service" class="btn btn-primary">
                                <i class="fas fa-check-square mr-2"></i>
                                <?= lang('save') ?>
                            </button>
                            <button id="cancel-service" class="btn btn-outline-secondary">
                                <i class="fas fa-ban mr-2"></i>
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

        <!-- CATEGORIES TAB -->

        <div class="tab-pane" id="categories">
            <div class="row">
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
    </div>
</div>
