<!DOCTYPE html>
<html>
<head>
    <title><?php echo $company_name; ?></title>
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
        
        #message-frame {
            width: 630px;
            margin: 150px auto 0 auto;
            background: #FFF;
            border: 1px solid #DDDADA;
            padding: 70px;
        }
        
        #message-icon {
            float: right;
            margin-top: 17px;
        }
    </style>
</head>
<body>
    <div id="message-frame" class="frame-container">
        <img id="message-icon" src="<?php echo $message_icon; ?>" />
        <h3><?php echo $message_title; ?></h3>
        <p><?php echo $message_text; ?></p>  
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