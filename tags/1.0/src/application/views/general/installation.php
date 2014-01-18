<!DOCTYPE html>
<html>
<head>
    <?php // PAGE META ?>
    <title>Easy!Appointments - Installation</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    
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
        href="<?php echo $base_url; ?>assets/images/favicon.ico">       

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
    
    <script type="text/javascript">
	    var GlobalVariables = {
            'baseUrl': <?php echo '"' . $base_url . '"'; ?>
        };

        var EALang = <?php echo json_encode($this->lang->language); ?>;
        
        $(document).ready(function() {            
            var MIN_PASSWORD_LENGTH = 7;
            var AJAX_SUCCESS = 'SUCCESS';
            var AJAX_FAILURE = 'FAILURE';
            
            $(document).ajaxStart(function() {
                $('#loading').show();
            });

            $(document).ajaxStop(function() {
                $('#loading').hide();
            });
            
            /**
             * Event: Install Easy!Appointments Button "Click"
             */
            $('#install').click(function() {
                if (!validate()) return;
        
                var postUrl = GlobalVariables.baseUrl + 'index.php/appointments/ajax_install';
                var postData = {
                    'admin': JSON.stringify(getAdminData()),
                    'company': JSON.stringify(getCompanyData())
                };
                
                $.post(postUrl, postData, function(response) {
                    //////////////////////////////////////////////////////
                    console.log('Ajax Install E!A Response:', response);
                    //////////////////////////////////////////////////////
                    
                    if (!GeneralFunctions.handleAjaxExceptions(response)) return;
                    
                    if (response == AJAX_SUCCESS) {
                        $('.alert').text('Easy!Appointments has been successfully installed!');
                        $('.alert').addClass('alert-success');
                        $('.alert').show();
                        setTimeout(function() {
                            window.location.href = GlobalVariables.baseUrl + 'backend';
                        }, 1000);
                    }
                }, 'json');
            });
            
            /**
             * Validates the user input. Use this before executing the installation procedure.
             * 
             * @returns {bool} Returns the validation result.
             */
            function validate() {
                try {
                    $('.alert').hide();
                    $('input').css('border', '');
                    
                    // Check for empty fields.
                    var missingRequired = false;
                    $('input').each(function() {
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
                    
                    if ($('#password').val().length < MIN_PASSWORD_LENGTH) {
                        $('#password').css('border', '2px solid red');
                        $('#retype-password').css('border', '2px solid red');
                        throw 'The password must be at least ' + MIN_PASSWORD_LENGTH + ' characters long.';
                    }
                    
                    // Validate Email
                    if (!GeneralFunctions.validateEmail($('#email').val())) {
                        $('#email').css('border', '2px solid red');
                        throw 'The email address is invalid!';
                    }
                    
                    if (!GeneralFunctions.validateEmail($('#company-email').val())) {
                        $('#company-email').css('border', '2px solid red');
                        throw 'The email address is invalid!';
                    }
                    
                    return true;
                } catch(exc) {
                    $('.alert').text(exc);
                    $('.alert').show();
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
                var company = {
                    'company_name': $('#company-name').val(),
                    'company_email': $('#company-email').val(),
                    'company_link': $('#company-link').val()
                };
                
                return company;
            }
        });
    </script>
    
    <style>
        header { 
            background: #DAFFEB;
            margin-bottom: 25px;
        }
        
        .content {
            margin: 32px;
            max-width: 980px;
        }
        
        .alert {
            margin: 25px 0 10px 0;
        }
        
        footer {
           padding: 10px 35px; 
           background-color: #FAFAFA;
           margin-top: 20px;
           border-top: 1px solid #EEE;
        }
        
        #loading { 
            position: fixed; 
            top: 0px; 
            left: 0px; 
            width: 100%; 
            height: 100%; 
            z-index: 999999;
            background: rgba(255, 255, 255, 0.75);
        }

        #loading img { 
            margin: auto; 
            display: block; 
        }
    </style>
</head>
<body>
    <div id="loading" style="display: none;">
        <img src="<?php echo $base_url; ?>assets/images/loading.gif" />
    </div>
    
    <header>
        <a href="http://easyappointments.org" target="_blank">
            <img src="<?php echo $base_url; ?>assets/images/installation-banner.png" alt="Easy!Appointents Installation Banner">
        </a>
    </header>
    
    <div class="content">
        <div class="welcome">
            <h2>Welcome to the Easy!Appointments installation page.</h2>
            <p>
                This page will help you set the main settings of your Easy!Appointments installation.
                You will be able to edit these settings and many more in the backend session of your 
                system. Remember to use the <span class="text-error"><?php echo $base_url; ?>backend</span> 
                url to connect to the backend section of Easy!Appointments.

                If you face any problems during the usage of Easy!Appointments you can always check
                the official <a href="https://code.google.com/p/easy-appointments/w/list">Wiki Pages</a> 
                and <a href="http://groups.google.com/group/easy-appointments">Support Group</a>
                for getting help. You may also submit new issues on
                <a href="https://code.google.com/p/easy-appointments/issues/list">Google Code</a>
                in order to help our development process.
            </p>
        </div>
        
        <div class="alert" style="display:none"></div>

        <div class="row-fluid">
            <div class="admin-settings span5">
                <h3>Administrator</h3>
                <label for="first-name">First Name</label>
                <input type="text" id="first-name" class="span10" />

                <label for="last-name">Last Name</label>
                <input type="text" id="last-name" class="span10" />

                <label for="email">Email</label>
                <input type="text" id="email" class="span10" />

                <label for="phone-number">Phone Number</label>
                <input type="text" id="phone-number" class="span10" />

                <label for="username">Username</label>
                <input type="text" id="username" class="span10" />

                <label for="password">Password</label>
                <input type="password" id="password" class="span10" />

                <label for="retype-password">Retype Password</label>
                <input type="password" id="retype-password" class="span10" />
            </div>

            <div class="company-settings span5">
                <h3>Company</h3>
                <label for="company-name">Company Name</label>
                <input type="text" id="company-name" class="span10" />

                <label for="company-email">Company Email</label>
                <input type="text" id="company-email" class="span10" />

                <label for="company-link">Company Link</label>
                <input type="text" id="company-link" class="span10" />

                
            </div>
        </div>
        
        <br>
        
        <p>
            You will be able to set your business logic in the backend settings page 
            after the installation is complete. 
            <br>
            Press the following button to complete the installation process.
        </p>
        
        <br>
        
        <p>
            <h3>License</h3>
            Easy!Appointments is licensed under the GPLv3 license. By using 
            the code of Easy!Appointments in any way <br> you are agreeing to the 
            terms described in the following url:
            <a href="http://www.gnu.org/copyleft/gpl.html">http://www.gnu.org/copyleft/gpl.html</a>
        </p>
        
        <br>
        
        <button type="button" id="install" class="btn btn-success btn-large">
            <i class="icon-white icon-ok"></i>
            Install Easy!Appointments</button>
    </div>

    <footer>
        Powered by <a href="http://easyappointments.org">Easy!Appointments</a>
    </footer>
    
    <script 
        type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/general_functions.js"></script>
</body>
</html>