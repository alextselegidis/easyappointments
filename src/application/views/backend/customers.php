<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/libs/jquery/jquery-ui-timepicker-addon.js"></script>
        
<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/backend_customers.js"></script>
        
<script type="text/javascript">    
    var GlobalVariables = {
        'availableProviders': <?php echo json_encode($available_providers); ?>,
        'availableServices': <?php echo json_encode($available_services); ?>,
        'baseUrl': <?php echo '"' . $base_url . '"'; ?>,
        'customers': <?php echo json_encode($customers); ?>,
        'user'                  : {
            'id'        : <?php echo $user_id; ?>,
            'email'     : <?php echo '"' . $user_email . '"'; ?>,
            'role_slug' : <?php echo '"' . $role_slug . '"'; ?>,
            'privileges': <?php echo json_encode($privileges); ?>
        }
    };
    
    $(document).ready(function() {
        BackendCustomers.initialize(true);
    });
</script>

<div id="customers-page" class="row-fluid">
	<div id="filter-customers" class="filter-records column span4">
		<form class="input-append">
			<input class="key span8" type="text" />
            <button class="filter btn" type="submit">
                    <i class="icon-filter"></i>
                    Filter
                </button>
                <button class="clear btn" type="button">
                    <i class="icon-remove-circle"></i>
                    Clear
                </button>
		</form>
        
        <h2>Customers</h2>
        <div class="results"></div>
	</div>

	<div id="details" class="span7 row-fluid">
        <div class="btn-toolbar">
            <div id="add-edit-delete-group" class="btn-group">
                <?php if ($privileges[PRIV_CUSTOMERS]['add'] == TRUE) { ?>
                <button id="add-customer" class="btn">
                    <i class="icon-plus"></i>
                    Add</button>
                <?php } ?>
                
                <?php if ($privileges[PRIV_CUSTOMERS]['edit'] == TRUE) { ?>
                <button id="edit-customer" class="btn" disabled="disabled">
                    <i class="icon-pencil"></i>
                    Edit</button>
                <?php }?>
                
                <?php if ($privileges[PRIV_CUSTOMERS]['delete'] == TRUE) { ?>
                <button id="delete-customer" class="btn" disabled="disabled">
                    <i class="icon-remove"></i>
                    Delete</button>
                <?php } ?>
            </div>
            
            <div id="save-cancel-group" class="btn-group" style="display:none;">
                <button id="save-customer" class="btn">
                    <i class="icon-ok"></i>
                    Save</button>
                <button id="cancel-customer" class="btn">
                    <i class="icon-ban-circle"></i>
                    Cancel</button>
            </div>
        </div>
        
        <input id="customer-id" type="hidden" />
        
        <div class="span6" style="margin-left: 0;">
            <h2>Details</h2>
            <div id="form-message" class="alert" style="display:none;"></div>
            
            <label for="first-name">First Name</label>
            <input type="text" id="first-name" class="span11" />

            <label for="last-name">Last Name *</label>
            <input type="text" id="last-name" class="span11 required" />

            <label for="email">Email *</label>
            <input type="text" id="email" class="span11 required" />

            <label for="phone-number">Phone Number *</label>
            <input type="text" id="phone-number" class="span11 required" />
            
            <label for="address">Address</label>
            <input type="text" id="address" class="span11" />

            <label for="city">City</label>
            <input type="text" id="city" class="span11" />

            <label for="zip-code">Zip Code</label>
            <input type="text" id="zip-code" class="span11" />

            <label for="notes">Notes</label>
            <textarea id="notes" rows="4" class="span11"></textarea>
            
            <br/><br/>
            <center><em id="form-message" class="text-error">Fields with * are required!</em></center>
        </div> 
        
        <div class="span5">
            <h2>Appointments</h2>
            <div id="customer-appointments"></div>
            <div id="appointment-details"></div>
        </div>
	</div>
</div>