<script src="<?php echo base_url('/assets/js/backend_services_helper.js'); ?>"></script>
<script src="<?php echo base_url('/assets/js/backend_categories_helper.js'); ?>"></script>
<script src="<?php echo base_url('/assets/js/backend_services.js'); ?>"></script>
<script>
    var GlobalVariables = {
        'csrfToken'     : <?php echo json_encode($this->security->get_csrf_hash()); ?>,
        'baseUrl'       : <?php echo json_encode($base_url); ?>,
        'dateFormat'    : <?php echo json_encode($date_format); ?>,
        'services'      : <?php echo json_encode($services); ?>,
        'categories'    : <?php echo json_encode($categories); ?>,
        'user'          : {
            'id'        : <?php echo $user_id; ?>,
            'email'     : <?php echo json_encode($user_email); ?>,
            'role_slug' : <?php echo json_encode($role_slug); ?>,
            'privileges': <?php echo json_encode($privileges); ?>
        }
    };

    $(document).ready(function() {
        BackendServices.initialize(true);
    });
</script>

<div id="services-page" class="container-fluid backend-page">
    <ul class="nav nav-tabs">
        <li role="presentation" class="services-tab tab active"><a><?php echo lang('services'); ?></a></li>
        <li role="presentation" class="categories-tab tab"><a><?php echo lang('categories'); ?></a></li>
    </ul>

    <!-- SERVICES TAB -->

    <div id="services" class="tab-content">
        <?php // FILTER SERVICES ?>
        <div class="row">
            <div id="filter-services" class="filter-records column col-xs-12 col-sm-5">
                <form class="input-append">
                    <input class="key" type="text" />
                    <div class="btn-group">
                        <button class="filter btn btn-default btn-sm" type="submit" title="<?php echo lang('filter'); ?>">
                            <span class="glyphglyphicon glyphicon glyphglyphicon glyphicon-search"></span>
                        </button>
                        <button class="clear btn btn-default btn-sm" type="button" title="<?php echo lang('clear'); ?>">
                            <span class="glyphglyphicon glyphicon glyphglyphicon glyphicon-repeat"></span>
                        </button>
                    </div>
                </form>

                <h3><?php echo lang('services'); ?></h3>
                <div class="results"></div>
            </div>

            <div class="record-details column col-xs-12 col-sm-7">
                <div class="btn-toolbar">
                    <div class="add-edit-delete-group btn-group">
                        <button id="add-service" class="btn btn-primary">
                            <span class="glyphicon glyphicon-plus"></span>
                            <?php echo lang('add'); ?>
                        </button>
                        <button id="edit-service" class="btn btn-default" disabled="disabled">
                            <span class="glyphicon glyphicon-pencil"></span>
                            <?php echo lang('edit'); ?>
                        </button>
                        <button id="delete-service" class="btn btn-default" disabled="disabled">
                            <span class="glyphicon glyphicon-remove"></span>
                            <?php echo lang('delete'); ?>
                        </button>
                    </div>

                    <div class="save-cancel-group btn-group" style="display:none;">
                        <button id="save-service" class="btn btn-primary">
                            <span class="glyphicon glyphicon-ok"></span>
                            <?php echo lang('save'); ?>
                        </button>
                        <button id="cancel-service" class="btn btn-default">
                            <span class="glyphicon glyphicon-ban-circle"></span>
                            <?php echo lang('cancel'); ?>
                        </button>
                    </div>
                </div>

                <h3><?php echo lang('details'); ?></h3>
                <div class="form-message alert" style="display:none;"></div>

                <input type="hidden" id="service-id" />

                <div class="form-group">
                    <label for="service-name"><?php echo lang('name'); ?> *</label>
                    <input type="text" id="service-name" class="form-control required" />
                </div>

                <div class="form-group">
                    <label for="service-duration"><?php echo lang('duration_minutes'); ?> *</label>
                    <input type="text" id="service-duration" class="form-control required"  />
                </div>

                <div class="form-group">
                    <label for="service-price"><?php echo lang('price'); ?> *</label>
                    <input type="text" id="service-price" class="form-control required" />
                </div>

                <div class="form-group">
                    <label for="service-currency"><?php echo lang('currency'); ?></label>
                    <input type="text" id="service-currency" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="service-category"><?php echo lang('category'); ?></label>
                    <select id="service-category" class="form-control"></select>
                </div>

                <div class="form-group">
                    <label for="service-availabilities-type"><?php echo lang('availabilities_type'); ?></label>
                    <select id="service-availabilities-type" class="form-control">
                        <option value="<?php echo AVAILABILITIES_TYPE_FLEXIBLE; ?>">
                            <?php echo lang('flexible'); ?>
                        </option>
                        <option value="<?php echo AVAILABILITIES_TYPE_FIXED; ?>">
                            <?php echo lang('fixed'); ?>
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="service-attendants-number"><?php echo lang('attendants_number'); ?> *</label>
                    <input type="text" id="service-attendants-number" class="form-control required"  />
                </div>

                <div class="form-group">
                    <label for="service-description"><?php echo lang('description'); ?></label>
                    <textarea id="service-description" rows="4" class="form-control"></textarea>
                </div>

                <p id="form-message" class="text-danger">
                    <em><?php echo lang('fields_are_required'); ?></em>
                </p>
            </div>
        </div>
    </div>

    <!-- CATEGORIES TAB -->

    <div id="categories" class="tab-content" style="display:none;">
        <div class="row">
            <div id="filter-categories" class="filter-records column col-xs-12 col-sm-5">
                <form class="input-append">
                    <input class="key" type="text" />
                    <div class="btn-group">
                        <button class="filter btn btn-default btn-sm" type="submit" title="<?php echo lang('filter'); ?>">
                            <span class="glyphicon glyphicon-search"></span>
                        </button>
                        <button class="clear btn btn-default btn-sm" type="button" title="<?php echo lang('clear'); ?>">
                            <span class="glyphicon glyphicon-repeat"></span>
                        </button>
                    </div>
                </form>

                <h3><?php echo lang('categories'); ?></h3>
                <div class="results"></div>
            </div>

            <div class="record-details col-xs-12 col-sm-7">
                <div class="btn-toolbar">
                    <div class="add-edit-delete-group btn-group">
                        <button id="add-category" class="btn btn-primary">
                            <span class="glyphicon glyphicon-plus glyphicon glyphicon-white"></span>
                            <?php echo lang('add'); ?>
                        </button>
                        <button id="edit-category" class="btn btn-default" disabled="disabled">
                            <span class="glyphicon glyphicon-pencil"></span>
                            <?php echo lang('edit'); ?>
                        </button>
                        <button id="delete-category" class="btn btn-default" disabled="disabled">
                            <span class="glyphicon glyphicon-remove"></span>
                            <?php echo lang('delete'); ?>
                        </button>
                    </div>

                    <div class="save-cancel-group btn-group" style="display:none;">
                        <button id="save-category" class="btn btn-primary">
                            <span class="glyphicon glyphicon-ok glyphicon glyphicon-white"></span>
                            <?php echo lang('save'); ?>
                        </button>
                        <button id="cancel-category" class="btn btn-default">
                            <span class="glyphicon glyphicon-ban-circle"></span>
                            <?php echo lang('cancel'); ?>
                        </button>
                    </div>
                </div>

                <h3><?php echo lang('details'); ?></h3>
                <div class="form-message alert" style="display:none;"></div>

                <input type="hidden" id="category-id" />

                <div class="form-group">
                    <label for="category-name"><?php echo lang('name'); ?> *</label>
                    <input type="text" id="category-name" class="form-control required" />
                </div>

                <div class="form-group">
                    <label for="category-description"><?php echo lang('description'); ?></label>
                    <textarea id="category-description" rows="4" class="form-control"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
