<script src="<?= asset_url('assets/js/backend_users_admins.js') ?>"></script>
<script src="<?= asset_url('assets/js/backend_users_providers.js') ?>"></script>
<script src="<?= asset_url('assets/js/backend_users_secretaries.js') ?>"></script>
<script src="<?= asset_url('assets/js/backend_users.js') ?>"></script>
<script src="<?= asset_url('assets/js/working_plan.js') ?>"></script>
<script src="<?= asset_url('assets/js/working_plan_exceptions_modal.js') ?>"></script>
<script src="<?= asset_url('assets/ext/jquery-ui/jquery-ui-timepicker-addon.min.js') ?>"></script>
<script src="<?= asset_url('assets/ext/jquery-jeditable/jquery.jeditable.min.js') ?>"></script>
<script>
    var GlobalVariables = {
        csrfToken: <?= json_encode($this->security->get_csrf_hash()) ?>,
        baseUrl: <?= json_encode($base_url) ?>,
        dateFormat: <?= json_encode($date_format) ?>,
        firstWeekday: <?= json_encode($first_weekday); ?>,
        timeFormat: <?= json_encode($time_format) ?>,
        admins: <?= json_encode($admins) ?>,
        providers: <?= json_encode($providers) ?>,
        secretaries: <?= json_encode($secretaries) ?>,
        services: <?= json_encode($services) ?>,
        timezones: <?= json_encode($timezones) ?>,
        workingPlan: <?= json_encode(json_decode($working_plan)) ?>,
        workingPlanExceptions: <?= json_encode(json_decode($working_plan_exceptions)) ?>,
        user: {
            id: <?= $user_id ?>,
            email: <?= json_encode($user_email) ?>,
            timezone: <?= json_encode($timezone) ?>,
            role_slug: <?= json_encode($role_slug) ?>,
            privileges: <?= json_encode($privileges) ?>
        }
    };

    $(function () {
        BackendUsers.initialize(true);
    });
</script>

<div class="container-fluid backend-page" id="users-page">

    <!-- PAGE NAVIGATION -->
    <ul class="nav nav-pills">
        <li class="nav-item">
            <a class="nav-link active" href="#providers" data-toggle="tab">
                <?= lang('providers') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#secretaries" data-toggle="tab">
                <?= lang('secretaries') ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#admins" data-toggle="tab">
                <?= lang('admins') ?>
            </a>
        </li>
    </ul>

    <div class="tab-content">

        <!-- PROVIDERS TAB -->

        <div class="tab-pane active" id="providers">
            <div class="row">
                <div id="filter-providers" class="filter-records column col-12 col-md-5">
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

                    <h3><?= lang('providers') ?></h3>
                    <div class="results"></div>
                </div>

                <div class="record-details column col-12 col-md-7">
                    <div class="float-md-left mb-4 mr-4">
                        <div class="add-edit-delete-group btn-group">
                            <button id="add-provider" class="btn btn-primary">
                                <i class="fas fa-plus-square mr-2"></i>
                                <?= lang('add') ?>
                            </button>
                            <button id="edit-provider" class="btn btn-outline-secondary" disabled="disabled">
                                <i class="fas fa-edit mr-2"></i>
                                <?= lang('edit') ?>
                            </button>
                            <button id="delete-provider" class="btn btn-outline-secondary" disabled="disabled">
                                <i class="fas fa-trash-alt mr-2"></i>
                                <?= lang('delete') ?>
                            </button>
                        </div>

                        <div class="save-cancel-group btn-group" style="display:none;">
                            <button id="save-provider" class="btn btn-primary">
                                <i class="fas fa-check-square mr-2"></i>
                                <?= lang('save') ?>
                            </button>
                            <button id="cancel-provider" class="btn btn-outline-secondary">
                                <i class="fas fa-ban mr-2"></i>
                                <?= lang('cancel') ?>
                            </button>
                        </div>
                    </div>

                    <ul class="nav nav-pills switch-view">
                        <li class="nav-item">
                            <a class="nav-link active" href="#details" data-toggle="tab">
                                <?= lang('details') ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#working-plan" data-toggle="tab">
                                <?= lang('working_plan') ?>
                            </a>
                        </li>
                    </ul>

                    <?php
                    // This form message is outside the details view, so that it can be
                    // visible when the user has working plan view active.
                    ?>

                    <div class="form-message alert" style="display:none;"></div>

                    <div class="tab-content">
                        <div class="details-view tab-pane fade show active clearfix" id="details">
                            <h3><?= lang('details') ?></h3>

                            <input type="hidden" id="provider-id" class="record-id">

                            <div class="row">
                                <div class="provider-details col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="provider-first-name">
                                            <?= lang('first_name') ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input id="provider-first-name" class="form-control required" maxlength="256">
                                    </div>

                                    <div class="form-group">
                                        <label for="provider-last-name">
                                            <?= lang('last_name') ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input id="provider-last-name" class="form-control required" maxlength="512">
                                    </div>

                                    <div class="form-group">
                                        <label for="provider-email">
                                            <?= lang('email') ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input id="provider-email" class="form-control required" max="512">
                                    </div>

                                    <div class="form-group">
                                        <label for="provider-phone-number">
                                            <?= lang('phone_number') ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input id="provider-phone-number" class="form-control required" max="128">
                                    </div>

                                    <div class="form-group">
                                        <label for="provider-mobile-number">
                                            <?= lang('mobile_number') ?>

                                        </label>
                                        <input id="provider-mobile-number" class="form-control" maxlength="128">
                                    </div>

                                    <div class="form-group">
                                        <label for="provider-address">
                                            <?= lang('address') ?>
                                        </label>
                                        <input id="provider-address" class="form-control" maxlength="256">
                                    </div>

                                    <div class="form-group">
                                        <label for="provider-city">
                                            <?= lang('city') ?>

                                        </label>
                                        <input id="provider-city" class="form-control" maxlength="256">
                                    </div>

                                    <div class="form-group">
                                        <label for="provider-state">
                                            <?= lang('state') ?>
                                        </label>
                                        <input id="provider-state" class="form-control" maxlength="256">
                                    </div>

                                    <div class="form-group">
                                        <label for="provider-zip-code">
                                            <?= lang('zip_code') ?>

                                        </label>
                                        <input id="provider-zip-code" class="form-control" maxlength="64">
                                    </div>

                                    <div class="form-group">
                                        <label for="provider-notes">
                                            <?= lang('notes') ?>
                                        </label>
                                        <textarea id="provider-notes" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="provider-settings col-12 col-md-6">
                                    <div class="form-group">
                                        <label for="provider-username">
                                            <?= lang('username') ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input id="provider-username" class="form-control required" maxlength="256">
                                    </div>

                                    <div class="form-group">
                                        <label for="provider-password">
                                            <?= lang('password') ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" id="provider-password" class="form-control required"
                                               maxlength="512" autocomplete="new-password">
                                    </div>

                                    <div class="form-group">
                                        <label for="provider-password-confirm">
                                            <?= lang('retype_password') ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="password" id="provider-password-confirm"
                                               class="form-control required" maxlength="512"
                                               autocomplete="new-password">
                                    </div>

                                    <div class="form-group">
                                        <label for="provider-calendar-view">
                                            <?= lang('calendar') ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select id="provider-calendar-view" class="form-control required">
                                            <option value="default">Default</option>
                                            <option value="table">Table</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="provider-timezone">
                                            <?= lang('timezone') ?>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <?= render_timezone_dropdown('id="provider-timezone" class="form-control required"') ?>
                                    </div>

                                    <br>

                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="provider-notifications">
                                        <label class="custom-control-label" for="provider-notifications">
                                            <?= lang('receive_notifications') ?>
                                        </label>
                                    </div>

                                    <br>

                                    <h4><?= lang('services') ?></h4>
                                    <div id="provider-services" class="card card-body bg-light border-light"></div>
                                </div>
                            </div>
                        </div>

                        <div class="working-plan-view tab-pane fade clearfix" id="working-plan">
                            <h3><?= lang('working_plan') ?></h3>
                            <button id="reset-working-plan" class="btn btn-primary"
                                    data-tippy-content="<?= lang('reset_working_plan') ?>">
                                <i class="fas fa-redo-alt mr-2"></i>
                                <?= lang('reset_plan') ?></button>
                            <table class="working-plan table table-striped mt-2">
                                <thead>
                                <tr>
                                    <th><?= lang('day') ?></th>
                                    <th><?= lang('start') ?></th>
                                    <th><?= lang('end') ?></th>
                                </tr>
                                </thead>
                                <tbody><!-- Dynamic Content --></tbody>
                            </table>

                            <br>

                            <h3><?= lang('breaks') ?></h3>

                            <p>
                                <?= lang('add_breaks_during_each_day') ?>
                            </p>

                            <div>
                                <button type="button" class="add-break btn btn-primary">
                                    <i class="fas fa-plus-square mr-2"></i>
                                    <?= lang('add_break') ?>
                                </button>
                            </div>

                            <br>

                            <table class="breaks table table-striped">
                                <thead>
                                <tr>
                                    <th><?= lang('day') ?></th>
                                    <th><?= lang('start') ?></th>
                                    <th><?= lang('end') ?></th>
                                    <th><?= lang('actions') ?></th>
                                </tr>
                                </thead>
                                <tbody><!-- Dynamic Content --></tbody>
                            </table>

                            <br>

                            <h3><?= lang('working_plan_exceptions') ?></h3>

                            <p>
                                <?= lang('add_working_plan_exceptions_during_each_day') ?>
                            </p>

                            <div>
                                <button type="button" class="add-working-plan-exception btn btn-primary mr-2">
                                    <i class="fas fa-plus-square"></i>
                                    <?= lang('add_working_plan_exception') ?>
                                </button>
                            </div>

                            <br>

                            <table class="working-plan-exceptions table table-striped">
                                <thead>
                                <tr>
                                    <th><?= lang('day') ?></th>
                                    <th><?= lang('start') ?></th>
                                    <th><?= lang('end') ?></th>
                                    <th><?= lang('actions') ?></th>
                                </tr>
                                </thead>
                                <tbody><!-- Dynamic Content --></tbody>
                            </table>

                            <?php require __DIR__ . '/working_plan_exceptions_modal.php' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECRETARIES TAB -->

        <div class="tab-pane" id="secretaries">
            <div class="row">
                <div id="filter-secretaries" class="filter-records column col-12 col-md-5">
                    <form class="mb-4">
                        <div class="input-group">
                            <input type="text" class="key form-control">

                            <span class="input-group-addon">
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
                    </span>
                        </div>
                    </form>

                    <h3><?= lang('secretaries') ?></h3>

                    <div class="results"></div>
                </div>

                <div class="record-details column col-12 col-md-7">
                    <div class="btn-toolbar mb-4">
                        <div class="add-edit-delete-group btn-group">
                            <button id="add-secretary" class="btn btn-primary">
                                <i class="fas fa-plus-square mr-2"></i>
                                <?= lang('add') ?>
                            </button>
                            <button id="edit-secretary" class="btn btn-outline-secondary" disabled="disabled">
                                <i class="fas fa-edit mr-2"></i>
                                <?= lang('edit') ?>
                            </button>
                            <button id="delete-secretary" class="btn btn-outline-secondary" disabled="disabled">
                                <i class="fas fa-trash-alt mr-2"></i>
                                <?= lang('delete') ?>
                            </button>
                        </div>

                        <div class="save-cancel-group btn-group" style="display:none;">
                            <button id="save-secretary" class="btn btn-primary">
                                <i class="fas fa-check-square mr-2"></i>
                                <?= lang('save') ?>
                            </button>
                            <button id="cancel-secretary" class="btn btn-outline-secondary">
                                <i class="fas fa-ban mr-2"></i>
                                <?= lang('cancel') ?>
                            </button>
                        </div>
                    </div>

                    <h3><?= lang('details') ?></h3>

                    <div class="form-message alert" style="display:none;"></div>

                    <input type="hidden" id="secretary-id" class="record-id">

                    <div class="row">
                        <div class="secretary-details col-12 col-md-6">
                            <div class="form-group">
                                <label for="secretary-first-name">
                                    <?= lang('first_name') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <input id="secretary-first-name" class="form-control required" maxlength="256">
                            </div>

                            <div class="form-group">
                                <label for="secretary-last-name">
                                    <?= lang('last_name') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <input id="secretary-last-name" class="form-control required" maxlength="512">
                            </div>

                            <div class="form-group">
                                <label for="secretary-email">
                                    <?= lang('email') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <input id="secretary-email" class="form-control required" maxlength="512">
                            </div>

                            <div class="form-group">
                                <label for="secretary-phone-number">
                                    <?= lang('phone_number') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <input id="secretary-phone-number" class="form-control required" maxlength="128">
                            </div>

                            <div class="form-group">
                                <label for="secretary-mobile-number">
                                    <?= lang('mobile_number') ?>

                                </label>
                                <input id="secretary-mobile-number" class="form-control" maxlength="128">
                            </div>

                            <div class="form-group">
                                <label for="secretary-address">
                                    <?= lang('address') ?>
                                </label>
                                <input id="secretary-address" class="form-control" maxlength="256">
                            </div>

                            <div class="form-group">
                                <label for="secretary-city">
                                    <?= lang('city') ?>

                                </label>
                                <input id="secretary-city" class="form-control" maxlength="256">
                            </div>

                            <div class="form-group">
                                <label for="secretary-state">
                                    <?= lang('state') ?>
                                </label>
                                <input id="secretary-state" class="form-control" maxlength="128">
                            </div>

                            <div class="form-group">
                                <label for="secretary-zip-code">
                                    <?= lang('zip_code') ?>

                                </label>
                                <input id="secretary-zip-code" class="form-control" maxlength="64">
                            </div>

                            <div class="form-group">
                                <label for="secretary-notes">
                                    <?= lang('notes') ?>
                                </label>
                                <textarea id="secretary-notes" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="secretary-settings col-12 col-md-6">
                            <div class="form-group">
                                <label for="secretary-username">
                                    <?= lang('username') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <input id="secretary-username" class="form-control required" maxlength="256">
                            </div>

                            <div class="form-group">
                                <label for="secretary-password">
                                    <?= lang('password') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="password" id="secretary-password" class="form-control required"
                                       maxlength="512" autocomplete="new-password">
                            </div>

                            <div class="form-group">
                                <label for="secretary-password-confirm">
                                    <?= lang('retype_password') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="password" id="secretary-password-confirm" class="form-control required"
                                       maxlength="512" autocomplete="new-password">
                            </div>

                            <div class="form-group">
                                <label for="secretary-calendar-view">
                                    <?= lang('calendar') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <select id="secretary-calendar-view" class="form-control required">
                                    <option value="default">Default</option>
                                    <option value="table">Table</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="secretary-timezone">
                                    <?= lang('timezone') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?= render_timezone_dropdown('id="secretary-timezone" class="form-control required"') ?>
                            </div>

                            <br>

                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="secretary-notifications">
                                <label class="custom-control-label" for="secretary-notifications">
                                    <?= lang('receive_notifications') ?>
                                </label>
                            </div>

                            <br>

                            <h4><?= lang('providers') ?></h4>
                            <div id="secretary-providers" class="card card-body bg-light border-light"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ADMINS TAB -->

        <div class="tab-pane" id="admins">
            <div class="row">
                <div id="filter-admins" class="filter-records column col-12 col-md-5">
                    <form class="mb-4">
                        <div class="input-group">
                            <input type="text" class="key form-control">

                            <span class="input-group-addon">
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
                    </span>
                        </div>
                    </form>

                    <h3><?= lang('admins') ?></h3>

                    <div class="results"></div>
                </div>

                <div class="record-details column col-12 col-md-7">
                    <div class="btn-toolbar mb-4">
                        <div class="add-edit-delete-group btn-group">
                            <button id="add-admin" class="btn btn-primary">
                                <i class="fas fa-plus-square mr-2"></i>
                                <?= lang('add') ?>
                            </button>
                            <button id="edit-admin" class="btn btn-outline-secondary" disabled="disabled">
                                <i class="fas fa-edit mr-2"></i>
                                <?= lang('edit') ?>
                            </button>
                            <button id="delete-admin" class="btn btn-outline-secondary" disabled="disabled">
                                <i class="fas fa-trash-alt mr-2"></i>
                                <?= lang('delete') ?>
                            </button>
                        </div>

                        <div class="save-cancel-group btn-group" style="display:none;">
                            <button id="save-admin" class="btn btn-primary">
                                <i class="fas fa-check-square mr-2"></i>
                                <?= lang('save') ?>
                            </button>
                            <button id="cancel-admin" class="btn btn-outline-secondary">
                                <i class="fas fa-ban mr-2"></i>
                                <?= lang('cancel') ?>
                            </button>
                        </div>
                    </div>

                    <h3><?= lang('details') ?></h3>

                    <div class="form-message alert" style="display:none;"></div>

                    <input type="hidden" id="admin-id" class="record-id">

                    <div class="row">
                        <div class="admin-details col-12 col-md-6">
                            <div class="form-group">
                                <label for="first-name">
                                    <?= lang('first_name') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <input id="admin-first-name" class="form-control required" maxlength="256">
                            </div>

                            <div class="form-group">
                                <label for="admin-last-name">
                                    <?= lang('last_name') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <input id="admin-last-name" class="form-control required" maxlength="512">
                            </div>

                            <div class="form-group">
                                <label for="admin-email">
                                    <?= lang('email') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <input id="admin-email" class="form-control required" maxlength="512">
                            </div>

                            <div class="form-group">
                                <label for="admin-phone-number">
                                    <?= lang('phone_number') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <input id="admin-phone-number" class="form-control required" maxlength="128">
                            </div>

                            <div class="form-group">
                                <label for="admin-mobile-number">
                                    <?= lang('mobile_number') ?>

                                </label>
                                <input id="admin-mobile-number" class="form-control" maxlength="128">
                            </div>

                            <div class="form-group">
                                <label for="admin-address">
                                    <?= lang('address') ?>
                                </label>
                                <input id="admin-address" class="form-control" maxlength="256">
                            </div>

                            <div class="form-group">
                                <label for="admin-city">
                                    <?= lang('city') ?>

                                </label>
                                <input id="admin-city" class="form-control" maxlength="256">
                            </div>

                            <div class="form-group">
                                <label for="admin-state">
                                    <?= lang('state') ?>
                                </label>
                                <input id="admin-state" class="form-control" maxlength="128">
                            </div>

                            <div class="form-group">
                                <label for="admin-zip-code">
                                    <?= lang('zip_code') ?>

                                </label>
                                <input id="admin-zip-code" class="form-control" maxlength="64">
                            </div>

                            <div class="form-group">
                                <label for="admin-notes">
                                    <?= lang('notes') ?>
                                </label>
                                <textarea id="admin-notes" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="admin-settings col-12 col-md-6">
                            <div class="form-group">
                                <label for="admin-username">
                                    <?= lang('username') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <input id="admin-username" class="form-control required" maxlength="256">
                            </div>

                            <div class="form-group">
                                <label for="admin-password">
                                    <?= lang('password') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="password" id="admin-password" class="form-control required" maxlength="512"
                                       autocomplete="new-password">
                            </div>

                            <div class="form-group">
                                <label for="admin-password-confirm">
                                    <?= lang('retype_password') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="password" id="admin-password-confirm" class="form-control required"
                                       maxlength="512" autocomplete="new-password">
                            </div>

                            <div class="form-group">
                                <label for="admin-calendar-view">
                                    <?= lang('calendar') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <select id="admin-calendar-view" class="form-control required">
                                    <option value="default">Default</option>
                                    <option value="table">Table</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="admin-timezone">
                                    <?= lang('timezone') ?>
                                    <span class="text-danger">*</span>
                                </label>
                                <?= render_timezone_dropdown('id="admin-timezone" class="form-control required"') ?>
                            </div>

                            <br>

                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="admin-notifications">
                                <label class="custom-control-label" for="admin-notifications">
                                    <?= lang('receive_notifications') ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
