<script src="<?= asset_url('assets/ext/jquery-ui/jquery-ui-timepicker-addon.js') ?>"></script>
<script src="<?= asset_url('assets/js/backend_customers_helper.js') ?>"></script>
<script src="<?= asset_url('assets/js/backend_customers.js') ?>"></script>
<script>
    var GlobalVariables = {
        csrfToken          : <?= json_encode($this->security->get_csrf_hash()) ?>,
        availableProviders : <?= json_encode($available_providers) ?>,
        availableServices  : <?= json_encode($available_services) ?>,
        dateFormat         : <?= json_encode($date_format) ?>,
        baseUrl            : <?= json_encode($base_url) ?>,
        customers          : <?= json_encode($customers) ?>,
        user               : {
            id         : <?= $user_id ?>,
            email      : <?= json_encode($user_email) ?>,
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
    	<div id="filter-customers" class="filter-records column col-xs-12 col-sm-5">
    		<form>
                <div class="input-group">
                    <input type="text" class="key form-control">

                    <div class="input-group-addon">
                        <div>
                            <button class="filter btn btn-default" type="submit" title="<?= lang('filter') ?>">
                                <span class="glyphicon glyphicon-search"></span>
                            </button>
                            <button class="clear btn btn-default" type="button" title="<?= lang('clear') ?>">
                                <span class="glyphicon glyphicon-repeat"></span>
                            </button>
                        </div>
                    </div>
                </div>
    		</form>

            <h3><?= lang('customers') ?></h3>
            <div class="results"></div>
    	</div>

    	<div class="record-details col-xs-12 col-sm-7">
            <div class="btn-toolbar">
                <div id="add-edit-delete-group" class="btn-group">
                    <?php if ($privileges[PRIV_CUSTOMERS]['add'] === TRUE): ?>
                    <button id="add-customer" class="btn btn-primary">
                        <span class="glyphicon glyphicon-plus"></span>
                        <?= lang('add') ?>
                    </button>
                    <?php endif ?>

                    <?php if ($privileges[PRIV_CUSTOMERS]['edit'] === TRUE): ?>
                    <button id="edit-customer" class="btn btn-default" disabled="disabled">
                        <span class="glyphicon glyphicon-pencil"></span>
                        <?= lang('edit') ?>
                    </button>
                    <?php endif ?>

                    <?php if ($privileges[PRIV_CUSTOMERS]['delete'] === TRUE): ?>
                    <button id="delete-customer" class="btn btn-default" disabled="disabled">
                        <span class="glyphicon glyphicon-remove"></span>
                        <?= lang('delete') ?>
                    </button>
                    <?php endif ?>
                </div>

                <div id="save-cancel-group" class="btn-group" style="display:none;">
                    <button id="save-customer" class="btn btn-primary">
                        <span class="glyphicon glyphicon-ok"></span>
                        <?= lang('save') ?>
                    </button>
                    <button id="cancel-customer" class="btn btn-default">
                        <i class="glyphicon glyphicon-ban-circle"></i>
                        <?= lang('cancel') ?>
                    </button>
                </div>
            </div>

            <input id="customer-id" type="hidden">

            <div class="row">
                <div class="col-xs-12 col-sm-6" style="margin-left: 0;">
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
                        <label class="control-label" for="phone-number"><?= lang('phone_number') ?> *</label>
                        <input id="phone-number" class="form-control required">
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
                        <label class="control-label" for="notes"><?= lang('notes') ?></label>
                        <textarea id="notes" rows="4" class="form-control"></textarea>
                    </div>

                    <p class="text-center">
                        <em id="form-message" class="text-danger"><?= lang('fields_are_required') ?></em>
                    </p>
                </div>

                <div class="col-xs-12 col-sm-6">
                    <h3><?= lang('appointments') ?></h3>
                    <div id="customer-appointments" class="well"></div>
                    <div id="appointment-details" class="well hidden"></div>
                </div>
            </div>
    	</div>
    </div>
</div>
