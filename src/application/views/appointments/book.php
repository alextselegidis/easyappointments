<!DOCTYPE html>
<html>
<head>
    <?php // INCLUDE CSS FILES ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config->base_url(); ?>assets/css/libs/bootstrap/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config->base_url(); ?>assets/css/libs/bootstrap/bootstrap-responsive.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config->base_url(); ?>assets/css/libs/jquery/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config->base_url(); ?>assets/css/libs/jquery/jquery.qtip.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config->base_url(); ?>assets/css/style.css">

    <?php // INCLUDE JS FILES ?>
    <script type="text/javascript" src="<?php echo $this->config->base_url(); ?>assets/js/libs/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->config->base_url(); ?>assets/js/libs/jquery/jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->config->base_url(); ?>assets/js/libs/jquery/jquery.qtip.min.js"></script>
    <script type="text/javascript" src="<?php echo $this->config->base_url(); ?>assets/js/libs/bootstrap/bootstrap.min.js"></script>
    
    <?php // SET FAVICON FOR PAGE ?>
    <link rel="icon" type="image/x-icon" href="<?php echo $this->config->base_url(); ?>assets/images/favicon.ico">
    
    <script type="text/javascript">
        $(document).ready(function() {
            
        });
    </script>
</head>

<body>
    <div id="main" class="container">
        <div id="book-appointment">
            <div id="top-bar">
                <span id="business-name"><?php echo $businessName; ?></span>
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
                <h2>Select Service & Provider</h2>
                <label for="select-service">Select Service</label>
                <select id="select-service">
                    <option value="1">General Examination</option>
                    <option value="2">Check Blood Pressure</option>
                </select>

                <label for="select-provider">Select Provider</label>
                <select id="select-provider">
                    <option value="1">Dr. Andy Smith</option>
                    <option value="2">Dr. Peter Grol</option>
                    <option value="3">Dr. Kurt Benstein</option>
                </select>
                
                <button type="button" id="button-next-1" class="btn">Next</button>
            </div>

            <?php // APPOINTMENT DATE ?>
            <div id="book-appointment-2" class="book-appoinment-step">


            </div>

            <?php // CUSTOMER'S INFO ?>
            <div id="book-appointment-3" class="book-appoinment-step">


            </div>

            <?php // CONFIRMATION STEP ?>
            <div id="book-appointment-4" class="book-appoinment-step">

            </div>
            
            <div id="frame-footer">
                <em>Powered By <a href="https://code.google.com/p/easy-appointments/">Easy!Appointments</a></em>
            </div>
        </div>
    </div>
</body>
</html>
