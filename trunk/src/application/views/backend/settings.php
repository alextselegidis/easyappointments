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
        <li class="general-tab tab"><a>General</a></li>
        <?php } ?>
        
        <?php if ($privileges[PRIV_SYSTEM_SETTINGS]['view'] == TRUE) { ?>
        <li class="business-logic-tab tab"><a>Business Logic</a></li>
        <?php } ?>
        
        <?php if ($privileges[PRIV_USER_SETTINGS]['view'] == TRUE) { ?>
        <li class="user-tab tab"><a>Current User</a></li>
        <?php } ?>
        
        <li class="about-tab tab"><a>About</a></li>
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
                    General Settings
                    <?php if ($privileges[PRIV_SYSTEM_SETTINGS]['edit'] == TRUE) { ?>
                    <button type="button" class="save-settings btn btn-primary btn-mini">Save</button>
                    <?php } ?>
                </legend>
                
                <div class="wrapper">
                    <label for="company-name">Company Name *</label>
                    <input type="text" id="company-name" data-field="company_name" class="required span12">
                    <span class="help-block">Company name will be displayed everywhere on the system 
                        (required).</span>

                    <br>

                    <label for="company-email">Company Email *</label>
                    <input type="text" id="company-email" data-field="company_email" class="required span12">
                    <span class="help-block">This will be the company email address. It will be used 
                        as the sender and the reply address of the system emails (required).</span>

                    <br>

                    <label for="company-link">Company Link *</label>
                    <input type="text" id="company-link" data-field="company_link" class="required span12">
                    <span class="help-block">Company link should point to the official website of 
                        the company (optional).</span>

                    <br>

                    <a href="<?php echo $this->config->base_url(); ?>" target="_blank" class="btn btn-info">
                        <i class="icon-calendar icon-white"></i>
                        Go To Booking Page
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
                    Business Logic
                    <?php if ($privileges[PRIV_SYSTEM_SETTINGS]['edit'] == TRUE) { ?>
                    <button type="button" class="save-settings btn btn-primary btn-mini">Save</button>
                    <?php } ?>
                </legend>
                
                <div class="row-fluid">
                    <div class="span7 working-plan-wrapper">
                        <h4>Working Plan</h4>
                        <span class="help-block">
                            Mark below the days and hours that your company will accept appointments. 
                            You will be able to adjust appointments in non working hours but the customers
                            will not be able to book appointments by themselves in non working periods. 
                            <strong>This working plan will be the default for every new provider record but 
                            you will be able to change each provider's plan separately by editing his 
                            record.</strong> After that you can add break periods.
                        </span>
                        
                        <table class="working-plan table table-striped">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Start</th>
                                    <th>End</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><label class="checkbox"><input type="checkbox" id="monday" />Monday</label></td>
                                    <td><input type="text" id="monday-start" class="work-start" /></td>
                                    <td><input type="text" id="monday-end" class="work-end" /></td>
                                </tr>
                                <tr>
                                    <td><label class="checkbox"><input type="checkbox" id="tuesday" />Tuesday</label></td>
                                    <td><input type="text" id="tuesday-start" class="work-start" /></td>
                                    <td><input type="text" id="tuesday-end" class="work-end" /></td>
                                </tr>
                                <tr>
                                    <td><label class="checkbox"><input type="checkbox" id="wednesday" />Wednesday</label></td>
                                    <td><input type="text" id="wednesday-start" class="work-start" /></td>
                                    <td><input type="text" id="wednesday-end" class="work-end" /></td>
                                </tr>
                                <tr>
                                    <td><label class="checkbox"><input type="checkbox" id="thursday" />Thursday</label></td>
                                    <td><input type="text" id="thursday-start" class="work-start" /></td>
                                    <td><input type="text" id="thursday-end" class="work-end" /></td>
                                </tr>
                                <tr>
                                    <td><label class="checkbox"><input type="checkbox" id="friday" />Friday</label></td>
                                    <td><input type="text" id="friday-start" class="work-start" /></td>
                                    <td><input type="text" id="friday-end" class="work-end" /></td>
                                </tr>
                                <tr>
                                    <td><label class="checkbox"><input type="checkbox" id="saturday" />Saturday</label></td>
                                    <td><input type="text" id="saturday-start" class="work-start" /></td>
                                    <td><input type="text" id="saturday-end" class="work-end" /></td>
                                </tr>
                                <tr>
                                    <td><label class="checkbox"><input type="checkbox" id="sunday" />Sunday</label></td>
                                    <td><input type="text" id="sunday-start" class="work-start" /></td>
                                    <td><input type="text" id="sunday-end" class="work-end" /></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <br>
                        
                        <h4>Book Advance Timeout</h4>
                        <span class="help-block">
                            Define the timeout (in minutes) before the customers can book or re-arrange
                            appointments with the company. 
                        </span>
                        
                        <label for="book-advance-timeout">Timeout (Minutes)</label>
                        <input type="text" id="book-advance-timeout" data-field="book_advance_timeout" />
                        
                    </div>
                    <div class="span5 breaks-wrapper">
                        <h4>Breaks</h4>

                        <span class="help-block">
                            Add the working breaks during each day. These breaks will be applied for 
                            all new providers. 
                        </span>

                        <div>
                            <button type="button" class="add-break btn btn-primary">
                                <i class="icon-white icon-plus"></i>
                                Add Break
                            </button>
                        </div>

                        <br>

                        <table class="breaks table table-striped">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Actions</th>
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
                    Personal Info 
                    <?php if ($privileges[PRIV_USER_SETTINGS]['edit'] == TRUE) { ?>
                    <button type="button" class="save-settings btn btn-primary btn-mini">Save</button>
                    <?php } ?>
                </legend>
                
                <input type="hidden" id="user-id" />
                
                <label for="first-name">First Name *</label>
                <input type="text" id="first-name" class="span9 required" />
                
                <label for="last-name">Last Name *</label>
                <input type="text" id="last-name" class="span9 required" />
                
                <label for="email">Email *</label>
                <input type="text" id="email" class="span9 required" />
                
                <label for="mobile-number">Mobile Number</label>
                <input type="text" id="mobile-number" class="span9" />
                
                <label for="phone-number">Phone Number *</label>
                <input type="text" id="phone-number" class="span9 required" />
                
                <label for="address">Address</label>
                <input type="text" id="address" class="span9" />
                
                <label for="city">City</label>
                <input type="text" id="city" class="span9" />
                
                <label for="state">State</label>
                <input type="text" id="state" class="span9" />
                
                <label for="zip-code">Zip Code</label>
                <input type="text" id="zip-code" class="span9" />
                
                <label for="notes">Notes</label>
                <textarea id="notes" class="span9" rows="3"></textarea>
            </fieldset>
            
            <fieldset class="span5 miscellaneous-wrapper">
                <legend>Miscellaneous</legend>
                
                <label for="username">Username *</label>
                <input type="text" id="username" class="required" />
                
                <label for="password">Password</label>
                <input type="password" id="password" />
                
                <label for="retype-password">Retype Password</label>
                <input type="password" id="retype-password" />
                
                <br>
                
                <button type="button" id="user-notifications" class="btn" data-toggle="button">
                    <i class="icon-envelope"></i>
                    Receive Notifications
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
        <h2>Easy!Appointments</h3>
        <p>
            <strong>Easy!Appointments</strong> is a highly customizable web application that allows 
            your customers to book appointments with you via the web. Moreover, it 
            provides the ability to sync your data with Google Calendar so you can 
            use them with other services. It is an open source project and you can 
            download and install it even for <strong>commercial use</strong>. Easy!Appointments will 
            run smoothly with your existing website, because it can be installed in 
            a single folder of the server and of course, both sites can share the same 
            database.
        </p>
        
        <div class="current-version"> 
            Current Version 
            <?php 
                echo $this->config->item('ea_version') 
                        . ' ' . $this->config->item('ea_release_title'); 
            ?>
        </div>
        
		<br>
		
        <h3>Support</h3>
        <p>
            If you encounter any problems when using Easy!Appointments you can search the 
            official Google Group for answers. You might also want to create a new issue
            on the Google Code page in order to help the development progress.
            <br><br>
            <a href="https://plus.google.com/communities/105333709485142846840">
                Google+ Community</a>
            | 
            <a href="https://groups.google.com/forum/#!forum/easy-appointments">
                Support Group</a>
            |
            <a href="https://code.google.com/p/easy-appointments/issues/list">Project Issues</a>
        </p>
		
		<br>
		
		<h3>License</h3>
		<p>
            Easy!Appointments is licensed under the GPLv3 license. By using 
            the code of Easy!Appointments in any way <br> you are agreeing to the 
            terms described in the following url:
            <a href="http://www.gnu.org/copyleft/gpl.html">http://www.gnu.org/copyleft/gpl.html</a>
        </p>
    </div>
</div>