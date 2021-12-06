<?php extend('layouts/account_layout') ?>

<?php section('content') ?>

<h3><?= lang('no_privileges') ?></h3>
<p>
    <?= lang('no_privileges_message') ?>
</p>

<br>

<a href="<?= site_url('backend') ?>" class="btn btn-success btn-large">
    <i class="icon-calendar icon-white me-2"></i>
    <?= lang('backend_calendar') ?>
</a>

<?php section('content') ?>
