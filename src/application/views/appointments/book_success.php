<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#35A768">
    <title><?php echo $this->lang->line('appointment_registered') . ' - ' . $company_name; ?></title>

    <?php
        // ------------------------------------------------------------
        // INCLUDE CSS FILES
        // ------------------------------------------------------------ ?>

    <link rel="stylesheet" type="text/css"
        href="<?php echo base_url('assets/ext/bootstrap/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" type="text/css"
        href="<?php echo base_url('assets/css/frontend.css'); ?>">

    <?php
        // ------------------------------------------------------------
        // SET PAGE FAVICON
        // ------------------------------------------------------------ ?>

    <link rel="icon" type="image/x-icon"
        href="<?php echo base_url('assets/img/favicon.ico'); ?>">

    <link rel="icon" sizes="192x192"
        href="<?php echo base_url('assets/img/logo.png'); ?>">
</head>
<body>
    <div id="main" class="container">
        <div class="wrapper row">
            <div id="success-frame" class="frame-container
                    col-xs-12
                    col-sm-offset-1 col-sm-10
                    col-md-offset-2 col-md-8
                    col-lg-offset-2 col-lg-8">

                <div class="col-xs-12 col-sm-2">
                    <img id="success-icon" class="pull-right" src="<?php echo base_url('assets/img/success.png'); ?>" />
                </div>
                <div class="col-xs-12 col-sm-10">
                    <?php
                        echo '
                            <h3>' . $this->lang->line('appointment_registered') . '</h3>
                            <p>' . $this->lang->line('appointment_details_was_sent_to_you') . '</p>
                            <a href="' . site_url() . '" class="btn btn-success btn-large">
                                <span class="glyphicon glyphicon-calendar"></span> ' .
                                $this->lang->line('go_to_booking_page') . '
                            </a>
                        ';

                        if ($this->config->item('google_sync_feature')) {
                            echo '
                                <button id="add-to-google-calendar" class="btn btn-primary">
                                    <span class="glyphicon glyphicon-plus"></span>
                                    ' . $this->lang->line('add_to_google_calendar') . '
                                </button>';
                        }

                        // Display exceptions (if any).
                        if (isset($exceptions)) {
                            echo '<div class="col-xs-12" style="margin:10px">';
                            echo '<h4>Unexpected Errors</h4>';
                            foreach($exceptions as $exc) {
                                echo exceptionToHtml($exc);
                            }
                            echo '</div>';
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php
        // ------------------------------------------------------------
        // INCLUDE JS FILES
        // ------------------------------------------------------------ ?>

    <script
        type="text/javascript"
        src="<?php echo base_url('assets/ext/jquery/jquery.min.js'); ?>"></script>
    <script
        type="text/javascript"
        src="<?php echo base_url('assets/ext/bootstrap/js/bootstrap.min.js'); ?>"></script>
    <script
        type="text/javascript"
        src="<?php echo base_url('assets/ext/datejs/date.js'); ?>"></script>
    <script
        type="text/javascript"
        src="https://apis.google.com/js/client.js"></script>

    <?php
        // ------------------------------------------------------------
        // CUSTOM PAGE JS
        // ------------------------------------------------------------ ?>

    <script type="text/javascript">
        var GlobalVariables = {
            'csrfToken'         : <?php echo json_encode($this->security->get_csrf_hash()); ?>,
            'appointmentData'   : <?php echo json_encode($appointment_data); ?>,
            'providerData'      : <?php echo json_encode($provider_data); ?>,
            'serviceData'       : <?php echo json_encode($service_data); ?>,
            'companyName'       : <?php echo '"' . $company_name . '"'; ?>,
            'googleApiKey'      : <?php echo '"' . Config::GOOGLE_API_KEY . '"'; ?>,
            'googleClientId'    : <?php echo '"' . Config::GOOGLE_CLIENT_ID . '"'; ?>,
            'googleApiScope'    : 'https://www.googleapis.com/auth/calendar'
        };

        var EALang = <?php echo json_encode($this->lang->language); ?>;
    </script>

    <script
        type="text/javascript"
        src="<?php echo base_url('assets/js/frontend_book_success.js'); ?>"></script>

    <script
        type="text/javascript"
        src="<?php echo base_url('assets/js/general_functions.js'); ?>"></script>

    <?php google_analytics_script(); ?>
</body>
</html>
