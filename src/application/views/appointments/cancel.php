<!DOCTYPE html>
<html>
<head>
    <title><?php echo $this->lang->line('appointment_cancelled_title'); ?></title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#35A768">
    
    <?php // INCLUDE JS FILES ?>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>/assets/js/libs/jquery/jquery.min.js"></script>
    
    <?php // INCLUDE CSS FILES ?>
    <link rel="stylesheet" type="text/css" 
        href="<?php echo $this->config->base_url(); ?>/assets/ext/bootstrap/css/bootstrap.css">

    
    <?php // SET FAVICON FOR PAGE ?>
    <link rel="icon" type="image/x-icon" 
        href="<?php echo $this->config->base_url(); ?>/assets/img/favicon.ico">

    <link rel="icon" sizes="192x192" 
            href="<?php echo $this->config->base_url(); ?>/assets/img/logo.png">
    
    <style>
        body {
            background-color: #CAEDF3;
        }
        
        #success-frame {
            width: 650px;
            margin: 150px auto 0 auto;
            background: #FFF;
            border: 1px solid #DDDADA;
            padding: 70px;
        }
        
        #success-icon {
            float: right;
            margin-top: 10px;
        }
            
    </style>
</head>
<body>
    <div id="success-frame" class="frame-container">
        <img id="success-icon" src="<?php echo $this->config->base_url(); ?>/assets/img/success.png" />
        
        <h3><?php echo $this->lang->line('appointment_cancelled'); ?></h3>
        
        <?php 
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
