<div id="appointments-modal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><?= lang('edit_appointment_title') ?></h3>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="modal-message alert d-none"></div>

                <form>
                    <fieldset>
                        <legend><?= lang('appointment_details_title') ?></legend>

                        <input id="appointment-id" type="hidden">

                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="mb-3">
                                    <label for="select-service" class="form-label">
                                        <?= lang('service') ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select id="select-service" class="required form-control">
                                        <?php
                                        // Group services by category, only if there is at least one service
                                        // with a parent category.
                                        $has_category = FALSE;
                                        foreach (vars('available_services') as $service)
                                        {
                                            if ( ! empty($service['category_id']))
                                            {
                                                $has_category = TRUE;
                                                break;
                                            }
                                        }

                                        if ($has_category)
                                        {
                                            $grouped_services = [];

                                            foreach (vars('available_services') as $service)
                                            {
                                                if ( ! empty($service['category_id']))
                                                {
                                                    if ( ! isset($grouped_services[$service['category_name']]))
                                                    {
                                                        $grouped_services[$service['category_name']] = [];
                                                    }

                                                    $grouped_services[$service['category_name']][] = $service;
                                                }
                                            }

                                            // We need the uncategorized services at the end of the list so we will use
                                            // another iteration only for the uncategorized services.
                                            $grouped_services['uncategorized'] = [];
                                            foreach (vars('available_services') as $service)
                                            {
                                                if ($service['category_id'] == NULL)
                                                {
                                                    $grouped_services['uncategorized'][] = $service;
                                                }
                                            }

                                            foreach ($grouped_services as $key => $group)
                                            {
                                                $group_label = $key !== 'uncategorized'
                                                    ? $group[0]['category_name']
                                                    : 'Uncategorized';

                                                if (count($group) > 0)
                                                {
                                                    echo '<optgroup label="' . $group_label . '">';

                                                    foreach ($group as $service)
                                                    {
                                                        echo '<option value="' . $service['id'] . '">'
                                                            . $service['name'] . '</option>';
                                                    }

                                                    echo '</optgroup>';
                                                }
                                            }
                                        }
                                        else
                                        {
                                            foreach (vars('available_services') as $service)
                                            {
                                                echo '<option value="' . $service['id'] . '">'
                                                    . $service['name'] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="select-provider" class="form-label">
                                        <?= lang('provider') ?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select id="select-provider" class="required form-control"></select>
                                </div>

                                <div class="mb-3">
                                    <label for="appointment-location" class="form-label">
                                        <?= lang('location') ?>
                                    </label>
                                    <input id="appointment-location" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label for="appointment-notes" class="form-label"><?= lang('notes') ?></label>
                                    <textarea id="appointment-notes" class="form-control" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="mb-3">
                                    <label for="start-datetime"
                                           class="form-label"><?= lang('start_date_time') ?></label>
                                    <input id="start-datetime" class="required form-control">
                                </div>

                                <div class="mb-3">
                                    <label for="end-datetime" class="form-label"><?= lang('end_date_time') ?></label>
                                    <input id="end-datetime" class="required form-control">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><?= lang('timezone') ?></label>

                                    <ul>
                                        <li>
                                            <?= lang('provider') ?>:
                                            <span class="provider-timezone">
                                                -
                                            </span>
                                        </li>
                                        <li>
                                            <?= lang('current_user') ?>:
                                            <span>
                                                <?= vars('timezones')[session('timezone', 'UTC')] ?>
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <br>

                    <fieldset>
                        <legend>
                            <?= lang('customer_details_title') ?>
                            <button id="new-customer" class="btn btn-outline-secondary btn-sm" type="button"
                                    data-tippy-content="<?= lang('clear_fields_add_existing_customer_hint') ?>">
                                <i class="fas fa-plus-square me-2"></i>
                                <?= lang('new') ?>
                            </button>
                            <button id="select-customer" class="btn btn-outline-secondary btn-sm" type="button"
                                    data-tippy-content="<?= lang('pick_existing_customer_hint') ?>">
                                <i class="fas fa-hand-pointer me-2"></i>
                                <span>
                                    <?= lang('select') ?>
                                </span>
                            </button>
                            <input id="filter-existing-customers"
                                   placeholder="<?= lang('type_to_filter_customers') ?>"
                                   style="display: none;" class="input-sm form-control">
                            <div id="existing-customers-list" style="display: none;"></div>
                        </legend>

                        <input id="customer-id" type="hidden">

                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="mb-3">
                                    <label for="first-name" class="form-label">
                                        <?= lang('first_name') ?>
                                        <?php if (vars('require_first_name')): ?>
                                            <span class="text-danger">*</span>
                                        <?php endif ?>
                                    </label>
                                    <input type="text" id="first-name"
                                           class="<?= vars('require_first_name') ? 'required' : '' ?> form-control"
                                           maxlength="100"/>
                                </div>

                                <div class="mb-3">
                                    <label for="last-name" class="form-label">
                                        <?= lang('last_name') ?>
                                        <?php if (vars('require_last_name')): ?>
                                            <span class="text-danger">*</span>
                                        <?php endif ?>
                                    </label>
                                    <input type="text" id="last-name"
                                           class="<?= vars('require_last_name') ? 'required' : '' ?> form-control"
                                           maxlength="120"/>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <?= lang('email') ?>
                                        <?php if (vars('require_email')): ?>
                                            <span class="text-danger">*</span>
                                        <?php endif ?>
                                    </label>
                                    <input type="text" id="email"
                                           class="<?= vars('require_email') ? 'required' : '' ?> form-control"
                                           maxlength="120"/>
                                </div>

                                <div class="mb-3">
                                    <label for="phone-number" class="form-label">
                                        <?= lang('phone_number') ?>
                                        <?php if (vars('require_phone_number')): ?>
                                            <span class="text-danger">*</span>
                                        <?php endif ?>
                                    </label>
                                    <input type="text" id="phone-number" maxlength="60"
                                           class="<?= vars('require_phone_number') ? 'required' : '' ?> form-control"/>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="mb-3">
                                    <label for="address" class="form-label">
                                        <?= lang('address') ?>
                                        <?php if (vars('require_address')): ?>
                                            <span class="text-danger">*</span>
                                        <?php endif ?>
                                    </label>
                                    <input type="text" id="address"
                                           class="<?= vars('require_address') ? 'required' : '' ?> form-control"
                                           maxlength="120"/>
                                </div>

                                <div class="mb-3">
                                    <label for="city" class="form-label">
                                        <?= lang('city') ?>
                                        <?php if (vars('require_city')): ?>
                                            <span class="text-danger">*</span>
                                        <?php endif ?>
                                    </label>
                                    <input type="text" id="city"
                                           class="<?= vars('require_city') ? 'required' : '' ?> form-control"
                                           maxlength="120"/>
                                </div>

                                <div class="mb-3">
                                    <label for="zip-code" class="form-label">
                                        <?= lang('zip_code') ?>
                                        <?php if (vars('require_zip_cod')): ?>
                                            <span class="text-danger">*</span>
                                        <?php endif ?>
                                    </label>
                                    <input type="text" id="zip-code"
                                           class="<?= vars('require_zip_code') ? 'required' : '' ?> form-control"
                                           maxlength="120"/>
                                </div>

                                <div class="mb-3">
                                    <label for="customer-notes" class="form-label">
                                        <?= lang('notes') ?>
                                        <?php if (vars('require_zip_code')): ?>
                                            <span class="text-danger">*</span>
                                        <?php endif ?>
                                    </label>
                                    <textarea id="customer-notes" rows="2"
                                              class="<?= vars('require_zip_code') ? 'required' : '' ?> form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-ban"></i>
                    <?= lang('cancel') ?>
                </button>
                <button id="save-appointment" class="btn btn-primary">
                    <i class="fas fa-check-square me-2"></i>
                    <?= lang('save') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<?php section('scripts') ?>

<script src="<?= asset_url('assets/js/components/appointments_modal.js') ?>"></script>

<?php section('scripts') ?>
