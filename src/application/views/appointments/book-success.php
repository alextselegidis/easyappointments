<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    
    <?php // INCLUDE CSS FILES ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config->base_url(); ?>assets/css/libs/bootstrap/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $this->config->base_url(); ?>assets/css/libs/bootstrap/bootstrap-responsive.css">
    
    <?php // SET FAVICON FOR PAGE ?>
    <link rel="icon" type="image/x-icon" href="<?php echo $this->config->base_url(); ?>assets/images/favicon.ico">
    
    <style>
        body {
            background-color: #CAEDF3;
        }
        
        #success-frame {
            width: 660px;
            margin: 150px auto 0 auto;
            background: #FFF;
            box-shadow: 0px 1px 1px #B6B6B6;
            min-height: 197px;
            padding: 108px 10px;
        }
        
        #success-icon {
            float: left;
            margin: 0px 26px 0 20px;
        }
            
    </style>
</head>
<body>
    <div id="success-frame" class="container">
        <img id="success-icon" src="<?php echo $this->config->base_url(); ?>/assets/images/success.png" />
        <h2>Your appointment has been successfully registered.</h2>
        <p>An email with the appointment details has been sented to you.</p>
    </div>
</body>
</html>