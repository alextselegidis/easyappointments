<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/backend_users.js"></script>
        
<script type="text/javascript">    
    var GlobalVariables = {
        'baseUrl': <?php echo '"' . $base_url . '"'; ?>,
        'admins': <?php echo json_encode($admins); ?>,
        'providers': <?php echo json_encode($providers); ?>,
        'secretaries': <?php echo json_encode($secretaries); ?>,
        'services': <?php echo json_encode($services); ?>
    };
    
    $(document).ready(function() {
        BackendUsers.initialize(true);
    });
</script>

<div id="users-page" class="row-fluid">
    
    <?php // Page Tabs ?>
    <ul class="nav nav-tabs">
        <li class="admins-tab tab active"><a>Admins</a></li>
        <li class="providers-tab tab"><a>Providers</a></li>
        <li class="secretaries-tab tab"><a>Secretaries</a></li>
    </ul>
    
    <?php // Admin Tab ?>
    <div id="admins" class="tab-content">
        <div class="filter span4">
            <div class="input-append">
                <input class="filter-key span12" type="text" />
                <button class="filter-admins btn" type="button">Filter</button>
            </div>
            <h2>Admins</h2>
            <div class="filter-results"></div>
        </div>
        
        <div class="details span7">
            <div class="btn-toolbar">
                <div class="add-edit-delete-group btn-group">
                    <button id="add-admin" class="btn">
                        <i class="icon-plus"></i>
                        Add</button>
                    <button id="edit-admin" class="btn" disabled="disabled">
                        <i class="icon-pencil"></i>
                        Edit</button>
                    <button id="delete-admin" class="btn" disabled="disabled">
                        <i class="icon-remove"></i>
                        Delete</button>
                </div>

                <div class="save-cancel-group btn-group" style="display:none;">
                    <button id="save-admin" class="btn">
                        <i class="icon-ok"></i>
                        Save</button>
                    <button id="cancel-admin" class="btn">
                        <i class="icon-ban-circle"></i>
                        Cancel</button>
                </div>
            </div>
            
            <h2>Details</h2>
            
            <div class="form-message alert" style="display:none;"></div>
            
            <input type="hidden" id="admin-id" />
            
            <div class="row-fluid">
                <div class="admin-details span6">
                    <label for="first-name">First Name</label>
                    <input type="text" id="admin-first-name" class="span11" />

                    <label for="admin-last-name">Last Name *</label>
                    <input type="text" id="admin-last-name" class="span11 required" />

                    <label for="admin-email">Email *</label>
                    <input type="text" id="admin-email" class="span11 required" />

                    <label for="admin-mobile-number">Mobile Number</label>
                    <input type="text" id="admin-mobile-number" class="span11" />

                    <label for="admin-phone-number">Phone Number *</label>
                    <input type="text" id="admin-phone-number" class="span11 required" />

                    <label for="admin-address">Address</label>
                    <input type="text" id="admin-address" class="span11" />

                    <label for="admin-city">City</label>
                    <input type="text" id="admin-city" class="span11" />

                    <label for="admin-state">State</label>
                    <input type="text" id="admin-state" class="span11" />

                    <label for="admin-zip-code">Zip Code</label>
                    <input type="text" id="admin-zip-code" class="span11" />

                    <label for="admin-notes">Notes</label>
                    <textarea id="admin-notes" class="span11"></textarea>
                </div>
                <div class="admin-settings span6">
                    <label for="admin-username">Username *</label>
                    <input type="text" id="admin-username" class="span7 required" />
                    
                    <label for="admin-password">Password *</label>
                    <input type="password" id="admin-password" class="span7 required"/>
                    
                    <label for="admin-password-confirm">Retype Password *</label>
                    <input type="password" id="admin-password-confirm" class="span7 required" />
                    
                    <br>
                    
                    <button type="button" id="admin-notifications" class="btn" data-toggle="button">
                        <i class="icon-asterisk"></i>
                        <span>Receive Notifications</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <?php // Providers Tab ?>
    <div id="providers" class="tab-content" style="display:none;">
        <div class="filter span4">
            <div class="input-append">
                <input class="filter-key span12" type="text" />
                <button class="filter-providers btn" type="button">Filter</button>
            </div>
            <h2>Providers</h2>
            <div class="filter-results"></div>
        </div>
        
        <div class="details span7">
            <div class="btn-toolbar">
                <div class="add-edit-delete-group btn-group">
                    <button id="add-provider" class="btn">
                        <i class="icon-plus"></i>
                        Add</button>
                    <button id="edit-provider" class="btn" disabled="disabled">
                        <i class="icon-pencil"></i>
                        Edit</button>
                    <button id="delete-provider" class="btn" disabled="disabled">
                        <i class="icon-remove"></i>
                        Delete</button>
                </div>

                <div class="save-cancel-group btn-group" style="display:none;">
                    <button id="save-provider" class="btn">
                        <i class="icon-ok"></i>
                        Save</button>
                    <button id="cancel-provider" class="btn">
                        <i class="icon-ban-circle"></i>
                        Cancel</button>
                </div>
            </div>
            
            <h2>Details</h2>
            
            <div class="form-message alert" style="display:none;"></div>
            
            <input type="hidden" id="provider-id" />
            
            <div class="row-fluid">
                <div class="provider-details span6">
                    <label for="provider-first-name">First Name</label>
                    <input type="text" id="provider-first-name" class="span11" />

                    <label for="provider-last-name">Last Name *</label>
                    <input type="text" id="provider-last-name" class="span11 required" />

                    <label for="provider-email">Email *</label>
                    <input type="text" id="provider-email" class="span11 required" />

                    <label for="provider-mobile-number">Mobile Number</label>
                    <input type="text" id="provider-mobile-number" class="span11" />

                    <label for="provider-phone-number">Phone Number *</label>
                    <input type="text" id="provider-phone-number" class="span11 required" />

                    <label for="provider-address">Address</label>
                    <input type="text" id="provider-address" class="span11" />

                    <label for="provider-city">City</label>
                    <input type="text" id="provider-city" class="span11" />

                    <label for="provider-state">State</label>
                    <input type="text" id="provider-state" class="span11" />

                    <label for="provider-zip-code">Zip Code</label>
                    <input type="text" id="provider-zip-code" class="span11" />

                    <label for="provider-notes">Notes</label>
                    <textarea id="provider-notes" class="span11"></textarea>
                </div>
                <div class="provider-settings span6">
                    <label for="provider-username">Username *</label>
                    <input type="text" id="provider-username" class="span7 required" />
                    
                    <label for="provider-password">Password *</label>
                    <input type="password" id="provider-password" class="span7 required"/>
                    
                    <label for="provider-password-confirm">Retype Password *</label>
                    <input type="password" id="provider-password-confirm" class="span7 required" />
                    
                    <br>
                    
                    <button type="button" id="provider-notifications" class="btn" data-toggle="button">
                        <i class="icon-asterisk"></i>
                        <span>Receive Notifications</span>
                    </button>
                    
                    <br><br>
                    
                    <h4>Services</h4>
                    <div id="provider-services"></div>
                </div>
            </div>
        </div>
    </div>
    
    <?php // Secretaries Tab ?>
    <div id="secretaries" class="tab-content" style="display:none;">
        <div class="filter span4">
            <div class="input-append">
                <input class="filter-key span12" type="text" />
                <button class="filter-secretaries btn" type="button">Filter</button>
            </div>
            <h2>Secretaries</h2>
            <div class="filter-results"></div>
        </div>
        
        <div class="details span7">
            <div class="btn-toolbar">
                <div class="add-edit-delete-group btn-group">
                    <button id="add-secretary" class="btn">
                        <i class="icon-plus"></i>
                        Add</button>
                    <button id="edit-secretary" class="btn" disabled="disabled">
                        <i class="icon-pencil"></i>
                        Edit</button>
                    <button id="delete-secretary" class="btn" disabled="disabled">
                        <i class="icon-remove"></i>
                        Delete</button>
                </div>

                <div class="save-cancel-group btn-group" style="display:none;">
                    <button id="save-secretary" class="btn">
                        <i class="icon-ok"></i>
                        Save</button>
                    <button id="cancel-secretary" class="btn">
                        <i class="icon-ban-circle"></i>
                        Cancel</button>
                </div>
            </div>
            
            <h2>Details</h2>
            
            <div class="form-message alert" style="display:none;"></div>
            
            <input type="hidden" id="secretary-id" />
            
            <div class="row-fluid">
                <div class="secretary-details span6">
                    <label for="secretary-first-name">First Name</label>
                    <input type="text" id="secretary-first-name" class="span11" />

                    <label for="secretary-last-name">Last Name *</label>
                    <input type="text" id="secretary-last-name" class="span11 required" />

                    <label for="secretary-email">Email *</label>
                    <input type="text" id="secretary-email" class="span11 required" />

                    <label for="secretary-mobile-number">Mobile Number</label>
                    <input type="text" id="secretary-mobile-number" class="span11" />

                    <label for="secretary-phone-number">Phone Number *</label>
                    <input type="text" id="secretary-phone-number" class="span11 required" />

                    <label for="secretary-address">Address</label>
                    <input type="text" id="secretary-address" class="span11" />

                    <label for="secretary-city">City</label>
                    <input type="text" id="secretary-city" class="span11" />

                    <label for="secretary-state">State</label>
                    <input type="text" id="secretary-state" class="span11" />

                    <label for="secretary-zip-code">Zip Code</label>
                    <input type="text" id="secretary-zip-code" class="span11" />

                    <label for="secretary-notes">Notes</label>
                    <textarea id="secretary-notes" class="span11"></textarea>
                </div>
                <div class="secretary-settings span6">
                    <label for="secretary-username">Username *</label>
                    <input type="text" id="secretary-username" class="span7 required" />
                    
                    <label for="secretary-password">Password *</label>
                    <input type="password" id="secretary-password" class="span7 required"/>
                    
                    <label for="secretary-password-confirm">Retype Password *</label>
                    <input type="password" id="secretary-password-confirm" class="span7 required" />
                    
                    <br>
                    
                    <button type="button" id="secretary-notifications" class="btn" data-toggle="button">
                        <i class="icon-asterisk"></i>
                        <span>Receive Notifications</span>
                    </button>
                    
                    <br><br>
                    
                    <h4>Providers</h4>
                    <div id="secretary-providers"></div>
                </div>
            </div>
        </div>
    </div>
</div>