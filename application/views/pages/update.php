<?php extend('layouts/account_layout'); ?>

<?php section('content'); ?>

<h3 class="mb-3">Easy!Appointments Update</h3>

<?php if (vars('success')): ?>
    <div>
        <div class="alert alert-success">
            Success! The database got updated successfully.
        </div>
        
        <p>
            You can now use the latest Easy!Appointments version.
        </p>
        
        <a href="<?= site_url('about') ?>" class="btn btn-success btn-large mb-3">
            <i class="fas fa-wrench me-2"></i>
            <?= lang('backend_section') ?>
        </a>
    </div>
<?php else: ?>
    <div>
        <div class="alert alert-success">
            Attention! There was an error during the update process.
        </div>

        <pre>Error Message: <?= vars('exception') ?></pre>

        <p>
            Please restore your database backup.
        </p>

        <a href="<?= site_url('login') ?>" class="btn btn-success btn-large mb-3">
            <i class="fas fa-wrench me-2"></i>
            <?= lang('backend_section') ?>
        </a>
    </div>
<?php endif; ?>

<?php end_section('content'); ?>

