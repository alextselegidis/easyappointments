<!DOCTYPE html>
<html>
<head>
    <title><?php echo $this->lang->line('forgot_your_password') . ' - ' . $company_name; ?></title>
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
    
    <script type="text/javascript">
        var EALang = <?php echo json_encode($this->lang->language); ?>;
    </script>
        
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
        
        #forgot-password-frame {
            width: 630px;
            margin: 150px auto 0 auto;
            background: #FFF;
            border: 1px solid #DDDADA;
            padding: 70px;
        }
        
        label {
            font-weight: bold;
        }
        
        .user-login{
            margin-left: 20px;
        }
    </style>
    
    <script type="text/javascript">
        $(document).ready(function() {
            var GlobalVariables = {
                'baseUrl': <?php echo '"' . $base_url . '"'; ?>,
                'AJAX_SUCCESS': 'SUCCESS',
                'AJAX_FAILURE': 'FAILURE'
            };
            
            var EALang = <?php echo json_encode($this->lang->language); ?>;
            
            /**
             * Event: Login Button "Click"
             * 
             * Make an ajax call to the server and check whether the user's credentials are right. 
             * If yes then redirect him to his desired page, otherwise display a message.
             */
            $('form').submit(function(event) {
                event.preventDefault(); 
                
                var postUrl = GlobalVariables.baseUrl + 'user/ajax_forgot_password';
                var postData = {
                    'username': $('#username').val(),
                    'email': $('#email').val()
                };
                
                $('.alert').addClass('hidden');
                $('#get-new-password').prop('disabled', true);
        
                $.post(postUrl, postData, function(response) {
                    //////////////////////////////////////////////////////////
                    console.log('Regenerate Password Response: ', response);
                    //////////////////////////////////////////////////////////
                    
                    $('#get-new-password').prop('disabled', false);
                    if (!GeneralFunctions.handleAjaxExceptions(response)) return;
                    
                    if (response == GlobalVariables.AJAX_SUCCESS) {
                        $('.alert').addClass('alert-success');
                        $('.alert').text(EALang['new_password_sent_with_email']);
                    } else {
                        $('.alert').text('The operation failed! Please enter a valid username '
                                + 'and email address in order to get a new password.');
                    }
                    $('.alert').removeClass('hidden');
                }, 'json');
            });
        });
    </script>
</head>
<body>
    <div id="forgot-password-frame" class="frame-container">
        <h2><?php echo $this->lang->line('forgot_your_password'); ?></h2>
        <p><?php echo $this->lang->line('type_username_and_email_for_new_password'); ?></p>  
        <hr>
        <div class="alert hidden"></div>  
        <form>
            <label for="username"><?php echo $this->lang->line('username'); ?></label>
            <input type="text" id="username" placeholder="<?php echo $this->lang->line('enter_username_here'); ?>" class="span3" />
            
            <label for="email"><?php echo $this->lang->line('email'); ?></label>
            <input type="text" id="email" placeholder="<?php echo $this->lang->line('enter_email_here'); ?>" class="span3" />    
            
            <br><br>
            
            <button type="submit" id="get-new-password" class="btn btn-primary btn-large">
                <?php echo $this->lang->line('regenerate_password'); ?>
            </button> 
            
            <a href="<?php echo $base_url; ?>user/login" class="user-login">
                <?php echo $this->lang->line('go_to_login'); ?>
            </a>
        </form>
    </div>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/general_functions.js"></script>
</body>
</html>