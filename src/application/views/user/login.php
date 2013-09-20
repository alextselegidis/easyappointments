<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    
    <?php // INCLUDE JS FILES ?>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/libs/jquery/jquery.min.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/libs/bootstrap/bootstrap.min.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/libs/date.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/general_functions.js"></script>
        
    <?php // INCLUDE CSS FILES ?>
    <link 
        rel="stylesheet" 
        type="text/css" 
        href="<?php echo $this->config->base_url(); ?>assets/css/libs/bootstrap/bootstrap.css">
    <link 
        rel="stylesheet" 
        type="text/css" 
        href="<?php echo $this->config->base_url(); ?>assets/css/libs/bootstrap/bootstrap-responsive.css">
    
    <?php // SET FAVICON FOR PAGE ?>
    <link 
        rel="icon" 
        type="image/x-icon" 
        href="<?php echo $this->config->base_url(); ?>assets/images/favicon.ico">
    
    <style>
        body {
            background-color: #CAEDF3;
        }
        
        #login-frame {
            width: 630px;
            margin: 150px auto 0 auto;
            background: #FFF;
            border: 1px solid #DDDADA;
            padding: 70px;
        }
        
        #login-icon {
            float: right;
            margin-top: 17px;
        }
        
        label {
            font-weight: bold;
        }
        
        .forgot-password {
            margin-left: 20px;
        }
    </style>
    
    <script>
        $(document).ready(function() {
            var GlobalVariables = {
                'baseUrl': <?php echo '"' . $base_url . '"'; ?>,
                'destUrl': <?php echo '"' . $dest_url . '"'; ?>
            };
            
            /**
             * Event: Login Button "Click"
             * 
             * Make an ajax call to the server and check whether the user's credentials are right. 
             * If yes then redirect him to his desired page, otherwise display a message.
             */
            $('#login').click(function() {
                var postUrl = GlobalVariables.baseUrl . 'user/ajax_check_login';
                var postData = {
                    'username': $('#username').val(),
                    'password': $('#password').val()
                };
                
                $.post(postUrl, postData, function(response) {
                    //////////////////////////////////////////////////
                    console.log('Check Login Response: ', response);
                    //////////////////////////////////////////////////
                    
                    if (response == true) {
                        location(GlobalVariables.destUrl);
                    } else {
                        $('.alert').text('Login failed, please enter the correct credentials '
                            + 'and try again.');
                        $('.alert').removeClass('hidden');
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div id="login-frame" class="frame-container">
        <h2>Login Required</h2>
        <p>Welcome! You will need to login in order to view this page.</p>  
        <hr>
        <div class="alert hidden"></div>  
        <form>
            <label for="username">Username</label>
            <input type="text" id="username" placeholder="Enter your username here ..." />
            
            <label for="password">Password</label>
            <input type="password" id="password" placeholder="Enter your password here ..." />    
            
            <br><br>
            
            <button type="button" id="login" class="btn btn-primary btn-large">Login</button> 
            
            <a href="<?php echo $base_url; ?>user/forgot_password" class="forgot-password">Forgot Your Password?</a>
        </form>
    </div>
</body>
</html>