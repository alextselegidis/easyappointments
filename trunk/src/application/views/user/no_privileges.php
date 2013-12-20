<!DOCTYPE html>
<html>
<head>
    <title><?php echo $this->lang->line('no_privileges') . ' - ' . $company_name; ?></title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    
    <?php // INCLUDE JS FILES ?>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/libs/jquery/jquery.min.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $this->config->base_url(); ?>assets/js/libs/bootstrap/bootstrap.min.js"></script>   
        
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
        
        #no-priv-frame {
            width: 630px;
            margin: 150px auto 0 auto;
            background: #FFF;
            border: 1px solid #DDDADA;
            padding: 70px;
        }
        
        #login-icon {
            float: right;
            margin-top: 17px;
        }
        
        label {
            font-weight: bold;
        }
        
        .btn {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div id="no-priv-frame" class="frame-container">
        <h3><?php echo $this->lang->line('no_privileges'); ?></h3>
        <p>
            <?php echo $this->lang->line('no_provileges_message'); ?>
        </p>  
        
        <br>
        
        <a href="<?php echo $this->config->base_url(); ?>backend" class="btn btn-success btn-large">
            <i class="icon-calendar icon-white"></i>
            <?php echo $this->lang->line('backend_calendar'); ?>
        </a>
    </div>
</body>
</html>