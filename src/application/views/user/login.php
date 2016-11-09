<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#35A768">
    <title><?php echo $this->lang->line('login') . ' - ' . $company_name; ?></title>


    <?php // INCLUDE JS FILES ?>
    <script
        type="text/javascript"
        src="<?php echo base_url('assets/ext/jquery/jquery.min.js'); ?>"></script>
    <script
        type="text/javascript"
        src="<?php echo base_url('assets/ext/bootstrap/js/bootstrap.min.js'); ?>"></script>
    <script
        type="text/javascript"
        src="<?php echo base_url('assets/ext/datejs/date.js'); ?>"></script>

    <?php // INCLUDE CSS FILES ?>
    <link
        rel="stylesheet"
        type="text/css"
        href="<?php echo base_url('assets/ext/bootstrap/css/bootstrap.min.css'); ?>">
	<link
        rel="stylesheet"
        type="text/css"
        href="<?php echo base_url('assets/css/general.css'); ?>">

    <?php // SET FAVICON FOR PAGE ?>
    <link
        rel="icon"
        type="image/x-icon"
        href="<?php echo base_url('assets/img/favicon.ico'); ?>">

    <style>
        body {
            width: 100vw;
            height: 100vh;
            display: table-cell;
            vertical-align: middle;
            background-color: #CAEDF3;
        }

        #login-frame {
            width: 630px;
            margin: auto;
            background: #FFF;
            border: 1px solid #DDDADA;
            padding: 70px;
        }

        @media(max-width: 640px) {
            #login-frame {
                width: 100%;
                padding: 20px;
            }
        }
    </style>

    <script type="text/javascript">
        var GlobalVariables = {
            'csrfToken': <?php echo json_encode($this->security->get_csrf_hash()); ?>,
            'baseUrl': <?php echo json_encode($base_url); ?>,
            'destUrl': <?php echo json_encode($dest_url); ?>,
            'AJAX_SUCCESS': 'SUCCESS',
            'AJAX_FAILURE': 'FAILURE'
        };

        var EALang = <?php echo json_encode($this->lang->language); ?>;
        var availableLanguages = <?php echo json_encode($this->config->item('available_languages')); ?>;

        $(document).ready(function() {
        	GeneralFunctions.enableLanguageSelection($('#select-language'));

            /**
             * Event: Login Button "Click"
             *
             * Make an ajax call to the server and check whether the user's credentials are right.
             * If yes then redirect him to his desired page, otherwise display a message.
             */
            $('#login-form').submit(function(event) {
                event.preventDefault();

                var postUrl = GlobalVariables.baseUrl + '/index.php/user/ajax_check_login';
                var postData = {
                    'csrfToken': GlobalVariables.csrfToken,
                    'username': $('#username').val(),
                    'password': $('#password').val()
                };

                $('.alert').addClass('hidden');

                $.post(postUrl, postData, function(response) {
                    //////////////////////////////////////////////////
                    console.log('Check Login Response: ', response);
                    //////////////////////////////////////////////////

                    if (!GeneralFunctions.handleAjaxExceptions(response)) return;

                    if (response == GlobalVariables.AJAX_SUCCESS) {
                        window.location.href = GlobalVariables.destUrl;
                    } else {
                        $('.alert').text(EALang['login_failed']);
                        $('.alert')
                            .removeClass('hidden alert-danger alert-success')
                            .addClass('alert-danger');
                    }
                }, 'json');
            });
        });
    </script>
</head>
<body>
    <div id="login-frame" class="frame-container">
        <h2><?php echo $this->lang->line('backend_section'); ?></h2>
        <p><?php echo $this->lang->line('you_need_to_login'); ?></p>
        <hr>
        <div class="alert hidden"></div>
        <form id="login-form">
            <div class="form-group">
                <label for="username"><?php echo $this->lang->line('username'); ?></label>
                <input type="text" id="username"
                		placeholder="<?php echo $this->lang->line('enter_username_here'); ?>"
                		class="form-control" />
            </div>
            <div class="form-group">
                <label for="password"><?php echo $this->lang->line('password'); ?></label>
                <input type="password" id="password"
                		placeholder="<?php echo $this->lang->line('enter_password_here'); ?>"
                		class="form-control" />
            </div>
            <br>

            <button type="submit" id="login" class="btn btn-primary">
            	<?php echo $this->lang->line('login'); ?>
            </button>

            <br><br>

            <a href="<?php echo site_url('user/forgot_password'); ?>" class="forgot-password">
            	<?php echo $this->lang->line('forgot_your_password'); ?></a>
            |
            <span id="select-language" class="label label-success">
	        	<?php echo ucfirst($this->config->item('language')); ?>
	        </span>
        </form>
    </div>

    <script
        type="text/javascript"
        src="<?php echo base_url('assets/js/general_functions.js'); ?>"></script>
</body>
</html>
