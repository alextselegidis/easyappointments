<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    
    <?php // INCLUDE CSS FILES ?>
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
        href="<?php echo $this->config->base_url(); ?>assets/css/style.css">

    <?php // INCLUDE JS FILES ?>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/libs/jquery/jquery.min.js">
    </script>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/libs/jquery/jquery-ui.min.js">
    </script>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/libs/jquery/jquery.qtip.min.js">
    </script>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/libs/bootstrap/bootstrap.min.js">
    </script>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/libs/date.js">
    </script>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/frontend/book_appointment.js">
    </script>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/general_functions.js">
    </script>
    
    <?php // SET FAVICON FOR PAGE ?>
    <link 
        rel="icon" 
        type="image/x-icon" 
        href="<?php echo $this->config->base_url(); ?>assets/images/favicon.ico">
    
    <?php // JS GLOBAL VARIABLE DECLARATION ?>
    <script type="text/javascript">
        // Define some global variables. 
        GlobalVariables = {
            services    : <?php echo json_encode($available_services); ?>,
            providers   : <?php echo json_encode($available_providers); ?>,
            baseUrl     : <?php echo '"' . $this->config->base_url() . '"'; ?>
        }
    </script>
    
    <?php // JQUERY PAGE STUFF ?>
    <script type="text/javascript">
        $(document).ready(function() {
            bookAppointment.initialize(true); 
            GeneralFunctions.centerElementOnPage($('#book-appointment'));
        });
    </script>
</head>

<body>
    <div id="main" class="container">
        <div id="book-appointment">
            <div id="top-bar">
                <span id="business-name"><?php echo $company_name; ?></span>
                <div id="book-steps">
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

            <?php // SELECT SERVICE AND PROVIDER ?>
            <div id="book-appointment-1" class="book-appoinment-step">
                <div class="step-frame">
                    <h2 class="step-title">Select Service & Provider</h2>
                    <div class="step-content"  style="width:270px">
                        <label for="select-service"><strong>Select Service</strong></label>
                        <select id="select-service">
                            <?php 
                                foreach($available_services as $service) {
                                    echo '<option value="' . $service['id'] . '">' 
                                            . $service['name'] . '</option>';
                                }
                            ?>
                        </select>

                        <label for="select-provider"><strong>Select Provider</strong></label>
                        <select id="select-provider"></select>
                    </div>
                </div>
                
                <div class="command-buttons">
                    <button type="button" id="button-next-1" class="btn button-next btn-primary" 
                            data-step_index="1">Next <i class="icon-forward icon-white"></i></button>
                </div>
            </div>

            <?php // APPOINTMENT DATE ?>
            <div id="book-appointment-2" class="book-appoinment-step" style="display:none;">
                <div class="step-frame">
                    <h2 class="step-title">Select Appointment Date And Time</h2>
                    <div class="step-content" style="width:600px">
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

            <?php // CUSTOMER'S INFO ?>
            <div id="book-appointment-3" class="book-appoinment-step" style="display:none;">
                <div class="step-frame">
                    <h2 class="step-title">Fill In Your Information</h2>
                    <div class="step-content" style="width:600px">
                        <div class="span3">
                            <label for="last-name">Last Name *</label>
                            <input type="text" id="last-name" class="required" maxlength="250" />

                            <label for="first-name">First Name</label>
                            <input type="text" id="first-name" maxlength="100" />

                            <label for="email">Email *</label>
                            <input type="text" id="email" class="required" maxlength="250" />

                            <label for="phone-number">Phone Number *</label>
                            <input type="text" id="phone-number" class="required" maxlength="60" />

                            <br/><br/>
                            <em class="text-error">Fields with * are mandatory.</em>
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

            <?php // CONFIRMATION STEP ?>
            <div id="book-appointment-4" class="book-appoinment-step" style="display:none;">
                <div class="step-frame">
                    <h2 class="step-title">Confirm Appointment</h2>
                    <div class="step-content" style="width:600px">
                        <div id="appointment-info" class="span3"></div>
                        <div id="customer-info" class="span3"></div>
                    </div>
                </div>
                
                <div class="command-buttons">
                    <button type="button" id="button-back-4" class="btn button-back" 
                            data-step_index="4"><i class="icon-backward"></i> Back</button>
                    <form id="book-appointment-form" style="display:inline-block" method="post">
                        <button type="submit" class="btn btn-success">
                            <i class="icon-ok icon-white"></i>Confirm</button>
                        <input type="hidden" name="post_data" />
                    </form>
                </div>
            </div>
            
            <div id="frame-footer">
                Powered By <a href="https://code.google.com/p/easy-appointments/">Easy!Appointments</a>
            </div>
        </div>
    </div>
</body>
</html>