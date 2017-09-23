<script src="<?= base_url('assets/js/backend_services_helper.js') ?>"></script>
<script src="<?= base_url('assets/js/backend_categories_helper.js') ?>"></script>
<script src="<?= base_url('assets/js/backend_services.js') ?>"></script>
<script>
    var GlobalVariables = {
        csrfToken     : <?= json_encode($this->security->get_csrf_hash()) ?>,
        baseUrl       : <?= json_encode($base_url) ?>,
        dateFormat    : <?= json_encode($date_format) ?>,
        services      : <?= json_encode($services) ?>,
        categories    : <?= json_encode($categories) ?>,
        user          : {
            id        : <?= $user_id ?>,
            email     : <?= json_encode($user_email) ?>,
            role_slug : <?= json_encode($role_slug) ?>,
            privileges: <?= json_encode($privileges) ?>
        }
    };

    $(document).ready(function() {
        BackendServices.initialize(true);
    });
</script>

<div id="services-page" class="container-fluid backend-page">
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#services" aria-controls="services" role="tab" data-toggle="tab"><?= lang('services') ?></a></li>
        <li role="presentation"><a href="#categories" aria-controls="categories" role="tab" data-toggle="tab"><?= lang('categories') ?></a></li>
    </ul>

    <div class="tab-content">

        <!-- SERVICES TAB -->

        <div role="tabpanel" class="tab-pane active" id="services">
            <div class="row">
                <div id="filter-services" class="filter-records column col-xs-12 col-sm-5">
                    <form>
                        <div class="form-group">
                            <input class="key">
                        </div>

                        <div class="btn-group">
                            <button class="filter btn btn-default btn-sm" type="submit" title="<?= lang('filter') ?>">
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                            <button class="clear btn btn-default btn-sm" type="button" title="<?= lang('clear') ?>">
                                <span class="glyphicon glyphicon-repeat"></span>
                            </button>
                        </div>
                    </form>

                    <h3><?= lang('services') ?></h3>
                    <div class="results"></div>
                </div>

                <div class="record-details column col-xs-12 col-sm-7">
                    <div class="btn-toolbar">
                        <div class="add-edit-delete-group btn-group">
                            <button id="add-service" class="btn btn-primary">
                                <span class="glyphicon glyphicon-plus"></span>
                                <?= lang('add') ?>
                            </button>
                            <button id="edit-service" class="btn btn-default" disabled="disabled">
                                <span class="glyphicon glyphicon-pencil"></span>
                                <?= lang('edit') ?>
                            </button>
                            <button id="delete-service" class="btn btn-default" disabled="disabled">
                                <span class="glyphicon glyphicon-remove"></span>
                                <?= lang('delete') ?>
                            </button>
                        </div>

                        <div class="save-cancel-group btn-group" style="display:none;">
                            <button id="save-service" class="btn btn-primary">
                                <span class="glyphicon glyphicon-ok"></span>
                                <?= lang('save') ?>
                            </button>
                            <button id="cancel-service" class="btn btn-default">
                                <span class="glyphicon glyphicon-ban-circle"></span>
                                <?= lang('cancel') ?>
                            </button>
                        </div>
                    </div>

                    <h3><?= lang('details') ?></h3>

                    <div class="form-message alert" style="display:none;"></div>

                    <input type="hidden" id="service-id">

                    <div class="form-group">
                        <label for="service-name"><?= lang('name') ?> *</label>
                        <input id="service-name" class="form-control required">
                    </div>

                    <div class="form-group">
                        <label for="service-duration"><?= lang('duration_minutes') ?> *</label>
                        <input id="service-duration" class="form-control required" >
                    </div>

                    <div class="form-group">
                        <label for="service-price"><?= lang('price') ?> *</label>
                        <input id="service-price" class="form-control required">
                    </div>

                    <div class="form-group">
                        <label for="service-currency"><?= lang('currency') ?></label>
                        <input id="service-currency" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="service-category"><?= lang('category') ?></label>
                        <select id="service-category" class="form-control"></select>
                    </div>

                    <div class="form-group">
                        <label for="service-availabilities-type"><?= lang('availabilities_type') ?></label>
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
                        <label for="service-attendants-number"><?= lang('attendants_number') ?> *</label>
                        <input id="service-attendants-number" class="form-control required" >
                    </div>

                    <div class="form-group">
                        <label for="service-description"><?= lang('description') ?></label>
                        <textarea id="service-description" rows="4" class="form-control"></textarea>
                    </div>

                    <p id="form-message" class="text-danger">
                        <em><?= lang('fields_are_required') ?></em>
                    </p>
                </div>
            </div>
        </div>

        <!-- CATEGORIES TAB -->

        <div role="tabpanel" class="tab-pane" id="categories">
            <div class="row">
                <div id="filter-categories" class="filter-records column col-xs-12 col-sm-5">
                    <form class="input-append">
                        <input class="key">
                        <div class="btn-group">
                            <button class="filter btn btn-default btn-sm" type="submit" title="<?= lang('filter') ?>">
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                            <button class="clear btn btn-default btn-sm" type="button" title="<?= lang('clear') ?>">
                                <span class="glyphicon glyphicon-repeat"></span>
                            </button>
                        </div>
                    </form>

                    <h3><?= lang('categories') ?></h3>
                    <div class="results"></div>
                </div>

                <div class="record-details col-xs-12 col-sm-7">
                    <div class="btn-toolbar">
                        <div class="add-edit-delete-group btn-group">
                            <button id="add-category" class="btn btn-primary">
                                <span class="glyphicon glyphicon-plus glyphicon glyphicon-white"></span>
                                <?= lang('add') ?>
                            </button>
                            <button id="edit-category" class="btn btn-default" disabled="disabled">
                                <span class="glyphicon glyphicon-pencil"></span>
                                <?= lang('edit') ?>
                            </button>
                            <button id="delete-category" class="btn btn-default" disabled="disabled">
                                <span class="glyphicon glyphicon-remove"></span>
                                <?= lang('delete') ?>
                            </button>
                        </div>

                        <div class="save-cancel-group btn-group" style="display:none;">
                            <button id="save-category" class="btn btn-primary">
                                <span class="glyphicon glyphicon-ok glyphicon glyphicon-white"></span>
                                <?= lang('save') ?>
                            </button>
                            <button id="cancel-category" class="btn btn-default">
                                <span class="glyphicon glyphicon-ban-circle"></span>
                                <?= lang('cancel') ?>
                            </button>
                        </div>
                    </div>

                    <h3><?= lang('details') ?></h3>

                    <div class="form-message alert" style="display:none;"></div>

                    <input type="hidden" id="category-id">

                    <div class="form-group">
                        <label for="category-name"><?= lang('name') ?> *</label>
                        <input id="category-name" class="form-control required">
                    </div>

                    <div class="form-group">
                        <label for="category-description"><?= lang('description') ?></label>
                        <textarea id="category-description" rows="4" class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
