<!DOCTYPE html>
<html>
<head>
    <title>Easy!Appointments - Installation</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">

    <link rel="stylesheet" type="text/css"
        href="<?php echo $base_url; ?>/assets/ext/bootstrap/css/bootstrap.min.css">

    <link rel="icon" type="image/x-icon"
        href="<?php echo $base_url; ?>/assets/img/favicon.ico">

    <link rel="stylesheet" type="text/css"
        href="<?php echo $base_url ?>/assets/ext/jquery-ui/jquery-ui.min.css">

    <link rel="stylesheet" type="text/css"
        href="<?php echo $base_url; ?>/assets/css/general.css">

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
            <img src="<?php echo $base_url; ?>/assets/img/installation-banner.png"
                alt="Easy!Appointents Installation Banner">
        </a>
    </header>

    <div class="content container-fluid">
        <div class="welcome">
            <h3>Welcome to the Easy!Appointments installation page.</h3>
            <p>
                This page will help you set the main settings of your Easy!Appointments installation.
                You will be able to edit these settings and many more in the backend session of your
                system. Remember to use the <strong class="text-primary"><?php echo site_url('backend'); ?></strong>
                url to connect to the backend section of Easy!Appointments.

                If you face any problems during the usage of Easy!Appointments you can always check the 
                <a href="http://easyappointments.org/docs.html">Documentation</a>
                and <a href="http://groups.google.com/group/easy-appointments">Support Group</a> for getting help. You 
                may also submit new issues on
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

    <script type="text/javascript">
        var GlobalVariables = {
            'csrfToken': <?php echo json_encode($this->security->get_csrf_hash()); ?>,
            'baseUrl': <?php echo '"' . $base_url . '"'; ?>
        };

        var EALang = <?php echo json_encode($this->lang->language); ?>;
    </script>

    <script
        type="text/javascript"
        src="<?php echo $base_url; ?>/assets/ext/jquery/jquery.min.js"></script>

    <script
        type="text/javascript"
        src="<?php echo $this->config->item('base_url'); ?>/assets/ext/jquery-ui/jquery-ui.min.js"></script>

    <script
        type="text/javascript"
        src="<?php echo $base_url; ?>/assets/ext/bootstrap/js/bootstrap.min.js"></script>

    <script
        type="text/javascript"
        src="<?php echo $base_url; ?>/assets/js/general_functions.js"></script>

    <script
        type="text/javascript"
        src="<?php echo $base_url; ?>/assets/ext/datejs/date.js"></script>

    <script
        type="text/javascript"
        src="<?php echo $base_url; ?>/assets/js/installation.js"></script>
</body>
</html>
