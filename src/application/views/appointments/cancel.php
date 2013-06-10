<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    
    <?php // INCLUDE JS FILES ?>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/libs/jquery/jquery.min.js">
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
        
        <h3>Your appointment has been successfully cancelled!</h3>
        
        <?php 
            // Display exception message (if any).
            if (isset($error_message)) {
                echo $error_message;
            }
        ?>
    </div>
</body>
</html>
