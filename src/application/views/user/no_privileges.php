<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#35A768">
    <title><?php echo $this->lang->line('no_privileges') . ' - ' . $company_name; ?></title>

    <?php // INCLUDE JS FILES ?>
    <script
        type="text/javascript"
        src="<?php echo base_url('assets/ext/jquery/jquery.min.js'); ?>"></script>
    <script
        type="text/javascript"
        src="<?php echo base_url('assets/ext/bootstrap/js/bootstrap.min.js'); ?>"></script>

    <?php // INCLUDE CSS FILES ?>
    <link
        rel="stylesheet"
        type="text/css"
        href="<?php echo base_url('assets/ext/bootstrap/css/bootstrap.min.css'); ?>">

    <?php // SET FAVICON FOR PAGE ?>
    <link
        rel="icon"
        type="image/x-icon"
        href="<?php echo base_url('assets/img/favicon.ico'); ?>">

    <style>
        body {
            width: 100vw;
            height: 100vh;
            display: table-cell;
            vertical-align: middle;
            background-color: #CAEDF3;
        }

        #no-priv-frame {
            width: 630px;
            margin: auto;
            background: #FFF;
            border: 1px solid #DDDADA;
            padding: 70px;
        }

        .btn {
            margin-right: 10px;
        }

        @media(max-width: 640px) {
            #no-priv-frame {
                width: 100%;
                padding: 20px;
            }

            .btn {
                width: 100%;
                margin-bottom: 20px;
            }
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

        <a href="<?php echo site_url('backend') ?>" class="btn btn-success btn-large">
            <i class="icon-calendar icon-white"></i>
            <?php echo $this->lang->line('backend_calendar'); ?>
        </a>
    </div>
</body>
</html>
