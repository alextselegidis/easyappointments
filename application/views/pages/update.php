<?php extend('layouts/account_layout'); ?>

<?php section('content'); ?>

<h3 class="mb-3">Easy!Appointments Update</h3>

<?php if (vars('confirmation')): ?>
    <div>
        <div class="alert alert-warning">
            Attention! This will update your database schema and cannot be undone.
        </div>

        <p>
            Please back up your database before continuing.
        </p>

        <form method="post" action="<?= site_url('update/apply') ?>">
            <input type="hidden" name="csrf_token" value="<?= vars('csrf_token') ?>">

            <button type="submit" class="btn btn-primary btn-large mb-3">
                <i class="fas fa-wrench me-2"></i>
                <?= lang('update') ?>
            </button>

            <a href="<?= site_url('about') ?>" class="btn btn-outline-secondary btn-large mb-3">
                <?= lang('cancel') ?>
            </a>
        </form>
    </div>
<?php elseif (vars('success')): ?>
    <div>
        <div class="alert alert-success">
            Success! The database got updated successfully.
        </div>
        
        <p>
            You can now use the latest Easy!Appointments version.
        </p>
        
        <a href="<?= site_url('about') ?>" class="btn btn-primary btn-large mb-3">
            <i class="fas fa-wrench me-2"></i>
            <?= lang('backend_section') ?>
        </a>
    </div>
<?php else: ?>
    <div>
        <div class="alert alert-danger">
            Attention! There was an error during the update process.
        </div>

        <pre>Error Message: <?= e(vars('exception')) ?></pre>

        <p>
            Please restore your database backup.
        </p>

        <a href="<?= site_url('login') ?>" class="btn btn-primary btn-large mb-3">
            <i class="fas fa-wrench me-2"></i>
            <?= lang('backend_section') ?>
        </a>
    </div>
<?php endif; ?>

<?php end_section('content'); ?>

