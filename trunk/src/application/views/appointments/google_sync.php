<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    
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
            width: 660px;
            margin: 150px auto 0 auto;
            background: #FFF;
            border: 1px solid #DDDADA;
            min-height: 197px;
            padding: 108px 10px;
        }
        
        #message-icon {
            float: left;
            margin: 10px 25px 25px 50px;
        }
            
    </style>
</head>
<body>
    <div id="message-frame" class="container">
        <img 
            id="message-icon" 
            src="<?php echo $this->config->base_url(); ?>assets/images/<?php echo $image; ?>" />
        <h2>Google Calendar Sync</h2>
        <p><?php echo $message; ?></p>
    </div>
</body>
</html>