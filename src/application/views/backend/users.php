<script type="text/javascript"
        src="<?php echo $base_url; ?>/assets/js/backend_users_admins.js"></script>
<script type="text/javascript"
        src="<?php echo $base_url; ?>/assets/js/backend_users_providers.js"></script>
<script type="text/javascript"
        src="<?php echo $base_url; ?>/assets/js/backend_users_secretaries.js"></script>
<script type="text/javascript"
        src="<?php echo $base_url; ?>/assets/js/backend_users.js"></script>
<script type="text/javascript"
        src="<?php echo $base_url; ?>/assets/js/working_plan.js"></script>
<script type="text/javascript"
        src="<?php echo $base_url; ?>/assets/ext/jquery-ui/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript"
        src="<?php echo $base_url; ?>/assets/ext/jquery-jeditable/jquery.jeditable.min.js"></script>

<script type="text/javascript">
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

    <?php
        // ---------------------------------------------------------------------
        //
        // Page Navigation
        //
        // ---------------------------------------------------------------------
    ?>
    <ul class="nav nav-tabs">
        <li role="presentation" class="admins-tab tab active"><a><?php echo $this->lang->line('admins'); ?></a></li>
        <li role="presentation" class="providers-tab tab"><a><?php echo $this->lang->line('providers'); ?></a></li>
        <li role="presentation" class="secretaries-tab tab"><a><?php echo $this->lang->line('secretaries'); ?></a></li>
    </ul>

    <?php
        // ---------------------------------------------------------------------
        //
        // Admins Tab
        //
        // ---------------------------------------------------------------------
    ?>
    <div id="admins" class="tab-content">
        <div class="row">
            <div id="filter-admins" class="filter-records column col-xs-12 col-sm-5">
                <form class="input-append">
                    <input class="key" type="text" />
                    <div class="btn-group">
                        <button class="filter btn btn-default btn-sm" type="submit" title="<?php echo $this->lang->line('filter'); ?>">
                            <span class="glyphicon glyphicon-search"></span>
                        </button>
                        <button class="clear btn btn-default btn-sm" type="button" title="<?php echo $this->lang->line('clear'); ?>">
                            <span class="glyphicon glyphicon-repeat"></span>
                        </button>
                    </div>
                </form>

                <h3><?php echo $this->lang->line('admins'); ?></h3>
                <div class="results"></div>
            </div>

            <div class="record-details column col-xs-12 col-sm-7">
                <div class="btn-toolbar">
                    <div class="add-edit-delete-group btn-group">
                        <button id="add-admin" class="btn btn-primary">
                            <span class="glyphicon glyphicon-plus"></span>
                            <?php echo $this->lang->line('add'); ?>
                        </button>
                        <button id="edit-admin" class="btn btn-default" disabled="disabled">
                            <span class="glyphicon glyphicon-pencil"></span>
                            <?php echo $this->lang->line('edit'); ?>
                        </button>
                        <button id="delete-admin" class="btn btn-default" disabled="disabled">
                            <span class="glyphicon glyphicon-remove"></span>
                            <?php echo $this->lang->line('delete'); ?>
                        </button>
                    </div>

                    <div class="save-cancel-group btn-group" style="display:none;">
                        <button id="save-admin" class="btn btn-primary">
                            <span class="glyphicon glyphicon-ok"></span>
                            <?php echo $this->lang->line('save'); ?>
                        </button>
                        <button id="cancel-admin" class="btn btn-default">
                            <span class="glyphicon glyphicon-ban-circle"></span>
                            <?php echo $this->lang->line('cancel'); ?>
                        </button>
                    </div>
                </div>

                <h3><?php echo $this->lang->line('details'); ?></h3>

                <div class="form-message alert" style="display:none;"></div>

                <input type="hidden" id="admin-id" class="record-id" />

                <div class="row">
                    <div class="admin-details col-md-6">
                        <div class="form-group">
                            <label for="first-name"><?php echo $this->lang->line('first_name'); ?> *</label>
                            <input type="text" id="admin-first-name" class="form-control required" />
                        </div>

                        <div class="form-group">
                            <label for="admin-last-name"><?php echo $this->lang->line('last_name'); ?> *</label>
                            <input type="text" id="admin-last-name" class="form-control required" />
                        </div>

                        <div class="form-group">
                            <label for="admin-email"><?php echo $this->lang->line('email'); ?> *</label>
                            <input type="text" id="admin-email" class="form-control required" />
                        </div>

                        <div class="form-group">
                            <label for="admin-phone-number"><?php echo $this->lang->line('phone_number'); ?> *</label>
                            <input type="text" id="admin-phone-number" class="form-control required" />
                        </div>

                        <div class="form-group">
                            <label for="admin-mobile-number"><?php echo $this->lang->line('mobile_number'); ?></label>
                            <input type="text" id="admin-mobile-number" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="admin-address"><?php echo $this->lang->line('address'); ?></label>
                            <input type="text" id="admin-address" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="admin-city"><?php echo $this->lang->line('city'); ?></label>
                            <input type="text" id="admin-city" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="admin-state"><?php echo $this->lang->line('state'); ?></label>
                            <input type="text" id="admin-state" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="admin-zip-code"><?php echo $this->lang->line('zip_code'); ?></label>
                            <input type="text" id="admin-zip-code" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="admin-notes"><?php echo $this->lang->line('notes'); ?></label>
                            <textarea id="admin-notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="admin-settings col-md-6">
                        <div class="form-group">
                            <label for="admin-username"><?php echo $this->lang->line('username'); ?> *</label>
                            <input type="text" id="admin-username" class="form-control required" />
                        </div>

                        <div class="form-group">
                            <label for="admin-password"><?php echo $this->lang->line('password'); ?> *</label>
                            <input type="password" id="admin-password" class="form-control required"/>
                        </div>

                        <div class="form-group">
                            <label for="admin-password-confirm"><?php echo $this->lang->line('retype_password'); ?> *</label>
                            <input type="password" id="admin-password-confirm" class="form-control required" />
                        </div>

                        <div class="form-group">
                            <label for="admin-calendar-view"><?php echo $this->lang->line('calendar'); ?> *</label>
                            <select id="admin-calendar-view" class="form-control required">
                                <option value="default">Default</option>
                                <option value="table">Table</option>
                            </select>
                        </div>

                        <br>

                        <button type="button" id="admin-notifications" class="btn btn-default" data-toggle="button">
                            <span class="glyphicon glyphicon-envelope"></span>
                            <span><?php echo $this->lang->line('receive_notifications'); ?></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
        // ---------------------------------------------------------------------
        //
        // Providers Tab
        //
        // ---------------------------------------------------------------------
    ?>
    <div id="providers" class="tab-content" style="display:none;">
        <div class="row">
            <div id="filter-providers" class="filter-records column col-xs-12 col-sm-5">
                <form class="input-append">
                    <input class="key" type="text" />
                    <div class="btn-group">
                        <button class="filter btn btn-default btn-sm" type="submit" title="<?php echo $this->lang->line('filter'); ?>">
                            <span class="glyphicon glyphicon-search"></span>
                        </button>
                        <button class="clear btn btn-default btn-sm" type="button" title="<?php echo $this->lang->line('clear'); ?>">
                            <span class="glyphicon glyphicon-repeat"></span>
                        </button>
                    </div>
                </form>

                <h3><?php echo $this->lang->line('providers'); ?></h3>
                <div class="results"></div>
            </div>

            <div class="record-details column col-xs-12 col-sm-7">
                <div class="pull-left">
                    <div class="add-edit-delete-group btn-group">
                        <button id="add-provider" class="btn btn-primary">
                            <span class="glyphicon glyphicon-plus"></span>
                            <?php echo $this->lang->line('add'); ?>
                        </button>
                        <button id="edit-provider" class="btn btn-default" disabled="disabled">
                            <span class="glyphicon glyphicon-pencil"></span>
                            <?php echo $this->lang->line('edit'); ?>
                        </button>
                        <button id="delete-provider" class="btn btn-default" disabled="disabled">
                            <span class="glyphicon glyphicon-remove"></span>
                            <?php echo $this->lang->line('delete'); ?>
                        </button>
                    </div>

                    <div class="save-cancel-group btn-group" style="display:none;">
                        <button id="save-provider" class="btn btn-primary">
                            <span class="glyphicon glyphicon-ok"></span>
                            <?php echo $this->lang->line('save'); ?>
                        </button>
                        <button id="cancel-provider" class="btn btn-default">
                            <span class="glyphicon glyphicon-ban-circle"></span>
                            <?php echo $this->lang->line('cancel'); ?>
                        </button>
                    </div>
                </div>

                <div class="switch-view pull-right">
                    <strong><?php echo $this->lang->line('current_view'); ?></strong>
                    <div class="display-details current"><?php echo $this->lang->line('details'); ?></div>
                    <div class="display-working-plan"><?php echo $this->lang->line('working_plan'); ?></div>
                </div>

                <?php // This form message is outside the details view, so that it can be
                      // visible when the user has working plan view active. ?>
                <div class="form-message alert" style="display:none;"></div>

                <div class="details-view provider-view">
                    <h3><?php echo $this->lang->line('details'); ?></h3>

                    <input type="hidden" id="provider-id" class="record-id" />

                    <div class="row">
                        <div class="provider-details col-md-6">
                            <div class="form-group">
                                <label for="provider-first-name"><?php echo $this->lang->line('first_name'); ?> *</label>
                                <input type="text" id="provider-first-name" class="form-control required" />
                            </div>

                            <div class="form-group">
                                <label for="provider-last-name"><?php echo $this->lang->line('last_name'); ?> *</label>
                                <input type="text" id="provider-last-name" class="form-control required" />
                            </div>

                            <div class="form-group">
                                <label for="provider-email"><?php echo $this->lang->line('email'); ?> *</label>
                                <input type="text" id="provider-email" class="form-control required" />
                            </div>

                            <div class="form-group">
                                <label for="provider-phone-number"><?php echo $this->lang->line('phone_number'); ?> *</label>
                                <input type="text" id="provider-phone-number" class="form-control required" />
                            </div>

                            <div class="form-group">
                                <label for="provider-mobile-number"><?php echo $this->lang->line('mobile_number'); ?></label>
                                <input type="text" id="provider-mobile-number" class="form-control" />
                            </div>

                            <div class="form-group">
                                <label for="provider-address"><?php echo $this->lang->line('address'); ?></label>
                                <input type="text" id="provider-address" class="form-control" />
                            </div>

                            <div class="form-group">
                                <label for="provider-city"><?php echo $this->lang->line('city'); ?></label>
                                <input type="text" id="provider-city" class="form-control" />
                            </div>

                            <div class="form-group">
                                <label for="provider-state"><?php echo $this->lang->line('state'); ?></label>
                                <input type="text" id="provider-state" class="form-control" />
                            </div>

                            <div class="form-group">
                                <label for="provider-zip-code"><?php echo $this->lang->line('zip_code'); ?></label>
                                <input type="text" id="provider-zip-code" class="form-control" />
                            </div>

                            <div class="form-group">
                                <label for="provider-notes"><?php echo $this->lang->line('notes'); ?></label>
                                <textarea id="provider-notes" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="provider-settings col-md-6">
                            <div class="form-group">
                                <label for="provider-username"><?php echo $this->lang->line('username'); ?> *</label>
                                <input type="text" id="provider-username" class="form-control required" />
                            </div>

                            <div class="form-group">
                                <label for="provider-password"><?php echo $this->lang->line('password'); ?> *</label>
                                <input type="password" id="provider-password" class="form-control required"/>
                            </div>

                            <div class="form-group">
                                <label for="provider-password-confirm"><?php echo $this->lang->line('retype_password'); ?> *</label>
                                <input type="password" id="provider-password-confirm" class="form-control required" />
                            </div>

                            <div class="form-group">
                                <label for="provider-calendar-view"><?php echo $this->lang->line('calendar'); ?> *</label>
                                <select id="provider-calendar-view" class="form-control required">
                                    <option value="default">Default</option>
                                    <option value="table">Table</option>
                                </select>
                            </div>

                            <br>

                            <button type="button" id="provider-notifications" class="btn btn-default" data-toggle="button">
                                <span class="glyphicon glyphicon-envelope"></span>
                                <span><?php echo $this->lang->line('receive_notifications'); ?></span>
                            </button>

                            <br><br>

                            <h4><?php echo $this->lang->line('services'); ?></h4>
                            <div id="provider-services"></div>
                        </div>
                    </div>
                </div>

                <div class="working-plan-view provider-view" style="display: none;">
                    <h3><?php echo $this->lang->line('working_plan'); ?></h3>
                    <button id="reset-working-plan" class="btn btn-primary"
                            title="Reset the working plan back to the default values.">
                        <span class="glyphicon glyphicon-repeat"></span>
                        <?php echo $this->lang->line('reset_plan'); ?></button>
                    <table class="working-plan table table-striped">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('day'); ?></th>
                                <th><?php echo $this->lang->line('start'); ?></th>
                                <th><?php echo $this->lang->line('end'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="checkbox">
                                        <label><input type="checkbox" id="monday" />
                                            <?php echo $this->lang->line('monday'); ?></label>
                                    </div>
                                </td>
                                <td><input type="text" id="monday-start" class="work-start" /></td>
                                <td><input type="text" id="monday-end" class="work-end" /></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="checkbox">
                                        <label><input type="checkbox" id="tuesday" />
                                            <?php echo $this->lang->line('tuesday'); ?></label>
                                    </div>
                                </td>
                                <td><input type="text" id="tuesday-start" class="work-start" /></td>
                                <td><input type="text" id="tuesday-end" class="work-end" /></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="checkbox">
                                        <label><input type="checkbox" id="wednesday" />
                                            <?php echo $this->lang->line('wednesday'); ?></label>
                                    </div>
                                </td>
                                <td><input type="text" id="wednesday-start" class="work-start" /></td>
                                <td><input type="text" id="wednesday-end" class="work-end" /></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="checkbox">
                                        <label><input type="checkbox" id="thursday" />
                                            <?php echo $this->lang->line('thursday'); ?></label>
                                    </div>
                                </td>
                                <td><input type="text" id="thursday-start" class="work-start" /></td>
                                <td><input type="text" id="thursday-end" class="work-end" /></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="checkbox">
                                        <label><input type="checkbox" id="friday" />
                                            <?php echo $this->lang->line('friday'); ?></label>
                                    </div>
                                </td>
                                <td><input type="text" id="friday-start" class="work-start" /></td>
                                <td><input type="text" id="friday-end" class="work-end" /></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="checkbox">
                                        <label><input type="checkbox" id="saturday" />
                                            <?php echo $this->lang->line('saturday'); ?></label>
                                    </div>
                                </td>
                                <td><input type="text" id="saturday-start" class="work-start" /></td>
                                <td><input type="text" id="saturday-end" class="work-end" /></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="checkbox">
                                        <label><input type="checkbox" id="sunday" />
                                            <?php echo $this->lang->line('sunday'); ?></label>
                                    </div>
                                </td>
                                <td><input type="text" id="sunday-start" class="work-start" /></td>
                                <td><input type="text" id="sunday-end" class="work-end" /></td>
                            </tr>
                        </tbody>
                    </table>

                    <br>

                    <h3><?php echo $this->lang->line('breaks');?></h3>

                    <span class="help-block">
                        <?php echo $this->lang->line('add_breaks_during_each_day');?>
                    </span>

                    <div>
                        <button type="button" class="add-break btn btn-primary">
                            <span class="glyphicon glyphicon-plus"></span>
                            <?php echo $this->lang->line('add_break');?>
                        </button>
                    </div>

                    <br>

                    <table class="breaks table table-striped">
                        <thead>
                            <tr>
                                <th><?php echo $this->lang->line('day');?></th>
                                <th><?php echo $this->lang->line('start');?></th>
                                <th><?php echo $this->lang->line('end');?></th>
                                <th><?php echo $this->lang->line('actions');?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php
        // ---------------------------------------------------------------------
        //
        // Secretaries Tab
        //
        // ---------------------------------------------------------------------
    ?>
    <div id="secretaries" class="tab-content" style="display:none;">
        <div class="row">
            <div id="filter-secretaries" class="filter-records column col-xs-12 col-sm-5">
                <form class="input-append">
                    <input class="key" type="text" />
                    <div class="btn-group">
                        <button class="filter btn btn-default btn-sm" type="submit" title="<?php echo $this->lang->line('filter');?>">
                            <span class="glyphicon glyphicon-search"></span>
                        </button>
                        <button class="clear btn btn-default btn-sm" type="button" title="<?php echo $this->lang->line('clear');?>">
                            <span class="glyphicon glyphicon-repeat"></span>
                        </button>
                    </div>
                </form>

                <h3><?php echo $this->lang->line('secretaries');?></h3>
                <div class="results"></div>
            </div>

            <div class="record-details column col-xs-12 col-sm-7">
                <div class="btn-toolbar">
                    <div class="add-edit-delete-group btn-group">
                        <button id="add-secretary" class="btn btn-primary">
                            <span class="glyphicon glyphicon-plus"></span>
                            <?php echo $this->lang->line('add');?>
                        </button>
                        <button id="edit-secretary" class="btn btn-default" disabled="disabled">
                            <span class="glyphicon glyphicon-pencil"></span>
                            <?php echo $this->lang->line('edit');?>
                        </button>
                        <button id="delete-secretary" class="btn btn-default" disabled="disabled">
                            <span class="glyphicon glyphicon-remove"></span>
                            <?php echo $this->lang->line('delete');?>
                        </button>
                    </div>

                    <div class="save-cancel-group btn-group" style="display:none;">
                        <button id="save-secretary" class="btn btn-primary">
                            <span class="glyphicon glyphicon-ok"></span>
                            <?php echo $this->lang->line('save');?>
                        </button>
                        <button id="cancel-secretary" class="btn btn-default">
                            <span class="glyphicon glyphicon-ban-circle"></span>
                            <?php echo $this->lang->line('cancel');?>
                        </button>
                    </div>
                </div>

                <h3><?php echo $this->lang->line('details');?></h3>

                <div class="form-message alert" style="display:none;"></div>

                <input type="hidden" id="secretary-id" class="record-id" />

                <div class="row">
                    <div class="secretary-details col-md-6">
                        <div class="form-group">
                            <label for="secretary-first-name"><?php echo $this->lang->line('first_name');?> *</label>
                            <input type="text" id="secretary-first-name" class="form-control required" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-last-name"><?php echo $this->lang->line('last_name');?> *</label>
                            <input type="text" id="secretary-last-name" class="form-control required" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-email"><?php echo $this->lang->line('email');?> *</label>
                            <input type="text" id="secretary-email" class="form-control required" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-phone-number"><?php echo $this->lang->line('phone_number');?> *</label>
                            <input type="text" id="secretary-phone-number" class="form-control required" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-mobile-number"><?php echo $this->lang->line('mobile_number');?></label>
                            <input type="text" id="secretary-mobile-number" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-address"><?php echo $this->lang->line('address');?></label>
                            <input type="text" id="secretary-address" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-city"><?php echo $this->lang->line('city');?></label>
                            <input type="text" id="secretary-city" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-state"><?php echo $this->lang->line('state');?></label>
                            <input type="text" id="secretary-state" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-zip-code"><?php echo $this->lang->line('zip_code');?></label>
                            <input type="text" id="secretary-zip-code" class="form-control" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-notes"><?php echo $this->lang->line('notes');?></label>
                            <textarea id="secretary-notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="secretary-settings col-md-6">
                        <div class="form-group">
                            <label for="secretary-username"><?php echo $this->lang->line('username');?> *</label>
                            <input type="text" id="secretary-username" class="form-control required" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-password"><?php echo $this->lang->line('password');?> *</label>
                            <input type="password" id="secretary-password" class="form-control required"/>
                        </div>

                        <div class="form-group">
                            <label for="secretary-password-confirm"><?php echo $this->lang->line('retype_password');?> *</label>
                            <input type="password" id="secretary-password-confirm" class="form-control required" />
                        </div>

                        <div class="form-group">
                            <label for="secretary-calendar-view"><?php echo $this->lang->line('calendar'); ?> *</label>
                            <select id="secretary-calendar-view" class="form-control required">
                                <option value="default">Default</option>
                                <option value="table">Table</option>
                            </select>
                        </div>

                        <br>

                        <button type="button" id="secretary-notifications" class="btn btn-default" data-toggle="button">
                            <span class="glyphicon glyphicon-envelope"></span>
                            <span><?php echo $this->lang->line('receive_notifications');?></span>
                        </button>

                        <br><br>

                        <h4><?php echo $this->lang->line('providers');?></h4>
                        <div id="secretary-providers"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
