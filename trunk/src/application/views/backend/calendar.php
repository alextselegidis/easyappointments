<link rel="stylesheet" type="text/css"
        href="<?php echo $base_url; ?>assets/css/libs/jquery/fullcalendar.css" />

<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/libs/jquery/fullcalendar.min.js"></script>
        
<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/backend_calendar.js"></script>
        
<script type="text/javascript">    
    var GlobalVariables = {
        'availableProviders'    : <?php echo json_encode($available_providers); ?>,
        'availableServices'     : <?php echo json_encode($available_services); ?>,
        'baseUrl'               : <?php echo '"' . $base_url . '"'; ?>
    };
    
    $(document).ready(function() {
        BackendCalendar.initialize(true);
    });
</script>

<div id="calendar-page">
    <div id="calendar-toolbar">
        <div id="calendar-filter">
            <label for="select-filter-item">Display Calendar</label>
            <select id="select-filter-item"></select>
        </div>
        
        <div id="calendar-actions" class="btn-group">
            <button id="google-sync" class="btn btn-primary" 
                    title="Trigger the Google Calendar synchronization process.">
                <i class="icon-refresh icon-white"></i>
                Synchronize
            </button>
            
            <button id="enable-sync" class="btn" data-toggle="button" 
                    title="Enable appointment synchronization with provider's Google Calendar account.">
                <i class="icon-calendar"></i>
                Enable Sync
            </button>
            
            <button id="insert-unavailable-period" class="btn" 
                    title="During unavailalbe period the provider won't accept new appointments.">
                <i class="icon-plus"></i>
                Unavailable
            </button>
        </div>
    </div>
    
    <div id="calendar"></div>
</div>

<div id="manage-appointment" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" 
                aria-hidden="true">&times;</button>
        <h3>Edit Appointment</h3>
    </div>
    
    <div class="modal-body">
        <form class="form-horizontal">
            <fieldset>
                <legend>Appointment Details</legend>
                
                <div class="control-group">
                    <label for="select-service" class="control-label">Service</label>
                    <div class="controls">
                        <select id="select-service"></select>
                    </div>
                </div>
                
                <div class="control-group">
                    <label for="select-provider" class="control-label">Provider</label>
                    <div class="controls">
                        <select id="select-provider"></select>
                    </div>
                </div>
                
                <div class="control-group">
                    <label for="select-date" class="control-label"></label>
                    <div class="controls">
                        <span id="select-date"></span>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>Customer Details</legend>

                <div class="control-group">
                    <label for="first-name" class="control-label">First Name</label>
                    <div class="controls">
                        <input type="text" id="first-name" />
                    </div>
                </div>
                
                <div class="control-group">
                    <label for="last-name" class="control-label">Last Name</label>
                    <div class="controls">
                        <input type="text" id="last-name" />
                    </div>
                </div>
                
                <div class="control-group">
                    <label for="email" class="control-label">Email</label>
                    <div class="controls">
                        <input type="text" id="email" />
                    </div>
                </div>
                
                <div class="control-group">
                    <label for="phone-number" class="control-label">Phone Number</label>
                    <div class="controls">
                        <input type="text" id="phone-number" />
                    </div>
                </div>
                
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
                        <textarea id="notes"></textarea>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
    
    <div class="modal-footer">
        <button id="save-button" class="btn btn-primary">Save</button>
        <button id="cancel-button" class="btn">Cancel</button>
    </div>
</div>