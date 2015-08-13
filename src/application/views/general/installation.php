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
        href="<?php echo $base_url; ?>/assets/ext/bootstrap/css/bootstrap.min.css">
    
    <?php // SET FAVICON FOR PAGE ?>
    <link 
        rel="icon" 
        type="image/x-icon" 
        href="<?php echo $base_url; ?>/assets/img/favicon.ico">       

    <?php // INCLUDE SCRIPTS ?>
    <script 
        type="text/javascript" 
        src="<?php echo $base_url; ?>/assets/ext/jquery/jquery.min.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $base_url; ?>/assets/ext/bootstrap/js/bootstrap.min.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $base_url; ?>/assets/ext/datejs/date.js"></script>
    
    <script type="text/javascript">
	    var GlobalVariables = {
            'csrfToken': <?php echo json_encode($this->security->get_csrf_hash()); ?>,
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
        
                var postUrl = GlobalVariables.baseUrl + '/index.php/appointments/ajax_install';
                var postData = {
                    'csrfToken': GlobalVariables.csrfToken,
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
                            window.location.href = GlobalVariables.baseUrl + '/index.php/backend';
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
    <div id="loading" class="hidden">
        <img src="<?php echo $base_url; ?>/assets/img/loading.gif" />
    </div>
    
    <header>
        <a href="http://easyappointments.org" target="_blank">
            <img src="<?php echo $base_url; ?>/assets/img/installation-banner.png" alt="Easy!Appointents Installation Banner">
        </a>
    </header>
    
    <div class="content container-fluid">
        <div class="welcome">
            <h3>Welcome to the Easy!Appointments installation page.</h3>
            <p>
                This page will help you set the main settings of your Easy!Appointments installation.
                You will be able to edit these settings and many more in the backend session of your 
                system. Remember to use the <strong class="text-primary"><?php echo $base_url; ?>/index.php/backend</strong> 
                url to connect to the backend section of Easy!Appointments.

                If you face any problems during the usage of Easy!Appointments you can always check
                the official <a href="https://github.com/alextselegidis/easyappointments/wiki">Wiki Pages</a> 
                and <a href="http://groups.google.com/group/easy-appointments">Support Group</a>
                for getting help. You may also submit new issues on
                <a href="https://github.com/alextselegidis/easyappointments/issues">GitHub Issues</a>
                in order to help our development process.
            </p>
        </div>
        
        <div class="alert hidden"></div>

        <div class="row">
            <div class="admin-settings col-md-5">
                <h3>Administrator</h3>

                <div class="form-group">
                    <label for="first-name" class="control-label">First Name</label>
                    <input type="text" id="first-name" class="form-control" />
                </div>

                <div class="form-group">
                <label for="last-name" class="control-label">Last Name</label>
                <input type="text" id="last-name" class="form-control" />
                </div>

                <div class="form-group">
                <label for="email" class="control-label">Email</label>
                <input type="text" id="email" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="phone-number" class="control-label">Phone Number</label>
                    <input type="text" id="phone-number" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="username" class="control-label">Username</label>
                    <input type="text" id="username" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="password" class="control-label">Password</label>
                    <input type="password" id="password" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="retype-password" class="control-label">Retype Password</label>
                    <input type="password" id="retype-password" class="form-control" />
                </div>
            </div>

            <div class="company-settings col-md-5">
                <h3>Company</h3>

                <div class="form-group">
                    <label for="company-name" class="control-label">Company Name</label>
                    <input type="text" id="company-name" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="company-email" class="control-label">Company Email</label>
                    <input type="text" id="company-email" class="form-control" />
                </div>

                <div class="form-group">
                    <label for="company-link" class="control-label">Company Link</label>
                    <input type="text" id="company-link" class="form-control" />        
                </div>
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
            Easy!Appointments is licensed under the <span class="label label-default">GPLv3 license</span>. 
            By using the code of Easy!Appointments in any way <br> you agree with the terms described in the 
            following url:
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
        src="<?php echo $base_url; ?>/assets/js/general_functions.js"></script>
</body>
</html>