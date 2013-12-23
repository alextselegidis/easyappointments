<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/backend_users.js"></script>
        
<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/backend_users_admins.js"></script>
<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/backend_users_providers.js"></script>
<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/backend_users_secretaries.js"></script>

<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/working_plan.js"></script>
        
<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/libs/jquery/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/libs/jquery/jquery.jeditable.min.js"></script>
        
<script type="text/javascript">    
    var GlobalVariables = {
        'baseUrl': <?php echo '"' . $base_url . '"'; ?>,
        'admins': <?php echo json_encode($admins); ?>,
        'providers': <?php echo json_encode($providers); ?>,
        'secretaries': <?php echo json_encode($secretaries); ?>,
        'services': <?php echo json_encode($services); ?>,
        'workingPlan': $.parseJSON(<?php echo json_encode($working_plan); ?>),
        'user'                  : {
            'id'        : <?php echo $user_id; ?>,
            'email'     : <?php echo '"' . $user_email . '"'; ?>,
            'role_slug' : <?php echo '"' . $role_slug . '"'; ?>,
            'privileges': <?php echo json_encode($privileges); ?>
        }
    };
    
    $(document).ready(function() {
        BackendUsers.initialize(true);
    });
</script>

<div id="users-page" class="row-fluid">
    
    <?php 
        // ---------------------------------------------------------------------
        //
        // Page Navigation
        //
        // ---------------------------------------------------------------------
    ?>
    <ul class="nav nav-tabs">
        <li class="admins-tab tab active"><a><?php echo $this->lang->line('admins'); ?></a></li>
        <li class="providers-tab tab"><a><?php echo $this->lang->line('providers'); ?></a></li>
        <li class="secretaries-tab tab"><a><?php echo $this->lang->line('secretaries'); ?></a></li>
    </ul>
    
    <?php 
        // ---------------------------------------------------------------------
        //
        // Admins Tab 
        //
        // ---------------------------------------------------------------------
    ?>
    <div id="admins" class="tab-content">
        <div id="filter-admins" class="filter-records column span4">
            <form class="input-append">
                <input class="key span12" type="text" />
                <button class="filter btn" type="submit" title="<?php echo $this->lang->line('filter'); ?>">
                    <i class="icon-search"></i>
                </button>
                <button class="clear btn" type="button" title="<?php echo $this->lang->line('clear'); ?>">
                    <i class="icon-repeat"></i>
                </button>
            </form>
            
            <h2><?php echo $this->lang->line('admins'); ?></h2>
            <div class="results"></div>
        </div>
        
        <div class="details column span7">
            <div class="btn-toolbar">
                <div class="add-edit-delete-group btn-group">
                    <button id="add-admin" class="btn btn-primary">
                        <i class="icon-plus icon-white"></i>
                        <?php echo $this->lang->line('add'); ?>
                    </button>
                    <button id="edit-admin" class="btn" disabled="disabled">
                        <i class="icon-pencil"></i>
                        <?php echo $this->lang->line('edit'); ?>
                    </button>
                    <button id="delete-admin" class="btn" disabled="disabled">
                        <i class="icon-remove"></i>
                        <?php echo $this->lang->line('delete'); ?>
                    </button>
                </div>

                <div class="save-cancel-group btn-group" style="display:none;">
                    <button id="save-admin" class="btn btn-primary">
                        <i class="icon-ok icon-white"></i>
                        <?php echo $this->lang->line('save'); ?>
                    </button>
                    <button id="cancel-admin" class="btn">
                        <i class="icon-ban-circle"></i>
                        <?php echo $this->lang->line('cancel'); ?>
                    </button>
                </div>
            </div>
            
            <h2><?php echo $this->lang->line('details'); ?></h2>
            
            <div class="form-message alert" style="display:none;"></div>
            
            <input type="hidden" id="admin-id" class="record-id" />
            
            <div class="row-fluid">
                <div class="admin-details span6">
                    <label for="first-name"><?php echo $this->lang->line('first_name'); ?> *</label>
                    <input type="text" id="admin-first-name" class="span11 required" />

                    <label for="admin-last-name"><?php echo $this->lang->line('last_name'); ?> *</label>
                    <input type="text" id="admin-last-name" class="span11 required" />

                    <label for="admin-email"><?php echo $this->lang->line('email'); ?> *</label>
                    <input type="text" id="admin-email" class="span11 required" />

                    <label for="admin-mobile-number"><?php echo $this->lang->line('mobile_number'); ?></label>
                    <input type="text" id="admin-mobile-number" class="span11" />

                    <label for="admin-phone-number"><?php echo $this->lang->line('phone_number'); ?> *</label>
                    <input type="text" id="admin-phone-number" class="span11 required" />

                    <label for="admin-address"><?php echo $this->lang->line('address'); ?></label>
                    <input type="text" id="admin-address" class="span11" />

                    <label for="admin-city"><?php echo $this->lang->line('city'); ?></label>
                    <input type="text" id="admin-city" class="span11" />

                    <label for="admin-state"><?php echo $this->lang->line('state'); ?></label>
                    <input type="text" id="admin-state" class="span11" />

                    <label for="admin-zip-code"><?php echo $this->lang->line('zip_code'); ?></label>
                    <input type="text" id="admin-zip-code" class="span11" />

                    <label for="admin-notes"><?php echo $this->lang->line('notes'); ?></label>
                    <textarea id="admin-notes" class="span11" rows="3"></textarea>
                </div>
                <div class="admin-settings span6">
                    <label for="admin-username"><?php echo $this->lang->line('username'); ?> *</label>
                    <input type="text" id="admin-username" class="span9 required" />
                    
                    <label for="admin-password"><?php echo $this->lang->line('password'); ?> *</label>
                    <input type="password" id="admin-password" class="span9 required"/>
                    
                    <label for="admin-password-confirm"><?php echo $this->lang->line('retype_password'); ?> *</label>
                    <input type="password" id="admin-password-confirm" class="span9 required" />
                    
                    <br>
                    
                    <button type="button" id="admin-notifications" class="btn" data-toggle="button">
                        <i class="icon-envelope"></i>
                        <span><?php echo $this->lang->line('receive_notifications'); ?></span>
                    </button>
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
        <div id="filter-providers" class="filter-records column span4">
            <form class="input-append">
                <input class="key span12" type="text" />
                <button class="filter btn" type="submit" title="<?php echo $this->lang->line('filter'); ?>">
                    <i class="icon-search"></i>
                </button>
                <button class="clear btn" type="button" title="<?php echo $this->lang->line('clear'); ?>">
                    <i class="icon-repeat"></i>
                </button>
            </form>
            
            <h2><?php echo $this->lang->line('providers'); ?></h2>
            <div class="results"></div>
        </div>
        
        <div class="details column span7">
            <div class="btn-toolbar span5">
                <div class="add-edit-delete-group btn-group">
                    <button id="add-provider" class="btn btn-primary">
                        <i class="icon-plus icon-white"></i>
                        <?php echo $this->lang->line('add'); ?>
                    </button>
                    <button id="edit-provider" class="btn" disabled="disabled">
                        <i class="icon-pencil"></i>
                        <?php echo $this->lang->line('edit'); ?>
                    </button>
                    <button id="delete-provider" class="btn" disabled="disabled">
                        <i class="icon-remove"></i>
                        <?php echo $this->lang->line('delete'); ?>
                    </button>
                </div>

                <div class="save-cancel-group btn-group" style="display:none;">
                    <button id="save-provider" class="btn btn-primary">
                        <i class="icon-ok icon-white"></i>
                        <?php echo $this->lang->line('save'); ?>
                    </button>
                    <button id="cancel-provider" class="btn">
                        <i class="icon-ban-circle"></i>
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
                <h2><?php echo $this->lang->line('details'); ?></h2>

                <input type="hidden" id="provider-id" class="record-id" />

                <div class="row-fluid">
                    <div class="provider-details span6">
                        <label for="provider-first-name"><?php echo $this->lang->line('first_name'); ?> *</label>
                        <input type="text" id="provider-first-name" class="span11 required" />

                        <label for="provider-last-name"><?php echo $this->lang->line('last_name'); ?> *</label>
                        <input type="text" id="provider-last-name" class="span11 required" />

                        <label for="provider-email"><?php echo $this->lang->line('email'); ?> *</label>
                        <input type="text" id="provider-email" class="span11 required" />

                        <label for="provider-mobile-number"><?php echo $this->lang->line('mobile_number'); ?></label>
                        <input type="text" id="provider-mobile-number" class="span11" />

                        <label for="provider-phone-number"><?php echo $this->lang->line('phone_number'); ?> *</label>
                        <input type="text" id="provider-phone-number" class="span11 required" />

                        <label for="provider-address"><?php echo $this->lang->line('address'); ?></label>
                        <input type="text" id="provider-address" class="span11" />

                        <label for="provider-city"><?php echo $this->lang->line('city'); ?></label>
                        <input type="text" id="provider-city" class="span11" />

                        <label for="provider-state"><?php echo $this->lang->line('state'); ?></label>
                        <input type="text" id="provider-state" class="span11" />

                        <label for="provider-zip-code"><?php echo $this->lang->line('zip_code'); ?></label>
                        <input type="text" id="provider-zip-code" class="span11" />

                        <label for="provider-notes"><?php echo $this->lang->line('notes'); ?></label>
                        <textarea id="provider-notes" class="span11" rows="3"></textarea>
                    </div>
                    <div class="provider-settings span6">
                        <label for="provider-username"><?php echo $this->lang->line('username'); ?> *</label>
                        <input type="text" id="provider-username" class="span9 required" />

                        <label for="provider-password"><?php echo $this->lang->line('password'); ?> *</label>
                        <input type="password" id="provider-password" class="span9 required"/>

                        <label for="provider-password-confirm"><?php echo $this->lang->line('retype_password'); ?> *</label>
                        <input type="password" id="provider-password-confirm" class="span9 required" />

                        <br>

                        <button type="button" id="provider-notifications" class="btn" data-toggle="button">
                            <i class="icon-envelope"></i>
                            <span><?php echo $this->lang->line('receive_notifications'); ?></span>
                        </button>

                        <br><br>

                        <h4><?php echo $this->lang->line('services'); ?></h4>
                        <div id="provider-services"></div>
                    </div>
                </div>
            </div>
                
            <div class="working-plan-view provider-view" style="display: none;">
                <h2><?php echo $this->lang->line('working_plan'); ?></h2>
                <button id="reset-working-plan" class="btn btn-primary"
                        title="Reset the working plan back to the default values.">
                    <i class="icon-repeat icon-white"></i>
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
                            <td><label class="checkbox"><input type="checkbox" id="monday" />
                                    <?php echo $this->lang->line('monday'); ?></label></td>
                            <td><input type="text" id="monday-start" class="work-start" /></td>
                            <td><input type="text" id="monday-end" class="work-end" /></td>
                        </tr>
                        <tr>
                            <td><label class="checkbox"><input type="checkbox" id="tuesday" />
                                    <?php echo $this->lang->line('tuesday'); ?></label></td>
                            <td><input type="text" id="tuesday-start" class="work-start" /></td>
                            <td><input type="text" id="tuesday-end" class="work-end" /></td>
                        </tr>
                        <tr>
                            <td><label class="checkbox"><input type="checkbox" id="wednesday" />
                                    <?php echo $this->lang->line('wednesday'); ?></label></td>
                            <td><input type="text" id="wednesday-start" class="work-start" /></td>
                            <td><input type="text" id="wednesday-end" class="work-end" /></td>
                        </tr>
                        <tr>
                            <td><label class="checkbox"><input type="checkbox" id="thursday" />
                                    <?php echo $this->lang->line('tuesday'); ?></label></td>
                            <td><input type="text" id="thursday-start" class="work-start" /></td>
                            <td><input type="text" id="thursday-end" class="work-end" /></td>
                        </tr>
                        <tr>
                            <td><label class="checkbox"><input type="checkbox" id="friday" />
                                    <?php echo $this->lang->line('friday'); ?></label></td>
                            <td><input type="text" id="friday-start" class="work-start" /></td>
                            <td><input type="text" id="friday-end" class="work-end" /></td>
                        </tr>
                        <tr>
                            <td><label class="checkbox"><input type="checkbox" id="saturday" />
                                    <?php echo $this->lang->line('saturday'); ?></label></td>
                            <td><input type="text" id="saturday-start" class="work-start" /></td>
                            <td><input type="text" id="saturday-end" class="work-end" /></td>
                        </tr>
                        <tr>
                            <td><label class="checkbox"><input type="checkbox" id="sunday" />
                                    <?php echo $this->lang->line('sunday'); ?></label></td>
                            <td><input type="text" id="sunday-start" class="work-start" /></td>
                            <td><input type="text" id="sunday-end" class="work-end" /></td>
                        </tr>
                    </tbody>
                </table>

                <br>
                
                <h2><?php echo $this->lang->line('breaks');?></h2>

                <span class="help-block">
                    <?php echo $this->lang->line('add_breaks_during_each_day');?>
                </span>

                <div>
                    <button type="button" class="add-break btn btn-primary">
                        <i class="icon-white icon-plus"></i>
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
    
    <?php 
        // ---------------------------------------------------------------------
        //
        // Secretaries Tab 
        //
        // ---------------------------------------------------------------------
    ?>
    <div id="secretaries" class="tab-content" style="display:none;">
        <div id="filter-secretaries" class="filter-records column span4">
            <form class="input-append">
                <input class="key span12" type="text" />
                <button class="filter btn" type="submit" title="<?php echo $this->lang->line('filter');?>">
                    <i class="icon-search"></i>
                </button>
                <button class="clear btn" type="button" title="<?php echo $this->lang->line('clear');?>">
                    <i class="icon-repeat"></i>
                </button>
            </form>
            
            <h2><?php echo $this->lang->line('secretaries');?></h2>
            <div class="results"></div>
        </div>
        
        <div class="details column span7">
            <div class="btn-toolbar">
                <div class="add-edit-delete-group btn-group">
                    <button id="add-secretary" class="btn btn-primary">
                        <i class="icon-plus icon-white"></i>
                        <?php echo $this->lang->line('add');?>
                    </button>
                    <button id="edit-secretary" class="btn" disabled="disabled">
                        <i class="icon-pencil"></i>
                        <?php echo $this->lang->line('edit');?>
                    </button>
                    <button id="delete-secretary" class="btn" disabled="disabled">
                        <i class="icon-remove"></i>
                        <?php echo $this->lang->line('delete');?>
                    </button>
                </div>

                <div class="save-cancel-group btn-group" style="display:none;">
                    <button id="save-secretary" class="btn btn-primary">
                        <i class="icon-ok icon-white"></i>
                        <?php echo $this->lang->line('save');?>
                    </button>
                    <button id="cancel-secretary" class="btn">
                        <i class="icon-ban-circle"></i>
                        <?php echo $this->lang->line('cancel');?>
                    </button>
                </div>
            </div>
            
            <h2><?php echo $this->lang->line('details');?></h2>
            
            <div class="form-message alert" style="display:none;"></div>
            
            <input type="hidden" id="secretary-id" class="record-id" />
            
            <div class="row-fluid">
                <div class="secretary-details span6">
                    <label for="secretary-first-name"><?php echo $this->lang->line('first_name');?> *</label>
                    <input type="text" id="secretary-first-name" class="span11 required" />

                    <label for="secretary-last-name"><?php echo $this->lang->line('last_name');?> *</label>
                    <input type="text" id="secretary-last-name" class="span11 required" />

                    <label for="secretary-email"><?php echo $this->lang->line('email');?> *</label>
                    <input type="text" id="secretary-email" class="span11 required" />

                    <label for="secretary-mobile-number"><?php echo $this->lang->line('mobile_number');?></label>
                    <input type="text" id="secretary-mobile-number" class="span11" />

                    <label for="secretary-phone-number"><?php echo $this->lang->line('phone_number');?> *</label>
                    <input type="text" id="secretary-phone-number" class="span11 required" />

                    <label for="secretary-address"><?php echo $this->lang->line('address');?></label>
                    <input type="text" id="secretary-address" class="span11" />

                    <label for="secretary-city"><?php echo $this->lang->line('city');?></label>
                    <input type="text" id="secretary-city" class="span11" />

                    <label for="secretary-state"><?php echo $this->lang->line('state');?></label>
                    <input type="text" id="secretary-state" class="span11" />

                    <label for="secretary-zip-code"><?php echo $this->lang->line('zip_code');?></label>
                    <input type="text" id="secretary-zip-code" class="span11" />

                    <label for="secretary-notes"><?php echo $this->lang->line('notes');?></label>
                    <textarea id="secretary-notes" class="span11" rows="3"></textarea>
                </div>
                <div class="secretary-settings span6">
                    <label for="secretary-username"><?php echo $this->lang->line('username');?> *</label>
                    <input type="text" id="secretary-username" class="span9 required" />
                   
                    <label for="secretary-password"><?php echo $this->lang->line('password');?> *</label>
                    <input type="password" id="secretary-password" class="span9 required"/>
                    
                    <label for="secretary-password-confirm"><?php echo $this->lang->line('retype_password');?> *</label>
                    <input type="password" id="secretary-password-confirm" class="span9 required" />
                    
                    <br>
                    
                    <button type="button" id="secretary-notifications" class="btn" data-toggle="button">
                        <i class="icon-envelope"></i>
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