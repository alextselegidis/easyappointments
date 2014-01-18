<link rel="stylesheet" type="text/css"
        href="<?php echo $base_url; ?>assets/css/libs/jquery/fullcalendar.css" />

<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/libs/jquery/fullcalendar.min.js"></script>

        <script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/libs/jquery/jquery-ui-timepicker-addon.js"></script>
        
<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/backend_calendar.js"></script>
        
<script type="text/javascript">    
    var GlobalVariables = {
        'availableProviders'    : <?php echo json_encode($available_providers); ?>,
        'availableServices'     : <?php echo json_encode($available_services); ?>,
        'baseUrl'               : <?php echo '"' . $base_url . '"'; ?>,
        'bookAdvanceTimeout'    : <?php echo $book_advance_timeout; ?>,
        'editAppointment'       : <?php echo json_encode($edit_appointment); ?>,
        'customers'             : <?php echo json_encode($customers); ?>,
        'secretaryProviders'    : <?php echo json_encode($secretary_providers); ?>,
        'user'                  : {
            'id'        : <?php echo $user_id; ?>,
            'email'     : <?php echo '"' . $user_email . '"'; ?>,
            'role_slug' : <?php echo '"' . $role_slug . '"'; ?>,
            'privileges': <?php echo json_encode($privileges); ?>
        }
    };
    
    $(document).ready(function() {
        BackendCalendar.initialize(true);
    });
</script>

<div id="calendar-page">
    <div id="calendar-toolbar">
        <div id="calendar-filter">
            <label for="select-filter-item">
                <?php echo $this->lang->line('display_calendar'); ?>
            </label>
            <select id="select-filter-item" 
                    title="<?php echo $this->lang->line('select_filter_item_hint'); ?>">
            </select>
        </div>
        
        <div id="calendar-actions">
            <div class="btn-group">
                <?php //if ($privileges[PRIV_USERS]['edit'] == TRUE) { ?>
                <?php if (($role_slug == DB_SLUG_ADMIN || $role_slug == DB_SLUG_PROVIDER)
                        && $this->config->item('ea_google_sync_feature') == TRUE) { ?>
                <button id="google-sync" class="btn btn-primary" 
                        title="<?php echo $this->lang->line('trigger_google_sync_hint'); ?>">
                    <i class="icon-refresh icon-white"></i>
                    <span><?php echo $this->lang->line('synchronize'); ?></span>
                </button>

                <button id="enable-sync" class="btn" data-toggle="button" 
                        title="<?php echo $this->lang->line('enable_appointment_sync_hint'); ?>">
                    <i class="icon-calendar"></i>
                    <span><?php echo $this->lang->line('enable_sync'); ?></span>
                </button>
                <?php } ?>
                
                <button id="reload-appointments" class="btn" 
                        title="<?php echo $this->lang->line('reload_appointments_hint'); ?>">
                    <i class="icon-repeat"></i>
                    <span><?php echo $this->lang->line('reload'); ?></span>
                </button>
            </div>
            
            <?php if ($privileges[PRIV_APPOINTMENTS]['add'] == TRUE) { ?>
            <div class="btn-group">
                <button id="insert-appointment" class="btn btn-info" 
                        title="<?php echo $this->lang->line('new_appointment_hint'); ?>">
                    <i class="icon-plus icon-white"></i>
                    <span><?php echo $this->lang->line('appointment'); ?></span>
                </button>

                <button id="insert-unavailable" class="btn" 
                        title="<?php echo $this->lang->line('unavailable_periods_hint'); ?>">
                    <i class="icon-plus"></i>
                    <span><?php echo $this->lang->line('unavailable'); ?></span>
                </button>
            </div>
            <?php } ?>
        </div>
    </div>
    
    <div id="calendar"></div> <?php // Main calendar container ?>
</div>

<?php
    // --------------------------------------------------------------------
    //
    // MANAGE APPOINTMENT
    //
    // --------------------------------------------------------------------
?>
<div id="manage-appointment" class="modal hide fade" data-keyboard="true" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" 
                aria-hidden="true">&times;</button>
        <h3><?php echo $this->lang->line('edit_appointment_title'); ?></h3>
    </div>
    
    <div class="modal-body">
        <div class="modal-message alert" style="display: none;"></div>
        
        <form class="form-horizontal">
            <fieldset>
                <legend><?php echo $this->lang->line('appointment_details_title'); ?></legend>
                
                <input id="appointment-id" type="hidden" />
                
                <div class="control-group">
                    <label for="select-service" class="control-label"><?php echo $this->lang->line('service'); ?> *</label>
                    <div class="controls">
                        <select id="select-service" class="required span4">
                            <?php 
                                // Group services by category, only if there is at least one service 
                                // with a parent category.
                                foreach($available_services as $service) {
                                    if ($service['category_id'] != NULL) {
                                        $has_category = TRUE;
                                        break;
                                    }
                                }
                                
                                if ($has_category) {
                                    $grouped_services = array();

                                    foreach($available_services as $service) {
                                        if ($service['category_id'] != NULL) {
                                            if (!isset($grouped_services[$service['category_name']])) {
                                                $grouped_services[$service['category_name']] = array();
                                            }

                                            $grouped_services[$service['category_name']][] = $service;
                                        } 
                                    }

                                    // We need the uncategorized services at the end of the list so
                                    // we will use another iteration only for the uncategorized services.
                                    $grouped_services['uncategorized'] = array();
                                    foreach($available_services as $service) {
                                        if ($service['category_id'] == NULL) {
                                            $grouped_services['uncategorized'][] = $service;
                                        }
                                    }

                                    foreach($grouped_services as $key => $group) {
                                        $group_label = ($key != 'uncategorized')
                                                ? $group[0]['category_name'] : 'Uncategorized';
                                        
                                        if (count($group) > 0) {
                                            echo '<optgroup label="' . $group_label . '">';
                                            foreach($group as $service) {
                                                echo '<option value="' . $service['id'] . '">' 
                                                    . $service['name'] . '</option>';
                                            }
                                            echo '</optgroup>';
                                        }
                                    }
                                }  else {
                                    foreach($available_services as $service) {
                                        echo '<option value="' . $service['id'] . '">' 
                                                    . $service['name'] . '</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class="control-group">
                    <label for="select-provider" class="control-label"><?php echo $this->lang->line('provider'); ?> *</label>
                    <div class="controls">
                        <select id="select-provider" class="required span4"></select>
                    </div>
                </div>
                
                <div class="control-group">
                    <label for="start-datetime" class="control-label"><?php echo $this->lang->line('start_date_time'); ?></label>
                    <div class="controls">
                        <input type="text" id="start-datetime" />
                    </div>
                </div>
                
                <div class="control-group">
                    <label for="end-datetime" class="control-label"><?php echo $this->lang->line('start_date_time'); ?></label>
                    <div class="controls">
                        <input type="text" id="end-datetime" />
                    </div>
                </div>
                
                <div class="control-group">
                    <label for="appointment-notes" class="control-label"><?php echo $this->lang->line('notes'); ?></label>
                    <div class="controls">
                        <textarea id="appointment-notes" class="span4" rows="3"></textarea>
                    </div>
                </div>
            </fieldset>

            <fieldset class="row-fluid">
                <legend>
                    <?php echo $this->lang->line('customer_details_title'); ?>
                    <button id="new-customer" class="btn btn-mini" 
                            title="<?php echo $this->lang->line('clear_fields_add_existing_customer_hint'); ?>"
                            type="button"><?php echo $this->lang->line('new'); ?>
                    </button>
                    <button id="select-customer" class="btn btn-primary btn-mini" 
                            title="<?php echo $this->lang->line('pick_existing_customer_hint'); ?>" 
                            type="button"><?php echo $this->lang->line('select'); ?>
                    </button>
                    <input type="text" id="filter-existing-customers"
                           placeholder="<?php echo $this->lang->line('type_to_filter_customers'); ?>" 
                           style="display: none;" class="input-medium span4"/>
                    <div id="existing-customers-list" style="display: none;"></div>
                </legend>

                <input id="customer-id" type="hidden" />
                
                <div class="span5">
                    <div class="control-group">
                        <label for="first-name" class="control-label">
                            <?php echo $this->lang->line('first_name'); ?> *</label>
                        <div class="controls">
                            <input type="text" id="first-name" class="required" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="last-name" class="control-label">
                            <?php echo $this->lang->line('last_name'); ?>*</label>
                        <div class="controls">
                            <input type="text" id="last-name" class="required" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="email" class="control-label">
                            <?php echo $this->lang->line('email'); ?>*</label>
                        <div class="controls">
                            <input type="text" id="email" class="required" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="phone-number" class="control-label">
                            <?php echo $this->lang->line('phone_number'); ?>*</label>
                        <div class="controls">
                            <input type="text" id="phone-number" class="required" />
                        </div>
                    </div>
                </div>
                <div class="span6">
                    <div class="control-group">
                        <label for="address" class="control-label">
                            <?php echo $this->lang->line('address'); ?>
                        </label>
                        <div class="controls">
                            <input type="text" id="address" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="city" class="control-label">
                            <?php echo $this->lang->line('city'); ?>
                        </label>
                        <div class="controls">
                            <input type="text" id="city" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="zip-code" class="control-label">
                            <?php echo $this->lang->line('zip_code'); ?>
                        </label>
                        <div class="controls">
                            <input type="text" id="zip-code" />
                        </div>
                    </div>
                    
                    <div class="control-group">
                        <label for="customer-notes" class="control-label">
                            <?php echo $this->lang->line('notes'); ?></label>
                        <div class="controls">
                            <textarea id="customer-notes" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
    
    <div class="modal-footer">
        <button id="save-appointment" class="btn btn-primary">
            <?php echo $this->lang->line('save'); ?>
        </button>
        <button id="cancel-appointment" class="btn">
            <?php echo $this->lang->line('cancel'); ?>
        </button>
    </div>
</div>

<?php
    // --------------------------------------------------------------------
    //
    // MANAGE UNAVAILABLE
    //
    // --------------------------------------------------------------------
?>
<div id="manage-unavailable" class="modal hide fade" data-keyboard="true" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" 
                aria-hidden="true">&times;</button>
        <h3><?php echo $this->lang->line('new_unavailable_title'); ?></h3>
    </div>
    
    <div class="modal-body">
        <div class="modal-message alert" style="display: none;"></div>
        
        <form class="form-horizontal">
            <fieldset>
                <input id="unavailable-id" type="hidden" />
                
                <div class="control-group">
                    <label for="unavailable-start" class="control-label">
                        <?php echo $this->lang->line('start'); ?>
                    </label>
                    <div class="controls">
                        <input type="text" id="unavailable-start" />
                    </div>
                </div>
                
                <div class="control-group">
                    <label for="unavailable-end" class="control-label">
                        <?php echo $this->lang->line('end'); ?>
                    </label>
                    <div class="controls">
                        <input type="text" id="unavailable-end" />
                    </div>
                </div>
                
                <div class="control-group">
                    <label for="unavailable-notes" class="control-label">
                        <?php echo $this->lang->line('notes'); ?>
                    </label>
                    <div class="controls">
                        <textarea id="unavailable-notes" rows="3" class="span3"></textarea>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
    
    <div class="modal-footer">
        <button id="save-unavailable" class="btn btn-primary">
            <?php echo $this->lang->line('save'); ?>
        </button>
        <button id="cancel-unavailable" class="btn">
            <?php echo $this->lang->line('cancel'); ?>
        </button>
    </div>
</div>

<?php
    // --------------------------------------------------------------------
    //
    // SELECT GOOGLE CALENDAR
    //
    // --------------------------------------------------------------------
?>
<div id="select-google-calendar" class="modal hide fade" data-keyboard="true" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" 
                aria-hidden="true">&times;</button>
        <h3><?php echo $this->lang->line('select_google_calendar'); ?></h3>
    </div>
    
    <div class="modal-body">
        <p>
            <?php echo $this->lang->line('select_google_calendar_prompt'); ?>
        </p>
        <select id="google-calendar"></select>
    </div>
    
    <div class="modal-footer">
        <button id="select-calendar" class="btn btn-primary">
            <?php echo $this->lang->line('select'); ?>
        </button>
        <button id="close-calendar" class="btn">
            <?php echo $this->lang->line('close'); ?>
        </button>
    </div>
</div>
