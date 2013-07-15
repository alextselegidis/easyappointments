<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/libs/jquery/jquery-ui-timepicker-addon.js"></script>
        
<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/backend_customers.js"></script>
        
<script type="text/javascript">    
    var GlobalVariables = {
        'availableProviders': <?php echo json_encode($available_providers); ?>,
        'availableServices': <?php echo json_encode($available_services); ?>,
        'baseUrl': <?php echo '"' . $base_url . '"'; ?>,
        'customers': <?php echo json_encode($customers); ?>
    };
    
    $(document).ready(function() {
        BackendCustomers.initialize(true);
    });
</script>

<div id="customers-page" class="row-fluid">
	<div id="filter" class="span4">
		<div class="input-append">
			<input class="span12" type="text" id="filter-key" />
            <button type="button" class="btn" id="filter-customers">Filter</button>
		</div>
        
        <h2>Customers</h2>
        <div id="filter-results"></div>
	</div>

	<div id="details" class="span7 row-fluid">
        <div class="btn-toolbar">
            <div id="add-edit-delete-group" class="btn-group">
                <button id="add-customer" class="btn">
                    <i class="icon-plus"></i>
                    Add</button>
                <button id="edit-customer" class="btn" disabled="disabled">
                    <i class="icon-pencil"></i>
                    Edit</button>
                <button id="delete-customer" class="btn" disabled="disabled">
                    <i class="icon-remove"></i>
                    Delete</button>
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
        
        <div class="span5">
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
        
        <div class="span7">
            <h2>Appointments</h2>
            <div id="customer-appointments"></div>
            <div id="appointment-details"></div>
        </div>
	</div>
</div>