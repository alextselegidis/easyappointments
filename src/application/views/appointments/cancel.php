<!DOCTYPE html>
<html>
<head>
    <title><?php echo $this->lang->line('appointment_cancelled_title'); ?></title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    
    <?php // INCLUDE JS FILES ?>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/libs/jquery/jquery.min.js"></script>
    
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
        <img id="success-icon" src="<?php echo $this->config->base_url(); ?>assets/images/success.png" />
        
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
