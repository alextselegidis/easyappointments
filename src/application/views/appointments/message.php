<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#35A768">
    <title><?php echo $message_title ?></title>

    <?php
        // ------------------------------------------------------------
        // INCLUDE CSS FILES
        // ------------------------------------------------------------ ?>

    <link rel="stylesheet" type="text/css"
        href="<?php echo $this->config->item('base_url'); ?>/assets/ext/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" type="text/css"
        href="<?php echo $this->config->item('base_url'); ?>/assets/css/frontend.css">

    <?php
        // ------------------------------------------------------------
        // SET PAGE FAVICON
        // ------------------------------------------------------------ ?>

    <link rel="icon" type="image/x-icon"
        href="<?php echo $this->config->item('base_url'); ?>/assets/img/favicon.ico">

    <link rel="icon" sizes="192x192"
        href="<?php echo $this->config->item('base_url'); ?>/assets/img/logo.png">

    <?php
        // ------------------------------------------------------------
        // CUSTOM PAGE JS
        // ------------------------------------------------------------ ?>

    <script type="text/javascript">
        var EALang = <?php echo json_encode($this->lang->language); ?>;
    </script>

    <?php
        // ------------------------------------------------------------
        // INCLUDE JS FILES
        // ------------------------------------------------------------ ?>

    <script
        type="text/javascript"
        src="<?php echo $this->config->item('base_url'); ?>/assets/ext/jquery/jquery.min.js"></script>
    <script
        type="text/javascript"
        src="<?php echo $this->config->item('base_url'); ?>/assets/ext/bootstrap/js/bootstrap.min.js"></script>
    <script
        type="text/javascript"
        src="<?php echo $this->config->item('base_url'); ?>/assets/ext/datejs/date.js"></script>
    <script
        type="text/javascript"
        src="<?php echo $this->config->item('base_url'); ?>/assets/js/general_functions.js"></script>
</head>

<body>
    <div id="main" class="container">
        <div class="wrapper row">

            <div id="message-frame" class="frame-container
                    col-xs-12
                    col-sm-offset-1 col-sm-10
                    col-md-offset-2 col-md-8
                    col-lg-offset-2 col-lg-8">

                <div class="col-xs-12 col-sm-2">
                    <img id="message-icon" src="<?php echo $message_icon; ?>" />
                </div>

                <div class="col-xs-12 col-sm-10">
                    <?php
                        echo '<h3>' . $message_title . '</h3>';
                        echo '<p>' . $message_text . '</p>';

                        // Display exceptions (if any).
                        if (isset($exceptions)) {
                            echo '<div style="margin: 10px">';
                            echo '<h4>Unexpected Errors</h4>';
                            foreach($exceptions as $exception) {
                                echo exceptionToHtml($exception);
                            }
                            echo '</div>';
                        }
                    ?>
                </div>
            </div>

        </div>
    </div>
    
    <?php google_analytics_script(); ?>
</body>
</html>
