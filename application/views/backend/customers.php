<script src="<?= asset_url('assets/ext/jquery-ui/jquery-ui-timepicker-addon.min.js') ?>"></script>
<script src="<?= asset_url('assets/js/backend_customers_helper.js') ?>"></script>
<script src="<?= asset_url('assets/js/backend_customers.js') ?>"></script>
<script>
    var GlobalVariables = {
        csrfToken          : <?= json_encode($this->security->get_csrf_hash()) ?>,
        availableProviders : <?= json_encode($available_providers) ?>,
        availableServices  : <?= json_encode($available_services) ?>,
        secretaryProviders : <?= json_encode($secretary_providers) ?>,
        dateFormat         : <?= json_encode($date_format) ?>,
        timeFormat         : <?= json_encode($time_format) ?>,
        baseUrl            : <?= json_encode($base_url) ?>,
        customers          : <?= json_encode($customers) ?>,
        timezones          : <?= json_encode($timezones) ?>,
        user               : {
            id         : <?= $user_id ?>,
            email      : <?= json_encode($user_email) ?>,
            timezone   : <?= json_encode($timezone) ?>,
            role_slug  : <?= json_encode($role_slug) ?>,
            privileges : <?= json_encode($privileges) ?>
        }
    };

    $(document).ready(function() {
        BackendCustomers.initialize(true);
    });
</script>

<div id="customers-page" class="container-fluid backend-page">
    <div class="row">
        <div id="filter-customers" class="filter-records col col-12 col-md-5">
            <form class="mb-4">
                <div class="input-group">
                    <input type="text" class="key form-control">

                    <div class="input-group-addon">
                        <div>
                            <button class="filter btn btn-light" type="submit" title="<?= lang('filter') ?>">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="clear btn btn-light" type="button" title="<?= lang('clear') ?>">
                            <i class="fas fa-redo-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <h3><?= lang('customers') ?></h3>
            <div class="results"></div>
    	</div>

    	<div class="record-details col-12 col-md-7">
            <div class="btn-toolbar mb-4">
                <div id="add-edit-delete-group" class="btn-group">
                    <?php if ($privileges[PRIV_CUSTOMERS]['add'] === TRUE): ?>
                    <button id="add-customer" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        <?= lang('add') ?>
                    </button>
                    <?php endif ?>

                    <?php if ($privileges[PRIV_CUSTOMERS]['edit'] === TRUE): ?>
                    <button id="edit-customer" class="btn btn-light" disabled="disabled">
                        <i class="far fa-edit"></i>
                        <?= lang('edit') ?>
                    </button>
                    <?php endif ?>

                    <?php if ($privileges[PRIV_CUSTOMERS]['delete'] === TRUE): ?>
                    <button id="delete-customer" class="btn btn-light" disabled="disabled">
                        <i class="far fa-trash-alt"></i>
                        <?= lang('delete') ?>
                    </button>
                    <?php endif ?>
                </div>

                <div id="save-cancel-group" class="btn-group" style="display:none;">
                    <button id="save-customer" class="btn btn-primary">
                        <i class="far fa-check-circle"></i>
                        <?= lang('save') ?>
                    </button>
                    <button id="cancel-customer" class="btn btn-light">
                        <i class="fas fa-ban"></i>
                        <?= lang('cancel') ?>
                    </button>
                </div>
            </div>

            <input id="customer-id" type="hidden">

            <div class="row">
                <div class="col-12 col-md-6" style="margin-left: 0;">
                    <h3><?= lang('details') ?></h3>

                    <div id="form-message" class="alert" style="display:none;"></div>

                    <div class="form-group">
                        <label class="control-label" for="first-name"><?= lang('first_name') ?> *</label>
                        <input id="first-name" class="form-control required">
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="last-name"><?= lang('last_name') ?> *</label>
                        <input id="last-name" class="form-control required">
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="email"><?= lang('email') ?> *</label>
                        <input id="email" class="form-control required">
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="phone-number"><?= lang('phone_number') ?>
                            <?= $require_phone_number === '1' ? '*' : ''; ?></label>
                        <input id="phone-number" class="form-control
                            <?= $require_phone_number === '1' ? 'required' : '' ?>">
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="address"><?= lang('address') ?></label>
                        <input id="address" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="city"><?= lang('city') ?></label>
                        <input id="city" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="zip-code"><?= lang('zip_code') ?></label>
                        <input id="zip-code" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="timezone"><?= lang('timezone') ?></label>
                        <?= render_timezone_dropdown('id="timezone" class="form-control"') ?>
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="notes"><?= lang('notes') ?></label>
                        <textarea id="notes" rows="4" class="form-control"></textarea>
                    </div>

                    <p class="text-center">
                        <em id="form-message" class="text-danger"><?= lang('fields_are_required') ?></em>
                    </p>
                </div>

                <div class="col-12 col-md-6">
                    <h3><?= lang('appointments') ?></h3>
                    <div id="customer-appointments" class="card card-body bg-light border-light"></div>
                    <div id="appointment-details" class="card card-body bg-light border-light d-none"></div>
                </div>
            </div>
    	</div>
    </div>
</div>
