<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/backend_settings.js"></script>
<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/working_plan.js"></script>
<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/libs/jquery/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/libs/jquery/jquery.jeditable.min.js"></script>
        
<script type="text/javascript">    
    var GlobalVariables = {
        'baseUrl': <?php echo '"' . $base_url . '"'; ?>,
        'userSlug': <?php echo '"' . $role_slug . '"'; ?>,
        'settings': {
            'system': <?php echo json_encode($system_settings); ?>,
            'user': <?php echo json_encode($user_settings); ?>
        },
        'user'                  : {
            'id'        : <?php echo $user_id; ?>,
            'email'     : <?php echo '"' . $user_email . '"'; ?>,
            'role_slug' : <?php echo '"' . $role_slug . '"'; ?>,
            'privileges': <?php echo json_encode($privileges); ?>
        }
    };
    
    $(document).ready(function() {
        BackendSettings.initialize(true);
    });
</script>

<div id="settings-page" class="row-fluid">
    <ul class="nav nav-tabs">
        <?php if ($privileges[PRIV_SYSTEM_SETTINGS]['view'] == TRUE) { ?>
        <li class="general-tab tab"><a><?php echo $this->lang->line('general'); ?></a></li>
        <?php } ?>
        
        <?php if ($privileges[PRIV_SYSTEM_SETTINGS]['view'] == TRUE) { ?>
        <li class="business-logic-tab tab"><a><?php echo $this->lang->line('business_logic'); ?></a></li>
        <?php } ?>
        
        <?php if ($privileges[PRIV_USER_SETTINGS]['view'] == TRUE) { ?>
        <li class="user-tab tab"><a><?php echo $this->lang->line('current_user'); ?></a></li>
        <?php } ?>
        
        <li class="about-tab tab"><a><?php echo $this->lang->line('about_ea'); ?></a></li>
    </ul>
    
    <?php 
        // -------------------------------------------------------------- 
        //        
        // GENERAL TAB 
        // 
        // --------------------------------------------------------------
    ?>
    <?php $hidden = ($privileges[PRIV_SYSTEM_SETTINGS]['view'] == TRUE) ? '' : 'hidden'; ?>
    <div id="general" class="tab-content <?php echo $hidden; ?>">
        <form>
            <fieldset>
                <legend>
                    <?php echo $this->lang->line('general_settings'); ?>
                    <?php if ($privileges[PRIV_SYSTEM_SETTINGS]['edit'] == TRUE) { ?>
                    <button type="button" class="save-settings btn btn-primary btn-mini">
                        <?php echo $this->lang->line('save'); ?>
                    </button>
                    <?php } ?>
                </legend>
                
                <div class="wrapper">
                    <label for="company-name"><?php echo $this->lang->line('company_name'); ?> *</label>
                    <input type="text" id="company-name" data-field="company_name" class="required span12">
                    <span class="help-block">
                        <?php echo $this->lang->line('company_name_hint'); ?>
                    </span>

                    <br>

                    <label for="company-email"><?php echo $this->lang->line('company_email'); ?> *</label>
                    <input type="text" id="company-email" data-field="company_email" class="required span12">
                    <span class="help-block">
                        <?php echo $this->lang->line('company_email_hint'); ?>
                    </span>

                    <br>

                    <label for="company-link"><?php echo $this->lang->line('company_link'); ?> *</label>
                    <input type="text" id="company-link" data-field="company_link" class="required span12">
                    <span class="help-block">
                        <?php echo $this->lang->line('company_link_hint'); ?>
                    </span>

                    <br>

                    <a href="<?php echo $this->config->base_url(); ?>" target="_blank" class="btn btn-info">
                        <i class="icon-calendar icon-white"></i>
                        <?php echo $this->lang->line('go_to_booking_page'); ?>
                    </a>
                </div>
            </fieldset>
        </form>
    </div>
    
    <?php 
        // -------------------------------------------------------------- 
        //        
        // BUSINESS LOGIC TAB 
        // 
        // --------------------------------------------------------------
    ?>
    <?php $hidden = ($privileges[PRIV_SYSTEM_SETTINGS]['view'] == TRUE) ? '' : 'hidden'; ?>
    <div id="business-logic" class="tab-content <?php echo $hidden; ?>">
        <form>
            <fieldset>
                <legend>
                    <?php echo $this->lang->line('business_logic'); ?>
                    <?php if ($privileges[PRIV_SYSTEM_SETTINGS]['edit'] == TRUE) { ?>
                    <button type="button" class="save-settings btn btn-primary btn-mini">
                        <?php echo $this->lang->line('save'); ?>
                    </button>
                    <?php } ?>
                </legend>
                
                <div class="row-fluid">
                    <div class="span7 working-plan-wrapper">
                        <h4><?php echo $this->lang->line('working_plan'); ?></h4>
                        <span class="help-block">
                            <?php echo $this->lang->line('edit_working_plan_hint'); ?>
                        </span>
                        
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
                                    <td><label class="checkbox"><input type="checkbox" id="monday" /><?php echo $this->lang->line('monday'); ?></label></td>
                                    <td><input type="text" id="monday-start" class="work-start" /></td>
                                    <td><input type="text" id="monday-end" class="work-end" /></td>
                                </tr>
                                <tr>
                                    <td><label class="checkbox"><input type="checkbox" id="tuesday" /><?php echo $this->lang->line('tuesday'); ?></label></td>
                                    <td><input type="text" id="tuesday-start" class="work-start" /></td>
                                    <td><input type="text" id="tuesday-end" class="work-end" /></td>
                                </tr>
                                <tr>
                                    <td><label class="checkbox"><input type="checkbox" id="wednesday" /><?php echo $this->lang->line('wednesday'); ?></label></td>
                                    <td><input type="text" id="wednesday-start" class="work-start" /></td>
                                    <td><input type="text" id="wednesday-end" class="work-end" /></td>
                                </tr>
                                <tr>
                                    <td><label class="checkbox"><input type="checkbox" id="thursday" /><?php echo $this->lang->line('thursday'); ?></label></td>
                                    <td><input type="text" id="thursday-start" class="work-start" /></td>
                                    <td><input type="text" id="thursday-end" class="work-end" /></td>
                                </tr>
                                <tr>
                                    <td><label class="checkbox"><input type="checkbox" id="friday" /><?php echo $this->lang->line('friday'); ?></label></td>
                                    <td><input type="text" id="friday-start" class="work-start" /></td>
                                    <td><input type="text" id="friday-end" class="work-end" /></td>
                                </tr>
                                <tr>
                                    <td><label class="checkbox"><input type="checkbox" id="saturday" /><?php echo $this->lang->line('saturday'); ?></label></td>
                                    <td><input type="text" id="saturday-start" class="work-start" /></td>
                                    <td><input type="text" id="saturday-end" class="work-end" /></td>
                                </tr>
                                <tr>
                                    <td><label class="checkbox"><input type="checkbox" id="sunday" /><?php echo $this->lang->line('sunday'); ?></label></td>
                                    <td><input type="text" id="sunday-start" class="work-start" /></td>
                                    <td><input type="text" id="sunday-end" class="work-end" /></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <br>
                        
                        <h4><?php echo $this->lang->line('book_advance_timeout'); ?></h4>
                        <span class="help-block">
                            <?php echo $this->lang->line('book_advance_timeout_hint'); ?> 
                        </span>
                        
                        <label for="book-advance-timeout"><?php echo $this->lang->line('timeout_minutes'); ?></label>
                        <input type="text" id="book-advance-timeout" data-field="book_advance_timeout" />
                        
                    </div>
                    <div class="span5 breaks-wrapper">
                        <h4><?php echo $this->lang->line('breaks'); ?></h4>

                        <span class="help-block">
                            <?php echo $this->lang->line('edit_breaks_hint'); ?>
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
                                    <th><?php echo $this->lang->line('day'); ?></th>
                                    <th><?php echo $this->lang->line('start'); ?></th>
                                    <th><?php echo $this->lang->line('end'); ?></th>
                                    <th><?php echo $this->lang->line('actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
    
    <?php 
        // -------------------------------------------------------------- 
        //        
        // USER TAB 
        // 
        // --------------------------------------------------------------
    ?>
    <?php $hidden = ($privileges[PRIV_USER_SETTINGS]['view'] == TRUE) ? '' : 'hidden'; ?>
    <div id="user" class="tab-content <?php echo $hidden; ?>">
        <form class="row-fluid">
            <fieldset class="span5 personal-info-wrapper">
                <legend>
                    <?php echo $this->lang->line('personal_information'); ?>
                    <?php if ($privileges[PRIV_USER_SETTINGS]['edit'] == TRUE) { ?>
                    <button type="button" class="save-settings btn btn-primary btn-mini">
                        <?php echo $this->lang->line('save'); ?>
                    </button>
                    <?php } ?>
                </legend>
                
                <input type="hidden" id="user-id" />
                
                <label for="first-name"><?php echo $this->lang->line('first_name'); ?> *</label>
                <input type="text" id="first-name" class="span9 required" />
                
                <label for="last-name"><?php echo $this->lang->line('last_name'); ?> *</label>
                <input type="text" id="last-name" class="span9 required" />
                
                <label for="email"><?php echo $this->lang->line('email'); ?> *</label>
                <input type="text" id="email" class="span9 required" />
                
                <label for="mobile-number"><?php echo $this->lang->line('mobile_number'); ?></label>
                <input type="text" id="mobile-number" class="span9" />
                
                <label for="phone-number"><?php echo $this->lang->line('phone_number'); ?> *</label>
                <input type="text" id="phone-number" class="span9 required" />
                
                <label for="address"><?php echo $this->lang->line('address'); ?></label>
                <input type="text" id="address" class="span9" />
                
                <label for="city"><?php echo $this->lang->line('city'); ?></label>
                <input type="text" id="city" class="span9" />
                
                <label for="state"><?php echo $this->lang->line('state'); ?></label>
                <input type="text" id="state" class="span9" />
                
                <label for="zip-code"><?php echo $this->lang->line('zip_code'); ?></label>
                <input type="text" id="zip-code" class="span9" />
                
                <label for="notes"><?php echo $this->lang->line('notes'); ?></label>
                <textarea id="notes" class="span9" rows="3"></textarea>
            </fieldset>
            
            <fieldset class="span5 miscellaneous-wrapper">
                <legend><?php echo $this->lang->line('system_login'); ?></legend>
                
                <label for="username"><?php echo $this->lang->line('username'); ?> *</label>
                <input type="text" id="username" class="required" />
                
                <label for="password"><?php echo $this->lang->line('password'); ?></label>
                <input type="password" id="password" />
                
                <label for="retype-password"><?php echo $this->lang->line('retype_password'); ?></label>
                <input type="password" id="retype-password" />
                
                <br>
                
                <button type="button" id="user-notifications" class="btn" data-toggle="button">
                    <i class="icon-envelope"></i>
                    <?php echo $this->lang->line('receive_notifications'); ?>
                </button>
            </fieldset>
        </form>
    </div>
    
    <?php 
        // -------------------------------------------------------------- 
        //        
        // ABOUT TAB 
        // 
        // --------------------------------------------------------------
    ?>
    <div id="about" class="tab-content">
        <h2>Easy!Appointments</h2>
        <p>
            <?php echo $this->lang->line('about_ea_info'); ?>
        </p>
        
        <br>
        
        <div class="current-version"> 
            <?php 
                echo $this->lang->line('current_version') . ' ';
                echo $this->config->item('ea_version');
                $release_title = $this->config->item('ea_release_title');
                if ($release_title != '') {
                    echo ' - ' . $release_title;
                }
            ?>
        </div>
        
		<br>
		
        <h3><?php echo $this->lang->line('support'); ?></h3>
        <p>
            <?php echo $this->lang->line('about_ea_support'); ?>
            <br><br>
            <a href="http://easyappointments.org">
                <?php echo $this->lang->line('official_website'); ?>
            </a>
            | 
            <a href="https://plus.google.com/communities/105333709485142846840">
                <?php echo $this->lang->line('google_plus_community'); ?>
            </a>
            | 
            <a href="https://groups.google.com/forum/#!forum/easy-appointments">
                <?php echo $this->lang->line('support_group'); ?>
            </a>
            |
            <a href="https://code.google.com/p/easy-appointments/issues/list">
                <?php echo $this->lang->line('project_issues'); ?>
            </a>
            |
            <a href="http://easyappointments.wordpress.com">
                E!A Blog
            </a>
        </p>
		
		<br>
		
		<h3><?php echo $this->lang->line('license'); ?></h3>
		<p>
            <?php echo $this->lang->line('about_ea_license'); ?>
            <a href="http://www.gnu.org/copyleft/gpl.html">http://www.gnu.org/copyleft/gpl.html</a>
        </p>
    </div>
</div>