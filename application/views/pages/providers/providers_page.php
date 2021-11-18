<?php
/**
 * @var string $timezones
 * @var string $services
 * @var array $privileges
 */
?>

<?php extend('layouts/backend/backend_layout') ?>

<?php section('content') ?>

<script src="<?= asset_url('assets/js/backend_providers_helper.js') ?>"></script>
<script src="<?= asset_url('assets/js/backend_providers.js') ?>"></script>
<script src="<?= asset_url('assets/js/working_plan.js') ?>"></script>
<script src="<?= asset_url('assets/js/working_plan_exceptions_modal.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.min.js') ?>"></script>
<script src="<?= asset_url('assets/vendor/jquery-jeditable/jquery.jeditable.min.js') ?>"></script>
<script>
    var GlobalVariables = {
        csrfToken: <?= json_encode($this->security->get_csrf_hash()) ?>,
        baseUrl: <?= json_encode(config('base_url')) ?>,
        dateFormat: <?= json_encode(setting('date_format')) ?>,
        timeFormat: <?= json_encode(setting('time_format')) ?>,
        firstWeekday: <?= json_encode(setting('first_weekday')) ?>,
        services: <?= json_encode($services) ?>,
        timezones: <?= json_encode($timezones) ?>,
        workingPlan: <?= json_encode(json_decode(setting('company_working_plan'))) ?>,
        user: {
            id: <?= session('user_id') ?>,
            email: <?= json_encode(session('user_email')) ?>,
            timezone: <?= json_encode(session('timezone')) ?>,
            role_slug: <?= json_encode(session('role_slug')) ?>,
            privileges: <?= json_encode($privileges) ?>
        }
    };

    $(function () {
        BackendProviders.initialize(true);
    });
</script>

<div class="container-fluid backend-page" id="users-page">
    <div class="row" id="providers">
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

                    <?php component('working_plan_exceptions_modal') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php section('content') ?>

