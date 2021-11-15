<?php
/**
 * @var string $message_title
 * @var string $message_icon
 * @var string $message_text
 */
?>

<?php extend('layouts/message/message_layout') ?>

<?php section('content') ?>

<div>
    <img id="message-icon" src="<?= $message_icon ?>" alt="warning">
</div>

<div>
    <h3><?= $message_title ?></h3>

    <p><?= $message_text ?></p>
</div>

<?php section('content') ?>

