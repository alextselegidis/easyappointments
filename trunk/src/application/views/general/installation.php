<!DOCTYPE html>
<html>
<head>
    <?php // PAGE META ?>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>Easy!Appointments - Installation</title>

    <?php // INCLUDE CSS ?>
    <link 
        rel="stylesheet" 
        type="text/css" 
        href="<?php echo $base_url; ?>assets/css/libs/bootstrap/bootstrap.css">
    <link 
        rel="stylesheet" 
        type="text/css" 
        href="<?php echo $base_url; ?>assets/css/libs/bootstrap/bootstrap-responsive.css">
    
    <?php // SET FAVICON FOR PAGE ?>
    <link 
        rel="icon" 
        type="image/x-icon" 
        href="<?php echo $base_url(); ?>assets/images/favicon.ico">       

    <?php // INCLUDE SCRIPTS ?>
    <script 
        type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/libs/jquery/jquery.min.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/libs/bootstrap/bootstrap.min.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/libs/date.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/general_functions.js"></script>
    
    <script type="text/javascript">
        $(document).ready(function() {
            var GlobalVariables = {
                'baseUrl': <?php echo '"' . $base_url . '"'; ?>
            };
            
            /**
             * Event: Install System "Click"
             */
            $('#install').click(function() {
                if (!validate()) return;
        
                var postUrl = GlobalVariables.baseUrl + 'ajax_install';
                var postData = {
                    'admin': getAdminData(),
                    'company': getCompanyDate()
                };
                
                $.post(postUrl, postData, function(response) {
                
                });
            });
            
            /**
             * Validates the user input. Use this before executing the installation procedure.
             * 
             * @returns {bool} Returns the validation result.
             */
            function validate() {
                try {
                    $('.alert').fadeOut();
                    $('input[type="text"]').css('border', '');
                    
                    // Check for empty fields.
                    var missingRequired = false;
                    $('input[type="text"]').each(function() {
                        if ($(this).val() == '') {
                            $(this).css('border', '2px solid red');
                            missingRequired = true;
                        }
                    });
                    
                    if (missingRequired)
                        throw 'All the page fields are required.';
                    
                    // Validate Passwords
                    if ($('#password').val() != $('#retype-password').val()) {
                        $('#password').css('border', '2px solid red');
                        $('#retype-password').css('border', '2px solid red');
                        throw 'Passwords do not match!';
                    }
                    
                    // Validate Email
                    if (!GeneralFunctions.validateEmail($('#email').val())) {
                        $('#email').css('border', '2px solid red');
                        throw 'The email addres is invalid!';
                    }
                    
                    return true;
                } catch(exc) {
                    $('.alert').txt(exc);
                    $('.alert').fadeIn();
                    return false;
                }
            }
            
            /**
             * Get the admin data as an object.
             * 
             * @returns {object} 
             */
            function getAdminData() {
                var admin = {
                    'first_name': $('#first-name').val(),
                    'last_name': $('#last-name').val(),
                    'email': $('#email').val(),
                    'phone_number': $('#phone-number').val(),
                    'username': $('#username').val(),
                    'password': $('#password').val()
                };
                
                return admin;
            }
            
            /**
             * Get the company data as an object.
             * 
             * @returns {object}
             */
            function getCompanyData() {
                
            }
        });
    </script>
</head>
<body>
    <header>
        <a href="http://easyappointments.org" target="_blank">
            <img src="<?php echo $base_url; ?>assets/images/installation-banner.png" alt="Easy!Appointents Installation Banner">
        </a>
    </header>
    <div class="content">
        <div class="welcome">
            <h2>Welcome To The Easy!Appointments Installation Page</h2>
            <p>
                This page will help you set the main settings of your Easy!Appointments installation.
                You will be able to edit these settings and many more in the backend session of your 
                system. Remember to use the <span class="label label-info"><?php echo $base_url; ?>
                backend</span> url to connect to the backend section of Easy!Appointments.
                
                If you face any problems during the usage of Easy!Appointments you can always check
                the official <a href="https://code.google.com/p/easy-appointments/w/list">Wiki Pages</a> 
                and the <a href="http://groups.google.com/group/easy-appointments">Support Group</a>
                for getting help. You may also submit new issues on the
                <a href="https://code.google.com/p/easy-appointments/issues/list">Google Code Issues</a>
                page, in order to help our development process.
            </p>
        </div>
        
        <hr>
        
        <div class="alert" style="display: none"></div>
        
        <div class="admin-settings">
            <h2>Administrator Settings</h2>
            <label for="first-name">First Name</label>
            <input type="text" id="first-name" />
            
            <label for="last-name">Last Name</label>
            <input type="text" id="last-name" />
            
            <label for="email">Email</label>
            <input type="text" id="email" />
            
            <label for="phone-number">Phone Number</label>
            <input type="text" id="phone-number" />
            
            <label for="username">Username</label>
            <input type="text" id="username" />
            
            <label for="password">Password</label>
            <input type="password" id="password" />
            
            <label for="retype-password">Retype Password</label>
            <input type="password" id="retype-password" />
        </div>
        
        <hr>
        
        <div class="company-settings">
            <h2>Company Settings</h2>
            <label for="company-name">Company Name</label>
            <input type="text" id="company-name">
            
            <label for="company-email">Company Email</label>
            <input type="text" id="company-email">
            
            <label for="company-link">Company Link</label>
            <input type="text" id="company-link">
            
            <div class="alert alert-info">
                You will be able to set your business logic in the backend settings page after 
                the installation is complete.
            </div>
        </div>
        
        <center>
            <button type="button" id="install" class="btn btn-success btn-large">
                <i class="icon-white icon-ok"></i>
                Install</button>
        </center>
    </div>

    <footer>
        Powered by <a href="http://easyappointments.org">Easy!Appointments</a>
    </footer>
</body>
</html>