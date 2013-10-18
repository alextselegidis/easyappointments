<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $company_name; ?> | Easy!Appointments</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    
    <link rel="icon" type="image/x-icon" 
          href="<?php echo $base_url; ?>assets/images/favicon.ico">
    
    <?php
        // ------------------------------------------------------------
        // INCLUDE CSS FILES 
        // ------------------------------------------------------------ ?>
    <link 
        rel="stylesheet" 
        type="text/css" 
        href="<?php echo $base_url; ?>assets/css/libs/bootstrap/bootstrap.css">
    <link 
        rel="stylesheet" 
        type="text/css" 
        href="<?php echo $base_url; ?>assets/css/libs/bootstrap/bootstrap-responsive.css">
    <link 
        rel="stylesheet" 
        type="text/css" 
        href="<?php echo $base_url; ?>assets/css/libs/jquery/jquery-ui.min.css">
    <link 
        rel="stylesheet" 
        type="text/css" 
        href="<?php echo $base_url; ?>assets/css/libs/jquery/jquery.qtip.min.css">
    <link 
        rel="stylesheet" 
        type="text/css" 
        href="<?php echo $base_url; ?>assets/css/backend.css">
    <link 
        rel="stylesheet" 
        type="text/css" 
        href="<?php echo $base_url; ?>assets/css/general.css">
    <link 
        rel="stylesheet" 
        type="text/css" 
        href="<?php echo $base_url; ?>assets/css/libs/jquery/jquery.jscrollpane.css">
    
    <?php
        // ------------------------------------------------------------
        // INCLUDE JAVASCRIPT FILES 
        // ------------------------------------------------------------ ?>
    <script 
        type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/libs/jquery/jquery.min.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/libs/jquery/jquery-ui.min.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/libs/jquery/jquery.qtip.min.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/libs/bootstrap/bootstrap.min.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/libs/date.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/general_functions.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/backend.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/libs/jquery/jquery.jscrollpane.min.js"></script>
    <script 
        type="text/javascript" 
        src="<?php echo $base_url; ?>assets/js/libs/jquery/jquery.mousewheel.js"></script>
</head>

<body>
<div id="header">
    <div id="header-logo">
        <img src="<?php echo $base_url; ?>assets/images/logo.png">
        <span><?php echo $company_name; ?></span>
    </div>
    
    <div id="header-menu">
        <?php // CALENDAR MENU ITEM 
              // ------------------------------------------------------ ?>
        <?php $hidden = ($privileges[PRIV_APPOINTMENTS]['view'] == TRUE) ? '' : 'hidden'; ?>
        <?php $active = ($active_menu == PRIV_APPOINTMENTS) ? 'active' : ''; ?>
        <a href="<?php echo $base_url; ?>backend" class="menu-item <?php echo $hidden; ?><?php echo $active; ?>"
                title="Manage all the appointment records of the available providers and services.">
            Calendar
        </a>
        
        <?php // CUSTOMERS MENU ITEM 
              // ------------------------------------------------------ ?>
        <?php $hidden = ($privileges[PRIV_CUSTOMERS]['view'] == TRUE) ? '' : 'hidden'; ?>
        <?php $active = ($active_menu == PRIV_CUSTOMERS) ? 'active' : ''; ?>
        <a href="<?php echo $base_url; ?>backend/customers" class="menu-item <?php echo $hidden; ?><?php echo $active; ?>"
                title="Manage the registered customers and view their booking history.">
            Customers
        </a>
        
        <?php // SERVICES MENU ITEM 
              // ------------------------------------------------------ ?>
        <?php $hidden = ($privileges[PRIV_SERVICES]['view'] == TRUE) ? '' : 'hidden'; ?>
        <?php $active = ($active_menu == PRIV_SERVICES) ? 'active' : ''; ?>
        <a href="<?php echo $base_url; ?>backend/services" class="menu-item <?php echo $hidden; ?><?php echo $active; ?>"
                title="Manage the available services and categories of the system.">
            Services
        </a>
        
        <?php // USERS MENU ITEM 
              // ------------------------------------------------------ ?>
        <?php $hidden = ($privileges[PRIV_USERS]['view'] ==  TRUE) ? '' : 'hidden'; ?>
        <?php $active = ($active_menu == PRIV_USERS) ? 'active' : ''; ?>
        <a href="<?php echo $base_url; ?>backend/users" class="menu-item <?php echo $hidden; ?><?php echo $active; ?>"
                title="Manage the backend users (admins, providers, secretaries).">
            Users
        </a>
        
        <?php // SETTINGS MENU ITEM 
              // ------------------------------------------------------ ?>
        <?php $hidden = ($privileges[PRIV_SYSTEM_SETTINGS]['view'] == TRUE 
                || $privileges[PRIV_USER_SETTINGS]['view'] == TRUE) ? '' : 'hidden'; ?>
        <?php $active = ($active_menu == PRIV_SYSTEM_SETTINGS) ? 'active' : ''; ?>
        <a href="<?php echo $base_url; ?>backend/settings" class="menu-item <?php echo $hidden; ?><?php echo $active; ?>"
                title="Set system and user settings.">
            Settings
        </a>
        
        <?php // LOGOUT MENU ITEM 
              // ------------------------------------------------------ ?>
        <a href="<?php echo $base_url; ?>user/logout" class="menu-item"
                title="Log out of the system.">
            Log Out
        </a>
        
    </div>
</div>
    
<div id="notification" style="display: none;"></div>

<div id="loading" style="display: none;">
    <img src="<?php echo $base_url; ?>assets/images/loading.gif" />
</div>