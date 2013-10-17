<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/backend_services.js"></script>
        
<script type="text/javascript">    
    var GlobalVariables = {
        'baseUrl': <?php echo '"' . $base_url . '"'; ?>,
        'services': <?php echo json_encode($services); ?>,
        'categories': <?php echo json_encode($categories); ?>,
        'user'                  : {
            'id'        : <?php echo $user_id; ?>,
            'email'     : <?php echo '"' . $user_email . '"'; ?>,
            'role_slug' : <?php echo '"' . $role_slug . '"'; ?>,
            'privileges': <?php echo json_encode($privileges); ?>
        }
    };
    
    $(document).ready(function() {
        BackendServices.initialize(true);
    });
</script>

<div id="services-page" class="row-fluid">
    <ul class="nav nav-tabs">
        <li class="services-tab tab active"><a>Services</a></li>
        <li class="categories-tab tab"><a>Categories</a></li>
    </ul>
    
    <?php 
        // -------------------------------------------------------------- 
        //        
        // SERVICES TAB 
        // 
        // --------------------------------------------------------------
    ?>
    <div id="services" class="tab-content">
        <?php // FILTER SERVICES ?>
        <div id="filter-services" class="filter-records column span4">
            <form class="input-append">
                <input class="key span7" type="text" />
                <button class="filter btn" type="submit">
                    <i class="icon-filter"></i>
                    Filter
                </button>
                <button class="clear btn" type="button">
                    <i class="icon-remove-circle"></i>
                    Clear
                </button>
            </form>
            
            <h2>Services</h2>
            <div class="results"></div>
        </div>

        <div class="details column span7">
            <div class="btn-toolbar">
                <div class="add-edit-delete-group btn-group">
                    <button id="add-service" class="btn btn-primary">
                        <i class="icon-plus icon-white"></i>
                        Add</button>
                    <button id="edit-service" class="btn" disabled="disabled">
                        <i class="icon-pencil"></i>
                        Edit</button>
                    <button id="delete-service" class="btn" disabled="disabled">
                        <i class="icon-remove"></i>
                        Delete</button>
                </div>

                <div class="save-cancel-group btn-group" style="display:none;">
                    <button id="save-service" class="btn">
                        <i class="icon-ok"></i>
                        Save</button>
                    <button id="cancel-service" class="btn">
                        <i class="icon-ban-circle"></i>
                        Cancel</button>
                </div>
            </div>
            
            <h2>Details</h2>
            <div class="form-message alert" style="display:none;"></div>
            
            <input type="hidden" id="service-id" />
            
            <label for="service-name">Name *</label>
            <input type="text" id="service-name" class="span6 required" />
            
            <label for="service-duration">Duration (Minutes) *</label>
            <input type="text" id="service-duration" class="required"  />
            
            <label for="service-price">Price *</label>
            <input type="text" id="service-price" class="required" />
            
            <label for="service-currency">Currency</label>
            <input type="text" id="service-currency" class="" />
            
            <label for="service-category">Category</label>
            <select id="service-category"></select>
            
            <label for="service-description">Description</label>
            <textarea id="service-description" rows="4" class="span6"></textarea>
            
            <br/><br/>
            <em id="form-message" class="text-error">Fields with * are required!</em>
        </div>
    </div>
    
    <?php 
        // -------------------------------------------------------------- 
        //        
        // CATEGORIES TAB 
        // 
        // --------------------------------------------------------------
    ?>
    <div id="categories" class="tab-content" style="display:none;">
        <div id="filter-categories" class="filter-records column span4">
            <form class="input-append">
                <input class="key span7" type="text" class="" />
                <button class="filter btn" type="submit">
                    <i class="icon-filter"></i>
                    Filter
                </button>
                <button class="clear btn" type="button">
                    <i class="icon-remove-circle"></i>
                    Clear
                </button>
            </form>

            <h2>Categories</h2>
            <div class="results"></div>
        </div>

        <div class="details span7">
            <div class="btn-toolbar">
                <div class="add-edit-delete-group btn-group">
                    <button id="add-category" class="btn btn-primary">
                        <i class="icon-plus icon-white"></i>
                        Add</button>
                    <button id="edit-category" class="btn" disabled="disabled">
                        <i class="icon-pencil"></i>
                        Edit</button>
                    <button id="delete-category" class="btn" disabled="disabled">
                        <i class="icon-remove"></i>
                        Delete</button>
                </div>

                <div class="save-cancel-group btn-group" style="display:none;">
                    <button id="save-category" class="btn">
                        <i class="icon-ok"></i>
                        Save</button>
                    <button id="cancel-category" class="btn">
                        <i class="icon-ban-circle"></i>
                        Cancel</button>
                </div>
            </div>
            
            <h2>Details</h2>
            <div class="form-message alert" style="display:none;"></div>
            
            <input type="hidden" id="category-id" />
            
            <label for="category-name">Name *</label>
            <input type="text" id="category-name" class="span7 required" />
            
            <label for="category-description">Description</label>
            <textarea id="category-description" rows="4" class="span7"></textarea>
        </div>
    </div>
</div>