<?php extend('layouts/message_layout') ?>

<?php section('content') ?>

<div>
    <img id="message-icon" src="<?= vars('message_icon') ?>" alt="warning">
</div>

<div>
    <h3><?= vars('message_title') ?></h3>

    <p><?= vars('message_text') ?></p>
</div>

<?php section('content') ?>

<?php section('scripts') ?>

<?php component('google_analytics_script', ['google_analytics_code' => vars('google_analytics_code')]) ?>
<?php component('matomo_analytics_script', ['matomo_analytics_url' => vars('matomo_analytics_url')]) ?>

<?php section('scripts') ?>

