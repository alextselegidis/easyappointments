<script src="<?= asset_url('assets/js/backend_users_admins.js') ?>"></script>
<script src="<?= asset_url('assets/js/backend_users_providers.js') ?>"></script>
<script src="<?= asset_url('assets/js/backend_users_secretaries.js') ?>"></script>
<script src="<?= asset_url('assets/js/backend_users.js') ?>"></script>
<script src="<?= asset_url('assets/js/working_plan.js') ?>"></script>
<script src="<?= asset_url('assets/ext/jquery-ui/jquery-ui-timepicker-addon.min.js') ?>"></script>
<script src="<?= asset_url('assets/ext/jquery-jeditable/jquery.jeditable.min.js') ?>"></script>
<script>
    var GlobalVariables = {
        csrfToken         : <?= json_encode($this->security->get_csrf_hash()) ?>,
        baseUrl           : <?= json_encode($base_url) ?>,
        dateFormat        : <?= json_encode($date_format) ?>,
        firstWeekday      : <?= json_encode($first_weekday); ?>,
        timeFormat        : <?= json_encode($time_format) ?>,
        admins            : <?= json_encode($admins) ?>,
        providers         : <?= json_encode($providers) ?>,
        secretaries       : <?= json_encode($secretaries) ?>,
        services          : <?= json_encode($services) ?>,
        timezones         : <?= json_encode($timezones) ?>,
        workingPlan       : <?= json_encode(json_decode($working_plan)) ?>,
        extraWorkingPlan  : <?= json_encode(json_decode($extra_working_plan)) ?>,
        user           : {
            id         : <?= $user_id ?>,
            email      : <?= json_encode($user_email) ?>,
            timezone   : <?= json_encode($timezone) ?>,
            role_slug  : <?= json_encode($role_slug) ?>,
            privileges : <?= json_encode($privileges) ?>
        }
    };

    $(document).ready(function() {
        BackendUsers.initialize(true);
    });
</script>

<div id="users-page" class="container-fluid backend-page">

    <!-- PAGE NAVIGATION -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="nav-item"><a class="nav-link active" href="#providers" aria-controls="providers" role="tab" data-toggle="tab"><?= lang('providers') ?></a></li>
        <li role="presentation" class="nav-item"><a class="nav-link" href="#secretaries" aria-controls="secretaries" role="tab" data-toggle="tab"><?= lang('secretaries') ?></a></li>
        <li role="presentation" class="nav-item"><a class="nav-link" href="#admins" aria-controls="admins" role="tab" data-toggle="tab"><?= lang('admins') ?></a></li>
    </ul>

    <div class="tab-content">

        <!-- PROVIDERS TAB -->

        <div role="tabpanel" class="tab-pane active" id="providers">
            <div class="row">
                <div id="filter-providers" class="filter-records column col-xs-12 col-md-5">
                    <form class="mb-4">
                        <div class="input-group">
                            <input type="text" class="key form-control">

                            <span class="input-group-addon">
                        <div>
                            <button class="filter btn btn-light" type="submit" title="<?= lang('filter') ?>">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="clear btn btn-light" type="button" title="<?= lang('clear') ?>">
                                <i class="fas fa-redo-alt"></i>
                            </button>
                        </div>
                    </span>
                        </div>
                    </form>

                    <h3><?= lang('providers') ?></h3>
                    <div class="results"></div>
                </div>

                <div class="record-details column col-xs-12 col-md-7">
                    <div class="float-md-left mb-4">
                        <div class="add-edit-delete-group btn-group">
                            <button id="add-provider" class="btn btn-info">
                                <i class="fas fa-plus"></i>
                                <?= lang('add') ?>
                            </button>
                            <button id="edit-provider" class="btn btn-light" disabled="disabled">
                                <i class="far fa-edit"></i>
                                <?= lang('edit') ?>
                            </button>
                            <button id="delete-provider" class="btn btn-light" disabled="disabled">
                                <i class="far fa-trash-alt"></i>
                                <?= lang('delete') ?>
                            </button>
                        </div>

                        <div class="save-cancel-group btn-group" style="display:none;">
                            <button id="save-provider" class="btn btn-info">
                                <i class="far fa-check-circle"></i>
                                <?= lang('save') ?>
                            </button>
                            <button id="cancel-provider" class="btn btn-light">
                                <i class="fas fa-ban"></i>
                                <?= lang('cancel') ?>
                            </button>
                        </div>
                    </div>

                    <div class="switch-view float-md-right d-md-flex align-items-center mb-4">
                        <strong><?= lang('current_view') ?></strong>
                        <div class="display-details current"><?= lang('details') ?></div>
                        <div class="display-working-plan"><?= lang('working_plan') ?></div>
                    </div>

                    <?php // This form message is outside the details view, so that it can be
                    // visible when the user has working plan view active. ?>
                    <div class="form-message alert" style="display:none;"></div>

                    <div class="details-view provider-view">
                        <h3><?= lang('details') ?></h3>

                        <input type="hidden" id="provider-id" class="record-id">

                        <div class="row">
                            <div class="provider-details col-xs-12 col-md-6">
                                <div class="form-group">
                                    <label for="provider-first-name"><?= lang('first_name') ?> *</label>
                                    <input id="provider-first-name" class="form-control required" maxlength="256">
                                </div>

                                <div class="form-group">
                                    <label for="provider-last-name"><?= lang('last_name') ?> *</label>
                                    <input id="provider-last-name" class="form-control required" maxlength="512">
                                </div>

                                <div class="form-group">
                                    <label for="provider-email"><?= lang('email') ?> *</label>
                                    <input id="provider-email" class="form-control required" max="512">
                                </div>

                                <div class="form-group">
                                    <label for="provider-phone-number"><?= lang('phone_number') ?> *</label>
                                    <input id="provider-phone-number" class="form-control required" max="128">
                                </div>

                                <div class="form-group">
                                    <label for="provider-mobile-number"><?= lang('mobile_number') ?></label>
                                    <input id="provider-mobile-number" class="form-control" maxlength="128">
                                </div>

                                <div class="form-group">
                                    <label for="provider-address"><?= lang('address') ?></label>
                                    <input id="provider-address" class="form-control" maxlength="256">
                                </div>

                                <div class="form-group">
                                    <label for="provider-city"><?= lang('city') ?></label>
                                    <input id="provider-city" class="form-control" maxlength="256">
                                </div>

                                <div class="form-group">
                                    <label for="provider-state"><?= lang('state') ?></label>
                                    <input id="provider-state" class="form-control" maxlength="256">
                                </div>

                                <div class="form-group">
                                    <label for="provider-zip-code"><?= lang('zip_code') ?></label>
                                    <input id="provider-zip-code" class="form-control" maxlength="64">
                                </div>

                                <div class="form-group">
                                    <label for="provider-notes"><?= lang('notes') ?></label>
                                    <textarea id="provider-notes" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="provider-settings col-xs-12 col-md-6">
                                <div class="form-group">
                                    <label for="provider-username"><?= lang('username') ?> *</label>
                                    <input id="provider-username" class="form-control required" maxlength="256">
                                </div>

                                <div class="form-group">
                                    <label for="provider-password"><?= lang('password') ?> *</label>
                                    <input type="password" id="provider-password" class="form-control required" maxlength="512" autocomplete="new-password">
                                </div>

                                <div class="form-group">
                                    <label for="provider-password-confirm"><?= lang('retype_password') ?> *</label>
                                    <input type="password" id="provider-password-confirm" class="form-control required" maxlength="512" autocomplete="new-password">
                                </div>

                                <div class="form-group">
                                    <label for="provider-calendar-view"><?= lang('calendar') ?> *</label>
                                    <select id="provider-calendar-view" class="form-control required">
                                        <option value="default">Default</option>
                                        <option value="table">Table</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="provider-timezone"><?= lang('timezone') ?></label>
                                    <?= render_timezone_dropdown('id="provider-timezone" class="form-control"') ?>
                                </div>

                                <br>

                                <button type="button" id="provider-notifications" class="btn btn-light" data-toggle="button">
                                    <i class="far fa-envelope"></i>
                                    <span><?= lang('receive_notifications') ?></span>
                                </button>

                                <br><br>

                                <h4><?= lang('services') ?></h4>
                                <div id="provider-services" class="card card-body bg-light border-light"></div>
                            </div>
                        </div>
                    </div>

                    <div class="working-plan-view provider-view" style="display: none;">
                        <h3><?= lang('working_plan') ?></h3>
                        <button id="reset-working-plan" class="btn btn-info"
                                title="<?= lang('reset_working_plan') ?>">
                            <i class="fas fa-redo-alt"></i>
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

                        <span class="help-block">
                            <?= lang('add_breaks_during_each_day') ?>
                        </span>

                        <div>
                            <button type="button" class="add-break btn btn-info">
                                <i class="fas fa-plus"></i>
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

                        <h3><?= lang('extra_periods') ?></h3>

                        <span class="help-block">
                            <?= lang('add_extra_periods_during_each_day') ?>
                        </span>

                        <div>
                            <button type="button" class="add-extra-periods btn btn-primary">
                                <span class="glyphicon glyphicon-plus"></span>
                                <?= lang('add_extra_period') ?>
                            </button>
                        </div>

                        <br>

                        <table class="extra-periods table table-striped">
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
                    </div>
                </div>
            </div>
        </div>

        <!-- SECRETARIES TAB -->

        <div role="tabpanel" class="tab-pane" id="secretaries">
            <div class="row">
                <div id="filter-secretaries" class="filter-records column col-xs-12 col-md-5">
                    <form class="mb-4">
                        <div class="input-group">
                            <input type="text" class="key form-control">

                            <span class="input-group-addon">
                        <div>
                            <button class="filter btn btn-light" type="submit" title="<?= lang('filter') ?>">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="clear btn btn-light" type="button" title="<?= lang('clear') ?>">
                                <i class="fas fa-redo-alt"></i>
                            </button>
                        </div>
                    </span>
                        </div>
                    </form>

                    <h3><?= lang('secretaries') ?></h3>

                    <div class="results"></div>
                </div>

                <div class="record-details column col-xs-12 col-md-7">
                    <div class="btn-toolbar mb-4">
                        <div class="add-edit-delete-group btn-group">
                            <button id="add-secretary" class="btn btn-info">
                                <i class="fas fa-plus"></i>
                                <?= lang('add') ?>
                            </button>
                            <button id="edit-secretary" class="btn btn-light" disabled="disabled">
                                <i class="far fa-edit"></i>
                                <?= lang('edit') ?>
                            </button>
                            <button id="delete-secretary" class="btn btn-light" disabled="disabled">
                                <i class="far fa-trash-alt"></i>
                                <?= lang('delete') ?>
                            </button>
                        </div>

                        <div class="save-cancel-group btn-group" style="display:none;">
                            <button id="save-secretary" class="btn btn-info">
                                <i class="far fa-check-circle"></i>
                                <?= lang('save') ?>
                            </button>
                            <button id="cancel-secretary" class="btn btn-light">
                                <i class="fas fa-ban"></i>
                                <?= lang('cancel') ?>
                            </button>
                        </div>
                    </div>

                    <h3><?= lang('details') ?></h3>

                    <div class="form-message alert" style="display:none;"></div>

                    <input type="hidden" id="secretary-id" class="record-id">

                    <div class="row">
                        <div class="secretary-details col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="secretary-first-name"><?= lang('first_name') ?> *</label>
                                <input id="secretary-first-name" class="form-control required" maxlength="256">
                            </div>

                            <div class="form-group">
                                <label for="secretary-last-name"><?= lang('last_name') ?> *</label>
                                <input id="secretary-last-name" class="form-control required" maxlength="512">
                            </div>

                            <div class="form-group">
                                <label for="secretary-email"><?= lang('email') ?> *</label>
                                <input id="secretary-email" class="form-control required" maxlength="512">
                            </div>

                            <div class="form-group">
                                <label for="secretary-phone-number"><?= lang('phone_number') ?> *</label>
                                <input id="secretary-phone-number" class="form-control required" maxlength="128">
                            </div>

                            <div class="form-group">
                                <label for="secretary-mobile-number"><?= lang('mobile_number') ?></label>
                                <input id="secretary-mobile-number" class="form-control" maxlength="128">
                            </div>

                            <div class="form-group">
                                <label for="secretary-address"><?= lang('address') ?></label>
                                <input id="secretary-address" class="form-control" maxlength="256">
                            </div>

                            <div class="form-group">
                                <label for="secretary-city"><?= lang('city') ?></label>
                                <input id="secretary-city" class="form-control" maxlength="256">
                            </div>

                            <div class="form-group">
                                <label for="secretary-state"><?= lang('state') ?></label>
                                <input id="secretary-state" class="form-control" maxlength="128">
                            </div>

                            <div class="form-group">
                                <label for="secretary-zip-code"><?= lang('zip_code') ?></label>
                                <input id="secretary-zip-code" class="form-control" maxlength="64">
                            </div>

                            <div class="form-group">
                                <label for="secretary-notes"><?= lang('notes') ?></label>
                                <textarea id="secretary-notes" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="secretary-settings col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="secretary-username"><?= lang('username') ?> *</label>
                                <input id="secretary-username" class="form-control required" maxlength="256">
                            </div>

                            <div class="form-group">
                                <label for="secretary-password"><?= lang('password') ?> *</label>
                                <input type="password" id="secretary-password" class="form-control required" maxlength="512" autocomplete="new-password">
                            </div>

                            <div class="form-group">
                                <label for="secretary-password-confirm"><?= lang('retype_password') ?> *</label>
                                <input type="password" id="secretary-password-confirm" class="form-control required" maxlength="512" autocomplete="new-password">
                            </div>

                            <div class="form-group">
                                <label for="secretary-calendar-view"><?= lang('calendar') ?> *</label>
                                <select id="secretary-calendar-view" class="form-control required">
                                    <option value="default">Default</option>
                                    <option value="table">Table</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="secretary-timezone"><?= lang('timezone') ?></label>
                                <?= render_timezone_dropdown('id="secretary-timezone" class="form-control"') ?>
                            </div>

                            <br>

                            <button type="button" id="secretary-notifications" class="btn btn-light" data-toggle="button">
                                <i class="far fa-envelope"></i>
                                <span><?= lang('receive_notifications') ?></span>
                            </button>

                            <br><br>

                            <h4><?= lang('providers') ?></h4>
                            <div id="secretary-providers" class="card card-body bg-light border-light"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ADMINS TAB -->

        <div role="tabpanel" class="tab-pane" id="admins">
            <div class="row">
                <div id="filter-admins" class="filter-records column col-xs-12 col-md-5">
                    <form class="mb-4">
                        <div class="input-group">
                            <input type="text" class="key form-control">

                            <span class="input-group-addon">
                        <div>
                            <button class="filter btn btn-light" type="submit" title="<?= lang('filter') ?>">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="clear btn btn-light" type="button" title="<?= lang('clear') ?>">
                                <i class="fas fa-redo-alt"></i>
                            </button>
                        </div>
                    </span>
                        </div>
                    </form>

                    <h3><?= lang('admins') ?></h3>

                    <div class="results"></div>
                </div>

                <div class="record-details column col-xs-12 col-md-7">
                    <div class="btn-toolbar mb-4">
                        <div class="add-edit-delete-group btn-group">
                            <button id="add-admin" class="btn btn-info">
                                <i class="fas fa-plus"></i>
                                <?= lang('add') ?>
                            </button>
                            <button id="edit-admin" class="btn btn-light" disabled="disabled">
                                <i class="far fa-edit"></i>
                                <?= lang('edit') ?>
                            </button>
                            <button id="delete-admin" class="btn btn-light" disabled="disabled">
                                <i class="far fa-trash-alt"></i>
                                <?= lang('delete') ?>
                            </button>
                        </div>

                        <div class="save-cancel-group btn-group" style="display:none;">
                            <button id="save-admin" class="btn btn-info">
                                <i class="far fa-check-circle"></i>
                                <?= lang('save') ?>
                            </button>
                            <button id="cancel-admin" class="btn btn-light">
                                <i class="fas fa-ban"></i>
                                <?= lang('cancel') ?>
                            </button>
                        </div>
                    </div>

                    <h3><?= lang('details') ?></h3>

                    <div class="form-message alert" style="display:none;"></div>

                    <input type="hidden" id="admin-id" class="record-id">

                    <div class="row">
                        <div class="admin-details col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="first-name"><?= lang('first_name') ?> *</label>
                                <input id="admin-first-name" class="form-control required" maxlength="256">
                            </div>

                            <div class="form-group">
                                <label for="admin-last-name"><?= lang('last_name') ?> *</label>
                                <input id="admin-last-name" class="form-control required" maxlength="512">
                            </div>

                            <div class="form-group">
                                <label for="admin-email"><?= lang('email') ?> *</label>
                                <input id="admin-email" class="form-control required" maxlength="512">
                            </div>

                            <div class="form-group">
                                <label for="admin-phone-number"><?= lang('phone_number') ?> *</label>
                                <input id="admin-phone-number" class="form-control required" maxlength="128">
                            </div>

                            <div class="form-group">
                                <label for="admin-mobile-number"><?= lang('mobile_number') ?></label>
                                <input id="admin-mobile-number" class="form-control" maxlength="128">
                            </div>

                            <div class="form-group">
                                <label for="admin-address"><?= lang('address') ?></label>
                                <input id="admin-address" class="form-control" maxlength="256">
                            </div>

                            <div class="form-group">
                                <label for="admin-city"><?= lang('city') ?></label>
                                <input id="admin-city" class="form-control" maxlength="256">
                            </div>

                            <div class="form-group">
                                <label for="admin-state"><?= lang('state') ?></label>
                                <input id="admin-state" class="form-control" maxlength="128">
                            </div>

                            <div class="form-group">
                                <label for="admin-zip-code"><?= lang('zip_code') ?></label>
                                <input id="admin-zip-code" class="form-control" maxlength="64">
                            </div>

                            <div class="form-group">
                                <label for="admin-notes"><?= lang('notes') ?></label>
                                <textarea id="admin-notes" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="admin-settings col-xs-12 col-md-6">
                            <div class="form-group">
                                <label for="admin-username"><?= lang('username') ?> *</label>
                                <input id="admin-username" class="form-control required" maxlength="256">
                            </div>

                            <div class="form-group">
                                <label for="admin-password"><?= lang('password') ?> *</label>
                                <input type="password" id="admin-password" class="form-control required" maxlength="512" autocomplete="new-password">
                            </div>

                            <div class="form-group">
                                <label for="admin-password-confirm"><?= lang('retype_password') ?> *</label>
                                <input type="password" id="admin-password-confirm" class="form-control required" maxlength="512" autocomplete="new-password">
                            </div>

                            <div class="form-group">
                                <label for="admin-calendar-view"><?= lang('calendar') ?> *</label>
                                <select id="admin-calendar-view" class="form-control required">
                                    <option value="default">Default</option>
                                    <option value="table">Table</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="admin-timezone"><?= lang('timezone') ?></label>
                                <?= render_timezone_dropdown('id="admin-timezone" class="form-control"') ?>
                            </div>

                            <br>

                            <button type="button" id="admin-notifications" class="btn btn-light" data-toggle="button">
                                <i class="far fa-envelope"></i>
                                <span><?= lang('receive_notifications') ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
