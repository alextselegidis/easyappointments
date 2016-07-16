<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $company_name; ?> | Easy!Appointments</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <link rel="icon" type="image/x-icon"
          href="<?php echo $base_url; ?>/assets/img/favicon.ico">

    <?php
        // ------------------------------------------------------------
        // INCLUDE CSS FILES
        // ------------------------------------------------------------ ?>
    <link
        rel="stylesheet"
        type="text/css"
        href="<?php echo $base_url; ?>/assets/ext/bootstrap/css/bootstrap.min.css">
    <link
        rel="stylesheet"
        type="text/css"
        href="<?php echo $base_url; ?>/assets/ext/jquery-ui/jquery-ui.min.css">
    <link
        rel="stylesheet"
        type="text/css"
        href="<?php echo $base_url; ?>/assets/ext/jquery-qtip/jquery.qtip.min.css">
    <link
        rel="stylesheet"
        type="text/css"
        href="<?php echo $base_url; ?>/assets/ext/jquery-jscrollpane/jquery.jscrollpane.css">
    <link
        rel="stylesheet"
        type="text/css"
        href="<?php echo $base_url; ?>/assets/css/backend.css">
    <link
        rel="stylesheet"
        type="text/css"
        href="<?php echo $base_url; ?>/assets/css/general.css">

    <?php
        // ------------------------------------------------------------
        // INCLUDE JAVASCRIPT FILES
        // ------------------------------------------------------------ ?>
    <script
        type="text/javascript"
        src="<?php echo $base_url; ?>/assets/ext/jquery/jquery.min.js"></script>
    <script
        type="text/javascript"
        src="<?php echo $base_url; ?>/assets/ext/bootstrap/js/bootstrap.min.js"></script>
    <script
        type="text/javascript"
        src="<?php echo $base_url; ?>/assets/ext/jquery-ui/jquery-ui.min.js"></script>
    <script
        type="text/javascript"
        src="<?php echo $base_url; ?>/assets/ext/jquery-qtip/jquery.qtip.min.js"></script>
    <script
        type="text/javascript"
        src="<?php echo $base_url; ?>/assets/ext/datejs/date.js"></script>
    <script
        type="text/javascript"
        src="<?php echo $base_url; ?>/assets/ext/jquery-jscrollpane/jquery.jscrollpane.min.js"></script>
    <script
        type="text/javascript"
        src="<?php echo $base_url; ?>/assets/ext/jquery-mousewheel/jquery.mousewheel.js"></script>

    <script type="text/javascript">
    	// Global JavaScript Variables - Used in all backend pages.
    	var availableLanguages = <?php echo json_encode($this->config->item('available_languages')); ?>;
    	var EALang = <?php echo json_encode($this->lang->language); ?>;
    </script>
</head>

<body>
<nav id="header" class="navbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <div id="header-logo" class="navbar-brand">
                <img src="<?php echo $base_url; ?>/assets/img/logo.png">
                <span><?php echo $company_name; ?></span>
            </div>
            
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#header-menu" 
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        

        <div id="header-menu" class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <?php // CALENDAR MENU ITEM
                      // ------------------------------------------------------ ?>
                <?php $hidden = ($privileges[PRIV_APPOINTMENTS]['view'] == TRUE) ? '' : 'hidden'; ?>
                <?php $active = ($active_menu == PRIV_APPOINTMENTS) ? 'active' : ''; ?>
                <li class="<?php echo $active . $hidden; ?>">
                    <a href="<?php echo site_url('backend'); ?>" class="menu-item"
                            title="<?php echo $this->lang->line('manage_appointment_record_hint'); ?>">
                        <?php echo $this->lang->line('calendar'); ?>
                    </a>
                </li>

                <?php // CUSTOMERS MENU ITEM
                      // ------------------------------------------------------ ?>
                <?php $hidden = ($privileges[PRIV_CUSTOMERS]['view'] == TRUE) ? '' : 'hidden'; ?>
                <?php $active = ($active_menu == PRIV_CUSTOMERS) ? 'active' : ''; ?>
                <li class="<?php echo $active . $hidden; ?>">
                    <a href="<?php echo site_url('backend/customers'); ?>" class="menu-item"
                            title="<?php echo $this->lang->line('manage_customers_hint'); ?>">
                        <?php echo $this->lang->line('customers'); ?>
                    </a>
                </li>

                <?php // SERVICES MENU ITEM
                      // ------------------------------------------------------ ?>
                <?php $hidden = ($privileges[PRIV_SERVICES]['view'] == TRUE) ? '' : 'hidden'; ?>
                <?php $active = ($active_menu == PRIV_SERVICES) ? 'active' : ''; ?>
                <li class="<?php echo $active . $hidden; ?>">
                    <a href="<?php echo site_url('backend/services'); ?>" class="menu-item"
                            title="<?php echo $this->lang->line('manage_services_hint'); ?>">
                        <?php echo $this->lang->line('services'); ?>
                    </a>
                </li>

                <?php // USERS MENU ITEM
                      // ------------------------------------------------------ ?>
                <?php $hidden = ($privileges[PRIV_USERS]['view'] ==  TRUE) ? '' : 'hidden'; ?>
                <?php $active = ($active_menu == PRIV_USERS) ? 'active' : ''; ?>
                <li class="<?php echo $active . $hidden; ?>">
                    <a href="<?php echo site_url('backend/users'); ?>" class="menu-item"
                            title="<?php echo $this->lang->line('manage_users_hint'); ?>">
                        <?php echo $this->lang->line('users'); ?>
                    </a>
                </li>

                <?php // SETTINGS MENU ITEM
                      // ------------------------------------------------------ ?>
                <?php $hidden = ($privileges[PRIV_SYSTEM_SETTINGS]['view'] == TRUE
                        || $privileges[PRIV_USER_SETTINGS]['view'] == TRUE) ? '' : 'hidden'; ?>
                <?php $active = ($active_menu == PRIV_SYSTEM_SETTINGS) ? 'active' : ''; ?>
                <li class="<?php echo $active . $hidden; ?>">
                    <a href="<?php echo site_url('backend/settings'); ?>" class="menu-item"
                            title="<?php echo $this->lang->line('settings_hint'); ?>">
                        <?php echo $this->lang->line('settings'); ?>
                    </a>
                </li>

                <?php // LOGOUT MENU ITEM
                      // ------------------------------------------------------ ?>
                <li>
                    <a href="<?php echo site_url('user/logout') ?>" class="menu-item"
                            title="<?php echo $this->lang->line('log_out_hint'); ?>">
                        <?php echo $this->lang->line('log_out'); ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div id="notification" style="display: none;"></div>

<div id="loading" style="display: none;">
    <img src="<?php echo $base_url; ?>/assets/img/loading.gif" />
</div>
