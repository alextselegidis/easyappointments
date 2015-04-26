<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" /> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#35A768">
    <title><?php echo $this->lang->line('appointment_cancelled_title'); ?></title>
    
    <?php
        // ------------------------------------------------------------
        // INCLUDE JS FILES 
        // ------------------------------------------------------------ ?>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>/assets/js/libs/jquery/jquery.min.js"></script>
    
    <?php
        // ------------------------------------------------------------
        // INCLUDE CSS FILES 
        // ------------------------------------------------------------ ?>
    <link rel="stylesheet" type="text/css" 
        href="<?php echo $this->config->base_url(); ?>/assets/ext/bootstrap/css/bootstrap.css">
    
    <link rel="stylesheet" type="text/css" 
        href="<?php echo $this->config->base_url(); ?>/assets/css/frontend.css">

    
    <?php
        // ------------------------------------------------------------
        // SET PAGE FAVICON 
        // ------------------------------------------------------------ ?>
    <link rel="icon" type="image/x-icon" 
        href="<?php echo $this->config->base_url(); ?>/assets/img/favicon.ico">

    <link rel="icon" sizes="192x192" 
            href="<?php echo $this->config->base_url(); ?>/assets/img/logo.png">
</head>
<body>
    <div id="success-frame" class="frame-container">
        
        <img id="success-icon" src="<?php echo $this->config->base_url(); ?>/assets/img/success.png" />
        
        <?php 
            echo '<h3>' . $this->lang->line('appointment_cancelled') . '</h3>';
        
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
</body>
</html>
