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
        <li class="services-tab tab active"><a><?php echo $this->lang->line('services'); ?></a></li>
        <li class="categories-tab tab"><a><?php echo $this->lang->line('categories'); ?></a></li>
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
                <input class="key span12" type="text" />
                <button class="filter btn" type="submit" title="<?php echo $this->lang->line('filter'); ?>">
                    <i class="icon-search"></i>
                </button>
                <button class="clear btn" type="button" title="<?php echo $this->lang->line('clear'); ?>">
                    <i class="icon-repeat"></i>
                </button>
            </form>
            
            <h2><?php echo $this->lang->line('services'); ?></h2>
            <div class="results"></div>
        </div>

        <div class="details column span7">
            <div class="btn-toolbar">
                <div class="add-edit-delete-group btn-group">
                    <button id="add-service" class="btn btn-primary">
                        <i class="icon-plus icon-white"></i>
                        <?php echo $this->lang->line('add'); ?>
                    </button>
                    <button id="edit-service" class="btn" disabled="disabled">
                        <i class="icon-pencil"></i>
                        <?php echo $this->lang->line('edit'); ?>
                    </button>
                    <button id="delete-service" class="btn" disabled="disabled">
                        <i class="icon-remove"></i>
                        <?php echo $this->lang->line('delete'); ?>
                    </button>
                </div>

                <div class="save-cancel-group btn-group" style="display:none;">
                    <button id="save-service" class="btn btn-primary">
                        <i class="icon-ok icon-white"></i>
                        <?php echo $this->lang->line('save'); ?>
                    </button>
                    <button id="cancel-service" class="btn">
                        <i class="icon-ban-circle"></i>
                        <?php echo $this->lang->line('cancel'); ?>
                    </button>
                </div>
            </div>
            
            <h2><?php echo $this->lang->line('details'); ?></h2>
            <div class="form-message alert" style="display:none;"></div>
            
            <input type="hidden" id="service-id" />
            
            <label for="service-name"><?php echo $this->lang->line('name'); ?> *</label>
            <input type="text" id="service-name" class="span12 required" />
            
            <label for="service-duration"><?php echo $this->lang->line('duration_minutes'); ?> *</label>
            <input type="text" id="service-duration" class="required"  />
            
            <label for="service-price"><?php echo $this->lang->line('price'); ?> *</label>
            <input type="text" id="service-price" class="span12 required" />
            
            <label for="service-currency"><?php echo $this->lang->line('currency'); ?></label>
            <input type="text" id="service-currency" class="span12" />
            
            <label for="service-category"><?php echo $this->lang->line('category'); ?></label>
            <select id="service-category" class="span12"></select>
            
            <label for="service-description"><?php echo $this->lang->line('description'); ?></label>
            <textarea id="service-description" rows="4" class="span12"></textarea>
            
            <br/><br/>
            <em id="form-message" class="text-error">
                <?php echo $this->lang->line('fields_are_required'); ?></em>
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
                <input class="key span12" type="text" />
                <button class="filter btn" type="submit" title="<?php echo $this->lang->line('filter'); ?>">
                    <i class="icon-search"></i>
                </button>
                <button class="clear btn" type="button" title="<?php echo $this->lang->line('clear'); ?>">
                    <i class="icon-repeat"></i>
                </button>
            </form>

            <h2><?php echo $this->lang->line('categories'); ?></h2>
            <div class="results"></div>
        </div>

        <div class="details span7">
            <div class="btn-toolbar">
                <div class="add-edit-delete-group btn-group">
                    <button id="add-category" class="btn btn-primary">
                        <i class="icon-plus icon-white"></i>
                        <?php echo $this->lang->line('add'); ?>
                    </button>
                    <button id="edit-category" class="btn" disabled="disabled">
                        <i class="icon-pencil"></i>
                        <?php echo $this->lang->line('edit'); ?>
                    </button>
                    <button id="delete-category" class="btn" disabled="disabled">
                        <i class="icon-remove"></i>
                        <?php echo $this->lang->line('delete'); ?>
                    </button>
                </div>

                <div class="save-cancel-group btn-group" style="display:none;">
                    <button id="save-category" class="btn btn-primary">
                        <i class="icon-ok icon-white"></i>
                        <?php echo $this->lang->line('save'); ?>
                    </button>
                    <button id="cancel-category" class="btn">
                        <i class="icon-ban-circle"></i>
                        <?php echo $this->lang->line('cancel'); ?>
                    </button>
                </div>
            </div>
            
            <h2><?php echo $this->lang->line('details'); ?></h2>
            <div class="form-message alert" style="display:none;"></div>
            
            <input type="hidden" id="category-id" />
            
            <label for="category-name"><?php echo $this->lang->line('name'); ?> *</label>
            <input type="text" id="category-name" class="span12 required" />
            
            <label for="category-description"><?php echo $this->lang->line('description'); ?></label>
            <textarea id="category-description" rows="4" class="span12"></textarea>
        </div>
    </div>
</div>