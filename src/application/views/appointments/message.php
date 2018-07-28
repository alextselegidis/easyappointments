<!DOCTYPE html>
<html>
<?php
	$this->load->model('settings_model');			
	$theme_color = $this->settings_model->get_setting('theme_color');
?>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#35A768">
    <title><?= $message_title ?></title>

    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/bootstrap/css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/ext/bootstrap/css/bootstrap.custom.css'); ?>">
    <link rel="stylesheet" type="text/css" href="<?= asset_url('assets/css/frontend_' . $theme_color . '.css'); ?>">

    <link rel="icon" type="image/x-icon" href="<?= asset_url('assets/img/favicon.ico') ?>">
    <link rel="icon" sizes="192x192" href="<?= asset_url('assets/img/logo.png') ?>">

    <script>
        var EALang = <?= json_encode($this->lang->language) ?>;
    </script>

    <script src="<?= asset_url('assets/ext/jquery/jquery.min.js') ?>"></script>
    <script src="<?= asset_url('assets/ext/bootstrap/js/bootstrap.min.js') ?>"></script>
    <script src="<?= asset_url('assets/ext/datejs/date.js') ?>"></script>
    <script src="<?= asset_url('assets/js/general_functions.js') ?>"></script>
</head>

<body>
    <div id="main" class="container">
        <div class="wrapper row">

            <div id="message-frame" class="frame-container
                    col-xs-12
                    col-sm-offset-1 col-sm-10
                    col-md-offset-2 col-md-8
                    col-lg-offset-2 col-lg-8">

                <div class="col-xs-12 col-sm-2">
                    <img id="message-icon" src="<?= $message_icon ?>">
                </div>

                <div class="col-xs-12 col-sm-10">
                    <h3><?= $message_title ?></h3>
                    <p><?= $message_text ?></p>

                    <?php if (isset($exception)): ?>
                        <div>
                            <h4><?= lang('unexpected_issues') ?></h4>
                            <?php foreach($exceptions as $exception): ?>
                                <?= exceptionToHtml($exception) ?>
                            <?php endforeach ?>
                        </div>
                    <?php endif ?>
					<form action="<?php echo $this->config->item('base_url'); ?>">
						<input type="submit" value="<?php echo $this->lang->line('return_to_book') ?>" class="btn btn-primary">
					</form>					
                </div>
            </div>

        </div>
    </div>
    
    <?php google_analytics_script() ?>
</body>
</html>
