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
            <label for="select-filter-item">Display Calendar</label>
            <select id="select-filter-item" title="Select a provider or a service and view the appointments on the calendar."></select>
        </div>
        
        <div id="calendar-actions">
            <div class="btn-group">
                <?php //if ($privileges[PRIV_USERS]['edit'] == TRUE) { ?>
                <?php if (($role_slug == DB_SLUG_ADMIN || $role_slug == DB_SLUG_PROVIDER)
                        && $this->config->item('ea_google_sync_feature') == TRUE) { ?>
                <button id="google-sync" class="btn btn-primary" 
                        title="Trigger the Google Calendar synchronization process.">
                    <i class="icon-refresh icon-white"></i>
                    <span>Synchronize</span>
                </button>

                <button id="enable-sync" class="btn" data-toggle="button" 
                        title="Enable appointment synchronization with provider's Google Calendar account.">
                    <i class="icon-calendar"></i>
                    <span>Enable Sync</span>
                </button>
                <?php } ?>
                
                <button id="reload-appointments" class="btn" title="Reload calendar appointments.">
                    <i class="icon-repeat"></i>
                    <span>Reload</span>
                </button>
            </div>
            
            <?php if ($privileges[PRIV_APPOINTMENTS]['add'] == TRUE) { ?>
            <div class="btn-group">
                <button id="insert-appointment" class="btn btn-info" 
                        title="Create a new appointment and store it into the database.">
                    <i class="icon-plus icon-white"></i>
                    <span>Appointment</span>
                </button>

                <button id="insert-unavailable" class="btn" 
                        title="During unavailable periods the provider won't accept new appointments.">
                    <i class="icon-plus"></i>
                    <span>Unavailable</span>
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
<div id="manage-appointment" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" 
                aria-hidden="true">&times;</button>
        <h3>Edit Appointment</h3>
    </div>
    
    <div class="modal-body">
        <div class="modal-message alert" style="display: none;"></div>
        
        <form class="form-horizontal">
            <fieldset>
                <legend>Appointment Details</legend>
                
                <input id="appointment-id" type="hidden" />
                
                <div class="control-group">
                    <label for="select-service" class="control-label">Service *</label>
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
                    <label for="select-provider" class="control-label">Provider *</label>
                    <div class="controls">
                        <select id="select-provider" class="required span4"></select>
                    </div>
                </div>
                
                <div class="control-group">
                    <label for="start-datetime" class="control-label">Start Date/Time</label>
                    <div class="controls">
                        <input type="text" id="start-datetime" />
                    </div>
                </div>
                
                <div class="control-group">
                    <label for="end-datetime" class="control-label">End Date/Time</label>
                    <div class="controls">
                        <input type="text" id="end-datetime" />
                    </div>
                </div>
                
                <div class="control-group">
                    <label for="notes" class="control-label">Notes</label>
                    <div class="controls">
                        <textarea id="appointment-notes" class="span4" rows="3"></textarea>
                    </div>
                </div>
            </fieldset>

            <fieldset class="row-fluid">
                <legend>
                    Customer Details
                    <button id="new-customer" class="btn btn-mini" title="Clear the fields and enter a new customer."
                            type="button">New</button>
                    <button id="select-customer" class="btn btn-primary btn-mini" 
                            title="Pick an existing customer." type="button">Select</button>
                    <input type="text" id="filter-existing-customers" placeholder="Type to filter customers." 
                           style="display: none;" class="input-medium"/>
                    <div id="existing-customers-list" style="display: none;"></div>
                </legend>

                <input id="customer-id" type="hidden" />
                
                <div class="span5">
                    <div class="control-group">
                        <label for="first-name" class="control-label">First Name *</label>
                        <div class="controls">
                            <input type="text" id="first-name" class="required" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="last-name" class="control-label">Last Name *</label>
                        <div class="controls">
                            <input type="text" id="last-name" class="required" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="email" class="control-label">Email *</label>
                        <div class="controls">
                            <input type="text" id="email" class="required" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="phone-number" class="control-label">Phone Number *</label>
                        <div class="controls">
                            <input type="text" id="phone-number" class="required" />
                        </div>
                    </div>
                </div>
                <div class="span6">
                    <div class="control-group">
                        <label for="address" class="control-label">Address</label>
                        <div class="controls">
                            <input type="text" id="address" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="city" class="control-label">City</label>
                        <div class="controls">
                            <input type="text" id="city" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="zip-code" class="control-label">Zip Code</label>
                        <div class="controls">
                            <input type="text" id="zip-code" />
                        </div>
                    </div>
                    
                    <div class="control-group">
                        <label for="notes" class="control-label">Notes</label>
                        <div class="controls">
                            <textarea id="customer-notes" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
    
    <div class="modal-footer">
        <button id="save-appointment" class="btn btn-primary">Save</button>
        <button id="cancel-appointment" class="btn">Cancel</button>
    </div>
</div>

<?php
    // --------------------------------------------------------------------
    //
    // MANAGE UNAVAILALBE
    //
    // --------------------------------------------------------------------
?>
<div id="manage-unavailable" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" 
                aria-hidden="true">&times;</button>
        <h3>Add Unavailable Period</h3>
        
    </div>
    
    <div class="modal-body">
        <div class="modal-message alert" style="display: none;"></div>
        
        <form class="form-horizontal">
            <fieldset>
                <input id="unavailable-id" type="hidden" />
                
                <div class="control-group">
                    <label for="unavailable-start" class="control-label">Start</label>
                    <div class="controls">
                        <input type="text" id="unavailable-start" />
                    </div>
                </div>
                
                <div class="control-group">
                    <label for="unavailable-end" class="control-label">End</label>
                    <div class="controls">
                        <input type="text" id="unavailable-end" />
                    </div>
                </div>
                
                <div class="control-group">
                    <label for="unavailable-notes" class="control-label">Notes</label>
                    <div class="controls">
                        <textarea id="unavailable-notes" rows="3" class="span3"></textarea>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
    
    <div class="modal-footer">
        <button id="save-unavailable" class="btn btn-primary">Save</button>
        <button id="cancel-unavailable" class="btn">Cancel</button>
    </div>
</div>