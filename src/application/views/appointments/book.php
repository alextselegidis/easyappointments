<!DOCTYPE html>
<html>
<head>
    <title>Book Appointment With <?php echo $company_name; ?></title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    
    <?php
        // ------------------------------------------------------------
        // INCLUDE CSS FILES 
        // ------------------------------------------------------------ ?>
    <link 
        rel="stylesheet" 
        type="text/css" 
        href="<?php echo $this->config->base_url(); ?>assets/css/libs/bootstrap/bootstrap.css">
    <link 
        rel="stylesheet" 
        type="text/css" 
        href="<?php echo $this->config->base_url(); ?>assets/css/libs/bootstrap/bootstrap-responsive.css">
    <link 
        rel="stylesheet" 
        type="text/css" 
        href="<?php echo $this->config->base_url(); ?>assets/css/libs/jquery/jquery-ui.min.css">
    <link 
        rel="stylesheet" 
        type="text/css" 
        href="<?php echo $this->config->base_url(); ?>assets/css/libs/jquery/jquery.qtip.min.css">
    <link 
        rel="stylesheet" 
        type="text/css" 
        href="<?php echo $this->config->base_url(); ?>assets/css/frontend.css">
    <link 
        rel="stylesheet" 
        type="text/css" 
        href="<?php echo $this->config->base_url(); ?>assets/css/general.css">

    <?php
        // ------------------------------------------------------------
        // INCLUDE JAVASCRIPT FILES 
        // ------------------------------------------------------------ ?>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/libs/jquery/jquery.min.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/libs/jquery/jquery-ui.min.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/libs/jquery/jquery.qtip.min.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/libs/bootstrap/bootstrap.min.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/libs/date.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/frontend_book.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/general_functions.js"></script>
    
    <?php
        // ------------------------------------------------------------
        // WEBPAGE FAVICON
        // ------------------------------------------------------------ ?>
    <link 
        rel="icon" 
        type="image/x-icon" 
        href="<?php echo $this->config->base_url(); ?>assets/images/favicon.ico">
    
    <?php
        // ------------------------------------------------------------
        // VIEW FILE JAVASCRIPT CODE
        // ------------------------------------------------------------ ?>
    <script type="text/javascript">
        GlobalVariables = {
            availableServices   : <?php echo json_encode($available_services); ?>,
            availableProviders  : <?php echo json_encode($available_providers); ?>,
            baseUrl             : <?php echo '"' . $this->config->base_url() . '"'; ?>,
            manageMode          : <?php echo ($manage_mode) ? 'true' : 'false'; ?>,
            appointmentData     : <?php echo json_encode($appointment_data); ?>,
            providerData        : <?php echo json_encode($provider_data); ?>,
            customerData        : <?php echo json_encode($customer_data); ?>
        };
        
        $(document).ready(function() {
            FrontendBook.initialize(true, GlobalVariables.manageMode); 
            GeneralFunctions.centerElementOnPage($('#book-appointment-wizard'));
        });
    </script>
</head>

<body>
    <div id="main" class="container">
        
        <div id="book-appointment-wizard">
            
            <?php 
                // ------------------------------------------------------
                // FRAME TOP BAR 
                // ------------------------------------------------------ ?>
            <div id="header">
                <span id="company-name"><?php echo $company_name; ?></span>
                
                <div id="steps">
                    <div id="step-1" class="book-step active-step" title="Select Service & Provider">
                        <strong>1</strong>
                    </div>
                    
                    <div id="step-2" class="book-step" title="Select Appointment Date">
                        <strong>2</strong>
                    </div>
                    <div id="step-3" class="book-step" title="Your Information">
                        <strong>3</strong>
                    </div>
                    <div id="step-4" class="book-step" title="Confirm Appointment">
                        <strong>4</strong>
                    </div>
                </div>
            </div>
            
            <?php
                // ------------------------------------------------------
                // CANCEL APPOINTMENT BUTTON
                // ------------------------------------------------------
                if ($manage_mode === TRUE) {
                    echo '
                        <div id="cancel-appointment-frame">
                            <p>
                                Press the "Cancel" button to remove the appointment
                                from the company schedule.
                            </p>
                            <form id="cancel-appointment-form" method="post" 
                                    action="' . $this->config->item('base_url') 
                                    . 'appointments/cancel/' . $appointment_data['hash'] . '">
                                <textarea name="cancel_reason" style="display:none"></textarea>
                                <button id="cancel-appointment" class="btn btn-inverse">
                                        Cancel</button>
                            </form>
                        </div>';
                }   
            ?>
            
            <?php 
                // ------------------------------------------------------
                // DISPLAY EXCEPTIONS (IF ANY)
                // ------------------------------------------------------
                if (isset($exceptions)) {
                    echo '<div style="margin: 10px">';
                    echo '<h4>Unexpected Errors</h4>';
                    foreach($exceptions as $exception) {
                        echo exceptionToHtml($exception);
                    }
                    echo '</div>';
                }
            ?>            
            <?php 
                // ------------------------------------------------------
                // SELECT SERVICE AND PROVIDER 
                // ------------------------------------------------------ ?>
            <div id="wizard-frame-1" class="wizard-frame">                
                <div class="frame-container">
                    <h2 class="frame-title">Select Service & Provider</h2>
                    
                    <div class="frame-content" style="width:520px">
                        <label for="select-service">
                            <strong>Select Service</strong>
                        </label>
                        
                        <select id="select-service">
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

                                    foreach($grouped_services as $group) {
                                        $group_label = ($group[0]['category_name'] != NULL) 
                                                ? $group[0]['category_name'] : 'Uncategorized'; 

                                        echo '<optgroup label="' . $group_label . '">';
                                        foreach($group as $service) {
                                            echo '<option value="' . $service['id'] . '">' 
                                                . $service['name'] . '</option>';
                                        }
                                        echo '</optgroup>';
                                    }
                                }  else {
                                    foreach($available_services as $service) {
                                        echo '<option value="' . $service['id'] . '">' 
                                                    . $service['name'] . '</option>';
                                    }
                                }
                            ?>
                        </select>

                        <label for="select-provider">
                            <strong>Select Provider</strong>
                        </label>
                        
                        <select id="select-provider"></select>
                        
                        <div id="service-description" style="display:none;"></div>
                    </div>
                </div>
                
                <div class="command-buttons">
                    <button type="button" id="button-next-1" class="btn button-next btn-primary" 
                            data-step_index="1">Next <i class="icon-forward icon-white"></i></button>
                </div>
            </div>

            <?php 
                // ------------------------------------------------------
                // SELECT APPOINTMENT DATE
                // ------------------------------------------------------ ?>
            <div id="wizard-frame-2" class="wizard-frame" style="display:none;">
                <div class="frame-container">
                    
                    <h2 class="frame-title">Select Appointment Date And Time</h2>
                    
                    <div class="frame-content" style="width:600px">
                        <div class="span3">
                            <div id="select-date"></div>
                        </div>

                        <div class="span3">
                            <?php // Available hours are going to be fetched via ajax call. ?>
                            <div id="available-hours"></div>
                        </div>
                    </div>
                </div>
                
                <div class="command-buttons">
                    <button type="button" id="button-back-2" class="btn button-back" 
                            data-step_index="2"><i class="icon-backward"></i> Back</button>
                    <button type="button" id="button-next-2" class="btn button-next btn-primary" 
                            data-step_index="2">Next <i class="icon-forward icon-white"></i></button>
                </div>
            </div>

            <?php 
                // ------------------------------------------------------
                // ENTER CUSTOMER DATA
                // ------------------------------------------------------ ?>
            <div id="wizard-frame-3" class="wizard-frame" style="display:none;">
                <div class="frame-container">
                    
                    <h2 class="frame-title">Fill In Your Information</h2>
                    
                    <div class="frame-content" style="width:600px">
                        <div class="span3">
                            <label for="first-name">First Name *</label>
                            <input type="text" id="first-name" class="required" maxlength="100" />
                            
                            <label for="last-name">Last Name *</label>
                            <input type="text" id="last-name" class="required" maxlength="250" />

                            <label for="email">Email *</label>
                            <input type="text" id="email" class="required" maxlength="250" />

                            <label for="phone-number">Phone Number *</label>
                            <input type="text" id="phone-number" class="required" maxlength="60" />

                            <br/><br/>
                            <em id="form-message" class="text-error">Fields with * are required!</em>
                        </div>

                        <div class="span3">
                            <label for="address">Address</label>
                            <input type="text" id="address" maxlength="250" />

                            <label for="city">City</label>
                            <input type="text" id="city" maxlength="120" />

                            <label for="zip-code">Zip Code</label>
                            <input type="text" id="zip-code" maxlength="120" />

                            <label for="notes">Notes</label>
                            <textarea id="notes" maxlength="500" rows="4"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="command-buttons">
                    <button type="button" id="button-back-3" class="btn button-back" 
                            data-step_index="3"><i class="icon-backward"></i> Back</button>
                    <button type="button" id="button-next-3" class="btn button-next btn-primary" 
                            data-step_index="3">Next <i class="icon-forward icon-white"></i></button>
                </div>
            </div>

            <?php 
                // ------------------------------------------------------
                // APPOINTMENT DATA CONFIRMATION 
                // ------------------------------------------------------ ?>
            <div id="wizard-frame-4" class="wizard-frame" style="display:none;">
                <div class="frame-container">
                    <h2 class="frame-title">Confirm Appointment</h2>
                    <div class="frame-content" style="width:600px">
                        <div id="appointment-details" class="span3"></div>
                        <div id="customer-details" class="span3"></div>
                    </div>
                </div>
                
                <div class="command-buttons">
                    <button type="button" id="button-back-4" class="btn button-back" 
                            data-step_index="4"><i class="icon-backward"></i> Back</button>
                    <form id="book-appointment-form" style="display:inline-block" method="post">
                        <button type="submit" class="btn btn-success">
                            <i class="icon-ok icon-white"></i>
                            <?php
                                echo (!$manage_mode) ? "Confirm" : "Update";
                            ?>
                        </button>
                        <input type="hidden" name="post_data" />
                    </form>
                </div>
            </div>
            
            <?php 
                // ------------------------------------------------------
                // FRAME FOOTER 
                // ------------------------------------------------------ ?>
            <div id="frame-footer">
                Powered By 
                <a href="http://easyappointments.org" target="_blank">
                    Easy!Appointments
                </a>
            </div>
        </div>
    </div>
</body>
</html>