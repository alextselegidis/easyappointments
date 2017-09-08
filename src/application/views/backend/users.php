<script src="<?php echo base_url('assets/js/backend_users_admins.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/backend_users_providers.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/backend_users_secretaries.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/backend_users.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/working_plan.js'); ?>"></script>
<script src="<?php echo base_url('assets/ext/jquery-ui/jquery-ui-timepicker-addon.js'); ?>"></script>
<script src="<?php echo base_url('assets/ext/jquery-jeditable/jquery.jeditable.min.js'); ?>"></script>
<script>
    var GlobalVariables = {
        csrfToken     : <?php echo json_encode($this->security->get_csrf_hash()); ?>,
        baseUrl       : <?php echo json_encode($base_url); ?>,
        dateFormat    : <?php echo json_encode($date_format); ?>,
        admins        : <?php echo json_encode($admins); ?>,
        providers     : <?php echo json_encode($providers); ?>,
        secretaries   : <?php echo json_encode($secretaries); ?>,
        services      : <?php echo json_encode($services); ?>,
        workingPlan   : <?php echo json_encode(json_decode($working_plan)); ?>,
        user          : {
            id        : <?php echo $user_id; ?>,
            email     : <?php echo json_encode($user_email); ?>,
            role_slug : <?php echo json_encode($role_slug); ?>,
            privileges: <?php echo json_encode($privileges); ?>
        }
    };

    $(document).ready(function() {
        BackendUsers.initialize(true);
    });
</script>

<div id="users-page" class="container-fluid backend-page">

    <!-- PAGE NAVIGATION -->

    <ul class="nav nav-tabs">
        <li role="presentation" class="admins-tab tab active"><a><?php echo lang('admins'); ?></a></li>
        <li role="presentation" class="providers-tab tab"><a><?php echo lang('providers'); ?></a></li>
        <li role="presentation" class="secretaries-tab tab"><a><?php echo lang('secretaries'); ?></a></li>
    </ul>

    <!-- ADMINS TAB -->

    <div id="admins" class="tab-content">
        <div class="row">
            <div id="filter-admins" class="filter-records column col-xs-12 col-sm-5">
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

                <h3><?php echo lang('admins'); ?></h3>
                <div class="results"></div>
            </div>

            <div class="record-details column col-xs-12 col-sm-7">
                <div class="btn-toolbar">
                    <div class="add-edit-delete-group btn-group">
                        <button id="add-admin" class="btn btn-primary">
                            <span class="glyphicon glyphicon-plus"></span>
                            <?php echo lang('add'); ?>
                        </button>
                        <button id="edit-admin" class="btn btn-default" disabled="disabled">
                            <span class="glyphicon glyphicon-pencil"></span>
                            <?php echo lang('edit'); ?>
                        </button>
                        <button id="delete-admin" class="btn btn-default" disabled="disabled">
                            <span class="glyphicon glyphicon-remove"></span>
                            <?php echo lang('delete'); ?>
                        </button>
                    </div>

                    <div class="save-cancel-group btn-group" style="display:none;">
                        <button id="save-admin" class="btn btn-primary">
                            <span class="glyphicon glyphicon-ok"></span>
                            <?php echo lang('save'); ?>
                        </button>
                        <button id="cancel-admin" class="btn btn-default">
                            <span class="glyphicon glyphicon-ban-circle"></span>
                            <?php echo lang('cancel'); ?>
                        </button>
                    </div>
                </div>

                <h3><?php echo lang('details'); ?></h3>

                <div class="form-message alert" style="display:none;"></div>

                <input type="hidden" id="admin-id" class="record-id" />

                <div class="row">
                    <div class="admin-details col-md-6">
                        <div class="form-group">
                            <label for="first-name"><?php echo lang('first_name'); ?> *</label>
                            <input type="text" id="admin-first-name" class="form-control required" maxlength="256" />
                        </div>

                        <div class="form-group">
                            <label for="admin-last-name"><?php echo lang('last_name'); ?> *</label>
                            <input type="text" id="admin-last-name" class="form-control required" maxlength="512" />
                        </div>

                        <div class="form-group">
                            <label for="admin-email"><?php echo lang('email'); ?> *</label>
                            <input type="text" id="admin-email" class="form-control required" maxlength="512" />
                        </div>

                        <div class="form-group">
                            <label for="admin-phone-number"><?php echo lang('phone_number'); ?> *</label>
                            <input type="text" id="admin-phone-number" class="form-control required" maxlength="128" />
                        </div>

                        <div class="form-group">
                            <label for="admin-mobile-number"><?php echo lang('mobile_number'); ?></label>
                            <input type="text" id="admin-mobile-number" class="form-control" maxlength="128" />
                        </div>

                        <div class="form-group">
                            <label for="admin-address"><?php echo lang('address'); ?></label>
                            <input type="text" id="admin-address" class="form-control" maxlength="256" />
                        </div>

                        <div class="form-group">
                            <label for="admin-city"><?php echo lang('city'); ?></label>
                            <input type="text" id="admin-city" class="form-control" maxlength="256" />
                        </div>

                        <div class="form-group">
                            <label for="admin-state"><?php echo lang('state'); ?></label>
                            <input type="text" id="admin-state" class="form-control" maxlength="128" />
                        </div>

                        <div class="form-group">
                            <label for="admin-zip-code"><?php echo lang('zip_code'); ?></label>
                            <input type="text" id="admin-zip-code" class="form-control" maxlength="64" />
                        </div>

                        <div class="form-group">
                            <label for="admin-notes"><?php echo lang('notes'); ?></label>
                            <textarea id="admin-notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="admin-settings col-md-6">
                        <div class="form-group">
                            <label for="admin-username"><?php echo lang('username'); ?> *</label>
                            <input type="text" id="admin-username" class="form-control required" maxlength="256" />
                        </div>

                        <div class="form-group">
                            <label for="admin-password"><?php echo lang('password'); ?> *</label>
                            <input type="password" id="admin-password" class="form-control required" maxlength="512" />
                        </div>

                        <div class="form-group">
                            <label for="admin-password-confirm"><?php echo lang('retype_password'); ?> *</label>
                            <input type="password" id="admin-password-confirm" class="form-control required" maxlength="512" />
                        </div>

                        <div class="form-group">
                            <label for="admin-calendar-view"><?php echo lang('calendar'); ?> *</label>
                            <select id="admin-calendar-view" class="form-control required">
                                <option value="default">Default</option>
                                <option value="table">Table</option>
                            </select>
                        </div>

                        <br>

                        <button type="button" id="admin-notifications" class="btn btn-default" data-toggle="button">
                            <span class="glyphicon glyphicon-envelope"></span>
                            <span><?php echo lang('receive_notifications'); ?></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PROVIDERS TAB -->

    <div id="providers" class="tab-content" style="display:none;">
        <div class="row">
            <div id="filter-providers" class="filter-records column col-xs-12 col-sm-5">
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

                <h3><?php echo lang('providers'); ?></h3>
                <div class="results"></div>
            </div>

            <div class="record-details column col-xs-12 col-sm-7">
                <div class="pull-left">
                    <div class="add-edit-delete-group btn-group">
                        <button id="add-provider" class="btn btn-primary">
                            <span class="glyphicon glyphicon-plus"></span>
                            <?php echo lang('add'); ?>
                        </button>
                        <button id="edit-provider" class="btn btn-default" disabled="disabled">
                            <span class="glyphicon glyphicon-pencil"></span>
                            <?php echo lang('edit'); ?>
                        </button>
                        <button id="delete-provider" class="btn btn-default" disabled="disabled">
                            <span class="glyphicon glyphicon-remove"></span>
                            <?php echo lang('delete'); ?>
                        </button>
                    </div>

                    <div class="save-cancel-group btn-group" style="display:none;">
                        <button id="save-provider" class="btn btn-primary">
                            <span class="glyphicon glyphicon-ok"></span>
                            <?php echo lang('save'); ?>
                        </button>
                        <button id="cancel-provider" class="btn btn-default">
                            <span class="glyphicon glyphicon-ban-circle"></span>
                            <?php echo lang('cancel'); ?>
                        </button>
                    </div>
                </div>

                <div class="switch-view pull-right">
                    <strong><?php echo lang('current_view'); ?></strong>
                    <div class="display-details current"><?php echo lang('details'); ?></div>
                    <div class="display-working-plan"><?php echo lang('working_plan'); ?></div>
                </div>

                <?php // This form message is outside the details view, so that it can be
                      // visible when the user has working plan view active. ?>
                <div class="form-message alert" style="display:none;"></div>

                <div class="details-view provider-view">
                    <h3><?php echo lang('details'); ?></h3>

                    <input type="hidden" id="provider-id" class="record-id" />

                    <div class="row">
                        <div class="provider-details col-md-6">
                            <div class="form-group">
                                <label for="provider-first-name"><?php echo lang('first_name'); ?> *</label>
                                <input type="text" id="provider-first-name" class="form-control required" maxlength="256" />
                            </div>

                            <div class="form-group">
                                <label for="provider-last-name"><?php echo lang('last_name'); ?> *</label>
                                <input type="text" id="provider-last-name" class="form-control required" maxlength="512" />
                            </div>

                            <div class="form-group">
                                <label for="provider-email"><?php echo lang('email'); ?> *</label>
                                <input type="text" id="provider-email" class="form-control required" max="512" />
                            </div>

                            <div class="form-group">
                                <label for="provider-phone-number"><?php echo lang('phone_number'); ?> *</label>
                                <input type="text" id="provider-phone-number" class="form-control required" max="128" />
                            </div>

                            <div class="form-group">
                                <label for="provider-mobile-number"><?php echo lang('mobile_number'); ?></label>
                                <input type="text" id="provider-mobile-number" class="form-control" maxlength="128" />
                            </div>

                            <div class="form-group">
                                <label for="provider-address"><?php echo lang('address'); ?></label>
                                <input type="text" id="provider-address" class="form-control" maxlength="256" />
                            </div>

                            <div class="form-group">
                                <label for="provider-city"><?php echo lang('city'); ?></label>
                                <input type="text" id="provider-city" class="form-control" maxlength="256" />
                            </div>

                            <div class="form-group">
                                <label for="provider-state"><?php echo lang('state'); ?></label>
                                <input type="text" id="provider-state" class="form-control" maxlength="256" />
                            </div>

                            <div class="form-group">
                                <label for="provider-zip-code"><?php echo lang('zip_code'); ?></label>
                                <input type="text" id="provider-zip-code" class="form-control" maxlength="64" />
                            </div>

                            <div class="form-group">
                                <label for="provider-notes"><?php echo lang('notes'); ?></label>
                                <textarea id="provider-notes" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="provider-settings col-md-6">
                            <div class="form-group">
                                <label for="provider-username"><?php echo lang('username'); ?> *</label>
                                <input type="text" id="provider-username" class="form-control required" maxlength="256" />
                            </div>

                            <div class="form-group">
                                <label for="provider-password"><?php echo lang('password'); ?> *</label>
                                <input type="password" id="provider-password" class="form-control required" maxlength="512"/>
                            </div>

                            <div class="form-group">
                                <label for="provider-password-confirm"><?php echo lang('retype_password'); ?> *</label>
                                <input type="password" id="provider-password-confirm" class="form-control required" maxlength="512" />
                            </div>

                            <div class="form-group">
                                <label for="provider-calendar-view"><?php echo lang('calendar'); ?> *</label>
                                <select id="provider-calendar-view" class="form-control required">
                                    <option value="default">Default</option>
                                    <option value="table">Table</option>
                                </select>
                            </div>

                            <br>

                            <button type="button" id="provider-notifications" class="btn btn-default" data-toggle="button">
                                <span class="glyphicon glyphicon-envelope"></span>
                                <span><?php echo lang('receive_notifications'); ?></span>
                            </button>

                            <br><br>

                            <h4><?php echo lang('services'); ?></h4>
                            <div id="provider-services"></div>
                        </div>
                    </div>
                </div>

                <div class="working-plan-view provider-view" style="display: none;">
                    <h3><?php echo lang('working_plan'); ?></h3>
                    <button id="reset-working-plan" class="btn btn-primary"
                            title="Reset the working plan back to the default values.">
                        <span class="glyphicon glyphicon-repeat"></span>
                        <?php echo lang('reset_plan'); ?></button>
                    <table class="working-plan table table-striped">
                        <thead>
                            <tr>
                                <th><?php echo lang('day'); ?></th>
                                <th><?php echo lang('start'); ?></th>
                                <th><?php echo lang('end'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="checkbox">
                                        <label><input type="checkbox" id="monday" />
                                            <?php echo lang('monday'); ?></label>
                                    </div>
                                </td>
                                <td><input type="text" id="monday-start" class="work-start" /></td>
                                <td><input type="text" id="monday-end" class="work-end" /></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="checkbox">
                                        <label><input type="checkbox" id="tuesday" />
                                            <?php echo lang('tuesday'); ?></label>
                                    </div>
                                </td>
                                <td><input type="text" id="tuesday-start" class="work-start" /></td>
                                <td><input type="text" id="tuesday-end" class="work-end" /></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="checkbox">
                                        <label><input type="checkbox" id="wednesday" />
                                            <?php echo lang('wednesday'); ?></label>
                                    </div>
                                </td>
                                <td><input type="text" id="wednesday-start" class="work-start" /></td>
                                <td><input type="text" id="wednesday-end" class="work-end" /></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="checkbox">
                                        <label><input type="checkbox" id="thursday" />
                                            <?php echo lang('thursday'); ?></label>
                                    </div>
                                </td>
                                <td><input type="text" id="thursday-start" class="work-start" /></td>
                                <td><input type="text" id="thursday-end" class="work-end" /></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="checkbox">
                                        <label><input type="checkbox" id="friday" />
                                            <?php echo lang('friday'); ?></label>
                                    </div>
                                </td>
                                <td><input type="text" id="friday-start" class="work-start" /></td>
                                <td><input type="text" id="friday-end" class="work-end" /></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="checkbox">
                                        <label><input type="checkbox" id="saturday" />
                                            <?php echo lang('saturday'); ?></label>
                                    </div>
                                </td>
                                <td><input type="text" id="saturday-start" class="work-start" /></td>
                                <td><input type="text" id="saturday-end" class="work-end" /></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="checkbox">
                                        <label><input type="checkbox" id="sunday" />
                                            <?php echo lang('sunday'); ?></label>
                                    </div>
                                </td>
                                <td><input type="text" id="sunday-start" class="work-start" /></td>
                                <td><input type="text" id="sunday-end" class="work-end" /></td>
                            </tr>
                        </tbody>
                    </table>

                    <br>

                    <h3><?php echo lang('breaks');?></h3>

                    <span class="help-block">
                        <?php echo lang('add_breaks_during_each_day');?>
                    </span>

                    <div>
                        <button type="button" class="add-break btn btn-primary">
                            <span class="glyphicon glyphicon-plus"></span>
                            <?php echo lang('add_break');?>
                        </button>
                    </div>

                    <br>

                    <table class="breaks table table-striped">
                        <thead>
                            <tr>
                                <th><?php echo lang('day');?></th>
                                <th><?php echo lang('start');?></th>
                                <th><?php echo lang('end');?></th>
                                <th><?php echo lang('actions');?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- SECRETARIES TAB -->

    <div id="secretaries" class="tab-content" style="display:none;">
        <div class="row">
            <div id="filter-secretaries" class="filter-records column col-xs-12 col-sm-5">
                <form class="input-append">
                    <input class="key" type="text" />
                    <div class="btn-group">
                        <button class="filter btn btn-default btn-sm" type="submit" title="<?php echo lang('filter');?>">
                            <span class="glyphicon glyphicon-search"></span>
                        </button>
                        <button class="clear btn btn-default btn-sm" type="button" title="<?php echo lang('clear');?>">
                            <span class="glyphicon glyphicon-repeat"></span>
                        </button>
                    </div>
                </form>

                <h3><?php echo lang('secretaries');?></h3>
                <div class="results"></div>
            </div>

            <div class="record-details column col-xs-12 col-sm-7">
                <div class="btn-toolbar">
                    <div class="add-edit-delete-group btn-group">
                        <button id="add-secretary" class="btn btn-primary">
                            <span class="glyphicon glyphicon-plus"></span>
                            <?php echo lang('add');?>
                        </button>
                        <button id="edit-secretary" class="btn btn-default" disabled="disabled">
                            <span class="glyphicon glyphicon-pencil"></span>
                            <?php echo lang('edit');?>
                        </button>
                        <button id="delete-secretary" class="btn btn-default" disabled="disabled">
                            <span class="glyphicon glyphicon-remove"></span>
                            <?php echo lang('delete');?>
                        </button>
                    </div>

                    <div class="save-cancel-group btn-group" style="display:none;">
                        <button id="save-secretary" class="btn btn-primary">
                            <span class="glyphicon glyphicon-ok"></span>
                            <?php echo lang('save');?>
                        </button>
                        <button id="cancel-secretary" class="btn btn-default">
                            <span class="glyphicon glyphicon-ban-circle"></span>
                            <?php echo lang('cancel');?>
                        </button>
                    </div>
                </div>

                <h3><?php echo lang('details');?></h3>

                <div class="form-message alert" style="display:none;"></div>

                <input type="hidden" id="secretary-id" class="record-id" />

                <div class="row">
                    <div class="secretary-details col-md-6">
                        <div class="form-group">
                            <label for="secretary-first-name"><?php echo lang('first_name');?> *</label>
                            <input type="text" id="secretary-first-name" class="form-control required" maxlength="256" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-last-name"><?php echo lang('last_name');?> *</label>
                            <input type="text" id="secretary-last-name" class="form-control required" maxlength="512" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-email"><?php echo lang('email');?> *</label>
                            <input type="text" id="secretary-email" class="form-control required" maxlength="512" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-phone-number"><?php echo lang('phone_number');?> *</label>
                            <input type="text" id="secretary-phone-number" class="form-control required" maxlength="128" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-mobile-number"><?php echo lang('mobile_number');?></label>
                            <input type="text" id="secretary-mobile-number" class="form-control" maxlength="128" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-address"><?php echo lang('address');?></label>
                            <input type="text" id="secretary-address" class="form-control" maxlength="256" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-city"><?php echo lang('city');?></label>
                            <input type="text" id="secretary-city" class="form-control" maxlength="256" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-state"><?php echo lang('state');?></label>
                            <input type="text" id="secretary-state" class="form-control" maxlength="128" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-zip-code"><?php echo lang('zip_code');?></label>
                            <input type="text" id="secretary-zip-code" class="form-control" maxlength="64" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-notes"><?php echo lang('notes');?></label>
                            <textarea id="secretary-notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="secretary-settings col-md-6">
                        <div class="form-group">
                            <label for="secretary-username"><?php echo lang('username');?> *</label>
                            <input type="text" id="secretary-username" class="form-control required" maxlength="256" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-password"><?php echo lang('password');?> *</label>
                            <input type="password" id="secretary-password" class="form-control required" maxlength="512" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-password-confirm"><?php echo lang('retype_password');?> *</label>
                            <input type="password" id="secretary-password-confirm" class="form-control required" maxlength="512" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-calendar-view"><?php echo lang('calendar'); ?> *</label>
                            <select id="secretary-calendar-view" class="form-control required">
                                <option value="default">Default</option>
                                <option value="table">Table</option>
                            </select>
                        </div>

                        <br>

                        <button type="button" id="secretary-notifications" class="btn btn-default" data-toggle="button">
                            <span class="glyphicon glyphicon-envelope"></span>
                            <span><?php echo lang('receive_notifications');?></span>
                        </button>

                        <br><br>

                        <h4><?php echo lang('providers');?></h4>
                        <div id="secretary-providers"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
