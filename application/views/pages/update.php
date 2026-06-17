<?php extend('layouts/account_layout'); ?>

<?php section('content'); ?>

<h3 class="mb-3"><?= lang('update_title') ?></h3>

<?php if (vars('success')): ?>
    <div>
        <div class="alert alert-success">
            <?= lang('database_update_success') ?>
        </div>
        
        <p>
            <?= lang('update_success_message') ?>
        </p>
        
        <a href="<?= site_url('about') ?>" class="btn btn-primary btn-large mb-3">
            <i class="fas fa-wrench me-2"></i>
            <?= lang('backend_section') ?>
        </a>
    </div>
<?php else: ?>
    <div>
        <div class="alert alert-success">
            <?= lang('database_update_error') ?>
        </div>

        <pre><?= lang('error_message_label') ?> <?= vars('exception') ?></pre>

        <p>
            <?= lang('restore_backup_message') ?>
        </p>

        <a href="<?= site_url('login') ?>" class="btn btn-primary btn-large mb-3">
            <i class="fas fa-wrench me-2"></i>
            <?= lang('backend_section') ?>
        </a>
    </div>
<?php endif; ?>

<?php end_section('content'); ?>

